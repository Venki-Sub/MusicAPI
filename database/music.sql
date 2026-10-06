-- Music API database: run this in phpMyAdmin (Import tab) or: mysql -u root -p < database/music.sql
CREATE DATABASE IF NOT EXISTS `music`;
USE `music`;


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
DROP TABLE IF EXISTS `artists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artists` (
  `idArtist` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) DEFAULT NULL,
  `Annee` varchar(4) DEFAULT NULL,
  `Description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idArtist`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `artists` VALUES (4,'Aani','2000','Techno');
INSERT INTO `artists` VALUES (5,'Venki','1988','rock');
INSERT INTO `artists` VALUES (6,'AR','1975','Roja');
INSERT INTO `artists` VALUES (7,'TATA','1964','Billa');
INSERT INTO `artists` VALUES (8,'Jill','1979','very good');
INSERT INTO `artists` VALUES (9,'Will','1988','All is well');
INSERT INTO `artists` VALUES (10,'Raja','1978','Melody');
INSERT INTO `artists` VALUES (11,'Joyce','1988','Love');
INSERT INTO `artists` VALUES (12,'Eric','2000','Guiter');
INSERT INTO `artists` VALUES (13,'celine','2000','Titanic');
DROP TABLE IF EXISTS `albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `albums` (
  `idAlbums` int NOT NULL AUTO_INCREMENT,
  `Titre` varchar(45) DEFAULT NULL,
  `Artist_idArtist` int NOT NULL,
  PRIMARY KEY (`idAlbums`),
  KEY `fk_Albums_Artist1_idx` (`Artist_idArtist`),
  CONSTRAINT `fk_Albums_Artist1` FOREIGN KEY (`Artist_idArtist`) REFERENCES `artists` (`idArtist`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `albums` VALUES (5,'SoloSun',4);
INSERT INTO `albums` VALUES (6,'Sunrise',4);
INSERT INTO `albums` VALUES (7,'HappyTime',5);
INSERT INTO `albums` VALUES (8,'PartyNight',5);
INSERT INTO `albums` VALUES (9,'Roja',6);
INSERT INTO `albums` VALUES (10,'annecy',12);
DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ratings` (
  `idRatings` int NOT NULL AUTO_INCREMENT,
  `Grade` varchar(45) DEFAULT NULL,
  `Albums_idAlbums` int NOT NULL,
  PRIMARY KEY (`idRatings`),
  KEY `fk_Ratings_Albums_idx` (`Albums_idAlbums`),
  CONSTRAINT `fk_Ratings_Albums` FOREIGN KEY (`Albums_idAlbums`) REFERENCES `albums` (`idAlbums`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `ratings` VALUES (5,'5 Star',7);
INSERT INTO `ratings` VALUES (6,'3 Star',8);
INSERT INTO `ratings` VALUES (7,'5 star',9);
INSERT INTO `ratings` VALUES (8,'5 Star',5);
INSERT INTO `ratings` VALUES (9,'2 Star',6);
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

