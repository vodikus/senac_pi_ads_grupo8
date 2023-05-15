CREATE DATABASE  IF NOT EXISTS `clube_livros` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `clube_livros`;

CREATE USER 'clube_livros'@'localhost' IDENTIFIED BY 'senha';
GRANT SELECT, INSERT, DELETE, UPDATE ON clube_livros.* TO 'clube_livros'@'localhost';
FLUSH PRIVILEGES;

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
-- Table structure for table `amigos`
--

DROP TABLE IF EXISTS `amigos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amigos` (
  `uid` bigint(20) NOT NULL,
  `uid_amigo` bigint(20) NOT NULL,
  `dh_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`,`uid_amigo`),
  KEY `fk_amigos_usuario_02_idx` (`uid_amigo`),
  CONSTRAINT `fk_amigos_usuario_01` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_amigos_usuario_02` FOREIGN KEY (`uid_amigo`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assuntos`
--

DROP TABLE IF EXISTS `assuntos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assuntos` (
  `iid` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome_assunto` varchar(255) DEFAULT NULL,
  `dh_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iid`),
  UNIQUE KEY `nome_assunto_uk` (`nome_assunto`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autores`
--

DROP TABLE IF EXISTS `autores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autores` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `nome_autor` varchar(255) NOT NULL,
  `dh_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`aid`),
  UNIQUE KEY `nome_autor_uk` (`nome_autor`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chamados`
--

DROP TABLE IF EXISTS `chamados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chamados` (
  `cid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid_origem` bigint(20) NOT NULL,
  `uid_destino` bigint(20) DEFAULT NULL,
  `lid` bigint(20) DEFAULT NULL,
  `tipo` enum('RECLAMACAO','SUGESTAO','BUG','DENUNCIA','SUPORTE') DEFAULT NULL,
  `assunto` varchar(255) DEFAULT NULL,
  `motivo` varchar(50) DEFAULT NULL,
  `texto` longtext,
  `dh_inclusao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_atualizacao` timestamp NULL DEFAULT NULL,
  `status` enum('ABERTO','FECHADO','CANCELADO','PENDENTE') NOT NULL DEFAULT 'ABERTO',
  PRIMARY KEY (`cid`),
  KEY `fk_chamado_usuario_01_idx` (`uid_origem`),
  KEY `fk_chamado_livros_01_idx` (`lid`),
  KEY `fk_chamado_usuario_02_idx` (`uid_destino`),
  CONSTRAINT `fk_chamado_livros_01` FOREIGN KEY (`lid`) REFERENCES `livros` (`lid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chamado_usuario_01` FOREIGN KEY (`uid_origem`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chamado_usuario_02` FOREIGN KEY (`uid_destino`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chamados_detalhe`
--

DROP TABLE IF EXISTS `chamados_detalhe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chamados_detalhe` (
  `chid` bigint(20) NOT NULL AUTO_INCREMENT,
  `cid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `mensagem` varchar(255) DEFAULT NULL,
  `tipo` enum('AVISO','MENSAGEM') NOT NULL DEFAULT 'MENSAGEM',
  `dh_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chid`),
  KEY `fk_chamados_id_idx` (`cid`),
  KEY `fk_chamados_hist_uid_idx` (`uid`),
  CONSTRAINT `fk_chamados_hist_uid` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chamados_id` FOREIGN KEY (`cid`) REFERENCES `chamados` (`cid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat`
--

DROP TABLE IF EXISTS `chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat` (
  `mid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `uid_amigo` bigint(20) NOT NULL,
  `mensagem` longtext NOT NULL,
  `dh_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mid`),
  KEY `fk_chat_usu_origem_idx` (`uid`),
  KEY `fk_chat_usu_destino_idx` (`uid_amigo`),
  CONSTRAINT `fk_chat_usu_destino` FOREIGN KEY (`uid_amigo`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chat_usu_origem` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emprestimos`
--

DROP TABLE IF EXISTS `emprestimos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emprestimos` (
  `eid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid_dono` bigint(20) NOT NULL COMMENT 'Usuário dono do livro',
  `lid` bigint(20) NOT NULL,
  `uid_tomador` bigint(20) NOT NULL COMMENT 'Usuário tomador do empréstimo',
  `qtd_dias` int(11) NOT NULL DEFAULT '0',
  `retirada_prevista` date DEFAULT NULL,
  `retirada_efetiva` timestamp NULL DEFAULT NULL,
  `devolucao_prevista` date DEFAULT NULL,
  `devolucao_efetiva` timestamp NULL DEFAULT NULL,
  `status` enum('SOLI','DEVO','CANC','EMPR','EXTR') NOT NULL DEFAULT 'SOLI' COMMENT 'SOLI - SOLICITADO\nDEVO - DEVOLVIDO\nCANC - CANCELADO\nEMPR - EMPRESTADO\nEXTR - EXTRAVIADO',
  `dh_solicitacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_atualizacao` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`eid`),
  KEY `fk_emp_usu_liv_01_idx` (`uid_dono`,`lid`),
  KEY `fk_emp_usu_tomador_01_idx` (`uid_tomador`),
  CONSTRAINT `fk_emp_usu_liv_01` FOREIGN KEY (`uid_dono`, `lid`) REFERENCES `usuarios_livros` (`uid`, `lid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_emp_usu_tomador_01` FOREIGN KEY (`uid_tomador`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `enderecos`
--

DROP TABLE IF EXISTS `enderecos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enderecos` (
  `enid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(255) DEFAULT NULL,
  `numero` varchar(45) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(45) DEFAULT NULL,
  `cidade` varchar(45) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `dh_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`enid`),
  UNIQUE KEY `usu_ende_cep_uk` (`uid`,`cep`),
  KEY `usu_ende_idx` (`uid`),
  CONSTRAINT `usu_ende` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favoritos` (
  `uid_usuario` bigint(20) NOT NULL,
  `lid` bigint(20) NOT NULL,
  `uid_dono` bigint(20) NOT NULL,
  PRIMARY KEY (`uid_usuario`,`lid`,`uid_dono`),
  KEY `fk_fav_usu_liv_01_idx` (`lid`,`uid_dono`),
  CONSTRAINT `fk_fav_usu_liv_01` FOREIGN KEY (`lid`, `uid_dono`) REFERENCES `usuarios_livros` (`lid`, `uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fav_usuario_01` FOREIGN KEY (`uid_usuario`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `livros`
--

DROP TABLE IF EXISTS `livros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livros` (
  `lid` bigint(20) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descricao` longtext,
  `avaliacao` double DEFAULT '0',
  `capa` varchar(255) DEFAULT NULL,
  `isbn` varchar(45) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `dh_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lid`),
  UNIQUE KEY `livro_isbn_uk` (`isbn`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `livros_assuntos`
--

DROP TABLE IF EXISTS `livros_assuntos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livros_assuntos` (
  `lid` bigint(20) NOT NULL,
  `iid` bigint(20) NOT NULL,
  PRIMARY KEY (`lid`,`iid`),
  KEY `fk_interesses_livros_interesses_idx` (`iid`),
  CONSTRAINT `fk_la_assuntos` FOREIGN KEY (`iid`) REFERENCES `assuntos` (`iid`),
  CONSTRAINT `fk_la_livros` FOREIGN KEY (`lid`) REFERENCES `livros` (`lid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `livros_autores`
--

DROP TABLE IF EXISTS `livros_autores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livros_autores` (
  `lid` bigint(20) NOT NULL,
  `aid` bigint(20) NOT NULL,
  PRIMARY KEY (`lid`,`aid`),
  KEY `fk_autores_idx` (`aid`),
  CONSTRAINT `fk_autores` FOREIGN KEY (`aid`) REFERENCES `autores` (`aid`),
  CONSTRAINT `fk_livros` FOREIGN KEY (`lid`) REFERENCES `livros` (`lid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `livros_avaliacoes`
--

DROP TABLE IF EXISTS `livros_avaliacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livros_avaliacoes` (
  `lid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `nota` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`lid`,`uid`),
  KEY `fk_livroav_usuarios_idx` (`uid`),
  CONSTRAINT `fk_livroav_livros` FOREIGN KEY (`lid`) REFERENCES `livros` (`lid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_livroav_usuarios` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `nascimento` date NOT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `dh_atualizacao` timestamp NULL DEFAULT NULL,
  `dh_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `avatar` varchar(255) DEFAULT NULL,
  `apelido` varchar(255) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `role` varchar(45) NOT NULL DEFAULT 'admin',
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `status_chat` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `email_uk` (`email`),
  UNIQUE KEY `cpf_uk` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_assuntos`
--

DROP TABLE IF EXISTS `usuarios_assuntos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_assuntos` (
  `uid` bigint(20) NOT NULL,
  `iid` bigint(20) NOT NULL,
  PRIMARY KEY (`uid`,`iid`),
  KEY `fk_interesses_01_idx` (`iid`),
  CONSTRAINT `fk_ua_assuntos_01` FOREIGN KEY (`iid`) REFERENCES `assuntos` (`iid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ua_usuarios_01` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_bloqueio`
--

DROP TABLE IF EXISTS `usuarios_bloqueio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_bloqueio` (
  `uid` bigint(20) NOT NULL,
  `uid_blq` bigint(20) NOT NULL,
  PRIMARY KEY (`uid`,`uid_blq`),
  KEY `fk_usublq_usu_destino_idx` (`uid_blq`),
  CONSTRAINT `fk_usublq_usu_destino` FOREIGN KEY (`uid_blq`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_usublq_usu_origem` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_livros`
--

DROP TABLE IF EXISTS `usuarios_livros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_livros` (
  `uid` bigint(20) NOT NULL,
  `lid` bigint(20) NOT NULL,
  `status` enum('D','N','E') NOT NULL DEFAULT 'N' COMMENT 'D - Disponivel\nN - Nao Disponivel\nE - Extraviado',
  `dh_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_atualizacao` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`,`lid`),
  KEY `fk_ul_livros_01_idx` (`lid`),
  CONSTRAINT `fk_ul_livros_01` FOREIGN KEY (`lid`) REFERENCES `livros` (`lid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ul_usuarios_01` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-05-15  4:25:55
