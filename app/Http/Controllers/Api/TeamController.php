<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamController extends Controller
{
    public function players($id)
    {
        $team = Team::with('players')->findOrFail($id);
        return $team->players;
    }
}
