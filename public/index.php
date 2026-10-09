<?php

declare(strict_types=1);

/**
 * Vest+ Platform - Front Controller e Roteador Central
 * Plataforma Open-Access de CMS Educacional e Banco de Questões
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

use App\Controllers\AdminArtigoController;
use App\Controllers\AdminDashboardController;
use App\Controllers\AdminQuestaoController;
use App\Controllers\AlunoController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Core\Auth;
use App\Core\Helper;

// Inicia a sessão da aplicação
Auth::start();

// Resolução inteligente da rota suportando subpastas do XAMPP e VirtualHosts
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir = dirname($scriptName);
$parentDir = dirname($scriptDir);

foreach ([$scriptDir, $parentDir] as $prefix) {
    $prefix = rtrim((string) $prefix, '/');
    if ($prefix !== '' && $prefix !== '/' && str_starts_with($requestUri, $prefix)) {
        $requestUri = substr($requestUri, strlen($prefix));
    }
}

$path = '/' . trim($requestUri, '/');
if ($path === '//') {
    $path = '/';
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

// Roteamento
switch ($path) {
    // --- LANDING PAGE PÚBLICA ---
    case '/':
        $controller = new HomeController();
        $controller->index();
        break;

    // --- AUTENTICAÇÃO E CADASTRO UNIFICADOS ---
    case '/login':
        $controller = new AuthController();
        $controller->login();
        break;

    case '/cadastro':
    case '/register':
        $controller = new AuthController();
        $controller->register();
        break;

    case '/logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    // --- PAINEL ADMINISTRATIVO ---
    case '/admin':
    case '/admin/dashboard':
        $controller = new AdminDashboardController();
        $controller->dashboard();
        break;

    case '/admin/artigos':
        $controller = new AdminArtigoController();
        $controller->index();
        break;

    case '/admin/artigos/criar':
    case '/admin/artigos/create':
        $controller = new AdminArtigoController();
        if ($method === 'POST') {
            $controller->store();
        } else {
            $controller->create();
        }
        break;

    case '/admin/artigos/editar':
    case '/admin/artigos/edit':
        $controller = new AdminArtigoController();
        if ($method === 'POST') {
            $controller->update($id);
        } else {
            $controller->edit($id);
        }
        break;

    case '/admin/artigos/excluir':
    case '/admin/artigos/delete':
        $controller = new AdminArtigoController();
        $controller->delete($id);
        break;

    case '/admin/questoes':
        $controller = new AdminQuestaoController();
        $controller->index();
        break;

    case '/admin/questoes/criar':
    case '/admin/questoes/create':
        $controller = new AdminQuestaoController();
        if ($method === 'POST') {
            $controller->store();
        } else {
            $controller->create();
        }
        break;

    case '/admin/questoes/editar':
    case '/admin/questoes/edit':
        $controller = new AdminQuestaoController();
        if ($method === 'POST') {
            $controller->update($id);
        } else {
            $controller->edit($id);
        }
        break;

    case '/admin/questoes/excluir':
    case '/admin/questoes/delete':
        $controller = new AdminQuestaoController();
        $controller->delete($id);
        break;

    // --- ÁREA DO ALUNO ---
    case '/aluno':
    case '/aluno/dashboard':
        $controller = new AlunoController();
        $controller->dashboard();
        break;

    case '/aluno/disciplina':
        $controller = new AlunoController();
        $controller->listarArtigos($id > 0 ? $id : null);
        break;

    case '/aluno/artigo':
        $controller = new AlunoController();
        $controller->lerArtigo();
        break;

    case '/aluno/questoes':
        $controller = new AlunoController();
        $controller->bancoQuestoes();
        break;

    // --- API DE RESOLUÇÃO ASSÍNCRONA ---
    case '/api/questoes/verificar':
    case '/api/verificar-questao':
    case '/api/verificar_questao.php':
        $controller = new AlunoController();
        $controller->verificarQuestao();
        break;

    // --- 404 NOT FOUND ---
    default:
        http_response_code(404);
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Página Não Encontrada (404) - Vest+</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
            <link rel="stylesheet" href="<?= Helper::url('/css/style.css') ?>">
        </head>
        <body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
            <div class="text-center p-4">
                <i class="bi bi-compass text-success" style="font-size: 4rem;"></i>
                <h1 class="display-5 fw-bold text-dark mt-2">404</h1>
                <h4 class="text-secondary mb-3">Página Não Encontrada</h4>
                <p class="text-muted mb-4">A rota solicitada <code><?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?></code> não existe.</p>
                <a href="<?= Helper::url('/') ?>" class="btn btn-primary">
                    <i class="bi bi-house-door me-1"></i> Ir para a Página Inicial
                </a>
            </div>
        </body>
        </html>
        <?php
        break;
}
