<?php

declare(strict_types=1);

namespace Entity;


class Plat
{
    private ?int   $platId;
    private int    $menuId;
    private int    $categorieId;
    private string $nom;
    private string $image;

    private string $categorieLibelle = '';
    private array  $allergenes       = [];

    public function __construct(
        ?int   $platId      = null,
        int    $menuId      = 0,
        int    $categorieId = 0,
        string $nom         = '',
        string $image       = ''
    ) {
        $this->platId      = $platId;
        $this->menuId      = $menuId;
        $this->categorieId = $categorieId;
        $this->nom         = $nom;
        $this->image       = $image;
    }

    /* Getters*/

    public function getPlatId(): ?int           { return $this->platId; }
    public function getMenuId(): int            { return $this->menuId; }
    public function getCategorieId(): int       { return $this->categorieId; }
    public function getNom(): string            { return $this->nom; }
    public function getImage(): string          { return $this->image; }
    public function getCategorieLibelle(): string { return $this->categorieLibelle; }
    public function getAllergenes(): array       { return $this->allergenes; }

    /* Setters */

    public function setPlatId(?int $id): void         { $this->platId = $id; }
    public function setMenuId(int $id): void          { $this->menuId = $id; }
    public function setCategorieId(int $id): void     { $this->categorieId = $id; }
    public function setImage(string $image): void     { $this->image = $image; }
    public function setCategorieLibelle(string $l): void { $this->categorieLibelle = $l; }
    public function setAllergenes(array $a): void     { $this->allergenes = $a; }

    public function setNom(string $nom): void
    {
        $nom = trim($nom);
        if (empty($nom)) {
            throw new \InvalidArgumentException('Nom du plat invalide.');
        }
        $this->nom = $nom;
    }

    public static function fromArray(array $data): static
    {
        $plat = new static(
            platId:      isset($data['plat_id']) ? (int) $data['plat_id'] : null,
            menuId:      (int) ($data['menu_id']      ?? 0),
            categorieId: (int) ($data['categorie_id'] ?? 0),
            nom:         $data['nom']   ?? '',
            image:       $data['image'] ?? '',
        );

        if (isset($data['categorie_libelle'])) {
            $plat->setCategorieLibelle($data['categorie_libelle']);
        }

        return $plat;
    }
}
