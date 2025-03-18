<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/login",[AuthController::class,"login"])->name("login");

Route::post('/forgot-password',[AuthController::class,'sendResetPasswordEmail']);

Route::get('/reset-password/{token}', function (string $token) {

    return response()->json(['token' => $token]);

})->middleware('guest')->name('password.reset');

Route::post('/reset-password',[AuthController::class,'resetPassword']);



