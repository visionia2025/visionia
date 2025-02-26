<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ReconocimientoController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::put('/user/update', [AuthController::class, 'update']);
Route::post('/reconocimiento', [ReconocimientoController::class, 'registrarReconocimiento']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'userInfo']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::get('/get-token', [AuthController::class, 'generateToken']);
Route::post('/password/request-reset', [PasswordResetController::class, 'requestReset']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
