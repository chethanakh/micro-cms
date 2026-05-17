<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table): void {
            $table->string('delivery_service_provider')->nullable()->after('delivery_charges');
            $table->string('tracking_slug')->nullable()->unique()->after('tracking_id');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table): void {
            $table->dropUnique(['tracking_slug']);
            $table->dropColumn(['delivery_service_provider', 'tracking_slug']);
        });
    }
};
