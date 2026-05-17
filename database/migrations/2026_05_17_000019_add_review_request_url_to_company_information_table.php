<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_information', function (Blueprint $table): void {
            $table->string('review_request_url')->nullable()->after('default_delivery_charges');
        });
    }

    public function down(): void
    {
        Schema::table('company_information', function (Blueprint $table): void {
            $table->dropColumn('review_request_url');
        });
    }
};
