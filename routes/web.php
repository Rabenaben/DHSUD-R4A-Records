<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisplayController;

// 🔹 Public route
Route::get('/', fn() => view('welcome'));

// 🔹 Authenticated routes with back history prevention
Route::middleware(['auth', 'prevent.back.history'])->group(function () {

    // Dashboard routes (DisplayController)
    Route::controller(DisplayController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/rem_records', 'remDashboard')->name('rem_records');
        Route::get('/hoa_records', 'hoaDashboard')->name('hoa_records');
        Route::get('/accounts', fn() => view('accounts'))->name('accounts');
        Route::get('/{theme}/folder/{province}', 'loadFolder')->name('folder.load');
    });

    // Profile routes (ProfileController)
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });
});

require __DIR__ . '/auth.php';
