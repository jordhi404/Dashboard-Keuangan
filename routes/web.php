<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BedController;
use Barryvdh\Debugbar\Facades\Debugbar;

Route::get('/', function () {
    Debugbar::info('Test debugbar');
    return view('welcome');
});

Route::get('/index',[BedController::class, 'index'])->name('Keuangan.index');
