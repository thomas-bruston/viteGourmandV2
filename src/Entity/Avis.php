<?php

declare(strict_types=1);

namespace Entity;


class Avis
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_VALIDE     = 'valide';
    public const STATUT_REFUSE     = 'refuse';

    private ?int    $avisId;
    private int     $utilisateurId;
    private ?int     $commandeId;
    private int     $note;
    private string  $commentaire;
    private string  $statut;
    private ?string $dateAvis;
    private string $utilisateurNom    = '';
    private string $utilisateurPrenom = '';

    public function __construct(
        ?int    $avisId        = null,
        int     $utilisateurId = 0,
        ?int    $commandeId    = null,
        int     $note          = 5,
        string  $commentaire   = '',
        string  $statut        = self::STATUT_EN_ATTENTE,
        ?string $dateAvis      = null
    ) {
        $this->avisId        = $avisId;
        $this->utilisateurId = $utilisateurId;
        $this->commandeId    = $commandeId;
        $this->setNote($note);
        $this->commentaire   = $commentaire;
        $this->statut        = $statut;
        $this->dateAvis      = $dateAvis;
    }

    /* Getters */

    public function getAvisId(): ?int           { return $this->avisId; }
    public function getUtilisateurId(): int     { return $this->utilisateurId; }
    public function getCommandeId(): ?int       { return $this->commandeId; }
    public function getNote(): int              { return $this->note; }
    public function getCommentaire(): string    { return $this->commentaire; }
    public function getStatut(): string         { return $this->statut; }
    public function getDateAvis(): ?string      { return $this->dateAvis; }
    public function getUtilisateurNom(): string { return $this->utilisateurNom; }
    public function getUtilisateurPrenom(): string { return $this->utilisateurPrenom; }

    /* Setters */

    public function setAvisId(?int $id): void { $this->avisId = $id; }

    public function setNote(int $note): void
    {
        if ($note < 1 || $note > 5) {
            throw new \InvalidArgumentException('La note doit être comprise entre 1 et 5.');
        }
        $this->note = $note;
    }

    public function setCommentaire(string $commentaire): void
    {
        $commentaire = trim($commentaire);
        if (empty($commentaire)) {
            throw new \InvalidArgumentException('Le commentaire ne peut pas être vide.');
        }
        $this->commentaire = $commentaire;
    }

    public function setStatut(string $statut): void
    {
        $valides = [self::STATUT_EN_ATTENTE, self::STATUT_VALIDE, self::STATUT_REFUSE];
        if (!in_array($statut, $valides)) {
            throw new \InvalidArgumentException("Statut invalide : {$statut}");
        }
        $this->statut = $statut;
    }

    public function setUtilisateurNom(string $nom): void    { $this->utilisateurNom = $nom; }
    public function setUtilisateurPrenom(string $p): void   { $this->utilisateurPrenom = $p; }
    public function setUtilisateurId(int $id): void         { $this->utilisateurId = $id; }
    public function setCommandeId(?int $id): void           { $this->commandeId = $id; }

    public static function fromArray(array $data): static
    {
        $avis = new static(
            avisId:        isset($data['avis_id']) ? (int) $data['avis_id'] : null,
            utilisateurId: (int) ($data['utilisateur_id'] ?? 0),
            commandeId: isset($data['commande_id']) && $data['commande_id'] !== null 
            ? (int) $data['commande_id'] : null,
            note:          (int) ($data['note']           ?? 5),
            commentaire:   $data['commentaire']           ?? '',
            statut:        $data['statut']                ?? self::STATUT_EN_ATTENTE,
            dateAvis:      $data['date_avis']             ?? null,
        );

        if (isset($data['utilisateur_nom'])) {
            $avis->setUtilisateurNom($data['utilisateur_nom']);
        }
        if (isset($data['utilisateur_prenom'])) {
            $avis->setUtilisateurPrenom($data['utilisateur_prenom']);
        }

        return $avis;
    }
}
