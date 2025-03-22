<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovieSearchController;

// Main Page Route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Authentication
Route::get('/login', [LoginBasic::class, 'index'])->name('login');
Route::post('/login-process', [LoginBasic::class, 'login'])->name('login-process');
Route::get('/register', [RegisterBasic::class, 'index'])->name('register');
Route::post('/register-process', [RegisterBasic::class, 'register'])->name('register-process');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginBasic::class, 'logout'])->name('logout');
    // movie search
    Route::middleware(['auth'])->group(function () {
        Route::get('/search', [MovieSearchController::class, 'index'])->name('search.index');
        Route::post('/search', [MovieSearchController::class, 'search'])->name('search.movies');
        Route::post('/favorites/add', [MovieSearchController::class, 'addToFavorites'])->name('add.favorite');
        Route::delete('/favorites/{movie}', [MovieSearchController::class, 'removeFavorite'])->name('remove.favorite');
    });
});
