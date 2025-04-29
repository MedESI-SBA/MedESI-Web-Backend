<?php 

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultationsController;
use App\Http\Controllers\MedicalRecordController;

Route::get("/me",[AuthController::class,"me"]);
Route::get("/logout",[AuthController::class,"logout"]);
Route::put('/me', [AuthController::class, 'updateProfile']);
Route::get('/medical-record',[MedicalRecordController::class,'showForPatient']);
Route::get('/appointments', [AppointmentsController::class,'getAppointmentsForPatient']);
Route::post('/appointments', [AppointmentsController::class,'requestAppointmentByPatient']);
Route::delete("/appointments", [AppointmentsController::class,"cancelAppointment"]);
Route::get("/consultations", [ConsultationsController::class,"getConsultationsForPatient"]);
Route::get("/consultations/{appointmentId}", [ConsultationsController::class,"getConsultationById"]);