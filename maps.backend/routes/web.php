<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;


Route::middleware('web')->get('/api/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')
     ->middleware('api')
     ->namespace('App\Http\Controllers')
     ->group(base_path('routes/api.php'));

     Route::get('/testssss', function() {
        return response()->json(['message' => 'Маршрут работает!']);
    });
    

