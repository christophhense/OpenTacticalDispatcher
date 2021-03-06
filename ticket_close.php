<?php
error_reporting(E_ALL);
ini_set('session.cookie_samesite', 'Strict');
@session_start();
require_once ("./incs/functions.inc.php");
do_login(basename(__FILE__));
set_session_expire_time();

$datetime_now = mysql_datetime();
$function = "";
if (isset ($_POST['function']) && (is_super() || is_admin() || is_operator())) {
	$function = $_POST['function'];
}
switch ($function) {
case "update":

	$query_old_data = "SELECT `description`, " .
		"`comments` " .
		"FROM `tickets` " .
		"WHERE `id` = " . $_POST['frm_ticket_id'] . " " .
		"LIMIT 1;";

	$result_old_data = db_query($query_old_data, __FILE__, __LINE__);
	$row_old_data = db_fetch_assoc($result_old_data);

	$log_str = get_text("Run End") . ": " . date(get_variable("date_format"), strtotime($_POST['problemend'])) . "  ";
	if ($row_old_data['description'] != $_POST['frm_synopsis']) {
		$log_str .= get_text("Synopsis") . ": " . $_POST['frm_synopsis'] . " ";
	}
	if ($row_old_data['comments'] != $_POST['frm_disp']) {
		$log_str .= get_text("Comments") . ": " . $_POST['frm_disp'] . " ";
	}

	$query = "UPDATE `tickets` " .
		"SET `problemend` = " . quote_smart(trim($_POST['problemend'])) . ", " .
		"`comments` = " . quote_smart(trim($_POST['frm_disp'])) . ", " .
		"`description` = " . quote_smart(trim($_POST['frm_synopsis'])) . ", " .
		"`updated` = '" . $datetime_now . "', " .
		"`user_id` = " . $_SESSION['user_id'] . ", " .
		"`status` = " . $GLOBALS['STATUS_CLOSED'] . " " .
		"WHERE `id` = " . $_POST['frm_ticket_id'] . " " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);

	foreach ($_POST as $VarName => $assign_id) {
		if (substr($VarName, 0, 8) == "frm_ckbx") {

			$query = "UPDATE `assigns` " .
				"SET `clear` = '" . $datetime_now . "', " .
				"`updated` = '" . $datetime_now . "' " .
				"WHERE `id` = " . $assign_id . " " .
				"LIMIT 1;";

			db_query($query, __FILE__, __LINE__);
			set_unit_updated($assign_id);

			$cl_res_query = "SELECT `assigns`.`id` AS `assign_id`, " .
				"`assigns`.`unit_id` AS `unit_id` " .
				"FROM `assigns` " .
				"WHERE `assigns`.`id` = " . $assign_id . " " .
				"LIMIT 1;";

			$cl_res_result = db_query($cl_res_query, __FILE__, __LINE__);
			$cl_res_row = stripslashes_deep(db_fetch_array($cl_res_result));
			do_log($GLOBALS['LOG_CALL_CLEAR'], $_POST['frm_ticket_id'], $cl_res_row['unit_id']);
			do_receipt_message($cl_res_row['unit_id']);
		}
	}

	$query = "SELECT * " .
		"FROM `tickets` " .
		"WHERE `id` = " . $_POST['frm_ticket_id'] ." " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = db_fetch_assoc($result, __FILE__, __LINE__);
	do_log($GLOBALS['LOG_INCIDENT_CLOSE'], $_POST['frm_ticket_id'], 0, $log_str);
	unset ($result_old_data, $result, $cl_res_result);
?>
<script>
	var changes_data ='{"type":"message","item":"success","action":"<?php print get_text("Incident closed");?>"}';
	window.parent.navigationbar.postMessage(changes_data, window.location.origin);
	window.location.href="situation.php?screen_id=<?php print $_POST['screen_id'];?>";
</script>
	<?php
	break;
default:
	$moment_date_format = php_to_moment(get_variable("date_format"));
	$auto_poll_settings = explode(",", get_variable("auto_poll"));
	$auto_poll_time = trim($auto_poll_settings[0]);
	$parking_form_data_settings = explode(",", get_variable("parking_form_data"));
	$additional_helptext_form_data_parking = "";
	if (trim($parking_form_data_settings[4]) != 0) {
		$additional_helptext_form_data_parking = " " . get_help_text("parked_trigger_chars", true) . ": " . trim($parking_form_data_settings[4]) . " " . get_help_text("parked_seconds", true) . ": " . trim($parking_form_data_settings[5]);
	}
	$query = "SELECT * " .
		"FROM `tickets` " .
		"WHERE `id` = " . $_GET['ticket_id'] . " " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = db_fetch_assoc($result);

	$problemstart = "2017-01-01 00:00:00";
	if (isset ($row['problemstart']) && is_datetime($row['problemstart'])) {
		$problemstart = $row['problemstart'];
	}

	$query = "SELECT *, " .
		"`assigns`.`id` AS `assign_id`, " .
		"`u`.`handle` AS `unit_handle`, " .
		"`u`.`name` AS `unit_name`, " .
		"`u`.`remote_data_services` AS `unit_remote_data_services`, " .
		"`u`.`unit_phone` AS `unit_phone`, " .
		"`u`.`unit_email` AS `unit_email`, " .
		"`u`.`mobile` AS `unit_mobile`, " .
		"`u`.`description` AS `unit_descr`, " .
		"`u`.`capabilities`, " .
		"`u`.`contact_name`, " .
		"`t`.`name` AS `type_name`, " .
		"`t`.`text_color` AS `text_color`, " .
		"`t`.`bg_color` AS `background_color`, " .
		"`f`.`handle` AS `guard_house_handle`, " .
		"`f`.`street` AS `guard_house_street`, " .
		"`f`.`city` AS `guard_house_city` " .
		"FROM `assigns` " .
		"LEFT JOIN `units` `u` ON (`assigns`.`unit_id` = `u`.`id`) " .
		"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
		"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
		"WHERE `assigns`.`ticket_id` = " . $_GET['ticket_id'] . " " .
		"ORDER BY `assigns`.`id` ASC;";

	$asgn_result = db_query($query, __FILE__, __LINE__);

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
			var parking_form_data_min_trigger_chars = <?php print trim($parking_form_data_settings[4]);?> + 0;
			var ticket_id = <?php print $_GET['ticket_id'];?> + 0;

			function validate() {
				var errmsg = "";
				var problemstart = "<?php print $problemstart;?>";
				var problemend = moment($("#problemend").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss");
				var datetime_now = "<?php print $datetime_now;?>";
				if (
					!moment(problemend, "YYYY-MM-DD HH:mm:ss").isValid() ||
					moment(problemend, "YYYY-MM-DD HH:mm:ss").isAfter(moment(datetime_now, "YYYY-MM-DD HH:mm:ss").add(1, 'm')) ||
					moment(problemend, "YYYY-MM-DD HH:mm:ss").isBefore(moment(problemstart, "YYYY-MM-DD HH:mm:ss"))
				) {
					errmsg += "<?php print get_text('Invalid problemend');?><br>";
				}
				if ($("#frm_disp").val().trim().length == 0) {
					errmsg += "<?php print get_text("Disposition is required");?><br>";
				}
				if (errmsg != "") {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", errmsg);
					return false;
				} else {
					$("#problemend_mysql_timestamp").val(problemend);
					set_parked_form_data();
					$("#ticket_close").submit();
				}
			}

			function set_parked_form_data(data) {
				try {
					if ((data !== undefined) && (data != null)) {
						var changes_data = {"type":"set_parked_form_data","item":"ticket_close_form_data","action":ticket_id};
						changes_data.ticket_close_form_data = data;
						changes_data = JSON.stringify(changes_data);
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						var changes_data ={"type":"set_parked_form_data","item":"ticket_close_timestamp","action":ticket_id,"datetime":Date.now()};
						changes_data = JSON.stringify(changes_data);
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
					} else {
						var changes_data = {"type":"set_parked_form_data","item":"ticket_close_delete","action":ticket_id};
						changes_data = JSON.stringify(changes_data);
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
					}
				} catch (e) {
				}
			}

			function get_parked_form_data() {
				try {
					if (ticket_id != 0) {
						if (
							(new_infos_array['parked_form_data']['ticket_close_timestamp'][ticket_id] !== undefined) && 
							(new_infos_array['parked_form_data']['ticket_close_timestamp'][ticket_id] != null)
						) {
							var form_content = [];
							for (var key in new_infos_array['parked_form_data']['ticket_close_form_data'][ticket_id]) {
								form_content[new_infos_array['parked_form_data']['ticket_close_form_data'][ticket_id][key]['name']] = new_infos_array['parked_form_data']['ticket_close_form_data'][ticket_id][key]['value'];
							}
						}
						$("#frm_synopsis").val(form_content['frm_synopsis']);
						$("#frm_disp").val(form_content['frm_disp']);
						do_unlock_readonly("problemend");
						$("#problemend").val(form_content['problemend_input']);
						set_textblock("", frm_disp);
						$("#frm_location").focus();
					}
				} catch (e) {
				}
			}

			$(document).ready(function() {

				$("#problemend").datetimepicker({
					locale: '<?php print get_variable("_locale");?>',
					format: '<?php print $moment_date_format;?>',
					widgetPositioning: {
						vertical: 'bottom'
					},
					sideBySide: true
				});

				$("#problemend").data("DateTimePicker").minDate(moment("<?php print $row['problemstart'];?>", "YYYY-MM-DD HH:mm:ss"));

				$("#frm_disp").focus();
				var changes_data ='{"type":"current_script","item":"script","action":"ticket_close"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
				<?php show_prevent_browser_back_button();?>
				var change_situation_first_set = 0;
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					new_infos_array = JSON.parse(event.data);
					if (change_situation_first_set == 0) {
						get_parked_form_data();
						$("#screen_id").val(new_infos_array['screen']['screen_id']);
						change_situation_first_set = 1;
					}
					if (
						(ticket_id != 0) && 
						($("#frm_disp").val().trim().length > 0) && 
						($("#frm_disp").val().length > parking_form_data_min_trigger_chars) && 
						(parking_form_data_min_trigger_chars != 0)
					) {
						var new_form_data = $("#ticket_close").serializeArray();
						var ticket_close_form_data = [];
						try {
							ticket_close_form_data = new_infos_array['parked_form_data']['ticket_close_form_data'][ticket_id];
						} catch (e) {
						}
						if (JSON.stringify(new_form_data) != JSON.stringify(ticket_close_form_data)) {
							set_parked_form_data(new_form_data);
						}
					}
				});
			});

		</script>
	</head>
	<body onload="check_frames();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" id="main_container">
			<div class="row infostring">
				<div<?php print get_table_id_title_str("ticket", $row['id']);?> class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
					<?php print get_text("Close incident") . get_table_id($row['id']) . " - " . get_variable("page_caption");?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-1">
					<div class="container-fluid" style="position: fixed;">
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="window.location.href='ticket_edit.php?ticket_id=<?php print $_GET['ticket_id'];?>';" tabindex=5><?php print get_text("Cancel");?></button>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="set_parked_form_data(); document.frm_note.reset(); do_lock_readonly('problemend');" tabindex=4><?php print get_text("Reset");?></button>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="validate();" tabindex=3><?php print get_text("Save");?></button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<div class="panel panel-default" style="padding: 0px;">
						<div id="table_left">
							<form id="ticket_close" name="frm_note" method="post" action="ticket_close.php">
								<input type="hidden" name="function" value="update">
								<input type="hidden" name="screen_id" id="screen_id" value="">
								<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
									<tr>
										<th style="width: 20%; border-top: 0px;"<?php print get_title_str(get_help_text("_synop", true) . $additional_helptext_form_data_parking);?>>
											<?php print get_text("Synopsis");?>:
										</th>
										<td style="width: 5%; border-top: 0px;"></td>
										<td style="width: 75%; border-top: 0px;">
											<textarea id="frm_synopsis" name="frm_synopsis" class="form-control" cols=56 rows=4><?php print remove_nls($row['description']);?></textarea>
											<?php print get_textblock_select_str("synopsis", "document.frm_note.frm_synopsis", "", 0, "");?>
										</td>
									</tr>
									<tr>
										<th<?php print get_title_str(get_help_text("_cmnts", true) . $additional_helptext_form_data_parking);?>>
											<?php print get_text("Comments");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
										</th>
										<td></td>
										<td>
											<textarea id="frm_disp" name="frm_disp" class="form-control mandatory" cols=56 rows=4 tabindex=1><?php print remove_nls($row['comments']);?></textarea>
											<?php print get_textblock_select_str("close", "document.frm_note.frm_disp", "", 0, "");?>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_end");?>>
											<?php print get_text("Run End");?>:
										</th>
										<td><span id="lock_problemend" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('problemend');"></span></td>
										<td>
											<input type="text" id="problemend" name="problemend_input" class="form-control" value="<?php print date(trim(get_variable("date_format")));?>" readonly>
											<input type="hidden" id="problemend_mysql_timestamp" name="problemend" class="form-control">
										</td>
									</tr>
	<?php
		if (db_affected_rows($asgn_result) > 0) {
			while ($asgn_row = stripslashes_deep(db_fetch_array($asgn_result))) {
				if (empty ($asgn_row['clear'])) {
	?>
									<tr>
										<th><?php print get_text("Clear");?>:</th>
										<td>
											<input type="checkbox" name="frm_ckbx_<?php print $asgn_row['assign_id'];?>" value="<?php print $asgn_row['assign_id'];?>" checked>
										</td>
										<td<?php print get_title_unit_str($asgn_row);?> style="vertical-align: middle;">
											<span class="label" style="background-color: <?php print $asgn_row['background_color']?>; color: <?php print $asgn_row['text_color'];?>; font-weight: bold; font-size: 12px;">
												<?php print remove_nls($asgn_row['unit_handle']);?>
											</span>
										</td>
									</tr>
	<?php
				}
			}
		}
	?>
								</table>
								<input type="hidden" name="frm_ticket_id" value="<?php print $_GET['ticket_id'];?>">
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<div class="panel panel-default" style="padding: 0px;">
						<div id="table_right">
							<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
								<?php show_head($_GET['ticket_id'], false, false);?>
							</table>
						</div>
				</div>
				</div>
				<div class="col-md-1"></div>
			</div>
		</div>
	<?php show_infobox();?>
	</body>
</html>
	<?php
}
?>