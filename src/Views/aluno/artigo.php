<?php

declare(strict_types=1);

use App\Core\Helper;

require dirname(__DIR__) . '/layout/header.php';

// Formatador seguro de Markdown básico para leitura agradável
$conteudoFormatado = (function (string $raw): string {
    $safe = htmlspecialchars($raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    // Títulos
    $safe = preg_replace('/^# (.*?)$/m', '<h2 class="h3 fw-bold text-dark mt-4 mb-3 border-bottom pb-2">$1</h2>', $safe);
    $safe = preg_replace('/^## (.*?)$/m', '<h3 class="h4 fw-bold text-dark mt-4 mb-2">$1</h3>', $safe);
    $safe = preg_replace('/^### (.*?)$/m', '<h4 class="h5 fw-semibold text-secondary mt-3 mb-2">$1</h4>', $safe);
    // Destaques e código
    $safe = preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-dark">$1</strong>', $safe);
    $safe = preg_replace('/`(.*?)`/', '<code class="bg-light px-2 py-1 rounded text-primary font-monospace">$1</code>', $safe);
    // Listas com marcadores
    $safe = preg_replace('/^- (.*?)$/m', '<div class="d-flex align-items-baseline mb-1"><i class="bi bi-dot text-primary fs-5 me-1"></i><div>$1</div></div>', $safe);
    
    return nl2br((string) $safe);
})((string) $artigo['conteudo']);
?>

<div class="row justify-content-center mb-5">
    <div class="col-lg-9 col-xl-8">
        <!-- Navegação Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= Helper::url('/aluno/dashboard') ?>" class="text-decoration-none">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= Helper::url('/aluno/disciplina?id=' . (int) $artigo['disciplina_id']) ?>" class="text-decoration-none"><?= Helper::e((string) $artigo['disciplina_nome']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= Helper::e(Helper::truncate((string) $artigo['titulo'], 35)) ?></li>
            </ol>
        </nav>

        <div class="card card-custom border-0 shadow-sm p-4 p-md-5 bg-white">
            <!-- Cabeçalho do Artigo -->
            <header class="border-bottom pb-4 mb-4">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge badge-discipline px-3 py-2 fs-7">
                        <i class="bi bi-book me-1"></i><?= Helper::e((string) $artigo['disciplina_nome']) ?>
                    </span>
                    <?php if (!empty($artigo['categorias'])): ?>
                        <?php foreach ($artigo['categorias'] as $cat): ?>
                            <span class="badge badge-category rounded-pill px-3 py-2 fs-7">
                                <?= Helper::e((string) $cat['nome']) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <h1 class="display-6 fw-bold text-dark lh-sm mb-3">
                    <?= Helper::e((string) $artigo['titulo']) ?>
                </h1>

                <div class="d-flex align-items-center text-muted small gap-3">
                    <span>
                        <i class="bi bi-clock me-1"></i> Publicado em <?= date('d/m/Y', strtotime((string) $artigo['criado_em'])) ?>
                    </span>
                    <?php if (!empty($artigo['atualizado_em']) && $artigo['atualizado_em'] !== $artigo['criado_em']): ?>
                        <span>
                            &bull; Revisado em <?= date('d/m/Y', strtotime((string) $artigo['atualizado_em'])) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Conteúdo Teórico Formatado -->
            <article class="article-content fs-5 lh-lg text-secondary" style="color: #334155 !important;">
                <?= $conteudoFormatado ?>
            </article>

            <!-- Rodapé e Ações de Prática -->
            <footer class="mt-5 pt-4 border-top">
                <div class="card bg-light border-0 rounded-3 p-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Pronto para testar seus conhecimentos?</h5>
                            <p class="text-muted small mb-0">Resolva questões de múltipla escolha com gabarito comentado desta matéria.</p>
                        </div>
                        <div>
                            <a href="<?= Helper::url('/aluno/questoes?disciplina_id=' . (int) $artigo['disciplina_id']) ?>" class="btn btn-primary shadow-sm d-inline-flex align-items-center gap-2">
                                <i class="bi bi-pencil-square"></i> Resolver Questões
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="<?= Helper::url('/aluno/disciplina?id=' . (int) $artigo['disciplina_id']) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Voltar para Artigos de <?= Helper::e((string) $artigo['disciplina_nome']) ?>
                    </a>
                </div>
            </footer>
        </div>
    </div>
</div>

<?php
require dirname(__DIR__) . '/layout/footer.php';
