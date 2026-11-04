<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helper;

require dirname(__DIR__, 2) . '/layout/header.php';

$actionUrl = $isEdit 
    ? Helper::url('/admin/artigos/editar?id=' . (int) ($artigo['id'] ?? 0))
    : Helper::url('/admin/artigos/criar');
$artigoId = (int) ($artigo['id'] ?? 0);
?>

<div class="row justify-content-center">
    <div class="col-xl-11 col-xxl-10">
        <!-- Topo da Página -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="<?= Helper::url('/admin/artigos') ?>" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para Listagem
                </a>
                <h1 class="h3 fw-bold text-dark mb-0">
                    <?= $isEdit ? '<i class="bi bi-pencil-square text-success me-2"></i>Editar Artigo Teórico' : '<i class="bi bi-plus-circle text-success me-2"></i>Novo Artigo Teórico' ?>
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 small" id="autosaveIndicator">
                    <i class="bi bi-cloud-check me-1"></i> Autosave Ativo
                </span>
            </div>
        </div>

        <!-- Alerta de Rascunho Recuperado (Oculto por padrão) -->
        <div class="alert alert-info border-info-subtle shadow-sm d-none align-items-center justify-content-between mb-4" id="draftAlert" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history fs-5 text-info"></i>
                <span>Encontramos um rascunho salvo automaticamente no seu navegador. Deseja restaurá-lo?</span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-info btn-sm text-white" id="btnRestoreDraft">
                    <i class="bi bi-arrow-counterclockwise"></i> Restaurar Rascunho
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnDiscardDraft">
                    Descartar
                </button>
            </div>
        </div>

        <div class="card card-custom shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <span class="text-muted small text-uppercase fw-semibold">
                    <?= $isEdit ? 'Atualize as informações do conteúdo abaixo com prévia em tempo real' : 'Preencha os campos abaixo com editor fluído e prévia simultânea' ?>
                </span>
            </div>

            <div class="card-body p-4">
                <form id="artigoForm" action="<?= $actionUrl ?>" method="POST" novalidate>
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
                        </div>
                    </div>

                    <!-- Categorias (N:N) -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">
                            Categorias e Tópicos Associados (N:N)
                        </label>
                        <div class="p-3 bg-light rounded border" style="max-height: 140px; overflow-y: auto;">
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
                                                <label class="form-check-label user-select-none small" for="cat_<?= $catId ?>">
                                                    <?= Helper::e((string) $cat['nome']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-text">Marque os tópicos para indexação e busca refinada.</div>
                    </div>

                    <!-- Seção Fluída de Conteúdo com Barra de Ferramentas e Live Preview -->
                    <div class="mb-4">
                        <!-- Toolbar Superior -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-2 bg-light border border-bottom-0 rounded-top">
                            <!-- Botões de Formatação Rápida -->
                            <div class="d-flex flex-wrap align-items-center gap-1" id="formatToolbar">
                                <button type="button" class="btn btn-sm btn-white border bg-white" data-action="h2" title="Título H2 (## )"><strong>H2</strong></button>
                                <button type="button" class="btn btn-sm btn-white border bg-white" data-action="h3" title="Subtítulo H3 (### )"><strong>H3</strong></button>
                                <div class="vr mx-1"></div>
                                <button type="button" class="btn btn-sm btn-white border bg-white" data-action="bold" title="Negrito (**texto**)"><i class="bi bi-type-bold"></i></button>
                                <button type="button" class="btn btn-sm btn-white border bg-white" data-action="italic" title="Itálico (*texto*)"><i class="bi bi-type-italic"></i></button>
                                <button type="button" class="btn btn-sm btn-white border bg-white" data-action="list" title="Lista (- item)"><i class="bi bi-list-ul"></i></button>
                                <button type="button" class="btn btn-sm btn-white border bg-white" data-action="quote" title="Citação (> texto)"><i class="bi bi-quote"></i></button>
                                <div class="vr mx-1"></div>
                                <button type="button" class="btn btn-sm btn-white border bg-white text-success fw-semibold" data-action="dica" title="Bloco Dica ENEM">
                                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> Dica ENEM
                                </button>
                                <button type="button" class="btn btn-sm btn-white border bg-white text-warning fw-semibold" data-action="nota" title="Bloco de Nota">
                                    <i class="bi bi-exclamation-circle-fill text-warning me-1"></i> Nota
                                </button>
                                <button type="button" class="btn btn-sm btn-white border bg-white" data-action="code" title="Fórmula ou Código">
                                    <i class="bi bi-code-slash"></i>
                                </button>
                            </div>

                            <!-- Alternador de Modos de Visualização & Estatísticas -->
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-white text-secondary border px-2 py-1 small" id="liveWordCounter">
                                    <i class="bi bi-fonts me-1"></i> 0 palavras &bull; ~1 min
                                </span>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Modo de Visualização">
                                    <button type="button" class="btn btn-outline-secondary active" id="btnModeSplit" title="Editor e Prévia Lado a Lado">
                                        <i class="bi bi-layout-split"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="btnModeEditor" title="Apenas Editor">
                                        <i class="bi bi-textarea-t"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="btnModePreview" title="Apenas Prévia">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Área de Edição e Prévia Simultânea (Split View) -->
                        <div class="row g-0 border rounded-bottom" id="editorSplitArea" style="min-height: 480px;">
                            <!-- Coluna do Editor -->
                            <div class="col-lg-6 border-end" id="colEditor">
                                <textarea class="form-control border-0 rounded-0 font-monospace p-3 <?= isset($errors['conteudo']) ? 'is-invalid' : '' ?>" 
                                          id="conteudo" 
                                          name="conteudo" 
                                          rows="18" 
                                          placeholder="Digite ou cole aqui o conteúdo estruturado do artigo..." 
                                          style="resize: vertical; min-height: 480px;"
                                          required><?= Helper::e((string) ($artigo['conteudo'] ?? '')) ?></textarea>
                            </div>

                            <!-- Coluna do Live Preview -->
                            <div class="col-lg-6 bg-white" id="colPreview">
                                <div class="p-3 border-bottom bg-light d-flex align-items-center justify-content-between">
                                    <span class="small fw-bold text-uppercase text-muted">
                                        <i class="bi bi-eye-fill text-success me-1"></i> Prévia em Tempo Real (Visualização do Aluno)
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary small">Renderização Instantânea</span>
                                </div>
                                <div class="p-4 overflow-auto article-body" id="livePreviewBox" style="max-height: 520px; min-height: 440px;">
                                    <p class="text-muted fst-italic">Comece a digitar no editor para visualizar a formatação em tempo real...</p>
                                </div>
                            </div>
                        </div>
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

<!-- Motor Vanilla JS: Live Preview, Autosave e Toolbar -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    const artigoId = <?= $artigoId ?>;
    const storageKey = 'vestmais_artigo_draft_' + artigoId;

    const form = document.getElementById('artigoForm');
    const tituloInput = document.getElementById('titulo');
    const slugInput = document.getElementById('slug');
    const conteudoArea = document.getElementById('conteudo');
    const previewBox = document.getElementById('livePreviewBox');
    const wordCounter = document.getElementById('liveWordCounter');
    const draftAlert = document.getElementById('draftAlert');
    const btnRestoreDraft = document.getElementById('btnRestoreDraft');
    const btnDiscardDraft = document.getElementById('btnDiscardDraft');

    const colEditor = document.getElementById('colEditor');
    const colPreview = document.getElementById('colPreview');
    const btnModeSplit = document.getElementById('btnModeSplit');
    const btnModeEditor = document.getElementById('btnModeEditor');
    const btnModePreview = document.getElementById('btnModePreview');

    // 1. Slugify Automático
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

    // 2. Parser Leve de Markdown / Destaques Didáticos Vest+
    function renderMarkdown(rawText) {
        if (!rawText || rawText.trim() === '') {
            return '<p class="text-muted fst-italic">Comece a digitar no editor para visualizar a formatação em tempo real...</p>';
        }

        let html = rawText;

        // Escape básico de tags HTML perigosas para visualização
        html = html
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Restaura tags permitidas de formatação
        // Dica ENEM
        html = html.replace(/\[DICA ENEM\]([\s\S]*?)\[\/DICA ENEM\]/gi, function (_, content) {
            return `<div class="callout-dica-enem"><div class="callout-title"><i class="bi bi-lightbulb-fill"></i> Dica para o ENEM / Vestibulares</div><div>${content.trim()}</div></div>`;
        });

        // Nota de Atenção
        html = html.replace(/\[NOTA\]([\s\S]*?)\[\/NOTA\]/gi, function (_, content) {
            return `<div class="callout-nota"><div class="callout-title"><i class="bi bi-exclamation-triangle-fill"></i> Ponto de Atenção</div><div>${content.trim()}</div></div>`;
        });

        // Cabeçalhos Markdown
        html = html.replace(/^### (.*$)/gim, '<h4 class="fw-bold mt-3 mb-2 text-dark">$1</h4>');
        html = html.replace(/^## (.*$)/gim, '<h3 class="fw-bold mt-4 mb-2 text-dark border-bottom pb-1">$1</h3>');
        html = html.replace(/^# (.*$)/gim, '<h2 class="fw-bold mt-4 mb-3 text-dark">$1</h2>');

        // Formatação em linha: Negrito, Itálico e Código
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        html = html.replace(/`([^`]+)`/g, '<code class="bg-light px-1 py-0.5 rounded text-dark font-monospace">$1</code>');

        // Citações
        html = html.replace(/^>\s?(.*$)/gim, '<blockquote class="border-start border-4 border-success ps-3 py-1 my-2 bg-light fst-italic text-secondary">$1</blockquote>');

        // Listas simples
        html = html.replace(/^- (.*$)/gim, '<li class="ms-3">$1</li>');

        // Parágrafos
        html = html.replace(/\n\n/g, '</p><p class="mb-3">');
        html = '<p class="mb-3">' + html + '</p>';

        return html;
    }

    // 3. Atualização da Prévia & Estatísticas
    function updatePreview() {
        const text = conteudoArea.value;
        previewBox.innerHTML = renderMarkdown(text);

        // Contagem de palavras e tempo estimado
        const words = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
        const readTime = Math.max(1, Math.ceil(words / 180));
        wordCounter.innerHTML = `<i class="bi bi-fonts me-1"></i> ${words} palavras &bull; ~${readTime} min`;

        // Autosave
        if (!isEdit && text.trim().length > 10) {
            localStorage.setItem(storageKey, JSON.stringify({
                titulo: tituloInput.value,
                slug: slugInput.value,
                conteudo: text,
                salvoEm: new Date().toLocaleTimeString()
            }));
        }
    }

    conteudoArea.addEventListener('input', updatePreview);
    tituloInput.addEventListener('input', updatePreview);

    // Inicializa a primeira renderização
    updatePreview();

    // 4. Verificação de Rascunho no LocalStorage
    if (!isEdit) {
        const savedDraft = localStorage.getItem(storageKey);
        if (savedDraft) {
            try {
                const draft = JSON.parse(savedDraft);
                if (draft.conteudo && draft.conteudo !== conteudoArea.value) {
                    draftAlert.classList.remove('d-none');
                    draftAlert.classList.add('d-flex');

                    btnRestoreDraft.addEventListener('click', function () {
                        if (draft.titulo) tituloInput.value = draft.titulo;
                        if (draft.slug) slugInput.value = draft.slug;
                        conteudoArea.value = draft.conteudo;
                        updatePreview();
                        draftAlert.classList.add('d-none');
                    });

                    btnDiscardDraft.addEventListener('click', function () {
                        localStorage.removeItem(storageKey);
                        draftAlert.classList.add('d-none');
                    });
                }
            } catch (e) {}
        }
    }

    // Limpa rascunho ao submeter o formulário
    form.addEventListener('submit', function () {
        localStorage.removeItem(storageKey);
    });

    // 5. Botões da Barra de Ferramentas de Formatação
    document.querySelectorAll('#formatToolbar button').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const action = this.getAttribute('data-action');
            const start = conteudoArea.selectionStart;
            const end = conteudoArea.selectionEnd;
            const selectedText = conteudoArea.value.substring(start, end);
            let replacement = '';

            switch (action) {
                case 'h2':
                    replacement = `\n## ${selectedText || 'Título da Seção'}\n`;
                    break;
                case 'h3':
                    replacement = `\n### ${selectedText || 'Subtítulo do Tópico'}\n`;
                    break;
                case 'bold':
                    replacement = `**${selectedText || 'texto em negrito'}**`;
                    break;
                case 'italic':
                    replacement = `*${selectedText || 'texto em itálico'}*`;
                    break;
                case 'list':
                    replacement = `\n- ${selectedText || 'Primeiro item'}\n- Segundo item\n`;
                    break;
                case 'quote':
                    replacement = `\n> ${selectedText || 'Citação ou conceito chave...'}\n`;
                    break;
                case 'dica':
                    replacement = `\n[DICA ENEM]\n${selectedText || 'Dica prática: preste atenção neste detalhe nas questões do ENEM!'}\n[/DICA ENEM]\n`;
                    break;
                case 'nota':
                    replacement = `\n[NOTA]\n${selectedText || 'Nota importante sobre pré-requisitos para este assunto.'}\n[/NOTA]\n`;
                    break;
                case 'code':
                    replacement = `\`${selectedText || 'f(x) = ax + b'}\``;
                    break;
            }

            conteudoArea.setRangeText(replacement, start, end, 'end');
            conteudoArea.focus();
            updatePreview();
        });
    });

    // 6. Alternador de Modos de Visualização (Split, Editor, Preview)
    btnModeSplit.addEventListener('click', function () {
        colEditor.className = 'col-lg-6 border-end';
        colPreview.className = 'col-lg-6 bg-white d-block';
        btnModeSplit.classList.add('active');
        btnModeEditor.classList.remove('active');
        btnModePreview.classList.remove('active');
    });

    btnModeEditor.addEventListener('click', function () {
        colEditor.className = 'col-12';
        colPreview.className = 'd-none';
        btnModeSplit.classList.remove('active');
        btnModeEditor.classList.add('active');
        btnModePreview.classList.remove('active');
    });

    btnModePreview.addEventListener('click', function () {
        colEditor.className = 'd-none';
        colPreview.className = 'col-12 bg-white d-block';
        btnModeSplit.classList.remove('active');
        btnModeEditor.classList.remove('active');
        btnModePreview.classList.add('active');
    });
});
</script>

<?php
require dirname(__DIR__, 2) . '/layout/footer.php';
