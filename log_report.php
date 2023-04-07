<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/log_codes.inc.php");
do_login(basename(__FILE__));

$function = "";
if (isset ($_POST['function'])) {
	$function = $_POST['function'];
}
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
if (isset ($_GET['filter_communication'])) {
	$_SESSION["log_report_filter"]["communication"] = $_GET['filter_communication'];
}
$filter_communication_checked_str = "";
if ($_SESSION["log_report_filter"]["communication"] == "true") {
	$filter_communication_checked_str = " checked";
}
if (isset ($_GET['filter_status'])) {
	$_SESSION["log_report_filter"]["status"] = $_GET['filter_status'];
}
$filter_status_checked_str = "";
if ($_SESSION["log_report_filter"]["status"] == "true") {
	$filter_status_checked_str = " checked";
}
if (isset ($_GET['filter_settings'])) {
	$_SESSION["log_report_filter"]["settings"] = $_GET['filter_settings'];
}
$filter_settings_checked_str = "";
if ($_SESSION["log_report_filter"]["settings"] == "true") {
	$filter_settings_checked_str = " checked";
}
$filter_settings_display_str = " display: none;";
if (is_admin() || is_super()) {
	$filter_settings_display_str = " display: inline;";
}
switch ($function) {
case "update":
	set_session_expire_time();
	if (is_operator() || is_admin() || is_super()) {
		do_log($GLOBALS['LOG_COMMENT'], 0, $_POST['unit_id'], strip_tags(trim($_POST['frm_comment'])), $_POST['facility_id']);
	}
	break;
case "table_bottom":
	?>
	<?php print show_day_night_style();?>
	<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
		<?php show_log_report("log_report", 0, 0, "", $_SESSION["log_report_filter"]);?>
	</table>
	<?php
	break;
default:
	set_session_expire_time();
	$unit_id = 0;
	if (isset ($_GET['unit_id'])) {
		$unit_id = $_GET['unit_id'];
	}
	$facility_id = 0;
	if (isset ($_GET['facility_id'])) {
		$facility_id = $_GET['facility_id'];
	}
	$url_back = "situation.php";
	switch ($_GET['back']) {
	case "units":
		$url_back = "units.php";
		break;
	case "facilities":
		$url_back = "facilities.php";
		break;
	default:
	}
	$auto_poll_settings = explode(",", get_variable("auto_poll"));
	$auto_poll_time = trim($auto_poll_settings[0]);
	$parking_form_data_settings = explode(",", get_variable("parking_form_data"));
	$additional_helptext_form_data_parking_str = "";
	if (trim($parking_form_data_settings[6]) != 0) {
		$additional_helptext_form_data_parking_str =  get_title_str(get_help_text("parked_trigger_chars", true) . ": " . 
			trim($parking_form_data_settings[6]) . " " . get_help_text("parked_seconds", true) . ": " . 
			trim($parking_form_data_settings[7]));
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
		<link href="./css/stylesheet.css" rel="stylesheet">
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
			var parking_form_data_min_trigger_chars = <?php print trim($parking_form_data_settings[6]);?> + 0;

			function send_data() {
				if (log_form.frm_comment.value) {
					$.post("log_report.php", {
						function: "update", frm_comment: log_form.frm_comment.value, unit_id: log_form.unit_id.value, facility_id: log_form.facility_id.value
					},
					function() {
					})
					.done(function() {
						var changes_data ='{"type":"message","item":"info","action":"<?php print get_text("Saved");?>"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						document.log_form.reset();
						set_parked_form_data();
					});
				} else {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", "<?php print get_text("Text");?>");
				}
			}

			function load_content() {
				$.get("./log_report.php?function=table_bottom&filter_communication=" +
					$("#filter_communication").is(':checked') + "&filter_status=" +
					$("#filter_status").is(':checked') + "&filter_settings=" +
					$("#filter_settings").is(':checked'), function(data) {
						$("#table_bottom").html(data);
				});
			}

			function set_parked_form_data(data) {
				try {
					if ((data !== undefined) && (data != null)) {
						var changes_data = {"type":"set_parked_form_data","item":"log_report_form_data","action":""};
						changes_data.log_report_form_data = data;
						changes_data = JSON.stringify(changes_data);
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						var changes_data ='{"type":"set_parked_form_data","item":"log_report_timestamp","action":' + Date.now() + '}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
					} else {
						var changes_data ='{"type":"set_parked_form_data","item":"log_report_form_data","action":""}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
						var changes_data ='{"type":"set_parked_form_data","item":"log_report_timestamp","action":"0"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
					}
				} catch (e) {
				}
			}

			function get_parked_form_data() {
				try {
					if (new_infos_array['parked_form_data']['log_report_timestamp'] != 0) {
						var form_content = [];
						for (var key in new_infos_array['parked_form_data']['log_report_form_data']) {
							form_content[new_infos_array['parked_form_data']['log_report_form_data'][key]['name']] = new_infos_array['parked_form_data']['log_report_form_data'][key]['value'];
						}
						$("#frm_comment").val(form_content['frm_comment']);
						$("#unit_id").val(form_content['unit_id']).change();
						$("#facility_id").val(form_content['facility_id']).change();
					}
				} catch (e) {
				}
			}

			$(document).ready(function() {
				load_content();
				show_to_top_button("<?php print get_text("To top");?>");
				get_parked_form_data();
				$("#frm_comment").focus();
				var changes_data ='{"type":"current_script","item":"script","action":"log_report"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
				<?php show_prevent_browser_back_button();?>
				var change_situation_first_set = 0;
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					new_infos_array = JSON.parse(event.data);
					if (change_situation_first_set == 0) {
						get_parked_form_data();
						screen_id_main = new_infos_array['screen']['screen_id'];
						change_situation_first_set = 1;
					}
					if (new_infos_array['reload_flags']['log']) {
						load_content();
					}
					if (
						$("#frm_comment").val().trim().length > 0 && 
						$("#frm_comment").val().length > parking_form_data_min_trigger_chars && 
						parking_form_data_min_trigger_chars != 0
					) {
						var new_form_data = $("#log_form").serializeArray();
						if ((JSON.stringify(new_form_data) != JSON.stringify(new_infos_array['parked_form_data']['log_report_form_data']))) {
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
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Log report") . " - " . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div id="button_container" class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('<?php print $url_back;?>?screen_id=' + screen_id_main);" tabindex=7><?php print get_text("Cancel");?></button>
								</div>
							</div>
	<?php
	if ((is_super() || is_admin() || is_operator())) {
	?>	
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="set_parked_form_data(); document.log_form.reset();" tabindex=6><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="send_data();" tabindex=5><?php print get_text("Save");?></button>
								</div>
							</div>
	<?php
	}
	?>
						</div>
					</div>
					<div class="col-md-10">
							<div class="panel panel-default" style="padding: 0px;">
								<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
	<?php
	if (is_operator() || is_admin() || is_super()) {
	?>
									<tr class="form-group">
										<th style="width: 10%;"<?php print $additional_helptext_form_data_parking_str;?>><?php print get_text("Text");?>:</th>
										<td colspan=6 style="width: 85%;">
											<form id="log_form" name="log_form">
												<textarea class="form-control" id="frm_comment" name="frm_comment" placeholder="<?php print get_text("New entry");?>" tabindex=1></textarea>
												<?php print get_textblock_select_str("log", "document.log_form.frm_comment", "", 0, "")?>
												<div<?php print get_help_text_str("log_unit_facility");?>>
													<?php print get_unit_select_str("log", $unit_id);?>
													<?php print get_facility_select_str("log", $facility_id);?>
												</div>
											</form>
										</td>
										<td style="width: 5%;"></td>
									</tr>
	<?php
	}
	?>
									<tr<?php print get_help_text_str("_reports_filter");?>>
										<th style="width: 10%;"><?php print get_text("Hide");?>:</th>
										<th>
											<input type="checkbox" id="filter_communication" onchange="load_content();"<?php print $filter_communication_checked_str;?>>
											<div style="display: inline; vertical-align: 22%; padding: 5px;">
												<?php print get_text("Communication");?>
											</div>
										</th>
										<th>
											<input type="checkbox" id="filter_status" onchange="load_content();"<?php print $filter_status_checked_str;?>>
											<div style="display: inline; vertical-align: 22%; padding: 5px;">
												<?php print get_text("Status");?>
											</div>
										</th>
										<th>
											<input type="checkbox" id="filter_settings" style="<?php print $filter_settings_display_str;?>" onchange="load_content();"<?php print $filter_settings_checked_str;?>>
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
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_bottom" style="padding: 0px;"></div>
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