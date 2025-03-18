<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::post('/upload', [UserManagementController::class, 'createStudentAccounts'])->name('upload.submit');

