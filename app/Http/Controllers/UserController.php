<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $novoUsuario = new User();
        $novoUsuario->name = $request->nome;
        $novoUsuario->email = $request->email;
        $novoUsuario->password = bcrypt($request->password);

        $novoUsuario->save();

        return response()->json([
            'status' => 200,
            'message' => 'Usuário criado com sucesso!',
            'data' => $novoUsuario
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Email ou senha incorretos'
            ],401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 200,
            'message' => 'Token gerado com sucesso',
            'token' => $token,
            'user' => $user,
        ]);
    }
    
}
