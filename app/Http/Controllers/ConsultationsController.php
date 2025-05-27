<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use Exception;
use File;
use Illuminate\Http\Request;
use Log;
use Storage;

class ConsultationsController extends Controller
{
    public function getConsultationsByPatientId(Request $request, $patientId)
    {

        validator(["patient_id" => $patientId], [
            "patient_id" => "required|exists:patients,id"
        ])->validate();


        try {

            $consultations = \App\Models\Consultation::with([
                "appointment",
                "doctor",
                "patient",
                "prescription.prescriptionItems"
            ])->where("patient_id", $patientId)->get();

            return response()->json([
                "status" => true,
                "message" => "Consultations fetched successfully",
                "data" => $consultations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    public function getConsultationsForPatient(Request $request)
    {

        $patientId = auth()->user()->id;


        try {

            $consultations = \App\Models\Consultation::with([
                "appointment",
                "doctor",
                "patient",
                "prescription.prescriptionItems"
            ])->where("patient_id", $patientId)->get();

            return response()->json([
                "status" => true,
                "message" => "Consultations fetched successfully",
                "data" => $consultations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    public function getConsultationById(Request $request, $consultationId)
    {
        validator(["consultation_id" => $consultationId], [
            "consultation_id" => "required|exists:consultations,id"
        ])->validate();


        try {
            $consultation = \App\Models\Consultation::with([
                "appointment",
                "doctor",
                "patient",
                "prescription.prescriptionItems"
            ])->where("id", $consultationId)->first();

            return response()->json([
                "status" => true,
                "message" => "Consultation fetched successfully",
                "data" => $consultation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    public function getConsultaionsForDoctor(Request $request)
    {
        try {
            $doctorId = auth()->user()->id;
            $consultations = \App\Models\Consultation::with([
                "appointment",
                "doctor",
                "patient",
                "prescription.prescriptionItems"
            ])->where("doctor_id", $doctorId)->paginate($request->limit ?? 10, ["*"], "page", $request->page ?? 1);

            return response()->json([
                "status" => true,
                "message" => "Consultations fetched successfully",
                "data" => $consultations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    public function createDirectConsultation(Request $request)
    {
        $doctorId = auth()->user()->id;
        $validated = $request->validate([
            "patient_id" => "required|exists:patients,id",
            "notes" => "sometimes|nullable|string|max:255",
            "reorientation" => "sometimes|nullable|string|max:255",
            "prescriptions" => "sometimes|nullable|array",
            "prescriptions.*.name" => "required|string|max:255",
            "prescriptions.*.dosage" => "required|string|max:255",
            "prescriptions.*.frequency" => "required|string|max:255",
            "prescriptions.*.duration" => "required|string|max:255",
            "prescriptionIssueDate" => "sometimes|nullable|date",
            "height" => "sometimes|nullable|string|max:255",
            "weight" => "sometimes|nullable|string|max:255",
            "bloodPressure" => "sometimes|nullable|string|max:255",
            "heartRate" => "sometimes|nullable|string|max:255",
            "condition" => "sometimes|nullable|string|max:255",
            "contagious" => "sometimes|nullable|boolean",
            "chronic" => "sometimes|nullable|boolean",
        ]);
        Log::info($validated);

        try {
            $consultation = \App\Models\Consultation::create([
                "patient_id" => $validated["patient_id"],
                "doctor_id" => $doctorId,
                "notes" => $validated["notes"] ?? null,
                "reorientation" => $validated["reorientation"] ?? null,
                "height" => $validated["height"] ?? null,
                "weight" => $validated["weight"] ?? null,
                "bloodPressure" => $validated["bloodPressure"] ?? null,
                "heartRate" => $validated["heartRate"] ?? null,
                "condition" => $validated["condition"] ?? null,
                "contagious" => $validated["contagious"] ?? null,
                "chronic" => $validated["chronic"] ?? null,
            ]);
            $consultation->prescription()->create([
                "issueDate" => $validated["prescriptionIssueDate"] ?? now(),
            ])->prescriptionItems()->createMany($validated["prescriptions"]);
            return response()->json([
                "status" => true,
                "message" => "Consultation created successfully",
                "data" => $consultation
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    public function createConsultationFromAppointment(Request $request)
    {
        $doctorId = auth()->user()->id;
        $validated = $request->validate([
            "patient_id" => "required|exists:patients,id",
            "appointment_id" => "required|exists:appointments,id",
            "notes" => "sometimes|nullable|string|max:255",
            "reorientation" => "sometimes|nullable|string|max:255",
            "prescriptions" => "sometimes|nullable|array",
            "prescriptions.*.name" => "required|string|max:255",
            "prescriptions.*.dosage" => "required|string|max:255",
            "prescriptions.*.frequency" => "required|string|max:255",
            "prescriptions.*.duration" => "required|string|max:255",
            "prescriptionIssueDate" => "sometimes|nullable|date",
            "height" => "sometimes|nullable|string|max:255",
            "weight" => "sometimes|nullable|string|max:255",
            "bloodPressure" => "sometimes|nullable|string|max:255",
            "heartRate" => "sometimes|nullable|string|max:255",
            "condition" => "sometimes|nullable|string|max:255",
            "contagious" => "sometimes|nullable|boolean",
            "chronic" => "sometimes|nullable|boolean",
        ]);
        Log::info($validated);

        try {
            $consultation = \App\Models\Consultation::create([
                "patient_id" => $validated["patient_id"],
                "doctor_id" => $doctorId,
                "notes" => $validated["notes"] ?? null,
                "reorientation" => $validated["reorientation"] ?? null,
                "appointment_id" => $validated["appointment_id"],
                "height" => $validated["height"] ?? null,
                "weight" => $validated["weight"] ?? null,
                "bloodPressure" => $validated["bloodPressure"] ?? null,
                "heartRate" => $validated["heartRate"] ?? null,
                "condition" => $validated["condition"] ?? null,
                "contagious" => $validated["contagious"] ?? null,
                "chronic" => $validated["chronic"] ?? null,
            ])->prescription()->create([
                        "issueDate" => $validated["prescriptionIssueDate"] ?? now(),
                    ])->prescriptionItems()->createMany($validated["prescriptions"])[0]->prescription->consultation->load(["prescription", "prescription.prescriptionItems", "appointment"]);
            $appointment = Appointments::findOrFail($validated["appointment_id"]);
            $appointment->update([
                "status" => "completed",
            ]);

            return response()->json([
                "status" => true,
                "message" => "Consultation created successfully",
                "data" => $consultation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    //update consultation
    public function updateConsultation(Request $request)
    {

        $validated = $request->validate([
            "consultation_id" => "required|exists:consultations,id",
            "notes" => "sometimes|nullable|string|max:255",
            "reorientation" => "sometimes|nullable|string|max:255",
            "prescriptions" => "sometimes|nullable|array",
            "prescriptions.*.name" => "required|string|max:255",
            "prescriptions.*.dosage" => "required|string|max:255",
            "prescriptions.*.frequency" => "required|string|max:255",
            "prescriptions.*.duration" => "required|string|max:255",
            "prescriptions.*.id" => "sometimes|nullable|exists:prescription_items,id",
            "height" => "sometimes|nullable|string|max:255",
            "weight" => "sometimes|nullable|string|max:255",
            "bloodPressure" => "sometimes|nullable|string|max:255",
            "heartRate" => "sometimes|nullable|string|max:255",
            "condition" => "sometimes|nullable|string|max:255",
            "contagious" => "sometimes|nullable|boolean",
            "chronic" => "sometimes|nullable|boolean",
        ]);
        try {
            $consultation = \App\Models\Consultation::find($validated["consultation_id"]);
            $consultation->update([
                "notes" => $validated["notes"] ?? null,
                "reorientation" => $validated["reorientation"] ?? null,
                "height" => $validated["height"] ?? null,
                "weight" => $validated["weight"] ?? null,
                "bloodPressure" => $validated["bloodPressure"] ?? null,
                "heartRate" => $validated["heartRate"] ?? null,
                "condition" => $validated["condition"] ?? null,
                "contagious" => $validated["contagious"] ?? null,
                "chronic" => $validated["chronic"] ?? null,
                "status" => "completed",
            ]);
            if (isset($validated["prescriptions"])) {
                $prescription = $consultation->prescription()->updateOrCreate([
                    "issueDate" => now(),
                ]);

                foreach ($validated["prescriptions"] as $prescriptionData) {
                    $prescription->prescriptionItems()->updateOrCreate(
                        [
                            "name" => $prescriptionData["name"],
                        ],
                        [
                            "dosage" => $prescriptionData["dosage"],
                            "frequency" => $prescriptionData["frequency"],
                            "duration" => $prescriptionData["duration"],
                        ]
                    );
                }
            }

            return response()->json([
                "status" => true,
                "message" => "Consultation updated successfully",
                "data" => $consultation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getConsultationByAppointment(Request $request, $appointmentId)
    {
        validator(["appointment_id" => $appointmentId], [
            "appointment_id" => "required|exists:appointments,id",
        ]);


        try {
            $consultation = \App\Models\Consultation::with([
                "appointment",
                "doctor",
                "patient",
                "prescription.prescriptionItems"
            ])->where("appointment_id", $appointmentId)->first();

            return response()->json([
                "status" => true,
                "message" => "Consultation fetched successfully",
                "data" => $consultation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function savePrescription(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png', // adjust mime types as needed
            'consultation_id' => 'required|string'
        ]);


        $file = $request->file('file');
        $consultationId = $request->consultation_id;

        $filename = "consultation_" . $consultationId . '.' . $file->getClientOriginalExtension();

        $destinationPath = public_path('prescriptions');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        return response()->json([
            'message' => 'Prescription saved successfully.',
        ]);
    }
}
