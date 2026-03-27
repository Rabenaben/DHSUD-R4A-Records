<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\HoaController;
use App\Http\Controllers\RemController;
use App\Http\Controllers\ClientRequestController;

// 🔹 Public route
Route::get('/', fn() => view('welcome'));

// 🔹 Authenticated routes with back history prevention
Route::middleware(['auth', 'prevent.back.history'])->group(function () {

    // Profile routes (ProfileController)
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // Dashboard routes (DisplayController)
    Route::controller(DisplayController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/dashboard/borrowed-records', 'borrowedRecords')->name('dashboard.borrowed-records');
        Route::get('/rem_records', 'remDashboard')->name('rem_records');
        Route::get('/hoa_records', 'hoaDashboard')->name('hoa_records');
        Route::get('/borrowers', 'borrowerDashboard')->name('borrowers')->middleware('role:Admin');
        Route::get('/request-history', 'requestHistoryDashboard')->name('request-history')->middleware('role:Admin');
        Route::get('/archive', 'archivedDashboard')->name('archive')->middleware('role:Admin');
        Route::get('/rem/folder/{province}', 'loadFolder')->name('folder.load');
        Route::get('/hoa_records/ajax', 'loadHoaRecordsAjax')->name('hoa_records.ajax');
    });

    // User management routes (UserController) - Admin only
    Route::controller(UserController::class)->prefix('users')->name('users.')->middleware('role:Admin')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::patch('/{id}/archive', 'archive')->name('archive');
        Route::patch('/{id}/unarchive', 'unarchive')->name('unarchive');
        Route::patch('/{id}', 'update')->name('update');
    });

    // Accounts route - Admin only
    Route::get('/accounts', [UserController::class, 'index'])->middleware('role:Admin')->name('accounts');

    Route::controller(ActivityController::class)->prefix('activity')->name('activity.')->group(function () {
        Route::get('/logs', 'getLogs')->name('logs');
    });

    // Borrower routes (BorrowerController)
    Route::controller(BorrowerController::class)->group(function () {
        Route::get('/borrowers/{id}', 'showBorrower')->name('borrowers.show');
        Route::get('/borrowers/history/{borrowerName}', 'getBorrowerHistory')->name('borrowers.history');
        Route::post('/borrowers', 'storeBorrower')->name('borrowers.store');
        Route::patch('/borrowers/{id}', 'updateBorrower')->name('borrowers.update');
        Route::patch('/borrowers/{id}/return', 'updateReturnedDate')->name('borrowers.update.return');
    });

// Archive routes (ArchiveController)
    Route::controller(ArchiveController::class)
        ->prefix('records')
        ->name('records.')
        ->group(function () {
            Route::patch('/{type}/{docketNo}/files/{fileIndex}/archive', 'archiveFile');
            Route::patch('/{type}/{docketNo}/files/{fileIndex}/unarchive', 'unarchiveFile');
            Route::get('/{type}/{docketNo}/download/{fileIndex}', 'downloadFile');
        });

    // HOA routes (HoaController)
    Route::controller(HoaController::class)->prefix('hoa')->name('hoa.')->group(function () {
        Route::get('/provinces', 'getProvinces')->name('provinces');
        Route::get('/municipalities', 'getMunicipalities')->name('municipalities');
        Route::get('/updated-data', 'getUpdatedData')->name('updated-data');
        Route::post('/', 'store')->name('store');
        Route::put('/{docketNo}', 'update')->name('update')->where('docketNo', '.*');
        Route::get('/{docketNo}/files', 'getFiles')->name('files')->where('docketNo', '.*');
        Route::patch('/{docketNo}/files/{fileIndex}/rename', 'renameFile')->name('rename-file')->where('docketNo', '.*');
        Route::post('/{docketNo}/upload-file', 'uploadFile')->name('upload-file')->where('docketNo', '.*');
        Route::get('/{docketNo}/download/{fileIndex}', 'downloadFile')->name('download-file')->where('docketNo', '.*');
        Route::get('/{docketNo}/preview/{fileIndex}', 'previewFile')->name('preview-file')->where('docketNo', '.*');
        Route::get('/{docketNo}/export-all-files', 'exportAllFiles')->name('export-all-files')->where('docketNo', '.*');
        Route::get('/export', 'export')->name('export');
        Route::get('/export-sql', 'exportSql')->name('hoa.export-sql');
        Route::get('/export-files', 'exportFiles')->name('hoa.export-files');
    });

    // REM routes (RemController)
    Route::controller(RemController::class)->prefix('rem')->name('rem.')->group(function () {
        Route::get('/provinces', 'getProvinces')->name('provinces');
        Route::get('/municipalities', 'getMunicipalities')->name('municipalities');
        Route::get('/updated-data', 'getUpdatedData')->name('updated-data');
        Route::post('/', 'store')->name('store');
        Route::put('/{docketNo}', 'update')->name('update')->where('docketNo', '.*');
        Route::get('/{docketNo}/files', 'getFiles')->name('files')->where('docketNo', '.*');
        Route::post('/{docketNo}/upload-file', 'uploadFile')->name('upload-file')->where('docketNo', '.*');
        Route::patch('/{docketNo}/files/{fileIndex}/rename', 'renameFile')->name('rename-file')->where('docketNo', '.*');
        Route::get('/{docketNo}/download/{fileIndex}', 'downloadFile')->name('download-file')->where('docketNo', '.*');
        Route::get('/{docketNo}/preview/{fileIndex}', 'previewFile')->name('preview-file')->where('docketNo', '.*');
        Route::get('/{docketNo}/export-all-files', 'exportAllFiles')->name('export-all-files')->where('docketNo', '.*');
        Route::get('/export', 'export')->name('export');
        Route::get('/export-sql', 'exportSql')->name('export-sql');
        Route::get('/export-files', 'exportFiles')->name('rem.export-files');
        Route::get('folder/{province}', 'folder')->name('folder');
    });

// Client Request routes (ClientRequestController)
    Route::controller(ClientRequestController::class)->prefix('client-requests')->name('client-requests.')->group(function () {
        Route::get('/data', 'getData')->name('data');
        Route::get('/dockets', 'getDocketNumbers')->name('dockets');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::get('/search', 'search')->name('search');
    });
});

require __DIR__ . '/auth.php';
