<?php

declare(strict_types=1);

namespace Entity;

class Horaire
{
    private ?int   $horaireId;
    private string $texte;

    public function __construct(
        ?int   $horaireId = null,
        string $texte     = ''
    ) {
        $this->horaireId = $horaireId;
        $this->texte     = $texte;
    }

    public function getHoraireId(): ?int { return $this->horaireId; }
    public function getTexte(): string   { return $this->texte; }

    public function setHoraireId(?int $id): void { $this->horaireId = $id; }
    public function setTexte(string $texte): void
    {
        if (empty(trim($texte))) {
            throw new \InvalidArgumentException('Le texte des horaires ne peut pas être vide.');
        }
        $this->texte = trim($texte);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            horaireId: isset($data['horaire_id']) ? (int) $data['horaire_id'] : null,
            texte:     $data['texte'] ?? '',
        );
    }
}