DELETE FROM `captions` WHERE `capt` = 'development mode - do not use for dispatching';
DELETE FROM `captions` WHERE `capt` = 'Module';
DELETE FROM `captions` WHERE `capt` = 'Time Elapsed';
DELETE FROM `hints` WHERE `tag` = 'development_mode';
UPDATE `hints` SET `hint` = 'Automatische Aktualisierung, in Zehntelsekunden - [Syncronisierung mit dem Server]<br>Voreinstellung: 10' WHERE `tag` = 'auto_poll';
UPDATE `settings` SET `value` = '10' WHERE `name` = 'auto_poll';