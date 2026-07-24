<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('message_logs', function (Blueprint $table) {
            $table->string('media_url')->nullable()->after('type');
            $table->string('media_filename')->nullable()->after('media_url');
            $table->string('media_mimetype')->nullable()->after('media_filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_logs', function (Blueprint $table) {
            $table->dropColumn(['media_url', 'media_filename', 'media_mimetype']);
        });
    }
};
