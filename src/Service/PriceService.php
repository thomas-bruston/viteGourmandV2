<?php

declare(strict_types=1);

namespace Service;

use Entity\Menu;

class PriceService
{
    private const FRAIS_LIVRAISON_BASE    = 5.00;
    private const PRIX_PAR_KM            = 0.59;
    private const CODE_POSTAL_BORDEAUX   = '33000';

    /* Calcule prix menu avce personnes */

    public function calculerPrixMenu(Menu $menu, int $nombrePersonnes): float
    {
        if ($nombrePersonnes < $menu->getNombrePersonneMinimum()) {
            throw new \InvalidArgumentException(
                "Le nombre minimum de personnes pour ce menu est {$menu->getNombrePersonneMinimum()}."
            );
        }

        return round($menu->getPrixParPersonne() * $nombrePersonnes, 2);
    }

    /* Calcul frais livraison */

    public function calculerFraisLivraison(string $codePostal, float $distanceKm = 0): float
        {
            if ($codePostal === self::CODE_POSTAL_BORDEAUX) {
                return self::FRAIS_LIVRAISON_BASE; // 5€
            }
            return round(
                self::FRAIS_LIVRAISON_BASE + ($distanceKm * self::PRIX_PAR_KM),
                2
            );
        }

    /* Calcul prix total commande */

    public function calculerTotal(
        Menu   $menu,
        int    $nombrePersonnes,
        string $codePostal,
        float  $distanceKm = 0
    ): array {
        $prixMenu      = $this->calculerPrixMenu($menu, $nombrePersonnes);
        $prixLivraison = $this->calculerFraisLivraison($codePostal, $distanceKm);
        $prixTotal     = round($prixMenu + $prixLivraison, 2);

        return [
            'prix_menu'      => $prixMenu,
            'prix_livraison' => $prixLivraison,
            'prix_total'     => $prixTotal,
        ];
    }

}
