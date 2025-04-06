<?php

namespace App\Enums;

enum UserTypes: string
{
    case ADMIN = 'admin';
    case PATIENT = 'patient';
    case DOCTOR = 'doctor';
}



