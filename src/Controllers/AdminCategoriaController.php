<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helper;
use App\Models\Categoria;
use App\Models\Disciplina;

/**
 * Controller responsável pela gestão das Disciplinas e Categorias/Tópicos do Vest+.
 */
final class AdminCategoriaController extends BaseController
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Exibe o painel consolidado de Categorias e Disciplinas com contagens de uso.
     */
    public function index(): void
    {
        $categorias = Categoria::getAllWithCounts();
        $disciplinas = Disciplina::getWithCounts();

        $this->render('admin/categorias/index', [
            'pageTitle'   => 'Gestão de Categorias e Disciplinas',
            'categorias'  => $categorias,
            'disciplinas' => $disciplinas,
        ]);
    }

    /**
     * Salva uma nova categoria de conteúdo/questão.
     */
    public function storeCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/categorias');
            return;
        }

        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Token de segurança inválido ou expirado.');
            $this->redirect('/admin/categorias');
            return;
        }

        $nome = trim((string) ($_POST['nome'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $disciplinaId = (int) ($_POST['disciplina_id'] ?? 0);

        if ($nome === '') {
            Flash::error('O nome da categoria é obrigatório.');
            $this->redirect('/admin/categorias');
            return;
        }

        if ($slug === '') {
            $slug = Helper::slugify($nome);
        } else {
            $slug = Helper::slugify($slug);
        }

        try {
            Categoria::create($nome, $slug, $disciplinaId > 0 ? $disciplinaId : null);
            Flash::success("Categoria '{$nome}' cadastrada e vinculada à disciplina com sucesso!");
        } catch (\Throwable $e) {
            Flash::error('Erro ao cadastrar categoria. Verifique se o nome/slug já existe.');
        }

        $this->redirect('/admin/categorias');
    }

    /**
     * Remove uma categoria existente.
     */
    public function deleteCategoria(int $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/categorias');
            return;
        }

        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Token de segurança inválido ou expirado.');
            $this->redirect('/admin/categorias');
            return;
        }

        $catId = $id > 0 ? $id : (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $categoria = Categoria::getById($catId);

        if (!$categoria) {
            Flash::error('Categoria não encontrada.');
            $this->redirect('/admin/categorias');
            return;
        }

        try {
            Categoria::delete($catId);
            Flash::success("Categoria '{$categoria['nome']}' excluída com sucesso.");
        } catch (\Throwable $e) {
            Flash::error('Não foi possível excluir a categoria.');
        }

        $this->redirect('/admin/categorias');
    }

    /**
     * Salva uma nova disciplina no currículo do vestibular.
     */
    public function storeDisciplina(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/categorias');
            return;
        }

        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Token de segurança inválido ou expirado.');
            $this->redirect('/admin/categorias');
            return;
        }

        $nome = trim((string) ($_POST['nome'] ?? ''));
        $descricao = trim((string) ($_POST['descricao'] ?? ''));

        if ($nome === '') {
            Flash::error('O nome da disciplina é obrigatório.');
            $this->redirect('/admin/categorias');
            return;
        }

        $slug = Helper::slugify($nome);
        $desc = $descricao !== '' ? $descricao : null;

        try {
            Disciplina::create($nome, $slug, $desc);
            Flash::success("Disciplina '{$nome}' cadastrada com sucesso!");
        } catch (\Throwable $e) {
            Flash::error('Erro ao cadastrar disciplina. Verifique se o nome já está em uso.');
        }

        $this->redirect('/admin/categorias');
    }

    /**
     * Remove uma disciplina e seus vínculos em cascata.
     */
    public function deleteDisciplina(int $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/categorias');
            return;
        }

        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Token de segurança inválido ou expirado.');
            $this->redirect('/admin/categorias');
            return;
        }

        $discId = $id > 0 ? $id : (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $disciplina = Disciplina::getById($discId);

        if (!$disciplina) {
            Flash::error('Disciplina não encontrada.');
            $this->redirect('/admin/categorias');
            return;
        }

        try {
            Disciplina::delete($discId);
            Flash::success("Disciplina '{$disciplina['nome']}' removida com sucesso.");
        } catch (\Throwable $e) {
            Flash::error('Erro ao excluir disciplina.');
        }

        $this->redirect('/admin/categorias');
    }
}
