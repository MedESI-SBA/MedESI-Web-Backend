<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    /** @use HasFactory<\Database\Factories\ConsultationFactory> */
    use HasFactory;

    protected $guarded = [];

    public function appointment() {
        return $this->belongsTo(Appointments::class,"appointment_id");
    }
    public function prescription() {
        return $this->hasOne(Prescription::class,'consultation_id');
    }
    public function doctor() {
        return $this->belongsTo(Doctor::class,"doctor_id");
    }
    
    public function patient() {
        return $this->belongsTo(Patient::class,"patient_id");
    }
}
