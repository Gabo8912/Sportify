<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(){
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            
            // CONEXIÓN: Esto conecta el perfil con el usuario (User ID)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); //To eliminate everthing if user is delated
            
            //PHYSIC DATA
            $table->string('position')->nullable();
            $table->string('dominant_foot')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->string('current_club')->nullable();
            
            // PERSONAL DATA
            $table->string('birth_date');
            $table->string('location')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('cover_photo_path')->nullable();
            $table->text('achievements')->nullable();
            $table->string('availability_status')->default('looking_for_club');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
