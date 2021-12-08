<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\UserController;

Route::group(['module' => 'User', 'prefix' => 'user', 'middleware' => ['auth', 'checkAdmin']], function () {

    Route::get('list', [UserController::class, 'list'])->name('user-list');
    Route::get('add', [UserController::class, 'add'])->name('user-add');
    Route::post('store', [UserController::class, 'store'])->name('user-store');
    Route::get('get-list', [UserController::class, 'getList'])->name('user-get-list');
    Route::get('edit/{id}', [UserController::class, 'edit'])->name('user-edit');

});
