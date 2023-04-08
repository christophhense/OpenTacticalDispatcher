SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+01:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


INSERT INTO `settings` (`name`, `value`) VALUES
('auto_dispatch', '1, 1, 1'),
('auto_poll', '10'),
('callboard', '0, 80, 35, 80, 300'),
('closed_interval', '0, 1440'),
('date_format', 'd.m.Y H:i:s'),
('date_format_time_only', 'H:i:s'),
('date_format_time_only_clock', 'H:i'),
('date_format_date_only', 'd.m.Y'),
('date_format_date_only_clock', 'l, d. F Y'),
('date_format_year_only', 'Y'),
('framesize', '115'),
('heading_blink', '10'),
('hide_booked', '30'),
('page_caption', 'Ihre Organisation'),
('parking_form_data', '10, 90, 10, 90, 10, 90, 10, 90'),
('release_file', 'https://update.onetwoserve.net/OpenTacticalDispatcher-release-list.txt'),
('reported_by_phone', '0, 0, 1, 0, 1'),
('report_last', '720, 0, 0, 1'),
('report_log', '720, 1440, 1440, 0, 0, 1'),
('session_time_limit', '30, 5'),
('sort_units', '2'),
('sort_facilities', '1'),
('title_string', 'OpenTacticalDispatcher'),
('tolerance', '90'),
('night_color', '#C0C0C0');

INSERT INTO `settings` (`name`, `value`) VALUES
('_inc_num', 'YTo2OntpOjA7czoxOiIwIjtpOjE7czowOiIiO2k6MjtzOjA6IiI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czoyOiIxMyI7fQ==');

/*== Settings that are not exported ==*/
INSERT INTO `settings` (`name`, `value`) VALUES
('_update_progress_time', ''),
('_locale', ''),
('_version', ''),
('_vowel_mutation', 'äöüÄÖÜß'),
('_api_status', 'false;2017-01-01 00:00:00;2017-01-01 00:00:00;null;'),
('_api_phone_status', 'false;2017-01-01 00:00:00;2017-01-01 00:00:00;null;');
/*====================================*/

INSERT INTO `settings` (`name`, `value`) VALUES
('_api_user_id', '2'),
('_api_hosts', '127.0.0.1, ::1, localhost'),
('_api_destination_host', 'localhost:3142'),
('_api_phone_host', ''),
('_api_destination_password', '743894a0e4a801fc3'),
('_api_connection_test_configuration', '60, 300, CONNECTION_TEST, success, warning, error, phone, HTTP/1.1 200 OK'),
('_api_prefix_phone_encdg', 'PHONE'),
('_api_prefix_phone_capt', 'Mobiltelefon'),
('_api_prefix_printer_encdg', 'PRINTER'),
('_api_prefix_printer_capt', 'Alarmdrucker'),
('_api_prefix_reporting_channel_1_encdg', 'TETRA'),
('_api_prefix_reporting_channel_1_capt', 'Digitalfunk'),
('_api_prefix_reporting_channel_1_regexp', '(([0-9]{7,8})|([0-9]{15})|([0-9]{3}\.[0-9]{4}\.[0-9]{7,8}))'),
('_api_prefix_reporting_channel_2_encdg', 'FMS'),
('_api_prefix_reporting_channel_2_capt', 'Funkmeldesystem'),
('_api_prefix_reporting_channel_2_regexp', '([0-9]|[a-fA-F]){8}'),
('_api_prefix_reporting_channel_3_encdg', 'ZVEI'),
('_api_prefix_reporting_channel_3_capt', 'Funkmeldeempfänger'),
('_api_prefix_reporting_channel_3_regexp', '(([0-9]{5}[adespuwzADESPUWZ]?)|([0-9]{3}[adespuwzADESPUWZ]?))'),
('_api_prefix_reporting_channel_4_encdg', 'POCSAG'),
('_api_prefix_reporting_channel_4_capt', 'Digit. Meldempfänger'),
('_api_prefix_reporting_channel_4_regexp', '[0-9]{7}[a-dA-D]'),
('_api_prefix_reporting_channel_5_encdg', ''),
('_api_prefix_reporting_channel_5_capt', ''),
('_api_prefix_reporting_channel_5_regexp', ''),
('_api_evaluate_unknown_unit_emergency_encdg', '1, 0, 5'),
('_api_emgcy_hi_encdg', 'EMERGENCY_CALL_HIGH'),
('_api_emgcy_hi_repl', '3'),
('_api_emgcy_hi_mess', '0'),
('_api_emgcy_hi_rece', 'Notruf'),
('_api_emgcy_lo_encdg', 'EMERGENCY_CALL_LOW'),
('_api_emgcy_lo_repl', '3'),
('_api_emgcy_lo_mess', '6'),
('_api_emgcy_lo_rece', 'Prio. Sprechen'),
('_api_callreq_encdg', 'CALL_REQUEST'),
('_api_callreq_repl', '3'),
('_api_callreq_mess', '6'),
('_api_callreq_rece', 'Sprechwunsch'),
('_api_manackn_encdg', 'MANUAL_ACKNOWLEDGE'),
('_api_manackn_repl', '3'),				/*bei Quittung*/
('_api_manackn_mess', '0'),
('_api_manackn_rece', 'Quittung'),
('_api_disp_encdg', 'DISPATCHED'),
('_api_disp_repl', '2'),
('_api_disp_mess', '3'),
('_api_disp_rece', 'Disponiert'),
('_api_resp_encdg', 'RESPONDING'),
('_api_resp_repl', '2'),
('_api_resp_mess', '0'),
('_api_resp_rece', 'Einsatzübernahme'),
('_api_onsc_encdg', 'ON_SCENE'),
('_api_onsc_repl', '2'),
('_api_onsc_mess', '0'),
('_api_onsc_rece', 'Einsatzort'),
('_api_fcen_encdg', 'FACILITY_ENROUTE'),
('_api_fcen_repl', '2'),
('_api_fcen_mess', '0'),
('_api_fcen_rece', 'Einsatzgebunden'),
('_api_fcar_encdg', 'FACILITY_ARRIVED'),
('_api_fcar_repl', '2'),				/*3 bei Datenabfrage*/
('_api_fcar_mess', '0'),
('_api_fcar_rece', 'Bed. verfügbar'),	/*Datenabfrage*/
('_api_clr_encdg', 'CLEAR'),
('_api_clr_stat', '1'),
('_api_clr_repl', '2'),
('_api_clr_mess', '7'),
('_api_clr_rece', 'E-bereit Funk'),
('_api_quat_encdg', 'TO_QUATER'),
('_api_quat_stat', '2'),
('_api_quat_repl', '2'),
('_api_quat_mess', '0'),
('_api_quat_rece', 'E-bereit Wache'),
('_api_off_duty_encdg', 'NO_SERVICE'),
('_api_off_duty_stat', '3'),
('_api_off_duty_repl', '2'),
('_api_off_duty_mess', '0'),
('_api_off_duty_rece', 'Nicht E-bereit'),
('_api_ptt_prefix_encdg', 'OPTA'),
('_api_ptt_encdg', 'PTT'),
('_api_ptt_release_encdg', 'PTT_RELEASE'),
('_api_ptt_display_encdg', '20, 60'),
('_api_log_max_display_setng', '30'),
('_api_log_max_age_setng', '1440'),
('_api_login_logout_setng', 'LOGIN, LOGOUT'),
('_api_subscr_unsubscr_setng', 'SUBSCRIBE, UNSUBSCRIBE'),
('_api_current_radio_encdg', 'RADIO'),
('_api_private_call_encdg', 'PRIVATE_CALL'),
('_api_batch_start_stop_setng', 'BATCH_START, BATCH_STOP'),
('_api_position_encdg', 'POSITION'),
('_api_message_encdg', 'MESSAGE'),
('_api_log_encdg', 'LOG'),
('_api_errlog_encdg', 'ERROR'),
('_api_default_subject_setng', 'Alarmschreiben - Ihre Organisation, Nachricht von Einsatzleitung - Ihre Organisation, Nachricht von Einsatzleitung - Ihre Organisation, Nachricht von Einsatzleitung - Ihre Organisation'),
('_api_dispatch_text_setng', 'N0,99; E0,99; G0,99; H0,99; O0,99; J0,250; T0,99; D0,99; K0,99; L0,99; O0,99; U0,99;'),
('_api_dispatch_shorttext_setng', 'N0,10; T0,25; B0,39; K0,35; D0,20; Z0,85;'),
('_api_email_smtp_host', 'mail.example.com'),
('_api_email_smtp_authentication', 'Benutzername, Passwort'),
('_api_email_from', 'ihreadresse@example.com, Ihre Organisation'),
('_api_email_cc', ''),
('_api_email_bcc', ''),
('_api_email_reply_to', '');

INSERT INTO `settings` (`name`, `value`) VALUES
('_audio_ticket', 'einsatz.ogg'),
('_alter_audio_ticket', 'einsatz.mp3'),
('_audio_dispatch', 'plop.ogg'),
('_alter_audio_dispatch', 'plop.mp3'),
('_audio_call_progression', 'status.ogg'),
('_alter_audio_call_progression', 'status.mp3'),
('_audio_status', 'status.ogg'),
('_alter_audio_status', 'status.mp3'),
('_audio_action', 'rueckmeldung.ogg'),
('_alter_audio_action', 'rueckmeldung.mp3'),
('_audio_default', 'ding.ogg'),
('_alter_audio_default', 'ding.mp3'),
('_audio_new_message', 'nachricht.ogg'),
('_alter_audio_new_message', 'nachricht.mp3'),
('_audio_call_request', 'sprechwunsch.ogg'),
('_alter_audio_call_request', 'sprechwunsch.mp3'),
('_audio_emergency_low', 'hilferuf.ogg'),
('_alter_audio_emergency_low', 'hilferuf.mp3'),
('_audio_emergency_high', 'notruf.ogg'),
('_alter_audio_emergency_high', 'notruf.mp3');

INSERT INTO `settings` (`name`, `value`) VALUES
('_def_lat', '51.5'),
('_def_lng', '10.5'),
('_def_zoom', '14'),
('_def_zoom_fixed', '0'),
('_lat_lng', '0'),
('_UTM', '1');

INSERT INTO `unit_status` (`id`, `status_name`, `description`, `dispatch`, `sort`, `bg_color`, `text_color`, `updated`, `client_address`, `user_id`) VALUES
(1, 'Frei(1)', 'Verfügbar über Funk', 0, 0, '#FFFFFF', '#000000', '2017-01-01 00:00:00', '127.0.0.1', 1),
(2, 'Wache(2)', 'Einheit an der Wache', 0, 10, '#FFFFFF', '#008000', '2017-01-01 00:00:00', '127.0.0.1', 1),
(3, 'Ausser Dienst(6)', 'Einheit ausser Dienst', 2, 20, '#C0C0C0', '#FFFFFF', '2017-01-01 00:00:00', '127.0.0.1', 1),
(4, 'keine Auswertung', 'Einheit nicht verfügbar - keine Statusauswertung', 4, 30, '#808080', '#FFFFFF', '2017-01-01 00:00:00', '127.0.0.1', 1),
(5, 'Pause(1)', 'Einheit in Pause', 1, 1, '#C0C0C0', '#FFFFFF', '2017-01-01 00:00:00', '127.0.0.1', 1);

INSERT INTO `facility_status` (`id`, `status_name`, `description`, `sort`, `display`, `bg_color`, `text_color`, `user_id`, `client_address`, `updated`) VALUES
(1, 'Einsatzort', 'Regeläßige Einsatzorte. Z.B. Bühnen- oder Toilettenbereich', 3, 1, '#FFFFFF', '#000000', 1, '127.0.0.1', '2017-01-01 00:00:00'),
(2, 'Transportstart / -ziel', 'z.B. Krankenhaus, sonstige Einrichtung', 4, 12, '#C0C0C0', '#FFFFFF', 1, '127.0.0.1', '2017-01-01 00:00:00'),
(3, 'Verbindungsstelle', 'z.B. Sicherheitsdienst, Veranstalter, Feuerwehr, o.Ä.', 5, 34, '#F0F0F0', '#000000', 1, '127.0.0.1', '2017-01-01 00:00:00'),
(4, 'Nicht verfügbar', 'Objekt für aktuellen Einsatz nicht verfügbar', 6, 0, '#808080', '#FFFFFF', 1, '127.0.0.1', '2017-01-01 00:00:00');

INSERT INTO `incident_types` (`id`, `type`, `description`, `protocol`, `set_severity`, `group`, `sort`, `user_id`, `client_address`, `updated`) VALUES
(1, 'SON_TEST', 'Test der Funktionen des Einsatzleitsystems', '', 0, 'Sonstiges', 0, 1, '127.0.0.1', '2017-01-01 00:00:00');

INSERT INTO `textblocks` (`id`, `type`, `group`, `text`, `code`, `report_channels`, `sort`, `user_id`, `client_address`, `updated`) VALUES
(1, 'fixtext', '', 'An alle(A)', 'FIXTEXT_01', 249, 1, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(2, 'fixtext', '', 'Eigensicherung(E)', 'FIXTEXT_02', 249, 2, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(3, 'fixtext', '', 'Melden(C)', 'FIXTEXT_03', 249, 3, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(4, 'fixtext', '', 'Telefon(F)', 'FIXTEXT_04', 249, 4, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(5, 'fixtext', '', 'Wache anfahren(H)', 'FIXTEXT_05', 249, 5, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(6, 'fixtext', '', 'Sprechen!(J)', 'FIXTEXT_06', 249, 6, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(7, 'fixtext', '', 'Entlassen(L)', 'FIXTEXT_07', 249, 7, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(8, 'fixtext', '', 'Sonder- und Wegerechte zugelassen(P)', 'FIXTEXT_08', 249, 8, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(9, 'fixtext', '', 'Sirene(U)', 'FIXTEXT_09', 249, 9, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(10, 'fixtext', '', 'Abgestellt(c)', 'FIXTEXT_10', 249, 10, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(11, 'fixtext', '', 'EDV positiv(d)', 'FIXTEXT_11', 249, 11, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(12, 'fixtext', '', 'Standort?(h)', 'FIXTEXT_12', 249, 12, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(13, 'fixtext', '', 'EDV negativ(o)', 'FIXTEXT_13', 249, 13, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(14, 'fixtext', '', 'Status/Funkgerät überprüfen(u)', 'FIXTEXT_14', 249, 14, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(15, 'fixtext', '', 'Lagemeldung abgeben(L)', 'FIXTEXT_15', 249, 15, 1, '127.0.0.1', '2017-01-01 00:00:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
