<?php

declare(strict_types=1);

namespace App\Models;

use Config\Database;
use PDO;

/**
 * Model responsável pela persistência do histórico de resoluções de questões
 * e pelo motor de gamificação e desbloqueio de medalhas/conquistas.
 */
final class Medalha
{
    /**
     * Retorna o total de medalhas cadastradas no sistema.
     */
    public static function countTotal(): int
    {
        $pdo = Database::getConnection();
        return (int) $pdo->query('SELECT COUNT(*) FROM medalhas')->fetchColumn();
    }

    /**
     * Registra no histórico do banco de dados a tentativa de resolução do estudante.
     */
    public static function registrarResposta(int $usuarioId, int $questaoId, string $alternativa, bool $acertou): bool
    {
        if ($usuarioId <= 0 || $questaoId <= 0) {
            return false;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO usuario_respostas (usuario_id, questao_id, alternativa_marcada, acertou) 
             VALUES (:usuario_id, :questao_id, :alternativa_marcada, :acertou)'
        );
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':questao_id', $questaoId, PDO::PARAM_INT);
        $stmt->bindValue(':alternativa_marcada', strtoupper($alternativa), PDO::PARAM_STR);
        $stmt->bindValue(':acertou', $acertou ? 1 : 0, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Retorna todas as medalhas cadastradas associadas ao status de conquista do usuário.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getVitrineParaUsuario(int $usuarioId): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT 
                    m.id,
                    m.slug,
                    m.titulo,
                    m.descricao,
                    m.categoria,
                    m.icone,
                    m.cor,
                    m.criterio_tipo,
                    m.criterio_valor,
                    (um.conquistada_em IS NOT NULL) AS conquistada,
                    um.conquistada_em
                FROM medalhas m
                LEFT JOIN usuario_medalhas um 
                    ON um.medalha_id = m.id AND um.usuario_id = :usuario_id
                ORDER BY conquistada DESC, m.id ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(static function (array $r): array {
            $r['conquistada'] = (bool) $r['conquistada'];
            return $r;
        }, $rows);
    }

    /**
     * Retorna apenas as medalhas conquistadas pelo estudante.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getConquistadasPorUsuario(int $usuarioId): array
    {
        $vitrine = self::getVitrineParaUsuario($usuarioId);
        return array_values(array_filter($vitrine, static fn(array $m) => $m['conquistada'] === true));
    }

    /**
     * Retorna métricas consolidadas de desempenho e progresso do estudante.
     *
     * @return array<string, mixed>
     */
    public static function getEstatisticasEstudante(int $usuarioId): array
    {
        $pdo = Database::getConnection();

        // Total de questões respondidas
        $stmtTotal = $pdo->prepare('SELECT COUNT(*) FROM usuario_respostas WHERE usuario_id = :uid');
        $stmtTotal->execute([':uid' => $usuarioId]);
        $totalRespondidas = (int) $stmtTotal->fetchColumn();

        // Total de acertos
        $stmtAcertos = $pdo->prepare('SELECT COUNT(*) FROM usuario_respostas WHERE usuario_id = :uid AND acertou = 1');
        $stmtAcertos->execute([':uid' => $usuarioId]);
        $totalAcertos = (int) $stmtAcertos->fetchColumn();

        // Taxa de acertos (%)
        $taxaAcertoPct = $totalRespondidas > 0 
            ? (int) round(($totalAcertos / $totalRespondidas) * 100) 
            : 0;

        // Sequência atual de acertos consecutivos
        $stmtUltimas = $pdo->prepare('SELECT acertou FROM usuario_respostas WHERE usuario_id = :uid ORDER BY criado_em DESC, id DESC LIMIT 50');
        $stmtUltimas->execute([':uid' => $usuarioId]);
        $ultimas = $stmtUltimas->fetchAll(PDO::FETCH_COLUMN);

        $sequenciaAtual = 0;
        foreach ($ultimas as $acerto) {
            if ((int) $acerto === 1) {
                $sequenciaAtual++;
            } else {
                break;
            }
        }

        // Total de medalhas
        $stmtMedalhasTotal = $pdo->query('SELECT COUNT(*) FROM medalhas');
        $totalMedalhas = (int) $stmtMedalhasTotal->fetchColumn();

        $stmtMedalhasUser = $pdo->prepare('SELECT COUNT(*) FROM usuario_medalhas WHERE usuario_id = :uid');
        $stmtMedalhasUser->execute([':uid' => $usuarioId]);
        $medalhasConquistadas = (int) $stmtMedalhasUser->fetchColumn();

        $progressoMedalhasPct = $totalMedalhas > 0 
            ? (int) round(($medalhasConquistadas / $totalMedalhas) * 100) 
            : 0;

        return [
            'total_respondidas'      => $totalRespondidas,
            'total_acertos'          => $totalAcertos,
            'taxa_acerto_pct'        => $taxaAcertoPct,
            'sequencia_atual'        => $sequenciaAtual,
            'medalhas_conquistadas'  => $medalhasConquistadas,
            'total_medalhas'         => $totalMedalhas,
            'progresso_medalhas_pct' => $progressoMedalhasPct,
        ];
    }

    /**
     * Avalia os critérios de gamificação e desbloqueia medalhas recém-conquistadas pelo aluno.
     * Retorna a lista de medalhas que acabaram de ser desbloqueadas nesta verificação.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function verificarNovasConquistas(int $usuarioId): array
    {
        if ($usuarioId <= 0) {
            return [];
        }

        $pdo = Database::getConnection();

        // Busca medalhas que o usuário AINDA NÃO possui
        $sql = 'SELECT m.* 
                FROM medalhas m
                LEFT JOIN usuario_medalhas um 
                    ON um.medalha_id = m.id AND um.usuario_id = :uid
                WHERE um.id IS NULL';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uid' => $usuarioId]);
        $bloqueadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($bloqueadas)) {
            return [];
        }

        // Estatísticas do estudante
        $stats = self::getEstatisticasEstudante($usuarioId);

        // Contagens por área de conhecimento
        $sqlAreas = 'SELECT d.nome, COUNT(ur.id) AS total
                     FROM usuario_respostas ur
                     INNER JOIN questoes q ON q.id = ur.questao_id
                     INNER JOIN disciplinas d ON d.id = q.disciplina_id
                     WHERE ur.usuario_id = :uid
                     GROUP BY d.nome';
        $stmtAreas = $pdo->prepare($sqlAreas);
        $stmtAreas->execute([':uid' => $usuarioId]);
        $areasRows = $stmtAreas->fetchAll(PDO::FETCH_ASSOC);

        $exatasCount = 0;
        $naturezaCount = 0;
        $humanasCount = 0;

        foreach ($areasRows as $row) {
            $nomeLower = mb_strtolower((string) $row['nome'], 'UTF-8');
            $cnt = (int) $row['total'];

            if (str_contains($nomeLower, 'matem') || str_contains($nomeLower, 'físic') || str_contains($nomeLower, 'fisic')) {
                $exatasCount += $cnt;
            } elseif (str_contains($nomeLower, 'biolog') || str_contains($nomeLower, 'químic') || str_contains($nomeLower, 'quimic')) {
                $naturezaCount += $cnt;
            } elseif (str_contains($nomeLower, 'histór') || str_contains($nomeLower, 'histor') || str_contains($nomeLower, 'geograf') || str_contains($nomeLower, 'filosof') || str_contains($nomeLower, 'sociolog')) {
                $humanasCount += $cnt;
            }
        }

        $novasConquistas = [];
        $stmtInsert = $pdo->prepare('INSERT INTO usuario_medalhas (usuario_id, medalha_id) VALUES (:uid, :mid)');

        foreach ($bloqueadas as $m) {
            $tipo = (string) $m['criterio_tipo'];
            $alvo = (int) $m['criterio_valor'];
            $atingiu = false;

            switch ($tipo) {
                case 'total_questoes':
                    $atingiu = ($stats['total_respondidas'] >= $alvo);
                    break;
                case 'total_acertos':
                    $atingiu = ($stats['total_acertos'] >= $alvo);
                    break;
                case 'sequencia_acertos':
                    $atingiu = ($stats['sequencia_atual'] >= $alvo);
                    break;
                case 'disciplina_exatas':
                    $atingiu = ($exatasCount >= $alvo);
                    break;
                case 'disciplina_natureza':
                    $atingiu = ($naturezaCount >= $alvo);
                    break;
                case 'disciplina_humanas':
                    $atingiu = ($humanasCount >= $alvo);
                    break;
            }

            if ($atingiu) {
                try {
                    $stmtInsert->execute([
                        ':uid' => $usuarioId,
                        ':mid' => (int) $m['id'],
                    ]);

                    $novasConquistas[] = [
                        'id'        => (int) $m['id'],
                        'slug'      => (string) $m['slug'],
                        'titulo'    => (string) $m['titulo'],
                        'descricao' => (string) $m['descricao'],
                        'icone'     => (string) $m['icone'],
                        'cor'       => (string) $m['cor'],
                    ];
                } catch (\Throwable $e) {
                    // Já inserida simultaneamente
                }
            }
        }

        return $novasConquistas;
    }
}
