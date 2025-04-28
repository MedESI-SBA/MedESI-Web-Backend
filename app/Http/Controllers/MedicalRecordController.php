<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
// Potentially add rules for specific fields if needed, e.g., date format, boolean checks
// use Illuminate\Validation\Rule;

class MedicalRecordController extends Controller
{
    /**
     * Display the specified patient's medical record for a doctor.
     * Assumes route is protected by 'auth:doctor' middleware.
     *
     * @param Patient $patient Automatically resolved via Route Model Binding
     * @return JsonResponse
     */
    public function showForDoctor(Request $request,string $patientId): JsonResponse
    {
     

        $patient = Patient::findOrFail($patientId);

        $medicalRecord = $patient->medicalRecord()->firstOrNew(["patient_id" => $patientId]);

        return response()->json(["medical-record" => $medicalRecord, "patient" => $patient]);
    }    


    /**
     * Update the specified patient's medical record for a doctor.
     * Assumes route is protected by 'auth:doctor' middleware.
     *
     * @param Request $request
     * @param Patient $patient Automatically resolved via Route Model Binding
     * @return JsonResponse
     */
    public function updateForDoctor(Request $request, string $patientId): JsonResponse
    {
        $patient = Patient::findOrFail($patientId);

        // Route Model Binding handles 404. Auth handled by middleware.
        $doctor = Auth::user(); // Get the currently authenticated doctor

        if (!$doctor) {
            // Should not happen if middleware is correct, but good practice
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // Define validation rules for *all* fields from the MedicalRecord schema
        // This is a subset for brevity, **expand this significantly** based on your schema definition
        $validationRules = [
            'dossier_number' => 'sometimes|nullable|string|max:255', // Use 'sometimes|nullable' if update is partial (PATCH)
            'blood_group' => 'sometimes|nullable|string|max:10',
            'social_security_number' => 'sometimes|nullable|string|max:50',
            'family_situation' => 'sometimes|nullable|string|max:255',
            'admission_date' => 'sometimes|nullable|date_format:Y-m-d',
            'study_field' => 'sometimes|nullable|string|max:255',
            'smoker' => 'sometimes|nullable|boolean',
            'cigarettes_per_day' => 'sometimes|nullable|integer|min:0',
            'chewer' => 'sometimes|nullable|boolean',
            'boxes_per_day_chew' => 'sometimes|nullable|integer|min:0',
            'snuff_user' => 'sometimes|nullable|boolean',
            'boxes_per_day_snuff' => 'sometimes|nullable|integer|min:0',
            'age_first_tobacco_use' => 'sometimes|nullable|integer|min:0',
            'former_smoker' => 'sometimes|nullable|boolean',
            'exposure_period' => 'sometimes|nullable|string|max:255',
            'alcohol_details' => 'sometimes|nullable|string',
            'medication_details' => 'sometimes|nullable|string',
            'other_intoxications' => 'sometimes|nullable|string',
            'congenital_conditions' => 'sometimes|nullable|string',
            'general_diseases' => 'sometimes|nullable|string',
            'surgical_interventions' => 'sometimes|nullable|string',
            'medication_allergies' => 'sometimes|nullable|string',
            'weight_kg' => 'sometimes|nullable|numeric|min:0',
            'height_cm' => 'sometimes|nullable|integer|min:0',
            'visual_acuity_od_sc' => 'sometimes|nullable|string|max:20',
            'visual_acuity_og_sc' => 'sometimes|nullable|string|max:20',
            'visual_acuity_od_wc' => 'sometimes|nullable|string|max:20',
            'visual_acuity_og_wc' => 'sometimes|nullable|string|max:20',
            'audition_od' => 'sometimes|nullable|string|max:255',
            'audition_og' => 'sometimes|nullable|string|max:255',
            'skin_conditions' => 'sometimes|nullable|string',
            'skin_exam_notes' => 'sometimes|nullable|string',
            'oph_tearing' => 'sometimes|nullable|boolean',
            'oph_pain' => 'sometimes|nullable|boolean',
            'oph_spots' => 'sometimes|nullable|boolean',
            'oph_exam_notes' => 'sometimes|nullable|string',
            'orl_tinnitus' => 'sometimes|nullable|boolean',
            'orl_repeated_angina' => 'sometimes|nullable|boolean',
            'orl_epistaxis' => 'sometimes|nullable|boolean',
            'orl_rhinorrhea' => 'sometimes|nullable|boolean',
            'orl_other' => 'sometimes|nullable|string',
            'orl_exam_notes' => 'sometimes|nullable|string',
            'loc_pain_muscular' => 'sometimes|nullable|boolean',
            'loc_pain_articular' => 'sometimes|nullable|boolean',
            'loc_pain_vertebral' => 'sometimes|nullable|boolean',
            'loc_pain_neurological' => 'sometimes|nullable|boolean',
            'loc_movement_difficulty' => 'sometimes|nullable|boolean',
            'loc_fatigability' => 'sometimes|nullable|boolean',
            'loc_exam_notes' => 'sometimes|nullable|string',
            'res_cough' => 'sometimes|nullable|boolean',
            'res_dyspnea_nocturnal' => 'sometimes|nullable|boolean',
            'res_dyspnea_diurnal' => 'sometimes|nullable|boolean',
            'res_expectorations' => 'sometimes|nullable|string',
            'res_thoracic_pain' => 'sometimes|nullable|boolean',
            'res_other' => 'sometimes|nullable|string',
            'respiratory_rate' => 'sometimes|nullable|integer|min:0',
            'res_exam_notes' => 'sometimes|nullable|string',
            'car_palpitations' => 'sometimes|nullable|boolean',
            'car_edema' => 'sometimes|nullable|boolean',
            'car_pain_walking' => 'sometimes|nullable|boolean',
            'car_pain_rest' => 'sometimes|nullable|boolean',
            'car_pain_effort' => 'sometimes|nullable|boolean',
            'car_pain_permanent' => 'sometimes|nullable|boolean',
            'pulse_rate' => 'sometimes|nullable|integer|min:0',
            'blood_pressure' => 'sometimes|nullable|string|max:15',
            'cyanosis' => 'sometimes|nullable|boolean',
            'car_exam_notes' => 'sometimes|nullable|string',
            'dig_appetite' => 'sometimes|nullable|string|max:255',
            'dig_transit' => 'sometimes|nullable|string|max:255',
            'dig_stools' => 'sometimes|nullable|string|max:255',
            'dig_pyrosis' => 'sometimes|nullable|boolean',
            'dig_vomiting' => 'sometimes|nullable|boolean',
            'dig_rectorrhagia' => 'sometimes|nullable|boolean',
            'dig_abdominal_pain' => 'sometimes|nullable|boolean',
            'dig_other' => 'sometimes|nullable|string',
            'dig_denture_caries' => 'sometimes|nullable|string',
            'dig_gingivopathy' => 'sometimes|nullable|string',
            'dig_other_mouth' => 'sometimes|nullable|string',
            'dig_abdomen_notes' => 'sometimes|nullable|string',
            'dig_hernia_notes' => 'sometimes|nullable|string',
            'dig_liver_notes' => 'sometimes|nullable|string',
            'gen_micturition_pollakiuria' => 'sometimes|nullable|boolean',
            'gen_micturition_dysuria' => 'sometimes|nullable|boolean',
            'gen_hematuria' => 'sometimes|nullable|boolean',
            'gen_micturition_burning' => 'sometimes|nullable|boolean',
            'gen_nephritic_colic' => 'sometimes|nullable|boolean',
            'gen_losses' => 'sometimes|nullable|string',
            'gen_cycles' => 'sometimes|nullable|string|max:255',
            'gen_other' => 'sometimes|nullable|string',
            'gen_bourses_notes' => 'sometimes|nullable|string',
            'gen_breasts_notes' => 'sometimes|nullable|string',
            'gen_tr_notes' => 'sometimes|nullable|string',
            'gen_tv_notes' => 'sometimes|nullable|string',
            'neu_sleep' => 'sometimes|nullable|string|max:255',
            'neu_headaches' => 'sometimes|nullable|boolean',
            'neu_vertigo' => 'sometimes|nullable|boolean',
            'neu_agoraphobia' => 'sometimes|nullable|boolean',
            'neu_loss_consciousness' => 'sometimes|nullable|boolean',
            'neu_paresis' => 'sometimes|nullable|boolean',
            'neu_paresthesia' => 'sometimes|nullable|boolean',
            'neu_other' => 'sometimes|nullable|string',
            'neu_tremor_notes' => 'sometimes|nullable|string',
            'neu_romberg_notes' => 'sometimes|nullable|string',
            'neu_reflexes_ro' => 'sometimes|nullable|string',
            'neu_reflexes_ach' => 'sometimes|nullable|string',
            'neu_coordination_notes' => 'sometimes|nullable|string',
            'neu_sensitivity_notes' => 'sometimes|nullable|string',
            'neu_motricity_notes' => 'sometimes|nullable|string',
            'neu_ocular_notes' => 'sometimes|nullable|string',
            'hem_ecchymoses' => 'sometimes|nullable|boolean',
            'hem_bleeding_tendency' => 'sometimes|nullable|boolean',
            'hem_petechiae_notes' => 'sometimes|nullable|string',
            'hem_purpura_notes' => 'sometimes|nullable|string',
            'hem_spleen_notes' => 'sometimes|nullable|string',
            'hem_ganglions_cervical' => 'sometimes|nullable|string',
            'hem_ganglions_axillary' => 'sometimes|nullable|string',
            'hem_ganglions_clavicular' => 'sometimes|nullable|string',
            'hem_ganglions_inguinal' => 'sometimes|nullable|string',
            'end_family_obesity' => 'sometimes|nullable|boolean',
            'end_family_thinness' => 'sometimes|nullable|boolean',
            'end_thyroid_notes' => 'sometimes|nullable|string',
            'end_testicles_notes' => 'sometimes|nullable|string',
            'end_mammary_notes' => 'sometimes|nullable|string',
            'psychological_profile' => 'sometimes|nullable|string',
            'exp_func_respiratory' => 'sometimes|nullable|string',
            'exp_func_circulatory' => 'sometimes|nullable|string',
            'exp_func_motor' => 'sometimes|nullable|string',
            'exam_comp_radiological' => 'sometimes|nullable|string',
            'exam_comp_bio_blood' => 'sometimes|nullable|string',
            'exam_comp_bio_urinary' => 'sometimes|nullable|string',
            'exam_comp_hep_viral' => 'sometimes|nullable|string',
            'exam_comp_syphilis' => 'sometimes|nullable|string',
            'exam_comp_hiv' => 'sometimes|nullable|string',
            'eps_apt' => 'sometimes|nullable|boolean',
            'eps_motifs' => 'sometimes|nullable|string',
            'orientation_specialist' => 'sometimes|nullable|string',
            'orientation_opinion' => 'sometimes|nullable|boolean',
            'orientation_hospitalization' => 'sometimes|nullable|boolean',
            'orientation_treatment' => 'sometimes|nullable|boolean',
            'orientation_response' => 'sometimes|nullable|string',
        ];

        try {
            // Validate the request data
            $validatedData = $request->validate($validationRules);

            // Find the existing record or create a new one if it doesn't exist
            $medicalRecord = $patient->medicalRecord()->firstOrCreate(
                ['patient_id' => $patient->id] // Ensure patient_id is set if creating
            );

            // Fill the model with validated data
            // Ensure attributes are $fillable in the MedicalRecord model
            $medicalRecord->fill($validatedData);

            // Set the doctor who last updated the record
            $medicalRecord->last_updated_by_doctor_id = $doctor->id;

            // Save the changes
            $medicalRecord->save();

            // Return success response with the updated record
            return response()->json([
                'message' => 'Medical record updated successfully.',
                'record' => $medicalRecord->refresh() // Get fresh data after save
            ]);

        } catch (ValidationException $e) {
            // Return validation errors (Laravel handles formatting)
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log any other errors during update process
            Log::error("Failed to update medical record for patient {$patient->id}: " . $e->getMessage());
            return response()->json(['message' => 'Failed to update medical record due to an internal error.'], 500);
        }
    }

    /**
     * Display the authenticated patient's own medical record.
     * Assumes route is protected by 'auth:patient' middleware.
     *
     * @return JsonResponse
     */
    public function showForPatient(): JsonResponse
    {
        $patient = Auth::user(); // Get the currently authenticated patient

        // Retrieve the associated medical record, or a new empty instance if none exists.
        $medicalRecord = $patient->medicalRecord()->firstOrNew([]);

        return response()->json($medicalRecord);
    }
}