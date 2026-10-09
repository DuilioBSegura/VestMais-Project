<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helper;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::e($pageTitle ?? 'Acesse sua Conta - Vest+') ?></title>
    <!-- Bootstrap 5.3 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Design System Global (Verde Esmeralda / Menta) -->
    <link rel="stylesheet" href="<?= Helper::url('/css/style.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #064e3b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .auth-card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .auth-header {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
            color: #ffffff;
            padding: 2.25rem 2rem 1.75rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <!-- Cabeçalho do Card -->
        <div class="auth-header">
            <a href="<?= Helper::url('/') ?>" class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 text-white rounded-circle mb-3 p-3 text-decoration-none shadow-sm" style="width: 58px; height: 58px;">
                <i class="bi bi-mortarboard-fill fs-2" style="color: #34d399;"></i>
            </a>
            <h1 class="h4 fw-bold text-white mb-1">Entrar no Vest+</h1>
            <p class="small text-light opacity-80 mb-0" style="color: #d1fae5;">Acesse sua área de estudos ou painel de gestão</p>
        </div>

        <div class="p-4 p-md-5">
            <!-- Mensagens Flash -->
            <?php foreach (Flash::all() as $type => $messages): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="alert alert-<?= Helper::e($type) ?> alert-dismissible fade show small d-flex align-items-center gap-2 mb-3 shadow-sm" role="alert">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div><?= Helper::e($msg) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <!-- Formulário de Login -->
            <form action="<?= Helper::url('/login') ?>" method="POST" novalidate>
                <?= Csrf::input() ?>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small text-secondary">Endereço de E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="seu.email@exemplo.com" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="senha" class="form-label fw-semibold small text-secondary mb-0">Senha de Acesso</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar na Plataforma
                </button>
            </form>

            <!-- Link para Criar Conta -->
            <div class="text-center mt-3 pt-2">
                <span class="small text-muted">Ainda não tem uma conta?</span>
                <a href="<?= Helper::url('/cadastro') ?>" class="small fw-bold text-success text-decoration-none ms-1">
                    Criar Conta Gratuita &rarr;
                </a>
            </div>

            <!-- Preenchimento Rápido para Avaliação -->
            <div class="mt-4 pt-3 border-top">
                <div class="small fw-semibold text-secondary text-center mb-2">Acesso Rápido para Avaliação / Demonstração:</div>
                <div class="d-grid gap-2">
                    <button type="button" id="btnFillAluno" class="btn btn-outline-success btn-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-person-fill"></i> Entrar como Aluno (aluno@vestplus.com.br)
                    </button>
                    <button type="button" id="btnFillAdmin" class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-shield-lock-fill"></i> Entrar como Admin (admin@vestplus.com.br)
                    </button>
                </div>
            </div>

            <!-- Link de Retorno à Landing Page -->
            <div class="text-center mt-3">
                <a href="<?= Helper::url('/') ?>" class="small text-muted text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para a Página Inicial
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Vanilla JS para preenchimento rápido -->
    <script>
        document.getElementById('btnFillAluno')?.addEventListener('click', function () {
            document.getElementById('email').value = 'aluno@vestplus.com.br';
            document.getElementById('senha').value = 'aluno123';
        });

        document.getElementById('btnFillAdmin')?.addEventListener('click', function () {
            document.getElementById('email').value = 'admin@vestplus.com.br';
            document.getElementById('senha').value = 'admin123';
        });
    </script>
</body>
</html>
