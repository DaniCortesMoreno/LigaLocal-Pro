<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::create('teams', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->longText('logo')->nullable();
        $table->string('color_equipacion')->nullable();
        $table->string('entrenador')->nullable();
        $table->unsignedBigInteger('tournament_id');
        $table->timestamps();

        $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
    });
}


    public function down(): void {
        Schema::dropIfExists('teams');
    }
};
