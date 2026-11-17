<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_order_list_is_paginated_and_limited_to_the_authenticated_user_and_company(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $sameCompanyUser = User::factory()->create(['company_id' => $buyer->company_id]);
        $otherCompanyUser = User::where('email', 'atacadao@example.test')->firstOrFail();

        foreach (range(1, 21) as $index) {
            $this->createOrder($buyer, $index);
        }
        $this->createOrder($sameCompanyUser, 30);
        $this->createOrder($otherCompanyUser, 31);

        $this->actingAs($buyer)->get(route('orders.index'))
            ->assertInertia(fn (Assert $page) => $page->component('Orders/Index')
                ->where('orders.total', 21)
                ->where('orders.current_page', 1)
                ->where('orders.last_page', 2)
                ->has('orders.data', 20));

        $this->get(route('orders.index', ['page' => 2]))
            ->assertInertia(fn (Assert $page) => $page->where('orders.data', fn ($orders) => $orders->count() === 1));
    }

    private function createOrder(User $user, int $key): void
    {
        Order::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'payment_term_id' => 'PIX',
            'checkout_key' => sprintf('00000000-0000-4000-8000-%012d', $key),
            'checkout_fingerprint' => str_repeat((string) ($key % 10), 64),
            'status' => 'RECEBIDO',
            'subtotal_cents' => 1000,
            'surcharge_cents' => 0,
            'total_cents' => 1000,
        ]);
    }
}
