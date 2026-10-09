<?php

declare(strict_types=1);

namespace App\Models;

use Config\Database;
use PDO;
use Throwable;

/**
 * Model responsável pela persistência e manipulação de Artigos Teóricos
 * e seu relacionamento N:N com Categorias.
 */
final class Artigo
{
    /**
     * Lista todos os artigos cadastrados acompanhados do nome da disciplina
     * e de suas categorias associadas.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAll(): array
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT 
                    a.id,
                    a.titulo,
                    a.slug,
                    a.disciplina_id,
                    d.nome AS disciplina_nome,
                    a.conteudo,
                    a.criado_em,
                    a.atualizado_em,
                    GROUP_CONCAT(DISTINCT c.nome ORDER BY c.nome SEPARATOR ", ") AS categorias_nomes,
                    GROUP_CONCAT(DISTINCT c.id ORDER BY c.id SEPARATOR ",") AS categorias_ids_str
                FROM artigos a
                INNER JOIN disciplinas d ON d.id = a.disciplina_id
                LEFT JOIN artigo_categorias ac ON ac.artigo_id = a.id
                LEFT JOIN categorias c ON c.id = ac.categoria_id
                GROUP BY a.id, a.titulo, a.slug, a.disciplina_id, d.nome, a.conteudo, a.criado_em, a.atualizado_em
                ORDER BY a.criado_em DESC, a.id DESC';

        $stmt = $pdo->query($sql);
        $artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Processa as categorias em arrays para facilitar o consumo nas views
        foreach ($artigos as &$artigo) {
            $ids = !empty($artigo['categorias_ids_str']) ? explode(',', (string) $artigo['categorias_ids_str']) : [];
            $artigo['categoria_ids'] = array_map('intval', $ids);
            $artigo['categorias_lista'] = !empty($artigo['categorias_nomes']) ? explode(', ', (string) $artigo['categorias_nomes']) : [];
        }

        return $artigos;
    }

    /**
     * Busca um artigo específico pelo ID com suas categorias associadas.
     *
     * @return array<string, mixed>|null
     */
    public static function getById(int $id): ?array
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT 
                    a.id,
                    a.titulo,
                    a.slug,
                    a.disciplina_id,
                    d.nome AS disciplina_nome,
                    a.conteudo,
                    a.criado_em,
                    a.atualizado_em
                FROM artigos a
                INNER JOIN disciplinas d ON d.id = a.disciplina_id
                WHERE a.id = :id
                LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $artigo = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$artigo) {
            return null;
        }

        // Recupera as categorias vinculadas
        $sqlCat = 'SELECT c.id, c.nome, c.slug 
                   FROM categorias c
                   INNER JOIN artigo_categorias ac ON ac.categoria_id = c.id
                   WHERE ac.artigo_id = :artigo_id
                   ORDER BY c.nome ASC';
        $stmtCat = $pdo->prepare($sqlCat);
        $stmtCat->bindValue(':artigo_id', $id, PDO::PARAM_INT);
        $stmtCat->execute();

        $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
        $artigo['categorias'] = $categorias;
        $artigo['categoria_ids'] = array_column($categorias, 'id');

        return $artigo;
    }

    /**
     * Busca todos os artigos de uma disciplina específica com suas categorias.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getByDisciplina(int $disciplinaId): array
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT 
                    a.id,
                    a.titulo,
                    a.slug,
                    a.disciplina_id,
                    d.nome AS disciplina_nome,
                    a.conteudo,
                    a.criado_em,
                    a.atualizado_em,
                    GROUP_CONCAT(DISTINCT c.nome ORDER BY c.nome SEPARATOR ", ") AS categorias_nomes,
                    GROUP_CONCAT(DISTINCT c.id ORDER BY c.id SEPARATOR ",") AS categorias_ids_str
                FROM artigos a
                INNER JOIN disciplinas d ON d.id = a.disciplina_id
                LEFT JOIN artigo_categorias ac ON ac.artigo_id = a.id
                LEFT JOIN categorias c ON c.id = ac.categoria_id
                WHERE a.disciplina_id = :disciplina_id
                GROUP BY a.id, a.titulo, a.slug, a.disciplina_id, d.nome, a.conteudo, a.criado_em, a.atualizado_em
                ORDER BY a.criado_em DESC, a.id DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':disciplina_id', $disciplinaId, PDO::PARAM_INT);
        $stmt->execute();
        $artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($artigos as &$artigo) {
            $ids = !empty($artigo['categorias_ids_str']) ? explode(',', (string) $artigo['categorias_ids_str']) : [];
            $artigo['categoria_ids'] = array_map('intval', $ids);
            $artigo['categorias_lista'] = !empty($artigo['categorias_nomes']) ? explode(', ', (string) $artigo['categorias_nomes']) : [];
        }

        return $artigos;
    }

    /**
     * Busca um artigo completo pelo seu slug com as categorias vinculadas.
     *
     * @return array<string, mixed>|null
     */
    public static function getBySlug(string $slug): ?array
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT 
                    a.id,
                    a.titulo,
                    a.slug,
                    a.disciplina_id,
                    d.nome AS disciplina_nome,
                    a.conteudo,
                    a.criado_em,
                    a.atualizado_em
                FROM artigos a
                INNER JOIN disciplinas d ON d.id = a.disciplina_id
                WHERE a.slug = :slug
                LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        $artigo = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$artigo) {
            return null;
        }

        $sqlCat = 'SELECT c.id, c.nome, c.slug 
                   FROM categorias c
                   INNER JOIN artigo_categorias ac ON ac.categoria_id = c.id
                   WHERE ac.artigo_id = :artigo_id
                   ORDER BY c.nome ASC';
        $stmtCat = $pdo->prepare($sqlCat);
        $stmtCat->bindValue(':artigo_id', (int) $artigo['id'], PDO::PARAM_INT);
        $stmtCat->execute();

        $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
        $artigo['categorias'] = $categorias;
        $artigo['categoria_ids'] = array_column($categorias, 'id');

        return $artigo;
    }

    /**
     * Verifica se determinado slug já está em uso por outro artigo.
     */
    public static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT 1 FROM artigos WHERE slug = :slug';
        if ($ignoreId !== null) {
            $sql .= ' AND id != :ignore_id';
        }
        $sql .= ' LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        if ($ignoreId !== null) {
            $stmt->bindValue(':ignore_id', $ignoreId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Cria um novo artigo e insere os vínculos N:N na tabela pivot dentro de uma transação PDO.
     *
     * @param array<string, mixed> $data Dados do artigo (titulo, slug, disciplina_id, conteudo)
     * @param array<int> $categoriaIds Lista de IDs de categorias a vincular
     * @return int ID do artigo recém-criado
     * @throws Throwable Em caso de erro na transação
     */
    public static function create(array $data, array $categoriaIds = []): int
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $sql = 'INSERT INTO artigos (titulo, slug, disciplina_id, conteudo)
                    VALUES (:titulo, :slug, :disciplina_id, :conteudo)';

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':titulo', (string) $data['titulo'], PDO::PARAM_STR);
            $stmt->bindValue(':slug', (string) $data['slug'], PDO::PARAM_STR);
            $stmt->bindValue(':disciplina_id', (int) $data['disciplina_id'], PDO::PARAM_INT);
            $stmt->bindValue(':conteudo', (string) $data['conteudo'], PDO::PARAM_STR);
            $stmt->execute();

            $artigoId = (int) $pdo->lastInsertId();

            // Vínculos N:N com categorias
            if (!empty($categoriaIds)) {
                $sqlPivot = 'INSERT INTO artigo_categorias (artigo_id, categoria_id) VALUES (:artigo_id, :categoria_id)';
                $stmtPivot = $pdo->prepare($sqlPivot);

                foreach (array_unique($categoriaIds) as $catId) {
                    $catId = (int) $catId;
                    if ($catId > 0) {
                        $stmtPivot->bindValue(':artigo_id', $artigoId, PDO::PARAM_INT);
                        $stmtPivot->bindValue(':categoria_id', $catId, PDO::PARAM_INT);
                        $stmtPivot->execute();
                    }
                }
            }

            $pdo->commit();
            return $artigoId;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Atualiza os dados de um artigo e sincroniza as relações N:N dentro de uma transação PDO.
     *
     * @param int $id ID do artigo
     * @param array<string, mixed> $data Dados atualizados
     * @param array<int> $categoriaIds Lista atualizada de IDs de categorias
     * @return bool
     * @throws Throwable Em caso de erro na transação
     */
    public static function update(int $id, array $data, array $categoriaIds = []): bool
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $sql = 'UPDATE artigos 
                    SET titulo = :titulo,
                        slug = :slug,
                        disciplina_id = :disciplina_id,
                        conteudo = :conteudo
                    WHERE id = :id';

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':titulo', (string) $data['titulo'], PDO::PARAM_STR);
            $stmt->bindValue(':slug', (string) $data['slug'], PDO::PARAM_STR);
            $stmt->bindValue(':disciplina_id', (int) $data['disciplina_id'], PDO::PARAM_INT);
            $stmt->bindValue(':conteudo', (string) $data['conteudo'], PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Sincroniza tabela pivot: remove vínculos existentes e recria
            $delPivot = $pdo->prepare('DELETE FROM artigo_categorias WHERE artigo_id = :artigo_id');
            $delPivot->bindValue(':artigo_id', $id, PDO::PARAM_INT);
            $delPivot->execute();

            if (!empty($categoriaIds)) {
                $sqlPivot = 'INSERT INTO artigo_categorias (artigo_id, categoria_id) VALUES (:artigo_id, :categoria_id)';
                $stmtPivot = $pdo->prepare($sqlPivot);

                foreach (array_unique($categoriaIds) as $catId) {
                    $catId = (int) $catId;
                    if ($catId > 0) {
                        $stmtPivot->bindValue(':artigo_id', $id, PDO::PARAM_INT);
                        $stmtPivot->bindValue(':categoria_id', $catId, PDO::PARAM_INT);
                        $stmtPivot->execute();
                    }
                }
            }

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Remove um artigo pelo ID. A constraint ON DELETE CASCADE gerencia as tabelas filhas,
     * mas a transação explícita garante integridade e rollback caso algo falhe.
     *
     * @throws Throwable
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $delPivot = $pdo->prepare('DELETE FROM artigo_categorias WHERE artigo_id = :id');
            $delPivot->bindValue(':id', $id, PDO::PARAM_INT);
            $delPivot->execute();

            $delArtigo = $pdo->prepare('DELETE FROM artigos WHERE id = :id');
            $delArtigo->bindValue(':id', $id, PDO::PARAM_INT);
            $delArtigo->execute();

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
