<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Helper;

$currentUser = Auth::user();
require dirname(__DIR__) . '/layout/header.php';
?>

<!-- Cabeçalho do Dashboard -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h3 fw-bold mb-0 text-dark">
                <i class="bi bi-speedometer2 text-success me-2"></i>Painel Operacional do Administrador
            </h1>
            <span class="badge badge-discipline">Gestão Ativa</span>
        </div>
        <p class="text-muted mb-0">
            Visão consolidada de métricas, catálogo de conteúdos e gestão da plataforma Vest+.
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= Helper::url('/admin/artigos/criar') ?>" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-lg"></i> Novo Artigo
        </a>
        <a href="<?= Helper::url('/admin/questoes/criar') ?>" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-circle"></i> Nova Questão
        </a>
    </div>
</div>

<!-- Cards de Métricas Superiores (Paleta Verde Esmeralda / Menta) -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 mb-4">
    <!-- Disciplinas -->
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-book-half"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Disciplinas</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) $totalDisciplinas ?></div>
                <span class="text-success small fw-medium">Áreas do Conhecimento</span>
            </div>
        </div>
    </div>

    <!-- Artigos Teóricos -->
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-journal-text"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Artigos Teóricos</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) $totalArtigos ?></div>
                <span class="text-success small fw-medium">Publicados no CMS</span>
            </div>
        </div>
    </div>

    <!-- Banco de Questões -->
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-patch-question"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Banco de Questões</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) $totalQuestoes ?></div>
                <span class="text-success small fw-medium">Questões com Gabarito</span>
            </div>
        </div>
    </div>

    <!-- Estudantes Ativos -->
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Estudantes Cadastrados</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) $totalAlunos ?></div>
                <span class="text-success small fw-medium">Contas Ativas</span>
            </div>
        </div>
    </div>
</div>

<!-- Atalhos Rápidos de Ação -->
<div class="card card-custom p-4 mb-4 bg-white">
    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-lightning-charge-fill text-success"></i> Ações Rápidas de Gestão
    </h5>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= Helper::url('/admin/artigos') ?>" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-journal-richtext"></i> Gerenciar Todos os Artigos (<?= (int) $totalArtigos ?>)
        </a>
        <a href="<?= Helper::url('/admin/questoes') ?>" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-patch-question-fill"></i> Gerenciar Banco de Questões (<?= (int) $totalQuestoes ?>)
        </a>
        <a href="<?= Helper::url('/aluno/dashboard') ?>" class="btn btn-outline-success d-inline-flex align-items-center gap-2">
            <i class="bi bi-eye"></i> Visualizar Experiência do Aluno
        </a>
        <a href="<?= Helper::url('/') ?>" class="btn btn-light border d-inline-flex align-items-center gap-2">
            <i class="bi bi-house-door"></i> Ir para a Landing Page
        </a>
    </div>
</div>

<!-- Tabelas de Atividades Recentes -->
<div class="row g-4 mb-5">
    <!-- Últimos Artigos Publicados -->
    <div class="col-lg-6">
        <div class="card card-custom h-100 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-journal-bookmark text-success"></i> Últimos Artigos Publicados
                </h6>
                <a href="<?= Helper::url('/admin/artigos') ?>" class="small text-success text-decoration-none fw-semibold">
                    Ver Todos &rarr;
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($ultimosArtigos)): ?>
                    <p class="text-muted p-4 small mb-0 text-center">Nenhum artigo cadastrado.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th class="ps-3">Título</th>
                                    <th>Disciplina</th>
                                    <th class="text-end pe-3">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimosArtigos as $art): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold text-dark small">
                                                <?= Helper::e(Helper::truncate((string) $art['titulo'], 40)) ?>
                                            </div>
                                            <span class="text-muted small font-monospace" style="font-size: 0.75rem;">
                                                /<?= Helper::e((string) $art['slug']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-discipline small">
                                                <?= Helper::e((string) $art['disciplina_nome']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="<?= Helper::url('/admin/artigos/editar?id=' . (int) $art['id']) ?>" class="btn btn-outline-secondary btn-sm py-0 px-2" title="Editar">
                                                <i class="bi bi-pencil-fill small"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Últimas Questões Cadastradas -->
    <div class="col-lg-6">
        <div class="card card-custom h-100 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-patch-question-fill text-success"></i> Últimas Questões Cadastradas
                </h6>
                <a href="<?= Helper::url('/admin/questoes') ?>" class="small text-success text-decoration-none fw-semibold">
                    Ver Todas &rarr;
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($ultimasQuestoes)): ?>
                    <p class="text-muted p-4 small mb-0 text-center">Nenhuma questão cadastrada.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th class="ps-3">Enunciado</th>
                                    <th class="text-center">Gabarito</th>
                                    <th class="text-end pe-3">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimasQuestoes as $q): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-medium text-dark small">
                                                <?= Helper::e(Helper::truncate((string) $q['enunciado'], 48)) ?>
                                            </div>
                                            <span class="badge badge-discipline small" style="font-size: 0.75rem;">
                                                <?= Helper::e((string) $q['disciplina_nome']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                                <?= Helper::e((string) $q['resposta_correta']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="<?= Helper::url('/admin/questoes/editar?id=' . (int) $q['id']) ?>" class="btn btn-outline-secondary btn-sm py-0 px-2" title="Editar">
                                                <i class="bi bi-pencil-fill small"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require dirname(__DIR__) . '/layout/footer.php';
