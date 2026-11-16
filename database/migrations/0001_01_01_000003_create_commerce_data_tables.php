<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_tables', function (Blueprint $table): void {
            $table->string('code', 40)->primary();
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('tax_id', 20)->unique();
            $table->string('legal_name');
            $table->string('trade_name');
            $table->string('city');
            $table->char('state', 2);
            $table->string('price_table_id', 40);
            $table->foreign('price_table_id')->references('code')->on('price_tables');
            $table->index('price_table_id');
            $table->bigInteger('credit_limit_cents');
            $table->bigInteger('credit_used_cents');
            $table->boolean('is_blocked')->default(false);
            $table->text('block_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('sku')->unique();
            $table->string('ean', 32)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('brand');
            $table->string('category')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('inventories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->timestamps();
        });

        Schema::create('price_table_items', function (Blueprint $table): void {
            $table->string('price_table_id', 40);
            $table->foreign('price_table_id')->references('code')->on('price_tables')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('regular_price_cents');
            $table->bigInteger('wholesale_price_cents')->nullable();
            $table->integer('wholesale_min_qty')->nullable();
            $table->primary(['price_table_id', 'product_id']);
            $table->index('product_id');
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('discount_percent', 7, 4);
            $table->date('starts_on');
            $table->date('ends_on');
            $table->index(['product_id', 'starts_on', 'ends_on']);
            $table->timestamps();
        });

        Schema::create('payment_terms', function (Blueprint $table): void {
            $table->string('code', 40)->primary();
            $table->string('description');
            $table->bigInteger('minimum_amount_cents');
            $table->decimal('surcharge_percent', 7, 4);
            $table->boolean('is_credit');
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE companies ADD CONSTRAINT companies_credit_amounts_nonnegative CHECK (credit_limit_cents >= 0 AND credit_used_cents >= 0)');
            DB::statement('ALTER TABLE inventories ADD CONSTRAINT inventories_quantity_nonnegative CHECK (quantity >= 0)');
            DB::statement('ALTER TABLE price_table_items ADD CONSTRAINT price_table_items_prices_nonnegative CHECK (regular_price_cents >= 0 AND (wholesale_price_cents IS NULL OR wholesale_price_cents >= 0))');
            DB::statement('ALTER TABLE price_table_items ADD CONSTRAINT price_table_items_wholesale_fields CHECK ((wholesale_price_cents IS NULL) = (wholesale_min_qty IS NULL) AND (wholesale_min_qty IS NULL OR wholesale_min_qty > 0))');
            DB::statement('ALTER TABLE promotions ADD CONSTRAINT promotions_valid_period CHECK (ends_on >= starts_on)');
            DB::statement('ALTER TABLE promotions ADD CONSTRAINT promotions_discount_nonnegative CHECK (discount_percent >= 0 AND discount_percent <= 100)');
            DB::statement('ALTER TABLE payment_terms ADD CONSTRAINT payment_terms_values_nonnegative CHECK (minimum_amount_cents >= 0 AND surcharge_percent >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_terms');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('price_table_items');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('price_tables');
    }
};
