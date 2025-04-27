<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Doctor extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\DoctorFactory> */
    use HasFactory,Notifiable;
    protected $guarded = [] ;


    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function appointments() {
        return $this->hasMany(Appointments::class,"doctor_id");
    }

    public function consultations() {
        return $this->hasMany(Consultation::class,"doctor_id");
    }
}
