<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table): void {
            $table->boolean('delivery_charges_available')->default(false)->after('contact_id');
            $table->decimal('delivery_charges', 10, 2)->nullable()->after('delivery_charges_available');
            $table->string('tracking_id')->nullable()->after('delivery_charges');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table): void {
            $table->dropColumn(['delivery_charges_available', 'delivery_charges', 'tracking_id']);
        });
    }
};
