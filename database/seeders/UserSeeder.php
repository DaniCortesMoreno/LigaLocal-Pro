<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nombre' => 'Admin',
                'apellidos' => 'Master',
                'email' => 'admin@ligalocal.com',
                'password' => Hash::make('admin123'),
                'rol' => 'admin',
                'fecha_registro' => Carbon::now(),
                'ultimo_login' => null,
                'remember_token' => Str::random(10),
            ],
            [
                'nombre' => 'Gestor',
                'apellidos' => 'Liga',
                'email' => 'gestor@ligalocal.com',
                'password' => Hash::make('gestor123'),
                'rol' => 'gestor',
                'fecha_registro' => Carbon::now(),
                'ultimo_login' => null,
                'remember_token' => Str::random(10),
            ],
            [
                'nombre' => 'Usuario',
                'apellidos' => 'Normal',
                'email' => 'usuario@ligalocal.com',
                'password' => Hash::make('usuario123'),
                'rol' => 'usuario',
                'fecha_registro' => Carbon::now(),
                'ultimo_login' => null,
                'remember_token' => Str::random(10),
            ]
        ]);
    }
}
