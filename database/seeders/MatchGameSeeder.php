<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MatchGame;
use App\Models\Tournament;
use App\Models\Team;
use Illuminate\Support\Carbon;

class MatchGameSeeder extends Seeder
{
    public function run(): void
    {
        $tournament = Tournament::first();
        $teams = Team::take(2)->get();

        if ($tournament && $teams->count() === 2) {
            MatchGame::create([
                'torneo_id' => $tournament->id,
                'equipo1_id' => $teams[0]->id,
                'equipo2_id' => $teams[1]->id,
                'fecha_partido' => Carbon::now()->addDays(3),
                'goles_equipo1' => null,
                'goles_equipo2' => null,
                'estado_partido' => 'pendiente',
                'arbitro' => 'Carlos Ruiz',
            ]);

            MatchGame::create([
                'torneo_id' => $tournament->id,
                'equipo1_id' => $teams[1]->id,
                'equipo2_id' => $teams[0]->id,
                'fecha_partido' => Carbon::now()->addDays(10),
                'goles_equipo1' => null,
                'goles_equipo2' => null,
                'estado_partido' => 'jugado',
                'arbitro' => 'Laura Gómez',
            ]);
        } else {
            $this->command->warn('No hay suficientes datos de torneos o equipos para crear partidos.');
        }
    }
}
