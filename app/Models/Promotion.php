<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'discount_percent', 'starts_on', 'ends_on'])]
class Promotion extends Model
{
    protected function casts(): array
    {
        return [
            'discount_percent' => 'decimal:4',
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
