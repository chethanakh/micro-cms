<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'product_id',
    'vendor_id',
    'cost_price',
    'retail_price',
    'quantity',
    'sold_quantity',
    'purchase_date',
])]
class Stock extends Model
{
    /**
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'retail_price' => 'decimal:2',
            'quantity' => 'integer',
            'sold_quantity' => 'integer',
            'purchase_date' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * @return HasMany<DealLineItem, $this>
     */
    public function dealLineItems(): HasMany
    {
        return $this->hasMany(DealLineItem::class);
    }

    public static function syncSoldQuantities(): void
    {
        $soldByStock = DealLineItem::query()
            ->selectRaw('stock_id, SUM(quantity) as sold_total')
            ->groupBy('stock_id')
            ->pluck('sold_total', 'stock_id');

        static::query()->update(['sold_quantity' => 0]);

        foreach ($soldByStock as $stockId => $soldTotal) {
            static::query()
                ->whereKey($stockId)
                ->update(['sold_quantity' => (int) $soldTotal]);
        }
    }
}
