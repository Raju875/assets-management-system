<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Allocate\Http\Controllers\AllocateController;

Route::group(['module' => 'Allocate', 'prefix' => 'allocate', 'middleware' => ['auth', 'checkAdmin']], function () {

    Route::get('list', [AllocateController::class, 'list'])->name('allocate-list');
    Route::post('get-list', [AllocateController::class, 'getList'])->name('allocate-get-list');

    // asset assign
    Route::get('assign-asset', [AllocateController::class, 'assignAsset'])->name('allocate-assign-asset');
    Route::post('get-user-by-department', [AllocateController::class, 'getUserByDepartment'])->name('allocate-user-by-department');
    Route::post('allocate-assign-asset-store', [AllocateController::class, 'assignAssetStore'])->name('allocate-assign-asset-store');
});
