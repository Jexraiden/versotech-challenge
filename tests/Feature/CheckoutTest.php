<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_checkout_requires_a_delivery_address_and_presents_available_terms(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 1])->assertRedirect();

        $this->get(route('checkout.show'))->assertInertia(fn(Assert $page) => $page->component('Checkout/Index')
            ->where('company.name', 'Bom Preço')
            ->where('company.available_credit_cents', $buyer->company->credit_limit_cents - $buyer->company->credit_used_cents)
            ->where('paymentTerms.0.eligible', true)
            ->has('checkoutKey'));

        $this->post(route('orders.store'), ['payment_term' => 'PIX', 'checkout_key' => '9cbf8c82-b2dd-4f8e-83f1-24dcb5d89967'])
            ->assertSessionHasErrors(['zipcode', 'state', 'city', 'district', 'address']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_reports_credit_shortfall_after_boleto_surcharge(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $subtotalCents = 307;
        DB::table('payment_terms')->where('code', 'BOL2856')->update(['minimum_amount_cents' => 1]);
        Company::whereKey($buyer->company_id)->update([
            'credit_limit_cents' => (int) $buyer->company->credit_used_cents + $subtotalCents,
        ]);
        $buyer->unsetRelation('company');
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 1])->assertRedirect();

        $this->get(route('checkout.show'))->assertInertia(fn(Assert $page) => $page->component('Checkout/Index')
            ->where('cart.subtotal_cents', $subtotalCents)
            ->where('company.available_credit_cents', $subtotalCents)
            ->where('paymentTerms.1.code', 'BOL2856')
            ->where('paymentTerms.1.eligible', false)
            ->where('paymentTerms.1.credit_shortfall_cents', 8)
            ->where('paymentTerms.2.credit_shortfall_cents', null));
    }

    public function test_pix_persists_order_and_delivery_snapshot_without_consuming_credit(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $initialCredit = (int) $buyer->company->credit_used_cents;
        $initialStock = (int) DB::table('inventories')->where('product_id', 1001)->value('quantity');
        $address = $this->deliveryAddress(['address_extra' => 'Casa 2']);
        $key = '9cbf8c82-b2dd-4f8e-83f1-24dcb5d89967';

        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 2])->assertRedirect();
        $this->post(route('orders.store'), ['payment_term' => 'PIX', ...$address, 'checkout_key' => $key])
            ->assertRedirect(route('orders.show', 1));

        $order = Order::with('items')->findOrFail(1);
        $this->assertSame('RECEBIDO', $order->status);
        $this->assertSame('Rua das Flores, 123', $order->address);
        $this->assertSame('Centro', $order->district);
        $this->assertSame('Casa 2', $order->address_extra);
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'delivery_address'));
        $this->assertSame(0, $order->surcharge_cents);
        $this->assertSame($initialCredit, (int) $buyer->company->fresh()->credit_used_cents);
        $this->assertSame($initialStock - 2, (int) DB::table('inventories')->where('product_id', 1001)->value('quantity'));
        $this->assertSame('SKU-1001', $order->items->first()->sku_snapshot);
        $this->assertSame(null, session('cart'));

        $this->get(route('orders.show', $order))->assertInertia(fn(Assert $page) => $page->component('Orders/Show')->where('order.delivery_address.zipcode', '80000-000')->where('order.delivery_address.state', 'PR'));
    }

    public function test_credit_order_consumes_final_total_and_duplicate_key_does_not_repeat_purchase(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        Company::whereKey($buyer->company_id)->update(['credit_limit_cents' => 10000000, 'credit_used_cents' => 0]);
        DB::table('payment_terms')->where('code', 'BOL2856')->update(['minimum_amount_cents' => 0]);
        $address = $this->deliveryAddress(['address' => 'Avenida Central, 456', 'zipcode' => '80000-100']);
        $key = 'e61bd96a-385f-4896-9a0b-38a1e153f121';
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 2])->assertRedirect();

        $payload = ['payment_term' => 'BOL2856', ...$address, 'checkout_key' => $key];
        $this->post(route('orders.store'), $payload)->assertRedirect(route('orders.show', 1));
        $order = Order::findOrFail(1);
        $expectedSurcharge = intdiv(($order->subtotal_cents * 25_000) + 500_000, 1_000_000);
        $this->assertSame($expectedSurcharge, $order->surcharge_cents);
        $this->assertSame($order->total_cents, (int) Company::findOrFail($buyer->company_id)->credit_used_cents);

        $this->post(route('orders.store'), $payload)->assertRedirect(route('orders.show', 1));
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(1, DB::table('order_items')->count());
    }

    public function test_reusing_checkout_key_with_a_different_payload_is_rejected_and_preserves_the_new_cart(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $address = $this->deliveryAddress();
        $key = 'a01ef752-eaa6-468a-98d3-a624c286b8fb';

        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 1])->assertRedirect();
        $payload = ['payment_term' => 'PIX', ...$address, 'checkout_key' => $key];
        $this->post(route('orders.store'), $payload)->assertRedirect(route('orders.show', 1));

        $this->post(route('cart.add', 1002), ['quantity' => 1])->assertRedirect();
        $stockBeforeReplay = (int) DB::table('inventories')->where('product_id', 1002)->value('quantity');

        $this->post(route('orders.store'), $payload)->assertSessionHasErrors('checkout');

        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(1, DB::table('order_items')->count());
        $this->assertSame($stockBeforeReplay, (int) DB::table('inventories')->where('product_id', 1002)->value('quantity'));
        $this->assertSame([1002 => 1], session('cart'));
    }

    public function test_checkout_key_cannot_be_reused_by_another_company(): void
    {
        $firstBuyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $address = $this->deliveryAddress(['address' => 'Avenida Central, 456', 'zipcode' => '80000-100']);
        $key = '8540a479-3ec3-47f5-ab1a-c1d775399a69';

        $this->actingAs($firstBuyer)->post(route('cart.add', 1001), ['quantity' => 1])->assertRedirect();
        $payload = ['payment_term' => 'PIX', ...$address, 'checkout_key' => $key];
        $this->post(route('orders.store'), $payload)->assertRedirect(route('orders.show', 1));

        $otherBuyer = User::where('email', 'atacadao@example.test')->firstOrFail();
        $this->actingAs($otherBuyer)->post(route('cart.add', 1001), ['quantity' => 1])->assertRedirect();
        $stockBeforeReplay = (int) DB::table('inventories')->where('product_id', 1001)->value('quantity');

        $this->post(route('orders.store'), $payload)->assertSessionHasErrors('checkout');

        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(1, DB::table('order_items')->count());
        $this->assertSame($stockBeforeReplay, (int) DB::table('inventories')->where('product_id', 1001)->value('quantity'));
        $this->assertSame([1001 => 1], session('cart'));
    }

    public function test_blocked_company_insufficient_stock_and_insufficient_credit_leave_resources_unchanged(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $key = '3900774d-1b2e-463a-aaf7-cbfe325dfe3f';
        $address = $this->deliveryAddress(['address' => 'Rua Um, 10', 'zipcode' => '80000-010']);
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 2])->assertRedirect();
        $initialCredit = (int) $buyer->company->credit_used_cents;
        $initialStock = (int) DB::table('inventories')->where('product_id', 1001)->value('quantity');

        Company::whereKey($buyer->company_id)->update(['is_blocked' => true]);
        $this->post(route('orders.store'), ['payment_term' => 'PIX', ...$address, 'checkout_key' => $key])->assertSessionHasErrors('checkout');
        $this->assertDatabaseCount('orders', 0);
        Company::whereKey($buyer->company_id)->update(['is_blocked' => false]);

        DB::table('inventories')->where('product_id', 1001)->update(['quantity' => 1]);
        $this->post(route('orders.store'), ['payment_term' => 'PIX', ...$address, 'checkout_key' => $key])->assertSessionHasErrors('checkout');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(1, (int) DB::table('inventories')->where('product_id', 1001)->value('quantity'));

        DB::table('inventories')->where('product_id', 1001)->update(['quantity' => $initialStock]);
        DB::table('payment_terms')->where('code', 'BOL28')->update(['minimum_amount_cents' => 0]);
        Company::whereKey($buyer->company_id)->update(['credit_limit_cents' => $initialCredit, 'credit_used_cents' => $initialCredit]);
        $this->post(route('orders.store'), ['payment_term' => 'BOL28', ...$address, 'checkout_key' => '3900774d-1b2e-463a-aaf7-cbfe325dfe40'])->assertSessionHasErrors('payment_term');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame($initialStock, (int) DB::table('inventories')->where('product_id', 1001)->value('quantity'));
        $this->assertSame($initialCredit, (int) Company::findOrFail($buyer->company_id)->credit_used_cents);
        $this->assertSame([1001 => 2], session('cart'));
    }

    public function test_order_is_not_visible_to_another_company(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 1]);
        $this->post(route('orders.store'), ['payment_term' => 'PIX', ...$this->deliveryAddress(['address' => 'Rua Dois, 20', 'zipcode' => '80000-020']), 'checkout_key' => 'dad21e5c-ad7f-42e5-a866-978303ffb006']);

        $otherBuyer = User::where('email', 'atacadao@example.test')->firstOrFail();
        $this->actingAs($otherBuyer)->get(route('orders.show', 1))->assertNotFound();
    }

    public function test_failure_while_saving_items_rolls_back_order_stock_and_credit(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 2])->assertRedirect();
        $initialStock = (int) DB::table('inventories')->where('product_id', 1001)->value('quantity');
        $initialCredit = (int) $buyer->company->credit_used_cents;
        DB::statement("CREATE TRIGGER fail_order_item_insert BEFORE INSERT ON order_items BEGIN SELECT RAISE(ABORT, 'forced order item failure'); END");

        $this->post(route('orders.store'), [
            'payment_term' => 'PIX',
            ...$this->deliveryAddress(['address' => 'Rua Três, 30', 'zipcode' => '80000-030']),
            'checkout_key' => '3a845942-c174-4e2a-9f9e-4bdbd80d1ea8',
        ])->assertServerError();

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame($initialStock, (int) DB::table('inventories')->where('product_id', 1001)->value('quantity'));
        $this->assertSame($initialCredit, (int) Company::findOrFail($buyer->company_id)->credit_used_cents);
        $this->assertSame([1001 => 2], session('cart'));
    }

    public function test_address_fields_are_trimmed_and_state_must_be_a_valid_brazilian_uf(): void
    {
        $buyer = User::where('email', 'bompreco@example.test')->firstOrFail();
        $this->actingAs($buyer)->post(route('cart.add', 1001), ['quantity' => 1])->assertRedirect();
        $address = $this->deliveryAddress(['city' => '   ', 'state' => 'XX']);

        $this->post(route('orders.store'), ['payment_term' => 'PIX', ...$address, 'checkout_key' => 'a6be7607-fab2-4daa-adde-240c7929a913'])
            ->assertSessionHasErrors(['city', 'state']);
        $this->assertDatabaseCount('orders', 0);
    }

    /** @return array{zipcode: string, state: string, city: string, district: string, address: string, address_extra: ?string} */
    private function deliveryAddress(array $overrides = []): array
    {
        return array_merge([
            'zipcode' => '80000-000',
            'state' => 'PR',
            'city' => 'Curitiba',
            'district' => 'Centro',
            'address' => 'Rua das Flores, 123',
            'address_extra' => null,
        ], $overrides);
    }
}
