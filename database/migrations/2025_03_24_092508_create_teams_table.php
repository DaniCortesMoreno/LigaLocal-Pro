<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('logo')->nullable();
            $table->integer('numero_jugadores')->default(0);
            $table->string('color_equipacion')->nullable();
            $table->string('entrenador')->nullable();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('teams');
    }
};
