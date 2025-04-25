<?php

namespace App\Enums;

enum AppoitmentStatus: string
{
    case REQUESTED = "requested";
    case SCHEDULED = "scheduled";
    case CANCELLED = "cancelled";
    case COMPLETED  = "completed"; 
}



