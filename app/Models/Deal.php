<?php

namespace App\Models;

use App\Enums\DealStage;
use App\Enums\DeliveryServiceProvider;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['contact_id', 'stage', 'invoice_number', 'invoice_generated_at', 'delivery_charges_available', 'delivery_charges', 'delivery_service_provider', 'tracking_id', 'tracking_slug'])]
class Deal extends Model
{
    /**
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invoice_generated_at' => 'datetime',
            'delivery_charges_available' => 'boolean',
            'delivery_charges' => 'decimal:2',
            'delivery_service_provider' => DeliveryServiceProvider::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $deal): void {
            if (blank($deal->tracking_slug)) {
                $deal->tracking_slug = (string) Str::ulid();
            }
        });

        static::saving(function (self $deal): void {
            if (blank($deal->tracking_slug)) {
                $deal->tracking_slug = (string) Str::ulid();
            }
        });

        static::deleted(function (): void {
            Stock::syncSoldQuantities();
        });

        static::updated(function (self $deal): void {
            if (! $deal->wasChanged('stage')) {
                return;
            }

            $deal->syncLatestInvoiceStatus();
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

    /**
     * @return HasMany<Invoice, $this>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    protected function syncLatestInvoiceStatus(): void
    {
        $latestInvoice = $this->invoices()->latest('id')->first();

        if (! $latestInvoice) {
            return;
        }

        $latestInvoice->update([
            'status' => $this->invoiceStatusForStage($this->stage),
        ]);
    }

    protected function invoiceStatusForStage(?string $stage): string
    {
        return DealStage::tryFrom($stage)?->invoiceStatus() ?? DealStage::Pending->invoiceStatus();
    }
}
