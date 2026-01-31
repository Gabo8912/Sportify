<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('likes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('video_id')->constrained()->onDelete('cascade');
        $table->timestamps();

        // Constraint: A user can only like a video once
        $table->unique(['user_id', 'video_id']);
    });
}
};
