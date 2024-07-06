<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BedController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/index', [BedController::class, 'index'])->name('Keuangan.index');
