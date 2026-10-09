<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Controle de Autenticação e Autorização da plataforma Vest+.
 */
final class Auth
{
    private const SESSION_USER_KEY = 'usuario';

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
    }

    /**
     * Retorna os dados do usuário autenticado na sessão.
     *
     * @return array<string, mixed>|null
     */
    public static function user(): ?array
    {
        self::start();
        return $_SESSION[self::SESSION_USER_KEY] ?? null;
    }

    /**
     * Verifica se existe um usuário logado.
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Verifica se o usuário atual possui privilégios de Administrador.
     */
    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user !== null && isset($user['tipo']) && $user['tipo'] === 'admin';
    }

    /**
     * Verifica se o usuário autenticado é um aluno.
     */
    public static function isAluno(): bool
    {
        $user = self::user();
        return $user !== null && isset($user['tipo']) && $user['tipo'] === 'aluno';
    }

    /**
     * Autentica o usuário na sessão.
     *
     * @param array<string, mixed> $user
     */
    public static function login(array $user): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION[self::SESSION_USER_KEY] = [
            'id'    => (int) ($user['id'] ?? 0),
            'nome'  => (string) ($user['nome'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'tipo'  => (string) ($user['tipo'] ?? 'aluno'),
        ];
    }

    /**
     * Encerra a sessão do usuário.
     */
    public static function logout(): void
    {
        self::start();
        unset($_SESSION[self::SESSION_USER_KEY]);
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Garante que haja um usuário autenticado na sessão (seja aluno ou administrador).
     * Se não estiver autenticado, retorna JSON 401 caso seja requisição de API ou redireciona para login.
     */
    public static function requireAuth(): void
    {
        self::start();

        if (!self::check()) {
            $isJson = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
                || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))
                || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')
                || str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/');

            if ($isJson) {
                http_response_code(401);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'sucesso' => false,
                    'erro'    => 'Não autenticado. Faça login para continuar.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            Flash::error('Você precisa estar conectado para acessar esta área.');
            $loginUrl = Helper::url('/login');
            header("Location: {$loginUrl}");
            exit;
        }
    }

    /**
     * Garante que o usuário ativo seja um administrador.
     * Caso não seja, redireciona para a tela de login.
     */
    public static function requireAdmin(): void
    {
        self::start();

        if (!self::isAdmin()) {
            Flash::error('Acesso restrito. Faça login com uma conta de administrador para continuar.');
            $loginUrl = Helper::url('/login');
            header("Location: {$loginUrl}");
            exit;
        }
    }
}
