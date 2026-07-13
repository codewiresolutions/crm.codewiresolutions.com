<?php

use App\Http\Controllers\Admin\CsvImportController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'active', 'role:admin,manager,user'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ContactController::class, 'dashboard'])->name('dashboard')->middleware('menu:dashboard');

    Route::middleware('menu:whatsapp')->group(function () {
        Route::get('/whatsapp', [ContactController::class, 'whatsapp'])->name('whatsapp');
        Route::post('/whatsapp', [ContactController::class, 'storeMessage'])->name('whatsapp.store');
        Route::get('/whatsapp/{message}/edit', [ContactController::class, 'editMessage'])->name('whatsapp.edit');
        Route::put('/whatsapp/{message}', [ContactController::class, 'updateMessage'])->name('whatsapp.update');
        Route::delete('/whatsapp/{message}', [ContactController::class, 'destroyMessage'])->name('whatsapp.destroy');
    });

    Route::middleware('menu:customers')->group(function () {
        Route::get('/customers', [ContactController::class, 'index'])->name('customers.index');
        Route::post('/customers', [ContactController::class, 'store'])->name('customers.store');
        Route::post('/customers/send-whatsapp', [ContactController::class, 'sendWhatsapp'])->name('customers.send-whatsapp');
        Route::get('/customers/{contact}/edit', [ContactController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{contact}', [ContactController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{contact}', [ContactController::class, 'destroy'])->name('customers.destroy');
        Route::patch('/customers/{contact}/selected-message', [ContactController::class, 'updateSelectedMessage'])->name('customers.update-selected-message');
    });

    Route::middleware('menu:csv')->group(function () {
        Route::get('/csv', [CsvImportController::class, 'index'])->name('csv.index');
        Route::post('/csv', [CsvImportController::class, 'store'])->name('csv.store');
        Route::get('/csv/{csvImport}/download', [CsvImportController::class, 'download'])->name('csv.download');
    });
});

Route::middleware(['auth', 'active', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
});

Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
});

Route::middleware(['auth', 'active', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/permissions', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions', [PermissionController::class, 'update'])->name('permissions.update');
});
