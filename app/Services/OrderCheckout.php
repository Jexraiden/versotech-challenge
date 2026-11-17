<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Order;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderCheckout
{
    public function __construct(private readonly SessionCart $cart, private readonly ProductPricing $pricing) {}

    public function confirm(
        Request $request,
        User $user,
        string $paymentTermCode,
        array $deliveryAddress,
        string $checkoutKey
    ): Order {
        $items = $this->cart->items($request);
        ksort($items);
        $fingerprint = $this->fingerprint($items, $paymentTermCode, $deliveryAddress);
        $existing = Order::query()->where('checkout_key', $checkoutKey)->first();
        if ($existing) {
            return $this->resolveReplay(
                $existing,
                $user,
                $paymentTermCode,
                $deliveryAddress,
                $items,
                $fingerprint
            );
        }

        try {
            $order = DB::transaction(function () use ($request, $user, $paymentTermCode, $deliveryAddress, $checkoutKey, $items, $fingerprint): Order {
                $company = Company::query()->whereKey($user->company_id)->lockForUpdate()->firstOrFail();
                $existing = Order::query()->where('checkout_key', $checkoutKey)->first();
                if ($existing) {
                    return $this->resolveReplay(
                        $existing,
                        $user,
                        $paymentTermCode,
                        $deliveryAddress,
                        $items,
                        $fingerprint
                    );
                }

                if ($company->is_blocked) {
                    throw ValidationException::withMessages([
                        'checkout' => 'Sua empresa está bloqueada e não pode confirmar pedidos.'
                    ]);
                }

                if ($items === []) {
                    throw ValidationException::withMessages([
                        'checkout' => 'Adicione itens ao carrinho antes de confirmar o pedido.'
                    ]);
                }

                $term = PaymentTerm::query()->whereKey($paymentTermCode)->first();
                if (! $term) {
                    throw ValidationException::withMessages([
                        'payment_term' => 'Selecione uma condição de pagamento disponível.'
                    ]);
                }

                $inventoryRows = DB::table('inventories')
                    ->whereIn('product_id', array_keys($items))
                    ->orderBy('product_id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('product_id');

                $products = Product::query()
                    ->whereIn('id', array_keys($items))
                    ->with([
                        'priceTableItems' => fn($query) => $query->where('price_table_id', $company->price_table_id),
                        'promotions',
                    ])
                    ->orderBy('id')
                    ->get()
                    ->keyBy('id');

                if ($products->count() !== count($items)) {
                    throw ValidationException::withMessages([
                        'checkout' => 'Um produto do carrinho não está mais disponível. Revise o carrinho.'
                    ]);
                }

                $lines = [];
                $subtotal = 0;
                $today = CarbonImmutable::now(config('app.timezone'));
                foreach ($items as $productId => $quantity) {
                    $product = $products->get($productId);
                    $inventory = $inventoryRows->get($productId);
                    $price = $product->priceTableItems->first();
                    if (! $product->is_active || ! $price) {
                        throw ValidationException::withMessages([
                            'checkout' => "{$product->name} não pode mais ser comprado. Revise o carrinho."
                        ]);
                    }
                    if (! $inventory || (int) $inventory->quantity < $quantity) {
                        throw ValidationException::withMessages([
                            'checkout' => "Estoque insuficiente para {$product->name}. Revise as quantidades do carrinho."
                        ]);
                    }

                    $promotions = $this->pricing->activePromotions($product->promotions, $today);
                    $wholesalePriceCents = $price->wholesale_price_cents === null
                        ? null
                        : (int) $price->wholesale_price_cents;
                    $wholesaleMinQty = $price->wholesale_min_qty === null
                        ? null
                        : (int) $price->wholesale_min_qty;
                    $quote = $this->pricing->quote(
                        (int) $price->regular_price_cents,
                        $wholesalePriceCents,
                        $wholesaleMinQty,
                        $quantity,
                        $promotions
                    );
                    $lineTotal = $quote['unit_price_cents'] * $quantity;
                    $subtotal += $lineTotal;
                    $lines[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'unit_price_cents' => $quote['unit_price_cents'],
                        'line_total_cents' => $lineTotal
                    ];
                }

                if ($subtotal <= 0 || $subtotal < (int) $term->minimum_amount_cents) {
                    throw ValidationException::withMessages([
                        'payment_term' => 'O subtotal não atende ao mínimo desta condição de pagamento.'
                    ]);
                }
                $surcharge = $this->percentageOf($subtotal, (string) $term->surcharge_percent);
                $total = $subtotal + $surcharge;
                if ($term->is_credit && $total > ((int) $company->credit_limit_cents - (int) $company->credit_used_cents)) {
                    throw ValidationException::withMessages([
                        'payment_term' => 'O valor do pedido excede o crédito disponível para sua empresa. Selecione PIX ou reduza o pedido.'
                    ]);
                }

                $order = Order::query()->create([
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'payment_term_id' => $term->code,
                    'checkout_key' => $checkoutKey,
                    'checkout_fingerprint' => $fingerprint,
                    ...$deliveryAddress,
                    'status' => 'RECEBIDO',
                    'subtotal_cents' => $subtotal,
                    'surcharge_cents' => $surcharge,
                    'total_cents' => $total,
                ]);
                foreach ($lines as $line) {
                    $order->items()->create([
                        'product_id' => $line['product']->id,
                        'sku_snapshot' => $line['product']->sku,
                        'name_snapshot' => $line['product']->name,
                        'quantity' => $line['quantity'],
                        'unit_price_cents' => $line['unit_price_cents'],
                        'line_total_cents' => $line['line_total_cents'],
                    ]);
                    DB::table('inventories')->where('product_id', $line['product']->id)->decrement('quantity', $line['quantity']);
                }
                if ($term->is_credit) {
                    $company->credit_used_cents = (int) $company->credit_used_cents + $total;
                    $company->save();
                }

                return $order;
            }, 3);
        } catch (QueryException $exception) {
            $existing = Order::query()->where('checkout_key', $checkoutKey)->first();
            if (! $existing) throw $exception;
            $order = $this->resolveReplay($existing, $user, $paymentTermCode, $deliveryAddress, $items, $fingerprint);
        }

        $request->session()->forget('cart');
        return $order;
    }

    private function resolveReplay(Order $order, User $user, string $paymentTermCode, array $deliveryAddress, array $items, string $fingerprint): Order
    {
        if ((int) $order->company_id !== (int) $user->company_id) throw ValidationException::withMessages(['checkout' => 'Esta chave de confirmação não pertence à sua empresa.']);
        if ($order->payment_term_id !== $paymentTermCode || $this->orderAddress($order) !== $deliveryAddress || ($items !== [] && ! hash_equals($order->checkout_fingerprint, $fingerprint))) {
            throw ValidationException::withMessages(['checkout' => 'Esta tentativa já foi usada com dados diferentes. Recarregue o checkout para iniciar outra tentativa.']);
        }
        return $order;
    }

    private function fingerprint(array $items, string $paymentTermCode, array $deliveryAddress): string
    {
        return hash('sha256', json_encode([$items, $paymentTermCode, $deliveryAddress], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    /** @return array{zipcode: string, state: string, city: string, district: string, address: string, address_extra: ?string} */
    private function orderAddress(Order $order): array
    {
        return [
            'zipcode' => $order->zipcode,
            'state' => $order->state,
            'city' => $order->city,
            'district' => $order->district,
            'address' => $order->address,
            'address_extra' => $order->address_extra,
        ];
    }

    private function percentageOf(int $amountCents, string $percentage): int
    {
        [$whole, $fraction] = array_pad(explode('.', $percentage, 2), 2, '');
        $scaledPercent = ((int) $whole * 10_000) + (int) substr(str_pad($fraction, 4, '0'), 0, 4);
        $denominator = 1_000_000;
        return intdiv(($amountCents * $scaledPercent) + intdiv($denominator, 2), $denominator);
    }
}
