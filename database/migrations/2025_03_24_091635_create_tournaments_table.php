<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['sala', 'futbol7', 'futbol11']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->integer('cantidad_equipos')->nullable()->default(0);
            $table->integer('cantidad_jugadores')->nullable()->default(0);
            $table->enum('estado', ['pendiente', 'en_curso', 'finalizado'])->default('pendiente');
            $table->enum('formato', ['liguilla', 'eliminacion', 'grupos_playoffs']);
            $table->enum('visibilidad', ['publico', 'privado'])->default('privado');
            $table->text('reglamento')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
