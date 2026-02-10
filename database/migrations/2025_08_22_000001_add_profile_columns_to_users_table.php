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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('email');
            $table->string('avatar')->nullable()->after('username');
            $table->string('cover_photo')->nullable()->after('avatar');
            $table->string('location')->nullable()->after('cover_photo');
            $table->string('google_id')->nullable()->index()->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['google_id']);
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'avatar', 'cover_photo', 'location', 'google_id']);
        });
    }
};
