SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP TABLE IF EXISTS `captions`;

DROP TABLE IF EXISTS `hints`;

DROP TABLE IF EXISTS `settings`;

DELETE FROM `textblocks` WHERE `type` = 'fixtext';

DELETE FROM `unit_status` WHERE `id` = 1;
DELETE FROM `unit_status` WHERE `id` = 2;
DELETE FROM `unit_status` WHERE `id` = 3;
DELETE FROM `unit_status` WHERE `id` = 4;
DELETE FROM `unit_status` WHERE `id` = 5;

DELETE FROM `facility_status` WHERE `id` = 1;
DELETE FROM `facility_status` WHERE `id` = 2;
DELETE FROM `facility_status` WHERE `id` = 3;
DELETE FROM `facility_status` WHERE `id` = 4;

DELETE FROM `incident_types` WHERE `id` = 1;

DROP TABLE IF EXISTS `presentation`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
