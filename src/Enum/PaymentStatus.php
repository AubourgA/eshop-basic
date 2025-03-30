<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case PENDING = 'En attente';
    case PAYED = 'Payé';
    case FAILED = 'Echec';

   
}