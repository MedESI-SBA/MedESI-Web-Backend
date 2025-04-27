<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function consultation() {
        return $this->belongsTo(Consultation::class,"consultation_id");
    }
    public function prescriptionItems() {
        return $this->hasMany(PrescriptionItem::class,"prescription_id");
    }
}
