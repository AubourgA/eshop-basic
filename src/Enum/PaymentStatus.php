<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case PENDING = 'En attente';
    case PAYED = 'Payé';
    case FAILED = 'Echec';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::PAYED => 'Payé',
            self::FAILED => 'Echec',
            
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'text-yellow-600 bg-yellow-100',
            self::PAYED => 'text-green-600 bg-green-100',
            self::FAILED => 'text-red-600 bg-red-100',
           
        };
    }
}