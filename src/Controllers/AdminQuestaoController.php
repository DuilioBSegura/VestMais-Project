<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Helper;
use App\Models\Categoria;
use App\Models\Disciplina;
use App\Models\Questao;
use Throwable;

/**
 * Controller responsável pelo gerenciamento administrativo de Questões de Múltipla Escolha (CRUD).
 */
final class AdminQuestaoController extends BaseController
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Lista todas as questões cadastradas.
     */
    public function index(): void
    {
        $questoes = Questao::getAll();
        $this->render('admin/questoes/index', [
            'pageTitle' => 'Banco de Questões (Admin)',
            'questoes'  => $questoes,
        ]);
    }

    /**
     * Renderiza o formulário de cadastro de nova questão.
     */
    public function create(): void
    {
        $disciplinas = Disciplina::getAll();
        $categorias = Categoria::getAll();

        $this->render('admin/questoes/form', [
            'pageTitle'              => 'Nova Questão de Múltipla Escolha',
            'isEdit'                 => false,
            'questao'                => [
                'id'               => null,
                'disciplina_id'    => '',
                'enunciado'        => '',
                'alternativa_a'    => '',
                'alternativa_b'    => '',
                'alternativa_c'    => '',
                'alternativa_d'    => '',
                'alternativa_e'    => '',
                'resposta_correta' => 'A',
                'explicacao'       => '',
            ],
            'categoriasSelecionadas' => [],
            'disciplinas'            => $disciplinas,
            'categorias'             => $categorias,
            'errors'                 => [],
        ]);
    }

    /**
     * Processa a criação de uma nova questão via POST.
     */
    public function store(): void
    {
        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Sessão expirada ou token de segurança inválido. Tente novamente.');
            $this->redirect('/admin/questoes');
            return;
        }

        $disciplinaId = (int) ($_POST['disciplina_id'] ?? 0);
        $enunciado = trim((string) ($_POST['enunciado'] ?? ''));
        $altA = trim((string) ($_POST['alternativa_a'] ?? ''));
        $altB = trim((string) ($_POST['alternativa_b'] ?? ''));
        $altC = trim((string) ($_POST['alternativa_c'] ?? ''));
        $altD = trim((string) ($_POST['alternativa_d'] ?? ''));
        $altE = trim((string) ($_POST['alternativa_e'] ?? ''));
        $respostaCorreta = strtoupper(trim((string) ($_POST['resposta_correta'] ?? 'A')));
        $explicacao = trim((string) ($_POST['explicacao'] ?? ''));
        $categoriasIds = isset($_POST['categorias']) && is_array($_POST['categorias'])
            ? array_map('intval', $_POST['categorias'])
            : [];

        $data = [
            'disciplina_id'    => $disciplinaId,
            'enunciado'        => $enunciado,
            'alternativa_a'    => $altA,
            'alternativa_b'    => $altB,
            'alternativa_c'    => $altC,
            'alternativa_d'    => $altD,
            'alternativa_e'    => $altE,
            'resposta_correta' => $respostaCorreta,
            'explicacao'       => $explicacao,
        ];

        $errors = $this->validate($data);

        if (!empty($errors)) {
            Flash::error('Não foi possível cadastrar a questão. Corrija os erros apontados no formulário.');
            $disciplinas = Disciplina::getAll();
            $categorias = Categoria::getAll();

            $this->render('admin/questoes/form', [
                'pageTitle'              => 'Nova Questão de Múltipla Escolha',
                'isEdit'                 => false,
                'questao'                => array_merge($data, ['id' => null]),
                'categoriasSelecionadas' => $categoriasIds,
                'disciplinas'            => $disciplinas,
                'categorias'             => $categorias,
                'errors'                 => $errors,
            ]);
            return;
        }

        try {
            $validCategorias = Categoria::filterValidIds($categoriasIds);
            $questaoId = Questao::create($data, $validCategorias);

            Flash::success(sprintf('Questão #%d cadastrada com sucesso no banco de questões!', $questaoId));
            $this->redirect('/admin/questoes');
        } catch (Throwable $e) {
            Flash::error('Falha ao cadastrar questão: ' . $e->getMessage());
            $this->redirect('/admin/questoes');
        }
    }

    /**
     * Renderiza o formulário de edição de questão.
     */
    public function edit(int $id): void
    {
        $questao = Questao::getById($id);
        if (!$questao) {
            Flash::error('Questão não encontrada.');
            $this->redirect('/admin/questoes');
            return;
        }

        $disciplinas = Disciplina::getAll();
        $categorias = Categoria::getAll();

        $this->render('admin/questoes/form', [
            'pageTitle'              => "Editar Questão #{$id}",
            'isEdit'                 => true,
            'questao'                => $questao,
            'categoriasSelecionadas' => $questao['categoria_ids'] ?? [],
            'disciplinas'            => $disciplinas,
            'categorias'             => $categorias,
            'errors'                 => [],
        ]);
    }

    /**
     * Processa a atualização de uma questão existente via POST.
     */
    public function update(int $id): void
    {
        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Sessão expirada ou token de segurança inválido. Tente novamente.');
            $this->redirect('/admin/questoes');
            return;
        }

        $questaoAtual = Questao::getById($id);
        if (!$questaoAtual) {
            Flash::error('Questão não encontrada para edição.');
            $this->redirect('/admin/questoes');
            return;
        }

        $disciplinaId = (int) ($_POST['disciplina_id'] ?? 0);
        $enunciado = trim((string) ($_POST['enunciado'] ?? ''));
        $altA = trim((string) ($_POST['alternativa_a'] ?? ''));
        $altB = trim((string) ($_POST['alternativa_b'] ?? ''));
        $altC = trim((string) ($_POST['alternativa_c'] ?? ''));
        $altD = trim((string) ($_POST['alternativa_d'] ?? ''));
        $altE = trim((string) ($_POST['alternativa_e'] ?? ''));
        $respostaCorreta = strtoupper(trim((string) ($_POST['resposta_correta'] ?? 'A')));
        $explicacao = trim((string) ($_POST['explicacao'] ?? ''));
        $categoriasIds = isset($_POST['categorias']) && is_array($_POST['categorias'])
            ? array_map('intval', $_POST['categorias'])
            : [];

        $data = [
            'disciplina_id'    => $disciplinaId,
            'enunciado'        => $enunciado,
            'alternativa_a'    => $altA,
            'alternativa_b'    => $altB,
            'alternativa_c'    => $altC,
            'alternativa_d'    => $altD,
            'alternativa_e'    => $altE,
            'resposta_correta' => $respostaCorreta,
            'explicacao'       => $explicacao,
        ];

        $errors = $this->validate($data);

        if (!empty($errors)) {
            Flash::error('Não foi possível atualizar a questão. Corrija os erros apontados no formulário.');
            $disciplinas = Disciplina::getAll();
            $categorias = Categoria::getAll();

            $this->render('admin/questoes/form', [
                'pageTitle'              => "Editar Questão #{$id}",
                'isEdit'                 => true,
                'questao'                => array_merge($data, ['id' => $id]),
                'categoriasSelecionadas' => $categoriasIds,
                'disciplinas'            => $disciplinas,
                'categorias'             => $categorias,
                'errors'                 => $errors,
            ]);
            return;
        }

        try {
            $validCategorias = Categoria::filterValidIds($categoriasIds);
            Questao::update($id, $data, $validCategorias);

            Flash::success("Questão #{$id} atualizada com sucesso!");
            $this->redirect('/admin/questoes');
        } catch (Throwable $e) {
            Flash::error('Erro ao atualizar questão: ' . $e->getMessage());
            $this->redirect('/admin/questoes');
        }
    }

    /**
     * Processa a exclusão de uma questão via POST.
     */
    public function delete(int $id): void
    {
        if (!Csrf::validateToken($_POST['csrf_token'] ?? null)) {
            Flash::error('Falha de segurança na requisição (token CSRF inválido). Ação abortada.');
            $this->redirect('/admin/questoes');
            return;
        }

        $questao = Questao::getById($id);
        if (!$questao) {
            Flash::error('Questão não encontrada para exclusão.');
            $this->redirect('/admin/questoes');
            return;
        }

        try {
            Questao::delete($id);
            Flash::success("Questão #{$id} excluída com sucesso do banco de questões!");
        } catch (Throwable $e) {
            Flash::error('Falha ao excluir questão: ' . $e->getMessage());
        }

        $this->redirect('/admin/questoes');
    }

    /**
     * Valida os dados de entrada de uma questão.
     *
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data['disciplina_id']) || !Disciplina::exists((int) $data['disciplina_id'])) {
            $errors['disciplina_id'] = 'Selecione uma disciplina válida.';
        }

        if (empty($data['enunciado'])) {
            $errors['enunciado'] = 'O enunciado da questão é obrigatório.';
        } elseif (mb_strlen((string) $data['enunciado'], 'UTF-8') < 10) {
            $errors['enunciado'] = 'O enunciado deve conter pelo menos 10 caracteres.';
        }

        if (empty($data['alternativa_a'])) {
            $errors['alternativa_a'] = 'O texto da alternativa A é obrigatório.';
        }
        if (empty($data['alternativa_b'])) {
            $errors['alternativa_b'] = 'O texto da alternativa B é obrigatório.';
        }
        if (empty($data['alternativa_c'])) {
            $errors['alternativa_c'] = 'O texto da alternativa C é obrigatório.';
        }
        if (empty($data['alternativa_d'])) {
            $errors['alternativa_d'] = 'O texto da alternativa D é obrigatório.';
        }
        if (empty($data['alternativa_e'])) {
            $errors['alternativa_e'] = 'O texto da alternativa E é obrigatório.';
        }

        if (!in_array($data['resposta_correta'], Questao::ALTERNATIVAS_VALIDAS, true)) {
            $errors['resposta_correta'] = 'A resposta correta deve ser uma alternativa válida (A, B, C, D ou E).';
        }

        return $errors;
    }
}
