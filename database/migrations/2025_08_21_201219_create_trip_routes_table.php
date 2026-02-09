<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trip_routes', function (Blueprint $table) {
            $table->uuid('trip_id')->primary();
            $table->text('polyline_encoded')->nullable();
            $table->json('geojson')->nullable(); // LineString
            $table->unsignedBigInteger('distance_meters')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('routing_provider', 20)->default('none');
            $table->string('checksum', 64)->index(); // p.ej. sha256 de (lat,lng,sequence)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_routes');
    }
};
