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
        Schema::create('trip_tag', function (Blueprint $table) {
            $table->uuid('trip_id');
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->primary(['trip_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_tag');
    }
};
