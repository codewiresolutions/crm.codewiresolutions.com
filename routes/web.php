<?php

use App\Http\Controllers\Admin\CsvImportController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactGroupController;
use App\Http\Controllers\ResendIntervalController;
use App\Http\Controllers\WhatsappInboxController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
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

    Route::middleware('menu:whatsapp-inbox')->group(function () {
        Route::get('/whatsapp-inbox', [WhatsappInboxController::class, 'index'])->name('whatsapp-inbox');
    });

    Route::middleware('menu:customers')->group(function () {
        Route::get('/customers', [ContactController::class, 'index'])->name('customers.index');
        Route::get('/customers/export', [ContactController::class, 'export'])->name('customers.export');
        Route::get('/customers/export-with-messages', [ContactController::class, 'exportWithMessages'])->name('customers.export-with-messages');
        Route::post('/customers', [ContactController::class, 'store'])->name('customers.store');
        Route::post('/customers/send-whatsapp', [ContactController::class, 'sendWhatsapp'])->name('customers.send-whatsapp');
        Route::post('/customers/bulk-send-whatsapp', [ContactController::class, 'bulkSendWhatsapp'])->name('customers.bulk-send-whatsapp');
        Route::get('/customers/{contact}/edit', [ContactController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{contact}', [ContactController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{contact}', [ContactController::class, 'destroy'])->name('customers.destroy');
        Route::patch('/customers/{contact}/selected-message', [ContactController::class, 'updateSelectedMessage'])->name('customers.update-selected-message');
        Route::patch('/customers/{contact}/toggle-interested', [ContactController::class, 'toggleInterested'])->name('customers.toggle-interested');
        Route::get('/customers/{contact}/messages', [ContactController::class, 'messages'])->name('customers.messages');
        Route::get('/customers/{contact}/whatsapp-chat', [ContactController::class, 'whatsappChat'])->name('customers.whatsapp-chat');

        Route::get('/groups', [ContactGroupController::class, 'index'])->name('groups.index');
        Route::patch('/groups/{group}/selected-message', [ContactGroupController::class, 'updateSelectedMessage'])->name('groups.update-selected-message');
        Route::post('/groups/{group}/send-whatsapp', [ContactGroupController::class, 'sendWhatsapp'])->name('groups.send-whatsapp');
        Route::post('/groups/{group}/schedule-resend', [ContactGroupController::class, 'scheduleResend'])->name('groups.schedule-resend');
        Route::delete('/groups/scheduled-resends/{resend}', [ContactGroupController::class, 'cancelScheduledResend'])->name('groups.cancel-scheduled-resend');

        Route::post('/resend-intervals', [ResendIntervalController::class, 'store'])->name('resend-intervals.store');
        Route::delete('/resend-intervals/{resendInterval}', [ResendIntervalController::class, 'destroy'])->name('resend-intervals.destroy');
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
