<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('last_updated_by_doctor_id')->nullable(); 
            $table->unsignedBigInteger('patient_id')->nullable(); 
            // Identification & Antecedents
            $table->string('dossier_number')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('social_security_number')->nullable();
            $table->string('family_situation')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('study_field')->nullable();

            // Intoxications: TABACS
            $table->boolean('smoker')->nullable();
            $table->integer('cigarettes_per_day')->unsigned()->nullable();
            $table->boolean('chewer')->nullable();
            $table->integer('boxes_per_day_chew')->unsigned()->nullable();
            $table->boolean('snuff_user')->nullable();
            $table->integer('boxes_per_day_snuff')->unsigned()->nullable();
            $table->integer('age_first_tobacco_use')->unsigned()->nullable();
            $table->boolean('former_smoker')->nullable();
            $table->string('exposure_period')->nullable();

            // Intoxications: Other
            $table->text('alcohol_details')->nullable();
            $table->text('medication_details')->nullable();
            $table->text('other_intoxications')->nullable();

            // Antecedents Medico-Chirurgicaux
            $table->text('congenital_conditions')->nullable();
            $table->text('general_diseases')->nullable();
            $table->text('surgical_interventions')->nullable();
            $table->text('medication_allergies')->nullable();

            // Clinical Exams Base
            $table->float('weight_kg', 5, 2)->unsigned()->nullable();
            $table->integer('height_cm')->unsigned()->nullable();

            // Acuite Visuelle
            $table->string('visual_acuity_od_sc')->nullable();
            $table->string('visual_acuity_og_sc')->nullable();
            $table->string('visual_acuity_od_wc')->nullable();
            $table->string('visual_acuity_og_wc')->nullable();

            // Audition
            $table->string('audition_od')->nullable();
            $table->string('audition_og')->nullable();

            // PEAU ET MUQUEUSES
            $table->text('skin_conditions')->nullable();
            $table->text('skin_exam_notes')->nullable();

            // OPHTALMOLOGIQUE
            $table->boolean('oph_tearing')->nullable();
            $table->boolean('oph_pain')->nullable();
            $table->boolean('oph_spots')->nullable();
            $table->text('oph_exam_notes')->nullable();

            // O.R.L. (ENT)
            $table->boolean('orl_tinnitus')->nullable();
            $table->boolean('orl_repeated_angina')->nullable();
            $table->boolean('orl_epistaxis')->nullable();
            $table->boolean('orl_rhinorrhea')->nullable();
            $table->text('orl_other')->nullable();
            $table->text('orl_exam_notes')->nullable();

            // LOCOMOTEUR
            $table->boolean('loc_pain_muscular')->nullable();
            $table->boolean('loc_pain_articular')->nullable();
            $table->boolean('loc_pain_vertebral')->nullable();
            $table->boolean('loc_pain_neurological')->nullable();
            $table->boolean('loc_movement_difficulty')->nullable();
            $table->boolean('loc_fatigability')->nullable();
            $table->text('loc_exam_notes')->nullable();

            // RESPIRATOIRE
            $table->boolean('res_cough')->nullable();
            $table->boolean('res_dyspnea_nocturnal')->nullable();
            $table->boolean('res_dyspnea_diurnal')->nullable();
            $table->text('res_expectorations')->nullable();
            $table->boolean('res_thoracic_pain')->nullable();
            $table->text('res_other')->nullable();
            $table->integer('respiratory_rate')->unsigned()->nullable();
            $table->text('res_exam_notes')->nullable();

            // CARDIO-VASCULAIRE
            $table->boolean('car_palpitations')->nullable();
            $table->boolean('car_edema')->nullable();
            $table->boolean('car_pain_walking')->nullable();
            $table->boolean('car_pain_rest')->nullable();
            $table->boolean('car_pain_effort')->nullable();
            $table->boolean('car_pain_permanent')->nullable();
            $table->integer('pulse_rate')->unsigned()->nullable();
            $table->string('blood_pressure', 15)->nullable();
            $table->boolean('cyanosis')->nullable();
            $table->text('car_exam_notes')->nullable();

            // DIGESTIF
            $table->string('dig_appetite')->nullable();
            $table->string('dig_transit')->nullable();
            $table->string('dig_stools')->nullable();
            $table->boolean('dig_pyrosis')->nullable();
            $table->boolean('dig_vomiting')->nullable();
            $table->boolean('dig_rectorrhagia')->nullable();
            $table->boolean('dig_abdominal_pain')->nullable();
            $table->text('dig_other')->nullable();
            $table->text('dig_denture_caries')->nullable();
            $table->text('dig_gingivopathy')->nullable();
            $table->text('dig_other_mouth')->nullable();
            $table->text('dig_abdomen_notes')->nullable();
            $table->text('dig_hernia_notes')->nullable();
            $table->text('dig_liver_notes')->nullable();

            // GENITO-URINAIRE
            $table->boolean('gen_micturition_pollakiuria')->nullable();
            $table->boolean('gen_micturition_dysuria')->nullable();
            $table->boolean('gen_hematuria')->nullable();
            $table->boolean('gen_micturition_burning')->nullable();
            $table->boolean('gen_nephritic_colic')->nullable();
            $table->text('gen_losses')->nullable();
            $table->string('gen_cycles')->nullable();
            $table->text('gen_other')->nullable();
            $table->text('gen_bourses_notes')->nullable();
            $table->text('gen_breasts_notes')->nullable();
            $table->text('gen_tr_notes')->nullable();
            $table->text('gen_tv_notes')->nullable();

            // NEUROLOGIQUE ET PSYCHISME
            $table->string('neu_sleep')->nullable();
            $table->boolean('neu_headaches')->nullable();
            $table->boolean('neu_vertigo')->nullable();
            $table->boolean('neu_agoraphobia')->nullable();
            $table->boolean('neu_loss_consciousness')->nullable();
            $table->boolean('neu_paresis')->nullable();
            $table->boolean('neu_paresthesia')->nullable();
            $table->text('neu_other')->nullable();
            $table->text('neu_tremor_notes')->nullable();
            $table->text('neu_romberg_notes')->nullable();
            $table->text('neu_reflexes_ro')->nullable();
            $table->text('neu_reflexes_ach')->nullable();
            $table->text('neu_coordination_notes')->nullable();
            $table->text('neu_sensitivity_notes')->nullable();
            $table->text('neu_motricity_notes')->nullable();
            $table->text('neu_ocular_notes')->nullable();

            // HEMATOLOGIQUE ET GANGLIONNAIRE
            $table->boolean('hem_ecchymoses')->nullable();
            $table->boolean('hem_bleeding_tendency')->nullable();
            $table->text('hem_petechiae_notes')->nullable();
            $table->text('hem_purpura_notes')->nullable();
            $table->text('hem_spleen_notes')->nullable();
            $table->text('hem_ganglions_cervical')->nullable();
            $table->text('hem_ganglions_axillary')->nullable();
            $table->text('hem_ganglions_clavicular')->nullable();
            $table->text('hem_ganglions_inguinal')->nullable();

            // ENDOCRINOLOGIE
            $table->boolean('end_family_obesity')->nullable();
            $table->boolean('end_family_thinness')->nullable();
            $table->text('end_thyroid_notes')->nullable();
            $table->text('end_testicles_notes')->nullable();
            $table->text('end_mammary_notes')->nullable();

            // PROFIL PSYCHOLOGIQUE
            $table->text('psychological_profile')->nullable();

            // EXPLORATIONS FONCTIONNELLES
            $table->text('exp_func_respiratory')->nullable();
            $table->text('exp_func_circulatory')->nullable();
            $table->text('exp_func_motor')->nullable();

            // EXAMENS COMPLEMENTAIRES
            $table->text('exam_comp_radiological')->nullable();
            $table->text('exam_comp_bio_blood')->nullable();
            $table->text('exam_comp_bio_urinary')->nullable();
            $table->text('exam_comp_hep_viral')->nullable();
            $table->text('exam_comp_syphilis')->nullable();
            $table->text('exam_comp_hiv')->nullable();

            // Aptitude E.P.S.
            $table->boolean('eps_apt')->nullable();
            $table->text('eps_motifs')->nullable();

            // ORIENTATIONS
            $table->text('orientation_specialist')->nullable();
            $table->boolean('orientation_opinion')->nullable();
            $table->boolean('orientation_hospitalization')->nullable();
            $table->boolean('orientation_treatment')->nullable();
            $table->text('orientation_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
