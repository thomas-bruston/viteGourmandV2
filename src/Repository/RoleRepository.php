<?php

declare(strict_types=1);

namespace Repository;

use Entity\Role;
use PDOException;


class RoleRepository extends AbstractRepository
{
    
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM role ORDER BY role_id ASC');
            $rows = $stmt->fetchAll();
            return array_map(fn($row) => Role::fromArray($row), $rows);
        } catch (PDOException $e) {
            error_log('[RoleRepository::findAll] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération des rôles.');
        }
    }

    /* rôle par ID */

    public function findById(int $roleId): ?Role
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM role WHERE role_id = :id LIMIT 1');
            $stmt->execute([':id' => $roleId]);
            $data = $stmt->fetch();
            return $data ? Role::fromArray($data) : null;
        } catch (PDOException $e) {
            error_log('[RoleRepository::findById] ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la récupération du rôle.');
        }
    }
}

/* Le contrôle d'accès par rôle est géré via la session PHP, les rôles sont fixes et gérés via AuthService */