<?php

declare(strict_types=1);

namespace App\Models;

use Config\Database;
use PDO;

/**
 * Model para gerenciamento de Disciplinas (Matemática, Física, etc.).
 */
final class Disciplina
{
    /**
     * Retorna todas as disciplinas ordenadas por nome.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT id, nome, slug, descricao, criado_em FROM disciplinas ORDER BY nome ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna todas as disciplinas com a contagem agregada de artigos teóricos e questões vinculadas.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getWithCounts(): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT 
                    d.id,
                    d.nome,
                    d.slug,
                    d.descricao,
                    d.criado_em,
                    COUNT(DISTINCT a.id) AS total_artigos,
                    COUNT(DISTINCT q.id) AS total_questoes
                FROM disciplinas d
                LEFT JOIN artigos a ON a.disciplina_id = d.id
                LEFT JOIN questoes q ON q.disciplina_id = d.id
                GROUP BY d.id, d.nome, d.slug, d.descricao, d.criado_em
                ORDER BY d.nome ASC';

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma disciplina pelo seu ID numérico.
     *
     * @return array<string, mixed>|null
     */
    public static function getById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, nome, slug, descricao, criado_em FROM disciplinas WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Verifica se uma disciplina existe pelo ID.
     */
    public static function exists(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT 1 FROM disciplinas WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }
}
