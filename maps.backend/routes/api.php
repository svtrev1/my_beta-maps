<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusStopController;

Route::post('/login', [AuthController::class, 'login']);


Route::post('/logout', [AuthController::class, 'logout']);
// ->middleware('auth:sanctum');

Route::post('/addbusstop', [BusStopController::class, 'store']);
// ->middleware('auth:sanctum');

Route::delete('/busstop/{id}', [BusStopController::class, 'destroy']);

Route::put('/busstop/{id}', [BusStopController::class, 'update']);