<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helper;

/**
 * Controller Base com utilitários de renderização, controle de acesso e redirecionamento.
 */
abstract class BaseController
{
    /**
     * Garante que apenas administradores autenticados tenham acesso ao controller.
     */
    protected function requireAdmin(): void
    {
        Auth::requireAdmin();
    }

    /**
     * Garante que haja um usuário autenticado (aluno ou administrador).
     */
    protected function requireAuth(): void
    {
        Auth::requireAuth();
    }

    /**
     * Retorna uma resposta formatada em JSON com cabeçalhos apropriados.
     *
     * @param mixed $data
     * @param int $status
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Renderiza uma view PHP passando os dados necessários.
     *
     * @param string $view Caminho relativo da view dentro de src/Views (ex: 'admin/artigos/index')
     * @param array<string, mixed> $data Dados a serem extraídos no escopo da view
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Erro: View '{$view}' não encontrada em {$viewFile}.";
            exit;
        }

        require $viewFile;
    }

    /**
     * Redireciona para um caminho interno e encerra a execução.
     */
    protected function redirect(string $path): void
    {
        $url = Helper::url($path);
        header("Location: {$url}");
        exit;
    }
}
