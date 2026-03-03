<?php

declare(strict_types=1);

namespace Entity;


class Menu
{
    private ?int    $menuId;
    private string  $titre;
    private string  $description;
    private int     $nombrePersonneMinimum;
    private float   $prixParPersonne;
    private int     $quantiteRestante;
    private string  $image;
    private bool    $actif;

    private array   $themes   = [];
    private array   $regimes  = [];
    private array   $plats    = [];

    public function __construct(
        ?int   $menuId                = null,
        string $titre                 = '',
        string $description           = '',
        int    $nombrePersonneMinimum = 2,
        float  $prixParPersonne       = 0.0,
        int    $quantiteRestante      = 100,
        string $image                 = '',
        bool   $actif                 = true
    ) {
        $this->menuId                = $menuId;
        $this->titre                 = $titre;
        $this->description           = $description;
        $this->nombrePersonneMinimum = $nombrePersonneMinimum;
        $this->prixParPersonne       = $prixParPersonne;
        $this->quantiteRestante      = $quantiteRestante;
        $this->image                 = $image;
        $this->actif                 = $actif;
    }

    /* Getters */

    public function getMenuId(): ?int               { return $this->menuId; }
    public function getTitre(): string              { return $this->titre; }
    public function getDescription(): string        { return $this->description; }
    public function getNombrePersonneMinimum(): int { return $this->nombrePersonneMinimum; }
    public function getPrixParPersonne(): float     { return $this->prixParPersonne; }
    public function getQuantiteRestante(): int      { return $this->quantiteRestante; }
    public function getImage(): string              { return $this->image; }
    public function isActif(): bool                 { return $this->actif; }
    public function getThemes(): array              { return $this->themes; }
    public function getRegimes(): array             { return $this->regimes; }
    public function getPlats(): array               { return $this->plats; }

    /* Calcul prix total */

    public function calculerPrixTotal(int $nombrePersonnes): float
    {
        return round($this->prixParPersonne * $nombrePersonnes, 2);
    }



    public function setMenuId(?int $menuId): void
    {
        $this->menuId = $menuId;
    }

    public function setTitre(string $titre): void
    {
        $titre = trim($titre);
        if (empty($titre) || strlen($titre) > 255) {
            throw new \InvalidArgumentException('Titre invalide.');
        }
        $this->titre = $titre;
    }

    public function setDescription(string $description): void
    {
        if (empty(trim($description))) {
            throw new \InvalidArgumentException('Description invalide.');
        }
        $this->description = $description;
    }

    public function setNombrePersonneMinimum(int $min): void
    {
        if ($min < 1) {
            throw new \InvalidArgumentException('Le nombre minimum de personnes doit être >= 1.');
        }
        $this->nombrePersonneMinimum = $min;
    }

    public function setPrixParPersonne(float $prix): void
    {
        if ($prix <= 0) {
            throw new \InvalidArgumentException('Le prix par personne doit être positif.');
        }
        $this->prixParPersonne = $prix;
    }

    public function setQuantiteRestante(int $quantite): void
    {
        if ($quantite < 0) {
            throw new \InvalidArgumentException('La quantité ne peut pas être négative.');
        }
        $this->quantiteRestante = $quantite;
    }

    public function setImage(string $image): void  { $this->image = $image; }
    public function setActif(bool $actif): void     { $this->actif = $actif; }
    public function setThemes(array $themes): void  { $this->themes = $themes; }
    public function setRegimes(array $regimes): void { $this->regimes = $regimes; }
    public function setPlats(array $plats): void    { $this->plats = $plats; }

    public static function fromArray(array $data): static
    {
        return new static(
            menuId:                (int) $data['menu_id'],
            titre:                 $data['titre']                   ?? '',
            description:           $data['description']             ?? '',
            nombrePersonneMinimum: (int) ($data['nombre_personne_minimum'] ?? 2),
            prixParPersonne:       (float) ($data['prix_par_personne']     ?? 0),
            quantiteRestante:      (int) ($data['quantite_restante']       ?? 100),
            image:                 $data['image']                   ?? '',
            actif:                 (bool) ($data['actif']           ?? true),
        );
    }
}
