<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function prescription() {
        return $this->belongsTo(Prescription::class,"prescription_id");
    }
}
