<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            $result['token'] = $authUser->createToken('MyAuthApp')->plainTextToken;
            $result['user'] = [
                'nombre' => $authUser->nombre,
                'apellidos' => $authUser->apellidos,
                'email' => $authUser->email,
                'rol' => $authUser->rol,
                'id' => $authUser->id,
            ];


            return $this->sendResponse($result, 'User signed in');
        }
        return $this->sendError('Unauthorised.', ['error' => 'incorrect Email/Password']);
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'nombre' => $validated['nombre'],
            'apellidos' => $validated['apellidos'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'fecha_registro' => now(),
            'ultimo_login' => now(),
            'rol' => 'usuario'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => $user // <- Aquí retornas el objeto completo del usuario
            ]
        ]);
    }

    public function logout(Request $request)
    {

        $user = request()->user(); //or Auth::user()
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        $success['nombre'] = $user->nombre;
        return $this->sendResponse($success, 'User successfully signed out.');
    }

}