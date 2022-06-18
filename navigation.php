<?php
error_reporting(E_ALL);
ini_set('session.cookie_samesite', 'Strict');
@session_start();
require_once ("./incs/functions.inc.php");

if (ini_get("display_errors") == true) {
	$auto_poll_time = 50;
	$display_str = "inline";
} else {
	$auto_poll_settings = explode(",", get_variable("auto_poll"));
	$auto_poll_time = trim($auto_poll_settings[0]);
	$display_str = "none";
}
$ptt_display_settings = explode(",", get_variable("_api_ptt_display_encdg"));
$ptt_display_time = trim($ptt_display_settings[0]) * 100;
$ptt_fade_out_time = trim($ptt_display_settings[1]) * 100;
$session_time_limit_settings = explode(",", get_variable("session_time_limit"));
$session_logout_warning = trim($session_time_limit_settings[1]);
$callboard_settings = explode(",", get_variable("callboard"));
$callboard_enabled = trim($callboard_settings[0]);
$moment_time_only_format = php_to_moment(get_variable("date_format_time_only_clock"));
$moment_date_only_format = php_to_moment(get_variable("date_format_date_only_clock"));
$moment_date_format = php_to_moment(get_variable("date_format"));
$connection_test_array = get_connection_test_configuration();
switch ($callboard_enabled) {
case 0:
	$display_callboard_str = "display: none;";
	break;
case 1:
	$display_callboard_str = "display: inline-block;";
	break;
default:
	$display_callboard_str = "display: none;";
}
$audio_sources_str = "";
$sound_names_array = get_sound_array();
foreach ($sound_names_array as $value) {
	$soundfile = get_variable("_" . $value);
	$alter_soundfile = get_variable("_alter_" . $value);
	if ((!empty ($soundfile)) || (!empty ($alter_soundfile))) {
		$audio_sources_str .= "<audio id='" . $value . "' preload>\n";
		if (!empty ($soundfile)) {
			$audio_sources_str .= "<source src='./sounds/" . get_variable("_" . $value) . "'>\n";
		}
		if (!empty ($alter_soundfile)) {
			$audio_sources_str .= "<source src='./sounds/" . get_variable("_alter_" . $value) . "'>\n";
		}
		$audio_sources_str .= "</audio>\n";
	}
}
	?>
<!doctype html>
<html lang="<?php print get_variable("_locale");?>">
	<head>
		<title><?php print get_variable("page_caption");?></title>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html;">
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Content-Script-Type" content="text/javascript">
		<link href="./css/bootstrap.min.css" rel="stylesheet">
		<link href="./css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="./css/stylesheet.css" rel="stylesheet">
		<script src="./js/jquery-2.1.4.min.js" type="text/javascript"></script>
		<script src="./js/moment-with-locales.js" type="text/javascript"></script>
		<script src="./js/functions.js" type="text/javascript"></script>
		<?php print show_day_night_style();?>
		<script>
			var last_infos_array = [];
			var current_button_id = "situation";
			var NOT_STR = "<?php echo get_text(" *not*");?>";
			var is_initialized = false;
			var client_poll_cycle = null;
			var highlighted_buttons = new Array();
			var session_logout_warning_period = <?php print $session_logout_warning;?> + 0;
			var session_logout_warning_reported = false;
			var ptt_display_time = <?php print $ptt_display_time;?> + 0;
			var ptt_fade_out_time = <?php print $ptt_fade_out_time;?> + 0;
			var api_connection_test_retry_time = <?php print $connection_test_array['retry_time'] * 1000;?> + 0;
			var api_connection_test_keepalive_time = <?php print $connection_test_array['keepalive_time'] * 1000;?> + 0;
			var retry = false;
			var keepalive = false;
			var primary_screen = false;
			var log_report_form_data = "";
			var log_report_timestamp = 0;
			var action_form_data = [];
			var action_timestamp = [];
			var ticket_add_form_data = "";
			var ticket_add_timestamp = 0;
			var ticket_add_ticket_id = 0;
			var ticket_close_form_data = [];
			var ticket_close_timestamp = [];
			moment.locale("<?php print get_variable("_locale");?>");

			function do_periodic_connection_test() {
				if (primary_screen) {
					setTimeout(function () {
						do_api_connection_test(true, "");
					}, Math.floor(Math.random() * 9999));
				}
			}

			function get_reload_flags(input_array) {
				var reload_tickets_flag = false;
				var reload_communication_flag = false;
				var reload_units_flag = false;
				var reload_facilities_flag = false;
				var reload_log_flag = false;
				try {
					if (
						((
							(last_infos_array['ticket']['id_max'] != input_array['ticket']['id_max']) ||
							(last_infos_array['ticket']['id_changed'] != input_array['ticket']['id_changed']) ||
							(last_infos_array['ticket']['update'] != input_array['ticket']['update']) ||
							(last_infos_array['ticket']['scheduled'] != input_array['ticket']['scheduled']) ||
							(last_infos_array['action']['update'] != input_array['action']['update'])
						)) || (
							(last_infos_array['assign']['quantity'] != input_array['assign']['quantity'])
						)
					) {
						reload_tickets_flag = true;
					}
					if (
						(last_infos_array['requests']['silent'] != input_array['requests']['silent']) ||
						(last_infos_array['requests']['message'] != input_array['requests']['message']) ||
						(last_infos_array['requests']['warn_text'] != input_array['requests']['warn_text']) ||
						(last_infos_array['requests']['auto_ticket'] != input_array['requests']['auto_ticket']) ||
						(last_infos_array['requests']['normal'] != input_array['requests']['normal']) ||
						(last_infos_array['requests']['emergency_low'] != input_array['requests']['emergency_low']) ||
						(last_infos_array['requests']['emergency_high'] != input_array['requests']['emergency_high'])
					) {
						reload_communication_flag = true;
					}
					if (
						(last_infos_array['units_status']['id'] != input_array['units_status']['id']) ||
						((last_infos_array['units_status']['update'] != input_array['units_status']['update']) &&
						(last_infos_array['units_status']['id'] == input_array['units_status']['id'])) ||
	
						(last_infos_array['call_progression']['id'] != input_array['call_progression']['id']) ||
						((last_infos_array['call_progression']['update'] != input_array['call_progression']['update']) &&
						(last_infos_array['call_progression']['id'] == input_array['call_progression']['id'])) ||
	
						(last_infos_array['assign']['id_max'] != input_array['assign']['id_max']) ||
						(last_infos_array['assign']['quantity'] != input_array['assign']['quantity'])
					) {
						reload_units_flag = true;
					}
					if (
						(last_infos_array['facilities_status']['id'] != input_array['facilities_status']['id']) ||
						(last_infos_array['facilities_status']['update'] != input_array['facilities_status']['update'])
					) {
						reload_facilities_flag = true;
					}
					if (last_infos_array['log']['id'] != input_array['log']['id']) {
						reload_log_flag = true;
					}
				} catch (e) {
				}
				return {"tickets":reload_tickets_flag, 
					"communication":reload_communication_flag, 
					"units":reload_units_flag, 
					"facilities":reload_facilities_flag, 
					"log":reload_log_flag};
			}

			function refresh_latest_infos(data) {
				try {
					last_infos_array = JSON.parse(data);
					var get_infos_array = JSON.parse(data);

					$("#div_first_screen").html(get_infos_array['screen']['first_screen'].trim());

					$("#div_user_name").html(get_infos_array['user']['name'].trim());
					$("#div_user_level").html(get_infos_array['user']['level'].trim());

					$("#div_ticket_latest_id").html(get_infos_array['ticket']['id_max'].trim());
					$("#div_ticket_changed_id").html(get_infos_array['ticket']['id_changed']);
					$("#div_scheduled").html(get_infos_array['ticket']['scheduled']);
					$("#div_ticket_updated").html(get_infos_array['ticket']['update'].trim());
					$("#div_ticket_user").html(get_infos_array['ticket']['user'].trim());
					
					$("#div_unit_id").html(parseInt(get_infos_array['units_status']['id'].trim()));
					$("#div_unit_updated").html(get_infos_array['units_status']['update'].trim());
					$("#div_unit_user").html(parseInt(get_infos_array['units_status']['user'].trim()));

					$("#div_unit_callprogress_id").html(parseInt(get_infos_array['call_progression']['id'].trim()));
					$("#div_unit_callprogress_updated").html(get_infos_array['call_progression']['update'].trim());
					$("#div_unit_callprogress_user").html(parseInt(get_infos_array['call_progression']['user'].trim()));

					$("#div_assign_max_id").html(get_infos_array['assign']['id_max'].trim());
					$("#div_assign_quantity").html(get_infos_array['assign']['quantity']);
					$("#div_assign_updated").html(get_infos_array['assign']['update'].trim());
					$("#div_assign_user").html(get_infos_array['assign']['user'].trim());

					$("#div_action_max_id").html(get_infos_array['action']['id_max'].trim());
					$("#div_action_changed_id").html(get_infos_array['action']['id_changed']);
					$("#div_action_updated").html(get_infos_array['action']['update'].trim());
					$("#div_action_user").html(get_infos_array['action']['user'].trim());

					$("#div_facility_id").html(parseInt(get_infos_array['facilities_status']['id'].trim()));
					$("#div_facility_updated").html(get_infos_array['facilities_status']['update'].trim());
					$("#div_facility_user").html(parseInt(get_infos_array['facilities_status']['user'].trim()));

					$("#div_silent_requests").html(get_infos_array['requests']['silent']);
					$("#div_message").html(get_infos_array['requests']['message']);
					$("#div_warn_text").html(get_infos_array['requests']['warn_text']);
					$("#div_auto_ticket").html(get_infos_array['requests']['auto_ticket']);
					$("#div_requests").html(get_infos_array['requests']['normal']);
					$("#div_emergency_requests_low").html(get_infos_array['requests']['emergency_low']);
					$("#div_emergency_requests_high").html(get_infos_array['requests']['emergency_high']);

					$("#div_day_night").html(get_infos_array['screen']['day_night']);

					$("#div_facility_id").html(get_infos_array['facilities_status']['id']);
					$("#div_facility_updated").html(get_infos_array['facilities_status']['update'])
					$("#div_facility_user").html(get_infos_array['facilities_status']['user']);

					$("#div_log").html(get_infos_array['log']['id']);

					var current_messages = get_infos_array['requests']['message'] + get_infos_array['requests']['warn_text'] + get_infos_array['requests']['auto_ticket'] +
						get_infos_array['requests']['normal'] + get_infos_array['requests']['emergency_low'] + get_infos_array['requests']['emergency_high'];
					if (current_messages <= 99) {
						$("#count_messages").html(current_messages);
					} else {
						$("#count_messages").html("99");
					}
				} catch (e) {
				}
			}

			function watch_latest_infos(data) {
				var get_infos_array = JSON.parse(data);
				get_infos_array['screen']['night_color'] = "<?php print get_variable("night_color");?>";
				get_infos_array.reload_flags = get_reload_flags(get_infos_array);
				get_infos_array.parked_form_data = {"ticket_add_form_data":ticket_add_form_data, 
					"ticket_add_timestamp":ticket_add_timestamp, 
					"ticket_add_ticket_id":ticket_add_ticket_id, 
					"ticket_close_form_data":ticket_close_form_data, 
					"ticket_close_timestamp":ticket_close_timestamp, 
					"action_form_data":action_form_data, 
					"action_timestamp":action_timestamp, 
					"log_report_form_data":log_report_form_data, 
					"log_report_timestamp":log_report_timestamp};
					data_additional = JSON.stringify(get_infos_array);
				window.parent.main.postMessage(data_additional, window.location.origin);
				try {
					window.parent.callboard.postMessage(data_additional, window.location.origin);
				} catch(e) {
				}
				var first_screen = get_infos_array['screen']['first_screen'];
				if (get_infos_array['user']['id'] != 0) {
					var get_infos_array = JSON.parse(data);
					if (first_screen.valueOf() == "on") {
						primary_screen = true;
					} else {
						primary_screen = false;
					}
					if (get_infos_array['screen']['date_time'].valueOf() != "") {
						$("#date_time_of_day").html(
							moment(get_infos_array['screen']['date_time'], "YYYY-MM-DD HH:mm:ss").
							format("<?php print $moment_date_only_format . " " . $moment_time_only_format;?>") +
							" <?php print get_text("o'clock");?>"
						);
						$("#div_server_time").html(get_infos_array['screen']['date_time']);
						$("#div_server_time_formatted").html(moment(get_infos_array['screen']['date_time'], "YYYY-MM-DD HH:mm:ss").format("<?php print $moment_date_format;?>"));
					}
					if (
						(($("#div_api_host_available").html() != get_infos_array['api']['api_host_available']) ||
						($("#div_api_phone_host_available").html() != get_infos_array['api']['api_phone_host_available']) ||
						((retry == false) && (keepalive == false))) &&
						((get_infos_array['api']['api_host_available'] != "null") ||
						(get_infos_array['api']['api_phone_host_available'] != "null"))
					) {
						if (retry != false) {
							clearInterval(retry);
							retry = false;
							keepalive = window.setInterval("do_periodic_connection_test();", api_connection_test_keepalive_time);
						} else {
							clearInterval(keepalive);
							keepalive = false;
							retry = window.setInterval("do_periodic_connection_test();", api_connection_test_retry_time);
						}
					}
					$("#div_api_current_radio").html(get_infos_array['api']['current_radio']);
					$("#div_api_host_available").html(get_infos_array['api']['api_host_available']);
					$("#div_api_host_code").html(get_infos_array['api']['api_host_code']);
					$("#div_api_host_text").html(get_infos_array['api']['api_host_text']);
					$("#div_api_host_timestamp_current_state").html(get_infos_array['api']['api_host_timestamp_current_state']);
					$("#div_api_phone_host_available").html(get_infos_array['api']['api_phone_host_available']);
					$("#div_api_phone_host_code").html(get_infos_array['api']['api_phone_host_code']);
					$("#div_api_phone_host_text").html(get_infos_array['api']['api_phone_host_text']);
					$("#div_api_phone_host_timestamp_current_state").html(get_infos_array['api']['api_phone_host_timestamp_current_state']);
					if (session_logout_warning_period != 0) {
						if (get_infos_array['user']['timeout'].valueOf() == "on") {
							change_class("timeout_button", "btn btn-xs btn-default");
							var caption_minute = " <?php print get_text("mins");?>";
							if (get_infos_array['user']['expires'] == 1) {
								caption_minute = " <?php print get_text("min");?>";
							}
							$("#timeout_info").html("<?php print get_text("Auto-logout in");?> " + get_infos_array['user']['expires'] + caption_minute);
							if ((get_infos_array['user']['expires'].valueOf() <= session_logout_warning_period) && (session_logout_warning_reported == false)) {
								change_class("timeout_info", "alert alert-warning alert-warning-flat");
								do_audio("audio_default");
								session_logout_warning_reported = true;
							}
							if ((get_infos_array['user']['expires'].valueOf() > session_logout_warning_period) && (session_logout_warning_reported == true)) {
								change_class("timeout_info", "");
								session_logout_warning_reported = false;
							}
						} else {
							$("#timeout_info").html("<?php print get_text("Auto-logout disabled");?>");
							change_class("timeout_info", "");
							change_class("timeout_button", "btn btn-xs btn-primary");
							session_logout_warning_reported = false;
						}
					} else {
						$("#timeout_button").css("display", "none");
						$("#timeout_info").css("display", "none");
					}
					if (typeof (get_infos_array['screen']['reset_button']) != "undefined") {
						if (!(current_button_id == get_infos_array['screen']['reset_button'].valueOf())) {
							highlight_button(get_infos_array['screen']['reset_button'], true);
						}
					}
					if ((parseInt(get_infos_array['user']['id'])) != $("#div_user_id").html()) {
						do_login(get_infos_array['user']['name'], get_infos_array['user']['level']);
						if (get_infos_array['screen']['day_night'] == "day") {
							do_day_night("day");
						} else {
							do_day_night("night");
						}
					}
					if ($("#div_day_night").html() == "") {
						$("#div_day_night").html(0);
					}
					if (typeof (get_infos_array['screen']['communication']) != "undefined") {
						var appearance = "default";
						if (typeof (get_infos_array['screen']['appearance']) != "undefined") {
							appearance = get_infos_array['screen']['appearance'];
						}
						show_communication_message(get_infos_array['screen']['communication'], appearance);
					}
					if (parseInt(get_infos_array['ticket']['id_max']) > $("#div_ticket_latest_id").html()) {
						if ((parseInt(get_infos_array['ticket']['user']) != $("#div_user_id").html()) && ($("#div_user_id").html() != "") && (parseInt(get_infos_array['ticket']['id_max']) != 0)) {
							ticket_signal();
							do_audio("audio_ticket");
						}
					}
					if ($("#div_scheduled").html() == "") {
						$("#div_scheduled").html(0);
					}
					if (get_infos_array['ticket']['scheduled'] > $("#div_scheduled").html()) {
							ticket_signal();
							do_audio("audio_ticket");
					}
					if ((parseInt(get_infos_array['units_status']['id']) != $("#div_unit_id").html()) || (get_infos_array['units_status']['update'].trim() != $("#div_unit_updated").html())) {
						if ((parseInt(get_infos_array['units_status']['user']) != $("#div_user_id").html()) && ($("#div_user_id").html() != "") && (parseInt(get_infos_array['units_status']['id']) != 0) && (get_infos_array['units_status']['update'].trim() != $("#div_unit_updated").html() != 0)) {
							do_audio("audio_status");
							unit_signal();
						}
					}
					if ((parseInt(get_infos_array['call_progression']['id']) != $("#div_unit_callprogress_id").html()) || (get_infos_array['call_progression']['update'].trim() != $("#div_unit_callprogress_updated").html())) {
						if ((parseInt(get_infos_array['call_progression']['user']) != $("#div_user_id").html()) &&
							($("#div_user_id").html() != "") &&
							($("#div_unit_callprogress_id").html() != 0) &&
							(get_infos_array['call_progression']['update'].trim() != $("#div_unit_callprogress_updated").html()) &&
							(get_infos_array['call_progression']['progession_changed'].trim() == "true")
						) {
							do_audio("audio_status");
							unit_signal();
						}
					}
					if (get_infos_array['assign']['quantity'].trim() > $("#div_assign_quantity").html()) {
						if ((parseInt(get_infos_array['assign']['user']) != $("#div_user_id").html()) && ($("#div_assign_quantity").html() != "")) {
							do_audio("audio_dispatch");
							unit_signal();
						}
					}
					if (get_infos_array['action']['update'].trim() != $("#div_action_updated").html())  {
						if ((parseInt(get_infos_array['action']['user']) != $("#div_user_id").html()) && ($("#div_user_id").html() != "")) {
							do_audio("audio_action");
							miscellaneous_signal();
						}
					}
					if (((get_infos_array['requests']['message'] + get_infos_array['requests']['warn_text'] + get_infos_array['requests']['auto_ticket'] +
						get_infos_array['requests']['normal'] + get_infos_array['requests']['emergency_low'] + get_infos_array['requests']['emergency_high']) !=
						(parseInt($("#div_message").html()) + parseInt($("#div_warn_text").html()) + parseInt($("#div_auto_ticket").html()) +
						parseInt($("#div_requests").html()) + parseInt($("#div_emergency_requests_low").html()) + parseInt($("#div_emergency_requests_high").html()))) &&
						(get_infos_array['user']['level'] <= 2)) {
						if (get_infos_array['requests']['emergency_high'] > $("#div_emergency_requests_high").html()) {
							do_audio("audio_emergency_high");
						} else {
							if (get_infos_array['requests']['emergency_low'] > $("#div_emergency_requests_low").html()) {
								do_audio("audio_emergency_low");
							} else {
								if (get_infos_array['requests']['auto_ticket'] > $("#div_auto_ticket").html()) {
									do_audio("audio_new_message");
								} else {
									if (get_infos_array['requests']['normal'] > $("#div_requests").html()) {
										do_audio("audio_call_request");	
									} else {
										if (get_infos_array['requests']['message'] > $("#div_message").html()) {
											do_audio("audio_new_message");
										}
									}
								}
							}
						}
						if (get_infos_array['requests']['emergency_high'] != 0) {
							calls_signal_danger(get_infos_array['requests']['message']);
						} else {
							if (get_infos_array['requests']['emergency_low'] != 0) {
								calls_signal_warning();
							} else {
								if (get_infos_array['requests']['auto_ticket'] != 0) {
									calls_signal_danger();
								} else {
									if (get_infos_array['requests']['normal'] != 0) {
										 calls_signal_info();
									} else {
										if (get_infos_array['requests']['message'] != 0) {
											calls_signal_info();
										}
									}
								}
							}
						}
						if ((get_infos_array['requests']['normal'] + get_infos_array['requests']['emergency_low'] + get_infos_array['requests']['emergency_high'] +
							get_infos_array['requests']['auto_ticket'] + get_infos_array['requests']['warn_text'] + get_infos_array['requests']['message']) == 0) {	
							highlight_button("communication", true);
						}
						$("#div_message").html(get_infos_array['requests']['message']);
						$("#div_warn_text").html(get_infos_array['requests']['warn_text']);
						$("#div_auto_ticket").html(get_infos_array['requests']['auto_ticket']);
						$("#div_requests").html(get_infos_array['requests']['normal']);
						$("#div_emergency_requests_low").html(get_infos_array['requests']['emergency_low']);
						$("#div_emergency_requests_high").html(get_infos_array['requests']['emergency_high']);
					}
					if (get_infos_array['screen']['day_night'].valueOf() != $("#div_day_night").html()) {
						if (get_infos_array['screen']['day_night'] == "day") {
							do_day_night("day");
						} else {
							do_day_night("night");
						}
					}
					$("#div_user_id").html(get_infos_array['user']['id']); //muss bleiben!!!
					refresh_latest_infos(data);
				} else {
					if ((parseInt(get_infos_array['user']['id'])) != $("#div_user_id").html()) {
						$("#div_user_id").html(get_infos_array['user']['id']); //muss bleiben!!!
						refresh_latest_infos(data);
						do_logout();
					}
				}
			}

			function do_loop_get_latest_infos() {
				var randomnumber = Math.floor(Math.random() * 99999999);
				send_request("get_data.php?request=" + randomnumber + "&screen_id=" + $("#div_screen_id").html(), watch_latest_infos);
			}

			function navigation_init() {
				$("#logged_in").html("<?php print (array_key_exists('user', $_SESSION))? $_SESSION['user_name'] : get_text(" *not*");?>");
				$("#level").html("<?php print (array_key_exists('level', $_SESSION))? get_level_text($_SESSION['level']) : get_text(" *na*");?>");
				try {
					if (is_initialized) {
						return;
					}
					var randomnumber = Math.floor(Math.random() * 99999999);
					is_initialized = true;
					$("#div_screen_id").html(Math.floor(Math.random() * 99999999));
					send_request("get_data.php?version=" + randomnumber + "&screen_id=" + $("#div_screen_id").html(), refresh_latest_infos);
					if (client_poll_cycle == null) {
						client_poll_cycle = window.setInterval("do_loop_get_latest_infos();", <?php print $auto_poll_time * 100;?>);
					}
				} catch (e) {
				}
			}

			function stop_polling() {
				clearInterval(client_poll_cycle);
				client_poll_cycle = null;
			}

//========= light Buttons

			function change_class(object_id, new_class) {
				if (new_class.valueOf() == "") {
					$("#" + object_id).removeClass();
				} else {
					$("#" + object_id).removeClass().addClass(new_class);
				}
				return true;
			}

			function highlight_button(button_id, only_off) {
				if ((typeof (only_off) == "undefined") || ((typeof (only_off) != "undefined") && (only_off.valueOf == false))) {
					change_class(button_id, "btn btn-xs btn-primary");
					if (!(current_button_id == button_id)) {
						change_class(current_button_id, "btn btn-xs btn-default");
					}
					current_button_id = button_id;
				} else {
					change_class(button_id, "btn btn-xs btn-default");
				}
				highlighted_buttons[button_id] = 0;
				switch (button_id.valueOf()) {
				case "situation":
					send_request("set_data.php?function=screen&reset_button=" + button_id.valueOf() + "&screen_id=" + $("#div_screen_id").html(), no_callback);
					break;
				default:
				}
			}

			function no_callback() {}

			function ticket_signal() {				// red light the button
				change_class("situation", "btn btn-xs btn-danger");
				highlighted_buttons["situation"] = 3;
			}

			function unit_signal() {				// light the units button and - if not already highlighted_buttons red - the situation button
				if (highlighted_buttons["situation"] > 2) {
					return;
				}									// already highlighted_buttons - possibly red
				change_class("situation", "btn btn-xs btn-info");
				highlighted_buttons["situation"] = 2;
			}

			function miscellaneous_signal() {		// blue light to situation button if not already highlighted_buttons
				if (highlighted_buttons["situation"] > 0) {
					return;
				}									// already highlighted_buttons - possibly red
				change_class("situation", "btn btn-xs btn-info");
				highlighted_buttons["situation"] = 1;
			}

			highlighted_buttons["communication"] = 0;
			function calls_signal_danger() {				// light the msg button		// already highlighted_buttons - possibly red
				change_class("communication", "btn btn-xs btn-danger");
				highlighted_buttons["communication"] = 3;
			}

			function calls_signal_info() {					// light the msg button	// already highlighted_buttons - possibly orange
				change_class("communication", "btn btn-xs btn-info");
				highlighted_buttons["communication"] = 2;
			}

			function calls_signal_warning() {					// light the msg button	// already highlighted_buttons - possibly yellow
				change_class("communication", "btn btn-xs btn-warning");
				highlighted_buttons["communication"] = 1;
			}

//========== misc

			function show_hide_callboard() {
				if ($("#show_hide_callboard").val() == 0) {
					change_class("callboard", "btn btn-xs btn-primary");
					try {
						parent.document.getElementById("callboard").style.height = "<?php print get_callboard_height();?>" + "px";
						parent.window.setIframeHeight();
					} catch (e) {
					}
					$("#show_hide_callboard").val(1);
				} else {
					change_class("callboard", "btn btn-xs btn-default");
					try {
						parent.document.getElementById("callboard").style.height = "0px";
						parent.window.setIframeHeight();
					} catch (e) {
					}
					$("#show_hide_callboard").val(0);
				}
			}

			function send_request(url, callback) {
				$.get(url, function (data) {callback(data);})
				.done()
				.fail(function () {
					alert("error: send_request(" + url + ", " + callback + ")");
				});
			}

			function show_message(message, appearance) {
				switch (appearance) {
				case "primary":
					$("#infostring_top").removeClass().addClass("label label-primary");
					break;
				case "success":
					$("#infostring_top").removeClass().addClass("label label-success");
					break;
				case "info":
					$("#infostring_top").removeClass().addClass("label label-info");
					break;
				case "warning":
					$("#infostring_top").removeClass().addClass("label label-warning");
					break;
				case "danger":
					$("#infostring_top").removeClass().addClass("label label-danger");
					break;
				default:
					$("#infostring_top").removeClass().addClass("label label-default");
				}
				$("#infostring_top").html(message);
				setTimeout("$('#infostring_top').html('');", 1500);
			}

			var communication_message_timeout = null;

			function fade_out_communication_message() {
				$("#communicationstring_top").fadeOut(ptt_fade_out_time, function() {
					$("#communicationstring_top").css("display","none");
					$("#communicationstring_top").text("");
					clearTimeout(communication_message_timeout);
				});
			}

			function show_communication_message(message, appearance) {
				switch (appearance) {
				case "primary":
					$("#communicationstring_top").removeClass().addClass("label label-primary");
					break;
				case "success":
					$("#communicationstring_top").removeClass().addClass("label label-success");
					break;
				case "info":
					$("#communicationstring_top").removeClass().addClass("label label-info");
					break;
				case "warning":
					$("#communicationstring_top").removeClass().addClass("label label-warning");
					break;
				case "danger":
					$("#communicationstring_top").removeClass().addClass("label label-danger");
					break;
				default:
					$("#communicationstring_top").removeClass().addClass("label label-default");
				}
				if ($("#communicationstring_top").text() != message) {
					clearTimeout(communication_message_timeout);
					$("#communicationstring_top").stop(true, true);
					$("#communicationstring_top").text(message);
					$("#communicationstring_top").css("display","inline");
					communication_message_timeout = window.setTimeout("fade_out_communication_message()", ptt_display_time);
				}
			}

			setInterval(buttons_blink, 0.25 * 1000);
			function buttons_blink() {
				var el1 = document.getElementsByClassName("btn-info_transparent");
				var el2 = document.getElementsByClassName("btn-info");
				for (var i = 0; i < el1.length; i++) {
					el1[i].classList.toggle("btn-info");
				}
				for (var i = 0; i < el2.length; i++) {
					el2[i].classList.toggle("btn-info_transparent");
				}
				var el3 = document.getElementsByClassName("btn-warning_transparent");
				var el4 = document.getElementsByClassName("btn-warning");
				for (var i = 0; i < el3.length; i++) {
					el3[i].classList.toggle("btn-warning");
				}
				for (var i = 0; i < el4.length; i++) {
					el4[i].classList.toggle("btn-warning_transparent");
				}
				var el5 = document.getElementsByClassName("btn-danger_transparent");
				var el6 = document.getElementsByClassName("btn-danger");
				for (var i = 0; i < el5.length; i++) {
					el5[i].classList.toggle("btn-danger");
				}
				for (var i = 0; i < el6.length; i++) {
					el6[i].classList.toggle("btn-danger_transparent");
				}
				var el7 = document.getElementsByClassName("alert-warning-flat_transparent");
				var el8 = document.getElementsByClassName("alert-warning-flat");
				for (var i = 0; i < el7.length; i++) {
					el7[i].classList.toggle("alert-warning-flat");
				}
				for (var i = 0; i < el8.length; i++) {	
					el8[i].classList.toggle("alert-warning-flat_transparent");
				}
			}

			function do_day_night(day_night_info) {
				if (day_night_info.valueOf() == "night") {
					$("#day_night_toggle").val("day");
					$("#div_day_night").html("night");
					parent.document.body.style.backgroundColor = "#000000";
					document.body.style.backgroundColor = "#000000";
					document.body.style.color = "#FFFFFF";
					try {
						parent.frames["callboard"].document.body.style.backgroundColor = "#000000";
						parent.frames["callboard"].document.body.style.color = "#FFFFFF";
						parent.frames["callboard"].$("#main_container").css("backgroundColor", "#000000");
						parent.frames["callboard"].$("#main_container").css("color", "#FFFFFF");
						parent.frames["callboard"].$("table th, td").css("backgroundColor", "<?php print get_variable("night_color");?>");
						parent.frames["callboard"].$(".panel").css("backgroundColor", "<?php print get_variable("night_color");?>");
						parent.frames["main"].document.body.style.backgroundColor = "#000000";
						parent.frames["main"].document.body.style.color = "#FFFFFF";
						parent.frames["main"].$("#main_container").css("backgroundColor", "#000000");
						parent.frames["main"].$("#main_container").css("color", "#FFFFFF");
						parent.frames["main"].$("h4").css("backgroundColor", "#000000");
						parent.frames["main"].$("h4").css("color", "#FFFFFF");
						parent.frames["main"].$("table th, td:not(.status_table)").css("backgroundColor", "<?php print get_variable("night_color");?>");
						parent.frames["main"].$(".panel").css("backgroundColor", "<?php print get_variable("night_color");?>");
						parent.frames["main"].$(".modal-content").css("backgroundColor", "<?php print get_variable("night_color");?>");
						parent.frames["main"].$(".modal-body").css("backgroundColor", "<?php print get_variable("night_color");?>");
						parent.frames["main"].$(".infobox-head").css("backgroundColor", "<?php print get_variable("night_color");?>");
					} catch (e) {
					}
				} else {
					$("#day_night_toggle").val("night");
					$("#div_day_night").html("day");
					parent.document.body.style.backgroundColor = "#FFFFFF";
					document.body.style.backgroundColor = "#FFFFFF";
					document.body.style.color = "#000000";
					try {
						parent.frames["callboard"].document.body.style.backgroundColor = "#FFFFFF";
						parent.frames["callboard"].document.body.style.color = "#000000";
						parent.frames["callboard"].$("#main_container").css("backgroundColor", "#FFFFFF");
						parent.frames["callboard"].$("#main_container").css("color", "#000000");
						parent.frames["callboard"].$("table th, td").css("background-color", "transparent");
						parent.frames["callboard"].$(".panel").css("background-color", "transparent");
						parent.frames["main"].document.body.style.backgroundColor = "#FFFFFF";
						parent.frames["main"].document.body.style.color = "#000000";
						parent.frames["main"].$("#main_container").css("backgroundColor", "#FFFFFF");
						parent.frames["main"].$("#main_container").css("color", "#000000");
						parent.frames["main"].$("h4").css("backgroundColor", "#FFFFFF");
						parent.frames["main"].$("h4").css("color", "#000000");
						parent.frames["main"].$("table th, td:not(.status_table)").css("background-color", "transparent");
						parent.frames["main"].$(".panel").css("background-color", "transparent");
						parent.frames["main"].$(".modal-content").css("background-color", "#FFFFFF");
						parent.frames["main"].$(".modal-body").css("background-color", "#FFFFFF");
						parent.frames["main"].$(".infobox-head").css("backgroundColor", "#FFFFFF");
					} catch (e) {
					}
				}
			}

			function do_audio(sound) {
				if (primary_screen) {
					try {
						$("#" + sound).trigger("play");
					} catch (e) {
					}
				}
			}

			function test_audio(sound) {
				try {
					$("#" + sound).trigger("play");
				} catch (e) {
				}
			}

			function do_login(user_name, user_level) {
				if (isNaN(user_level)) {
					return;
				}
				do_api_connection_test(false, "");
				if (user_level == 0) {
					$("#level").html("<?php print get_text("permission_super");?>");
				} else {
					if (user_level == 1) {
						$("#level").html("<?php print get_text("permission_admin");?>");
					} else {
						if (user_level == 2) {
							$("#level").html("<?php print get_text("permission_operator");?>");
						} else {
							if (user_level == 3) {
								$("#level").html("<?php print get_text("permission_guest");?>");
							}
						}
					}
				}
				if ((user_name != "") && (typeof (user_name) != "undefined")) {
					$("#logged_in").html(user_name);
				}
				$("#buttons").css("display", "block");
				$("#permission_info").css("display", "inline");
				$("#timeout_info").css("display", "inline");
				$("#day_night").css("display", "inline");
				$("#date_time").css("display", "inline");
				if ((user_level != 0) && (user_level != 1) &&  (user_level != 2)) {
					$("#add_ticket").css("display", "none");
					$("#units").css("display", "none");
					$("#facilities").css("display", "none");
					$("#communication").css("display", "none");
				}
				parent.frames["callboard"].window.location.href = "callboard.php";
				parent.frames["main"].window.location.href = "situation.php";
			}

			function do_logout() {
				$("#date_time_of_day").html("");
				$("#logged_in").html(NOT_STR);
				is_initialized = false;
				$("#buttons").css("display", "none");
				$("#permission_info").css("display", "none");
				$("#timeout_info").css("display", "none");
				$("#day_night").css("display","none");
				$("#date_time").css("display", "none");
				try {
					parent.frames["callboard"].hide_callboard();
				} catch (e) {
				}
				parent.frames["main"].window.location.href = "situation.php?logout=true";
				parent.frames["callboard"].window.location.href = "callboard.php";
				parent.window.document.body.style.backgroundColor = "#FFFFFF";
			}

			function callback_no_timeout(result) {
				if (result.valueOf() == "timeout_disabled") {
					change_class("timeout_button", "btn btn-xs btn-primary");
				}
			}

			var get_changes_array;

			$(document).ready(function() {
				parent.window.setIframeHeight();
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					get_changes_array = JSON.parse(event.data);
					switch (get_changes_array["type"]) {
					case "button":
						switch (get_changes_array["action"]) {
						case "highlight":
							highlight_button(get_changes_array["item"])
							break;
						default:
						}
						break;
					case "message":
						show_message(get_changes_array["action"], get_changes_array["item"]);
						break;
					case "div":
						$("#" + get_changes_array["item"]).html(get_changes_array["action"]);
						break;
					case "script":
						switch (get_changes_array["item"]) {
						case "main":
							window.parent.main.location.href=get_changes_array["action"];
							break;
						default:
						}	
						break;
					case "function":
						switch (get_changes_array["item"]) {
						case "test_audio":
							test_audio(get_changes_array["action"]);
							break;
						case "start_polling":
							start_polling();
							break;
						case "stop_polling":
							stop_polling();
							break;
						case "window_location_reload":
							window.location.reload();
							break;
						default:
						}
						break;
					case "set_parked_form_data":
						switch (get_changes_array["item"]) {
						case "ticket_add_form_data":
							ticket_add_form_data = get_changes_array["ticket_add_form_data"];
							break;
						case "ticket_add_timestamp":
							ticket_add_timestamp = get_changes_array["action"];
							break;
						case "ticket_add_ticket_id":
							ticket_add_ticket_id = get_changes_array["action"];
							break;
						case "ticket_close_form_data":
							ticket_close_form_data[get_changes_array["action"]] = get_changes_array["ticket_close_form_data"];
							break;
						case "ticket_close_timestamp":
							ticket_close_timestamp[get_changes_array["action"]] = get_changes_array["datetime"];
							break;
						case "ticket_close_delete":
							ticket_close_form_data[get_changes_array["action"]] = (function () {return;})();
							ticket_close_timestamp[get_changes_array["action"]] = (function () {return;})();
							break;
						case "action_form_data":
							action_form_data[get_changes_array["action"]] = get_changes_array["action_form_data"];
							break;
						case "action_timestamp":
							action_timestamp[get_changes_array["action"]] = get_changes_array["datetime"];
							break;
						case "action_delete":
							action_form_data[get_changes_array["action"]] = (function () {return;})();
							action_timestamp[get_changes_array["action"]] = (function () {return;})();
							break;
						case "log_report_form_data":
							log_report_form_data = get_changes_array["log_report_form_data"];
							break;
						case "log_report_timestamp":
							log_report_timestamp = get_changes_array["action"];
							break;
						default:
						}
						break;
					default:
					}
					get_changes_array = "undefined";
				});
			});

			function show_situation() {
				window.parent.main.location.href="situation.php?screen_id=" + $('#div_screen_id').html();
			}

		</script>
	</head>
	<body onload="check_frames(); navigation_init();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<?php print $audio_sources_str;?>
		<div id="infostr_screen_id" style="display: <?php print $display_str;?>;">screen_id: </div>
		<div id="div_screen_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_first_screen" style="display: <?php print $display_str;?>;">first_screen: </div>
		<div id="div_first_screen" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_server_time" style="display: <?php print $display_str;?>;">server_time: </div>
		<div id="div_server_time" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_server_time_formatted" style="display: <?php print $display_str;?>;">server_time_formatted: </div>
		<div id="div_server_time_formatted" style="display: <?php print $display_str;?>;"></div>
		<!--  ==== for multiuser and development mode ====  -->
		<div id="infostr_user_id" style="display: <?php print $display_str;?>;">user id: </div>
		<div id="div_user_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_user_name" style="display: <?php print $display_str;?>;">name: </div>
		<div id="div_user_name" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_user_level" style="display: <?php print $display_str;?>;">level: </div>
		<div id="div_user_level" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_ticket_latest_id" style="display: <?php print $display_str;?>;">| ticket latest_id: </div>
		<div id="div_ticket_latest_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_ticket_changed_id" style="display: <?php print $display_str;?>;">changed_id: </div>
		<div id="div_ticket_changed_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_ticket_updated" style="display: <?php print $display_str;?>;">updated: </div>
		<div id="div_ticket_updated" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_ticket_user" style="display: <?php print $display_str;?>;">user: </div>
		<div id="div_ticket_user" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_unit_id" style="display: <?php print $display_str;?>;">| unit id: </div>
		<div id="div_unit_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_unit_updated" style="display: <?php print $display_str;?>;">updated: </div>
		<div id="div_unit_updated" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_unit_user" style="display: <?php print $display_str;?>;">user: </div>
		<div id="div_unit_user" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_unit_callprogress_id" style="display: <?php print $display_str;?>;">| callprogress unit_id: </div>
		<div id="div_unit_callprogress_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_unit_callprogress_updated" style="display: <?php print $display_str;?>;">updated: </div>
		<div id="div_unit_callprogress_updated" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_unit_callprogress_user" style="display: <?php print $display_str;?>;">user: </div>
		<div id="div_unit_callprogress_user" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_assign_max_id" style="display: <?php print $display_str;?>;">| assign max_id: </div>
		<div id="div_assign_max_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_assign_quanity" style="display: <?php print $display_str;?>;"> quantity: </div>
		<div id="div_assign_quantity" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_assign_updated" style="display: <?php print $display_str;?>;"> updated: </div>
		<div id="div_assign_updated" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_assign_user" style="display: <?php print $display_str;?>;"> user: </div>
		<div id="div_assign_user" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_action_max_id" style="display: <?php print $display_str;?>;">| action max_id: </div>
		<div id="div_action_max_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_action_changed_id" style="display: <?php print $display_str;?>;"> changed_id: </div>
		<div id="div_action_changed_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_action_updated" style="display: <?php print $display_str;?>;"> updated: </div>
		<div id="div_action_updated" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_action_user" style="display: <?php print $display_str;?>;"> user: </div>
		<div id="div_action_user" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_facility_id" style="display: <?php print $display_str;?>;">| facility id: </div>
		<div id="div_facility_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_facility_updated" style="display: <?php print $display_str;?>;">updated: </div>
		<div id="div_facility_updated" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_facility_user" style="display: <?php print $display_str;?>;">user: </div>
		<div id="div_facility_user" style="display: <?php print $display_str;?>;"></div>

		<div style="display: <?php print $display_str;?>;">| requests silent: </div>
		<div id="div_silent_requests" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| requests message: </div>
		<div id="div_message" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| requests warn-text: </div>
		<div id="div_warn_text" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| requests auto-ticket: </div>
		<div id="div_auto_ticket" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| normal: </div>
		<div id="div_requests" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;"> emcy_lo: </div>
		<div id="div_emergency_requests_low" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;"> emcy_hi: </div>
		<div id="div_emergency_requests_high" style="display: <?php print $display_str;?>;"></div>

		<div style="display: <?php print $display_str;?>;">| current_radio: </div>
		<div id="div_api_current_radio" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| api_host_available: </div>
		<div id="div_api_host_available" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| api_host_code: </div>
		<div id="div_api_host_code" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| api_host_text: </div>
		<div id="div_api_host_text" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| api_host_timestamp_current_state: </div>
		<div id="div_api_host_timestamp_current_state" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| api_phone_host_available: </div>
		<div id="div_api_phone_host_available" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| api_phone_host_code: </div>
		<div id="div_api_phone_host_code" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| api_phone_host_text: </div>
		<div id="div_api_phone_host_text" style="display: <?php print $display_str;?>;"></div>
		<div style="display: <?php print $display_str;?>;">| api_phone_host_timestamp_current_state: </div>
		<div id="div_api_phone_host_timestamp_current_state" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_scheduled" style="display: <?php print $display_str;?>;">| scheduled: </div>
		<div id="div_scheduled" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_day_night" style="display: <?php print $display_str;?>;">| day_night: </div>
		<div id="div_day_night" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_object_id" style="display: <?php print $display_str;?>;">| object id: </div>
		<div id="div_facility_id" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_object_updated" style="display: <?php print $display_str;?>;">updated: </div>
		<div id="div_facility_updated" style="display: <?php print $display_str;?>;"></div>
		<div id="infostr_object_user" style="display: <?php print $display_str;?>;">user: </div>
		<div id="div_facility_user" style="display: <?php print $display_str;?>;"></div>

		<div id="infostr_log" style="display: <?php print $display_str;?>;">| log: </div>
		<div id="div_log" style="display: <?php print $display_str;?>;"></div>

		<div id="database" style="display: <?php print $display_str;?>;">&nbsp;&nbsp;<?php print get_text("Database");?>:&nbsp;<?php print $GLOBALS['db_name'];?></div>
		<div style="display: <?php print $display_str;?>;"><?php print "&nbsp;&nbsp;" . get_text("Module"); ?>: </div>
		<div id="script" style="display: <?php print $display_str;?>;"></div>
		<div class="btn btn-xs btn-default" onclick="top.location.href='install.php';" style="display: <?php print $display_str;?>;"><?php print "install.php";?></div>

		<div id="head_line" class="container-fluid hidden-print">
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-3">
					<h3><span class="label label-primary"><?php print get_variable("title_string");?></span></h3>
				</div>
				<div class="col-md-4" style="text-align: center;">
					<h3>
						<span id="infostring_top" class="label label-success"></span>
						<span id="communicationstring_top" class="label label-info"></span>
					</h3>
					<span style="font-weight:bold; color:red; display:<?php print $display_str;?>"<?php print get_help_text_str("development_mode");?>>
						<?php print get_text("development mode - do not use for dispatching");?>
					</span>
				</div>
				<div id="date_time" class="col-md-3" style="display: none;">
					<h3><span id="date_time_of_day" class="label label-clock" style="float: right;"></span></h3>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row" style="margin-bottom: 5px; height: 20px;">
				<div class="col-md-1"></div>
				<div id="permission_info" class="col-md-4" style="display: none;">
					<div>
						<span><?php print get_text("Logged in");?>:</span>
						<span id="logged_in"><?php print get_text(" *not*");?></span>&nbsp;
						<span><?php print get_text("Level");?>: </span>
						<span id="level"> <?php print get_text(" *na*");?></span>
					</div>
				</div>
				<div class="col-md-4"></div>
				<div class="col-md-2">
					<div id="timeout_info" style="float: right; display: none;""></div>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row" id="buttons" style="display: none;">
				<div class="col-md-1"></div>
				<div class="col-md-7">
					<button id="situation" class="btn btn-xs btn-default btn-blink"
						onclick="show_situation();"><?php print get_text("Situation");?></button>
					<button id="callboard" class="btn btn-xs btn-default"
						onclick="show_hide_callboard();" style="<?php print $display_callboard_str;?>"><?php print get_text("Board");?></button>
					<button id="communication" class="btn btn-xs btn-default btn-blink"
						onclick="window.parent.main.location.href='communication.php';">
						<?php print get_text("Communication");?>
						<span id="count_messages" class="badge" style=" width:23px; margin-left: 3px; background-color: grey; color: white;">0</span>
					</button>				
					<button id="add_ticket" class="btn btn-xs btn-default"
						onclick="window.parent.main.location.href='ticket_add.php';"><?php print get_text("New");?></button>
					<button id="log_report" class="btn btn-xs btn-default"
						onclick="window.parent.main.location.href='log_report.php';"><?php print get_text("Log report");?></button>
					<button id="units" class="btn btn-xs btn-default"
						onclick="window.parent.main.location.href='units.php';"><?php print get_text("Units");?></button>
					<button id="facilities" class="btn btn-xs btn-default"
						onclick="window.parent.main.location.href='facilities.php';"><?php print get_text("Facilities");?></button>
					<button id="reports" class="btn btn-xs btn-default"
						onclick="window.parent.main.location.href='reports.php';"><?php print get_text("Reports");?></button>
					<button id="configuration" class="btn btn-xs btn-default"
						onclick="window.parent.main.window.location.href='configuration.php';"><?php print get_text("Configuration");?></button>
				</div>
				<div class="col-md-3">
					<div style="float: right;">
						<button<?php print get_help_text_str("toggle_day_night");?> id="day_night_toggle" class="btn btn-xs btn-default" value="night"
							onclick="send_request('./set_data.php?function=day_night&value=' + this.value, do_day_night);"><?php print get_text("Day / Night");?></button>
						<button<?php print get_help_text_str("deactivate_timeout");?> id="timeout_button" class="btn btn-xs btn-default"
							onclick="send_request('set_data.php?function=timeout&value=off', callback_no_timeout);"><?php print get_text("Auto-logout off");?></button>
						<button id="logout" class="btn btn-xs btn-default"
							onclick="do_logout();"><?php print get_text("Logout");?></button>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
		</div>
	 	<input type="hidden" id="show_hide_callboard" value=0>
	</body>
</html>