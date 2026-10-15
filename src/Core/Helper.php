<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Utilitários gerais do sistema (URLs, sanitização e tratamento de strings).
 */
final class Helper
{
    /**
     * Gera uma URL absoluta com base na raiz da aplicação.
     * Suporta ambientes com subdiretórios (ex: XAMPP /VestMais/ ou /VestMais/public/)
     * e VirtualHosts na raiz.
     */
    public static function url(string $path = ''): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        $baseDir = str_replace('\\', '/', $baseDir);

        // Se terminar em /public, mantém como base para os assets e rotas front controller
        $base = rtrim($baseDir, '/');

        $cleanPath = '/' . ltrim($path, '/');
        if ($cleanPath === '/') {
            return $base !== '' ? $base : '/';
        }

        return $base . $cleanPath;
    }

    /**
     * Sanitiza strings para exibição segura contra XSS (Cross-Site Scripting).
     */
    public static function e(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Gera slugs URL-friendly a partir de strings com suporte a caracteres acentuados em português.
     */
    public static function slugify(string $text): string
    {
        // Converte caracteres acentuados para formato ASCII correspondente
        $transliterator = 'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()';
        if (function_exists('transliterator_transliterate')) {
            $slug = transliterator_transliterate($transliterator, $text);
        } else {
            $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        }

        // Substitui caracteres não alfanuméricos por hífen
        $slug = preg_replace('~[^\pL\d]+~u', '-', (string) $slug);
        $slug = trim((string) $slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = strtolower((string) $slug);

        return $slug !== '' ? $slug : 'n-a';
    }

    /**
     * Trunca texto preservando palavras inteiras para previews e listagens.
     */
    public static function truncate(string $text, int $limit = 120, string $end = '...'): string
    {
        $clean = strip_tags($text);
        if (mb_strlen($clean, 'UTF-8') <= $limit) {
            return $clean;
        }

        $cut = mb_substr($clean, 0, $limit, 'UTF-8');
        $lastSpace = mb_strrpos($cut, ' ', 0, 'UTF-8');
        if ($lastSpace !== false) {
            $cut = mb_substr($cut, 0, $lastSpace, 'UTF-8');
        }

        return $cut . $end;
    }
}
