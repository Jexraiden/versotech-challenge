<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SessionCartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        DB::table('inventories')->where('product_id', 1001)->update(['quantity' => 100]);
    }

    public function test_cart_add_consolidates_repeated_products_and_keeps_only_identifiers_and_quantities_in_session(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 2])->assertRedirect();
        $this->post(route('cart.add', 1001), ['quantity' => 3])->assertRedirect();

        $this->assertSame([1001 => 5], session('cart'));
        $this->assertSame(100, (int) DB::table('inventories')->where('product_id', 1001)->value('quantity'));
        $this->assertSame(320000, (int) $buyer->company->fresh()->credit_used_cents);

        $this->get(route('cart.show'))->assertInertia(fn (Assert $page) => $page->component('Cart/Index')
            ->where('cart.item_count', 5)
            ->where('cart.items.0.valid', true));
    }

    public function test_quantity_update_recalculates_wholesale_price_and_removal_empties_cart(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 2]);
        $this->patch(route('cart.update', 1001), ['quantity' => 50])->assertRedirect();

        $this->get(route('cart.show'))->assertInertia(fn (Assert $page) => $page->where('cart.items.0.quantity', 50)
            ->where('cart.items.0.regular_price_cents', 307)
            ->where('cart.items.0.unit_price_cents', 276)
            ->where('cart.items.0.line_total_cents', 13800)
            ->where('cart.subtotal_cents', 13800)
            ->where('cart.savings_cents', 1550)
            ->where('cart.total_cents', 13800));

        $this->delete(route('cart.remove', 1001))->assertRedirect();
        $this->assertSame([], session('cart'));
    }

    public function test_cart_displays_wholesale_price_and_applies_it_at_the_minimum_quantity(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $this->actingAs($buyer)->post(route('cart.add', 1265), ['quantity' => 19])->assertRedirect();

        $this->get(route('cart.show'))->assertInertia(fn (Assert $page) => $page
            ->where('cart.items.0.wholesale_price_cents', 1243)
            ->where('cart.items.0.wholesale_min_qty', 20)
            ->where('cart.items.0.unit_price_cents', 1381)
            ->where('cart.items.0.line_total_cents', 26239));

        $this->patch(route('cart.update', 1265), ['quantity' => 20])->assertRedirect();
        $this->get(route('cart.show'))->assertInertia(fn (Assert $page) => $page
            ->where('cart.items.0.wholesale_price_cents', 1243)
            ->where('cart.items.0.wholesale_min_qty', 20)
            ->where('cart.items.0.unit_price_cents', 1243)
            ->where('cart.items.0.line_total_cents', 24860));
    }

    public function test_invalid_products_and_quantities_are_rejected_without_mutating_the_cart(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 101])
            ->assertSessionHasErrors('quantity');
        $this->assertSame([], session('cart', []));

        DB::table('products')->where('id', 1001)->update(['is_active' => false]);
        $this->post(route('cart.add', 1001), ['quantity' => 1])->assertSessionHasErrors('quantity');
        $this->assertSame([], session('cart', []));
    }

    public function test_quantity_update_rejects_products_that_are_not_already_in_the_cart(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();

        $this->actingAs($buyer)->patch(route('cart.update', 1001), ['quantity' => 1])
            ->assertSessionHasErrors('quantity');

        $this->assertSame([], session('cart', []));
    }

    public function test_cart_requires_authentication(): void
    {
        $this->get(route('cart.show'))->assertRedirect(route('login'));
        $this->post(route('cart.add', 1001), ['quantity' => 1])->assertRedirect(route('login'));
    }

    public function test_cart_mutation_routes_block_concurrent_requests_for_the_same_session(): void
    {
        foreach (['cart.add', 'cart.update', 'cart.remove'] as $routeName) {
            $route = app('router')->getRoutes()->getByName($routeName);

            $this->assertNotNull($route, "Route {$routeName} should exist.");
            $this->assertSame(10, $route->locksFor(), "Route {$routeName} should acquire a session lock.");
            $this->assertSame(10, $route->waitsFor(), "Route {$routeName} should wait for a concurrent session lock.");
        }
    }

    public function test_cart_is_cleared_when_the_authenticated_user_changes(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 1]);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->post(route('login.store'), ['email' => 'atacadao@example.test', 'password' => 'password'])
            ->assertRedirect(route('catalog.index'));

        $this->get(route('cart.show'))->assertInertia(fn (Assert $page) => $page
            ->where('cart.items', [])
            ->where('cart.item_count', 0)
            ->where('cart.total_cents', 0));
    }
}
