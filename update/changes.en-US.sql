ALTER TABLE `tickets` CHANGE `scope` `incident_name` text;
ALTER TABLE `units` MODIFY COLUMN `parent_unit_id` bigint(4) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `units` MODIFY COLUMN `guard_house_id` bigint(4) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `actions` ADD `call_taker_id` int(8) NOT NULL DEFAULT '0';
DELETE FROM `settings` WHERE `name` = '_serial_no_ap';
DELETE FROM `captions` WHERE `capt` = 'Printed by';
DELETE FROM `captions` WHERE `capt` = 'Call is already closed';
UPDATE `hints` SET `hint` = 'Here the program can be updated over the Internet to a newer version. If newer versions are available, they are displayed with update notes. The update can only be made from one version to the next, it is not possible to skip versions. Close all other applications on the server-computer before performing the update! Usually, the update process takes only a few seconds, depending on the Internet speed. All other users are logged out during the update process. The update process is finished when the situation-site is displayed. Before updating, you should back up the database and the program files. An update should not be made immediately before a scheduled use of the program.' WHERE `tag` = 'Updates';
UPDATE `hints` SET `hint` = '[Show last entries in reports, from 5 - 99999 in minutes], [Entries for communication are hidden by default, 1 = Hidden], [Entries for status are hidden by default, 1 = Hidden], [Entries for settings are hidden by default, 1 = Hidden] Default setting: 720, 720, 720, 0, 0, 1' WHERE `tag` = 'report_last';
UPDATE `hints` SET `hint` = '[Show last entries in the log, from 5 - 99999 in minutes], [Show last manual entries in the tooltip of the respective unit, 0 to 99999 in minutes, 0 = disabled], [Show last manual entries in the tooltip of the respective object 0 - 99999 in minutes, 0 = disabled], [Entries for communication are hidden by default, 1 = Hidden], [Entries for status are hidden by default, 1 = Hidden], [Entries for settings are hidden by default, 1 = Hidden] Default setting: 720, 1440, 1440, 0, 0, 1' WHERE `tag` = 'report_log';
UPDATE `hints` SET `hint` = 'Here you can define the status designations for the availability of units. For each status it can be set whether the disposition in a ticket should be possible or not. A disposition triggered via the communication side is always possible. The current status of the respective unit is not relevant. The pre-configured at this point Status values are necessary for the proper functioning of the application interface and can not be modified or deleted if they are in use. Changes at this point are not usually required. The status label for use progress and answering requests are predefined in program code.' WHERE `tag` = 'set_units_status_value';
UPDATE `hints` SET `hint` = 'Here the program can be updated over the Internet to a newer version. If newer versions are available, they are displayed with update notes. The update can only be made from one version to the next, it is not possible to skip versions. Close all other applications on the server-computer before performing the update! Usually, the update process takes only a few seconds, depending on the Internet speed. All other users are logged out during the update process. The update process is finished when the info box with the green progress bar disappears again and the display has automatically switched to the situation-site. To avoid malfunctions, all data in the browser cache must also be refreshed. The easiest way is to log the user out once and close the browser. After opening the browser and re-logging in a user, all information in the browser cache should have been refreshed. Before updating, you should back up the database and the program files. An update should not be made immediately before a scheduled use of the program.' WHERE `tag` = 'Updates';
UPDATE `hints` SET `hint` = 'Call Board - [0, 1, 2 - for none, floating window, fixed frame], [fixed part in pix], [heigh per line in pix], [min. height in pix],  [max. height in pix]<br>Default: 0, 80, 35, 80, 300' WHERE `tag` = 'callboard';
UPDATE `hints` SET `hint` = 'Phone number. Numbers can be specified separated by comma.' WHERE `tag` = '_ResPhon';
UPDATE `hints` SET `hint` = 'Phone number for callback. The characters *#+-/0123456789 are permitted. Numbers can be specified separated by comma. When selecting the reporter from the selection field, telephone numbers are automatically adopted.' WHERE `tag` = '_callback';
UPDATE `hints` SET `hint` = 'Caller reporting the incident. Stored phone numbers are automatically transferred to the Callback number field.' WHERE `tag` = '_callback';
UPDATE `hints` SET `hint` = '[After new ticket automatically: to situation: 0, to dispatch units: 1], [after last unit clear automatically: no action: 0, to edit ticket: 1, to close ticket: 2], [The browsers back button takes you back to the situation page: Off: 0, On: 1]. Default: 1, 1, 1' WHERE `tag` = 'auto_dispatch';
UPDATE `settings` SET `value` = CONCAT(`value`, ', 0, 0, 1') WHERE `name` = 'report_last';
UPDATE `settings` SET `value` = '720, 1440, 1440, 0, 0, 1' WHERE `name` = 'report_log';
UPDATE `settings` SET `value` = '1, 1, 1' WHERE `name` = 'auto_dispatch';
INSERT INTO `captions` (`capt`, `repl`) VALUES ('System', 'System');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Login failed. Username', 'Login failed. Username');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Printed at', 'Printed at');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Incident dispatch system', 'Incident dispatch system');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Click to edit log report', 'Click to edit log report');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Click right to set status', 'Click right to set status');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Hide', 'Hide');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Window too low for menu!', 'Window too low for menu!');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Selected for transfer in the Callback number field on the New-ticket and Edit-ticket page.', 'Selected for transfer in the Callback number field on the New-ticket and Edit-ticket page.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('_reports_filter', 'Entries in the categories communication, status and settings can be hidden when the log is displayed.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('Update-hint', 'The update process is finished when the info box with the green progress bar disappears again and the display has automatically switched to the situation-site. To avoid malfunctions, all data in the browser cache must also be refreshed. The easiest way is to log the user out once and close the browser. After opening the browser and re-logging in a user, all information in the browser cache should have been refreshed.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('reported_by_phone', 'Transfer of the telephone number of the Incident-reporter from the Select-field to the Callback number field. [Facility Direct dialing 1, 1 = Selected], [Facility Direct dialing 2, 1 = Selected], [Facility Security phone, 1 = Selected], [Facility Contact phone, 1 = Selected], [Unit Cellular phone, 1 = Selected] Default: 0, 0, 1, 0, 1');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('parking_form_data', '[Add ticket - activate the parking function based on the characters entered in the fields of location, callback number or emergency situation, from 1 - 99 in number of characters, 0 = off], [Add ticket - storage period of the parking function, from 1 - 999 in seconds, 0 = off] , [New action - activate the parking function using the characters entered, from 1 - 99 in number of characters, 0 = off], [New action - storage period of the parking function, from 1 - 999 in seconds, 0 = off], [Close ticket - activate parking function based on the characters entered in the field Synopsis or Comments, from 1 - 99 in number of characters, 0 = off], [Close ticket - storage period of the parking function, from 1 - 999 in seconds, 0 = off], [Log - activate the parking function using entered characters, from 1 - 99 in number of characters, 0 = off], [Log - storage period of the parking function, from 1 - 999 in seconds, 0 = off]. Default setting: 10, 90, 10, 90, 10, 90, 10, 90');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('parked_trigger_chars', 'Entered text is parked at the following minimum number of characters');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('parked_seconds', 'The parking time is in seconds');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('show_hide_password', 'Show or hide password.');
INSERT INTO `settings` (`name`, `value`) VALUES ('reported_by_phone', '0, 0, 1, 0, 1');
INSERT INTO `settings` (`name`, `value`) VALUES ('parking_form_data', '10, 90, 10, 90, 10, 90, 10, 90');