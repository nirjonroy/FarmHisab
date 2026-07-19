<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\DashboardController;
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

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/coming-soon/{module}', ComingSoonController::class)->name('coming-soon');

    Route::prefix('admin')->name('admin.')->middleware('permission:users.view')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.update')->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.update')->name('users.update');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:users.activate')->name('users.toggle-status');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');
    });
});
