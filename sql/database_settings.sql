SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `captions` (
	`id` int(8) NOT NULL AUTO_INCREMENT,
	`capt` varchar(256) NOT NULL,
	`repl` varchar(256) NOT NULL,
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hints` (
	`id` int(8) NOT NULL AUTO_INCREMENT,
	`tag` varchar(200) NOT NULL,
	`hint` varchar(4096) NOT NULL,
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `settings` (
	`id` int(8) NOT NULL AUTO_INCREMENT,
	`name` tinytext,
	`value` varchar(512) DEFAULT NULL, 
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `presentation` (
	`id` int(8) NOT NULL AUTO_INCREMENT,
	`tab_id` int(8) NOT NULL,
	`type_id` int(8) NOT NULL,
	`row` int(8) NOT NULL,
	`item_id_0` int(8),
	`label_0` varchar(64) NOT NULL DEFAULT '',
	`item_id_1` int(8),
	`label_1` varchar(64) NOT NULL DEFAULT '',
	`item_id_2` int(8),
	`label_2` varchar(64) NOT NULL DEFAULT '',
	`item_id_3` int(8),
	`label_3` varchar(64) NOT NULL DEFAULT '',
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
