<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Asset\Http\Controllers\AssetController;
use App\Modules\Asset\Http\Controllers\CategoryController;

Route::group(['module' => 'Asset', 'prefix' => 'asset', 'middleware' => ['auth', 'checkAdmin']], function () {
    /* category */
    Route::get('category-list', [CategoryController::class, 'categoryList'])->name('asset-category-list');
    Route::get('category-add', [CategoryController::class, 'categoryAdd'])->name('asset-category-add');
    Route::post('category-store', [CategoryController::class, 'categoryStore'])->name('asset-category-store');
    Route::post('category-get-list', [CategoryController::class, 'categoryGetList'])->name('asset-category-get-list');
    Route::get('category-edit/{id}', [CategoryController::class, 'categoryEdit'])->name('asset-category-edit');

    /* asset */
    Route::get('list', [AssetController::class, 'list'])->name('asset-list');
    Route::get('add', [AssetController::class, 'add'])->name('asset-add');
    Route::post('get-sub-category-by-category', [AssetController::class, 'subCategoryByCategory'])->name('asset-sub-category-by-category');
    Route::post('store', [AssetController::class, 'store'])->name('asset-store');
    Route::post('get-list', [AssetController::class, 'getList'])->name('asset-get-list');
    Route::get('edit/{id}', [AssetController::class, 'edit'])->name('asset-edit');
});

