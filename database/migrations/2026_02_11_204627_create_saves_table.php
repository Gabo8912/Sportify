<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('saveable'); 
            $table->timestamps();
            $table->unique(['user_id', 'saveable_id', 'saveable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saves');
    }
};