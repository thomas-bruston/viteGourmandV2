<?php

declare(strict_types=1);

namespace Entity;

class Allergene
{
    private ?int   $allergeneId;
    private string $libelle;

    public function __construct(
        ?int   $allergeneId = null,
        string $libelle     = ''
    ) {
        $this->allergeneId = $allergeneId;
        $this->libelle     = $libelle;
    }

    public function getAllergeneId(): ?int { return $this->allergeneId; }
    public function getLibelle(): string  { return $this->libelle; }

    public function setAllergeneId(?int $id): void { $this->allergeneId = $id; }
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
            allergeneId: isset($data['allergene_id']) ? (int) $data['allergene_id'] : null,
            libelle:     $data['libelle'] ?? '',
        );
    }
}