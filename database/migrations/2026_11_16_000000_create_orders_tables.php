<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('payment_term_id', 40);
            $table->foreign('payment_term_id')->references('code')->on('payment_terms')->restrictOnDelete();
            $table->uuid('checkout_key')->unique();
            $table->char('checkout_fingerprint', 64);
            $table->text('delivery_address');
            $table->string('status', 30)->default('RECEBIDO');
            $table->bigInteger('subtotal_cents');
            $table->bigInteger('surcharge_cents');
            $table->bigInteger('total_cents');
            $table->timestamps();
            $table->index(['company_id', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku_snapshot');
            $table->string('name_snapshot');
            $table->integer('quantity');
            $table->bigInteger('unit_price_cents');
            $table->bigInteger('line_total_cents');
            $table->timestamps();
            $table->index(['order_id', 'id']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_amounts_nonnegative CHECK (subtotal_cents >= 0 AND surcharge_cents >= 0 AND total_cents >= 0)");
            DB::statement("ALTER TABLE order_items ADD CONSTRAINT order_items_values_valid CHECK (quantity > 0 AND unit_price_cents >= 0 AND line_total_cents >= 0)");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
