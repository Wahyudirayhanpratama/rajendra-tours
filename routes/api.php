<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;

Route::post('/midtrans/callback', [MidtransController::class, 'handleNotification']);

Route::post('/register', [UserController::class, 'registerPelanggan'])->name('register.pelanggan.submit');
