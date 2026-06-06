<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/hallo', function () {
    return 'Hallo, ini Halaman pertama ku';
});
use App\Http\Controllers\HalamanController;
use App\Http\Controllers\AuthController;

route::get('/halaman',[HalamanController::class,'index']);

// =============================================
// Routes untuk Authentication (Login & Register)
// =============================================

// Halaman Login (GET = tampilkan form, POST = proses login)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Halaman Register (GET = tampilkan form, POST = proses daftar)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Dashboard (dilindungi Auth::check di controller)
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

// Logout (POST untuk keamanan CSRF)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');