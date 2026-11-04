<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\Usuario;

/**
 * Controller responsável pela gestão e administração de usuários da plataforma (Admin RBAC).
 */
final class AdminUsuarioController extends BaseController
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Listagem geral de usuários com filtros por termo de busca e tipo de conta.
     */
    public function index(): void
    {
        $busca = isset($_GET['busca']) && trim((string) $_GET['busca']) !== ''
            ? trim((string) $_GET['busca'])
            : null;

        $tipo = isset($_GET['tipo']) && in_array($_GET['tipo'], ['aluno', 'admin'], true)
            ? (string) $_GET['tipo']
            : null;

        $usuarios = Usuario::getAll($busca, $tipo);
        $totalUsuarios = Usuario::countTotal();
        $totalAlunos = Usuario::countByTipo('aluno');
        $totalAdmins = Usuario::countByTipo('admin');

        $this->render('admin/usuarios/index', [
            'pageTitle'      => 'Gestão de Usuários da Plataforma',
            'usuarios'       => $usuarios,
            'busca'          => $busca,
            'tipo'           => $tipo,
            'totalUsuarios'  => $totalUsuarios,
            'totalAlunos'    => $totalAlunos,
            'totalAdmins'    => $totalAdmins,
        ]);
    }

    /**
     * Altera o papel / privilégio de um usuário ('aluno' ou 'admin').
     */
    public function alterarTipo(int $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/usuarios');
            return;
        }

        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Token de segurança inválido ou expirado. Tente novamente.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $userId = $id > 0 ? $id : (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $novoTipo = (string) ($_POST['tipo'] ?? 'aluno');

        if ($userId <= 0 || !in_array($novoTipo, ['aluno', 'admin'], true)) {
            Flash::error('Parâmetros inválidos para alteração de privilégio.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $currentUser = Auth::user();
        if ((int) ($currentUser['id'] ?? 0) === $userId) {
            Flash::error('Ação bloqueada: você não pode alterar os privilégios da sua própria conta de administrador.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $usuario = Usuario::getById($userId);
        if (!$usuario) {
            Flash::error('Usuário não localizado no banco de dados.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $sucesso = Usuario::updateTipo($userId, $novoTipo);
        if ($sucesso) {
            $label = $novoTipo === 'admin' ? 'Administrador' : 'Aluno';
            Flash::success("Privilégio do usuário '{$usuario['nome']}' alterado com sucesso para {$label}!");
        } else {
            Flash::error('Não foi possível alterar o privilégio do usuário.');
        }

        $this->redirect('/admin/usuarios');
    }

    /**
     * Remove uma conta de usuário do sistema.
     */
    public function delete(int $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/usuarios');
            return;
        }

        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Token de segurança inválido ou expirado. Tente novamente.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $userId = $id > 0 ? $id : (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($userId <= 0) {
            Flash::error('Identificador de usuário inválido para exclusão.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $currentUser = Auth::user();
        if ((int) ($currentUser['id'] ?? 0) === $userId) {
            Flash::error('Ação bloqueada: você não pode excluir a sua própria conta logada.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $usuario = Usuario::getById($userId);
        if (!$usuario) {
            Flash::error('Usuário não localizado no sistema.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $sucesso = Usuario::delete($userId);
        if ($sucesso) {
            Flash::success("Conta do usuário '{$usuario['nome']}' ({$usuario['email']}) removida com êxito!");
        } else {
            Flash::error('Erro ao excluir usuário do banco de dados.');
        }

        $this->redirect('/admin/usuarios');
    }
}
