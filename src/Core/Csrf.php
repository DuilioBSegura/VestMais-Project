<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Gerenciador de proteção contra CSRF (Cross-Site Request Forgery).
 */
final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    /**
     * Garante que a sessão PHP esteja iniciada.
     */
    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
    }

    /**
     * Retorna o token CSRF atual ou gera um novo caso não exista.
     */
    public static function getToken(): string
    {
        self::ensureSession();

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION[self::SESSION_KEY];
    }

    /**
     * Regenera o token CSRF atual.
     */
    public static function regenerateToken(): string
    {
        self::ensureSession();
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        return (string) $_SESSION[self::SESSION_KEY];
    }

    /**
     * Valida um token CSRF fornecido contra o token ativo na sessão.
     */
    public static function validateToken(?string $token): bool
    {
        self::ensureSession();

        if ($token === null || empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }

    /**
     * Renderiza o input HTML oculto com o token CSRF pronto para formulários POST.
     */
    public static function input(): string
    {
        $token = self::getToken();
        return sprintf('<input type="hidden" name="csrf_token" value="%s">', htmlspecialchars($token, ENT_QUOTES, 'UTF-8'));
    }
}
