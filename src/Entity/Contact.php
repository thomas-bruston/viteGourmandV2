<?php

declare(strict_types=1);

namespace Entity;


class Contact
{
    private ?int    $contactId;
    private string  $nom;
    private string  $prenom;
    private string  $email;
    private string  $titre;
    private string  $message;
    private ?string $dateEnvoi;
    private bool    $lu;

    public function __construct(
        ?int    $contactId = null,
        string  $nom       = '',
        string  $prenom    = '',
        string  $email     = '',
        string  $titre     = '',
        string  $message   = '',
        ?string $dateEnvoi = null,
        bool    $lu        = false
    ) {
        $this->contactId = $contactId;
        $this->nom       = $nom;
        $this->prenom    = $prenom;
        $this->email     = $email;
        $this->titre     = $titre;
        $this->message   = $message;
        $this->dateEnvoi = $dateEnvoi;
        $this->lu        = $lu;
    }

    /* Getters */

    public function getContactId(): ?int  { return $this->contactId; }
    public function getNom(): string      { return $this->nom; }
    public function getPrenom(): string   { return $this->prenom; }
    public function getEmail(): string    { return $this->email; }
    public function getTitre(): string    { return $this->titre; }
    public function getMessage(): string  { return $this->message; }
    public function getDateEnvoi(): ?string { return $this->dateEnvoi; }
    public function isLu(): bool          { return $this->lu; }

    /* Setters */

    public function setContactId(?int $id): void { $this->contactId = $id; }
    public function setLu(bool $lu): void         { $this->lu = $lu; }

    public function setNom(string $nom): void
    {
        $nom = trim($nom);
        if (empty($nom)) throw new \InvalidArgumentException('Nom invalide.');
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void
    {
        $prenom = trim($prenom);
        if (empty($prenom)) throw new \InvalidArgumentException('Prénom invalide.');
        $this->prenom = $prenom;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email invalide.');
        }
        $this->email = trim($email);
    }

    public function setTitre(string $titre): void
    {
        $titre = trim($titre);
        if (empty($titre)) throw new \InvalidArgumentException('Titre invalide.');
        $this->titre = $titre;
    }

    public function setMessage(string $message): void
    {
        $message = trim($message);
        if (empty($message)) throw new \InvalidArgumentException('Message invalide.');
        $this->message = $message;
    }

    public static function fromArray(array $data): static
    {
        return new static(
            contactId: isset($data['contact_id']) ? (int) $data['contact_id'] : null,
            nom:       $data['nom']        ?? '',
            prenom:    $data['prenom']     ?? '',
            email:     $data['email']      ?? '',
            titre:     $data['titre']      ?? '',
            message:   $data['message']    ?? '',
            dateEnvoi: $data['date_envoi'] ?? null,
            lu:        (bool) ($data['lu'] ?? false),
        );
    }
}
