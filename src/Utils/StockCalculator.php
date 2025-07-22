<?php

namespace App\Utils;

/**
 * Classe utilitaire pour effectuer les calculs liés au stock.
 *
 * Cette classe contient des méthodes statiques permettant d'isoler la logique de calcul,
 * comme la détermination de la quantité disponible à la vente en fonction des réservations.
 */
final class StockCalculator
{
    /**
     * Calcule la quantité disponible en fonction de la quantité physique et des réservations.
     *
     * @param int $physicalQuantity Quantité physique actuelle en stock.
     * @param int $reserved Quantité réservée (commandes en cours ou à expédier).
     *
     * @return int Quantité disponible à la vente (jamais négative).
     */
   public static function calculateAvailableQuantity(int $physicalQuantity, int $reserved): int
    {
        return max($physicalQuantity - $reserved, 0);
    }


    /**
     * Calcul full quantity of products in stock.
     *
     * @param array $stocks Tableau d'objets Stock, chaque objet devant avoir une méthode getQuantity().
     *
     * @return int Quantité totale de produits en stock.
     */
     public static function calculateFullStockQuantity(array $stocks): float
    {
        $totalProduct = 0.0;

        foreach ($stocks as $stock) {
            $totalProduct += $stock->getQuantity();
        }

        return $totalProduct;
    }


    /**
     * Calcul value of full stock based on the quantity and price of each product.
     *
     * @param array $stocks Tableau d'objets Stock, chaque objet devant avoir une méthode getQuantity()
     *                      et un objet Product avec une méthode getPrice().
     *
     * @return float Valeur totale du stock.
     */
    public static function calculateFullStockValue(array $stocks): float
    {
        $totalValue = 0.0;

        foreach ($stocks as $stock) {
            $totalValue += $stock->getQuantity() * $stock->getProduct()->getPrice();
        }

        return $totalValue;
    }

    
}