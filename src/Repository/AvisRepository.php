<?php

declare(strict_types=1);

namespace Repository;

use Entity\Avis;
use PDOException;


class AvisRepository extends AbstractRepository
{
    /**
     * Récupère tous les avis validés (page d'accueil)
     * @return Avis[]
     */
    public function findValides(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT a.*, u.nom AS utilisateur_nom, u.prenom AS utilisateur_prenom
                 FROM avis a
                 INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
                 WHERE a.statut = :statut
                 ORDER BY a.date_avis DESC'
            );
            $stmt->execute([':statut' => Avis::STATUT_VALIDE]);
            $rows = $stmt->fetchAll();

            return array_map(fn($row) => Avis::fromArray($row), $rows);

        } catch (PDOException $e) {
            error_log('[AvisRepository::findValides] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération des avis.');
        }
    }

    /**
     * Récupère tous les avis (espace employé)
     * @return Avis[]
     */
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT a.*, u.nom AS utilisateur_nom, u.prenom AS utilisateur_prenom
                 FROM avis a
                 INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
                 ORDER BY a.date_avis DESC'
            );
            $stmt->execute();
            $rows = $stmt->fetchAll();

            return array_map(fn($row) => Avis::fromArray($row), $rows);

        } catch (PDOException $e) {
            error_log('[AvisRepository::findAll] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération des avis.');
        }
    }

    /* Create avis */

    public function create(Avis $avis): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO avis (utilisateur_id, commande_id, note, commentaire)
                 VALUES (:utilisateur_id, :commande_id, :note, :commentaire)'
            );
            $stmt->execute([
                ':utilisateur_id' => $avis->getUtilisateurId(),
                ':commande_id'    => $avis->getCommandeId(),
                ':note'           => $avis->getNote(),
                ':commentaire'    => $avis->getCommentaire(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log('[AvisRepository::create] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la création de l\'avis.');
        }
    }

    /* valider/refuser avis */

    public function updateStatut(int $avisId, string $statut): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE avis SET statut = :statut WHERE avis_id = :id'
            );
            return $stmt->execute([':statut' => $statut, ':id' => $avisId]);

        } catch (PDOException $e) {
            error_log('[AvisRepository::updateStatut] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour du statut de l\'avis.');
        }
    }

    /* Delete avis */
    
    public function delete(int $avisId): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM avis WHERE avis_id = :id');
            return $stmt->execute([':id' => $avisId]);

        } catch (PDOException $e) {
            error_log('[AvisRepository::delete] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la suppression de l\'avis.');
        }
    }
}
