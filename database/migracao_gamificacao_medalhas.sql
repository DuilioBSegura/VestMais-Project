-- ==============================================================================
-- PROJETO: Vest+ - Plataforma de CMS e Banco de Questões
-- SPRINT 5: Gamificação & Sistema de Medalhas e Conquistas do Estudante
-- ARQUIVO: database/migracao_gamificacao_medalhas.sql
-- COMPATIBILIDADE: MySQL 8.0+ / MariaDB 10.4+
-- ==============================================================================

USE `vestplus`;

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. TABELA: usuario_respostas
-- Registra o histórico individual de resolução de simulados e questões pelo aluno.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario_respostas` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED NOT NULL,
    `questao_id` INT UNSIGNED NOT NULL,
    `alternativa_marcada` ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
    `acertou` TINYINT(1) NOT NULL DEFAULT 0,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_usuario_respostas` PRIMARY KEY (`id`),
    CONSTRAINT `fk_usuario_respostas_user` FOREIGN KEY (`usuario_id`)
        REFERENCES `usuarios` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_usuario_respostas_questao` FOREIGN KEY (`questao_id`)
        REFERENCES `questoes` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    INDEX `idx_usuario_respostas_user` (`usuario_id`),
    INDEX `idx_usuario_respostas_questao` (`questao_id`),
    INDEX `idx_usuario_respostas_acertou` (`usuario_id`, `acertou`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. TABELA: medalhas
-- Catálogo oficial de conquistas e badges disponíveis para desbloqueio.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `medalhas` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `slug` VARCHAR(80) NOT NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `descricao` TEXT NOT NULL,
    `categoria` VARCHAR(50) NOT NULL DEFAULT 'geral',
    `icone` VARCHAR(80) NOT NULL DEFAULT 'bi-trophy-fill',
    `cor` VARCHAR(30) NOT NULL DEFAULT 'emerald',
    `criterio_tipo` VARCHAR(50) NOT NULL,
    `criterio_valor` INT NOT NULL DEFAULT 1,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_medalhas` PRIMARY KEY (`id`),
    CONSTRAINT `uk_medalhas_slug` UNIQUE (`slug`),
    INDEX `idx_medalhas_criterio` (`criterio_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. TABELA PIVOT: usuario_medalhas
-- Registra quais medalhas já foram desbloqueadas por cada estudante.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario_medalhas` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED NOT NULL,
    `medalha_id` INT UNSIGNED NOT NULL,
    `conquistada_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_usuario_medalhas` PRIMARY KEY (`id`),
    CONSTRAINT `uk_usuario_medalha` UNIQUE (`usuario_id`, `medalha_id`),
    CONSTRAINT `fk_usuario_medalhas_user` FOREIGN KEY (`usuario_id`)
        REFERENCES `usuarios` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_usuario_medalhas_medalha` FOREIGN KEY (`medalha_id`)
        REFERENCES `medalhas` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- POPULAÇÃO INICIAL: Catálogo de Medalhas e Conquistas do Vestibulando
-- ==============================================================================
INSERT INTO `medalhas` (`slug`, `titulo`, `descricao`, `categoria`, `icone`, `cor`, `criterio_tipo`, `criterio_valor`)
VALUES
    (
        'primeiro-passo',
        'Primeiro Passo',
        'Respondeu à sua primeira questão no simulado interativo do Vest+.',
        'iniciante',
        'bi-play-circle-fill',
        'emerald',
        'total_questoes',
        1
    ),
    (
        'sequencia-implacavel',
        'Sequência Implacável',
        'Acertou 3 questões consecutivas no gabarito oficial sem cometer erros.',
        'precisao',
        'bi-fire',
        'danger',
        'sequencia_acertos',
        3
    ),
    (
        'ritmo-de-prova',
        'Ritmo de Prova',
        'Resolveu com dedicação 5 questões no banco de simulados.',
        'volume',
        'bi-lightning-charge-fill',
        'warning',
        'total_questoes',
        5
    ),
    (
        'maratonista-vestibular',
        'Maratonista do ENEM',
        'Alcançou a marca expressiva de 15 questões respondidas na plataforma.',
        'volume',
        'bi-trophy-fill',
        'purple',
        'total_questoes',
        15
    ),
    (
        'precisao-cirurgica',
        'Precisão Cirúrgica',
        'Alcançou 5 acertos confirmados no gabarito do banco de questões.',
        'precisao',
        'bi-bullseye',
        'emerald',
        'total_acertos',
        5
    ),
    (
        'explorador-exatas',
        'Mestre de Exatas',
        'Respondeu a 3 questões de disciplinas da área de Ciências Exatas (Matemática e Física).',
        'disciplina',
        'bi-calculator-fill',
        'info',
        'disciplina_exatas',
        3
    ),
    (
        'ciencias-natureza',
        'Cientista da Natureza',
        'Respondeu a 3 questões de Biologia ou Química.',
        'disciplina',
        'bi-flower1',
        'success',
        'disciplina_natureza',
        3
    ),
    (
        'pensador-humanas',
        'Pensador Crítico',
        'Respondeu a 3 questões da área de Ciências Humanas (História, Geografia ou Filosofia).',
        'disciplina',
        'bi-book-half',
        'primary',
        'disciplina_humanas',
        3
    )
ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`), `descricao` = VALUES(`descricao`);
