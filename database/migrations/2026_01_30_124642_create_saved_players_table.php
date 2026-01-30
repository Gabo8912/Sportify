<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('saved_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scout_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('player_id')->constrained('users')->onDelete('cascade');
            $table->text('scout_notes')->nullable();
            $table->timestamps();
        });
    }
};
