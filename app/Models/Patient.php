<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class Patient extends Authenticatable implements JWTSubject 
{
    use HasFactory,Notifiable;

    protected $guarded = [] ;

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function medicalRecord() {
        return $this->hasOne(MedicalRecord::class,"patient_id");
    }
}
