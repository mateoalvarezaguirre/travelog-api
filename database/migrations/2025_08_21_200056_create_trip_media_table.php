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
        Schema::create('trip_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->string('media_type', 50); // e.g., image, video
            $table->string('media_url'); // URL or path to the media file
            $table->string('caption', 255)->nullable(); // Optional caption for the media
            $table->unsignedInteger('order')->default(0); // Order of the media in
            $table->boolean('is_featured')->default(false); // Whether this media is featured
            $table->boolean('is_visible')->default(true); // Whether this media is visible to
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // User who uploaded the media
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
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
