<?php

use App\Http\Controllers\BoardController;
use Illuminate\Support\Facades\Route;

Route::get('/boards', [BoardController::class, 'index']);
Route::post('/boards', [BoardController::class, 'store']);
Route::patch('/boards/{id}', [BoardController::class, 'update']);
Route::delete('/boards/{id}', [BoardController::class, 'destroy']);
Route::put('/boards/{id}/layout', [BoardController::class, 'layout']);
Route::put('/boards/{id}/slots/{index}', [BoardController::class, 'saveSlot'])->whereNumber('index');
Route::delete('/boards/{id}/slots/{index}', [BoardController::class, 'clearSlot'])->whereNumber('index');