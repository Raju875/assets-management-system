<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Custom Authentication Routes...
Route::get('admin-login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admin_login'); // Admin Login Routes...

//Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login'); // Login Routes...
//Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']); // Login Routes...
//
//Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']); // Logout Routes...
//
//Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register'); // Register Routes...
//Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']); // Register Routes...


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
