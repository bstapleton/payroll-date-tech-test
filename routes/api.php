<?php

use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

Route::post('/', [PayrollController::class, 'check']);
