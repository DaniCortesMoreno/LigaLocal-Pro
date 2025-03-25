<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear torneos de ejemplo
        Tournament::create([
            'nombre' => 'Liga Primavera Fútbol 7',
            'tipo' => 'futbol7',
            'fecha_inicio' => Carbon::now()->addDays(7),
            'fecha_fin' => Carbon::now()->addMonths(2),
            'cantidad_equipos' => 8,
            'cantidad_jugadores' => 12,
            'estado' => 'pendiente',
            'formato' => 'liguilla',
            'reglamento' => 'Todos contra todos a una vuelta. Puntos: 3-1-0.',
            'user_id' => 2,
            'visibilidad' => 'publico',
        ]);

        Tournament::create([
            'nombre' => 'Torneo Relámpago Fútbol Sala',
            'tipo' => 'sala',
            'fecha_inicio' => Carbon::now()->addDays(3),
            'fecha_fin' => Carbon::now()->addDays(10),
            'cantidad_equipos' => 6,
            'cantidad_jugadores' => 10,
            'estado' => 'pendiente',
            'formato' => 'eliminacion',
            'reglamento' => 'Eliminación directa con partidos únicos.',
            'user_id' => 2,
            'visibilidad' => 'publico',
        ]);

        Tournament::create([
            'nombre' => 'Liga Invernal Fútbol 11',
            'tipo' => 'futbol11',
            'fecha_inicio' => Carbon::now()->addDays(7),
            'fecha_fin' => Carbon::now()->addMonths(2),
            'cantidad_equipos' => 8,
            'cantidad_jugadores' => 12,
            'estado' => 'pendiente',
            'formato' => 'liguilla',
            'reglamento' => 'Todos contra todos a doble vuelta. Puntos: 3-1-0.',
            'user_id' => 3,
            'visibilidad' => 'privado',
        ]);
    }
}
