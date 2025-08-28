-- MySQL dump 10.13  Distrib 8.4.4, for Linux (x86_64)
--
-- Host: localhost    Database: checklist_db
-- ------------------------------------------------------
-- Server version	8.4.4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `checklist_item_names`
--

DROP TABLE IF EXISTS `checklist_item_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checklist_item_names` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL,
  `checklist_type` varchar(255) NOT NULL,
  `filial` varchar(255) NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checklist_item_names`
--

LOCK TABLES `checklist_item_names` WRITE;
/*!40000 ALTER TABLE `checklist_item_names` DISABLE KEYS */;
INSERT INTO `checklist_item_names` VALUES (1,'Servidores Físicos','infra',''),(2,'Sistemas de Armazenamento (SAN, NAS, Storages)','infra',''),(3,'Virtualização','infra',''),(4,'Switches (core, de acesso)','infra',''),(5,'Servidor Backup','infra',''),(6,'Unidades de Fita Tape Library','infra',''),(7,'Firewall','infra',''),(8,'Sistemas de Monitoramento','infra',''),(9,'Temperatura do datacenter','infra',''),(10,'Umidade Relativa do Datacenter','infra',''),(11,'Nobreak','infra',''),(12,'Gerador de Energia','infra',''),(13,'Sistemas de Ar-Condicionado','infra',''),(14,'Sistemas de Controle de Acesso','infra',''),(15,'Câmeras de Segurança','infra',''),(16,'Rede sem Fio WI-FI','infra',''),(17,'Impressoras','infra',''),(18,'Backup de Servidores Físico','backup',''),(19,'Backup de Máquinas Virtuais','backup',''),(20,'Backup Fita','backup',''),(21,'Backup Cloud','backup',''),(29,'Backup de Banco de Dados','backup',''),(46,'wdwd','infra','PLP_ARGENTINA'),(47,'dwdw','infra','PLP_ARGENTINA'),(48,'Sala Clevaland','helpdesk','PLP_BRASIL'),(49,'Sala Rogers','helpdesk','PLP_BRASIL'),(50,'Verificar Cardapio do Dia','checklist','GLOBAL'),(53,'Teste','monitoramento','MAXXWELD'),(54,'Teste','infra','PLP_COLOMBIA'),(55,'Teste_2','infra','PLP_COLOMBIA'),(56,'Teste de eeee','infra','PLP_COLOMBIA'),(57,'wdwdw','infra','PLP_COLOMBIA'),(60,'Sala Melhoria','helpdesk','PLP_BRASIL'),(61,'Sala Buenos Aires','helpdesk','PLP_BRASIL'),(62,'Sala Projetos','helpdesk','PLP_BRASIL'),(64,'Teste do servidor remoto Totvs (srv-rds02)','infra','MAXXWELD'),(65,'Ar condicionado','infra','MAXXWELD'),(66,'Temperatura Datacenter','infra','MAXXWELD'),(67,'Nobreak','infra','MAXXWELD'),(68,'SRV-DC01-MAXX','backup','MAXXWELD'),(69,'SRV-UNIFI-MAXX','backup','MAXXWELD'),(70,'SRV-FS01-MAXX','backup','MAXXWELD'),(71,'srv-HyperV-maxx','infra','MAXXWELD'),(72,'srv-bkp01-maxx','infra','MAXXWELD'),(73,'Tape Backup','infra','MAXXWELD'),(74,'Datacenter','infra','MAXXWELD'),(75,'Link internet','infra','MAXXWELD'),(76,'Firewall Sophos','infra','MAXXWELD'),(77,'Wi-Fi','infra','MAXXWELD'),(79,'Sala Medellin','helpdesk','PLP_BRASIL'),(80,'SERVIDOR DE BACKUP FÍSICO','infra','PLP_BRASIL'),(81,'TAPE-LTO ','infra','PLP_BRASIL'),(82,'SERVIDOR FÍSICO ESXI 01','infra','PLP_BRASIL'),(83,'SERVIDOR FÍSICO ESXI 02','infra','PLP_BRASIL'),(84,'SERVIDOR FÍSICO ESXI 03','infra','PLP_BRASIL'),(85,'STORAGE','infra','PLP_BRASIL'),(86,'FIREWALL PLP BRASIL','infra','PLP_BRASIL'),(87,'FIREWALL MAXXWELD','infra','PLP_BRASIL'),(88,'FIREWALL COLÔMBIA','infra','PLP_BRASIL'),(89,'AR CONDICIONADO DO DATACENTER (19º à 24º)','infra','PLP_BRASIL'),(90,'TEMPERATURA DO AR CONDICIONADO DATACENTER','infra','PLP_BRASIL'),(91,'EQUIPAMENTOS WI-FI','infra','PLP_BRASIL'),(92,'NOBREAK','infra','PLP_BRASIL'),(93,'ORGANIZAÇÃO SALA DO DATACENTER','infra','PLP_BRASIL'),(94,'LINK PRIMARIO ALGAR','infra','PLP_BRASIL'),(95,'LINK SEGUNDARIO UNITELCO','infra','PLP_BRASIL'),(96,'SERVIDORES VIRTUAIS','infra','PLP_BRASIL'),(97,'SRV-BOLT','backup','PLP_BRASIL'),(98,'SRV-CKECKLIST','backup','PLP_BRASIL'),(99,'SRV-CLEVELAND','backup','PLP_BRASIL'),(100,'SRV-DC01','backup','PLP_BRASIL'),(101,'SRV-DC02','backup','PLP_BRASIL'),(103,'SRV-DIMEP','backup','PLP_BRASIL'),(104,'SRV-ERP01','backup','PLP_BRASIL'),(105,'SRV-ERP02','backup','PLP_BRASIL'),(106,'SRV-ERP03','backup','PLP_BRASIL'),(107,'SRV-FS01','backup','PLP_BRASIL'),(108,'SRV-GLPI01','backup','PLP_BRASIL'),(109,'SRV-MEURH','backup','PLP_BRASIL'),(110,'SRV-NODERED','backup','PLP_BRASIL'),(111,'SRV-PRN02','backup','PLP_BRASIL'),(112,'SRV-PROCESSHUB','backup','PLP_BRASIL'),(113,'SRV-RDS02','backup','PLP_BRASIL'),(114,'SRV-RPA','backup','PLP_BRASIL'),(115,'SRV-SIG01','backup','PLP_BRASIL'),(116,'SRV-SIG02','backup','PLP_BRASIL'),(117,'SRV-TAF','backup','PLP_BRASIL'),(118,'SRV-VCENTER','backup','PLP_BRASIL'),(119,'SRV-ZABBIX','backup','PLP_BRASIL');
/*!40000 ALTER TABLE `checklist_item_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itens`
--

DROP TABLE IF EXISTS `itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `submission_id` int DEFAULT NULL,
  `item_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `observacao` text,
  PRIMARY KEY (`id`),
  KEY `submission_id` (`submission_id`),
  CONSTRAINT `itens_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itens`
--

LOCK TABLES `itens` WRITE;
/*!40000 ALTER TABLE `itens` DISABLE KEYS */;
INSERT INTO `itens` VALUES (13,3,'64','ok',NULL),(14,3,'65','nok','Não gela.'),(15,3,'66','ok',NULL),(16,3,'67','ok',NULL),(17,4,'68','ok',NULL),(18,4,'69','running',NULL),(19,4,'70','erro','Backup com erro'),(20,5,'102','Backup 2º Semana',NULL),(21,5,'108','0001',NULL),(22,5,'110','Tape',NULL),(23,5,'105','22/08/2025',NULL),(24,5,'109','0002',NULL),(25,5,'111','Armário',NULL),(26,5,'104','Sim',NULL),(47,10,'48','ok',NULL),(48,10,'49','ok',NULL),(49,10,'60','ok',NULL),(50,10,'61','ok',NULL),(51,10,'62','ok',NULL),(52,10,'79','ok',NULL),(68,13,'48','ok',NULL),(69,13,'49','ok',NULL),(70,13,'60','ok',NULL),(71,13,'61','ok',NULL),(72,13,'62','ok',NULL),(73,13,'79','ok',NULL),(78,15,'80','ok',NULL),(79,15,'81','ok',NULL),(80,15,'82','ok',NULL),(81,15,'83','ok',NULL),(82,15,'84','ok',NULL),(83,15,'85','ok',NULL),(84,15,'86','ok',NULL),(85,15,'87','ok',NULL),(86,15,'88','ok',NULL),(87,15,'89','ok',NULL),(88,15,'90','ok',NULL),(89,15,'91','ok',NULL),(90,15,'92','ok',NULL),(91,15,'93','ok',NULL),(92,15,'94','ok',NULL),(93,15,'95','ok',NULL),(94,15,'96','ok',NULL),(95,16,'97','ok','Tipo de Backup: Incremental Backup'),(96,16,'98','ok','Tipo de Backup: Incremental Backup'),(97,16,'99','ok','Tipo de Backup: Incremental Backup'),(98,16,'100','ok','Tipo de Backup: Incremental Backup'),(99,16,'101','ok','Tipo de Backup: Incremental Backup'),(100,16,'103','ok','Tipo de Backup: Incremental Backup'),(101,16,'104','ok','Tipo de Backup: Incremental Backup'),(102,16,'105','ok','Tipo de Backup: Incremental Backup'),(103,16,'106','ok','Tipo de Backup: Incremental Backup'),(104,16,'107','ok','Tipo de Backup: Incremental Backup'),(105,16,'108','ok','Tipo de Backup: Incremental Backup'),(106,16,'109','ok','Tipo de Backup: Incremental Backup'),(107,16,'110','ok','Tipo de Backup: Incremental Backup'),(108,16,'111','ok','Tipo de Backup: Incremental Backup'),(109,16,'112','ok','Tipo de Backup: Incremental Backup'),(110,16,'113','ok','Tipo de Backup: Incremental Backup'),(111,16,'114','ok','Tipo de Backup: Incremental Backup'),(112,16,'115','ok','Tipo de Backup: Incremental Backup'),(113,16,'116','ok','Tipo de Backup: Incremental Backup'),(114,16,'117','ok','Tipo de Backup: Incremental Backup'),(115,16,'118','ok','Tipo de Backup: Incremental Backup'),(116,16,'119','ok','Tipo de Backup: Incremental Backup'),(117,17,'97','ok','Tipo de Backup: Incremental Backup'),(118,17,'98','ok','Tipo de Backup: Incremental Backup'),(119,17,'99','ok','Tipo de Backup: Incremental Backup'),(120,17,'100','ok','Tipo de Backup: Incremental Backup'),(121,17,'101','ok','Tipo de Backup: Incremental Backup'),(122,17,'103','ok','Tipo de Backup: Incremental Backup'),(123,17,'104','ok','Tipo de Backup: Incremental Backup'),(124,17,'105','ok','Tipo de Backup: Incremental Backup'),(125,17,'106','ok','Tipo de Backup: Incremental Backup'),(126,17,'107','ok','Tipo de Backup: Incremental Backup'),(127,17,'108','ok','Tipo de Backup: Incremental Backup'),(128,17,'109','ok','Tipo de Backup: Incremental Backup'),(129,17,'110','ok','Tipo de Backup: Incremental Backup'),(130,17,'111','ok','Tipo de Backup: Incremental Backup'),(131,17,'112','ok','Tipo de Backup: Incremental Backup'),(132,17,'113','ok','Tipo de Backup: Incremental Backup'),(133,17,'114','ok','Tipo de Backup: Incremental Backup'),(134,17,'115','ok','Tipo de Backup: Incremental Backup'),(135,17,'116','ok','Tipo de Backup: Incremental Backup'),(136,17,'117','ok','Tipo de Backup: Incremental Backup'),(137,17,'118','ok','Tipo de Backup: Incremental Backup'),(138,17,'119','ok','Tipo de Backup: Incremental Backup'),(139,18,'102','Backup 4º Semana',NULL),(140,18,'108','000004',NULL),(141,18,'110','Tape',NULL),(142,18,'105','Nenhum histórico encontrado.',NULL),(143,18,'109','000003',NULL),(144,18,'111','Tape',NULL),(145,18,'104','Não',NULL),(146,19,'68','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(147,19,'69','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(148,19,'70','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(149,20,'64','ok',NULL),(150,20,'65','ok',NULL),(151,20,'66','ok',NULL),(152,20,'67','ok',NULL),(153,20,'71','ok',NULL),(154,20,'72','ok',NULL),(155,20,'73','ok',NULL),(156,20,'74','ok',NULL),(157,20,'75','ok',NULL),(158,20,'76','ok',NULL),(159,20,'77','ok',NULL),(160,21,'80','ok',NULL),(161,21,'81','ok',NULL),(162,21,'82','ok',NULL),(163,21,'83','ok',NULL),(164,21,'84','',NULL),(165,21,'85','ok',NULL),(166,21,'86','ok',NULL),(167,21,'87','ok',NULL),(168,21,'88','ok',NULL),(169,21,'89','ok',NULL),(170,21,'90','ok',NULL),(171,21,'91','ok',NULL),(172,21,'92','ok',NULL),(173,21,'93','ok',NULL),(174,21,'94','ok',NULL),(175,21,'95','ok',NULL),(176,21,'96','ok',NULL),(199,23,'97','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(200,23,'98','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(201,23,'99','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(202,23,'100','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(203,23,'101','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(204,23,'103','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(205,23,'104','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(206,23,'105','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(207,23,'106','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(208,23,'107','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(209,23,'108','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(210,23,'109','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(211,23,'110','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(212,23,'111','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(213,23,'112','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(214,23,'113','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(215,23,'114','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(216,23,'115','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(217,23,'116','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(218,23,'117','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(219,23,'118','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(220,23,'119','ok','Tipo de Backup: Incremental Backup'),(221,24,'102','Backup 4º Semana',NULL),(222,24,'108','000004',NULL),(223,24,'110','Tape',NULL),(224,24,'105','25/08/2025',NULL),(225,24,'109','000003',NULL),(226,24,'111','Tape',NULL),(227,24,'104','Não',NULL),(228,25,'68','running','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(229,25,'69','running','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(230,25,'70','running','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(231,26,'64','ok',NULL),(232,26,'65','ok','21°'),(233,26,'66','na',NULL),(234,26,'67','ok',NULL),(235,26,'71','ok',NULL),(236,26,'72','ok',NULL),(237,26,'73','ok',NULL),(238,26,'74','ok',NULL),(239,26,'75','ok',NULL),(240,26,'76','ok',NULL),(241,26,'77','ok',NULL),(242,27,'80','ok',NULL),(243,27,'81','ok',NULL),(244,27,'82','ok',NULL),(245,27,'83','ok',NULL),(246,27,'84','ok',NULL),(247,27,'85','ok',NULL),(248,27,'86','ok',NULL),(249,27,'87','ok',NULL),(250,27,'88','ok',NULL),(251,27,'89','ok',NULL),(252,27,'90','ok',NULL),(253,27,'91','ok',NULL),(254,27,'92','ok',NULL),(255,27,'93','ok',NULL),(256,27,'94','ok',NULL),(257,27,'95','ok',NULL),(258,27,'96','ok',NULL),(259,28,'97','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(260,28,'98','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(261,28,'99','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(262,28,'100','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(263,28,'101','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(264,28,'103','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(265,28,'104','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(266,28,'105','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(267,28,'106','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(268,28,'107','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(269,28,'108','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(270,28,'109','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(271,28,'110','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(272,28,'111','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(273,28,'112','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(274,28,'113','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(275,28,'114','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(276,28,'115','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(277,28,'116','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(278,28,'117','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(279,28,'118','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(280,28,'119','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Sim'),(281,29,'102','Backup 4º Semana',NULL),(282,29,'108','000004',NULL),(283,29,'110','Tape',NULL),(284,29,'105','26/08/2025',NULL),(285,29,'109','000003',NULL),(286,29,'111','Tape',NULL),(287,29,'104','Não',NULL),(288,30,'68','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(289,30,'69','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(290,30,'70','ok','Tipo de Backup: Incremental Backup\nCloud Replication: Não'),(291,31,'64','ok',NULL),(292,31,'65','ok',NULL),(293,31,'66','na',NULL),(294,31,'67','ok',NULL),(295,31,'71','ok',NULL),(296,31,'72','ok',NULL),(297,31,'73','ok',NULL),(298,31,'74','ok',NULL),(299,31,'75','ok',NULL),(300,31,'76','ok',NULL),(301,31,'77','ok',NULL),(302,32,'80','ok',NULL),(303,32,'81','ok',NULL),(304,32,'82','ok',NULL),(305,32,'83','ok',NULL),(306,32,'84','ok',NULL),(307,32,'85','ok',NULL),(308,32,'86','ok',NULL),(309,32,'87','ok',NULL),(310,32,'88','ok',NULL),(311,32,'89','ok',NULL),(312,32,'90','ok',NULL),(313,32,'91','ok',NULL),(314,32,'92','ok',NULL),(315,32,'93','ok',NULL),(316,32,'94','ok',NULL),(317,32,'95','ok',NULL),(318,32,'96','ok',NULL);
/*!40000 ALTER TABLE `itens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `submissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filial` varchar(255) NOT NULL,
  `data_preenchimento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `checklist_type` varchar(50) NOT NULL,
  `tape_number` varchar(255) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submissions`
--

LOCK TABLES `submissions` WRITE;
/*!40000 ALTER TABLE `submissions` DISABLE KEYS */;
INSERT INTO `submissions` VALUES (3,'MAXXWELD','2025-08-22 17:23:39','infra',NULL,12),(4,'MAXXWELD','2025-08-22 17:31:27','backup',NULL,12),(5,'MAXXWELD','2025-08-22 17:34:07','tape_inventory',NULL,12),(10,'PLP_BRASIL','2025-08-22 18:14:15','helpdesk',NULL,3),(13,'PLP_BRASIL','2025-08-22 18:21:51','helpdesk',NULL,3),(15,'PLP_BRASIL','2025-08-22 19:42:09','infra',NULL,8),(16,'PLP_BRASIL','2025-08-22 19:53:07','backup',NULL,8),(17,'PLP_BRASIL','2025-08-25 10:52:31','backup',NULL,8),(18,'MAXXWELD','2025-08-25 14:18:13','tape_inventory',NULL,12),(19,'MAXXWELD','2025-08-25 14:18:52','backup',NULL,12),(20,'MAXXWELD','2025-08-25 14:30:38','infra',NULL,12),(21,'PLP_BRASIL','2025-08-25 19:06:03','infra',NULL,8),(23,'PLP_BRASIL','2025-08-26 11:28:03','backup',NULL,8),(24,'MAXXWELD','2025-08-26 18:00:54','tape_inventory',NULL,12),(25,'MAXXWELD','2025-08-26 18:02:16','backup',NULL,12),(26,'MAXXWELD','2025-08-26 18:06:32','infra',NULL,12),(27,'PLP_BRASIL','2025-08-26 19:43:35','infra',NULL,8),(28,'PLP_BRASIL','2025-08-27 12:30:36','backup',NULL,8),(29,'MAXXWELD','2025-08-27 18:00:57','tape_inventory',NULL,12),(30,'MAXXWELD','2025-08-27 18:02:23','backup',NULL,12),(31,'MAXXWELD','2025-08-27 18:09:04','infra',NULL,12),(32,'PLP_BRASIL','2025-08-27 20:00:23','infra',NULL,8);
/*!40000 ALTER TABLE `submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$v73sd4zU01dwYXjkJeWepOXtFOXwZPv05x8YTN5rCGrZDCqUNSSMu',1),(3,'lfelix','$2y$10$PFCPEEpayoYIT/4fqiVcgOCVABiwRLk2TyNfWRkxKsoCdOkpiP/Jm',1),(8,'acorrea','$2y$10$xdfOtBcSy.TpNTxKhSQPf.DNFKJkg4BXCjCLS7hFYXhnmx9l.RZSC',1),(12,'bxavier','$2y$10$Sf6YBtrH0xdKdxLX9SMWq.aLgFjuZyILWTIJbQBQZnJTVBMyGwBbe',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-28  9:16:32
