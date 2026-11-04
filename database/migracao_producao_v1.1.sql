-- ==============================================================================
-- Vest+ Platform - Script de Migração Idempotente de Produção (v1.1.0)
-- Arquivo: database/migracao_producao_v1.1.sql
-- Finalidade: Atualiza com segurança bancos existentes sem perda de dados
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. Atualização da Tabela de Usuários (Configurações de Perfil & Foco)
-- ------------------------------------------------------------------------------
ALTER TABLE `usuarios` 
    ADD COLUMN IF NOT EXISTS `vestibular_foco` VARCHAR(50) NOT NULL DEFAULT 'ENEM' AFTER `tipo`,
    ADD COLUMN IF NOT EXISTS `bio` VARCHAR(255) NULL AFTER `vestibular_foco`,
    ADD COLUMN IF NOT EXISTS `telefone` VARCHAR(20) NULL AFTER `bio`;

-- ------------------------------------------------------------------------------
-- 1.1 Vínculo de Categorias a Disciplinas Existentes
-- ------------------------------------------------------------------------------
ALTER TABLE `categorias`
    ADD COLUMN IF NOT EXISTS `disciplina_id` INT UNSIGNED NULL AFTER `nome`;

SET @fk_exists = (
    SELECT COUNT(*) FROM information_schema.table_constraints 
    WHERE table_schema = DATABASE() AND table_name = 'categorias' AND constraint_name = 'fk_categorias_disciplina'
);
SET @sql_fk = IF(@fk_exists = 0, 'ALTER TABLE categorias ADD CONSTRAINT fk_categorias_disciplina FOREIGN KEY (disciplina_id) REFERENCES disciplinas (id) ON DELETE SET NULL ON UPDATE CASCADE', 'SELECT 1');
PREPARE stmt_fk FROM @sql_fk;
EXECUTE stmt_fk;
DEALLOCATE PREPARE stmt_fk;

-- ------------------------------------------------------------------------------
-- 2. Tabela de Histórico de Resoluções do Estudante (Gamificação)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario_respostas` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED NOT NULL,
    `questao_id` INT UNSIGNED NOT NULL,
    `alternativa_marcada` ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
    `acertou` TINYINT(1) NOT NULL DEFAULT 0,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_usuario_respostas` PRIMARY KEY (`id`),
    CONSTRAINT `fk_usuario_respostas_user` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_usuario_respostas_questao` FOREIGN KEY (`questao_id`) REFERENCES `questoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_usuario_respostas_user` (`usuario_id`),
    INDEX `idx_usuario_respostas_questao` (`questao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Tabela de Medalhas / Conquistas
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
    CONSTRAINT `uk_medalhas_slug` UNIQUE (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Tabela de Relação Usuário-Medalhas (Conquistas Desbloqueadas)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario_medalhas` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED NOT NULL,
    `medalha_id` INT UNSIGNED NOT NULL,
    `conquistada_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_usuario_medalhas` PRIMARY KEY (`id`),
    CONSTRAINT `uk_usuario_medalha` UNIQUE (`usuario_id`, `medalha_id`),
    CONSTRAINT `fk_usuario_medalhas_user` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_usuario_medalhas_medalha` FOREIGN KEY (`medalha_id`) REFERENCES `medalhas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. Carga Idempotente das 8 Medalhas Oficiais do Vest+
-- ------------------------------------------------------------------------------
INSERT INTO `medalhas` (`slug`, `titulo`, `descricao`, `categoria`, `icone`, `cor`, `criterio_tipo`, `criterio_valor`)
VALUES
    ('primeiro-passo', 'Primeiro Passo', 'Respondeu à sua primeira questão no simulado interativo do Vest+.', 'iniciante', 'bi-play-circle-fill', 'emerald', 'total_questoes', 1),
    ('sequencia-implacavel', 'Sequência Implacável', 'Acertou 3 questões consecutivas no gabarito oficial sem cometer erros.', 'precisao', 'bi-fire', 'danger', 'sequencia_acertos', 3),
    ('ritmo-de-prova', 'Ritmo de Prova', 'Resolveu com dedicação 5 questões no banco de simulados.', 'volume', 'bi-lightning-charge-fill', 'warning', 'total_questoes', 5),
    ('maratonista-vestibular', 'Maratonista do ENEM', 'Alcançou a marca expressiva de 15 questões respondidas na plataforma.', 'volume', 'bi-trophy-fill', 'warning', 'total_questoes', 15),
    ('precisao-cirurgica', 'Precisão Cirúrgica', 'Alcançou 5 acertos confirmados no gabarito do banco de questões.', 'precisao', 'bi-bullseye', 'info', 'total_acertos', 5),
    ('mestre-de-exatas', 'Mestre de Exatas', 'Respondeu a 3 questões de disciplinas da área de Ciências Exatas (Matemática e Física).', 'disciplina', 'bi-calculator-fill', 'primary', 'disciplina_exatas', 3),
    ('cientista-da-natureza', 'Cientista da Natureza', 'Respondeu a 3 questões de Biologia ou Química.', 'disciplina', 'bi-flower1', 'emerald', 'disciplina_natureza', 3),
    ('pensador-critico', 'Pensador Crítico', 'Respondeu a 3 questões da área de Ciências Humanas (História, Geografia ou Filosofia).', 'disciplina', 'bi-lightbulb-fill', 'purple', 'disciplina_humanas', 3)
ON DUPLICATE KEY UPDATE 
    `titulo` = VALUES(`titulo`),
    `descricao` = VALUES(`descricao`),
    `icone` = VALUES(`icone`),
    `cor` = VALUES(`cor`),
    `criterio_tipo` = VALUES(`criterio_tipo`),
    `criterio_valor` = VALUES(`criterio_valor`);

-- ------------------------------------------------------------------------------
-- 6. Índices de Alta Performance (Caso não existam)
-- ------------------------------------------------------------------------------
SET @exist_idx_artigos_disc = (
    SELECT COUNT(*) FROM information_schema.statistics 
    WHERE table_schema = DATABASE() AND table_name = 'artigos' AND index_name = 'idx_artigos_disciplina'
);
SET @sql_idx_artigos = IF(@exist_idx_artigos_disc = 0, 'ALTER TABLE artigos ADD INDEX idx_artigos_disciplina (disciplina_id)', 'SELECT 1');
PREPARE stmt FROM @sql_idx_artigos;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;
