<?php

namespace App\Imports;

use App\Models\Student;
use Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Mail;
use Str;

class StudentImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
        Log ::info('hi');
            validator($row, [
                "first_name" => "required",
                "family_name" => "required",
                "phone_number" => "required",
                "age" => 'required|integer|min:16',
                "email" => 'required|email|ends_with:esi-sba.dz',
            ])->validate();

            Log:info('importing');

            $password = Str::random(16);

            $recipient = $row['email'];
            $subject = 'Your MedEsi account';
            $message = "i fucking hate you guys , here is Your MedEsi account creds {$row['email']}:{$password} \n fuck you nassim";

            Mail::raw($message, function ($mail) use ($recipient, $subject) {
                $mail->to($recipient)
                    ->subject($subject);
            });

            return new Student([
                "firstName" => $row["first_name"],
                "familyName" => $row["family_name"],
                "age" => $row["age"],
                "phoneNumber" => $row["phone_number"],
                "email" => $row["email"],
                "password" => Hash::make($password)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error " . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::error("" . $e->getMessage());
            throw $e;
        }

    }
}
