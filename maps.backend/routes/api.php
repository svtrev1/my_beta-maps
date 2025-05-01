<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
   
});

Route::post('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF token']);
});


Route::post('/logout', [AuthController::class, 'logout']);
