<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'user_id', 'payment_term_id', 'checkout_key', 'checkout_fingerprint', 'zipcode', 'state', 'city', 'district', 'address', 'address_extra', 'status', 'subtotal_cents', 'surcharge_cents', 'total_cents'])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'subtotal_cents' => 'integer',
            'surcharge_cents' => 'integer',
            'total_cents' => 'integer'
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class, 'payment_term_id', 'code');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
