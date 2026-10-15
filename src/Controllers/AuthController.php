<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use Config\Database;
use PDO;

/**
 * Controller responsável pelo login, cadastro de novos alunos e logout na plataforma Vest+.
 */
final class AuthController extends BaseController
{
    /**
     * Renderiza o formulário de login unificado ou processa a autenticação via POST.
     */
    public function login(): void
    {
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/aluno/dashboard');
            }
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
                Flash::error('Token de segurança expirado ou inválido. Tente novamente.');
                $this->redirect('/login');
                return;
            }

            $email = trim((string) ($_POST['email'] ?? ''));
            $senha = (string) ($_POST['senha'] ?? '');

            if ($email === '' || $senha === '') {
                Flash::error('Por favor, informe seu e-mail e senha de acesso.');
                $this->redirect('/login');
                return;
            }

            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario || !password_verify($senha, (string) $usuario['senha'])) {
                Flash::error('E-mail ou senha incorretos. Verifique suas credenciais.');
                $this->redirect('/login');
                return;
            }

            Auth::login($usuario);
            Flash::success('Autenticação realizada com sucesso! Bem-vindo(a), ' . $usuario['nome'] . '.');

            if ($usuario['tipo'] === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/aluno/dashboard');
            }
            return;
        }

        $this->render('auth/login', [
            'pageTitle' => 'Acesse sua Conta - Vest+',
        ]);
    }

    /**
     * Renderiza o formulário de cadastro de novo aluno ou processa o registro via POST.
     */
    public function register(): void
    {
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/aluno/dashboard');
            }
            return;
        }

        $errors = [];
        $nome = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
                Flash::error('Token de segurança expirado ou inválido. Tente novamente.');
                $this->redirect('/cadastro');
                return;
            }

            $nome = trim((string) ($_POST['nome'] ?? ''));
            $email = trim(mb_strtolower((string) ($_POST['email'] ?? '')));
            $senha = (string) ($_POST['senha'] ?? '');
            $confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

            // Validações de Entrada
            if ($nome === '' || mb_strlen($nome, 'UTF-8') < 3) {
                $errors['nome'] = 'O nome completo deve conter pelo menos 3 caracteres.';
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Informe um endereço de e-mail válido.';
            } else {
                $pdo = Database::getConnection();
                $checkStmt = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = :email LIMIT 1');
                $checkStmt->bindValue(':email', $email, PDO::PARAM_STR);
                $checkStmt->execute();
                if ($checkStmt->fetchColumn()) {
                    $errors['email'] = 'Este e-mail já está cadastrado. Faça login ou use outro endereço.';
                }
            }

            if (mb_strlen($senha) < 6) {
                $errors['senha'] = 'A senha deve conter no mínimo 6 caracteres.';
            } elseif ($senha !== $confirmarSenha) {
                $errors['confirmar_senha'] = 'A confirmação de senha não coincide com a senha digitada.';
            }

            if (!empty($errors)) {
                Flash::error('Corrija os erros apontados no formulário para concluir o cadastro.');
                $this->render('auth/register', [
                    'pageTitle' => 'Crie sua Conta Gratuita - Vest+',
                    'errors'    => $errors,
                    'nome'      => $nome,
                    'email'     => $email,
                ]);
                return;
            }

            try {
                $pdo = Database::getConnection();
                $hash = password_hash($senha, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, "aluno")');
                $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->bindValue(':senha', $hash, PDO::PARAM_STR);
                $stmt->execute();

                $novoId = (int) $pdo->lastInsertId();

                // Autentica automaticamente o aluno recém-cadastrado
                Auth::login([
                    'id'    => $novoId,
                    'nome'  => $nome,
                    'email' => $email,
                    'tipo'  => 'aluno'
                ]);

                Flash::success('Parabéns, ' . $nome . '! Sua conta foi criada com sucesso. Bons estudos!');
                $this->redirect('/aluno/dashboard');
                return;
            } catch (\Throwable $e) {
                Flash::error('Ocorreu uma falha ao criar a conta: ' . $e->getMessage());
            }
        }

        $this->render('auth/register', [
            'pageTitle' => 'Crie sua Conta Gratuita - Vest+',
            'errors'    => $errors,
            'nome'      => $nome,
            'email'     => $email,
        ]);
    }

    /**
     * Realiza o logout do usuário e redireciona para a página inicial.
     */
    public function logout(): void
    {
        Auth::logout();
        Flash::info('Você saiu da sua conta com segurança.');
        $this->redirect('/');
    }
}
