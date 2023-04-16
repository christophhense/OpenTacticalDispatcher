DELETE FROM `captions` WHERE `capt` = 'development mode - do not use for dispatching';
DELETE FROM `captions` WHERE `capt` = 'Module';
DELETE FROM `captions` WHERE `capt` = 'Time Elapsed';
DELETE FROM `hints` WHERE `tag` = 'development_mode';
UPDATE `hints` SET `hint` = 'Automatische Aktualisierung, in Zehntelsekunden - [Syncronisierung mit dem Server]<br>Voreinstellung: 10' WHERE `tag` = 'auto_poll';
UPDATE `hints` SET `hint` = 'SMTP-Server für E-Mailversand. Angaben zu Protokoll, Verschlüsselung und Port sind optional. Bei \"smtps://\" oder \"tls://\" wird die Verbindung nur verschlüsselte aufgebaut, bei \"smtp://\" oder ohne Angabe wird eine verschlüsselte Verbindung nur aufgebaut, wenn der Server dies anbietet. Bsp.: smtps://mail.example.com:25. Ohne Portangabe werden folgende verwendet: smtp 25, starttls 587, smtps/tls 465. Bei potentiell instabiler Internetverbindung bietet es sich an, die Zustellung einem lokalen smpt-Server zu überlassen, z.B. Postfix (Unix / Linux) oder h-Mailserver (Windows).' WHERE `tag` = '_api_email_smtp_host';
UPDATE `settings` SET `value` = '10' WHERE `name` = 'auto_poll';
ALTER TABLE `captions` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';
ALTER TABLE `hints` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';
