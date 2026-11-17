<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_catalog_uses_only_the_authenticated_company_price_table(): void
    {
        $atacadao = User::where('email', 'atacadao@example.test')->firstOrFail();

        $this->actingAs($atacadao)->get(route('catalog.index', ['search' => 'SKU-1029']))
            ->assertInertia(fn (Assert $page) => $page->component('Catalog/Index')->has('products.data', 0));

        $this->actingAs($atacadao)->get(route('catalog.show', 1029))
            ->assertInertia(fn (Assert $page) => $page->component('Catalog/Show')->where('product.unit_price_cents', null)->where('product.available', false));
    }

    public function test_catalog_search_and_category_filter_are_applied_and_query_is_preserved(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();

        $this->actingAs($buyer)->get(route('catalog.index', ['search' => 'sku-1001', 'category' => 'Bebidas']))
            ->assertInertia(fn (Assert $page) => $page->component('Catalog/Index')
                ->where('filters.search', 'sku-1001')
                ->where('filters.category', 'Bebidas')
                ->has('products.data', 1)
                ->where('products.data.0.id', 1001));
    }

    public function test_catalog_search_matches_partial_name_sku_and_ean(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $product = Product::findOrFail(1001);

        foreach (['Guaraná São João 350ml', $product->sku, $product->ean] as $search) {
            $this->actingAs($buyer)->get(route('catalog.index', ['search' => $search]))
                ->assertInertia(fn (Assert $page) => $page->component('Catalog/Index')
                    ->where('filters.search', $search)
                    ->where('products.data.0.id', $product->id));
        }
    }

    public function test_catalog_paginates_results_and_preserves_filters_in_pagination_links(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();

        $this->actingAs($buyer)->get(route('catalog.index', ['category' => 'Bebidas', 'page' => 2]))
            ->assertInertia(fn (Assert $page) => $page->component('Catalog/Index')
                ->where('filters.category', 'Bebidas')
                ->where('products.current_page', 2)
                ->has('products.data', 20)
                ->where('products.links.1.url', fn ($url) => str_contains($url, 'category=Bebidas') && str_contains($url, 'page=1')));
    }

    public function test_product_without_stock_is_not_available(): void
    {
        DB::table('inventories')->where('product_id', 1001)->update(['quantity' => 0]);
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();

        $this->actingAs($buyer)->get(route('catalog.show', 1001))
            ->assertInertia(fn (Assert $page) => $page->component('Catalog/Show')
                ->where('product.stock_quantity', 0)
                ->where('product.available', false));
    }

    public function test_catalog_does_not_return_products_without_a_price_for_the_company(): void
    {
        $buyer = User::where('email', 'atacadao@example.test')->firstOrFail();

        $this->actingAs($buyer)->get(route('catalog.index', ['search' => 'SKU-1029']))
            ->assertInertia(fn (Assert $page) => $page->component('Catalog/Index')->has('products.data', 0));
    }

    public function test_categories_are_limited_to_active_products_in_the_company_price_table(): void
    {
        $product = Product::create([
            'sku' => 'SKU-CATEGORY-TEST',
            'ean' => 'EAN-CATEGORY-TEST',
            'name' => 'Category test product',
            'description' => null,
            'brand' => 'Test',
            'category' => 'PADRAO-only category',
            'is_active' => true,
        ]);
        DB::table('price_table_items')->insert([
            'price_table_id' => 'PADRAO',
            'product_id' => $product->id,
            'regular_price_cents' => 100,
        ]);

        $bompreco = User::where('email', 'bompreco@example.test')->firstOrFail();
        $atacadao = User::where('email', 'atacadao@example.test')->firstOrFail();

        $this->actingAs($bompreco)->get(route('catalog.index'))
            ->assertInertia(fn (Assert $page) => $page->where('categories', fn ($categories) => $categories->contains('PADRAO-only category')));

        $this->actingAs($atacadao)->get(route('catalog.index'))
            ->assertInertia(fn (Assert $page) => $page->where('categories', fn ($categories) => ! $categories->contains('PADRAO-only category')));

        $product->update(['is_active' => false]);
        $this->actingAs($bompreco)->get(route('catalog.index'))
            ->assertInertia(fn (Assert $page) => $page->where('categories', fn ($categories) => ! $categories->contains('PADRAO-only category')));
    }

    public function test_detail_is_informational_and_inactive_product_is_unavailable(): void
    {
        $product = \App\Models\Product::findOrFail(1001);
        $product->update(['is_active' => false]);
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();

        $this->actingAs($buyer)->get(route('catalog.show', $product))
            ->assertInertia(fn (Assert $page) => $page->component('Catalog/Show')
                ->where('product.is_active', false)
                ->where('product.available', false));
    }

    public function test_detail_presents_the_authenticated_company_price(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $product = Product::findOrFail(1001);

        $this->actingAs($buyer)->get(route('catalog.show', $product))
            ->assertInertia(fn (Assert $page) => $page->component('Catalog/Show')
                ->where('product.regular_price_cents', 307)
                ->where('product.wholesale_price_cents', 276)
                ->where('product.wholesale_min_qty', 50));
    }

    public function test_authenticated_pages_share_navigation_categories_and_current_cart_count(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();

        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 3])->assertRedirect();
        $this->get(route('catalog.show', 1001))->assertInertia(fn (Assert $page) => $page
            ->where('auth.user.name', $buyer->name)
            ->where('navigation.cart.item_count', 3)
            ->where('navigation.categories', fn ($categories) => $categories->isNotEmpty()));
    }
}
