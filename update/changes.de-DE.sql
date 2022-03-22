ALTER TABLE `captions` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';
ALTER TABLE `hints` CHANGE `client_address` `client_address` varchar(50) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip-address';
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
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Presentation', 'Darstellung');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Facilities presentation configuration', 'Objekt-Darstellung bearbeiten');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Units presentation configuration', 'Einheiten-Darstellung bearbeiten');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab order preview', 'Vorschau der Reiter-Reihenfolge');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Admin can add', 'Schichtführer kann zufügen');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab name', 'Reiter-Name');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Visible', 'Sichtbar');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Add tickets', 'Einsätze zeigen');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Columns', 'Spalten');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Rows', 'Zeilen');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Admin can config', 'Schichtführer kann bearbeiten');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Facility tab', 'Objekt-Reiter');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Unit tab', 'Einheiten-Reiter');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Singlemonitor only', 'Ein Bildschirm');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Multimonitor only', 'Mehrere Bildschirme');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab show/hide', 'Reiter ein-/ausblenden');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Tab edit/delete', 'Reiter editieren/löschen');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Set to admin can add facility tabs', 'Schichtführer kann Objekt-Reiters zufügen gespeichert');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Set to admin can not add facility tabs', 'Schichtführer kann Objekt-Reiters nicht zufügen gespeichert');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('New facility tab added', 'Neuer Objekt-Reiter zugefügt');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Facilty tabs updated', 'Objekt-Reiter geändert');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Facility tabs deleted', 'Objekt-Reiter gelöscht');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Set to admin can add unit tabs', 'Schichtführer kann Einheiten-Reiters zufügen gespeichert');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Set to admin can not add unit tabs', 'Schichtführer kann Einheiten-Reiters nicht zufügen gespeichert');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('New unit tab added', 'Neuer Einheiten-Reiter zugefügt');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Unit tabs updated', 'Einheiten-Reiter geändert');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Unit tabs deleted', 'Einheiten-Reiter gelöscht');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Changed Tab name', 'Geänderter Reiter-Name');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Deleted Tab name', 'Gelöschter Reiter-Name');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Edit custom units representation', 'Benutzerdefinierte Einheiten-Darstellung bearbeiten');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Edit custom facilities representation', 'Benutzerdefinierte Objekt-Darstellung bearbeiten');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Row', 'Zeile');
INSERT INTO `captions` (`capt`, `repl`) VALUES ('Column', 'Spalte');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('facility_presentation', 'Hier ist die Darstellung und Reihenfolge der Reiter für Objekte in der Lageübersicht definiert. Reiter mit benutzerdefinierter Objekt-Übersicht können hier erstellt werden. Reiter für Einheiten, Objekte und Einsätze werden mit der eingestellten Reihenfolge und Sichtbarkeit in einer Vorschau dargestellt.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('facility_presentation_tab_list', 'Hier können benutzerdefinierte Objekt-Übersichten für die Lageübersicht erstellt und verwaltet werden. Die Freigabe von einzelnen Einstellungen für die Benutzerrolle Schichtführer kann ebenfalls hier durchgeführt werden. Um eine benutzerdefinierte Objekt-Übersicht neu anzulegen muss deren Reiter-Name mindestens 4 Zeichen haben. Als Zeichen können nur Groß- und Kleinbuchstaben, Ziffern, Punkt, Minus-Zeichen, Unterstrich und Leerzeichen verwendet werden. Um einen Reiter-Name ändern zu können, muss diese Regel ebenfalls eingehalten werden. Unter dem Menuepunkt Sichtbar kann die Sichtbarkeit eines Reiters definiert werden. Dies kann z.B. genutzt werden um eine vorbereitete benutzerdefinierte Objekt-Übersicht zu verbergen nur in einem bestimmten Kontext anzubieten. Die Sichtbarkeit kann in Abhängigkeit von der Anzahl der genutzten Bildschirme konfiguriert werden. Die automatische Erkennung der Anzahl der genutzten Bildschirme und der daraus folgenden Anpassung der Lageübersicht kann unter Umständen einige Sekunden dauern. Anhand der Zahl im Feld Sortieren nach wird die Reihenfolge der Reiter in der Lageübersicht definiert. Die Anzeige erfolgt von Links nach Rechts in aufsteigender Reihenfolge anhand der hier konfigurierten Zahlen, in Abhängigkeit von der Konfiguration von Reitern mit Einheiten und Einsätzen. In den Feldern Spalten und Zeilen werden die von der jeweiligen benutzerdefinierte Objekt-Übersicht genutzten Spalten und Zeilen angezeigt, um eine schnelle Übersicht über die zu erwartenden Darstellungsformen zu bekommen. Unter den Menuepunkten Schichtführer kann zufügen	und Schichtführer kann bearbeiten können die Berechtigungen für die Benutzerrolle Schichtführer stufenweise definiert werden.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('unit_presentation', 'Hier ist die Darstellung und Reihenfolge der Reiter für Einheiten und Einsätze in der Lageübersicht definiert. Zusätzlich zum Reiter Lage können weitere Reiter mit benutzerdefinierter Einheiten-Übersicht erstellt werden. Reiter für Einheiten, Objekte und Einsätze werden mit der eingestellten Reihenfolge und Sichtbarkeit in einer Vorschau dargestellt.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('unit_presentation_tab_list', 'Hier können benutzerdefinierte Einheiten-Übersichten für die Lageübersicht erstellt und verwaltet werden. Die Freigabe von einzelnen Einstellungen für die Benutzerrolle Schichtführer kann ebenfalls hier durchgeführt werden. Um eine benutzerdefinierte Einheiten-Übersicht neu anzulegen muss deren Reiter-Name mindestens 4 Zeichen haben. Als Zeichen können nur Groß- und Kleinbuchstaben, Ziffern, Punkt, Minus-Zeichen, Unterstrich und Leerzeichen verwendet werden. Um einen Reiter-Name ändern zu können, muss diese Regel ebenfalls eingehalten werden. Unter dem Menuepunkt Sichtbar kann die Sichtbarkeit eines Reiters definiert werden. Dies kann z.B. genutzt werden um eine vorbereitete benutzerdefinierte Einheiten-Übersicht zu verbergen nur in einem bestimmten Kontext anzubieten. Wenn die Reiter für Lage und Einsätze als nicht sichtbar konfiguriert sind, werden sie nur dann ausgeblendet wenn die enthaltenen Informationen unter anderen eingeblendeten Reitern sichtbar sind. Mit dem Menuepunkt Einsätze zeigen können unter Reitern mit Einheiten zusätzlich die Einsätze angezeigt werden. Für das Ausblenden der Einsätze im Reiter Lage gilt die vorgenannte Regel entsprechend. Die Sichtbarkeit der benutzerdefinierte Einheiten-Übersicht sowie darin zusätzlich angezeigten Einsätze kann in Abhängigkeit von der Anzahl der genutzten Bildschirme konfiguriert werden. Die automatische Erkennung der Anzahl der genutzten Bildschirme und der daraus folgenden Anpassung der Lageübersicht kann unter Umständen einige Sekunden dauern. Anhand der Zahl im Feld Sortieren nach wird die Reihenfolge der Reiter in der Lageübersicht definiert. Die Anzeige erfolgt von Links nach Rechts in aufsteigender Reihenfolge anhand der hier konfigurierten Zahlen, in Abhängigkeit von der Konfiguration von Reitern mit Objekten. In den Feldern Spalten und Zeilen werden die von der jeweiligen benutzerdefinierte Einheiten-Übersicht genutzten Spalten und Zeilen angezeigt, um eine schnelle Übersicht über die zu erwartenden Darstellungsformen zu bekommen. Unter den Menuepunkten Schichtführer kann zufügen	und Schichtführer kann bearbeiten können die Berechtigungen für die Benutzerrolle Schichtführer stufenweise definiert werden.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('tab_order_preview', 'Reiter für Einheiten, Objekte und Einsätze werden hier in der eingestellten Reihenfolge angezeigt. Der Reiter-Name wird in Abhängigkeit von der jeweiligen Konfiguration um Raute- und Stern-Symbole ergänzt. Die Ergänzungen besitzen folgende Bedeutung: *zusätzlich Einsätze anzeigen **zusätzlich Einsätze nur bei mehreren Bildschirmen anzeigen #Einblenden nur bei einem Bildschirm ##Einblenden nur bei mehreren Bildschirmen #*Einblenden nur bei einem Bildschirm mit zusätzlicher Anzeige der Einsätze ##*Einblenden nur bei mehreren Bildschirmen mit zusätzlicher Anzeige der Einsätze. Wenn die Reiter für Lage und Einsätze als nicht sichtbar konfiguriert sind, werden sie nur dann ausgeblendet wenn die enthaltenen Informationen unter anderen eingeblendeten Reitern sichtbar sind.');
INSERT INTO `hints` (`tag`, `hint`) VALUES ('not_editable', 'Bearbeiten durch Konfiguration des Administrators gesperrt.');