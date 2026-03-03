<?php

declare(strict_types=1);

namespace Repository;

use Entity\Horaire;
use PDOException;


class HoraireRepository extends AbstractRepository
{
    /* Get Horaire */

    public function get(): ?Horaire
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM horaire LIMIT 1');
            $row  = $stmt->fetch();
            return $row ? Horaire::fromArray($row) : null;
        } catch (PDOException $e) {
            error_log('[HoraireRepository::get] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération des horaires.');
        }
    }

    /**
     * Récupère uniquement le texte (compatibilité avec le footer) necessaire ???
     */
    public function getTexte(): string
    {
        $horaire = $this->get();
        return $horaire ? $horaire->getTexte() : '';
    }

    /**
     * Met à jour le texte des horaires
     */
    public function updateTexte(string $texte): bool
    {
        try {
            $count = (int) $this->pdo->query('SELECT COUNT(*) FROM horaire')->fetchColumn();
            if ($count === 0) {
                $stmt = $this->pdo->prepare('INSERT INTO horaire (texte) VALUES (:texte)');
            } else {
                $stmt = $this->pdo->prepare('UPDATE horaire SET texte = :texte WHERE horaire_id = 1');
            }
            return $stmt->execute([':texte' => $texte]);
        } catch (PDOException $e) {
            error_log('[HoraireRepository::updateTexte] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour des horaires.');
        }
    }
}