<?php

declare(strict_types=1);

namespace Core;

/* Classe Session */

class Session
{
    private const TIMEOUT = 1800; 

    /* Démarre session */

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $isProduction = Env::get('APP_ENV', 'development') === 'production';

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isProduction, 
            'httponly' => true,           
            'samesite' => 'Strict',
        ]);

        session_name('VG_SESSION');
        session_start();
        self::checkTimeout();
    }

    /* Destroy session timeout */

    private static function checkTimeout(): void
    {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > self::TIMEOUT) {
                self::destroy();
                return;
            }
        }

        $_SESSION['last_activity'] = time();
    }

    /* Valeur session set */

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

   /* Valeur session get */

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /* Vérif clé session */

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    /* supp clé session */

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /* Destroy session logout */

    public static function destroy(): void
    {
        $_SESSION = [];
        session_destroy();

        /* supp cookies */

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
    }

    /* ID session */

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

/* Gestion des messages succès + erreur */


    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    public static function getFlash(string $type): ?string
    {
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }

    public static function hasFlash(string $type): bool
    {
        return isset($_SESSION['flash'][$type]);
    }

   /* Gestion tokens CSRF */

   public static function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

    /* valid CSRF */

    public static function verifyCsrfToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /* Vérif user connect */

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

/* get role */

    public static function getUserRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

/* get id */

    public static function getUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }
}
