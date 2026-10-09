<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Artigo;
use App\Models\Disciplina;
use App\Models\Questao;
use Config\Database;
use PDO;

/**
 * Controller responsável pelo Dashboard Executivo e Operacional do Administrador.
 */
final class AdminDashboardController extends BaseController
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Renderiza o painel operacional do administrador com métricas consolidadas.
     */
    public function dashboard(): void
    {
        $pdo = Database::getConnection();

        $disciplinas = Disciplina::getAll();
        $artigos = Artigo::getAll();
        $questoes = Questao::getAll();

        // Contagem de estudantes cadastrados
        $stmtAlunos = $pdo->query('SELECT COUNT(*) FROM usuarios WHERE tipo = "aluno"');
        $totalAlunos = (int) $stmtAlunos->fetchColumn();

        // Últimos 5 artigos cadastrados
        $ultimosArtigos = array_slice($artigos, 0, 5);

        // Últimas 5 questões cadastradas
        $ultimasQuestoes = array_slice($questoes, 0, 5);

        $this->render('admin/dashboard', [
            'pageTitle'        => 'Painel Administrativo - Visão Geral',
            'totalDisciplinas' => count($disciplinas),
            'totalArtigos'     => count($artigos),
            'totalQuestoes'    => count($questoes),
            'totalAlunos'      => $totalAlunos,
            'ultimosArtigos'   => $ultimosArtigos,
            'ultimasQuestoes'  => $ultimasQuestoes,
            'disciplinas'      => $disciplinas,
        ]);
    }
}
