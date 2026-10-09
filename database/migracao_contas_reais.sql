-- ==============================================================================
-- PROJETO: Vest+ - Plataforma de CMS e Banco de Questões
-- SPRINT: Migração e Limpeza de Contas Reais
-- ARQUIVO: database/migracao_contas_reais.sql
-- COMPATIBILIDADE: MySQL 8.0+ / MariaDB 10.4+ (InnoDB, UTF-8 MB4)
--
-- DESCRIÇÃO:
-- 1. Remove os usuários seed / mockados criados durante os testes iniciais.
-- 2. Registra o Administrador Real do projeto com senha hasheada com PASSWORD_DEFAULT.
-- 3. Registra a conta de demonstração do Aluno para homologação acadêmica.
-- ==============================================================================

USE `vestplus`;

START TRANSACTION;

-- 1. Remoção de contas seeds antigas / mockadas
DELETE FROM `usuarios` 
WHERE `email` IN (
    'admin@vestmais.local',
    'aluno@vestmais.local',
    'admin@vestplus.com.br',
    'aluno@vestplus.com.br'
);

-- 2. Inserção do Administrador Real
-- Credenciais:
--   E-mail: duiliobsegura@gmail.com
--   Senha:  AdminVest@2026
--   Hash:   $2y$10$NUqBfWL5QxxS.gqSsreg0ud8Nqd4KOriws2mj9LZpnVjidbYurIFC
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `tipo`, `criado_em`)
VALUES (
    'Duilio Brito Segura',
    'duiliobsegura@gmail.com',
    '$2y$10$NUqBfWL5QxxS.gqSsreg0ud8Nqd4KOriws2mj9LZpnVjidbYurIFC',
    'admin',
    NOW()
) ON DUPLICATE KEY UPDATE 
    `nome` = VALUES(`nome`),
    `senha` = VALUES(`senha`),
    `tipo` = VALUES(`tipo`);

-- 3. Inserção da Conta de Estudante (Homologação / Demonstração)
-- Credenciais:
--   E-mail: estudante@vestmais.com.br
--   Senha:  AlunoVest@2026
--   Hash:   $2y$10$JWl3qvmhYOZBROmyLQp7PuTwyWyPuzh7rLFDn8hTlOzCmc5xRjxUa
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `tipo`, `criado_em`)
VALUES (
    'Estudante Vest+',
    'estudante@vestmais.com.br',
    '$2y$10$JWl3qvmhYOZBROmyLQp7PuTwyWyPuzh7rLFDn8hTlOzCmc5xRjxUa',
    'aluno',
    NOW()
) ON DUPLICATE KEY UPDATE 
    `nome` = VALUES(`nome`),
    `senha` = VALUES(`senha`),
    `tipo` = VALUES(`tipo`);

COMMIT;
