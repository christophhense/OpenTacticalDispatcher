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