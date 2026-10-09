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
            $host = getenv('DB_HOST') ?: (defined('DB_HOST') ? DB_HOST : '127.0.0.1');
            $port = getenv('DB_PORT') ?: (defined('DB_PORT') ? (string) DB_PORT : '3306');
            $dbname = getenv('DB_NAME') ?: (defined('DB_NAME') ? DB_NAME : 'vestplus');
            $user = getenv('DB_USER') ?: (defined('DB_USER') ? DB_USER : 'root');
            $pass = getenv('DB_PASS') !== false ? (string) getenv('DB_PASS') : (defined('DB_PASS') ? (string) DB_PASS : '');
            $charset = getenv('DB_CHARSET') ?: (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4');

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
                    sprintf('Falha na conexão com o banco de dados [%s]: %s', $dbname, $e->getMessage()),
                    (int) $e->getCode(),
                    $e
                );
            }
        }

        return self::$connection;
    }

    /**
     * Encerra a conexão PDO ativa (útil para testes unitários ou recriação do pool).
     */
    public static function disconnect(): void
    {
        self::$connection = null;
    }
}
