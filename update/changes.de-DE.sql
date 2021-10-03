DELETE FROM `captions` WHERE `capt` = 'USNG';
DELETE FROM `hints` WHERE `tag` = '_IncUSNG';
DELETE FROM `hints` WHERE `tag` = 'IncOSGB';
DELETE FROM `hints` WHERE `tag` = '__UTM2';
ALTER TABLE `settings` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';