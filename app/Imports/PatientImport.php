<?php

namespace App\Imports;

use App\Enums\PatientType;
use App\Enums\PatientTypes;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PatientImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Patient
    {
        $password = Str::random(16);

        try {
            $patient = new Patient([
                "firstName" => $row["first_name"],
                "familyName" => $row["family_name"],
                "age" => $row["age"],
                "phoneNumber" => $row["phone_number"],
                "email" => $row["email"],
                "patientType" => $row["patient_type"],
                "password" => Hash::make($password),
            ]);

            $patient->save();

            $this->sendCredentialsEmail($patient->email, $password);

            return $patient;

        } catch (\Exception $e) {
            Log::error("Error processing row for email {$row['email']}: " . $e->getMessage());
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            '*.first_name' => 'required|string|max:255',
            '*.family_name' => 'required|string|max:255',
            '*.phone_number' => 'required|string|max:20',
            '*.age' => 'required|integer|min:0',
            '*.email' => 'required|email|ends_with:esi-sba.dz|unique:patients,email',
            '*.patient_type' => ['required', Rule::enum(PatientTypes::class)],
        ];
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
            // Consider re-throwing if email failure should halt the process
            // throw new \Exception("Failed to send email to {$recipientEmail}.");
        }
    }
}