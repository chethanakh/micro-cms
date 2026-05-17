<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_name',
    'phone_number',
    'mobile_number',
    'address_line_1',
    'address_line_2',
    'city',
    'postal_code',
    'default_delivery_charges',
    'logo_path',
])]
class CompanyInformation extends Model
{
    protected $table = 'company_information';

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'company_name' => config('app.name'),
                'default_delivery_charges' => 0,
            ],
        );
    }
}
