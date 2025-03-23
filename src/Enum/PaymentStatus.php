<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case PAYED = 'payé';
    case PENDING = 'en attente';
    case FAILED = 'échec';

   
}