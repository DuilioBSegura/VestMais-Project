<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helper;
use App\Models\Artigo;
use App\Models\Categoria;
use App\Models\Disciplina;
use Throwable;

/**
 * Controller responsável pelo gerenciamento administrativo de Artigos Teóricos (CRUD).
 */
final class AdminArtigoController extends BaseController
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Lista todos os artigos cadastrados.
     */
    public function index(): void
    {
        $artigos = Artigo::getAll();
        $this->render('admin/artigos/index', [
            'pageTitle' => 'Gerenciamento de Artigos',
            'artigos'   => $artigos,
        ]);
    }

    /**
     * Renderiza o formulário de cadastro de artigo.
     */
    public function create(): void
    {
        $disciplinas = Disciplina::getAll();
        $categorias = Categoria::getAll();

        $this->render('admin/artigos/form', [
            'pageTitle'              => 'Novo Artigo Teórico',
            'isEdit'                 => false,
            'artigo'                 => [
                'id'            => null,
                'titulo'        => '',
                'slug'          => '',
                'disciplina_id' => '',
                'conteudo'      => '',
            ],
            'categoriasSelecionadas' => [],
            'disciplinas'            => $disciplinas,
            'categorias'             => $categorias,
            'errors'                 => [],
        ]);
    }

    /**
     * Processa a criação de um novo artigo via POST.
     */
    public function store(): void
    {
        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Sessão expirada ou token de segurança inválido. Tente novamente.');
            $this->redirect('/admin/artigos');
            return;
        }

        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $disciplinaId = (int) ($_POST['disciplina_id'] ?? 0);
        $conteudo = trim((string) ($_POST['conteudo'] ?? ''));
        $categoriasIds = isset($_POST['categorias']) && is_array($_POST['categorias'])
            ? array_map('intval', $_POST['categorias'])
            : [];

        if ($slug === '' && $titulo !== '') {
            $slug = Helper::slugify($titulo);
        } else {
            $slug = Helper::slugify($slug);
        }

        $errors = $this->validate([
            'titulo'        => $titulo,
            'slug'          => $slug,
            'disciplina_id' => $disciplinaId,
            'conteudo'      => $conteudo,
        ]);

        if (!empty($errors)) {
            Flash::error('Não foi possível salvar o artigo. Corrija os campos indicados abaixo.');
            $disciplinas = Disciplina::getAll();
            $categorias = Categoria::getAll();

            $this->render('admin/artigos/form', [
                'pageTitle'              => 'Novo Artigo Teórico',
                'isEdit'                 => false,
                'artigo'                 => [
                    'id'            => null,
                    'titulo'        => $titulo,
                    'slug'          => $slug,
                    'disciplina_id' => $disciplinaId,
                    'conteudo'      => $conteudo,
                ],
                'categoriasSelecionadas' => $categoriasIds,
                'disciplinas'            => $disciplinas,
                'categorias'             => $categorias,
                'errors'                 => $errors,
            ]);
            return;
        }

        try {
            $validCategorias = Categoria::filterValidIds($categoriasIds);
            Artigo::create([
                'titulo'        => $titulo,
                'slug'          => $slug,
                'disciplina_id' => $disciplinaId,
                'conteudo'      => $conteudo,
            ], $validCategorias);

            Flash::success(sprintf('Artigo "%s" publicado com sucesso!', Helper::truncate($titulo, 50)));
            $this->redirect('/admin/artigos');
        } catch (Throwable $e) {
            Flash::error('Erro ao persistir o artigo no banco de dados: ' . $e->getMessage());
            $this->redirect('/admin/artigos');
        }
    }

    /**
     * Renderiza o formulário de edição de um artigo existente.
     */
    public function edit(int $id): void
    {
        $artigo = Artigo::getById($id);
        if (!$artigo) {
            Flash::error('Artigo não encontrado.');
            $this->redirect('/admin/artigos');
            return;
        }

        $disciplinas = Disciplina::getAll();
        $categorias = Categoria::getAll();

        $this->render('admin/artigos/form', [
            'pageTitle'              => 'Editar Artigo: ' . Helper::truncate((string) $artigo['titulo'], 40),
            'isEdit'                 => true,
            'artigo'                 => $artigo,
            'categoriasSelecionadas' => $artigo['categoria_ids'] ?? [],
            'disciplinas'            => $disciplinas,
            'categorias'             => $categorias,
            'errors'                 => [],
        ]);
    }

    /**
     * Processa a atualização de um artigo existente via POST.
     */
    public function update(int $id): void
    {
        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Sessão expirada ou token de segurança inválido. Tente novamente.');
            $this->redirect('/admin/artigos');
            return;
        }

        $artigoAtual = Artigo::getById($id);
        if (!$artigoAtual) {
            Flash::error('Artigo não encontrado para edição.');
            $this->redirect('/admin/artigos');
            return;
        }

        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $disciplinaId = (int) ($_POST['disciplina_id'] ?? 0);
        $conteudo = trim((string) ($_POST['conteudo'] ?? ''));
        $categoriasIds = isset($_POST['categorias']) && is_array($_POST['categorias'])
            ? array_map('intval', $_POST['categorias'])
            : [];

        if ($slug === '' && $titulo !== '') {
            $slug = Helper::slugify($titulo);
        } else {
            $slug = Helper::slugify($slug);
        }

        $errors = $this->validate([
            'titulo'        => $titulo,
            'slug'          => $slug,
            'disciplina_id' => $disciplinaId,
            'conteudo'      => $conteudo,
        ], $id);

        if (!empty($errors)) {
            Flash::error('Não foi possível atualizar o artigo. Corrija os erros apontados.');
            $disciplinas = Disciplina::getAll();
            $categorias = Categoria::getAll();

            $this->render('admin/artigos/form', [
                'pageTitle'              => 'Editar Artigo: ' . Helper::truncate($titulo, 40),
                'isEdit'                 => true,
                'artigo'                 => [
                    'id'            => $id,
                    'titulo'        => $titulo,
                    'slug'          => $slug,
                    'disciplina_id' => $disciplinaId,
                    'conteudo'      => $conteudo,
                ],
                'categoriasSelecionadas' => $categoriasIds,
                'disciplinas'            => $disciplinas,
                'categorias'             => $categorias,
                'errors'                 => $errors,
            ]);
            return;
        }

        try {
            $validCategorias = Categoria::filterValidIds($categoriasIds);
            Artigo::update($id, [
                'titulo'        => $titulo,
                'slug'          => $slug,
                'disciplina_id' => $disciplinaId,
                'conteudo'      => $conteudo,
            ], $validCategorias);

            Flash::success('Artigo atualizado com sucesso!');
            $this->redirect('/admin/artigos');
        } catch (Throwable $e) {
            Flash::error('Erro ao atualizar o artigo: ' . $e->getMessage());
            $this->redirect('/admin/artigos');
        }
    }

    /**
     * Processa a exclusão de um artigo com validação rigorosa de CSRF.
     */
    public function delete(int $id): void
    {
        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Falha de segurança na requisição (token CSRF inválido). Ação abortada.');
            $this->redirect('/admin/artigos');
            return;
        }

        $artigo = Artigo::getById($id);
        if (!$artigo) {
            Flash::error('Artigo não encontrado para exclusão.');
            $this->redirect('/admin/artigos');
            return;
        }

        try {
            Artigo::delete($id);
            Flash::success(sprintf('Artigo "%s" removido com sucesso!', Helper::truncate((string) $artigo['titulo'], 50)));
        } catch (Throwable $e) {
            Flash::error('Falha ao excluir o artigo: ' . $e->getMessage());
        }

        $this->redirect('/admin/artigos');
    }

    /**
     * Valida os campos obrigatórios e unicidade de regras de negócio.
     *
     * @param array<string, mixed> $data
     * @param int|null $ignoreId
     * @return array<string, string>
     */
    private function validate(array $data, ?int $ignoreId = null): array
    {
        $errors = [];

        if (empty($data['titulo'])) {
            $errors['titulo'] = 'O título do artigo é obrigatório.';
        } elseif (mb_strlen((string) $data['titulo'], 'UTF-8') < 3) {
            $errors['titulo'] = 'O título deve conter pelo menos 3 caracteres.';
        }

        if (empty($data['slug'])) {
            $errors['slug'] = 'O slug identificador é obrigatório.';
        } elseif (Artigo::slugExists((string) $data['slug'], $ignoreId)) {
            $errors['slug'] = 'Este slug já está em uso por outro artigo. Escolha outro valor.';
        }

        if (empty($data['disciplina_id']) || !Disciplina::exists((int) $data['disciplina_id'])) {
            $errors['disciplina_id'] = 'Selecione uma disciplina válida para o artigo.';
        }

        if (empty($data['conteudo'])) {
            $errors['conteudo'] = 'O conteúdo do artigo não pode ficar vazio.';
        }

        return $errors;
    }
}
