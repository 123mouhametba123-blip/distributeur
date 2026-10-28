<?php

use App\Http\Controllers\{AuthController, DistributeurController, ClientController};
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/register');


Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth', 'role:distributeur'])->prefix('distributeur')->group(function () {
    Route::get('/', [DistributeurController::class, 'index']);
    Route::post('/clients', [DistributeurController::class, 'inscrireClient']);
    Route::post('/credit', [DistributeurController::class, 'crediter']);
});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard']);
    Route::post('/transfert', [ClientController::class, 'transferer']);
});

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);