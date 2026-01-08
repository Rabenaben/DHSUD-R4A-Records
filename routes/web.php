<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\HoaController;
use App\Http\Controllers\RemController;

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
        Route::get('/archive', 'archivedDashboard')->name('archive');
        Route::get('/rem/folder/{province}', 'loadFolder')->name('folder.load');
    });

    // Archive routes (ArchiveController)
    Route::controller(ArchiveController::class)
        ->prefix('records')
        ->name('records.')
        ->group(function () {
            Route::patch('/{type}/{docketNo}/files/{fileIndex}/archive', 'archiveFile');
            Route::patch('/{type}/{docketNo}/files/{fileIndex}/unarchive', 'unarchiveFile');
        });

    // Borrower routes (BorrowerController)
    Route::controller(BorrowerController::class)->group(function () {
        Route::get('/borrowers/{id}', 'showBorrower')->name('borrowers.show');
        Route::get('/borrowers/history/{borrowerName}', 'getBorrowerHistory')->name('borrowers.history');
        Route::post('/borrowers', 'storeBorrower')->name('borrowers.store');
        Route::patch('/borrowers/{id}', 'updateBorrower')->name('borrowers.update');
        Route::patch('/borrowers/{id}/return', 'updateReturnedDate')->name('borrowers.update.return');
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

    // HOA routes (HoaController)
    Route::controller(HoaController::class)->prefix('hoa')->name('hoa.')->group(function () {
        Route::get('/municipalities', 'getMunicipalities')->name('municipalities');
        Route::get('/updated-data', 'getUpdatedData')->name('updated-data');
        Route::post('/', 'store')->name('store');
        Route::get('/{docketNo}/files', 'getFiles')->name('files');
        Route::post('/{docketNo}/upload-file', 'uploadFile')->name('upload-file');
        Route::get('/{docketNo}/download/{fileIndex}', 'downloadFile')->name('download-file');
        Route::get('/{docketNo}/preview/{fileIndex}', 'previewFile')->name('preview-file');
    });

    // REM routes (RemController)
    Route::controller(RemController::class)->prefix('rem')->name('rem.')->group(function () {
        Route::get('/{docketNo}/files', 'getFiles')->name('files');
        Route::post('/{docketNo}/upload-file', 'uploadFile')->name('upload-file');
        Route::get('/{docketNo}/download/{fileIndex}', 'downloadFile')->name('download-file');
        Route::get('/{docketNo}/preview/{fileIndex}', 'previewFile')->name('preview-file');
    });
});

require __DIR__ . '/auth.php';
