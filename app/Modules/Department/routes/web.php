<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Department\Http\Controllers\DepartmentController;

Route::group(['module' => 'Department', 'prefix' => 'department', 'middleware' => ['auth', 'checkAdmin']], function () {

    Route::get('list', [DepartmentController::class, 'list'])->name('department-list');
    Route::get('add', [DepartmentController::class, 'add'])->name('department-add');
    Route::post('store', [DepartmentController::class, 'store'])->name('department-store');
    Route::post('get-list', [DepartmentController::class, 'getList'])->name('department-get-list');
    Route::get('edit/{id}', [DepartmentController::class, 'edit'])->name('department-edit');
});

