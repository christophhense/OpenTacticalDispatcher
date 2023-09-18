<?php
function get_unit_data($function, $source, $source_regexp) {

	$unit_data = array (
		"unit_id" => 0,
		"unit_handle" => "",
		"unit_name" => false,
		"unit_status" => false,
		"unit_dispatchable" => false,
		"multiple_address_error" => "",
		"store_unknown_unit_position_report" => false,
		"store_unknown_unit_data" => false
	);

	$mandatory_in_service = true;
	$where_emergency_str = "";
	$emergency_settings = explode(",", get_variable("_api_evaluate_unknown_unit_emergency_encdg"));
	$evaluate_emergency_hi = false;
	if (trim($emergency_settings[0]) == "1") {
		$evaluate_emergency_hi = true;
	}
	$evaluate_emergency_lo = false;
	if (trim($emergency_settings[1]) == "1") {
		$evaluate_emergency_lo = true;
	}
	$position_report_evaluation_time = 5;
	if (trim($emergency_settings[2]) >= 0) {
		$position_report_evaluation_time = trim($emergency_settings[2]);
	}
	switch ($function) {
	case "_api_log_encdg":
	case "_api_errlog_encdg":
	case "_api_message_encdg":
		$unit_data["store_unknown_unit_data"] = true;
		break;
	default:
	}
	if (($source != "") && ($function != "_connection_test")) {
		$where_source_address_str = " LIKE '%" . $source . "%'";
		if ($source_regexp != "") {
			$where_source_address_str = " REGEXP '" . $source_regexp . "'";
		}
		if ($function == "_api_emgcy_hi_encdg" && $evaluate_emergency_hi) {
			$unit_data["store_unknown_unit_data"] = true;
			$mandatory_in_service = false;
		}
		if ($function == "_api_emgcy_lo_encdg" && $evaluate_emergency_lo) {
			$unit_data["store_unknown_unit_data"] = true;
			$mandatory_in_service = false;
		}
		//===========
		if ($evaluate_emergency_hi) {
			$where_emergency_str = "AND `code` = " . $GLOBALS['LOG_EMGCY_HI'] . " ";
		}
		if ($evaluate_emergency_lo) {
			$where_emergency_str = "AND `code` = " . $GLOBALS['LOG_EMGCY_LO'] . " ";
		}
		if ($evaluate_emergency_hi && $evaluate_emergency_lo) {
			$where_emergency_str = "AND (`code` = " . $GLOBALS['LOG_EMGCY_HI'] . " OR `code` = " . $GLOBALS['LOG_EMGCY_LO'] . ") ";
		}
		if ($where_emergency_str != "") {

			$query_emergency = "SELECT `code` " .
				"FROM `api_log` " .
				"WHERE `source`" . $where_source_address_str . " " .
				$where_emergency_str .
				"AND (DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " . $position_report_evaluation_time . " MINUTE) <= `datetime`);";

			$result_emergency = db_query($query_emergency, __FILE__, __LINE__);
			if ((db_affected_rows($result_emergency) != 0) && ($function == "_api_position_encdg")) {
				$mandatory_in_service = false;
				$unit_data["store_unknown_unit_position_report"] = true;
			}
		}
		//===========
		$where_str = "";
		if ($mandatory_in_service) {
			$where_str = " AND (`unit_status`.`dispatch` < 3 OR `unit_status`.`dispatch` IS NULL)";
		}	
		$phone_bracket_str = "";
		$where_phone_address_str = "";
		if (preg_match("/^" . get_variable("_api_prefix_phone_encdg") . ":*/", $source)) {
			$phone_bracket_str = "(";
			$where_phone_address_str = "OR (`units`.`unit_phone` LIKE '%" . trim(substr($source, strlen(get_variable("_api_prefix_phone_encdg")) + 1)) . "%')) ";
			if (preg_match("/^" . get_variable("_api_prefix_phone_encdg") . ":*/", $source_regexp)) {
				$where_phone_address_str = "OR (`units`.`unit_phone` REGEXP '" . trim(substr($source_regexp, strlen(get_variable("_api_prefix_phone_encdg")) + 1)) . "')) ";
			}
		}

		$query = "SELECT `units`.`id`, " .
			"`units`.`handle`, " .
			"`units`.`name`, " .
			"`units`.`unit_status_id`, " .
			"`unit_status`.`dispatch` " .
			"FROM `units` " .
			"LEFT JOIN `unit_status` ON `units`.`unit_status_id` = `unit_status`.`id` " .
			"WHERE " . $phone_bracket_str . "(`units`.`remote_data_services`" . $where_source_address_str . ") " .
			$where_phone_address_str .
			$where_str . " " .
			"ORDER BY `units`.`id` ASC;";

		$result = db_query($query, __FILE__, __LINE__);
		$num_rows = db_num_rows($result);
		if ($num_rows > 0) {
			if ($num_rows == 1) {
				$row = stripslashes_deep(db_fetch_array($result));
				$unit_data["unit_id"] = $row['id'];
				$unit_data["unit_handle"] = $row['handle'];
				$unit_data["unit_name"] = $row['name'];
				$unit_data["unit_status"] = $row['unit_status_id'];
				$unit_data["unit_dispatchable"] = $row['dispatch'];
			} else {
				if (
					($evaluate_emergency_hi && ($function == "_api_emgcy_hi_encdg")) ||
					($evaluate_emergency_lo && ($function == "_api_emgcy_lo_encdg")) ||
					($unit_data["store_unknown_unit_position_report"] && ($function == "_api_position_encdg"))
				) {
					while ($row = stripslashes_deep(db_fetch_array($result))) {
						if ($row['dispatch'] < 3) {
							$unit_data["unit_id"] = $row['id'];
							$unit_data["unit_handle"] = $row['handle'];
							$unit_data["unit_name"] = $row['name'];
							$unit_data["unit_status"] = $row['unit_status_id'];
							$unit_data["unit_dispatchable"] = $row['dispatch'];
							break;
						}
					}
				} else {
					$unit_data["multiple_address_error"] = $unit_data["multiple_address_error"] . get_text("Associated data service address multiple");
					while ($row = stripslashes_deep(db_fetch_array($result))) {		
						$unit_data["multiple_address_error"] = $unit_data["multiple_address_error"] . " #" . $row['id']. " " . $row['handle'];
					}
				}
			}
		} else {
			$unit_data["unit_handle"] = $source;
		}
	}
	return $unit_data;
}

function do_unit($unit_id, $status_id, $datetime, $lat, $lon) {

	$query = "UPDATE `units` " .
		"SET `unit_status_id` = " . $status_id . ", " .
		"`updated`='" . $datetime . "', " .
		"`status_updated` = '" . $datetime . "', " .
		"`user_id` = " . get_variable("_api_user_id") . " " .
		"WHERE `id` = " . $unit_id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	if ($lat) {
		do_lat_lng($unit_id, $lat, $lon);
	}
}

function do_assigns($unit_id, $assign_id, $call_progression_column, $datetime, $lat, $lon) {

	$query = "UPDATE `assigns` " .
		"SET `updated` = '" . $datetime . "', " .
		"`" . $call_progression_column . "` = '" . $datetime . "', " .
		"`user_id` = " . get_variable("_api_user_id") . ", " .
		"`progession_changed` = 'true' " .
		"WHERE `id` = " . $assign_id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	if ($lat) {
		do_lat_lng($unit_id, $lat, $lon);
	}
}

function do_lat_lng($unit_id, $lat, $lon) {

	$query = "UPDATE `units` " .
		"SET `lat` = " . $lat . ", " .
		"`lng` = '" . $lon . "' " .
		"WHERE `id` = " . $unit_id . ";";

	$result = db_query($query, __FILE__, __LINE__);
}

function get_assign_id($unit_id) {
	return "";
}

function do_receipt_message($unit_id) {	
	$code = 0;
	$assign_data = get_assigns($unit_id, 0);
	if ($assign_data[0] != false) {
		$code = $assign_data[2];
	} else {

		$query = "SELECT `units`.`unit_status_id`, " .
			"`unit_status`.`dispatch`" .
			"FROM `units` " .
			"LEFT JOIN `unit_status` ON `units`.`unit_status_id` = `unit_status`.`id` " .
			"WHERE `units`.`id` = " . $unit_id . ";";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_num_rows($result) > 0) {
			$row = stripslashes_deep(db_fetch_array($result));
			switch ($row['unit_status_id']) {
			case get_variable("_api_clr_stat"):
				$code = $GLOBALS['LOG_CALL_CLEAR'];
				break;
			case get_variable("_api_quat_stat"):
				$code = $GLOBALS['LOG_UNIT_TO_QUARTERS'];
				break;
			case get_variable("_api_off_duty_stat"):
				$code = $GLOBALS['LOG_UNIT_NO_SERVICE'];
				break;
			default:
				switch ($row['dispatch']) {
				case 0:
					$code = $GLOBALS['LOG_CALL_CLEAR'];
					break;
				case 1:
					$code = $GLOBALS['LOG_UNIT_TO_QUARTERS'];
					break;
				case 2:
					$code = $GLOBALS['LOG_UNIT_NO_SERVICE'];
					break;
				default:
				}
			}
		}
	}
	$message_array = get_receipt_message($code);
	if ($message_array["code"] != "") {		
		do_api_infomessage($unit_id, $message_array["code"], $message_array["text"]);
	}
}

function do_api_infomessage($unit_id, $code, $text = "") {
	$destination_prefix = "";
	$report_channels = array (
		get_variable("_api_prefix_reporting_channel_1_encdg"),
		get_variable("_api_prefix_reporting_channel_2_encdg"),
		get_variable("_api_prefix_reporting_channel_3_encdg"),
		get_variable("_api_prefix_reporting_channel_4_encdg"),
		get_variable("_api_prefix_reporting_channel_5_encdg"),
		get_variable("_api_prefix_phone_encdg")
	);

	$query = "SELECT `remote_data_services`, " .
		"`unit_phone` " .
		"FROM `units` " .
		"WHERE `id` = " . $unit_id . " " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_assoc($result));
	$addresses = get_units_addresses($row['remote_data_services'], $row['unit_phone'], "");
	foreach ($report_channels as $destination_prefix) {
		if (array_key_exists($destination_prefix, $addresses)) {
			$batch_start_stop_settings = explode(",", get_variable("_api_batch_start_stop_setng"));
			$batch_start_setting = trim($batch_start_stop_settings[0]);
			$batch_stop_setting = trim($batch_start_stop_settings[1]);
			if ((count($addresses[$destination_prefix]) > 1) && ($batch_start_setting != "") && ($batch_stop_setting != "")) {
				do_api_message("", $destination_prefix, $batch_start_setting, "", "", "");
			}
			if (count($addresses[$destination_prefix]) > 0) {
				foreach ($addresses[$destination_prefix] as $key => $value) {
					do_api_message(get_assign_id($unit_id), $value, $code, $text, "", "");
				}
			}
			if ((count($addresses[$destination_prefix]) > 1) && ($batch_start_setting != "") && ($batch_stop_setting != "")) {
				do_api_message("", $destination_prefix, $batch_stop_setting, "", "", "");
			}
		}
	}
}

function get_receipt_message($status) {
	$mode = 0;
	$message_id = 0;
	$receipt ="";
	$return_array = array ();
	$return_array["code"] = "";
	$return_array["text"] = "";
	$return_array["lat"] = "";
	$return_array["lng"] = "";
	switch ($status) {
	case $GLOBALS['LOG_CALL_REQ']:
	case $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']:
	case $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']:
	case $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET']:
	case $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']:
		$return_array["code"] = get_variable("_api_callreq_encdg");
		$mode = get_variable("_api_callreq_repl");
		$receipt = get_variable("_api_callreq_rece");
		$message_id = get_variable("_api_callreq_mess");
		break;
	case $GLOBALS['LOG_CALL_MANACKN']:
		$return_array["code"] = get_variable("_api_manackn_encdg");
		$mode = get_variable("_api_manackn_repl");
		$receipt = get_variable("_api_manackn_rece");
		$message_id = get_variable("_api_manackn_mess");
		break;
	case $GLOBALS['LOG_EMGCY_LO']:
		$return_array["code"] = get_variable("_api_emgcy_lo_encdg");
		$mode = get_variable("_api_emgcy_lo_repl");
		$receipt = get_variable("_api_emgcy_lo_rece");
		$message_id = get_variable("_api_emgcy_lo_mess");
		break;
	case $GLOBALS['LOG_EMGCY_HI']:
		$return_array["code"] = get_variable("_api_emgcy_hi_encdg");
		$mode = get_variable("_api_emgcy_hi_repl");
		$receipt = get_variable("_api_emgcy_hi_rece");
		$message_id = get_variable("_api_emgcy_hi_mess");
		break;
	case $GLOBALS['LOG_CALL_DISPATCHED']:
		$return_array["lat"] = "0.99999";	//lat holen
		$return_array["lng"] = "0.99999";	//lon holen
		$return_array["code"] = get_variable("_api_disp_encdg");
		$mode = get_variable("_api_disp_repl");
		$receipt = get_variable("_api_disp_rece");
		$message_id = get_variable("_api_disp_mess");
		break;
	case $GLOBALS['LOG_CALL_RESPONDING']:
		$return_array["code"] = get_variable("_api_resp_encdg");
		$mode = get_variable("_api_resp_repl");
		$receipt = get_variable("_api_resp_rece");
		$message_id = get_variable("_api_resp_mess");
		break;
	case $GLOBALS['LOG_CALL_ON_SCENE']:
		$return_array["code"] = get_variable("_api_onsc_encdg");
		$mode = get_variable("_api_onsc_repl");
		$receipt = get_variable("_api_onsc_rece");
		$message_id = get_variable("_api_onsc_mess");
		break;
	case $GLOBALS['LOG_CALL_FACILITY_ENROUTE']:
		$return_array["code"] = get_variable("_api_fcen_encdg");
		$mode = get_variable("_api_fcen_repl");
		$receipt = get_variable("_api_fcen_rece");
		$message_id = get_variable("_api_fcen_mess");
		break;
	case $GLOBALS['LOG_CALL_FACILITY_ARRIVED']:
		$return_array["code"] = get_variable("_api_fcar_encdg");
		$mode = get_variable("_api_fcar_repl");
		$receipt = get_variable("_api_fcar_rece");
		$message_id = get_variable("_api_fcar_mess");
		break;
	case $GLOBALS['LOG_CALL_CLEAR']:
		$return_array["code"] = get_variable("_api_clr_encdg");
		$mode = get_variable("_api_clr_repl");
		$receipt = get_variable("_api_clr_rece");
		$message_id = get_variable("_api_clr_mess");
		break;
	case $GLOBALS['LOG_UNIT_TO_QUARTERS']:
		$return_array["code"] = get_variable("_api_quat_encdg");
		$mode = get_variable("_api_quat_repl");
		$receipt = get_variable("_api_quat_rece");
		$message_id = get_variable("_api_quat_mess");
		break;
	case $GLOBALS['LOG_UNIT_NO_SERVICE']:
		$return_array["code"] = get_variable("_api_off_duty_encdg");
		$mode = get_variable("_api_off_duty_repl");
		$receipt = get_variable("_api_off_duty_rece");
		$message_id = get_variable("_api_off_duty_mess");
		break;
	default:
	}
	switch ($mode) {
	case 0:
		$return_array["code"] = "";
		break;
	case 1:
		break;
	case 2:
		$return_array["text"] = $receipt;
		break;
	case 3:

		$query = "SELECT `id`, " .
			"`text`, " .
			"`code`, " .
			"`report_channels` " .
			"FROM `textblocks` " .
			"WHERE `id` = " . $message_id . ";";

		$result = db_query($query, __FILE__, __LINE__);
		$row = stripslashes_deep(db_fetch_assoc($result));
		if (db_num_rows($result) > 0) {
			if ($row['code'] != "") {
				$return_array["code"] = $row['code'];
			} else {
				$return_array["code"] = get_variable("_api_message_encdg");
			}
			if ($row['text'] != "") {
				$return_array["text"] = $row['text'];
			}
		}
		break;
	case 4:
		$return_array["code"] = "";
		break;
	case 5:

		$query = "SELECT `id`, " .
			"`text`, " .
			"`code`, " .
			"`report_channels` " .
			"FROM `textblocks` " .
			"WHERE `id` = " . $message_id . ";";

		$result = db_query($query, __FILE__, __LINE__);
		$row = stripslashes_deep(db_fetch_assoc($result));
		if (db_num_rows($result) > 0) {
			if ($row['code'] != "") {
				$return_array["code"] = $row['code'];
			} else {
				$return_array["code"] = get_variable("_api_message_encdg");
			}
			if ($row['text'] != "") {
				$return_array["text"] = $row['text'];
			}
		}
		break;
	case 6:
		break;
	default:
	}
	return $return_array;
}

function do_api_log($datetime, $source, $source_regexp, $unit_id, $destination, $destination_alias, $audio_link, $code, $text, $lat, $lon, $cleared = false) {
	$text = preg_replace("/\s+/", " ", $text);
	$cleared_column_part = "";
	$cleared_data_part = "";
	if ($cleared == true) {
		$cleared_column_part = " `cleared_user_id`, `cleared_datetime`,";
		$cleared_data_part = "'" . get_variable("_api_user_id") . "', '" . $datetime . "', ";
	}
	if ($lat == "") {
		$lat = 0.999999;
	}
	if ($lon == "") {
		$lon = 0.999999;
	}
	$query = "INSERT INTO `api_log` (`datetime`, `source`, `source_regexp`, `unit_id`, " .
		"`destination`, `destination_alias`, `audio_link`, `code`, " .
		"`text`, `lat`, `lng`," . $cleared_column_part  . " " .
		"`host`) " . "
		VALUES ('" . $datetime . "', '" . $source . "', '" . $source_regexp . "', " . intval($unit_id) . ", '" .
		$destination . "', '" . $destination_alias . "', '" . $audio_link . "', " . $code . ", '" .
		$text . "', " . floatval($lat) . ", " . floatval($lon) . ", " . $cleared_data_part .
		"'" . $_SERVER['REMOTE_ADDR'] . "');";

	$result = db_query($query, __FILE__, __LINE__);
}

function get_status_description($status_id) {

	$query_un_status = "SELECT `status_name`, " .
		"`description` " .
		"FROM `unit_status` " .
		"WHERE `id` = " . $status_id . ";";

	$result_un_status = db_query($query_un_status, __FILE__, __LINE__);
	$row_un_status = stripslashes_deep(db_fetch_assoc($result_un_status));
	$un_status_upd_val = remove_nls($row_un_status['status_name'] . ", " . $row_un_status['description']);
	return $un_status_upd_val;
}

function set_current_radio($source, $destination) {

	$query = "UPDATE `users` " .
		"SET `current_radio` = '" . $source . "'  " .
		"WHERE `id` = " . $destination . ";";

	db_query($query, __FILE__, __LINE__);
}

function do_log_connection_changed($api_type, $availability_array, $device_text = false) {
	$text = "";
	$datetime = $availability_array['timestamp_current_state'];
	if ($device_text) {
		$code = $GLOBALS['LOG_API_DEVICE_TEXT'];
		$datetime = mysql_datetime();
		$text = $availability_array['text'];
	} else {
		if ($availability_array['available'] == "true") {
			$code = $GLOBALS['LOG_API_CONNECTED'];
		} else {
			$code = $GLOBALS['LOG_API_DISCONNECTED'];
		}
	}
	do_api_log($datetime, "", "", "", $api_type, "", "", $code, $text, "", "", true);
}

function set_api_availability($api_type = "api", $available = "true", $code = "", $text = "") {
	$connection_test_array = get_connection_test_configuration();
	$availability_array = get_api_availability($api_type);
	$setting_name = "_api_status";
	if ($api_type != "api") {
		$setting_name = "_api_phone_status";
	}
	if ($code == "") {
		if ($availability_array['available'] != "null") {
			if ($available != $availability_array['available']) {
				
				$query = "UPDATE `settings` " .
					"SET `value` = '" . $available . ";" .
					mysql_datetime() . ";" .
					mysql_datetime() . ";;'" .
					"WHERE `name` = '" . $setting_name . "';";
			
				db_query($query, __FILE__, __LINE__);
				$availability_array['available'] = $available;
				$availability_array['timestamp_current_state'] = mysql_datetime();
				$availability_array['timestamp_last_retry'] = mysql_datetime();
				if ($available == "true") {
					do_log_connection_changed($api_type, $availability_array, false);
				}
				if ($available == "false") {
					do_log_connection_changed($api_type, $availability_array, false);
				}
			} else {

				$query = "UPDATE `settings` " .
					"SET `value` = '" . $available . ";" .
					$availability_array['timestamp_current_state'] . ";" .
					mysql_datetime() . ";" .
					$availability_array['code'] . ";" .
					$availability_array['text'] . "'" .
					"WHERE `name` = '" . $setting_name . "';";

				db_query($query, __FILE__, __LINE__);
				$availability_array['timestamp_last_retry'] = mysql_datetime();
			}
		}
	} else {
		switch ($code) {
		case $connection_test_array['source_success_code']:
			$code = "success";
			break;
		case $connection_test_array['source_warning_code']:
			$code = "warning";
			break;
		case $connection_test_array['source_error_code']:
			$code = "error";
			break;
		default:
			$code = "";
		}
		if (($code != "") || (($code == "") && ($availability_array['available'] != "true"))) {
			$availability_array['available'] = "true";
			$current_state = $availability_array['timestamp_current_state'];
			$last_retry = $availability_array['timestamp_last_retry'];
			if ($current_state == "2017-01-01 00:00:00") {
			$current_state = $last_retry = mysql_datetime();
			}

			$query = "UPDATE `settings` " .
				"SET `value` = '" . $availability_array['available'] . ";" .
				$current_state . ";" .
				$last_retry . ";" .
				$code . ";" .
				$text . "'" .
				" WHERE `name` = '" . $setting_name . "';";
			
			db_query($query, __FILE__, __LINE__);
			$availability_array['code'] = $code;
			$availability_array['text'] = $text;
			if (($code == "success") && ($availability_array['available'] != "null")) {
				do_log_connection_changed($api_type, $availability_array, true);
			}
			if ((($code == "warning") || ($code == "error") || ($code == "")) && ($availability_array['available'] != "null")) {
				do_log_connection_changed($api_type, $availability_array, true);
			}
		}
	}
}

function get_api_availability($api_type = "api") {
	$current_state_array = array ();
	$return_array = array ();
	$return_array['available'] = "null";
	$setting_name = "_api_status";
	$api_connection = "";
	switch ($api_type) {
		case "api":
			$api_connection = get_variable("_api_destination_host");
			break;
		case "api_test":
			$api_connection = get_variable("_api_destination_host");
			$return_array['available'] = "true";
			break;
		case "phone":
			$api_connection = get_variable("_api_phone_host");
			$setting_name = "_api_phone_status";
			break;
		case "phone_test":
			$api_connection = get_variable("_api_phone_host");
			$setting_name = "_api_phone_status";
			$return_array['available'] = "true";
			break;
		default:
	}

	$query = "SELECT `value` " .
		"FROM `settings` " .
		"WHERE `name` = '" . $setting_name . "';";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_assoc($result));
	unset ($result);
	$current_state_array = explode(";", $row['value']);
	if ($return_array['available'] != "true") {
		if ($api_connection != "") {
			$return_array['available'] = $current_state_array[0];
		} else {
			$return_array['available'] = "null";
		}
	}
	$return_array['timestamp_current_state'] = $current_state_array[1];
	$return_array['timestamp_last_retry'] = $current_state_array[2];
	$return_array['code'] = $current_state_array[3];
	$return_array['text'] = "";
	foreach ($current_state_array as $key => $value) {
		if ($key >= 4) {
			$return_array['text'] .= remove_nls($value);
		}
	}
	unset ($key, $value);
	return $return_array;
}

function do_api_connection($api_type = "api", $source = "", $destination = "", $code = "", $text_str = "", $lat_lon_str = "", $test = false) {
	require_once ("./incs/install.inc.php");
	$connection_host = "";
	$connection_parameter = array ();
	switch ($api_type) {
	case "api":
	case "api_test":
		$connection_host = get_variable("_api_destination_host");
		break;
	case "phone":
	case "phone_test":
		$connection_host = get_variable("_api_phone_host");
		break;
	default:
	}
	$connection_parameter = parse_url($connection_host);
	if (!isset ($connection_parameter["scheme"])) {
		$connection_parameter = parse_url("http://" . $connection_host);
	}
	$host = "";
	if (isset ($connection_parameter["host"])) {
		$host = $connection_parameter["host"];
	}
	$port = 80;
	if (isset ($connection_parameter["port"])) {
		$port = intval($connection_parameter["port"]);
	}
	$path = "";
	if (isset ($connection_parameter["path"])) {
		$path = $connection_parameter["path"];
	}
	$url = $path . "?password=" . urlencode(get_variable("_api_destination_password")) .
		"&source=" . urlencode($source) .
		"&destination=" . urlencode($destination) .
		"&code=" . urlencode($code) .
		$text_str .
		$lat_lon_str;
	$timeout = 10;
	$data = "";
	$errno = "";
	$errstr = "";
	$result_data = array ();
	$result_data[0] = "DISABLED";
	$result_data[1] = "";
	$api_availability_array = get_api_availability($api_type);
	if ($api_availability_array['available'] == "true") {
		$socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
		if ($socket) {
			$request  = "GET " . $url . " HTTP/1.1\r\n";
			$request .= "Host: " . $host . "\r\n";
			$request .= "User-Agent: OpenTacticalDispatcher (" . php_uname() . "; ";
			$request .= get_variable("_locale") . "; rv: " . get_version() . ")\r\n";	
			$request .= "Connection: Close\r\n\r\n";
			fwrite($socket, $request);
			while (!feof($socket)) {
				$data .= fgets($socket, 128);
			}
			fclose($socket);
			$result_data[0] = "OK";
			$result_data[1] = $data;
		} else {
			$error_log = $errno . ": " . $errstr . "\r\n";
			$error_log .= $host;
			if ($port != "") {
				$error_log .= ":" . $port;
			}
			$error_log .= $url;
			if (!$test) {
				@error_log("ERROR: " . $error_log);
			}
			$result_data[0] = "ERROR";
			$result_data[1] = $error_log;
		}
	}
	return $result_data;
}

function do_api_connection_test($periodic = "false") {
	$connection_test_array = get_connection_test_configuration();
	$availability_array = get_api_availability("api");
	$wait_time = round($connection_test_array['retry_time'] / 1.2);
	if ($availability_array['available'] == "true") {
		$wait_time = round($connection_test_array['keepalive_time'] / 1.2);
	}
	$availability_array = get_api_availability("api_test");
	if (
		((($periodic == "true") && ((date("U") - $wait_time) > strtotime($availability_array['timestamp_last_retry']))) ||
		($periodic == "false")) &&
		(get_variable("_api_destination_host") != "")
	) {
		$result_array = do_api_connection("api_test", "", "", $connection_test_array['code'], "", "", true);
		if (($result_array[0] == "OK") &&	
			(($connection_test_array['response_code'] == "") ||
			($connection_test_array['response_code'] == substr(trim($result_array[1]), 0, strlen($connection_test_array['response_code']))))
		) {
			set_api_availability("api", "true", "", "");
		} else {
			set_api_availability("api", "false", "", "");
		}
	}
	$availability_array = get_api_availability("phone");
	$wait_time = round($connection_test_array['retry_time'] / 1.2);
	if ($availability_array['available'] == "true") {
		$wait_time = round($connection_test_array['keepalive_time'] / 1.2);
	}
	$availability_array = get_api_availability("phone_test");
	if (
		((($periodic == "true") && ((date("U") - $wait_time) > strtotime($availability_array['timestamp_last_retry']))) ||
		($periodic == "false")) &&
		(get_variable("_api_phone_host") != "")
	) {
		$result_array = do_api_connection("phone_test", "", "", $connection_test_array['code'], "", "", true);
		if (($result_array[0] == "OK") &&
			(($connection_test_array['response_code'] == "") ||
			($connection_test_array['response_code'] == substr(trim($result_array[1]), 0, strlen($connection_test_array['response_code']))))
		) {
			set_api_availability("phone", "true", "", "");
		} else {
			set_api_availability("phone", "false", "", "");
		}
	}
}

function do_api_message($source = "", $destination = "", $code = "", $text = "", $lat = "", $lon = "") {
	$api_type = "api";
	$is_phone_array = split_api_receiver_str($destination);
	if ((get_variable("_api_prefix_phone_encdg") == $is_phone_array[0]) && (get_variable("_api_phone_host") != "false")) {
		$api_type = "phone";
	}
	$text_str = "";
	if ($text != "") {
		$text_str = "&text=" . urlencode($text);
	}
	$lat_lon_str = "";
	if (($lat != "") && ($lon != "")) {
		$lat_lon_str = "&lat=" . urlencode($lat) . "&lon=" . urlencode($lon);
	}
	$result_array = do_api_connection($api_type, $source, $destination, $code, $text_str, $lat_lon_str, false);
	if ($result_array[0] != "OK") {
		set_api_availability($api_type, "false", "", "");
	}
	return $result_array;
}
?>