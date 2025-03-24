<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Tournament;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $tournaments = Tournament::all();

        if ($tournaments->isEmpty()) {
            $this->command->warn('❗ No hay torneos para asignar equipos.');
            return;
        }

        foreach ($tournaments as $tournament) {
            for ($i = 1; $i <= 4; $i++) {
                Team::create([
                    'nombre' => 'Equipo ' . $i . ' - ' . $tournament->nombre,
                    'logo' => null,
                    'numero_jugadores' => 0,
                    'color_equipacion' => 'Rojo',
                    'entrenador' => 'Entrenador ' . $i,
                    'tournament_id' => $tournament->id,
                ]);
            }
        }
    }
}
