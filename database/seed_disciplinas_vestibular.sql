-- ==============================================================================
-- PROJETO: Vest+ - Plataforma Gratuita de Estudos para Vestibulares
-- ARQUIVO: database/seed_disciplinas_vestibular.sql
-- DESCRIÇÃO: Carga completa e idempotente das 14 disciplinas oficiais de vestibulares
-- ==============================================================================

USE `vestplus`;

INSERT INTO `disciplinas` (`nome`, `slug`, `descricao`) VALUES
('Matemática', 'matematica', 'Aritmética, Álgebra, Geometria, Funções, Trigonometria, Estatística e Probabilidade aplicadas ao ENEM e grandes vestibulares.'),
('Biologia', 'biologia', 'Citologia, Genética, Ecologia, Evolução, Fisiologia Humana e Botânica direcionadas aos vestibulares nacionais.'),
('Química', 'quimica', 'Química Geral, Físico-Química, Química Orgânica e Atomística com foco nas principais matrizes de vestibular.'),
('Física', 'fisica', 'Mecânica, Termologia, Óptica, Ondulatória, Eletromagnetismo e Física Moderna aplicadas a questões contextualizadas.'),
('História', 'historia', 'História Geral e História do Brasil, explorando transformações sociais, políticas e culturais com pensamento crítico.'),
('Geografia', 'geografia', 'Geografia Humana, Econômica, Política e Física, análise de relevo, clima, cartografia e geopolítica contemporânea.'),
('Sociologia', 'sociologia', 'Teoria sociológica clássica e contemporânea, cidadania, movimentos sociais, direitos humanos e cultura.'),
('Filosofia', 'filosofia', 'História da filosofia antiga, medieval, moderna e contemporânea, ética, política, epistemologia e lógica.'),
('Língua Portuguesa', 'lingua-portuguesa', 'Gramática aplicada, interpretação de texto, coesão, coerência, gêneros textuais e variação linguística.'),
('Literatura', 'literatura', 'Escolas literárias brasileiras e portuguesas, análise de obras obrigatórias, figuras de linguagem e teoria da literatura.'),
('Artes', 'artes', 'História da arte, manifestações artísticas contemporâneas, artes visuais, música, teatro e patrimônio cultural.'),
('Educação Física', 'educacao-fisica', 'Cultura corporal do movimento, esportes, saúde, inclusão social e manifestações corporais contemporâneas.'),
('Inglês', 'ingles', 'Leitura, interpretação textual em língua inglesa, vocabulário instrumental e estruturas sintáticas para o vestibular.'),
('Espanhol', 'espanhol', 'Compreensão e interpretação de textos em língua espanhola, falsos cognatos, conectivos e diversidade linguística.')
ON DUPLICATE KEY UPDATE
    `nome` = VALUES(`nome`),
    `descricao` = VALUES(`descricao`);
