<?php

namespace App\Enum;

enum OrderStatus: string
{
    case IN_PROGRESS = 'En cours';
    case PROCESSING = 'En traitement';
    case SHIPPED = 'Envoyé';
    case DELIVERY = 'Livré';

   
}