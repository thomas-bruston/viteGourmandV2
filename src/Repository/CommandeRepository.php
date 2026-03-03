<?php

declare(strict_types=1);

namespace Repository;

use Entity\Commande;
use PDOException;


class CommandeRepository extends AbstractRepository
{
    /**
     * Récupère toutes les commandes d'un utilisateur
     * @return Commande[]
     */
    public function findByUtilisateurId(int $utilisateurId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT c.*, m.titre AS menu_titre, m.image AS menu_image
                 FROM commande c
                 INNER JOIN menu m ON c.menu_id = m.menu_id
                 WHERE c.utilisateur_id = :id
                 ORDER BY c.date_commande DESC'
            );
            $stmt->execute([':id' => $utilisateurId]);
            $rows = $stmt->fetchAll();

            return array_map(fn($row) => Commande::fromArray($row), $rows);

        } catch (PDOException $e) {
            error_log('[CommandeRepository::findByUtilisateurId] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération des commandes.');
        }
    }

    /* Récupère commande par ID */

    public function findById(int $commandeId): ?Commande
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT c.*, m.titre AS menu_titre, m.image AS menu_image,
                        u.nom AS utilisateur_nom, u.prenom AS utilisateur_prenom,
                        u.email AS utilisateur_email
                 FROM commande c
                 INNER JOIN menu m ON c.menu_id = m.menu_id
                 INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
                 WHERE c.commande_id = :id
                 LIMIT 1'
            );
            $stmt->execute([':id' => $commandeId]);
            $data = $stmt->fetch();

            return $data ? Commande::fromArray($data) : null;

        } catch (PDOException $e) {
            error_log('[CommandeRepository::findById] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération de la commande.');
        }
    }

    /**
     * Récupère toutes les commandes (espace employé) avec filtres optionnels
     * @return Commande[]
     */
   public function findAll(array $filters = []): array
{
    try {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['statut'])) {
            $where[]           = 'c.statut = :statut';
            $params[':statut'] = $filters['statut'];
        }

        if (!empty($filters['utilisateur_id'])) {
            $where[]                   = 'c.utilisateur_id = :utilisateur_id';
            $params[':utilisateur_id'] = (int) $filters['utilisateur_id'];
        }

        $whereClause = implode(' AND ', $where);

        $stmt = $this->pdo->prepare(
            "SELECT c.*,
                    m.titre AS menu_titre,
                    u.nom AS user_nom,
                    u.prenom AS user_prenom,
                    u.email AS user_email,
                    u.telephone AS user_tel
             FROM commande c
             INNER JOIN menu m ON c.menu_id = m.menu_id
             INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
             WHERE {$whereClause}
             ORDER BY c.date_commande DESC"
        );
        $stmt->execute($params);

        return $stmt->fetchAll(); 

    } catch (PDOException $e) {
        error_log('[CommandeRepository::findAll] ' . $e->getMessage());
        throw new \RuntimeException('Erreur lors de la récupération des commandes.');
    }
}

    /* Create commande */

    public function create(Commande $commande): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO commande
                    (utilisateur_id, menu_id, numero_commande, date_prestation, heure_livraison,
                     adresse_livraison, code_postal_livraison, ville_livraison,
                     nombre_personnes, prix_menu, prix_livraison, prix_total)
                 VALUES
                    (:utilisateur_id, :menu_id, :numero_commande, :date_prestation, :heure_livraison,
                     :adresse_livraison, :code_postal_livraison, :ville_livraison,
                     :nombre_personnes, :prix_menu, :prix_livraison, :prix_total)'
            );
            $stmt->execute([
                ':utilisateur_id'       => $commande->getUtilisateurId(),
                ':menu_id'              => $commande->getMenuId(),
                ':numero_commande'      => $commande->getNumeroCommande(),
                ':date_prestation'      => $commande->getDatePrestation(),
                ':heure_livraison'      => $commande->getHeureLivraison(),
                ':adresse_livraison'    => $commande->getAdresseLivraison(),
                ':code_postal_livraison' => $commande->getCodePostalLivraison(),
                ':ville_livraison'      => $commande->getVilleLivraison(),
                ':nombre_personnes'     => $commande->getNombrePersonnes(),
                ':prix_menu'            => $commande->getPrixMenu(),
                ':prix_livraison'       => $commande->getPrixLivraison(),
                ':prix_total'           => $commande->getPrixTotal(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log('[CommandeRepository::create] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la création de la commande.');
        }
    }

    /* MAJ statut commande */

    public function updateStatut(int $commandeId, string $statut, ?string $commentaire = null): bool
    {
        try {
            $this->pdo->beginTransaction();

            
            $stmt = $this->pdo->prepare(
                'UPDATE commande SET statut = :statut WHERE commande_id = :id'
            );
            $stmt->execute([':statut' => $statut, ':id' => $commandeId]);

            
            $stmt = $this->pdo->prepare(
                'INSERT INTO suivi_commande (commande_id, statut, commentaire)
                 VALUES (:commande_id, :statut, :commentaire)'
            );
            $stmt->execute([
                ':commande_id' => $commandeId,
                ':statut'      => $statut,
                ':commentaire' => $commentaire,
            ]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log('[CommandeRepository::updateStatut] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour du statut.');
        }
    }

    /* Annulation commande */

    public function annuler(int $commandeId, string $motif,): bool
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                'UPDATE commande
                 SET statut = :statut, motif_annulation = :motif
                 WHERE commande_id = :id'
            );
            $stmt->execute([
                ':statut' => Commande::STATUT_ANNULEE,
                ':motif'  => $motif,
                ':id'     => $commandeId,
            ]);

            $stmt = $this->pdo->prepare(
                'INSERT INTO suivi_commande (commande_id, statut, commentaire)
                 VALUES (:commande_id, :statut, :commentaire)'
            );
            $stmt->execute([
                ':commande_id' => $commandeId,
                ':statut'      => Commande::STATUT_ANNULEE,
                ':commentaire' => $motif,
            ]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log('[CommandeRepository::annuler] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de l\'annulation de la commande.');
        }
    }

    /* Modif commande */

    public function modifier(int $commandeId, string $datePrestation, string $heureLivraison): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE commande
                 SET date_prestation = :date, heure_livraison = :heure
                 WHERE commande_id = :id AND statut = :statut'
            );
            return $stmt->execute([
                ':date'   => $datePrestation,
                ':heure'  => $heureLivraison,
                ':id'     => $commandeId,
                ':statut' => Commande::STATUT_EN_ATTENTE,
            ]);

        } catch (PDOException $e) {
            error_log('[CommandeRepository::modifier] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la modification de la commande.');
        }
    }

    /* create numero commande */
    
   public function generateNumeroCommande(): string
{
    try {
        $year = date('Y');
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM commande WHERE YEAR(date_commande) = :year'
        );
        $stmt->execute([':year' => $year]);
        $count = (int) $stmt->fetchColumn();

        // Chercher le prochain numéro disponible
        do {
            $count++;
            $numero = 'VG-' . $year . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*) FROM commande WHERE numero_commande = :numero'
            );
            $stmt->execute([':numero' => $numero]);
        } while ((int) $stmt->fetchColumn() > 0);

        return $numero;

    } catch (PDOException $e) {
        error_log('[CommandeRepository::generateNumeroCommande] ' . $e->getMessage());
        throw new \RuntimeException('Erreur lors de la génération du numéro de commande.');
    }
}
}
