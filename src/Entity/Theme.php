<?php

declare(strict_types=1);

namespace Entity;

class Theme
{
    private ?int   $themeId;
    private string $libelle;

    public function __construct(
        ?int   $themeId = null,
        string $libelle = ''
    ) {
        $this->themeId = $themeId;
        $this->libelle = $libelle;
    }

    public function getThemeId(): ?int   { return $this->themeId; }
    public function getLibelle(): string { return $this->libelle; }

    public function setThemeId(?int $id): void { $this->themeId = $id; }
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
            themeId: isset($data['theme_id']) ? (int) $data['theme_id'] : null,
            libelle: $data['libelle'] ?? '',
        );
    }
}