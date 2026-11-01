-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: localhost    Database: online_banking
-- ------------------------------------------------------
-- Server version	8.0.45

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

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `loan_id` varchar(30) NOT NULL,
  `loan_type` varchar(50) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `remaining` decimal(15,2) NOT NULL,
  `duration` int NOT NULL,
  `income` decimal(15,2) NOT NULL,
  `purpose` text,
  `emi` decimal(15,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loan_id` (`loan_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loans`
--

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
INSERT INTO `loans` VALUES (1,3,'HO-2026-1825','Home Loan',100000.00,100000.00,15,50000.00,'0',7073.64,'approved','2026-02-21 14:29:57'),(2,3,'PE-2026-4795','Personal Loan',25000.00,25000.00,6,10000.00,'0',4276.72,'pending','2026-02-22 11:37:26');
/*!40000 ALTER TABLE `loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `money_requests`
--

DROP TABLE IF EXISTS `money_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `money_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `requester_id` int NOT NULL,
  `payer_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `status` enum('pending','paid','declined') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `requester_id` (`requester_id`),
  KEY `payer_id` (`payer_id`),
  CONSTRAINT `money_requests_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `money_requests_ibfk_2` FOREIGN KEY (`payer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `money_requests`
--

LOCK TABLES `money_requests` WRITE;
/*!40000 ALTER TABLE `money_requests` DISABLE KEYS */;
INSERT INTO `money_requests` VALUES (1,2,3,100.00,'Dinner split','paid','2026-02-21 14:27:38'),(2,4,3,1000.00,'for shopping','paid','2026-02-22 11:25:11'),(3,3,2,100.00,'dinner split','pending','2026-02-23 04:53:28');
/*!40000 ALTER TABLE `money_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `related_account` varchar(8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,2,'debit',500.00,'Sent to Aryan More — for education','23456781','2026-02-21 14:19:49'),(2,3,'credit',500.00,'Received from Test User — for education','12345678','2026-02-21 14:19:49'),(3,3,'debit',100.00,'Paid request to Test User','12345678','2026-02-21 14:28:23'),(4,2,'credit',100.00,'Received from Aryan More','23456781','2026-02-21 14:28:23'),(5,3,'credit',100000.00,'Loan Disbursed: Home Loan #HO-2026-1825',NULL,'2026-02-21 14:34:09'),(6,4,'debit',500.00,'Sent to Aryan More — get 3 shawarma and cold drinks','23456781','2026-02-22 11:21:15'),(7,3,'credit',500.00,'Received from Divya Gopale — get 3 shawarma and cold drinks','77316640','2026-02-22 11:21:15'),(8,3,'debit',500.00,'Sent to Test User — for education','12345678','2026-02-23 04:52:59'),(9,2,'credit',500.00,'Received from Aryan More — for education','23456781','2026-02-23 04:52:59'),(10,3,'debit',1000.00,'Paid request to Divya Gopale','77316640','2026-02-23 04:53:37'),(11,4,'credit',1000.00,'Received from Aryan More','23456781','2026-02-23 04:53:37'),(12,2,'debit',100.00,'Sent to Aryan More — Transfer','23456781','2026-10-30 12:43:19'),(13,3,'credit',100.00,'Received from Test User — Transfer','12345678','2026-10-30 12:43:19');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `account_number` varchar(8) NOT NULL,
  `balance` decimal(15,2) DEFAULT '1000.00',
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `account_number` (`account_number`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@securebank.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','00000001',999999.00,1,'2026-02-21 14:10:30'),(2,'Test User','test@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','12345678',5000.00,1,'2026-02-21 14:10:30'),(3,'Aryan More','aryan@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','23456781',102500.00,0,'2026-02-21 14:10:30'),(4,'Divya Gopale','divya@gmail.com','$2y$10$7Hl7/MDBvuvuJ/b9n3HE5uAW1KtAZ9SnR7RUTHIAkJU475xPB9ytO','77316640',1500.00,0,'2026-02-22 11:13:48'),(5,'Aarti More','aartimore10@gmail.com','$2y$10$A//8eGK/a4UuTqhTV6u/NebNiLcxYTlAQJbB.0Hq6iP8V7RIIBk16','65465304',1000.00,0,'2026-08-07 16:48:35');
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

-- Dump completed on 2026-11-01 18:09:01
