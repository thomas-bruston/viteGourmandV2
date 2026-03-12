<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Entity\Avis;

class AvisTest extends TestCase
{
    // ---- setNote ----

    public function testNoteValide(): void
    {
        $avis = new Avis();
        $avis->setNote(5);
        $this->assertEquals(5, $avis->getNote());
    }

    public function testNoteMinimum(): void
    {
        $avis = new Avis();
        $avis->setNote(1);
        $this->assertEquals(1, $avis->getNote());
    }

    public function testNoteTropEleveeLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $avis = new Avis();
        $avis->setNote(6);
    }

    public function testNoteZeroLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $avis = new Avis();
        $avis->setNote(0);
    }

    // ---- setCommentaire ----

    public function testCommentaireValide(): void
    {
        $avis = new Avis();
        $avis->setCommentaire('Super menu !');
        $this->assertEquals('Super menu !', $avis->getCommentaire());
    }

    public function testCommentaireVideLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $avis = new Avis();
        $avis->setCommentaire('');
    }

    // ---- setStatut ----

    public function testStatutInvalideLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $avis = new Avis();
        $avis->setStatut('invalide');
    }
}