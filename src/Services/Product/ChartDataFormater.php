<?php

namespace App\Services\Product;

use App\Entity\ProductPriceHistory;

class ChartDataFormater
{
     /**
     * Formate une liste d'objets ProductPriceHistory en un tableau exploitable pour un graphique.
     *
     * Chaque élément retourné contient :
     * - 'label' : la date et l'heure du changement de prix, formatée en "d/m/Y H:i"
     * - 'value' : le nouveau prix appliqué à cette date
     *
     * @param ProductPriceHistory[] $data Liste d'objets représentant l'historique des prix d'un produit
     * @return array<int, array{label: string, value: float}> Données formatées pour affichage graphique
     */
    public function formatProductPrice(array $data):array
    {
       return array_map(fn(ProductPriceHistory $item) => [
            'label' => $item->getChangedAt()->format('d/m/Y H:i'),
            'value' => $item->getNewPrice(),
        ], $data);
    }
}