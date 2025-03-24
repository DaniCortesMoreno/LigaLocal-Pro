<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'logo',
        'numero_jugadores',
        'color_equipacion',
        'entrenador',
        'tournament_id',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function players()
{
    return $this->hasMany(Player::class);
}

}
