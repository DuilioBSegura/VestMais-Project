<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;

$currentUser = Auth::user();
?>
<?php if ($currentUser): ?>
                </div> <!-- /.container-fluid -->
            </main> <!-- /.app-main-content -->

            <!-- Rodapé da Aplicação com Barra Lateral -->
            <footer class="app-footer">
                <div class="container-fluid px-3 px-md-4 d-flex flex-column flex-sm-row justify-content-between align-items-center text-muted small">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark">Vest+</span>
                        <span>&bull;</span>
                        <span>Democratizando o acesso ao conhecimento pré-vestibular e ao ENEM</span>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        &copy; <?= date('Y') ?> Vest+ Platform &bull; Todos os direitos reservados.
                    </div>
                </div>
            </footer>
        </div> <!-- /.app-main-wrapper -->
    </div> <!-- /.app-layout -->
<?php else: ?>
    </main>
    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container-fluid px-lg-4 d-flex flex-column flex-sm-row justify-content-between align-items-center text-muted small">
            <div>
                <strong>Vest+</strong> &bull; Plataforma Gratuita de Estudos para Vestibulares
            </div>
            <div>
                &copy; <?= date('Y') ?> Todos os direitos reservados.
            </div>
        </div>
    </footer>
<?php endif; ?>

    <!-- Modal Global de Confirmação de Exclusão -->
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
                    <input type="hidden" name="id" id="deleteConfirmId" value="">
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

    <!-- Scripts da Aplicação -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Script para Controle da Barra Lateral (Sidebar) & Modais -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Controle da Sidebar Mobile
            const sidebar = document.getElementById('appSidebar');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const closeBtn = document.getElementById('sidebarCloseBtn');
            const backdrop = document.getElementById('sidebarBackdrop');

            function openSidebar() {
                if (sidebar) sidebar.classList.add('show');
                if (backdrop) backdrop.classList.add('show');
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('show');
                if (backdrop) backdrop.classList.remove('show');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            // Controle do Modal Global de Exclusão
            const deleteModalEl = document.getElementById('deleteConfirmModal');
            if (deleteModalEl) {
                const deleteForm = document.getElementById('deleteConfirmForm');
                const deleteItemTitle = document.getElementById('deleteConfirmItemTitle');

                document.querySelectorAll('.btn-delete-confirm').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const actionUrl = this.getAttribute('data-action');
                        const itemTitle = this.getAttribute('data-title');
                        const itemId = this.getAttribute('data-id');
                        const idInput = document.getElementById('deleteConfirmId');
                        if (idInput) {
                            idInput.value = itemId || '';
                        }

                        if (deleteForm && actionUrl) {
                            deleteForm.setAttribute('action', actionUrl);
                        }
                        if (deleteItemTitle) {
                            deleteItemTitle.textContent = itemTitle || 'Item selecionado';
                        }
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            bootstrap.Modal.getOrCreateInstance(deleteModalEl).show();
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
