<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;

Route::post('/midtrans/callback', [MidtransController::class, 'handleNotification']);

