<?php 

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultationsController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\UserManagementController;

Route::get("/me",[AuthController::class,"me"]);
Route::get("/logout",[AuthController::class,"logout"]);
Route::put('/me', [AuthController::class, 'updateProfile']);
Route::get('/medical-records/{patientId}',[MedicalRecordController::class,'showForDoctor']);
Route::post('/medical-records/{patientId}',[MedicalRecordController::class,'updateForDoctor']);
Route::get('/patients', [UserManagementController::class,'getPatients']);
Route::get('/appointments/requested', [AppointmentsController::class,'getRequestedAppointmentsForDoctor']);
Route::post('/appointments', [AppointmentsController::class,'createAppointmentByDoctor']);
Route::get('/appointments', [AppointmentsController::class,'getAppointmentsForDoctor']);
Route::post("/appointments/schedule", [AppointmentsController::class,"schedulePatientRequest"]);
Route::put("/appointments", [AppointmentsController::class,"completeAppointment"]);
Route::delete("/appointments", [AppointmentsController::class,"cancelAppointment"]);
Route::get("/consultations/patient/{patientId}", [ConsultationsController::class,"getConsultationsByPatientId"]);
Route::get("/consultations/{consultationId}", [ConsultationsController::class,"getConsultationById"]);
Route::get("/consultations", [ConsultationsController::class,"getConsultaionsForDoctor"]);
Route::post("/consultations", [ConsultationsController::class,"createDirectConsultation"]);
Route::post("/consultations/appointment", [ConsultationsController::class,"createConsultationFromAppointment"]);
Route::patch("/consultations", [ConsultationsController::class,"updateConsultation"]);
Route::get("/patients",[AppointmentsController::class,"getPatientByEmail"]);

