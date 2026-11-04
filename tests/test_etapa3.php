<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/database.php';

use App\Core\Auth;
use App\Models\Medalha;
use App\Models\Questao;
use App\Models\Usuario;

echo "=== TESTE DE VALIDAÇÃO DA ETAPA 3 (GAMIFICAÇÃO & MEDALHAS) ===" . PHP_EOL;

// 1. Identifica o aluno de teste
$aluno = Usuario::getByEmail('estudante@vestmais.com.br');
if (!$aluno) {
    echo "[FAIL] Conta de estudante não localizada." . PHP_EOL;
    exit(1);
}
$userId = (int) $aluno['id'];
Auth::login($aluno);
echo "[PASS] Aluno autenticado: {$aluno['nome']} (ID: {$userId})." . PHP_EOL;

// 2. Busca uma questão existente para teste
$questoes = Questao::getAll();
if (empty($questoes)) {
    echo "[FAIL] Nenhuma questão cadastrada para testar." . PHP_EOL;
    exit(1);
}
$primeiraQuestao = $questoes[0];
$questaoId = (int) $primeiraQuestao['id'];
$gabarito = (string) $primeiraQuestao['resposta_correta'];

// 3. Testa persistência de resposta e desbloqueio de medalha sob transação
$pdo = Config\Database::getConnection();
$pdo->beginTransaction();

// Limpa histórico prévio temporário se houver
$pdo->prepare('DELETE FROM usuario_respostas WHERE usuario_id = :uid')->execute([':uid' => $userId]);
$pdo->prepare('DELETE FROM usuario_medalhas WHERE usuario_id = :uid')->execute([':uid' => $userId]);

// Registra 1ª resposta correta
$reg1 = Medalha::registrarResposta($userId, $questaoId, $gabarito, true);
if (!$reg1) {
    $pdo->rollBack();
    echo "[FAIL] Falha ao registrar primeira resposta no banco." . PHP_EOL;
    exit(1);
}
echo "[PASS] 1ª resposta registrada com sucesso na tabela usuario_respostas." . PHP_EOL;

// Avalia conquistas (deve desbloquear 'primeiro-passo')
$desbloqueadas = Medalha::verificarNovasConquistas($userId);
$slugs = array_column($desbloqueadas, 'slug');

if (!in_array('primeiro-passo', $slugs, true)) {
    $pdo->rollBack();
    echo "[FAIL] Medalha 'primeiro-passo' não foi desbloqueada na 1ª tentativa." . PHP_EOL;
    exit(1);
}
echo "[PASS] Conquista desbloqueada com sucesso: 'Primeiro Passo'!" . PHP_EOL;

// Valida que ao chamar de novo, não desbloqueia duplicada
$segundaChamada = Medalha::verificarNovasConquistas($userId);
if (!empty($segundaChamada)) {
    $pdo->rollBack();
    echo "[FAIL] Sistema retornou medalhas duplicadas em segunda chamada." . PHP_EOL;
    exit(1);
}
echo "[PASS] Validação de unicidade de conquistas validada (sem duplicidade)." . PHP_EOL;

// Registra mais 2 acertos seguidos (total 3 acertos seguidos)
Medalha::registrarResposta($userId, $questaoId, $gabarito, true);
Medalha::registrarResposta($userId, $questaoId, $gabarito, true);

$desbloqueadasSequencia = Medalha::verificarNovasConquistas($userId);
$slugsSeq = array_column($desbloqueadasSequencia, 'slug');

if (!in_array('sequencia-implacavel', $slugsSeq, true)) {
    $pdo->rollBack();
    echo "[FAIL] Medalha 'sequencia-implacavel' não foi desbloqueada após 3 acertos seguidos." . PHP_EOL;
    exit(1);
}
echo "[PASS] Conquista desbloqueada com sucesso: 'Sequência Implacável' (3 acertos seguidos)!" . PHP_EOL;

// Valida cálculo de estatísticas
$stats = Medalha::getEstatisticasEstudante($userId);
if ($stats['total_respondidas'] !== 3 || $stats['total_acertos'] !== 3 || $stats['taxa_acerto_pct'] !== 100 || $stats['sequencia_atual'] !== 3) {
    $pdo->rollBack();
    echo "[FAIL] Estatísticas do estudante calculadas incorretamente: " . json_encode($stats) . PHP_EOL;
    exit(1);
}
echo "[PASS] Estatísticas do estudante consolidadas: 3 respondidas, 3 acertos (100%), sequência 3 🔥." . PHP_EOL;

// Desfaz a transação de teste
$pdo->rollBack();
echo "[PASS] Transação desfeita com sucesso. Banco de dados limpo e íntegro!" . PHP_EOL;

// 4. Testa renderização da Vitrine de Medalhas
ob_start();
$alunoCtrl = new App\Controllers\AlunoController();
$alunoCtrl->medalhas();
$outMedalhas = ob_get_clean();

if (!str_contains($outMedalhas, 'Vitrine de Conquistas do Estudante') || !str_contains($outMedalhas, 'Catálogo de Medalhas')) {
    echo "[FAIL] Renderização da tela de medalhas falhou." . PHP_EOL;
    exit(1);
}
echo "[PASS] Tela da Vitrine de Conquistas (/aluno/medalhas) renderizada com sucesso (" . strlen($outMedalhas) . " bytes)!" . PHP_EOL;

// 5. Testa renderização do Dashboard do Aluno com card de desempenho e medalhas
ob_start();
$alunoCtrl->dashboard();
$outDashboard = ob_get_clean();

if (!str_contains($outDashboard, 'Minhas Medalhas') || !str_contains($outDashboard, 'Taxa de Acerto')) {
    echo "[FAIL] Dashboard do aluno não incluiu os cards de desempenho e medalhas." . PHP_EOL;
    exit(1);
}
echo "[PASS] Dashboard do Aluno com bloco de Conquistas renderizado com sucesso (" . strlen($outDashboard) . " bytes)!" . PHP_EOL;

echo "=== ETAPA 3 TOTALMENTE VALIDADA COM 100% DE SUCESSO! ===" . PHP_EOL;
