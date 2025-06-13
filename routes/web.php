<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Books');
})->name('home');

Route::resource('authors', AuthorController::class);