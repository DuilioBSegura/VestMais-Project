<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helper;

$currentUser = Auth::user();
require dirname(__DIR__) . '/layout/header.php';

// Iniciais do nome para o avatar circular
$partesNome = explode(' ', trim((string) ($usuario['nome'] ?? 'U')));
$iniciais = mb_substr($partesNome[0], 0, 1, 'UTF-8');
if (count($partesNome) > 1) {
    $iniciais .= mb_substr(end($partesNome), 0, 1, 'UTF-8');
}
$iniciais = mb_strtoupper($iniciais, 'UTF-8');

$focoAtual = strtoupper((string) ($usuario['vestibular_foco'] ?? 'ENEM'));
$isAluno = ($usuario['tipo'] ?? 'aluno') === 'aluno';
?>

<!-- Cabeçalho da Página -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h3 fw-bold mb-0 text-dark">
                <i class="bi bi-gear-fill text-success me-2"></i>Configurações de Usuário
            </h1>
            <span class="badge <?= $isAluno ? 'badge-discipline' : 'badge-emerald' ?>">
                <?= $isAluno ? 'Conta Aluno' : 'Conta Admin' ?>
            </span>
        </div>
        <p class="text-muted mb-0">
            Gerencie seus dados cadastrais, preferências de preparação e credenciais de segurança.
        </p>
    </div>
</div>

<div class="row g-4">
    <!-- Coluna Lateral: Resumo do Perfil -->
    <div class="col-lg-4">
        <div class="card card-custom shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <div class="avatar-circle-lg mx-auto mb-3 shadow-sm">
                    <?= Helper::e($iniciais) ?>
                </div>
                <h5 class="fw-bold text-dark mb-1"><?= Helper::e((string) $usuario['nome']) ?></h5>
                <p class="text-muted small mb-3"><?= Helper::e((string) $usuario['email']) ?></p>

                <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">
                    <span class="badge bg-light text-dark border px-3 py-2">
                        <i class="bi bi-shield-check text-success me-1"></i> <?= $isAluno ? 'Estudante' : 'Administrador' ?>
                    </span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                        <i class="bi bi-bullseye me-1"></i> Foco: <?= Helper::e($focoAtual) ?>
                    </span>
                </div>

                <hr class="my-3 text-muted opacity-25">

                <div class="text-start small text-secondary">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Membro desde:</span>
                        <span class="fw-semibold text-dark">
                            <?= date('d/m/Y', strtotime((string) $usuario['criado_em'])) ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Status do Acesso:</span>
                        <span class="badge bg-success text-white">Ativo & Seguro</span>
                    </div>
                    <?php if (!empty($usuario['telefone'])): ?>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Telefone:</span>
                            <span class="fw-semibold text-dark"><?= Helper::e((string) $usuario['telefone']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Dica Educacional -->
        <div class="card border-0 shadow-sm p-3" style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border-left: 4px solid #059669 !important;">
            <div class="d-flex gap-3 align-items-start">
                <div class="fs-4 text-success flex-shrink-0">
                    <i class="bi bi-lightbulb-fill"></i>
                </div>
                <div class="small">
                    <strong class="text-dark d-block mb-1">Dica de Preparação:</strong>
                    <span class="text-secondary">
                        Manter seu vestibular de foco atualizado nos ajuda a direcionar os simulados e artigos teóricos mais relevantes para a sua meta de aprovação!
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna Principal: Formulários de Configuração -->
    <div class="col-lg-8">
        <!-- Card 1: Dados Pessoais & Preferências -->
        <div class="card card-custom shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-success fs-5"></i>
                <h5 class="mb-0 fw-bold text-dark">Informações do Perfil</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/configuracoes/salvar-perfil') ?>" method="POST" novalidate>
                    <?= Csrf::input() ?>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="nome" class="form-label fw-semibold">
                                Nome Completo <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="nome" 
                                       name="nome" 
                                       value="<?= Helper::e((string) $usuario['nome']) ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label for="vestibular_foco" class="form-label fw-semibold">
                                Vestibular de Foco <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-bullseye"></i></span>
                                <select class="form-select" id="vestibular_foco" name="vestibular_foco" required>
                                    <option value="ENEM" <?= $focoAtual === 'ENEM' ? 'selected' : '' ?>>ENEM</option>
                                    <option value="FUVEST" <?= $focoAtual === 'FUVEST' ? 'selected' : '' ?>>FUVEST (USP)</option>
                                    <option value="UNICAMP" <?= $focoAtual === 'UNICAMP' ? 'selected' : '' ?>>UNICAMP</option>
                                    <option value="UNESP" <?= $focoAtual === 'UNESP' ? 'selected' : '' ?>>UNESP (Vunesp)</option>
                                    <option value="TODOS" <?= $focoAtual === 'TODOS' ? 'selected' : '' ?>>Todos os Vestibulares</option>
                                    <option value="OUTROS" <?= $focoAtual === 'OUTROS' ? 'selected' : '' ?>>Outros Concursos</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="email" class="form-label fw-semibold">
                                E-mail de Acesso <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?= Helper::e((string) $usuario['email']) ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label for="telefone" class="form-label fw-semibold">
                                Telefone / WhatsApp <span class="text-muted small">(opcional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="telefone" 
                                       name="telefone" 
                                       placeholder="(11) 99999-9999" 
                                       value="<?= Helper::e((string) ($usuario['telefone'] ?? '')) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="bio" class="form-label fw-semibold">
                            Meta de Estudos / Biografia <span class="text-muted small">(opcional, até 255 caracteres)</span>
                        </label>
                        <textarea class="form-control" 
                                  id="bio" 
                                  name="bio" 
                                  rows="2" 
                                  placeholder="Ex: Estudando para Medicina na USP; focado em Biologia e Redação Nota 1000..."><?= Helper::e((string) ($usuario['bio'] ?? '')) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-check2-circle fs-5"></i> Salvar Informações
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card 2: Segurança & Redefinição de Senha -->
        <div class="card card-custom shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                <h5 class="mb-0 fw-bold text-dark">Segurança & Alteração de Senha</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/configuracoes/alterar-senha') ?>" method="POST" novalidate>
                    <?= Csrf::input() ?>

                    <div class="mb-3">
                        <label for="senha_atual" class="form-label fw-semibold">
                            Senha Atual <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" 
                                   class="form-control" 
                                   id="senha_atual" 
                                   name="senha_atual" 
                                   placeholder="Digite sua senha atual para confirmar" 
                                   required>
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="senha_atual">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="nova_senha" class="form-label fw-semibold">
                                Nova Senha <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" 
                                       class="form-control" 
                                       id="nova_senha" 
                                       name="nova_senha" 
                                       placeholder="Mínimo de 6 caracteres" 
                                       required>
                                <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="nova_senha">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="confirmar_senha" class="form-label fw-semibold">
                                Confirmar Nova Senha <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-shield-check"></i></span>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirmar_senha" 
                                       name="confirmar_senha" 
                                       placeholder="Repita a nova senha" 
                                       required>
                                <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="confirmar_senha">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 pt-2">
                        <div class="small text-muted">
                            <i class="bi bi-info-circle text-primary me-1"></i> As senhas são criptografadas com hash nativo Bcrypt (RNF04).
                        </div>
                        <button type="submit" class="btn btn-outline-danger px-4 d-inline-flex align-items-center gap-2">
                            <i class="bi bi-arrow-repeat"></i> Atualizar Senha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Alternador visual para exibir/ocultar senha
    document.querySelectorAll('.btn-toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });
});
</script>

<?php
require dirname(__DIR__) . '/layout/footer.php';
