<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Gerenciamento de mensagens temporárias (Flash Messages) via sessão PHP.
 */
final class Flash
{
    private const SESSION_KEY = '_flash_messages';

    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
    }

    public static function set(string $type, string $message): void
    {
        self::ensureSession();
        $_SESSION[self::SESSION_KEY][$type][] = $message;
    }

    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    public static function error(string $message): void
    {
        self::set('danger', $message);
    }

    public static function warning(string $message): void
    {
        self::set('warning', $message);
    }

    public static function info(string $message): void
    {
        self::set('info', $message);
    }

    /**
     * Recupera todas as mensagens flash de todos os tipos e limpa a sessão.
     *
     * @return array<string, array<int, string>>
     */
    public static function all(): array
    {
        self::ensureSession();
        $messages = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);
        return $messages;
    }

    public static function has(): bool
    {
        self::ensureSession();
        return !empty($_SESSION[self::SESSION_KEY]);
    }
}
