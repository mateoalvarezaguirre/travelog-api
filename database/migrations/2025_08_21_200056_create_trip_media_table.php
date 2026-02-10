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
        Schema::create('trip_media', function (Blueprint $table) {
            $table->id();
            $table->string('trip_id'); // Foreign key to the trips table
            $table->string('media_type', 50); // e.g., image, video
            $table->string('media_url'); // URL or path to the media file
            $table->string('caption', 255)->nullable(); // Optional caption for the media
            $table->unsignedInteger('order')->default(0); // Order of the media in
            $table->boolean('is_featured')->default(false); // Whether this media is featured
            $table->boolean('is_visible')->default(true); // Whether this media is visible to
            $table->unsignedInteger('uploaded_by'); // User who uploaded the media
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });

        Schema::table('trip_media', function (Blueprint $table) {
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_media');
    }
};
