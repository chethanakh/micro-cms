<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_information', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_information');
    }
};
