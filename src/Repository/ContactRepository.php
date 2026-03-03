<?php

declare(strict_types=1);

namespace Repository;

use Entity\Contact;
use PDOException;

class ContactRepository extends AbstractRepository
{
    /**
     * Récupère tous les messages de contact
     * @return Contact[]
     */
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query(
                'SELECT * FROM contact ORDER BY date_envoi DESC'
            );
            $rows = $stmt->fetchAll();
            return array_map(fn($row) => Contact::fromArray($row), $rows);

        } catch (PDOException $e) {
            error_log('[ContactRepository::findAll] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération des messages.');
        }
    }

    /*Create message */

    public function create(Contact $contact): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO contact (nom, prenom, email, titre, message)
                 VALUES (:nom, :prenom, :email, :titre, :message)'
            );
            $stmt->execute([
                ':nom'     => $contact->getNom(),
                ':prenom'  => $contact->getPrenom(),
                ':email'   => $contact->getEmail(),
                ':titre'   => $contact->getTitre(),
                ':message' => $contact->getMessage(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log('[ContactRepository::create] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la sauvegarde du message.');
        }
    }

    /*Delete message */
    public function delete(int $contactId): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM contact WHERE contact_id = :id');
            return $stmt->execute([':id' => $contactId]);

        } catch (PDOException $e) {
            error_log('[ContactRepository::delete] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la suppression du message.');
        }
    }
}
