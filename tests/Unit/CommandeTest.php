<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Entity\Commande;

class CommandeTest extends TestCase
{
    // ---- estModifiable ----

    public function testEstModifiableEnAttente(): void
    {
        $commande = new Commande(statut: Commande::STATUT_EN_ATTENTE);
        $this->assertTrue($commande->estModifiable());
    }

    public function testEstModifiableAccepteeRetourneFalse(): void
    {
        $commande = new Commande(statut: Commande::STATUT_ACCEPTEE);
        $this->assertFalse($commande->estModifiable());
    }

    public function testEstModifiableTermineeRetourneFalse(): void
    {
        $commande = new Commande(statut: Commande::STATUT_TERMINEE);
        $this->assertFalse($commande->estModifiable());
    }

    // ---- setNombrePersonnes ----

    public function testNombrePersonnesValide(): void
    {
        $commande = new Commande();
        $commande->setNombrePersonnes(4);
        $this->assertEquals(4, $commande->getNombrePersonnes());
    }

    public function testNombrePersonnesZeroLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $commande = new Commande();
        $commande->setNombrePersonnes(0);
    }

    // ---- getStatutLibelle ----

    public function testStatutLibelleEnAttente(): void
    {
        $commande = new Commande(statut: Commande::STATUT_EN_ATTENTE);
        $this->assertEquals('En attente', $commande->getStatutLibelle());
    }

    public function testStatutLibelleTerminee(): void
    {
        $commande = new Commande(statut: Commande::STATUT_TERMINEE);
        $this->assertEquals('Terminée', $commande->getStatutLibelle());
    }

    // ---- setStatut ----

    public function testStatutInvalideLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $commande = new Commande();
        $commande->setStatut('invalide');
    }
}