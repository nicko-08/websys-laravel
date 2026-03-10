<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountActivationController;

// Landing
Route::get('/', fn() => view('pages.welcome'))->name('welcome');

// User
Route::get('/login', fn() => view('pages.auth.login'))->name('login');
Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');
Route::get('/budgets', fn() => view('pages.budget.index'))->name('budgets');
Route::get('/budgets/{id}', fn() => view('pages.budget.details'))->name('budgets.show');
Route::get('/expenses', fn() => view('pages.expense.index'))->name('expenses');
Route::get('/expenses/{id}', fn() => view('pages.expense.details'))->name('expenses.show');
Route::get('/analytics', fn() => view('pages.analytics.index'))->name('analytics');

// Super admin
Route::get('/admin/users', fn() => view('pages.admin.users'))->name('admin.users');
Route::get('/admin/units', fn() => view('pages.admin.units'))->name('admin.units');
Route::get('/admin/fiscal-years', fn() => view('pages.admin.fiscal-years'))->name('admin.fiscal-years');
Route::get('/admin/audit-logs', fn() => view('pages.admin.audit-logs'))->name('admin.audit-logs');
Route::get('/activate-account/{token}', [AccountActivationController::class, 'show'])->name('account.activate.show');
Route::post('/activate-account/{token}', [AccountActivationController::class, 'activate'])->name('account.activate.process');
