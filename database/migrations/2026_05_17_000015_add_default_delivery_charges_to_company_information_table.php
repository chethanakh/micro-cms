<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_information', function (Blueprint $table): void {
            $table->decimal('default_delivery_charges', 10, 2)->default(0)->after('postal_code');
        });
    }

    public function down(): void
    {
        Schema::table('company_information', function (Blueprint $table): void {
            $table->dropColumn('default_delivery_charges');
        });
    }
};
