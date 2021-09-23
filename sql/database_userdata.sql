SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `actions` (
	`id` bigint(8) NOT NULL AUTO_INCREMENT,
	`ticket_id` int(8) NOT NULL DEFAULT '0',
	`description` text DEFAULT NULL,
	`action_type` int(8) DEFAULT NULL,
	`unit_id` int(8) DEFAULT NULL,
	`call_taker_id` int(8) NOT NULL DEFAULT '0',
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	`datetime` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `allocates` (
	`id` bigint(8) NOT NULL AUTO_INCREMENT,
	`group` int(8) NOT NULL DEFAULT '1',
	`type` tinyint(1) NOT NULL DEFAULT '1',
	`resource_id` int(8) DEFAULT NULL,
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL, 
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `api_log` (
	`id` bigint(16) NOT NULL AUTO_INCREMENT,
	`datetime` datetime DEFAULT NULL,
	`source` varchar(64) DEFAULT NULL,
	`source_regexp` varchar(1024) DEFAULT NULL,
	`unit_id` bigint(8) DEFAULT NULL,
	`destination` varchar(32) DEFAULT NULL,
	`destination_alias` varchar(32) DEFAULT NULL,
	`audio_link` varchar(64) DEFAULT NULL,
	`code` smallint(7) NOT NULL DEFAULT '0',
	`text` varchar(2048) DEFAULT NULL,
	`lat` double DEFAULT NULL,
	`lng` double DEFAULT NULL,
	`host` varchar(50) NOT NULL DEFAULT '0.0.0.0'COMMENT 'ip-address',
	`cleared_user_id` int(8) DEFAULT NULL,
	`cleared_datetime` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log of application interface actions';

CREATE TABLE IF NOT EXISTS `assigns` (
	`id` bigint(8) NOT NULL AUTO_INCREMENT,
	`ticket_id` int(4) DEFAULT NULL,
	`unit_id` int(4) DEFAULT NULL,
	`comments` varchar(1024) DEFAULT NULL,
	`start_miles` int(8) DEFAULT NULL,
	`on_scene_miles` int(8) DEFAULT NULL,
	`end_miles` int(8) DEFAULT NULL,
	`miles` int(8) DEFAULT NULL,
	`dispatched` datetime DEFAULT NULL,
	`responding` datetime DEFAULT NULL,
	`on_scene` datetime DEFAULT NULL,
	`u2fenr` datetime DEFAULT NULL,
	`u2farr` datetime DEFAULT NULL, 
	`clear` datetime DEFAULT NULL,
	`on_scene_location` text,
	`on_scene_facility_id` int(8) DEFAULT NULL,
	`on_scene_lat` double DEFAULT NULL,
	`on_scene_lng` double DEFAULT NULL,
	`receiving_location` text,
	`receiving_facility_id` int(8) DEFAULT NULL,
	`receiving_lat` double DEFAULT NULL,
	`receiving_lng` double DEFAULT NULL,
	`progession_changed` VARCHAR(10) DEFAULT NULL,
	`user_id` int(8) NOT NULL DEFAULT '0',
	`updated` datetime DEFAULT NULL,
	`dispatching_user_id` int(4) NOT NULL,
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`datetime` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facilities` (
	`id` bigint(8) NOT NULL AUTO_INCREMENT,
	`name` text,
	`handle` varchar(64) DEFAULT NULL,
	`object_id` varchar(28) DEFAULT NULL,
	`direct_dialing_1` varchar(255) DEFAULT NULL,
	`direct_dialing_2` varchar(255) DEFAULT NULL,
	`street` text,
	`city` varchar(28) DEFAULT NULL,
	`security_contact` varchar(64) DEFAULT NULL,
	`security_phone` varchar(255) DEFAULT NULL,
	`security_email` varchar(255) DEFAULT NULL,
	`type` tinyint(3) DEFAULT NULL,
	`facility_status_id` int(4) NOT NULL DEFAULT '0',
	`description` text DEFAULT '',
	`capabilities` varchar(255) DEFAULT NULL,
	`opening_hours` mediumtext,
	`access_rules` mediumtext,
	`contact_name` varchar(64) DEFAULT NULL,
	`contact_phone` varchar(255) DEFAULT NULL,
	`contact_email` varchar(255) DEFAULT NULL,
	`admin_only` tinyint(1) NOT NULL DEFAULT '0',
	`icon_url` varchar(255) DEFAULT NULL COMMENT 'map icon value',
	`boundary` varchar(255) DEFAULT NULL,
	`lat` double DEFAULT NULL,
	`lng` double DEFAULT NULL,
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facility_status` (
	`id` bigint(4) NOT NULL AUTO_INCREMENT,
	`status_name` varchar(30) DEFAULT NULL,
	`description` varchar(60) DEFAULT NULL,
	`sort` int(11) NOT NULL DEFAULT '0',
	`display` bigint(1) NOT NULL DEFAULT '0',
	`bg_color` varchar(16) NOT NULL DEFAULT 'transparent',
	`text_color` varchar(16) NOT NULL DEFAULT '#000000',
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `facility_types` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(48) DEFAULT NULL,
	`description` varchar(96) DEFAULT NULL,
	`bg_color` varchar(16) NOT NULL DEFAULT 'transparent' COMMENT 'background color',
	`text_color` varchar(16) NOT NULL DEFAULT '#000000' COMMENT 'text color',
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allows for variable facility types';

CREATE TABLE IF NOT EXISTS `incident_types` (
	`id` bigint(4) NOT NULL AUTO_INCREMENT,
	`type` varchar(20) NOT NULL,
	`description` varchar(255) NOT NULL,
	`protocol` varchar(255) DEFAULT NULL,
	`set_severity` int(1) NOT NULL DEFAULT '0' COMMENT 'sets incident severity',
	`group` varchar(20) DEFAULT NULL,
	`sort` int(11) DEFAULT NULL,
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Incident types';

CREATE TABLE IF NOT EXISTS `log` (
	`id` bigint(8) NOT NULL AUTO_INCREMENT,
	`code` smallint(7) NOT NULL DEFAULT '0',
	`ticket_id` int(7) DEFAULT NULL,
	`unit_id` int(7) DEFAULT NULL,
	`facility_id` int(7) DEFAULT NULL,
	`text` varchar(2048) DEFAULT NULL,
	`lat` double DEFAULT NULL,
	`lng` double DEFAULT NULL,
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`datetime` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log of station actions';

CREATE TABLE IF NOT EXISTS `regions` (
	`id` bigint(8) NOT NULL AUTO_INCREMENT,
	`region_name` varchar(60) NOT NULL,
	`group` varchar(20) DEFAULT NULL,
	`sort` int(11) DEFAULT NULL,
	`description` varchar(60) DEFAULT NULL,
	`owner_id` int(2) NOT NULL DEFAULT '1',
	`def_city` varchar(20) DEFAULT NULL,
	`def_lat` double DEFAULT NULL,
	`def_lng` double DEFAULT NULL,
	`def_zoom` int(2) NOT NULL DEFAULT '10',
	`boundary` int(4) DEFAULT NULL, 
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `textblocks` (
	`id` int(7) NOT NULL AUTO_INCREMENT,
	`type` varchar(20) DEFAULT '',
	`group` varchar(20) DEFAULT '',
	`text` varchar(128) DEFAULT '',
	`code` varchar(128) DEFAULT '',
	`report_channels` bigint(8) NOT NULL DEFAULT '0',
	`sort` int(3) NOT NULL DEFAULT '0',
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tickets` (
	`id` bigint(8) NOT NULL AUTO_INCREMENT,
	`incident_type_id` int(4) NOT NULL DEFAULT '0',
	`contact` varchar(48) NOT NULL DEFAULT '',
	`location` text,
	`phone` varchar(48) DEFAULT NULL,
	`facility_id` int(4) DEFAULT '0',
	`problemstart` datetime DEFAULT NULL,
	`problemend` datetime DEFAULT NULL,
	`incident_name` text DEFAULT '',
	`description` text DEFAULT '',
	`comments` text,
	`status` tinyint(1) NOT NULL DEFAULT '0',
	`severity` int(2) NOT NULL DEFAULT '0',
	`booked_date` datetime DEFAULT NULL,
	`lat` double DEFAULT NULL,
	`lng` double DEFAULT NULL,
	`call_taker_id` tinyint(4) NOT NULL DEFAULT '0',
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	`datetime` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `units` (
	`id` bigint(8) NOT NULL AUTO_INCREMENT,
	`name` text,
	`handle` varchar(32) DEFAULT NULL,
	`remote_data_services` varchar(255) DEFAULT NULL,
	`unit_phone` varchar(255) DEFAULT NULL,
	`unit_email` varchar(255) DEFAULT NULL,
	`type` tinyint(3) DEFAULT NULL,
	`unit_status_id` int(4) NOT NULL DEFAULT '0',
	`multi` int(1) NOT NULL DEFAULT '0' COMMENT 'if 2, allow multiple call assigns',
	`mobile` varchar(32) DEFAULT NULL,
	`parent_unit_id` bigint(4) UNSIGNED NOT NULL DEFAULT '0',
	`guard_house_id` bigint(4) UNSIGNED NOT NULL DEFAULT '0',
	`description` text NOT NULL,
	`capabilities` varchar(255) DEFAULT NULL COMMENT 'Capability',
	`contact_name` varchar(64) DEFAULT NULL,
	`admin_only` tinyint(1) NOT NULL DEFAULT '0',
	`icon_url` char(255) DEFAULT NULL COMMENT 'map icon value',
	`lat` varchar(255) DEFAULT NULL,
	`lng` varchar(255) DEFAULT NULL,
	`lat_lng_updated` datetime DEFAULT NULL,
	`status_updated` datetime DEFAULT NULL,
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `unit_status` (
	`id` bigint(4) NOT NULL AUTO_INCREMENT,
	`status_name` varchar(30) NOT NULL,
	`description` varchar(60) NOT NULL,
	`dispatch` int(1) NOT NULL DEFAULT '0'COMMENT '0 - can dispatch, 1 - no - enforceable, 2 - no - not enforceable, 3 - no - only monitor, 4 - no - no evaluation',
	`sort` int(11) NOT NULL DEFAULT '0',
	`bg_color` varchar(16) NOT NULL DEFAULT 'transparent' COMMENT 'background color',
	`text_color` varchar(16) NOT NULL DEFAULT '#000000' COMMENT 'text color',
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `unit_types` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(48) NOT NULL,
	`description` varchar(96) NOT NULL,
	`bg_color` varchar(16) NOT NULL DEFAULT 'transparent' COMMENT 'background color',
	`text_color` varchar(16) NOT NULL DEFAULT '#000000' COMMENT 'text color',
	`user_id` int(8) NOT NULL DEFAULT '0',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allows for variable unit types';

CREATE TABLE IF NOT EXISTS `users` (
	`id` bigint(8) NOT NULL AUTO_INCREMENT,
	`name` text NOT NULL COMMENT 'user name',
	`password` tinytext NOT NULL COMMENT 'MySQL hash',
	`level` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'privileges',
	`email` varchar(255) COMMENT 'email addr - primary',
	`expires` timestamp NULL DEFAULT NULL COMMENT 'session start time',
	`session_id` varchar(40) DEFAULT NULL COMMENT 'php session id',
	`current_radio` varchar(40) DEFAULT NULL COMMENT 'current radio circuit',
	`browser` varchar(40) DEFAULT NULL COMMENT 'used at last login', 
	`individual` varchar(1024) DEFAULT '',
	`login_datetime` timestamp NULL DEFAULT NULL COMMENT 'last login',
	`login_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'login-ip-address',
	`client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'change-user-ip-address',
	`user_id` int(8) NOT NULL DEFAULT '0' COMMENT 'change-user-ip-address',
	`updated` datetime DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
