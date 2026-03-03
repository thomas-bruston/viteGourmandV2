<?php

declare(strict_types=1);

namespace Repository;

use Entity\Utilisateur;
use PDOException;


class UtilisateurRepository extends AbstractRepository
{
    
    public function findByEmail(string $email): ?Utilisateur
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT u.*, r.libelle AS role_libelle
                 FROM utilisateur u
                 INNER JOIN role r ON u.role_id = r.role_id
                 WHERE u.email = :email
                 LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $data = $stmt->fetch();

            return $data ? Utilisateur::fromArray($data) : null;

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::findByEmail] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la recherche de l\'utilisateur.');
        }
    }

    public function findById(int $id): ?Utilisateur
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT u.*, r.libelle AS role_libelle
                 FROM utilisateur u
                 INNER JOIN role r ON u.role_id = r.role_id
                 WHERE u.utilisateur_id = :id
                 LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch();

            return $data ? Utilisateur::fromArray($data) : null;

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::findById] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la recherche de l\'utilisateur.');
        }
    }

    /* Vérif mail existe deja */

    public function emailExists(string $email): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*) FROM utilisateur WHERE email = :email'
            );
            $stmt->execute([':email' => $email]);
            return (int) $stmt->fetchColumn() > 0;

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::emailExists] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la vérification de l\'email.');
        }
    }

   /* Create user */

    public function create(Utilisateur $utilisateur): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO utilisateur
                    (role_id, nom, prenom, email, mot_de_passe, telephone, adresse, code_postal, ville)
                 VALUES
                    (:role_id, :nom, :prenom, :email, :mot_de_passe, :telephone, :adresse, :code_postal, :ville)'
            );
            $stmt->execute([
                ':role_id'      => $utilisateur->getRoleId(),
                ':nom'          => $utilisateur->getNom(),
                ':prenom'       => $utilisateur->getPrenom(),
                ':email'        => $utilisateur->getEmail(),
                ':mot_de_passe' => $utilisateur->getMotDePasse(),
                ':telephone'    => $utilisateur->getTelephone(),
                ':adresse'      => $utilisateur->getAdresse(),
                ':code_postal'  => $utilisateur->getCodePostal(),
                ':ville'        => $utilisateur->getVille(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::create] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la création de l\'utilisateur.');
        }
    }

    /* MAJ info user */

    public function update(Utilisateur $utilisateur): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE utilisateur
                 SET nom = :nom, prenom = :prenom, telephone = :telephone,
                     adresse = :adresse, code_postal = :code_postal, ville = :ville
                 WHERE utilisateur_id = :id'
            );
            return $stmt->execute([
                ':nom'         => $utilisateur->getNom(),
                ':prenom'      => $utilisateur->getPrenom(),
                ':telephone'   => $utilisateur->getTelephone(),
                ':adresse'     => $utilisateur->getAdresse(),
                ':code_postal' => $utilisateur->getCodePostal(),
                ':ville'       => $utilisateur->getVille(),
                ':id'          => $utilisateur->getUtilisateurId(),
            ]);

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::update] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour de l\'utilisateur.');
        }
    }

    /* MAJ MDP */

    public function updatePassword(int $utilisateurId, string $hash): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE utilisateur SET mot_de_passe = :hash WHERE utilisateur_id = :id'
            );
            return $stmt->execute([':hash' => $hash, ':id' => $utilisateurId]);

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::updatePassword] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour du mot de passe.');
        }
    }

    /**
     * Récupère tous les employés (pour l'admin)
     * @return Utilisateur[]
     */
    public function findAllEmployes(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT u.*, r.libelle AS role_libelle
                 FROM utilisateur u
                 INNER JOIN role r ON u.role_id = r.role_id
                 WHERE r.libelle = :role
                 ORDER BY u.nom ASC'
            );
            $stmt->execute([':role' => 'employe']);
            $rows = $stmt->fetchAll();

            return array_map(fn($row) => Utilisateur::fromArray($row), $rows);

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::findAllEmployes] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération des employés.');
        }
    }

    /**
     * Active ou désactive un utilisateur necessaire ????
     */
    public function setStatut(int $utilisateurId, bool $statut): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE utilisateur SET statut = :statut WHERE utilisateur_id = :id'
            );
            return $stmt->execute([':statut' => (int) $statut, ':id' => $utilisateurId]);

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::setStatut] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour du statut.');
        }
    }

    /* Token reinit MDP */

    public function saveResetToken(int $utilisateurId, string $token, \DateTime $expireAt): bool
    {
        try {
            
            $stmt = $this->pdo->prepare(
                'DELETE FROM password_reset_token WHERE utilisateur_id = :id'
            );
            $stmt->execute([':id' => $utilisateurId]);

            $stmt = $this->pdo->prepare(
                'INSERT INTO password_reset_token (utilisateur_id, token, expire_at)
                 VALUES (:id, :token, :expire_at)'
            );
            return $stmt->execute([
                ':id'        => $utilisateurId,
                ':token'     => $token,
                ':expire_at' => $expireAt->format('Y-m-d H:i:s'),
            ]);

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::saveResetToken] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la sauvegarde du token.');
        }
    }

    /**
     * Trouve un utilisateur via son token de reset (valide et non utilisé) necessaire ???
     */
    public function findByResetToken(string $token): ?Utilisateur
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT u.*
                 FROM utilisateur u
                 INNER JOIN password_reset_token t ON u.utilisateur_id = t.utilisateur_id
                 WHERE t.token = :token
                   AND t.expire_at > NOW()
                   AND t.utilise = 0
                 LIMIT 1'
            );
            $stmt->execute([':token' => $token]);
            $data = $stmt->fetch();

            return $data ? Utilisateur::fromArray($data) : null;

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::findByResetToken] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la vérification du token.');
        }
    }

    /* Invalide used token */
    
    public function invalidateResetToken(string $token): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE password_reset_token SET utilise = 1 WHERE token = :token'
            );
            return $stmt->execute([':token' => $token]);

        } catch (PDOException $e) {
            error_log('[UtilisateurRepository::invalidateResetToken] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de l\'invalidation du token.');
        }
    }
}
