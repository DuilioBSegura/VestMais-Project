<?php

declare(strict_types=1);

/**
 * Vest+ Platform - Endpoint da API de Verificação Assíncrona de Questões
 * Sprint 3: Resolução de Questões pelo Aluno
 */

require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

use App\Controllers\AlunoController;

$controller = new AlunoController();
$controller->verificarQuestao();
