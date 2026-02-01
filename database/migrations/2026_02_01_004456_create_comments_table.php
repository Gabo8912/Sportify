<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quién comentó
        $table->foreignId('video_id')->constrained()->onDelete('cascade'); // En qué video
        $table->text('body'); // El contenido del comentario
        $table->timestamps();
    });
}
};
