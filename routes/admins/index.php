<?php 

use App\Http\Controllers\AuthController;

Route::get("/me",[AuthController::class,"me"]);
Route::get("/logout",[AuthController::class,"logout"]);

