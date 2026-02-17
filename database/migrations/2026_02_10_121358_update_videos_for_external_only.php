<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{   
    public function up()
{
    Schema::table('videos', function (Blueprint $table) {
        if (Schema::hasColumn('videos', 'video_path')) { // Verifica antes de borrar
            $table->dropColumn('video_path');
        }
        
        // Aquí añade tus nuevas columnas si no existen
        if (!Schema::hasColumn('videos', 'external_url')) {
            $table->string('external_url')->nullable();
        }
    });
}
};
