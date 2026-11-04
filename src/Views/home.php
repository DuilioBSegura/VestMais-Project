<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Helper;

$currentUser = Auth::user();
$dashboardUrl = ($currentUser && $currentUser['tipo'] === 'admin') 
    ? Helper::url('/admin/dashboard') 
    : Helper::url('/aluno/dashboard');

// Lista igualitária dos 6 integrantes da equipe de criação e desenvolvimento
$equipeIntegrantes = [
    [
        'nome'     => 'Duílio',
        'iniciais' => 'D',
    ],
    [
        'nome'     => 'Davi',
        'iniciais' => 'D',
    ],
    [
        'nome'     => 'Lucas César',
        'iniciais' => 'LC',
    ],
    [
        'nome'     => 'Ricardo',
        'iniciais' => 'R',
    ],
    [
        'nome'     => 'Cauã Meira',
        'iniciais' => 'CM',
    ],
    [
        'nome'     => 'Ethan',
        'iniciais' => 'E',
    ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::e($pageTitle ?? 'Vest+ | Plataforma Gratuita para o ENEM e Vestibulares') ?></title>
    <!-- Estilos da Aplicação -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= Helper::url('/css/style.css?v=1.1.2') ?>">
</head>
<body class="bg-white">

    <!-- ==============================================================================
         NAVBAR PÚBLICA (MINIMALISTA, CLEAN & ALTA VISIBILIDADE)
         ============================================================================== -->
    <nav class="navbar navbar-expand-lg navbar-landing py-3 sticky-top">
        <div class="container px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= Helper::url('/') ?>">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10 p-2" style="border: 1px solid rgba(5, 150, 105, 0.2);">
                    <i class="bi bi-mortarboard-fill text-success fs-5"></i>
                </div>
                <span class="fs-4 fw-bold text-dark tracking-tight">Vest<span class="text-success">+</span></span>
            </a>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <ul class="navbar-nav d-flex flex-row align-items-center mb-0 gap-1 gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link" href="#recursos">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#disciplinas">Disciplinas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">Dúvidas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#equipe">Equipe</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 ms-auto ms-lg-3">
                    <?php if ($currentUser): ?>
                        <a href="<?= $dashboardUrl ?>" class="btn btn-primary btn-pill d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-speedometer2"></i> Meu Dashboard
                        </a>
                        <a href="<?= Helper::url('/logout') ?>" class="btn btn-outline-secondary btn-sm rounded-circle p-2" title="Encerrar sessão">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= Helper::url('/login') ?>" class="btn-nav-login">
                            <i class="bi bi-box-arrow-in-right"></i> Entrar
                        </a>
                        <a href="<?= Helper::url('/cadastro') ?>" class="btn-nav-signup">
                            Começar Grátis <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ==============================================================================
         HERO SECTION COM MOCKUP REALISTA DO COMPUTADOR / DASHBOARD
         ============================================================================== -->
    <header class="py-5 pt-lg-6" style="background: radial-gradient(120% 80% at 50% 0%, #ecfdf5 0%, #f8fafc 40%, #ffffff 85%); border-bottom: 1px solid #f1f5f9;">
        <div class="container px-lg-4 text-center">
            
            <!-- Pill Badge Superior -->
            <div class="d-inline-flex align-items-center gap-2 mb-4">
                <span class="clean-pill-badge">
                    <i class="bi bi-stars text-success"></i> Plataforma Aberta &bull; 100% Gratuita para o ENEM & Vestibulares
                </span>
            </div>

            <!-- Título Principal & Subtítulo -->
            <h1 class="display-4 fw-bold text-dark tracking-tight mb-3 mx-auto" style="max-width: 860px; line-height: 1.15;">
                O ecossistema definitivo para sua aprovação no vestibular.
            </h1>
            <p class="lead text-secondary mx-auto mb-4" style="max-width: 680px; font-size: 1.15rem; line-height: 1.6;">
                Teoria didática sintetizada, simulados com gabarito imediato e sistema gamificado de medalhas para acompanhar seu progresso diário sem pagar nada.
            </p>

            <!-- Chamadas para Ação (CTAs) -->
            <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap mb-4">
                <?php if ($currentUser): ?>
                    <a href="<?= $dashboardUrl ?>" class="btn btn-primary btn-pill btn-lg px-4 shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-right-circle-fill"></i> Continuar Meus Estudos
                    </a>
                <?php else: ?>
                    <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-primary btn-pill btn-lg px-4 shadow-sm d-inline-flex align-items-center gap-2">
                        Criar Conta Gratuita <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="<?= Helper::url('/login') ?>" class="btn btn-outline-secondary btn-pill btn-lg px-4">
                        Acessar Plataforma
                    </a>
                <?php endif; ?>
            </div>

            <!-- Micro-confiança -->
            <div class="d-flex align-items-center justify-content-center gap-4 text-muted small mb-5 flex-wrap">
                <span class="d-flex align-items-center gap-1"><i class="bi bi-shield-check text-success"></i> Sem mensalidades</span>
                <span class="d-flex align-items-center gap-1"><i class="bi bi-lightning-charge text-success"></i> Acesso imediato</span>
                <span class="d-flex align-items-center gap-1"><i class="bi bi-mortarboard text-success"></i> Foco no ENEM & Vestibulares SP</span>
            </div>

            <!-- ==============================================================================
                 MOCKUP REALISTA DO COMPUTADOR MOSTRANDO O DASHBOARD NO FUNDO
                 ============================================================================== -->
            <div class="laptop-mockup-wrapper">
                <!-- Tampa / Tela do Laptop -->
                <div class="laptop-screen-frame">
                    <!-- Câmera do Laptop -->
                    <div class="laptop-webcam"></div>
                    
                    <!-- Display / Tela Interna mostrando o Dashboard -->
                    <div class="laptop-display shadow-inner">
                        <!-- Mini Barra Lateral do Dashboard -->
                        <div class="mockup-sidebar d-none d-md-flex flex-column text-white">
                            <div class="mockup-sidebar-header">
                                <i class="bi bi-mortarboard-fill text-success fs-5"></i>
                                <span class="fw-bold tracking-tight">Vest+</span>
                                <span class="badge bg-success bg-opacity-25 text-success ms-auto" style="font-size: 0.65rem;">Aluno</span>
                            </div>
                            <div class="mockup-sidebar-nav">
                                <div class="mockup-nav-item active">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </div>
                                <div class="mockup-nav-item">
                                    <i class="bi bi-patch-question"></i> Questões
                                </div>
                                <div class="mockup-nav-item">
                                    <i class="bi bi-journal-text"></i> Artigos
                                </div>
                                <div class="mockup-nav-item">
                                    <i class="bi bi-award-fill text-warning"></i> Conquistas 
                                    <span class="badge bg-warning text-dark ms-auto" style="font-size: 0.6rem;">4</span>
                                </div>
                                <div class="mockup-nav-item">
                                    <i class="bi bi-gear"></i> Configurações
                                </div>
                            </div>
                            <div class="mt-auto pt-2 border-top border-secondary border-opacity-25 small text-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="mockup-avatar">E</div>
                                    <div class="lh-1">
                                        <div class="text-white fw-bold" style="font-size: 0.72rem;">Estudante Vest+</div>
                                        <span class="text-muted" style="font-size: 0.62rem;">Foco: ENEM 2026</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Área Principal do Dashboard -->
                        <div class="mockup-main flex-grow-1 d-flex flex-column bg-light text-start">
                            <!-- Mini Topbar -->
                            <div class="mockup-topbar d-flex align-items-center justify-content-between px-3 py-2 bg-white">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold" style="font-size: 0.68rem;">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> Modo de Estudo Ativo
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border small" style="font-size: 0.68rem;">
                                        <i class="bi bi-fire text-danger"></i> 5 Dias Consecutivos
                                    </span>
                                    <span class="badge bg-success text-white small" style="font-size: 0.68rem;">
                                        <i class="bi bi-award"></i> 4 Medalhas
                                    </span>
                                </div>
                            </div>

                            <!-- Mini Conteúdo com Métricas e Questão -->
                            <div class="p-3">
                                <!-- Cards Rápidos de Desempenho -->
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <div class="bg-white p-2 rounded border">
                                            <span class="text-muted d-block" style="font-size: 0.68rem;">Resolvidas</span>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <span class="fw-bold fs-6 text-dark">48</span>
                                                <span class="badge bg-success-subtle text-success" style="font-size: 0.58rem;">+6 hoje</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="bg-white p-2 rounded border">
                                            <span class="text-muted d-block" style="font-size: 0.68rem;">Taxa de Acertos</span>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <span class="fw-bold fs-6 text-success">85,4%</span>
                                                <span class="badge bg-success-subtle text-success" style="font-size: 0.58rem;">Excelente</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="bg-white p-2 rounded border">
                                            <span class="text-muted d-block" style="font-size: 0.68rem;">Conquistas</span>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <span class="fw-bold fs-6 text-warning">4</span>
                                                <i class="bi bi-patch-check-fill text-warning" style="font-size: 0.75rem;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mini Card de Resolução Interativa -->
                                <div class="bg-white p-3 rounded border shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-primary-subtle text-primary fw-semibold" style="font-size: 0.65rem;">FUVEST &bull; Matemática</span>
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Progressões</span>
                                        </div>
                                        <span class="badge bg-success text-white" style="font-size: 0.62rem;">
                                            <i class="bi bi-check-lg"></i> Resolvida com Sucesso
                                        </span>
                                    </div>
                                    <p class="mb-2 text-dark fw-medium" style="font-size: 0.75rem; line-height: 1.35;">
                                        Uma progressão aritmética possui termo inicial $a_1 = 4$ e razão $r = 3$. Determine o valor do 10º termo ($a_{10}$).
                                    </p>
                                    <div class="d-flex flex-column gap-1 mb-2">
                                        <div class="p-1 px-2 rounded border bg-light text-muted" style="font-size: 0.68rem;">
                                            <strong>A)</strong> 28
                                        </div>
                                        <div class="p-1 px-2 rounded border bg-success-subtle border-success text-success fw-bold d-flex align-items-center justify-content-between" style="font-size: 0.68rem;">
                                            <span><strong>B)</strong> 31 &bull; Resposta Correta</span>
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                        <div class="p-1 px-2 rounded border bg-light text-muted" style="font-size: 0.68rem;">
                                            <strong>C)</strong> 34
                                        </div>
                                    </div>
                                    <div class="bg-success-subtle p-2 rounded small text-success-emphasis border border-success-subtle" style="font-size: 0.65rem; line-height: 1.3;">
                                        <i class="bi bi-lightbulb-fill text-success me-1"></i>
                                        <strong>Resolução Comentada:</strong> Pela fórmula do termo geral da P.A., temos $a_{10} = a_1 + 9r = 4 + (9 \times 3) = 4 + 27 = 31$.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Base do Laptop -->
                <div class="laptop-base">
                    <div class="laptop-notch"></div>
                </div>

                <!-- Sombra Suave no Piso -->
                <div class="laptop-floor-shadow"></div>
            </div>
        </div>
    </header>

    <!-- ==============================================================================
         FAIXA DE MÉTRICAS & NÚMEROS (CLEAN STRIP)
         ============================================================================== -->
    <section class="py-5 bg-white border-bottom">
        <div class="container px-lg-4">
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3">
                    <div class="clean-stat-box">
                        <div class="fs-2 fw-bold text-dark mb-1"><?= $totalArtigos ?>+</div>
                        <div class="text-secondary small fw-medium">Artigos Didáticos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="clean-stat-box">
                        <div class="fs-2 fw-bold text-dark mb-1"><?= $totalQuestoes ?>+</div>
                        <div class="text-secondary small fw-medium">Questões Comentadas</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="clean-stat-box">
                        <div class="fs-2 fw-bold text-dark mb-1"><?= count($disciplinas) ?></div>
                        <div class="text-secondary small fw-medium">Disciplinas Base</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="clean-stat-box">
                        <div class="fs-2 fw-bold text-dark mb-1"><?= $totalMedalhas ?></div>
                        <div class="text-secondary small fw-medium">Medalhas & Conquistas</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         RECURSOS & PILHARES DIDÁTICOS (MINIMALIST 3-COLUMN GRID)
         ============================================================================== -->
    <section id="recursos" class="py-5 py-lg-6" style="background-color: #f8fafc;">
        <div class="container px-lg-4">
            <div class="text-center mb-5" style="max-width: 640px; margin: 0 auto;">
                <span class="text-uppercase fw-bold text-success small tracking-wide">Recursos da Plataforma</span>
                <h2 class="display-6 fw-bold text-dark mt-2 mb-3 tracking-tight">Tudo o que você precisa para dominar a prova</h2>
                <p class="text-secondary">
                    Uma abordagem pragmática para vestibulandos que buscam objetividade, prática com questões reais e retenção de conteúdo.
                </p>
            </div>

            <div class="row g-4">
                <!-- Pilar 1: Teoria Estruturada -->
                <div class="col-md-4">
                    <div class="clean-feature-box">
                        <div class="clean-icon-circle">
                            <i class="bi bi-journal-richtext"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Artigos Teóricos Diretos ao Ponto</h4>
                        <p class="text-secondary small lh-base mb-4 flex-grow-1">
                            Textos didáticos organizados por disciplinas e assuntos, com síntese dos pontos mais cobrados e dicas práticas para o ENEM.
                        </p>
                        <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0 border-top pt-3">
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Estimativa do tempo de leitura</li>
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Linguagem clara e descomplicada</li>
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Organizado por temas e vestibulares</li>
                        </ul>
                    </div>
                </div>

                <!-- Pilar 2: Banco de Questões com Feedback Imediato -->
                <div class="col-md-4">
                    <div class="clean-feature-box">
                        <div class="clean-icon-circle">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Questões com Gabarito Instantâneo</h4>
                        <p class="text-secondary small lh-base mb-4 flex-grow-1">
                            Exercícios oficiais com correção imediata assíncrona (sem recarregar a tela) e resolução passo a passo de cada alternativa.
                        </p>
                        <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0 border-top pt-3">
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Simulado com gabarito na hora</li>
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Explicação pedagógica detalhada</li>
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Filtros rápidos por matéria e banca</li>
                        </ul>
                    </div>
                </div>

                <!-- Pilar 3: Gamificação & Medalhas -->
                <div class="col-md-4">
                    <div class="clean-feature-box">
                        <div class="clean-icon-circle">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Gamificação & Sistema de Medalhas</h4>
                        <p class="text-secondary small lh-base mb-4 flex-grow-1">
                            Mantenha o foco e a motivação desbloqueando medalhas de consistência, precisão e volume de exercícios completados.
                        </p>
                        <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0 border-top pt-3">
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> 8 medalhas exclusivas para desbloquear</li>
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Taxa de acerto em tempo real</li>
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-check2 text-success"></i> Vitrine pública no painel do aluno</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         DISCIPLINAS (ACERVO ORGANIZADO)
         ============================================================================== -->
    <section id="disciplinas" class="py-5 py-lg-6 bg-white border-bottom">
        <div class="container px-lg-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
                <div>
                    <span class="text-uppercase fw-bold text-success small tracking-wide">Acervo Didático</span>
                    <h2 class="display-6 fw-bold text-dark mb-0 mt-1 tracking-tight">Disciplinas Disponíveis</h2>
                </div>
                <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-outline-primary btn-pill">
                    Criar Conta e Praticar Todas &rarr;
                </a>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($disciplinas as $d): ?>
                    <div class="col">
                        <div class="clean-stat-box h-100 p-4 text-start">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success fs-4">
                                    <i class="bi bi-book-half"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark"><?= Helper::e((string) $d['nome']) ?></h5>
                                    <span class="badge bg-light text-secondary border small mt-1">ENEM &bull; Vestibulares</span>
                                </div>
                            </div>
                            <p class="text-secondary small lh-base mb-0">
                                <?= Helper::e((string) ($d['descricao'] ?: 'Fundamentos teóricos e exercícios comentados para aprovação.')) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         FAQ (PERGUNTAS FREQUENTES - ACCORDION MINIMALISTA)
         ============================================================================== -->
    <section id="faq" class="py-5 py-lg-6" style="background-color: #f8fafc;">
        <div class="container px-lg-4">
            <div class="text-center mb-5" style="max-width: 600px; margin: 0 auto;">
                <span class="text-uppercase fw-bold text-success small tracking-wide">Tire suas Dúvidas</span>
                <h2 class="display-6 fw-bold text-dark mt-2 mb-3 tracking-tight">Perguntas Frequentes</h2>
                <p class="text-secondary">
                    Tudo o que você precisa saber sobre o Vest+ e como ele pode apoiar a sua rotina de estudos.
                </p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion accordion-clean" id="vestFaqAccordion">
                        <!-- FAQ 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                    A plataforma é realmente 100% gratuita?
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#vestFaqAccordion">
                                <div class="accordion-body">
                                    Sim, integralmente! O Vest+ é um projeto educacional aberto. Não há períodos de teste pagos, cobrança de mensalidades ou conteúdos bloqueados por assinatura. Todos os artigos, questões e recursos de gamificação são livres para qualquer estudante.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    Quais vestibulares e provas são contemplados?
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#vestFaqAccordion">
                                <div class="accordion-body">
                                    O Vest+ tem foco prioritário no Exame Nacional do Ensino Médio (ENEM) e nos principais vestibulares do estado de São Paulo: FUVEST (USP), UNICAMP e UNESP (VUNESP). Nossos conteúdos cobrem os assuntos mais recorrentes dessas bancas examinadoras.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    Como funciona o sistema de medalhas e gamificação?
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#vestFaqAccordion">
                                <div class="accordion-body">
                                    Conforme você resolve exercícios na plataforma, o sistema monitora automaticamente suas marcas históricas (como primeira questão respondida, sequências de acertos, precisão acima de 80% e total de resoluções). Cada conquista desbloqueia uma medalha exclusiva no seu perfil.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                    Preciso instalar algum programa no computador ou aplicativo no celular?
                                </button>
                            </h2>
                            <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#vestFaqAccordion">
                                <div class="accordion-body">
                                    Não! O Vest+ é 100% web e foi construído com design totalmente responsivo. Você pode estudar diretamente pelo navegador do seu computador, tablet ou smartphone com a mesma fluidez e rapidez.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                    Como posso acompanhar minha taxa de acertos e evolução?
                                </button>
                            </h2>
                            <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#vestFaqAccordion">
                                <div class="accordion-body">
                                    No Painel do Estudante, você tem acesso imediato à sua taxa geral de precisão, total de exercícios respondidos, histórico de tentativas e a aba de conquistas com barras de progresso para as próximas medalhas.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         EQUIPE DO PROJETO (APRESENTAÇÃO COLETIVA & IGUALITÁRIA)
         ============================================================================== -->
    <section id="equipe" class="py-5 py-lg-6 bg-white border-bottom">
        <div class="container px-lg-4">
            <div class="text-center mb-5" style="max-width: 620px; margin: 0 auto;">
                <span class="text-uppercase fw-bold text-success small tracking-wide">Quem Faz Acontecer</span>
                <h2 class="display-6 fw-bold text-dark mt-2 mb-3 tracking-tight">Equipe de Criação & Desenvolvimento</h2>
                <p class="text-secondary">
                    O Vest+ é fruto do trabalho integrado e colaborativo de seus idealizadores, unidos pela democratização do conhecimento e pela excelência em engenharia de software.
                </p>
            </div>

            <!-- Grid Minimalista de Integrantes -->
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 justify-content-center">
                <?php foreach ($equipeIntegrantes as $membro): ?>
                    <div class="col">
                        <div class="team-member-minimal h-100">
                            <div class="team-avatar-gradient">
                                <?= Helper::e($membro['iniciais']) ?>
                            </div>
                            <h6 class="fw-bold text-dark mb-0"><?= Helper::e($membro['nome']) ?></h6>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         CTA FINAL (CARD ELEGANTE EM GRADIENTE ESMERALDA)
         ============================================================================== -->
    <section class="py-5 py-lg-6 bg-white">
        <div class="container px-lg-4">
            <div class="p-5 rounded-4 text-white text-center shadow-lg" style="background: linear-gradient(135deg, #064e3b 0%, #059669 100%);">
                <h2 class="display-6 fw-bold mb-3 tracking-tight">Pronto para acelerar sua aprovação?</h2>
                <p class="fs-5 text-light opacity-90 col-md-8 mx-auto mb-4" style="color: #ecfdf5 !important;">
                    Junte-se ao Vest+ agora mesmo. Crie sua conta gratuita em menos de 1 minuto e comece a estudar com foco nos resultados.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-light btn-pill btn-lg px-4 py-3 fw-bold text-success shadow-sm">
                        <i class="bi bi-person-plus-fill me-1"></i> Criar Conta Gratuita
                    </a>
                    <a href="<?= Helper::url('/login') ?>" class="btn btn-outline-light btn-pill btn-lg px-4 py-3">
                        Fazer Login
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         FOOTER INSTITUCIONAL (MINIMALISTA)
         ============================================================================== -->
    <footer class="py-5 text-white" style="background-color: #0f172a;">
        <div class="container px-lg-4">
            <div class="row g-4 justify-content-between">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-mortarboard-fill text-success fs-3" style="color: #34d399 !important;"></i>
                        <span class="fs-4 fw-bold text-white tracking-tight">Vest<span class="text-success">+</span></span>
                    </div>
                    <p class="small lh-base mb-3" style="color: #94a3b8 !important;">
                        Plataforma aberta de estudos e Banco de Questões com gabarito comentado, desenvolvida especialmente para vestibulandos e candidatos do ENEM.
                    </p>
                    <div class="small" style="color: #64748b !important;">
                        Licença MIT &bull; Repositório aberto no <a href="https://github.com/DuilioBSegura/VestMais-Project" class="text-light text-decoration-underline" target="_blank">GitHub</a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <h6 class="text-uppercase fw-bold text-white small mb-3">Navegação</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="#recursos" class="footer-clean-link"><i class="bi bi-chevron-right text-success small"></i> Recursos</a></li>
                        <li><a href="#disciplinas" class="footer-clean-link"><i class="bi bi-chevron-right text-success small"></i> Disciplinas</a></li>
                        <li><a href="#faq" class="footer-clean-link"><i class="bi bi-chevron-right text-success small"></i> Perguntas Frequentes</a></li>
                        <li><a href="#equipe" class="footer-clean-link"><i class="bi bi-chevron-right text-success small"></i> Equipe do Projeto</a></li>
                    </ul>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <h6 class="text-uppercase fw-bold text-white small mb-3">Acesso Rápido</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="<?= Helper::url('/login') ?>" class="footer-clean-link"><i class="bi bi-chevron-right text-success small"></i> Acessar Plataforma (Login)</a></li>
                        <li><a href="<?= Helper::url('/cadastro') ?>" class="footer-clean-link"><i class="bi bi-chevron-right text-success small"></i> Criar Conta de Estudante</a></li>
                        <li><a href="https://github.com/DuilioBSegura/VestMais-Project" class="footer-clean-link" target="_blank"><i class="bi bi-github text-white small"></i> Repositório Oficial no GitHub</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-4 mt-4 border-top border-secondary border-opacity-25 text-center small" style="color: #64748b !important;">
                &copy; <?= date('Y') ?> Vest+ &bull; Plataforma Gratuita de Estudos para Vestibulares. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <!-- Scripts da Aplicação -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
