<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/tickets.inc.php");

$moment_date_format = php_to_moment(get_variable("date_format"));

if (!(isset ($_SESSION['latestticket']))) {

	$query = "SELECT `id` " .
		"FROM `tickets` " .
		"WHERE `status` != " . $GLOBALS['STATUS_RESERVED'] . " " .
		"ORDER BY `id` DESC;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) > 0) {
		$row = db_fetch_assoc($result);
		$_SESSION['latestticket'] = $row['id'];
	} else {
		$_SESSION['latestticket'] = 0;		
	}
}
if (isset ($_GET['logout'])) {
	do_logout();
	exit ();
} else {
	do_login(basename(__FILE__));
}
$time = microtime(true);
$blink_seconds = get_variable("heading_blink");
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
$screen_id = 0;
if (isset ($_GET['screen_id'])) {
	$screen_id = $_GET['screen_id'];
}
switch ($function) {
case "infostring_middle":
	$blink_news_period = mysql_datetime(time() - $blink_seconds);

	$query = "(SELECT '0' AS `which`, `t`.`id`, `incident_name` AS `the_value` FROM `actions` `a` " .
		"LEFT JOIN `tickets` `t` ON (`t`.`id` = `a`.`ticket_id`) " .
		"WHERE `a`.`updated` > '" . $blink_news_period . "' AND `t`.`status` != " . $GLOBALS['STATUS_RESERVED'] . "  LIMIT 1) " .
		
		"UNION (SELECT '1' AS `which`, `id`, `incident_name` AS `the_value` FROM `tickets` `t` " .
		"WHERE `updated` > '" . $blink_news_period . "' AND `t`.`status` != " . $GLOBALS['STATUS_RESERVED'] . " LIMIT 1) " .
		
		"UNION (SELECT '3' AS `which`, `id`, NULL  AS `the_value` FROM `log` " .
		"WHERE `datetime` = (SELECT MAX(`datetime`) FROM `log` WHERE (`code` IN ('" . $GLOBALS['LOG_CALL_DISPATCHED'] . "', '" . $GLOBALS['LOG_CALL_RESPONDING'] . "', '" . $GLOBALS['LOG_CALL_ON_SCENE'] .
		"', '" . $GLOBALS['LOG_CALL_CLEAR'] . "', '" . $GLOBALS['LOG_CALL_FACILITY_ENROUTE'] . "', '" . $GLOBALS['LOG_CALL_FACILITY_ARRIVED'] . "'))) AND (`datetime` > '" . $blink_news_period . "') LIMIT 1);";

	$result = db_query($query, __FILE__, __LINE__);
	$info_str = "";
	if ((db_num_rows($result) > 0) && ($blink_seconds != 0)) {
		$row = stripslashes_deep(db_fetch_assoc($result));
		switch ($row['which']) {
		case "0":	
			$info_str = get_text("Incident") . " " . $row['the_value'] . ": " . get_text("Add Action");
			break;
		case "1":
			if($_SESSION['latestticket'] < $row['id']) {
				$_SESSION['latestticket'] = $row['id'];
				$info_str = get_text("Incident") . " " . $row['the_value'] . ": " . get_text("New");
			} else {
				$info_str = get_text("Incident") . " " . $row['the_value'] . ": " . get_text("Edited");
			}
			break;
		case "3":
			$query = "SELECT * FROM `log` `l` " .
				"LEFT JOIN `tickets` `t` ON (`l`.`ticket_id` = `t`.`id`) " .
				"LEFT JOIN `units` `r` ON (`l`.`unit_id` = `r`.`id`) " .
				"WHERE `l`.`id` = " . $row['id'] . " " .
				"LIMIT 1;";
			$result = db_query($query, __FILE__, __LINE__);
			$row = stripslashes_deep(db_fetch_assoc($result));
			switch ($row['code']) {
			case $GLOBALS['LOG_CALL_DISPATCHED']:
				$info_str = get_text("Incident") . " " . $row['incident_name'] . ": " . get_text("Unit") . " " . $row['handle'] . " " . get_text("Dispatched");
				break;
			case $GLOBALS['LOG_CALL_RESPONDING']:
				$info_str = get_text("Incident") . " " . $row['incident_name'] . ": " . get_text("Unit") . " " . $row['handle'] . " " . get_text("Responding");
				break;
			case $GLOBALS['LOG_CALL_ON_SCENE']:
				$info_str = get_text("Incident") . " " . $row['incident_name'] . ": " . get_text("Unit") . " " . $row['handle'] . " " . get_text("On-scene");
				break;
			case $GLOBALS['LOG_CALL_FACILITY_ENROUTE']:
				$info_str = get_text("Incident") . " " . $row['incident_name'] . ": " . get_text("Unit") . " " . $row['handle'] . " " . get_text("Fac en-route");
				break;
			case $GLOBALS['LOG_CALL_FACILITY_ARRIVED']:
				$info_str = get_text("Incident") . " " . $row['incident_name'] . ": " . get_text("Unit") . " " . $row['handle'] . " " . get_text("Fac arr");
				break;
			case $GLOBALS['LOG_CALL_CLEAR']:
				$info_str = get_text("Incident") . " " . $row['incident_name'] . ": " . get_text("Unit") . " " . $row['handle'] . " " . get_text("Clear");
				break;
			default:
				$info_str = get_text ("Updated");
			}
		}
	}
	print $info_str;
	unset ($result);
		break;
case "table_top":
//	show_ticketlist("situation", $tickets_views, $screen_id);
	show_ticketlist("situation", $screen_id);
	break;
case "table_left":
	show_units_list("situation", 1, 2, 0);
	break;
case "table_right":
	show_units_list("situation", 2, 2, 0);
	break;
default:
	set_session_expire_time();
	$auto_poll_settings = explode(",", get_variable("auto_poll"));
	$auto_poll_time = trim($auto_poll_settings[0]);
	$current_situation_type = "tickets_units";
	$report_last_settings = explode(",", get_variable("report_last"));
	$start_date = mysql_datetime(time() - trim($report_last_settings[0]) * 60);
	$end_date = mysql_datetime();
	if ($screen_id != 0) {
		if (isset ($_SESSION["screen_id_" . $screen_id]['situation_type'])) {
			$current_situation_type = $_SESSION["screen_id_" . $screen_id]['situation_type'];
		} else {
			$_SESSION["screen_id_" . $screen_id]['situation_type'] = $current_situation_type;
		}
		if (isset ($_SESSION["screen_id_" . $screen_id]['closed_interval_start'])) {
			if ($_SESSION["screen_id_" . $screen_id]['closed_interval_start'] != 0) {
				$start_date = $_SESSION["screen_id_" . $screen_id]['closed_interval_start'];
			}
		} else {
			$_SESSION["screen_id_" . $screen_id]['closed_interval_start'] = $start_date;
		}
		if (isset ($_SESSION["screen_id_" . $screen_id]['closed_interval_end'])) {
			if ($_SESSION["screen_id_" . $screen_id]['closed_interval_end'] != 0) {
				$end_date = $_SESSION["screen_id_" . $screen_id]['closed_interval_end'];
			}
		} else {
			$_SESSION["screen_id_" . $screen_id]['closed_interval_end'] = $end_date;
		}
	}
	$get_id = null;
	if (array_key_exists('id', ($_GET))) {
		$get_id = $_GET['id'];
	}
	$set_regions_control = "";
	if ((!($get_id)) && ((get_num_groups()) && (count(get_allocates($GLOBALS['TYPE_USER'], $_SESSION['user_id'])) > 1))) {
		$set_regions_control = "set_regions_control('" . get_num_groups() . "');";
	}
	?>
<!DOCTYPE html>
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
		<link href="./css/stylesheet.css" rel="stylesheet">
		<link href="./css/tabdrop.css" rel="stylesheet">
		<script src="./js/jquery-2.1.4.min.js" type="text/javascript"></script>
		<script src="./js/bootstrap.min.js" type="text/javascript"></script>
		<script src="./js/moment-with-locales.js" type="text/javascript"></script>
		<script src="./js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
		<script src="./js/bootstrap-tabdrop.js" type="text/javascript"></script>
		<script src="./js/functions.js" type="text/javascript"></script>
		<?php print show_day_night_style();?>
		<script>

			var new_infos_array = [];
			var screen_id_main = <?php print $screen_id;?> + 0;
			var report_last = <?php print (trim($report_last_settings[0]) * 60);?> + 0;
			var start_date = "<?php print $start_date;?>";
			var end_date = "<?php print $end_date;?>";
			var blink_var = false;
			var blink_count;
			var original_heading_str;

			function do_blink(infostring) {
				if ($("#infostring_middle").html() == "&nbsp;") {
					$("#infostring_middle").html(infostring);
				} else {
					$("#infostring_middle").html("&nbsp;");
				}
				blink_count--;
				if (blink_count == 0) {
					end_blink();
				}
			}

			function start_blink(infostring) {
				original_heading_str = $("#infostring_middle").html();
				blink_var = setInterval("do_blink('" + infostring + "')", 500);
				blink_count = <?php print $blink_seconds;?>;
			}

			function end_blink() {
				if (blink_var) {
					$("#infostring_middle").html(original_heading_str);
					clearInterval(blink_var);
				}
			}

			function edit_assign(assign_id) {
				<?php if (is_operator() || is_admin() || is_super()) { ?>
				goto_window("assign.php?back=situation&assign_id=" + assign_id);
				<?php } ?>
			}

			function edit_ticket(ticket_id) {
				goto_window("<?php print (is_guest())? "ticket_report.php" : "ticket_edit.php";?>?ticket_id=" + ticket_id);
			}

			function get_infostring() {
				$.get("./situation.php?function=infostring_middle", function(data) {
					if (data != "") {
						end_blink();
						start_blink(data);
					}
				});
			}

			function get_tickets() {
				$.get("./situation.php?function=table_top&screen_id=" + screen_id_main, function(data) {
					$("#table_top").html(data);
				})
				.done(function() {
					$("#severity_normal").html($("#count_severity_normal").html());
					$("#severity_medium").html($("#count_severity_medium").html());
					$("#severity_high").html($("#count_severity_high").html());
				})
				.fail(function () {
					alert("error");
				});
			}

			function get_units() {
				$.get("./situation.php?function=table_left&screen_id=" + screen_id_main, function(data) {
					$("#table_left").html(data);
				});
				$.get("./situation.php?function=table_right&screen_id=" + screen_id_main, function(data) {
					$("#table_right").html(data);
				});
			}

			function closed_interval_changed() {
				var errmsg = "";
				if ((moment($("#closed_interval_start").val(), "<?php print $moment_date_format;?>").isValid())) {
					$("#closed_interval_start_mysql_timestamp").val(moment($("#closed_interval_start").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
					set_closed_interval("start", $("#closed_interval_start_mysql_timestamp").val());
				} else {
					errmsg += "<?php print get_text('date/time error');?><br>";
				}
				if ((moment($("#closed_interval_end").val(), "<?php print $moment_date_format;?>").isValid())) {
					$("#closed_interval_end_mysql_timestamp").val(moment($("#closed_interval_end").val(), "<?php print $moment_date_format;?>").format("YYYY-MM-DD HH:mm:ss"));
					set_closed_interval("end", $("#closed_interval_end_mysql_timestamp").val());
				} else {
					errmsg += "<?php print get_text('date/time error');?><br>";
				}
				get_tickets();
				return errmsg;
			}

			function set_closed_interval(type, time) {
				var url = "./set_data.php";
				var params = "";
				if (type == "start") {
					params = "function=closed_interval_start&value=" + time + "&screen_id=" + screen_id_main;
					start_date = time;
				} else {
					params = "function=closed_interval_end&value=" + time + "&screen_id=" + screen_id_main;
					end_date = time;
				}
				$.get(url, params)
				.done(function () {
				})
				.fail(function () {
					alert("error");
				});
			}

			function change_situation(tab_id) {
				$.get("./set_data.php", "function=situation_type&value=" + tab_id + "&screen_id=" + screen_id_main)
				.done(function () {
				})
				.fail(function () {
					alert("error");
				});
				$("#" + tab_id).addClass("active");
				if (tab_id == "tickets_closed") {
					if (start_date.valueOf() == "0") {
						$("#closed_interval_start").val(moment(moment(new_infos_array['screen']['date_time'], "YYYY-MM-DD HH:mm:ss").subtract(report_last, "seconds")).format("<?php print $moment_date_format;?>"));
						$("#closed_interval_start_mysql").val(moment(new_infos_array['screen']['date_time']).subtract(report_last, "seconds"));
					}
					if (end_date.valueOf() == "0") {
						$("#closed_interval_end").val(moment(new_infos_array['screen']['date_time'], "YYYY-MM-DD HH:mm:ss").format("<?php print $moment_date_format;?>"));
						$("#closed_interval_end_mysql").val(new_infos_array['screen']['date_time']);
					}
					closed_interval_changed();
					$("#severity_counts").css("display", "none");
					$("#closed_interval").css("display", "inline");
				} else {
					set_closed_interval("start", 0);
					set_closed_interval("end", 0);
					$("#severity_counts").css("display", "inline");
					$("#closed_interval").css("display", "none");
				}
				switch (tab_id) {
				case "tickets_units":
					$("#infostring_middle").html("<?php print get_text("Current situation") . " - " . get_variable("page_caption");?>");
					original_heading_str = "<?php print get_text("Current situation") . " - " . get_variable("page_caption");?>";
					$("#table_top_panel").css("display", "inline-block");
					$("#table_left_panel").css("display", "inline-block");
					$("#table_right_panel").css("display", "inline-block");
					get_tickets();
					get_units();
					break;
				case "tickets_0":
					$("#infostring_middle").html("<?php print get_text("Tickets") . " - " . get_variable("page_caption");?>");
					original_heading_str = "<?php print get_text("Tickets") . " - " . get_variable("page_caption");?>";
					$("#table_top_panel").css("display", "inline-block");
					$("#table_left_panel").css("display", "none");
					$("#table_right_panel").css("display", "none");
					get_tickets();
					break;
				case "tickets_scheduled":
					$("#infostring_middle").html("<?php print get_text("Scheduled tickets") . " - " . get_variable("page_caption");?>");
					original_heading_str = "<?php print get_text("Scheduled tickets") . " - " . get_variable("page_caption");?>";
					$("#table_top_panel").css("display", "inline-block");
					$("#table_left_panel").css("display", "none");
					$("#table_right_panel").css("display", "none");
					get_tickets();
					break;
				case "tickets_closed":
					$("#infostring_middle").html("<?php print get_text("Closed tickets") . " - " . get_variable("page_caption");?>");
					original_heading_str = "<?php print get_text("Closed tickets") . " - " . get_variable("page_caption");?>";
					$("#table_top_panel").css("display", "inline-block");
					$("#table_left_panel").css("display", "none");
					$("#table_right_panel").css("display", "none");
					break;
				case "units_0":
					$("#infostring_middle").html("<?php print get_text("Units") . " - " . get_variable("page_caption");?>");
					original_heading_str = "<?php print get_text("Units") . " - " . get_variable("page_caption");?>";
					$("#table_top_panel").css("display", "none");
					$("#table_left_panel").css("display", "inline-block");
					$("#table_right_panel").css("display", "inline-block");
					$("#severity_counts").css("display", "none");
					$("#table_top").html("");
					get_units();
					break;
				default:
					//tickets_1ff
					//units_1ff
					//default:
				}
			}

			$(document).ready(function() {
				$("#closed_interval_start").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true
				});

				$("#closed_interval_start").on("dp.change", function (e) {
					$("#closed_interval_end").data("DateTimePicker").minDate(e.date);
					closed_interval_changed();
				});

				$("#closed_interval_start").data("DateTimePicker").
					maxDate(moment($("#closed_interval_end").val(), "<?php print $moment_date_format;?>"));

				$("#closed_interval_end").datetimepicker({
					locale: "<?php print get_variable("_locale");?>",
					format: "<?php print $moment_date_format;?>",
					sideBySide: true,
					useCurrent: false
				});

				$("#closed_interval_end").on("dp.change", function (e) {
					$("#closed_interval_start").data("DateTimePicker").maxDate(e.date);
					closed_interval_changed();
				});

				$("#closed_interval_end").data("DateTimePicker").
					minDate(moment($("#closed_interval_start").val(), "<?php print $moment_date_format;?>"));

				change_situation("<?php print $current_situation_type;?>");
				get_infostring();
				var changes_data ='{"type":"current_script","item":"script","action":"situation"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					new_infos_array = JSON.parse(event.data);
					screen_id_main = new_infos_array['screen']['screen_id'];
					if (new_infos_array['reload_flags']['tickets']) {
						if ((current_unit_id !== undefined) && (current_unit_id > 0)) {
							show_assigns(current_unit_id);
						}
						get_tickets();
						get_infostring();
					}
					if (new_infos_array['reload_flags']['units']) {
						if ((current_unit_id !== undefined) && (current_unit_id > 0)) {
							show_assigns(current_unit_id);
						}
						get_units();
						get_infostring();
					}
				});
			});

		</script>
	</head>
	<body onload="check_frames(); <?php print $set_regions_control;?> location.href='#top';" onunload="end_blink();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" id="main_container">
			<div class="row" style="height: 45px;">
				<div class="col-md-1"></div>
				<div class="col-md-5 infostring" id="infostring_middle" style="text-align: right; margin-bottom: 10px;"></div>
				<div class="col-md-5" style="text-align: left;">
					<div id="severity_counts" style="display: none;">
						<span<?php print get_help_text_str("_SevSeverity");?>><?php print get_text("Severity");?>:</span>
						<span class="severity_normal"<?php print get_help_text_str("_SevNormal");?>>&nbsp;
							<?php print get_text("Normal");?> (<a class="severity_normal" id="severity_normal"></a>)
						</span>
						<span class="severity_medium"<?php print get_help_text_str("_SevMedium");?>>&nbsp;
							<?php print get_text("Medium");?> (<a class="severity_medium" id="severity_medium"></a>)
						</span>
						<span class="severity_high"<?php print get_help_text_str("_SevHigh");?>>&nbsp;
							<?php print get_text("High");?> (<a class="severity_high" id="severity_high"></a>)
						</span>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<span><?php print get_text("Mouseover for details.");?></span>
					</div>
					<div id="closed_interval" style="display: none;">
						<div style="float: left; width: 40%;">
							<input type="text" class="form-control" id="closed_interval_start" value="<?php print date(trim(get_variable("date_format")), strtotime($start_date));?>">
							<input type="hidden" class="form-control" id="closed_interval_start_mysql_timestamp" name="closed_interval_start">
						</div>
						<div class=" infostring" style="display: inline-block; width: 20%; text-align: center;"><?php print get_text("to");?></div>
						<div style="float: right; width: 40%;">
							<input type="text" class="form-control" id="closed_interval_end" value="<?php print date(trim(get_variable("date_format")), strtotime($end_date));?>">
							<input type="hidden" class="form-control" id="closed_interval_end_mysql_timestamp" name="closed_interval_end">
						</div>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row" style="margin-top: 5px; margin-bottom: 5px;">
				<div class="col-md-1"></div>
	 				<div class="col-md-10">
						<ul class="nav nav-tabs">
							<li id="tickets_units" role="presentation" data-toggle="tab" onclick="change_situation(this.id);"><a href="#"><?php print get_text("Situation");?></a></li>
							<li id="tickets_0" role="presentation" data-toggle="tab" onclick="change_situation(this.id);"><a href="#"><?php print get_text("Tickets");?></a></li>
							<li id="tickets_scheduled" role="presentation" data-toggle="tab" onclick="change_situation(this.id);"><a href="#"><?php print get_text("Scheduled tickets_short");?></a></li>
							<li id="tickets_closed" role="presentation" data-toggle="tab" onclick="change_situation(this.id);"><a href="#"><?php print get_text("Closed tickets_short");?></a></li>
							<li id="units_0" role="presentation" data-toggle="tab" onclick="change_situation(this.id);"><a href="#"><?php print get_text("Units");?></a></li>
						</ul>
					</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div id="table_top_panel" class="panel panel-default" style="display: none; padding: 0px;">
						<div id="table_top"></div>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-5">
					<div id="table_left_panel" class="panel panel-default" style="display: none; padding: 0px;">
						<div id="table_left"></div>
					</div>
				</div>
				<div class="col-md-5">
					<div id="table_right_panel" class="panel panel-default" style="display: none; padding: 0px;">
						<div id="table_right"></div>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
		</div>
		<script>
			$(".nav-pills, .nav-tabs").tabdrop();
		</script>
		<?php show_infobox();?>
		<?php show_infobox("large");?>
	</body>
</html>
	<?php
 }
 ?>