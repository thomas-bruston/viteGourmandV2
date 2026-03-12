<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Service\PriceService;
use Entity\Menu;

class PriceServiceTest extends TestCase
{
    private PriceService $priceService;
    private Menu $menu;

    protected function setUp(): void
    {
        $this->priceService = new PriceService();

        // Menu de test : 25€/pers, minimum 2 personnes
        $this->menu = new Menu(
            menuId:                1,
            titre:                 'Menu Test',
            description:           'Description test',
            nombrePersonneMinimum: 2,
            prixParPersonne:       25.0,
            quantiteRestante:      100,
            image:                 '',
            actif:                 true
        );
    }

    // ---- calculerPrixMenu ----

    public function testPrixMenuNormal(): void
    {
        $result = $this->priceService->calculerPrixMenu($this->menu, 4);
        $this->assertEquals(100.00, $result); // 25 x 4 = 100
    }

    public function testPrixMenuMinimum(): void
    {
        $result = $this->priceService->calculerPrixMenu($this->menu, 2);
        $this->assertEquals(50.00, $result); // 25 x 2 = 50
    }

    public function testPrixMenuSousMinimumLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->priceService->calculerPrixMenu($this->menu, 1); // minimum est 2
    }

    // ---- calculerFraisLivraison ----

    public function testLivraisonBordeaux(): void
    {
        $result = $this->priceService->calculerFraisLivraison('33000', 0);
        $this->assertEquals(5.00, $result);
    }



    public function testLivraisonHorsBordeauxAvecKm(): void
{
    $result = $this->priceService->calculerFraisLivraison('34000', 10);
    $this->assertEquals(10.90, $result); // 5 + (10 * 0.59) = 10.90
}

    // ---- calculerTotal ----

    public function testTotalBordeaux(): void
    {
        $result = $this->priceService->calculerTotal($this->menu, 4, '33000');
        $this->assertEquals(100.00, $result['prix_menu']);
        $this->assertEquals(5.00,   $result['prix_livraison']);
        $this->assertEquals(105.00, $result['prix_total']);
    }

    public function testTotalHorsBordeaux(): void
    {
        $result = $this->priceService->calculerTotal($this->menu, 4, '34000', 10);
        $this->assertEquals(100.00, $result['prix_menu']);
        $this->assertEquals(10.90,  $result['prix_livraison']);
        $this->assertEquals(110.90, $result['prix_total']);
    }

    public function testRetourTableauAvecClesCorrectes(): void
    {
        $result = $this->priceService->calculerTotal($this->menu, 4, '33000');
        $this->assertArrayHasKey('prix_menu',      $result);
        $this->assertArrayHasKey('prix_livraison', $result);
        $this->assertArrayHasKey('prix_total',     $result);
    }
}