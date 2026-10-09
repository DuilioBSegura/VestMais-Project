<?php

declare(strict_types=1);

use App\Core\Helper;

require dirname(__DIR__) . '/layout/header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= Helper::url('/aluno/dashboard') ?>" class="text-decoration-none">Início</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= Helper::e((string) $disciplina['nome']) ?></li>
    </ol>
</nav>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">
            <i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Artigos de <?= Helper::e((string) $disciplina['nome']) ?>
        </h1>
        <p class="text-muted mb-0">
            <?= Helper::e((string) ($disciplina['descricao'] ?: 'Acervo de artigos teóricos para estudo e aprofundamento.')) ?>
        </p>
    </div>
    <div>
        <a href="<?= Helper::url('/aluno/questoes?disciplina_id=' . (int) $disciplina['id']) ?>" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-pencil-square"></i> Praticar Questões Desta Matéria
        </a>
    </div>
</div>

<?php if (empty($artigos)): ?>
    <div class="card card-custom shadow-sm text-center py-5">
        <div class="card-body">
            <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-secondary">Nenhum artigo teórico publicado para esta disciplina.</h5>
            <p class="text-muted small">Novos conteúdos estão sendo preparados pela equipe de professores.</p>
            <a href="<?= Helper::url('/aluno/dashboard') ?>" class="btn btn-outline-secondary btn-sm mt-2">
                <i class="bi bi-arrow-left me-1"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row row-cols-1 g-4 mb-5">
        <?php foreach ($artigos as $artigo): ?>
            <div class="col">
                <div class="card card-custom shadow-sm hover-shadow transition">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="badge badge-discipline">
                                <?= Helper::e((string) $disciplina['nome']) ?>
                            </span>
                            <?php if (!empty($artigo['categorias_lista'])): ?>
                                <?php foreach ($artigo['categorias_lista'] as $cat): ?>
                                    <span class="badge badge-category rounded-pill">
                                        <?= Helper::e((string) $cat) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <h3 class="h5 fw-bold text-dark mb-2">
                            <a href="<?= Helper::url('/aluno/artigo?slug=' . urlencode((string) $artigo['slug'])) ?>" class="text-dark text-decoration-none hover-primary">
                                <?= Helper::e((string) $artigo['titulo']) ?>
                            </a>
                        </h3>

                        <p class="text-secondary small mb-3">
                            <?= Helper::e(Helper::truncate((string) $artigo['conteudo'], 220)) ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <span class="text-muted small">
                                <i class="bi bi-clock me-1"></i> Atualizado em <?= date('d/m/Y', strtotime((string) ($artigo['atualizado_em'] ?? $artigo['criado_em']))) ?>
                            </span>
                            <a href="<?= Helper::url('/aluno/artigo?slug=' . urlencode((string) $artigo['slug'])) ?>" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm">
                                Ler Artigo <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
require dirname(__DIR__) . '/layout/footer.php';
