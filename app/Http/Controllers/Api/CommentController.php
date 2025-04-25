<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MatchGame $match)
    {
        $comentarios = $match->comments()
            ->with('user')
            ->latest()
            ->paginate(10); // o el número que quieras por página

        return response()->json([
            'success' => true,
            'data' => $comentarios
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MatchGame $match)
    {
        $validated = $request->validate([
            'contenido' => 'required|string|max:500',
        ]);

        $comment = $match->comments()->create([
            'user_id' => auth()->id(),
            'contenido' => $validated['contenido'],
        ]);

        return $comment->load('user');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
