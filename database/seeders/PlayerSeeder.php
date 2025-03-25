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
                    'goles' => rand(0, 10),
                    'asistencias' => rand(0, 5),
                    'amarillas' => rand(0, 3),
                    'rojas' => rand(0, 1),
                    'cantidad_partidos' => rand(1, 20),
                    'foto' => null,
                    'team_id' => $team->id,
                ]);
            }

            // Actualizar número de jugadores del equipo
            $team->save();
        }
    }
}
