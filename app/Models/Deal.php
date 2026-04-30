<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['contact_id', 'stage', 'invoice_number', 'invoice_generated_at'])]
class Deal extends Model
{
    /**
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invoice_generated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (): void {
            Stock::syncSoldQuantities();
        });
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * @return HasMany<DealLineItem, $this>
     */
    public function lineItems(): HasMany
    {
        return $this->hasMany(DealLineItem::class);
    }
}
