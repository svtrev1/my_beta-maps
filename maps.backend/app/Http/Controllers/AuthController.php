<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Вход
    public function login(Request $request)
{
    \Log::info('Login attempt', $request->all());
    
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        \Log::warning('Failed login attempt for email: ' . $request->email);
        return response()->json(['message' => 'Неверные учетные данные'], 401);
    }

    $token = $user->createToken('auth-token')->plainTextToken;
    
    \Log::info('User logged in', ['user_id' => $user->id]);

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]
    ]);
}

    // Проверка пользователя (например, при загрузке сайта)
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // Выход
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Выход выполнен']);
    }
}
