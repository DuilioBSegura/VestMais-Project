<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helper;

$errors = $errors ?? [];
$nome = $nome ?? '';
$email = $email ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::e($pageTitle ?? 'Crie sua Conta Gratuita - Vest+') ?></title>
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
            max-width: 480px;
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
            <h1 class="h4 fw-bold text-white mb-1">Criar Conta Gratuita</h1>
            <p class="small text-light opacity-80 mb-0" style="color: #d1fae5;">Tenha acesso a todos os artigos e banco de questões</p>
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

            <!-- Formulário de Cadastro -->
            <form action="<?= Helper::url('/cadastro') ?>" method="POST" novalidate>
                <?= Csrf::input() ?>

                <!-- Nome Completo -->
                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold small text-secondary">Nome Completo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person-fill"></i></span>
                        <input type="text" 
                               class="form-control <?= isset($errors['nome']) ? 'is-invalid' : '' ?>" 
                               id="nome" 
                               name="nome" 
                               value="<?= Helper::e($nome) ?>" 
                               placeholder="Ex: Maria Silva" 
                               required 
                               autofocus>
                    </div>
                    <?php if (isset($errors['nome'])): ?>
                        <div class="invalid-feedback d-block"><?= Helper::e($errors['nome']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- E-mail -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small text-secondary">E-mail para Acesso</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" 
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= Helper::e($email) ?>" 
                               placeholder="seu.email@exemplo.com" 
                               required>
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback d-block"><?= Helper::e($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Senha -->
                <div class="mb-3">
                    <label for="senha" class="form-label fw-semibold small text-secondary">Senha de Acesso</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" 
                               class="form-control <?= isset($errors['senha']) ? 'is-invalid' : '' ?>" 
                               id="senha" 
                               name="senha" 
                               placeholder="Mínimo 6 caracteres" 
                               required>
                    </div>
                    <?php if (isset($errors['senha'])): ?>
                        <div class="invalid-feedback d-block"><?= Helper::e($errors['senha']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Confirmação de Senha -->
                <div class="mb-4">
                    <label for="confirmar_senha" class="form-label fw-semibold small text-secondary">Confirme sua Senha</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-shield-lock-fill"></i></span>
                        <input type="password" 
                               class="form-control <?= isset($errors['confirmar_senha']) ? 'is-invalid' : '' ?>" 
                               id="confirmar_senha" 
                               name="confirmar_senha" 
                               placeholder="Digite a mesma senha" 
                               required>
                    </div>
                    <?php if (isset($errors['confirmar_senha'])): ?>
                        <div class="invalid-feedback d-block"><?= Helper::e($errors['confirmar_senha']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-person-check-fill"></i> Concluir Cadastro Gratuito
                </button>
            </form>

            <!-- Link para Login -->
            <div class="text-center mt-3 pt-2">
                <span class="small text-muted">Já possui uma conta?</span>
                <a href="<?= Helper::url('/login') ?>" class="small fw-bold text-success text-decoration-none ms-1">
                    Fazer Login &rarr;
                </a>
            </div>

            <!-- Link de Retorno à Landing Page -->
            <div class="text-center mt-3 border-top pt-3">
                <a href="<?= Helper::url('/') ?>" class="small text-muted text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para a Página Inicial
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
