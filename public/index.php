<?php

declare(strict_types=1);

/**
 * Vest+ Platform - Entry Point Mínimo (Sprint 1)
 *
 * Responsável pela inicialização da configuração do ambiente,
 * validação da conexão segura via PDO e renderização do status do setup.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

use Config\Database;

header('Content-Type: text/html; charset=UTF-8');

$dbStatus = false;
$dbMessage = '';
$phpVersion = PHP_VERSION;

try {
    $pdo = Database::getConnection();
    // Teste de consulta para validação da sessão do banco
    $stmt = $pdo->query('SELECT DATABASE() AS current_db');
    $result = $stmt->fetch();
    $currentDb = $result['current_db'] ?? DB_NAME;

    $dbStatus = true;
    $dbMessage = sprintf('Conexão PDO estabelecida com sucesso no banco "%s".', htmlspecialchars((string) $currentDb, ENT_QUOTES, 'UTF-8'));
} catch (PDOException $e) {
    $dbStatus = false;
    $dbMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello World - Vest+ Platform Ready</title>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #38bdf8;
            --success: #10b981;
            --error: #ef4444;
            --border: #334155;
            --font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: var(--font-family);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2.5rem;
            max-width: 580px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--accent);
            letter-spacing: -0.025em;
        }
        p.subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 1.75rem;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.875rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .status-badge.success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-badge.error {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .details-box {
            background: #0f172a;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.25rem;
            font-size: 0.875rem;
            line-height: 1.6;
        }
        .details-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #1e293b;
            padding: 0.4rem 0;
        }
        .details-item:last-child {
            border-bottom: none;
        }
        .details-label {
            color: var(--text-muted);
        }
        .details-val {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            color: #e2e8f0;
        }
        .db-msg {
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid #334155;
            font-size: 0.825rem;
            color: var(--text-muted);
            word-break: break-all;
        }
        .instruction {
            margin-top: 1rem;
            padding: 0.75rem;
            background: rgba(56, 189, 248, 0.08);
            border: 1px dashed rgba(56, 189, 248, 0.3);
            border-radius: 6px;
            font-size: 0.8rem;
            color: #bae6fd;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Hello World - Vest+ Platform Ready</h1>
        <p class="subtitle">Sprint 1: Setup da Codebase & Arquitetura Base</p>

        <?php if ($dbStatus): ?>
            <div class="status-badge success">
                <span>●</span> Banco de Dados Conectado com Sucesso
            </div>
        <?php else: ?>
            <div class="status-badge error">
                <span>●</span> Aguardando Banco de Dados / MySQL Offline
            </div>
        <?php endif; ?>

        <div class="details-box">
            <div class="details-item">
                <span class="details-label">Ambiente:</span>
                <span class="details-val"><?= htmlspecialchars(APP_ENV, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="details-item">
                <span class="details-label">Versão PHP:</span>
                <span class="details-val"><?= htmlspecialchars($phpVersion, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="details-item">
                <span class="details-label">Driver PDO:</span>
                <span class="details-val">MySQL (<?= htmlspecialchars(DB_CHARSET, ENT_QUOTES, 'UTF-8') ?>)</span>
            </div>
            <div class="details-item">
                <span class="details-label">Host / Porta:</span>
                <span class="details-val"><?= htmlspecialchars(DB_HOST . ':' . (string) DB_PORT, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="details-item">
                <span class="details-label">Banco Alvo:</span>
                <span class="details-val"><?= htmlspecialchars(DB_NAME, ENT_QUOTES, 'UTF-8') ?></span>
            </div>

            <div class="db-msg">
                <strong>Status da Conexão:</strong><br>
                <?= htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') ?>
            </div>

            <?php if (!$dbStatus): ?>
                <div class="instruction">
                    <strong>Dica de inicialização:</strong> Certifique-se de que o MySQL está ativo no painel do XAMPP e importe o script <code>database/schema.sql</code>.
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
