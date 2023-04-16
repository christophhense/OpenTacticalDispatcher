DELETE FROM `captions` WHERE `capt` = 'development mode - do not use for dispatching';
DELETE FROM `captions` WHERE `capt` = 'Module';
DELETE FROM `captions` WHERE `capt` = 'Time Elapsed';
DELETE FROM `hints` WHERE `tag` = 'development_mode';
UPDATE `hints` SET `hint` = 'Automatic updating, in tenths of a second - [synchronize with the server]<br>Default: 10' WHERE `tag` = 'auto_poll';
UPDATE `hints` SET `hint` = 'SMTP server for sending emails. Protocol, encryption and port information are optional. With \"smtps://\" or \"tls://\" the connection is only established encrypted, with \"smtp://\" or without specification an encrypted connection is only established if the server offers it. Example: smtps://mail.example.com:25. If no port is specified, the following are used: smtp 25, starttls 587, smtps/tls 465. If the Internet connection is potentially unstable, it is advisable to leave the delivery to a local smpt server, e.g. Postfix (Unix / Linux) or h-mail server (Windows).' WHERE `tag` = '_api_email_smtp_host';
UPDATE `settings` SET `value` = '10' WHERE `name` = 'auto_poll';
ALTER TABLE `captions` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';
ALTER TABLE `hints` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';
