<?php

declare(strict_types=1);

/**
 * Vest+ Platform - Configurações Gerais da Aplicação
 *
 * Define as constantes básicas do ambiente e credenciais de banco de dados.
 * Suporta leitura nativa de variáveis de ambiente (.env, getenv, $_ENV e Docker).
 */

// Blindagem de Produção: Desativa exibição de erros na tela e registra em log interno
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}
error_reporting(E_ALL);

// Remove a assinatura do PHP nos cabeçalhos HTTP
header_remove('X-Powered-By');

// Carregador simples de .env (caso o arquivo exista na raiz e não tenha sido pré-carregado pelo Docker)
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            if (getenv($k) === false) {
                putenv("{$k}={$v}");
                $_ENV[$k] = $v;
                $_SERVER[$k] = $v;
            }
        }
    }
}

// Helper local para resolver variáveis de ambiente com fallback
$resolveEnv = static function (string $key, string $default): string {
    $val = getenv($key);
    if ($val !== false && $val !== '') {
        return (string) $val;
    }
    if (isset($_ENV[$key]) && (string) $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }
    if (isset($_SERVER[$key]) && (string) $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }
    return $default;
};

// Constantes de Ambiente e Aplicação
define('APP_NAME', $resolveEnv('APP_NAME', 'Vest+'));
define('APP_VERSION', '1.0.0');
define('APP_ENV', $resolveEnv('APP_ENV', 'production'));

// Parâmetros de Conexão com o Banco de Dados
define('DB_HOST', $resolveEnv('DB_HOST', '127.0.0.1'));
define('DB_PORT', $resolveEnv('DB_PORT', '3306'));
define('DB_NAME', $resolveEnv('DB_NAME', 'vestplus'));
define('DB_USER', $resolveEnv('DB_USER', 'root'));
define('DB_PASS', $resolveEnv('DB_PASS', ''));
define('DB_CHARSET', $resolveEnv('DB_CHARSET', 'utf8mb4'));

// Autoload simples sem Composer (PSR-4 básico para Config e App)
spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Config\\' => __DIR__ . '/',
        'App\\'    => dirname(__DIR__) . '/src/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});
