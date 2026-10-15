<?php

declare(strict_types=1);

namespace App\Models;

use Config\Database;
use PDO;
use Throwable;

/**
 * Model responsável pela persistência e manipulação de Questões de Múltipla Escolha
 * e do relacionamento N:N com Categorias.
 */
final class Questao
{
    /**
     * Alternativas válidas suportadas pela plataforma.
     */
    public const ALTERNATIVAS_VALIDAS = ['A', 'B', 'C', 'D', 'E'];

    /**
     * Lista todas as questões cadastradas com informações da disciplina
     * e categorias vinculadas.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAll(): array
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT 
                    q.id,
                    q.disciplina_id,
                    d.nome AS disciplina_nome,
                    q.enunciado,
                    q.alternativa_a,
                    q.alternativa_b,
                    q.alternativa_c,
                    q.alternativa_d,
                    q.alternativa_e,
                    q.resposta_correta,
                    q.explicacao,
                    q.criado_em,
                    GROUP_CONCAT(DISTINCT c.nome ORDER BY c.nome SEPARATOR ", ") AS categorias_nomes,
                    GROUP_CONCAT(DISTINCT c.id ORDER BY c.id SEPARATOR ",") AS categorias_ids_str
                FROM questoes q
                INNER JOIN disciplinas d ON d.id = q.disciplina_id
                LEFT JOIN questao_categorias qc ON qc.questao_id = q.id
                LEFT JOIN categorias c ON c.id = qc.categoria_id
                GROUP BY q.id, q.disciplina_id, d.nome, q.enunciado, q.alternativa_a, q.alternativa_b, 
                         q.alternativa_c, q.alternativa_d, q.alternativa_e, q.resposta_correta, 
                         q.explicacao, q.criado_em
                ORDER BY q.criado_em DESC, q.id DESC';

        $stmt = $pdo->query($sql);
        $questoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($questoes as &$q) {
            $ids = !empty($q['categorias_ids_str']) ? explode(',', (string) $q['categorias_ids_str']) : [];
            $q['categoria_ids'] = array_map('intval', $ids);
            $q['categorias_lista'] = !empty($q['categorias_nomes']) ? explode(', ', (string) $q['categorias_nomes']) : [];
        }

        return $questoes;
    }

    /**
     * Filtra questões com base em disciplina e/ou categoria para a área do aluno.
     *
     * @param int|null $disciplinaId
     * @param int|null $categoriaId
     * @return array<int, array<string, mixed>>
     */
    public static function filtrar(?int $disciplinaId = null, ?int $categoriaId = null): array
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT 
                    q.id,
                    q.disciplina_id,
                    d.nome AS disciplina_nome,
                    q.enunciado,
                    q.alternativa_a,
                    q.alternativa_b,
                    q.alternativa_c,
                    q.alternativa_d,
                    q.alternativa_e,
                    q.resposta_correta,
                    q.explicacao,
                    q.criado_em,
                    GROUP_CONCAT(DISTINCT c.nome ORDER BY c.nome SEPARATOR ", ") AS categorias_nomes,
                    GROUP_CONCAT(DISTINCT c.id ORDER BY c.id SEPARATOR ",") AS categorias_ids_str
                FROM questoes q
                INNER JOIN disciplinas d ON d.id = q.disciplina_id
                LEFT JOIN questao_categorias qc ON qc.questao_id = q.id
                LEFT JOIN categorias c ON c.id = qc.categoria_id';

        $where = [];
        $params = [];

        if ($categoriaId !== null && $categoriaId > 0) {
            $sql .= ' INNER JOIN questao_categorias qc_filter ON qc_filter.questao_id = q.id AND qc_filter.categoria_id = :cat_filter';
            $params[':cat_filter'] = $categoriaId;
        }

        if ($disciplinaId !== null && $disciplinaId > 0) {
            $where[] = 'q.disciplina_id = :disc_filter';
            $params[':disc_filter'] = $disciplinaId;
        }

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' GROUP BY q.id, q.disciplina_id, d.nome, q.enunciado, q.alternativa_a, q.alternativa_b, 
                           q.alternativa_c, q.alternativa_d, q.alternativa_e, q.resposta_correta, 
                           q.explicacao, q.criado_em
                  ORDER BY q.id ASC';

        $stmt = $pdo->prepare($sql);
        foreach ($params as $param => $val) {
            $stmt->bindValue($param, $val, PDO::PARAM_INT);
        }
        $stmt->execute();
        $questoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($questoes as &$q) {
            $ids = !empty($q['categorias_ids_str']) ? explode(',', (string) $q['categorias_ids_str']) : [];
            $q['categoria_ids'] = array_map('intval', $ids);
            $q['categorias_lista'] = !empty($q['categorias_nomes']) ? explode(', ', (string) $q['categorias_nomes']) : [];
        }

        return $questoes;
    }

    /**
     * Valida uma resposta submetida pelo aluno de forma segura via PDO.
     * Retorna se está correta, qual é o gabarito e a explicação cadastrada.
     *
     * @param int $questaoId
     * @param string $resposta
     * @return array<string, mixed>|null
     */
    public static function verificarResposta(int $questaoId, string $resposta): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, resposta_correta, explicacao FROM questoes WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $questaoId, PDO::PARAM_INT);
        $stmt->execute();

        $questao = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$questao) {
            return null;
        }

        $respostaNormalizada = strtoupper(trim($resposta));
        $gabarito = strtoupper(trim((string) $questao['resposta_correta']));
        $estaCorreta = ($respostaNormalizada === $gabarito);

        return [
            'sucesso'    => true,
            'correta'    => $estaCorreta,
            'gabarito'   => $gabarito,
            'explicacao' => (string) ($questao['explicacao'] ?? 'Nenhum comentário cadastrado para esta questão.')
        ];
    }

    /**
     * Busca uma questão específica pelo ID com todas as alternativas e categorias.
     *
     * @return array<string, mixed>|null
     */
    public static function getById(int $id): ?array
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT 
                    q.id,
                    q.disciplina_id,
                    d.nome AS disciplina_nome,
                    q.enunciado,
                    q.alternativa_a,
                    q.alternativa_b,
                    q.alternativa_c,
                    q.alternativa_d,
                    q.alternativa_e,
                    q.resposta_correta,
                    q.explicacao,
                    q.criado_em
                FROM questoes q
                INNER JOIN disciplinas d ON d.id = q.disciplina_id
                WHERE q.id = :id
                LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $questao = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$questao) {
            return null;
        }

        // Recupera categorias associadas
        $sqlCat = 'SELECT c.id, c.nome, c.slug 
                   FROM categorias c
                   INNER JOIN questao_categorias qc ON qc.categoria_id = c.id
                   WHERE qc.questao_id = :questao_id
                   ORDER BY c.nome ASC';
        $stmtCat = $pdo->prepare($sqlCat);
        $stmtCat->bindValue(':questao_id', $id, PDO::PARAM_INT);
        $stmtCat->execute();

        $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
        $questao['categorias'] = $categorias;
        $questao['categoria_ids'] = array_column($categorias, 'id');

        return $questao;
    }

    /**
     * Cadastra uma nova questão de múltipla escolha e seus relacionamentos N:N
     * com controle estrito de transação PDO.
     *
     * @param array<string, mixed> $data
     * @param array<int> $categoriaIds
     * @return int ID da questão criada
     * @throws Throwable
     */
    public static function create(array $data, array $categoriaIds = []): int
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $sql = 'INSERT INTO questoes (
                        disciplina_id,
                        enunciado,
                        alternativa_a,
                        alternativa_b,
                        alternativa_c,
                        alternativa_d,
                        alternativa_e,
                        resposta_correta,
                        explicacao
                    ) VALUES (
                        :disciplina_id,
                        :enunciado,
                        :alternativa_a,
                        :alternativa_b,
                        :alternativa_c,
                        :alternativa_d,
                        :alternativa_e,
                        :resposta_correta,
                        :explicacao
                    )';

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':disciplina_id', (int) $data['disciplina_id'], PDO::PARAM_INT);
            $stmt->bindValue(':enunciado', (string) $data['enunciado'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_a', (string) $data['alternativa_a'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_b', (string) $data['alternativa_b'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_c', (string) $data['alternativa_c'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_d', (string) $data['alternativa_d'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_e', (string) $data['alternativa_e'], PDO::PARAM_STR);
            $stmt->bindValue(':resposta_correta', strtoupper(trim((string) $data['resposta_correta'])), PDO::PARAM_STR);
            $stmt->bindValue(':explicacao', !empty($data['explicacao']) ? (string) $data['explicacao'] : null, PDO::PARAM_STR);
            $stmt->execute();

            $questaoId = (int) $pdo->lastInsertId();

            if (!empty($categoriaIds)) {
                $sqlPivot = 'INSERT INTO questao_categorias (questao_id, categoria_id) VALUES (:questao_id, :categoria_id)';
                $stmtPivot = $pdo->prepare($sqlPivot);

                foreach (array_unique($categoriaIds) as $catId) {
                    $catId = (int) $catId;
                    if ($catId > 0) {
                        $stmtPivot->bindValue(':questao_id', $questaoId, PDO::PARAM_INT);
                        $stmtPivot->bindValue(':categoria_id', $catId, PDO::PARAM_INT);
                        $stmtPivot->execute();
                    }
                }
            }

            $pdo->commit();
            return $questaoId;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Atualiza uma questão existente e sincroniza suas categorias em transação PDO segura.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @param array<int> $categoriaIds
     * @return bool
     * @throws Throwable
     */
    public static function update(int $id, array $data, array $categoriaIds = []): bool
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $sql = 'UPDATE questoes SET
                        disciplina_id = :disciplina_id,
                        enunciado = :enunciado,
                        alternativa_a = :alternativa_a,
                        alternativa_b = :alternativa_b,
                        alternativa_c = :alternativa_c,
                        alternativa_d = :alternativa_d,
                        alternativa_e = :alternativa_e,
                        resposta_correta = :resposta_correta,
                        explicacao = :explicacao
                    WHERE id = :id';

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':disciplina_id', (int) $data['disciplina_id'], PDO::PARAM_INT);
            $stmt->bindValue(':enunciado', (string) $data['enunciado'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_a', (string) $data['alternativa_a'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_b', (string) $data['alternativa_b'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_c', (string) $data['alternativa_c'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_d', (string) $data['alternativa_d'], PDO::PARAM_STR);
            $stmt->bindValue(':alternativa_e', (string) $data['alternativa_e'], PDO::PARAM_STR);
            $stmt->bindValue(':resposta_correta', strtoupper(trim((string) $data['resposta_correta'])), PDO::PARAM_STR);
            $stmt->bindValue(':explicacao', !empty($data['explicacao']) ? (string) $data['explicacao'] : null, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Sincroniza tabela pivot
            $delPivot = $pdo->prepare('DELETE FROM questao_categorias WHERE questao_id = :questao_id');
            $delPivot->bindValue(':questao_id', $id, PDO::PARAM_INT);
            $delPivot->execute();

            if (!empty($categoriaIds)) {
                $sqlPivot = 'INSERT INTO questao_categorias (questao_id, categoria_id) VALUES (:questao_id, :categoria_id)';
                $stmtPivot = $pdo->prepare($sqlPivot);

                foreach (array_unique($categoriaIds) as $catId) {
                    $catId = (int) $catId;
                    if ($catId > 0) {
                        $stmtPivot->bindValue(':questao_id', $id, PDO::PARAM_INT);
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
     * Remove uma questão pelo ID dentro de uma transação PDO.
     *
     * @throws Throwable
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $delPivot = $pdo->prepare('DELETE FROM questao_categorias WHERE questao_id = :id');
            $delPivot->bindValue(':id', $id, PDO::PARAM_INT);
            $delPivot->execute();

            $delQuestao = $pdo->prepare('DELETE FROM questoes WHERE id = :id');
            $delQuestao->bindValue(':id', $id, PDO::PARAM_INT);
            $delQuestao->execute();

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
