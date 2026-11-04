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
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="<?= Helper::url('/admin/questoes') ?>" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para o Banco de Questões
                </a>
                <h1 class="h3 fw-bold text-dark mb-0">
                    <?= $isEdit ? '<i class="bi bi-pencil-square text-success me-2"></i>Editar Questão #' . $questaoId : '<i class="bi bi-plus-circle text-success me-2"></i>Nova Questão de Múltipla Escolha' ?>
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 small">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Simulador de Aluno Ativo
                </span>
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

        <div class="row g-4">
            <!-- Formulário de Cadastro (Coluna Principal) -->
            <div class="col-lg-7 col-xl-7">
                <div class="card card-custom shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <span class="text-muted small text-uppercase fw-semibold">
                            <?= $isEdit ? 'Atualize as informações da questão, alternativas e gabarito comentado' : 'Cadastre o enunciado, as 5 alternativas (A a E) e selecione o gabarito oficial' ?>
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

                            <!-- Enunciado da Questão -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="enunciado" class="form-label fw-semibold mb-0">
                                        Enunciado da Questão <span class="text-danger">*</span>
                                    </label>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary py-0 px-2" data-format-target="enunciado" data-syntax="bold" title="Negrito"><strong>B</strong></button>
                                        <button type="button" class="btn btn-outline-secondary py-0 px-2" data-format-target="enunciado" data-syntax="italic" title="Itálico"><em>I</em></button>
                                        <button type="button" class="btn btn-outline-secondary py-0 px-2" data-format-target="enunciado" data-syntax="formula" title="Fórmula Matemátca">∑</button>
                                    </div>
                                </div>
                                <textarea class="form-control <?= isset($errors['enunciado']) ? 'is-invalid' : '' ?>" 
                                          id="enunciado" 
                                          name="enunciado" 
                                          rows="5" 
                                          placeholder="Digite o texto-base e o problema da questão..." 
                                          required><?= Helper::e((string) ($questao['enunciado'] ?? '')) ?></textarea>
                                <?php if (isset($errors['enunciado'])): ?>
                                    <div class="invalid-feedback d-block"><?= Helper::e($errors['enunciado']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Alternativas A a E com Seleção Fluída de Gabarito -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold mb-0 text-dark">
                                        Alternativas & Gabarito Oficial <span class="text-danger">*</span>
                                    </label>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                        <i class="bi bi-hand-index-thumb me-1"></i> Clique no botão da letra para definir o gabarito
                                    </span>
                                </div>

                                <?php 
                                $letras = ['A', 'B', 'C', 'D', 'E'];
                                foreach ($letras as $letra):
                                    $field = 'alternativa_' . strtolower($letra);
                                    $isSelected = ($respostaCorreta === $letra);
                                ?>
                                    <div class="card card-custom p-3 mb-2 alternative-interactive-box <?= $isSelected ? 'border-success bg-success-subtle' : '' ?>" id="box_alt_<?= $letra ?>">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
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
                                            <button type="button" class="btn btn-sm <?= $isSelected ? 'btn-success text-white' : 'btn-outline-secondary' ?> btn-mark-gabarito" data-target-radio="resp_<?= $letra ?>">
                                                <i class="bi <?= $isSelected ? 'bi-check-circle-fill' : 'bi-circle' ?> me-1"></i>
                                                <?= $isSelected ? 'Gabarito Oficial' : 'Definir como Gabarito' ?>
                                            </button>
                                        </div>
                                        <div>
                                            <textarea class="form-control input-alt-text <?= isset($errors[$field]) ? 'is-invalid' : '' ?>" 
                                                      name="<?= $field ?>" 
                                                      id="<?= $field ?>" 
                                                      data-letra="<?= $letra ?>"
                                                      rows="2" 
                                                      placeholder="Texto da alternativa <?= $letra ?>..." 
                                                      required><?= Helper::e((string) ($questao[$field] ?? '')) ?></textarea>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Explicação / Resolução Comentada -->
                            <div class="mb-4">
                                <label for="explicacao" class="form-label fw-semibold">
                                    Resolução Comentada / Explicação do Professor
                                </label>
                                <textarea class="form-control <?= isset($errors['explicacao']) ? 'is-invalid' : '' ?>" 
                                          id="explicacao" 
                                          name="explicacao" 
                                          rows="4" 
                                          placeholder="Passo a passo da resolução, justificativa das incorretas e raciocínio oficial..."><?= Helper::e((string) ($questao['explicacao'] ?? '')) ?></textarea>
                                <div class="form-text">Exibido ao estudante após a tentativa de resposta.</div>
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
            <div class="col-lg-5 col-xl-5">
                <div class="card card-custom shadow-sm sticky-top" style="top: 85px;">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-phone text-success fs-5"></i>
                            <h5 class="mb-0 fw-bold text-dark">Simulador do Aluno</h5>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small">
                            Ao Vivo
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="small text-muted mb-2 text-uppercase fw-semibold" id="simDisciplina">
                            <i class="bi bi-book me-1"></i> Selecione uma Disciplina
                        </div>

                        <!-- Enunciado Simulado -->
                        <div class="fw-semibold text-dark mb-4 text-break" id="simEnunciado" style="min-height: 50px;">
                            <span class="text-muted fst-italic">O texto do enunciado aparecerá aqui enquanto você digita...</span>
                        </div>

                        <!-- Alternativas Simuladas -->
                        <div class="d-flex flex-column gap-2 mb-4">
                            <?php foreach ($letras as $letra): ?>
                                <div class="card-alternativa sim-card-alt d-flex align-items-center gap-2" id="sim_card_<?= $letra ?>" style="cursor: default;">
                                    <span class="badge bg-light text-dark border badge-alternativa fw-bold px-2 py-1" id="sim_badge_<?= $letra ?>">
                                        <?= $letra ?>
                                    </span>
                                    <span class="small text-dark flex-grow-1 text-break" id="sim_text_<?= $letra ?>">
                                        <span class="text-muted fst-italic">Alternativa <?= $letra ?>...</span>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Feedback Simulado -->
                        <div class="alert alert-success border-success-subtle shadow-sm p-3 mb-0" id="simFeedbackBox">
                            <div class="d-flex align-items-center gap-2 fw-bold text-success mb-1">
                                <i class="bi bi-check-circle-fill"></i> Gabarito Oficial: Letra <span id="simGabaritoLetra"><?= $respostaCorreta ?></span>
                            </div>
                            <div class="small text-secondary text-break" id="simExplicacao">
                                <?= !empty($questao['explicacao']) ? Helper::e((string) $questao['explicacao']) : 'A resolução comentada digitada pelo professor aparecerá aqui.' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

    const simEnunciado = document.getElementById('simEnunciado');
    const simExplicacao = document.getElementById('simExplicacao');
    const simDisciplina = document.getElementById('simDisciplina');
    const simGabaritoLetra = document.getElementById('simGabaritoLetra');

    // 1. Atualização do Simulador
    function updateSimulator() {
        // Enunciado
        const enun = enunciadoArea.value.trim();
        simEnunciado.textContent = enun !== '' ? enun : 'O texto do enunciado aparecerá aqui enquanto você digita...';

        // Disciplina
        const selectedDisc = disciplinaSelect.options[disciplinaSelect.selectedIndex]?.text;
        simDisciplina.innerHTML = `<i class="bi bi-book me-1"></i> ${disciplinaSelect.value ? selectedDisc : 'Selecione uma Disciplina'}`;

        // Explicação
        const expl = explicacaoArea.value.trim();
        simExplicacao.textContent = expl !== '' ? expl : 'A resolução comentada digitada pelo professor aparecerá aqui.';

        // Alternativas
        altInputs.forEach(function (input) {
            const letra = input.getAttribute('data-letra');
            const simText = document.getElementById('sim_text_' + letra);
            const val = input.value.trim();
            if (simText) {
                simText.textContent = val !== '' ? val : `Alternativa ${letra}...`;
            }
        });

        // Gabarito Selecionado
        const checkedRadio = document.querySelector('.radio-gabarito:checked');
        const gabaritoLetra = checkedRadio ? checkedRadio.value : 'A';
        simGabaritoLetra.textContent = gabaritoLetra;

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

    // 2. Ouvintes de Eventos
    enunciadoArea.addEventListener('input', updateSimulator);
    explicacaoArea.addEventListener('input', updateSimulator);
    disciplinaSelect.addEventListener('change', updateSimulator);
    altInputs.forEach(input => input.addEventListener('input', updateSimulator));
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

    // 3. Verificação de Rascunho
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

    // Inicializa Simulador
    updateSimulator();
});
</script>

<?php
require dirname(__DIR__, 2) . '/layout/footer.php';
