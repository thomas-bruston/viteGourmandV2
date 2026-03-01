<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;

/* Classe Database PDO */

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $host     = Env::get('MYSQL_HOST', 'mysql');
        $port     = Env::get('MYSQL_PORT', '3306');
        $dbname   = Env::get('MYSQL_DATABASE', 'viteGourmand');
        $user     = Env::get('MYSQL_USER', 'vg_app');
        $password = Env::get('MYSQL_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        try {
            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, 
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci", 
            ]);
        } catch (PDOException $e) {
            error_log('[Database] Erreur de connexion : ' . $e->getMessage());
            throw new \RuntimeException('Erreur de connexion à la base de données.');
        }
    }

    /* Retourne l'instance unique */

    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Empêche le clonage (Singleton) necessaire ?
     */
    private function __clone(): void {}
}
