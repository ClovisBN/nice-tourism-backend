<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_of_interests', function (Blueprint $table) {
            $table->id();
            $table->string('name_point');
            $table->json('coordinate');
            $table->json('details')->nullable();
            $table->string('image_path')->nullable(); // Ajouter ce champ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_of_interests');
    }
};
