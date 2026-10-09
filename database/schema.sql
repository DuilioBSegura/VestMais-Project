-- ==============================================================================
-- PROJETO: Vest+ - Plataforma de CMS e Banco de Questões
-- SPRINT 1: Setup da Codebase & Arquitetura Base
-- ARQUIVO: database/schema.sql
-- COMPATIBILIDADE: MySQL 8.0+ / MariaDB 10.4+
-- PADRÃO: ENGINE=InnoDB, DEFAULT CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci
-- ==============================================================================

-- Criação do banco de dados (caso não exista)
CREATE DATABASE IF NOT EXISTS `vestplus`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `vestplus`;

-- Desativa temporariamente a verificação de chaves estrangeiras para limpeza/migração
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. TABELA: usuarios
-- Armazena as contas de acesso (alunos e administradores da plataforma).
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nome` VARCHAR(150) NOT NULL,
    `email` VARCHAR(191) NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    `tipo` ENUM('aluno', 'admin') NOT NULL DEFAULT 'aluno',
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_usuarios` PRIMARY KEY (`id`),
    CONSTRAINT `uk_usuarios_email` UNIQUE (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. TABELA: disciplinas
-- Armazena as matérias estruturais do vestibular/ENEM (ex: Matemática, Física).
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `disciplinas` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL,
    `descricao` TEXT NULL,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_disciplinas` PRIMARY KEY (`id`),
    CONSTRAINT `uk_disciplinas_slug` UNIQUE (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. TABELA: categorias
-- Armazena os tópicos/assuntos vinculados aos conteúdos e questões.
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `pk_categorias` PRIMARY KEY (`id`),
    CONSTRAINT `uk_categorias_slug` UNIQUE (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- SEEDS INICIAIS (Dados de Fundação)
-- ==============================================================================

-- 1. Usuário Administrador Inicial
-- Credenciais padrão:
--   E-mail: admin@vestplus.com.br
--   Senha:  admin123
--   Hash:   $2y$10$3W8pY3OPwnLs8IylhPow0.NtLxy962NEag8L77bvStFw7MGAKwJAe (PASSWORD_BCRYPT)
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `tipo`)
VALUES (
    'Administrador Vest+',
    'admin@vestplus.com.br',
    '$2y$10$3W8pY3OPwnLs8IylhPow0.NtLxy962NEag8L77bvStFw7MGAKwJAe',
    'admin'
) ON DUPLICATE KEY UPDATE `email` = `email`;

-- 2. Disciplinas Base
INSERT INTO `disciplinas` (`nome`, `slug`, `descricao`)
VALUES
    ('Matemática', 'matematica', 'Álgebra, Geometria, Trigonometria, Funções e Estatística para ENEM e vestibulares.'),
    ('Física', 'fisica', 'Mecânica, Termologia, Óptica, Ondulatória e Eletricidade para ENEM e vestibulares.'),
    ('Biologia', 'biologia', 'Citologia, Genética, Ecologia, Fisiologia e Evolução para ENEM e vestibulares.')
ON DUPLICATE KEY UPDATE `slug` = `slug`;
