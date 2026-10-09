-- ==============================================================================
-- PROJETO: Vest+ - Plataforma de CMS e Banco de Questões
-- SPRINT 2: Motor de Conteúdo - Artigos Teóricos e Banco de Questões (Admin)
-- ARQUIVO: database/sprint2_content.sql
-- COMPATIBILIDADE: MySQL 8.0+ / MariaDB 10.4+
-- PADRÃO: ENGINE=InnoDB, DEFAULT CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci
-- ==============================================================================

USE `vestplus`;

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. TABELA: artigos
-- Armazena o conteúdo teórico formatado para vestibulandos e estudantes do ENEM.
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
    CONSTRAINT `fk_artigos_disciplina` FOREIGN KEY (`disciplina_id`)
        REFERENCES `disciplinas` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. TABELA: questoes
-- Armazena o banco de questões de múltipla escolha com alternativas A a E e explicação.
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
    `resposta_correta` ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
    `explicacao` TEXT NULL,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_questoes` PRIMARY KEY (`id`),
    CONSTRAINT `fk_questoes_disciplina` FOREIGN KEY (`disciplina_id`)
        REFERENCES `disciplinas` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. TABELA PIVOT N:N: artigo_categorias
-- Relacionamento N:N entre artigos teóricos e múltiplas categorias/tópicos.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `artigo_categorias` (
    `artigo_id` INT UNSIGNED NOT NULL,
    `categoria_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `pk_artigo_categorias` PRIMARY KEY (`artigo_id`, `categoria_id`),
    CONSTRAINT `fk_artigo_categorias_artigo` FOREIGN KEY (`artigo_id`)
        REFERENCES `artigos` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_artigo_categorias_categoria` FOREIGN KEY (`categoria_id`)
        REFERENCES `categorias` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. TABELA PIVOT N:N: questao_categorias
-- Relacionamento N:N entre questões e múltiplos tópicos/categorias.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `questao_categorias` (
    `questao_id` INT UNSIGNED NOT NULL,
    `categoria_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `pk_questao_categorias` PRIMARY KEY (`questao_id`, `categoria_id`),
    CONSTRAINT `fk_questao_categorias_questao` FOREIGN KEY (`questao_id`)
        REFERENCES `questoes` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_questao_categorias_categoria` FOREIGN KEY (`categoria_id`)
        REFERENCES `categorias` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- POPULAÇÃO INICIAL: Categorias de Estudo Essenciais
-- ==============================================================================
INSERT INTO `categorias` (`id`, `nome`, `slug`)
VALUES
    (1, 'Álgebra e Funções', 'algebra-e-funcoes'),
    (2, 'Geometria Espacial', 'geometria-espacial'),
    (3, 'Cinemática e Movimento', 'cinematica-e-movimento'),
    (4, 'Termodinâmica e Calor', 'termodinamica-e-calor'),
    (5, 'Ecologia e Meio Ambiente', 'ecologia-e-meio-ambiente'),
    (6, 'Citologia e Genética', 'citologia-e-genetica')
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`), `slug` = VALUES(`slug`);

-- ==============================================================================
-- SEEDS INICIAIS: Artigos Teóricos (2 Artigos Reais)
-- ==============================================================================
INSERT INTO `artigos` (`id`, `titulo`, `slug`, `disciplina_id`, `conteudo`)
VALUES
(
    1,
    'Funções Afim e Quadrática: Conceitos Essenciais e Aplicações no ENEM',
    'funcoes-afim-e-quadratica-conceitos-essenciais-enem',
    1,
    '# Funções Afim e Quadrática no ENEM\n\nO estudo de funções representa uma das habilidades mais cobradas na prova de Matemática e suas Tecnologias do ENEM. Saber modelar fenômenos cotidianos por meio de equações algébricas é determinante para um bom desempenho.\n\n## 1. Função Afim (1º Grau)\nUma função afim possui a lei de formação geral dada por:\n`f(x) = ax + b`\n\nOnde:\n- **a** é o coeficiente angular (taxa de variação média);\n- **b** é o coeficiente linear (ponto em que a reta intercepta o eixo vertical das ordenadas y).\n\n> Nota: Em funções afins a taxa de variação (coeficiente a) é estritamente constante ao longo de todo o domínio da reta.\n\n### Aplicações Frequentes\n- Cobrança de tarifas de táxi e corridas por aplicativo (taxa fixa + valor por km rodado);\n- Planos de telefonia e serviços de consumo de água e energia elétrica.\n\n## 2. Função Quadrática (2º Grau)\nPossui lei geral expressa por:\n`f(x) = ax² + bx + c` (com `a ≠ 0`).\n\nSua representação gráfica é uma parábola. Quando `a > 0`, a concavidade é voltada para cima (ponto de mínimo); quando `a < 0`, a concavidade é para baixo (ponto de máximo).\n\n### Coordenadas do Vértice\n- `Xv = -b / (2a)`\n- `Yv = -Δ / (4a)`, onde `Δ = b² - 4ac`\n\n> Dica ENEM: Questões que envolvem ponto de máximo ou mínimo quase sempre pedem as coordenadas do vértice. Quando o exercício perguntar "quando" ou "qual quantidade produz o pico", calcule Xv. Se a pergunta for "qual o lucro máximo" ou "qual a altura máxima atingida", calcule Yv!'
),
(
    2,
    'Ecologia Trófica: Fluxo de Energia e Bioacumulação nos Ecossistemas',
    'ecologia-trofica-fluxo-de-energia-e-bioacumulacao',
    3,
    '# Ecologia Trófica: Fluxo de Energia e Cadeias Alimentares\n\nA ecologia é invariavelmente a área de Biologia com o maior número de questões na prova de Ciências da Natureza do ENEM. Compreender como a matéria circula e a energia flui é essencial para gabaritar a prova.\n\n## 1. Níveis Tróficos\n- **Produtores (Autótrofos):** Vegetais, algas e cianobactérias capazes de sintetizar matéria orgânica via fotossíntese ou quimiossíntese.\n- **Consumidores (Heterótrofos):** Herbívoros (primários), carnívoros (secundários, terciários etc.).\n- **Decompositores:** Fungos e bactérias que reciclam nutrientes orgânicos de volta ao meio mineral.\n\n## 2. Unidirecionalidade do Fluxo de Energia\nDiferente dos elementos químicos que participam de ciclos biogeoquímicos fechados, a energia solar captada pelos produtores flui de maneira **unidirecional e decrescente**. A cada nível trófico subsequente, grande parte da energia é dissipada na forma de calor metabólico (cerca de 90% de perda em cada passagem trófica).\n\n> Nota: A pirâmide de energia NUNCA pode ser invertida, pois o fluxo energético decresce continuamente dos produtores aos consumidores de topo.\n\n## 3. Bioacumulação vs. Biomagnificação Trófica\n- **Bioacumulação:** Acúmulo de poluentes não biodegradáveis (ex: mercúrio, chumbo, agrotóxicos organoclorados) nos tecidos de um mesmo indivíduo ao longo do tempo.\n- **Biomagnificação Trófica:** Ocorrência progressiva de concentrações mais altas do contaminante nos níveis tróficos superiores.\n\n> Dica ENEM: Atenção redobrada na diferença entre bioacumulação (no próprio indivíduo) e biomagnificação (ao longo dos níveis da teia alimentar). O predador de topo sempre apresentará a maior concentração de toxinas acumuladas!'
)
ON DUPLICATE KEY UPDATE
    `titulo` = VALUES(`titulo`),
    `disciplina_id` = VALUES(`disciplina_id`),
    `conteudo` = VALUES(`conteudo`);

-- Relações de Categorias para Artigos
INSERT INTO `artigo_categorias` (`artigo_id`, `categoria_id`)
VALUES
    (1, 1), -- Artigo 1 -> Álgebra e Funções
    (2, 5)  -- Artigo 2 -> Ecologia e Meio Ambiente
ON DUPLICATE KEY UPDATE `artigo_id` = `artigo_id`;

-- ==============================================================================
-- SEEDS INICIAIS: Questões de Múltipla Escolha (3 Questões Reais com Gabarito)
-- ==============================================================================
INSERT INTO `questoes` (`id`, `disciplina_id`, `enunciado`, `alternativa_a`, `alternativa_b`, `alternativa_c`, `alternativa_d`, `alternativa_e`, `resposta_correta`, `explicacao`)
VALUES
(
    1,
    1,
    'Uma transportadora estabelece o valor cobrado por frete através de uma tarifa fixa de partida no valor de R$ 12,00 somada a um custo variável de R$ 3,50 por quilômetro percorrido. Um cliente contratou o envio de uma remessa de livros didáticos entre duas cidades cuja distância rodoviária é de 40 quilômetros. O valor total pago pelo cliente pelo serviço de entrega foi de:',
    'R$ 140,00',
    'R$ 148,00',
    'R$ 152,00',
    'R$ 160,00',
    'R$ 164,00',
    'C',
    'Resolução detalhada:\nA relação do custo total pode ser expressa pela função afim C(x) = b + ax, onde:\n- b = R$ 12,00 (valor fixo)\n- a = R$ 3,50 (valor por quilômetro)\n- x = 40 km (distância percorrida)\n\nC(40) = 12 + (3,50 * 40)\nC(40) = 12 + 140 = R$ 152,00.\nPortanto, a alternativa correta é a letra C.'
),
(
    2,
    2,
    'Em um teste de segurança automotiva em linha reta, um veículo inicialmente em repouso acelera com aceleração escalar constante de 3,0 m/s² durante um intervalo de 8,0 segundos. Desconsiderando qualquer efeito dissipativo do ar, a distância total percorrida pelo automóvel ao término desse tempo é de:',
    '48 metros',
    '72 metros',
    '96 metros',
    '120 metros',
    '192 metros',
    'C',
    'Resolução detalhada:\nTrata-se de um Movimento Uniformemente Variado (MUV). A equação horária do espaço é dada por:\nS = S₀ + V₀·t + (a·t²)/2\n\nComo o veículo partiu do repouso, S₀ = 0 e V₀ = 0:\nS = (3,0 · 8,0²) / 2\nS = (3,0 · 64) / 2 = 192 / 2 = 96 metros.\nPortanto, a alternativa correta é a letra C.'
),
(
    3,
    3,
    'Em um ecossistema aquático continental contaminado pelo descarte inadequado de efluentes industriais contendo mercúrio (composto não biodegradável), pesquisadores avaliaram os teores desse metal tóxico em diferentes componentes de uma teia trófica: fitoplâncton, zooplâncton, lambaris (pequenos peixes) e garças-brancas (aves piscívoras). A maior concentração de mercúrio por unidade de biomassa foi constatada:',
    'No fitoplâncton, pois são os produtores que retiram o elemento diretamente da coluna d\'água.',
    'No zooplâncton, em decorrência da taxa acelerada de filtragem da água em relação ao seu volume.',
    'Nos lambaris, devido ao armazenamento primário do poluente nas escamas e no tecido muscular.',
    'Nas garças-brancas, em virtude do acúmulo biológico progressivo conhecido como magnificação trófica.',
    'De forma equivalente em todos os níveis, haja vista a diluição homogênea do elemento no meio aquático.',
    'D',
    'Resolução detalhada:\nSubstâncias bioacumulativas e não metabolizáveis, como o mercúrio e compostos organoclorados, não são eliminadas pelos organismos e passam de um nível trófico ao seguinte. Devido à diminuição da biomassa a cada nível superior, a concentração do contaminante por quilograma de tecido aumenta exponencialmente ao longo da cadeia alimentar (magnificação trófica). Por ocupar o topo da cadeia nessa teia, a garça-branca apresentará a maior concentração residual do metal pesado. Portanto, a resposta correta é a alternativa D.'
)
ON DUPLICATE KEY UPDATE
    `disciplina_id` = VALUES(`disciplina_id`),
    `enunciado` = VALUES(`enunciado`),
    `alternativa_a` = VALUES(`alternativa_a`),
    `alternativa_b` = VALUES(`alternativa_b`),
    `alternativa_c` = VALUES(`alternativa_c`),
    `alternativa_d` = VALUES(`alternativa_d`),
    `alternativa_e` = VALUES(`alternativa_e`),
    `resposta_correta` = VALUES(`resposta_correta`),
    `explicacao` = VALUES(`explicacao`);

-- Relações de Categorias para Questões
INSERT INTO `questao_categorias` (`questao_id`, `categoria_id`)
VALUES
    (1, 1), -- Questão 1 -> Álgebra e Funções
    (2, 3), -- Questão 2 -> Cinemática e Movimento
    (3, 5)  -- Questão 3 -> Ecologia e Meio Ambiente
ON DUPLICATE KEY UPDATE `questao_id` = `questao_id`;
