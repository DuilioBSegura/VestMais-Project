-- ==============================================================================
-- PROJETO: Vest+ - Plataforma de CMS e Banco de Questões
-- FASE: Preparação para Produção (Carga Completa de Disciplinas)
-- ARQUIVO: database/seed_disciplinas_vestibular.sql
-- COMPATIBILIDADE: MySQL 8.0+ / MariaDB 10.4+ (InnoDB, UTF-8 MB4)
--
-- DESCRIÇÃO:
-- Cadastra as 14 disciplinas oficiais cobradas no ENEM e nos principais vestibulares.
-- Utiliza ON DUPLICATE KEY UPDATE no slug para garantir idempotência sem quebrar
-- os artigos e questões previamente associados às matérias já existentes.
-- ==============================================================================

USE `vestplus`;

INSERT INTO `disciplinas` (`nome`, `slug`, `descricao`)
VALUES
    ('Matemática', 'matematica', 'Álgebra, geometria, estatística, funções e matemática financeira.'),
    ('Biologia', 'biologia', 'Ecologia, genética, citologia, fisiologia e evolução.'),
    ('Química', 'quimica', 'Química geral, físico-química, química orgânica e meio ambiente.'),
    ('Física', 'fisica', 'Mecânica, termologia, óptica, ondulatória e eletromagnetismo.'),
    ('História', 'historia', 'História do Brasil, história geral, patrimônio e historiografia.'),
    ('Geografia', 'geografia', 'Geopolítica, cartografia, geografia física, humana e econômica.'),
    ('Sociologia', 'sociologia', 'Cidadania, movimentos sociais, cultura, política e trabalho.'),
    ('Filosofia', 'filosofia', 'Ética, teoria do conhecimento, filosofia antiga, moderna e contemporânea.'),
    ('Língua Portuguesa', 'lingua-portuguesa', 'Interpretação de textos, gramática, norma-padrão e gêneros textuais.'),
    ('Literatura', 'literatura', 'Escolas literárias, análise de obras, movimentos artísticos e cânone brasileiro.'),
    ('Artes', 'artes', 'História da arte, expressões culturais, música, teatro e artes visuais.'),
    ('Educação Física', 'educacao-fisica', 'Práticas corporais, saúde, esporte e lazer na sociedade.'),
    ('Inglês', 'ingles', 'Compreensão leitora, vocabulário e interpretação de textos em língua inglesa.'),
    ('Espanhol', 'espanhol', 'Compreensão leitora, vocabulário e interpretação de textos em língua espanhola.')
ON DUPLICATE KEY UPDATE
    `nome` = VALUES(`nome`),
    `descricao` = VALUES(`descricao`);
