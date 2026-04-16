<?php

declare(strict_types=1);

namespace Repository;

use Entity\Menu;
use PDOException;

class MenuRepository extends AbstractRepository
{
    /**
     * Récupère tous les menus actifs avec leurs thèmes et régimes
     * @return Menu[]
     */
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT m.*,
                        GROUP_CONCAT(DISTINCT t.libelle ORDER BY t.libelle SEPARATOR ",") AS themes,
                        GROUP_CONCAT(DISTINCT r.libelle ORDER BY r.libelle SEPARATOR ",") AS regimes
                 FROM menu m
                 LEFT JOIN menu_theme mt ON m.menu_id = mt.menu_id
                 LEFT JOIN theme t ON mt.theme_id = t.theme_id
                 LEFT JOIN menu_regime mr ON m.menu_id = mr.menu_id
                 LEFT JOIN regime r ON mr.regime_id = r.regime_id
                 WHERE m.actif = 1
                 GROUP BY m.menu_id
                 ORDER BY m.menu_id ASC'
            );
            $stmt->execute();
            $rows = $stmt->fetchAll();

            return array_map(function ($row) {
                $menu = Menu::fromArray($row);
                $menu->setThemes($row['themes'] ? explode(',', $row['themes']) : []);
                $menu->setRegimes($row['regimes'] ? explode(',', $row['regimes']) : []);
                return $menu;
            }, $rows);

        } catch (PDOException $e) {
            error_log('[MenuRepository::findAll] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération des menus.');
        }
    }

    /* Récup menu par ID + plats + allergenes */

    public function findById(int $menuId): ?Menu
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT m.*,
                        GROUP_CONCAT(DISTINCT t.libelle ORDER BY t.libelle SEPARATOR ",") AS themes,
                        GROUP_CONCAT(DISTINCT r.libelle ORDER BY r.libelle SEPARATOR ",") AS regimes
                 FROM menu m
                 LEFT JOIN menu_theme mt ON m.menu_id = mt.menu_id
                 LEFT JOIN theme t ON mt.theme_id = t.theme_id
                 LEFT JOIN menu_regime mr ON m.menu_id = mr.menu_id
                 LEFT JOIN regime r ON mr.regime_id = r.regime_id
                 WHERE m.menu_id = :id
                 GROUP BY m.menu_id
                 LIMIT 1'
            );
            $stmt->execute([':id' => $menuId]);
            $data = $stmt->fetch();

            if (!$data) return null;

            $menu = Menu::fromArray($data);
            $menu->setThemes($data['themes'] ? explode(',', $data['themes']) : []);
            $menu->setRegimes($data['regimes'] ? explode(',', $data['regimes']) : []);

            $plats = (new PlatRepository())->findByMenuId($menuId);
            $menu->setPlats($plats);

            return $menu;

        } catch (PDOException $e) {
            error_log('[MenuRepository::findById] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération du menu.');
        }
    }


    public function findWithFilters(array $filters): array
    {
        try {
            $where  = ['m.actif = 1'];
            $params = [];

            if (!empty($filters['theme'])) {
                $where[]          = 't.libelle = :theme';
                $params[':theme'] = $filters['theme'];
            }

            if (!empty($filters['regime'])) {
                $where[]           = 'r.libelle = :regime';
                $params[':regime'] = $filters['regime'];
            }

            if (!empty($filters['prix_max'])) {
                $where[]            = 'm.prix_par_personne <= :prix_max';
                $params[':prix_max'] = (float) $filters['prix_max'];
            }

            if (!empty($filters['personnes'])) {
                $where[]              = 'm.nombre_personne_minimum <= :personnes';
                $params[':personnes'] = (int) $filters['personnes'];
            }

            $whereClause = implode(' AND ', $where);

            $stmt = $this->pdo->prepare(
                "SELECT m.*,
                        GROUP_CONCAT(DISTINCT t.libelle ORDER BY t.libelle SEPARATOR ',') AS themes,
                        GROUP_CONCAT(DISTINCT r.libelle ORDER BY r.libelle SEPARATOR ',') AS regimes
                 FROM menu m
                 LEFT JOIN menu_theme mt ON m.menu_id = mt.menu_id
                 LEFT JOIN theme t ON mt.theme_id = t.theme_id
                 LEFT JOIN menu_regime mr ON m.menu_id = mr.menu_id
                 LEFT JOIN regime r ON mr.regime_id = r.regime_id
                 WHERE {$whereClause}
                 GROUP BY m.menu_id
                 ORDER BY m.menu_id ASC"
            );
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            return array_map(function ($row) {
                $menu = Menu::fromArray($row);
                $menu->setThemes($row['themes'] ? explode(',', $row['themes']) : []);
                $menu->setRegimes($row['regimes'] ? explode(',', $row['regimes']) : []);
                return $menu;
            }, $rows);

        } catch (PDOException $e) {
            error_log('[MenuRepository::findWithFilters] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors du filtrage des menus.');
        }
    }

    /**
     * Create menu necessaire 
     */
    public function create(Menu $menu): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO menu (titre, description, nombre_personne_minimum, prix_par_personne, quantite_restante, image)
                 VALUES (:titre, :description, :minimum, :prix, :quantite, :image)'
            );
            $stmt->execute([
                ':titre'       => $menu->getTitre(),
                ':description' => $menu->getDescription(),
                ':minimum'     => $menu->getNombrePersonneMinimum(),
                ':prix'        => $menu->getPrixParPersonne(),
                ':quantite'    => $menu->getQuantiteRestante(),
                ':image'       => $menu->getImage(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log('[MenuRepository::create] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la création du menu.');
        }
    }

    /* MAJ menu */

    public function update(Menu $menu): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE menu
                 SET titre = :titre, description = :description,
                     nombre_personne_minimum = :minimum, prix_par_personne = :prix,
                     quantite_restante = :quantite, image = :image
                 WHERE menu_id = :id'
            );
            return $stmt->execute([
                ':titre'       => $menu->getTitre(),
                ':description' => $menu->getDescription(),
                ':minimum'     => $menu->getNombrePersonneMinimum(),
                ':prix'        => $menu->getPrixParPersonne(),
                ':quantite'    => $menu->getQuantiteRestante(),
                ':image'       => $menu->getImage(),
                ':id'          => $menu->getMenuId(),
            ]);

        } catch (PDOException $e) {
            error_log('[MenuRepository::update] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour du menu.');
        }
    }

    /* Supprime menu */

    public function delete(int $menuId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE menu SET actif = 0 WHERE menu_id = :id'
            );
            return $stmt->execute([':id' => $menuId]);

        } catch (PDOException $e) {
            error_log('[MenuRepository::delete] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la suppression du menu.');
        }
    }

    /* MAJ theme */

    public function syncThemes(int $menuId, array $themeIds): void
    {
        try {
            $this->pdo->prepare('DELETE FROM menu_theme WHERE menu_id = :id')
                      ->execute([':id' => $menuId]);

            $stmt = $this->pdo->prepare(
                'INSERT INTO menu_theme (menu_id, theme_id) VALUES (:menu_id, :theme_id)'
            );
            foreach ($themeIds as $themeId) {
                $stmt->execute([':menu_id' => $menuId, ':theme_id' => (int) $themeId]);
            }

        } catch (PDOException $e) {
            error_log('[MenuRepository::syncThemes] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour des thèmes.');
        }
    }

    /* MAJ regime */
    
    public function syncRegimes(int $menuId, array $regimeIds): void
    {
        try {
            $this->pdo->prepare('DELETE FROM menu_regime WHERE menu_id = :id')
                      ->execute([':id' => $menuId]);

            $stmt = $this->pdo->prepare(
                'INSERT INTO menu_regime (menu_id, regime_id) VALUES (:menu_id, :regime_id)'
            );
            foreach ($regimeIds as $regimeId) {
                $stmt->execute([':menu_id' => $menuId, ':regime_id' => (int) $regimeId]);
            }

        } catch (PDOException $e) {
            error_log('[MenuRepository::syncRegimes] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour des régimes.');
        }
    }

/* Récupère themes */

public function findAllThemes(): array
{
    try {
        $stmt = $this->pdo->query('SELECT * FROM theme ORDER BY libelle ASC');
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => \Entity\Theme::fromArray($row), $rows);
    } catch (PDOException $e) {
        error_log('[MenuRepository::findAllThemes] ' . $e->getMessage());
        throw new \RuntimeException('Erreur lors de la récupération des thèmes.');
    }
}

/* Récupère regime */

public function findAllRegimes(): array
{
    try {
        $stmt = $this->pdo->query('SELECT * FROM regime ORDER BY libelle ASC');
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => \Entity\Regime::fromArray($row), $rows);
    } catch (PDOException $e) {
        error_log('[MenuRepository::findAllRegimes] ' . $e->getMessage());
        throw new \RuntimeException('Erreur lors de la récupération des régimes.');
    }
}


public function getThemeIds(int $menuId): array
{
    $stmt = $this->pdo->prepare('SELECT theme_id FROM menu_theme WHERE menu_id = :id');
    $stmt->execute([':id' => $menuId]);
    return $stmt->fetchAll(\PDO::FETCH_COLUMN);
}

public function getRegimeIds(int $menuId): array
{
    $stmt = $this->pdo->prepare('SELECT regime_id FROM menu_regime WHERE menu_id = :id');
    $stmt->execute([':id' => $menuId]);
    return $stmt->fetchAll(\PDO::FETCH_COLUMN);

}

/* decremente quantité */

public function decrementerQuantite(int $menuId, int $nombrePersonnes): void
{
    try {
        $stmt = $this->pdo->prepare(
            'UPDATE menu
             SET quantite_restante = quantite_restante - :nb
             WHERE menu_id = :id
               AND quantite_restante >= :nb_check'
        );

        $stmt->execute([
            ':nb'       => $nombrePersonnes,
            ':id'       => $menuId,
            ':nb_check' => $nombrePersonnes,  // ← paramètre distinct, même valeur
        ]);

        // Vérification atomique : si 0 ligne affectée = stock épuisé (race condition)
        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException(
                'Stock insuffisant pour le menu #' . $menuId .
                ' (race condition détectée ou quantité insuffisante).'
            );
        }

    } catch (\PDOException $e) {
        error_log('[MenuRepository::decrementerQuantite] ' . $e->getMessage());
        throw new \RuntimeException('Erreur lors de la mise à jour des quantités.');
    }
}}