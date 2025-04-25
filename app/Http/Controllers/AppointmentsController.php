<?php

namespace App\Http\Controllers;

use App\Enums\AppoitmentCreator;
use App\Enums\AppoitmentStatus;
use App\Models\Appointments;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Log;
use Mail;

class AppointmentsController extends Controller
{
    public function getAppointmentsForDoctor()
    {
        $doctor = auth()->user();
        $appointments = $doctor->appointments()->with('patient')->get();

        return response()->json([
            'appointments' => $appointments,
        ]);
    }
    public function getRequestedAppointmentsForDoctor(Request $request)
    {

        $appointments = Appointments::where("status", AppoitmentStatus::REQUESTED)
            ->with('patient')
            ->get();

        return response()->json([
            'appointments' => $appointments,
        ]);
    }
    public function getAppointmentsForPatient()
    {
        $patient = auth()->user();
        $appointments = $patient->appointments()->with('doctor')->get();

        return response()->json([
            'appointments' => $appointments,
        ]);
    }
    public function createAppointmentByDoctor(Request $request)
    {
        $doctor = auth()->user();
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'notes' => 'sometimes|string',
            'date' => 'required|date_format:Y-m-d',
        ]);



        $appoitments = $doctor->appointments;
        $requestedDate = new DateTime($request->date)->format("Y-m-d");
        while (true) {
            $startDate = new DateTime($requestedDate)->setTime(8, 0);
            $endDate = new DateTime($requestedDate)->setTime(12, 0);
            $interval = DateInterval::createFromDateString("15 minutes");
            $firstPeriod = new DatePeriod($startDate, $interval, $endDate);
            $startDate = new DateTime($requestedDate)->setTime(13, 0);
            $endDate = new DateTime($requestedDate)->setTime(17, 0);
            $secondPeriod = new DatePeriod($startDate, $interval, $endDate);
            $period = array_merge(iterator_to_array($firstPeriod), iterator_to_array($secondPeriod));
            foreach ($period as $date) {
                $isFree = true;
                foreach ($appoitments as $appoitment) {
                    if (new DateTime($appoitment->dateTime) == $date) {
                        $isFree = false;
                        break;
                    }
                }
                if ($isFree) {
                    try {
                        $appointment = $doctor->appointments()->create([
                            'patient_id' => $request->patient_id,
                            'notes' => $request->notes,
                            'status' => AppoitmentStatus::SCHEDULED,
                            'createdBy' => AppoitmentCreator::DOCTOR,
                            'dateTime' => $date->format("Y-m-d H:i"),
                        ]);

                        $subject = 'You have an appointment';
                        $message = "Hello.\n\nYour appointment has been scheduled at {$date->format("Y-m-d H:i")}.\nBy : {$doctor->firstName} {$doctor->familyName}\nNotes: {$appointment->notes}\n\nPlease Be sure to be there.\n Regards.";
                        Mail::raw($message, function ($mail) use ($appointment, $subject) {
                            $recipientEmail = $appointment->patient->email;
                            $mail->to($recipientEmail)
                                ->subject($subject);
                        });
                        return response()->json([
                            'message' => 'Appointment created successfully',
                            'appointment' => $appointment,
                        ]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'Error creating appointment',
                            'error' => $e->getMessage(),
                        ], 500);
                    }
                }
            }
            $requestedDate = new DateTime($requestedDate);
            $requestedDate->modify("+1 day");
            $requestedDate = $requestedDate->format("Y-m-d");
        }

    }

    public function requestAppointmentByPatient(Request $request)
    {
        try {
            $request->validate([
                'notes' => 'sometimes|string',
                'date' => 'required|date_format:Y-m-d',
            ]);
            $requestedDate = new DateTime($request->date)->format("Y-m-d");
            $appointment = Appointments::create([
                'patient_id' => $request->patient_id,
                'notes' => $request->notes,
                'status' => AppoitmentStatus::REQUESTED,
                'createdBy' => AppoitmentCreator::PATIENT,
                'requestedDate' => $requestedDate,
            ]);
            return response()->json([
                'message' => 'Appointment created successfully',
                'appointment' => $appointment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating appointment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function schedulePatientRequest(Request $request)
    {
        $doctor = auth()->user();
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'date' => 'required|date_format:Y-m-d',
        ]);
        $appointment = Appointments::find($request->appointment_id);
        if ($appointment->status != AppoitmentStatus::REQUESTED->value) {
            return response()->json([
                'message' => 'Appointment is not requested',
            ], 400);
        }
        $appoitments = $doctor->appointments;
        $requestedDate = new DateTime($request->date)->format("Y-m-d");
        while (true) {
            $startDate = new DateTime($requestedDate)->setTime(8, 0);
            $endDate = new DateTime($requestedDate)->setTime(12, 0);
            $interval = DateInterval::createFromDateString("15 minutes");
            $firstPeriod = new DatePeriod($startDate, $interval, $endDate);
            $startDate = new DateTime($requestedDate)->setTime(13, 0);
            $endDate = new DateTime($requestedDate)->setTime(17, 0);
            $secondPeriod = new DatePeriod($startDate, $interval, $endDate);
            $period = array_merge(iterator_to_array($firstPeriod), iterator_to_array($secondPeriod));
            foreach ($period as $date) {
                $isFree = true;
                foreach ($appoitments as $appoitment) {
                    if (new DateTime($appoitment->dateTime) == $date) {
                        $isFree = false;
                        break;
                    }
                }
                if ($isFree) {
                    try {
                        $appointment->dateTime = $date->format("Y-m-d H:i");
                        $appointment->doctor_id = $doctor->id;
                        $appointment->status = AppoitmentStatus::SCHEDULED;
                        $appointment->save();
                        $subject = 'Your has been appointment scheduled';
                        $message = "Hello.\n\nYour appointment has been scheduled at {$date->format("Y-m-d H:i")}.\nBy : {$doctor->firstName} {$doctor->familyName}\nNotes: {$appointment->notes}\n\nPlease Be sure to be there.\n Regards.";
                        Mail::raw($message, function ($mail) use ($appointment, $subject) {
                            $recipientEmail = $appointment->patient->email;
                            $mail->to($recipientEmail)
                                ->subject($subject);
                        });
                        return response()->json([
                            'message' => 'Appointment schedueled successfully',
                            'appointment' => $appointment,
                        ]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'Error schedueled appointment',
                            'error' => $e->getMessage(),
                        ], 500);
                    }
                }
            }
            $requestedDate = new DateTime($requestedDate);
            $requestedDate->modify("+1 day");
            $requestedDate = $requestedDate->format("Y-m-d");
        }
    }
}
