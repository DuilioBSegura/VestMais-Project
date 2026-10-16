<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Helper;

$currentUser = Auth::user();
require dirname(__DIR__) . '/layout/header.php';
?>

<!-- Banner de Boas-Vindas Personalizado -->
<div class="p-4 p-md-5 mb-4 rounded-4 text-white shadow-sm" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #0f172a 100%); border-left: 6px solid #34d399;">
    <div class="container-fluid py-2">
        <div class="d-flex align-items-center gap-2 mb-2 small fw-bold text-uppercase" style="color: #a7f3d0;">
            <i class="bi bi-mortarboard-fill"></i> Ambiente de Estudos &bull; Foco em Aprovação
        </div>
        <h1 class="display-6 fw-bold text-white mb-2">
            Olá, <?= Helper::e($currentUser['nome'] ?? 'Estudante') ?>! O que vamos praticar hoje? 👋
        </h1>
        <p class="col-md-9 fs-6 mb-4" style="color: #ecfdf5; opacity: 0.95;">
            Explore nossos artigos teóricos formatados para leitura focada ou teste seus conhecimentos no banco interativo de questões com correção imediata e gabarito comentado.
        </p>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= Helper::url('/aluno/questoes') ?>" class="btn btn-light text-success fw-bold d-inline-flex align-items-center gap-2 shadow-sm px-4 py-2">
                <i class="bi bi-patch-question-fill text-success"></i> Acessar Banco de Questões
            </a>
            <a href="#materias" class="btn btn-outline-light d-inline-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-arrow-down-circle"></i> Ver Todas as Disciplinas
            </a>
        </div>
    </div>
</div>

<!-- Banner de Acesso Rápido ao Banco de Questões -->
<div class="card card-custom border-0 shadow-sm p-4 mb-4" style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1px solid #a7f3d0 !important;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-success bg-opacity-20 text-success fs-2 flex-shrink-0">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-1">Simulado Interativo com Feedback Imediato</h5>
                <p class="text-secondary small mb-0">
                    Selecione as alternativas de A a E e receba o gabarito oficial com resolução comentada na hora, sem recarregar a página!
                </p>
            </div>
        </div>
        <div>
            <a href="<?= Helper::url('/aluno/questoes') ?>" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm text-nowrap px-4 py-2">
                <i class="bi bi-play-circle-fill"></i> Iniciar Prática
            </a>
        </div>
    </div>
</div>

<!-- Grade de Disciplinas -->
<div id="materias" class="d-flex justify-content-between align-items-center mb-3 pt-2">
    <div>
        <h3 class="h4 fw-bold text-dark mb-0">
            <i class="bi bi-collection-fill text-success me-2"></i>Disciplinas Disponíveis
        </h3>
        <p class="text-muted small mb-0">Navegue pelas matérias para acessar resumos teóricos e questões específicas.</p>
    </div>
    <span class="badge badge-discipline">
        <?= count($disciplinas) ?> matérias ativas
    </span>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
    <?php foreach ($disciplinas as $disc): ?>
        <?php
            $totalArtigos = (int) ($disc['total_artigos'] ?? 0);
            $totalQuestoes = (int) ($disc['total_questoes'] ?? 0);

            // Ícones contextuais para todas as 14 disciplinas oficiais
            $nomeLower = mb_strtolower((string) $disc['nome'], 'UTF-8');
            if (str_contains($nomeLower, 'educa')) {
                $icon = 'bi-activity';
            } elseif (str_contains($nomeLower, 'matem')) {
                $icon = 'bi-calculator-fill';
            } elseif (str_contains($nomeLower, 'físic') || str_contains($nomeLower, 'fisic')) {
                $icon = 'bi-lightning-charge-fill';
            } elseif (str_contains($nomeLower, 'biolog')) {
                $icon = 'bi-flower1';
            } elseif (str_contains($nomeLower, 'quím') || str_contains($nomeLower, 'quim')) {
                $icon = 'bi-droplet-fill';
            } elseif (str_contains($nomeLower, 'histór') || str_contains($nomeLower, 'histor')) {
                $icon = 'bi-hourglass-split';
            } elseif (str_contains($nomeLower, 'geograf')) {
                $icon = 'bi-globe-americas';
            } elseif (str_contains($nomeLower, 'sociolog')) {
                $icon = 'bi-people-fill';
            } elseif (str_contains($nomeLower, 'filosof')) {
                $icon = 'bi-lightbulb-fill';
            } elseif (str_contains($nomeLower, 'portugu') || str_contains($nomeLower, 'língua')) {
                $icon = 'bi-chat-square-text-fill';
            } elseif (str_contains($nomeLower, 'literat')) {
                $icon = 'bi-bookmark-heart-fill';
            } elseif (str_contains($nomeLower, 'arte')) {
                $icon = 'bi-palette-fill';
            } elseif (str_contains($nomeLower, 'ingl')) {
                $icon = 'bi-translate';
            } elseif (str_contains($nomeLower, 'espanh')) {
                $icon = 'bi-globe2';
            } else {
                $icon = 'bi-journal-bookmark-fill';
            }
        ?>
        <div class="col">
            <div class="card card-custom h-100 hover-lift border-top border-4 border-success">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3 bg-success bg-opacity-10 text-success">
                            <i class="bi <?= $icon ?> fs-3"></i>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <span class="badge badge-discipline">
                                <i class="bi bi-journal-text me-1"></i><?= $totalArtigos ?> <?= $totalArtigos === 1 ? 'artigo' : 'artigos' ?>
                            </span>
                            <span class="badge bg-light text-secondary border">
                                <i class="bi bi-patch-question me-1"></i><?= $totalQuestoes ?> <?= $totalQuestoes === 1 ? 'questão' : 'questões' ?>
                            </span>
                        </div>
                    </div>

                    <h4 class="card-title h5 fw-bold text-dark mb-2">
                        <?= Helper::e((string) $disc['nome']) ?>
                    </h4>

                    <p class="card-text text-muted small flex-grow-1 mb-4 lh-base">
                        <?= Helper::e((string) ($disc['descricao'] ?: 'Fundamentos teóricos e exercícios de fixação para aprovação no vestibular.')) ?>
                    </p>

                    <div class="pt-3 border-top d-grid gap-2">
                        <a href="<?= Helper::url('/aluno/disciplina?id=' . (int) $disc['id']) ?>" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i class="bi bi-book-half"></i> Ver Matéria (Artigos Teóricos)
                        </a>
                        <a href="<?= Helper::url('/aluno/questoes?disciplina_id=' . (int) $disc['id']) ?>" class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-pencil-square"></i> Praticar Questões Desta Matéria
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
require dirname(__DIR__) . '/layout/footer.php';
