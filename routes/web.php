<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use Inertia\Inertia;

Route::get('/', [BookController::class, 'index'])->name('home');
Route::resource('books', BookController::class);

Route::resource('authors', AuthorController::class);