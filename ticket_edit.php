<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/units.inc.php");
do_login(basename(__FILE__));

$datetime_now = mysql_datetime();
if (array_key_exists('ticket_id', ($_GET))) {
	$_SESSION['active_ticket'] = $_GET['ticket_id'];
	$ticket_id = $_GET['ticket_id'];
} else {
	if (array_key_exists('id', ($_SESSION))){
		$ticket_id = $_SESSION['active_ticket'];
	} else {
		echo "error at " . __LINE__;
	}
}
$ticket_id = 0;
if (isset ($_GET['ticket_id'])) {
	$ticket_id = $_GET['ticket_id'];
}
$unit_id_str = "";
if (isset ($_GET['unit_id'])) {
	$unit_id_str = "&unit_id=" . $_GET['unit_id'];
}
$function = "";
if (isset ($_GET['function']) && (is_super() || is_admin() || is_operator())) {
	$function = $_GET['function'];
}
if (isset ($_POST['function']) && (is_super() || is_admin() || is_operator())) {
	$function = $_POST['function'];
	$ticket_id = $_POST['ticket_id'];
}
switch ($function) {
	case "update":
		set_session_expire_time("on");

		$query_old_data = "SELECT * " .
			"FROM `tickets` " .
			"WHERE `id` = " . $ticket_id . " " .
			"LIMIT 1;";

		$result_old_data = db_query($query_old_data, __FILE__, __LINE__);
		$row_old_data = stripslashes_deep(db_fetch_array($result_old_data));
		if (!isset ($_POST['frm_status'])) {
			$_POST['frm_status'] = $row_old_data['status'];
		}
		$curr_groups = $_POST['frm_exist_groups'];
		$groups = isset ($_POST['frm_group'])? ", " . implode(',', $_POST['frm_group']) . "," : $_POST['frm_exist_groups'];	//fixes error when accessed from view ticket screen..
		$query_problemstart_str = "";
		if (isset ($_POST['problemstart'])) {
			$query_problemstart_str = "`problemstart` = ".		quote_smart(trim($_POST['problemstart'])) . ", ";
		}
		if (isset ($_POST['scheduled_date']) && ($_POST['frm_status'] == $GLOBALS['STATUS_SCHEDULED'])) {
			$log_scheduled = $_POST['scheduled_date'];
			$query_scheduled = quote_smart($_POST['scheduled_date']);
		} else {
			$log_scheduled = "NULL";
			$query_scheduled = "NULL";
		}
		if ($_POST['frm_status'] != $GLOBALS['STATUS_CLOSED']) {
			$log_problemend = "NULL";
			$query_problemend = "NULL";
		} else {
			$log_problemend = $_POST['problemend'];
			$query_problemend = quote_smart(trim($_POST['problemend']));
		}

		$query = "UPDATE `tickets` SET " .
			"`contact` = " . 			quote_smart(trim($_POST['frm_contact'])) .", " .
			"`location` = " . 			quote_smart(trim($_POST['frm_location'])) .", " .
			"`phone` = " . 				quote_smart(trim($_POST['frm_phone'])) . ", " .
			"`facility_id` = " . 		$_POST['frm_facility_id'] . ", " .
			"`lat` = " . 				floatval($_POST['frm_lat']) . ", " .
			"`lng` = " . 				floatval($_POST['frm_lng']) . ", " .
			"`incident_name` = " . 				quote_smart(trim($_POST['frm_incident_name'])) . ", " .
			"`severity` = " . 			intval($_POST['frm_severity']) . ", " .
			"`incident_type_id` = " . 	intval($_POST['frm_in_types_id']) . ", " .
			"`status` = " . 			intval($_POST['frm_status']) . ", " .
			$query_problemstart_str .
			"`problemend` = ".			$query_problemend . ", " .
			"`description` = " .		quote_smart(trim($_POST['frm_description'])) .", " .
			"`comments` = " . 			quote_smart(trim($_POST['frm_comments'])) .", " .
			"`booked_date` = " .		$query_scheduled . ", " .
			"`user_id` = " . 			$_SESSION['user_id'] . ", " .
			"`updated` = '" . 			$datetime_now . "' " .
			"WHERE `id` = " . 			$ticket_id . ";";

		db_query($query, __FILE__, __LINE__);

		$list = $_POST['frm_exist_groups'];
		$ex_grps = explode(',', $list);

		if ($curr_groups != $groups) {
			foreach ($_POST['frm_group'] as $posted_grp) {
				if (!(in_array($posted_grp, $ex_grps))) {
					insert_into_allocates($posted_grp, $GLOBALS['TYPE_TICKET'], $ticket_id, $_SESSION['user_id'], $datetime_now);
				}
			}
			foreach ($ex_grps as $existing_grp) {
				if (in_array($existing_grp, get_allocates(4, $ticket_id))) {
					if (!(in_array($existing_grp, $_POST['frm_group']))) {

						$query  = "DELETE FROM `allocates` " .
							"WHERE `type` = " . $GLOBALS['TYPE_TICKET'] . " " .
							"AND `group` = " . $existing_grp . " " .
							"AND `resource_id` = " . $ticket_id . ";";

						db_query($query, __FILE__, __LINE__);
					}
				}
			}
		}

		$query = "SELECT * " .
			"FROM `assigns` " .
			"WHERE `ticket_id` = " . $ticket_id . " " .
			"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00');";

		$result = db_query($query, __FILE__, __LINE__);
		$num_assigns = db_num_rows($result);

		$query_facilities = "SELECT `handle` " .
			"FROM `facilities` " .
			"WHERE `id` = " . $_POST['frm_facility_id'] . ";";

		$result_facilities = db_query($query_facilities, __FILE__, __LINE__);
		$row_facilities = db_fetch_assoc($result_facilities);

		switch ($_POST['frm_facility_changed']) {
			case 0:
				break;
			case 1:
				if ($_POST['frm_exist_fac'] == 0) {
					do_log($GLOBALS['LOG_FACILITY_INCIDENT_OPEN'], $ticket_id, 0, $row_facilities['handle'], $_POST['frm_facility_id'], "", "", "");
				} else {
					if ($_POST['frm_facility_id'] == 0) {
						do_log($GLOBALS['LOG_FACILITY_INCIDENT_UNSET'], $ticket_id, 0, "", 0, "", "", "");
					} else {
						do_log($GLOBALS['LOG_FACILITY_INCIDENT_CHANGE'], $ticket_id, 0, $row_facilities['handle'], $_POST['frm_facility_id'], "", "", "");
					}
				}
				break;
			default:
				print "ERROR in " . basename(__FILE__) . " " . __LINE__ . "<br>";
		}

		$query_inc = "SELECT `type` " .
			"FROM `incident_types` " .
			"WHERE `id` = " . $_POST['frm_in_types_id'] . ";";

		$result_inc = db_query($query_inc, __FILE__, __LINE__);
		$inc_type = stripslashes_deep(db_fetch_array($result_inc));
		if ($row_old_data['status'] != $_POST['frm_status']) {
			switch($_POST['frm_status']) {
				case 1:
					$log_type = "LOG_INCIDENT_CLOSE";
					$log_str = get_text("Run End") . ": " . date(get_variable("date_format"), strtotime($log_problemend)) . "  ";
					$row_old_data['problemend'] = $_POST['problemend'];
					break;
				case 2:
					$log_type = "LOG_INCIDENT_OPEN";
					$log_str = "";
					break;
				case 3:
					$log_type = "LOG_INCIDENT_SCHEDULED";
					$log_str = get_text("Scheduled Date") . ": " . date(get_variable("date_format"), strtotime($log_scheduled)) . "  ";
					$row_old_data['booked_date'] = $_POST['scheduled_date'];
					break;
				default:
			}
		} else {
			$log_type = "LOG_INCIDENT_CHANGE";
			$log_str = "";
		}
		if ($row_old_data['location'] != $_POST['frm_location']) {
			$log_str .= get_text("Incident location") . ": " . $_POST['frm_location'] . "  ";
		}
		if ($row_old_data['phone'] != $_POST['frm_phone']) {
			$log_str .= get_text("Callback phone") . ": " . $_POST['frm_phone'] . "  ";
		}
		if ($row_old_data['description'] != $_POST['frm_description']) {
			$log_str .= get_text("Synopsis") . ": " . $_POST['frm_description'] . "  ";
		}
		if ($row_old_data['contact'] != $_POST['frm_contact']) {
			$log_str .= get_text("Reported by") . ": " . $_POST['frm_contact'] . "  ";
		}
		if ($row_old_data['incident_type_id'] != $_POST['frm_in_types_id']) {
			$log_str .= get_text("Incident type") . ": " . $inc_type[0] . "  ";
		}
		if ($row_old_data['severity'] != $_POST['frm_severity']) {
			$log_str .= get_text("Severity") . ": ";
			switch($_POST['frm_severity']) {
				case 0:
					$log_str .= get_text("Normal") . "  ";
					break;
				case 1:
					$log_str .= get_text("Medium") . "  ";
					break;
				case 2:
					$log_str .= get_text("High") . "  ";
					break;
				default:
			}
		}
		if ($row_old_data['comments'] != $_POST['frm_comments']) {
			$log_str .= get_text("Comments") . ": " . $_POST['frm_comments'] . "  ";
		}
		if ($row_old_data['incident_name'] != $_POST['frm_incident_name']) {
			$log_str .= get_text("Incident name") . ": " . $_POST['frm_incident_name'] . "  ";
		}
		if ($row_old_data['problemstart'] != $_POST['problemstart']) {
			$log_str .= get_text("Run Start") . ": " . date(get_variable("date_format"), strtotime($_POST['problemstart'])) . "  ";
		}
		if (($row_old_data['booked_date'] != $_POST['scheduled_date']) && ($row_old_data['booked_date'] != null)) {
			$log_str .= get_text("Scheduled Date") . ": " . date(get_variable("date_format"), strtotime($log_scheduled)) . "  ";
		}
		if (($row_old_data['problemend'] != $_POST['problemend']) && ($row_old_data['problemend'] != null) && ($_POST['frm_status'] != 1)) {
			$log_str .= get_text("Run End") . ": " . date(get_variable("date_format"), strtotime($_POST['problemend'])) . "  ";
		}
		do_log($GLOBALS[$log_type], $ticket_id, 0, $log_str, 0, "", "", "");
		unset ($_SESSION['active_ticket']);
		break;
	case "assigns":
		show_units_list("assigns", 0, 0, $_GET['ticket_id']);
		break;
	case "actions":
		?>
	<?php print show_day_night_style();?>
	<table id="data_3" class="table table-striped table-condensed" style="table-layout: fixed;">
		<?php show_actions($_GET['ticket_id'], false);?>
	</table>
		<?php
		break;
	default:
		set_session_expire_time("on");
		$moment_date_format = php_to_moment(get_variable("date_format"));
		$reported_by_select_array = get_reported_by_select_str("ticket_edit_form");

		$query = "SELECT *, " .
			"UNIX_TIMESTAMP(`problemstart`) AS `problemstart2`, " .
			"UNIX_TIMESTAMP(`t`.`datetime`) AS `datetime`, " .
			"UNIX_TIMESTAMP(`t`.`updated`) AS `updated`, " .
			"`t`.`id`, " .
			"`t`.`description` AS `ticket_description`, " .
			"`t`.`status` AS `ticket_status`, " .
			"`u`.`name` AS `user_name` " .
			"FROM `tickets` `t` " .
			"LEFT JOIN `incident_types` `ty` ON (`t`.`incident_type_id` = `ty`.`id`) " .
			"LEFT JOIN `users` `u` ON (`t`.`user_id` = `u`.`id`) " .
			"WHERE `t`.`id` = " . $ticket_id . " LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$row = stripslashes_deep(db_fetch_array($result));
		if (($row['ticket_status'] == $GLOBALS['STATUS_OPEN']) || ($row['ticket_status'] == $GLOBALS['STATUS_SCHEDULED'])) {
			$buttons_display_str = "";
		} else {
			$buttons_display_str = " display: none;'";
		}
		if ($row['facility_id'] > 0) {
			$location_readonly_str = " readonly";
		} else {
			$location_readonly_str = "";
		}
		$inc_name_readonly_or_tabindex_str = " tabindex=11";
		$inc_name_mandatory_str = "";
		$inc_num_array = unserialize(base64_decode(get_variable("_inc_num")));
		if (strpos(get_variable("_inc_num"), "{") > 0) {
			$inc_num_array = unserialize(get_variable("_inc_num"));
		}
		switch ($inc_num_array[0]) {
			case 7:
				break;
			default:
				$inc_name_mandatory_str = " <span style='font-size: small; vertical-align: top; color: red;'>*</span>";
				$inc_name_readonly_or_tabindex_str = " readonly";
		}
		if ($row['booked_date'] != null) {
			$scheduled_checked_str = "checked";
			$scheduled_type_str = "text";
			$scheduled_date = date(trim(get_variable("date_format")), strtotime($row['booked_date']));
		} else {
			$scheduled_checked_str = "";
			$scheduled_type_str = "hidden";
			$scheduled_date = date(trim(get_variable("date_format")));
		}
		if (is_datetime($row['problemend'])) {
			$problemend_checked_str = "checked";
			$problemend_type_str = "text";
			$problemend = date(get_variable("date_format"), strtotime($row['problemend']));
		} else {
			$problemend_checked_str = "";
			$problemend_type_str = "hidden";
			$problemend = date(trim(get_variable("date_format")));
		}
		$lat = $row['lat'];
		$lng = $row['lng'];
		$incident_location_select_array = get_incident_location_select_str("ticket_edit_form", $row['facility_id']);
		$auto_ticket_settings = get_auto_ticket_configuration("settings");
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
				var severities = [];
				var protocols = [];
				var reported_by_phone = [];
				var fac_lat = [];
				var fac_lng = [];
				var facility_adress = [];
			<?php print get_severity_protocol_array_str();?>
			<?php print $reported_by_select_array["reported_by_phone"];?>
			<?php print $incident_location_select_array["facility_address"];?>
			<?php print $incident_location_select_array["facility_coordinates"];?>

				function get_units() {
					$.get("ticket_edit.php?function=assigns&ticket_id=<?php print $ticket_id;?>", function(data) {
						$("#table_right_2").html(data);
					});
				}
		
				function get_actions() {
					$.get("ticket_edit.php?function=actions&ticket_id=<?php print $ticket_id;?>", function(data) {
						$("#table_right_3").html(data);
					});
				}

				function validate_ticket_edit_form() {
					var error_message = "";
					var scheduled = moment($("#scheduled_date").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss");
					var problemstart = moment($("#problemstart").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss");
					var problemend = moment($("#problemend").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss");
					var datetime_now = "<?php print $datetime_now;?>";
					if (($("#frm_status").val() == <?php print $GLOBALS['STATUS_CLOSED'];?>) && ($("#frm_comments").val() == "")) {
						error_message += "<?php print get_text("Closed ticket requires disposition data");?><br>";
					}
					if ($("#frm_contact").val() == "") {
						error_message += "<?php print get_text("Reported-by is required");?><br>";
					}
					if ($("#frm_in_types_id").val() == 0)	{
						error_message += "<?php print get_text("Incident type is required");?><br>";
					}
					if (($("#frm_incident_name").val() == "") && ("<?php print $inc_name_readonly_or_tabindex_str;?>"  == " readonly")) {
						error_message += "<?php print get_text("Incident name is required");?><br>";
					}
					if (!moment(problemstart, "YYYY-MM-DD HH:mm:ss").isValid()) {
						error_message += "<?php print get_text("Invalid problemstart");?><br>";
					}
					if (
						(
							!moment(scheduled, "YYYY-MM-DD HH:mm:ss").isValid() ||
							moment(scheduled, "YYYY-MM-DD HH:mm:ss").isBefore(moment(problemstart, "YYYY-MM-DD HH:mm:ss"))
						) && (
							$("#frm_status").val() == <?php print $GLOBALS['STATUS_SCHEDULED'];?>
						)
					) {
						error_message += "<?php print get_text("Invalid scheduled date");?><br>";
					}
					if (
						(
							!moment(problemend, "YYYY-MM-DD HH:mm:ss").isValid() ||
							moment(problemend, "YYYY-MM-DD HH:mm:ss").isAfter(moment(datetime_now, "YYYY-MM-DD HH:mm:ss").add(1, 'm')) ||
							moment(problemend, "YYYY-MM-DD HH:mm:ss").isBefore(moment(problemstart, "YYYY-MM-DD HH:mm:ss"))	
						) && (
							$("#frm_status").val() == <?php print $GLOBALS['STATUS_CLOSED'];?>
						)
					) {
						error_message += "<?php print get_text("Invalid problemend");?><br>";
					}
					if (error_message != "") {
						show_infobox("<?php print get_text("Please correct the following and re-submit");?>", error_message);
						return false;
					} else {
						$("#submit_frm_status").val($("#frm_status").val());
						$("#frm_phone").val($("#frm_phone").val().replace(/[^0-9\+\/\-\*\s#,]/g, ""));
						$("#problemstart_mysql_timestamp").val(problemstart);
						if ((moment(scheduled, "YYYY-MM-DD HH:mm:ss").isValid())) {
							$("#scheduled_date_mysql_timestamp").val(scheduled);
						}
						if ((moment(problemend, "YYYY-MM-DD HH:mm:ss").isValid())) {
							$("#problemend_mysql_timestamp").val(problemend);
						}
						$.post("ticket_edit.php", $("#ticket_edit_form").serialize())
						.done(function (data) {
							show_top_notice("success", "<?php print get_text("Saved");?>");
							goto_window("situation.php?screen_id=" + screen_id_main);
						})
						.fail(function () {
							show_top_notice("danger", "<?php print get_text("Error");?>");
							goto_window("situation.php?screen_id=" + screen_id_main);
						});
					}
				}

				function do_problemend() {
					if ($("#problemend_checkbox").prop("checked") == true) {
						$("#frm_status").val("<?php print $GLOBALS['STATUS_CLOSED'];?>");
						$("#scheduled_checkbox").prop("checked", false);
						$("#scheduled_date").prop("disabled", true);
						$("#scheduled_date").prop("type", "hidden");
						$("#problemend").prop("disabled", false);
						$("#problemend").prop("type", "text");
						$("#problemend").focus();
					} else {
						$("#frm_status").val("<?php print $GLOBALS['STATUS_OPEN'];?>");
						$("#problemend").prop("disabled", true);
						$("#problemend").prop("type", "hidden");
						$("#scheduled_checkbox").prop("checked", false);
						$("#scheduled_date").prop("disabled", true);
						$("#scheduled_date").prop("type", "hidden");
					}
				}

				function do_scheduled() {
					if ($("#scheduled_checkbox").prop("checked") == true) {
						$("#frm_status").val("<?php print $GLOBALS['STATUS_SCHEDULED'];?>");
						$("#problemend_checkbox").prop("checked", false);
						$("#problemend").prop("disabled", true);
						$("#problemend").prop("type", "hidden");
						$("#scheduled_date").prop("disabled", false);
						$("#scheduled_date").prop("type", "text");
						$("#scheduled_date").focus();
					} else {
						$("#frm_status").val("<?php print $GLOBALS['STATUS_OPEN'];?>");
						$("#problemend_checkbox").prop("checked", false);
						$("#problemend").prop("disabled", true);
						$("#problemend").prop("type", "hidden");
						$("#scheduled_date").prop("disabled", true);
						$("#scheduled_date").prop("type", "hidden");
					}
				}

				function do_status_change() {
					if ($("#frm_status").val() == 3) {				//Scheduled
						$("#scheduled_checkbox").prop("checked", true);
						$("#scheduled_date").prop("disabled", false);
						$("#scheduled_date").prop("type", "text");
						$("#scheduled_date").focus();
						
						$("#problemend_checkbox").prop("checked", false);
						$("#problemend").prop("disabled", true);
						$("#problemend").prop("type", "hidden");
					} else {
						if ($("#frm_status").val() == 1) {			//Close
							$("#problemend_checkbox").prop("checked", true);
							$("#problemend").prop("disabled", false);
							$("#problemend").prop("type", "text");
							$("#problemend").focus();
						} else {
							$("#problemend_checkbox").prop("checked", false);
							$("#problemend").prop("disabled", true);
							$("#problemend").prop("type", "hidden");
						}
						$("#scheduled_checkbox").prop("checked", false);
						$("#scheduled_date").prop("disabled", true);
						$("#scheduled_date").prop("type", "hidden");
					}
				}

				function edit_assign(assign_id) {
					<?php if (is_operator() || is_admin() || is_super()) { ?>
					goto_window("assign.php?back=ticket&assign_id=" + assign_id + "&ticket_id=" + <?php print $_GET['ticket_id'];?>);
					<?php } ?>
				}

				function do_reset_edit_form() {
					var default_severity = <?php print $row['severity'];?> + 0;		//+0 against syntax error, in case of DB-row==null
					document.ticket_edit_form.reset();
					switch (default_severity) {
						case 2:
							$("#frm_severity").css({"background-color": "#FF0000"});	//Red
							break;
						case 1:
							$("#frm_severity").css({"background-color": "#008000"});	//Green
						break;
						default:
							$("#frm_severity").css({"background-color": "#0000FF"});	//Blue
					}
					if ($("#frm_in_types_id").val() == 0) {
						$("#incident_type_protocol").html("");
					} else {
						$("#incident_type_protocol").html(protocols[$("#frm_in_types_id").val()]);
					}
					do_lock_disabled("frm_status");
					do_lock_readonly("problemstart");
					if ($("#frm_facility_id").val() == 0) {
						$("#frm_location").prop("readonly", false);
					} else {
						$("#frm_location").prop("readonly", true);
					}
					if ($("#scheduled_checkbox").prop("checked") == false) {
						$("#scheduled_date").prop("type", "hidden");
					}
					if ($("#problemend_checkbox").prop("checked") == false) {
						$("#problemend").prop("type", "hidden");
					}
				}

				$(document).ready(function() {
					$("#problemstart").datetimepicker({
						locale: '<?php print get_variable("_locale");?>',
						format: '<?php print $moment_date_format;?>',
						sideBySide: true
					});
					$("#problemend").datetimepicker({
						locale: '<?php print get_variable("_locale");?>',
						format: '<?php print $moment_date_format;?>',
						sideBySide: true
					});
					$("#problemend").data("DateTimePicker").minDate(moment($("#problemstart").val(), "<?php print $moment_date_format;?>"));
					$("#scheduled_date").datetimepicker({
						locale: '<?php print get_variable("_locale");?>',
						format: '<?php print $moment_date_format;?>',
						sideBySide: true
					});
					$("#scheduled_date").data("DateTimePicker").minDate(moment($("#problemstart").val(), "<?php print $moment_date_format;?>"));
					get_units();
					get_actions();
					set_window_present("ticket_edit");
					<?php show_prevent_browser_back_button();?>
					window.addEventListener("message", function(event) {
						if (event.origin != window.location.origin) return;
						new_infos_array = JSON.parse(event.data);
						screen_id_main = new_infos_array['screen']['screen_id'];
						if (new_infos_array['reload_flags']['units']) {
							if ((current_unit_id !== undefined) && (current_unit_id > 0)) {
								show_assigns(current_unit_id);
							}
							get_units();
						}
						if (new_infos_array['reload_flags']['actions']) {
							get_actions();
						}
					});
				});

			</script>
		</head>
		<body onload="check_frames();">
			<script type="text/javascript" src="./js/wz_tooltip.js"></script>
			<div class="container-fluid" id="main_container">
				<form id="ticket_edit_form" name="ticket_edit_form">
					<input type="hidden" name="function" value="update">
					<input type="hidden" name="ticket_id" value="<?php print $ticket_id;?>">
					<input type="hidden" id="frm_lat" name="frm_lat" value="<?php print $lat;?>">
					<input type="hidden" id="frm_lng" name="frm_lng" value="<?php print $lng;?>">
					<input type="hidden" name="frm_group[]" value="1">
					<input type="hidden" name="frm_exist_fac" value="<?php print $row['facility_id'];?>">
					<input type="hidden" name="frm_exist_groups" value="<?php print (isset($alloc_groups))? $alloc_groups : 1;?>">
					<input type="hidden" name="frm_facility_changed" value="0">
					<input type="hidden" id="incident_type" value="<?php print $row['incident_type_id'];?>">
					<div class="row infostring">
						<div<?php print get_table_id_title_str("ticket", $ticket_id);?> class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
							<?php print get_text("Edit Ticket") . get_table_id($ticket_id) . " - "  . get_variable("page_caption");?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-1">
							<div class="container-fluid" style="position: fixed;">
								<div class="row" style="margin-top: 10px;">
									<div class="col-md-12">
										<button type="button" class="btn btn-xs btn-default" accesskey="c" onclick="goto_window('situation.php?screen_id=' + new_infos_array['screen']['screen_id']);" tabindex=19><?php print get_text("Cancel");?></button>
									</div>
								</div>
								<div class="row" style="margin-top: 10px;">
									<div class="col-md-12">
										<button type="button" class="btn btn-xs btn-default" accesskey="r" onclick="do_reset_edit_form();" tabindex=18><?php print get_text("Reset");?></button>
									</div>
								</div>
								<div class="row" style="margin-top: 10px;">
									<div class="col-md-12">
										<button type="button" class="btn btn-xs btn-default" accesskey="s" onclick="validate_ticket_edit_form();" tabindex=17><?php print get_text("Save");?></button>
									</div>
								</div>
								<div style="margin-top: 20px;">
									<div class="row" style="margin-top: 10px;<?php print $buttons_display_str;?>">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" accesskey="m" onclick="goto_window('ticket_report.php?ticket_id=<?php print $ticket_id;?>&function=dispatch_text&back=ticket');" tabindex=16><?php print get_text("Dispatch text");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" accesskey="u" onclick="goto_window('ticket_report.php?ticket_id=<?php print $ticket_id;?>');" tabindex=15><?php print get_text("Incident Report");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;<?php print $buttons_display_str;?>">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" accesskey="z" onclick="goto_window('ticket_close.php?ticket_id=<?php print $ticket_id;?>');" tabindex=14><?php print get_text("Close_incident_short");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;<?php print $buttons_display_str;?>">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" accesskey="a" onclick="goto_window('action.php?back=ticket&ticket_id=<?php print $ticket_id . $unit_id_str;?>');" tabindex=13><?php print get_text("Add Action");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;<?php print $buttons_display_str;?>">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" accesskey="q" onclick="goto_window('dispatch.php?ticket_id=<?php print $ticket_id;?>');" tabindex=12><?php print get_text("Dispatch_Units_short");?></button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="panel panel-default" style="padding: 0px;">
								<div id="table_left">
									<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
										<tr>
											<th style="width: 20%; border-top: 0px;"<?php print get_help_text_str("_loca");?>><?php print get_text("Incident location");?>: </th>
											<td style="width: 5%; border-top: 0px;"></td>
											<td style="width: 75%; border-top: 0px;">
												<textarea id="frm_location" name="frm_location" class="form-control" tabindex=1 cols=48 rows=3 tabindex=1<?php print $location_readonly_str;?>><?php print remove_nls($row['location']);?></textarea>
												<?php print $incident_location_select_array["select_str"];?>
											</td>
										</tr>
										<tr>
											<th<?php print get_help_text_str("_callback");?>><?php print get_text("Callback phone");?>:</th>
											<td></td>
											<td>
												<input type="text" id="frm_phone" name="frm_phone" class="form-control" tabindex=3 value="<?php print remove_nls($row['phone']);?>">
											</td>
										</tr>
										<tr>
											<th colspan=2<?php print get_help_text_str("_synop");?>><?php print get_text("Synopsis");?>:</th>
											<td>
												<textarea name="frm_description" class="form-control" tabindex=4 cols=48 rows=3 ><?php print remove_nls($row['ticket_description']);?></textarea>
												<?php print get_textblock_select_str("synopsis", "document.ticket_edit_form.frm_description", "", 0, "");?>
											</td>
										</tr>
										<tr>
											<th colspan=2<?php print get_help_text_str("_caller");?>>
												<?php print get_text("Reported by");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
											</th>
											<td>
												<input type="text" id="frm_contact" name="frm_contact" class="form-control mandatory" tabindex=6 value="<?php print remove_nls($row['contact']);?>">
												<?php print $reported_by_select_array["reported_by_select"];?>
											</td>
										</tr>
										<tr>
											<th colspan=2<?php print get_help_text_str("_incident_type");?>>
												<?php print get_text("Incident type");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
											</th>
											<td>
												<div style="float:left; width: 55%;">
													<?php print get_incident_type_select_str("ticket_edit_form", "frm_in_types_id", $row['incident_type_id']);?>
												</div>
												<div style="float:right; width: 40%;">
													<?php print get_priority_select_str("ticket_edit_form", "frm_severity", $row['severity']);?>
												</div>
											</td>
										</tr>	
										<tr>
											<th colspan=2<?php print get_help_text_str("_cmnts");?>>
												<?php print get_text("Comments");?>:
											</th>
											<td>
												<textarea id="frm_comments" name="frm_comments" class="form-control" cols=48 rows=3 tabindex=9><?php print remove_nls($row['comments']);?></textarea>
												<?php print get_textblock_select_str("description", "document.ticket_edit_form.frm_comments", "", 0, "");?>
											</td>
										</tr>
										<tr style="height: 45px;">
											<th colspan=2>
												<div<?php print get_help_text_str("_added");?>><?php print get_text("Incident added");?>:</div>
												<div<?php print get_help_text_str("_asof");?>><?php print get_text("Edited");?>:</div>
											</th>
											<td>
												<div<?php print get_title_str(date(get_variable("date_format"), $row['datetime']));?>>
													<?php print date(get_variable("date_format_time_only"), $row['datetime']) . " " . get_text("by") . " " . get_user_name($row['call_taker_id']);?>
												</div>
												<div<?php print get_title_str(date(get_variable("date_format"), $row['updated']));?>>
													<?php print date(get_variable("date_format_time_only"), $row['updated']) . " " . get_text("by") . " " . remove_nls($row['user_name']);?>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="panel panel-default" style="padding: 0px;">
								<div id="table_right_1">
									<table id="data_1" class="table table-striped table-condensed" style="table-layout: fixed;">
										<tr>
											<th style="width: 25%; border-top: 0px;"<?php print get_help_text_str("_name");?>>
												<?php print  get_text("Incident name") . ":" . $inc_name_mandatory_str;?>
											</th>
											<td style="width: 5%; border-top: 0px;"></td>
											<td style="width: 70%; border-top: 0px;">
												<input type="text" id="frm_incident_name" name="frm_incident_name" class="form-control" value="<?php print remove_nls($row['incident_name']);?>" <?php print $inc_name_readonly_or_tabindex_str;?>>
											</td>
										</tr>
										<tr>
											<th<?php print get_help_text_str("_status");?>>
												<?php print get_text("Status");?>:
											</th>
											<td>
												<span id="lock_frm_status" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_disabled('frm_status');"></span>
											</td>
											<td>
												<?php print get_ticket_status_select_str("ticket_edit_form", "frm_status", "", $row['ticket_status']);?>
												<input type="hidden" id="submit_frm_status" name="frm_status" value="">
											</td>
										</tr>
										<tr>
											<th<?php print get_help_text_str("_start");?>>
												<?php print get_text("Run Start");?>:
											</th>
											<td>
												<span id="lock_problemstart" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('problemstart');"></span>
											</td>
											<td>
												<input type="text" id="problemstart" class="form-control" value="<?php print date(trim(get_variable("date_format")), strtotime($row['problemstart']));?>" readonly>
												<input type="hidden" id="problemstart_mysql_timestamp" name="problemstart" class="form-control">
											</td>
										</tr>
										<tr style="height: 45px;">
											<th<?php print get_help_text_str("_booked");?>>
												<?php print get_text("Scheduled Date");?>:
											</th>
											<td>
												<input type="checkbox" id="scheduled_checkbox" onclick="do_scheduled();" <?php print $scheduled_checked_str;?>>
											</td>
											<td>
												<input type="<?php print $scheduled_type_str;?>" id="scheduled_date" class="form-control" value="<?php print $scheduled_date;?>">
												<input type="hidden" id="scheduled_date_mysql_timestamp" name="scheduled_date" class="form-control">
											</td>
										</tr>
										<tr style="height: 45px;">
											<th<?php print get_help_text_str("_end");?>>
												<?php print get_text("Run End");?>:
											</th>
											<td>
												<input type="checkbox" id="problemend_checkbox" onClick ="do_problemend();" <?php print $problemend_checked_str;?>>
											</td>
											<td>
												<input type="<?php print $problemend_type_str;?>" id="problemend" class="form-control" value="<?php print $problemend;?>">
												<input type="hidden" id="problemend_mysql_timestamp" name="problemend" class="form-control">
											</td>
										</tr>
										<tr>
											<th colspan=2<?php print get_help_text_str("_proto");?>>
												<?php print get_text("Protocol");?>:
											</th>
											<td id="incident_type_protocol" style="white-space: normal !important;">
												<?php print remove_nls($row['protocol']);?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="panel panel-default" style="padding: 0px;">
								<div id="table_right_2"></div>
							</div>
							<div class="panel panel-default" style="padding: 0px;">
								<div id="table_right_3"></div>
							</div>
						</div>
					<div class="col-md-1"></div>
				</form>
			</div>
			<?php show_infobox("small");?>
			<?php show_infobox("large");?>
			<?php show_accesskeys();?>
		</body>
	</html>	
	<?php
}
?>