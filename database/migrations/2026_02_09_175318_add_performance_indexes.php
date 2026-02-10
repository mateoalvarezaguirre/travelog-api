<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->index(['owner_id', 'status'], 'trips_owner_status_idx');
            $table->index(['visibility', 'status', 'created_at'], 'trips_public_feed_idx');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->index('user_id', 'likes_user_id_idx');
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->index('following_id', 'follows_following_id_idx');
        });

        Schema::table('places', function (Blueprint $table) {
            $table->index(['user_id', 'marker_type'], 'places_user_marker_idx');
        });

        Schema::table('trip_media', function (Blueprint $table) {
            $table->index(['trip_id', 'order'], 'trip_media_trip_order_idx');
        });

        Schema::table('trip_tag', function (Blueprint $table) {
            $table->index('tag_id', 'trip_tag_tag_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex('trips_owner_status_idx');
            $table->dropIndex('trips_public_feed_idx');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex('likes_user_id_idx');
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->dropIndex('follows_following_id_idx');
        });

        Schema::table('places', function (Blueprint $table) {
            $table->dropIndex('places_user_marker_idx');
        });

        Schema::table('trip_media', function (Blueprint $table) {
            $table->dropIndex('trip_media_trip_order_idx');
        });

        Schema::table('trip_tag', function (Blueprint $table) {
            $table->dropIndex('trip_tag_tag_id_idx');
        });
    }
};
