<?php

namespace App\Http\Controllers;

use DB;
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
    
        // Создаем сессию Laravel
        Auth::login($user);
    
        // Генерируем токен Sanctum
        $token = $user->createToken('auth-token')->plainTextToken;
        
        \Log::info('User logged in', ['user_id' => $user->id]);
    
        return response()->json([
            'token' => $token,
            'user' => $user->only('id', 'name', 'email')
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
        try {
            \Log::info('Attempting logout', ['user_id' => $request->user()->id]);

            // Завершаем сессию
            Auth::logout();

            $tokens = $request->user()->tokens;
            \Log::info('Tokens to delete:', ['tokens' => $tokens]);

            // Удаляем токены
            $request->user()->tokens()->delete();
            
            \Log::info('Logout successful', ['user_id' => $request->user()->id]);
            
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            \Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error'], 500);
        }
    }

}
