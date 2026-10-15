<?php

declare(strict_types=1);

use App\Core\Helper;

require dirname(__DIR__, 2) . '/layout/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">
            <i class="bi bi-journal-richtext text-primary me-2"></i>Gerenciamento de Artigos Teóricos
        </h1>
        <p class="text-muted mb-0">
            Cadastre, edite e organize o acervo de conteúdo teórico para vestibulandos e candidatos do ENEM.
        </p>
    </div>
    <div>
        <a href="<?= Helper::url('/admin/artigos/criar') ?>" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-lg"></i> Novo Artigo
        </a>
    </div>
</div>

<div class="card card-custom shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-semibold text-secondary">
            Artigos Cadastrados <span class="badge bg-light text-dark border ms-1"><?= count($artigos) ?></span>
        </h5>
    </div>

    <div class="card-body p-0">
        <?php if (empty($artigos)): ?>
            <div class="text-center py-5">
                <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">Nenhum artigo cadastrado até o momento.</h5>
                <p class="text-secondary small">Clique no botão "Novo Artigo" acima para cadastrar o primeiro conteúdo teórico.</p>
                <a href="<?= Helper::url('/admin/artigos/criar') ?>" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="bi bi-plus-circle me-1"></i> Criar Primeiro Artigo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th style="width: 70px;" class="ps-3">ID</th>
                            <th>Título e Identificador (Slug)</th>
                            <th style="width: 160px;">Disciplina</th>
                            <th>Categorias Vinculadas</th>
                            <th style="width: 140px;">Atualização</th>
                            <th style="width: 140px;" class="text-end pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($artigos as $artigo): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-muted">#<?= (int) $artigo['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark mb-1">
                                        <?= Helper::e((string) $artigo['titulo']) ?>
                                    </div>
                                    <div class="small text-muted font-monospace">
                                        <i class="bi bi-link-45deg"></i> <?= Helper::e((string) $artigo['slug']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-discipline px-2 py-1">
                                        <i class="bi bi-book me-1"></i><?= Helper::e((string) $artigo['disciplina_nome']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($artigo['categorias_lista'])): ?>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($artigo['categorias_lista'] as $catNome): ?>
                                                <span class="badge badge-category rounded-pill px-2 py-1">
                                                    <?= Helper::e((string) $catNome) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Sem categorias</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= date('d/m/Y H:i', strtotime((string) ($artigo['atualizado_em'] ?? $artigo['criado_em']))) ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= Helper::url('/admin/artigos/editar?id=' . (int) $artigo['id']) ?>" 
                                           class="btn btn-outline-secondary" 
                                           title="Editar Artigo">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-delete-confirm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteConfirmModal"
                                                data-id="<?= (int) $artigo['id'] ?>"
                                                data-title="<?= Helper::e((string) $artigo['titulo']) ?>"
                                                data-action="<?= Helper::url('/admin/artigos/excluir?id=' . (int) $artigo['id']) ?>"
                                                title="Excluir Artigo">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require dirname(__DIR__, 2) . '/layout/footer.php';
