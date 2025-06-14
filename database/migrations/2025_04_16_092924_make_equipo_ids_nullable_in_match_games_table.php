<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('match_games', function (Blueprint $table) {
            Schema::table('match_games', function (Blueprint $table) {
                $table->unsignedBigInteger('equipo1_id')->nullable()->change();
                $table->unsignedBigInteger('equipo2_id')->nullable()->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_games', function (Blueprint $table) {
            
    Schema::table('match_games', function (Blueprint $table) {
        $table->unsignedBigInteger('equipo1_id')->nullable(false)->change();
        $table->unsignedBigInteger('equipo2_id')->nullable(false)->change();
    });
        });
    }
};
