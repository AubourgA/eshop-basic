<?php

namespace App\Enum;

enum OrderStatus: string
{
    case IN_PROGRESS = 'En cours';
    case PROCESSING = 'En traitement';
    case SHIPPED = 'Envoyé';
    case DELIVERY = 'Livré';

    public function getLabel(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'En cours',
            self::PROCESSING => 'En traitement',
            self::SHIPPED => 'Envoyé',
            self::DELIVERY => 'Livré',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'text-yellow-600 bg-yellow-100',
            self::PROCESSING => 'text-green-600 bg-green-100',
            self::SHIPPED => 'text-blue-600 bg-blue-100',
            self::DELIVERY => 'text-red-600 bg-red-100',
        };
    }

}