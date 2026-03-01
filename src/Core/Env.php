<?php

declare(strict_types=1);

namespace Core;

/* Classe Env */

class Env
{

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Fichier .env introuvable : {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);
            $value = trim($value, '"\'');

            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key]    = $value;
                $_SERVER[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }

    /**
     * Récupère une variable d'environnement
     * 
     * @param string $key     Nom de la variable
     * @param mixed  $default Valeur par défaut si absente
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
