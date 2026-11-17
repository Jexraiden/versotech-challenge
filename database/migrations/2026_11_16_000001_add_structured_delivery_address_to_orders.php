<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            // Nullable for orders created before the address was split into fields.
            $table->string('zipcode', 20)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('district', 120)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('address_extra', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['zipcode', 'state', 'city', 'district', 'address', 'address_extra']);
        });
    }
};
