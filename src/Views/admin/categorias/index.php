<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helper;

require dirname(__DIR__, 2) . '/layout/header.php';
?>

<!-- Cabeçalho -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h3 fw-bold mb-0 text-dark">
                <i class="bi bi-tags-fill text-success me-2"></i>Disciplinas & Categorias
            </h1>
            <span class="badge badge-discipline">Taxonomia da Plataforma</span>
        </div>
        <p class="text-muted mb-0">
            Gerencie as matérias curriculares e tópicos transversais para indexação de artigos e questões.
        </p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNovaCategoria">
            <i class="bi bi-tag"></i> Nova Categoria
        </button>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNovaDisciplina">
            <i class="bi bi-book"></i> Nova Disciplina
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Coluna 1: Categorias / Tópicos (N:N) -->
    <div class="col-lg-6">
        <div class="card card-custom shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-tags text-success fs-5"></i>
                    <h5 class="mb-0 fw-bold text-dark">Tópicos e Categorias</h5>
                </div>
                <span class="badge bg-light text-muted border"><?= count($categorias) ?> cadastrada(s)</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nome / Slug</th>
                                <th class="text-center">Artigos</th>
                                <th class="text-center">Questões</th>
                                <th class="text-end pe-3">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $cat): ?>
                                <?php $cId = (int) $cat['id']; ?>
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="fw-semibold text-dark d-flex align-items-center gap-2 flex-wrap">
                                            <span><?= Helper::e((string) $cat['nome']) ?></span>
                                            <?php if (!empty($cat['disciplina_nome'])): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle small py-1 px-2" style="font-size: 0.72rem;">
                                                    <i class="bi bi-book me-1"></i><?= Helper::e((string) $cat['disciplina_nome']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border small py-1 px-2" style="font-size: 0.72rem;">Geral</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="font-monospace small text-muted">/<?= Helper::e((string) $cat['slug']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border"><?= (int) ($cat['total_artigos'] ?? 0) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><?= (int) ($cat['total_questoes'] ?? 0) ?></span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm btn-delete-confirm" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-id="<?= $cId ?>" 
                                                data-action="<?= Helper::url('/admin/categorias/excluir?id=' . $cId) ?>" 
                                                data-title="Categoria: <?= Helper::e((string) $cat['nome']) ?>"
                                                title="Excluir Categoria">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna 2: Disciplinas Estruturais -->
    <div class="col-lg-6">
        <div class="card card-custom shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-collection text-success fs-5"></i>
                    <h5 class="mb-0 fw-bold text-dark">Disciplinas Estruturais</h5>
                </div>
                <span class="badge bg-light text-muted border"><?= count($disciplinas) ?> ativa(s)</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Disciplina</th>
                                <th class="text-center">Artigos</th>
                                <th class="text-center">Questões</th>
                                <th class="text-end pe-3">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disciplinas as $disc): ?>
                                <?php $dId = (int) $disc['id']; ?>
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="fw-semibold text-dark"><?= Helper::e((string) $disc['nome']) ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 220px;">
                                            <?= Helper::e((string) ($disc['descricao'] ?? 'Sem descrição definida')) ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border"><?= (int) ($disc['total_artigos'] ?? 0) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><?= (int) ($disc['total_questoes'] ?? 0) ?></span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm btn-delete-confirm" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-id="<?= $dId ?>" 
                                                data-action="<?= Helper::url('/admin/disciplinas/excluir?id=' . $dId) ?>" 
                                                data-title="Disciplina: <?= Helper::e((string) $disc['nome']) ?>"
                                                title="Excluir Disciplina">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nova Categoria -->
<div class="modal fade" id="modalNovaCategoria" tabindex="-1" aria-labelledby="modalNovaCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalNovaCategoriaLabel">
                    <i class="bi bi-tag-fill"></i> Cadastrar Nova Categoria
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?= Helper::url('/admin/categorias/criar') ?>" method="POST">
                <?= Csrf::input() ?>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="cat_nome" class="form-label fw-semibold">Nome do Tópico / Categoria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cat_nome" name="nome" placeholder="Ex: Termodinâmica e Calorimetria" required>
                    </div>
                    <div class="mb-3">
                        <label for="cat_disciplina" class="form-label fw-semibold">Matéria / Disciplina Associada <span class="text-danger">*</span></label>
                        <select name="disciplina_id" id="cat_disciplina" class="form-select" required>
                            <option value="">-- Selecione a Matéria Existente --</option>
                            <?php foreach ($disciplinas as $disc): ?>
                                <option value="<?= (int) $disc['id'] ?>"><?= Helper::e((string) $disc['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Vincula este tópico temático diretamente à sua respectiva matéria.</div>
                    </div>
                    <div class="mb-2">
                        <label for="cat_slug" class="form-label fw-semibold">Slug (Identificador URL)</label>
                        <input type="text" class="form-control font-monospace" id="cat_slug" name="slug" placeholder="gerado-automaticamente">
                        <div class="form-text">Deixe em branco para gerar automaticamente a partir do nome.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Categoria</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Nova Disciplina -->
<div class="modal fade" id="modalNovaDisciplina" tabindex="-1" aria-labelledby="modalNovaDisciplinaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalNovaDisciplinaLabel">
                    <i class="bi bi-book-fill"></i> Cadastrar Nova Disciplina
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?= Helper::url('/admin/disciplinas/criar') ?>" method="POST">
                <?= Csrf::input() ?>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="disc_nome" class="form-label fw-semibold">Nome da Matéria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="disc_nome" name="nome" placeholder="Ex: Filosofia e Sociologia" required>
                    </div>
                    <div class="mb-2">
                        <label for="disc_desc" class="form-label fw-semibold">Descrição Curta</label>
                        <textarea class="form-control" id="disc_desc" name="descricao" rows="2" placeholder="Resumo do conteúdo programático cobrado nos vestibulares..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Disciplina</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require dirname(__DIR__, 2) . '/layout/footer.php';
