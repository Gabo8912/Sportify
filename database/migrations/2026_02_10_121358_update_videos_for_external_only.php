<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::table('videos', function (Blueprint $table) {
        $table->dropColumn('video_path');

        $table->string('video_url'); 
        $table->string('platform'); 
        $table->string('external_video_id'); 
    });
    }
};
