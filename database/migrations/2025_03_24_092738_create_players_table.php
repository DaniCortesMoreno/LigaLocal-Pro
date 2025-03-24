<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->integer('edad');
            $table->integer('dorsal');
            $table->string('posición')->nullable()->default(null); // También puedes usar enum si lo prefieres
            $table->enum('estado', ['activo', 'lesionado', 'suspendido'])->default('activo');
            $table->integer('goles')->default(0)->nullable();
            $table->integer('asistencias')->default(0)->nullable();
            $table->integer('amarillas')->default(0)->nullable();
            $table->integer('rojas')->default(0)->nullable();
            $table->integer('cantidad_partidos')->default(0)->nullable();
            $table->string('foto')->nullable();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('players');
    }
};
