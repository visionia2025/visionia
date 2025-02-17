<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::post('/custom-login', [AuthController::class, 'loginWeb'])->name('login.submit');

Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

