DELETE FROM `captions` WHERE `capt` = 'USNG';
DELETE FROM `hints` WHERE `tag` = '_IncUSNG';
DELETE FROM `hints` WHERE `tag` = 'IncOSGB';
DELETE FROM `hints` WHERE `tag` = '__UTM2';
ALTER TABLE `settings` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';
ALTER TABLE `captions` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `captions` CHANGE `user_id` `user_id` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `hints` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `hints` CHANGE `user_id` `user_id` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `settings` CHANGE `user_id` `user_id` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `assigns` CHANGE `ticket_id` `ticket_id` int(8) DEFAULT NULL;
ALTER TABLE `assigns` CHANGE `unit_id` `unit_id` int(8) DEFAULT NULL;
ALTER TABLE `assigns` CHANGE `dispatching_user_id` `dispatching_user_id` int(8) NOT NULL;
ALTER TABLE `facilities` CHANGE `facility_status_id` `facility_status_id` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `facilities` CHANGE `user_id` `user_id` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `facility_status` CHANGE `sort` `sort` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `facility_types` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `incident_types` CHANGE `sort` `sort` int(8) DEFAULT NULL;
ALTER TABLE `log` CHANGE `ticket_id` `ticket_id` int(8) DEFAULT NULL;
ALTER TABLE `log` CHANGE `unit_id` `unit_id` int(8) DEFAULT NULL;
ALTER TABLE `log` CHANGE `facility_id` `facility_id` int(8) DEFAULT NULL;
ALTER TABLE `regions` CHANGE `sort` `sort` int(8) DEFAULT NULL;
ALTER TABLE `regions` CHANGE `owner_id` `owner_id` int(8) NOT NULL DEFAULT '1';
ALTER TABLE `textblocks` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `textblocks` CHANGE `sort` `sort` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `tickets` CHANGE `incident_type_id` `incident_type_id` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `tickets` CHANGE `facility_id` `facility_id` int(8) DEFAULT '0';
ALTER TABLE `units` CHANGE `unit_status_id` `unit_status_id` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `unit_status` CHANGE `sort` `sort` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `unit_types` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `api_log` CHANGE `code` `code` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `log` CHANGE `code` `code` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `actions` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `allocates` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `api_log` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `api_log` CHANGE `unit_id` `unit_id` int(8) DEFAULT NULL;
ALTER TABLE `assigns` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `facilities` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `facility_status` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `facility_status` CHANGE `display` `display` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `incident_types` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `log` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `regions` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `textblocks` CHANGE `report_channels` `report_channels` int(8) NOT NULL DEFAULT '0';	
ALTER TABLE `tickets` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `units` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `units` CHANGE `parent_unit_id` `parent_unit_id` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `units` CHANGE `guard_house_id` `guard_house_id` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `unit_status` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users` CHANGE `id` `id` int(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Presentation', 'Presentation');
CREATE TABLE IF NOT EXISTS `presentation` (
	`id` int(8) NOT NULL AUTO_INCREMENT,
	`tab_id` int(8) NOT NULL,
	`typ` int(8) NOT NULL,
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
INSERT INTO `presentation` (`tab_id`, `type_id`, `row`, `item_id_0`, `label_0`, `item_id_1`, `label_1`, `item_id_2`, `label_2`, `item_id_3`, `label_3`, 
	`client_address`, `user_id`, `updated`) VALUES
	(0, 0, 0, 0, '', 0, '', 0, '', 0, '', '127.0.0.1', 1, NOW()),
	(1, 2, 0, 3, 'Situation', 3, '', 10, '', 0, '', '127.0.0.1', 1, NOW()),
	(2, 1, 0, 3, 'Tickets', 0, '', 20, '', 0, '', '127.0.0.1', 1, NOW()),
	(3, 1, 0, 3, 'Scheduled tickets_short', 0, '', 30, '', 0, '', '127.0.0.1', 1, NOW()),
	(4, 1, 0, 3, 'Closed tickets_short', 0, '', 40, '', 0, '', '127.0.0.1', 1, NOW());
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Facilities presentation configuration', 'Facilities presentation configuration');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Units presentation configuration', 'Units presentation configuration');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab order preview', 'Tab order preview');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Admin can add', 'Admin can add');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab name', 'Tab name');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Visible', 'Visible');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Add tickets', 'Add tickets');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Colums', 'Colums');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Rows', 'Rows');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Admin can config', 'Admin can config');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Facility tab', 'Facility tab');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Unit tab', 'Unit tab');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Singlemonitor only', 'Singlemonitor only');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Multimonitor only', 'Multimonitor only');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab show/hide', 'Tab show/hide');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab edit/delete', 'Tab edit/delete');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Set to admin can add facility tabs', 'Set to admin can add facility tabs');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Set to admin can not add facility tabs', 'Set to admin can not add facility tabs');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('New facility tab added', 'New facility tab added');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Facilty tabs updated', 'Facilty tabs updated');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Facility tabs deleted', 'Facility tabs deleted');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Set to admin can add unit tabs', 'Set to admin can add unit tabs');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Set to admin can not add unit tabs', 'Set to admin can not add unit tabs');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('New unit tab added', 'New unit tab added');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Unit tabs updated', 'Unit tabs updated');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Unit tabs deleted', 'Unit tabs deleted');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Changed Tab name', 'Changed Tab name');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Deleted Tab name', 'Deleted Tab name');