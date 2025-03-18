<?php

namespace App\Enums;

enum UserTypes: string
{
    case ADMIN = 'admin';
    case STUDENT = 'student';
    case DOCTOR = 'doctor';
}



