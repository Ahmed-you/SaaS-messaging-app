<?php

use App\Http\Controllers\AdminCompanyController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MessageController::class, 'index'])->name('messages.index');
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
Route::patch('/admin/companies/{company}/status', [AdminCompanyController::class, 'updateStatus'])->name('admin.companies.status');
Route::patch('/admin/companies/{company}/modules', [AdminCompanyController::class, 'updateModules'])->name('admin.companies.modules');
