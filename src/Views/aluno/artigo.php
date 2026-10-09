<?php

declare(strict_types=1);

use App\Core\Helper;

require dirname(__DIR__) . '/layout/header.php';

// Parser didático seguro de conteúdo Markdown para leitura imersiva
$conteudoFormatado = (function (string $raw): string {
    $safe = htmlspecialchars($raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    // Títulos estruturados
    $safe = preg_replace('/^# (.*?)$/m', '<h2 class="h3 fw-bold text-dark mt-4 mb-3 border-bottom pb-2">$1</h2>', $safe);
    $safe = preg_replace('/^## (.*?)$/m', '<h3 class="h4 fw-bold text-dark mt-4 mb-2">$1</h3>', $safe);
    $safe = preg_replace('/^### (.*?)$/m', '<h4 class="h5 fw-semibold text-secondary mt-3 mb-2">$1</h4>', $safe);

    // Destaques e código em linha
    $safe = preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-dark">$1</strong>', $safe);
    $safe = preg_replace('/`(.*?)`/', '<code>$1</code>', $safe);

    // Caixas de Destaque Didáticas (Callout Dica ENEM)
    $safe = preg_replace(
        '/^>\s*(?:\[!DICA\]|\[!ENEM\]|Dica ENEM:?)\s*(.*?)$/m',
        '<div class="callout-dica-enem"><div class="callout-title"><i class="bi bi-lightbulb-fill"></i> Dica ENEM & Vestibulares</div><div>$1</div></div>',
        $safe
    );

    // Caixas de Destaque Didáticas (Callout Nota / Atenção)
    $safe = preg_replace(
        '/^>\s*(?:\[!NOTA\]|\[!ATENCAO\]|Nota:?)\s*(.*?)$/m',
        '<div class="callout-nota"><div class="callout-title"><i class="bi bi-info-circle-fill"></i> Nota Importante</div><div>$1</div></div>',
        $safe
    );

    // Citações em Bloco Padrão
    $safe = preg_replace('/^>\s*(.*?)$/m', '<blockquote>$1</blockquote>', $safe);

    // Listas com marcadores
    $safe = preg_replace('/^- (.*?)$/m', '<div class="d-flex align-items-baseline mb-2"><i class="bi bi-check-circle-fill text-success fs-6 me-2"></i><div>$1</div></div>', $safe);

    return nl2br((string) $safe);
})((string) $artigo['conteudo']);

$tempoLeitura = (int) ($tempoLeitura ?? 3);
$artigosRelacionados = $artigosRelacionados ?? [];
?>

<div class="container px-lg-4 py-4 mb-5">
    <!-- Navegação Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small bg-white px-3 py-2 rounded-3 border shadow-sm">
            <li class="breadcrumb-item">
                <a href="<?= Helper::url('/aluno/dashboard') ?>" class="text-decoration-none">
                    <i class="bi bi-house-door me-1"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= Helper::url('/aluno/disciplina?id=' . (int) $artigo['disciplina_id']) ?>" class="text-decoration-none">
                    <?= Helper::e((string) $artigo['disciplina_nome']) ?>
                </a>
            </li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 320px;" aria-current="page">
                <?= Helper::e((string) $artigo['titulo']) ?>
            </li>
        </ol>
    </nav>

    <div class="row g-4 justify-content-center">
        <!-- Coluna Principal de Leitura -->
        <div class="col-lg-8">
            <main class="article-reader">
                <!-- Cabeçalho do Artigo -->
                <header class="border-bottom pb-4 mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge badge-discipline px-3 py-2 fs-7">
                                <i class="bi bi-book-half me-1"></i><?= Helper::e((string) $artigo['disciplina_nome']) ?>
                            </span>
                            <?php if (!empty($artigo['categorias'])): ?>
                                <?php foreach ($artigo['categorias'] as $cat): ?>
                                    <span class="badge badge-category rounded-pill px-3 py-2 fs-7">
                                        <?= Helper::e((string) $cat['nome']) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Badge de Estimativa de Leitura -->
                        <span class="reading-time-badge shadow-sm">
                            <i class="bi bi-stopwatch text-success"></i> <?= $tempoLeitura ?> min de leitura
                        </span>
                    </div>

                    <h1 class="display-6 fw-bold text-dark lh-sm mb-3">
                        <?= Helper::e((string) $artigo['titulo']) ?>
                    </h1>

                    <div class="d-flex flex-wrap align-items-center text-muted small gap-3">
                        <span>
                            <i class="bi bi-calendar3 me-1"></i> Publicado em <?= date('d/m/Y', strtotime((string) $artigo['criado_em'])) ?>
                        </span>
                        <?php if (!empty($artigo['atualizado_em']) && $artigo['atualizado_em'] !== $artigo['criado_em']): ?>
                            <span>
                                &bull; <i class="bi bi-pencil me-1"></i> Atualizado em <?= date('d/m/Y', strtotime((string) $artigo['atualizado_em'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </header>

                <!-- Corpo do Conteúdo Teórico -->
                <article class="article-content">
                    <?= $conteudoFormatado ?>
                </article>

                <!-- Bloco de Fixação / Chamada para Questões -->
                <footer class="mt-5 pt-4 border-top">
                    <div class="card bg-success bg-opacity-10 border-success border-opacity-25 rounded-4 p-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <div>
                                <h5 class="fw-bold text-success mb-1">
                                    <i class="bi bi-patch-question-fill me-1"></i> Fixe este conteúdo agora
                                </h5>
                                <p class="text-secondary small mb-0">
                                    Resolva questões de múltipla escolha desta matéria e receba explicações passo a passo.
                                </p>
                            </div>
                            <div>
                                <a href="<?= Helper::url('/aluno/questoes?disciplina_id=' . (int) $artigo['disciplina_id']) ?>" class="btn btn-primary btn-pill shadow-sm d-inline-flex align-items-center gap-2 text-nowrap">
                                    <i class="bi bi-pencil-square"></i> Resolver Questões
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="<?= Helper::url('/aluno/disciplina?id=' . (int) $artigo['disciplina_id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Ver mais artigos de <?= Helper::e((string) $artigo['disciplina_nome']) ?>
                        </a>
                        <a href="<?= Helper::url('/aluno/dashboard') ?>" class="btn btn-light btn-sm rounded-pill px-3 border">
                            <i class="bi bi-grid-fill me-1"></i> Dashboard
                        </a>
                    </div>
                </footer>
            </main>
        </div>

        <!-- Sidebar / Bloco Lateral de Apoio -->
        <aside class="col-lg-4">
            <div class="article-sidebar d-flex flex-column gap-4">
                <!-- Card 1: Ações Rápidas -->
                <div class="card card-custom p-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-lightning-charge-fill text-success"></i> Estudo Ativo
                    </h6>
                    <p class="text-muted small mb-3">
                        A prática imediata com questões potencializa a retenção da teoria lida em até 70%.
                    </p>
                    <a href="<?= Helper::url('/aluno/questoes?disciplina_id=' . (int) $artigo['disciplina_id']) ?>" class="btn btn-primary btn-pill w-100 mb-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check2-circle"></i> Praticar Questões Desta Matéria
                    </a>
                    <a href="<?= Helper::url('/aluno/disciplina?id=' . (int) $artigo['disciplina_id']) ?>" class="btn btn-outline-secondary btn-sm btn-pill w-100">
                        Índice da Disciplina
                    </a>
                </div>

                <!-- Card 2: Artigos Relacionados da Mesma Matéria -->
                <div class="card card-custom p-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-collection-fill text-success"></i> Mais em <?= Helper::e((string) $artigo['disciplina_nome']) ?>
                    </h6>

                    <?php if (!empty($artigosRelacionados)): ?>
                        <div class="list-group list-group-flush small">
                            <?php foreach ($artigosRelacionados as $rel): ?>
                                <a href="<?= Helper::url('/aluno/artigo?slug=' . urlencode((string) $rel['slug'])) ?>" class="list-group-item list-group-item-action px-0 py-2 border-bottom text-dark text-decoration-none">
                                    <div class="fw-semibold text-truncate"><?= Helper::e((string) $rel['titulo']) ?></div>
                                    <div class="text-muted fs-8 mt-1">
                                        <i class="bi bi-clock me-1"></i> Publicado em <?= date('d/m/Y', strtotime((string) $rel['criado_em'])) ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">
                            Você está lendo o artigo em destaque desta disciplina. Novos resumos serão disponibilizados em breve!
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Card 3: Metodologia e Dica de Revisão -->
                <div class="card card-custom p-4 bg-light border-0">
                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-bookmark-check-fill text-success"></i> Dica de Aprendizagem
                    </h6>
                    <p class="text-muted small mb-0 lh-base">
                        Faça anotações com suas próprias palavras ao finalizar a leitura e teste resolver ao menos 5 questões sem consulta antes de avançar para o próximo tópico.
                    </p>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php
require dirname(__DIR__) . '/layout/footer.php';
