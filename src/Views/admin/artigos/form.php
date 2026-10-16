<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helper;

require dirname(__DIR__, 2) . '/layout/header.php';

$actionUrl = $isEdit 
    ? Helper::url('/admin/artigos/editar?id=' . (int) ($artigo['id'] ?? 0))
    : Helper::url('/admin/artigos/criar');
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="<?= Helper::url('/admin/artigos') ?>" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para Listagem
                </a>
                <h1 class="h3 fw-bold text-dark mb-0">
                    <?= $isEdit ? '<i class="bi bi-pencil-square text-primary me-2"></i>Editar Artigo Teórico' : '<i class="bi bi-plus-circle text-primary me-2"></i>Novo Artigo Teórico' ?>
                </h1>
            </div>
        </div>

        <div class="card card-custom shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="text-muted small text-uppercase fw-semibold">
                    <?= $isEdit ? 'Atualize as informações do conteúdo abaixo' : 'Preencha os campos abaixo para publicar um novo conteúdo teórico' ?>
                </span>
            </div>

            <div class="card-body p-4">
                <form action="<?= $actionUrl ?>" method="POST" novalidate>
                    <?= Csrf::input() ?>

                    <!-- Título do Artigo -->
                    <div class="mb-3">
                        <label for="titulo" class="form-label fw-semibold">
                            Título do Artigo <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg <?= isset($errors['titulo']) ? 'is-invalid' : '' ?>" 
                               id="titulo" 
                               name="titulo" 
                               value="<?= Helper::e((string) ($artigo['titulo'] ?? '')) ?>" 
                               placeholder="Ex: Introdução ao Estudo das Funções no ENEM" 
                               required>
                        <?php if (isset($errors['titulo'])): ?>
                            <div class="invalid-feedback d-block"><?= Helper::e($errors['titulo']) ?></div>
                        <?php else: ?>
                            <div class="form-text">Título claro e objetivo para identificação pelos vestibulandos.</div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Slug -->
                        <div class="col-md-6">
                            <label for="slug" class="form-label fw-semibold">
                                Slug (URL Amigável) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted font-monospace small">/artigos/</span>
                                <input type="text" 
                                       class="form-control font-monospace <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                                       id="slug" 
                                       name="slug" 
                                       value="<?= Helper::e((string) ($artigo['slug'] ?? '')) ?>" 
                                       placeholder="ex-introducao-funcoes" 
                                       required>
                            </div>
                            <?php if (isset($errors['slug'])): ?>
                                <div class="invalid-feedback d-block"><?= Helper::e($errors['slug']) ?></div>
                            <?php else: ?>
                                <div class="form-text">Identificador único na URL (gerado automaticamente a partir do título).</div>
                            <?php endif; ?>
                        </div>

                        <!-- Disciplina -->
                        <div class="col-md-6">
                            <label for="disciplina_id" class="form-label fw-semibold">
                                Disciplina Relacionada <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= isset($errors['disciplina_id']) ? 'is-invalid' : '' ?>" 
                                    id="disciplina_id" 
                                    name="disciplina_id" 
                                    required>
                                <option value="">-- Selecione uma disciplina --</option>
                                <?php foreach ($disciplinas as $disc): ?>
                                    <option value="<?= (int) $disc['id'] ?>" <?= ((int) ($artigo['disciplina_id'] ?? 0) === (int) $disc['id']) ? 'selected' : '' ?>>
                                        <?= Helper::e((string) $disc['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['disciplina_id'])): ?>
                                <div class="invalid-feedback d-block"><?= Helper::e($errors['disciplina_id']) ?></div>
                            <?php else: ?>
                                <div class="form-text">Área principal do conhecimento à qual o artigo pertence.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Categorias (N:N) com Checkboxes em Cards/Grid -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">
                            Categorias e Tópicos Associados (N:N)
                        </label>
                        <div class="p-3 bg-light rounded border">
                            <?php if (empty($categorias)): ?>
                                <p class="text-muted small mb-0">Nenhuma categoria cadastrada no banco.</p>
                            <?php else: ?>
                                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
                                    <?php foreach ($categorias as $cat): ?>
                                        <?php 
                                            $catId = (int) $cat['id'];
                                            $isChecked = in_array($catId, $categoriasSelecionadas, true);
                                        ?>
                                        <div class="col">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="categorias[]" 
                                                       value="<?= $catId ?>" 
                                                       id="cat_<?= $catId ?>"
                                                       <?= $isChecked ? 'checked' : '' ?>>
                                                <label class="form-check-label user-select-none" for="cat_<?= $catId ?>">
                                                    <?= Helper::e((string) $cat['nome']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-text">Marque uma ou mais categorias para indexação cruzada do conteúdo.</div>
                    </div>

                    <!-- Conteúdo do Artigo -->
                    <div class="mb-4">
                        <label for="conteudo" class="form-label fw-semibold">
                            Conteúdo Teórico Formatado <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control <?= isset($errors['conteudo']) ? 'is-invalid' : '' ?>" 
                                  id="conteudo" 
                                  name="conteudo" 
                                  rows="14" 
                                  placeholder="Digite ou cole aqui o conteúdo detalhado do artigo teórico (suporta Markdown e texto formatado)..." 
                                  required><?= Helper::e((string) ($artigo['conteudo'] ?? '')) ?></textarea>
                        <?php if (isset($errors['conteudo'])): ?>
                            <div class="invalid-feedback d-block"><?= Helper::e($errors['conteudo']) ?></div>
                        <?php else: ?>
                            <div class="form-text">Conteúdo completo para estudo do estudante (definições, fórmulas, exemplos e dicas).</div>
                        <?php endif; ?>
                    </div>

                    <!-- Barra de Ações -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="<?= Helper::url('/admin/artigos') ?>" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-check-lg fs-5"></i>
                            <?= $isEdit ? 'Salvar Alterações' : 'Publicar Artigo' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Geração Automática de Slug -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tituloInput = document.getElementById('titulo');
    const slugInput = document.getElementById('slug');
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;

    function slugify(text) {
        return text
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9 ]/g, '')
            .replace(/\s+/g, '-');
    }

    if (!isEdit && tituloInput && slugInput) {
        let slugManuallyEdited = false;

        slugInput.addEventListener('input', function () {
            slugManuallyEdited = this.value.trim().length > 0;
        });

        tituloInput.addEventListener('input', function () {
            if (!slugManuallyEdited) {
                slugInput.value = slugify(this.value);
            }
        });
    }
});
</script>

<?php
require dirname(__DIR__, 2) . '/layout/footer.php';
