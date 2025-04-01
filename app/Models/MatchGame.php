<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'torneo_id',
        'equipo1_id',
        'equipo2_id',
        'fecha_partido',
        'goles_equipo1',
        'goles_equipo2',
        'estado_partido',
        'arbitro',
    ];


    public function torneo()
    {
        return $this->belongsTo(Tournament::class, 'torneo_id');
    }

    public function equipo1()
    {
        return $this->belongsTo(Team::class, 'equipo1_id');
    }

    public function equipo2()
    {
        return $this->belongsTo(Team::class, 'equipo2_id');
    }


}
