<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_name',
    'phone_number',
    'mobile_number',
    'logo_path',
])]
class CompanyInformation extends Model
{
    protected $table = 'company_information';

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            ['company_name' => config('app.name')],
        );
    }
}
