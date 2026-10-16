<?php

declare(strict_types=1);

namespace Config;

use PDO;
use PDOException;

/**
 * Classe responsável pelo gerenciamento da conexão com o banco de dados via PDO.
 * Padrão Singleton/Factory para garantir reuso seguro e controlado de conexões.
 */
final class Database
{
    private static ?PDO $connection = null;

    /**
     * Construtor privado para evitar instanciação direta fora da classe.
     */
    private function __construct()
    {
    }

    /**
     * Bloqueia clonagem da instância Singleton.
     */
    private function __clone()
    {
    }

    /**
     * Retorna a instância única de conexão PDO configurada com as melhores práticas de segurança.
     *
     * @return PDO Conexão ativa com o banco de dados.
     * @throws PDOException Caso ocorra qualquer falha na conexão.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = self::getEnvVar('DB_HOST', defined('DB_HOST') ? DB_HOST : 'db');
            $port = self::getEnvVar('DB_PORT', defined('DB_PORT') ? (string) DB_PORT : '3306');
            $dbname = self::getEnvVar('DB_NAME', defined('DB_NAME') ? DB_NAME : 'vestplus');
            $user = self::getEnvVar('DB_USER', defined('DB_USER') ? DB_USER : 'vestuser');
            $pass = self::getEnvVar('DB_PASS', defined('DB_PASS') ? (string) DB_PASS : '');
            $charset = self::getEnvVar('DB_CHARSET', defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4');

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $dbname, $charset);

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Propaga a exceção para tratamento adequado na camada receptora
                throw new PDOException(
                    sprintf('Falha na conexão com o banco de dados [%s] no host [%s]: %s', $dbname, $host, $e->getMessage()),
                    (int) $e->getCode(),
                    $e
                );
            }
        }

        return self::$connection;
    }

    /**
     * Recupera variável de ambiente com busca em múltiplas fontes (getenv, $_ENV, $_SERVER) e fallback seguro.
     */
    private static function getEnvVar(string $key, string $default = ''): string
    {
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return (string) $val;
        }

        if (array_key_exists($key, $_ENV) && (string) $_ENV[$key] !== '') {
            return (string) $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER) && (string) $_SERVER[$key] !== '') {
            return (string) $_SERVER[$key];
        }

        return $default;
    }

    /**
     * Encerra a conexão PDO ativa (útil para testes unitários ou recriação do pool).
     */
    public static function disconnect(): void
    {
        self::$connection = null;
    }
}
