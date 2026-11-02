-- ==============================================================================
-- PROJETO: Vest+ - Plataforma de Estudos e Banco de Questões
-- ARQUIVO: database/docker-init/01_init.sql
-- DESCRIÇÃO: Script consolidado de inicialização para containerização Docker
-- PADRÃO: MariaDB 11+ / MySQL 8.0+ (InnoDB, UTF-8 MB4 Unicode CI)
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. TABELA: usuarios
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nome` VARCHAR(150) NOT NULL,
    `email` VARCHAR(191) NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    `tipo` ENUM('aluno', 'admin') NOT NULL DEFAULT 'aluno',
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_usuarios` PRIMARY KEY (`id`),
    CONSTRAINT `uk_usuarios_email` UNIQUE (`email`),
    INDEX `idx_usuarios_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. TABELA: disciplinas
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `disciplinas` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL,
    `descricao` TEXT NULL,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_disciplinas` PRIMARY KEY (`id`),
    CONSTRAINT `uk_disciplinas_slug` UNIQUE (`slug`),
    INDEX `idx_disciplinas_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. TABELA: categorias
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_categorias` PRIMARY KEY (`id`),
    CONSTRAINT `uk_categorias_slug` UNIQUE (`slug`),
    INDEX `idx_categorias_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. TABELA: artigos
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `artigos` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `titulo` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `disciplina_id` INT UNSIGNED NOT NULL,
    `conteudo` LONGTEXT NOT NULL,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `pk_artigos` PRIMARY KEY (`id`),
    CONSTRAINT `uk_artigos_slug` UNIQUE (`slug`),
    CONSTRAINT `fk_artigos_disciplina` FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_artigos_disc_id` (`disciplina_id`, `id`),
    INDEX `idx_artigos_atualizado_em` (`atualizado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. TABELA: questoes
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `questoes` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `disciplina_id` INT UNSIGNED NOT NULL,
    `enunciado` LONGTEXT NOT NULL,
    `alternativa_a` TEXT NOT NULL,
    `alternativa_b` TEXT NOT NULL,
    `alternativa_c` TEXT NOT NULL,
    `alternativa_d` TEXT NOT NULL,
    `alternativa_e` TEXT NOT NULL,
    `gabarito` ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
    `explicacao` LONGTEXT NOT NULL,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_questoes` PRIMARY KEY (`id`),
    CONSTRAINT `fk_questoes_disciplina` FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_questoes_disc_id` (`disciplina_id`, `id`),
    INDEX `idx_questoes_criado_em` (`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. TABELA RELACIONAL: artigo_categorias (N:N)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `artigo_categorias` (
    `artigo_id` INT UNSIGNED NOT NULL,
    `categoria_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `pk_artigo_categorias` PRIMARY KEY (`artigo_id`, `categoria_id`),
    CONSTRAINT `fk_artigo_categorias_artigo` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_artigo_categorias_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_artigo_categorias_cat_art` (`categoria_id`, `artigo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. TABELA RELACIONAL: questao_categorias (N:N)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `questao_categorias` (
    `questao_id` INT UNSIGNED NOT NULL,
    `categoria_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `pk_questao_categorias` PRIMARY KEY (`questao_id`, `categoria_id`),
    CONSTRAINT `fk_questao_categorias_questao` FOREIGN KEY (`questao_id`) REFERENCES `questoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_questao_categorias_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_questao_categorias_cat_q` (`categoria_id`, `questao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- CARGA INICIAL (SEEDS): DISCIPLINAS OFICIAIS DO VESTIBULAR (14 MATÉRIAS)
-- ==============================================================================
INSERT INTO `disciplinas` (`id`, `nome`, `slug`, `descricao`)
VALUES
    (1, 'Matemática', 'matematica', 'Álgebra, geometria, estatística, funções e matemática financeira.'),
    (2, 'Física', 'fisica', 'Mecânica, termologia, óptica, ondulatória e eletromagnetismo.'),
    (3, 'Biologia', 'biologia', 'Ecologia, genética, citologia, fisiologia e evolução.'),
    (4, 'Química', 'quimica', 'Química geral, físico-química, química orgânica e meio ambiente.'),
    (5, 'História', 'historia', 'História do Brasil, história geral, patrimônio e historiografia.'),
    (6, 'Geografia', 'geografia', 'Geopolítica, cartografia, geografia física, humana e econômica.'),
    (7, 'Sociologia', 'sociologia', 'Cidadania, movimentos sociais, cultura, política e trabalho.'),
    (8, 'Filosofia', 'filosofia', 'Ética, teoria do conhecimento, filosofia antiga, moderna e contemporânea.'),
    (9, 'Língua Portuguesa', 'lingua-portuguesa', 'Interpretação de textos, gramática, norma-padrão e gêneros textuais.'),
    (10, 'Literatura', 'literatura', 'Escolas literárias, análise de obras, movimentos artísticos e cânone brasileiro.'),
    (11, 'Artes', 'artes', 'História da arte, expressões culturais, música, teatro e artes visuais.'),
    (12, 'Educação Física', 'educacao-fisica', 'Práticas corporais, saúde, esporte e lazer na sociedade.'),
    (13, 'Inglês', 'ingles', 'Compreensão leitora, vocabulário e interpretação de textos em língua inglesa.'),
    (14, 'Espanhol', 'espanhol', 'Compreensão leitora, vocabulário e interpretação de textos em língua espanhola.')
ON DUPLICATE KEY UPDATE
    `nome` = VALUES(`nome`),
    `descricao` = VALUES(`descricao`);

-- ==============================================================================
-- CARGA INICIAL (SEEDS): CATEGORIAS TEMÁTICAS
-- ==============================================================================
INSERT INTO `categorias` (`id`, `nome`, `slug`)
VALUES
    (1, 'Álgebra e Funções', 'algebra-e-funcoes'),
    (2, 'Geometria Espacial', 'geometria-espacial'),
    (3, 'Cinemática e Movimento', 'cinematica-e-movimento'),
    (4, 'Termodinâmica e Calor', 'termodinamica-e-calor'),
    (5, 'Ecologia e Meio Ambiente', 'ecologia-e-meio-ambiente'),
    (6, 'Citologia e Genética', 'citologia-e-genetica')
ON DUPLICATE KEY UPDATE
    `nome` = VALUES(`nome`);

-- ==============================================================================
-- CARGA INICIAL (SEEDS): CONTAS DE USUÁRIO DE PRODUÇÃO
-- ==============================================================================
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`)
VALUES
    (1, 'Duilio Brito Segura', 'duiliobsegura@gmail.com', '$2y$10$NUqBfWL5QxxS.gqSsreg0ud8Nqd4KOriws2mj9LZpnVjidbYurIFC', 'admin'),
    (2, 'Estudante Vest+', 'estudante@vestmais.com.br', '$2y$10$JWl3qvmhYOZBROmyLQp7PuTwyWyPuzh7rLFDn8hTlOzCmc5xRjxUa', 'aluno')
ON DUPLICATE KEY UPDATE
    `nome` = VALUES(`nome`),
    `senha` = VALUES(`senha`),
    `tipo` = VALUES(`tipo`);

-- ==============================================================================
-- CARGA INICIAL (SEEDS): ARTIGOS TEÓRICOS
-- ==============================================================================
INSERT INTO `artigos` (`id`, `titulo`, `slug`, `disciplina_id`, `conteudo`)
VALUES
    (1, 'Funções Afim e Quadrática: Conceitos Essenciais e Aplicações no ENEM', 'funcoes-afim-e-quadratica-conceitos-essenciais-enem', 1, '# Funções Afim e Quadrática no ENEM\n\nO estudo de funções representa uma das habilidades mais cobradas na prova de Matemática e suas Tecnologias do ENEM. Saber modelar fenômenos cotidianos por meio de equações algébricas é determinante para um bom desempenho.\n\n## 1. Função Afim (1º Grau)\nUma função afim possui a lei de formação geral dada por:\n`f(x) = ax + b`\n\nOnde:\n- **a** é o coeficiente angular (taxa de variação média);\n- **b** é o coeficiente linear (ponto em que a reta intercepta o eixo vertical das ordenadas y).\n\n> Nota: Em funções afins a taxa de variação (coeficiente a) é estritamente constante ao longo de todo o domínio da reta.\n\n### Aplicações Frequentes\n- Cobrança de tarifas de táxi e corridas por aplicativo (taxa fixa + valor por km rodado);\n- Planos de telefonia e serviços de consumo de água e energia elétrica.\n\n## 2. Função Quadrática (2º Grau)\nPossui lei geral expressa por:\n`f(x) = ax² + bx + c` (com `a ≠ 0`).\n\nSua representação gráfica é uma parábola. Quando `a > 0`, a concavidade é voltada para cima (ponto de mínimo); quando `a < 0`, a concavidade é para baixo (ponto de máximo).\n\n### Coordenadas do Vértice\n- `Xv = -b / (2a)`\n- `Yv = -Δ / (4a)`, onde `Δ = b² - 4ac`\n\n> Dica ENEM: Questões que envolvem ponto de máximo ou mínimo quase sempre pedem as coordenadas do vértice. Quando o exercício perguntar \"quando\" ou \"qual quantidade produz o pico\", calcule Xv. Se a pergunta for \"qual o lucro máximo\" ou \"qual a altura máxima atingida\", calcule Yv!'),
    (2, 'Ecologia Trófica: Fluxo de Energia e Bioacumulação nos Ecossistemas', 'ecologia-trofica-fluxo-de-energia-e-bioacumulacao', 3, '# Ecologia Trófica: Fluxo de Energia e Cadeias Alimentares\n\nA ecologia é invariavelmente a área de Biologia com o maior número de questões na prova de Ciências da Natureza do ENEM. Compreender como a matéria circula e a energia flui é essencial para gabaritar a prova.\n\n## 1. Níveis Tróficos\n- **Produtores (Autótrofos):** Vegetais, algas e cianobactérias capazes de sintetizar matéria orgânica via fotossíntese ou quimiossíntese.\n- **Consumidores (Heterótrofos):** Herbívoros (primários), carnívoros (secundários, terciários etc.).\n- **Decompositores:** Fungos e bactérias que reciclam nutrientes orgânicos de volta ao meio mineral.\n\n## 2. Unidirecionalidade do Fluxo de Energia\nDiferente dos elementos químicos que participam de ciclos biogeoquímicos fechados, a energia solar captada pelos produtores flui de maneira **unidirecional e decrescente**. A cada nível trófico subsequente, grande parte da energia é dissipada na forma de calor metabólico (cerca de 90% de perda em cada passagem trófica).\n\n> Nota: A pirâmide de energia NUNCA pode ser invertida, pois o fluxo energético decresce continuamente dos produtores aos consumidores de topo.\n\n## 3. Bioacumulação vs. Biomagnificação Trófica\n- **Bioacumulação:** Acúmulo de poluentes não biodegradáveis (ex: mercúrio, chumbo, agrotóxicos organoclorados) nos tecidos de um mesmo indivíduo ao longo do tempo.\n- **Biomagnificação Trófica:** Ocorrência progressiva de concentrações mais altas do contaminante nos níveis tróficos superiores.\n\n> Dica ENEM: Atenção redobrada na diferença entre bioacumulação (no próprio indivíduo) e biomagnificação (ao longo dos níveis da teia alimentar). O predador de topo sempre apresentará a maior concentração de toxinas acumuladas!')
ON DUPLICATE KEY UPDATE
    `titulo` = VALUES(`titulo`),
    `conteudo` = VALUES(`conteudo`);

-- ==============================================================================
-- CARGA INICIAL (SEEDS): QUESTÕES DE VESTIBULAR
-- ==============================================================================
INSERT INTO `questoes` (`id`, `disciplina_id`, `enunciado`, `alternativa_a`, `alternativa_b`, `alternativa_c`, `alternativa_d`, `alternativa_e`, `gabarito`, `explicacao`)
VALUES
    (1, 1, 'Uma transportadora estabelece o valor cobrado por frete através de uma tarifa fixa de partida no valor de R$ 12,00 somada a um custo variável de R$ 3,50 por quilômetro percorrido. Um cliente contratou o envio de uma remessa de livros didáticos entre duas cidades cuja distância rodoviária é de 40 quilômetros. O valor total pago pelo cliente pelo serviço de entrega foi de:', 'R$ 140,00', 'R$ 148,00', 'R$ 152,00', 'R$ 160,00', 'R$ 164,00', 'C', 'Resolução detalhada:\nA relação do custo total pode ser expressa pela função afim C(x) = b + ax, onde:\n- b = R$ 12,00 (valor fixo)\n- a = R$ 3,50 (valor por quilômetro)\n- x = 40 km (distância percorrida)\n\nC(40) = 12 + (3,50 * 40)\nC(40) = 12 + 140 = R$ 152,00.\nPortanto, a alternativa correta é a letra C.'),
    (2, 2, 'Em um teste de segurança automotiva em linha reta, um veículo inicialmente em repouso acelera com aceleração escalar constante de 3,0 m/s² durante um intervalo de 8,0 segundos. Desconsiderando qualquer efeito dissipativo do ar, a distância total percorrida pelo automóvel ao término desse tempo é de:', '48 metros', '72 metros', '96 metros', '120 metros', '192 metros', 'C', 'Resolução detalhada:\nTrata-se de um Movimento Uniformemente Variado (MUV). A equação horária do espaço é dada por:\nS = S₀ + V₀·t + (a·t²)/2\n\nComo o veículo partiu do repouso, S₀ = 0 e V₀ = 0:\nS = (3,0 · 8,0²) / 2\nS = (3,0 · 64) / 2 = 192 / 2 = 96 metros.\nPortanto, a alternativa correta é a letra C.'),
    (3, 3, 'Em um ecossistema aquático continental contaminado pelo descarte inadequado de efluentes industriais contendo mercúrio (composto não biodegradável), pesquisadores avaliaram os teores desse metal tóxico em diferentes componentes de uma teia trófica: fitoplâncton, zooplâncton, lambaris (pequenos peixes) e garças-brancas (aves piscívoras). A maior concentração de mercúrio por unidade de biomassa foi constatada:', 'No fitoplâncton, pois são os produtores que retiram o elemento diretamente da coluna d\'água.', 'No zooplâncton, em decorrência da taxa acelerada de filtragem da água em relação ao seu volume.', 'Nos lambaris, devido ao armazenamento primário do poluente nas escamas e no tecido muscular.', 'Nas garças-brancas, em virtude do acúmulo biológico progressivo conhecido como magnificação trófica.', 'De forma equivalente em todos os níveis, haja vista a diluição homogênea do elemento no meio aquático.', 'D', 'Resolução detalhada:\nSubstâncias bioacumulativas e não metabolizáveis, como o mercúrio e compostos organoclorados, não são eliminadas pelos organismos e passam de um nível trófico ao seguinte. Devido à diminuição da biomassa a cada nível superior, a concentração do contaminante por quilograma de tecido aumenta exponencialmente ao longo da cadeia alimentar (magnificação trófica). Por ocupar o topo da cadeia nessa teia, a garça-branca apresentará a maior concentração residual do metal pesado. Portanto, a resposta correta é a alternativa D.')
ON DUPLICATE KEY UPDATE
    `enunciado` = VALUES(`enunciado`),
    `gabarito` = VALUES(`gabarito`),
    `explicacao` = VALUES(`explicacao`);

-- ==============================================================================
-- CARGA INICIAL (SEEDS): VÍNCULOS N:N (CATEGORIAS)
-- ==============================================================================
INSERT IGNORE INTO `artigo_categorias` (`artigo_id`, `categoria_id`) VALUES (1, 1), (2, 5);
INSERT IGNORE INTO `questao_categorias` (`questao_id`, `categoria_id`) VALUES (1, 1), (2, 3), (3, 5);

SET FOREIGN_KEY_CHECKS = 1;
