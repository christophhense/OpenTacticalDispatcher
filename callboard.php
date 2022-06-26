<?php
error_reporting(E_ALL);
ini_set('session.cookie_samesite', 'Strict');
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/log_codes.inc.php");

function get_disp_cell($row_element, $form_element, $severity_class, $strike = "", $strike_end = "", $assign_id, $i) {
	$title_call_progression_time_click_str = "";
	$on_click_str = "";
	$disabled_str = " disabled";
	if ((isset ($_SESSION['level'])) && (is_super() || is_admin() || is_operator())) {
		$title_call_progression_time_click_str = get_text("Click to edit assign");
		$on_click_str = " onclick='assign_edit(" . $assign_id . ");'";
		$disabled_str = "";
	}
	if (is_datetime($row_element)) {
		return "\n\t<td class='" . $severity_class . "' align='center'" . $on_click_str . " " .
			get_nowrap_title_str(date(get_variable("date_format"), strtotime($row_element)) . "<br><br>" .
			$title_call_progression_time_click_str) . ">" . $strike .
			date("H:i", strtotime($row_element)) . $strike_end . "</td>\n";
	} else {
		return "\n\t<td class='" . $severity_class . "' align='center'><input type='checkbox' name='" .
			$form_element . "' id='F" . $i . "_" . $form_element . "'" . $disabled_str .
			" onclick='checkbox_clicked(\"F" . $i . "_" . $form_element . "\");'></td>\n";
	}
}

$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
$callboard_settings = explode(",", get_variable("callboard"));
$callboard_enabled = trim($callboard_settings[0]);
if ((isset ($_GET['cleared_assigns'])) && (($_GET['cleared_assigns'] == "show") || ($_GET['cleared_assigns'] == "hide"))) {
	$_SESSION['cleared_assigns'] = $_GET['cleared_assigns'];
}
switch ($function) {
case "table":
	if (!isset ($_SESSION['sort_asc_desc'])) {
		$_SESSION['sort_asc_desc'] = "ASC";
	}
	if ((isset ($_GET['sort_order'])) && (($_GET['sort_order'] == "tickets") || ($_GET['sort_order'] == "units"))) {
		$_SESSION['sort_order'] = $_GET['sort_order'];
		if ($_SESSION['sort_asc_desc'] == "ASC") {
			$_SESSION['sort_asc_desc'] = "DESC";
		} else {
			$_SESSION['sort_asc_desc'] = "ASC";
		}
	}
	$sort_order = "tickets";
	if (isset ($_SESSION['sort_order'])) {
		$sort_order = $_SESSION['sort_order'];
	}
	$sort_order_str = "`severity` DESC, `ticket_street` " . $_SESSION['sort_asc_desc'] . ", `unit_name` ASC ";
	if ($sort_order == "units") {
		$sort_order_str = "`handle`" . $_SESSION['sort_asc_desc'] . " ";
	}
	$cleared_assigns = "";
	if (isset ($_SESSION['cleared_assigns'])) {
		$cleared_assigns = $_SESSION['cleared_assigns'];
	}
	$closed_interval_settings = explode(",", get_variable("closed_interval"));
	$cleared_assign_time = trim($closed_interval_settings[1]);
	$cleared_assigns_where_str = "";
	if ($cleared_assigns == "show") {
		$time_back = mysql_datetime(time() - ($cleared_assign_time * 60));
		$cleared_assigns_where_str = " OR `assigns`.`clear` >= '" . $time_back . "' ";
	}
	if (isset ($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	} else {
		$user_id = 0;
	}

	$query = "SELECT `assigns`.`updated` AS `as_of`, " .
		"`assigns`.`id` AS `assign_id`, " .
		"`assigns`.`comments` AS `assign_comments`, " .
		"`assigns`.`on_scene_facility_id` AS `assign_facility_id`, " .
		"`assigns`.`on_scene_location` AS `assign_on_scene_location`, " .
		"`assigns`.`dispatched`, " .
		"`assigns`.`responding`, " .
		"`assigns`.`on_scene`, " .
		"`assigns`.`u2fenr`, " .
		"`assigns`.`u2farr`, " .
		"`assigns`.`clear`, " .
		"`f_a_o`.`handle` AS `assign_on_scene_facility_handle`, " .
		"`assigns`.`receiving_location` AS `assign_receiving_location`, " .
		"`f_a_r`.`handle` AS `assign_rec_facility_handle`, " .
		"UNIX_TIMESTAMP(booked_date) AS booked_date, " .
		"`t`.`description` AS `ticket_description`, " .
		"`t`.`id` AS `ticket_id`, " .
		"`t`.`status` AS `tick_status`, " .
		"`t`.`location` AS `ticket_street`, " .
		"`t`.`phone` AS `tick_phone`, " .
		"`t`.`incident_name` AS `incident_name`, " .
		"`t`.`contact` AS `contact`, " .
		"`t`.`description` AS `ticket_description`, " .
		"`t`.`comments` AS `tick_comm`,	" .
		"`t`.`severity` AS `tick_severity`, " .
		"`f`.`handle` AS `fac_handle`, " .
		"`u`.`id` AS `unit_id`, " .
		"`u`.`name` AS `unit_name`, " .
		"`u`.`handle` AS `handle`, " .
		"`u`.`unit_email` AS `unit_email`, " .
		"`u`.`remote_data_services` AS `unit_remote_data_services`, " .
		"`u`.`unit_phone` AS `unit_phone`, " .
		"`u`.`multi` AS `multi`, " .
		"`u`.`description` AS `unit_descr`, " .
		"`u`.`type` AS `unit_type`, " .
		"`u`.`guard_house_id` AS `street`, " .
		"`u`.`contact_name` AS `contact_name`, " .
		"`u_t`.`name` AS `type_name`, " .
		"`u_t`.`bg_color` AS `unit_background_color`, " .
		"`u_t`.`text_color` AS `unit_text_color`, " .
		"`assigns`.`updated` AS `assign_as_of`, " .
		"`f_guard_house`.`handle` AS `guard_house_handle`, " .
		"`f_guard_house`.`street` AS `guard_house_street`, " .
		"`f_guard_house`.`city` AS `guard_house_city` " .
		"FROM `assigns` " .
		"LEFT JOIN `tickets` `t` ON (`assigns`.`ticket_id` = `t`.`id`) " .
		"LEFT JOIN `allocates` `allocates` ON (`assigns`.`ticket_id` = `allocates`.`resource_id`) " .
		"LEFT JOIN `units` `u` ON (`assigns`.`unit_id` = `u`.`id`) " .
		"LEFT JOIN `facilities` `f` ON `t`.`facility_id` = `f`.`id` " .
		"LEFT JOIN `facilities` `f_a_o` ON `assigns`.`on_scene_facility_id` = `f_a_o`.`id` " .
		"LEFT JOIN `facilities` `f_a_r` ON `assigns`.`receiving_facility_id` = `f_a_r`.`id` " .
		"LEFT JOIN `facilities` `f_guard_house` ON (`u`.`guard_house_id` = `f_guard_house`.`id`) " .
		"LEFT JOIN `unit_types`	`u_t` ON (`u`.`type` = `u_t`.`id`) " .
		get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_TICKET'], "WHERE") . " " .
		"AND (`assigns`.`clear` IS NULL OR DATE_FORMAT(`assigns`.`clear`,'%y') = '00') " . $cleared_assigns_where_str . " " .
		"GROUP BY `assigns`.`id` " .
		"ORDER BY " . $sort_order_str;

	$result = db_query($query, __FILE__, __LINE__);
	$lines = db_num_rows($result);
	?>
<script>
	<?php
	if ($callboard_enabled == 2) {
	?>
	parent.document.getElementById("callboard").style.height = (<?php print $lines;?> == 0)? "0px" : <?php print get_callboard_height();?> + "px";	// new cb frame height if no assigns; re-use top
	parent.window.setIframeHeight();
	<?php
		} else {
	?>
	parent.document.getElementById("callboard").style.height = (window.innerHeight == 0)? "0px" : <?php print get_callboard_height();?> + "px";	// new cb frame height if no assigns; re-use top
	parent.window.setIframeHeight();
	<?php
		}
	?>
</script>
<table class="table table-striped table-condensed" style="table-layout: fixed;">
	<?php
//---------------------------------------------------------------------------------------------------------------------------
	$header = "";
	if ($lines == 0) {
		$header = "<tr><td colspan=12 style='text-align: center;'><strong>" . get_text("No current dispatches") . "</strong></td></tr>\n";
		print $header;
	} else {
		$i = 1;	
		$ticket_view_or_edit = "ticket_view";
		$disabled_str = " disabled ";
		if (is_super() || is_admin() || is_operator())	{
			$ticket_view_or_edit = "ticket_edit";
			$disabled_str = "";
		}
		$unit_ids = array ();
//---------------------------------------------------------------------------------------------------------------------------
//major while begin
//---------------------------------------------------------------------------------------------------------------------------
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			if ($i == 1) {
				$sort_order_head_str = "&nbsp;<span class='glyphicon glyphicon-sort-by-alphabet' aria-hidden='true' style='font-size: 12px; padding-right: 2px;'></span>";
				if ((isset ($_SESSION['sort_asc_desc'])) && ($_SESSION['sort_asc_desc'] == "DESC") ) {
					$sort_order_head_str = "&nbsp;<span class='glyphicon glyphicon-sort-by-alphabet-alt' aria-hidden='true' style='font-size: 12px; padding-right: 2px;'></span>";
				}
	?>
	<tr>
		<td style="width: 3%"><?php print $sort_order_head_str;?></td>
		<td style="width: 2%; text-align: center;"><?php print get_message_click_str("callboard", 0, 0, "", "", "", "");?></td>
		<td style="width: 15%; text-align: left;" onclick="sort('units');"<?php print get_title_str(get_text("Click to sort by Unit"));?>><b><?php print get_text("Unit");?></b></td>
		<td style="width: 6%; text-align: center;" onclick="sort('units');""<?php print get_title_str(get_text("Click to sort by Unit"));?>><b><?php print get_text("Dispatched");?></b></td>
		<td style="width: 6%; text-align: center;" onclick="sort('units');"<?php print get_title_str(get_text("Click to sort by Unit"));?>><b><?php print get_text("Responding");?></b></td>
		<td style="width: 6%; text-align: center;" onclick="sort('units');"<?php print get_title_str(get_text("Click to sort by Unit"));?>><b><?php print get_text("On-scene");?></b></td>
		<td style="width: 6%; text-align: center;" onclick="sort('units');"<?php print get_title_str(get_text("Click to sort by Unit"));?>><b><?php print get_text("Fac en-route");?></b></td>
		<td style="width: 6%; text-align: center;" onclick="sort('units');"<?php print get_title_str(get_text("Click to sort by Unit"));?>><b><?php print get_text("Fac arr");?></b></td>
		<td style="width: 6%; text-align: center;" onclick="sort('units');"<?php print get_title_str(get_text("Click to sort by Unit"));?>><b><?php print get_text("Clear");?></b></td>
		<td style="width: 4%;"<?php print get_title_str(get_text("Reset unit dispatch times or Delete dispatch"));?>><span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="padding-left: 35%; padding-right: 65%;"></td>
		<td style="width: 35%; text-align: left;" onclick="sort('tickets');"<?php print get_title_str(get_text("Click to sort by Incident"));?>><b><?php print get_text("Incident location");?></b></td>
		<td style="width: 5%; text-align: left;" onclick="sort('tickets');"<?php print get_title_str(get_text("Click to sort by Incident"));?>><b><?php print get_text("inc_name_short");?></b></td>
	</tr>
	<?php
			}
			$priorities = array ("severity_normal", "severity_medium", "severity_high");
			$class = ($row['tick_severity'] == "")? "" : $priorities[$row['tick_severity']];
	?>
	<tr>
	<?php
			$title_assign = get_title_ticket($row);
//---------------------------------------------------------------------------------------------------------------------------
				print "\t<td></td>\n";
//---------------------------------------------------------------------------------------------------------------------------
				$unit_handle = empty ($row['unit_id']) ? "[#" . $row['unit_id'] . "]" : ($row['handle']);
				print "\t<td style='text-align: center;'>" . get_message_click_str("unit", $row['unit_id'], $row['ticket_id'],
					$row['handle'], $row['unit_phone'], $row['unit_email'], $row['unit_remote_data_services']) . "</td>\n";
//---------------------------------------------------------------------------------------------------------------------------
				if (is_datetime($row['clear'])) {
					$strike = "<span style='text-decoration: line-through;'>"; $strikend = "</span>";
				} else {
					$strike = $strikend = "";
				}
				if (!($row['unit_id'] == 0)) {
					unset ($row_type);
					print "\t<td" . get_title_unit_str($row) . " style='text-align: left; font-weight: bold; " .
						"font-size: 16px; vertical-align: top;'>" . $strike . "<div style='overflow:hidden; text-overflow:ellipsis;'>" .
						"<span class='label' style='background-color: " . $row['unit_background_color'] . "; color: " . $row['unit_text_color'] . ";'>" .
						remove_nls($row['handle']) . "</span></div>" . $strikend . "</td>\n";
//---------------------------------------------------------------------------------------------------------------------------
					print get_disp_cell($row['dispatched'], "frm_dispatched", $class, $strike, $strikend, $row['assign_id'], $i);
					print get_disp_cell($row['responding'], "frm_responding", $class, $strike, $strikend, $row['assign_id'], $i);
					print get_disp_cell($row['on_scene'], "frm_on_scene", $class, $strike, $strikend, $row['assign_id'], $i);
					print get_disp_cell($row['u2fenr'], "frm_u2fenr", $class, $strike, $strikend, $row['assign_id'], $i);
					print get_disp_cell($row['u2farr'], "frm_u2farr", $class, $strike, $strikend, $row['assign_id'], $i);
					print get_disp_cell($row['clear'], "frm_clear", $class, $strike, $strikend, $row['assign_id'], $i);
				} else {
					print "\t<td colspan=10 class='" . $class . "' onclick=assign_edit(" . $row['assign_id'] . ") id='myDate$i' align='left'><b>&nbsp;&nbsp;&nbsp;&nbsp;" . get_text("NA") . "</b></td>\n";	
				}
//---------------------------------------------------------------------------------------------------------------------------
				print "\t<td style='text-align: center;'><input type='radio'  id='F" . $row['assign_id'] . "_res_times' " . $disabled_str .
					" onclick=\"do_assign_reset_callboard(" . $row['assign_id'] . ")\"></td>\n";
//-------------------------------------Address:
				$in_strike = ((!(empty ($row['incident_name']))) && ($row['tick_status'] == $GLOBALS['STATUS_CLOSED']))? "<span style='text-decoration: line-through;'>" : "";
				$in_strikend = ((!(empty ($row['incident_name']))) && ($row['tick_status'] == $GLOBALS['STATUS_CLOSED']))? "</span>": "";
				$address = (empty($row['ticket_street']))? get_text("[No Address]") : remove_nls($row['ticket_street']) . "  ";
				print "\t<td onclick=" . $ticket_view_or_edit . "('" . $row['ticket_id'] . "') class='" . $class . "'" . get_title_str($title_assign) .
				" align='left'>" . $in_strike . "<div style='overflow:hidden; text-overflow: ellipsis;'>" .
				$address . "</div>" . $in_strikend . "</td>\n";
				print "\t<td onclick=" . $ticket_view_or_edit . "('" . $row['ticket_id'] . "') class='" . $class . "'" . get_title_str($title_assign) .
				" align='left'>" . $in_strike . "<div style='overflow:hidden; text-overflow: ellipsis;'>" .
				remove_nls($row['incident_name']) . "</div>" . $in_strikend . "</td>\n";
//---------------------------------------------------------------------------------------------------------------------------
	?>
		<input type="hidden" id="F<?php print $i;?>_frm_unit_id" value="<?php print $row['unit_id'];?>">
		<input type="hidden" id="F<?php print $i;?>_frm_ticket_id" value="<?php print $row['ticket_id'];?>">
		<input type="hidden" id="F<?php print $i;?>_frm_assign_id" value="<?php print $row['assign_id'];?>">
		<input type="hidden" name="counter[]">
	</tr>
	<?php
			$i++;
		}
	}
	?>
</table>
	<?php
		break;
	default:
		$query = "SELECT * FROM `assigns` WHERE `clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00'";
		$result = db_query($query, __FILE__, __LINE__);
		$lines = db_num_rows($result);
		unset ($result);
		$auto_poll_settings = explode(",", get_variable("auto_poll"));
		$auto_poll_time = trim($auto_poll_settings[0]);
		$active_assigns_button_display_str = " style=\"display: none;\"";
		$cleared_assigns_button_display_str = " style=\"display: inline;\"";
		if ((isset ($_SESSION['cleared_assigns'])) && ($_SESSION['cleared_assigns'] == "show")) {
			$active_assigns_button_display_str = " style=\"display: inline;\"";
			$cleared_assigns_button_display_str = " style=\"display: none;\"";
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
		<?php print show_day_night_style();?>
		<script src="./js/jquery-2.1.4.min.js" type="text/javascript"></script>
		<script src="./js/functions.js" type="text/javascript"></script>
	</head>
	<script>

		function show_cleared_assigns() {
			$.get("./callboard.php?function=table&cleared_assigns=show", function(data) {
				$("#callboard").html(data);
			});
			$("#active_assigns_button").css("display", "inline");
			$("#cleared_assigns_button").css("display", "none");
		}

		function hide_cleared_assigns() {
			$.get("./callboard.php?function=table&cleared_assigns=hide", function(data) {
				$("#callboard").html(data);
			});
			$("#active_assigns_button").css("display", "none");
			$("#cleared_assigns_button").css("display", "inline");
		}

		function sort(sort_order) {
			$.get("./callboard.php?function=table&sort_order=" + sort_order, function(data) {
				$("#callboard").html(data);
			});
		}

		var active_assigns_button = false;
		var cleared_assigns_button = false;

		function checkbox_clicked(id) {
			if ($("#" + id).prop("checked")) {
				if ($("#refresh_button").css("display").match(/[.]?inline[.]?/)) {
					$("#refresh_button").css("display", "none");
				}
				$("#save_button").css("display", "inline");
				$("#cancel_button").css("display", "inline");
				if ($("#active_assigns_button").css("display").match(/[.]?inline[.]?/)) {
					active_assigns_button = true;
					cleared_assigns_button = false;
				}
				if ($("#cleared_assigns_button").css("display").match(/[.]?inline[.]?/)) {
					active_assigns_button = false;
					cleared_assigns_button = true;
				}
				$("#active_assigns_button").css("display", "none");
				$("#cleared_assigns_button").css("display", "none");
			}
		}

		function cancel_clicked() {
			$.get("./callboard.php?function=table", function(data) {
				$("#callboard").html(data);
			});
			if (active_assigns_button) {
				$("#active_assigns_button").css("display", "inline");
			}
			if (cleared_assigns_button) {
				$("#cleared_assigns_button").css("display", "inline");
			}
			$("#refresh_button").css("display", "inline");
			$("#save_button").css("display", "none");
			$("#cancel_button").css("display", "none");
		}

		function set_progression(line_number, progression) {
			var params = "assign_id=" + $("#F" + line_number + "_frm_assign_id").val();
			params += "&frm_callprogression=" + progression;
			params += "&function=call_progression";
			$.post("./set_data.php", params, function(data) {
			})
			.done(function() {
				var changes_data ='{"type":"message","item":"success","action":"<?php print get_text("Status update applied");?>"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
			})
			.fail(function() {
				alert("error");
			});	
		}

		function apply_all_clicked() {
			var i = 1;
			for (i = 1; i <= $('input[name="counter[]"]').length; i++) {
				if ($("#F" + i + "_frm_dispatched").length) {
					if ($("#F" + i + "_frm_dispatched").prop("checked")) {
						set_progression(i, "frm_dispatched");
					}
				}
				if ($("#F" + i + "_frm_responding").length) {
					if ($("#F" + i + "_frm_responding").prop("checked")) {
						set_progression(i, "frm_responding");
					}
				}
				if ($("#F" + i + "_frm_on_scene").length) {
					if ($("#F" + i + "_frm_on_scene").prop("checked")) {
						set_progression(i, "frm_on_scene");
					}
				}
				if ($("#F" + i + "_frm_u2fenr").length) {
					if ($("#F" + i + "_frm_u2fenr").prop("checked")) {
						set_progression(i, "frm_u2fenr");
					}
				}
				if ($("#F" + i + "_frm_u2farr").length) {
					if ($("#F" + i + "_frm_u2farr").prop("checked")) {
						set_progression(i, "frm_u2farr");
					}
				}
				if ($("#F" + i + "_" + "frm_clear").length) {
					if ($("#F" + i + "_frm_clear").prop("checked")) {
						set_progression(i, "frm_clear");
					}
				}
			}
			if (active_assigns_button) {
				$("#active_assigns_button").css("display", "inline");
			}
			if (cleared_assigns_button) {
				$("#cleared_assigns_button").css("display", "inline");
			}
			$("#refresh_button").css("display", "inline");
			$("#save_button").css("display", "none");
			$("#cancel_button").css("display", "none");
		}

		function do_assign_reset_callboard(id) {
	<?php
		if (is_super() || is_admin() || is_operator()) {
	?>
			var resp = "";
			while ((resp.toLowerCase() !="r") && (resp != "d")) {
				resp = prompt("<?php print html_entity_decode(get_text("Enter r to Reset dispatch times.") . " - " . get_text("Enter d to Delete this dispatch."));?>\n", "");
				if (resp === null) {
					$("#F" + id + "_res_times").prop("checked", false);
					return;
				} else {
					switch(resp.toLowerCase()) {
					case "r":
						$.post("./set_data.php", "function=assign_reset&assign_id=" + id)
						.done(function() {
							var changes_data ='{"type":"message","item":"success","action":"<?php print get_text("Assign calls deleted");?>"}';
							window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						})
						.fail(function() {
							alert("error");
						});	
						break;
					case "d":
						if (confirm("<?php print html_entity_decode(get_text('Delete this dispatch record?'));?>")) {
							$.post("./set_data.php", "function=assign_delete&assign_id=" + id)
							.done(function() {
								var changes_data ='{"type":"message","item":"success","action":"<?php print get_text("Assign deleted");?>"}';
								window.parent.navigationbar.postMessage(changes_data, window.location.origin);
							})
							.fail(function() {
								alert("error");
							});
						}
						break;
					default:
						$("#F" + id + "_res_times").prop("checked", false);
						return;
					}
				}
			}
	<?php
		}
	?>
			$("#F" + id + "_res_times").prop("checked", false);
		}

		function assign_edit(id) {
			var changes_data ={"type":"script","item":"main","action":"assign.php?assign_id=" + id};
			changes_data = JSON.stringify(changes_data);
			window.parent.navigationbar.postMessage(changes_data, window.location.origin);
		}

		function ticket_view(id) {
			var changes_data ={"type":"script","item":"main","action":"ticket_report.php?ticket_id=" + id};
			changes_data = JSON.stringify(changes_data);
			window.parent.navigationbar.postMessage(changes_data, window.location.origin);
		}

		function ticket_edit(id) {
			var changes_data ={"type":"script","item":"main","action":"ticket_edit.php?ticket_id=" + id};
			changes_data = JSON.stringify(changes_data);
			window.parent.navigationbar.postMessage(changes_data, window.location.origin);
		}

	<?php
		if ($callboard_enabled == 2) {
	?>
		parent.document.getElementById("callboard").style.height = (<?php print $lines;?> == 0)? "0px" : <?php print get_callboard_height();?> + "px";	// new cb frame height if no assigns; re-use top
		parent.window.setIframeHeight();
	<?php
		} else {
	?>
		parent.document.getElementById("callboard").style.height = (window.innerHeight == 0)? "0px" : <?php print get_callboard_height();?> + "px";	// new cb frame height if no assigns; re-use top
		parent.window.setIframeHeight();
	<?php
		}
	?>

		$(document).ready(function() {
			$.get("./callboard.php?function=table", function(data) {
				$("#callboard").html(data);
			});
			window.addEventListener("message", function(event) {
				if (event.origin != window.location.origin) return;
				new_infos_array_callboard = JSON.parse(event.data);
				if (
					new_infos_array_callboard['reload_flags'] !== undefined && 
					new_infos_array_callboard['reload_flags']['units']
				) {
					$.get("./callboard.php?function=table", function(data) {
						$("#callboard").html(data);
					});
				}
			});
		});

	</script>
	<body onload="check_frames();" onunload="">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" id="main_container">
			<div class="row">
				<div class="col-md-1">
					<div class="container-fluid" style="position: fixed;">
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12"></div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-xs btn-default" type="button" id="refresh_button" onclick="window.location.reload();" style="display: inline;"><?php print get_text("Refresh");?></button>
	<?php
		if (is_super() || is_admin() || is_operator()) {
	?>
								<button class="btn btn-xs btn-default" type="button" id="cancel_button" onclick="cancel_clicked();" style="display: none;"><?php print get_text("Cancel");?></button>
	<?php
		}
	?>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button class="btn btn-xs btn-default" type="button" id="active_assigns_button"<?php print $active_assigns_button_display_str;?> onclick="hide_cleared_assigns();"><?php print get_text("Hide closed assigns");?></button>
								<button class="btn btn-xs btn-default" type="button" id="cleared_assigns_button"<?php print $cleared_assigns_button_display_str;?> onclick="show_cleared_assigns();"><?php print get_text("Show closed assigns");?></button>	
	<?php
		if (is_super() || is_admin() || is_operator()) {
	?>
								<button class="btn btn-xs btn-default" type="button" id="save_button" onclick="apply_all_clicked();" style="display: none;"><?php print get_text("Apply all");?></button>
	<?php
		}
	?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-10">
					<div class="panel panel-default" style="padding: 0px;" id="callboard"></div>
					<div class="col-md-1"></div>
				</div>
			</div>
		</div>
	</body>
</html>
	<?php
	}
?>