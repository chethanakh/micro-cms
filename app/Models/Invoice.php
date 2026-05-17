<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'deal_id',
    'invoice_number',
    'status',
    'line_items',
])]
class Invoice extends Model
{
    /** @var array<string, string> */
    public const STATUS_OPTIONS = [
        'quote' => 'Quote',
        'accepted' => 'Accepted',
        'sent' => 'Sent',
        'paid' => 'Paid',
    ];

    protected function casts(): array
    {
        return [
            'line_items' => 'array',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
