<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\UserController;

// 🔹 Public route
Route::get('/', fn() => view('welcome'));

// 🔹 Authenticated routes with back history prevention
Route::middleware(['auth', 'prevent.back.history'])->group(function () {

    // Dashboard routes (DisplayController)
    Route::controller(DisplayController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/rem_records', 'remDashboard')->name('rem_records');
        Route::get('/hoa_records', 'hoaDashboard')->name('hoa_records');
        Route::get('/borrowers', 'borrowerDashboard')->name('borrowers');
        Route::get('/rem/folder/{province}', 'loadFolder')->name('folder.load');
    });

    // User management routes (UserController)
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::patch('/{id}/archive', 'archive')->name('archive');
        Route::patch('/{id}/unarchive', 'unarchive')->name('unarchive');
        Route::patch('/{id}', 'update')->name('update');
    });

    // Accounts route
    Route::get('/accounts', [UserController::class, 'index'])->name('accounts');

    // Profile routes (ProfileController)
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });
});

require __DIR__ . '/auth.php';
