<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/log_codes.inc.php");
require_once ("./incs/assign.inc.php");
require_once ("./incs/api.inc.php");
do_login(basename(__FILE__));
set_session_expire_time();

$back = "";
$url_back = "";
$ticket_id = 0;
$unit_id = 0;
$datetime_now = mysql_datetime();
if (isset ($_GET['ticket_id'])) {
	$ticket_id = $_GET['ticket_id'];
}
if (isset ($_GET['unit_id'])) {
	$unit_id = $_GET['unit_id'];
}
if (($ticket_id == 0) && isset ($_POST['frm_ticket_id'])) {
	$ticket_id = $_POST['frm_ticket_id'];
}
if (isset ($_GET['back'])) {
	$back = $_GET['back'];
}
if (($back == "") && isset ($_POST['back'])) {
	$back = $_POST['back'];
}
switch ($back) {
case "ticket":
	$url_back = "ticket_edit.php";
	break;
case "units":
	$url_back = "units.php";
	break;
default:
	$url_back = "situation.php";
}
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
if (($function == "") && isset ($_POST['function'])) {
	$function = $_POST['function'];
}
if ((isset ($_POST['frm_reset'])) && ($_POST['frm_reset'] == "reset")) {
	$function = "reset";
}
if ((isset ($_POST['frm_delete'])) && ($_POST['frm_delete'] == "delete")) {
	$function = "delete";
}
switch ($function) {
case "multiple":
	$background_color = get_variable("night_color");
	if ((!(isset ($_SESSION['day_night']))) || (isset ($_SESSION['day_night']) && ($_SESSION['day_night'] == "day"))) {
		$background_color = "transparent";
	}
	$json_additional_infos = array (
		"head_text" => get_text("Multidispatch - Select ticket"),
		"background-color" => $background_color
	);
	require_once ("./incs/units.inc.php");
	$result = get_units_data("WHERE `u`.`id` = " . $unit_id, "", "LIMIT 1", "");
	$row = stripslashes_deep(db_fetch_assoc($result));
	$unit_str = "<tr>" .
		"<td style=\"width: 15%;\"" . get_title_unit_str($row) . " style=\"vertical-align: middle;\">" .
		"<span class=\"label\" style=\"background-color: " . $row['background_color'] . "; " .
		"color: " . $row['text_color'] . "; font-weight: bold; font-size: 12px;\">" .
		remove_nls($row['handle']) . "</span></td>" .
		"<th" . get_help_text_str("_loca") . " style='width: 25%;'>" . get_text("Incident location") . "</th>" .
		"<th" . get_help_text_str("_incident_type") . " style='width: 15%;'>" . get_text("Incident type") . "</th>" .
		"<th" . get_help_text_str("_synop") . " style='width: 35%;'>" . get_text("Synopsis") . "</th>" .
		"<th" . get_help_text_str("_name") . " style='width: 10%;'>" . get_text("inc_name_short") . "</th>" .
	"</tr>";
	$json_assign_content = array ();
	array_push($json_assign_content, $unit_str);

	$query = "SELECT *, " .
		"`assigns`.`id` AS `assign_id`, " .
		"`assigns`.`updated` AS `assign_updated`, " .
		"`assigns`.`on_scene_facility_id` AS `assign_facility_id`, " .
		"`assigns`.`on_scene_location` AS `assign_on_scene_location`, " .
		"`assigns`.`comments` AS `assign_comments`, " .
		"`f_a_o`.`handle` AS `assign_on_scene_facility_handle`, " .
		"`assigns`.`receiving_location` AS `assign_receiving_location`, " .
		"`f_a_r`.`handle` AS `assign_rec_facility_handle`, " .
		"`t`.`location` AS `ticket_street`, " .
		"`t`.`description` AS `ticket_description`, " .
		"`t`.`phone` AS `tick_phone`, " .
		"`t`.`comments` AS `tick_comm`, " .
		"`f`.`handle` AS `fac_handle`, " .
		"`ty`.`type` AS `type`, " .
		"`ty`.`description` AS `t_des`, " .
		"`ty`.`protocol` AS `t_proto`, " .
		"UNIX_TIMESTAMP(`t`.`booked_date`) AS `booked_date` " .
		"FROM `assigns` " .
		"LEFT JOIN `tickets` `t` ON (`assigns`.`ticket_id` = `t`.`id`) " .
		"LEFT JOIN `facilities` `f` ON (`t`.`facility_id` = `f`.`id`) " .
		"LEFT JOIN `facilities` `f_a_o` ON `assigns`.`on_scene_facility_id` = `f_a_o`.`id` " .
		"LEFT JOIN `facilities` `f_a_r` ON `assigns`.`receiving_facility_id` = `f_a_r`.`id` " .
		"LEFT JOIN `incident_types` `ty` ON `t`.`incident_type_id` = `ty`.`id` " .
		"WHERE `unit_id` = " . $unit_id . " " .
		"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00') " .
		"ORDER BY `assign_id` ASC;";

	$result_assigns = db_query($query, __FILE__, __LINE__);
	$primary_disposition_hint = "<br>" . get_text("Oldest open disposition. Primary disposition for status reception and acknowledgment via the application interface.") . "<br>";
	if (db_affected_rows($result_assigns) != 0) {
		while ($row_assign = stripslashes_deep(db_fetch_array($result_assigns))) {
			$title_status_assign = get_title_ticket($row_assign);
			$severityclass = "";
			switch ($row_assign['severity']) {
				case $GLOBALS['SEVERITY_MEDIUM']:
					$severityclass = " class='severity_medium'";
					break;
				case $GLOBALS['SEVERITY_HIGH']:
					$severityclass = " class='severity_high'";
					break;
				default:
					$severityclass = " class='severity_normal'";
			}
			$assign_str = "<tr>" .
				"<td" . get_title_str($title_status_assign . $primary_disposition_hint . "<br>" . get_text("Click to edit assign") . "<br>" . get_text("Click right to set callprogression")) . ">" .
					get_status_display_str($row_assign, " onClick='edit_assign(" . $row_assign['assign_id'] . ");'", 0) .
				"</td>" .
				"<td" . $severityclass . get_title_str($title_status_assign . $primary_disposition_hint) . " onClick='edit_ticket(" . $row_assign['ticket_id'] . ");'>" .
					$row_assign['ticket_street'] . "</td> " .
					"<td" . $severityclass . get_title_type_str($row_assign) . " onClick='edit_ticket(" . $row_assign['ticket_id'] . ");'>" . $row_assign['type'] . "</td>" .
					"<td" . $severityclass . get_title_str($title_status_assign . $primary_disposition_hint) . " onClick='edit_ticket(" . $row_assign['ticket_id'] . ");'>" . $row_assign['ticket_description'] . "</td>" .
					"<td" . $severityclass . get_title_str($title_status_assign . $primary_disposition_hint) . " onClick='edit_ticket(" . $row_assign['ticket_id'] . ");'>" . $row_assign['incident_name'] . "</td>" .
				"</tr>";
			array_push($json_assign_content, $assign_str);
			$primary_disposition_hint = "";
		}
	}
	$json_array = array (
		"additional_infos" => $json_additional_infos,
		"assigns" => $json_assign_content
	);
	print json_encode($json_array);
	break;
case "reset":

	$query = "SELECT * " .
		"FROM `assigns` " .
		"WHERE `id` = " . $_POST['assign_id'] . " " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = db_fetch_assoc($result);

	$query_update = "UPDATE `assigns` " .
		"SET `dispatched` = '" . $row['datetime'] . "', " .
		"`responding` = NULL, " .
		"`on_scene` = NULL, " .
		"`u2fenr` = NULL, " .
		"`u2farr` = NULL, " .
		"`clear` = NULL, " .
		"`updated` = '" . $datetime_now . "', " .
		"`user_id` = " . $_SESSION['user_id'] . " " .
		"WHERE `id` = " . $_POST['assign_id'] . " " .
		"LIMIT 1;";

	$result = db_query($query_update, __FILE__, __LINE__);

	do_log($GLOBALS['LOG_CALL_RESET'], $row['ticket_id'], $row['unit_id']);
	set_unit_updated($_POST['assign_id']);
	unset ($result);
	do_receipt_message($row['unit_id']);
	break;
case "delete":

	$query = "SELECT * " .
		"FROM `assigns` " .
		"WHERE `id` = " . $_POST['assign_id'] . " " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = db_fetch_assoc($result);

	do_log($GLOBALS['LOG_CALL_DELETED'], $row['ticket_id'], $row['unit_id']);
	set_unit_updated($_POST['assign_id']);

	$query = "DELETE FROM `assigns` " .
		"WHERE `id` = " . $_POST['assign_id'] . " " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	unset ($result);
	do_receipt_message($row['unit_id']);
	break;
case "update":

	$as_query_old = "SELECT *, " .
		"(SELECT  COUNT(*) as `numfound` " .
		"FROM `assigns` " .
		"WHERE `assigns`.`ticket_id` = " . $ticket_id . " " .
		"AND `clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00') AS `current_assigns` " .
		"FROM `assigns` " .
		"WHERE `id` = " . $_POST['assign_id'] . " " .
		"LIMIT 1;";

	$as_result_old	= db_query($as_query_old, __FILE__, __LINE__);
	$as_row_old = stripslashes_deep(db_fetch_assoc($as_result_old));
	$miles_part = "";
	if (!empty ($_POST['frm_miles_strt'])) {
		$miles_part .= ", `start_miles` = " . quote_smart($_POST['frm_miles_strt']);
	}
	if (!empty ($_POST['frm_on_scene_miles'])) {
		$miles_part .= ", `start_miles` = " . quote_smart($_POST['frm_on_scene_miles']);
	}
	if (!empty ($_POST['frm_miles_end'])) {
		$miles_part .= ", `start_miles` = " . quote_smart($_POST['frm_miles_end']);
	}
	if (!empty ($_POST['frm_miles_tot'])) {
		$miles_part .= ", `start_miles` = " . quote_smart($_POST['frm_miles_tot']);
	}
	$dispatch_part = "";
	if (isset ($_POST['dispatched'])) {
		$dispatch_part .= "`dispatched` = " . quote_smart($_POST['dispatched']) . ", ";
	}
	if (isset ($_POST['responding'])) {
		$dispatch_part .= "`responding` = " . quote_smart($_POST['responding']) . ", ";
	}
	if (isset ($_POST['on_scene'])) {
		$dispatch_part .= "`on_scene` = " . quote_smart($_POST['on_scene']) . ", ";
	}
	if (isset ($_POST['facility_enroute'])) {
		$dispatch_part .= "`u2fenr` = " . quote_smart($_POST['facility_enroute']) . ", ";
	}
	if (isset ($_POST['facility_arrived'])) {
		$dispatch_part .= "`u2farr` = " . quote_smart($_POST['facility_arrived']) . ", ";
	}
	if (isset ($_POST['clear'])) {
		$dispatch_part .= "`clear` = " . quote_smart($_POST['clear']);
		$auto_dispatch_settings = explode(",", get_variable("auto_dispatch"));
		$auto_last_assign = trim($auto_dispatch_settings[1]);
		if ($as_row_old['current_assigns'] == 1) {
			switch ($auto_last_assign) {
			case 1:
				$url_back = "ticket_edit.php";
				break;
			case 2:
				$url_back = "ticket_close.php";
				break;
			default:
			}
		}
	} else {
		$dispatch_part = substr($dispatch_part, 0, -2);
	}
	if ($dispatch_part != "") {

		$query = "UPDATE `assigns` " .
			"SET " . $dispatch_part . " " .
			"WHERE `id` = " . $_POST['assign_id'] . " " .
			"LIMIT 1;";

		db_query($query, __FILE__, __LINE__);
	}
	$comm_part = "";
	if (isset ($_POST['frm_comments'])) {
		$comm_part = ", `comments`= " . quote_smart($_POST['frm_comments']);
	}
	$on_scene_location_part = ", `on_scene_location` = " . quote_smart($_POST['frm_on_scene_location']);
	if ((empty ($_POST['frm_on_scene_location'])) || ($_POST['frm_on_scene_facility_id'] == -1)) {
		$on_scene_location_part = ", `on_scene_location` = ''";
	}
	$facility_id_part = ", `on_scene_facility_id` = " . quote_smart($_POST['frm_on_scene_facility_id']);
	if (empty ($_POST['frm_on_scene_facility_id'])) {
		$facility_id_part = ", `on_scene_facility_id` = 0";
	}
	$receiving_location_part = ", `receiving_location` = " . quote_smart($_POST['frm_receiving_location']);
	if (empty ($_POST['frm_receiving_location'])) {
		$receiving_location_part = ", `receiving_location` = ''";
	}
	$rec_facility_id_part = ", `receiving_facility_id` = " . quote_smart($_POST['frm_receiving_facility_id']);
	if (empty ($_POST['frm_receiving_facility_id'])) {
		$rec_facility_id_part = ", `receiving_facility_id` = 0";
	}

	$query = "UPDATE `assigns` " .
		"SET `updated` = " . quote_smart($datetime_now);
	//				 `start_miles` = " . 		quote_smart($_POST['frm_miles_strt']) . ",
	//				 `on_scene_miles` = " . 		quote_smart($_POST['frm_on_scene_miles']) . ",
	//				 `end_miles` = " . 			quote_smart($_POST['frm_miles_end']) . ",
	//				 `miles` = " . 				quote_smart($_POST['frm_miles_tot']);
	$query .= $miles_part;
	$query .= $comm_part;
	$query .= $on_scene_location_part;
	$query .= $facility_id_part;
	$query .= $receiving_location_part;
	$query .= $rec_facility_id_part;
	$query .= ", `user_id` = " . $_SESSION['user_id'] . ", ";
	$query .= "`progession_changed` = 'false' ";
	$query .=  "WHERE `id` = " . $_POST['assign_id'] . " LIMIT 1";

	$result	= db_query($query, __FILE__, __LINE__);
	unset ($result);
	$do_receipt = false;
	if ((array_key_exists('dispatched_button', $_POST)) && ($as_row_old['dispatched'] != date("Y-m-d H:i:s", strtotime($_POST['dispatched'])))) {
		if (is_datetime($as_row_old['dispatched'])) {
			do_log($GLOBALS['LOG_CALL_EDIT'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], $types[$GLOBALS['LOG_CALL_DISPATCHED']] . ": " . date(get_variable("date_format"), strtotime($_POST['dispatched'])));
		} else {
			do_log($GLOBALS['LOG_CALL_DISPATCHED'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], "", "", $_POST['dispatched']);
		}
		$do_receipt = true;
	}
	if ((array_key_exists('responding_button', $_POST)) && ($as_row_old['responding'] != date("Y-m-d H:i:s", strtotime($_POST['responding'])))) {
		if (is_datetime($as_row_old['responding'])) {
			do_log($GLOBALS['LOG_CALL_EDIT'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], $types[$GLOBALS['LOG_CALL_RESPONDING']] . ": " . date(get_variable("date_format"), strtotime($_POST['responding'])));
		} else {
			do_log($GLOBALS['LOG_CALL_RESPONDING'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], "", "", $_POST['responding']);
		}
		$do_receipt = true;
	}
	if ((array_key_exists('on_scene_button', $_POST)) && ($as_row_old['on_scene'] != date("Y-m-d H:i:s", strtotime($_POST['on_scene'])))) {
		if (is_datetime($as_row_old['on_scene'])) {
			do_log($GLOBALS['LOG_CALL_EDIT'],$_POST['frm_ticket_id'], $as_row_old['unit_id'], $types[$GLOBALS['LOG_CALL_ON_SCENE']] . ": " . date(get_variable("date_format"), strtotime($_POST['on_scene'])));
		} else {
			do_log($GLOBALS['LOG_CALL_ON_SCENE'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], "", "", $_POST['on_scene']);
		}
		$do_receipt = true;
	}
	if ((array_key_exists('facility_enroute_button', $_POST)) && ($as_row_old['u2fenr'] != date("Y-m-d H:i:s", strtotime($_POST['facility_enroute'])))) {
		if (is_datetime($as_row_old['u2fenr'])) {
			do_log($GLOBALS['LOG_CALL_EDIT'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], $types[$GLOBALS['LOG_CALL_FACILITY_ENROUTE']] . ": " . date(get_variable("date_format"), strtotime($_POST['facility_enroute'])));
		} else {
			do_log($GLOBALS['LOG_CALL_FACILITY_ENROUTE'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], "", "", $_POST['facility_enroute']);
		}
		$do_receipt = true;
	}
	if ((array_key_exists('facility_arrived_button', $_POST)) && ($as_row_old['u2farr'] != date("Y-m-d H:i:s", strtotime($_POST['facility_arrived'])))) {
		if (is_datetime($as_row_old['u2farr'])) {
			do_log($GLOBALS['LOG_CALL_EDIT'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], $types[$GLOBALS['LOG_CALL_FACILITY_ARRIVED']] . ": " . date(get_variable("date_format"), strtotime($_POST['facility_arrived'])));
		} else {
			do_log($GLOBALS['LOG_CALL_FACILITY_ARRIVED'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], "", "", $_POST['facility_arrived']);
		}
		$do_receipt = true;
	}
	if ((array_key_exists('clear_button', $_POST)) && ($as_row_old['clear'] != date("Y-m-d H:i:s", strtotime($_POST['clear'])))) {
		if (is_datetime($as_row_old['clear'])) {
			do_log($GLOBALS['LOG_CALL_EDIT'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], $types[$GLOBALS['LOG_CALL_CLEAR']] . ": " . date(get_variable("date_format"), strtotime($_POST['clear'])));
		} else {
			do_log($GLOBALS['LOG_CALL_CLEAR'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], "", "", $_POST['clear']);
		}
		$do_receipt = true;
	}
	if ((array_key_exists('frm_comments', $_POST)) && ($as_row_old['comments'] != $_POST['frm_comments'])) {
		do_log($GLOBALS['LOG_CALL_EDIT'], $_POST['frm_ticket_id'], $as_row_old['unit_id'], get_text("Comments") . ": " . remove_nls($_POST['frm_comments']));
	}
	if ((array_key_exists('frm_on_scene_location', $_POST)) && ($as_row_old['on_scene_location'] != $_POST['frm_on_scene_location'])) {
		$facility_on_scene_str = "";
		if (isset ($_POST['frm_on_scene_facility_id'])) {
			$query_facility_on_scene = "SELECT * FROM `facilities` WHERE `id` = " . $_POST['frm_on_scene_facility_id'];
			$result_facility_on_scene = db_query($query_facility_on_scene, __FILE__, __LINE__);
			if (db_num_rows($result_facility_on_scene) > 0) {
				$row_facility_on_scene = stripslashes_deep(db_fetch_assoc($result_facility_on_scene));
				$facility_on_scene_str = $row_facility_on_scene['name'] . ", ";
			}
		}
		if (($as_row_old['on_scene_location'] == "") && ((isset ($_POST['frm_on_scene_facility_id'])) && ($_POST['frm_on_scene_facility_id'] != -1))) {
			do_log($GLOBALS['LOG_CALL_FACILITY_SET'], $as_row_old['ticket_id'], $as_row_old['unit_id'], get_text("On-Scene location") .
				": " . $facility_on_scene_str . $_POST['frm_on_scene_location']);
		} else {
			if (($as_row_old['on_scene_facility_id'] >= 0) && ((isset ($_POST['frm_on_scene_facility_id'])) && ($_POST['frm_on_scene_facility_id'] == -1))) {
				do_log($GLOBALS['LOG_CALL_FACILITY_UNSET'], $as_row_old['ticket_id'], $as_row_old['unit_id'], "");
			} else {
				if (($as_row_old['on_scene_location'] != "") && ((isset ($_POST['frm_on_scene_facility_id'])) && ($_POST['frm_on_scene_facility_id'] != -1))) {
					do_log($GLOBALS['LOG_CALL_FACILITY_CHANGE'], $as_row_old['ticket_id'], $as_row_old['unit_id'], get_text("On-Scene location") .
						": " . $facility_on_scene_str . $_POST['frm_on_scene_location']);
				}
			}
		}
	}
	if ((array_key_exists('frm_receiving_location', $_POST)) && ($as_row_old['receiving_location'] != $_POST['frm_receiving_location'])) {
		$facility_receiving_str = "";
		if (isset ($_POST['frm_on_scene_facility_id'])) {
			$query_facility_receiving = "SELECT `name` FROM `facilities` WHERE `id` = " . $_POST['frm_receiving_facility_id'];
			$result_facility_receiving = db_query($query_facility_receiving, __FILE__, __LINE__);
			if (db_num_rows($result_facility_receiving) > 0) {
				$row_facility_receiving = stripslashes_deep(db_fetch_assoc($result_facility_receiving));
				$facility_receiving_str = $row_facility_receiving['name'] . ", ";
			}
		}
		if ($as_row_old['receiving_location'] == "") {
			do_log($GLOBALS['LOG_CALL_RECEIVING_FACILITY_SET'], $as_row_old['ticket_id'], $as_row_old['unit_id'], get_text("Receiving location") . ": " . $facility_receiving_str . $_POST['frm_receiving_location']);
		} else {
			if ($_POST['frm_receiving_location'] == "") {
				do_log($GLOBALS['LOG_CALL_RECEIVING_FACILITY_UNSET'], $as_row_old['ticket_id'], $as_row_old['unit_id'], "");
			} else {
				do_log($GLOBALS['LOG_CALL_RECEIVING_FACILITY_CHANGE'], $as_row_old['ticket_id'], $as_row_old['unit_id'], get_text("Receiving location") . ": " . $facility_receiving_str . $_POST['frm_receiving_location']);
			}
		}
	}
	unset ($result);
	if ($do_receipt) {
		do_receipt_message($as_row_old['unit_id']);
	}
	break;
default:
	$moment_date_format = php_to_moment(get_variable("date_format"));

	$query = "SELECT *, " .
		"`assigns`.`updated` AS `assign_updated`, " .
		"`assigns`.`id` AS `assign_id`, " .
		"`assigns`.`on_scene_facility_id` AS `on_scene_facility_id`, " .
		"`assigns`.`receiving_facility_id` AS `receiving_facility_id`, " .
		"`assigns`.`on_scene_location` AS `on_scene_location`, " .
		"`assigns`.`receiving_location` AS `receiving_location`, " .
		"`assigns`.`comments` AS `assign_comments`, " .
		"`assigns`.`datetime` AS `assign_dispatched`, " .
		"`t`.`location` AS `ticket_location`, " .
		"`t`.`problemstart` AS `problemstart`, " .
		"`t`.`status` AS `ticket_status`, " .
		"`u`.`name` AS `user_name`, " .
		"`r`.`name` AS `unit_name` " .
		"FROM `assigns` " .
		"LEFT JOIN `tickets` `t` ON (`assigns`.`ticket_id` = `t`.`id`) " .
		"LEFT JOIN `users` `u` ON (`assigns`.`user_id` = `u`.`id`) " .
		"LEFT JOIN `units` `r` ON (`assigns`.`unit_id` = `r`.`id`) " .
		"WHERE `assigns`.`id` = " . $_GET['assign_id'] . " LIMIT 1;";

	$asgn_result = db_query($query, __FILE__, __LINE__);
	$asgn_row = stripslashes_deep(db_fetch_array($asgn_result));

	$buttons_display_str = " display: none;'";
	if (($asgn_row['ticket_status'] == $GLOBALS['STATUS_OPEN']) || ($asgn_row['ticket_status'] == $GLOBALS['STATUS_SCHEDULED'])) {
		$buttons_display_str = "";
	}

	$array_on_scene_str = get_start_end_facility_select_array("on_scene", $asgn_row['on_scene_facility_id'], $asgn_row['on_scene_location'], $asgn_row['ticket_location']);
	$array_receiving_str = get_start_end_facility_select_array("receiving", $asgn_row['receiving_facility_id'], $asgn_row['receiving_location'], "");
	unset ($result);
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
		<link href="./css/bootstrap-datetimepicker.css" rel="stylesheet">
		<link href="./css/jquery-ui.min.css" rel="stylesheet">
		<link href="./css/stylesheet.css" rel="stylesheet">
		<script src="./js/jquery-2.1.4.min.js" type="text/javascript"></script>
		<script src="./js/bootstrap.min.js" type="text/javascript"></script>
		<script src="./js/moment-with-locales.js" type="text/javascript"></script>
		<script src="./js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
		<script src="./js/functions.js" type="text/javascript"></script>
		<?php print show_day_night_style();?>
		<script>
			var new_infos_array = [];
			var screen_id_main = 0;
			var on_scene_fac_lat = [];
			var on_scene_fac_lng = [];
			var on_scene_facility_adress = [];
			var receiving_fac_lat = [];
			var receiving_fac_lng = [];
			var receiving_facility_adress = [];

			<?php print $array_on_scene_str["facility_address"];?>

			<?php print $array_on_scene_str["facility_coordinates"];?>

			<?php print $array_receiving_str["facility_address"];?>

			<?php print $array_receiving_str["facility_coordinates"];?>

			function validate() {
				var errmsg = "";
				if (!(moment($("#dispatched").val(), "<?php print $moment_date_format;?>").isValid())) {
					errmsg += "<?php print get_text("Invalid dispatched datetime");?><br>";
				}
				if (!(moment($("#responding").val(), "<?php print $moment_date_format;?>").isValid())) {
					errmsg += "<?php print get_text("Invalid responding datetime");?><br>";
				}
				if (!(moment($("#on_scene").val(), "<?php print $moment_date_format;?>").isValid())) {
					errmsg += "<?php print get_text("Invalid on-scene datetime");?><br>";
				}
				if (!(moment($("#facility_enroute").val(), "<?php print $moment_date_format;?>").isValid())) {
					errmsg += "<?php print get_text("Invalid facility-enroute datetime");?><br>";
				}
				if (!(moment($("#facility_arrived").val(), "<?php print $moment_date_format;?>").isValid())) {
					errmsg += "\t<?php print get_text("Invalid facility-arrived datetime");?><br>";
				}
				if (!(moment($("#clear").val(), "<?php print $moment_date_format;?>").isValid())) {
					errmsg += "<?php print get_text("Invalid clear datetime");?><br>";
				}
				if (errmsg != "") {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", errmsg);
					return false;
				} else {
					if ((moment($("#dispatched").val(), "<?php print $moment_date_format;?>").isValid())) {
						$("#dispatched_mysql_timestamp").val(moment($("#dispatched").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
					}
					if ((moment($("#responding").val(), "<?php print $moment_date_format;?>").isValid())) {
						$("#responding_mysql_timestamp").val(moment($("#responding").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
					}
					if ((moment($("#on_scene").val(), "<?php print $moment_date_format;?>").isValid())) {
						$("#on_scene_mysql_timestamp").val(moment($("#on_scene").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
					}
					if ((moment($("#facility_enroute").val(), "<?php print $moment_date_format;?>").isValid())) {
						$("#facility_enroute_mysql_timestamp").val(moment($("#facility_enroute").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
					}
					if ((moment($("#facility_arrived").val(), "<?php print $moment_date_format;?>").isValid())) {
						$("#facility_arrived_mysql_timestamp").val(moment($("#facility_arrived").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
					}
					if ((moment($("#clear").val(), "<?php print $moment_date_format;?>").isValid())) {
						$("#clear_mysql_timestamp").val(moment($("#clear").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
					}
					$.post("assign.php", $("#edit_form").serialize())
					.done(function (data) {
						var changes_data = '{"type":"message","item":"info","action":"<?php print get_text("Assign update applied");?>"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						goto_window("<?php print $url_back;?>?ticket_id=<?php print $asgn_row['ticket_id'];?>&screen_id=" + screen_id_main);
					})
					.fail(function () {
						var changes_data ='{"type":"message","item":"danger","action":"<?php print get_text("Error");?>"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						goto_window("situation.php?screen_id=" + screen_id_main);
					});
				}
			}

			function do_reset() {
				document.edit_form.reset();
				var status = ["dispatched", "responding", "on_scene", "facility_enroute", "facility_arrived", "clear"];	
				for (var i in status) {
					if ($("#" + status[i] + "_button").attr("checked")) {
						enable(status[i]);
						$("#" + status[i]).val($("#" + status[i] + "_initial_timestamp").val());
					} else {
						disable(status[i]);
					}
				}
				if ($("#frm_on_scene_facility_id").val() == 0) {
					$("#frm_on_scene_location").attr("readonly", false);
				} else {
					$("#frm_on_scene_location").attr("readonly", true);
				}
				if ($("#frm_receiving_facility_id").val() == 0) {
					$("#frm_receiving_location").attr("readonly", false);
				} else {
					$("#frm_receiving_location").attr("readonly", true);
				}
			}

			function enable(form_id) {
				$("#" + form_id).prop("type", "text");
				$("#" + form_id).attr("readonly", true);
				$("#" + form_id + "_mysql_timestamp").prop("disabled", false);
				$("#lock_" + form_id).css("visibility", "visible");
			}

			function disable(form_id) {
				$("#" + form_id).prop("type", "hidden");
				$("#" + form_id).attr("readonly", true);
				$("#" + form_id + "_mysql_timestamp").prop("disabled", true);
				$("#lock_" + form_id).css("visibility", "hidden");
			}

			function do_assign_reset(result) {
				if ((typeof result != "undefined") && (result == "show_promt")) {
					show_infobox("<?php print get_text("Reset dispatch");?>","<?php print get_text("Enter r to Reset dispatch times.") . "<br>" . get_text("Enter d to Delete this dispatch.");?>", "form", do_assign_reset);
				} else {
					if ((typeof result != "undefined") && (result != false) && (result != true)) {
						switch (result.toLowerCase()) {
						case "r":
							$("#frm_reset").prop("value", "reset");
							$.post("assign.php", $("#edit_form").serialize())
							.done(function (data) {
								var changes_data = '{"type":"message","item":"info","action":"<?php print get_text("Assign calls deleted");?>"}';
								window.parent.navigationbar.postMessage(changes_data, window.location.origin);
								goto_window("<?php print $url_back;?>?ticket_id=<?php print $asgn_row['ticket_id'];?>&screen_id=" + screen_id_main);
							})
							.fail(function () {
								var changes_data ='{"type":"message","item":"danger","action":"<?php print get_text("Error");?>"}';
								window.parent.navigationbar.postMessage(changes_data, window.location.origin);
								goto_window("situation.php?screen_id=" + screen_id_main);
							});
							break;
						case "d":
							if ($("#frm_reset_checkbox").prop("checked") == true) {
								setTimeout(function() {
									show_infobox("<?php print get_text("Delete this dispatch record?");?>", "", false, do_delete);
								}, 500);
							}
							break;
						default:
							$("#frm_reset_checkbox").attr("checked", false);
						}
					} else {
						$("#frm_reset_checkbox").attr("checked", false);
					}
				}	
			}

			function do_delete(result) {
				if (result == true) {
					$("#frm_delete").prop("value", "delete");
					$.post("assign.php", $("#edit_form").serialize())
					.done(function (data) {
						var changes_data = '{"type":"message","item":"info","action":"<?php print get_text("Assign deleted");?>"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						goto_window("<?php print $url_back;?>?ticket_id=<?php print $asgn_row['ticket_id'];?>&screen_id=" + screen_id_main);
					})
					.fail(function () {
						var changes_data ='{"type":"message","item":"danger","action":"<?php print get_text("Error");?>"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						goto_window("situation.php?screen_id=" + screen_id_main);
					});
				} else {
					$("#frm_reset_checkbox").attr("checked", false);
				}
			}

			function do_facility_to_on_scene_location(index) {
				if (index > 0) {
					$("#frm_on_scene_location").val(on_scene_facility_adress[index]);
					$("#frm_on_scene_location").attr("readonly", true);
				} else {
					if (index == 0) {
						$("#frm_on_scene_location").val("");
						$("#frm_on_scene_location").attr("readonly", false);
						$("#frm_on_scene_location").focus();
					} else {
						$("#frm_on_scene_location").val("<?php print html_entity_decode(remove_nls($asgn_row['ticket_location']));?>");
						$("#frm_on_scene_location").attr("readonly", true);
						$("#frm_on_scene_location").focus();
					}
				}
			}

			function do_facility_to_receiving_location(index) {
				if (index > 0) {
					$("#frm_receiving_location").val(receiving_facility_adress[index]);
					$("#frm_receiving_location").attr("readonly", true);
				} else {
					$("#frm_receiving_location").val("");
					$("#frm_receiving_location").attr("readonly", false);
					$("#frm_receiving_location").focus();
				}
			}

			$(document).ready(function() {
				$("#dispatched").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true
				});
				$("#responding").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true
				});
				$("#on_scene").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true
				});
				$("#facility_enroute").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true
				});
				$("#facility_arrived").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true
				});
				$("#clear").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true
				});
				$("#frm_receiving_location").focus();
				var changes_data ='{"type":"current_script","item":"script","action":"assign"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
				<?php show_prevent_browser_back_button();?>
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					new_infos_array = JSON.parse(event.data);
					$("#screen_id").val(new_infos_array['screen']['screen_id']);
					screen_id_main = new_infos_array['screen']['screen_id'];
				});
			});

		</script>
	</head>
	<body onload="check_frames();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" id="main_container">
			<div class="row infostring">
				<div<?php print get_table_id_title_str("assign", $asgn_row['assign_id']);?> class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
					<?php print get_text("Edit this Call Assignment") . get_table_id($asgn_row['assign_id']) . " - "  . get_variable("page_caption");?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-1">
					<div class="container-fluid" style="position: fixed;">
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="cancel_button('<?php print $url_back;?>', '<?php print $ticket_id;?>', new_infos_array['screen']['screen_id']);" tabindex=9><?php print get_text("Cancel");?></button>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="do_reset();" tabindex=8><?php print get_text("Reset");?></button>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="validate();" tabindex=7><?php print get_text("Save");?></button>
							</div>
						</div>
						<div style="margin-top: 20px;">
							<div class="row" style="margin-top: 10px;<?php print $buttons_display_str;?>">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('action.php?back=<?php print $back;?>&ticket_id=<?php print $asgn_row['ticket_id'] . "&unit_id=" . $asgn_row['unit_id'];?>');" tabindex=6><?php print get_text("Add Action");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;<?php print $buttons_display_str;?>">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('ticket_edit.php?ticket_id=<?php print $asgn_row['ticket_id'];?>');" tabindex=5><?php print get_text("Incident");?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<form id="edit_form" name="edit_form" action="<?php print basename(__FILE__);?>" method="post" target="main">
					<input type="hidden" name="frm_by_id" value="<?php print $_SESSION['user_id'];?>">
					<input type="hidden" name="function" value="update">
					<input type="hidden" name="assign_id" value="<?php print $_GET['assign_id'];?>">
					<input type="hidden" name="frm_ticket_id" value="<?php print $asgn_row['ticket_id'];?>">
					<input type="hidden" name="screen_id" id="screen_id" value="">
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_left">
								<input type="hidden" name="back" value="<?php print $back;?>">
								<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
									<tr>
										<th style="width: 15%; border-top: 0px;"><?php print get_text("Unit");?>:</th>
										<td style="width: 5%; border-top: 0px;"></td>
										<td style="width: 5%; border-top: 0px;"></td>
										<td<?php print get_title_str($asgn_row['unit_name']);?> colspan=2  style="border-top: 0px;">
											<input name="responder_handle" type="text" class="form-control" value="<?php print remove_nls($asgn_row['handle']);?>" readonly/>
										</td>
									</tr>
									<tr style="height: 45px;">
										<th><?php print get_text("Dispatched");?>:</th>
										<td><input id="dispatched_button" name="dispatched_button" type="radio" onclick="enable('dispatched');"<?php print callprogression_checked_str($asgn_row['dispatched']);?>></td>
										<td><span<?php print callprogression_lock_visible_str($asgn_row['dispatched']);?> id="lock_dispatched" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('dispatched');"></span></td>
										<td colspan=2>
											<input type="hidden" id="dispatched_initial_timestamp" value="<?php print callprogression_date_str($asgn_row['dispatched'], $datetime_now);?>">
											<input type="<?php print callprogression_input_type_str($asgn_row['dispatched']);?>" class="form-control" id="dispatched" value="<?php print callprogression_date_str($asgn_row['dispatched'], $datetime_now);?>" readonly>
											<input type="hidden" id="dispatched_mysql_timestamp" name="dispatched" <?php print callprogression_disabled_str($asgn_row['dispatched']);?>>
										</td>
									</tr>
									<tr style="height: 45px;">
										<th><?php print get_text("Responding");?>:</th>
										<td><input id="responding_button" name="responding_button" type="radio" onclick="enable('responding');"<?php print callprogression_checked_str($asgn_row['responding']);?>></td>
										<td><span<?php print callprogression_lock_visible_str($asgn_row['responding']);?> id="lock_responding" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('responding');"></span></td>
										<td colspan=2>
											<input type="hidden" id="responding_initial_timestamp" value="<?php print callprogression_date_str($asgn_row['responding'], $datetime_now);?>">
											<input type="<?php print callprogression_input_type_str($asgn_row['responding']);?>" class="form-control" id="responding" value="<?php print callprogression_date_str($asgn_row['responding'], $datetime_now);?>" readonly>
											<input type="hidden" class="form-control" id="responding_mysql_timestamp" name="responding" <?php print callprogression_disabled_str($asgn_row['responding']);?>>
										</td>
									</tr>
									<tr style="height: 45px;">
										<th><?php print get_text("On-scene");?>:</th>
										<td><input id="on_scene_button" name="on_scene_button" type="radio" onclick="enable('on_scene');"<?php print callprogression_checked_str($asgn_row['on_scene']);?>></td>
										<td><span<?php print callprogression_lock_visible_str($asgn_row['on_scene']);?> id="lock_on_scene" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('on_scene');"></span></td>
										<td colspan=2>
											<input type="hidden" id="on_scene_initial_timestamp" value="<?php print callprogression_date_str($asgn_row['on_scene'], $datetime_now);?>">
											<input type="<?php print callprogression_input_type_str($asgn_row['on_scene']);?>" class="form-control" id="on_scene" value="<?php print callprogression_date_str($asgn_row['on_scene'], $datetime_now);?>" readonly>
											<input type="hidden" class="form-control" id="on_scene_mysql_timestamp" name="on_scene" <?php print callprogression_disabled_str($asgn_row['on_scene']);?>>
										</td>
									</tr>
									<tr style="height: 45px;">
										<th><?php print get_text("Fac en-route");?>:</th>
										<td><input id="facility_enroute_button" name="facility_enroute_button" type="radio" onclick="enable('facility_enroute');"<?php print callprogression_checked_str($asgn_row['u2fenr']);?>></td>
										<td><span<?php print callprogression_lock_visible_str($asgn_row['u2fenr']);?> id="lock_facility_enroute" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('facility_enroute');"></span></td>
										<td colspan=2>
											<input type="hidden" id="facility_enroute_initial_timestamp" value="<?php print callprogression_date_str($asgn_row['u2fenr'], $datetime_now);?>">
											<input type="<?php print callprogression_input_type_str($asgn_row['u2fenr']);?>" class="form-control" id="facility_enroute" value="<?php print callprogression_date_str($asgn_row['u2fenr'], $datetime_now);?>" readonly>
											<input type="hidden" class="form-control" id="facility_enroute_mysql_timestamp" name="facility_enroute" <?php print callprogression_disabled_str($asgn_row['u2fenr']);?>>
										</td>
									</tr>
									<tr style="height: 45px;">
										<th><?php print get_text("Fac arr");?>:</th>
										<td><input id="facility_arrived_button" name="facility_arrived_button" type="radio" onclick="enable('facility_arrived');"<?php print callprogression_checked_str($asgn_row['u2farr']);?>></td>
										<td><span<?php print callprogression_lock_visible_str($asgn_row['u2farr']);?> id="lock_facility_arrived" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('facility_arrived');"></span></td>
										<td colspan=2>
											<input type="hidden" id="facility_arrived_initial_timestamp" value="<?php print callprogression_date_str($asgn_row['u2farr'], $datetime_now);?>">
											<input type="<?php print callprogression_input_type_str($asgn_row['u2farr']);?>" class="form-control" id="facility_arrived" value="<?php print callprogression_date_str($asgn_row['u2farr'], $datetime_now);?>" readonly>
											<input type="hidden" class="form-control" id="facility_arrived_mysql_timestamp" name="facility_arrived" <?php print callprogression_disabled_str($asgn_row['u2farr']);?>>
										</td>
									</tr>
									<tr style="height: 45px;">
										<th><?php print get_text("Clear");?>:</th>
										<td><input id="clear_button" name="clear_button" type="radio" onclick="enable('clear');"<?php print callprogression_checked_str($asgn_row['clear']);?>></td>
										<td><span<?php print callprogression_lock_visible_str($asgn_row['clear']);?> id="lock_clear" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('clear');"></span></td>
										<td colspan=2>
											<input type="hidden" id="clear_initial_timestamp" value="<?php print callprogression_date_str($asgn_row['clear'], $datetime_now);?>">
											<input type="<?php print callprogression_input_type_str($asgn_row['clear']);?>" class="form-control" id="clear" value="<?php print callprogression_date_str($asgn_row['clear'], $datetime_now);?>" readonly>
											<input type="hidden" class="form-control" id="clear_mysql_timestamp" name="clear" <?php print callprogression_disabled_str($asgn_row['clear']);?>>
										</td>
									</tr>
									<tr style="height: 45px;">
										<th colspan=2><?php print get_text("Reset");?>:</th>
										<td>
											<input id="frm_reset_checkbox" name="frm_reset_checkbox" type="checkbox" onclick="do_assign_reset('show_promt');">
											<input id="frm_reset" name="frm_reset" type="hidden" value="">
											<input id="frm_delete" name="frm_delete" type="hidden" value="">
										</td>
										<td colspan=2 style="text-align: center; vertical-align: middle; font-size: larger;"><?php print callprogression_is_cleared($asgn_row['clear']);?></td>
									</tr> 
									<tr>
										<th style="width: 25%;"<?php print get_help_text_str("_loca");?> colspan=2>
											<?php print get_text("On-Scene location"); ?>:
										</th>
										<td></td>
										<td style="width: 75%;" colspan=2>
											<textarea id="frm_on_scene_location" name="frm_on_scene_location" class="form-control" cols="48" rows="2"<?php print $array_on_scene_str["readonly_str"];?>><?php print $array_on_scene_str["start_end_location"];?></textarea>
											<?php print $array_on_scene_str["select_str"];?>
										</td>
									</tr>
									<tr style="height: 92px;">
										<th style="width: 25%;"<?php print get_help_text_str("_loca");?> colspan=2>
											<?php print get_text("Receiving location");?>:
										</th>
										<td></td>
										<td style="width: 75%;" colspan=2>
											<textarea id="frm_receiving_location" name="frm_receiving_location" class="form-control" tabindex=1 cols="48" rows="2"<?php print $array_receiving_str["readonly_str"];?>><?php print $array_receiving_str["start_end_location"];?></textarea>
											<?php print $array_receiving_str["select_str"];?>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_right_1">
								<table class="table table-striped table-condensed" style="table-layout: fixed;">
								   <?php show_head($asgn_row['ticket_id'], false, false);?>
								</table>
							</div>
						</div>
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_right_2">	
								<table class="table table-striped table-condensed" style="table-layout: fixed;">
									<tr>
										<th style="width: 20%;"<?php print get_help_text_str("_cmnts_ass");?>><?php print get_text("Dispo-info");?>:</th>
										<td style="width: 5%;"></td>
										<td colspan=2>
											<textarea name="frm_comments" class="form-control" cols="45" rows="2" tabindex=3><?php print $asgn_row['assign_comments'];?></textarea>
											<?php print get_textblock_select_str("assign", "document.edit_form.frm_comments", "", 0, "");?>
										</td>
									</tr>
							<!--	<tr style="visibility:hidden">
										<th><?php print get_text("Mileage");?>:</th>
										<td colspan=3 align='center'>
											<span> <?php print get_text("Start Miles");?>:</span>
											<input maxlength="8" size="8" name="frm_miles_strt" value="<?php print $asgn_row['start_miles'];?>" type="text">
											<span> <?php print get_text("On Scene Miles");?>:</span>
											<input maxlength="8" size="8" name="frm_on_scene_miles" value="<?php print $asgn_row['on_scene_miles'];?>" type="text">
											<span><?php print get_text("End Miles");?>:</span>
											<input maxlength="8" size="8" name="frm_miles_end" value="<?php print $asgn_row['end_miles'];?>" type="text">
											<span><?php print get_text("TOTAL MILES");?>:</span>
											<input maxlength="8" size="8" name="frm_miles_tot" value="<?php print $asgn_row['miles'];?>" type="text">
										</td>
									</tr>	-->
									<tr>
										<th>
											<div><?php print get_text("Dispatched");?>:</div>
											<div><?php print get_text("Edited");?>:</div>
										</th>
										<td></td>
										<td colspan=2>
											<div<?php print get_title_str("<nobr>" . date(get_variable("date_format"), strtotime($asgn_row['assign_dispatched'])) . "</nobr>");?>>
												<?php print date(get_variable("date_format_time_only"), strtotime($asgn_row['assign_dispatched'])) . " " . get_text("by") . " " . get_user_name($asgn_row['dispatching_user_id']);?>
											</div>
											<div<?php print get_title_str("<nobr>" . date(get_variable("date_format"), strtotime($asgn_row['assign_updated'])) . "</nobr>");?>>
												<?php print date(get_variable("date_format_time_only"), strtotime($asgn_row['assign_updated'])) . " " . get_text("by") . " " . $asgn_row['user_name'];?>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</form>
				<div class="col-md-1"></div>
			</div>
		</div>
		<?php show_infobox();?>
	</body>
</html>
	<?php
}
?>