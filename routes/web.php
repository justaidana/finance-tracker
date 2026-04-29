<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Locale switcher (public)
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Static advice page (public if you wish, but gated for personalisation)
Route::middleware(['auth', 'set.locale'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::get('/transactions',           [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions',          [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}',      [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}',   [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/transactions/export',    [TransactionController::class, 'export'])->name('transactions.export');

    // Budgets
    Route::get('/budgets',  [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Savings Goals (resource named 'savings')
    Route::get('/savings',                     [SavingsGoalController::class, 'index'])->name('savings.index');
    Route::post('/savings',                    [SavingsGoalController::class, 'store'])->name('savings.store');
    Route::put('/savings/{saving}',            [SavingsGoalController::class, 'update'])->name('savings.update');
    Route::delete('/savings/{saving}',         [SavingsGoalController::class, 'destroy'])->name('savings.destroy');
    Route::post('/savings/{saving}/add',       [SavingsGoalController::class, 'addFunds'])->name('savings.add');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Advice (static page)
    Route::view('/advice', 'advice')->name('advice');

    // Categories
    Route::get('/categories',           [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories',          [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}',[CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}',[CategoryController::class, 'destroy'])->name('categories.destroy');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',    [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
