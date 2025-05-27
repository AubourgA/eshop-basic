<?php

namespace App\Enum;

enum MarketingPosition: string
{
    case BEST_SELLER = 'Best Seller';
    case NEW = 'Nouveauté';
    case SEASONAL = 'DE SAISON';
    case PROMO ="En Promotion";

    public function label(): string
    {
        return match ($this) {
            self::BEST_SELLER => 'Best Seller',
            self::NEW => 'Nouveauté',
            self::SEASONAL => 'DE SAISON',
            self::PROMO => 'En Promotion',
        };
    }

    public static function choices(): array
    {
        return [
            'Best-seller' => self::BEST_SELLER,
            'Nouveauté' => self::NEW,
            'Produit saisonnier' => self::SEASONAL,
        ];
    }
}