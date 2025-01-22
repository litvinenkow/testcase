<?php

use Illuminate\Support\Facades\Route;

// заглушка
Route::get('/login', function () {
    return response('This is RestAPI APP, please use "Accept=application/json" header to use it.');
})->name('login');
