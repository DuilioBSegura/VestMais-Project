<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Helper;

$currentUser = Auth::user();
$pageTitle = $pageTitle ?? 'Vest+ Plataforma Educacional';

// Iniciais e primeiro nome do usuário para os avatares visuais
$nomeCompleto = (string) ($currentUser['nome'] ?? 'Usuário');
$partesNome = explode(' ', trim($nomeCompleto));
$primeiroNome = $partesNome[0] ?? 'Usuário';
$iniciais = mb_substr($primeiroNome, 0, 1, 'UTF-8');
if (count($partesNome) > 1) {
    $iniciais .= mb_substr(end($partesNome), 0, 1, 'UTF-8');
}
$iniciais = mb_strtoupper($iniciais, 'UTF-8');

$homeUrl = ($currentUser && $currentUser['tipo'] === 'aluno') 
    ? Helper::url('/aluno/dashboard') 
    : ($currentUser ? Helper::url('/admin/dashboard') : Helper::url('/'));

$roleLabel = ($currentUser && $currentUser['tipo'] === 'admin') ? 'Admin' : 'Aluno';
$roleBadgeClass = ($currentUser && $currentUser['tipo'] === 'admin') ? 'badge-emerald' : 'badge-discipline';
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::e($pageTitle) ?> - Vest+</title>
    <!-- Estilos da Aplicação -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= Helper::url('/css/style.css') ?>">
</head>
<body>

<?php if ($currentUser): ?>
    <!-- ==============================================================================
         LAYOUT DE DASHBOARD AUTENTICADO COM BARRA LATERAL (SIDEBAR)
         ============================================================================== -->
    <div class="app-layout" id="appLayout">
        <!-- Cortina de fundo para mobile -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Barra Lateral Fixa / Retrátil -->
        <aside class="app-sidebar" id="appSidebar">
            <!-- Cabeçalho da Sidebar -->
            <div class="sidebar-header">
                <a href="<?= $homeUrl ?>" class="sidebar-brand">
                    <div class="sidebar-brand-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div>
                        <span class="brand-title d-block">Vest+</span>
                    </div>
                    <span class="badge <?= $roleBadgeClass ?> sidebar-role-badge ms-1"><?= $roleLabel ?></span>
                </a>
                <button type="button" class="btn-close-sidebar d-lg-none" id="sidebarCloseBtn" aria-label="Fechar Navegação Lateral">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Links de Navegação -->
            <div class="sidebar-nav-wrapper">
                <?php if ($currentUser['tipo'] === 'aluno'): ?>
                    <div class="nav-section-title">Estudos & Preparação</div>
                    <ul class="nav flex-column sidebar-nav mb-3">
                        <li class="nav-item">
                            <a class="nav-link <?= (str_contains($currentUri, 'dashboard') || str_contains($currentUri, 'disciplina')) && !str_contains($currentUri, 'questoes') && !str_contains($currentUri, 'configuracoes') ? 'active' : '' ?>" href="<?= Helper::url('/aluno/dashboard') ?>">
                                <i class="bi bi-house-door-fill"></i>
                                <span>Painel de Estudos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'questoes') ? 'active' : '' ?>" href="<?= Helper::url('/aluno/questoes') ?>">
                                <i class="bi bi-patch-question-fill"></i>
                                <span>Banco de Questões</span>
                                <span class="badge bg-success bg-opacity-25 text-success small ms-auto px-2 py-1">Simulado</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'artigo') ? 'active' : '' ?>" href="<?= Helper::url('/aluno/dashboard#materias') ?>">
                                <i class="bi bi-journal-text"></i>
                                <span>Artigos Teóricos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'medalha') || str_contains($currentUri, 'conquista') ? 'active' : '' ?>" href="<?= Helper::url('/aluno/medalhas') ?>">
                                <i class="bi bi-trophy-fill text-warning"></i>
                                <span>Minhas Conquistas</span>
                            </a>
                        </li>
                    </ul>

                    <div class="nav-section-title">Minha Conta</div>
                    <ul class="nav flex-column sidebar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'configuracoes') ? 'active' : '' ?>" href="<?= Helper::url('/configuracoes') ?>">
                                <i class="bi bi-gear-fill"></i>
                                <span>Configurações & Perfil</span>
                            </a>
                        </li>
                    </ul>

                <?php elseif ($currentUser['tipo'] === 'admin'): ?>
                    <div class="nav-section-title">Gestão Educacional</div>
                    <ul class="nav flex-column sidebar-nav mb-3">
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'admin/dashboard') ? 'active' : '' ?>" href="<?= Helper::url('/admin/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard Executivo</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'admin/artigos') ? 'active' : '' ?>" href="<?= Helper::url('/admin/artigos') ?>">
                                <i class="bi bi-journal-text"></i>
                                <span>Artigos Teóricos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'admin/questoes') ? 'active' : '' ?>" href="<?= Helper::url('/admin/questoes') ?>">
                                <i class="bi bi-patch-question-fill"></i>
                                <span>Banco de Questões</span>
                            </a>
                        </li>
                    </ul>

                    <div class="nav-section-title">Administração & Taxonomia</div>
                    <ul class="nav flex-column sidebar-nav mb-3">
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'admin/usuarios') ? 'active' : '' ?>" href="<?= Helper::url('/admin/usuarios') ?>">
                                <i class="bi bi-people-fill"></i>
                                <span>Gestão de Usuários</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'admin/categorias') || str_contains($currentUri, 'admin/disciplinas') ? 'active' : '' ?>" href="<?= Helper::url('/admin/categorias') ?>">
                                <i class="bi bi-tags-fill"></i>
                                <span>Disciplinas & Categorias</span>
                            </a>
                        </li>
                    </ul>

                    <div class="nav-section-title">Visão do Aluno & Conta</div>
                    <ul class="nav flex-column sidebar-nav">
                        <li class="nav-item">
                            <a class="nav-link text-success" href="<?= Helper::url('/aluno/dashboard') ?>" target="_blank" title="Abre o ambiente do estudante em nova aba">
                                <i class="bi bi-person-workspace text-success"></i>
                                <span class="text-white">Ver como Aluno</span>
                                <i class="bi bi-box-arrow-up-right ms-auto small text-secondary"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'configuracoes') ? 'active' : '' ?>" href="<?= Helper::url('/configuracoes') ?>">
                                <i class="bi bi-gear-fill"></i>
                                <span>Minhas Configurações</span>
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Rodapé da Sidebar com Usuário e Logout -->
            <div class="sidebar-user-footer">
                <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden me-2">
                    <div class="avatar-circle-sm">
                        <?= Helper::e($iniciais) ?>
                    </div>
                    <div class="overflow-hidden">
                        <span class="user-name text-truncate d-block"><?= Helper::e($currentUser['nome']) ?></span>
                        <span class="user-role small"><?= $roleLabel ?> &bull; <?= Helper::e($currentUser['email']) ?></span>
                    </div>
                </div>
                <a href="<?= Helper::url('/logout') ?>" class="btn-sidebar-logout" title="Encerrar sessão" aria-label="Sair">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </aside>

        <!-- Área de Conteúdo Principal e Topbar -->
        <div class="app-main-wrapper">
            <!-- Barra Superior (Topbar) -->
            <header class="app-topbar">
                <div class="d-flex align-items-center gap-3">
                    <!-- Breadcrumb Dinâmico -->
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <span class="text-secondary fw-semibold">
                            <?= $currentUser['tipo'] === 'admin' ? 'Painel Administrativo' : 'Área do Aluno' ?>
                        </span>
                        <i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i>
                        <span class="text-dark fw-bold"><?= Helper::e($pageTitle) ?></span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <?php if ($currentUser['tipo'] === 'aluno'): ?>
                        <a href="<?= Helper::url('/aluno/questoes') ?>" class="btn btn-sm btn-outline-primary d-none d-md-inline-flex align-items-center gap-2">
                            <i class="bi bi-play-circle-fill"></i> Praticar Questões
                        </a>
                    <?php else: ?>
                        <div class="d-none d-md-flex gap-2">
                            <a href="<?= Helper::url('/admin/artigos/criar') ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                <i class="bi bi-plus-lg"></i> Novo Artigo
                            </a>
                            <a href="<?= Helper::url('/admin/questoes/criar') ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                <i class="bi bi-plus-circle"></i> Nova Questão
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Dropdown do Usuário -->
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 py-1 px-2 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-circle-xs">
                                <?= Helper::e($iniciais) ?>
                            </div>
                            <span class="d-none d-md-inline fw-semibold small text-dark"><?= Helper::e($primeiroNome) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark"><?= Helper::e($currentUser['nome']) ?></div>
                                <div class="small text-muted"><?= Helper::e($currentUser['email']) ?></div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?= Helper::url('/configuracoes') ?>">
                                    <i class="bi bi-gear text-secondary"></i> Configurações da Conta
                                </a>
                            </li>
                            <?php if ($currentUser['tipo'] === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?= Helper::url('/aluno/dashboard') ?>">
                                        <i class="bi bi-person-workspace text-secondary"></i> Visualizar Área do Aluno
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger d-flex align-items-center gap-2" href="<?= Helper::url('/logout') ?>">
                                    <i class="bi bi-box-arrow-right"></i> Sair da Plataforma
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Conteúdo da Página -->
            <main class="app-main-content">
                <div class="container-fluid px-3 px-md-4 py-4">
                    <!-- Renderização de Mensagens Flash -->
                    <?php foreach (Flash::all() as $type => $messages): ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="alert alert-<?= Helper::e($type) ?> alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm" role="alert">
                                <?php if ($type === 'success'): ?>
                                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                                <?php elseif ($type === 'danger'): ?>
                                    <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                                <?php else: ?>
                                    <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                                <?php endif; ?>
                                <div><?= Helper::e($msg) ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

<?php else: ?>
    <!-- Fallback para Páginas Não Autenticadas -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-2 sticky-top">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= Helper::url('/') ?>">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-25 p-2">
                    <i class="bi bi-mortarboard-fill text-success fs-4" style="color: #34d399 !important;"></i>
                </div>
                <span class="fs-4 fw-bold">Vest+</span>
            </a>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="<?= Helper::url('/login') ?>" class="btn btn-outline-light btn-sm">Entrar</a>
                <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-primary btn-sm">Criar Conta</a>
            </div>
        </div>
    </nav>
    <main class="container-fluid px-lg-4 my-4 flex-grow-1">
        <?php foreach (Flash::all() as $type => $messages): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="alert alert-<?= Helper::e($type) ?> alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm" role="alert">
                    <div><?= Helper::e($msg) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
<?php endif; ?>
