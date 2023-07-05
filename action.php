<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
do_login(basename(__FILE__));
set_session_expire_time("on");

$datetime_now = mysql_datetime();
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
if (isset ($_POST['function'])) {
	$function = $_POST['function'];
}
switch ($function) {
case "insert":
	if ($_POST['frm_unit'] != "") {
		$unit_id = $_POST['frm_unit'];
	}

	$query 	= "SELECT * " .
		"FROM `actions` " .
		"WHERE `description` = '" . $_POST['frm_description'] . "' " .
		"AND `ticket_id` = '" . $_POST['ticket_id'] . "' " .
		"AND `user_id` = '" . $_SESSION['user_id'] . "' " .
		"AND `action_type` = '" . $GLOBALS['ACTION_COMMENT'] . "' " .
		"AND `updated` = '" . $_POST['asof'] . "' " .
		"AND `unit_id` = " . $unit_id . ";";

		$result	= db_query($query, __FILE__, __LINE__);

		if (db_num_rows($result) == 0) {

			$query 	= "INSERT INTO `actions` (`ticket_id`, `description`, `action_type`, " .
				"`unit_id`, `call_taker_id`, `user_id`, `client_address`, `updated`, `datetime`) " .
				"VALUES ('" . $_POST['ticket_id'] . "', '" . addslashes($_POST['frm_description']) . "', " . $GLOBALS['ACTION_COMMENT'] . ", " .
				$unit_id . ", " . $_SESSION['user_id'] . ", " . $_SESSION['user_id'] . ", '" . $_SERVER['REMOTE_ADDR'] . "', '" . 
				$_POST['asof'] . "', '" . $datetime_now . "');";

			$result	= db_query($query, __FILE__, __LINE__);

			$query = "UPDATE `tickets` " .
					"SET `updated` = '" . $datetime_now . "', " .
				"`user_id` = " . $_SESSION['user_id'] .
				" WHERE `id`='" . $_POST['ticket_id'] . "' " .
				"LIMIT 1;";

			$result = db_query($query, __FILE__, __LINE__);

			$log_text = "";
			if ($_POST['frm_description'] != "") {
				$log_text .= get_text("Action") . ": " . $_POST['frm_description'] . "  ";
			}
			if ($_POST['asof'] != "") {
				$log_text .= get_text("As of") . ": " . format_date($_POST['asof']) . "  ";
			}
			do_log($GLOBALS['LOG_ACTION_ADD'], $_POST['ticket_id'], $unit_id, $log_text, 0, "", "", "");
		}
	break;
case "update":

	$query_old_data = "SELECT * " .
		"FROM `actions` " .
		"WHERE `id` = " . $_POST['action_id'] . " " .
		"LIMIT 1;";

	$result_old_data = db_query($query_old_data, __FILE__, __LINE__);

	$row_old_data = stripslashes_deep(db_fetch_array($result_old_data));

	$result = db_query("UPDATE `actions` " .
		"SET `description` = '" . $_POST['frm_description'] . "', " .
		"`unit_id` = " . $_POST['frm_unit'] . ", " .
		"`updated` = '" . $_POST['asof'] . "', " .
		"`user_id` = " . $_SESSION['user_id'] . " " .
		"WHERE `id` = " . $_POST['action_id'] . " " .
		"LIMIT 1;", __FILE__, __LINE__);

	$result = db_query("UPDATE `tickets` " .
		"SET `updated` = '" . $datetime_now . "', " .
		"`user_id` = " . $_SESSION['user_id'] . " " .
		"WHERE id = " . $_POST['ticket_id'] . " " .
		"LIMIT 1;", __FILE__, __LINE__);
	unset ($result);

	$query_unit_handle = "SELECT `handle` " .
		"FROM `units` " .
		"WHERE `id` = " . $_POST['frm_unit'] . ";";

	$result_unit_handle = db_query($query_unit_handle, __FILE__, __LINE__);
	$row_unit_handle = stripslashes_deep(db_fetch_array($result_unit_handle));
	$log_text = "";
	if ($_POST['frm_description'] != $row_old_data['description']) {
		$log_text .= get_text("Action") . ": " . $_POST['frm_description'] . "  ";
	}
	if ($_POST['frm_unit'] != $row_old_data['unit_id']) {
		$log_text .= get_text("Unit") . ": " . remove_nls($row_unit_handle['handle']) . "  ";
	}
	if ($_POST['asof'] != "") {
		$log_text .= get_text("As of") . ": " . format_date($_POST['asof']) . "  ";
	}
	if ($row_old_data['datetime'] != "") {
		$log_text .= get_text("Written") . ": " . format_date(strtotime($row_old_data['datetime'])) . "  ";
	}
	do_log($GLOBALS['LOG_ACTION_EDIT'], $_POST['ticket_id'], $_POST['frm_unit'], $log_text, 0, "", "", "");
	break;
default:
	$ticket_id = 0;
	if (isset ($_GET['ticket_id'])) {
		$ticket_id = $_GET['ticket_id'];
	}
	$action_id = 0;
	if (isset ($_GET['action_id'])) {
		$action_id = $_GET['action_id'];
	}
	$unit_id = 0;
	$unit_id_str = "";
	if (isset ($_GET['unit_id'])) {
		$unit_id = $_GET['unit_id'];
		$unit_id_str = "&unit_id=" . $_GET['unit_id'];
	}
	$url_back = "situation.php";
	if (isset ($_GET['back']) && $_GET['back'] == "ticket") {
		$url_back = "ticket_edit.php";
	}
	$moment_date_format = php_to_moment(get_variable("date_format"));

	$query_ticket = "SELECT `problemstart` " .
		"FROM `tickets` " .
		"WHERE `id` = " . $ticket_id . " " .
		"LIMIT 1;";

	$result_ticket = db_query($query_ticket, __FILE__, __LINE__);
	$row_ticket = stripslashes_deep(db_fetch_array($result_ticket));

	$function_str = "insert";
	$caption_str = get_text("Add Action") . " - " . get_variable("page_caption");
	$parking_title_str = get_parking_form_data_helptext("action");
	$description = "";
	$display_datetime_row = " display: none;";
	$written_title_str = "";
	$written_str = "";
	$edited_title_str = "";
	$edited_str = "";
	if ($function == "edit") {

		$query = "SELECT * " .
			"FROM `actions` " .
			"WHERE `id` = " . $action_id . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);

		$row = stripslashes_deep(db_fetch_array($result));

		$unit_id = $row['unit_id'];
		$function_str = "update";
		$caption_str = get_text("Edit Action") . get_table_id($action_id) . " - " . get_variable("page_caption");
		$parking_title_str = "";
		$description = remove_nls($row['description']);
		$display_datetime_row = "";
		$written_title_str = get_title_str(date(get_variable("date_format"), strtotime($row['datetime'])));
		$written_str = date(get_variable("date_format_time_only"), strtotime($row['datetime'])) . " " . get_text("by") . " " . get_user_name($row['call_taker_id']);
		$edited_title_str = get_title_str(date(get_variable("date_format"), strtotime($row['updated'])));
		$edited_str = date(get_variable("date_format_time_only"), strtotime($row['updated'])) . " " . get_text("by") . " " . get_user_name($row['user_id']);
	}
	$action_select_str = get_unit_select_str("action", $unit_id, $ticket_id);
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
			var parking_form_data_min_trigger_chars = <?php print get_parking_form_data_time("action");?> + 0;
			var ticket_id = <?php print $ticket_id;?> + 0;// + 0 prevent Syntax-Error if php-Variable contains "0"

			function validate_action_form() {
				var error_message = "";
				var asof = moment($("#asof").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss");
				var datetime_now = "<?php print $datetime_now;?>";
				if ($("#frm_description").val() == "") {
					error_message += "<?php print get_text("ActionDescription is required");?><br>";
				}
				if (
					!moment(asof, "YYYY-MM-DD HH:mm:ss").isValid() ||
					moment(asof, "YYYY-MM-DD HH:mm:ss").isAfter(moment(datetime_now, "YYYY-MM-DD HH:mm:ss").add(1, 'm')) ||
					moment(asof, "YYYY-MM-DD HH:mm:ss").isBefore(moment($("#written").val(), "YYYY-MM-DD HH:mm:ss"))
				) {
					error_message += "<?php print get_text('date/time error');?><br>";
				}
				if (error_message != "") {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", error_message);
					return false;
				} else {
					$("#asof_mysql_timestamp").val(asof);
					set_parked_form_data();
					$.post("action.php", $('#action_form').serialize())
					.done(function (data) {
						show_top_notice("success", "<?php print get_text("Saved");?>");
						goto_window("<?php print $url_back;?>?ticket_id=" + ticket_id + "&screen_id=" + screen_id_main);
					})
					.fail(function () {
						show_top_notice("danger", "<?php print get_text("Error");?>");
						goto_window("<?php print $url_back;?>?ticket_id=" + ticket_id + "&screen_id=" + screen_id_main);
					});
				}
			}

			function set_parked_form_data(data) {
				try {
					if ((ticket_id != 0)  && ("<?php print $function;?>" != "edit")) {
						if ((data !== undefined) && (data != null)) {
							save_parked_form_data("action_form_data", ticket_id, data);
							save_parked_form_data("action_timestamp", ticket_id, Date.now());
						} else {
							save_parked_form_data("action_delete", ticket_id, "");
						}
					}
				} catch (e) {
				}
			}

			function get_parked_form_data() {
				try {
					if ((ticket_id != 0)  && ("<?php print $function;?>" != "edit")) {
						if (
							(new_infos_array['parked_form_data']['action_timestamp'][ticket_id] !== undefined) && 
							(new_infos_array['parked_form_data']['action_timestamp'][ticket_id] != null)
						) {
							var form_content = [];
							for (var key in new_infos_array['parked_form_data']['action_form_data'][ticket_id]) {
								form_content[new_infos_array['parked_form_data']['action_form_data'][ticket_id][key]['name']] 
									= new_infos_array['parked_form_data']['action_form_data'][ticket_id][key]['value'];
							}
						}
						$("#frm_description").val(form_content['frm_description']);
						$("#frm_unit").val(form_content['frm_unit']).change();
						do_unlock_readonly("asof");
						$("#asof").val(form_content['asof_textfield']);
						set_textblock("", frm_description);
						$("#frm_description").focus();
					}
				} catch (e) {
				}
			}

			$(document).ready(function() {
				$("#asof").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					widgetPositioning: {
						vertical: "bottom"
					},
					sideBySide: true
				});
				$("#asof").data("DateTimePicker").minDate(moment($("#written").val(), "YYYY-MM-DD HH:mm:ss"));
				set_textblock("", frm_description);
				<?php show_prevent_browser_back_button();?>
				var change_situation_first_set = 0;

				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					new_infos_array = JSON.parse(event.data);
					set_window_present("action");
					$("#screen_id").val(new_infos_array['screen']['screen_id']);
					screen_id_main = new_infos_array['screen']['screen_id'];

					if (change_situation_first_set == 0) {
						get_parked_form_data();
						change_situation_first_set = 1;
					}
					if (
						(ticket_id != 0) && 
						($("#frm_description").val().trim().length > 0) && 
						($("#frm_description").val().length > parking_form_data_min_trigger_chars) && 
						(parking_form_data_min_trigger_chars != 0)
					) {
						var new_form_data = $("#action_add_form").serializeArray();
						var action_form_data = [];
						try {
							action_form_data = new_infos_array['parked_form_data']['action_form_data'][ticket_id];
						} catch (e) {
						}
						if (JSON.stringify(new_form_data) != JSON.stringify(action_form_data)) {
							set_parked_form_data(new_form_data);
						}
					}
				});
			});

		</script>
	</head>
	<body onload="check_frames();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<form id="action_form" name="action_form">
			<input id="function" name="function" type="hidden" value="<?php print $function_str;?>">
			<input id="action_id" name="action_id" type="hidden" value="<?php print $action_id;?>">
			<input id="ticket_id" name="ticket_id" type="hidden" value="<?php print $ticket_id;?>">
			<input id="written" type="hidden" class="form-control" value="<?php print $row_ticket['problemstart'];?>">
			<div class="container-fluid" id="main_container">
				<div class="row infostring">
					<div<?php print get_table_id_title_str("action", $action_id);?> class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print $caption_str;?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('ticket_edit.php?ticket_id=<?php print $ticket_id . $unit_id_str;?>');" tabindex=6><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="action_form.reset(); do_lock_readonly('asof');" tabindex=5><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="validate_action_form();" tabindex=4><?php print get_text("Save");?></button>
								</div>
							</div>
						</div>
					</div>	
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_left">
								<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
									<tr <?php print $parking_title_str;?>>
										<th style="width: 20%; border-top: 0px;"><?php print get_text("Action description");?>:</th>
										<td style="width: 5%; border-top: 0px;"></td>
										<td style="width: 75%; border-top: 0px;">
											<div>
												<textarea id="frm_description" name="frm_description" class="form-control" cols="80" rows="10" wrap="soft" tabindex=1><?php print $description;?></textarea>
											</div>
											<div>
												<?php print get_textblock_select_str("action", "document.action_form.frm_description", "", 0, "");?>
											</div>
											<div>
												<?php print $action_select_str;?>
											</div>
										</td>
									</tr>
									<tr>
										<th><?php print get_text("As of") . ":";?></th>
										<td><span id="asof_lock" class="glyphicon glyphicon-lock" aria-hidden="true" onclick="do_unlock_readonly('asof');"></span></td>
										<td>
											<input id="asof" type="text" class="form-control" value="<?php print date(get_variable("date_format"));?>" readonly>
											<input id="asof_mysql_timestamp" name="asof" type="hidden" class="form-control" value="<?php print $datetime_now;?>">
										</td>
									</tr>
									<tr style="height: 45px;<?php print $display_datetime_row;?>">
										<th colspan=2>
											<div><?php print get_text("Written");?>:</div>
											<div><?php print get_text("Edited");?>:</div>
										</th>
										<td>
											<div <?php print $written_title_str;?>><?php print $written_str;?></div>
											<div <?php print $edited_title_str;?>><?php print $edited_str;?></div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_right">
								<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
									<?php show_head($ticket_id, false, false);?>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</div>
		</form>
		<?php show_infobox("small");?>
	</body>
</html>
	<?php
}
?>