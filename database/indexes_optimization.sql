-- ==============================================================================
-- PROJETO: Vest+ - Plataforma de CMS e Banco de Questões
-- FASE: Pré-Apresentação (Auditoria de Performance - RNF08 e RNF09)
-- ARQUIVO: database/indexes_optimization.sql
-- COMPATIBILIDADE: MySQL 8.0+ / MariaDB 10.4+ (InnoDB, UTF-8 MB4)
--
-- DESCRIÇÃO:
-- Adiciona índices de alta performance para otimizar:
-- 1. Buscas e filtros de questões por disciplina e categoria (reduz tempo para < 50ms).
-- 2. Listagem de artigos teóricos e ordenação cronológica.
-- 3. Verificação de papéis (RBAC) e integridade nas tabelas associativas N:N.
-- ==============================================================================

USE `vestplus`;

-- 1. Tabela: usuarios
CREATE INDEX IF NOT EXISTS `idx_usuarios_tipo`
    ON `usuarios` (`tipo`);

-- 2. Tabela: disciplinas
CREATE INDEX IF NOT EXISTS `idx_disciplinas_nome`
    ON `disciplinas` (`nome`);

-- 3. Tabela: categorias
CREATE INDEX IF NOT EXISTS `idx_categorias_nome`
    ON `categorias` (`nome`);

-- 4. Tabela: artigos
CREATE INDEX IF NOT EXISTS `idx_artigos_criado_em`
    ON `artigos` (`criado_em`);

CREATE INDEX IF NOT EXISTS `idx_artigos_disc_criado`
    ON `artigos` (`disciplina_id`, `criado_em`);

-- 5. Tabela: questoes
CREATE INDEX IF NOT EXISTS `idx_questoes_criado_em`
    ON `questoes` (`criado_em`);

CREATE INDEX IF NOT EXISTS `idx_questoes_disc_id`
    ON `questoes` (`disciplina_id`, `id`);

-- 6. Tabela Pivot: artigo_categorias (Índice reverso para otimização de joins)
CREATE INDEX IF NOT EXISTS `idx_artigo_categorias_cat_art`
    ON `artigo_categorias` (`categoria_id`, `artigo_id`);

-- 7. Tabela Pivot: questao_categorias (Índice reverso essencial para filtro por categoria)
CREATE INDEX IF NOT EXISTS `idx_questao_categorias_cat_q`
    ON `questao_categorias` (`categoria_id`, `questao_id`);
