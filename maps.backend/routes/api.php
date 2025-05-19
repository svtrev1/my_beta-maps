<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusStopController;
use App\Http\Controllers\ContractController;

Route::post('/login', [AuthController::class, 'login']);


Route::post('/logout', [AuthController::class, 'logout']);
// ->middleware('auth:sanctum');

Route::post('/addbusstop', [BusStopController::class, 'store']);
// ->middleware('auth:sanctum');

Route::delete('/busstop/{id}', [BusStopController::class, 'destroy']);

Route::put('/busstop/{id}', [BusStopController::class, 'update']);

Route::post('/stops/{id}/contracts', [ContractController::class, 'upload']);
Route::get('/stops/{id}/contracts', [ContractController::class, 'index']);
Route::get('/contracts/{id}/download', [ContractController::class, 'download']);
Route::delete('/contracts/{id}', [ContractController::class, 'destroy']);