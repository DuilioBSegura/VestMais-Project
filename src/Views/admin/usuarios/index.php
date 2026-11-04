<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helper;

$currentUser = Auth::user();
require dirname(__DIR__, 2) . '/layout/header.php';
?>

<!-- Cabeçalho da Gestão de Usuários -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h3 fw-bold mb-0 text-dark">
                <i class="bi bi-people-fill text-success me-2"></i>Gestão de Usuários
            </h1>
            <span class="badge badge-discipline">Painel de Acesso RBAC</span>
        </div>
        <p class="text-muted mb-0">
            Controle de contas, auditoria de acessos e concessão de privilégios de administrador.
        </p>
    </div>
</div>

<!-- Cards de Estatísticas Rápidas -->
<div class="row row-cols-1 row-cols-sm-3 g-3 mb-4">
    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Total de Contas</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) $totalUsuarios ?></div>
                <span class="text-success small fw-medium">Cadastradas no Sistema</span>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-mortarboard"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Estudantes (Alunos)</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) $totalAlunos ?></div>
                <span class="text-success small fw-medium">Vestibulandos Ativos</span>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-emerald">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Administradores</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) $totalAdmins ?></div>
                <span class="text-success small fw-medium">Acesso Total ao CMS</span>
            </div>
        </div>
    </div>
</div>

<!-- Barra de Filtros & Busca -->
<div class="card card-custom shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= Helper::url('/admin/usuarios') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-7">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" 
                           class="form-control" 
                           name="busca" 
                           placeholder="Buscar por nome ou e-mail..." 
                           value="<?= Helper::e((string) ($busca ?? '')) ?>">
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <select class="form-select" name="tipo">
                    <option value="">-- Todos os Papéis --</option>
                    <option value="aluno" <?= ($tipo === 'aluno') ? 'selected' : '' ?>>Apenas Estudantes (Alunos)</option>
                    <option value="admin" <?= ($tipo === 'admin') ? 'selected' : '' ?>>Apenas Administradores</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <?php if ($busca !== null || $tipo !== null): ?>
                    <a href="<?= Helper::url('/admin/usuarios') ?>" class="btn btn-outline-secondary" title="Limpar Filtro">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Usuários -->
<div class="card card-custom shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-person-lines-fill text-success me-2"></i>Usuários Cadastrados
        </h5>
        <span class="badge bg-light text-muted border">
            <?= count($usuarios) ?> registro(s) encontrado(s)
        </span>
    </div>

    <div class="card-body p-0">
        <?php if (empty($usuarios)): ?>
            <div class="p-5 text-center text-muted">
                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                <h5 class="fw-bold">Nenhum usuário encontrado</h5>
                <p class="small mb-3">Tente ajustar os termos da sua busca ou filtros aplicados.</p>
                <a href="<?= Helper::url('/admin/usuarios') ?>" class="btn btn-outline-primary btn-sm">Limpar Filtros</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Usuário</th>
                            <th>Papel / Acesso</th>
                            <th>Foco de Preparação</th>
                            <th>Data de Cadastro</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <?php 
                                $uId = (int) $u['id'];
                                $isSelf = ((int) ($currentUser['id'] ?? 0)) === $uId;
                                $uTipo = (string) ($u['tipo'] ?? 'aluno');
                                
                                // Iniciais
                                $partes = explode(' ', trim((string) $u['nome']));
                                $iniciais = mb_substr($partes[0], 0, 1, 'UTF-8');
                                if (count($partes) > 1) {
                                    $iniciais .= mb_substr(end($partes), 0, 1, 'UTF-8');
                                }
                                $iniciais = mb_strtoupper($iniciais, 'UTF-8');
                            ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle-sm">
                                            <?= Helper::e($iniciais) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                <?= Helper::e((string) $u['nome']) ?>
                                                <?php if ($isSelf): ?>
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 0.65rem;">Você</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted"><?= Helper::e((string) $u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($uTipo === 'admin'): ?>
                                        <span class="badge badge-emerald">
                                            <i class="bi bi-shield-check me-1"></i> Administrador
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-discipline">
                                            <i class="bi bi-mortarboard me-1"></i> Aluno
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= Helper::e(strtoupper((string) ($u['vestibular_foco'] ?? 'ENEM'))) ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d/m/Y H:i', strtotime((string) $u['criado_em'])) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <?php if (!$isSelf): ?>
                                            <!-- Formulário de Alternar Papel -->
                                            <form action="<?= Helper::url('/admin/usuarios/alterar-tipo') ?>" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza de que deseja alterar o nível de acesso deste usuário?');">
                                                <?= Csrf::input() ?>
                                                <input type="hidden" name="id" value="<?= $uId ?>">
                                                <?php if ($uTipo === 'admin'): ?>
                                                    <input type="hidden" name="tipo" value="aluno">
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm" title="Rebaixar para Aluno">
                                                        <i class="bi bi-person-down me-1"></i> Tornar Aluno
                                                    </button>
                                                <?php else: ?>
                                                    <input type="hidden" name="tipo" value="admin">
                                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Promover para Administrador">
                                                        <i class="bi bi-shield-plus me-1"></i> Promover a Admin
                                                    </button>
                                                <?php endif; ?>
                                            </form>

                                            <!-- Botão de Excluir -->
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm btn-delete-confirm" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-id="<?= $uId ?>" 
                                                    data-action="<?= Helper::url('/admin/usuarios/excluir?id=' . $uId) ?>" 
                                                    data-title="Conta de: <?= Helper::e((string) $u['nome']) ?> (<?= Helper::e((string) $u['email']) ?>)"
                                                    title="Excluir Conta">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small fst-italic">Conta em uso</span>
                                        <?php endif; ?>
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

<?php
require dirname(__DIR__, 2) . '/layout/footer.php';
