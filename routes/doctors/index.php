<?php 

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicalRecordController;

Route::get("/me",[AuthController::class,"me"]);
Route::get("/logout",[AuthController::class,"logout"]);
Route::put('/me', [AuthController::class, 'updateProfile']);
Route::get('/medical-records/{patientId}',[MedicalRecordController::class,'showForDoctor']);
Route::post('/medical-records/{patientId}',[MedicalRecordController::class,'updateForDoctor']);
