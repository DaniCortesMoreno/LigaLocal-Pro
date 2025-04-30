<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Team;
use Faker\Factory as Faker;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $teams = Team::all();

        if ($teams->isEmpty()) {
            $this->command->warn('❗ No hay equipos para asignar jugadores.');
            return;
        }

        foreach ($teams as $team) {
            for ($i = 1; $i <= 10; $i++) {
                Player::create([
                    'nombre' => $faker->firstName,
                    'apellidos' => $faker->lastName,
                    'edad' => rand(18, 40),
                    'dorsal' => $i,
                    'posición' => $faker->randomElement(['portero', 'defensa', 'centrocampista', 'delantero']),
                    'estado' => 'activo',
                    'partidos_jugados' => rand(0, 0),
                    'goles' => rand(0, 0),
                    'asistencias' => rand(0, 0),
                    'amarillas' => rand(0, 0),
                    'rojas' => rand(0, 0),
                    'cantidad_partidos' => rand(0, 0),
                    'foto' => null,
                    'team_id' => $team->id,
                ]);
            }

            // Actualizar número de jugadores del equipo
            $team->save();
        }
    }
}
