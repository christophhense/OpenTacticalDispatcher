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
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Presentation', 'Presentation');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Facilities presentation configuration', 'Facilities presentation configuration');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Units presentation configuration', 'Units presentation configuration');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab order preview', 'Tab order preview');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Admin can add', 'Admin can add');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab name', 'Tab name');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Visible', 'Visible');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Add tickets', 'Add tickets');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Columns', 'Columns');
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
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Edit custom units representation', 'Edit custom units representation');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Edit custom facilities representation', 'Edit custom facilities representation');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Row', 'Row');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Column', 'Column');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('facility_presentation', 'The display and order of the tabs for objects in the situation overview is defined here. Tabs with a user-defined object overview can be created here. Tabs for units, objects and operations are shown in a preview with the set order and visibility.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('facility_presentation_tab_list', 'User-defined facility overviews for the situation overview can be created and managed here. The release of individual settings for the Admin user role can also be carried out here. To create a new user-defined facility overview, its tab name must have at least 4 characters. Only uppercase and lowercase letters, numbers, periods, minus signs, underscores and spaces can be used as characters. In order to be able to change a tab name, this rule must also be adhered to. The visibility of a tab can be defined under the Visible menu item. This can be used, for example, to hide a prepared user-defined facility overview and only offer it in a certain context. The visibility can be configured depending on the number of screens used. The automatic detection of the number of screens used and the resulting adjustment of the situation overview may take a few seconds. The order of the tabs in the location overview is defined using the number in the Sort by field. The display is from left to right in ascending order using the numbers configured here, depending on the configuration of tabs with units and tickets. The columns and rows used by the respective user-defined facility overview are displayed in the Columns and Rows fields in order to get a quick overview of the types of display to be expected. Under the menu items Admin can add and Admin can edit, the authorizations for the Admin user role can be defined in stages.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('unit_presentation', 'The representation and order of the tabs for units and tickets in the situation overview is defined here. In addition to the situation-tab, other tabs with a user-defined unit overview can be created. Tabs for units, objects and operations are shown in a preview with the set order and visibility.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('unit_presentation_tab_list', 'User-defined unit overviews for the situation overview can be created and managed here. The release of individual settings for the admin user role can also be carried out here. To create a new user-defined unit overview, its tab name must have at least 4 characters. Only uppercase and lowercase letters, numbers, periods, minus signs, underscores and spaces can be used as characters. In order to be able to change a tab name, this rule must also be adhered to. The visibility of a tab can be defined under the Visible menu item. This can be used, for example, to hide a prepared user-defined unit overview and only offer it in a certain context. If the tabs for location and operations are configured as invisible, they are only hidden if the information they contain is visible under other tabs that are shown. With the menu item Show tickets, tickets can also be shown under tabs with units. The aforementioned rule applies accordingly to hiding the inserts in the Situation tab. The visibility of the user-defined unit overview as well as the additionally displayed operations can be configured depending on the number of screens used. The automatic detection of the number of screens used and the resulting adjustment of the situation overview may take a few seconds. The order of the tabs in the location overview is defined using the number in the Sort by field. The display is from left to right in ascending order using the numbers configured here, depending on the configuration of tabs with objects. The columns and rows used by the respective user-defined unit overview are displayed in the Columns and Rows fields in order to get a quick overview of the types of display to be expected. Under the menu items Admin can add and Admin can edit, the authorizations for the Admin user role can be defined in stages.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('tab_order_preview', 'Tabs for units, objects and tickets are displayed here in the order preview. The tab name is supplemented by hash and star symbols depending on the respective configuration. The additions have the following meaning: *Show tickets only with one screen **Show tickets only with multiple screens #Show tab only with one screen ##Show tab only with multiple screens #*Show tab and tickets only with one screen ##*Show tab and tickets only with multiple screens. If the tabs for location and operations are configured as invisible, they are only hidden if the information they contain is visible under other tabs that are shown.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('not_editable', 'Editing blocked by configuration of super.');

