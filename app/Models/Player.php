<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellidos',
        'edad',
        'dorsal',
        'posición',
        'estado',
        'goles',
        'asistencias',
        'amarillas',
        'rojas',
        'cantidad_partidos',
        'foto',
        'team_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
