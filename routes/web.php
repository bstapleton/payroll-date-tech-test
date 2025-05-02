<?php

use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');
Route::post('/payroll', [PayrollController::class, 'show'])->name('show');
