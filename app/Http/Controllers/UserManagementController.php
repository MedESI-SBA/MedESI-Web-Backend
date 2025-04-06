<?php

namespace App\Http\Controllers;

use App\Enums\PatientTypes;
use App\Imports\PatientImport;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Admin;
use App\Enums\UserTypes;
use App\Enums\PatientType;
use App\Enums\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UserManagementController extends Controller
{
    public function createPatientsFromFile(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls|max:2048',
        ]);

        $path = $validated['file']->store('temp_uploads');
        $storagePath = $path;

        try {
            Excel::import(new PatientImport, $storagePath);
            Storage::delete($storagePath);
            return response()->json(['message' => 'File imported successfully and patient accounts created!'], 200);

        } catch (ValidationException $e) {
             Log::error('Import validation failed: ' . $e->getMessage());
             Storage::delete($storagePath);
             return response()->json([
                 'message' => 'Import failed due to validation errors within the file.',
                 'errors' => $e->failures()
             ], 422);

        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            Storage::delete($storagePath);
            return response()->json(['message' => 'Import failed due to an unexpected error.'], 500);
        }
    }

    public function createPatient(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'familyName' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'email' => 'required|string|email|max:255|unique:patients,email',
            'phoneNumber' => 'required|string|max:20',
            'patient_type' => ['required', new Enum(PatientTypes::class)],
        ]);

        $password = Str::random(10);

        try {
            $patient = Patient::create([
                'firstName' => $validated['firstName'],
                'familyName' => $validated['familyName'],
                'age' => $validated['age'],
                'email' => $validated['email'],
                'phoneNumber' => $validated['phoneNumber'],
                'patientType' => $validated['patient_type'],
                'password' => Hash::make($password),
            ]);

            $this->sendCredentialsEmail($patient->email, $password);

            return response()->json(['message' => 'Patient created successfully.', 'patient' => $patient->refresh()], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create patient: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create patient due to an error.'], 500);
        }
    }

    public function createDoctor(Request $request)
    {
         $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'familyName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:doctors,email',
            'phoneNumber' => 'required|string|max:20',
        ]);

        $password = Str::random(10);

        try {
            $doctor = Doctor::create([
                'firstName' => $validated['firstName'],
                'familyName' => $validated['familyName'],
                'email' => $validated['email'],
                'phoneNumber' => $validated['phoneNumber'],
                'password' => Hash::make($password),
            ]);

            $this->sendCredentialsEmail($doctor->email, $password);

            return response()->json(['message' => 'Doctor created successfully.', 'doctor' => $doctor->refresh()], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create doctor: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create doctor due to an error.'], 500);
        }
    }

    public function createAdmin(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'familyName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email',
            'phoneNumber' => 'required|string|max:20',
            'roles' => ['required', 'array'],
            'roles.*' => ['required', new Enum(Roles::class)],
        ]);

        $password = Str::random(10);

        try {
            $admin = Admin::create([
                'firstName' => $validated['firstName'],
                'familyName' => $validated['familyName'],
                'email' => $validated['email'],
                'phoneNumber' => $validated['phoneNumber'],
                'password' => Hash::make($password),
                'roles' => $validated['roles'],
            ]);

            $this->sendCredentialsEmail($admin->email, $password);

            return response()->json(['message' => 'Admin created successfully.', 'admin' => $admin->refresh()], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create admin: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create admin due to an error.'], 500);
        }
    }

    protected function sendCredentialsEmail(string $recipientEmail, string $plainPassword): void
    {
        $subject = 'Your MedEsi Account Credentials';
        $message = "Welcome to MedEsi.\n\nYour account has been created.\nEmail: {$recipientEmail}\nTemporary Password: {$plainPassword}\n\nPlease log in and change your password immediately.";

        try {
            Mail::raw($message, function ($mail) use ($recipientEmail, $subject) {
                $mail->to($recipientEmail)
                     ->subject($subject);
            });
            Log::info("Credentials email sent successfully to {$recipientEmail}.");
        } catch(\Exception $e) {
            Log::error("Failed to send credentials email to {$recipientEmail}: " . $e->getMessage());
             // throw new \Exception("Failed to send email to {$recipientEmail}.");
        }
    }

    public function getAdmins(int $page = 0, int $limit = 10) {
        return Admin::paginate(request('limit'), ['*'], 'page', $page);
    }
    public function getDoctors(int $page = 0, int $limit = 10) {
        return Doctor::paginate(request('limit'), ['*'], 'page', $page);
    }
    public function getPatients(int $page = 0, int $limit = 10, ?PatientTypes $patientType) {
        $patientTypes = request('patient_type');
        return Patient::when($patientType, function ($query) use ($patientType) {
            return $query->where('patientType', $patientType);
        })->paginate(request('limit'), ['*'], 'page', $page);
    }
}