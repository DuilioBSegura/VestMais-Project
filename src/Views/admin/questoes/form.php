<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helper;

require dirname(__DIR__, 2) . '/layout/header.php';

$actionUrl = $isEdit 
    ? Helper::url('/admin/questoes/editar?id=' . (int) ($questao['id'] ?? 0))
    : Helper::url('/admin/questoes/criar');

$questaoId = (int) ($questao['id'] ?? 0);
$respostaCorreta = strtoupper((string) ($questao['resposta_correta'] ?? 'A'));
?>

<div class="row justify-content-center">
    <div class="col-xl-11 col-xxl-10">
        <!-- Topo da Página -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <a href="<?= Helper::url('/admin/questoes') ?>" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para o Banco de Questões
                </a>
                <h1 class="h3 fw-bold text-dark mb-0">
                    <?= $isEdit ? '<i class="bi bi-pencil-square text-success me-2"></i>Editar Questão #' . $questaoId : '<i class="bi bi-plus-circle text-success me-2"></i>Nova Questão de Múltipla Escolha' ?>
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 small" id="autosaveIndicator">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Renderizador Obsidian Ativo
                </span>

                <!-- Alternador de Modos de Visualização -->
                <div class="btn-group btn-group-sm" role="group" aria-label="Modo de Visualização">
                    <button type="button" class="btn btn-outline-secondary active" id="btnModeSplit" title="Editor e Simulador Lado a Lado">
                        <i class="bi bi-layout-split"></i> Dividido
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnModeForm" title="Apenas Formulário">
                        <i class="bi bi-textarea-t"></i> Editor
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnModeSim" title="Apenas Simulador do Aluno">
                        <i class="bi bi-eye"></i> Simulador
                    </button>
                </div>
            </div>
        </div>

        <!-- Alerta de Rascunho Recuperado -->
        <div class="alert alert-info border-info-subtle shadow-sm d-none align-items-center justify-content-between mb-4" id="draftAlertQuestao" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history fs-5 text-info"></i>
                <span>Encontramos um rascunho salvo desta questão no seu navegador. Deseja restaurá-lo?</span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-info btn-sm text-white" id="btnRestoreQuestao">
                    <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnDiscardQuestao">
                    Descartar
                </button>
            </div>
        </div>

        <div class="row g-4" id="splitContainer">
            <!-- Formulário de Cadastro (Coluna Principal) -->
            <div class="col-lg-7 col-xl-7" id="colForm">
                <div class="card card-custom shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <span class="text-muted small text-uppercase fw-semibold">
                            <?= $isEdit ? 'Atualize as informações da questão, alternativas e gabarito comentado em Markdown' : 'Cadastre o enunciado, as 5 alternativas e a resolução comentada com suporte total a Obsidian Markdown e LaTeX' ?>
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <form id="questaoForm" action="<?= $actionUrl ?>" method="POST" novalidate>
                            <?= Csrf::input() ?>

                            <!-- Disciplina e Categorias -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="disciplina_id" class="form-label fw-semibold">
                                        Disciplina <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select <?= isset($errors['disciplina_id']) ? 'is-invalid' : '' ?>" 
                                            id="disciplina_id" 
                                            name="disciplina_id" 
                                            required>
                                        <option value="">-- Selecione a disciplina --</option>
                                        <?php foreach ($disciplinas as $disc): ?>
                                            <option value="<?= (int) $disc['id'] ?>" <?= ((int) ($questao['disciplina_id'] ?? 0) === (int) $disc['id']) ? 'selected' : '' ?>>
                                                <?= Helper::e((string) $disc['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['disciplina_id'])): ?>
                                        <div class="invalid-feedback d-block"><?= Helper::e($errors['disciplina_id']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold d-block">
                                        Categorias / Tópicos (N:N)
                                    </label>
                                    <div class="p-2 bg-light rounded border" style="max-height: 140px; overflow-y: auto;">
                                        <?php if (empty($categorias)): ?>
                                            <p class="text-muted small mb-0 p-1">Nenhuma categoria disponível.</p>
                                        <?php else: ?>
                                            <div class="row row-cols-1 g-1">
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
                                                                   id="cat_q_<?= $catId ?>"
                                                                   <?= $isChecked ? 'checked' : '' ?>>
                                                            <label class="form-check-label small user-select-none" for="cat_q_<?= $catId ?>">
                                                                <?= Helper::e((string) $cat['nome']) ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Enunciado da Questão com Toolbar Obsidian -->
                            <div class="mb-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                                    <label for="enunciado" class="form-label fw-bold mb-0 text-dark">
                                        Enunciado da Questão <span class="text-danger">*</span>
                                    </label>
                                    <span class="badge bg-light text-muted border small">
                                        <i class="bi bi-markdown-fill text-success me-1"></i> Markdown + KaTeX ($)
                                    </span>
                                </div>

                                <!-- Toolbar Obsidian para o Enunciado -->
                                <div class="d-flex flex-wrap align-items-center gap-1 p-2 bg-light border border-bottom-0 rounded-top" id="toolbarEnunciado">
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="enunciado" data-action="h3" title="Subtítulo H3"><strong>H3</strong></button>
                                    <div class="vr mx-1"></div>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="enunciado" data-action="bold" title="Negrito (**texto**)"><i class="bi bi-type-bold"></i></button>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="enunciado" data-action="italic" title="Itálico (*texto*)"><i class="bi bi-type-italic"></i></button>
                                    <button type="button" class="btn btn-sm btn-warning bg-opacity-25 border text-dark fw-bold" data-target="enunciado" data-action="highlight" title="Destaque Obsidian (==texto==)">
                                        <i class="bi bi-highlighter"></i> ==Marca==
                                    </button>
                                    <div class="vr mx-1"></div>
                                    <button type="button" class="btn btn-sm btn-white border bg-white text-primary" data-target="enunciado" data-action="math-inline" title="Fórmula Matemática em Linha ($x$)">
                                        <strong>$f(x)$</strong>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-white border bg-white text-primary" data-target="enunciado" data-action="math-block" title="Bloco Matemático Centralizado ($$)">
                                        <strong>$$ \Sigma $$</strong>
                                    </button>
                                    <div class="vr mx-1"></div>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="enunciado" data-action="list" title="Lista com Marcadores (- )"><i class="bi bi-list-ul"></i></button>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="enunciado" data-action="quote" title="Citação ou Texto de Apoio (> texto)"><i class="bi bi-quote"></i></button>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="enunciado" data-action="table" title="Tabela"><i class="bi bi-table"></i></button>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="enunciado" data-action="code" title="Código"><i class="bi bi-code-slash"></i></button>
                                    <div class="vr mx-1"></div>
                                    <!-- Dropdown de Callouts para Enunciado -->
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-sm btn-white border bg-white text-success fw-semibold dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Inserir Caixa de Destaque Obsidian">
                                            <i class="bi bi-card-text text-success me-1"></i> Callout
                                        </button>
                                        <ul class="dropdown-menu shadow-sm">
                                            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-target="enunciado" data-action="callout-tip"><i class="bi bi-lightbulb-fill text-success"></i> Contexto / Dica ([!tip])</button></li>
                                            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-target="enunciado" data-action="callout-important"><i class="bi bi-exclamation-circle-fill text-primary"></i> Ponto Importante ([!important])</button></li>
                                            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-target="enunciado" data-action="callout-warning"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Atenção ([!warning])</button></li>
                                        </ul>
                                    </div>
                                </div>
                                <textarea class="form-control rounded-bottom font-monospace <?= isset($errors['enunciado']) ? 'is-invalid' : '' ?>" 
                                          id="enunciado" 
                                          name="enunciado" 
                                          rows="6" 
                                          placeholder="Digite o texto-base e problema da questão em Markdown... Use $f(x)$ para fórmulas matemáticas, ==texto== para destaque e > para citações..." 
                                          style="font-size: 0.95rem; line-height: 1.6;"
                                          required><?= Helper::e((string) ($questao['enunciado'] ?? '')) ?></textarea>
                                <?php if (isset($errors['enunciado'])): ?>
                                    <div class="invalid-feedback d-block"><?= Helper::e($errors['enunciado']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Alternativas A a E com Seleção Fluída de Gabarito & Suporte a Markdown -->
                            <div class="mb-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                                    <label class="form-label fw-bold mb-0 text-dark">
                                        Alternativas & Gabarito Oficial <span class="text-danger">*</span>
                                    </label>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                        <i class="bi bi-hand-index-thumb me-1"></i> Clique em "Definir como Gabarito"
                                    </span>
                                </div>

                                <?php 
                                $letras = ['A', 'B', 'C', 'D', 'E'];
                                foreach ($letras as $letra):
                                    $field = 'alternativa_' . strtolower($letra);
                                    $isSelected = ($respostaCorreta === $letra);
                                ?>
                                    <div class="card card-custom p-3 mb-3 alternative-interactive-box <?= $isSelected ? 'border-success bg-success-subtle' : '' ?>" id="box_alt_<?= $letra ?>">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <input class="form-check-input radio-gabarito" 
                                                       type="radio" 
                                                       name="resposta_correta" 
                                                       id="resp_<?= $letra ?>" 
                                                       value="<?= $letra ?>" 
                                                       data-letra="<?= $letra ?>"
                                                       <?= $isSelected ? 'checked' : '' ?> 
                                                       required>
                                                <label class="form-check-label fw-bold fs-6 text-dark cursor-pointer user-select-none" for="resp_<?= $letra ?>">
                                                    Alternativa <?= $letra ?>
                                                </label>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <!-- Toolbar Rápida de Formatação da Alternativa -->
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary py-0 px-2" data-target="<?= $field ?>" data-action="bold" title="Negrito (**texto**)"><strong>B</strong></button>
                                                    <button type="button" class="btn btn-outline-secondary py-0 px-2" data-target="<?= $field ?>" data-action="italic" title="Itálico (*texto*)"><em>I</em></button>
                                                    <button type="button" class="btn btn-outline-secondary py-0 px-2 text-primary" data-target="<?= $field ?>" data-action="math-inline" title="Fórmula Matemática ($x$)"><strong>$x$</strong></button>
                                                    <button type="button" class="btn btn-outline-secondary py-0 px-2" data-target="<?= $field ?>" data-action="code" title="Código (`` `texto` ``)"><i class="bi bi-code"></i></button>
                                                </div>

                                                <button type="button" class="btn btn-sm <?= $isSelected ? 'btn-success text-white' : 'btn-outline-secondary' ?> btn-mark-gabarito" data-target-radio="resp_<?= $letra ?>">
                                                    <i class="bi <?= $isSelected ? 'bi-check-circle-fill' : 'bi-circle' ?> me-1"></i>
                                                    <?= $isSelected ? 'Gabarito Oficial' : 'Definir como Gabarito' ?>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <textarea class="form-control input-alt-text font-monospace <?= isset($errors[$field]) ? 'is-invalid' : '' ?>" 
                                                      name="<?= $field ?>" 
                                                      id="<?= $field ?>" 
                                                      data-letra="<?= $letra ?>"
                                                      rows="2" 
                                                      placeholder="Texto da alternativa <?= $letra ?> (suporta Markdown e fórmulas com $)..." 
                                                      style="font-size: 0.92rem; line-height: 1.5;"
                                                      required><?= Helper::e((string) ($questao[$field] ?? '')) ?></textarea>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Explicação / Resolução Comentada com Toolbar Obsidian -->
                            <div class="mb-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                                    <label for="explicacao" class="form-label fw-bold mb-0 text-dark">
                                        Resolução Comentada / Explicação do Professor
                                    </label>
                                    <span class="badge bg-light text-muted border small">
                                        <i class="bi bi-markdown-fill text-success me-1"></i> Markdown + Callouts + LaTeX
                                    </span>
                                </div>

                                <!-- Toolbar Obsidian para a Explicação -->
                                <div class="d-flex flex-wrap align-items-center gap-1 p-2 bg-light border border-bottom-0 rounded-top" id="toolbarExplicacao">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-sm btn-white border bg-white text-success fw-semibold dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Inserir Caixa de Destaque Obsidian">
                                            <i class="bi bi-card-text text-success me-1"></i> Callouts Obsidian
                                        </button>
                                        <ul class="dropdown-menu shadow-sm">
                                            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-target="explicacao" data-action="callout-tip"><i class="bi bi-lightbulb-fill text-success"></i> Dica ENEM & Vestibulares ([!tip])</button></li>
                                            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-target="explicacao" data-action="callout-warning"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Atenção / Pegadinha ([!warning])</button></li>
                                            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-target="explicacao" data-action="callout-important"><i class="bi bi-exclamation-circle-fill text-primary"></i> Ponto Fundamental ([!important])</button></li>
                                            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-target="explicacao" data-action="callout-example"><i class="bi bi-journal-bookmark-fill text-info"></i> Passo a Passo da Resolução ([!example])</button></li>
                                            <li><button class="dropdown-item d-flex align-items-center gap-2" type="button" data-target="explicacao" data-action="callout-success"><i class="bi bi-check-circle-fill text-success"></i> Conclusão / Gabarito ([!check])</button></li>
                                        </ul>
                                    </div>
                                    <div class="vr mx-1"></div>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="explicacao" data-action="bold" title="Negrito (**texto**)"><i class="bi bi-type-bold"></i></button>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="explicacao" data-action="italic" title="Itálico (*texto*)"><i class="bi bi-type-italic"></i></button>
                                    <button type="button" class="btn btn-sm btn-warning bg-opacity-25 border text-dark fw-bold" data-target="explicacao" data-action="highlight" title="Destaque Obsidian (==texto==)">
                                        <i class="bi bi-highlighter"></i> ==Marca==
                                    </button>
                                    <div class="vr mx-1"></div>
                                    <button type="button" class="btn btn-sm btn-white border bg-white text-primary" data-target="explicacao" data-action="math-inline" title="Fórmula Matemática em Linha ($x$)">
                                        <strong>$f(x)$</strong>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-white border bg-white text-primary" data-target="explicacao" data-action="math-block" title="Bloco Matemático Centralizado ($$)">
                                        <strong>$$ \Sigma $$</strong>
                                    </button>
                                    <div class="vr mx-1"></div>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="explicacao" data-action="list" title="Lista com Marcadores (- )"><i class="bi bi-list-ul"></i></button>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="explicacao" data-action="code" title="Código"><i class="bi bi-code-slash"></i></button>
                                    <button type="button" class="btn btn-sm btn-white border bg-white" data-target="explicacao" data-action="quote" title="Citação"><i class="bi bi-quote"></i></button>
                                </div>
                                <textarea class="form-control rounded-bottom font-monospace <?= isset($errors['explicacao']) ? 'is-invalid' : '' ?>" 
                                          id="explicacao" 
                                          name="explicacao" 
                                          rows="5" 
                                          placeholder="Passo a passo da resolução, justificativa das incorretas e raciocínio oficial... Use callouts como > [!tip] e fórmulas LaTeX com $..." 
                                          style="font-size: 0.95rem; line-height: 1.6;"><?= Helper::e((string) ($questao['explicacao'] ?? '')) ?></textarea>
                                <div class="form-text">Exibido ao estudante após a tentativa de resposta com renderização Obsidian completa.</div>
                            </div>

                            <!-- Barra de Ações -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="<?= Helper::url('/admin/questoes') ?>" class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                                    <i class="bi bi-check-lg fs-5"></i>
                                    <?= $isEdit ? 'Atualizar Questão' : 'Cadastrar Questão' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Simulador / Prévia Interativa do Aluno (Coluna Lateral) -->
            <div class="col-lg-5 col-xl-5" id="colSimulator">
                <div class="card card-custom shadow-sm sticky-top" style="top: 85px;">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-phone text-success fs-5"></i>
                            <h5 class="mb-0 fw-bold text-dark">Simulador do Aluno</h5>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small">
                            <i class="bi bi-eye-fill me-1"></i> Prévia Obsidian Ao Vivo
                        </span>
                    </div>

                    <div class="card-body p-4" style="max-height: calc(100vh - 120px); overflow-y: auto;">
                        <div class="small text-muted mb-2 text-uppercase fw-semibold" id="simDisciplina">
                            <i class="bi bi-book me-1"></i> Selecione uma Disciplina
                        </div>

                        <!-- Enunciado Simulado (Renderizado em Obsidian) -->
                        <div class="p-3 bg-light rounded-3 border article-content text-break mb-4" id="simEnunciado" style="min-height: 60px;">
                            <span class="text-muted fst-italic">O texto do enunciado aparecerá aqui enquanto você digita...</span>
                        </div>

                        <!-- Alternativas Simuladas (Renderizadas em Obsidian) -->
                        <div class="d-flex flex-column gap-2 mb-4">
                            <?php foreach ($letras as $letra): ?>
                                <div class="card-alternativa sim-card-alt d-flex align-items-center gap-2" id="sim_card_<?= $letra ?>" style="cursor: default;">
                                    <span class="badge bg-light text-dark border badge-alternativa fw-bold px-2 py-1" id="sim_badge_<?= $letra ?>">
                                        <?= $letra ?>
                                    </span>
                                    <div class="small text-dark flex-grow-1 text-break article-content" id="sim_text_<?= $letra ?>">
                                        <span class="text-muted fst-italic">Alternativa <?= $letra ?>...</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Feedback Simulado (Renderizado em Obsidian) -->
                        <div class="alert alert-success border-success-subtle shadow-sm p-3 mb-0" id="simFeedbackBox">
                            <div class="d-flex align-items-center gap-2 fw-bold text-success mb-2">
                                <i class="bi bi-check-circle-fill"></i> Gabarito Oficial: Letra <span id="simGabaritoLetra"><?= $respostaCorreta ?></span>
                            </div>
                            <div class="small text-secondary text-break article-content" id="simExplicacao">
                                <?= !empty($questao['explicacao']) ? Helper::e((string) $questao['explicacao']) : 'A resolução comentada digitada pelo professor aparecerá aqui com suporte total a Obsidian e KaTeX.' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KaTeX e Marked.js para Renderização Estilo Obsidian -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.2/marked.min.js"></script>
<script src="<?= Helper::url('/js/obsidian-markdown.js?v=1.1.5') ?>"></script>

<!-- Motor Vanilla JS: Simulador em Tempo Real e Gabarito Fluído -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    const questaoId = <?= $questaoId ?>;
    const storageKey = 'vestmais_questao_draft_' + questaoId;

    const form = document.getElementById('questaoForm');
    const enunciadoArea = document.getElementById('enunciado');
    const explicacaoArea = document.getElementById('explicacao');
    const disciplinaSelect = document.getElementById('disciplina_id');
    const radioButtons = document.querySelectorAll('.radio-gabarito');
    const altInputs = document.querySelectorAll('.input-alt-text');
    const markButtons = document.querySelectorAll('.btn-mark-gabarito');

    const colForm = document.getElementById('colForm');
    const colSimulator = document.getElementById('colSimulator');
    const btnModeSplit = document.getElementById('btnModeSplit');
    const btnModeForm = document.getElementById('btnModeForm');
    const btnModeSim = document.getElementById('btnModeSim');

    const simEnunciado = document.getElementById('simEnunciado');
    const simExplicacao = document.getElementById('simExplicacao');
    const simDisciplina = document.getElementById('simDisciplina');
    const simGabaritoLetra = document.getElementById('simGabaritoLetra');

    // 1. Atualização do Simulador com Obsidian Markdown & KaTeX
    function updateSimulator() {
        // Enunciado
        const enun = enunciadoArea.value.trim();
        if (!enun) {
            simEnunciado.innerHTML = '<span class="text-muted fst-italic">O texto do enunciado aparecerá aqui enquanto você digita...</span>';
        } else if (typeof ObsidianMarkdown !== 'undefined') {
            ObsidianMarkdown.render(enun, simEnunciado);
        } else {
            simEnunciado.textContent = enun;
        }

        // Disciplina
        const selectedDisc = disciplinaSelect.options[disciplinaSelect.selectedIndex]?.text;
        simDisciplina.innerHTML = `<i class="bi bi-book me-1"></i> ${disciplinaSelect.value ? selectedDisc : 'Selecione uma Disciplina'}`;

        // Alternativas
        altInputs.forEach(function (input) {
            const letra = input.getAttribute('data-letra');
            const simText = document.getElementById('sim_text_' + letra);
            const val = input.value.trim();
            if (simText) {
                if (!val) {
                    simText.innerHTML = `<span class="text-muted fst-italic">Alternativa ${letra}...</span>`;
                } else if (typeof ObsidianMarkdown !== 'undefined') {
                    ObsidianMarkdown.render(val, simText);
                } else {
                    simText.textContent = val;
                }
            }
        });

        // Gabarito Selecionado
        const checkedRadio = document.querySelector('.radio-gabarito:checked');
        const gabaritoLetra = checkedRadio ? checkedRadio.value : 'A';
        simGabaritoLetra.textContent = gabaritoLetra;

        // Explicação
        const expl = explicacaoArea.value.trim();
        if (!expl) {
            simExplicacao.innerHTML = '<span class="text-muted fst-italic">A resolução comentada digitada pelo professor aparecerá aqui com suporte total a Obsidian e KaTeX.</span>';
        } else if (typeof ObsidianMarkdown !== 'undefined') {
            ObsidianMarkdown.render(expl, simExplicacao);
        } else {
            simExplicacao.textContent = expl;
        }

        // Destaque visual no formulário e no simulador
        ['A', 'B', 'C', 'D', 'E'].forEach(function (letra) {
            const formBox = document.getElementById('box_alt_' + letra);
            const simCard = document.getElementById('sim_card_' + letra);
            const simBadge = document.getElementById('sim_badge_' + letra);
            const markBtn = formBox ? formBox.querySelector('.btn-mark-gabarito') : null;

            if (letra === gabaritoLetra) {
                if (formBox) {
                    formBox.classList.add('border-success', 'bg-success-subtle');
                }
                if (markBtn) {
                    markBtn.className = 'btn btn-sm btn-success text-white btn-mark-gabarito';
                    markBtn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Gabarito Oficial';
                }
                if (simCard) {
                    simCard.className = 'card-alternativa sim-card-alt d-flex align-items-center gap-2 border-success bg-success-subtle';
                }
                if (simBadge) {
                    simBadge.className = 'badge bg-success text-white badge-alternativa fw-bold px-2 py-1';
                }
            } else {
                if (formBox) {
                    formBox.classList.remove('border-success', 'bg-success-subtle');
                }
                if (markBtn) {
                    markBtn.className = 'btn btn-sm btn-outline-secondary btn-mark-gabarito';
                    markBtn.innerHTML = '<i class="bi bi-circle me-1"></i> Definir como Gabarito';
                }
                if (simCard) {
                    simCard.className = 'card-alternativa sim-card-alt d-flex align-items-center gap-2';
                }
                if (simBadge) {
                    simBadge.className = 'badge bg-light text-dark border badge-alternativa fw-bold px-2 py-1';
                }
            }
        });

        // Autosave LocalStorage
        if (!isEdit && enun.length > 10) {
            const draft = {
                disciplina_id: disciplinaSelect.value,
                enunciado: enun,
                resposta_correta: gabaritoLetra,
                explicacao: expl,
                alternativas: {}
            };
            altInputs.forEach(i => draft.alternativas[i.getAttribute('data-letra')] = i.value);
            localStorage.setItem(storageKey, JSON.stringify(draft));
        }
    }

    // Debounce na digitação para performance ideal com KaTeX
    let debounceTimer = null;
    function debouncedUpdate() {
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(updateSimulator, 60);
    }

    // 2. Ouvintes de Eventos
    enunciadoArea.addEventListener('input', debouncedUpdate);
    explicacaoArea.addEventListener('input', debouncedUpdate);
    disciplinaSelect.addEventListener('change', updateSimulator);
    altInputs.forEach(input => input.addEventListener('input', debouncedUpdate));
    radioButtons.forEach(radio => radio.addEventListener('change', updateSimulator));

    // Botões de definição de gabarito em 1 clique
    markButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const radioId = this.getAttribute('data-target-radio');
            const radio = document.getElementById(radioId);
            if (radio) {
                radio.checked = true;
                updateSimulator();
            }
        });
    });

    // 3. Sistema Unificado de Inserção de Formatação Obsidian / LaTeX
    function applyFormatTo(targetTextarea, action) {
        if (!targetTextarea) return;
        const start = targetTextarea.selectionStart;
        const end = targetTextarea.selectionEnd;
        const selected = targetTextarea.value.substring(start, end);
        let rep = '';

        switch (action) {
            case 'h3':
                rep = `\n### ${selected || 'Título'}\n`;
                break;
            case 'bold':
                rep = `**${selected || 'texto em negrito'}**`;
                break;
            case 'italic':
                rep = `*${selected || 'texto em itálico'}*`;
                break;
            case 'highlight':
                rep = `==${selected || 'texto destacado'}==`;
                break;
            case 'math-inline':
                rep = `$${selected || 'f(x)'}$`;
                break;
            case 'math-block':
                rep = `\n$$\n${selected || 'x = \\frac{-b \\pm \\sqrt{\\Delta}}{2a}'}\n$$\n`;
                break;
            case 'list':
                rep = `\n- ${selected || 'Item 1'}\n- Item 2\n`;
                break;
            case 'quote':
                rep = `\n> ${selected || 'Texto de apoio ou citação...'}\n`;
                break;
            case 'table':
                rep = `\n| Item | Descrição |\n| :--- | :--- |\n| A | Exemplo 1 |\n| B | Exemplo 2 |\n`;
                break;
            case 'code':
                rep = `\`${selected || 'código'}\``;
                break;
            case 'callout-tip':
                rep = `\n> [!tip] Dica para o ENEM & Vestibulares\n> ${selected || 'Preste atenção nos dados e entrelinhas do enunciado.'}\n`;
                break;
            case 'callout-warning':
                rep = `\n> [!warning] Atenção / Pegadinha Recorrente\n> ${selected || 'Cuidado para não confundir os conceitos fundamentais.'}\n`;
                break;
            case 'callout-important':
                rep = `\n> [!important] Ponto Fundamental\n> ${selected || 'Propriedade obrigatória para a correta resolução.'}\n`;
                break;
            case 'callout-example':
                rep = `\n> [!example] Passo a Passo da Resolução\n> **Passo 1:** Levantamento das variáveis.\n> **Passo 2:** Aplicação da fórmula matemática.\n`;
                break;
            case 'callout-success':
                rep = `\n> [!check] Conclusão do Gabarito\n> Portanto, a alternativa correta é a **Letra X**.\n`;
                break;
        }

        targetTextarea.setRangeText(rep, start, end, 'end');
        targetTextarea.focus();
        updateSimulator();
    }

    // Vincula todos os botões de ação e formatação declarativos
    document.querySelectorAll('[data-action]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const targetId = this.getAttribute('data-target');
            const action = this.getAttribute('data-action');
            if (targetId && action) {
                const targetEl = document.getElementById(targetId);
                if (targetEl) {
                    applyFormatTo(targetEl, action);
                }
            }
        });
    });

    // 4. Alternador de Modos de Visualização (Split, Editor, Simulador)
    if (btnModeSplit && btnModeForm && btnModeSim) {
        btnModeSplit.addEventListener('click', function () {
            colForm.className = 'col-lg-7 col-xl-7 d-block';
            colSimulator.className = 'col-lg-5 col-xl-5 d-block';
            btnModeSplit.classList.add('active');
            btnModeForm.classList.remove('active');
            btnModeSim.classList.remove('active');
        });

        btnModeForm.addEventListener('click', function () {
            colForm.className = 'col-12 d-block';
            colSimulator.className = 'd-none';
            btnModeSplit.classList.remove('active');
            btnModeForm.classList.add('active');
            btnModeSim.classList.remove('active');
        });

        btnModeSim.addEventListener('click', function () {
            colForm.className = 'd-none';
            colSimulator.className = 'col-12 d-block';
            btnModeSplit.classList.remove('active');
            btnModeForm.classList.remove('active');
            btnModeSim.classList.add('active');
        });
    }

    // 5. Verificação de Rascunho
    if (!isEdit) {
        const saved = localStorage.getItem(storageKey);
        if (saved) {
            try {
                const draft = JSON.parse(saved);
                const draftAlert = document.getElementById('draftAlertQuestao');
                if (draft.enunciado && draft.enunciado !== enunciadoArea.value) {
                    draftAlert.classList.remove('d-none');
                    draftAlert.classList.add('d-flex');

                    document.getElementById('btnRestoreQuestao').addEventListener('click', function () {
                        if (draft.disciplina_id) disciplinaSelect.value = draft.disciplina_id;
                        enunciadoArea.value = draft.enunciado;
                        explicacaoArea.value = draft.explicacao || '';
                        if (draft.resposta_correta) {
                            const r = document.getElementById('resp_' + draft.resposta_correta);
                            if (r) r.checked = true;
                        }
                        if (draft.alternativas) {
                            for (const [letra, txt] of Object.entries(draft.alternativas)) {
                                const input = document.getElementById('alternativa_' + letra.toLowerCase());
                                if (input) input.value = txt;
                            }
                        }
                        updateSimulator();
                        draftAlert.classList.add('d-none');
                    });

                    document.getElementById('btnDiscardQuestao').addEventListener('click', function () {
                        localStorage.removeItem(storageKey);
                        draftAlert.classList.add('d-none');
                    });
                }
            } catch (e) {}
        }
    }

    form.addEventListener('submit', function () {
        localStorage.removeItem(storageKey);
    });

    // Inicializa Simulador após carga inicial dos scripts
    setTimeout(updateSimulator, 50);
});
</script>

<?php
require dirname(__DIR__, 2) . '/layout/footer.php';
