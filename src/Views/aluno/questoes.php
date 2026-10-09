<?php

declare(strict_types=1);

use App\Core\Helper;

require dirname(__DIR__) . '/layout/header.php';
?>

<!-- Cabeçalho do Módulo -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">
            <i class="bi bi-patch-question-fill text-primary me-2"></i>Banco de Questões & Simulado
        </h1>
        <p class="text-muted mb-0">
            Resolva questões de vestibular e do ENEM com correção imediata e gabarito comentado passo a passo.
        </p>
    </div>
</div>

<!-- Card de Filtros -->
<div class="card card-custom shadow-sm mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="<?= Helper::url('/aluno/questoes') ?>" class="row g-3 align-items-end">
            <!-- Filtro por Disciplina -->
            <div class="col-md-5">
                <label for="disciplina_id" class="form-label fw-semibold small text-secondary">
                    <i class="bi bi-book me-1"></i> Filtrar por Disciplina
                </label>
                <select class="form-select" id="disciplina_id" name="disciplina_id">
                    <option value="">Todas as Disciplinas</option>
                    <?php foreach ($disciplinas as $d): ?>
                        <option value="<?= (int) $d['id'] ?>" <?= ($disciplinaSelecionada === (int) $d['id']) ? 'selected' : '' ?>>
                            <?= Helper::e((string) $d['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filtro por Categoria -->
            <div class="col-md-5">
                <label for="categoria_id" class="form-label fw-semibold small text-secondary">
                    <i class="bi bi-tags me-1"></i> Filtrar por Categoria / Tópico
                </label>
                <select class="form-select" id="categoria_id" name="categoria_id">
                    <option value="">Todas as Categorias</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= ($categoriaSelecionada === (int) $c['id']) ? 'selected' : '' ?>>
                            <?= Helper::e((string) $c['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Botões de Ação do Filtro -->
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                    <i class="bi bi-funnel-fill"></i> Filtrar
                </button>
                <?php if ($disciplinaSelecionada !== null || $categoriaSelecionada !== null): ?>
                    <a href="<?= Helper::url('/aluno/questoes') ?>" class="btn btn-outline-secondary" title="Limpar Filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Barra de Resumo dos Resultados -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-secondary small">
        Exibindo <strong><?= count($questoes) ?></strong> <?= count($questoes) === 1 ? 'questão encontrada' : 'questões encontradas' ?>
        <?php if ($disciplinaSelecionada !== null || $categoriaSelecionada !== null): ?>
            <span class="badge bg-light text-secondary border ms-1">Filtros aplicados</span>
        <?php endif; ?>
    </div>
</div>

<!-- Listagem de Questões ou Estado Vazio -->
<?php if (empty($questoes)): ?>
    <div class="card card-custom shadow-sm text-center py-5 my-4">
        <div class="card-body">
            <i class="bi bi-search text-muted" style="font-size: 3.5rem;"></i>
            <h4 class="mt-3 text-secondary fw-semibold">Nenhuma questão encontrada</h4>
            <p class="text-muted small col-md-6 mx-auto mb-4">
                Não há questões cadastradas para a combinação de disciplina e categoria selecionada. Tente ajustar ou remover os filtros aplicados.
            </p>
            <a href="<?= Helper::url('/aluno/questoes') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Limpar Filtros e Ver Todas
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="questoes-lista d-flex flex-column gap-4 mb-5">
        <?php foreach ($questoes as $index => $q): ?>
            <div class="card card-custom shadow-sm border-0 bg-white p-3 p-md-4">
                <form class="form-questao" 
                      data-questao-id="<?= (int) $q['id'] ?>" 
                      data-api-url="<?= Helper::url('/api/questoes/verificar') ?>">
                    
                    <!-- Cabeçalho da Questão -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 fw-bold">
                                Questão #<?= (int) $q['id'] ?>
                            </span>
                            <span class="badge badge-discipline px-2 py-1">
                                <i class="bi bi-book me-1"></i><?= Helper::e((string) $q['disciplina_nome']) ?>
                            </span>
                        </div>
                        <?php if (!empty($q['categorias_lista'])): ?>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($q['categorias_lista'] as $cat): ?>
                                    <span class="badge badge-category rounded-pill px-2 py-1 small">
                                        <?= Helper::e((string) $cat) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Enunciado -->
                    <div class="mb-4">
                        <p class="fs-6 text-dark lh-base mb-0" style="white-space: pre-line;">
                            <?= Helper::e((string) $q['enunciado']) ?>
                        </p>
                    </div>

                    <!-- Alternativas de A a E -->
                    <div class="alternativas-wrapper d-flex flex-column gap-2 mb-4">
                        <?php 
                        $letras = ['A', 'B', 'C', 'D', 'E'];
                        foreach ($letras as $letra):
                            $field = 'alternativa_' . strtolower($letra);
                            $inputRadioId = 'q_' . (int) $q['id'] . '_' . $letra;
                        ?>
                            <div class="card-alternativa border rounded-3 p-3 transition" 
                                 data-letra="<?= $letra ?>" 
                                 style="cursor: pointer; background-color: #fafafa;">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="form-check pt-1 mb-0">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="resposta" 
                                               id="<?= $inputRadioId ?>" 
                                               value="<?= $letra ?>">
                                    </div>
                                    <div class="d-flex align-items-baseline gap-2 flex-grow-1">
                                        <span class="badge bg-secondary rounded-pill badge-alternativa fw-bold"><?= $letra ?></span>
                                        <label class="form-check-label text-secondary mb-0 w-100 cursor-pointer user-select-none" for="<?= $inputRadioId ?>">
                                            <?= Helper::e((string) ($q[$field] ?? '')) ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Botão de Resolução -->
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary btn-responder d-inline-flex align-items-center gap-2 shadow-sm px-4">
                            <i class="bi bi-send-check"></i> Responder Questão
                        </button>
                    </div>

                    <!-- Caixa de Feedback Assíncrono (revelada via Vanilla JS) -->
                    <div class="feedback-container d-none mt-4 pt-3 border-top">
                        <div class="alerta-resultado alert mb-3" role="alert">
                            <div class="titulo-resultado d-flex align-items-center mb-1"></div>
                            <div class="texto-gabarito small"></div>
                        </div>

                        <!-- Resolução Comentada -->
                        <div class="bg-light p-3 rounded-3 border">
                            <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-semibold small text-uppercase">
                                <i class="bi bi-lightbulb-fill"></i> Resolução e Comentário do Professor
                            </div>
                            <div class="texto-explicacao text-secondary small lh-base" style="white-space: pre-line;"></div>
                        </div>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Script Vanilla JS do Motor de Resolução Assíncrona -->
<script src="<?= Helper::url('/js/questoes.js') ?>"></script>

<?php
require dirname(__DIR__) . '/layout/footer.php';
