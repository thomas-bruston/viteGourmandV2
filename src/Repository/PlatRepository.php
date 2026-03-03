<?php

declare(strict_types=1);

namespace Repository;

use Entity\Plat;
use PDOException;

/* classe repository palt */

class PlatRepository extends AbstractRepository
{
    /**
     * Récupère tous les plats d'un menu avec leur catégorie et allergènes
     * @return Plat[]
     */
    public function findByMenuId(int $menuId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT p.*, c.libelle AS categorie_libelle,
                        GROUP_CONCAT(a.libelle ORDER BY a.libelle SEPARATOR ", ") AS allergenes_liste
                 FROM plat p
                 INNER JOIN categorie_plat c ON p.categorie_id = c.categorie_id
                 LEFT JOIN plat_allergene pa ON p.plat_id = pa.plat_id
                 LEFT JOIN allergene a ON pa.allergene_id = a.allergene_id
                 WHERE p.menu_id = :menu_id
                 GROUP BY p.plat_id
                 ORDER BY c.ordre ASC, p.nom ASC'
            );
            $stmt->execute([':menu_id' => $menuId]);
            $rows = $stmt->fetchAll();

            return array_map(function ($row) {
                $plat = Plat::fromArray($row);
                if (!empty($row['allergenes_liste'])) {
                    $plat->setAllergenes(explode(', ', $row['allergenes_liste']));
                }
                return $plat;
            }, $rows);

        } catch (PDOException $e) {
            error_log('[PlatRepository::findByMenuId] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération des plats.');
        }
    }

    /* Créer plat */

    public function create(Plat $plat): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO plat (menu_id, categorie_id, nom, image)
                 VALUES (:menu_id, :categorie_id, :nom, :image)'
            );
            $stmt->execute([
                ':menu_id'      => $plat->getMenuId(),
                ':categorie_id' => $plat->getCategorieId(),
                ':nom'          => $plat->getNom(),
                ':image'        => $plat->getImage(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log('[PlatRepository::create] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la création du plat.');
        }
    }

    /* update plat */

    public function update(Plat $plat): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE plat SET nom = :nom, categorie_id = :categorie_id, image = :image
                 WHERE plat_id = :id'
            );
            return $stmt->execute([
                ':nom'          => $plat->getNom(),
                ':categorie_id' => $plat->getCategorieId(),
                ':image'        => $plat->getImage(),
                ':id'           => $plat->getPlatId(),
            ]);

        } catch (PDOException $e) {
            error_log('[PlatRepository::update] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour du plat.');
        }
    }

    public function updateNom(int $platId, string $nom): bool
{
    try {
        $stmt = $this->pdo->prepare(
            'UPDATE plat SET nom = :nom WHERE plat_id = :id'
        );
        return $stmt->execute([':nom' => $nom, ':id' => $platId]);
    } catch (\PDOException $e) {
        error_log('[PlatRepository::updateNom] ' . $e->getMessage());
        throw new \RuntimeException('Erreur lors de la mise à jour du plat.');
    }
}

    public function updateImage(int $platId, string $image): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE plat SET image = :image WHERE plat_id = :id'
            );
            return $stmt->execute([':image' => $image, ':id' => $platId]);
        } catch (\PDOException $e) {
            error_log('[PlatRepository::updateImage] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour de l\'image.');
        }
    }

    /* delete plat */

    public function delete(int $platId): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM plat WHERE plat_id = :id');
            return $stmt->execute([':id' => $platId]);

        } catch (PDOException $e) {
            error_log('[PlatRepository::delete] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la suppression du plat.');
        }
    }

    /* Récupère categories */

    public function findAllCategories(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categorie_plat ORDER BY ordre ASC');
        return $stmt->fetchAll();
    }

   /* Récupère tous les allergènes */

public function findAllAllergenes(): array
{
    try {
        $stmt = $this->pdo->query('SELECT * FROM allergene ORDER BY libelle ASC');
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => \Entity\Allergene::fromArray($row), $rows);
    } catch (PDOException $e) {
        error_log('[PlatRepository::findAllAllergenes] ' . $e->getMessage());
        throw new \RuntimeException('Erreur lors de la récupération des allergènes.');
    }
}
    /* rempli les cases allergens */

public function findAllergeneIdsByMenuId(int $menuId): array
{
    try {
        $stmt = $this->pdo->prepare(
            'SELECT DISTINCT pa.allergene_id 
             FROM plat_allergene pa
             INNER JOIN plat p ON pa.plat_id = p.plat_id
             WHERE p.menu_id = :menu_id'
        );
        $stmt->execute([':menu_id' => $menuId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    } catch (\PDOException $e) {
        error_log('[PlatRepository::findAllergeneIdsByMenuId] ' . $e->getMessage());
        throw new \RuntimeException('Erreur lors de la récupération des allergènes.');
    }
}

    /* MAJ allergenes admin */
    
    public function syncAllergenes(int $platId, array $allergeneIds): void
    {
        try {
            $this->pdo->prepare('DELETE FROM plat_allergene WHERE plat_id = :id')
                      ->execute([':id' => $platId]);

            $stmt = $this->pdo->prepare(
                'INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (:plat_id, :allergene_id)'
            );
            foreach ($allergeneIds as $allergeneId) {
                $stmt->execute([':plat_id' => $platId, ':allergene_id' => (int) $allergeneId]);
            }

        } catch (PDOException $e) {
            error_log('[PlatRepository::syncAllergenes] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour des allergènes.');
        }
    }
}
