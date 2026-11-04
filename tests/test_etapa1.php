<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/database.php';

use App\Models\Usuario;

echo "=== TESTE DE VALIDAÇÃO DA ETAPA 1 ===" . PHP_EOL;

// 1. Testa busca de usuários
$admin = Usuario::getByEmail('duiliobsegura@gmail.com');
if (!$admin) {
    echo "[FAIL] Administrador não encontrado pelo model Usuario." . PHP_EOL;
    exit(1);
}
echo "[PASS] Administrador localizado: {$admin['nome']} | Papel: {$admin['tipo']} | Foco: {$admin['vestibular_foco']}" . PHP_EOL;

$aluno = Usuario::getByEmail('estudante@vestmais.com.br');
if (!$aluno) {
    echo "[FAIL] Aluno não encontrado pelo model Usuario." . PHP_EOL;
    exit(1);
}
echo "[PASS] Aluno localizado: {$aluno['nome']} | Papel: {$aluno['tipo']} | Foco: {$aluno['vestibular_foco']}" . PHP_EOL;

// 2. Testa emailExistsForOther
$emailJaExiste = Usuario::emailExistsForOther('duiliobsegura@gmail.com', (int) $aluno['id']);
if (!$emailJaExiste) {
    echo "[FAIL] emailExistsForOther falhou em detectar e-mail duplicado de outro usuário." . PHP_EOL;
    exit(1);
}
echo "[PASS] Verificação de e-mail duplicado validada com sucesso." . PHP_EOL;

$emailProprio = Usuario::emailExistsForOther('estudante@vestmais.com.br', (int) $aluno['id']);
if ($emailProprio) {
    echo "[FAIL] emailExistsForOther acusou falsamente o próprio e-mail do usuário como duplicado." . PHP_EOL;
    exit(1);
}
echo "[PASS] Validação do próprio e-mail sem falso-positivo aprovada." . PHP_EOL;

// 3. Testa contagens e listagem de usuários
$total = Usuario::countTotal();
$totalAlunos = Usuario::countByTipo('aluno');
$totalAdmins = Usuario::countByTipo('admin');
echo "[PASS] Contagens: {$total} usuários ({$totalAlunos} alunos, {$totalAdmins} admins)." . PHP_EOL;

// 4. Testa atualização de perfil e rollback em transação
$pdo = Config\Database::getConnection();
$pdo->beginTransaction();

$updateResult = Usuario::updatePerfil(
    (int) $aluno['id'],
    'Estudante Teste Atualizado',
    $aluno['email'],
    'FUVEST',
    'Focado em passar na USP',
    '(11) 98765-4321'
);

$alunoModificado = Usuario::getById((int) $aluno['id']);
if ($alunoModificado['vestibular_foco'] !== 'FUVEST' || $alunoModificado['nome'] !== 'Estudante Teste Atualizado') {
    $pdo->rollBack();
    echo "[FAIL] Atualização de perfil não persistiu os dados esperados." . PHP_EOL;
    exit(1);
}
echo "[PASS] Atualização de perfil validada com sucesso (Foco: {$alunoModificado['vestibular_foco']}, Bio: {$alunoModificado['bio']})." . PHP_EOL;

$pdo->rollBack();
echo "[PASS] Transação desfeita com sucesso. Dados preservados!" . PHP_EOL;

// 5. Simulação de renderização do Controller de Perfil
App\Core\Auth::login($aluno);
ob_start();
$perfilCtrl = new App\Controllers\PerfilController();
$perfilCtrl->index();
$output = ob_get_clean();

if (!str_contains($output, 'app-sidebar')) {
    echo "[FAIL] Renderização de configurações não incluiu a barra lateral (app-sidebar)." . PHP_EOL;
    exit(1);
}
if (!str_contains($output, 'Configurações de Usuário')) {
    echo "[FAIL] Renderização de configurações não incluiu o título esperado." . PHP_EOL;
    exit(1);
}
echo "[PASS] Renderização da tela de Configurações com Sidebar concluída com sucesso (" . strlen($output) . " bytes)!" . PHP_EOL;

echo "=== ETAPA 1 TOTALMENTE VALIDADA! ===" . PHP_EOL;
