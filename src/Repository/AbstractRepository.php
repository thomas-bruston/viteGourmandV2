<?php

declare(strict_types=1);

namespace Repository;

use Core\Database;
use PDO;


/* Classe abstract Repository*/

abstract class AbstractRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }
}
