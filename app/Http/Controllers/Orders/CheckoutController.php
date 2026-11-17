<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTerm;
use App\Services\OrderCheckout;
use App\Services\SessionCart;
use App\Support\BrazilianStates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(private readonly SessionCart $cart, private readonly OrderCheckout $checkout) {}

    public function show(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $cart = $this->cart->present($request, $user);

        if ($cart['items'] === []) {
            return redirect()
                ->route('cart.show')
                ->withErrors(['checkout' => 'Adicione itens ao carrinho para continuar.']);
        }

        $availableCredit = (int) $user->company->credit_limit_cents - (int) $user->company->credit_used_cents;

        $terms = PaymentTerm::query()
            ->orderBy('minimum_amount_cents')
            ->get()
            ->map(function (PaymentTerm $term) use ($cart, $availableCredit): array {
                $surcharge = $this->percentageOf($cart['subtotal_cents'], (string) $term->surcharge_percent);
                $termTotal = $cart['subtotal_cents'] + $surcharge;
                $meetsMinimum = $cart['subtotal_cents'] >= (int) $term->minimum_amount_cents && $cart['subtotal_cents'] > 0;
                $hasCredit = ! $term->is_credit || $termTotal <= $availableCredit;
                return [
                    'code' => $term->code,
                    'description' => $term->description,
                    'minimum_amount_cents' => (int) $term->minimum_amount_cents,
                    'surcharge_percent' => (string) $term->surcharge_percent,
                    'eligible' => $meetsMinimum && $hasCredit,
                    'credit_shortfall_cents' => $meetsMinimum && ! $hasCredit ? $termTotal - $availableCredit : null,
                    'restriction' => ! $meetsMinimum
                        ? 'Subtotal abaixo do mínimo desta condição.'
                        : (! $hasCredit ? 'Crédito disponível insuficiente.' : null),
                ];
            })
            ->all();

        return Inertia::render('Checkout/Index', [
            'cart' => $cart,
            'company' => [
                'name' => $user->company->trade_name,
                'city' => $user->company->city,
                'state' => $user->company->state,
                'is_blocked' => $user->company->is_blocked,
                'available_credit_cents' => $availableCredit,
            ],
            'paymentTerms' => $terms,
            'checkoutKey' => (string) Str::uuid(),
            'states' => BrazilianStates::codes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(array_map(
            static fn($value) => is_string($value) ? trim($value) : $value,
            $request->only(['zipcode', 'state', 'city', 'district', 'address', 'address_extra'])
        ));

        $input = $request->validate([
            'payment_term' => ['required', 'string', 'exists:payment_terms,code'],
            'zipcode' => ['required', 'string', 'max:20'],
            'state' => ['required', 'string', 'size:2', 'in:' . implode(',', BrazilianStates::codes())],
            'city' => ['required', 'string', 'max:120'],
            'district' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'address_extra' => ['nullable', 'string', 'max:255'],
            'checkout_key' => ['required', 'uuid'],
        ], [
            'zipcode.required' => 'Informe o CEP.',
            'state.required' => 'Selecione a UF.',
            'state.in' => 'Selecione uma UF válida.',
            'city.required' => 'Informe a cidade.',
            'district.required' => 'Informe o bairro.',
            'address.required' => 'Informe o logradouro.',
        ]);

        $order = $this->checkout->confirm(
            $request,
            $request->user(),
            $input['payment_term'],
            [
                'zipcode' => $input['zipcode'],
                'state' => $input['state'],
                'city' => $input['city'],
                'district' => $input['district'],
                'address' => $input['address'],
                'address_extra' => $input['address_extra'] ?? null,
            ],
            $input['checkout_key']
        );

        return redirect()->route('orders.show', $order);
    }

    public function order(Request $request, int $order): Response|RedirectResponse
    {
        $order = Order::query()
            ->with(['items', 'paymentTerm'])
            ->where('company_id', $request->user()->company_id)
            ->whereKey($order)
            ->firstOrFail();

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'delivery_address' => $order->zipcode === null ? null : [
                    'zipcode' => $order->zipcode,
                    'state' => $order->state,
                    'city' => $order->city,
                    'district' => $order->district,
                    'address' => $order->address,
                    'address_extra' => $order->address_extra,
                ],
                'subtotal_cents' => $order->subtotal_cents,
                'surcharge_cents' => $order->surcharge_cents,
                'total_cents' => $order->total_cents,
                'payment_term' => $order->paymentTerm->description,
                'items' => $order->items->map(fn($item): array => [
                    'sku' => $item->sku_snapshot,
                    'name' => $item->name_snapshot,
                    'quantity' => $item->quantity,
                    'unit_price_cents' => $item->unit_price_cents,
                    'line_total_cents' => $item->line_total_cents
                ])->all(),
            ]
        ]);
    }

    private function percentageOf(int $amountCents, string $percentage): int
    {
        [$whole, $fraction] = array_pad(explode('.', $percentage, 2), 2, '');
        $scaledPercent = ((int) $whole * 10_000) + (int) substr(str_pad($fraction, 4, '0'), 0, 4);
        $denominator = 1_000_000;
        return intdiv(($amountCents * $scaledPercent) + intdiv($denominator, 2), $denominator);
    }
}
