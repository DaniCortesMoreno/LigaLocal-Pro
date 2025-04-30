<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'nombre',
        'apellidos',
        'edad',
        'dorsal',
        'posición',
        'estado',
        'foto'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function matchGames()
    {
        return $this->belongsToMany(MatchGame::class, 'player_match_game')
            ->withPivot(['goles', 'asistencias', 'amarillas', 'rojas', 'partidos_jugados'])
            ->withTimestamps();
    }

    public function mvps()
    {
        return $this->hasMany(MatchGame::class, 'mvp_id');
    }

}
