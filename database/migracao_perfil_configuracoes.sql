-- ==============================================================================
-- PROJETO: Vest+ - Plataforma de CMS e Banco de Questões
-- SPRINT 4: Configurações de Usuário e Personalização de Perfil
-- ARQUIVO: database/migracao_perfil_configuracoes.sql
-- ==============================================================================

USE `vestplus`;

-- Adiciona campos de perfil e personalização do estudante/admin
ALTER TABLE `usuarios`
    ADD COLUMN IF NOT EXISTS `vestibular_foco` VARCHAR(50) NOT NULL DEFAULT 'ENEM' AFTER `tipo`,
    ADD COLUMN IF NOT EXISTS `bio` VARCHAR(255) NULL AFTER `vestibular_foco`,
    ADD COLUMN IF NOT EXISTS `telefone` VARCHAR(20) NULL AFTER `bio`;
