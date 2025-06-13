<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Books');
})->name('home');

Route::get('/authors', function () {
    return Inertia::render('Authors');
})->name('authors');    

