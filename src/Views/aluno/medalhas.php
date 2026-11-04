<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Helper;

$currentUser = Auth::user();
require dirname(__DIR__) . '/layout/header.php';

$totalMedalhas = (int) ($stats['total_medalhas'] ?? 8);
$medalhasConquistadas = (int) ($stats['medalhas_conquistadas'] ?? 0);
$progressoPct = (int) ($stats['progresso_medalhas_pct'] ?? 0);
?>

<!-- Banner Gamificado de Conquistas -->
<div class="p-4 p-md-5 mb-4 rounded-4 text-white shadow-sm" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #1e1b4b 100%); border-left: 6px solid #fbbf24;">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2 small fw-bold text-uppercase" style="color: #fde68a;">
                <i class="bi bi-trophy-fill text-warning"></i> Gamificação &bull; Foco na Aprovação
            </div>
            <h1 class="display-6 fw-bold text-white mb-2">
                Vitrine de Conquistas do Estudante 🏆
            </h1>
            <p class="fs-6 mb-4" style="color: #ecfdf5; opacity: 0.95;">
                Cada questão resolvida aproxima você da sua vaga no vestibular. Complete metas, mantenha sequências de acertos e desbloqueie todas as medalhas!
            </p>

            <!-- Barra de Progresso de Medalhas -->
            <div class="bg-black bg-opacity-25 p-3 rounded-3 border border-white border-opacity-10" style="max-width: 520px;">
                <div class="d-flex justify-content-between align-items-center mb-2 small">
                    <span class="fw-semibold">Progresso Geral de Medalhas:</span>
                    <span class="badge bg-warning text-dark fw-bold"><?= $medalhasConquistadas ?> / <?= $totalMedalhas ?> Desbloqueadas (<?= $progressoPct ?>%)</span>
                </div>
                <div class="progress" style="height: 10px;" role="progressbar" aria-valuenow="<?= $progressoPct ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width: <?= $progressoPct ?>%;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 text-center mt-4 mt-lg-0 d-none d-lg-block">
            <div class="d-inline-flex p-4 rounded-circle bg-warning bg-opacity-10 border border-warning border-opacity-25 shadow">
                <i class="bi bi-award-fill text-warning" style="font-size: 5rem;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Métricas de Desempenho do Estudante -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 mb-4">
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-patch-question-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Questões Resolvidas</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) ($stats['total_respondidas'] ?? 0) ?></div>
                <span class="text-success small fw-medium">Tentativas no Simulado</span>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Acertos Confirmados</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) ($stats['total_acertos'] ?? 0) ?></div>
                <span class="text-success small fw-medium">Gabarito Oficial</span>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-pie-chart-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Taxa de Precisão</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) ($stats['taxa_acerto_pct'] ?? 0) ?>%</div>
                <span class="text-success small fw-medium">Aproveitamento Global</span>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-fire text-danger"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Sequência Atual</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) ($stats['sequencia_atual'] ?? 0) ?> 🔥</div>
                <span class="text-danger small fw-medium">Acertos Consecutivos</span>
            </div>
        </div>
    </div>
</div>

<!-- Grid de Medalhas (Vitrine) -->
<div class="d-flex justify-content-between align-items-center mb-3 pt-2">
    <div>
        <h3 class="h4 fw-bold text-dark mb-0">
            <i class="bi bi-award text-warning me-2"></i>Catálogo de Medalhas
        </h3>
        <p class="text-muted small mb-0">Complete os desafios propostos para desbloquear e colecionar todos os selos.</p>
    </div>
    <span class="badge bg-light text-dark border">
        <?= $medalhasConquistadas ?> Conquistada(s)
    </span>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-5">
    <?php foreach ($vitrine as $m): ?>
        <?php 
            $conquistada = (bool) $m['conquistada'];
            $cor = (string) ($m['cor'] ?? 'emerald');
            $icone = (string) ($m['icone'] ?? 'bi-trophy-fill');

            // Mapeamento de cores
            $corClasses = [
                'emerald' => ['bg' => 'bg-success bg-opacity-10', 'text' => 'text-success', 'border' => 'border-success border-opacity-25'],
                'warning' => ['bg' => 'bg-warning bg-opacity-15', 'text' => 'text-warning', 'border' => 'border-warning border-opacity-50'],
                'danger'  => ['bg' => 'bg-danger bg-opacity-10',  'text' => 'text-danger',  'border' => 'border-danger border-opacity-25'],
                'purple'  => ['bg' => 'bg-primary bg-opacity-10', 'text' => 'text-primary', 'border' => 'border-primary border-opacity-25'],
                'info'    => ['bg' => 'bg-info bg-opacity-10',    'text' => 'text-info',    'border' => 'border-info border-opacity-25'],
                'primary' => ['bg' => 'bg-primary bg-opacity-10', 'text' => 'text-primary', 'border' => 'border-primary border-opacity-25'],
            ];
            $style = $corClasses[$cor] ?? $corClasses['emerald'];
        ?>
        <div class="col">
            <div class="card card-custom h-100 p-4 text-center position-relative <?= $conquistada ? 'shadow-sm' : 'opacity-75 bg-light' ?>" style="<?= $conquistada ? 'border: 2px solid #34d399;' : 'border: 1px dashed #cbd5e1;' ?>">
                <?php if ($conquistada): ?>
                    <span class="position-absolute top-0 end-0 m-3 badge bg-success text-white shadow-sm" style="font-size: 0.7rem;">
                        <i class="bi bi-check-circle-fill me-1"></i> Desbloqueada
                    </span>
                <?php else: ?>
                    <span class="position-absolute top-0 end-0 m-3 badge bg-secondary text-white" style="font-size: 0.7rem;">
                        <i class="bi bi-lock-fill me-1"></i> Bloqueada
                    </span>
                <?php endif; ?>

                <!-- Ícone da Medalha -->
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle <?= $conquistada ? $style['bg'] . ' ' . $style['border'] : 'bg-secondary bg-opacity-10 border' ?>" style="width: 76px; height: 76px; font-size: 2.2rem; border-width: 2px !important;">
                    <i class="bi <?= $conquistada ? Helper::e($icone) . ' ' . $style['text'] : 'bi-lock-fill text-muted' ?>"></i>
                </div>

                <!-- Título & Descrição -->
                <h5 class="fw-bold mb-2 <?= $conquistada ? 'text-dark' : 'text-muted' ?>">
                    <?= Helper::e((string) $m['titulo']) ?>
                </h5>
                <p class="small text-muted mb-3 flex-grow-1">
                    <?= Helper::e((string) $m['descricao']) ?>
                </p>

                <!-- Rodapé da Medalha -->
                <div class="pt-3 border-top mt-auto small">
                    <?php if ($conquistada): ?>
                        <span class="text-success fw-semibold">
                            <i class="bi bi-calendar-check me-1"></i> 
                            <?= date('d/m/Y', strtotime((string) $m['conquistada_em'])) ?>
                        </span>
                    <?php else: ?>
                        <span class="text-secondary">
                            <i class="bi bi-bullseye me-1"></i> Meta: <?= (int) $m['criterio_valor'] ?> <?= str_contains((string) $m['criterio_tipo'], 'acerto') ? 'acertos' : 'questões' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Banner de Convite para Praticar -->
<div class="card card-custom p-4 mb-4" style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1px solid #a7f3d0 !important;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-success bg-opacity-20 text-success fs-2 flex-shrink-0">
                <i class="bi bi-patch-question-fill"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-1">Pronto para conquistar sua próxima medalha?</h5>
                <p class="text-secondary small mb-0">
                    Acesse o Banco de Questões, responda aos simulados e desbloqueie novas conquistas em tempo real!
                </p>
            </div>
        </div>
        <div>
            <a href="<?= Helper::url('/aluno/questoes') ?>" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm text-nowrap px-4 py-2">
                <i class="bi bi-play-circle-fill"></i> Resolver Simulado
            </a>
        </div>
    </div>
</div>

<?php
require dirname(__DIR__) . '/layout/footer.php';
