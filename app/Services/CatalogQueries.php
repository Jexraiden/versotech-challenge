<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CatalogQueries
{
    /** @return Collection<int, string> */
    public function categoriesForPriceTable(string $priceTableId): Collection
    {
        return Product::query()
            ->join('price_table_items', 'price_table_items.product_id', '=', 'products.id')
            ->where('price_table_items.price_table_id', $priceTableId)
            ->where('products.is_active', true)
            ->distinct()
            ->orderBy('products.category')
            ->pluck('products.category');
    }
}
