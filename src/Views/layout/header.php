<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Helper;

$currentUser = Auth::user();
$pageTitle = $pageTitle ?? 'Vest+ Plataforma Educacional';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::e($pageTitle) ?> - Vest+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= Helper::url('/css/style.css') ?>">
</head>
<body>
    <!-- Barra de Navegação Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-2 sticky-top">
        <div class="container-fluid px-lg-4">
            <?php 
                $homeUrl = ($currentUser && $currentUser['tipo'] === 'aluno') 
                    ? Helper::url('/aluno/dashboard') 
                    : ($currentUser ? Helper::url('/admin/dashboard') : Helper::url('/'));
                $roleLabel = ($currentUser && $currentUser['tipo'] === 'admin') ? 'Admin' : 'Aluno';
                $roleBadgeClass = ($currentUser && $currentUser['tipo'] === 'admin') ? 'badge-emerald' : 'badge-discipline';
                $currentUri = $_SERVER['REQUEST_URI'] ?? '';
            ?>
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $homeUrl ?>">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-25 p-2">
                    <i class="bi bi-mortarboard-fill text-success fs-4" style="color: #34d399 !important;"></i>
                </div>
                <span class="fs-4 fw-bold">Vest+</span>
                <?php if ($currentUser): ?>
                    <span class="badge <?= $roleBadgeClass ?> fs-8 py-1 px-2 ms-1"><?= $roleLabel ?></span>
                <?php endif; ?>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Alternar navegação">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <?php if ($currentUser && $currentUser['tipo'] === 'aluno'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= (str_contains($currentUri, 'dashboard') || str_contains($currentUri, 'disciplina') || str_contains($currentUri, 'artigo')) && !str_contains($currentUri, 'questoes') ? 'active text-white' : '' ?>" href="<?= Helper::url('/aluno/dashboard') ?>">
                                <i class="bi bi-house-door me-1"></i> Disciplinas & Conteúdos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'questoes') ? 'active text-white' : '' ?>" href="<?= Helper::url('/aluno/questoes') ?>">
                                <i class="bi bi-patch-question me-1"></i> Banco de Questões
                            </a>
                        </li>
                    <?php elseif ($currentUser && $currentUser['tipo'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'admin/dashboard') ? 'active text-white' : '' ?>" href="<?= Helper::url('/admin/dashboard') ?>">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'admin/artigos') ? 'active text-white' : '' ?>" href="<?= Helper::url('/admin/artigos') ?>">
                                <i class="bi bi-journal-text me-1"></i> Artigos Teóricos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= str_contains($currentUri, 'admin/questoes') ? 'active text-white' : '' ?>" href="<?= Helper::url('/admin/questoes') ?>">
                                <i class="bi bi-patch-question me-1"></i> Gestão de Questões
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-success <?= str_contains($currentUri, 'aluno') ? 'active text-white' : '' ?>" href="<?= Helper::url('/aluno/dashboard') ?>" style="color: #34d399 !important;">
                                <i class="bi bi-person-workspace me-1"></i> Ver Área do Aluno
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= Helper::url('/') ?>">
                                <i class="bi bi-house-door me-1"></i> Página Inicial
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <?php if ($currentUser): ?>
                        <div class="text-light d-none d-md-block text-end">
                            <span class="d-block small text-secondary">Conectado como</span>
                            <span class="fw-semibold text-white"><?= Helper::e($currentUser['nome']) ?></span>
                        </div>
                        <a href="<?= Helper::url('/logout') ?>" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    <?php else: ?>
                        <a href="<?= Helper::url('/login') ?>" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                        </a>
                        <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-primary btn-sm shadow-sm">
                            Criar Conta
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="container-fluid px-lg-4 my-4 flex-grow-1">
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
