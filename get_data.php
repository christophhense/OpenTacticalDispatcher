<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/api.inc.php");

$api_log_max_age = get_variable("_api_log_max_age_setng");

if ($api_log_max_age != 0) {

	$delete_old_api_log = "DELETE FROM `api_log` " .
		"WHERE `datetime` <= '" . mysql_datetime(time() - (ceil($api_log_max_age * 60))) . "';";

	db_query($delete_old_api_log, __FILE__, __LINE__);
}

$auto_poll_settings = explode(",", get_variable("auto_poll"));
$auto_poll_time = trim($auto_poll_settings[0]);
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
if ((isset ($_SESSION['user_id'])) && ($_SESSION['user_id'] > 0)) {
	$current_user_id = "0";
	if (isset ($_SESSION['user_id'])) {
		$current_user_id = $_SESSION['user_id'];
	}

	$query = "SELECT `name` AS `user_name`, " .
		"`level`, " .
		"`expires` " .
		"FROM `users` " .
		"WHERE `id` = " . $current_user_id . " LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	$rows_affected = db_affected_rows($result);
	$row = false;
	if (db_num_rows($result) > 0) {
		$row = stripslashes_deep(db_fetch_assoc($result));
	}
	$current_user_name = "0";
	if ($row) {
		$current_user_name = $row['user_name'];
	}
	if ($row['expires'] <= mysql_datetime(time() + 60)) {
		if ($_SESSION['timeout'] == "off") {
			set_session_expire_time("off");
		}
		if ($_SESSION['timeout'] == "disabled") {
			set_session_expire_time("disabled");
		}
	}
}
switch ($function) {
case "reporting_channel":
	if ((isset ($_SESSION['user_id'])) && ($_SESSION['user_id'] > 0) && ($rows_affected == 1) && ($row['expires'] > mysql_datetime())) {
		$unit_id = "";
		if (isset ($_GET['unit_id'])) {
			$unit_id = $_GET['unit_id'];
		}

		$query_reporting_channel = "SELECT `unit_phone`, " .
			"`unit_email`, " .
			"`remote_data_services` " .
			"FROM `units` " .
			"WHERE `id` = " . $unit_id . ";";

		$result_reporting_channel = db_query($query_reporting_channel, __FILE__, __LINE__);
		if (db_num_rows($result_reporting_channel) > 0)  {
			$row_reporting_channel = stripslashes_deep(db_fetch_assoc($result_reporting_channel));
			$json_connections = array (
				"smsg_id" => $row_reporting_channel['remote_data_services'],
				"email" => $row_reporting_channel['unit_email'],
				"phone" => $row_reporting_channel['unit_phone']
			);
			print json_encode($json_connections);
		}
		unset ($result_reporting_channel);
	}
	break;
default:
	//========== Screen
	$json_screen = array ();
	$json_screen["date_time"] = mysql_datetime();
	$json_screen["screen_id"] = $_GET["screen_id"];
	if (isset ($_SESSION['day_night'])) {
		$json_screen["day_night"] = $_SESSION['day_night'];
	} else {
		$json_screen["day_night"] = "day";
	}
	$_SESSION['screens'][$_GET['screen_id']] = date("U");
	foreach ($_SESSION['screens'] as $key => $value) {
		if (date("U") - $value > (ceil(($auto_poll_time * 200) / 1000))) {
			unset ($_SESSION['screens'][$key]);
		}
	}
	$json_screen['screen_list'] = $_SESSION['screens'];
	$json_screen["first_screen"] = "off";
	if ((isset ($_SESSION['first_screen'])) && ($_SESSION['first_screen'] == $_GET['screen_id'])) {
		$json_screen["first_screen"] = "on";
		$_SESSION['first_screen_timestamp'] = date("U");
	} else {
		if ((!isset ($_SESSION['first_screen_timestamp'])) || (date("U") - $_SESSION['first_screen_timestamp'] > (ceil(($auto_poll_time * 200) / 1000)))) {
			$_SESSION['first_screen'] = $_GET['screen_id'];
			$_SESSION['first_screen_timestamp'] = date("U");
			$json_screen["first_screen"] = "on";
		}
	}
	if (get_variable("_update_progress_time") != "") {
		$json_screen["update_progress_time"] = get_variable("_update_progress_time");
	}
	if (isset ($_SESSION["screen_id_" . $_GET['screen_id']]['situation_type'])) {
		$json_screen["situation_type"] = $_SESSION["screen_id_" . $_GET['screen_id']]['situation_type'];
	}
	//========== User
	if ((isset ($_SESSION['user_id'])) && ($_SESSION['user_id'] > 0) && ($rows_affected == 1) && ($row['expires'] > mysql_datetime(time()))) {
		foreach ($_SESSION['reset_button'] as $key => $value) {
			if ($key == $_GET['screen_id']) {
				$json_screen['reset_button'] = $value;
				unset ($_SESSION['reset_button'][$key]);
			}
		}
		$current_user_name = "0";
		if ($row) {
			$current_user_name = $row['user_name'];
		}
		$current_user_level = "0";
		if ($row) {
			$current_user_level = $row['level'];
		}
		$current_user_expires = "0";
		if ($row) {
			$current_user_expires = round((strtotime($row['expires']) - time()) / 60);
		}
		$json_user = array (
			"id" => $current_user_id,
			"name" => $current_user_name,
			"level" => $current_user_level,
			"expires" => $current_user_expires,
			"timeout" => $_SESSION['timeout']
		);
		//========== Communication

		 $query = "SELECT `al`.`source` AS `apilog_source`, " .
			"`al`.`destination` AS `apilog_destination`, " .
			"`al`.`destination_alias` AS `apilog_destination_alias` " .
			"FROM `api_log` `al` " .
			"WHERE `al`.`datetime` >=  '" . mysql_datetime(time() - (ceil(($auto_poll_time * 200) / 1000))) . "' " .
			"AND `al`.`code` = " . $GLOBALS['LOG_CURRENT_RADIO'] .
			" LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_num_rows($result) > 0)  {
			$row = stripslashes_deep(db_fetch_assoc($result));
/*			$destination = remove_nls($row['apilog_destination']);
			if ($row['apilog_destination_alias'] != null) {
				$destination = remove_nls($row['apilog_destination_alias']);
			}
			$json_screen['communication'] = get_text("Current radio") . ": " . $destination;
			*/
			$source = remove_nls($row['apilog_source']);
			$json_screen['communication'] = get_text("Current radio") . ": " . $source;
			$json_screen['appearance'] = "success";
		}	
		$current_radio = "";
		$where_radio_str = "";

		$query = "SELECT `current_radio` " .
			"FROM `users` " .
			"WHERE `id` = " . $_SESSION['user_id'] . " " . 
			"LIMIT 1;";
		
		$result = db_query($query, __FILE__, __LINE__);
		if (db_num_rows($result) > 0) {
			$row = stripslashes_deep(db_fetch_assoc($result));
			if (($row['current_radio'] != null) && ($row['current_radio'] != "")) {
				$where_radio_str = " AND `al`.`destination` = '" . $row['current_radio'] . "'";
				$current_radio = $row['current_radio'];
			}
		}

		$query = "SELECT `al`.`source` AS `apilog_source`, " .
			"`al`.`code` AS `apilog_code`, " .
			"`u`.`handle` AS `unit_handle` " .
			"FROM `api_log` `al` " .
			"LEFT JOIN `units` `u` ON `al`.`unit_id` = `u`.`id` " .
			"WHERE `al`.`datetime` >=  '" . mysql_datetime(time() - (ceil(($auto_poll_time * 200) / 1000))) . "' " .
			"AND `al`.`code` = " . $GLOBALS['LOG_PTT'] . $where_radio_str . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_num_rows($result) > 0)  {
		$row = stripslashes_deep(db_fetch_assoc($result));
			if ($row['unit_handle'] != "") {
				$json_screen['communication'] = remove_nls($row['unit_handle']);
				$json_screen['appearance'] = "primary";
			} else {
				$receiver_array = split_api_receiver_str($row['apilog_source']);
				$json_screen['communication'] = $receiver_array[1];
				$json_screen['appearance'] = "default";
			}
		}
		//========== Application interface
		$json_api = array ();
		$json_api['current_radio'] = $current_radio;
		$api_availability = get_api_availability("api");
		$json_api['host_available'] = $api_availability['available'];
		$json_api['host_timestamp_current_state'] = $api_availability['timestamp_current_state'];
		$json_api['host_timestamp_last_retry'] = $api_availability['timestamp_last_retry'];
		$json_api['host_code'] = $api_availability['code'];
		$json_api['host_text'] = $api_availability['text'];
		$api_availability = get_api_availability("phone");
		$json_api['phone_host_available'] = $api_availability['available'];
		$json_api['phone_host_timestamp_current_state'] = $api_availability['timestamp_current_state'];
		$json_api['phone_host_timestamp_last_retry'] = $api_availability['timestamp_last_retry'];
		$json_api['phone_host_code'] = $api_availability['code'];
		$json_api['phone_host_text'] = $api_availability['text'];
		//========== Ticket
		$where_str = get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_TICKET'], "AND");

		$query = "SELECT `t`.`id` AS `ticket_id` " .
			"FROM `tickets` `t` " .
			"LEFT JOIN `allocates` ON `t`.`id` = `allocates`.`resource_id` " .
			"WHERE `t`.`status` = " . $GLOBALS['STATUS_OPEN'] . " " .  $where_str . " " .
			"ORDER BY `t`.`id` DESC " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$row = false;
		if (db_num_rows($result) > 0) {
			$row = stripslashes_deep(db_fetch_assoc($result));	
		}
		if ($row) {
			$ticket_max_id = $row['ticket_id'];
		} else {
			$ticket_max_id = "0";
		}

		$query = "SELECT `t`.`id` AS `ticket_id`, " .
			"`t`.`user_id` AS `user_id`, " .
			"`t`.`updated` AS `updated` " .
			"FROM `tickets` `t` " .
			"LEFT JOIN `allocates` ON `t`.`id` = `allocates`.`resource_id` " .
			"WHERE (`t`.`status` = " . $GLOBALS['STATUS_OPEN'] . ")||" .
			"(`t`.`status` = " . $GLOBALS['STATUS_SCHEDULED'] . ") " .  $where_str . " " .
			"ORDER BY `t`.`updated` DESC " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$row = false;
		if (db_num_rows($result) > 0) {
			$row = stripslashes_deep(db_fetch_assoc($result));
		}
		$ticket_changed_id = "0";
		if ($row) {
			$ticket_changed_id = $row['ticket_id'];
		}
		$ticket_updated = "0";
		if ($row) {
			$ticket_updated = $row['updated'];
		}
		$ticket_user_id = "0";
		if ($row) {
			$ticket_user_id = $row['user_id'];
		}
		//========== Scheduled

		$query = "SELECT `id` " .
			"FROM `tickets` " .
			"WHERE `status` = '" . $GLOBALS['STATUS_SCHEDULED'] . "' " .
			"AND `booked_date` <= (NOW() + INTERVAL " . get_variable("hide_booked") . " MINUTE);";

		$result = db_query($query, __FILE__, __LINE__);
		$scheduled_quantity = "0";
		if (db_num_rows($result)) {
			$scheduled_quantity = "" . db_num_rows($result) . "";
		}
		$json_ticket = array (
			"id_max" => $ticket_max_id,
			"id_changed" => $ticket_changed_id,
			"scheduled" => $scheduled_quantity,
			"update" => $ticket_updated,
			"user" => $ticket_user_id
		);
		//========== Units
		$unit_update_unit_id = 0;
		$unit_update_time = "";

		$query = "SELECT *, `u`.`id` AS `unit_id`, " .
			"`u`.`updated` AS `unit_updated` " .
			"FROM `units` `u` " .
			"LEFT JOIN `allocates` ON `u`.`id` = `allocates`.`resource_id` " .
			"WHERE (TRUE) " . get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_UNIT'], "AND") . " " .
			"ORDER BY `u`.`updated` DESC " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$row = false;
		if (db_num_rows($result) > 0) {
			$row = stripslashes_deep(db_fetch_assoc($result));
		}
		if ($row) {
			$unit_update_unit_id = $row['unit_id'];
			$unit_update_time = $row['unit_updated'];
		}

		$query = "SELECT `u`.`status_updated` AS `updated`, " .
			"`u`.`id` AS `unit_id`, " .
			"`u`.`user_id` AS `user_id` " .
			"FROM `units` `u` " .
			"LEFT JOIN `allocates` ON `u`.`id` = `allocates`.`resource_id` " .
			"LEFT JOIN `assigns` `as` ON `u`.`id` = `as`.`unit_id` " .
			"WHERE `u`.`user_id` != 0 " . get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_UNIT'], "AND") . " " .
			"ORDER BY `updated` DESC " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$row = false;
		if (db_num_rows($result) > 0) {
			$row = stripslashes_deep(db_fetch_assoc($result));
		}
		if ($row) {
			if ($row['updated'] > $unit_update_time) {
				$unit_update_unit_id = $row['unit_id'];
				$unit_update_time = $row['updated'];
			}
		}
		$unit_id = "0";
		if ($row) {
			$unit_id = $row['unit_id'];
		}
		$unit_updated = "0";
		if (($row != false) && ($row['updated']) && ($row['updated'] != null)) {
			$unit_updated = $row['updated'];
		}
		$unit_user = "0";
		if (($row != false) && ($row['user_id'])) {
			$unit_user = $row['user_id'];
		}
		$json_units_status = array (
			"id" => $unit_id,
			"update" => $unit_updated,
			"user" => $unit_user
		);
		//========== Call progression

		$query = "SELECT `as`.`updated` AS `updated`, " .
			"`as`.`id` AS `assign_id`, " .
			"`as`.`user_id` AS `user_id`, " .
			"`as`.`unit_id` AS `unit_id`, " .
			"`as`.`progession_changed` " .
			"FROM `assigns` `as` " .
			"LEFT JOIN `tickets` `t` ON `as`.`ticket_id` = `t`.`id` " .
			"LEFT JOIN `allocates` ON `t`.`id` = `allocates`.`resource_id` " .
			"WHERE `as`.`user_id` != 0 " . get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_TICKET'], "AND") . " " .
			"AND (`as`.`responding` IS NOT NULL " .
			"OR `as`.`on_scene` IS NOT NULL " .
			"OR `as`.`u2fenr` IS NOT NULL " .
			"OR `as`.`u2farr` IS NOT NULL) " .
			"ORDER BY `as`.`updated` DESC " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$assign_row = false;
		if (($result != false) && (db_num_rows($result) > 0)) {
			$assign_row = stripslashes_deep(db_fetch_assoc($result));
		}
		$call_progression_id = "0";
		if ($assign_row) {
			$call_progression_id = $assign_row['assign_id'];
		}
		$call_progression_change = "0";
		if ($assign_row) {
			$call_progression_change = $assign_row['updated'];
		}
		$call_progression_user = "0";
		if ($assign_row) {
			$call_progression_user = $assign_row['user_id'];
		}
		$progession_changed = "true";
		if ($assign_row && (trim($assign_row['progession_changed'])) == "false") {
			$progession_changed = "false";
		}
		$json_call_progression = array (
			"id" => $call_progression_id,
			"update" => $call_progression_change,
			"user" => $call_progression_user,
			"progession_changed" => $progession_changed
		);
		if (($assign_row != false) && ($assign_row['updated'] > $unit_update_time)) {
			$unit_update_unit_id = $assign_row['unit_id'];
			$unit_update_time = $assign_row['updated'];
		}
		//========== Dispatch

		$query = "SELECT `as`.`updated` AS `updated`, " .
			"`as`.`id` AS `assign_id`, " .
			"`as`.`user_id` AS `user_id`, " .
			"`as`.`unit_id` AS `unit_id`, " .
			"(SELECT  COUNT(*) as `numfound` FROM `assigns` WHERE `clear` IS NULL) AS `quantity_assigned` " .
			"FROM `assigns` `as` " .
			"LEFT JOIN `tickets` `t` ON `as`.`ticket_id` = `t`.`id` " .
			"LEFT JOIN `allocates` ON `t`.`id` = `allocates`.`resource_id` " . 	
			"WHERE `as`.`user_id` != 0 " . get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_TICKET'], "AND") . " " .
			"ORDER BY `as`.`datetime` DESC " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$assign_row = false;
		if (db_num_rows($result) > 0) {
			$assign_row = stripslashes_deep(db_fetch_assoc($result));
		}
		$assign_max_id = "0";
		if ($assign_row) {
			$assign_max_id = $assign_row['assign_id'];
		}
		$assign_quantity = "-1";
		if ($assign_row) {
			$assign_quantity = $assign_row['quantity_assigned'];
		}
		$assign_changed = "0";
		if ($assign_row) {
			$assign_changed = $assign_row['updated'];
		}
		$assign_user_id = "0";
		if ($assign_row) {
			$assign_user_id = $assign_row['user_id'];
		}		
		$json_assign = array (
			"id_max" => $assign_max_id,
			"quantity" => $assign_quantity,
			"update" => $assign_changed,
			"user" => $assign_user_id
		);
		if (($assign_row != false) && ($assign_row['updated'] > $unit_update_time)) {
			$unit_update_unit_id = $assign_row['unit_id'];
			$unit_update_time = $assign_row['updated'];
		}
		$_SESSION['unit_flag_1'] = $unit_update_unit_id;
		//========== Actions

		$query = "SELECT `id`, " .
			"`updated`, " .
			"`user_id` " .
			"FROM `actions` " .
			"WHERE `updated` = (SELECT MAX(`updated`) FROM `actions`) " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$action_row = false;
		if (db_num_rows($result) > 0) {
			$action_row = stripslashes_deep(db_fetch_assoc($result));
		}
		$action_max_id ="0";
		if ($action_row) {
			$action_max_id = $action_row['id'];
		}
		$action_changed_id = "0";
		if ($action_row) {
			$action_changed_id = $action_row['id'];
		}
		$action_updated = "0";
		if ($action_row) {
			$action_updated = $action_row['updated'];
		}
		$action_user_id = "0";
		if ($action_row) {
			$action_user_id = $action_row['user_id'];
		}
		$json_action = array (
			"id_max" => $action_max_id,
			"id_changed" => $action_changed_id,
			"update" => $action_updated,
			"user" => $action_user_id
		);
		//========== Call requests - ptt

		$query = "SELECT * FROM `api_log` " .
			"WHERE (`code` = " . $GLOBALS['LOG_PTT'] . ") " .
			"AND `cleared_datetime` IS NULL;";

		$result = db_query($query, __FILE__, __LINE__);	
		$silent_requests = 0;
		if (db_num_rows($result)) {
			$silent_requests = db_num_rows($result);
		}
		//========== Call requests - api status

		$query = "SELECT * FROM `api_log` " .
			"WHERE (`code` = " . $GLOBALS['LOG_API_CONNECTED'] . " " .
			"OR `code` = " . $GLOBALS['LOG_API_DISCONNECTED'] . " " .
			"OR `code` = " . $GLOBALS['LOG_API_DEVICE_TEXT'] .
			") AND (DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " .
			get_variable("_api_log_max_display_setng") . " MINUTE) <= `api_log`.`datetime`);";

		$result = db_query($query, __FILE__, __LINE__);

		$new_messages = 0;
		if (db_num_rows($result) && is_super()) {
			$new_messages = $new_messages + db_num_rows($result);
		}
		//========== Call requests - messages

		$query = "SELECT * FROM `api_log` " .
			"WHERE (`code` = " . $GLOBALS['LOG_INFO'] . " " .
			"OR `code` = " . $GLOBALS['LOG_ERROR'] . " " .
			"OR `code` = " . $GLOBALS['LOG_MESSAGE_RECEIVE'] . ") " .
			"AND `cleared_datetime` IS NULL;";

		$result = db_query($query, __FILE__, __LINE__);

		if (db_num_rows($result)) {
			$new_messages = db_num_rows($result);
		}
		//========== Call requests - auto-ticket
		$auto_ticket = 0;
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$is_auto_ticket = get_is_auto_ticket_line($row['text']);
			if ($is_auto_ticket["MATCH"]) {
				$auto_ticket++;
			}
		}
		$new_messages = $new_messages - $auto_ticket;
		$warn_text = 0;
		//========== Requests-badge

		$query = "SELECT * FROM `api_log` " .
			"WHERE (`code` = " . $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_REQ'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_MANACKN'] . ") " .
			"AND `cleared_datetime` IS NULL;";

		$result = db_query($query, __FILE__, __LINE__);
		$requests = 0;
		if (db_num_rows($result)) {
			$requests = db_num_rows($result);
		}
		//========== Emergency-requests

		$query = "SELECT * FROM `api_log` " .
			"WHERE `code` = " . $GLOBALS['LOG_EMGCY_LO'] . " " .
			"AND `cleared_datetime` IS NULL;";

		$result = db_query($query, __FILE__, __LINE__);
		$emergency_requests_low = 0;
		if (db_num_rows($result)) {
			$emergency_requests_low = db_num_rows($result);
		}

		$query = "SELECT * FROM `api_log` " .
			"WHERE `code` = " . $GLOBALS['LOG_EMGCY_HI'] . " " .
			"AND `cleared_datetime` IS NULL;";
		
		$result = db_query($query, __FILE__, __LINE__);
		$emergency_requests_high = 0;
		if (db_num_rows($result)) {
			$emergency_requests_high = db_num_rows($result);
		}

		$json_requests = array (
			"silent" => $silent_requests,
			"message" => $new_messages,
			"warn_text" => $warn_text,
			"auto_ticket" => $auto_ticket,
			"normal" => $requests,
			"emergency_low" => $emergency_requests_low,
			"emergency_high" => $emergency_requests_high
		);
		//========== Facilities

		$query = "SELECT `f`.`updated` AS `updated`, " .
			"`f`.`id` AS `facility_id`, " .
			"`f`.`user_id` AS `user_id` " .
			"FROM `facilities` `f` " .
			"LEFT JOIN `allocates` ON `f`.`id` = `allocates`.`resource_id` " .
			"WHERE `f`.`user_id` != 0 " . get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_FACILITY'], "AND") . " " .
			"ORDER BY `updated` DESC LIMIT 1";		// get most recent

		$result = db_query($query, __FILE__, __LINE__);
		if (db_num_rows($result) > 0) {
			$row = stripslashes_deep(db_fetch_assoc($result)) ;
		}
		$facility_id = "0";
		if ($row) {
			$facility_id = $row['facility_id'];
		}
		$facility_updated = "0";
		if (($row != false) && isset($row['updated']) && ($row['updated'] != null)) {
			$facility_updated = $row['updated'];
		}
		$facility_user = "0";
		if (($row != false) && ($row['user_id'])) {
			$facility_user = $row['user_id'];
		}
		$json_facilities_status = array (
			"id" => $facility_id,
			"update" => $facility_updated,
			"user" => $facility_user
		);
		//========== Log

		$query = "SELECT `id` " .
			"FROM `log` `l`" .
			generate_log_where_str("get_infos", "", 0, 0, "") . ";";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_num_rows($result)) {
			$row = stripslashes_deep(db_fetch_assoc($result));
			$the_log_data = $row['id'];
		} else {
			$the_log_data = 0;
		}
		$json_log = array("id" => $the_log_data);
		//========== buildt json return

		$json_array = array (
			"user" => $json_user,
			"ticket" => $json_ticket,
			"units_status" => $json_units_status,
			"call_progression" => $json_call_progression,
			"assign" => $json_assign,
			"action" => $json_action,
			"requests" => $json_requests,
			"api" => $json_api,
			"screen" => $json_screen,
			"facilities_status" => $json_facilities_status,
			"log" => $json_log
		);
		print json_encode($json_array);
	} else {
		$json_user = array (
			"id" => 0,
			"name" => "",
			"level" => 99
		);
		print json_encode(array (
			"user" => $json_user,
			"screen" => $json_screen
		));
	}
}
?>