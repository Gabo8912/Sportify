<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::disableForeignKeyConstraints();

    
    DB::table('likes')->truncate(); 
    DB::table('videos')->truncate();

    Schema::enableForeignKeyConstraints();

    Schema::table('videos', function (Blueprint $table) {
        if (Schema::hasColumn('videos', 'video_path')) {
            $table->dropColumn('video_path');
        }
        
        if (Schema::hasColumn('videos', 'video_url')) {
            $table->string('video_url', 500)->change();
        } else {
            $table->string('video_url', 500);
        }

        if (!Schema::hasColumn('videos', 'platform')) {
            $table->string('platform')->default('youtube')->after('user_id');
        }

        if (!Schema::hasColumn('videos', 'external_video_id')) {
            $table->string('external_video_id')->nullable()->after('platform');
        }
    });
}
};
