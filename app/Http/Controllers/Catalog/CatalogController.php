<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\PriceTableItem;
use App\Models\Product;
use App\Services\CatalogQueries;
use App\Services\ProductPricing;
use App\Services\SessionCart;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function __construct(
        private readonly ProductPricing $pricing,
        private readonly CatalogQueries $catalogQueries,
        private readonly SessionCart $cart,
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $company = $request->user()->company;

        $query = $this->catalogQuery($company->price_table_id)
            ->where('products.is_active', true);

        if (($filters['search'] ?? '') !== '') {
            $search = trim($filters['search']);
            $query->where(function (Builder $query) use ($search): void {
                $pattern = '%'.mb_strtolower($search).'%';
                $query->whereRaw('LOWER(products.name) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(products.sku) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(products.ean) LIKE ?', [$pattern]);
            });
        }

        if (($filters['category'] ?? '') !== '') {
            $query->where('products.category', $filters['category']);
        }

        $products = $query->orderBy('products.name')->paginate(20)->withQueryString();
        $today = CarbonImmutable::now(config('app.timezone'));
        $items = $products->getCollection()->map(fn (Product $product): array => $this->present(
            $product,
            $today,
            $product->regular_price_cents,
            $product->wholesale_price_cents,
            $product->wholesale_min_qty,
        ));

        return Inertia::render('Catalog/Index', [
            'products' => [
                'data' => $items,
                'links' => $products->linkCollection(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
            'categories' => $this->catalogQueries->categoriesForPriceTable($company->price_table_id),
            'filters' => ['search' => $filters['search'] ?? '', 'category' => $filters['category'] ?? ''],
            'companyName' => $company->trade_name,
            'cart' => $this->cart->present($request, $request->user()),
        ]);
    }

    public function show(Request $request, Product $product): Response
    {
        $product->load(['inventory', 'promotions']);
        $price = PriceTableItem::query()
            ->where('price_table_id', $request->user()->company->price_table_id)
            ->where('product_id', $product->id)
            ->first(['regular_price_cents', 'wholesale_price_cents', 'wholesale_min_qty']);

        return Inertia::render('Catalog/Show', [
            'product' => $this->present(
                $product,
                CarbonImmutable::now(config('app.timezone')),
                $price?->regular_price_cents,
                $price?->wholesale_price_cents,
                $price?->wholesale_min_qty,
            ),
        ]);
    }

    private function catalogQuery(string $priceTableId): Builder
    {
        return Product::query()
            ->select('products.*', 'price_table_items.regular_price_cents', 'price_table_items.wholesale_price_cents', 'price_table_items.wholesale_min_qty')
            ->join('price_table_items', function ($join) use ($priceTableId): void {
                $join->on('price_table_items.product_id', '=', 'products.id')
                    ->where('price_table_items.price_table_id', '=', $priceTableId);
            })
            ->with(['inventory', 'promotions']);
    }

    private function present(
        Product $product,
        CarbonImmutable $today,
        int|string|null $regularPriceCents,
        int|string|null $wholesalePriceCents,
        int|string|null $wholesaleMinQty,
    ): array
    {
        $regularPriceCents = $regularPriceCents === null ? null : (int) $regularPriceCents;
        $wholesalePriceCents = $wholesalePriceCents === null ? null : (int) $wholesalePriceCents;
        $wholesaleMinQty = $wholesaleMinQty === null ? null : (int) $wholesaleMinQty;
        $stock = (int) ($product->inventory?->quantity ?? 0);
        $activePromotions = $this->pricing->activePromotions($product->promotions, $today);
        $quote = $regularPriceCents === null ? null : $this->pricing->quote(
            $regularPriceCents,
            $wholesalePriceCents,
            $wholesaleMinQty,
            1,
            $activePromotions,
        );
        $promotionPriceCents = $regularPriceCents === null
            ? null
            : $this->pricing->bestPromotionPrice($regularPriceCents, $activePromotions);

        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'ean' => $product->ean,
            'name' => $product->name,
            'description' => $product->description,
            'brand' => $product->brand,
            'category' => $product->category,
            'is_active' => $product->is_active,
            'stock_quantity' => $stock,
            'available' => $product->is_active && $quote !== null && $stock > 0,
            'regular_price_cents' => $regularPriceCents,
            'unit_price_cents' => $quote['unit_price_cents'] ?? null,
            'promotion_price_cents' => $promotionPriceCents,
            'wholesale_price_cents' => $wholesalePriceCents,
            'wholesale_min_qty' => $wholesaleMinQty,
        ];
    }
}
