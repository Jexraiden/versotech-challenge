<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['sku', 'ean', 'name', 'description', 'brand', 'category', 'is_active'])]
class Product extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
