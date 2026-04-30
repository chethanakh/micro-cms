<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'channel_id',
    'first_name',
    'last_name',
    'email',
    'phone_number',
    'mobile_number',
    'whatsapp_number',
    'billing_address_line_1',
    'billing_address_line_2',
    'billing_city',
    'billing_postal_code',
    'delivery_same_as_billing',
    'delivery_address_line_1',
    'delivery_address_line_2',
    'delivery_city',
    'delivery_postal_code',
])]
class Contact extends Model
{
    /**
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delivery_same_as_billing' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $contact): void {
            if (! $contact->delivery_same_as_billing) {
                return;
            }

            // Keep delivery fields aligned when the "same as billing" flag is enabled.
            $contact->delivery_address_line_1 = $contact->billing_address_line_1;
            $contact->delivery_address_line_2 = $contact->billing_address_line_2;
            $contact->delivery_city = $contact->billing_city;
            $contact->delivery_postal_code = $contact->billing_postal_code;
        });
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
