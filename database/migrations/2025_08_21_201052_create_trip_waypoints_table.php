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
        Schema::create('trip_waypoints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trip_id')->index();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->unsignedInteger('sequence')->index();
            $table->string('display_name', 255);
            $table->decimal('lat', 9, 6);
            $table->decimal('lng', 9, 6);
            $table->string('provider', 20)->nullable();  // google|mapbox|osm
            $table->string('place_id', 191)->nullable()->index();
            $table->char('country_code', 2)->nullable()->index();
            $table->json('address_json')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->timestamps();
            $table->unique(['trip_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_waypoints');
    }
};
