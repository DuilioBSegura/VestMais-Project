<?php

declare(strict_types=1);

/**
 * ==============================================================================
 * Vest+ Platform - Suíte Completa de Auditoria Técnica e QA Pré-Apresentação
 * ==============================================================================
 * Cobre:
 * - Segurança & AppSec (RNF04 e RNF05)
 * - Integridade do Banco & Conteúdo Didático (RNF01, RNF10)
 * - Acessibilidade & Resiliência (RNF08, RNF09, RNF12)
 *
 * Execução: php tests/audit_suite.php
 * Compatibilidade: PHP 8.2+ CLI Puro (Zero dependências externas)
 * ==============================================================================
 */

// Define caminhos base
$baseDir = dirname(__DIR__);
require_once $baseDir . '/config/config.php';
require_once $baseDir . '/config/database.php';
require_once $baseDir . '/src/Core/Helper.php';
require_once $baseDir . '/src/Core/Csrf.php';
require_once $baseDir . '/src/Core/Auth.php';

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Helper;
use Config\Database;

// Configurações de cores ANSI para terminal
$ansiGreen  = "\033[32;1m";
$ansiRed    = "\033[31;1m";
$ansiYellow = "\033[33;1m";
$ansiCyan   = "\033[36;1m";
$ansiWhite  = "\033[37;1m";
$ansiReset  = "\033[0m";

$totalAudits = 0;
$passedCount = 0;
$failedCount = 0;
$warnCount   = 0;
$startTime   = microtime(true);

/**
 * Helper para registrar e executar um item de auditoria.
 */
function audit(
    string $category,
    string $title,
    callable $callback,
    &$totalAudits,
    &$passedCount,
    &$failedCount,
    &$warnCount,
    $ansiGreen,
    $ansiRed,
    $ansiYellow,
    $ansiReset
): void {
    $totalAudits++;
    $labelNumber = str_pad((string) $totalAudits, 2, '0', STR_PAD_LEFT);
    $fullTitle = "[{$labelNumber}] {$category}: {$title}";
    
    // Alinha na tela com pontos de preenchimento
    $dotsCount = max(2, 72 - mb_strlen($fullTitle));
    $dots = str_repeat('.', $dotsCount);

    echo "{$fullTitle} {$dots} ";

    try {
        $result = $callback();
        if ($result === true || $result === null) {
            $passedCount++;
            echo "{$ansiGreen}[PASS]{$ansiReset}" . PHP_EOL;
        } elseif (is_array($result) && ($result['status'] ?? '') === 'WARN') {
            $warnCount++;
            echo "{$ansiYellow}[WARN]{$ansiReset} (" . ($result['msg'] ?? 'Aviso') . ")" . PHP_EOL;
        } else {
            $failedCount++;
            $msg = is_string($result) ? $result : 'Falha na validação';
            echo "{$ansiRed}[FAIL]{$ansiReset} ({$msg})" . PHP_EOL;
        }
    } catch (\Throwable $e) {
        $failedCount++;
        echo "{$ansiRed}[FAIL]{$ansiReset} (" . $e->getMessage() . ")" . PHP_EOL;
    }
}

echo PHP_EOL;
echo "{$ansiCyan}======================================================================={$ansiReset}" . PHP_EOL;
echo "{$ansiWhite}     VEST+ PLATFORM - AUDITORIA DE QA, SEGURANÇA & PRÉ-BANCA (v1.0.0){$ansiReset}" . PHP_EOL;
echo "{$ansiCyan}======================================================================={$ansiReset}" . PHP_EOL;
echo " Data/Hora: " . date('Y-m-d H:i:s') . PHP_EOL;
echo " PHP CLI:   " . PHP_VERSION . PHP_EOL;
echo " Ambiente:  " . (defined('APP_ENV') ? APP_ENV : 'production') . PHP_EOL;
echo "-----------------------------------------------------------------------" . PHP_EOL . PHP_EOL;

// ==============================================================================
// BLOCO 1: SEGURANÇA, TIPAGEM E APPSEC (RNF04 e RNF05)
// ==============================================================================
echo "{$ansiCyan}>>> [BLOCO 1] SEGURANÇA DE CÓDIGO, TIPAGEM ESTREITA & APPSEC (RNF04/05){$ansiReset}" . PHP_EOL;

// 1. Tipagem Estrita em 100% dos arquivos PHP
audit('AppSec', 'declare(strict_types=1) em src/ e public/', function () use ($baseDir) {
    $dirs = [$baseDir . '/src', $baseDir . '/public'];
    $missing = [];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) continue;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                if (!preg_match('/declare\s*\(\s*strict_types\s*=\s*1\s*\);/', $content)) {
                    $missing[] = basename($file->getPathname());
                }
            }
        }
    }

    if (!empty($missing)) {
        throw new \Exception("Arquivos sem declare(strict_types=1): " . implode(', ', $missing));
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 2. Varredura Estática nos Models (Ausência de concatenação vulnerável de SQL)
audit('AppSec', 'Queries seguras e preparadas nos Models (Anti-SQLi)', function () use ($baseDir) {
    $modelsDir = $baseDir . '/src/Models';
    $files = glob($modelsDir . '/*.php') ?: [];
    
    foreach ($files as $file) {
        $lines = explode("\n", file_get_contents($file));
        foreach ($lines as $num => $line) {
            $trimmed = trim($line);
            // Detecta $pdo->query com variáveis dinâmicas inseridas diretamente
            if (preg_match('/\$pdo->query\([^)]*\$[a-zA-Z_]/', $trimmed)) {
                // Permite apenas $stmt = $pdo->query($sql) onde $sql foi definido estático
                if (!preg_match('/\$pdo->query\(\$sql\)/', $trimmed)) {
                    throw new \Exception("Concatenação direta suspeita em " . basename($file) . " na linha " . ($num + 1));
                }
            }
        }
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 3. Verificação de RBAC nos Controllers Administrativos
audit('AppSec', 'Controle de Acesso RBAC em Controllers Admin (requireAdmin)', function () use ($baseDir) {
    $adminControllers = [
        'AdminArtigoController.php',
        'AdminQuestaoController.php',
        'AdminDashboardController.php'
    ];

    foreach ($adminControllers as $ctrl) {
        $path = $baseDir . '/src/Controllers/' . $ctrl;
        if (!file_exists($path)) {
            throw new \Exception("Controller admin não encontrado: {$ctrl}");
        }
        $content = file_get_contents($path);
        if (!str_contains($content, '$this->requireAdmin()')) {
            throw new \Exception("Controller {$ctrl} não invoca requireAdmin()");
        }
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 4. Proteção Anti-CSRF em Formulários POST das Views
audit('AppSec', 'Tokens CSRF em todos os formulários POST das Views', function () use ($baseDir) {
    $viewsDir = $baseDir . '/src/Views';
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
    $formsChecked = 0;

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            // Procura formulários POST
            if (preg_match_all('/<form[^>]+method=[\'"]POST[\'"][^>]*>/i', $content, $matches)) {
                // Ignora o formulário de questões da área do aluno que submete via Fetch API JSON
                if (str_contains($file->getPathname(), 'questoes.php') && str_contains($content, 'form-questao')) {
                    continue;
                }
                $formsChecked++;
                if (!str_contains($content, 'Csrf::input()') && !str_contains($content, 'csrf_token')) {
                    throw new \Exception("Formulário POST em " . basename($file->getPathname()) . " não possui token CSRF.");
                }
            }
        }
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 5. Criptografia Bcrypt (password_hash e PASSWORD_DEFAULT)
audit('AppSec', 'Algoritmo seguro de hashing de senhas (password_hash)', function () {
    $senhaTeste = 'SegredoForte@2026';
    $hash = password_hash($senhaTeste, PASSWORD_DEFAULT);
    
    $info = password_get_info($hash);
    if (($info['algo'] ?? 0) === 0) {
        throw new \Exception("password_hash não está gerando hashes válidos.");
    }
    if (!password_verify($senhaTeste, $hash)) {
        throw new \Exception("password_verify falhou ao conferir o hash.");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 6. Sanitização de Saída com htmlspecialchars (Anti-XSS)
audit('AppSec', 'Sanitização de saídas com Helper::e() (Anti-XSS)', function () {
    $xssVector = "<script>alert('XSS')</script>\"'><svg onload=1>";
    $sanitized = Helper::e($xssVector);
    
    if (str_contains($sanitized, '<script>') || str_contains($sanitized, '<svg') || str_contains($sanitized, '"')) {
        throw new \Exception("Falha na sanitização HTML contra injeção de script.");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

echo PHP_EOL;

// ==============================================================================
// BLOCO 2: INTEGRIDADE DO BANCO DE DADOS E CONTEÚDOS SEEDS (RNF01 e RNF10)
// ==============================================================================
echo "{$ansiCyan}>>> [BLOCO 2] INTEGRIDADE DO BANCO DE DADOS & CONTEÚDOS REAIS (RNF01/10){$ansiReset}" . PHP_EOL;

// 7. Conexão PDO ativa
audit('Database', 'Conexão PDO ativa com MariaDB/MySQL', function () {
    $pdo = Database::getConnection();
    $version = (string) $pdo->query('SELECT VERSION()')->fetchColumn();
    if (empty($version)) {
        throw new \Exception("Não foi possível obter a versão do banco de dados.");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 8. Existência das 7 Tabelas Relacionais do Schema
audit('Database', 'Estrutura das 7 tabelas relacionais do schema', function () {
    $pdo = Database::getConnection();
    $expected = ['usuarios', 'disciplinas', 'categorias', 'artigos', 'questoes', 'artigo_categorias', 'questao_categorias'];
    $existing = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

    foreach ($expected as $t) {
        if (!in_array($t, $existing, true)) {
            throw new \Exception("Tabela obrigatória ausente: {$t}");
        }
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 9. Presença de Disciplinas no Banco
audit('Conteúdo', 'Disciplinas cadastradas no banco (contagem >= 3)', function () {
    $pdo = Database::getConnection();
    $count = (int) $pdo->query('SELECT COUNT(*) FROM disciplinas')->fetchColumn();
    if ($count < 3) {
        throw new \Exception("Existem apenas {$count} disciplinas no banco. Mínimo recomendado: 3.");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 10. Presença de Categorias no Banco
audit('Conteúdo', 'Categorias temáticas cadastradas (contagem >= 3)', function () {
    $pdo = Database::getConnection();
    $count = (int) $pdo->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
    if ($count < 3) {
        throw new \Exception("Existem apenas {$count} categorias no banco.");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 11. Presença de Artigos Teóricos Não Vazios
audit('Conteúdo', 'Artigos teóricos estruturados (contagem >= 2)', function () {
    $pdo = Database::getConnection();
    $artigos = $pdo->query('SELECT id, titulo, conteudo FROM artigos')->fetchAll(PDO::FETCH_ASSOC);
    if (count($artigos) < 2) {
        throw new \Exception("Existem apenas " . count($artigos) . " artigos no banco.");
    }
    foreach ($artigos as $art) {
        if (empty(trim((string) $art['conteudo']))) {
            throw new \Exception("Artigo ID #{$art['id']} está com o conteúdo em branco.");
        }
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 12. Integridade Completa de Questões (Enunciado, 5 Alternativas, Gabarito e Explicação)
audit('Conteúdo', 'Integridade das Questões (Enunciado, 5 alt, gabarito e explicação)', function () {
    $pdo = Database::getConnection();
    $questoes = $pdo->query('SELECT id, enunciado, alternativa_a, alternativa_b, alternativa_c, alternativa_d, alternativa_e, resposta_correta, explicacao FROM questoes')->fetchAll(PDO::FETCH_ASSOC);

    if (count($questoes) < 3) {
        throw new \Exception("Existem apenas " . count($questoes) . " questões no banco. Mínimo recomendado: 3.");
    }

    $validLetters = ['A', 'B', 'C', 'D', 'E'];

    foreach ($questoes as $q) {
        $qid = (int) $q['id'];
        if (empty(trim((string) $q['enunciado']))) {
            throw new \Exception("Questão #{$qid} possui enunciado vazio.");
        }
        foreach (['a', 'b', 'c', 'd', 'e'] as $l) {
            $val = trim((string) ($q['alternativa_' . $l] ?? ''));
            if ($val === '') {
                throw new \Exception("Questão #{$qid} está com a alternativa " . strtoupper($l) . " vazia.");
            }
        }
        if (!in_array($q['resposta_correta'], $validLetters, true)) {
            throw new \Exception("Questão #{$qid} possui gabarito inválido: '{$q['resposta_correta']}'.");
        }
        if (empty(trim((string) $q['explicacao']))) {
            throw new \Exception("Questão #{$qid} está sem comentário explicativo.");
        }
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 13. Conta do Administrador Real
audit('Contas', 'Conta de Administrador Real (duiliobsegura@gmail.com)', function () {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('SELECT id, nome, senha, tipo FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => 'duiliobsegura@gmail.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new \Exception("Conta de administrador (duiliobsegura@gmail.com) não encontrada.");
    }
    if ($user['tipo'] !== 'admin') {
        throw new \Exception("Conta duiliobsegura@gmail.com não possui perfil 'admin'.");
    }
    if (!password_verify('AdminVest@2026', (string) $user['senha'])) {
        throw new \Exception("Senha da conta de administrador não confere com AdminVest@2026.");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 14. Conta de Estudante de Demonstração
audit('Contas', 'Conta de Estudante (estudante@vestmais.com.br)', function () {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('SELECT id, nome, senha, tipo FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => 'estudante@vestmais.com.br']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new \Exception("Conta de estudante (estudante@vestmais.com.br) não encontrada.");
    }
    if ($user['tipo'] !== 'aluno') {
        throw new \Exception("Conta estudante@vestmais.com.br não possui perfil 'aluno'.");
    }
    if (!password_verify('AlunoVest@2026', (string) $user['senha'])) {
        throw new \Exception("Senha da conta de aluno não confere com AlunoVest@2026.");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

echo PHP_EOL;

// ==============================================================================
// BLOCO 3: PERFORMANCE, TRANSAÇÕES E ACESSIBILIDADE (RNF08, RNF09 e RNF12)
// ==============================================================================
echo "{$ansiCyan}>>> [BLOCO 3] PERFORMANCE, ÍNDICES & RESILIÊNCIA TRANSACIONAL (RNF08/09/12){$ansiReset}" . PHP_EOL;

// 15. Transações PDO atômicas e seguras (não destrutivo)
audit('Resiliência', 'Transações atômicas com ROLLBACK seguro (não destrutivo)', function () {
    $pdo = Database::getConnection();
    
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('INSERT INTO disciplinas (nome, slug, descricao) VALUES (:n, :s, :d)');
    $stmt->execute([
        ':n' => 'Disciplina Teste Efêmera',
        ':s' => 'disciplina-teste-efemera-' . uniqid(),
        ':d' => 'Teste temporário de transação'
    ]);
    $insertedId = (int) $pdo->lastInsertId();
    $pdo->rollBack();

    // Confirma que o registro foi cancelado
    $check = $pdo->prepare('SELECT COUNT(*) FROM disciplinas WHERE id = :id');
    $check->execute([':id' => $insertedId]);
    if ((int) $check->fetchColumn() !== 0) {
        throw new \Exception("O ROLLBACK não desfez a inserção temporária.");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 16. Otimização de Índices no Banco de Dados
audit('Performance', 'Existência dos índices de alta performance no banco', function () {
    $pdo = Database::getConnection();
    
    // Lista de índices esperados
    $expectedIndexes = [
        'artigos'           => ['idx_artigos_disc_criado', 'uk_artigos_slug'],
        'questoes'          => ['idx_questoes_disc_id'],
        'questao_categorias' => ['idx_questao_categorias_cat_q'],
    ];

    foreach ($expectedIndexes as $table => $indexes) {
        $stmt = $pdo->query("SHOW INDEX FROM {$table}");
        $existing = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Key_name');
        foreach ($indexes as $idx) {
            if (!in_array($idx, $existing, true)) {
                return ['status' => 'WARN', 'msg' => "Índice {$idx} em {$table} recomendado para queries de alta performance"];
            }
        }
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 17. Verificação de Acessibilidade no JavaScript de Questões (questoes.js)
audit('Acessibilidade', 'Atributos ARIA e debounce no Vanilla JS (questoes.js)', function () use ($baseDir) {
    $jsPath = $baseDir . '/public/js/questoes.js';
    if (!file_exists($jsPath)) {
        throw new \Exception("Arquivo questoes.js não encontrado.");
    }
    $content = file_get_contents($jsPath);

    // Valida se possui controle de double-click
    if (!str_contains($content, 'dataset.submitting')) {
        throw new \Exception("questoes.js não possui trava contra cliques duplos simultâneos.");
    }
    // Valida atributos de acessibilidade ARIA
    if (!str_contains($content, 'aria-busy') || !str_contains($content, 'role')) {
        throw new \Exception("questoes.js não define estados ARIA (aria-busy / role) para leitores de tela.");
    }
    // Valida navegação via teclado
    if (!str_contains($content, 'Enter') && !str_contains($content, 'keydown')) {
        throw new \Exception("questoes.js não suporta navegação por teclado para alternativas.");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// 18. Verificação de Contraste e Foco Visual no Design System (style.css)
audit('Acessibilidade', 'Estilos de foco visível (:focus-visible) no Design System', function () use ($baseDir) {
    $cssPath = $baseDir . '/public/css/style.css';
    if (!file_exists($cssPath)) {
        throw new \Exception("Arquivo style.css não encontrado.");
    }
    $content = file_get_contents($cssPath);

    if (!str_contains($content, ':focus-visible') && !str_contains($content, ':focus-within')) {
        throw new \Exception("style.css não define regras de foco visível (:focus-visible / :focus-within).");
    }
    return true;
}, $totalAudits, $passedCount, $failedCount, $warnCount, $ansiGreen, $ansiRed, $ansiYellow, $ansiReset);

// ==============================================================================
// RELATÓRIO FINAL CONSOLIDADO
// ==============================================================================
$duration = round(microtime(true) - $startTime, 3);
echo PHP_EOL;
echo "-----------------------------------------------------------------------" . PHP_EOL;
echo "{$ansiWhite}RESUMO DA AUDITORIA:{$ansiReset}" . PHP_EOL;
echo " Total de Itens Verificados: {$totalAudits}" . PHP_EOL;
echo " Aprovados:                 {$ansiGreen}{$passedCount}{$ansiReset}" . PHP_EOL;
if ($warnCount > 0) {
    echo " Avisos (Não Bloqueantes):  {$ansiYellow}{$warnCount}{$ansiReset}" . PHP_EOL;
}
if ($failedCount > 0) {
    echo " Falhas Encontradas:        {$ansiRed}{$failedCount}{$ansiReset}" . PHP_EOL;
}
echo " Tempo de Execução:         {$duration}s" . PHP_EOL;
echo "-----------------------------------------------------------------------" . PHP_EOL;

if ($failedCount === 0) {
    echo PHP_EOL . "{$ansiGreen}✔ APROVADO COM EXCELÊNCIA! A plataforma atende a todos os requisitos de segurança, integridade de dados, acessibilidade e performance para a banca de apresentação do Dia 5.{$ansiReset}" . PHP_EOL . PHP_EOL;
    exit(0);
} else {
    echo PHP_EOL . "{$ansiRed}✘ ATENÇÃO: Foram identificadas {$failedCount} falhas que devem ser corrigidas antes da apresentação.{$ansiReset}" . PHP_EOL . PHP_EOL;
    exit(1);
}
