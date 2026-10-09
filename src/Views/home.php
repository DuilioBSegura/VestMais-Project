<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Helper;

$currentUser = Auth::user();
$dashboardUrl = ($currentUser && $currentUser['tipo'] === 'admin') 
    ? Helper::url('/admin/dashboard') 
    : Helper::url('/aluno/dashboard');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::e($pageTitle ?? 'Vest+ | Plataforma Gratuita para o ENEM e Vestibulares') ?></title>
    <!-- Bootstrap 5.3 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Design System Global (Verde Esmeralda / Menta) -->
    <link rel="stylesheet" href="<?= Helper::url('/css/style.css') ?>">
</head>
<body>

    <!-- ==============================================================================
         NAVBAR PÚBLICA
         ============================================================================== -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 sticky-top">
        <div class="container px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= Helper::url('/') ?>">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-25 p-2">
                    <i class="bi bi-mortarboard-fill text-success fs-4" style="color: #34d399 !important;"></i>
                </div>
                <span class="fs-4 fw-bold">Vest+</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbar" aria-controls="landingNavbar" aria-expanded="false" aria-label="Navegação">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="landingNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#sobre">Sobre o Projeto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#recursos">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#disciplinas">Disciplinas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#equipe">Equipe & Propósito</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if ($currentUser): ?>
                        <a href="<?= $dashboardUrl ?>" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-speedometer2"></i> Meu Dashboard
                        </a>
                        <a href="<?= Helper::url('/logout') ?>" class="btn btn-outline-light btn-sm" title="Encerrar sessão">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= Helper::url('/login') ?>" class="btn btn-outline-light px-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                        </a>
                        <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-primary px-3 shadow-sm">
                            Criar Conta Gratuita
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ==============================================================================
         HERO SECTION (IMPACTO VISUAL)
         ============================================================================== -->
    <header class="hero-gradient text-white py-5 py-lg-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <div class="container px-lg-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <!-- Badges de Foco -->
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="hero-badge">
                            <i class="bi bi-check-circle-fill"></i> 100% Gratuito & Aberto
                        </span>
                        <span class="badge bg-light bg-opacity-10 text-white border border-light border-opacity-25 rounded-pill px-3 py-2">
                            ENEM &bull; FUVEST &bull; UNICAMP &bull; VUNESP
                        </span>
                    </div>

                    <h1 class="display-4 fw-bold lh-sm text-white mb-3">
                        Sua preparação para o ENEM e Vestibulares totalmente <span style="color: #34d399;">gratuita</span>.
                    </h1>

                    <p class="fs-5 text-light opacity-90 mb-4 lh-base" style="color: #e2e8f0 !important;">
                        Domine os conteúdos mais cobrados com nosso acervo estruturado de artigos teóricos e pratique em tempo real com questões comentadas de múltipla escolha. Sem pegadinhas, sem planos pagos.
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        <?php if ($currentUser): ?>
                            <a href="<?= $dashboardUrl ?>" class="btn btn-primary btn-lg px-4 py-3 shadow d-inline-flex align-items-center gap-2">
                                <i class="bi bi-arrow-right-circle-fill fs-5"></i> Continuar Meus Estudos
                            </a>
                        <?php else: ?>
                            <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-primary btn-lg px-4 py-3 shadow d-inline-flex align-items-center gap-2">
                                <i class="bi bi-rocket-takeoff-fill fs-5"></i> Começar a Estudar Agora
                            </a>
                            <a href="<?= Helper::url('/login') ?>" class="btn btn-outline-light btn-lg px-4 py-3">
                                Já Tenho Conta
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Métricas Rápidas no Hero -->
                    <div class="row g-3 mt-4 pt-3 border-top border-light border-opacity-10">
                        <div class="col-auto">
                            <div class="fw-bold fs-4 text-white"><?= count($disciplinas) ?>+</div>
                            <div class="small opacity-75">Disciplinas Base</div>
                        </div>
                        <div class="col-auto px-4 border-start border-light border-opacity-10">
                            <div class="fw-bold fs-4 text-white"><?= $totalArtigos ?>+</div>
                            <div class="small opacity-75">Artigos Teóricos</div>
                        </div>
                        <div class="col-auto px-4 border-start border-light border-opacity-10">
                            <div class="fw-bold fs-4 text-white"><?= $totalQuestoes ?>+</div>
                            <div class="small opacity-75">Questões Comentadas</div>
                        </div>
                    </div>
                </div>

                <!-- Card Ilustrativo Interativo -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4 p-4 text-dark bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-discipline px-2 py-1">Matemática</span>
                                <span class="badge badge-category rounded-pill">Funções</span>
                            </div>
                            <span class="badge bg-success text-white small px-2 py-1">Simulado Interativo</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-3">Exemplo de Resolução em Tempo Real:</h6>
                        <p class="small text-secondary mb-3">
                            Uma função afim determina o valor do frete com taxa fixa de R$ 12,00 somada a R$ 3,50 por km. Para 40 km, qual o valor final?
                        </p>
                        <div class="list-group list-group-flush small mb-3">
                            <div class="list-group-item px-0 py-2 d-flex align-items-center gap-2 text-muted">
                                <span class="badge bg-secondary rounded-pill">A</span> R$ 140,00
                            </div>
                            <div class="list-group-item px-0 py-2 d-flex align-items-center gap-2 text-muted">
                                <span class="badge bg-secondary rounded-pill">B</span> R$ 148,00
                            </div>
                            <div class="list-group-item px-0 py-2 d-flex align-items-center gap-2 fw-bold text-success bg-success-subtle rounded-2 px-2">
                                <span class="badge bg-success rounded-pill">C</span> R$ 152,00 (Gabarito Correto)
                            </div>
                        </div>
                        <div class="p-2 rounded bg-light border small text-muted">
                            <i class="bi bi-lightbulb-fill text-warning me-1"></i> <strong>Resolução:</strong> C(40) = 12 + (3,50 &times; 40) = R$ 152,00.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ==============================================================================
         SEÇÃO SOBRE & PROPÓSITO
         ============================================================================== -->
    <section id="sobre" class="py-5 bg-white border-bottom">
        <div class="container px-lg-4 py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-uppercase fw-bold text-success small tracking-wide">Sobre a Plataforma</span>
                    <h2 class="h2 fw-bold text-dark mt-2 mb-3">
                        Construído para democratizar a aprovação universitária.
                    </h2>
                    <p class="text-secondary lh-lg mb-4">
                        O <strong>Vest+</strong> nasceu como uma iniciativa acadêmica aberta com um objetivo transparente: fornecer uma plataforma limpa, rápida e acessível para que qualquer estudante brasileiro tenha recursos de alto nível para se preparar para o ENEM e os vestibulares das universidades paulistas (USP, UNICAMP e UNESP).
                    </p>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i> Zero barreiras financeiras
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i> Sem anúncios intrusivos
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i> Gabaritos 100% explicados
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i> Arquitetura rápida e leve
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="p-4 p-md-5 rounded-4 bg-light border shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success fs-3">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Compromisso com o Código Aberto</h5>
                                <p class="text-muted small mb-0">Projeto sob licença permissiva MIT no GitHub.</p>
                            </div>
                        </div>
                        <p class="text-secondary small lh-base mb-0">
                            Desenvolvido com arquitetura limpa em PHP 8.2+ puro e Bootstrap 5, o Vest+ elimina dependências pesadas e garante acessibilidade até mesmo em dispositivos e conexões modestas, reforçando nossa responsabilidade com a inclusão digital educacional.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         SEÇÃO DE RECURSOS (OS DOIS MOTORES DO VEST+)
         ============================================================================== -->
    <section id="recursos" class="py-5" style="background-color: var(--vest-bg);">
        <div class="container px-lg-4 py-4">
            <div class="text-center max-w-xl mx-auto mb-5">
                <span class="text-uppercase fw-bold text-success small tracking-wide">Recursos da Plataforma</span>
                <h2 class="h2 fw-bold text-dark mt-2 mb-3">Tudo o que você precisa em um só lugar</h2>
                <p class="text-muted col-lg-8 mx-auto">
                    Aliamos teoria focada e prática constante para garantir a melhor retenção de conceitos no menor tempo de estudo.
                </p>
            </div>

            <div class="row g-4">
                <!-- Card 1: CMS de Conteúdo Teórico -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-custom h-100 p-4 border-top border-4 border-success hover-lift">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3 mb-3 bg-success bg-opacity-10 text-success" style="width: 54px; height: 54px;">
                            <i class="bi bi-journal-text fs-3"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">CMS de Conteúdo Teórico</h4>
                        <p class="text-muted small lh-base flex-grow-1">
                            Artigos aprofundados com formatação limpa, tipografia confortável e fórmulas claras organizadas por matéria e tópicos prioritários do ENEM.
                        </p>
                        <div class="pt-3 border-top mt-auto">
                            <span class="text-success small fw-semibold"><i class="bi bi-check2 me-1"></i> Leitura Focada &bull; Sem distrações</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Banco de Questões Assíncrono -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-custom h-100 p-4 border-top border-4 border-success hover-lift">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3 mb-3 bg-success bg-opacity-10 text-success" style="width: 54px; height: 54px;">
                            <i class="bi bi-patch-question fs-3"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Banco de Questões Interativo</h4>
                        <p class="text-muted small lh-base flex-grow-1">
                            Resolução instantânea via Fetch API (Vanilla JS) sem recarregar a tela. Ao responder, você descobre na hora se acertou ou errou.
                        </p>
                        <div class="pt-3 border-top mt-auto">
                            <span class="text-success small fw-semibold"><i class="bi bi-lightning-charge me-1"></i> Resolução Imediata &bull; Zero recarregamento</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Gabarito Comentado -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-custom h-100 p-4 border-top border-4 border-success hover-lift">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3 mb-3 bg-success bg-opacity-10 text-success" style="width: 54px; height: 54px;">
                            <i class="bi bi-chat-square-quote fs-3"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Gabarito Comentado Passo a Passo</h4>
                        <p class="text-muted small lh-base flex-grow-1">
                            Não fique na dúvida. Todas as questões acompanham explicação didática detalhada escrita por professores e pesquisadores da área.
                        </p>
                        <div class="pt-3 border-top mt-auto">
                            <span class="text-success small fw-semibold"><i class="bi bi-lightbulb me-1"></i> Aprendizado Ativo &bull; Entenda o porquê</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         SEÇÃO DE DISCIPLINAS
         ============================================================================== -->
    <section id="disciplinas" class="py-5 bg-white border-top border-bottom">
        <div class="container px-lg-4 py-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <span class="text-uppercase fw-bold text-success small tracking-wide">Grade Curricular</span>
                    <h2 class="h3 fw-bold text-dark mb-0 mt-1">Disciplinas Disponíveis</h2>
                </div>
                <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-outline-primary btn-sm">
                    Cadastre-se para Praticar Todas
                </a>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($disciplinas as $d): ?>
                    <div class="col">
                        <div class="card card-custom h-100 p-4 hover-lift">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success fs-4">
                                    <i class="bi bi-book-half"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark"><?= Helper::e((string) $d['nome']) ?></h5>
                                    <span class="badge badge-discipline small mt-1">ENEM / Vestibulares</span>
                                </div>
                            </div>
                            <p class="text-muted small lh-base mb-0">
                                <?= Helper::e((string) ($d['descricao'] ?: 'Fundamentos teóricos e exercícios de fixação para aprovação.')) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         SEÇÃO DA EQUIPE & PROPÓSITO DO PROJETO
         ============================================================================== -->
    <section id="equipe" class="py-5" style="background-color: var(--vest-bg);">
        <div class="container px-lg-4 py-4">
            <div class="text-center max-w-xl mx-auto mb-5">
                <span class="text-uppercase fw-bold text-success small tracking-wide">Quem Faz Acontecer</span>
                <h2 class="h2 fw-bold text-dark mt-2 mb-3">Equipe de Desenvolvimento & Propósito</h2>
                <p class="text-muted col-lg-8 mx-auto">
                    Conheça as áreas responsáveis pela concepção, engenharia, curadoria pedagógica e garantia de qualidade do projeto Vest+.
                </p>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <!-- Membro 1: Tech Lead & Engenharia -->
                <div class="col">
                    <div class="card card-custom h-100 p-4 text-center hover-lift">
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center bg-success text-white mb-3 shadow-sm" style="width: 72px; height: 72px;">
                            <i class="bi bi-code-slash fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Duilio Brito Segura</h5>
                        <span class="badge badge-discipline mb-3">Tech Lead & Full Stack</span>
                        <p class="text-muted small lh-base mb-0">
                            Responsável pela arquitetura MVC, modelagem relacional PDO, segurança anti-XSS/CSRF e motor assíncrono Vanilla JS.
                        </p>
                    </div>
                </div>

                <!-- Membro 2: Conteúdo & Artigos -->
                <div class="col">
                    <div class="card card-custom h-100 p-4 text-center hover-lift">
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center bg-dark text-white mb-3 shadow-sm" style="width: 72px; height: 72px;">
                            <i class="bi bi-pencil-fill fs-3 text-success"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Curadoria de Conteúdo</h5>
                        <span class="badge bg-light text-secondary border mb-3">Equipe de Artigos</span>
                        <p class="text-muted small lh-base mb-0">
                            Pesquisa, redação e formatação didática dos artigos teóricos com foco nos tópicos de maior incidência no ENEM e na FUVEST.
                        </p>
                    </div>
                </div>

                <!-- Membro 3: Banco de Questões -->
                <div class="col">
                    <div class="card card-custom h-100 p-4 text-center hover-lift">
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center bg-dark text-white mb-3 shadow-sm" style="width: 72px; height: 72px;">
                            <i class="bi bi-patch-question-fill fs-3 text-success"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Banco & Resoluções</h5>
                        <span class="badge bg-light text-secondary border mb-3">Pesquisa de Questões</span>
                        <p class="text-muted small lh-base mb-0">
                            Seleção de questões oficiais de múltipla escolha e elaboração das resoluções explicativas passo a passo.
                        </p>
                    </div>
                </div>

                <!-- Membro 4: QA & Validação -->
                <div class="col">
                    <div class="card card-custom h-100 p-4 text-center hover-lift">
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center bg-dark text-white mb-3 shadow-sm" style="width: 72px; height: 72px;">
                            <i class="bi bi-clipboard2-check-fill fs-3 text-success"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">QA & Usabilidade</h5>
                        <span class="badge bg-light text-secondary border mb-3">Qualidade & Testes</span>
                        <p class="text-muted small lh-base mb-0">
                            Validação contínua de rotas, testes de fumaça automatizados, compatibilidade mobile e critérios de aceitação.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         CTA FINAL
         ============================================================================== -->
    <section class="py-5 bg-white border-top">
        <div class="container px-lg-4 py-4">
            <div class="p-5 rounded-4 text-white text-center shadow-lg" style="background: linear-gradient(135deg, #064e3b 0%, #059669 100%);">
                <h2 class="display-6 fw-bold mb-3">Pronto para acelerar sua aprovação?</h2>
                <p class="fs-5 text-light opacity-90 col-md-8 mx-auto mb-4" style="color: #ecfdf5 !important;">
                    Junte-se ao Vest+ agora mesmo. Crie sua conta gratuita em menos de 1 minuto e comece a praticar hoje.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-light btn-lg px-4 py-3 fw-bold text-success shadow-sm">
                        <i class="bi bi-person-plus-fill me-1"></i> Criar Conta Gratuita
                    </a>
                    <a href="<?= Helper::url('/login') ?>" class="btn btn-outline-light btn-lg px-4 py-3">
                        Fazer Login
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         FOOTER INSTITUCIONAL
         ============================================================================== -->
    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container px-lg-4">
            <div class="row g-4 justify-content-between">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-mortarboard-fill text-success fs-3" style="color: #34d399 !important;"></i>
                        <span class="fs-4 fw-bold text-white">Vest+</span>
                    </div>
                    <p class="text-secondary small lh-base mb-3" style="color: #94a3b8 !important;">
                        Plataforma aberta de CMS e Banco de Questões voltada para vestibulandos e candidatos do ENEM. Desenvolvida em PHP 8.2+ puro com Bootstrap 5 e Vanilla JavaScript.
                    </p>
                    <div class="text-secondary small" style="color: #64748b !important;">
                        Licença MIT &bull; Código Aberto no <a href="https://github.com/DuilioBSegura/VestMais-Project" class="text-light" target="_blank">GitHub</a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <h6 class="text-uppercase fw-bold text-white small mb-3">Navegação</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="#sobre" class="text-secondary">Sobre o Projeto</a></li>
                        <li><a href="#recursos" class="text-secondary">Recursos da Plataforma</a></li>
                        <li><a href="#disciplinas" class="text-secondary">Disciplinas</a></li>
                        <li><a href="#equipe" class="text-secondary">Equipe do Projeto</a></li>
                    </ul>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <h6 class="text-uppercase fw-bold text-white small mb-3">Acesso Rápido</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="<?= Helper::url('/login') ?>" class="text-secondary">Acessar Painel (Login)</a></li>
                        <li><a href="<?= Helper::url('/cadastro') ?>" class="text-secondary">Criar Conta de Aluno</a></li>
                        <li><a href="https://github.com/DuilioBSegura/VestMais-Project" class="text-secondary" target="_blank">Repositório Oficial</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-4 mt-4 border-top border-secondary border-opacity-25 text-center text-secondary small" style="color: #64748b !important;">
                &copy; <?= date('Y') ?> Vest+ Platform. Todos os direitos reservados. Foco na aprovação e no conhecimento livre.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
