<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Helper;

$currentUser = Auth::user();
$dashboardUrl = ($currentUser && $currentUser['tipo'] === 'admin') 
    ? Helper::url('/admin/dashboard') 
    : Helper::url('/aluno/dashboard');

// Lista igualitária dos integrantes da equipe de criação e desenvolvimento
$equipeIntegrantes = [
    [
        'nome'     => 'Duilio Brito Segura',
        'iniciais' => 'DS',
    ],
    [
        'nome'     => 'Arthur Henrique Rodrigues',
        'iniciais' => 'AR',
    ],
    [
        'nome'     => 'Gabriel Santos Oliveira',
        'iniciais' => 'GO',
    ],
    [
        'nome'     => 'Lucas Ferreira Lima',
        'iniciais' => 'LL',
    ],
];
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
                        <a class="nav-link" href="#equipe">Equipe</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if ($currentUser): ?>
                        <a href="<?= $dashboardUrl ?>" class="btn btn-primary btn-pill d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-speedometer2"></i> Meu Dashboard
                        </a>
                        <a href="<?= Helper::url('/logout') ?>" class="btn btn-outline-light btn-sm rounded-circle p-2" title="Encerrar sessão">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= Helper::url('/login') ?>" class="btn btn-outline-light btn-pill px-4">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                        </a>
                        <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-primary btn-pill px-4 shadow-sm">
                            Criar Conta Gratuita
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ==============================================================================
         HERO SECTION (ACOLHEDORA & IMPACTO VISUAL PARA VESTIBULANDOS)
         ============================================================================== -->
    <header class="hero-gradient text-white py-5 py-lg-6" style="padding-top: 4.5rem; padding-bottom: 4.5rem;">
        <div class="container px-lg-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <!-- Badges de Foco -->
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="hero-badge">
                            <i class="bi bi-stars"></i> 100% Gratuito & Aberto
                        </span>
                        <span class="badge bg-light bg-opacity-10 text-white border border-light border-opacity-25 rounded-pill px-3 py-2">
                            Preparação Focada: ENEM &bull; FUVEST &bull; UNICAMP &bull; VUNESP
                        </span>
                    </div>

                    <h1 class="display-4 fw-bold lh-sm text-white mb-3">
                        Sua jornada rumo à aprovação <span style="color: #34d399;">começa aqui</span>.
                    </h1>

                    <p class="fs-5 text-light opacity-90 mb-4 lh-base" style="color: #e2e8f0 !important; max-width: 620px;">
                        Acesse artigos teóricos estruturados e resolva questões oficiais comentadas passo a passo. Uma experiência de estudos completa, gratuita e pensada para a rotina do vestibulando.
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        <?php if ($currentUser): ?>
                            <a href="<?= $dashboardUrl ?>" class="btn btn-primary btn-pill btn-lg shadow d-inline-flex align-items-center gap-2">
                                <i class="bi bi-arrow-right-circle-fill fs-5"></i> Continuar Meus Estudos
                            </a>
                        <?php else: ?>
                            <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-primary btn-pill btn-lg shadow d-inline-flex align-items-center gap-2">
                                <i class="bi bi-rocket-takeoff-fill fs-5"></i> Começar a Estudar Agora
                            </a>
                            <a href="<?= Helper::url('/login') ?>" class="btn btn-outline-light btn-pill btn-lg px-4">
                                <i class="bi bi-door-open me-1"></i> Entrar na Plataforma
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Métricas Rápidas no Hero -->
                    <div class="row g-3 mt-4 pt-4 border-top border-light border-opacity-10">
                        <div class="col-auto">
                            <div class="fw-bold fs-3 text-white"><?= count($disciplinas) ?>+</div>
                            <div class="small opacity-75">Disciplinas Base</div>
                        </div>
                        <div class="col-auto px-4 border-start border-light border-opacity-10">
                            <div class="fw-bold fs-3 text-white"><?= $totalArtigos ?>+</div>
                            <div class="small opacity-75">Artigos Teóricos</div>
                        </div>
                        <div class="col-auto px-4 border-start border-light border-opacity-10">
                            <div class="fw-bold fs-3 text-white"><?= $totalQuestoes ?>+</div>
                            <div class="small opacity-75">Questões Comentadas</div>
                        </div>
                    </div>
                </div>

                <!-- Card Flutuante de Simulação em Tempo Real -->
                <div class="col-lg-5">
                    <div class="hero-card-floating p-4 text-dark">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-discipline px-2 py-1">Matemática</span>
                                <span class="badge badge-category rounded-pill">Funções</span>
                            </div>
                            <span class="badge bg-success text-white small px-2 py-1">Simulado Interativo</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Exemplo de Resolução com Feedback Imediato:</h6>
                        <p class="small text-secondary mb-3">
                            Uma função afim determina o valor do frete com taxa fixa de R$ 12,00 somada a R$ 3,50 por km. Para uma viagem de 40 km, qual o valor final?
                        </p>
                        <div class="list-group list-group-flush small mb-3">
                            <div class="list-group-item px-0 py-2 d-flex align-items-center gap-2 text-muted">
                                <span class="badge bg-secondary rounded-pill">A</span> R$ 140,00
                            </div>
                            <div class="list-group-item px-0 py-2 d-flex align-items-center gap-2 text-muted">
                                <span class="badge bg-secondary rounded-pill">B</span> R$ 148,00
                            </div>
                            <div class="list-group-item px-0 py-2 d-flex align-items-center gap-2 fw-bold text-success bg-success-subtle rounded-2 px-2">
                                <span class="badge bg-success rounded-pill">C</span> R$ 152,00 &bull; <em>Gabarito Correto!</em>
                            </div>
                            <div class="list-group-item px-0 py-2 d-flex align-items-center gap-2 text-muted">
                                <span class="badge bg-secondary rounded-pill">D</span> R$ 160,00
                            </div>
                        </div>
                        <div class="alert alert-success py-2 px-3 mb-0 small rounded-3">
                            <i class="bi bi-lightbulb-fill text-success me-1"></i>
                            <strong>Explicação:</strong> $f(40) = 12 + (3{,}50 \times 40) = 12 + 140 = \text{R\$\ } 152{,}00$.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ==============================================================================
         SEÇÃO SOBRE O PROJETO (MISSÃO SOCIAL E GRATUIDADE)
         ============================================================================== -->
    <section id="sobre" class="py-5 bg-white border-bottom">
        <div class="container px-lg-4 py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-uppercase fw-bold text-success small tracking-wide">Propósito & Missão</span>
                    <h2 class="display-6 fw-bold text-dark mt-2 mb-3">
                        Educação de qualidade, acessível e sem barreiras financeiras
                    </h2>
                    <p class="text-secondary lh-lg mb-3">
                        O <strong>Vest+</strong> nasceu com a missão clara de democratizar a preparação pré-vestibular. Acreditamos que o acesso a resumos teóricos claros e exercícios com explicações detalhadas deve ser livre para todos os estudantes do Brasil.
                    </p>
                    <p class="text-secondary lh-lg mb-4">
                        Aqui você não encontra pegadinhas de assinaturas nem conteúdos bloqueados. Todas as matérias, artigos didáticos e resoluções comentadas estão abertos para apoiar o seu ritmo de estudo.
                    </p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>100% Gratuito e Aberto</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Questões Comentadas</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Teoria em Artigos Focados</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Feedback Imediato</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card card-custom p-4 p-md-5 border-0 shadow-sm bg-light">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success fs-3">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Feito por e para quem estuda</h5>
                                <p class="text-muted small mb-0">Desenvolvimento com foco na melhor experiência de aprendizagem</p>
                            </div>
                        </div>
                        <p class="text-secondary small lh-lg mb-0">
                            Construído sobre pilares de código limpo em PHP moderno, arquitetura MVC sem dependências pesadas e interfaces rápidas, o Vest+ oferece alta performance tanto no computador quanto no celular para que seu foco seja único: <em>aprender e ser aprovado</em>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         SEÇÃO DE RECURSOS (PILHARES DIDÁTICOS)
         ============================================================================== -->
    <section id="recursos" class="py-5" style="background-color: var(--vest-bg);">
        <div class="container px-lg-4 py-4">
            <div class="text-center max-w-xl mx-auto mb-5">
                <span class="text-uppercase fw-bold text-success small tracking-wide">Como a Plataforma Funciona</span>
                <h2 class="display-6 fw-bold text-dark mt-2 mb-3">Tudo o que você precisa em um só lugar</h2>
                <p class="text-muted col-lg-8 mx-auto">
                    Conheça as três ferramentas essenciais desenvolvidas para estruturar e acelerar seu aprendizado.
                </p>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Recurso 1: Teoria Direta -->
                <div class="col">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-journal-richtext"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Teoria Descomplicada</h4>
                        <p class="text-muted small lh-base mb-3">
                            Artigos estruturados com linguagem clara, sínteses dos pontos mais cobrados nos vestibulares e caixas de dicas práticas do ENEM.
                        </p>
                        <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                            <li><i class="bi bi-check2 text-success me-1"></i> Resumos organizados por assunto</li>
                            <li><i class="bi bi-check2 text-success me-1"></i> Estimativa do tempo de leitura</li>
                            <li><i class="bi bi-check2 text-success me-1"></i> Navegação rápida por tópicos</li>
                        </ul>
                    </div>
                </div>

                <!-- Recurso 2: Banco de Questões -->
                <div class="col">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-patch-question-fill"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Questões com Gabarito Comentado</h4>
                        <p class="text-muted small lh-base mb-3">
                            Exercícios de múltipla escolha com correção instantânea e explicação pedagógica do porquê cada alternativa está certa ou errada.
                        </p>
                        <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                            <li><i class="bi bi-check2 text-success me-1"></i> Resolução assíncrona sem recarregar a tela</li>
                            <li><i class="bi bi-check2 text-success me-1"></i> Justificativas passo a passo</li>
                            <li><i class="bi bi-check2 text-success me-1"></i> Padrão oficial ENEM e vestibulares</li>
                        </ul>
                    </div>
                </div>

                <!-- Recurso 3: Filtros por Matéria -->
                <div class="col">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-funnel-fill"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark mb-2">Filtros Inteligentes</h4>
                        <p class="text-muted small lh-base mb-3">
                            Encontre rapidamente o conteúdo ou a lista de exercícios que você quer estudar por matéria específica ou categoria temática.
                        </p>
                        <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                            <li><i class="bi bi-check2 text-success me-1"></i> Seleção de disciplina em um clique</li>
                            <li><i class="bi bi-check2 text-success me-1"></i> Cruzamento dinâmico de categorias</li>
                            <li><i class="bi bi-check2 text-success me-1"></i> Estudo personalizado para o seu cronograma</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         GRADE CURRICULAR (DISCIPLINAS)
         ============================================================================== -->
    <section id="disciplinas" class="py-5 bg-white border-top border-bottom">
        <div class="container px-lg-4 py-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <span class="text-uppercase fw-bold text-success small tracking-wide">Acervo Didático</span>
                    <h2 class="h3 fw-bold text-dark mb-0 mt-1">Disciplinas Disponíveis</h2>
                </div>
                <a href="<?= Helper::url('/cadastro') ?>" class="btn btn-outline-primary btn-pill btn-sm">
                    Cadastre-se para Praticar Todas &rarr;
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
         SEÇÃO DA EQUIPE (APRESENTAÇÃO COLETIVA & IGUALITÁRIA)
         ============================================================================== -->
    <section id="equipe" class="py-5" style="background-color: var(--vest-bg);">
        <div class="container px-lg-4 py-4">
            <div class="text-center max-w-xl mx-auto mb-5">
                <span class="text-uppercase fw-bold text-success small tracking-wide">Quem Faz Acontecer</span>
                <h2 class="display-6 fw-bold text-dark mt-2 mb-3">Equipe de Desenvolvimento & Criação</h2>
                <p class="text-muted col-lg-8 mx-auto">
                    O projeto Vest+ é fruto do trabalho colaborativo e integrado de seus idealizadores, unindo pesquisa, engenharia de software e compromisso com a democratização do conhecimento.
                </p>
            </div>

            <!-- Grid Igualitário de Integrantes da Equipe -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 justify-content-center">
                <?php foreach ($equipeIntegrantes as $membro): ?>
                    <div class="col">
                        <div class="team-card-minimal">
                            <div class="team-avatar-circle">
                                <?= Helper::e($membro['iniciais']) ?>
                            </div>
                            <h5 class="fw-bold text-dark mb-1"><?= Helper::e($membro['nome']) ?></h5>
                            <span class="badge badge-discipline rounded-pill small px-3 py-1 mt-2">
                                Equipe Vest+
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
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
                        Licença MIT &bull; Código Aberto no <a href="https://github.com/DuilioBSegura/VestMais-Project" class="text-light text-decoration-underline" target="_blank">GitHub</a>
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
