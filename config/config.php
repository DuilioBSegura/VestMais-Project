<?php

declare(strict_types=1);

/**
 * Vest+ Platform - Configurações Gerais da Aplicação
 *
 * Define as constantes básicas do ambiente e credenciais de banco de dados.
 * Suporta variáveis de ambiente (getenv) com valores de fallback padrão para XAMPP/desenvolvimento.
 */

// Ambiente e Aplicação
define('APP_NAME', 'Vest+');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Parâmetros de Conexão com o Banco de Dados
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'vestplus');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? (string) getenv('DB_PASS') : '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

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
