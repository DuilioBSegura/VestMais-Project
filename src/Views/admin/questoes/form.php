<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Helper;

require dirname(__DIR__, 2) . '/layout/header.php';

$actionUrl = $isEdit 
    ? Helper::url('/admin/questoes/editar?id=' . (int) ($questao['id'] ?? 0))
    : Helper::url('/admin/questoes/criar');

$respostaCorreta = strtoupper((string) ($questao['resposta_correta'] ?? 'A'));
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="<?= Helper::url('/admin/questoes') ?>" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para o Banco de Questões
                </a>
                <h1 class="h3 fw-bold text-dark mb-0">
                    <?= $isEdit ? '<i class="bi bi-pencil-square text-primary me-2"></i>Editar Questão #' . (int) ($questao['id'] ?? 0) : '<i class="bi bi-plus-circle text-primary me-2"></i>Nova Questão de Múltipla Escolha' ?>
                </h1>
            </div>
        </div>

        <div class="card card-custom shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <span class="text-muted small text-uppercase fw-semibold">
                    <?= $isEdit ? 'Atualize as informações da questão, alternativas e gabarito comentado' : 'Cadastre o enunciado, as 5 alternativas (A a E) e selecione a resposta correta' ?>
                </span>
            </div>

            <div class="card-body p-4">
                <form action="<?= $actionUrl ?>" method="POST" novalidate>
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
                            <?php else: ?>
                                <div class="form-text">Área temática da questão.</div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">
                                Categorias / Tópicos (N:N)
                            </label>
                            <div class="p-2 bg-light rounded border" style="max-height: 150px; overflow-y: auto;">
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
                            <div class="form-text">Selecione tópicos específicos para filtragem refinada.</div>
                        </div>
                    </div>

                    <!-- Enunciado da Questão -->
                    <div class="mb-4">
                        <label for="enunciado" class="form-label fw-semibold">
                            Enunciado da Questão <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control <?= isset($errors['enunciado']) ? 'is-invalid' : '' ?>" 
                                  id="enunciado" 
                                  name="enunciado" 
                                  rows="6" 
                                  placeholder="Digite o texto base, contexto e pergunta da questão de vestibular/ENEM..." 
                                  required><?= Helper::e((string) ($questao['enunciado'] ?? '')) ?></textarea>
                        <?php if (isset($errors['enunciado'])): ?>
                            <div class="invalid-feedback d-block"><?= Helper::e($errors['enunciado']) ?></div>
                        <?php else: ?>
                            <div class="form-text">Texto completo do problema a ser resolvido pelo estudante.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Alternativas de Resposta (A a E) com Seleção de Gabarito via Radio -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0 text-dark">
                                Alternativas de Múltipla Escolha e Gabarito <span class="text-danger">*</span>
                            </label>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-info-circle me-1"></i> Marque o botão da alternativa correta
                            </span>
                        </div>
                        <p class="text-muted small mb-3">
                            Preencha os textos das alternativas de A a E e selecione o botão de opção correspondente ao gabarito oficial.
                        </p>

                        <?php if (isset($errors['resposta_correta'])): ?>
                            <div class="alert alert-danger py-2 small mb-3"><?= Helper::e($errors['resposta_correta']) ?></div>
                        <?php endif; ?>

                        <?php 
                        $letras = ['A', 'B', 'C', 'D', 'E'];
                        foreach ($letras as $letra):
                            $field = 'alternativa_' . strtolower($letra);
                            $isSelected = ($respostaCorreta === $letra);
                        ?>
                            <div class="alternative-radio-card <?= $isSelected ? 'active-answer' : '' ?>" id="card_alt_<?= $letra ?>">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="form-check mb-0">
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
                                            <span class="badge <?= $isSelected ? 'bg-success text-white' : 'bg-light text-muted border' ?> ms-2 badge-status-gabarito">
                                                <?= $isSelected ? 'Gabarito Correto' : 'Marcar como Correta' ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <textarea class="form-control <?= isset($errors[$field]) ? 'is-invalid' : '' ?>" 
                                              name="<?= $field ?>" 
                                              id="<?= $field ?>" 
                                              rows="2" 
                                              placeholder="Texto da alternativa <?= $letra ?>..." 
                                              required><?= Helper::e((string) ($questao[$field] ?? '')) ?></textarea>
                                    <?php if (isset($errors[$field])): ?>
                                        <div class="invalid-feedback d-block"><?= Helper::e($errors[$field]) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Explicação / Resolução Comentada -->
                    <div class="mb-4">
                        <label for="explicacao" class="form-label fw-semibold">
                            Comentário Explicativo / Resolução do Gabarito
                        </label>
                        <textarea class="form-control <?= isset($errors['explicacao']) ? 'is-invalid' : '' ?>" 
                                  id="explicacao" 
                                  name="explicacao" 
                                  rows="5" 
                                  placeholder="Explique o passo a passo da resolução, fórmulas utilizadas e o raciocínio que justifica a alternativa correta..."><?= Helper::e((string) ($questao['explicacao'] ?? '')) ?></textarea>
                        <div class="form-text">Exibido aos estudantes após a tentativa de resolução para aprendizado ativo.</div>
                    </div>

                    <!-- Ações -->
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
</div>

<!-- Destaque visual da alternativa selecionada como gabarito -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const radioButtons = document.querySelectorAll('.radio-gabarito');

    function updateActiveCards() {
        radioButtons.forEach(function (radio) {
            const letra = radio.getAttribute('data-letra');
            const card = document.getElementById('card_alt_' + letra);
            const badge = card ? card.querySelector('.badge-status-gabarito') : null;

            if (radio.checked) {
                if (card) card.classList.add('active-answer');
                if (badge) {
                    badge.className = 'badge bg-success text-white ms-2 badge-status-gabarito';
                    badge.textContent = 'Gabarito Correto';
                }
            } else {
                if (card) card.classList.remove('active-answer');
                if (badge) {
                    badge.className = 'badge bg-light text-muted border ms-2 badge-status-gabarito';
                    badge.textContent = 'Marcar como Correta';
                }
            }
        });
    }

    radioButtons.forEach(function (radio) {
        radio.addEventListener('change', updateActiveCards);
    });

    updateActiveCards();
});
</script>

<?php
require dirname(__DIR__, 2) . '/layout/footer.php';
