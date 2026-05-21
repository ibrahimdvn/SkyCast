<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteCityController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', [WeatherController::class, 'index'])->name('weather.index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/favorites/toggle', [FavoriteCityController::class, 'toggle'])->name('favorites.toggle');
});
