<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'platform')) {
                $table->string('platform')->default('local')->after('video_url');
            }
            
            if (!Schema::hasColumn('videos', 'external_video_id')) {
                $table->string('external_video_id')->nullable()->after('platform');
            }
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['platform', 'external_video_id']);
        });
    }
};