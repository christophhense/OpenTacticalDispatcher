<?php
error_reporting(E_ALL);
ini_set('session.cookie_samesite', 'Strict');
@session_start();
require_once ("./incs/functions.inc.php");

if (ini_get("display_errors") == true) {
	$framesize = intval(get_variable("framesize") + 50);
} else {
	$framesize = intval(get_variable("framesize"));
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
	</head>
	<script>

		function setIframeHeight() {
			var navigation_height = $("#navigationbar").outerHeight();
			var callboard_height = $("#callboard").outerHeight();
			callboard_offset = 10;
			if (callboard_height == 0) {
				callboard_offset = 20;
			}
			var main_height = window.innerHeight - navigation_height - callboard_height - callboard_offset - 10;
			$("#main").css("height", main_height + "px");
			try {
				//======================================
				//var element_height = parent.frames["navigationbar"].$("#head_line").outerHeight();
				var element_height = window.parent.navigationbar.$("#head_line").outerHeight();
				//console.log(element_height);
				//======================================
				var min_height = <?php print $framesize;?> + 0;
				if (element_height < min_height) {
					element_height = min_height;
				}
				$("#navigationbar").css("height", element_height + "px");
			} catch (e) {}
		}

	</script>
	<?php
if (!(file_exists("./incs/db_credentials.inc.php"))) {
	print "This appears to be a new installation; file './incs/db_credentials.inc.php' absent. Please run <a href=\"install.php\">install.php</a> with valid database configuration information.";
	exit ();
}
$first_start = "no";
if (isset ($_GET['first_start'])) {
	$first_start = $_GET['first_start'];
}
if (((count_units_and_facilities_and_users() == 0) && (($first_start == "yes") || (empty ($_SESSION['wizard']))))) {
	$_SESSION['wizard'] = "do_not_show_wizard_select_page";
	?>
	<body onLoad="setIframeHeight();">
		<div class="container-fluid">
				<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<h3><span class="label label-primary">OpenTacticalDispatcher</span></h3>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<?php print get_variable("_version");?>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row" style="margin-top: 100px;">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="panel panel-default">
						<div class="panel-heading" style="padding: 1px; text-align: center;">
							<h4><?php print get_text("Basic Configuration");?></h4>
						</div>
						<div class="panel-body">
							<?php print get_help_text("help7", true);?>
						</div>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-4">
					<button class="btn btn-default btn-lg btn-block" onclick="location.href='quick_start.php?function=first_start';"><?php print get_text("Start configuration wizard");?></button>
				</div>
				<div class="col-md-4">
					<button class="btn btn-default btn-lg btn-block" onclick="location.href='index.php';"><?php print get_text("Go to login");?></button>
				</div>
				<div class="col-md-2"></div>
			</div>
		</div>
	<?php
} else {
	?>
	<style type="text/css">
		html, body {
			overflow: hidden; height: 100% ; background-color: white;
		}
		body {
			margin: 0; padding: 0;
		}
	</style>
	<body onresize="setIframeHeight();">
		<iframe id="navigationbar" name="navigationbar" src="navigation.php" scrolling="no" frameborder="0" style="width: 100%; height: <?php print $framesize;?>px; overflow: hidden;">
			<?php print get_text("Requires a iframes-capable browser.");?>
		</iframe>
		<iframe id="callboard" name="callboard" src="callboard.php" scrolling="auto" frameborder="0" style="width: 100%; height: 0px;">
			<?php print get_text("Requires a iframes-capable browser.");?>
		</iframe>
		<iframe id="main" name="main" src="situation.php" scrolling="auto" frameborder="0" style="width: 100%; height: 0px;">
			<?php print get_text("Requires a iframes-capable browser.");?>
		</iframe>
	<?php
}
	?>
	</body>
</html>