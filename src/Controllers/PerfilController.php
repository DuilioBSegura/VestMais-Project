<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\Usuario;

/**
 * Controller responsável pelas Configurações e Gerenciamento de Perfil do Usuário autenticado.
 */
final class PerfilController extends BaseController
{
    public function __construct()
    {
        $this->requireAuth();
    }

    /**
     * Exibe o formulário de configurações da conta e perfil.
     */
    public function index(): void
    {
        $currentUser = Auth::user();
        $userId = (int) ($currentUser['id'] ?? 0);

        $usuario = Usuario::getById($userId);
        if (!$usuario) {
            Flash::error('Usuário não localizado no sistema.');
            $this->redirect(Auth::isAdmin() ? '/admin/dashboard' : '/aluno/dashboard');
            return;
        }

        $this->render('usuario/configuracoes', [
            'pageTitle' => 'Configurações de Conta e Perfil',
            'usuario'   => $usuario,
        ]);
    }

    /**
     * Atualiza as informações cadastrais e preferências de estudo do perfil.
     */
    public function salvarPerfil(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/configuracoes');
            return;
        }

        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Token de segurança inválido ou expirado. Tente novamente.');
            $this->redirect('/configuracoes');
            return;
        }

        $currentUser = Auth::user();
        $userId = (int) ($currentUser['id'] ?? 0);

        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $vestibularFoco = trim((string) ($_POST['vestibular_foco'] ?? 'ENEM'));
        $bio = trim((string) ($_POST['bio'] ?? ''));
        $telefone = trim((string) ($_POST['telefone'] ?? ''));

        // Validações de consistência
        if (mb_strlen($nome, 'UTF-8') < 3 || mb_strlen($nome, 'UTF-8') > 150) {
            Flash::error('O nome completo deve conter entre 3 e 150 caracteres.');
            $this->redirect('/configuracoes');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::error('Por favor, informe um endereço de e-mail válido.');
            $this->redirect('/configuracoes');
            return;
        }

        if (Usuario::emailExistsForOther($email, $userId)) {
            Flash::error('O e-mail informado já está em uso por outro usuário.');
            $this->redirect('/configuracoes');
            return;
        }

        $vestibularesValidos = ['ENEM', 'FUVEST', 'UNICAMP', 'UNESP', 'TODOS', 'OUTROS'];
        if (!in_array(strtoupper($vestibularFoco), $vestibularesValidos, true)) {
            $vestibularFoco = 'ENEM';
        }

        $bio = $bio !== '' ? mb_substr($bio, 0, 255, 'UTF-8') : null;
        $telefone = $telefone !== '' ? mb_substr($telefone, 0, 20, 'UTF-8') : null;

        $sucesso = Usuario::updatePerfil($userId, $nome, $email, $vestibularFoco, $bio, $telefone);

        if ($sucesso) {
            Auth::updateUser([
                'nome'            => $nome,
                'email'           => $email,
                'vestibular_foco' => $vestibularFoco,
            ]);
            Flash::success('Suas informações de perfil foram atualizadas com sucesso!');
        } else {
            Flash::error('Ocorreu um erro ao atualizar os dados do perfil. Tente novamente.');
        }

        $this->redirect('/configuracoes');
    }

    /**
     * Processa a alteração segura de senha do usuário.
     */
    public function alterarSenha(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/configuracoes');
            return;
        }

        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Token de segurança inválido ou expirado. Tente novamente.');
            $this->redirect('/configuracoes');
            return;
        }

        $currentUser = Auth::user();
        $userId = (int) ($currentUser['id'] ?? 0);

        $senhaAtual = (string) ($_POST['senha_atual'] ?? '');
        $novaSenha = (string) ($_POST['nova_senha'] ?? '');
        $confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

        if ($senhaAtual === '' || $novaSenha === '' || $confirmarSenha === '') {
            Flash::error('Todos os campos de senha são obrigatórios.');
            $this->redirect('/configuracoes');
            return;
        }

        $usuario = Usuario::getById($userId);
        if (!$usuario || !password_verify($senhaAtual, (string) $usuario['senha'])) {
            Flash::error('A senha atual informada está incorreta.');
            $this->redirect('/configuracoes');
            return;
        }

        if (mb_strlen($novaSenha, 'UTF-8') < 6) {
            Flash::error('A nova senha deve possuir no mínimo 6 caracteres.');
            $this->redirect('/configuracoes');
            return;
        }

        if ($novaSenha !== $confirmarSenha) {
            Flash::error('A confirmação da nova senha não confere com a nova senha digitada.');
            $this->redirect('/configuracoes');
            return;
        }

        $hashNovaSenha = password_hash($novaSenha, PASSWORD_BCRYPT);
        $sucesso = Usuario::updateSenha($userId, $hashNovaSenha);

        if ($sucesso) {
            Flash::success('Sua senha foi redefinida com sucesso! Utilize-a em seu próximo login.');
        } else {
            Flash::error('Não foi possível atualizar a senha no momento. Tente novamente.');
        }

        $this->redirect('/configuracoes');
    }
}
