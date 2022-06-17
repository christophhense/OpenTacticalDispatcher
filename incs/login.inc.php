<?php
require_once ("api.inc.php");

function do_logout() {
	global $hide_dispatched, $hide_status_groups;
	ini_set('session.cookie_samesite', 'Strict');
	@session_start();

	$query = "UPDATE `users` " .
		"SET `expires` = NULL, " .
		"`session_id` = NULL, " .
		"`current_radio` = NULL " .
		"WHERE `session_id` = '" . session_id() . "' " .
		"LIMIT 1;";

	db_query($query, __FILE__, __LINE__);
	if (is_super() || is_admin() || is_operator()) {
		$login_logout_settings = explode(",", get_variable("_api_login_logout_setng"));
		$logout_setting = trim($login_logout_settings[1]);
		do_api_message($_SESSION['user_id'], $_SERVER['REMOTE_ADDR'], $logout_setting, $_SESSION['user_name'], "", "");
	}
	do_log($GLOBALS['LOG_SIGN_OUT'], 0, 0, "");
	if (isset ($_COOKIE[session_name()])) {
		setcookie(session_name(), "", time() - 42000, "/");
	}
	$_SESSION = array ();
	@session_destroy();
	do_login("situation.php", true);
}

function is_not_expired($user_id) {
	if ($user_id > 0) {

		$query = "SELECT * " .
			"FROM `users` " .
			"WHERE `id` = " . $user_id . " " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		$row = stripslashes_deep(db_fetch_assoc($result));
		return ((db_affected_rows($result) == 1) && ($row['expires'] > mysql_datetime()));
	} else {
		return false;
	}
}

function do_login($requested_page, $logout = false) {
	global $hide_dispatched, $hide_status_groups;
	if (!get_working_in_development_environement()) {
		db_query('SET sql_mode = "ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"', __FILE__, __LINE__);
	}
	ini_set('session.cookie_samesite', 'Strict');
	@session_start();
	$datetime_now = mysql_datetime();
	if ((!(isset ($_SESSION['user_id']))) || (!(is_not_expired($_SESSION['user_id']))) || ($logout == true)) {
		$warn_str = "";
		if (isset ($_POST['frm_passwd'])) {
			$categories = array ();

			$query = "SELECT * " .
				"FROM `assigns` " .
				"WHERE `clear` != NULL;";

			$result = db_query($query, __FILE__, __LINE__);

			$num_disp = db_num_rows($result);
			if (($num_disp > 0) && ($hide_dispatched == 1)) {
				$i = 1;
				$category_butts[0] = "Deployed";
			} else {
				$i = 0;
			}
			if ($hide_status_groups == 1) {

				$query = "SELECT DISTINCT `group` " .
					"FROM `unit_status` " .
					"ORDER BY `group` ASC;";

				$result = db_query($query, __FILE__, __LINE__);
				while ($row = stripslashes_deep(db_fetch_assoc($result))) {
					$categories[$i] = $row['group'];
					$i++;
				}
				unset ($result);
			} else {
				$categories[$i] = "Available";
				$i++;
				$categories[$i] = "Not Available";
			}
			$fac_categories = array ();
			$i = 0;

			$query = "SELECT * " .
				"FROM `facility_types` " .
				"ORDER BY `name` ASC;";

			$result = db_query($query, __FILE__, __LINE__);
			while ($row = stripslashes_deep(db_fetch_assoc($result))) {
				$fac_categories[$i] = $row['name'];
				$i++;
			}
			unset ($result);

			$query 	= "SELECT *, " .
				"`name` AS `user_name` " .
				"FROM `users` " . 
				"WHERE `name` = " . quote_smart($_POST['frm_user']) . 
				"AND (`password` = PASSWORD(" . quote_smart($_POST['frm_passwd']) . ") " .
				"OR `password` = MD5(" . quote_smart(strtolower($_POST['frm_passwd'])) . " )) " .
				"AND `password` != '55606758fdb765ed015f0612112a6ca7' " .
				"LIMIT 1;";

			$result = db_query($query, __FILE__, __LINE__);
			$affected_rows = db_affected_rows($result);
			if ($affected_rows == 1) {
				$row = stripslashes_deep(db_fetch_assoc($result));
				if ($row['id'] == get_variable("_api_user_id")) {
					$warn_str = get_text("Application Interface - No Login possible");
				}
			}
			if (($affected_rows == 1) && ($row['id'] != get_variable("_api_user_id"))) {
				$session_time_limit_settings = explode(",", get_variable("session_time_limit"));
				$session_time_limit = trim($session_time_limit_settings[0]);
				$session_logout_warning = trim($session_time_limit_settings[1]);
				$browser = check_browser();
				set_database_timezone();

				$query = "UPDATE `users` SET `session_id` = '" . session_id() . "', " .
					"`expires` = '" . mysql_datetime(time() + ($session_time_limit * 60)) . "', " .
					"`current_radio` = NULL, " .
					"`login_datetime` = '" . $datetime_now . "', " .
					"`login_address` = '" . $_SERVER['REMOTE_ADDR'] . "', " .
					"`browser` = '" . $browser . "' " .
					"WHERE `id` = " . $row['id'] . " " .
					"LIMIT 1;";

				$result = db_query($query, __FILE__, __LINE__);

				$_SESSION['id'] = session_id();
				$_SESSION['user_id'] = $row['id'];
				$_SESSION['user_name'] = $row['user_name'];
				$_SESSION['level'] = $row['level'];
				$_SESSION['login_at'] = $datetime_now;
				$_SESSION['unit_flag_1'] = "";		// unit id where status or position change (Flag-Icon)
				$_SESSION['regions_boxes'] = "s";
				$_SESSION['timeout'] = "on";
				if ($session_logout_warning == 0) {
					$_SESSION['timeout'] = "off";
				}
				$_SESSION['screens'] = array ();
				$_SESSION['reset_button'] = array ();
				foreach ($categories as $key => $value) {
					$sess_flag = "show_hide_" . $value;
					$_SESSION[$sess_flag] = "s";
				}
				foreach ($fac_categories as $key => $value) {
					$fac_sess_flag = "show_hide_fac_" . $value;
					$_SESSION[$fac_sess_flag] = "h";
				}
				$report_log_settings = explode(",", get_variable("report_log"));
				$_SESSION["log_report_filter"]["communication"] = "false";
				if (trim($report_log_settings[3]) == 1) {
					$_SESSION["log_report_filter"]["communication"] = "true";
				}
				$_SESSION["log_report_filter"]["status"] = "false";
				if (trim($report_log_settings[4]) == 1) {
					$_SESSION["log_report_filter"]["status"] = "true";
				}
				$_SESSION["log_report_filter"]["settings"] = "false";
				if (trim($report_log_settings[5]) == 1) {
					$_SESSION["log_report_filter"]["settings"] = "true";
				}
				$report_last_settings = explode(",", get_variable("report_last"));
				$_SESSION["reports_filter"]["communication"] = "false";
				if (trim($report_last_settings[1]) == 1) {
					$_SESSION["reports_filter"]["communication"] = "true";
				}
				$_SESSION["reports_filter"]["status"] = "false";
				if (trim($report_last_settings[2]) == 1) {
					$_SESSION["reports_filter"]["status"] = "true";
				}
				$_SESSION["reports_filter"]["settings"] = "false";
				if (trim($report_last_settings[3]) == 1) {
					$_SESSION["reports_filter"]["settings"] = "true";
				}
				do_log($GLOBALS['LOG_SIGN_IN'], 0, 0, $browser);
				if (is_super() || is_admin() || is_operator()) {
					$login_logout_settings = explode(",", get_variable("_api_login_logout_setng"));
					$login_setting = trim($login_logout_settings[0]);
					do_api_message($_SESSION['user_id'], $_SERVER['REMOTE_ADDR'], $login_setting, $_SESSION['user_name'], "", "");
				}
				exit ();
			} else {
				$log_message =  $_POST['frm_user'] . "  " . $_SERVER['REMOTE_ADDR'] . "  " . check_browser();
				@error_log("Login failed. Username: " . $log_message);
				do_log($GLOBALS['LOG_INFO'], 0, 0, get_text("Login failed. Username") . ": " . $log_message);
			}
		}
		@session_destroy();
		if (isset ($_POST['frm_passwd'])) {
			$warn_str = get_text("Login failed. Please enter correct values and try again.");
		}
		if ((($_SERVER["HTTP_HOST"] == "localhost") || ($_SERVER["HTTP_HOST"] == "127.0.0.1") || ($_SERVER["HTTP_HOST"] == "[::1]"))) {
			$login_click_str = " onclick=\"document.login_form.frm_user.value='admin'; document.login_form.frm_passwd.value='admin'; document.login_form.submit();\"";
		} else {
			$login_click_str = "";
		}
		$moment_time_format = php_to_moment(get_variable("date_format_time_only_clock"));
		$moment_date_format = php_to_moment(get_variable("date_format_date_only_clock"));
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
		<script src="./js/moment-with-locales.js" type="text/javascript"></script>
		<script src="./js/functions.js" type="text/javascript"></script>
		<script defer="defer">
			window.parent.navigationbar.href="navigation.php";

			$(document).ready(function() {
				moment.locale("<?php print get_variable("_locale");?>");
				$("#time_of_day").html(moment("<?php print $datetime_now;?>", "YYYY-MM-DD HH:mm:ss").format("<?php print $moment_time_format;?>") + " <?php print get_text("o'clock");?>");
				$("#date_of_day").html(moment("<?php print $datetime_now;?>", "YYYY-MM-DD HH:mm:ss").format("<?php print $moment_date_format;?>"));
				activate_show_hide_password();
				var change_situation_first_set = 0;
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					get_infos_array = JSON.parse(event.data);
					$("#time_of_day").html(moment(get_infos_array['screen']['date_time']).format("<?php print $moment_time_format;?>") + " <?php print get_text("o'clock");?>");
					$("#date_of_day").html(moment(get_infos_array['screen']['date_time']).format("<?php print $moment_date_format;?>"));
				});
			});

		</script>
	</head>
	<body onload="document.login_form.frm_user.focus();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" style="margin-left: 18px;">
			<div class="row" style="height: 180px;">
				<div class="col-md-6"></div>
				<div class="col-md-5">
					<div id="time_of_day" style="font-size: 600%;"></div>
					<div id="date_of_day" style="font-size: 250%;"></div>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-4 jumbotron">
					<div style="height: 20px;">
						<h4><span class="label label-danger"><?php print $warn_str;?></span></h4>
					</div>
					<h4<?php print $login_click_str;?> style="margin-bottom: 20px;"><?php print get_text("Please login");?>:</h4>
					<form class="form-horizontal" method="post" action="<?php print $requested_page;?>" name="login_form">
						<div class="form-group">
							<div class="col-sm-8">
								<input type="text" class="form-control" name="frm_user" onchange="document.login_form.frm_user.value = document.login_form.frm_user.value.trim();" placeholder="<?php print get_text("User");?>">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-8">
								<input type="password" class="form-control" id="frm_passwd" name="frm_passwd" onchange="document.login_form.frm_passwd.value = document.login_form.frm_passwd.value.trim();" placeholder="<?php print get_text("Password");?>">
							</div>
							<div class="pw_show glyphicon glyphicon-eye-open"<?php print get_help_text_str("show_hide_password");?>></div>
						</div>
						<div class="form-group">
							<div class="col-sm-8">
							<button type="submit" class="btn btn-default"><?php print get_text("Login");?></button>
							</div>
						</div>
					</form>
				</div>
				<div class="col-md-7"></div>
			</div>
		</div>
	</body>
</html>
	<?php
		exit ();
	}
}
?>