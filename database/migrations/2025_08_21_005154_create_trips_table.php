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
        Schema::create('trips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 255);
            $table->longText('content');
            $table->longText('private_content')->nullable(); // Content visible only to the owner
            $table->unsignedInteger('owner_id');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('status', 50)->default('draft'); // e.g., draft, published, archived
            $table->string('visibility', 50)->default('public'); // e.g., public, private, unlisted
            $table->timestamp('published_at')->nullable(); // Timestamp for when the trip was published
            $table->unsignedInteger('likes_count')->default(0); // Count of likes for the trip
            $table->unsignedInteger('comments_count')->default(0); // Count of comments on
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
