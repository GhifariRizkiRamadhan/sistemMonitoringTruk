<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataTrukController;
use App\Http\Controllers\ShipmentsController;
use App\Http\Controllers\OperationalExpensesController;
use App\Http\Controllers\MonthlyCheckController;
use App\Http\Controllers\IncomeReportController;

//Route::get('/test-connection', [TestController::class, 'testConnection']);

Route::get('/', function () {
    return view('login');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route untuk Data Truk tanpa middleware auth
Route::resource('trucks', DataTrukController::class);
Route::get('dataTruk', [DataTrukController::class, 'index'])->name('dataTruk');

Route::resource('shipments', ShipmentsController::class);
Route::get('catatanAngkutan', [ShipmentsController::class, 'index'])->name('catatanAngkutan');

// Route untuk Catatan Operasional
Route::resource('operational-expenses', OperationalExpensesController::class);
Route::get('catatanOperasional', [OperationalExpensesController::class, 'index'])->name('catatanOperasional');

//pengecekan bulanan
Route::resource('monthly-checks', MonthlyCheckController::class);
Route::get('pengecekanBulanan', [MonthlyCheckController::class, 'index'])->name('pengecekanBulanan');

//laporan
Route::get('reports/income', [IncomeReportController::class, 'index'])->name('incomeReport');