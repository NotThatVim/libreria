-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: libreria_progetto
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
-- Table structure for table `Categoria`
--

DROP TABLE IF EXISTS `Categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Categoria`
--

LOCK TABLES `Categoria` WRITE;
/*!40000 ALTER TABLE `Categoria` DISABLE KEYS */;
INSERT INTO `Categoria` VALUES (4,'Arti Marziali/Cultivazione'),(2,'Avventura'),(1,'Azione'),(6,'Commedia'),(8,'Drama'),(3,'Fantasy'),(10,'Isekai/Regressione'),(5,'Sci-Fi'),(9,'Sovrannaturale'),(7,'Sport');
/*!40000 ALTER TABLE `Categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `ClassificaOpere`
--

DROP TABLE IF EXISTS `ClassificaOpere`;
/*!50001 DROP VIEW IF EXISTS `ClassificaOpere`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `ClassificaOpere` AS SELECT
 1 AS `id`,
  1 AS `titolo`,
  1 AS `numero_lettori`,
  1 AS `voto_medio` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Libreria`
--

DROP TABLE IF EXISTS `Libreria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Libreria` (
  `utente_id` int(11) NOT NULL,
  `opera_id` int(11) NOT NULL,
  `stato` enum('da_leggere','in_corso','completato','abbandonato','interrotto') NOT NULL DEFAULT 'da_leggere',
  `capitoli_letti` int(11) NOT NULL DEFAULT 0,
  `valutazione` tinyint(4) DEFAULT NULL,
  `data_aggiunta` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_ultimo_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`utente_id`,`opera_id`),
  KEY `opera_id` (`opera_id`),
  KEY `idx_libreria_stato` (`stato`),
  CONSTRAINT `Libreria_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `Utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Libreria_ibfk_2` FOREIGN KEY (`opera_id`) REFERENCES `Opera` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`valutazione` is null or `valutazione` between 1 and 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Libreria`
--

LOCK TABLES `Libreria` WRITE;
/*!40000 ALTER TABLE `Libreria` DISABLE KEYS */;
INSERT INTO `Libreria` VALUES (1,1,'in_corso',244,10,'2026-08-24 12:44:14','2026-08-24 12:44:32'),(1,2,'in_corso',335,10,'2026-08-24 12:43:49','2026-08-24 12:44:45'),(1,11,'interrotto',987,10,'2026-09-05 16:46:11','2026-09-05 16:46:27'),(1,12,'completato',700,7,'2026-09-18 08:38:20','2026-09-18 08:38:51'),(1,15,'completato',139,8,'2026-09-17 21:39:38','2026-09-17 21:39:53'),(1,16,'completato',519,3,'2026-09-16 18:26:59','2026-09-17 22:13:29'),(3,8,'completato',350,6,'2026-09-05 16:42:42','2026-09-05 16:45:28'),(3,11,'in_corso',1190,9,'2026-09-05 16:43:02','2026-09-05 16:45:43'),(3,18,'completato',0,8,'2026-09-05 01:16:44','2026-09-17 17:08:32'),(5,4,'in_corso',150,6,'2026-09-05 18:47:02','2026-09-05 18:47:51'),(6,13,'interrotto',6,6,'2026-09-11 14:06:53','2026-09-11 14:07:23'),(6,21,'in_corso',108,9,'2026-09-11 14:07:42','2026-09-11 14:10:48'),(7,18,'completato',400,3,'2026-09-17 22:24:26','2026-09-17 22:24:51'),(9,15,'da_leggere',0,NULL,'2026-09-21 11:41:38','2026-09-21 11:41:38');
/*!40000 ALTER TABLE `Libreria` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_libreria_capitoli_insert
BEFORE INSERT ON Libreria
FOR EACH ROW
BEGIN
    DECLARE tot INT;
    SELECT capitoli_totali INTO tot FROM Opera WHERE id = NEW.opera_id;
    IF NEW.capitoli_letti > tot THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'capitoli_letti supera capitoli_totali';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_libreria_capitoli_update
BEFORE UPDATE ON Libreria
FOR EACH ROW
BEGIN
    DECLARE tot INT;
    SELECT capitoli_totali INTO tot FROM Opera WHERE id = NEW.opera_id;
    IF NEW.capitoli_letti > tot THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'capitoli_letti supera capitoli_totali';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Opera`
--

DROP TABLE IF EXISTS `Opera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Opera` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titolo` varchar(200) NOT NULL,
  `tipo` enum('manga','manhwa','novel') NOT NULL,
  `autore_id` int(11) NOT NULL,
  `disegnatore_id` int(11) DEFAULT NULL,
  `protagonista_nome` varchar(100) DEFAULT NULL,
  `protagonista_nazionalita` varchar(50) DEFAULT NULL,
  `capitoli_totali` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `disegnatore_id` (`disegnatore_id`),
  KEY `idx_opera_tipo` (`tipo`),
  KEY `idx_opera_autore` (`autore_id`),
  CONSTRAINT `Opera_ibfk_1` FOREIGN KEY (`autore_id`) REFERENCES `Persona` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `Opera_ibfk_2` FOREIGN KEY (`disegnatore_id`) REFERENCES `Persona` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Opera`
--

LOCK TABLES `Opera` WRITE;
/*!40000 ALTER TABLE `Opera` DISABLE KEYS */;
INSERT INTO `Opera` VALUES (1,'Mirror World: Destined Return','novel',1,NULL,'Cheon Seong-Hwi','Corea del Sud',244),(2,'Martial God\'s Youngest Disciple','novel',2,NULL,'Luan Badniker','Impero (Fantasy)',335),(3,'Where Immortals Once Walked','novel',3,NULL,'He Lingchuan','Antica Cina',760),(4,'The Overlord of Puluo','novel',4,NULL,'Li Banfeng','Puluo',391),(5,'Ultragene Warlord','novel',5,NULL,'Zhang Lie','Cina',1600),(6,'I Shall Seal The Heavens','novel',6,NULL,'Meng Hao','Paradiso del Sud(quarto pianeta)',1620),(7,'Nano Machine','manhwa',7,14,'Cheon Yeo Woon','Dominio del Demone Celeste',350),(8,'Eternally Regressing Knight','manhwa',9,8,'Encrid','Regno di Versilia',350),(9,'Regressor of the Fallen Family','manhwa',10,11,'Logan McLain','Regno di Grandia',212),(10,'Prismatic Genius Mage','manhwa',12,13,'Ray','settore 50',156),(11,'One Piece','manga',15,15,'Monkey D. Luffy','Regno di Goa',1191),(12,'Naruto','manga',16,16,'Naruto Uzumaki','Villaggio della Foglia',700),(13,'One Punch Man','manga',17,18,'Saitama','Giappone',234),(14,'Saiki Kusuo no Psi Nan','manga',19,19,'Saiki Kusuo','Giappone',290),(15,'Shingeki No Kyojin','manga',20,20,'Eren Jaeger','Paradis',139),(16,'Dragon Ball','manga',21,21,'Goku','Saiyan',519),(17,'Inazuma Eleven','manga',22,22,'Mark Evans','Giappone',127),(18,'Capitan Tsubasa','manga',23,23,'Tsubasa Ozora','Giappone',400),(21,'Fullmetal Alchemist','manga',24,24,'Edward Elric','Amestris',108),(22,'The Ancient Magus\' Bride','manga',25,25,'Chise Hatori','Giappone',96);
/*!40000 ALTER TABLE `Opera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Opera_Categoria`
--

DROP TABLE IF EXISTS `Opera_Categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Opera_Categoria` (
  `opera_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  PRIMARY KEY (`opera_id`,`categoria_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `Opera_Categoria_ibfk_1` FOREIGN KEY (`opera_id`) REFERENCES `Opera` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Opera_Categoria_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `Categoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Opera_Categoria`
--

LOCK TABLES `Opera_Categoria` WRITE;
/*!40000 ALTER TABLE `Opera_Categoria` DISABLE KEYS */;
INSERT INTO `Opera_Categoria` VALUES (1,1),(1,10),(2,4),(2,10),(3,3),(3,4),(4,3),(4,4),(5,4),(5,5),(6,3),(6,4),(7,4),(7,5),(8,4),(8,10),(9,3),(9,10),(10,3),(10,10),(11,1),(11,2),(12,1),(12,2),(13,1),(13,6),(14,6),(14,9),(15,1),(15,8),(16,1),(16,2),(17,1),(17,7),(18,7),(21,1),(21,3),(22,3),(22,9);
/*!40000 ALTER TABLE `Opera_Categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Persona`
--

DROP TABLE IF EXISTS `Persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Persona` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `nazionalita` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Persona`
--

LOCK TABLES `Persona` WRITE;
/*!40000 ALTER TABLE `Persona` DISABLE KEYS */;
INSERT INTO `Persona` VALUES (1,'Falsum',NULL),(2,'Nakhasan',NULL),(3,'Wind and Water Between Clouds',NULL),(4,'Salagus',NULL),(5,'Mo Lai Feng',NULL),(6,'Er Gen',NULL),(7,'Han Joong Wuel Ya',NULL),(8,'Ganara',NULL),(9,'Soul-Pung',NULL),(10,'PainKiller',NULL),(11,'YESWORKS',NULL),(12,'Hasu',NULL),(13,'MSTORYHUB',NULL),(14,'REDICE Studio',NULL),(15,'Eiichiro Oda','Giapponese'),(16,'Masashi Kishimoto','Giapponese'),(17,'One','Giapponese'),(18,'Yusuke Murata','Giapponese'),(19,'Shuichi Aso','Giapponese'),(20,'Hajime Isayama','Giapponese'),(21,'Akira Toriyama','Giapponese'),(22,'Ten\'ya Yabuno','Giapponese'),(23,'Yoichi Takahashi','Giapponese'),(24,'Hiromu Arakawa','Giapponese'),(25,'Kore Yamazaki','Giapponese'),(26,'UdayBajwa','Inglese'),(27,'bidneofc','USA');
/*!40000 ALTER TABLE `Persona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Utente`
--

DROP TABLE IF EXISTS `Utente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Utente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `ruolo` enum('admin','utente') NOT NULL DEFAULT 'utente',
  `data_registrazione` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Utente`
--

LOCK TABLES `Utente` WRITE;
/*!40000 ALTER TABLE `Utente` DISABLE KEYS */;
INSERT INTO `Utente` VALUES (1,'admin','vimalbajwa05@gmail.com','$2y$10$1pqDxnDzDKdhrlrxJpVJu.xvJWwe/4pwy1za4T9sp/rh0YJhsfZNq','admin','2026-08-24 12:24:05'),(2,'DanielCiuc','palamarciuc.daniel@studenti.unibo.it','$2y$10$Y7N.DvsBUi3f7YnHiOdK5OfSsCtvUBlnDo7QgFm68bpzT0GqlRpMq','utente','2026-09-05 01:10:08'),(3,'dan','dan@gmail.com','$2y$10$vRL3MV4dIkFTVPChkSftTO7QmsXnKf3/u/gNVaZ0nsM3eZLMKCwZm','utente','2026-09-05 01:10:51'),(4,'cip','cip@random.it','$2y$10$TgrI54gSIBd51neZ2QrogOLhJCNFY3BaqV98CW8ovCjqq4QN2K9bW','utente','2026-09-05 17:49:09'),(5,'uday','uday@bigL.it','$2y$10$AS.2IVy0kOojTlY4Ru844O5IgriZZOeju1zqxIVyge6jesJuvdNMC','utente','2026-09-05 18:46:02'),(6,'veraBaddie','vera.baddie@libero.it','$2y$10$bNr6cb6Q80pCDRte60RYGuB6ycsX2A9.mwZqZQX5873zxqNzJ2zxG','utente','2026-09-11 14:04:17'),(7,'user','user@unipr.it','$2y$10$7T7b783J14aHvELOM7Q3oOcJhCiy7qiLSq7v80Jlt.mWsWdteVxDa','utente','2026-09-17 22:16:10'),(8,'pres','presentazione@uni.3','$2y$10$pfwb1v2DYjDRGpuYhI44mO3bVwncaQ5SdH8Ob.Q55Fs6/z7L532l2','utente','2026-09-18 08:31:31'),(9,'ale','ale@ale','$2y$10$HR5BnfJ2KV2uvKAKVb0w3eCMomXG4c2xuP3bfXGk/hhogoEwMiCZ.','utente','2026-09-21 11:40:09');
/*!40000 ALTER TABLE `Utente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'libreria_progetto'
--
