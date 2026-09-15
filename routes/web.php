<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountSharingController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authenticated user routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('accounts', AccountController::class);

    // Account sharing (N-N pivot CRUD)
    Route::get('/accounts/{account}/sharing', [AccountSharingController::class, 'index'])->name('accounts.sharing.index');
    Route::post('/accounts/{account}/sharing', [AccountSharingController::class, 'store'])->name('accounts.sharing.store');
    Route::patch('/accounts/{account}/sharing/{user}', [AccountSharingController::class, 'update'])->name('accounts.sharing.update');
    Route::delete('/accounts/{account}/sharing/{user}', [AccountSharingController::class, 'destroy'])->name('accounts.sharing.destroy');

    Route::resource('transactions', TransactionController::class)->except(['show']);
    Route::resource('budgets', BudgetController::class)->except(['show']);

    Route::get('/reports', ReportController::class)->name('reports');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('transactions', AdminTransactionController::class)->only(['index', 'show']);
});

require __DIR__.'/auth.php';
