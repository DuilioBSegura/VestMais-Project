<?php

declare(strict_types=1);

namespace App\Models;

use Config\Database;
use PDO;

/**
 * Model para gerenciamento de Categorias de Conteúdo e Questões.
 */
final class Categoria
{
    /**
     * Retorna todas as categorias cadastradas ordenadas alfabeticamente.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT id, nome, slug, criado_em FROM categorias ORDER BY nome ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma categoria por ID.
     *
     * @return array<string, mixed>|null
     */
    public static function getById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, nome, slug, criado_em FROM categorias WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Retorna as categorias associadas a um artigo específico.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getByArtigoId(int $artigoId): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT c.id, c.nome, c.slug 
                FROM categorias c
                INNER JOIN artigo_categorias ac ON ac.categoria_id = c.id
                WHERE ac.artigo_id = :artigo_id
                ORDER BY c.nome ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':artigo_id', $artigoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna as categorias associadas a uma questão específica.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getByQuestaoId(int $questaoId): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT c.id, c.nome, c.slug 
                FROM categorias c
                INNER JOIN questao_categorias qc ON qc.categoria_id = c.id
                WHERE qc.questao_id = :questao_id
                ORDER BY c.nome ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':questao_id', $questaoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Filtra e valida um array de IDs para garantir que existam no banco.
     *
     * @param array<int> $ids
     * @return array<int>
     */
    public static function filterValidIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $cleanIds = array_filter(array_map('intval', $ids), fn($id) => $id > 0);
        if (empty($cleanIds)) {
            return [];
        }

        $pdo = Database::getConnection();
        $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
        $stmt = $pdo->prepare("SELECT id FROM categorias WHERE id IN ({$placeholders})");
        $stmt->execute(array_values($cleanIds));

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}
