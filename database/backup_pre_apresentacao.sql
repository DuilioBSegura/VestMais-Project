-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: vestplus
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `artigo_categorias`
--

DROP TABLE IF EXISTS `artigo_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artigo_categorias` (
  `artigo_id` int(10) unsigned NOT NULL,
  `categoria_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`artigo_id`,`categoria_id`),
  KEY `idx_artigo_categorias_cat_art` (`categoria_id`,`artigo_id`),
  CONSTRAINT `fk_artigo_categorias_artigo` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_artigo_categorias_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `artigo_categorias`
--

LOCK TABLES `artigo_categorias` WRITE;
/*!40000 ALTER TABLE `artigo_categorias` DISABLE KEYS */;
INSERT INTO `artigo_categorias` VALUES (1,1),(2,5);
/*!40000 ALTER TABLE `artigo_categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `artigos`
--

DROP TABLE IF EXISTS `artigos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artigos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `disciplina_id` int(10) unsigned NOT NULL,
  `conteudo` longtext NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_artigos_slug` (`slug`),
  KEY `idx_artigos_disc_criado` (`disciplina_id`,`criado_em`),
  KEY `idx_artigos_criado_em` (`criado_em`),
  CONSTRAINT `fk_artigos_disciplina` FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `artigos`
--

LOCK TABLES `artigos` WRITE;
/*!40000 ALTER TABLE `artigos` DISABLE KEYS */;
INSERT INTO `artigos` VALUES (1,'Funções Afim e Quadrática: Conceitos Essenciais e Aplicações no ENEM','funcoes-afim-e-quadratica-conceitos-essenciais-enem',1,'# Funções Afim e Quadrática no ENEM\n\nO estudo de funções representa uma das habilidades mais cobradas na prova de Matemática e suas Tecnologias do ENEM. Saber modelar fenômenos cotidianos por meio de equações algébricas é determinante para um bom desempenho.\n\n## 1. Função Afim (1º Grau)\nUma função afim possui a lei de formação geral dada por:\n`f(x) = ax + b`\n\nOnde:\n- **a** é o coeficiente angular (taxa de variação média);\n- **b** é o coeficiente linear (ponto em que a reta intercepta o eixo vertical das ordenadas y).\n\n> Nota: Em funções afins a taxa de variação (coeficiente a) é estritamente constante ao longo de todo o domínio da reta.\n\n### Aplicações Frequentes\n- Cobrança de tarifas de táxi e corridas por aplicativo (taxa fixa + valor por km rodado);\n- Planos de telefonia e serviços de consumo de água e energia elétrica.\n\n## 2. Função Quadrática (2º Grau)\nPossui lei geral expressa por:\n`f(x) = ax² + bx + c` (com `a ≠ 0`).\n\nSua representação gráfica é uma parábola. Quando `a > 0`, a concavidade é voltada para cima (ponto de mínimo); quando `a < 0`, a concavidade é para baixo (ponto de máximo).\n\n### Coordenadas do Vértice\n- `Xv = -b / (2a)`\n- `Yv = -Δ / (4a)`, onde `Δ = b² - 4ac`\n\n> Dica ENEM: Questões que envolvem ponto de máximo ou mínimo quase sempre pedem as coordenadas do vértice. Quando o exercício perguntar \"quando\" ou \"qual quantidade produz o pico\", calcule Xv. Se a pergunta for \"qual o lucro máximo\" ou \"qual a altura máxima atingida\", calcule Yv!','2026-10-09 17:36:29','2026-10-09 19:39:47'),(2,'Ecologia Trófica: Fluxo de Energia e Bioacumulação nos Ecossistemas','ecologia-trofica-fluxo-de-energia-e-bioacumulacao',3,'# Ecologia Trófica: Fluxo de Energia e Cadeias Alimentares\n\nA ecologia é invariavelmente a área de Biologia com o maior número de questões na prova de Ciências da Natureza do ENEM. Compreender como a matéria circula e a energia flui é essencial para gabaritar a prova.\n\n## 1. Níveis Tróficos\n- **Produtores (Autótrofos):** Vegetais, algas e cianobactérias capazes de sintetizar matéria orgânica via fotossíntese ou quimiossíntese.\n- **Consumidores (Heterótrofos):** Herbívoros (primários), carnívoros (secundários, terciários etc.).\n- **Decompositores:** Fungos e bactérias que reciclam nutrientes orgânicos de volta ao meio mineral.\n\n## 2. Unidirecionalidade do Fluxo de Energia\nDiferente dos elementos químicos que participam de ciclos biogeoquímicos fechados, a energia solar captada pelos produtores flui de maneira **unidirecional e decrescente**. A cada nível trófico subsequente, grande parte da energia é dissipada na forma de calor metabólico (cerca de 90% de perda em cada passagem trófica).\n\n> Nota: A pirâmide de energia NUNCA pode ser invertida, pois o fluxo energético decresce continuamente dos produtores aos consumidores de topo.\n\n## 3. Bioacumulação vs. Biomagnificação Trófica\n- **Bioacumulação:** Acúmulo de poluentes não biodegradáveis (ex: mercúrio, chumbo, agrotóxicos organoclorados) nos tecidos de um mesmo indivíduo ao longo do tempo.\n- **Biomagnificação Trófica:** Ocorrência progressiva de concentrações mais altas do contaminante nos níveis tróficos superiores.\n\n> Dica ENEM: Atenção redobrada na diferença entre bioacumulação (no próprio indivíduo) e biomagnificação (ao longo dos níveis da teia alimentar). O predador de topo sempre apresentará a maior concentração de toxinas acumuladas!','2026-10-09 17:36:29','2026-10-09 19:39:47');
/*!40000 ALTER TABLE `artigos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categorias_slug` (`slug`),
  KEY `idx_categorias_nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Álgebra e Funções','algebra-e-funcoes','2026-10-09 17:36:29'),(2,'Geometria Espacial','geometria-espacial','2026-10-09 17:36:29'),(3,'Cinemática e Movimento','cinematica-e-movimento','2026-10-09 17:36:29'),(4,'Termodinâmica e Calor','termodinamica-e-calor','2026-10-09 17:36:29'),(5,'Ecologia e Meio Ambiente','ecologia-e-meio-ambiente','2026-10-09 17:36:29'),(6,'Citologia e Genética','citologia-e-genetica','2026-10-09 17:36:29');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disciplinas`
--

DROP TABLE IF EXISTS `disciplinas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disciplinas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `descricao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_disciplinas_slug` (`slug`),
  KEY `idx_disciplinas_nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disciplinas`
--

LOCK TABLES `disciplinas` WRITE;
/*!40000 ALTER TABLE `disciplinas` DISABLE KEYS */;
INSERT INTO `disciplinas` VALUES (1,'Matemática','matematica','Álgebra, Geometria, Trigonometria, Funções e Estatística para ENEM e vestibulares.','2026-10-09 17:30:33'),(2,'Física','fisica','Mecânica, Termologia, Óptica, Ondulatória e Eletricidade para ENEM e vestibulares.','2026-10-09 17:30:33'),(3,'Biologia','biologia','Citologia, Genética, Ecologia, Fisiologia e Evolução para ENEM e vestibulares.','2026-10-09 17:30:33');
/*!40000 ALTER TABLE `disciplinas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questao_categorias`
--

DROP TABLE IF EXISTS `questao_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questao_categorias` (
  `questao_id` int(10) unsigned NOT NULL,
  `categoria_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`questao_id`,`categoria_id`),
  KEY `idx_questao_categorias_cat_q` (`categoria_id`,`questao_id`),
  CONSTRAINT `fk_questao_categorias_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_questao_categorias_questao` FOREIGN KEY (`questao_id`) REFERENCES `questoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questao_categorias`
--

LOCK TABLES `questao_categorias` WRITE;
/*!40000 ALTER TABLE `questao_categorias` DISABLE KEYS */;
INSERT INTO `questao_categorias` VALUES (1,1),(2,3),(3,5);
/*!40000 ALTER TABLE `questao_categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questoes`
--

DROP TABLE IF EXISTS `questoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questoes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `disciplina_id` int(10) unsigned NOT NULL,
  `enunciado` longtext NOT NULL,
  `alternativa_a` text NOT NULL,
  `alternativa_b` text NOT NULL,
  `alternativa_c` text NOT NULL,
  `alternativa_d` text NOT NULL,
  `alternativa_e` text NOT NULL,
  `resposta_correta` enum('A','B','C','D','E') NOT NULL,
  `explicacao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_questoes_disc_id` (`disciplina_id`,`id`),
  KEY `idx_questoes_criado_em` (`criado_em`),
  CONSTRAINT `fk_questoes_disciplina` FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questoes`
--

LOCK TABLES `questoes` WRITE;
/*!40000 ALTER TABLE `questoes` DISABLE KEYS */;
INSERT INTO `questoes` VALUES (1,1,'Uma transportadora estabelece o valor cobrado por frete através de uma tarifa fixa de partida no valor de R$ 12,00 somada a um custo variável de R$ 3,50 por quilômetro percorrido. Um cliente contratou o envio de uma remessa de livros didáticos entre duas cidades cuja distância rodoviária é de 40 quilômetros. O valor total pago pelo cliente pelo serviço de entrega foi de:','R$ 140,00','R$ 148,00','R$ 152,00','R$ 160,00','R$ 164,00','C','Resolução detalhada:\nA relação do custo total pode ser expressa pela função afim C(x) = b + ax, onde:\n- b = R$ 12,00 (valor fixo)\n- a = R$ 3,50 (valor por quilômetro)\n- x = 40 km (distância percorrida)\n\nC(40) = 12 + (3,50 * 40)\nC(40) = 12 + 140 = R$ 152,00.\nPortanto, a alternativa correta é a letra C.','2026-10-09 17:36:29'),(2,2,'Em um teste de segurança automotiva em linha reta, um veículo inicialmente em repouso acelera com aceleração escalar constante de 3,0 m/s² durante um intervalo de 8,0 segundos. Desconsiderando qualquer efeito dissipativo do ar, a distância total percorrida pelo automóvel ao término desse tempo é de:','48 metros','72 metros','96 metros','120 metros','192 metros','C','Resolução detalhada:\nTrata-se de um Movimento Uniformemente Variado (MUV). A equação horária do espaço é dada por:\nS = S₀ + V₀·t + (a·t²)/2\n\nComo o veículo partiu do repouso, S₀ = 0 e V₀ = 0:\nS = (3,0 · 8,0²) / 2\nS = (3,0 · 64) / 2 = 192 / 2 = 96 metros.\nPortanto, a alternativa correta é a letra C.','2026-10-09 17:36:29'),(3,3,'Em um ecossistema aquático continental contaminado pelo descarte inadequado de efluentes industriais contendo mercúrio (composto não biodegradável), pesquisadores avaliaram os teores desse metal tóxico em diferentes componentes de uma teia trófica: fitoplâncton, zooplâncton, lambaris (pequenos peixes) e garças-brancas (aves piscívoras). A maior concentração de mercúrio por unidade de biomassa foi constatada:','No fitoplâncton, pois são os produtores que retiram o elemento diretamente da coluna d\'água.','No zooplâncton, em decorrência da taxa acelerada de filtragem da água em relação ao seu volume.','Nos lambaris, devido ao armazenamento primário do poluente nas escamas e no tecido muscular.','Nas garças-brancas, em virtude do acúmulo biológico progressivo conhecido como magnificação trófica.','De forma equivalente em todos os níveis, haja vista a diluição homogênea do elemento no meio aquático.','D','Resolução detalhada:\nSubstâncias bioacumulativas e não metabolizáveis, como o mercúrio e compostos organoclorados, não são eliminadas pelos organismos e passam de um nível trófico ao seguinte. Devido à diminuição da biomassa a cada nível superior, a concentração do contaminante por quilograma de tecido aumenta exponencialmente ao longo da cadeia alimentar (magnificação trófica). Por ocupar o topo da cadeia nessa teia, a garça-branca apresentará a maior concentração residual do metal pesado. Portanto, a resposta correta é a alternativa D.','2026-10-09 17:36:29');
/*!40000 ALTER TABLE `questoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `email` varchar(191) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('aluno','admin') NOT NULL DEFAULT 'aluno',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuarios_email` (`email`),
  KEY `idx_usuarios_tipo` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (3,'Duilio Brito Segura','duiliobsegura@gmail.com','$2y$10$NUqBfWL5QxxS.gqSsreg0ud8Nqd4KOriws2mj9LZpnVjidbYurIFC','admin','2026-10-09 19:33:41'),(4,'Estudante Vest+','estudante@vestmais.com.br','$2y$10$JWl3qvmhYOZBROmyLQp7PuTwyWyPuzh7rLFDn8hTlOzCmc5xRjxUa','aluno','2026-10-09 19:33:41');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-14 19:48:53
