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
                'resultado' => null,
                'estado_partido' => 'pendiente',
                'marcador_parcial' => null,
                'arbitro' => 'Carlos Ruiz',
            ]);

            MatchGame::create([
                'torneo_id' => $tournament->id,
                'equipo1_id' => $teams[1]->id,
                'equipo2_id' => $teams[0]->id,
                'fecha_partido' => Carbon::now()->addDays(10),
                'resultado' => '1-1',
                'estado_partido' => 'jugado',
                'marcador_parcial' => '1-0 al descanso',
                'arbitro' => 'Laura Gómez',
            ]);
        } else {
            $this->command->warn('No hay suficientes datos de torneos o equipos para crear partidos.');
        }
    }
}
