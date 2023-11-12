CREATE DATABASE  IF NOT EXISTS `clube_livros` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `clube_livros`;
-- MySQL dump 10.13  Distrib 8.0.32, for Win64 (x86_64)
--
-- Host: localhost    Database: clube_livros
-- ------------------------------------------------------
-- Server version	5.7.41-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '';

--
-- Dumping data for table `amigos`
--

LOCK TABLES `amigos` WRITE;
/*!40000 ALTER TABLE `amigos` DISABLE KEYS */;
INSERT INTO `amigos` VALUES (1,3,'2023-05-08 12:05:59'),(1,4,'2023-05-08 12:05:55'),(2,4,'2023-05-15 06:52:54'),(3,1,'2023-05-08 12:05:59'),(4,1,'2023-05-08 12:05:55'),(4,2,'2023-05-15 06:52:54');
/*!40000 ALTER TABLE `amigos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `assuntos`
--

LOCK TABLES `assuntos` WRITE;
/*!40000 ALTER TABLE `assuntos` DISABLE KEYS */;
INSERT INTO `assuntos` VALUES (1,'Fantasia','2023-04-16 22:22:43'),(2,'Ficção','2023-04-16 22:23:01'),(3,'Aventura','2023-04-16 22:23:26'),(4,'Direito','2023-05-15 04:55:55'),(5,'Direito Civil','2023-05-15 04:56:16'),(6,'Direito Penal','2023-05-15 04:56:22'),(7,'Direito Tributário','2023-05-15 04:56:29'),(8,'Direito Internacional','2023-05-15 04:56:35'),(9,'Linguagens de Programação','2023-05-15 04:56:49'),(10,'Java','2023-05-15 04:56:55'),(11,'Python','2023-05-15 04:57:01'),(12,'Go','2023-05-15 04:57:05'),(13,'.NET','2023-05-15 04:57:14'),(14,'C / C++','2023-05-15 04:57:24'),(15,'Javascript','2023-05-15 04:57:36'),(16,'HTML','2023-05-15 04:57:41'),(17,'Inteligência Artificial','2023-05-15 04:57:51'),(18,'Machine Learning','2023-05-15 04:58:01'),(19,'Redes de Computadores','2023-05-15 04:58:09'),(20,'Arquitetura de Software','2023-05-15 04:58:19'),(21,'Esportes','2023-05-15 04:58:26'),(22,'Futebol','2023-05-15 04:58:31'),(23,'Basquete','2023-05-15 04:58:36'),(24,'Vôlei','2023-05-15 04:58:43'),(25,'Música','2023-05-15 04:58:51'),(26,'Redes de Pesca','2023-05-15 04:59:43'),(27,'Lingua Portuguesa','2023-05-15 04:59:54'),(28,'Lingua Inglesa','2023-05-15 04:59:59'),(29,'Literatura Inglesa','2023-05-15 05:00:05'),(30,'Literatura Portuguesa','2023-05-15 05:00:10'),(31,'Literatura Russa','2023-05-15 05:00:14'),(32,'Literatura Espanhola','2023-05-15 05:00:25'),(33,'Literatura Brasileira','2023-05-15 05:53:22'),(34,'Suspense','2023-05-15 05:54:54');
/*!40000 ALTER TABLE `assuntos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `autores`
--

LOCK TABLES `autores` WRITE;
/*!40000 ALTER TABLE `autores` DISABLE KEYS */;
INSERT INTO `autores` VALUES (1,'J. R. R. Tolkien','2023-04-16 22:21:41'),(2,'Arthur Conan Doyle','2023-05-15 05:02:25'),(3,'Agatha Christie','2023-05-15 05:02:39'),(4,'Stephen King','2023-05-15 05:02:47'),(5,'Machado de Assis','2023-05-15 05:02:57'),(6,'Ian Sommerville','2023-05-15 05:04:28'),(7,'Paul Deitel','2023-05-15 05:41:28'),(8,'Harvey Deitel','2023-05-15 05:41:44');
/*!40000 ALTER TABLE `autores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `chamados`
--

LOCK TABLES `chamados` WRITE;
/*!40000 ALTER TABLE `chamados` DISABLE KEYS */;
/*!40000 ALTER TABLE `chamados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `chamados_detalhe`
--

LOCK TABLES `chamados_detalhe` WRITE;
/*!40000 ALTER TABLE `chamados_detalhe` DISABLE KEYS */;
/*!40000 ALTER TABLE `chamados_detalhe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `chat`
--

LOCK TABLES `chat` WRITE;
/*!40000 ALTER TABLE `chat` DISABLE KEYS */;
INSERT INTO `chat` VALUES (1,1,3,'Olá!','2023-05-11 02:28:07'),(4,3,1,'Olá!','2023-05-15 06:10:36'),(5,3,1,'Hello','2023-05-15 06:11:03'),(8,4,1,'Bom dia!','2023-05-15 06:29:11');
/*!40000 ALTER TABLE `chat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `emprestimos`
--

LOCK TABLES `emprestimos` WRITE;
/*!40000 ALTER TABLE `emprestimos` DISABLE KEYS */;
INSERT INTO `emprestimos` VALUES (1,1,1,2,15,'2023-05-15',NULL,'2023-05-30',NULL,'SOLI','2023-05-11 09:10:47','2023-05-11 09:11:35'),(2,1,3,2,15,'2023-05-15',NULL,'2023-05-30',NULL,'CANC','2023-05-11 09:12:09','2023-05-11 09:19:58'),(3,1,3,1,7,NULL,NULL,NULL,NULL,'SOLI','2023-05-13 04:42:01',NULL),(4,3,11,5,30,'2023-05-16','2023-05-15 06:54:43','2023-06-15','2023-05-15 06:54:50','DEVO','2023-05-15 06:06:14','2023-05-15 06:54:50'),(5,2,9,4,7,NULL,NULL,NULL,NULL,'CANC','2023-05-15 06:07:01','2023-05-15 06:45:09'),(6,4,10,3,45,NULL,NULL,NULL,NULL,'SOLI','2023-05-15 06:07:53',NULL),(7,2,9,4,45,'2023-05-17','2023-05-15 06:46:07','2023-07-01',NULL,'EMPR','2023-05-15 06:45:24','2023-05-15 06:46:07'),(8,3,11,2,45,'2023-05-19','2023-05-15 06:55:15','2023-07-04',NULL,'EMPR','2023-05-15 06:54:12','2023-05-15 06:55:15');
/*!40000 ALTER TABLE `emprestimos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `enderecos`
--

LOCK TABLES `enderecos` WRITE;
/*!40000 ALTER TABLE `enderecos` DISABLE KEYS */;
INSERT INTO `enderecos` VALUES (2,5,'08673-040','Rua José Garcia de Souza','1123456789','','Parque Suzano','Suzano','SP','2023-05-15 06:05:24');
/*!40000 ALTER TABLE `enderecos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `favoritos`
--

LOCK TABLES `favoritos` WRITE;
/*!40000 ALTER TABLE `favoritos` DISABLE KEYS */;
INSERT INTO `favoritos` VALUES (1,1,1),(1,3,1),(2,10,4),(2,11,3);
/*!40000 ALTER TABLE `favoritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `livros`
--

LOCK TABLES `livros` WRITE;
/*!40000 ALTER TABLE `livros` DISABLE KEYS */;
INSERT INTO `livros` VALUES (1,'O Senhor dos Anéis - A Sociedade do Anel','Frodo Bolseiro é um hobbit do Condado, que recebe de seu tio Bilbo um anel de rara beleza. Esse anel tem uma longa história: foi roubado de uma criatura chamada Gollum (como relatado no livro O Hobbit), e desde então ele tem sido guardado por Bilbo.',4.75,'/imagens/livros/lotr-1.jpg','1','A','2023-04-16 22:20:48'),(2,'O Senhor dos Anéis - As Duas Torres','Aragorn, Legolas e Gimli seguem os rastros dos hobbits capturados (Merry e Pippin) e o caminho os leva até a Floresta de Fangorn. Nela encontram o Mago Branco que inicialmente pensam ser Saruman, o traidor. No entanto, o velho enigmático revela-se Gandalf, que morreu enfrentando o Balrog e retornou da morte para cumprir sua missão na Terra-Média.',3.5,'/imagens/livros/lotr-2.jpg','2','A','2023-04-16 22:19:44'),(3,'O Senhor dos Anéis - O Retorno do Rei','Gandalf e Pippin entram na cidade de Minas Tirith, onde se encontram com Denethor, regente do reino de Gondor. Gandalf o avisa da guerra próxima, e o regente pede a ajuda de Rohan, mas revela seu rancor por Aragorn, que, sendo descendente direto do último rei, é o herdeiro legítimo do trono de Gondor. Merry, entretanto, permanece com os rohirrim, para servir ao rei Théoden, que reúne todos os guerreiros aptos de seu reino e parte para a guerra em Minas Tirith. Junto com ele vão Aragorn, Legolas e Gimli.',5,'/imagens/livros/lotr-3.jpg','3','A','2023-04-16 22:21:19'),(4,'Engenharia de Software','A 10ª edição de Engenharia de software foi extensivamente atualizada para refletir a adoção crescente de métodos ágeis na engenharia de software. Um dos destaques da nova edição é o acréscimo de conteúdo sobre a metodologia do Scrum. A divisão em quatro partes do livro foi significativamente reformulada para acomodar novos capítulos sobre engenharia de resiliência, engenharia de sistemas e sistemas de sistemas.',3,'/imagens/livros/uid-4-capa.jpg','8543024978','A','2023-05-15 05:06:39'),(5,'Dança da Morte','Após um erro de computação no Departamento de Defesa, um vírus é liberado, dando origem à doença que ficará conhecida como Capitão Viajante, ou supergripe',4.5,'/imagens/livros/uid-5-capa.jpg','8581050549','A','2023-05-15 05:24:08'),(6,'Quincas Borba','Ao lado de Memórias póstumas de Brás Cubas e Dom Casmurro, este livro faz parte da trilogia realista de Machado de Assis, na qual o autor se utiliza da ironia e do pessimismo para tecer críticas à sociedade.',2,'/imagens/livros/uid-6-capa.jpg','856709772X','A','2023-05-15 05:25:31'),(7,'Morte no Nilo','A tranquilidade de um cruzeiro de luxo pelo Nilo chega ao fim quando o corpo de Linnet Doyle, uma bela e jovem milionária, é descoberto em sua cabine.',3.5,'/imagens/livros/uid-7-capa.jpg','6555110023','A','2023-05-15 05:27:04'),(8,'Assassinato no Expresso do Oriente','Neste clássico da literatura, e um dos mistérios mais famosos da Rainha do Crime, Hercule Poirot precisa descobrir quem está por trás do assassinato no Expresso do Oriente â€“ e o culpado está entre os passageiros do trem.',2,'/imagens/livros/uid-8-capa.jpg','8595086788','A','2023-05-15 05:27:39'),(9,'O mundo perdido','O jovem jornalista Ed Malone não tem ideia do que encontrará nesta viagem exploratória a qual acaba de se candidatar: ir até os confins da América do Sul, junto com um estudioso e um caçador para comprovar a veracidade da teoria do excêntrico professor Challenger.',1.75,'/imagens/livros/uid-9-capa.jpg','8594318723','A','2023-05-15 05:29:34'),(10,'As aventuras de Sherlock Holmes','Sherlock Holmes é um detetive britÃ¢nico enigmático e pedante do final do século XIX e início do século XX. Ele utiliza a metodologia científica e a lógica dedutiva para solucionar seus casos e conta com a ajuda de seu fiel amigo e parceiro dr. Watson.',3.25,'/imagens/livros/uid-10-capa.jpg','8594318553','A','2023-05-15 05:30:19'),(11,'JavaÂ®: Como Programar','Milhões de alunos e profissionais aprenderam programação e desenvolvimento de software com os livros DeitelÂ®. Java: como programar, 10ª edição, fornece uma introdução clara, simples, envolvente e divertida à programação Java com ênfase inicial em objetos.',4,'/imagens/livros/uid-11-capa.jpg','8543004799','A','2023-05-15 05:42:48');
/*!40000 ALTER TABLE `livros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `livros_assuntos`
--

LOCK TABLES `livros_assuntos` WRITE;
/*!40000 ALTER TABLE `livros_assuntos` DISABLE KEYS */;
INSERT INTO `livros_assuntos` VALUES (1,1),(2,1),(3,1),(5,2),(1,3),(2,3),(3,3),(11,9),(11,10),(4,20),(7,29),(8,29),(9,29),(10,29),(6,33),(7,34),(8,34),(9,34),(10,34);
/*!40000 ALTER TABLE `livros_assuntos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `livros_autores`
--

LOCK TABLES `livros_autores` WRITE;
/*!40000 ALTER TABLE `livros_autores` DISABLE KEYS */;
INSERT INTO `livros_autores` VALUES (1,1),(2,1),(3,1),(9,2),(10,2),(7,3),(8,3),(5,4),(6,5),(4,6),(11,7),(11,8);
/*!40000 ALTER TABLE `livros_autores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `livros_avaliacoes`
--

LOCK TABLES `livros_avaliacoes` WRITE;
/*!40000 ALTER TABLE `livros_avaliacoes` DISABLE KEYS */;
INSERT INTO `livros_avaliacoes` VALUES (1,4,4.75),(2,4,3.5),(3,4,5),(4,4,3),(5,2,4),(5,4,5),(6,4,2),(7,2,4),(7,4,3),(8,2,3),(8,4,1),(9,2,2),(9,4,1.5),(10,2,4),(10,4,2.5),(11,2,4.5),(11,4,3.5);
/*!40000 ALTER TABLE `livros_avaliacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'teste@teste.com.br','Dino da Silva Sauro','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','12345678900','2000-01-01','M','2023-04-16 21:29:37','2023-04-16 21:29:37','/imagens/usuarios/dino.jpg','dinossauro','A','admin',NULL,0),(2,'teste2@teste.com.br','Fran da Silva Sauro','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','12345678901','2000-01-01','F','2023-04-16 22:25:07','2023-04-16 22:25:07','/imagens/usuarios/fran.jpg','franssauro','A','admin',NULL,0),(3,'teste3@teste.com.br','Bob da Silva Sauro','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','12345678902','2000-01-01','M','2023-04-16 22:25:31','2023-04-16 22:25:31','/imagens/usuarios/bob.jpg','bobssauro','A','admin',NULL,0),(4,'teste4@teste.com.br','Charlene da Silva Sauro','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','12345678903','2000-01-01','F','2023-04-16 22:26:17','2023-04-16 22:26:17','/imagens/usuarios/charlene.jpg','charlenessauro','A','admin',NULL,0),(5,'teste5@teste.com.br','Roy Hess','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','12345678904','2000-01-01','masculino','2023-05-15 04:22:27','2023-05-15 04:22:27','/imagens/usuarios/uid-5-avatar.jpg','Rex','A','admin',NULL,0);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `usuarios_assuntos`
--

LOCK TABLES `usuarios_assuntos` WRITE;
/*!40000 ALTER TABLE `usuarios_assuntos` DISABLE KEYS */;
INSERT INTO `usuarios_assuntos` VALUES (5,9),(1,10),(5,10),(5,15),(5,17),(5,18),(5,19),(2,27),(2,28),(1,29),(1,30),(5,31),(1,33),(1,34);
/*!40000 ALTER TABLE `usuarios_assuntos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `usuarios_bloqueio`
--

LOCK TABLES `usuarios_bloqueio` WRITE;
/*!40000 ALTER TABLE `usuarios_bloqueio` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuarios_bloqueio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `usuarios_livros`
--

LOCK TABLES `usuarios_livros` WRITE;
/*!40000 ALTER TABLE `usuarios_livros` DISABLE KEYS */;
INSERT INTO `usuarios_livros` VALUES (1,1,'D','2023-04-16 22:26:47','2023-05-11 02:27:01'),(1,2,'N','2023-04-16 22:26:51',NULL),(1,3,'D','2023-04-16 22:26:55','2023-05-11 02:32:27'),(1,8,'D','2023-05-15 06:02:12','2023-05-15 06:02:12'),(2,2,'D','2023-05-11 02:35:15','2023-05-11 02:35:30'),(2,3,'D','2023-05-11 02:34:44','2023-05-11 02:35:25'),(2,9,'D','2023-05-15 06:02:12','2023-05-15 06:02:12'),(3,4,'D','2023-05-15 06:02:12','2023-05-15 06:02:12'),(3,5,'D','2023-05-15 06:02:12','2023-05-15 06:02:12'),(3,11,'D','2023-05-15 06:02:12','2023-05-15 06:02:12'),(4,4,'D','2023-05-15 05:11:49','2023-05-15 05:11:49'),(4,6,'D','2023-05-15 06:02:12','2023-05-15 06:02:12'),(4,10,'D','2023-05-15 06:02:12','2023-05-15 06:02:12'),(5,2,'D','2023-05-15 06:02:12','2023-05-15 06:02:12'),(5,7,'D','2023-05-15 06:02:12','2023-05-15 06:02:12');
/*!40000 ALTER TABLE `usuarios_livros` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-05-15  4:26:45