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
            $host    = self::getEnvVar('DB_HOST', defined('DB_HOST') ? (string) DB_HOST : '127.0.0.1');
            $port    = self::getEnvVar('DB_PORT', defined('DB_PORT') ? (string) DB_PORT : '3306');
            $dbname  = self::getEnvVar('DB_NAME', defined('DB_NAME') ? (string) DB_NAME : 'vestplus');
            $user    = self::getEnvVar('DB_USER', defined('DB_USER') ? (string) DB_USER : 'root');
            $pass    = self::getEnvVar('DB_PASS', defined('DB_PASS') ? (string) DB_PASS : '');
            $charset = self::getEnvVar('DB_CHARSET', defined('DB_CHARSET') ? (string) DB_CHARSET : 'utf8mb4');

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $dbname, $charset);

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Propaga a exceção com mensagem padronizada e segura
                throw new PDOException(
                    sprintf('Falha na conexão com o banco de dados [%s]: %s', $dbname, $e->getMessage()),
                    (int) $e->getCode(),
                    $e
                );
            }
        }

        return self::$connection;
    }

    /**
     * Recupera o valor de uma variável de ambiente consultando getenv(), $_ENV e $_SERVER.
     *
     * @param string $key Nome da variável de ambiente.
     * @param string $default Valor de fallback padrão.
     * @return string Valor resolvido.
     */
    private static function getEnvVar(string $key, string $default): string
    {
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
    }

    /**
     * Encerra a conexão PDO ativa (útil para testes unitários ou recriação do pool).
     */
    public static function disconnect(): void
    {
        self::$connection = null;
    }
}
