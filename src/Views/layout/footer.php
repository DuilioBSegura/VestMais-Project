<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
    </main>

    <!-- Rodapé Administrativo -->
    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container-fluid px-lg-4 d-flex flex-column flex-sm-row justify-content-between align-items-center text-muted small">
            <div>
                <strong>Vest+ Plataforma Educacional</strong> &bull; Sprint 2: Motor de Conteúdo (CMS e Questões)
            </div>
            <div>
                PHP <?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?> &bull; Bootstrap 5 &bull; MariaDB/MySQL
            </div>
        </div>
    </footer>

    <!-- Modal Global de Confirmação de Exclusão (Vanilla JS + Bootstrap 5) -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="deleteConfirmModalLabel">
                        <i class="bi bi-exclamation-triangle-fill"></i> Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="deleteConfirmForm" method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
                    <div class="modal-body py-4">
                        <p class="mb-2">Tem certeza de que deseja excluir o seguinte registro?</p>
                        <div class="alert alert-light border fw-semibold text-break mb-3" id="deleteConfirmItemTitle">
                            ...
                        </div>
                        <p class="text-danger small mb-0">
                            <i class="bi bi-info-circle-fill"></i> Esta operação é irreversível e removerá todos os vínculos com categorias associadas.
                        </p>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger d-flex align-items-center gap-1">
                            <i class="bi bi-trash-fill"></i> Confirmar Exclusão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Script Vanilla JS para Controle de Modais de Exclusão -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteModalEl = document.getElementById('deleteConfirmModal');
            if (deleteModalEl) {
                const deleteForm = document.getElementById('deleteConfirmForm');
                const deleteItemTitle = document.getElementById('deleteConfirmItemTitle');

                document.querySelectorAll('.btn-delete-confirm').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const actionUrl = this.getAttribute('data-action');
                        const itemTitle = this.getAttribute('data-title');

                        if (deleteForm && actionUrl) {
                            deleteForm.setAttribute('action', actionUrl);
                        }
                        if (deleteItemTitle) {
                            deleteItemTitle.textContent = itemTitle || 'Item selecionado';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
