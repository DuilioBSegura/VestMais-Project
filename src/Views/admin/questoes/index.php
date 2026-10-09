<?php

declare(strict_types=1);

use App\Core\Helper;

require dirname(__DIR__, 2) . '/layout/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">
            <i class="bi bi-patch-question-fill text-primary me-2"></i>Banco de Questões (Admin)
        </h1>
        <p class="text-muted mb-0">
            Gerencie o banco de questões de múltipla escolha (A a E) com gabarito comentado e categorização.
        </p>
    </div>
    <div>
        <a href="<?= Helper::url('/admin/questoes/criar') ?>" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-lg"></i> Nova Questão
        </a>
    </div>
</div>

<div class="card card-custom shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-semibold text-secondary">
            Questões Cadastradas <span class="badge bg-light text-dark border ms-1"><?= count($questoes) ?></span>
        </h5>
    </div>

    <div class="card-body p-0">
        <?php if (empty($questoes)): ?>
            <div class="text-center py-5">
                <i class="bi bi-question-circle text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">Nenhuma questão cadastrada até o momento.</h5>
                <p class="text-secondary small">Clique no botão "Nova Questão" acima para cadastrar a primeira questão do banco.</p>
                <a href="<?= Helper::url('/admin/questoes/criar') ?>" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="bi bi-plus-circle me-1"></i> Cadastrar Primeira Questão
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th style="width: 70px;" class="ps-3">ID</th>
                            <th>Enunciado da Questão</th>
                            <th style="width: 150px;">Disciplina</th>
                            <th>Categorias</th>
                            <th style="width: 120px;" class="text-center">Gabarito</th>
                            <th style="width: 150px;" class="text-end pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questoes as $q): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-muted">#<?= (int) $q['id'] ?></td>
                                <td>
                                    <div class="text-dark fw-medium mb-1">
                                        <?= Helper::e(Helper::truncate((string) $q['enunciado'], 140)) ?>
                                    </div>
                                    <span class="text-muted small">
                                        Cadastrada em <?= date('d/m/Y', strtotime((string) $q['criado_em'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-discipline px-2 py-1">
                                        <i class="bi bi-book me-1"></i><?= Helper::e((string) $q['disciplina_nome']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($q['categorias_lista'])): ?>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($q['categorias_lista'] as $catNome): ?>
                                                <span class="badge badge-category rounded-pill px-2 py-1">
                                                    <?= Helper::e((string) $catNome) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Sem categorias</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-6 px-3 py-1 fw-bold">
                                        <?= Helper::e((string) $q['resposta_correta']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-outline-success btn-view-question" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewQuestionModal"
                                                data-id="<?= (int) $q['id'] ?>"
                                                data-disciplina="<?= Helper::e((string) $q['disciplina_nome']) ?>"
                                                data-enunciado="<?= Helper::e((string) $q['enunciado']) ?>"
                                                data-alta="<?= Helper::e((string) $q['alternativa_a']) ?>"
                                                data-altb="<?= Helper::e((string) $q['alternativa_b']) ?>"
                                                data-altc="<?= Helper::e((string) $q['alternativa_c']) ?>"
                                                data-altd="<?= Helper::e((string) $q['alternativa_d']) ?>"
                                                data-alte="<?= Helper::e((string) $q['alternativa_e']) ?>"
                                                data-correta="<?= Helper::e((string) $q['resposta_correta']) ?>"
                                                data-explicacao="<?= Helper::e((string) ($q['explicacao'] ?? 'Nenhum comentário cadastrado.')) ?>"
                                                title="Visualizar Detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <a href="<?= Helper::url('/admin/questoes/editar?id=' . (int) $q['id']) ?>" 
                                           class="btn btn-outline-secondary" 
                                           title="Editar Questão">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-delete-confirm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteConfirmModal"
                                                data-id="<?= (int) $q['id'] ?>"
                                                data-title="Questão #<?= (int) $q['id'] ?> - <?= Helper::e(Helper::truncate((string) $q['enunciado'], 60)) ?>"
                                                data-action="<?= Helper::url('/admin/questoes/excluir?id=' . (int) $q['id']) ?>"
                                                title="Excluir Questão">
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

<!-- Modal para Visualização Completa da Questão (Vanilla JS) -->
<div class="modal fade" id="viewQuestionModal" tabindex="-1" aria-labelledby="viewQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="viewQuestionModalLabel">
                    <i class="bi bi-file-earmark-text text-primary"></i> <span id="modalQId">Questão</span>
                    <span class="badge badge-discipline ms-2" id="modalQDisciplina">Disciplina</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold small">Enunciado</h6>
                    <div class="p-3 bg-light rounded border text-dark" id="modalQEnunciado" style="white-space: pre-line;"></div>
                </div>

                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold small">Alternativas de Resposta</h6>
                    <div class="list-group">
                        <div class="list-group-item d-flex gap-3 align-items-start" id="modalAltA">
                            <span class="badge bg-secondary rounded-pill fs-7">A</span>
                            <div class="flex-grow-1 text-alt"></div>
                        </div>
                        <div class="list-group-item d-flex gap-3 align-items-start" id="modalAltB">
                            <span class="badge bg-secondary rounded-pill fs-7">B</span>
                            <div class="flex-grow-1 text-alt"></div>
                        </div>
                        <div class="list-group-item d-flex gap-3 align-items-start" id="modalAltC">
                            <span class="badge bg-secondary rounded-pill fs-7">C</span>
                            <div class="flex-grow-1 text-alt"></div>
                        </div>
                        <div class="list-group-item d-flex gap-3 align-items-start" id="modalAltD">
                            <span class="badge bg-secondary rounded-pill fs-7">D</span>
                            <div class="flex-grow-1 text-alt"></div>
                        </div>
                        <div class="list-group-item d-flex gap-3 align-items-start" id="modalAltE">
                            <span class="badge bg-secondary rounded-pill fs-7">E</span>
                            <div class="flex-grow-1 text-alt"></div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success border-success-subtle mb-0">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <strong class="text-success">Gabarito Oficial: Letra <span id="modalQCorreta"></span></strong>
                    </div>
                    <div class="small text-dark" id="modalQExplicacao" style="white-space: pre-line;"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Vanilla JS para Preenchimento do Modal de Visualização da Questão -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewButtons = document.querySelectorAll('.btn-view-question');
    viewButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const disciplina = this.getAttribute('data-disciplina');
            const enunciado = this.getAttribute('data-enunciado');
            const correta = this.getAttribute('data-correta');
            const explicacao = this.getAttribute('data-explicacao');

            document.getElementById('modalQId').textContent = 'Questão #' + id;
            document.getElementById('modalQDisciplina').textContent = disciplina;
            document.getElementById('modalQEnunciado').textContent = enunciado;
            document.getElementById('modalQCorreta').textContent = correta;
            document.getElementById('modalQExplicacao').textContent = explicacao;

            const alts = {
                'A': { el: document.getElementById('modalAltA'), text: this.getAttribute('data-alta') },
                'B': { el: document.getElementById('modalAltB'), text: this.getAttribute('data-altb') },
                'C': { el: document.getElementById('modalAltC'), text: this.getAttribute('data-altc') },
                'D': { el: document.getElementById('modalAltD'), text: this.getAttribute('data-altd') },
                'E': { el: document.getElementById('modalAltE'), text: this.getAttribute('data-alte') },
            };

            for (const [letter, data] of Object.entries(alts)) {
                if (data.el) {
                    data.el.querySelector('.text-alt').textContent = data.text;
                    const badge = data.el.querySelector('.badge');
                    if (letter === correta) {
                        data.el.classList.add('list-group-item-success', 'border-success', 'fw-bold');
                        badge.className = 'badge bg-success rounded-pill fs-7';
                    } else {
                        data.el.classList.remove('list-group-item-success', 'border-success', 'fw-bold');
                        badge.className = 'badge bg-secondary rounded-pill fs-7';
                    }
                }
            }
        });
    });
});
</script>

<?php
require dirname(__DIR__, 2) . '/layout/footer.php';
