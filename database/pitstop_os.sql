-- PitStop OS - Milestone 2 database export (MySQL 8.0.39).
-- Import into a NEW empty pitstop_os database; existing tables are not dropped.
-- All sample users are fictional. Sample password: PitStop-M2-Sample!
-- Password hashes are stored; real login is a later milestone.
-- AUTO_INCREMENT gaps are expected after rolled-back DAO tests.

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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `pitstop_os` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `pitstop_os`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_price` decimal(10,2) NOT NULL,
  `minimum_stock` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  CONSTRAINT `chk_part_minimum` CHECK ((`minimum_stock` >= 0)),
  CONSTRAINT `chk_part_price` CHECK ((`unit_price` >= 0)),
  CONSTRAINT `chk_part_quantity` CHECK ((`quantity` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `parts` VALUES (1,'Engine oil 5W-30 (1 L)','OIL-530','Lubricants',24,12.00,10);
INSERT INTO `parts` VALUES (2,'Oil filter','FLT-001','Filters',3,17.00,5);
INSERT INTO `parts` VALUES (3,'Front brake pads','BRK-204','Brakes',8,65.00,4);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_order_id` int NOT NULL,
  `service_id` int DEFAULT NULL,
  `part_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_item_order` (`service_order_id`),
  KEY `fk_item_service` (`service_id`),
  KEY `fk_item_part` (`part_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_item_part` FOREIGN KEY (`part_id`) REFERENCES `parts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_item_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_item_price` CHECK ((`unit_price` >= 0)),
  CONSTRAINT `chk_item_quantity` CHECK ((`quantity` > 0)),
  CONSTRAINT `chk_item_source` CHECK ((((`service_id` is not null) and (`part_id` is null)) or ((`service_id` is null) and (`part_id` is not null))))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `service_order_items` VALUES (1,1,1,NULL,1,120.00);
INSERT INTO `service_order_items` VALUES (2,1,NULL,1,4,12.00);
INSERT INTO `service_order_items` VALUES (3,1,NULL,2,1,17.00);
INSERT INTO `service_order_items` VALUES (4,2,3,NULL,1,80.00);
INSERT INTO `service_order_items` VALUES (5,3,4,NULL,1,50.00);
INSERT INTO `service_order_items` VALUES (6,4,1,NULL,1,120.00);
INSERT INTO `service_order_items` VALUES (7,4,NULL,1,4,12.00);
INSERT INTO `service_order_items` VALUES (8,4,NULL,2,1,17.00);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_request_id` int NOT NULL,
  `mechanic_id` int DEFAULT NULL,
  `diagnosis` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Approved','Diagnosing','In Progress','Ready for Pickup','Completed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Approved',
  `estimated_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `final_cost` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_request_id` (`service_request_id`),
  KEY `fk_order_mechanic` (`mechanic_id`),
  CONSTRAINT `fk_order_mechanic` FOREIGN KEY (`mechanic_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_order_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_order_estimate` CHECK ((`estimated_cost` >= 0)),
  CONSTRAINT `chk_order_final` CHECK (((`final_cost` is null) or (`final_cost` >= 0)))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `service_orders` VALUES (1,1,4,'Routine service. Replace oil and filter.','In Progress',185.00,NULL,'2026-11-01 00:00:00');
INSERT INTO `service_orders` VALUES (2,3,4,'Inspect intake sensor and wiring.','Diagnosing',80.00,NULL,'2026-11-01 00:00:00');
INSERT INTO `service_orders` VALUES (3,4,4,'Winter tyres fitted and balanced.','Ready for Pickup',50.00,NULL,'2026-10-30 00:00:00');
INSERT INTO `service_orders` VALUES (4,5,4,'Oil and filter replaced. Inspection passed.','Completed',185.00,185.00,'2026-09-15 00:00:00');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `requested_by` int NOT NULL,
  `service_id` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `preferred_date` date NOT NULL,
  `priority` enum('Normal','High','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `status` enum('Requested','Approved','Diagnosing','In Progress','Ready for Pickup','Completed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Requested',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_request_vehicle` (`vehicle_id`),
  KEY `fk_request_customer` (`requested_by`),
  KEY `fk_request_service` (`service_id`),
  CONSTRAINT `fk_request_customer` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_request_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_request_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `service_requests` VALUES (1,1,1,1,'Annual service and oil replacement.','2026-11-03','Normal','In Progress','Please check wipers.','2026-10-28 00:00:00');
INSERT INTO `service_requests` VALUES (2,2,1,2,'Squeaking when braking at low speed.','2026-11-05','High','Requested','','2026-10-30 00:00:00');
INSERT INTO `service_requests` VALUES (3,3,2,3,'Engine warning light is on.','2026-11-02','Urgent','Diagnosing','','2026-10-29 00:00:00');
INSERT INTO `service_requests` VALUES (4,4,3,4,'Fit winter tyres.','2026-10-30','Normal','Ready for Pickup','','2026-10-27 00:00:00');
INSERT INTO `service_requests` VALUES (5,2,1,1,'Scheduled maintenance.','2026-09-15','Normal','Completed','','2026-09-10 00:00:00');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `standard_price` decimal(10,2) NOT NULL,
  `estimated_duration` int NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_service_duration` CHECK ((`estimated_duration` > 0)),
  CONSTRAINT `chk_service_price` CHECK ((`standard_price` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `services` VALUES (1,'Annual maintenance','Oil, filters and a full vehicle inspection.',120.00,90);
INSERT INTO `services` VALUES (2,'Brake inspection','Inspect pads, discs and brake fluid.',60.00,45);
INSERT INTO `services` VALUES (3,'Engine diagnostics','Fault-code scan and diagnostic report.',80.00,60);
INSERT INTO `services` VALUES (4,'Tyre change','Seasonal tyre fitting and wheel balancing.',50.00,40);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `service_request_id` int DEFAULT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('Normal','High','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `status` enum('Open','In Progress','Waiting for Customer','Resolved','Closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Open',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_ticket_customer` (`customer_id`),
  KEY `fk_ticket_request` (`service_request_id`),
  CONSTRAINT `fk_ticket_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_ticket_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `support_tickets` VALUES (1,1,1,'Can I collect my car after 17:00?','I finish work at 17:00. Is a later pickup possible?','Pickup','Normal','Open','2026-11-01 00:00:00');
INSERT INTO `support_tickets` VALUES (2,2,3,'Diagnostic estimate','Please confirm the estimate before further work.','Billing','High','Waiting for Customer','2026-10-31 00:00:00');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `sender_id` int NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_message_ticket` (`ticket_id`),
  KEY `fk_message_sender` (`sender_id`),
  CONSTRAINT `fk_message_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_message_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `ticket_messages` VALUES (1,1,1,'I finish work at 17:00. Is a later pickup possible?','2026-11-01 09:15:00');
INSERT INTO `ticket_messages` VALUES (2,1,5,'Of course, Amina. We are open until 18:00. We will let you know when your Volvo is ready.','2026-11-01 09:24:00');
INSERT INTO `ticket_messages` VALUES (3,2,2,'Please confirm the estimate before further work.','2026-10-31 12:00:00');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('customer','admin','mechanic') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `users` VALUES (1,'Amina Brković','amina@example.test','$2y$10$ed4G8.W/VRn9XwGarU8TO.eYIXYU0rYUw.7BsY58NonJvrQwzV.hG','customer','Sarajevo','2026-11-02 00:22:08');
INSERT INTO `users` VALUES (2,'Amir Delić','amir@example.test','$2y$10$ed4G8.W/VRn9XwGarU8TO.eYIXYU0rYUw.7BsY58NonJvrQwzV.hG','customer','Mostar','2026-11-02 00:22:08');
INSERT INTO `users` VALUES (3,'Sara Kovač','sara@example.test','$2y$10$ed4G8.W/VRn9XwGarU8TO.eYIXYU0rYUw.7BsY58NonJvrQwzV.hG','customer','Tuzla','2026-11-02 00:22:08');
INSERT INTO `users` VALUES (4,'Emin Bešić','emin@example.test','$2y$10$ed4G8.W/VRn9XwGarU8TO.eYIXYU0rYUw.7BsY58NonJvrQwzV.hG','mechanic','Sarajevo','2026-11-02 00:22:08');
INSERT INTO `users` VALUES (5,'Nina Marić','nina@example.test','$2y$10$ed4G8.W/VRn9XwGarU8TO.eYIXYU0rYUw.7BsY58NonJvrQwzV.hG','admin','Sarajevo','2026-11-02 00:22:08');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `vin` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` smallint NOT NULL,
  `license_plate` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mileage` int NOT NULL DEFAULT '0',
  `fuel_type` enum('Petrol','Diesel','Hybrid','Electric') COLLATE utf8mb4_unicode_ci NOT NULL,
  `transmission` enum('Automatic','Manual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `license_plate` (`license_plate`),
  UNIQUE KEY `vin` (`vin`),
  KEY `fk_vehicle_owner` (`user_id`),
  CONSTRAINT `fk_vehicle_owner` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_vehicle_mileage` CHECK ((`mileage` >= 0)),
  CONSTRAINT `chk_vehicle_vin` CHECK (((`vin` is null) or (char_length(`vin`) = 17))),
  CONSTRAINT `chk_vehicle_year` CHECK ((`year` between 1886 and 2155))
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `vehicles` VALUES (1,1,NULL,'Volvo','XC60',2022,'A12-K-345',42850,'Hybrid','Automatic','2026-11-02 00:22:08');
INSERT INTO `vehicles` VALUES (2,1,NULL,'Volkswagen','Golf VIII',2021,'E23-M-678',67200,'Diesel','Manual','2026-11-02 00:22:08');
INSERT INTO `vehicles` VALUES (3,2,NULL,'BMW','320d',2020,'J45-A-901',89200,'Diesel','Automatic','2026-11-02 00:22:08');
INSERT INTO `vehicles` VALUES (4,3,NULL,'Audi','A3',2023,'K67-T-234',21600,'Petrol','Automatic','2026-11-02 00:22:08');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

