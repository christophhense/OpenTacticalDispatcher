<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/api.inc.php");
do_login(basename(__FILE__));
set_session_expire_time("on");

function get_current_dispatch_str($ticket_id, $unit_id, $dispatch, $multi) {
	$return_array = array (
		"code" => "",
		"assigns" => 0,
		"disabled_str" => "",
		"checked_str" => ""
	);
	if ((intval($dispatch) >= 2) || ($multi == 0)) {
		$return_array["code"] = "no_service";
		$return_array["disabled_str"] = " disabled";
		return $return_array;
	}

	$query = "SELECT * " .
		"FROM `assigns` " .	
		"WHERE `unit_id` = " . $unit_id . " " .
		"AND ((`clear` IS NULL) " .
		"OR (DATE_FORMAT(`clear`,'%y') = '00'));";

	$result = db_query($query, __FILE__, __LINE__);
	$return_array["assigns"] = db_num_rows($result);
	if ((db_num_rows($result) > 0) && ($multi < 2)) {
		$return_array["code"] = "dispatched";
		$return_array["disabled_str"] = " disabled";
		$return_array["checked_str"] = " checked";
		return $return_array;
	}

	$query = "SELECT * " .
		"FROM `assigns` " .
		"WHERE `ticket_id` = " . quote_smart($ticket_id) . " " .
		"AND (`unit_id` = " . $unit_id . ") " .
		"AND ((`clear` IS NULL) " .
		"OR (DATE_FORMAT(`clear`,'%y') = '00'));";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) == 1) {
		$return_array["code"] = "same_ticket";
		$return_array["disabled_str"] = " disabled";
		$return_array["checked_str"] = " checked";
		return $return_array;
	}
	return $return_array;
}

function show_units($ticket_id, $show_all_units) {
	$i = 1;
	?>
	<table id="table_left" class="table table-striped table-condensed" style="table-layout: fixed;">
		<colgroup>
			<col span="1" style="width: 6%;">
			<col span="1" style="width: 5%;">
			<col span="1" style="width: 40%;">
			<col span="1" style="width: 35%;">
			<col span="1" style="width: 14%;">
		</colgroup>
		<tr style="vertical-align: bottom;">
			<th style="border-top: 0px;"></th>
			<th style="border-top: 0px; text-align: center;">
				<input type="checkbox" id="display_dispatch-message" name="display_dispatch-message" tabindex=<?php print $i;?>>
			</th>
			<th colspan=2 style="border-top: 0px; text-align: left;"<?php print get_help_text_str("_reports_dispatch_message");?>><?php print get_text("Display dispatch-message for printing");?></th>
			<th style="border-top: 0px;"></th>
		</tr>
		<tr style="white-space: nowrap;">
			<th></th>
			<th></th>
			<th>&nbsp;<?php print get_text("Unit");?></th>
			<th>&nbsp;<?php print get_text("Status");?></th>
			<th><?php print get_text("As of");?></th>
		</tr>
	<?php
	$i++;
	$no_units_available = true;

	$query = "SELECT * " .
		"FROM `tickets` " .
		"WHERE `id` = " . quote_smart($ticket_id) . " " .
		"LIMIT 1;";

	$result_pos = db_query($query, __FILE__, __LINE__);
	$latitude = 0;
	$longitude = 0;
	if (db_num_rows($result_pos) == 1) {
		$row_position = stripslashes_deep(db_fetch_array($result_pos));
		$latitude = $row_position['lat'];
		$longitude = $row_position['lng'];
		unset ($result_pos);
	}

	$query = "SELECT UNIX_TIMESTAMP(`u`. `updated`) AS `updated`, " .
		"`u`.`id` AS `unit_id`, " .
		"`u`.`mobile` AS `unit_mobile`, " .
		"`u`.`name` AS `unit_name`, " .
		"`u`.`handle` AS `handle`, " .
		"`u`.`description` AS `unit_descr`, " .
		"`u`.`multi`, " .
		"`u`.`unit_phone` AS `unit_phone`, " .
		"`u`.`unit_email` AS `unit_email`, " .
		"`u`.`remote_data_services` AS `unit_remote_data_services`, " .
		"`t`.`bg_color` AS `unit_bg_color`, " .
		"`t`.`text_color` AS `unit_text_color`,  " .
		"`t`.`name` AS `type_name`, " .
		"`s`.`status_name` AS `unitstatus`, " .
		"`s`.`bg_color` AS `status_bg_color`, " .
		"`s`.`text_color` AS `status_text_color`, " .
		"`s`.`description` AS `unitstatusdesc`, " .
		"`s`.`dispatch` AS `dispatch`, " .
		"`unit_email`, " .
		"(POW(ABS(" . $latitude . " - `u`.`lat`), 2.0) +  POW(ABS(" . $longitude . " - `u`.`lng`), 2.0)) AS `distance`, " .
		"`f`.`handle` AS `guard_house_handle`, " .
		"`f`.`street` AS `guard_house_street`, " .
		"`f`.`city` AS `guard_house_city` " .
		"FROM `units` `u` " .
		"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
		"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
		"LEFT JOIN `allocates` ON (`u`.`id` = `allocates`.`resource_id`) " .
		"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
		get_allocates_where_str($GLOBALS['TYPE_TICKET'], $GLOBALS['TYPE_UNIT'], "WHERE") . " GROUP BY `unit_id` " .
		"ORDER BY `distance` ASC, `u`.`handle` ASC, `u`.`name` ASC, `unit_id` ASC;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) > 0) {
		while ($unit_row = stripslashes_deep(db_fetch_assoc($result))) {
			if (!empty ($unit_row['updated'])) {
				if (abs(gmdate("U") - $unit_row['updated']) > (get_variable("tolerance") * 60)) {
					$update_time = "<span style='text-decoration: line-through;'>" . date(get_variable("date_format_time_only"), (($unit_row['updated']))) . "</span>";
					$update_date_time = "<span style=\\\'text-decoration: line-through;\\\'>" . date(get_variable("date_format"), (($unit_row['updated']))) . "</span>";
				} else {
					$update_time = date(get_variable("date_format_time_only"), (($unit_row['updated']))) . "</strike>";
					$update_date_time = date(get_variable("date_format"),(($unit_row['updated'])));
				}
			} else {
				$update_time = "";
				$update_date_time = "";
			}
			$current_dispatch_array = get_current_dispatch_str($ticket_id, $unit_row['unit_id'], $unit_row['dispatch'], $unit_row['multi']);
			switch ($current_dispatch_array["code"]) {
				case "dispatched":
					$unit_status_description = $unit_status = get_text("Dispatched");
					$unit_bg_color = "#E0E0E0";
					$unit_text_color = "#000000";
					break;
				case "same_ticket":
					$unit_status_description = $unit_status = get_text("Same ticket");
					$unit_bg_color = "#C0C0C0";
					$unit_text_color = "#FFFFFF";
					break;
				case "no_service":
					if ($unit_row['unitstatus']) {
						$unit_status_description = remove_nls($unit_row['unitstatusdesc']);
						$unit_status = remove_nls(shorten($unit_row['unitstatus'], 16));
						$unit_bg_color = $unit_row['status_bg_color'];
						$unit_text_color = $unit_row['status_text_color'];
					} else {
						$unit_status_description = $unit_status = get_text("Unit out of Service");
						$unit_bg_color = "#808080";
						$unit_text_color = "#FFFFFF";
					}
					break;
				default:
					if ($current_dispatch_array["assigns"] == 0) {
						if ($unit_row['unitstatus']) {
							$unit_status_description = remove_nls($unit_row['unitstatusdesc']);
							$unit_status = remove_nls(shorten($unit_row['unitstatus'], 16));
							$unit_bg_color = $unit_row['status_bg_color'];
							$unit_text_color = $unit_row['status_text_color'];
						} else {
							$unit_status_description = $unit_status = get_text("Clear");
							$unit_bg_color = "#FFFFFF";
								$unit_text_color = "#000000";
						}
					} else {
						if ($current_dispatch_array["assigns"] == 1) {
							$unit_status_description = get_current_tickets_title($unit_row['unit_id']);
							$unit_status = "1 " . get_text("Incident");
							$unit_bg_color = "#E0E0E0";
							$unit_text_color = "#000000";
						} else {
							$unit_status_description = get_current_tickets_title($unit_row['unit_id']);
							$unit_status = $current_dispatch_array["assigns"] . " " . get_text("Tickets");
							$unit_bg_color = "#E0E0E0";
							$unit_text_color = "#000000";
						}
				}
			}
			$strike = $strike_end = "";
			if ($current_dispatch_array["code"] != "") {
				$strike = "<span style='text-decoration: line-through;'>";
				$strike_end = "</span>";
			} else {
				$no_units_available = false;
			}
			$envelope_str = "<td></td>";
			if (get_message_to_unit_available($unit_row['unit_phone'], $unit_row['unit_email'], $unit_row['unit_remote_data_services'])) {
				$title_text = get_text("Reporting channel");
				if (is_smsg_id($unit_row['unit_remote_data_services'])) {
					$title_text .= "<br>" . get_text("Remote data services") . ": " . remove_nls($unit_row['unit_remote_data_services']);
				}
				if (is_phone($unit_row['unit_phone'])) {
					$title_text .= "<br>" . get_text("Cellular phone") . ": " . remove_nls($unit_row['unit_phone']);
				}
				if (is_email($unit_row['unit_email'])) {
					$title_text .= "<br>" . get_text("Email") . ": " . remove_nls($unit_row['unit_email']);
				}
				$envelope_str = "<td style='text-align: center;'>&nbsp;<span " . get_title_str($title_text) .
				" class='glyphicon glyphicon-envelope' aria-hidden='true' style='font-size: 12px;'></span></td>";
			}
			if (($current_dispatch_array["code"] == "") || $show_all_units) {
	?>
		<tr style="vertical-align: bottom;">
			<?php print $envelope_str;?>
			<td style="text-align: center;">
				<input type="checkbox"<?php print $current_dispatch_array["disabled_str"] . $current_dispatch_array["checked_str"];?> id="checkbox_row<?php print $i;?>" onclick="unit_clicked(<?php print $i . ", " . $unit_row['unit_id'];?>);" tabindex=<?php print $i;?>>
				<input type="hidden" id="row_id_<?php print $i;?>" name="unit_id[]" value="">
			</td>
			<td<?php print get_title_unit_str($unit_row);?>>
				<span class="label" style="font-weight: bold; font-size: 12px; background-color: <?php print $unit_row['unit_bg_color'];?>; color: <?php print $unit_row['unit_text_color'];?>;"><?php print $strike . remove_nls($unit_row['handle']) . $strike_end;?></span>
			</td>
			<td<?php print get_title_str($unit_status_description);?>>
				<div class="label label-default" style="display: block; border-width: 1px; border-style: solid; border-color: #000000; text-align: left; font-weight: bold; font-size: 12px; background-color: <?php print $unit_bg_color;?>; color: <?php print $unit_text_color;?>;"><?php print $unit_status;?></div>
			</td>
			<td<?php print get_title_str($update_date_time);?> style="text-align: center; white-space: nowrap;"><?php print $update_time;?></td>
		</tr>
	<?php
				$i++;
			}
		}
	}
	$i++;
	if ($no_units_available) {
	?>
		<tr style="vertical-align: bottom;">
			<td colspan=2></td>
			<td colspan=2 style="text-align: center;"><strong><?php print get_text("No units available!");?></strong></td>
			<td></td>
		</tr>
	<?php
	}
	?>
	</table>
	<?php
}

$function = "";
if (isset ($_POST['function']) && (is_super() || is_admin() || is_operator())) {
	$function = $_POST['function'];
}
switch ($function) {
	case "insert":
		$datetime_now = mysql_datetime();
		$unit_id = array ();
		$log_dispatch_facility = false;
		$send_dispatch_message = false;

		$query_status = "SELECT `status`, " .
			"`facility_id`, " .
			"`f`.`handle` AS `facility_handle` " .
			"FROM `tickets` " .
			"LEFT JOIN `facilities` `f` ON (`tickets`.`facility_id` = `f`.`id`) " .
			"WHERE `tickets`.`id` = " . $_POST['frm_ticket_id'] . ";";

		$result_status = db_query($query_status, __FILE__, __LINE__);
		$row_status = stripslashes_deep(db_fetch_assoc($result_status));
		if ($row_status['status'] == 3) {

			$query_status = "UPDATE `tickets` " .
				"SET `status` = 2 " .
				"WHERE `id` = " . $_POST['frm_ticket_id'] . ";";

			$result_status = db_query($query_status, __FILE__, __LINE__);
			do_log($GLOBALS['LOG_INCIDENT_OPEN'], $_POST['frm_ticket_id'], 0, get_text("Incident opened"), 0, "", "", "");
		}
		if ($row_status['facility_id'] != 0) {
			$log_dispatch_facility = true;
		}
		$failed_dispatch = 0;
		$count_dispatch = 0;
		foreach ($_POST['unit_id'] as $VarName => $VarValue) {
			if ($_POST['unit_id'][$VarName]) {
				array_push($unit_id, $_POST['unit_id'][$VarName]);
				$count_dispatch++;
				$dispatch = 0;
				$multi = 0;

				$query_unit = "SELECT `u`.`multi` AS `multi`, " .
					"`u_s`.`dispatch` AS `dispatch` " .
					"FROM `units` `u`" .
					"LEFT JOIN `unit_status` `u_s` ON (`u`.`unit_status_id` = `u_s`.`id`) " .
					"WHERE `u`.`id` = " . $_POST['unit_id'][$VarName] . ";";

				$result_unit = db_query($query_unit, __FILE__, __LINE__);
				if (db_num_rows($result_unit) > 0) {
					$row_unit = stripslashes_deep(db_fetch_assoc($result_unit));
					$dispatch = $row_unit['dispatch'];
					$multi = $row_unit['multi'];
				}
				$current_dispatch_array = get_current_dispatch_str($_POST['frm_ticket_id'], $_POST['unit_id'][$VarName], $dispatch, $multi);
				if (($current_dispatch_array["code"] == "") || (isset ($_POST['api_log_id']))) {

					$query = "INSERT INTO `assigns` (`ticket_id`, `unit_id`, `comments`, `start_miles`, " .
						"`on_scene_miles`, `end_miles`, `miles`, `dispatched`, " .
						"`responding`, `on_scene`, `u2fenr`, `u2farr`, " .
						"`clear`, `on_scene_location`, `on_scene_facility_id`, `on_scene_lat`, " .
						"`on_scene_lng`, `receiving_location`, `receiving_facility_id`, `receiving_lat`, " .
						"`receiving_lng`, `progession_changed`, `user_id`, `updated`, " .
						"`dispatching_user_id`, `client_address`, `datetime`) " .
						"VALUES (" . $_POST['frm_ticket_id'] . ", " . $_POST['unit_id'][$VarName] . ", '', NULL, " .
						"NULL, NULL, NULL, " . quote_smart($datetime_now) . ", " .
						"NULL, NULL, NULL, NULL, " .
						"NULL, '', -1, NULL, " .
						"NULL, '', NULL, NULL, " .
						"NULL, NULL, " . $_SESSION['user_id'] . ", " . quote_smart($datetime_now) . ", " .
						$_SESSION['user_id'] . ", '" . $_SERVER['REMOTE_ADDR'] . "', " . quote_smart($datetime_now) . ");";

					db_query($query, __FILE__, __LINE__);

					$query = "SELECT MAX(`id`) FROM `assigns`";

					$result = db_query($query, __FILE__, __LINE__);
					$row = stripslashes_deep(db_fetch_array($result));
					$new_id = $row[0];
					if (isset ($_POST['api_log_id'])) {

						$query = "SELECT `code`, " .
							"`datetime` " .
							"FROM `api_log` " .
							"WHERE `id`= " . $_POST['api_log_id'] . ";";

						$result	= db_query($query, __FILE__, __LINE__);
						$row = stripslashes_deep(db_fetch_assoc($result));
						switch ($row['code']) {
							case $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']:

								$query = "UPDATE `assigns` " .
									"SET `responding` = '" . $row['datetime'] . "' " .
									"WHERE `id` = " .  $new_id . ";";

								db_query($query, __FILE__, __LINE__);
								do_log($GLOBALS['LOG_CALL_RESPONDING'], $_POST['frm_ticket_id'], $_POST['unit_id'][$VarName], "", 0, $row['datetime'], "", "");
								break;
							case $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']:

								$query = "UPDATE `assigns` " .
									"SET `on_scene` = '" . $row['datetime'] . "' " .
									"WHERE `id` = " .  $new_id . ";";

								db_query($query, __FILE__, __LINE__);
								do_log($GLOBALS['LOG_CALL_ON_SCENE'], $_POST['frm_ticket_id'], $_POST['unit_id'][$VarName], "", 0, $row['datetime'], "", "");
								break;
							case $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET']:

								$query = "UPDATE `assigns` " .
									"SET `u2fenr` = '" . $row['datetime'] . "' " .
									"WHERE `id` = " .  $new_id . ";";

								db_query($query, __FILE__, __LINE__);
								do_log($GLOBALS['LOG_CALL_FACILITY_ENROUTE'], $_POST['frm_ticket_id'], $_POST['unit_id'][$VarName], "", 0, $row['datetime'], "", "");
								break;
							case $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']:

								$query = "UPDATE `assigns` " .
									"SET `u2farr` = '" . $row['datetime'] . "' " .
									"WHERE `id` = " .  $new_id . ";";

								db_query($query, __FILE__, __LINE__);
								do_log($GLOBALS['LOG_CALL_FACILITY_ARRIVED'], $_POST['frm_ticket_id'], $_POST['unit_id'][$VarName], "", 0, $row['datetime'], "", "");
								break;
							default:
						}
					}

					$query = "UPDATE `units` " .
						"SET `unit_status_id` = 1 " .
						"WHERE `id` = " . quote_smart($_POST['unit_id'][$VarName])  . " " .
						"LIMIT 1;";

					db_query($query, __FILE__, __LINE__);
					do_log($GLOBALS['LOG_UNIT_STATUS'], $_POST['frm_ticket_id'], $_POST['unit_id'][$VarName], get_text("Resourced"), 0, "", "", "");
					if ($log_dispatch_facility) {
						do_log($GLOBALS['LOG_FACILITY_DISPATCHED'], $_POST['frm_ticket_id'], $_POST['unit_id'][$VarName], remove_nls($row_status['facility_handle']), $row_status['facility_id'], "", "", "");
					}
					if (($_POST['unit_id'][$VarName] != "") && ($_POST['unit_id'][$VarName] != 0)) {
						do_receipt_message($_POST['unit_id'][$VarName]);
					}

					$query = "SELECT `id`, " .
						"`unit_phone`, " .
						"`unit_email`, " .
						"`remote_data_services` " .
						"FROM `units` " .
						"WHERE `id` = " . $_POST['unit_id'][$VarName]  . " " .
						"LIMIT 1;";

					$result = db_query($query, __FILE__, __LINE__);
					$row_addr = stripslashes_deep(db_fetch_assoc($result));
					if (get_message_to_unit_available($row_addr['unit_phone'], $row_addr['unit_email'], $row_addr['remote_data_services'])) {
						$send_dispatch_message = true;
					}
				} else {
					$failed_dispatch++;
				}
			}
		}
		unset ($result);
		$message_text = get_text("Saved");
		$appearance = "success";
		if ($failed_dispatch > 0) {
			if ($failed_dispatch == $count_dispatch) {
				$message_text = get_text("Not saved");
				$appearance = "danger";
			} else {
				$message_text = get_text("Not all saved");
				$appearance = "warning";
			}
		}
		print '{"0":{"type":"message","item":"' . $appearance . '","action":"' . $message_text . '"},"1":{"id_array":"' . urlencode(implode(",", array_unique($unit_id))) . '","send_message":"' . $send_dispatch_message . '"}}';
		break;
	default:
		$url_back = "ticket_edit.php";
		if ((isset ($_GET['new_incident']) && ($_GET['new_incident'] == "true"))) {
			$url_back = "situation.php";
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
			<script src="./js/bootstrap.min.js" type="text/javascript"></script>
			<script src="./js/functions.js" type="text/javascript"></script>
			<?php print show_day_night_style();?>
			<script>
				var new_infos_array = [];
				var screen_id_main = 0;
				var count_units_checked = 0;

				function unit_clicked(row, unit) {
					if ($("#checkbox_row" + row).prop("checked")) {
						$("#row_id_" + row).val(unit);
						count_units_checked++;
					} else {
						$("#row_id_" + row).val("");
						count_units_checked--;
					}
				}

				function validate_dispatch() {
					if (count_units_checked == 0) {
						show_infobox("<?php print get_text("Please select units, or cancel");?>");
					} else {
						var display_dispatchmessage = "off";
						if ($("#display_dispatch-message").is(':checked') == true) display_dispatchmessage = "on";
						$.post("dispatch.php", $("#dispatch_form").serialize())
						.done(function (data) {
							data = JSON.parse(data);
							show_top_notice(data['0']['item'], data['0']['action']);
							if (data['1']['send_message']) {
								goto_window("communication.php?function=send_message&message_group=unit_ticket&targets_ids=" + 
									data['1']['id_array'] + "&ticket_id=" + $("#ticket_id").val() + "&display_dispatch-message=" + 
									display_dispatchmessage + "&screen_id=" + screen_id_main);
							} else {
								if (display_dispatchmessage == "on") {
									goto_window("ticket_report.php?function=dispatch_text&ticket_id=" + $("#ticket_id").val() + "&back=situation");
								} else {
									goto_window("situation.php?screen_id=" + screen_id_main);
								}
							}
						})
						.fail(function () {
							show_top_notice("danger", "<?php print get_text("Error");?>");
							goto_window("situation.php?screen_id=" + screen_id_main);
						});
					}
				}

				$(document).ready(function() {
					set_window_present("dispatch");
					<?php show_prevent_browser_back_button();?>
					window.addEventListener("message", function(event) {
						if (event.origin != window.location.origin) return;
						new_infos_array = JSON.parse(event.data);
						screen_id_main = new_infos_array['screen']['screen_id'];
					});
				});

			</script>
		</head>
		<body onload="check_frames();" >
			<script type="text/javascript" src="./js/wz_tooltip.js"></script>
			<div class="container-fluid" id="main_container">
				<form id="dispatch_form">
					<input type="hidden" name="function" value="insert">
					<input type="hidden" id="ticket_id" name="frm_ticket_id" value="<?php print $_GET['ticket_id']; ?>">
					<div class="row infostring">
						<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
							<?php print get_text("Dispatch Units") . " - "  . get_variable("page_caption");?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-1">
							<div class="container-fluid" style="position: fixed;">
								<div class="row" style="margin-top: 10px;">
									<div class="col-md-12">
										<button type="button" class="btn btn-xs btn-default" accesskey="c" onclick="goto_window('<?php print $url_back;?>?ticket_id=<?php print $_GET['ticket_id'];?>&screen_id=' + screen_id_main);" tabindex=10002><?php print get_text("Cancel");?></button>
									</div>
								</div>
								<div class="row" style="margin-top: 10px;">
									<div class="col-md-12">
										<button type="reset" class="btn btn-xs btn-default" accesskey="r" tabindex=10001><?php print get_text("Reset");?></button>
									</div>
								</div>
								<div class="row" style="margin-top: 10px;">
									<div class="col-md-12">
										<button type="button" class="btn btn-xs btn-default" accesskey="s" onclick="validate_dispatch();" tabindex=10000><?php print get_text("Save");?></button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="panel panel-default" style="padding: 0px;">
								<div id="table_left">
									<?php show_units($_GET['ticket_id'], false);?>
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="panel panel-default" style="padding: 0px;">
								<div id="table_right_1">					
									<table class="table table-striped table-condensed" style="table-layout: fixed;">
										<?php show_head($_GET['ticket_id'], false, false);?>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-1"></div>
					</div>
				</form>
			</div>
			<?php show_infobox("small");?>
			<?php show_accesskeys();?>
		</body>
	</html>
	<?php
}
?>