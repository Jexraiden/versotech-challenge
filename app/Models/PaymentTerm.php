<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'description', 'minimum_amount_cents', 'surcharge_percent', 'is_credit'])]
class PaymentTerm extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'minimum_amount_cents' => 'integer',
            'surcharge_percent' => 'decimal:4',
            'is_credit' => 'boolean',
        ];
    }
}
