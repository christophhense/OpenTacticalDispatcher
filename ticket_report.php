<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
do_login(basename(__FILE__));
set_session_expire_time("on");

$ticket_id = 1;
if (isset ($_GET['ticket_id'])) {
	$ticket_id = $_GET['ticket_id'];
}
$back_button_lable = get_text("Back");
$back_button_click_str = "ticket_edit.php?ticket_id=" . $ticket_id;
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
$page_function = "show_ticket";
$page_name = "Incident Report";
$script_name = "ticket_report";
if ($function == "dispatch_text") {
	$page_function = "show_dispatch_text";
	$page_name = "Dispatch text";
	$script_name = "dispatch_text";
	$back_button_lable = get_text("Next Page");
	$back_button_click_str = "situation.php?screen_id=' + new_infos_array['screen']['screen_id'] +'";
	$back = "situation";
	if (isset ($_GET['back'])) {
		$back = $_GET['back'];
	}
	if ($back == "ticket") {
		$back_button_lable = get_text("Back");
		$back_button_click_str = "ticket_edit.php?ticket_id=" . $ticket_id;
	}
}
if (is_guest()) {
	$back_button_click_str = "situation.php?screen_id=' + new_infos_array['screen']['screen_id'] +'";
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
		<style type="text/css">
			@media print {
				.printer {display: none;};
			}
		</style>
		<script src="./js/jquery-2.1.4.min.js" type="text/javascript"></script>
		<script src="./js/bootstrap.min.js" type="text/javascript"></script>
		<script src="./js/functions.js" type="text/javascript"></script>
		<script>
			var new_infos_array = [];
			var screen_id_main = 0;

			$(document).ready(function() {
				show_to_top_button("<?php print get_text("To top");?>");
				set_window_present("<?php print $script_name;?>");
				<?php show_prevent_browser_back_button();?>
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					new_infos_array = JSON.parse(event.data);
					screen_id_main = new_infos_array['screen']['screen_id'];
				});
			});

		</script>
	</head>
	<body onload="check_frames();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" id="main_container">
			<div class="row infostring">
				<div<?php print get_table_id_title_str("ticket", $ticket_id);?> class="col-md-12 hidden-print" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
					<?php print get_text($page_name) . get_table_id($ticket_id) . " - "  . get_variable("page_caption");?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-1">
					<div id="button_container" class="container-fluid hidden-print" style="position: fixed;">
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="goto_window('<?php print $back_button_click_str;?>');" tabindex=2><?php print $back_button_lable;?></button>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="window.parent.main.focus(); window.parent.main.print();" tabindex=1><?php print get_text("Print");?></button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-10">
					<div class="panel panel-default" style="padding: 0px;">
						<?php if ($ticket_id) {$page_function($ticket_id, false, true);}?>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
		</div>
		<?php show_accesskeys();?>
	</body>
</html>