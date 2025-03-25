<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('match_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('tournaments')->onDelete('cascade');
            $table->foreignId('equipo1_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('equipo2_id')->constrained('teams')->onDelete('cascade');
            $table->dateTime('fecha_partido');
            $table->string('resultado')->nullable(); // Ejemplo: "2-1"
            $table->string('estado_partido'); // Ej: "pendiente", "jugado", "cancelado"
            $table->string('marcador_parcial')->nullable(); // Ejemplo: "1-0 al descanso"
            $table->string('arbitro')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_games');
    }
};
