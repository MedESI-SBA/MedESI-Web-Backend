<?php 

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\UserManagementController;

Route::get("/me",[AuthController::class,"me"]);
Route::get("/logout",[AuthController::class,"logout"]);
Route::put('/me', [AuthController::class, 'updateProfile']);
Route::get('/medical-records/{patientId}',[MedicalRecordController::class,'showForDoctor']);
Route::post('/medical-records/{patientId}',[MedicalRecordController::class,'updateForDoctor']);
Route::get('/patients', [UserManagementController::class,'getPatients']);
