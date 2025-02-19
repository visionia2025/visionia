<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::post('/custom-login', [AuthController::class, 'loginWeb'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

Route::get('/custom-register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/custom-register', [AuthController::class, 'registerWeb'])->name('register.submit');



Route::get('/usuarios', [UserController::class, 'lstUsuarios'])->name('usuarios.lstUsuarios');
Route::get('/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
Route::get('/usuarios/{id}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
Route::get('/usuarios/{id}/permisos', [UserController::class, 'permisos'])->name('usuarios.permisos');
Route::get('/usuarios/{id}/logs', [UserController::class, 'logs'])->name('usuarios.logs');
Route::get('/usuarios/{id}/inactivar', [UserController::class, 'inactivar'])->name('usuarios.inactivar');


Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

