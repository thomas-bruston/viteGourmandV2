<?php

declare(strict_types=1);

namespace Entity;

class Commande
{
    public const STATUT_EN_ATTENTE                = 'en_attente';
    public const STATUT_ACCEPTEE                  = 'acceptee';
    public const STATUT_EN_PREPARATION            = 'en_preparation';
    public const STATUT_EN_COURS_LIVRAISON        = 'en_cours_livraison';
    public const STATUT_LIVREE                    = 'livree';
    public const STATUT_EN_ATTENTE_RETOUR_MATERIEL = 'en_attente_retour_materiel';
    public const STATUT_TERMINEE                  = 'terminee';
    public const STATUT_ANNULEE                   = 'annulee';

    public const STATUTS_VALIDES = [
        self::STATUT_EN_ATTENTE,
        self::STATUT_ACCEPTEE,
        self::STATUT_EN_PREPARATION,
        self::STATUT_EN_COURS_LIVRAISON,
        self::STATUT_LIVREE,
        self::STATUT_EN_ATTENTE_RETOUR_MATERIEL,
        self::STATUT_TERMINEE,
        self::STATUT_ANNULEE,
    ];

    private ?int    $commandeId;
    private int     $utilisateurId;
    private int     $menuId;
    private string  $numeroCommande;
    private ?string $dateCommande;
    private string  $datePrestation;
    private string  $heureLivraison;
    private string  $adresseLivraison;
    private string  $codePostalLivraison;
    private string  $villeLivraison;
    private int     $nombrePersonnes;
    private float   $prixMenu;
    private float   $prixLivraison;
    private float   $prixTotal;
    private string  $statut;
    private ?string $motifAnnulation;

    public function __construct(
        ?int    $commandeId            = null,
        int     $utilisateurId         = 0,
        int     $menuId                = 0,
        string  $numeroCommande        = '',
        ?string $dateCommande          = null,
        string  $datePrestation        = '',
        string  $heureLivraison        = '',
        string  $adresseLivraison      = '',
        string  $codePostalLivraison   = '',
        string  $villeLivraison        = '',
        int     $nombrePersonnes       = 0,
        float   $prixMenu              = 0.0,
        float   $prixLivraison         = 5.0,
        float   $prixTotal             = 0.0,
        string  $statut                = self::STATUT_EN_ATTENTE,
        ?string $motifAnnulation       = null,
    ) {
        $this->commandeId            = $commandeId;
        $this->utilisateurId         = $utilisateurId;
        $this->menuId                = $menuId;
        $this->numeroCommande        = $numeroCommande;
        $this->dateCommande          = $dateCommande;
        $this->datePrestation        = $datePrestation;
        $this->heureLivraison        = $heureLivraison;
        $this->adresseLivraison      = $adresseLivraison;
        $this->codePostalLivraison   = $codePostalLivraison;
        $this->villeLivraison        = $villeLivraison;
        $this->nombrePersonnes       = $nombrePersonnes;
        $this->prixMenu              = $prixMenu;
        $this->prixLivraison         = $prixLivraison;
        $this->prixTotal             = $prixTotal;
        $this->statut                = $statut;
        $this->motifAnnulation       = $motifAnnulation;

    }

    /* Getters */

    public function getCommandeId(): ?int             { return $this->commandeId; }
    public function getUtilisateurId(): int           { return $this->utilisateurId; }
    public function getMenuId(): int                  { return $this->menuId; }
    public function getNumeroCommande(): string       { return $this->numeroCommande; }
    public function getDateCommande(): ?string        { return $this->dateCommande; }
    public function getDatePrestation(): string       { return $this->datePrestation; }
    public function getHeureLivraison(): string       { return $this->heureLivraison; }
    public function getAdresseLivraison(): string     { return $this->adresseLivraison; }
    public function getCodePostalLivraison(): string  { return $this->codePostalLivraison; }
    public function getVilleLivraison(): string       { return $this->villeLivraison; }
    public function getNombrePersonnes(): int         { return $this->nombrePersonnes; }
    public function getPrixMenu(): float              { return $this->prixMenu; }
    public function getPrixLivraison(): float         { return $this->prixLivraison; }
    public function getPrixTotal(): float             { return $this->prixTotal; }
    public function getStatut(): string               { return $this->statut; }
    public function getMotifAnnulation(): ?string     { return $this->motifAnnulation; }

    /* Modif commande user */

    public function estModifiable(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

   
    public function getStatutLibelle(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE                => 'En attente',
            self::STATUT_ACCEPTEE                  => 'Acceptée',
            self::STATUT_EN_PREPARATION            => 'En préparation',
            self::STATUT_EN_COURS_LIVRAISON        => 'En cours de livraison',
            self::STATUT_LIVREE                    => 'Livrée',
            self::STATUT_EN_ATTENTE_RETOUR_MATERIEL => 'En attente retour matériel',
            self::STATUT_TERMINEE                  => 'Terminée',
            self::STATUT_ANNULEE                   => 'Annulée',
            default                                => ucfirst($this->statut),
        };
    }

    /* Setters */

    public function setCommandeId(?int $id): void { $this->commandeId = $id; }

    public function setStatut(string $statut): void
    {
        if (!in_array($statut, self::STATUTS_VALIDES)) {
            throw new \InvalidArgumentException("Statut invalide : {$statut}");
        }
        $this->statut = $statut;
    }

    public function setNombrePersonnes(int $nb): void
    {
        if ($nb < 1) {
            throw new \InvalidArgumentException('Le nombre de personnes doit être >= 1.');
        }
        $this->nombrePersonnes = $nb;
    }

    public function setPrixMenu(float $prix): void          { $this->prixMenu = $prix; }
    public function setPrixLivraison(float $prix): void     { $this->prixLivraison = $prix; }
    public function setPrixTotal(float $prix): void         { $this->prixTotal = $prix; }
    public function setMotifAnnulation(?string $m): void    { $this->motifAnnulation = $m; }
    public function setDatePrestation(string $d): void      { $this->datePrestation = $d; }
    public function setHeureLivraison(string $h): void      { $this->heureLivraison = $h; }
    public function setAdresseLivraison(string $a): void    { $this->adresseLivraison = $a; }
    public function setCodePostalLivraison(string $c): void { $this->codePostalLivraison = $c; }
    public function setVilleLivraison(string $v): void      { $this->villeLivraison = $v; }
    public function setNumeroCommande(string $n): void      { $this->numeroCommande = $n; }
    public function setUtilisateurId(int $id): void         { $this->utilisateurId = $id; }
    public function setMenuId(int $id): void                { $this->menuId = $id; }

    public static function fromArray(array $data): static
    {
        return new static(
            commandeId:            isset($data['commande_id']) ? (int) $data['commande_id'] : null,
            utilisateurId:         (int) ($data['utilisateur_id']       ?? 0),
            menuId:                (int) ($data['menu_id']              ?? 0),
            numeroCommande:        $data['numero_commande']             ?? '',
            dateCommande:          $data['date_commande']               ?? null,
            datePrestation:        $data['date_prestation']             ?? '',
            heureLivraison:        $data['heure_livraison']             ?? '',
            adresseLivraison:      $data['adresse_livraison']           ?? '',
            codePostalLivraison:   $data['code_postal_livraison']       ?? '',
            villeLivraison:        $data['ville_livraison']             ?? '',
            nombrePersonnes:       (int) ($data['nombre_personnes']     ?? 0),
            prixMenu:              (float) ($data['prix_menu']          ?? 0),
            prixLivraison:         (float) ($data['prix_livraison']     ?? 5),
            prixTotal:             (float) ($data['prix_total']         ?? 0),
            statut:                $data['statut']                      ?? self::STATUT_EN_ATTENTE,
            motifAnnulation:       $data['motif_annulation']            ?? null,
           
        );
    }
}
