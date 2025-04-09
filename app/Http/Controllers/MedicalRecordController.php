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
        // Route Model Binding handles the 404 if patient not found.
        // Authorization (is user a Doctor) is handled by middleware.

        $patient = Patient::findOrFail($patientId);
        // Retrieve the associated medical record, or a new empty instance if none exists.
        // Assumes a 'medicalRecord' relationship is defined on the Patient model.
        $medicalRecord = $patient->medicalRecord()->firstOrNew([]);

        return response()->json($medicalRecord);
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
            'dossier_number' => 'sometimes|string|max:255', // Use 'sometimes' if update is partial (PATCH)
            'blood_group' => 'sometimes|string|max:10',
            'social_security_number' => 'sometimes|string|max:50',
            'family_situation' => 'sometimes|string|max:255',
            'admission_date' => 'sometimes|date_format:Y-m-d',
            'study_field' => 'sometimes|string|max:255',
            'smoker' => 'sometimes|boolean',
            'cigarettes_per_day' => 'sometimes|integer|min:0',
            'chewer' => 'sometimes|boolean',
            'boxes_per_day_chew' => 'sometimes|integer|min:0',
            'snuff_user' => 'sometimes|boolean',
            'boxes_per_day_snuff' => 'sometimes|integer|min:0',
            'age_first_tobacco_use' => 'sometimes|integer|min:0',
            'former_smoker' => 'sometimes|boolean',
            'exposure_period' => 'sometimes|string|max:255',
            'alcohol_details' => 'sometimes|string',
            'medication_details' => 'sometimes|string',
            'other_intoxications' => 'sometimes|string',
            'congenital_conditions' => 'sometimes|string',
            'general_diseases' => 'sometimes|string',
            'surgical_interventions' => 'sometimes|string',
            'medication_allergies' => 'sometimes|string',
            'weight_kg' => 'sometimes|numeric|min:0',
            'height_cm' => 'sometimes|integer|min:0',
            'visual_acuity_od_sc' => 'sometimes|string|max:20',
            'visual_acuity_og_sc' => 'sometimes|string|max:20',
            'visual_acuity_od_wc' => 'sometimes|string|max:20',
            'visual_acuity_og_wc' => 'sometimes|string|max:20',
            'audition_od' => 'sometimes|string|max:255',
            'audition_og' => 'sometimes|string|max:255',
            'skin_conditions' => 'sometimes|string',
            'skin_exam_notes' => 'sometimes|string',
            'oph_tearing' => 'sometimes|boolean',
            'oph_pain' => 'sometimes|boolean',
            'oph_spots' => 'sometimes|boolean',
            'oph_exam_notes' => 'sometimes|string',
            'orl_tinnitus' => 'sometimes|boolean',
            'orl_repeated_angina' => 'sometimes|boolean',
            'orl_epistaxis' => 'sometimes|boolean',
            'orl_rhinorrhea' => 'sometimes|boolean',
            'orl_other' => 'sometimes|string',
            'orl_exam_notes' => 'sometimes|string',
            'loc_pain_muscular' => 'sometimes|boolean',
            'loc_pain_articular' => 'sometimes|boolean',
            'loc_pain_vertebral' => 'sometimes|boolean',
            'loc_pain_neurological' => 'sometimes|boolean',
            'loc_movement_difficulty' => 'sometimes|boolean',
            'loc_fatigability' => 'sometimes|boolean',
            'loc_exam_notes' => 'sometimes|string',
            'res_cough' => 'sometimes|boolean',
            'res_dyspnea_nocturnal' => 'sometimes|boolean',
            'res_dyspnea_diurnal' => 'sometimes|boolean',
            'res_expectorations' => 'sometimes|string',
            'res_thoracic_pain' => 'sometimes|boolean',
            'res_other' => 'sometimes|string',
            'respiratory_rate' => 'sometimes|integer|min:0',
            'res_exam_notes' => 'sometimes|string',
            'car_palpitations' => 'sometimes|boolean',
            'car_edema' => 'sometimes|boolean',
            'car_pain_walking' => 'sometimes|boolean',
            'car_pain_rest' => 'sometimes|boolean',
            'car_pain_effort' => 'sometimes|boolean',
            'car_pain_permanent' => 'sometimes|boolean',
            'pulse_rate' => 'sometimes|integer|min:0',
            'blood_pressure' => 'sometimes|string|max:15',
            'cyanosis' => 'sometimes|boolean',
            'car_exam_notes' => 'sometimes|string',
            'dig_appetite' => 'sometimes|string|max:255',
            'dig_transit' => 'sometimes|string|max:255',
            'dig_stools' => 'sometimes|string|max:255',
            'dig_pyrosis' => 'sometimes|boolean',
            'dig_vomiting' => 'sometimes|boolean',
            'dig_rectorrhagia' => 'sometimes|boolean',
            'dig_abdominal_pain' => 'sometimes|boolean',
            'dig_other' => 'sometimes|string',
            'dig_denture_caries' => 'sometimes|string',
            'dig_gingivopathy' => 'sometimes|string',
            'dig_other_mouth' => 'sometimes|string',
            'dig_abdomen_notes' => 'sometimes|string',
            'dig_hernia_notes' => 'sometimes|string',
            'dig_liver_notes' => 'sometimes|string',
            'gen_micturition_pollakiuria' => 'sometimes|boolean',
            'gen_micturition_dysuria' => 'sometimes|boolean',
            'gen_hematuria' => 'sometimes|boolean',
            'gen_micturition_burning' => 'sometimes|boolean',
            'gen_nephritic_colic' => 'sometimes|boolean',
            'gen_losses' => 'sometimes|string',
            'gen_cycles' => 'sometimes|string|max:255',
            'gen_other' => 'sometimes|string',
            'gen_bourses_notes' => 'sometimes|string',
            'gen_breasts_notes' => 'sometimes|string',
            'gen_tr_notes' => 'sometimes|string',
            'gen_tv_notes' => 'sometimes|string',
            'neu_sleep' => 'sometimes|string|max:255',
            'neu_headaches' => 'sometimes|boolean',
            'neu_vertigo' => 'sometimes|boolean',
            'neu_agoraphobia' => 'sometimes|boolean',
            'neu_loss_consciousness' => 'sometimes|boolean',
            'neu_paresis' => 'sometimes|boolean',
            'neu_paresthesia' => 'sometimes|boolean',
            'neu_other' => 'sometimes|string',
            'neu_tremor_notes' => 'sometimes|string',
            'neu_romberg_notes' => 'sometimes|string',
            'neu_reflexes_ro' => 'sometimes|string',
            'neu_reflexes_ach' => 'sometimes|string',
            'neu_coordination_notes' => 'sometimes|string',
            'neu_sensitivity_notes' => 'sometimes|string',
            'neu_motricity_notes' => 'sometimes|string',
            'neu_ocular_notes' => 'sometimes|string',
            'hem_ecchymoses' => 'sometimes|boolean',
            'hem_bleeding_tendency' => 'sometimes|boolean',
            'hem_petechiae_notes' => 'sometimes|string',
            'hem_purpura_notes' => 'sometimes|string',
            'hem_spleen_notes' => 'sometimes|string',
            'hem_ganglions_cervical' => 'sometimes|string',
            'hem_ganglions_axillary' => 'sometimes|string',
            'hem_ganglions_clavicular' => 'sometimes|string',
            'hem_ganglions_inguinal' => 'sometimes|string',
            'end_family_obesity' => 'sometimes|boolean',
            'end_family_thinness' => 'sometimes|boolean',
            'end_thyroid_notes' => 'sometimes|string',
            'end_testicles_notes' => 'sometimes|string',
            'end_mammary_notes' => 'sometimes|string',
            'psychological_profile' => 'sometimes|string',
            'exp_func_respiratory' => 'sometimes|string',
            'exp_func_circulatory' => 'sometimes|string',
            'exp_func_motor' => 'sometimes|string',
            'exam_comp_radiological' => 'sometimes|string',
            'exam_comp_bio_blood' => 'sometimes|string',
            'exam_comp_bio_urinary' => 'sometimes|string',
            'exam_comp_hep_viral' => 'sometimes|string',
            'exam_comp_syphilis' => 'sometimes|string',
            'exam_comp_hiv' => 'sometimes|string',
            'eps_apt' => 'sometimes|boolean',
            'eps_motifs' => 'sometimes|string',
            'orientation_specialist' => 'sometimes|string',
            'orientation_opinion' => 'sometimes|boolean',
            'orientation_hospitalization' => 'sometimes|boolean',
            'orientation_treatment' => 'sometimes|boolean',
            'orientation_response' => 'sometimes|string',
            // Add validation for ALL other fields in your medical_records table
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