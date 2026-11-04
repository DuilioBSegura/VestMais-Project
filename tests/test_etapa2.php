<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/database.php';

use App\Core\Auth;
use App\Models\Categoria;
use App\Models\Disciplina;
use App\Models\Usuario;

echo "=== TESTE DE VALIDAÇÃO DA ETAPA 2 ===" . PHP_EOL;

// 1. Testa autenticação de Admin
$admin = Usuario::getByEmail('duiliobsegura@gmail.com');
if (!$admin || $admin['tipo'] !== 'admin') {
    echo "[FAIL] Conta de admin não identificada." . PHP_EOL;
    exit(1);
}
Auth::login($admin);
echo "[PASS] Autenticado com sucesso como Administrador: {$admin['nome']}." . PHP_EOL;

// 2. Testa métodos de Categoria e Disciplina com transação e Rollback
$pdo = Config\Database::getConnection();
$pdo->beginTransaction();

$catId = Categoria::create('Tópico de Teste Temporário', 'topico-de-teste-temporario-' . uniqid());
if ($catId <= 0) {
    $pdo->rollBack();
    echo "[FAIL] Falha ao criar categoria temporária." . PHP_EOL;
    exit(1);
}
echo "[PASS] Categoria criada com ID: {$catId}." . PHP_EOL;

$discId = Disciplina::create('Disciplina de Teste', 'disciplina-de-teste-' . uniqid(), 'Descrição breve');
if ($discId <= 0) {
    $pdo->rollBack();
    echo "[FAIL] Falha ao criar disciplina temporária." . PHP_EOL;
    exit(1);
}
echo "[PASS] Disciplina criada com ID: {$discId}." . PHP_EOL;

$pdo->rollBack();
echo "[PASS] Rollback de taxonomia executado com sucesso." . PHP_EOL;

// 3. Testa renderização das telas administrativas
ob_start();
$usuarioCtrl = new App\Controllers\AdminUsuarioController();
$usuarioCtrl->index();
$outUsuarios = ob_get_clean();

if (!str_contains($outUsuarios, 'Gestão de Usuários') || !str_contains($outUsuarios, 'duiliobsegura@gmail.com')) {
    echo "[FAIL] Renderização de gestão de usuários falhou." . PHP_EOL;
    exit(1);
}
echo "[PASS] Tela de Gestão de Usuários renderizada com sucesso (" . strlen($outUsuarios) . " bytes)!" . PHP_EOL;

ob_start();
$catCtrl = new App\Controllers\AdminCategoriaController();
$catCtrl->index();
$outCategorias = ob_get_clean();

if (!str_contains($outCategorias, 'Disciplinas & Categorias')) {
    echo "[FAIL] Renderização de categorias falhou." . PHP_EOL;
    exit(1);
}
echo "[PASS] Tela de Disciplinas & Categorias renderizada com sucesso (" . strlen($outCategorias) . " bytes)!" . PHP_EOL;

// 4. Testa renderização do formulário fluído de Artigos (com Live Preview e Toolbar)
ob_start();
$artigoCtrl = new App\Controllers\AdminArtigoController();
$artigoCtrl->create();
$outArtigoForm = ob_get_clean();

if (!str_contains($outArtigoForm, 'formatToolbar') || !str_contains($outArtigoForm, 'livePreviewBox')) {
    echo "[FAIL] Formulário de artigos não contém a toolbar ou a caixa de live preview." . PHP_EOL;
    exit(1);
}
echo "[PASS] Formulário fluído de Artigos com Live Preview e Toolbar validado (" . strlen($outArtigoForm) . " bytes)!" . PHP_EOL;

// 5. Testa renderização do formulário fluído de Questões (com Simulador do Aluno e Gabarito em 1 clique)
ob_start();
$questaoCtrl = new App\Controllers\AdminQuestaoController();
$questaoCtrl->create();
$outQuestaoForm = ob_get_clean();

if (!str_contains($outQuestaoForm, 'Simulador do Aluno') || !str_contains($outQuestaoForm, 'btn-mark-gabarito')) {
    echo "[FAIL] Formulário de questões não contém o simulador do aluno ou botão de gabarito." . PHP_EOL;
    exit(1);
}
echo "[PASS] Formulário fluído de Questões com Simulador ao Vivo validado (" . strlen($outQuestaoForm) . " bytes)!" . PHP_EOL;

echo "=== ETAPA 2 TOTALMENTE VALIDADA COM SUCESSO! ===" . PHP_EOL;
