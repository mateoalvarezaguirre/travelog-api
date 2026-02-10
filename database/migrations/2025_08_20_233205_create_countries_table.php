<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->comment('ISO ref: https://www.iso.org/iso-3166-country-codes.html');
            $table->unsignedSmallInteger('id')->primary();
            $table->string('country_name', 100);
            $table->char('alpha2_code', 2);
            $table->char('alpha3_code', 3);
            $table->string('phone_prefix', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
