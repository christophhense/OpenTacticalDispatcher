<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
do_login(basename(__FILE__));
set_session_expire_time();

function get_reserved_row() {
	$datetime_now = mysql_datetime();

	$query  = "SELECT `id` AS `ticket_id`, " .
		"`incident_name`, " .
		"`status` " .
		"FROM `tickets` " .
		"WHERE `status`= " . $GLOBALS['STATUS_RESERVED'] . " " .
		"AND `user_id` = " . $_SESSION['user_id'] . " " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) != 1) {

		$query_insert = "INSERT INTO `tickets` (`incident_type_id`, `contact`, `location`, `phone`, " .
			"`facility_id`, `problemstart`, `problemend`, `incident_name`, " .
			"`description`, `comments`, `status`, `severity`, " .
			"`booked_date`, `lat`, `lng`, `call_taker_id`, " .
			"`user_id`, `client_address`, `updated`, `datetime`) " .
			"VALUES (0, '', '', '', " .
			"NULL, '" . $datetime_now . "', NULL, '', " .
			"'', '" . get_text("RESERVED TICKET") . "', " . $GLOBALS['STATUS_RESERVED'] . ", 0, " .
			"NULL, 0.999999, 0.999999, 0, " .
			$_SESSION['user_id'] . ", " . "'" . $_SERVER['REMOTE_ADDR'] . "', '2017-01-01 00:00:00', '" . $datetime_now . "');";

		db_query($query_insert, __FILE__, __LINE__);
	}
	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_assoc($result));
	return $row;
}

$datetime_now = mysql_datetime();
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
if (($function == "") && isset ($_POST['function'])) {
	$function = $_POST['function'];
}
switch ($function) {
case "get_reserved_ticket":
	if (is_super() || is_admin() || is_operator()) {
		$reserved_row = array ();
		$reserved_row = get_reserved_row();
		print $reserved_row['ticket_id'];
	}
	break;
case "insert":
	if (is_super() || is_admin() || is_operator()) {
		$ticket_id = trim($_POST['ticket_id']);
		if ((isset ($_POST['auto_ticket'])) && ($_POST['auto_ticket'] == "true") && (isset ($_POST['api_log_id']))) {

			$query = "SELECT `code`, " .
				"`datetime`, " .
				"`unit_id`, " .
				"`text`, " .
				"`u`.`id` AS `unit_id`, " .
				"`u`.`handle` AS `unit_handle` " .
				"FROM `api_log` " .
				"LEFT JOIN `units` `u` ON (`u`.`id` = `api_log`.`unit_id`) " .
				"WHERE `api_log`.`id` = " . $_POST['api_log_id'] . ";";

			$result	= db_query($query, __FILE__, __LINE__);
			$row = stripslashes_deep(db_fetch_assoc($result));
			switch ($row['code']) {
			case $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']:
			case $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']:
			case $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET']:
			case $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']:
				$_POST['frm_lat'] = $_POST['frm_lng'] = 0.999999;
				$_POST['frm_facility_id'] = $_POST['frm_in_types_id'] = 0;
				$_POST['frm_severity'] = $_POST['frm_group'][0] = 1;
				$_POST['frm_location'] = $_POST['frm_phone'] = $_POST['frm_description'] = $_POST['frm_comments'] = "";
				$_POST['problemstart'] = $row['datetime'];
				$_POST['frm_contact'] = remove_nls($row['unit_handle']);
				$_POST['frm_incident_name'] = "#" . $ticket_id;
				break;
			case $GLOBALS['LOG_MESSAGE_RECEIVE']:
				$message_time = substr(remove_nls($row['text']), 0, 17);
				$message_text = substr(remove_nls($row['text']), 18);
				$ticket_fields = array ();
				$ticket_fields = explode(";", $message_text);

				$query_fac = "SELECT `id`, " .
					"`street`, " .
					"`city` " .
					"FROM `facilities` " .
					"WHERE `object_id` = '" . trim($ticket_fields[2]) . "';";

				$result_fac	= db_query($query_fac, __FILE__, __LINE__);
				$auto_ticket_settings = get_auto_ticket_configuration("settings");
				if ((db_num_rows($result_fac) != 0))  {
					$row_fac = stripslashes_deep(db_fetch_assoc($result_fac));
					$_POST['frm_facility_id'] = $row_fac['id'];
					$_POST['frm_lat'] = $_POST['frm_lng'] = 0.999999;
					$_POST['frm_in_types_id'] = 0;
					$_POST['frm_severity'] = $auto_ticket_settings["severity"];
					$_POST['frm_group'][0] = 1;
					$_POST['frm_location'] = remove_nls($row_fac['street'] . " " . $row_fac['city']);
					$_POST['frm_phone'] = $auto_ticket_settings["phone"];
					$_POST['frm_description'] = $ticket_fields[0] . " " . $ticket_fields[4];
					$_POST['frm_comments'] = remove_nls($row['text']);
					$_POST['problemstart'] = $row['datetime'];
					$_POST['frm_contact'] = $auto_ticket_settings["contact"];
					$_POST['frm_incident_name'] = "#" . $ticket_id;
				} else {
					$_POST['frm_lat'] = $_POST['frm_lng'] = 0.999999;
					$_POST['frm_facility_id'] = $_POST['frm_in_types_id'] = 0;
					$_POST['frm_severity'] = $auto_ticket_settings["severity"];
					$_POST['frm_group'][0] = 1;
					$_POST['frm_location'] = $ticket_fields[3] . " " . $ticket_fields[5] . " " . $ticket_fields[6];
					$_POST['frm_phone'] = $auto_ticket_settings["phone"];
					$_POST['frm_description'] = $ticket_fields[0] . " " . $ticket_fields[4];
					$_POST['frm_comments'] = remove_nls($row['text']);
					$_POST['problemstart'] = $row['datetime'];
					$_POST['frm_contact'] = $auto_ticket_settings["contact"];
					$_POST['frm_incident_name'] = "#" . $ticket_id;
				}
				break;
			default:
			}
		}
		$ticket_status = $GLOBALS['STATUS_OPEN'];
		$booked_date = "NULL";
		$booked_date_update = "NULL";
		if (isset ($_POST['scheduled_date']) && (intval(trim($_POST['frm_do_scheduled']) == 1))) {
			$ticket_status = $GLOBALS['STATUS_SCHEDULED'];
			$booked_date = $_POST['scheduled_date'];
			$booked_date_update = quote_smart($_POST['scheduled_date']);
		}
		$frm_problemstart = $_POST['problemstart'];
		$inc_num_array = unserialize(base64_decode(get_variable("_inc_num")));
		if (strpos(get_variable("_inc_num"), "{") > 0) {
			$inc_num_array = unserialize(get_variable("_inc_num"));
		}
		if (($inc_num_array[0] == 0) || ($inc_num_array[0] > 3)) {	// no auto numbering scheme
			switch ($inc_num_array[0]) {
			case 0:
				$name_rev = $_POST['frm_incident_name'];
				break;
			case 4:												/*  append  */
				$name_rev = $_POST['frm_incident_name'] . "/#" .  $ticket_id;
				break;
			case 5:												/*  prepend  */
				$name_rev =  "#" . $ticket_id . "/" . $_POST['frm_incident_name'];
				break;
			default:											/*  no serial no. */
				$name_rev =  $_POST['frm_incident_name'];
			}
		} else {
			switch ((int) $inc_num_array[0]) {
			case 1: 																					// number only
				$name_rev = (string) $inc_num_array[3] . " ";
				break;
			case 2:																						// labeled
				$name_rev = $inc_num_array[1] . $inc_num_array[2] . (string) $inc_num_array[3] . " ";	// label, separator, number
				break;
			case 3:																						// year
				$name_rev = date(get_variable("date_format_year_only")) . $inc_num_array[2] . (string) $inc_num_array[3] . " " ;		// year, separator, number
				break;
			default:
				alert("ERROR @ " + "<?php print __LINE__;?>");
			}
			if ((((int) $inc_num_array[0]) == 3) && (!($inc_num_array[5] == date("y")))) {	// year style and change?
				$inc_num_array[3] = 1;	// roll over and start at 1
				$inc_num_array[5] = date("y");
			}
			if (((int) $inc_num_array[0]) > 0) {	// step to next no. if scheme in use
				$inc_num_array[3]++;				// do the deed for next use
			}
			$incident_number_str = base64_encode(serialize($inc_num_array));

			$query = "UPDATE `settings` " .
				"SET `value` = '" . $incident_number_str . "' " .
				"WHERE `name` = '_inc_num'";

			db_query($query, __FILE__, __LINE__);
		}
		$groups = "," . implode(',', $_POST['frm_group']) . ",";
		$facility_id = 0;
		if ($_POST['frm_facility_id']) {
			$facility_id = trim($_POST['frm_facility_id']);
		}
		$lat = 0.999999;
		if (trim($_POST['frm_lat']) != "") {
			$lat = $_POST['frm_lat'];
		}
		$lng = 0.999999;
		if (trim($_POST['frm_lng']) != "") {
			$lng = $_POST['frm_lng'];
		}

		$query = "UPDATE `tickets` SET " .
			"`contact` = " . quote_smart(trim($_POST['frm_contact'])) .", " .
			"`location` = " . quote_smart(trim($_POST['frm_location'])) .", " .
			"`phone` = " . quote_smart(trim($_POST['frm_phone'])) . ", " .
			"`facility_id` = " . $facility_id . ", " .
			"`lat` = " . $lat . ", " .
			"`lng` = " . $lng . ", " .
			"`incident_name` = " . quote_smart(trim($name_rev)) . ", " .
			"`call_taker_id` = " . $_SESSION['user_id'] . ", " .
			"`severity` = " . $_POST['frm_severity'] . ", " .
			"`incident_type_id` = " . $_POST['frm_in_types_id'] . ", " .
			"`status` = " . $ticket_status . ", " .
			"`problemstart` = " . quote_smart(trim($frm_problemstart)) . ", " .
			"`problemend` = NULL, " .
			"`description` = " . quote_smart(trim($_POST['frm_description'])) .", " .
			"`comments` = " . quote_smart(trim($_POST['frm_comments'])) .", " .
			"`booked_date` = " . $booked_date_update . ", " .
			"`datetime` = '" . $datetime_now . "', " .
			"`updated` = '" . $datetime_now . "', " .
			"`user_id` = " . $_SESSION['user_id'] . " " .
			"WHERE `id` = " . $ticket_id;

		db_query($query, __FILE__, __LINE__);	
		foreach ($_POST['frm_group'] as $grp_val) {
			if (test_allocates($ticket_id, $grp_val, 1)) {
				insert_into_allocates($grp_val, $GLOBALS['TYPE_TICKET'], $ticket_id, $_SESSION['user_id'], $datetime_now);
			}
		}

		$query_inc = "SELECT `type` " .
			"FROM `incident_types` " .
			"WHERE `id` = " . $_POST['frm_in_types_id'] . ";";

		$result_inc = db_query($query_inc, __FILE__, __LINE__);
		$inc_type = stripslashes_deep(db_fetch_array($result_inc));
		$opened_or_scheduled = "LOG_INCIDENT_OPEN";
		$log_str = "";
		if ((isset ($_POST['frm_do_scheduled'])) && ($_POST['frm_do_scheduled'] == 1)) {
			$opened_or_scheduled = "LOG_INCIDENT_SCHEDULED";
			$log_str = get_text("Scheduled Date") . ": " . date(get_variable("date_format"), strtotime($booked_date)) . "  ";
		}
		if (!empty ($_POST['frm_location'])) {
			$log_str .= get_text("Incident location") . ": " . $_POST['frm_location'] . "  ";
		}
		if (!empty ($_POST['frm_phone'])) {
			$log_str .= get_text("Callback phone") . ": " . $_POST['frm_phone'] . "  ";
		}
		if (!empty ($_POST['frm_in_types_id'])) {
			$log_str .= get_text("Incident type") . ": " . $inc_type['type'] . "  ";
		}
		if (!empty ($_POST['frm_severity'])) {
			$log_str .= get_text("Severity") . ": ";
			switch ($_POST['frm_severity']) {
			case (0):
				$log_str .= get_text("Normal") . "  ";
				break;
			case (1):
				$log_str .= get_text("Medium") . "  ";
				break;
			case (2):
				$log_str .= get_text("High") . "  ";
				break;
			}
		}
		if (!empty ($_POST['frm_description'])) {
			$log_str .= get_text("Synopsis") . ": " . $_POST['frm_description'] . "  ";
		}
		if (!empty ($_POST['frm_contact'])) {
			$log_str .= get_text("Reported by") . ": " . $_POST['frm_contact'] . "  ";
		}
		if (!empty ($_POST['frm_contact'])) {
			$log_str .= get_text("Incident name") . ": " . $name_rev . "  ";
		}
		if (!empty ($_POST['frm_comments'])) {
			$log_str .= get_text("Comments") . ": " . $_POST['frm_comments'] . "  ";
		}
		do_log($GLOBALS['LOG_INCIDENT_ADDED'], $ticket_id, 0, get_text("Run Start") . ": " . date(get_variable("date_format"), strtotime(trim($frm_problemstart))));
		do_log($GLOBALS[$opened_or_scheduled], $ticket_id, 0, html_entity_decode(remove_nls($log_str)), $facility_id);
		if (intval($facility_id) > 0) {
			$query_facilities = "SELECT `handle` FROM `facilities` WHERE `id` = " . $facility_id;
			$result_facilities = db_query($query_facilities, __FILE__, __LINE__);
			$row_facs = db_fetch_assoc($result_facilities);
			do_log($GLOBALS['LOG_FACILITY_INCIDENT_OPEN'], $ticket_id, "", remove_nls($row_facs['handle']), $facility_id);
		}
			$auto_dispatch_settings = explode(",", get_variable("auto_dispatch"));
			$auto_dispatch = trim($auto_dispatch_settings[0]);
		if ((isset ($_POST['frm_do_scheduled'])) && ($_POST['frm_do_scheduled'] == 0) && ($auto_dispatch == 1)) {
			//======================================
			//$url_str = "dispatch.php?ticket_id=" . $_POST['ticket_id'] . "&new_incident=true&screen_id=\" + parent.frames['navigation'].$('#div_screen_id').html();\";";
			$url_str = "dispatch.php?ticket_id=" . $_POST['ticket_id'] . 
				"&new_incident=true&screen_id=\'" .$_POST["screen_id"] . "\'";
			//======================================
		} else {
			//======================================
			//$url_str = "situation.php?screen_id=\" + parent.frames['navigation'].$('#div_screen_id').html();\";";
			$url_str = "situation.php?screen_id=\'" . $_POST["screen_id"] . "\'";
			//======================================
		}
		?>
	<script>
		//======================================
		//parent.frames["navigation"].show_message("<?php print get_text("Saved");?>", "success");
		var changes_data ='{"type":"message","item":"success","action":"<?php print get_text("Saved");?>"}';
		window.parent.navigationbar.postMessage(changes_data, window.location.origin);
		//======================================
		window.location.href="<?php print $url_str;?>";
	</script>
		<?php
	}
	break;
default:
	$auto_poll_settings = explode(",", get_variable("auto_poll"));
	$auto_poll_time = trim($auto_poll_settings[0]);
	$parking_form_data_settings = explode(",", get_variable("parking_form_data"));
	$additional_helptext_form_data_parking = "";
	if (trim($parking_form_data_settings[0]) != 0) {
		$additional_helptext_form_data_parking = " " . get_help_text("parked_trigger_chars", true) . ": " . trim($parking_form_data_settings[0]) . " " . get_help_text("parked_seconds", true) . ": " . trim($parking_form_data_settings[1]);
	}
	$moment_date_format = php_to_moment(get_variable("date_format"));
	$incident_location_select_array = get_incident_location_select_str("add", 0);
	$reported_by_select_array = get_reported_by_select_str("add");
	$inc_num_array = unserialize(base64_decode(get_variable("_inc_num")));
	if (strpos(get_variable("_inc_num"), "{") > 0) {
		$inc_num_array = unserialize(get_variable("_inc_num"));
	}
	$reserved_ticket_row = get_reserved_row();
	$ticket_id = $reserved_ticket_row['ticket_id'];
	$incident_name = $reserved_ticket_row['incident_name'];
	$inc_name_readonly_or_tabindex_str = " tabindex=11";
	$inc_name_mandatory_str = "";
	$inc_name_class = "";
	switch ($inc_num_array[0]) {
	case 1:
	case 2:
	case 3:
		$inc_name_readonly_or_tabindex_str = " readonly";
		$incident_name = get_text("RESERVED TICKET");
		break;
	case 4:
	case 5:
		break;
	case 6:
		$inc_name_mandatory_str = " <span style='font-size: small; vertical-align: top; color: red;'>*</span>";
		$inc_name_class = " mandatory";
		break;
	case 7:
		break;
	default:
		$inc_name_mandatory_str = " <span style='font-size: small; vertical-align: top; color: red;'>*</span>";
		$inc_name_readonly_or_tabindex_str = " readonly";
		$incident_name = "#" . $ticket_id;
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
			var get_infos_array;

			var parking_form_data_min_trigger_chars = <?php print trim($parking_form_data_settings[0]);?> + 0;
			var parking_form_data_cache_period = (<?php print trim($parking_form_data_settings[1]);?> + 0) * 1000;
			var inc_num_array_0 = <?php print trim($inc_num_array[0]);?> + 0;

			try {
				//======================================
				/*parent.frames["navigation"].$("#script").html("<?php print basename(__FILE__);?>");
				parent.frames["navigation"].highlight_button("add_ticket");*/
				var changes_data ='{"type":"div","item":"script","action":"<?php print basename(__FILE__);?>"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
				var changes_data ='{"type":"button","item":"add_ticket","action":"highlight"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
				//======================================
			} catch(e) {
			}

			var severities = new Array();
			var protocols = new Array();
			var reported_by_phone = new Array();
			var fac_lat = new Array();
			var fac_lng = new Array();
			var facility_adress = new Array();
		<?php print get_severity_protocol_array_str();?>
		<?php print $reported_by_select_array["reported_by_phone"];?>
		<?php print $incident_location_select_array["facility_address"];?>
		<?php print $incident_location_select_array["facility_coordinates"];?>

			function validate() {
				var errmsg = "";
				var scheduled = moment($("#scheduled_date").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss");
				var problemstart = moment($("#problemstart").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss");
				if ($("#frm_contact").val() == "") {
					errmsg += "<?php print get_text("Reported-by is required");?><br>";
				}
				if ($("#frm_in_types_id").val() == 0) {
					errmsg += "<?php print get_text("Incident type is required");?><br>";
				}
				if (($("#frm_incident_name").val() == "") && ("<?php print $inc_name_class;?>"  == " mandatory")) {
					errmsg += "<?php print get_text("Incident name is required");?><br>";
				}
				if (!moment(problemstart, "YYYY-MM-DD HH:mm:ss").isValid()) {
					errmsg += "<?php print get_text("Invalid problemstart");?><br>";
				}
				if (
					(
						!moment(scheduled, "YYYY-MM-DD HH:mm:ss").isValid() ||
						moment(scheduled, "YYYY-MM-DD HH:mm:ss").isBefore(moment(problemstart, "YYYY-MM-DD HH:mm:ss"))
					) && (
						$("#frm_do_scheduled").val() == 1
					)
				) {
					errmsg += "<?php print get_text("Invalid scheduled date");?><br>";
				}
				if (errmsg != "") {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", errmsg);
					return false;
				} else {
					$("#frm_phone").val($("#frm_phone").val().replace(/[^0-9\+\/\-\*\s#,]/g, ""));
					$("#problemstart_mysql_timestamp").val(problemstart);
					if ((moment(scheduled, "YYYY-MM-DD HH:mm:ss").isValid())) {
						$("#scheduled_date_mysql_timestamp").val(scheduled);
					}
					set_parked_form_data();
					$("#ticket_add").submit();
				}
			}

			function set_parked_form_data(data) {
				try {
					if ((typeof(data) != "undefined") && (data != null)) {
						//======================================
						/*parent.frames["navigation"].ticket_add_form_data = data;
						parent.frames["navigation"].ticket_add_timestamp = Date.now();
						parent.frames["navigation"].ticket_add_ticket_id = <?php print $ticket_id;?> + 0;*/
						var changes_data = {"type":"set_parked_form_data","item":"ticket_add_form_data","action":""};
						changes_data.ticket_add_form_data = data;
						//console.log(changes_data);
						changes_data = JSON.stringify(changes_data);
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						var changes_data ='{"type":"set_parked_form_data","item":"ticket_add_timestamp","action":"' + Date.now() + '"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						var changes_data ='{"type":"set_parked_form_data","item":"ticket_add_ticket_id","action":"' + <?php print $ticket_id;?> + '"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						//======================================
					} else {
						//======================================
						/*parent.frames["navigation"].ticket_add_form_data = "";
						parent.frames["navigation"].ticket_add_timestamp = 0;
						parent.frames["navigation"].ticket_add_ticket_id = 0;*/
						var changes_data ='{"type":"set_parked_form_data","item":"ticket_add_form_data","action":""}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						var changes_data ='{"type":"set_parked_form_data","item":"ticket_add_timestamp","action":"0"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						var changes_data ='{"type":"set_parked_form_data","item":"ticket_add_ticket_id","action":"0"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						//======================================
					}
				} catch (e) {
				}
			}

			function get_parked_form_data() {
				try {
					var current_timestamp = Date.now();
					//======================================
					/*if ((current_timestamp < (parent.frames["navigation"].ticket_add_timestamp + parking_form_data_cache_period)) &&
						(parent.frames["navigation"].ticket_add_ticket_id == (<?php print $ticket_id;?> + 0))) {
						if (parent.frames["navigation"].ticket_add_form_data[7]['value'] == 0) {
							$("#frm_location").val(parent.frames["navigation"].ticket_add_form_data[6]['value']);
						} else {
							$("#frm_facility_id").val(parent.frames["navigation"].ticket_add_form_data[7]['value']).change();
						}
						$("#frm_phone").val(parent.frames["navigation"].ticket_add_form_data[8]['value']);
						$("#frm_description").val(parent.frames["navigation"].ticket_add_form_data[9]['value']);
						$("#frm_contact").val(parent.frames["navigation"].ticket_add_form_data[11]['value']);
						$("#frm_in_types_id").val(parent.frames["navigation"].ticket_add_form_data[13]['value']).change();
						$("#frm_severity").val(parent.frames["navigation"].ticket_add_form_data[14]['value']).change();
						$("#frm_comments").val(parent.frames["navigation"].ticket_add_form_data[15]['value']);
						if ((inc_num_array_0 > 3) && (inc_num_array_0 < 8)) {
							$("#frm_incident_name").val(parent.frames["navigation"].ticket_add_form_data[17]['value']);
						}
						do_unlock_readonly('problemstart');
						$("#problemstart").val(parent.frames["navigation"].ticket_add_form_data[18]['value']);
						if (parent.frames["navigation"].ticket_add_form_data[20]['value'].trim() == "on") {
							$("#scheduled_checkbox").prop("checked", true);
							do_scheduled();
							$("#scheduled_date").val(parent.frames["navigation"].ticket_add_form_data[21]['value']);
						}*/
					if ((parseInt(current_timestamp) < (parseInt(get_infos_array['parked_form_data']['ticket_add_timestamp']) + parseInt(parking_form_data_cache_period))) &&
						(get_infos_array['parked_form_data']['ticket_add_ticket_id'] == (<?php print $ticket_id;?>))) {
						var form_content = new Array;
						form_content['frm_facility_id'] = 0;
						form_content['scheduled_checkbox'] = "off";
						for (var key in get_infos_array['parked_form_data']['ticket_add_form_data']) {
							form_content[get_infos_array['parked_form_data']['ticket_add_form_data'][key]['name']] = get_infos_array['parked_form_data']['ticket_add_form_data'][key]['value'];
						}
						if (form_content['frm_facility_id'] == 0) {
							$("#frm_location").val(form_content['frm_location']);
						} else {
							$("#frm_facility_id").val(form_content['frm_facility_id']).change();
						}
						$("#frm_phone").val(form_content['frm_phone']);
						$("#frm_description").val(form_content['frm_description']);
						$("#frm_contact").val(form_content['frm_contact']);
						$("#frm_in_types_id").val(form_content['frm_in_types_id']).change();
						$("#frm_severity").val(form_content['frm_severity']).change();
						$("#frm_comments").val(form_content['frm_comments']);
						if ((inc_num_array_0 > 3) && (inc_num_array_0 < 8)) {
							$("#frm_incident_name").val(form_content['frm_incident_name']);
						}
						do_unlock_readonly('problemstart');
						$("#problemstart").val(form_content['problemstart_input']);
						if (form_content['scheduled_checkbox'] == "on") {
							$("#scheduled_checkbox").prop("checked", true);
							do_scheduled();
							$("#scheduled_date").val(form_content['scheduled_date_input']);
						}
						$("#frm_location").focus();
					//======================================
					} else {
						set_parked_form_data(null);
					}
				} catch (e) {
				}
			}

/*			var watch_val;
			var log;
			function start_polling() {
				watch_val = window.setInterval("do_watch()", <?php print $auto_poll_time * 100;?>);
			}

			function stop_polling() {
				if (watch_val) {
					window.clearInterval(watch_val);
				}
			}*/

			function do_watch() {
				//console.log(get_infos_array['parked_form_data']['ticket_add_form_data']);
				try {
					if ((
						($("#frm_location").val().trim().length > 0 && $("#frm_location").val().length > parking_form_data_min_trigger_chars) ||
						($("#frm_facility_id").val() != 0) ||
						($("#frm_phone").val().trim().length > 0 && $("#frm_phone").val().length > parking_form_data_min_trigger_chars) ||
						($("#frm_description").val().trim().length > 0 && $("#frm_description").val().length > parking_form_data_min_trigger_chars)
						) && (parking_form_data_min_trigger_chars != 0)
					) {
						var new_form_data = $("#ticket_add").serializeArray();
						//======================================
						//if ((JSON.stringify(new_form_data) != JSON.stringify(parent.frames["navigation"].ticket_add_form_data))) {
						if ((JSON.stringify(new_form_data) != JSON.stringify(get_infos_array['parked_form_data']['ticket_add_form_data']))) {
							//console.log(get_infos_array['parked_form_data']['ticket_add_form_data']);
						//======================================
							set_parked_form_data(new_form_data);
						}
					} else {
						new_form_data = null;
						set_parked_form_data(new_form_data);
					}
				} catch (e) {
				}
			}

			function do_scheduled() {
				if ($("#scheduled_checkbox").prop("checked") == true) {
					$("#frm_do_scheduled").val("1");
					$("#scheduled_date").prop("type", "text");
					$("#scheduled_date").focus();
				} else {
					$("#scheduled_date").prop("type", "hidden");
					$("#frm_do_scheduled").val("0");
				}
			}

			function do_reset_form() {
				$("#ticket_add").trigger("reset");
				$("#frm_severity").css({"background-color": "#0000FF"});	//Blue
				do_lock_readonly("problemstart");
				$('#frm_location').prop("readonly", false);
				$("#frm_do_scheduled").val("0");
				$("#incident_type_protocol").html("");
				$("#scheduled_date").prop("type", "hidden");
				set_parked_form_data();
			}

			$(document).ready(function() {
				$("#problemstart").datetimepicker({
					locale: '<?php print get_variable("_locale");?>',
					format: '<?php print $moment_date_format;?>',
					sideBySide: true
				});

				$("#problemstart").on("dp.change", function (e) {
					$("#scheduled_date").data("DateTimePicker").minDate(e.date);
				});

				$("#scheduled_date").datetimepicker({
					locale: '<?php print get_variable("_locale");?>',
					format: '<?php print $moment_date_format;?>',
					sideBySide: true
				});

				$("#scheduled_date").on("dp.change", function (e) {
					$("#problemstart").data("DateTimePicker").maxDate(e.date);
				});

				$("#scheduled_date").data("DateTimePicker").minDate(moment($("#problemstart").val(), "<?php print $moment_date_format;?>"));
				//======================================
				//start_polling();
				//get_parked_form_data();
				//======================================
				if ($("#frm_facility_id").val() == 0) {
					$("#frm_location").focus();
				} else {
					if ($("#frm_phone").val().trim().length == 0) {
						$("#frm_phone").focus();
					} else {
						$("#frm_description").focus();
					}
				}
				<?php show_prevent_browser_back_button();?>
				//======================================
				var change_situation_first_set = 0;
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					get_infos_array = JSON.parse(event.data);
					//console.log(get_infos_array["parked_form_data"]["ticket_add_timestamp"]);
					if (change_situation_first_set == 0) { 
						//start_polling();
						get_parked_form_data();
						$("#screen_id").val(get_infos_array['screen']['screen_id']);
						change_situation_first_set = 1;
					}
					do_watch();
					// can message back using event.source.postMessage(...)
				});
				//======================================
			});

		</script>
	</head>
<!-- 	<body onload="check_frames();" onunload="stop_polling();"> -->
	<body onload="check_frames();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" id="main_container">
			<form id="ticket_add" name="add" method="post" action="ticket_add.php">
				<input type="hidden" name="function" value="insert">
				<input type="hidden" name="ticket_id" value="<?php print $ticket_id;?>">
				<input type="hidden" id="frm_do_scheduled" name="frm_do_scheduled" value=0>
				<input type="hidden" name="frm_group[]" value="1">
				<input type="hidden" id="frm_lat" name="frm_lat" value="">
				<input type="hidden" id="frm_lng" name="frm_lng" value="">
				<input type="hidden" id="incident_type" value=0>
				<input type="hidden" name="screen_id" id="screen_id" value="">
				<div class="row infostring">
					<div<?php print get_table_id_title_str("ticket", $ticket_id);?> class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("New") . get_table_id($ticket_id) . " - " . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="cancel_button('', '');" tabindex=14><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="do_reset_form();" tabindex=13><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="validate();" tabindex=12><?php print get_text("Save");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_left">
								<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
									<tr>
										<th style="width: 25%; border-top: 0px;"<?php print get_title_str(get_help_text("_loca", true) . $additional_helptext_form_data_parking);?>>
											<?php print get_text("Incident location"); ?>:
										</th>
										<td style="width: 75%; border-top: 0px;">
											<textarea id="frm_location" name="frm_location" class="form-control" tabindex=1 cols="48" rows="3"></textarea>
											<?php print $incident_location_select_array["select_str"];?>		
										</td>
									</tr>
									<tr>
										<th<?php print get_title_str(get_help_text("_callback", true) . $additional_helptext_form_data_parking);?>><?php print get_text("Callback phone");?>:</th>
										<td>
											<input type="text" id="frm_phone" name="frm_phone" class="form-control" tabindex=3 value="">
										</td>
									</tr>
									<tr>
										<th<?php print get_title_str(get_help_text("_synop", true) . $additional_helptext_form_data_parking);?>><?php print get_text("Synopsis");?>:</th>
										<td>
											<textarea  id="frm_description" name="frm_description" class="form-control" tabindex=4 cols="48" rows="3" wrap="virtual"></textarea>
											<?php print get_textblock_select_str("synopsis", "document.add.frm_description", "", 0, "");?>	
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_caller");?>>
											<?php print get_text("Reported by");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
										</th>
										<td>
											<input type="text" id="frm_contact" name="frm_contact" class="form-control mandatory" value="" tabindex=6>
											<?php print $reported_by_select_array["reported_by_select"];?>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_incident_type");?>>
											<?php print get_text("Incident type");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
										</th>
										<td>
										<div style="float:left; width: 55%;">
											<?php print get_incident_type_select_str("add", "frm_in_types_id");?>
										</div>
										<div style="float:right; width: 44%;">
											<?php print get_priority_select_str("add", "frm_severity", 0);?>
										</div>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_cmnts");?>>
											<?php print get_text("Comments");?>:
										</th>
										<td>
											<textarea id="frm_comments" name="frm_comments" class="form-control" tabindex=9 cols="48" rows="3" wrap="virtual" tabindex=9></textarea>
											<?php print get_textblock_select_str("description", "document.add.frm_comments", "", 0, "");?>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_right">
								<table class="table table-striped table-condensed" style="table-layout: fixed;">
									<tr>
										<th style="width: 25%; border-top: 0px;"<?php print get_help_text_str("_name");?>>
											<?php print get_text("Incident name") . ":" . $inc_name_mandatory_str;?>
										</th>
										<td style="width: 5%; border-top: 0px;"></td>
										<td style="width: 70%; border-top: 0px;">
											<input type="text" id="frm_incident_name" name="frm_incident_name" class="form-control<?php print $inc_name_class;?>" value="<?php print $incident_name;?>" <?php print $inc_name_readonly_or_tabindex_str;?>>
										</td>
									</tr>
									<tr style="height: 45px;">
										<td colspan=3></td>
									</tr>
									<tr style="height: 45px;">
										<th<?php print get_help_text_str("_start");?>>
											<?php print get_text("Run Start");?>:
										</th>
										<td>
											<span id="lock_problemstart" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('problemstart');"></span>
										</td>
										<td>
											<input type="text" id="problemstart" name="problemstart_input" class="form-control" value="<?php print date(trim(get_variable("date_format")));?>" readonly>
											<input type="hidden" id="problemstart_mysql_timestamp" class="form-control" name="problemstart">
										</td>
									</tr>
									<tr style="height: 45px;">
										<th<?php print get_help_text_str("_booked");?>>
											<?php print get_text("Scheduled Date");?>:
										</th>
										<td>
											<input type="checkbox" id="scheduled_checkbox" name="scheduled_checkbox" onClick ="do_scheduled();">
										</td>
										<td>
											<input type="hidden" id="scheduled_date" name="scheduled_date_input" class="form-control" value="<?php print date(trim(get_variable("date_format")));?>">
											<input type="hidden" id="scheduled_date_mysql_timestamp" class="form-control" name="scheduled_date">
										</td>
									</tr>
									<tr style="height: 45px;">
										<td colspan=3></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_proto");?>>
											<?php print get_text("Protocol");?>:
										</th>
										<td></td>
										<td id="incident_type_protocol" style="white-space: normal !important;"></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
		<?php show_infobox();?>
	</body>
</html>	
	<?php
}
?>