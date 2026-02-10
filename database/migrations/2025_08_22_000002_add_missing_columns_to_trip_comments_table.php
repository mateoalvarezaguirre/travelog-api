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
        Schema::table('trip_comments', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('trip_id');
            $table->text('text')->after('user_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_comments', function (Blueprint $table) {
            $table->dropForeign('trip_comments_user_id_foreign');

            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'text']);
        });
    }
};
