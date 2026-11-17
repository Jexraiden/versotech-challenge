<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SessionCart
{
    public function __construct(private readonly ProductPricing $pricing) {}

    public function add(Request $request, User $user, Product $product, int $quantity): void
    {
        $items = $this->items($request);
        $newQuantity = ($items[$product->id] ?? 0) + $quantity;
        $this->assertPurchasable($user, $product, $newQuantity);
        $items[$product->id] = $newQuantity;
        $request->session()->put('cart', $items);
    }

    public function update(Request $request, User $user, Product $product, int $quantity): void
    {
        $items = $this->items($request);
        if (! array_key_exists($product->id, $items)) {
            throw ValidationException::withMessages(['quantity' => 'Este produto não está no carrinho.']);
        }

        $this->assertPurchasable($user, $product, $quantity);
        $items[$product->id] = $quantity;
        $request->session()->put('cart', $items);
    }

    public function remove(Request $request, Product $product): void
    {
        $items = $this->items($request);
        unset($items[$product->id]);
        $request->session()->put('cart', $items);
    }

    /** @return array{items: array<int, array<string, mixed>>, item_count: int, subtotal_cents: int, savings_cents: int, total_cents: int, notice: ?string} */
    public function present(Request $request, User $user): array
    {
        $items = $this->items($request);
        $products = Product::query()->with([
            'inventory',
            'promotions',
            'priceTableItems' => fn($query) => $query->where('price_table_id', $user->company->price_table_id),
        ])->whereIn('id', array_keys($items))->get()->keyBy('id');
        $missingIds = array_diff(array_keys($items), $products->keys()->map(fn($id): int => (int) $id)->all());
        foreach ($missingIds as $missingId) unset($items[$missingId]);
        if ($missingIds !== []) $request->session()->put('cart', $items);
        $presented = [];
        $itemCount = 0;
        $subtotal = 0;
        $total = 0;
        $savings = 0;
        $today = CarbonImmutable::now(config('app.timezone'));

        foreach ($items as $productId => $quantity) {
            $product = $products->get($productId);
            if (! $product) {
                $presented[] = ['id' => $productId, 'quantity' => $quantity, 'valid' => false, 'issue' => 'Produto não encontrado. Remova este item do carrinho.'];
                continue;
            }

            $price = $product->priceTableItems->first();
            $stock = (int) ($product->inventory?->quantity ?? 0);
            $issue = null;
            if (! $product->is_active) $issue = 'Produto indisponível no momento.';
            elseif (! $price) $issue = 'Produto sem preço para sua empresa.';
            elseif ($stock < $quantity) $issue = $stock > 0 ? "Estoque atualizado: há apenas {$stock} unidades." : 'Produto sem estoque disponível.';

            if ($issue !== null) {
                $presented[] = [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'valid' => false,
                    'can_update' => $product->is_active && $price !== null && $stock > 0,
                    'stock_quantity' => $stock,
                    'issue' => $issue,
                ];
                continue;
            }

            $promotions = $this->pricing->activePromotions($product->promotions, $today);
            $quote = $this->pricing->quote((int) $price->regular_price_cents, $price->wholesale_price_cents === null ? null : (int) $price->wholesale_price_cents, $price->wholesale_min_qty === null ? null : (int) $price->wholesale_min_qty, $quantity, $promotions);
            $lineTotal = $quote['unit_price_cents'] * $quantity;
            $subtotal += $lineTotal;
            $total += $lineTotal;
            $savings += max(0, ((int) $price->regular_price_cents * $quantity) - $lineTotal);
            $itemCount += $quantity;
            $presented[] = [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'quantity' => $quantity,
                'valid' => true,
                'stock_quantity' => $stock,
                'regular_price_cents' => (int) $price->regular_price_cents,
                'wholesale_price_cents' => $price->wholesale_price_cents === null ? null : (int) $price->wholesale_price_cents,
                'wholesale_min_qty' => $price->wholesale_min_qty === null ? null : (int) $price->wholesale_min_qty,
                'unit_price_cents' => $quote['unit_price_cents'],
                'line_total_cents' => $lineTotal,
            ];
        }

        return [
            'items' => $presented,
            'item_count' => $itemCount,
            'subtotal_cents' => $subtotal,
            'savings_cents' => $savings,
            'total_cents' => $total,
            'notice' => $missingIds === [] ? null : 'Itens removidos porque não estão mais disponíveis no catálogo.',
        ];
    }

    private function assertPurchasable(User $user, Product $product, int $quantity): void
    {
        $product->loadMissing('inventory');
        if (! $product->is_active) {
            throw ValidationException::withMessages(['quantity' => 'Este produto está indisponível.']);
        }
        $stock = (int) ($product->inventory?->quantity ?? 0);
        if ($stock < 1 || $quantity > $stock) {
            throw ValidationException::withMessages(['quantity' => 'A quantidade solicitada excede o estoque disponível. Há ' . $stock . ' unidades.']);
        }
        $hasPrice = $product->priceTableItems()->where('price_table_id', $user->company->price_table_id)->exists();
        if (! $hasPrice) {
            throw ValidationException::withMessages(['quantity' => 'Este produto não possui preço para sua empresa.']);
        }
    }

    /** @return array<int, int> */
    public function items(Request $request): array
    {
        $items = $request->session()->get('cart', []);
        if (! is_array($items)) return [];
        $clean = [];
        foreach ($items as $productId => $quantity) {
            if (filter_var($productId, FILTER_VALIDATE_INT) !== false && (int) $productId > 0 && filter_var($quantity, FILTER_VALIDATE_INT) !== false && (int) $quantity > 0) {
                $clean[(int) $productId] = (int) $quantity;
            }
        }
        return $clean;
    }
}
