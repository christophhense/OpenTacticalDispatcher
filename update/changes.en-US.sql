DELETE FROM `captions` WHERE `capt` = 'development mode - do not use for dispatching';
DELETE FROM `captions` WHERE `capt` = 'Module';
DELETE FROM `captions` WHERE `capt` = 'Time Elapsed';
DELETE FROM `captions` WHERE `capt` = 'SVN project files still exist. Update is only simulated.';
DELETE FROM `hints` WHERE `tag` = 'development_mode';
UPDATE `hints` SET `hint` = 'Automatic updating, in tenths of a second - [synchronize with the server]<br>Default: 10' WHERE `tag` = 'auto_poll';
UPDATE `hints` SET `hint` = 'SMTP server for sending emails. Protocol, encryption and port information are optional. With \"smtps://\" or \"tls://\" the connection is only established encrypted, with \"smtp://\" or without specification an encrypted connection is only established if the server offers it. Example: smtps://mail.example.com:25. If no port is specified, the following are used: smtp 25, starttls 587, smtps/tls 465. If the Internet connection is potentially unstable, it is advisable to leave the delivery to a local smpt server, e.g. Postfix (Unix / Linux) or h-mail server (Windows).' WHERE `tag` = '_api_email_smtp_host';
UPDATE `hints` SET `hint` = 'Here the program can be updated over the Internet to a newer version. If newer versions are available, they are displayed with update notes. The update can only be made from one version to the next, it is not possible to skip versions. Close all other applications on the server-computer before performing the update! Usually, the update process takes only a few seconds, depending on the Internet speed. All other users are logged out during the update process. The update process is finished when the info box with the green progress bar disappears again and the display has automatically switched to the situation-site. To avoid malfunctions, all data in the browser cache must also be refreshed. Before the update, the cache must be deactivated in the browsers developer tools under the Network tab. Before updating, you should back up the database and the program files. An update should not be made immediately before a scheduled use of the program.' WHERE `tag` = 'Updates';
UPDATE `hints` SET `hint` = 'The update process is finished when the info box with the green progress bar disappears again and the display has automatically switched to the position display. To avoid malfunctions, all data in the browser cache must also be refreshed. Before the update, the cache must be deactivated in the browsers developer tools under the Network tab.' WHERE `tag` = 'Update-hint';
UPDATE `settings` SET `value` = '10' WHERE `name` = 'auto_poll';
ALTER TABLE `captions` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';
ALTER TABLE `hints` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Settings reseted.', 'Settings reseted.');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Reseted database-credentials only.', 'Reseted database-credentials only.');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Version control files still exist. Update is only simulated.', 'Version control files still exist. Update is only simulated.');