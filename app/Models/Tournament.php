<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tournament extends Model
{
    protected $fillable = [
        'nombre',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'cantidad_equipos',
        'cantidad_jugadores',
        'estado',
        'formato',
        'reglamento',
        'user_id',
        'visibilidad'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function matches()
    {
        return $this->hasMany(MatchGame::class);
    }

}
