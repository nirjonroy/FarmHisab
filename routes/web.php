<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FarmCategoryController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FarmVarietyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\ShedController;
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

Route::post('/language/{locale}', LanguageController::class)->name('language.switch');

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

    Route::prefix('farms')->name('farms.')->group(function () {
        Route::get('/', [FarmController::class, 'index'])->middleware('permission:farms.view')->name('index');
        Route::get('/create', [FarmController::class, 'create'])->middleware('permission:farms.manage')->name('create');
        Route::post('/', [FarmController::class, 'store'])->middleware('permission:farms.manage')->name('store');
        Route::get('/{farm}/edit', [FarmController::class, 'edit'])->middleware('permission:farms.manage')->name('edit');
        Route::put('/{farm}', [FarmController::class, 'update'])->middleware('permission:farms.manage')->name('update');
    });

    Route::prefix('sheds')->name('sheds.')->group(function () {
        Route::get('/', [ShedController::class, 'index'])->middleware('permission:farms.view')->name('index');
        Route::get('/create', [ShedController::class, 'create'])->middleware('permission:farms.manage')->name('create');
        Route::post('/', [ShedController::class, 'store'])->middleware('permission:farms.manage')->name('store');
        Route::get('/{shed}/edit', [ShedController::class, 'edit'])->middleware('permission:farms.manage')->name('edit');
        Route::put('/{shed}', [ShedController::class, 'update'])->middleware('permission:farms.manage')->name('update');
    });

    Route::prefix('farm-categories')->name('farm-categories.')->group(function () {
        Route::get('/', [FarmCategoryController::class, 'index'])->middleware('permission:farm-categories.view')->name('index');
        Route::get('/create', [FarmCategoryController::class, 'create'])->middleware('permission:farm-categories.manage')->name('create');
        Route::post('/', [FarmCategoryController::class, 'store'])->middleware('permission:farm-categories.manage')->name('store');
        Route::get('/{farmCategory}/edit', [FarmCategoryController::class, 'edit'])->middleware('permission:farm-categories.manage')->name('edit');
        Route::put('/{farmCategory}', [FarmCategoryController::class, 'update'])->middleware('permission:farm-categories.manage')->name('update');
    });

    Route::prefix('farm-varieties')->name('farm-varieties.')->group(function () {
        Route::get('/', [FarmVarietyController::class, 'index'])->middleware('permission:farm-varieties.view')->name('index');
        Route::get('/create', [FarmVarietyController::class, 'create'])->middleware('permission:farm-varieties.manage')->name('create');
        Route::post('/', [FarmVarietyController::class, 'store'])->middleware('permission:farm-varieties.manage')->name('store');
        Route::get('/{farmVariety}/edit', [FarmVarietyController::class, 'edit'])->middleware('permission:farm-varieties.manage')->name('edit');
        Route::put('/{farmVariety}', [FarmVarietyController::class, 'update'])->middleware('permission:farm-varieties.manage')->name('update');
    });

    Route::prefix('measurement-units')->name('measurement-units.')->group(function () {
        Route::get('/', [MeasurementUnitController::class, 'index'])->middleware('permission:measurement-units.view')->name('index');
        Route::get('/create', [MeasurementUnitController::class, 'create'])->middleware('permission:measurement-units.manage')->name('create');
        Route::post('/', [MeasurementUnitController::class, 'store'])->middleware('permission:measurement-units.manage')->name('store');
        Route::get('/{measurementUnit}/edit', [MeasurementUnitController::class, 'edit'])->middleware('permission:measurement-units.manage')->name('edit');
        Route::put('/{measurementUnit}', [MeasurementUnitController::class, 'update'])->middleware('permission:measurement-units.manage')->name('update');
    });

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
