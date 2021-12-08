<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Http\Controllers\DashboardController;

Route::group(['module' => 'Dashboard', 'prefix' => 'dashboard', 'middleware' => ['auth', 'checkAdmin']], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
