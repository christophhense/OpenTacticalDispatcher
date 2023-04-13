<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/api.inc.php");

$datetime_now = mysql_datetime();
$response = "";
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
if (isset ($_POST['function'])) {
	$function = $_POST['function'];
}
if (is_guest() || is_operator() || is_admin() || is_super()) {
	switch ($function) {
	case "screen":
		if (isset ($_GET['reset_button'])) {
			foreach ($_SESSION['screens'] as $key => $value) {
				if ($key != $_GET['screen_id']) {
					$_SESSION['reset_button'][$key] = $_GET['reset_button'];
				}
			}
		}
		print_r($_SESSION['reset_button']);
		break;
	case "timeout":
		$_SESSION['timeout'] = $_GET['value'];
		$response = "timeout_disabled";
		break;
	case "viewed_groups":
		set_session_expire_time("on");
		$_SESSION['viewed_groups'] = $_GET['value'];
		break;
	case "situation_type":
		set_session_expire_time("on");
		if (isset ($_GET['screen_id'])) {
			$_SESSION["screen_id_" . $_GET['screen_id']]['situation_type'] = $_GET['value'];
		}
		break;
	case "closed_interval_start":
		set_session_expire_time("on");
		if (isset ($_GET['screen_id'])) {
			$_SESSION["screen_id_" . $_GET['screen_id']]['closed_interval_start'] = $_GET['value'];
		}
		break;
	case "closed_interval_end":
		set_session_expire_time("on");
		if (isset ($_GET['screen_id'])) {
			$_SESSION["screen_id_" . $_GET['screen_id']]['closed_interval_end'] = $_GET['value'];
		}
		break;
	case "day_night":
		set_session_expire_time("on");
		$_SESSION['day_night'] = $_GET['value'];
		$response = $_SESSION['day_night'];
		break;
	case "api_connection_test":
		do_api_connection_test($_GET['periodic']);
		break;
	default:
	}
}
if (is_operator() || is_admin() || is_super()) {
	$do_receipt = false;
	$unit_id = 0;
	switch ($function) {
	case "unit_status":
		set_session_expire_time("on");
		$subscribed = false;
		$unsubscribed = true;

		$query_old_status = "SELECT `u_s`.`dispatch` " .
			"FROM `units` " .
			"LEFT JOIN `unit_status` `u_s` ON `units`.`unit_status_id` = `u_s`.`id` " .
			"WHERE `units`.`id` = " . $_GET['frm_unit_id'] . " " .
			"LIMIT 1;";

		$result_old_status = db_query($query_old_status, __FILE__, __LINE__);
		if (db_num_rows($result_old_status) > 0) {
			$row_old_status = stripslashes_deep(db_fetch_assoc($result_old_status));
			if ($row_old_status['dispatch'] < 3) {
				$subscribed = true;
				$unsubscribed = false;
			}
		}

		$query = "UPDATE `units` SET `unit_status_id` = " .
			$_GET['frm_status_id'] . ", " .
			"`updated` = " . quote_smart($datetime_now) . ", " .
			"`status_updated` = " . quote_smart($datetime_now) . ", " .
			"`user_id` = " . $_SESSION['user_id'] . " " .
			"WHERE `id` = " . $_GET['frm_unit_id'] . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);

		$query_un_status = "SELECT `status_name`, " .
			"`description`, " .
			"`dispatch` " .
			"FROM `unit_status` " .
			"WHERE `id` = " . $_GET['frm_status_id'] . ";";

		$result_un_status = db_query($query_un_status, __FILE__, __LINE__);
		$row_un_status = stripslashes_deep(db_fetch_assoc($result_un_status));
		$un_status_upd_val = $row_un_status['status_name'] . ", " . $row_un_status['description'];
		do_log($GLOBALS['LOG_UNIT_STATUS'], 0, $_GET['frm_unit_id'], $un_status_upd_val);
		$subscribe_value = "";
		$subscr_unsubscr_settings = explode(",", get_variable("_api_subscr_unsubscr_setng"));
		if (($row_un_status['dispatch'] < 3) && ($unsubscribed)) {
			$subscribe_value = trim($subscr_unsubscr_settings[0]);
		}
		if (($row_un_status['dispatch'] >= 3) && ($subscribed)) {
			$subscribe_value = trim($subscr_unsubscr_settings[1]);
		}
		if ($subscribe_value != "") {
			do_api_infomessage($_GET['frm_unit_id'], $subscribe_value, "");
		}
		if ($row_un_status['dispatch'] < 3) {
			$unit_id = $_GET['frm_unit_id'];
			$do_receipt = true;
		}
		$response = remove_nls($row_un_status['description']);
		break;
	case "facility_status":
		set_session_expire_time("on");

		$query = "UPDATE `facilities` SET `facility_status_id` = " .
			$_GET['frm_status_id'] . ", " .
			"`updated` = " . quote_smart($datetime_now) . ", " .
			"`user_id` = " . $_SESSION['user_id'] . " " .
			"WHERE `id` = " . $_GET['frm_facility_id'] . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);

		$query_fac_status = "SELECT `status_name`, " .
			"`description` " .
			"FROM `facility_status` " .
			"WHERE `id` = " . $_GET['frm_status_id'] . ";";

		$result_fac_status = db_query($query_fac_status, __FILE__, __LINE__);
		$row_fac_status = stripslashes_deep(db_fetch_assoc($result_fac_status));
		$fac_status_upd_val = $row_fac_status['status_name'] . ", " . $row_fac_status['description'];
		do_log($GLOBALS['LOG_FACILITY_STATUS'], 0, 0, $fac_status_upd_val, $_GET['frm_facility_id']);
		$response = remove_nls($row_fac_status['description']);
		break;
	case "call_progression":
		set_session_expire_time("on");
		$log_text = "";
		if (isset($_POST['call_progression_datetime'])) {
			$datetime_now = $_POST['call_progression_datetime'];
			$log_text = get_text("Time taken on in disposition") . ": " . date(get_variable("date_format"), strtotime($datetime_now));
		}

		$query = "SELECT * " .
			"FROM `assigns` " .
			"WHERE `id` = " . $_POST['assign_id'] . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$row_temp = db_fetch_assoc($result);
		$frm_tick = $row_temp['ticket_id'];
		$frm_unit = $row_temp['unit_id'];
		$date_part="";
		if ($_POST['frm_callprogression'] == "frm_dispatched") {
			$date_part .= "`dispatched` = " . quote_smart($datetime_now) . ", ";
			do_log($GLOBALS['LOG_CALL_DISPATCHED'], $frm_tick, $frm_unit);
		}
		if ($_POST['frm_callprogression'] == "frm_responding") {
			$date_part .= "`responding` = " . quote_smart($datetime_now) . ", ";
			do_log($GLOBALS['LOG_CALL_RESPONDING'], $frm_tick, $frm_unit, $log_text);
		}
		if ($_POST['frm_callprogression'] == "frm_on_scene") {
			$date_part .= "`on_scene` = ". quote_smart($datetime_now) . ", ";
			do_log($GLOBALS['LOG_CALL_ON_SCENE'], $frm_tick, $frm_unit, $log_text);
		}
		if ($_POST['frm_callprogression'] == "frm_clear") {
			$date_part .= "`clear` = " . quote_smart($datetime_now) . ", ";
			do_log($GLOBALS['LOG_CALL_CLEAR'], $frm_tick, $frm_unit);
			$auto_dispatch_settings = explode(",", get_variable("auto_dispatch"));
			$auto_last_assign = trim($auto_dispatch_settings[1]);
			if ($auto_last_assign > 0) {

				$query_current_assigns = "SELECT COUNT(*) as `numfound` " .
					"FROM `assigns` " .
					"WHERE `ticket_id` = (SELECT `ticket_id` " .
						"FROM `assigns` " .
						"WHERE `id` = " . $_POST['assign_id'] . ") " .
						"AND `clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00';";

				$result_current_assigns	= db_query($query_current_assigns, __FILE__, __LINE__);

				if (db_num_rows($result_current_assigns) != 0) {
					$row_current_assigns = stripslashes_deep(db_fetch_assoc($result_current_assigns));
					if ($row_current_assigns['numfound'] == 1) {
						//=================
						$query_ticket_id = "SELECT `ticket_id` " .
							"FROM `assigns` " .
							"WHERE `id` = " . $_POST['assign_id'] . ";";

						$row_ticket_id = stripslashes_deep(db_fetch_assoc(db_query($query_ticket_id, __FILE__, __LINE__)));
						//=================
						switch ($auto_last_assign) {
						case 1:
							$response .= "ticket_edit.php?ticket_id=" . $row_ticket_id['ticket_id'];
							break;
						case 2:
							$response .= "ticket_close.php?ticket_id=" . $row_ticket_id['ticket_id'];
							break;
						default:
						}
					}
				}
			}
		}
		if ($_POST['frm_callprogression'] == "frm_u2fenr") {
			$date_part .= "`u2fenr` = " . quote_smart($datetime_now) . ", ";
			do_log($GLOBALS['LOG_CALL_FACILITY_ENROUTE'], $frm_tick, $frm_unit, $log_text);
		}
		if ($_POST['frm_callprogression'] == "frm_u2farr") {
			$date_part .= "`u2farr` = " . quote_smart($datetime_now) . ", ";
			do_log($GLOBALS['LOG_CALL_FACILITY_ARRIVED'], $frm_tick, $frm_unit, $log_text);
		}
		$date_part .= "`user_id` = " . $_SESSION['user_id'] . ", ";
		$date_part .= substr($date_part, 0, -2);

		$query = "UPDATE `assigns` " .
			"SET `updated` = " . quote_smart($datetime_now) .", " . $date_part . ", " .
			"`progession_changed` = 'true' " .
			"WHERE `id` = " . $_POST['assign_id'] . " " .
			"LIMIT 1;";

		$result	= db_query($query, __FILE__, __LINE__);

		set_unit_updated($_POST['assign_id']);
		$unit_id = $frm_unit;
		$do_receipt = true;
		break;
	case "assign_reset":
		set_session_expire_time("on");

		$query = "UPDATE `assigns` " .
			"SET `responding` = NULL, " .
			"`on_scene` = NULL, " .
			"`u2fenr` = NULL, " .
			"`u2farr` = NULL, " .
			"`clear` = NULL, " .
			"`progession_changed` = 'false', " .
			"`updated` = '" . $datetime_now . "', " .
			"`user_id` = " . $_SESSION['user_id'] . " " .
			"WHERE `id` = " . $_POST['assign_id'] . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);

		$query = "SELECT * " .
			"FROM `assigns` " .
			"WHERE `id` = " . $_POST['assign_id'] . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$row = db_fetch_assoc($result);
		do_log($GLOBALS['LOG_CALL_RESET'], $row['ticket_id'], $row['unit_id']);
		set_unit_updated($_POST['assign_id']);
		unset ($result);
		$unit_id = $row['unit_id'];
		$do_receipt = true;
		break;
	case "assign_delete":
		set_session_expire_time("on");

		$query = "SELECT * " .
			"FROM `assigns` " .
			"WHERE `id` = " . $_POST['assign_id'] . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$row = db_fetch_assoc($result);													// collect for log
		do_log($GLOBALS['LOG_CALL_DELETED'], $row['ticket_id'], $row['unit_id']);
		set_unit_updated($_POST['assign_id']);

		$query = "DELETE FROM `assigns` " .
			"WHERE `id` = " . $_POST['assign_id'] . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		unset ($result);
		$unit_id = $row['unit_id'];
		$do_receipt = true;
		break;
	default:
	}
	if ($do_receipt && ($unit_id != 0)) {
		do_receipt_message($unit_id);
	}
}
echo $response;
?>