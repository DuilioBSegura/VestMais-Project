<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Flash;
use App\Models\Artigo;
use App\Models\Categoria;
use App\Models\Disciplina;
use App\Models\Questao;

/**
 * Controller responsável pela experiência do Aluno:
 * Dashboard, leitura de artigos teóricos, catálogo de questões com filtros e resolução assíncrona.
 */
final class AlunoController extends BaseController
{
    public function __construct()
    {
        $this->requireAuth();
    }

    /**
     * Dashboard principal do aluno com resumo de disciplinas e contagens de artigos e questões.
     */
    public function dashboard(): void
    {
        $disciplinas = Disciplina::getWithCounts();

        $this->render('aluno/dashboard', [
            'pageTitle'   => 'Área do Estudante - Dashboard',
            'disciplinas' => $disciplinas,
        ]);
    }

    /**
     * Exibe os artigos teóricos de uma disciplina selecionada.
     */
    public function listarArtigos(?int $disciplinaId = null): void
    {
        $id = $disciplinaId ?? (int) ($_GET['id'] ?? $_GET['disciplina_id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('/aluno/dashboard');
            return;
        }

        $disciplina = Disciplina::getById($id);
        if (!$disciplina) {
            Flash::error('Disciplina não encontrada.');
            $this->redirect('/aluno/dashboard');
            return;
        }

        $artigos = Artigo::getByDisciplina($id);

        $this->render('aluno/artigos', [
            'pageTitle'  => 'Artigos de ' . $disciplina['nome'],
            'disciplina' => $disciplina,
            'artigos'    => $artigos,
        ]);
    }

    /**
     * Renderiza a leitura focada e formatada de um artigo teórico completo.
     */
    public function lerArtigo(?string $slug = null): void
    {
        $artigoSlug = trim($slug ?? (string) ($_GET['slug'] ?? ''));

        if ($artigoSlug === '') {
            $this->redirect('/aluno/dashboard');
            return;
        }

        $artigo = Artigo::getBySlug($artigoSlug);
        if (!$artigo) {
            Flash::error('Artigo não encontrado ou indisponível.');
            $this->redirect('/aluno/dashboard');
            return;
        }

        // Estima o tempo de leitura (~180 palavras por minuto)
        $totalPalavras = str_word_count(strip_tags((string) $artigo['conteudo']));
        $tempoLeitura = max(1, (int) ceil($totalPalavras / 180));

        // Busca artigos relacionados da mesma disciplina
        $todosDaDisciplina = Artigo::getByDisciplina((int) $artigo['disciplina_id']);
        $artigosRelacionados = array_values(array_filter(
            $todosDaDisciplina,
            static fn(array $item): bool => (int) $item['id'] !== (int) $artigo['id']
        ));
        $artigosRelacionados = array_slice($artigosRelacionados, 0, 5);

        $this->render('aluno/artigo', [
            'pageTitle'           => $artigo['titulo'],
            'artigo'              => $artigo,
            'tempoLeitura'        => $tempoLeitura,
            'artigosRelacionados' => $artigosRelacionados,
        ]);
    }

    /**
     * Catálogo e filtro de questões com suporte a parâmetros de disciplina e categoria.
     */
    public function bancoQuestoes(): void
    {
        $disciplinaId = isset($_GET['disciplina_id']) && $_GET['disciplina_id'] !== ''
            ? (int) $_GET['disciplina_id']
            : null;

        $categoriaId = isset($_GET['categoria_id']) && $_GET['categoria_id'] !== ''
            ? (int) $_GET['categoria_id']
            : null;

        $disciplinas = Disciplina::getAll();
        $categorias = Categoria::getAll();
        $questoes = Questao::filtrar($disciplinaId, $categoriaId);

        $this->render('aluno/questoes', [
            'pageTitle'            => 'Banco de Questões e Simulado Interativo',
            'questoes'             => $questoes,
            'disciplinas'          => $disciplinas,
            'categorias'           => $categorias,
            'disciplinaSelecionada' => $disciplinaId,
            'categoriaSelecionada'  => $categoriaId,
        ]);
    }

    /**
     * Endpoint interno para verificação assíncrona de respostas enviadas pelo aluno.
     * Recebe payload JSON ou POST e retorna resultado imediato com gabarito e explicação.
     */
    public function verificarQuestao(): void
    {
        // Lê dados via JSON body ou via $_POST
        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);

        $questaoId = (int) ($jsonData['questao_id'] ?? $_POST['questao_id'] ?? 0);
        $resposta = strtoupper(trim((string) ($jsonData['resposta'] ?? $_POST['resposta'] ?? '')));

        if ($questaoId <= 0 || !in_array($resposta, Questao::ALTERNATIVAS_VALIDAS, true)) {
            $this->json([
                'sucesso' => false,
                'erro'    => 'Parâmetros inválidos. Informe um ID de questão válido e uma alternativa entre A e E.'
            ], 400);
            return;
        }

        $resultado = Questao::verificarResposta($questaoId, $resposta);

        if (!$resultado) {
            $this->json([
                'sucesso' => false,
                'erro'    => 'Questão não encontrada no banco de dados.'
            ], 404);
            return;
        }

        $this->json($resultado, 200);
    }
}
