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
use App\Http\Controllers\GeneratePdfController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\KasirController;

Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Google OAuth
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// OTP Verification
Route::get('/otp', [AuthController::class, 'showOtpForm'])->name('otp.form');
Route::post('/otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (protected)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth','session.user']);

// Category routes
Route::get('/categories', [CategoryController::class, 'index'])->middleware(['auth','session.user']);
Route::get('/categories/create', [CategoryController::class, 'create'])->middleware(['auth','session.user']);
Route::post('/categories', [CategoryController::class, 'store'])->middleware(['auth','session.user']);
Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->middleware(['auth','session.user']);
Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware(['auth','session.user']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware(['auth','session.user']);

// Book routes
Route::get('/books', [BookController::class, 'index'])->middleware(['auth','session.user']);
Route::get('/books/create', [BookController::class, 'create'])->middleware(['auth','session.user']);
Route::post('/books', [BookController::class, 'store'])->middleware(['auth','session.user']);
Route::get('/books/{book}/edit', [BookController::class, 'edit'])->middleware(['auth','session.user']);
Route::put('/books/{book}', [BookController::class, 'update'])->middleware(['auth','session.user']);
Route::delete('/books/{book}', [BookController::class, 'destroy'])->middleware(['auth','session.user']);

// PDF generator (legacy single-page endpoints)
// Route::get('/generate-pdf', [GeneratePdfController::class, 'index'])
//     ->middleware(['auth','session.user'])
//     ->name('pdf.generate');
// Route::post('/generate-pdf', [GeneratePdfController::class, 'generate'])->middleware(['auth','session.user']);
Route::get('/generate-pdf/download/{type}', [GeneratePdfController::class, 'download'])
    ->whereIn('type', ['certificate', 'invitation'])
    ->middleware(['auth','session.user']);

// Separate pages for certificate and invitation
Route::get('/generate-pdf/certificate', [GeneratePdfController::class, 'certificate'])
    ->middleware(['auth','session.user'])
    ->name('pdf.certificate');
Route::post('/generate-pdf/certificate', [GeneratePdfController::class, 'generateCertificate'])
    ->middleware(['auth','session.user']);

Route::get('/generate-pdf/invitation', [GeneratePdfController::class, 'invitation'])
    ->middleware(['auth','session.user'])
    ->name('pdf.invitation');
Route::post('/generate-pdf/invitation', [GeneratePdfController::class, 'generateInvitation'])
    ->middleware(['auth','session.user']);

Route::get('/barang', [BarangController::class, 'index'])->middleware(['auth','session.user']);
Route::get('/barang/create', [BarangController::class, 'create'])->middleware(['auth','session.user']);
Route::post('/barang', [BarangController::class, 'store'])->middleware(['auth','session.user']);
Route::get('/barang/{id_barang}/edit', [BarangController::class, 'edit'])->middleware(['auth','session.user']);
Route::put('/barang/{id_barang}', [BarangController::class, 'update'])->middleware(['auth','session.user']);
Route::delete('/barang/{id_barang}', [BarangController::class, 'destroy'])->middleware(['auth','session.user']);
Route::post('/cetak-label', [BarangController::class, 'cetak'])->middleware(['auth','session.user']);
Route::get('/customer', [CustomerController::class, 'index'])->middleware(['auth','session.user'])->name('customer.index');
Route::get('/customer/tambah-1', [CustomerController::class, 'createBlob'])->middleware(['auth','session.user'])->name('customer.create.blob');
Route::post('/customer/tambah-1', [CustomerController::class, 'storeBlob'])->middleware(['auth','session.user'])->name('customer.store.blob');
Route::get('/customer/tambah-2', [CustomerController::class, 'createPath'])->middleware(['auth','session.user'])->name('customer.create.path');
Route::post('/customer/tambah-2', [CustomerController::class, 'storePath'])->middleware(['auth','session.user'])->name('customer.store.path');
Route::get('/api/wilayah/kodepos', [CustomerController::class, 'postalCodes'])->middleware(['auth','session.user'])->name('api.wilayah.kodepos');

Route::get('/tugas-js', fn () => view('tugas-js'))->middleware(['auth','session.user'])->name('tugas.js');
Route::get('/wilayah-indonesia', [WilayahController::class, 'index'])
    ->middleware(['auth','session.user'])
    ->name('wilayah.index');

Route::get('/kasir', [KasirController::class, 'index'])
    ->middleware(['auth','session.user'])
    ->name('kasir.index');
Route::get('/kasir/laporan', [KasirController::class, 'laporan'])
    ->middleware(['auth','session.user'])
    ->name('kasir.laporan');
Route::get('/kasir/struk/{id}', [KasirController::class, 'struk'])
    ->middleware(['auth','session.user'])
    ->name('kasir.struk');
Route::get('/kasir/success/{id}', [KasirController::class, 'success'])
    ->middleware(['auth','session.user'])
    ->name('kasir.success');
Route::get('/api/barang/{kode}', [KasirController::class, 'cariBarang'])
    ->middleware(['auth','session.user'])
    ->name('kasir.cari-barang');
Route::get('/api/barang-search', [KasirController::class, 'cariKode'])
    ->middleware(['auth','session.user'])
    ->name('kasir.cari-kode');
Route::post('/penjualan/store', [KasirController::class, 'storeTransaksi'])
    ->middleware(['auth','session.user'])
    ->name('kasir.store');
