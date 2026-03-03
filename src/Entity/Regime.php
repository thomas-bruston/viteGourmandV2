<?php

declare(strict_types=1);

namespace Entity;

class Regime
{
    private ?int   $regimeId;
    private string $libelle;

    public function __construct(
        ?int   $regimeId = null,
        string $libelle  = ''
    ) {
        $this->regimeId = $regimeId;
        $this->libelle  = $libelle;
    }

    public function getRegimeId(): ?int  { return $this->regimeId; }
    public function getLibelle(): string { return $this->libelle; }

    public function setRegimeId(?int $id): void { $this->regimeId = $id; }
    public function setLibelle(string $l): void
    {
        if (empty(trim($l))) {
            throw new \InvalidArgumentException('Le libellé ne peut pas être vide.');
        }
        $this->libelle = trim($l);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            regimeId: isset($data['regime_id']) ? (int) $data['regime_id'] : null,
            libelle:  $data['libelle'] ?? '',
        );
    }
}