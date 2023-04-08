<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/log_codes.inc.php");
do_login(basename(__FILE__));
set_session_expire_time();

$moment_date_format = php_to_moment(get_variable("date_format"));

$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
$report_last_settings = explode(",", get_variable("report_last"));
$start_date = time() - trim($report_last_settings[0]) * 60;
if (isset ($_GET['start'])) {
	$start_date = strtotime($_GET['start']);
}
$end_date = time();
if (isset ($_GET['end'])) {
	$end_date = strtotime($_GET['end']);
}
$incident_location = "";
if (isset ($_GET['frm_incident_location'])) {
	$incident_location = $_GET['frm_incident_location'];
}
$incident_facility_id = 0;
if (isset ($_GET['frm_incident_facility'])) {
	$incident_facility_id = $_GET['frm_incident_facility'];
}
$receiving_location = "";
if (isset ($_GET['frm_receiving_location'])) {
	$receiving_location = $_GET['frm_receiving_location'];
}
$receiving_facility_id = 0;
if (isset ($_GET['frm_receiving_facility'])) {
	$receiving_facility_id = $_GET['frm_receiving_facility'];
}
$query_text = "";
if (isset ($_GET['frm_query_text'])) {
	$query_text = $_GET['frm_query_text'];
}
$incident_name = "";
if (isset ($_GET['frm_incident_name'])) {
	$incident_name = $_GET['frm_incident_name'];
}
$in_types_id = 0;
if (isset ($_GET['frm_in_types_id'])) {
	$in_types_id = $_GET['frm_in_types_id'];
}
$unit_id = 0;
if (isset ($_GET['frm_unit'])) {
	$unit_id = $_GET['frm_unit'];
}
$guard_house_id = 0;
if (isset ($_GET['frm_guard_house'])) {
	$guard_house_id = $_GET['frm_guard_house'];
}
$user_id = 0;
if (isset ($_GET['frm_user'])) {
	$user_id = $_GET['frm_user'];
}
$status = 0;
if (isset ($_GET['frm_status'])) {
	$status = $_GET['frm_status'];
}
if (isset ($_GET['filter_communication'])) {
	$_SESSION["reports_filter"]["communication"] = $_GET['filter_communication'];
}
$filter_communication_checked_str = "";
if ($_SESSION["reports_filter"]["communication"] == "true") {
	$filter_communication_checked_str = " checked";
}
if (isset ($_GET['filter_status'])) {
	$_SESSION["reports_filter"]["status"] = $_GET['filter_status'];
}
$filter_status_checked_str = "";
if ($_SESSION["reports_filter"]["status"] == "true") {
	$filter_status_checked_str = " checked";
}
if (isset ($_GET['filter_settings'])) {
	$_SESSION["reports_filter"]["settings"] = $_GET['filter_settings'];
}
$filter_settings_checked_str = "";
if ($_SESSION["reports_filter"]["settings"] == "true") {
	$filter_settings_checked_str = " checked";
}
$filter_settings_display_str = " display: none;";
if (is_admin() || is_super()) {
	$filter_settings_display_str = " display: inline;";
}
switch ($function) {
case "ticket_report":
	$where = "";
	$where .= " WHERE (`problemstart` >= '" . date("Y-m-d H:i:s", $start_date) . "' AND `problemstart` < '" . date("Y-m-d H:i:s", $end_date) . "'";
	$where .= " OR `problemend` >= '" . date("Y-m-d H:i:s", $start_date) . "' AND `problemend` < '" . date("Y-m-d H:i:s", $end_date) . "')  AND";
	$where .= " (`t`.`updated` != '2017-01-01 00:00:00') AND (0 = 0 ";
	if (($incident_location != "") && ($incident_facility_id == 0)) {
		$where .= " AND (`t`.`location` LIKE '%" . $incident_location . "%'";
		$where .= " OR `a`.`on_scene_location` LIKE '%" . $incident_location . "%'";
		$where .= " OR `inc_fac`.`handle` LIKE '%" . $incident_location . "%'";
		$where .= " OR `on_sc_fac`.`handle` LIKE '%" . $incident_location . "%')";
	}
	if ($incident_facility_id != 0) {
		$where .= " AND (`t`.`facility_id` = " . $incident_facility_id;
		$where .= " OR `a`.`on_scene_facility_id` = " . $incident_facility_id . ")";
	}
	if (($receiving_location != "") && ($receiving_facility_id == 0)) {
		$where .= " AND (`a`.`receiving_location` LIKE '%" . $receiving_location . "%'";
		$where .= " OR `rec_fac`.`handle` LIKE '%" . $receiving_location . "%')";
	}
	if ($receiving_facility_id != 0) {
		$where .= " AND `a`.`receiving_facility_id` = " . $receiving_facility_id;
	}
	if ($query_text != "") {
		$where .= " AND (`t`.`location` LIKE '%" . $query_text . "%'";
		$where .= " OR `t`.`phone` LIKE '%" . $query_text . "%'";
		$where .= " OR `t`.`contact` LIKE '%" . $query_text . "%'";
		$where .= " OR `t`.`description` LIKE '%" . $query_text . "%'";
		$where .= " OR `t`.`comments` LIKE '%" . $query_text . "%'";
		$where .= " OR `a`.`on_scene_location` LIKE '%" . $query_text . "%'";
		$where .= " OR `a`.`receiving_location` LIKE '%" . $query_text . "%'";
		$where .= " OR `a`.`comments` LIKE '%" . $query_text . "%'";
		$where .= " OR `ac`.`description` LIKE '%" . $query_text . "%'";
		$where .= " OR `inc_fac`.`handle` LIKE '%" . $query_text . "%'";
		$where .= " OR `on_sc_fac`.`handle` LIKE '%" . $query_text . "%'";
		$where .= " OR `rec_fac`.`handle` LIKE '%" . $query_text . "%')";
	}
	if ($incident_name != "") {
		$where .= " AND `incident_name` LIKE '%" . $incident_name . "%'";
	}
	if ($in_types_id != 0) {
		$where .= " AND `t`.`incident_type_id` = " . $in_types_id;
	}
	if ($unit_id != 0) {
		$where .= " AND `a`.`unit_id` = " . $unit_id;
	}
	if ($guard_house_id != 0) {
		$where .= " AND `u`.`guard_house_id` = " . $guard_house_id;
	}
	if ($user_id != 0) {
		$where .= " AND (`t`.`call_taker_id` = " . $user_id;
		$where .= " OR `t`.`user_id` = " . $user_id;
		$where .= " OR `a`.`user_id` = " . $user_id . ")";
	}
	if ($status != 0) {
		$where .= " AND `t`.`status` = " . $status;
	}
	$where .= ")";

	$query = "SELECT DISTINCT `t`.`id`, " .
		"`t`.`problemstart` " .
		"FROM `tickets` `t` " .
		"LEFT JOIN `assigns` `a` ON `a`.`ticket_id` = `t`.`id` " .
		"LEFT JOIN `actions` `ac` ON `ac`.`ticket_id` = `t`.`id` " .
		"LEFT JOIN `facilities` `inc_fac` ON `inc_fac`.`id` = `t`.`facility_id` " .
		"LEFT JOIN `facilities` `on_sc_fac` ON `on_sc_fac`.`id` = `a`.`on_scene_facility_id` " .
		"LEFT JOIN `facilities` `rec_fac` ON `rec_fac`.`id` = `a`.`receiving_facility_id` " .
		"LEFT JOIN `units` `u` ON `u`.`id` = `a`.`unit_id` " .
		$where . " " .
		"ORDER BY `t`.`problemstart` ASC;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_affected_rows($result) == 0) {
	?>
	<?php print show_day_night_style();?>
	<div class="panel panel-default hidden-print" style="padding: 0px;">
		<table class="table table-striped table-condensed">
			<tr>
				<th style="text-align: center; width: 100%"><?php print get_text("No data for this filter!");?></th>
			</tr>
		</table>
	</div>
	<?php
	} else {
		$numrows = db_num_rows($result);
		$page_num = 1;
		while ($row_ticket = stripslashes_deep(db_fetch_array($result))) {
			$last_page = false;
			if ($numrows == $page_num) {
				$last_page = true;
			}
			?>
	<?php print show_day_night_style();?>
	<div class="hidden-print" style="text-align: left;"><h5><strong><?php print get_text("Incident") . " " . $page_num . " " . get_text("of") . " " . $numrows;?></strong></h5></div>
	<div class="panel panel-default" style="padding: 0px;">
	<?php
			show_ticket($row_ticket['id'], false, $last_page);
			$page_num++;
	?>
	</div>
	<?php
		}
	}
	break;
case "log_report":
	$where = "";
	if (($incident_location != "") && ($incident_facility_id == 0)) {
		$where .= " AND (`t`.`location` LIKE '%" . $incident_location . "%'";
		$where .= " OR `a`.`on_scene_location` LIKE '%" . $incident_location . "%'";
		$where .= " OR `inc_fac`.`handle` LIKE '%" . $incident_location . "%'";
		$where .= " OR `on_sc_fac`.`handle` LIKE '%" . $incident_location . "%')";
	}
	if ($incident_facility_id != 0) {
		$where .= " AND `t`.`facility_id` = " . $incident_facility_id;
	}
	if (($receiving_location != "") && ($receiving_facility_id == 0)) {
		$where .= " AND (`a`.`receiving_location` LIKE '%" . $receiving_location . "%'";
		$where .= " OR `rec_fac`.`handle` LIKE '%" . $receiving_location . "%')";
	}
	if ($receiving_facility_id != 0) {
		$where .= " AND `a`.`receiving_facility_id` = " . $receiving_facility_id;
	}
	if ($query_text != "") {
		$where .= " AND (`t`.`location` LIKE '%" . $query_text . "%'";
		$where .= " OR `t`.`phone` LIKE '%" . $query_text . "%'";
		$where .= " OR `t`.`contact` LIKE '%" . $query_text . "%'";
		$where .= " OR `t`.`description` LIKE '%" . $query_text . "%'";
		$where .= " OR `t`.`comments` LIKE '%" . $query_text . "%'";
		$where .= " OR `a`.`on_scene_location` LIKE '%" . $query_text . "%'";
		$where .= " OR `a`.`receiving_location` LIKE '%" . $query_text . "%'";
		$where .= " OR `a`.`comments` LIKE '%" . $query_text . "%'";
		$where .= " OR `l`.`text` LIKE '%" . $query_text . "%'";
		$where .= " OR `inc_fac`.`handle` LIKE '%" . $query_text . "%'";
		$where .= " OR `on_sc_fac`.`handle` LIKE '%" . $query_text . "%'";
		$where .= " OR `rec_fac`.`handle` LIKE '%" . $query_text . "%')";
	}
	if ($incident_name != "") {
		$where .= " AND `t`.`incident_name` LIKE '%" . $incident_name . "%'";
	}
	if ($in_types_id != 0) {
		$where .= " AND `t`.`incident_type_id` = " . $in_types_id;
	}
	if ($unit_id != 0) {
		$where .= " AND `l`.`unit_id` = " . $unit_id;
	}
	if ($guard_house_id != 0) {
		$where .= " AND `r`.`guard_house_id` = " . $guard_house_id;
	}
	if ($user_id != 0) {
		$where .= " AND `l`.`user_id` = " . $user_id;
	}
	$status_codes = array (0, 11, 10, 26);
	if ($status != 0) {
		$where .= " AND `l`.`code` = " . $status_codes[$status];
	}
	?>
	<?php print show_day_night_style();?>
	<div style="text-align: center; height: 50px;"><h3><strong><?php print get_text("Log report");?></strong></h3></div>
	<div class="panel panel-default" style="padding: 0px;">
		<table class="table table-striped table-condensed" style="table-layout: fixed;">
			<?php show_log_report("reports", $start_date, $end_date , $where, $_SESSION["reports_filter"]);?>
		</table>
	</div>
	<?php
	break;
default:
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
		<script src="./js/jquery.highlight-5.closure.js" type="text/javascript"></script>
		<?php print show_day_night_style();?>
		<script>

			function query_changed() {
				var errmsg = "";
				if ((moment($("#start").val(), "<?php print $moment_date_format;?>").isValid())) {
					$("#start_mysql_timestamp").val(moment($("#start").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
				} else {
					errmsg += "<?php print get_text('date/time error');?><br>";
				}
				if ((moment($("#end").val(), "<?php print $moment_date_format;?>").isValid())) {
					$("#end_mysql_timestamp").val(moment($("#end").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
				} else {
					errmsg += "<?php print get_text('date/time error');?><br>";
				}
				if (errmsg != "") {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", errmsg);
					return false;
				} else {
					if ($("#frm_incident_facility").val() > 0) {
						$("#frm_incident_location").val("");
						$("#frm_incident_location").prop("readonly", true);
					} else {
						$("#frm_incident_location").prop("readonly", false);
					}
					if ($("#frm_receiving_facility").val() > 0) {
						$("#frm_receiving_location").val("");
						$("#frm_receiving_location").prop("readonly", true);
					} else {
						$("#frm_receiving_location").prop("readonly", false);
					}
					if ($("#frm_function option:selected" ).val() == "log_report") {
						$("#filter_communication").prop("disabled", false);
						$("#filter_status").prop("disabled", false);
						$("#filter_settings").prop("disabled", false);
					} else {
						$("#filter_communication").prop("disabled", true);
						$("#filter_status").prop("disabled", true);
						$("#filter_settings").prop("disabled", true);
					}
					$.get("reports.php", {
						function: $("#frm_function").val(), start: $("#start_mysql_timestamp").val(), end: $("#end_mysql_timestamp").val(),
						frm_incident_location: $("#frm_incident_location").val(), frm_incident_facility: $("#frm_incident_facility").val(),
						frm_receiving_location: $("#frm_receiving_location").val(), frm_receiving_facility: $("#frm_receiving_facility").val(),
						frm_query_text: $("#frm_query_text").val(), frm_incident_name: $("#frm_incident_name").val(), frm_in_types_id: $("#frm_in_types_id").val(),
						frm_unit: $("#frm_unit").val(), frm_guard_house: $("#frm_guard_house").val(), frm_user: $("#frm_user").val(), frm_status: $("#frm_status").val(),
						filter_communication: $("#filter_communication").is(':checked'), filter_status: $("#filter_status").is(':checked'), filter_settings: $("#filter_settings").is(':checked')
					}, function(data) {
						$("#content").html("<div style=\"display: flex; align-items: center; justify-content: center;\"><div class=\"loader\"></div></div>");
					})
					 .done(function(data) {
						setTimeout(function() {
							$("#content").html(data);
							$("#content").highlight($("#frm_query_text").val(), {caseSensitive: false});
							$("#content").highlight($("#frm_incident_location").val(), {caseSensitive: false});
							$("#content").highlight($("#frm_receiving_location").val(), {caseSensitive: false});
							$("#content").highlight($("#frm_incident_name").val(), {caseSensitive: false});
						}, 100);
					});	
				}
			}

			$(document).ready(function() {
				$("#start").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true
				});

				$("#start").on("dp.change", function (e) {
					$("#end").data("DateTimePicker").minDate(e.date);
					query_changed();
				});

				$("#start").data("DateTimePicker").maxDate(moment($("#end").val(), "<?php print $moment_date_format;?>"));

				$("#end").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true,
					useCurrent: false
				});

				$("#end").on("dp.change", function (e) {
					$("#start").data("DateTimePicker").maxDate(e.date);
					query_changed();
				});

				$("#end").data("DateTimePicker").minDate(moment($("#start").val(), "<?php print $moment_date_format;?>"));

				show_to_top_button("<?php print get_text("To top");?>");
				$("#frm_query_text").focus();
				set_window_present("reports");
				query_changed();
				<?php show_prevent_browser_back_button();?>
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					new_infos_array = JSON.parse(event.data);
				});
			});

		</script>
	</head>
	<body onload="check_frames();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<form name="reports_form">
			<div class="container-fluid" id="main_container">
				<div class="row infostring">
					<div class="col-md-12 hidden-print" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Reports") . " - " . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row hidden-print">
					<div class="col-md-1">
						<div id="button_container" class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('situation.php?screen_id=' + new_infos_array['screen']['screen_id']);"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="document.reports_form.reset(); query_changed();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="window.focus(); window.print();"><?php print get_text("Print");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
								<tr<?php print get_help_text_str("_reports_period");?> style="height: 45px;">
									<th style="width: 30%;"><?php print get_text("Select the period") . ":";?></th>
									<td colspan=3 style="width: 70%;">
										<div style="float: left; width: 48%;">
											<input type="text" class="form-control" id="start" value="<?php print date(trim(get_variable("date_format")), $start_date);?>">
											<input type="hidden" class="form-control" id="start_mysql_timestamp" name="start">
										</div>
										<div style="float: right; width: 48%;">
											<input type="text" class="form-control" id="end" value="<?php print date(trim(get_variable("date_format")), $end_date);?>">
											<input type="hidden" class="form-control" id="end_mysql_timestamp" name="end">
										</div>
									</td>
								</tr>
								<tr style="height: 45px;">
									<th<?php print get_help_text_str("_reports_function");?>><?php print get_text("Report type");?>:</th>
									<td colspan=3>
										<select id="frm_function" name="frm_function" style="margin-top: 5px;" class="form-control" onchange="query_changed();">
											<option value="ticket_report"><?php print get_text("Incident Reports");?></option>
											<option value="log_report"><?php print get_text("Log report");?></option>
										</select>
									</td>
								</tr>
								<tr<?php print get_help_text_str("_reports_text_query");?> style="height: 45px;">
									<th><?php print get_text("Query text");?>:</th>
									<td colspan=3><input id="frm_query_text" name="frm_query_text" type="text" class="form-control" onchange="query_changed();" tabindex=1></td>
								</tr>
								<tr style="height: 45px;">
									<th<?php print get_help_text_str("_reports_location");?>><?php print get_text("Incident location");?> / <?php print get_text("On-Scene location");?>:</th>
									<td colspan=3>
										<input type="text" id="frm_incident_location" name="frm_incident_location" class="form-control" onchange="query_changed();" cols=48 rows=3 tabindex=2>
										<?php print get_facility_select_str("report_on_scene_location");?>
									</td>
								</tr>
								<tr style="height: 45px;">
									<th<?php print get_help_text_str("_reports_receiving");?>><?php print get_text("Receiving location");?>:</th>
									<td colspan=3>
										<input type="text" id="frm_receiving_location" name="frm_receiving_location" class="form-control" onchange="query_changed();" cols=48 rows=3 tabindex=3>
										<?php print get_facility_select_str("report_receiving_location");?>
									</td>
								</tr>
								<tr<?php print get_help_text_str("_reports_filter");?> style="height: 45px;">
									<th style="width: 10%;"><?php print get_text("Hide");?>:</th>
									<th>
										<input type="checkbox" id="filter_communication" onchange="query_changed();"<?php print $filter_communication_checked_str;?>>
										<div style="display: inline; vertical-align: 22%; padding: 5px;">
											<?php print get_text("Communication");?>
										</div>
									</th>
									<th>
										<input type="checkbox" id="filter_status" onchange="query_changed();"<?php print $filter_status_checked_str;?>>
										<div style="display: inline; vertical-align: 22%; padding: 5px;">
											<?php print get_text("Status");?>
										</div>
									</th>
									<th>
										<input type="checkbox" id="filter_settings" style="<?php print $filter_settings_display_str;?>" onchange="query_changed();"<?php print $filter_settings_checked_str;?>>
										<div style="vertical-align: 22%; padding: 5px;<?php print $filter_settings_display_str;?>">
											<?php print get_text("Configuration");?>
										</div>
									</th>
									<td colspan=3></td>
									<td style="width: 5%;"></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
								<tr<?php print get_help_text_str("_reports_incident_name");?> style="height: 45px;">
									<th style="width: 30%;"><?php print get_text("Incident name");?>:</th>
									<td style="width: 70%;"><input id="frm_incident_name" name="frm_incident_name" type="text" class="form-control" onchange="query_changed();" tabindex=4></td>
								</tr>
								<tr<?php print get_help_text_str("_reports_incident_type");?> style="height: 45px;">
									<th><?php print get_text("Incident type");?>:</th>
									<td><?php print get_incident_type_select_str("reports_form", "frm_in_types_id");?></td>
								</tr>
								<tr<?php print get_help_text_str("_reports_unit");?> style="height: 45px;">
									<th><?php print get_text("Unit");?>:</th>
									<td><?php print get_unit_select_str("report");?></td>
								</tr>
								<tr<?php print get_help_text_str("_reports_guard_house");?> style="height: 45px;">
									<th><?php print get_text("Guard house");?>:</th>
									<td><?php print get_guard_house_select_str("report");?><br>
								</tr>
								<tr<?php print get_help_text_str("_reports_user");?> style="height: 45px;">
									<th><?php print get_text("User");?>:</th>
									<td><?php print get_user_select_str("report");?></td>
								</tr>
								<tr<?php print get_help_text_str("_reports_status");?> style="height: 45px;">
									<th><?php print get_text("Status");?>:</th>
									<td><?php print get_ticket_status_select_str("report");?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="col-md-1 hidden-print"></div>
				<div id="content" class="col-md-10" style="padding: 0px;"></div>
				<div class="col-md-1 hidden-print"></div>
			</div>
		</form>
		<?php show_infobox();?>
	</body>
</html>
	<?php
}
?>