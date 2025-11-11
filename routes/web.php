<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisplayController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DisplayController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/rem_records', [DisplayController::class, 'remDashboard'])->middleware(['auth', 'verified'])->name('rem_records');
Route::get('/hoa_records', [DisplayController::class, 'HoaDashboard'])->middleware(['auth', 'verified'])->name('hoa_records');

Route::get('/{theme}/folder/{province}', [DisplayController::class, 'loadFolder'])
        ->name('folder.load');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
