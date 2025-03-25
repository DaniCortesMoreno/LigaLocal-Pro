<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index() {
        return response()->json([
            'success' => true,
            'data' => User::all()
        ]);
    }

    public function show($id) {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        // Solo el mismo usuario o admin puede editar
        if ($request->user()->id !== $user->id && $request->user()->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string',
            'apellidos' => 'sometimes|string',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|min:6',
        ]);

        $user->update($request->only('name', 'apellidos', 'email'));

        if ($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Usuario actualizado correctamente.'
        ]);
    }

    public function destroy(Request $request, $id) {
        $user = User::findOrFail($id);
    
        // Solo el mismo usuario o admin puede eliminar
        if ($request->user()->id !== $user->id && $request->user()->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    
        $user->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado.'
        ]);
    }
    
}
