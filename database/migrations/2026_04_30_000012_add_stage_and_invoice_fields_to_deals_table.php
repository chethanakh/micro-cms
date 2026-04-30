<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table): void {
            $table->string('stage')->default('new')->after('contact_id');
            $table->string('invoice_number')->nullable()->after('stage');
            $table->timestamp('invoice_generated_at')->nullable()->after('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table): void {
            $table->dropColumn(['stage', 'invoice_number', 'invoice_generated_at']);
        });
    }
};
