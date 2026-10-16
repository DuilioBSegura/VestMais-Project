<?php

declare(strict_types=1);

/**
 * Vest+ Platform - Configurações Gerais da Aplicação
 *
 * Define as constantes básicas do ambiente e credenciais de banco de dados.
 * Suporta variáveis de ambiente (getenv) com valores de fallback padrão para XAMPP/desenvolvimento.
 */

// Carregador simples de variáveis de ambiente (.env) caso exista na raiz
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                    putenv(sprintf('%s=%s', $key, $value));
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}

// Ambiente e Aplicação
define('APP_NAME', 'Vest+');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Ocultação de assinaturas de servidor e segurança HTTP
if (!headers_sent()) {
    header_remove('X-Powered-By');
}
@ini_set('expose_php', '0');

// Tratamento e exibição de erros conforme o ambiente
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    $logDir = dirname(__DIR__) . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    ini_set('error_log', $logDir . '/app_error.log');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Parâmetros de Conexão com o Banco de Dados
define('DB_HOST', getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? 'db')));
define('DB_PORT', getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? ($_SERVER['DB_PORT'] ?? '3306')));
define('DB_NAME', getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? ($_SERVER['DB_NAME'] ?? 'vestplus')));
define('DB_USER', getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? ($_SERVER['DB_USER'] ?? 'vestuser')));
define('DB_PASS', getenv('DB_PASS') !== false ? (string) getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? ($_SERVER['DB_PASS'] ?? '')));
define('DB_CHARSET', getenv('DB_CHARSET') ?: ($_ENV['DB_CHARSET'] ?? ($_SERVER['DB_CHARSET'] ?? 'utf8mb4')));

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
