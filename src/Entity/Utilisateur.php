<?php

declare(strict_types=1);

namespace Entity;

class Utilisateur
{
    private ?int    $utilisateurId;
    private int     $roleId;
    private string  $nom;
    private string  $prenom;
    private string  $email;
    private string  $motDePasse;
    private string  $telephone;
    private string  $adresse;
    private string  $codePostal;
    private string  $ville;
    private bool    $statut;
    private ?string $dateCreation;

    public function __construct(
        ?int    $utilisateurId = null,
        int     $roleId        = 1,
        string  $nom           = '',
        string  $prenom        = '',
        string  $email         = '',
        string  $motDePasse    = '',
        string  $telephone     = '',
        string  $adresse       = '',
        string  $codePostal    = '',
        string  $ville         = '',
        bool    $statut        = true,
        ?string $dateCreation  = null
    ) {
        $this->utilisateurId = $utilisateurId;
        $this->roleId        = $roleId;
        $this->nom           = $nom;
        $this->prenom        = $prenom;
        $this->email         = $email;
        $this->motDePasse    = $motDePasse;
        $this->telephone     = $telephone;
        $this->adresse       = $adresse;
        $this->codePostal    = $codePostal;
        $this->ville         = $ville;
        $this->statut        = $statut;
        $this->dateCreation  = $dateCreation;
    }

    /* Getters */

    public function getUtilisateurId(): ?int    { return $this->utilisateurId; }
    public function getRoleId(): int            { return $this->roleId; }
    public function getNom(): string            { return $this->nom; }
    public function getPrenom(): string         { return $this->prenom; }
    public function getEmail(): string          { return $this->email; }
    public function getMotDePasse(): string     { return $this->motDePasse; }
    public function getTelephone(): string      { return $this->telephone; }
    public function getAdresse(): string        { return $this->adresse; }
    public function getCodePostal(): string     { return $this->codePostal; }
    public function getVille(): string          { return $this->ville; }
    public function getStatut(): bool           { return $this->statut; }
    public function getDateCreation(): ?string  { return $this->dateCreation; }

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /* Setters */

    public function setUtilisateurId(?int $id): void
    {
        $this->utilisateurId = $id;
    }

    public function setRoleId(int $roleId): void
    {
        if (!in_array($roleId, [1, 2, 3])) {
            throw new \InvalidArgumentException("RoleId invalide : {$roleId}");
        }
        $this->roleId = $roleId;
    }

    public function setNom(string $nom): void
    {
        $nom = trim($nom);
        if (empty($nom) || strlen($nom) > 100) {
            throw new \InvalidArgumentException('Nom invalide.');
        }
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void
    {
        $prenom = trim($prenom);
        if (empty($prenom) || strlen($prenom) > 100) {
            throw new \InvalidArgumentException('Prénom invalide.');
        }
        $this->prenom = $prenom;
    }

    public function setEmail(string $email): void
    {
        $email = trim(strtolower($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email invalide.');
        }
        $this->email = $email;
    }

    public function setMotDePasse(string $hash): void
    {
        $this->motDePasse = $hash;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = trim($telephone);
    }

    public function setAdresse(string $adresse): void
    {
        $adresse = trim($adresse);
        if (empty($adresse)) {
            throw new \InvalidArgumentException('Adresse invalide.');
        }
        $this->adresse = $adresse;
    }

    public function setCodePostal(string $codePostal): void
    {
        $codePostal = trim($codePostal);
        if (!preg_match('/^\d{5}$/', $codePostal)) {
            throw new \InvalidArgumentException('Code postal invalide (5 chiffres requis).');
        }
        $this->codePostal = $codePostal;
    }

    public function setVille(string $ville): void
    {
        $ville = trim($ville);
        if (empty($ville)) {
            throw new \InvalidArgumentException('Ville invalide.');
        }
        $this->ville = $ville;
    }

    public function setStatut(bool $statut): void
    {
        $this->statut = $statut;
    }

    /* Construct user */
    public static function fromArray(array $data): static
    {
        return new static(
            utilisateurId: (int) $data['utilisateur_id'],
            roleId:        (int) $data['role_id'],
            nom:           $data['nom']          ?? '',
            prenom:        $data['prenom']        ?? '',
            email:         $data['email']         ?? '',
            motDePasse:    $data['mot_de_passe']  ?? '',
            telephone:     $data['telephone']     ?? '',
            adresse:       $data['adresse']       ?? '',
            codePostal:    $data['code_postal']   ?? '',
            ville:         $data['ville']         ?? '',
            statut:        (bool) ($data['statut'] ?? true),
            dateCreation:  $data['date_creation'] ?? null,
        );
    }
}
