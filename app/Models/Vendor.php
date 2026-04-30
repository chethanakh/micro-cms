<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'address_line_1',
    'address_line_2',
    'city',
    'postal_code',
    'special_notes',
    'phone_number',
    'mobile_number',
    'email',
])]
class Vendor extends Model
{
    /**
     * @return HasMany<Stock, $this>
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }
}
