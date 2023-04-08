SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


INSERT INTO `settings` (`name`, `value`) VALUES
('auto_dispatch', '1, 1, 1'),
('auto_poll', '10'),
('callboard', '0, 80, 35, 80, 300'),
('closed_interval', '0, 1440'),
('date_format', 'm/d/Y H:i:s'),
('date_format_time_only', 'H:i:s'),
('date_format_time_only_clock', 'H:i'),
('date_format_date_only', 'm/d/Y'),
('date_format_date_only_clock', 'l, d. F Y'),
('date_format_year_only', 'Y'),
('framesize', '115'),
('heading_blink', '10'),
('hide_booked', '30'),
('page_caption', 'Your Organisation'),
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
('_vowel_mutation', ''),
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
('_api_prefix_phone_capt', 'Cellphone'),
('_api_prefix_printer_encdg', 'PRINTER'),
('_api_prefix_printer_capt', 'Printer'),
('_api_prefix_reporting_channel_1_encdg', 'TETRA'),
('_api_prefix_reporting_channel_1_capt', 'Trunked Radio'),
('_api_prefix_reporting_channel_1_regexp', '(([0-9]{7,8})|([0-9]{15})|([0-9]{3}\.[0-9]{4}\.[0-9]{7,8}))'),
('_api_prefix_reporting_channel_2_encdg', 'RMS'),
('_api_prefix_reporting_channel_2_capt', 'Radio messaging system'),
('_api_prefix_reporting_channel_2_regexp', '([0-9]|[a-fA-F]){8}'),
('_api_prefix_reporting_channel_3_encdg', 'PAGER'),
('_api_prefix_reporting_channel_3_capt', 'Multitone Pager'),
('_api_prefix_reporting_channel_3_regexp', '(([0-9]{5}[adespuwzADESPUWZ]?)|([0-9]{3}[adespuwzADESPUWZ]?))'),
('_api_prefix_reporting_channel_4_encdg', 'POCSAG'),
('_api_prefix_reporting_channel_4_capt', 'Digital Pager'),
('_api_prefix_reporting_channel_4_regexp', '[0-9]{7}[a-dA-D]'),
('_api_prefix_reporting_channel_5_encdg', ''),
('_api_prefix_reporting_channel_5_capt', ''),
('_api_prefix_reporting_channel_5_regexp', ''),
('_api_evaluate_unknown_unit_emergency_encdg', '1, 0, 5'),
('_api_emgcy_hi_encdg', 'EMERGENCY_CALL_HIGH'),
('_api_emgcy_hi_repl', '3'),
('_api_emgcy_hi_mess', '0'),
('_api_emgcy_hi_rece', 'Emergency call'),
('_api_emgcy_lo_encdg', 'EMERGENCY_CALL_LOW'),
('_api_emgcy_lo_repl', '3'),
('_api_emgcy_lo_mess', '6'),
('_api_emgcy_lo_rece', 'Prio. Callrequest'),
('_api_callreq_encdg', 'CALL_REQUEST'),
('_api_callreq_repl', '3'),
('_api_callreq_mess', '6'),
('_api_callreq_rece', 'Callrequest'),
('_api_manackn_encdg', 'MANUAL_ACKNOWLEDGE'),
('_api_manackn_repl', '0'),
('_api_manackn_mess', '0'),
('_api_manackn_rece', 'Receipt'),
('_api_disp_encdg', 'DISPATCHED'),
('_api_disp_repl', '2'),
('_api_disp_mess', '3'),
('_api_disp_rece', 'Dispatched'),
('_api_resp_encdg', 'RESPONDING'),
('_api_resp_repl', '2'),
('_api_resp_mess', '0'),
('_api_resp_rece', 'Responding'),
('_api_onsc_encdg', 'ON_SCENE'),
('_api_onsc_repl', '2'),
('_api_onsc_mess', '0'),
('_api_onsc_rece', 'On scene'),
('_api_fcen_encdg', 'FACILITY_ENROUTE'),
('_api_fcen_repl', '2'),
('_api_fcen_mess', '0'),
('_api_fcen_rece', 'Facility enroute'),
('_api_fcar_encdg', 'FACILITY_ARRIVED'),
('_api_fcar_repl', '2'),
('_api_fcar_mess', '0'),
('_api_fcar_rece', 'Facility arrived'),
('_api_clr_encdg', 'CLEAR'),
('_api_clr_stat', '1'),
('_api_clr_repl', '2'),
('_api_clr_mess', '7'),
('_api_clr_rece', 'Clear'),
('_api_quat_encdg', 'TO_QUATER'),
('_api_quat_stat', '2'),
('_api_quat_repl', '2'),
('_api_quat_mess', '0'),
('_api_quat_rece', 'To quater'),
('_api_off_duty_encdg', 'NO_SERVICE'),
('_api_off_duty_stat', '3'),
('_api_off_duty_repl', '2'),
('_api_off_duty_mess', '0'),
('_api_off_duty_rece', 'No service'),
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
('_api_default_subject_setng', 'Dispatch message - Your organisation, Message from mission control - Your organisation, Message from mission control - Your organisation, Message from mission control - Your organisation'),
('_api_dispatch_text_setng', 'N0,99; E0,99; G0,99; H0,99; O0,99; J0,250; T0,99; D0,99; K0,99; L0,99; O0,99; U0,99;'),
('_api_dispatch_shorttext_setng', 'N0,10; T0,25; B0,39; K0,35; D0,20; Z0,85;'),
('_api_email_smtp_host', 'mail.example.com'),
('_api_email_smtp_authentication', 'username, password'),
('_api_email_from', 'myadress@example.com, My name'),
('_api_email_cc', ''),
('_api_email_bcc', ''),
('_api_email_reply_to', '');

INSERT INTO `settings` (`name`, `value`) VALUES
('_audio_ticket', 'incident.ogg'),
('_alter_audio_ticket', 'incident.mp3'),
('_audio_dispatch', 'plop.ogg'),
('_alter_audio_dispatch', 'plop.mp3'),
('_audio_call_progression', 'status.ogg'),
('_alter_audio_call_progression', 'status.mp3'),
('_audio_status', 'status.ogg'),
('_alter_audio_status', 'status.mp3'),
('_audio_action', 'action.ogg'),
('_alter_audio_action', 'action.mp3'),
('_audio_default', 'ding.ogg'),
('_alter_audio_default', 'ding.mp3'),
('_audio_new_message', 'message.ogg'),
('_alter_audio_new_message', 'message.mp3'),
('_audio_call_request', 'call_request.ogg'),
('_alter_audio_call_request', 'call_request.mp3'),
('_audio_emergency_low', 'urgend_call_request.ogg'),
('_alter_audio_emergency_low', 'urgend_call_request.mp3'),
('_audio_emergency_high', 'emergency.ogg'),
('_alter_audio_emergency_high', 'emergency.mp3');

INSERT INTO `settings` (`name`, `value`) VALUES
('_def_lat', '0.999999'),
('_def_lng', '0.999999'),
('_def_zoom', '14'),
('_def_zoom_fixed', '0'),
('_lat_lng', '0'),
('_UTM', '1');

INSERT INTO `unit_status` (`id`, `status_name`, `description`, `dispatch`, `sort`, `bg_color`, `text_color`, `updated`, `client_address`, `user_id`) VALUES
(1, 'Clear', 'Unit available via radio', 0, 0, '#FFFFFF', '#000000', '2017-01-01 00:00:00', '127.0.0.1', 1),
(2, 'To Quarter', 'Unit at the guard house', 0, 10, '#FFFFFF', '#008000', '2017-01-01 00:00:00', '127.0.0.1', 1),
(3, 'No Service', 'Unit out of service', 2, 20, '#C0C0C0', '#FFFFFF', '2017-01-01 00:00:00', '127.0.0.1', 1),
(4, 'No Evaluation', 'Unit is not available - no status evaluation', 4, 30, '#808080', '#FFFFFF', '2017-01-01 00:00:00', '127.0.0.1', 1),
(5, 'Pause', 'Unit in Pause', 1, 1, '#C0C0C0', '#FFFFFF', '2017-01-01 00:00:00', '127.0.0.1', 1);

INSERT INTO `facility_status` (`id`, `status_name`, `description`, `sort`, `display`, `bg_color`, `text_color`, `user_id`, `client_address`, `updated`) VALUES
(1, 'On-scene location', 'Regular locations. E.g. Stage or toilet area at event', 3, 1, '#FFFFFF', '#000000', 1, '127.0.0.1', '2017-01-01 00:00:00'),
(2, 'Transportstart / -destination', 'E.g. Hospital, other establishment', 4, 12, '#C0C0C0', '#FFFFFF', 1, '127.0.0.1', '2017-01-01 00:00:00'),
(3, 'Connection headquater', 'E.g. Security service, organizer, fire brigade, etc.', 5, 34, '#F0F0F0', '#000000', 1, '127.0.0.1', '2017-01-01 00:00:00'),
(4, 'Not available', 'Object not available for current use', 6, 0, '#808080', '#FFFFFF', 1, '127.0.0.1', '2017-01-01 00:00:00');

INSERT INTO `incident_types` (`id`, `type`, `description`, `protocol`, `set_severity`, `group`, `sort`, `user_id`, `client_address`, `updated`) VALUES
(1, 'TEST', 'Test of dispatch system', '', 0, 'Miscellaneous', 0, 1, '127.0.0.1', '2017-01-01 00:00:00');

INSERT INTO `textblocks` (`id`, `type`, `group`, `text`, `code`, `report_channels`, `sort`, `user_id`, `client_address`, `updated`) VALUES
(1, 'fixtext', '', 'To all', 'FIXTEXT_01', 0, 1, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(2, 'fixtext', '', 'Security', 'FIXTEXT_02', 0, 2, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(3, 'fixtext', '', 'Report', 'FIXTEXT_03', 0, 3, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(4, 'fixtext', '', 'Phone', 'FIXTEXT_04', 0, 4, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(5, 'fixtext', '', 'To Quater', 'FIXTEXT_05', 0, 5, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(6, 'fixtext', '', 'Speech prompt', 'FIXTEXT_06', 0, 6, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(7, 'fixtext', '', 'Free', 'FIXTEXT_07', 0, 7, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(8, 'fixtext', '', 'Special rights', 'FIXTEXT_08', 0, 8, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(9, 'fixtext', '', 'Siren', 'FIXTEXT_09', 0, 9, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(10, 'fixtext', '', 'Dispatched', 'FIXTEXT_10', 0, 10, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(11, 'fixtext', '', 'positiv', 'FIXTEXT_11', 0, 11, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(12, 'fixtext', '', 'Location?', 'FIXTEXT_12', 0, 12, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(13, 'fixtext', '', 'negativ', 'FIXTEXT_13', 0, 13, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(14, 'fixtext', '', 'Check device', 'FIXTEXT_14', 0, 14, 1, '127.0.0.1', '2017-01-01 00:00:00'),
(15, 'fixtext', '', 'Situation report', 'FIXTEXT_15', 0, 15, 1, '127.0.0.1', '2017-01-01 00:00:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
