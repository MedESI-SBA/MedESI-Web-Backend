<?php 

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Patient;

Route::get("/me",[AuthController::class,"me"]);
Route::get("/logout",[AuthController::class,"logout"]);
Route::post('/upload', [UserManagementController::class, 'createPatientAccounts'])->name('upload.submit');
Route::put('/me', [AuthController::class, 'updateProfile']);
Route::get('/admins', [UserManagementController::class,'getAdmins']);
Route::get('/doctors', [UserManagementController::class,'getDoctors']);
Route::get('/patients', [UserManagementController::class,'getPatients']);
Route::get('/patients/{patient}', function (string $patient) {
    return Patient::findOrFail( $patient );
});
Route::get('/admins/{admin}', function (string $admin) {
    return Admin::findOrFail( $admin );
});

Route::get('/doctors/{doctor}', function (string $doctor) {
    return Doctor::findOrFail( $doctor );
});
