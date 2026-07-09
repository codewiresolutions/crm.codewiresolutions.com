<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', [ContactController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/whatsapp', [ContactController::class, 'whatsapp'])->name('admin.whatsapp');
Route::post('/admin/whatsapp', [ContactController::class, 'storeMessage'])->name('admin.whatsapp.store');
Route::get('/admin/whatsapp/{message}/edit', [ContactController::class, 'editMessage'])->name('admin.whatsapp.edit');
Route::put('/admin/whatsapp/{message}', [ContactController::class, 'updateMessage'])->name('admin.whatsapp.update');
Route::delete('/admin/whatsapp/{message}', [ContactController::class, 'destroyMessage'])->name('admin.whatsapp.destroy');
Route::get('/admin/customers', [ContactController::class, 'index'])->name('admin.customers.index');
Route::post('/admin/customers', [ContactController::class, 'store'])->name('admin.customers.store');
Route::post('/admin/customers/send-whatsapp', [ContactController::class, 'sendWhatsapp'])->name('admin.customers.send-whatsapp');
Route::get('/admin/customers/{contact}/edit', [ContactController::class, 'edit'])->name('admin.customers.edit');
Route::put('/admin/customers/{contact}', [ContactController::class, 'update'])->name('admin.customers.update');
Route::delete('/admin/customers/{contact}', [ContactController::class, 'destroy'])->name('admin.customers.destroy');
