<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (protected)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth','session.user']);

// Category routes
Route::get('/categories', [CategoryController::class, 'index'])->middleware(['auth','session.user']);
Route::get('/categories/create', [CategoryController::class, 'create'])->middleware(['auth','session.user']);
Route::post('/categories', [CategoryController::class, 'store'])->middleware(['auth','session.user']);

// Book routes
Route::get('/books', [BookController::class, 'index'])->middleware(['auth','session.user']);
Route::get('/books/create', [BookController::class, 'create'])->middleware(['auth','session.user']);
Route::post('/books', [BookController::class, 'store'])->middleware(['auth','session.user']);
