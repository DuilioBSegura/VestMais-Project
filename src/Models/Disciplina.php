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

    /**
     * Cadastra uma nova disciplina.
     */
    public static function create(string $nome, string $slug, ?string $descricao = null): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO disciplinas (nome, slug, descricao) VALUES (:nome, :slug, :descricao)');
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, $descricao !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        return (int) $pdo->lastInsertId();
    }

    /**
     * Atualiza os dados de uma disciplina existente.
     */
    public static function update(int $id, string $nome, string $slug, ?string $descricao = null): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE disciplinas SET nome = :nome, slug = :slug, descricao = :descricao WHERE id = :id');
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, $descricao !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Remove uma disciplina pelo ID com tratamento transacional dos vínculos.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // Desvincula categorias desta disciplina
            $stmtCat = $pdo->prepare('UPDATE categorias SET disciplina_id = NULL WHERE disciplina_id = :id');
            $stmtCat->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtCat->execute();

            // Remove artigos e questões associados à disciplina
            $stmtArt = $pdo->prepare('DELETE FROM artigos WHERE disciplina_id = :id');
            $stmtArt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtArt->execute();

            $stmtQ = $pdo->prepare('DELETE FROM questoes WHERE disciplina_id = :id');
            $stmtQ->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtQ->execute();

            $stmt = $pdo->prepare('DELETE FROM disciplinas WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
