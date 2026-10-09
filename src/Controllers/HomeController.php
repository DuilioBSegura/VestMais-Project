<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Artigo;
use App\Models\Disciplina;
use App\Models\Questao;

/**
 * Controller responsável pela Landing Page institucional do Vest+.
 */
final class HomeController extends BaseController
{
    /**
     * Renderiza a Landing Page pública da plataforma.
     */
    public function index(): void
    {
        $disciplinas = Disciplina::getAll();
        $totalArtigos = count(Artigo::getAll());
        $totalQuestoes = count(Questao::getAll());

        $this->render('home', [
            'pageTitle'     => 'Vest+ | Preparação Gratuita para o ENEM e Vestibulares',
            'disciplinas'   => $disciplinas,
            'totalArtigos'  => $totalArtigos,
            'totalQuestoes' => $totalQuestoes,
        ]);
    }
}
