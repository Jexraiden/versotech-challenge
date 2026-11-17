<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tax_id',
    'legal_name',
    'trade_name',
    'city',
    'state',
    'price_table_id',
    'credit_limit_cents',
    'credit_used_cents',
    'is_blocked',
    'block_reason',
])]
class Company extends Model
{
    protected function casts(): array
    {
        return [
            'credit_limit_cents' => 'integer',
            'credit_used_cents' => 'integer',
            'is_blocked' => 'boolean',
        ];
    }

    public function priceTable(): BelongsTo
    {
        return $this->belongsTo(PriceTable::class, 'price_table_id', 'code');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
