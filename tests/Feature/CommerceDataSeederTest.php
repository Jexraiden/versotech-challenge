<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommerceDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_source_rows_and_relationships(): void
    {
        $this->seed();

        $this->assertDatabaseCount('companies', 3);
        $this->assertDatabaseCount('products', 500);
        $this->assertDatabaseCount('inventories', 500);
        $this->assertDatabaseCount('price_tables', 2);
        $this->assertDatabaseCount('price_table_items', 972);
        $this->assertDatabaseCount('promotions', 3);
        $this->assertDatabaseCount('payment_terms', 3);
        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseHas('products', ['id' => 1001, 'sku' => 'SKU-1001']);
        $inventory = Inventory::where('product_id', 1001)->firstOrFail();
        $this->assertGreaterThan(0, $inventory->id);
        $this->assertSame(1001, (int) $inventory->product_id);
        $this->assertDatabaseHas('companies', ['id' => 1, 'price_table_id' => 'PADRAO', 'credit_limit_cents' => 1500000]);
        $this->assertDatabaseHas('users', ['email' => 'bompreco@example.test', 'company_id' => 1]);
        $this->assertDatabaseHas('price_table_items', ['price_table_id' => 'ATACADISTA', 'product_id' => 1001]);
        $this->assertDatabaseMissing('price_table_items', ['price_table_id' => 'ATACADISTA', 'product_id' => 1029]);
    }

    public function test_reseeding_does_not_restore_operational_inventory_or_credit(): void
    {
        $this->seed();

        Inventory::where('product_id', 1001)->update(['quantity' => 7]);
        Company::whereKey(1)->update(['credit_used_cents' => 654321]);

        $this->seed();

        $this->assertSame(7, Inventory::where('product_id', 1001)->value('quantity'));
        $this->assertSame(654321, (int) Company::whereKey(1)->value('credit_used_cents'));
    }

    public function test_source_ids_do_not_collide_with_generated_ids_after_seeding(): void
    {
        $this->seed();

        $company = Company::create([
            'tax_id' => '22.333.444/0001-55',
            'legal_name' => 'Empresa de teste',
            'trade_name' => 'Empresa de teste',
            'city' => 'Curitiba',
            'state' => 'PR',
            'price_table_id' => 'PADRAO',
            'credit_limit_cents' => 10000,
            'credit_used_cents' => 0,
            'is_blocked' => false,
        ]);
        $product = Product::create([
            'sku' => 'SKU-NEW',
            'ean' => '0000000000000',
            'name' => 'Produto novo',
            'brand' => 'Versotech',
            'category' => 'Teste',
            'is_active' => true,
        ]);
        $promotion = Promotion::create([
            'product_id' => 1001,
            'discount_percent' => '1.0000',
            'starts_on' => '2030-01-01',
            'ends_on' => '2030-01-02',
        ]);
        $this->assertSame(4, $company->id);
        $this->assertSame(1501, $product->id);
        $this->assertSame(4, $promotion->id);
    }

    public function test_demo_accounts_are_linked_to_distinct_companies(): void
    {
        $this->seed();

        $this->assertSame(3, User::query()->distinct()->count('company_id'));
        $this->assertTrue(Hash::check('password', User::where('email', 'bompreco@example.test')->value('password')));
    }
}
