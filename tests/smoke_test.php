<?php

declare(strict_types=1);

/**
 * Vest+ Platform - Smoke Test & Quality Assurance Suite
 * 
 * Script autônomo para execução via PHP CLI sem dependências externas.
 * Valida a integridade do banco de dados, conexão PDO, segurança anti-XSS/CSRF,
 * integridade referencial com transações PDO e motor de resolução assíncrona.
 * 
 * Uso: php tests/smoke_test.php
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

use Config\Database;
use App\Core\Helper;
use App\Core\Csrf;
use App\Models\Questao;
use App\Models\Artigo;

// Inicializa a sessão antes de qualquer output no terminal
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Cores ANSI para saída no terminal
$ansiGreen = "\033[32m";
$ansiRed   = "\033[31m";
$ansiBlue  = "\033[34m";
$ansiBold  = "\033[1m";
$ansiReset = "\033[0m";

echo PHP_EOL . "{$ansiBold}{$ansiBlue}======================================================={$ansiReset}" . PHP_EOL;
echo "{$ansiBold}   Vest+ Platform - Smoke Test & QA Suite (v1.0.0){$ansiReset}" . PHP_EOL;
echo "{$ansiBold}{$ansiBlue}======================================================={$ansiReset}" . PHP_EOL . PHP_EOL;

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function runTest(string $description, callable $callback, &$total, &$passed, &$failed, $green, $red, $reset): void
{
    $total++;
    echo sprintf("[%02d] %-60s ", $total, $description);
    try {
        $result = $callback();
        if ($result === true || $result === null) {
            $passed++;
            echo "{$green}[PASS]{$reset}" . PHP_EOL;
        } else {
            $failed++;
            echo "{$red}[FAIL]{$reset} (Retornou falso)" . PHP_EOL;
        }
    } catch (\Throwable $e) {
        $failed++;
        echo "{$red}[FAIL]{$reset} ({$e->getMessage()})" . PHP_EOL;
    }
}

// -----------------------------------------------------------------------------
// TESTE 1: Conexão ativa via PDO
// -----------------------------------------------------------------------------
runTest("Conexão com Banco de Dados via PDO (MySQL/MariaDB)", function () {
    $pdo = Database::getConnection();
    $stmt = $pdo->query('SELECT 1');
    return (int) $stmt->fetchColumn() === 1;
}, $totalTests, $passedTests, $failedTests, $ansiGreen, $ansiRed, $ansiReset);

// -----------------------------------------------------------------------------
// TESTE 2: Existência das tabelas relacionais do schema
// -----------------------------------------------------------------------------
runTest("Existência das 7 tabelas do banco de dados (schema & pivots)", function () {
    $pdo = Database::getConnection();
    $tabelasNecessarias = [
        'usuarios',
        'disciplinas',
        'categorias',
        'artigos',
        'questoes',
        'artigo_categorias',
        'questao_categorias'
    ];
    
    $stmt = $pdo->query('SHOW TABLES');
    $tabelasExistentes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tabelasNecessarias as $tab) {
        if (!in_array($tab, $tabelasExistentes, true)) {
            throw new \Exception("Tabela '{$tab}' não encontrada no banco.");
        }
    }
    return true;
}, $totalTests, $passedTests, $failedTests, $ansiGreen, $ansiRed, $ansiReset);

// -----------------------------------------------------------------------------
// TESTE 3: Usuários Seeds com autenticação Bcrypt
// -----------------------------------------------------------------------------
runTest("Existência e integridade das contas seed (Admin e Aluno)", function () {
    $pdo = Database::getConnection();
    
    // Valida admin
    $stmtAdmin = $pdo->prepare('SELECT id, senha, tipo FROM usuarios WHERE email = :email LIMIT 1');
    $stmtAdmin->execute([':email' => 'admin@vestplus.com.br']);
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    if (!$admin || $admin['tipo'] !== 'admin' || !password_verify('admin123', (string) $admin['senha'])) {
        throw new \Exception("Conta de administrador padrão não configurada corretamente.");
    }

    // Valida aluno
    $stmtAluno = $pdo->prepare('SELECT id, senha, tipo FROM usuarios WHERE email = :email LIMIT 1');
    $stmtAluno->execute([':email' => 'aluno@vestplus.com.br']);
    $aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC);
    if (!$aluno || $aluno['tipo'] !== 'aluno' || !password_verify('aluno123', (string) $aluno['senha'])) {
        throw new \Exception("Conta de estudante padrão não configurada corretamente.");
    }

    return true;
}, $totalTests, $passedTests, $failedTests, $ansiGreen, $ansiRed, $ansiReset);

// -----------------------------------------------------------------------------
// TESTE 4: Sanitização XSS e Tratamento de Strings
// -----------------------------------------------------------------------------
runTest("Sanitização de saídas com htmlspecialchars (Anti-XSS)", function () {
    $payloadMalicioso = "<script>alert('XSS')</script>&\"'";
    $saidaSanitizada = Helper::e($payloadMalicioso);

    if (str_contains($saidaSanitizada, '<script>') || !str_contains($saidaSanitizada, '&lt;script&gt;')) {
        throw new \Exception("Falha na sanitização HTML contra scripts maliciosos.");
    }
    return true;
}, $totalTests, $passedTests, $failedTests, $ansiGreen, $ansiRed, $ansiReset);

// -----------------------------------------------------------------------------
// TESTE 5: Proteção e Validação de Tokens CSRF
// -----------------------------------------------------------------------------
runTest("Geração e validação estrita de tokens CSRF", function () {
    $token = Csrf::getToken();
    if (strlen($token) !== 64) {
        throw new \Exception("Token CSRF gerado com tamanho inesperado: " . strlen($token));
    }

    if (!Csrf::validateToken($token)) {
        throw new \Exception("Validação com o token legítimo falhou.");
    }

    if (Csrf::validateToken('token_forjado_invalido')) {
        throw new \Exception("Validação aceitou um token falso.");
    }

    return true;
}, $totalTests, $passedTests, $failedTests, $ansiGreen, $ansiRed, $ansiReset);

// -----------------------------------------------------------------------------
// TESTE 6: Motor Assíncrono de Questões (Validação de Gabarito)
// -----------------------------------------------------------------------------
runTest("Motor de conferência de gabarito e resolução comentada", function () {
    // Questão 1 cadastrada nos seeds (gabarito C)
    $resAcerto = Questao::verificarResposta(1, 'C');
    if (!$resAcerto || !$resAcerto['correta'] || $resAcerto['gabarito'] !== 'C' || empty($resAcerto['explicacao'])) {
        throw new \Exception("Avaliação de resposta correta da Questão #1 falhou.");
    }

    $resErro = Questao::verificarResposta(1, 'A');
    if (!$resErro || $resErro['correta'] || $resErro['gabarito'] !== 'C') {
        throw new \Exception("Avaliação de resposta incorreta da Questão #1 falhou.");
    }

    return true;
}, $totalTests, $passedTests, $failedTests, $ansiGreen, $ansiRed, $ansiReset);

// -----------------------------------------------------------------------------
// TESTE 7: Transações PDO Atômicas e Integridade ON DELETE CASCADE
// -----------------------------------------------------------------------------
runTest("Transações PDO seguras e deleção em cascata (ON DELETE CASCADE)", function () {
    $pdo = Database::getConnection();

    // Cria artigo temporário com categorias associadas
    $artigoTemp = [
        'titulo'        => 'Artigo Smoke Test Temporário',
        'slug'          => 'artigo-smoke-test-' . uniqid(),
        'disciplina_id' => 1,
        'conteudo'      => 'Conteúdo de teste para verificação de transação e cascade.'
    ];
    $id = Artigo::create($artigoTemp, [1, 2]);

    // Verifica se os registros foram criados na tabela associativa
    $stmtPivot = $pdo->prepare('SELECT COUNT(*) FROM artigo_categorias WHERE artigo_id = :id');
    $stmtPivot->execute([':id' => $id]);
    if ((int) $stmtPivot->fetchColumn() !== 2) {
        throw new \Exception("Vínculos N:N não foram persistidos na tabela pivot.");
    }

    // Exclui o artigo e valida se a tabela associativa foi limpa
    Artigo::delete($id);

    $stmtPivot->execute([':id' => $id]);
    if ((int) $stmtPivot->fetchColumn() !== 0) {
        throw new \Exception("Registros órfãos detectados na tabela pivot após delete.");
    }

    return true;
}, $totalTests, $passedTests, $failedTests, $ansiGreen, $ansiRed, $ansiReset);

// -----------------------------------------------------------------------------
// Relatório Final
// -----------------------------------------------------------------------------
echo PHP_EOL . "-------------------------------------------------------" . PHP_EOL;
echo sprintf("Resultado: %s%d APROVADOS%s | %s%d FALHAS%s (Total: %d testes)" . PHP_EOL,
    $ansiGreen, $passedTests, $ansiReset,
    $failedTests > 0 ? $ansiRed : '', $failedTests, $ansiReset,
    $totalTests
);
echo "-------------------------------------------------------" . PHP_EOL . PHP_EOL;

if ($failedTests > 0) {
    echo "{$ansiRed}ERRO: O checklist de qualidade detectou pendências críticas. Corrija antes da release.{$ansiReset}" . PHP_EOL;
    exit(1);
} else {
    echo "{$ansiGreen}SUCESSO: Todos os testes de fumaça e segurança foram validados com êxito! Pronto para release.{$ansiReset}" . PHP_EOL;
    exit(0);
}
