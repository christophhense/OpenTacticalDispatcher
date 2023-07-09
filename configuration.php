<?php
error_reporting(E_ALL);
session_start();	
require_once ("./incs/functions.inc.php");
require_once ("./incs/configuration.inc.php");
require_once ("./incs/log_codes.inc.php");
require_once ("./incs/api.inc.php");
do_login(basename(__FILE__));

$datetime_now = mysql_datetime();	
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
if (($function == "") && isset ($_POST['function'])) {
	$function = $_POST['function'];
}
switch ($function) {
case "profile_update":
case "user_insert":
case "user_update":
case "audio_update":
case "settings_update":
case "incident_numbers_update":
case "api_update":
case "facilities_status_reset_update":
case "facility_types_update":
case "facility_status_update":
case "unit_status_reset_update":
case "unit_types_update":
case "unit_status_update":
case "presentation_tab_update":
case "presentation_list_update":
////case "regions_update":
////case "cleanse_regions_update":
////case "reset_regions_update":
case "incident_types_update":
case "textblocks_update":
case "captions_update":
case "hints_update":
case "optimize":
case "do_reset":
case "do_update":	
	break;
default:

	$query = "SELECT `name` AS `user_name` " .
		"FROM `users` " .
		"WHERE `password` != '55606758fdb765ed015f0612112a6ca7';";

	$result	= db_query($query, __FILE__, __LINE__);
	$users = "";
	while ($row = stripslashes_deep(db_fetch_assoc($result))) {
		$users .= trim($row['user_name']) . "\t";
	}
	$language = get_language();
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
		<link href="./css/bootstrap-multiselect.css" rel="stylesheet" type="text/css">
		<link href="./css/fileinput.min.css" media="all" rel="stylesheet" type="text/css">
		<link href="./css/stylesheet.css" rel="stylesheet">
		<link href="./css/tabdrop.css" rel="stylesheet">
		<script src="./js/jquery-2.1.4.min.js" type="text/javascript"></script>
		<script src="./js/bootstrap.min.js" type="text/javascript"></script>
		<script src="./js/moment-with-locales.js" type="text/javascript"></script>
		<script src="./js/bootstrap-multiselect.js" type="text/javascript"></script>
		<script src="./js/md5.js"></script>
		<script src="./js/jscolor/jscolor.js"></script>
		<script src="./js/functions.js" type="text/javascript"></script>
		<script src="./js/bootstrap-tabdrop.js" type="text/javascript"></script>
		<script src="./js/fileinput.min.js" type="text/javascript"></script>
		<script src="./js/fileinput_locales/<?php print $language;?>.js" type="text/javascript"></script>
		<?php print show_day_night_style();?>
		<script>

			function in_array(ary, val) {
				for (var i = 0; i < ary.length; i++) {
					if(ary[i] == val) {
						return true;
					}
				}
				return false;
			}

			var str_users = "<?php print $users;?>";
			var ary_users = str_users.split("\t");

			function do_delete_user(result) {
				if (result == true) {
					send_configuration_form("frm_user");
				}
			}

			function validate_user(theForm) {
				add_or_edit = "add";
				if (theForm.frm_func.value == "e") {
					add_or_edit = "edit";
				}
				if ($("#frm_remove").val() == "true") {	
					show_infobox("<?php print get_text("Please confirm removing");?>", $("#frm_user").val(), false, do_delete_user);
					return false;
				}
				var error_message = "";
				if ((add_or_edit == "add") && ($("#frm_user").val() == ""))	{
					error_message += "<?php print html_entity_decode(get_text('UserID is required.'));?><br>";
				}
				if ((theForm.frm_func.value=="a") && (theForm.frm_user.value.length > 0) && (in_array(ary_users, theForm.frm_user.value.trim())) && (theForm.frm_user.value != "")) {
					error_message += "<?php print html_entity_decode(get_text('UserID duplicates existing one.'));?><br>";
				}
				var got_level = false;
				for (i = 0; i < theForm.frm_level.length; i++) {
					if (theForm.frm_level[i].checked) {
						got_level = true;
					}
				}
				if (!got_level) {
					error_message += "<?php print get_text("User LEVEL is required.");?><br>";
				}	
				if (theForm.frm_passwd.value != theForm.frm_passwd_confirm.value) {
					error_message += "<?php print get_text("Passwd and confirmation must match.");?><br>";
				}
				if ((theForm.frm_func.value == "a") && (theForm.frm_passwd.value == "")) {
					error_message += "<?php print get_text('PASSWORD is required.');?><br>";
				}
				if ((theForm.frm_passwd.value.trim().length > 0) && (theForm.frm_passwd.value.trim().length < 6)) {
					error_message += "<?php print get_text("Passwd length 6 or more is required.");?><br>";
				}
				if (error_message != "") {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", error_message);
					return false;
				} else {
					theForm.frm_hash.value = ((theForm.frm_passwd.value.trim() == "!!!!!!!!") || (theForm.frm_passwd.value.trim() == ""))? "": hex_md5(theForm.frm_passwd.value.trim().toLowerCase());
					theForm.frm_passwd.value = "";
					theForm.frm_passwd_confirm.value = "";
					send_configuration_name_form(theForm);
				}
			}

			$(document).ready(function() {
				activate_show_hide_password();
				set_window_present("configuration");
				<?php show_prevent_browser_back_button();?>
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					new_infos_array = JSON.parse(event.data);
					try {
						set_current_infos();
					} catch(e) {
					}
				});
			});

		</script>
	</head>
	<body onload="check_frames();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
	<?php
	show_infobox("small");
	show_infobox("large");
}
switch ($function) {
case "profile_update":
	if (empty ($_POST['frm_hash'])) {
		$pass = "";
	} else {
		$pass = "`password` = '" . $_POST['frm_hash'] . "',";
	}

	$query = "UPDATE `users` SET " . $pass . " " .
		"`email` = '" . $_POST['frm_email'] . "', " .
		"`user_id` = " . $_SESSION['user_id'] . ", " .
		"`client_address` = '" . $_SERVER['REMOTE_ADDR'] . "', " .
		"`updated` = '" . $datetime_now . "' " .
		"WHERE `id` = " . $_SESSION['user_id'] . ";";

	db_query($query, __FILE__, __LINE__);
	do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("Your profile has been updated.") . "  ", 0, "", "", "");
	print get_text("Your profile has been updated.") . "<br>";
	exit;
case "profile":

	$query = "SELECT `id` " .
		"FROM `users` " .
		"WHERE `id` = " . $_SESSION['user_id'] . ";";

	if ($_SESSION['user_id'] < 0 OR check_for_rows($query) == 0) {
		do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, __LINE__ . " Invalid user id '" . $_SESSION['user_id'] . "'.", 0, "", "", "");
		print __LINE__ . " Invalid user id '" . $_SESSION['user_id'] . "'." . "<br>";
	} else {

		$query	= "SELECT * " .
			"FROM `users` " .
			"WHERE `id` = " . $_SESSION['user_id'] . ";";

		$result	= db_query($query, __FILE__, __LINE__);
		$row = db_fetch_array($result, __FILE__, __LINE__);
	?>
		<script>

			function validate_profile(theForm) {
				var error_message = "";
				if (theForm.frm_passwd.value!=theForm.frm_passwd_confirm.value) {
					error_message += "<?php print get_text("Passwd and confirmation must match.");?><br>";
				} else {
					if ((theForm.frm_passwd.value.trim() == "") || (theForm.frm_passwd.value.trim().length < 6)) {
						error_message += "<?php print get_text("Passwd length 6 or more is required.");?><br>";
					}
				}

				if (error_message != "") {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", error_message);
					return false;
				} else {
//					if(theForm.frm_passwd.value != "") {
//						theForm.frm_hash.value = hex_md5(theForm.frm_passwd.value.trim().toLowerCase());
						theForm.frm_hash.value = ((theForm.frm_passwd.value.trim() == "!!!!!!!!") || (theForm.frm_passwd.value.trim() == ""))? "": hex_md5(theForm.frm_passwd.value.trim().toLowerCase());		
						theForm.frm_passwd.value = theForm.frm_passwd_confirm.value = "";
//				}
					send_configuration_form("frm_profile");
				}
			}

		</script>
		<div class="container-fluid" id="main_container">
			<form id="frm_profile" name="frm_profile">
				<input type="hidden" id="function" name="function" value="profile_update">
				<input type="hidden" id="frm_id" name="frm_id" value="<?php print $_SESSION['user_id'];?>">
				<input type="hidden" id="frm_hash" name="frm_hash" value="<?php print $row['password'];?>">
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Edit My Profile") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="document.frm_profile.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="validate_profile(this.form);"><?php print get_text("Save");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr>
									<th<?php print get_help_text_str("_user_password");?> style="width: 20%;"><?php print get_text("New Password");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
									<td<?php print get_help_text_str("_user_password");?> style="width: 40%;"><input type="password" id="frm_passwd" name="frm_passwd" class="form-control mandatory" value="!!!!!!!!"></td>
									<td style="width: 40%; padding-left: 15px;"><div class="pw_show glyphicon glyphicon-eye-open"<?php print get_help_text_str("show_hide_password");?>></div></td>
								</tr>
								<tr<?php print get_help_text_str("_user_password");?>>
									<th><?php print get_text("Confirm");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
									<td><input type="password" id="frm_passwd_confirm" name="frm_passwd_confirm" class="form-control mandatory" value="!!!!!!!!"></td>
									<td></td>
								</tr>
								<tr<?php print get_help_text_str("_user_email");?>>
									<th><?php print get_text("Email");?>:</th>
									<td><input class="form-control" value="<?php print $row['email'];?>" name="frm_email"></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</form>		
		</div>
	</body>
</html>
	<?php
	}
	break;
case "user_insert":
	if (is_super() || is_admin()) {
		if ($_POST['frm_passwd'] == $_POST['frm_passwd_confirm']) {
			if (($_POST['frm_level'] == 0) && is_admin()) {
				$level = 1;
			} else {
				$level = $_POST['frm_level'];
			}
			$new_id = insert_into_users($_POST['frm_user'], $_POST['frm_hash'], $level, $_POST['frm_email'], $datetime_now);
			if ($new_id > 0) {
				foreach ($_POST['frm_group'] as $grp_val) {
					insert_into_allocates($grp_val, $GLOBALS['TYPE_USER'], $new_id, $_SESSION['user_id'], $datetime_now);
				}
				do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("User has been added") . remove_nls(": " . $_POST['frm_user'] . "  " . get_text("Level") . ": " . get_level_text(trim($_POST['frm_level'])) . "  " . get_text("Email") . ": " . trim($_POST['frm_email']) . "  "), 0, "", "", "");
				print get_text("User has been added") . ": <i>" . $_POST['frm_user'] . "</i><br>";
			}
		}
	}
	exit;
case "user_add":
	if (is_super() || is_admin()) {
		$super_disabled = "";
		$admin_disabled = "";
		if (!(is_super())) {
			$super_disabled = " disabled";
			$admin_disabled = " disabled";
		}
	?>
		<script>

			$(function () {
				$("#frm_user").focus();
			})

		</script>
		<div class="container-fluid" id="main_container">
			<form id="user_add_form" name="user_add_Form">
				<input type="hidden" id="function" name="function" value="user_insert">
				<input type="hidden" id="frm_func" name="frm_func" value="a">
				<input type="hidden" id="frm_hash" name="frm_hash" value="">
				<input type="hidden" id="frm_group[]" name="frm_group[]" value="1">
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Add user") . " - " . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<?php if (is_admin() || is_super()) { ?>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="document.user_add_Form.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="validate_user(document.user_add_Form);"><?php print get_text("Save");?></button>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr<?php print get_help_text_str("_user_name");?>>
									<th style="width: 20%;"><?php print get_text("User ID");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
									<td style="width: 40%;"><input type="text" id="frm_user" name="frm_user" class="form-control mandatory"></td>
									<td style="width: 40%;"></td>
								</tr>
								<tr>
									<th<?php print get_help_text_str("_user_password");?>><?php print get_text("Password");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
									<td<?php print get_help_text_str("_user_password");?>><input type="password" id="frm_passwd" name="frm_passwd" class="form-control mandatory"></td>
									<td style="padding-left: 15px;"><div class="pw_show glyphicon glyphicon-eye-open"<?php print get_help_text_str("show_hide_password");?>></div></td>
								</tr>
								<tr<?php print get_help_text_str("_user_password");?>>
									<th><?php print get_text("Confirm");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
									<td><input type="password" id="frm_passwd_confirm" name="frm_passwd_confirm" class="form-control mandatory"></td>
									<td style="width: 40%;"></td>
								</tr>
								<tr style="height: 44px;">
									<th<?php print get_help_text_str("_user_level");?>><?php print get_text("Level");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
									<td>
										<label class="radio-inline"<?php print get_help_text_str("level_0");?>>
											<input type="radio" id="frm_level_super" name="frm_level"<?php print $super_disabled;?> value="<?php print $GLOBALS['LEVEL_SUPER'];?>">
											<?php print get_text("permission_super");?>
											</label>
										<label class="radio-inline"<?php print get_help_text_str("level_1");?>>
											<input type="radio" id="frm_level_admin" name="frm_level" name="frm_level"<?php print $admin_disabled;?> value="<?php print $GLOBALS['LEVEL_ADMINISTRATOR'];?>">
											<?php print get_text("permission_admin");?>
										</label>
										<label class="radio-inline"<?php print get_help_text_str("level_2");?>>
											<input type="radio" id="frm_level_operator" name="frm_level" value="<?php print $GLOBALS['LEVEL_OPERATOR'];?>">
											<?php print get_text("permission_operator");?></label>
										<label class="radio-inline"<?php print get_help_text_str("level_3");?>>
											<input type="radio" id="frm_level_guest" name="frm_level" value="<?php print $GLOBALS['LEVEL_GUEST'];?>">
											<?php print get_text("permission_guest");?>
										</label>
									</td>
									<td style="width: 40%;"></td>
								</tr>
								<tr<?php print get_help_text_str("_user_email");?>>
									<th><?php print get_text("Email");?>: </th>
									<td><input id="frm_email" name="frm_email" class="form-control" value=""></td>
									<td style="width: 40%;"></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "user_update":
	$user_deletet = false;
	$log_text_password = "";
	$log_text_level = "";
	$log_text_email = "";

	$query = "SELECT `name`, `level`, `email` " .
		"FROM `users` " .
		"WHERE `id` = " . $_POST['frm_id'] . ";";

	$result	= db_query($query, __FILE__, __LINE__);
	$row = db_fetch_assoc($result);
	$old_value = $row;
	if (
		is_super() || (
			is_admin() && (
				$row['level'] == $GLOBALS['LEVEL_OPERATOR'] ||
				$row['level'] == $GLOBALS['LEVEL_GUEST']
			)
		)
	) {
		$level_str = "";
		if (isset ($_POST['frm_level'])) {
			if (($_POST['frm_level'] == 0) && (is_super())) {
				$level_str = "`level` = 0 ";
			}
			if (($_POST['frm_level'] > 0) && (is_super() || is_admin())) {
				$level_str = "`level` = " . trim($_POST['frm_level']) . " ";
				if (trim($old_value['level']) != trim($_POST['frm_level'])) {
					$log_text_level = get_text("Level") . ": " . get_level_text(trim($old_value['level'])) . " => " . get_level_text(trim($_POST['frm_level'])) . "  ";
				}
			}
		}
		$email_str = "";
		if (isset ($_POST['frm_email'])) {
			$email_str = " `email` = '" . $_POST['frm_email'] . "', ";
			if (trim($old_value['email']) != trim($_POST['frm_email'])) {
				$log_text_email = get_text("Email") . ": " . trim($old_value['email']) . " => " . trim($_POST['frm_email']) . "  ";
			}
			if ($level_str == "") {
				$email_str = substr($email_str, 0, -2) . " ";
			}
		}
		$password_str = "";
		if (!empty ($_POST['frm_hash'])) {
			$password_str = "`password` = '" . $_POST['frm_hash'] . "', ";
			$log_text_password = get_text("Password") . ": ******** => ********  ";
		}
		if (
			(array_key_exists('frm_remove', $_POST)) &&
			($_POST['frm_remove'] == "true") &&
			($_POST['frm_id'] != $_SESSION['user_id'])
		) {
			$password_str = "`password` = '55606758fdb765ed015f0612112a6ca7', ";
			$user_deletet = true;
		}
		if ($email_str == "") {
			$password_str = substr($password_str, 0, -2) . " ";
		}
		if (($password_str != "") || ($email_str != "") || ($level_str != "")) {

			$query = "UPDATE `users` SET " .
				"`user_id` = " . $_SESSION['user_id'] . ", " .
				"`client_address` = '" . $_SERVER['REMOTE_ADDR'] . "', " .
				"`updated` = '" . $datetime_now . "', " .
				$password_str . $email_str . $level_str .
				"WHERE `id` = " . $_POST['frm_id'] . ";";

			$result = db_query($query, __FILE__, __LINE__);
			$groups = "," . implode(',', $_POST['frm_group']) . ",";
			$curr_groups = implode(',', get_allocates(4, $_POST['frm_id']));
			$ex_grps = explode(',', $curr_groups);
			if (($curr_groups != $groups) && ($_POST['frm_group'] != "")) {
				foreach ($_POST['frm_group'] as $posted_grp) {
					if (!in_array($posted_grp, $ex_grps)) {
						insert_into_allocates($posted_grp, $GLOBALS['TYPE_USER'], $_POST['frm_id'], $_SESSION['user_id'], $datetime_now);
					}
				}
				foreach ($ex_grps as $key => $existing_grps) {
					if ((($existing_grps != "") && ((!in_array($existing_grps, $_POST['frm_group'])) || $user_deletet))) {

						$query  = "DELETE FROM `allocates` " .
							"WHERE `type` = " . $GLOBALS['TYPE_USER'] . " " .
							"AND `group` = " . $existing_grps . " " .
							"AND `resource_id` = " . $_POST['frm_id'] . ";";

						db_query($query, __FILE__, __LINE__);
					}
				}
			}
		}
		if ($user_deletet) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("User has been deleted") . ": " . $old_value['name'] . "  ", 0, "", "", "");
			print get_text("User has been deleted") . ": <i>" . $old_value['name'] . "</i><br>";
		} else {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("User data has been updated") . ": " . remove_nls($old_value['name'] . "  " . get_text("Edited") . "  " . $log_text_password . $log_text_level . $log_text_email . "  "), 0, "", "", "");
			print get_text("User data has been updated") . ": <i>" . $old_value['name'] . "</i><br>";
		}
	}
	exit;
case "user_edit":
	if ((isset ($_GET['id'])) && ($_GET['id'] != "")) {
		if (is_super() || is_admin()) {

			$query = "SELECT `id` " .
				"FROM `users` " .
				"WHERE `id` = " . $_GET['id'] . ";";

			if ($_GET['id'] < 0 OR check_for_rows($query) == 0) {
				do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, __LINE__ . " Invalid user id '" . $_SESSION['user_id'] . "'.", 0, "", "", "");
				print __LINE__ . " Invalid user id '" . $_SESSION['user_id'] . "'." . "<br>";
			} else {

				$query = "SELECT `u`.`id` AS `id`, " .
					"`u`.`name` AS `user_name`, " .
					"`u`.`password` AS `password`, " .
					"`u`.`level` AS `level`, " .
					"`u`.`email` AS `email`, " .
					"UNIX_TIMESTAMP(`u`.`updated`) AS `user_updated`, " .
					"`uu`.`name` AS `update_user` " .
					"FROM `users` `u` " .
					"LEFT JOIN `users` `uu` ON (`uu`.`id` = `u`.`user_id`) " .
					"WHERE `u`.`id` = " . $_GET['id'] . " " .
					"LIMIT 1;";

				$result	= db_query($query, __FILE__, __LINE__);
				$row = db_fetch_assoc($result);
				$higher_evel = false;
				if ((!is_super()) && ($row['level'] == $GLOBALS['LEVEL_SUPER'])) {
					$higher_evel = true;
				}
				$equal_level = false;
				if ((is_admin() && ($row['level'] == $GLOBALS['LEVEL_ADMINISTRATOR']))) {
					$equal_level = true;
				}
				$application_interface_str = "";
				if ($_GET['id'] == get_variable("_api_user_id")) {
					$application_interface_str = get_text("Application Interface");
				}
				$save_disabled = "";
				if ($higher_evel) {
					$save_disabled = " disabled";
				}
				$delete_disabled = "";
				if (
					$higher_evel ||
					$equal_level ||
					($_GET['id'] == get_variable("_api_user_id")) ||
					($row['id'] == $_SESSION['user_id'])
				) {
					$delete_disabled = " disabled";
				}
				$password_disabled = "";
				if ($higher_evel) {
					$password_disabled = " disabled";
				}
				$super_disabled = "";
				if (($_GET['id'] == $_SESSION['user_id']) ||
					($_GET['id'] == get_variable("_api_user_id")) ||
					(!(is_super())) ||
					(($_SESSION['level'] == $GLOBALS['LEVEL_ADMINISTRATOR']) && ($row['level'] == $GLOBALS['LEVEL_SUPER']))
				) {
					$super_disabled = " disabled";
				}
				$admin_disabled = "";
				if (($_GET['id'] == $_SESSION['user_id']) ||
					($_GET['id'] == get_variable("_api_user_id")) ||
					(!(is_super())) ||
					(($_SESSION['level'] == $GLOBALS['LEVEL_ADMINISTRATOR']) && ($row['level'] == $GLOBALS['LEVEL_SUPER']))
				) {
					$admin_disabled = " disabled";
				}
				$operator_disabled = "";
				if (($_GET['id'] == $_SESSION['user_id']) ||
					($_GET['id'] == get_variable("_api_user_id")) ||
					(($_SESSION['level'] == $GLOBALS['LEVEL_ADMINISTRATOR']) && ($row['level'] == $GLOBALS['LEVEL_SUPER']))
				) {
					$operator_disabled = " disabled";
				}
				$info_disabled = "";
				if (($_GET['id'] == $_SESSION['user_id']) ||
					($_GET['id'] == get_variable("_api_user_id")) ||
					(($_SESSION['level'] == $GLOBALS['LEVEL_ADMINISTRATOR']) && ($row['level'] == $GLOBALS['LEVEL_SUPER']))
				) {
					$info_disabled = " disabled";
				}
				$super_checked = "";
				if (intval($row['level']) == intval($GLOBALS['LEVEL_SUPER'])) {
					$super_checked = " checked";
				}
				$admin_checked = "";
				if (intval($row['level']) == intval($GLOBALS['LEVEL_ADMINISTRATOR'])) {
					$admin_checked = " checked";
				}
				$operator_checked = "";
				if (intval($row['level']) == intval($GLOBALS['LEVEL_OPERATOR'])) {
					$operator_checked = " checked";
				}
				$info_checked = "";
				if (intval($row['level']) == intval($GLOBALS['LEVEL_GUEST'])) {
					$info_checked =  " checked";
				}
				$email_disabled = "";
				if (
					$higher_evel ||
					($_GET['id'] == get_variable("_api_user_id"))
				) {
					$email_disabled = " disabled";
				}
	?>
		<div class="container-fluid" id="main_container">
			<form id="frm_user" name="frm_user">
				<input type="hidden" id="frm_id" name="frm_id" value="<?php print $_GET['id'];?>">
				<input type="hidden" id="frm_remove" name="frm_remove" value="">
				<input type="hidden" id="function" name="function" value="user_update">
				<input type="hidden" id="frm_hash" name="frm_hash" value="<?php print $row['password'];?>">
				<input type="hidden" id="frm_func" name="frm_func" value="e">
				<input type="hidden" id="frm_group[]" name="frm_group[]" value="1">
				<div class="row infostring">
					<div<?php print get_table_id_title_str("user", $_GET['id']);?> class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Edit User Data") . ": " . $row['user_name'] . get_table_id($_GET['id']) . " - " . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>	
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="document.frm_user.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="validate_user(document.frm_user);" <?php print $save_disabled;?>><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 20px;">
								<div<?php print get_help_text_str("_user_remove");?> class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="$('#frm_remove').val('true'); validate_user(document.frm_user);"<?php print $delete_disabled;?>><?php print get_text("Delete");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
						<input maxlength="20" size="50" type="hidden" value="<?php print $row['user_name'];?>" name="frm_user">
							<table class='table table-striped table-condensed' style="text-align: left;">
								<tr<?php print get_help_text_str("_user_name");?>>
									<th style="width: 20%;"><?php print get_text("User ID");?>: </th>
									<td style="width: 40%;"><input type="text" id="frm_user" name="frm_user" class="form-control" value="<?php print $row['user_name'];?>" disabled></td>
									<td style="width: 40%;"></td>
								</tr>
								<tr>
									<th<?php print get_help_text_str("_user_password");?>><?php print get_text("New Password");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
									<td<?php print get_help_text_str("_user_password");?>><input type="password" id="frm_passwd" name="frm_passwd" class="form-control mandatory" value="!!!!!!!!" <?php print $password_disabled;?>></td>			
									<td style="padding-left: 15px;"><div class="pw_show glyphicon glyphicon-eye-open"<?php print get_help_text_str("show_hide_password");?>></div></td>
								</tr>
								<tr<?php print get_help_text_str("_user_password");?>>
									<th><?php print get_text("Confirm");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
									<td><input type="password" id="frm_passwd_confirm" name="frm_passwd_confirm" class="form-control mandatory" value="!!!!!!!!" <?php print $password_disabled;?>></td>
									<td></td>
								</tr>
								<tr style="height: 44px;">
									<th<?php print get_help_text_str("_user_level");?>><?php print get_text("Level");?>:</th>
									<td>
										<label class="radio-inline"<?php print get_help_text_str("level_0");?>>
											<input type="radio" id="frm_level_super" name="frm_level" value="<?php print $GLOBALS['LEVEL_SUPER'];?>"<?php print $super_checked . $super_disabled;?>>
											<?php print get_text("permission_super");?>
										</label>
										<label class="radio-inline"<?php print get_help_text_str("level_1");?>>
											<input type="radio" id="frm_level_admin" name="frm_level" value="<?php print $GLOBALS['LEVEL_ADMINISTRATOR'];?>"<?php print $admin_checked . $admin_disabled;?>>
											<?php print get_text("permission_admin");?>
										</label>
										<label class="radio-inline"<?php print get_help_text_str("level_2");?>>
											<input type="radio" id="frm_level_operator" name="frm_level" value="<?php print $GLOBALS['LEVEL_OPERATOR'];?>"<?php print $operator_checked . $operator_disabled;?>>
											<?php print get_text("permission_operator");?>
										</label>
										<label class="radio-inline"<?php print get_help_text_str("level_3");?>>
											<input type="radio" id="frm_level_guest" name="frm_level" value="<?php print $GLOBALS['LEVEL_GUEST'];?>"<?php print $info_checked . $info_disabled;?>>
											<?php print get_text("permission_guest");?>
										</label>
										<label class="radio-inline">
											<font color='red'>
												<b>
													<?php print $application_interface_str;?>
												</b>
											</font>
										</label>
									</td>
									<td></td>
								</tr>
								<tr<?php print get_help_text_str("_user_email");?>>
									<th><?php print get_text("Email");?>: </th>
									<td><input id="frm_email" name="frm_email" class="form-control" value="<?php print remove_nls($row['email']);?>"<?php print $email_disabled;?>></td>
									<td></td>
								</tr>
								<tr style="height: 44px;">
									<th>
										<div<?php print get_help_text_str("_asof");?>><?php print get_text("Edited");?>:</div>
									</th>
									<td>
										<div>
											<?php print date(get_variable("date_format"), $row['user_updated']) . " " . get_text("by") . " " . remove_nls($row['update_user']);?>
										</div>
									</td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</form>	
		</div>
	</body>
</html>
	<?php
			}
		}
	}
	break;
case "audio_update":
	if (is_super()) {
		$updated_rows = 0;
		$sound_names_array = get_sound_array();
		foreach ($sound_names_array as $value) {

			$query = "UPDATE `settings` " .
				"SET `value` = '" . $_POST["_" . $value] . "' " .
				"WHERE `name` = '_" . $value . "';";

			$result = db_query($query, __FILE__, __LINE__);
			$updated_rows = $updated_rows + db_affected_rows($result);

			$query = "UPDATE `settings` " .
				"SET `value` = '" . $_POST["_alter_" . $value] . "' " .
				"WHERE `name` = '_alter_" . $value . "';";

			$result = db_query($query, __FILE__, __LINE__);
			$updated_rows = $updated_rows + db_affected_rows($result);
		}
		if ($updated_rows != 0) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("Audio files updated") . ": " .  $updated_rows . "  ", 0, "", "", "");
			print get_text("Audio files updated") . ": " .  $updated_rows . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "audio":
	?>
		<div class="container-fluid" id="main_container">
			<form id="frm_audio_files" name="frm_audio_files" method="post" action="<?php print basename(__FILE__);?>">
				<input type="hidden" id="function" name="function" value="audio_update">
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Alarm audio files") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
	<?php
	if (is_super()) {
	?>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="document.frm_audio_files.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="send_configuration_form('frm_audio_files');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_audio");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
	<?php
	}
	?>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class='table table-striped table-condensed' style="text-align: left;">
								<tr class="form-group" style="height: 44px;">
									<th<?php print get_help_text_str("sound_code");?> colspan=4><?php print get_text("Code");?></th>
									<th<?php print get_help_text_str("sound_file");?>><?php print get_text("sound file");?></th>
									<td></td>
									<th<?php print get_help_text_str("sound_file_alternative");?>><?php print get_text("alternative format sound file(optional)");?></th>
								</tr>
	<?php
$sound_names_array = get_sound_array();
if (is_super()) {
	$disable_str = "";
} else {
	$disable_str = " style='background-color: #DFDFDF;' readonly";
}
foreach ($sound_names_array as $value) {
	print "<tr" . get_help_text_str($value) . ">";
	print "<th>" . get_text($value) . ":</th>";
	print "<td>&nbsp;</td><td><a onclick='send_test_audio(\"" . $value . "\");'>" . get_text("play it") . "</a></td>";//
	print "<td>&nbsp;</td><td><input class='form-control'" . $disable_str . " cols=40 name='_" . $value . "' value='" . get_variable("_" . $value) . "'></td>";
	print "<td>&nbsp;</td><td><input class='form-control'" . $disable_str . " cols=40 name='_alter_" . $value . "' value='" . get_variable("_alter_" . $value) . "'></td>";
 	print "\n";;
}
	?>
								</tr>
							</table>	
						</div>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	break;
case "settings_update":
	if (is_super()) {
		$updated_rows = 0;
		foreach ($_POST as $VarName => $VarValue) {

			$query = "UPDATE `settings` " .
				"SET `value` = " . quote_smart($VarValue) . " " .
				"WHERE `name` = '" . $VarName . "';";

			$result = db_query($query, __FILE__, __LINE__);
			$updated_rows = $updated_rows + db_affected_rows($result);
		}
		if ($updated_rows != 0) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("Settings saved (will take effect at next re-start)") . ": " .  $updated_rows . "  ", 0, "", "", "");
			print get_text("Settings saved (will take effect at next re-start)") . ": " .  $updated_rows . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "settings":
	if (is_super()) {
	?>
		<div class="container-fluid" id="main_container">
			<form id="settings_form" name="settings_form">
				<input type="hidden" id="function" name="function" value="settings_update">
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Edit Settings") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="document.settings_form.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="send_configuration_form('settings_form');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_settings");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr style="height: 44px;">
									<th><?php print get_text("Key");?></th>
									<th><?php print get_text("Value");?></th>
								</tr>
	<?php
			$result = db_query("SELECT * FROM `settings`;", __FILE__, __LINE__);
			while($row = stripslashes_deep(db_fetch_array($result))) {
				if (substr($row['name'], 0, 1) != "_" ) {
					print "<tr class='form-group'" . get_help_text_str($row['name']) . "><th>" . $row['name'] . ":</th>";
					print "<td><input class='form-control' maxlength='512' size='80' type='text' value='" . $row['value'] . "' name='" . $row['name'] . "'></td></tr>\n";
				}
			}
	?>	
							</table>
						</div>
					</div>
				</div>
			</form>		
		</div>
	</body>
</html>
	<?php
	}
	break;
case "incident_numbers_update":
	if (is_super()) {
		$updated_rows = 0;
		$frm_do_nature = 0;
		if (array_key_exists('frm_do_nature', ($_POST))) {
			$frm_do_nature = $_POST['frm_do_nature'];
		}
		$inc_num_array = array ();
		$inc_num_array[0] = trim($_POST['frm_style']);
		$inc_num_array[1] = trim($_POST['frm_label']);
		$inc_num_array[2] = $_POST['frm_sep'];
		$inc_num_array[3] = trim($_POST['frm_number']);
		$inc_num_array[4] = $frm_do_nature;
		$inc_num_array[5] = date("y");
		$the_val = base64_encode(serialize($inc_num_array));

		$query = "UPDATE `settings` " .
			"SET `value`='" . $the_val . "' " .
			"WHERE `name`='_inc_num'";

		$result = db_query($query, __FILE__, __LINE__);
		$updated_rows = $updated_rows + db_affected_rows($result);
		if ($updated_rows != 0) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("Incident number settings updateted") . ": " .  $updated_rows . "  ", 0, "", "", "");
			print get_text("Incident number settings updateted") . ": " .  $updated_rows . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "incident_numbers":
	if (is_super()) {
			$inc_num_array = unserialize(base64_decode(get_variable('_inc_num')));
			$do_nature = ((int)$inc_num_array[4] == 0);
			$style_checked = array ("", "", "", "", "", "", "", "");
			$style_checked[$inc_num_array[0]] = "checked";
	?>
		<script>

			function inc_num_by_counter_attributes_disable(value) {
				switch (value) {
				case 1:
					document.getElementsByName('frm_label')[0].readOnly = true;
					$("#frm_label_mandatory").css("display","none");
					document.getElementsByName('frm_sep')[0].readOnly = true;
					$("#frm_sep_mandatory").css("display","none");
					document.getElementsByName('frm_number')[0].readOnly = false;
					$("#frm_number_mandatory").css("display","inline");
					break;
				case 2:
					document.getElementsByName('frm_label')[0].readOnly = false;
					$("#frm_label_mandatory").css("display","inline");
					document.getElementsByName('frm_sep')[0].readOnly = false;
					$("#frm_sep_mandatory").css("display","inline");
					document.getElementsByName('frm_number')[0].readOnly = false;
					$("#frm_number_mandatory").css("display","inline");
					break;
				case 3:
					document.getElementsByName('frm_label')[0].readOnly = true;
					$("#frm_label_mandatory").css("display","none");
					document.getElementsByName('frm_sep')[0].readOnly = false;
					$("#frm_sep_mandatory").css("display","inline");
					document.getElementsByName('frm_number')[0].readOnly = false;
					$("#frm_number_mandatory").css("display","inline");
					break;
				case 4:
				case 5:
				case 6:
				case 7:
				default:
					document.getElementsByName('frm_label')[0].readOnly = true;
					$("#frm_label_mandatory").css("display","none");
					document.getElementsByName('frm_sep')[0].readOnly = true;
					$("#frm_sep_mandatory").css("display","none");
					document.getElementsByName('frm_number')[0].readOnly = true;
					$("#frm_number_mandatory").css("display","none");
				}
			}

			function validate_inc_num(theForm) {

				function get_radio_val(my_form) {
					for (var i = 0; i < my_form.elements.length; i++) {
						if ((my_form.elements[i].name=='frm_style')&&(my_form.elements[i].checked)) {
							return parseInt(my_form.elements[i].value);
						}
					}
					return null;
				}

				var error_message = "";
				switch (get_radio_val(theForm)) {
				case 0:
					theForm.frm_do_nature.value = 0;
					theForm.frm_label.value = "";
					theForm.frm_sep.value.value = "";
					theForm.frm_number.value = "";
					break;
				case 1:
					if (isNaN(theForm.frm_number.value.trim())) {
						error_message += "<?php print get_text("Next number must be numeric");?><br>";
					} else {
						if (!(theForm.frm_number.value.trim() > 0)) {
							error_message += "<?php print get_text("Next number must be 1 or greater");?><br>";
						}
					}
					theForm.frm_do_nature.value = 0;
					break;
				case 2:
					if ((theForm.frm_label.value.trim()) == "") {
						error_message += "<?php print get_text("Label required  with this option");?><br>";
					}
					if (isNaN(theForm.frm_number.value.trim())) {
						error_message += "<?php print get_text("Next number must be numeric");?><br>";
					} else {
						if (!(theForm.frm_number.value.trim() > 0)) {
							error_message += "<?php print get_text("Next number must be 1 or greater");?><br>";
						}
					}	
					theForm.frm_do_nature.value = 0;
					break;
				case 3:
					if (isNaN(theForm.frm_number.value.trim())) {
						error_message += "<?php print get_text("Next number must be numeric");?><br>";
					} else {
					if (!(theForm.frm_number.value.trim() > 0)) {
							error_message += "<?php print get_text("Next number must be 1 or greater");?><br>";
						}
					}
					theForm.frm_do_nature.value = 0;
					break;
				case 4:
					theForm.frm_do_nature.value = 0;
					theForm.frm_label.value = "";
					theForm.frm_sep.value.value = "";
					theForm.frm_number.value = "";
					break;
				case 5:
					theForm.frm_do_nature.value = 0;
					theForm.frm_label.value = "";
					theForm.frm_sep.value.value = "";
					theForm.frm_number.value = "";
					break;
				case 6:
					theForm.frm_do_nature.value = 0;
					theForm.frm_label.value = "";
					theForm.frm_sep.value.value = "";
					theForm.frm_number.value = ""; 
					break;
				case 7:
					theForm.frm_do_nature.value = 0;
					theForm.frm_label.value = "";
					theForm.frm_sep.value.value = "";
					theForm.frm_number.value = "";
					break;
				default:
					alert("ERROR @ " + "<?php print __LINE__;?>");
				}
				if (error_message != "") {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", error_message)
					return false;
				} else {
					send_configuration_form("inc_num_Form");
				}
			}

		</script>
		<div class="container-fluid" id="main_container">
			<form id="inc_num_Form" name="inc_num_Form">
				<input type="hidden" id="function" name="function" value="incident_numbers_update">
				<input type="hidden" id="do_db" name="do_db" value="true">
				<input type="hidden" id="frm_do_nature" name="frm_do_nature" value=<?php print $do_nature;?>>
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Incident Numbers") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="document.inc_num_Form.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="validate_inc_num(this.form);"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_incident_names");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class='table table-striped table-condensed' style="text-align: left;">
								<tr>
									<th colspan=2 style="height: 44px;"><?php print get_text("Incident name with counter");?>: </th>
									<td>
										<label class="radio-inline"<?php print get_help_text_str("_NO12345");?>>
											 <input type="radio" id="frm_style1" name="frm_style" value=1 <?php print $style_checked[1];?> onclick="inc_num_by_counter_attributes_disable(1);"><?php print get_text("NO12345");?>
										</label>
										<label class="radio-inline"<?php print get_help_text_str("_Label12345");?>>
											 <input type="radio" id="frm_style2" name="frm_style" value=2 <?php print $style_checked[2];?> onclick="inc_num_by_counter_attributes_disable(2);"><?php print get_text("Label12345");?>
										</label>
										<label class="radio-inline"<?php print get_help_text_str("_YR12345");?>>
											<input type="radio" id="frm_style3" name="frm_style" value=3 <?php print $style_checked[3];?> onclick="inc_num_by_counter_attributes_disable(3);"><?php print get_text("YR12345");?>
										</label>
									</td>
								</tr>
								<tr<?php print get_help_text_str("_Label12345");?>>
									<th></th>
									<th><?php print get_text("Incident name Label");?>: <span id="frm_label_mandatory" style="font-size: small; vertical-align: top; color: red; display: none;">*</span></th>
									<td><input type="text" id="frm_label" name="frm_label" class="form-control mandatory" size=16 maxlength=16 value="<?php print $inc_num_array[1];?>"></td>
								</tr>
								<tr<?php print get_help_text_str("_inc_name_sep");?>>
									<th></th>
									<th><?php print get_text("Incident name Separator");?>: <span id="frm_sep_mandatory" style="font-size: small; vertical-align: top; color: red; display: none;">*</span></th>
									<td><input type="text" id="frm_sep" name="frm_sep" class="form-control mandatory" size=4 maxlength=4 value="<?php print $inc_num_array[2];?>"></td>
								</tr>
								<tr<?php print get_help_text_str("_inc_next_num");?>>
									<th></th>
									<th><?php print get_text("Next number");?>: <span id="frm_number_mandatory" style="font-size: small; vertical-align: top; color: red; display: none;">*</span></th>
									<td><input type="text" id="frm_number" name="frm_number" class="form-control mandatory" size=8 maxlength=8 value="<?php print $inc_num_array[3];?>"></td>
								</tr>
								<tr>
									<td colspan=3 style="height: 44px;"></td>
								</tr>
								<tr>
									<th colspan=2 style="height: 44px;"><?php print get_text("Incident name with Database-ID");?>: </th>
									<td>
										<label class="radio-inline"<?php print get_help_text_str("_NO#12345");?>>
											<input type="radio" id="frm_style0" name="frm_style" value=0 <?php print $style_checked[0];?> onclick="inc_num_by_counter_attributes_disable(0);"><?php print get_text("NO#12345");?>
										</label>
										<label class="radio-inline"<?php print get_help_text_str("_Free_text/NO#12345");?>>
											 <input type="radio" id="frm_style4" name="frm_style" value=4 <?php print $style_checked[4];?> onclick="inc_num_by_counter_attributes_disable(4);"><?php print get_text("Free_text/NO#12345");?>
										</label>
										<label class="radio-inline"<?php print get_help_text_str("_NO#12345/Free_text");?>>
											 <input type="radio" id="frm_style5" name="frm_style" value=5 <?php print $style_checked[5];?> onclick="inc_num_by_counter_attributes_disable(5);"><?php print get_text("NO#12345/Free_text");?>
										</label>
									</td>
								</tr>
								<tr>
									<td style="height: 44px;" colspan=3></td>
								</tr>
								<tr>
									<th style="height: 44px;" colspan=2><?php print get_text("Incident name manual edit");?>: </th>
									<td>
										<label class="radio-inline"<?php print get_help_text_str("_Free_text(add)");?>>
											 <input type="radio" id="frm_style6" name="frm_style" value=6 <?php print $style_checked[6];?> onclick="inc_num_by_counter_attributes_disable(6);"><?php print get_text("Free_text(add)");?>
										</label>
										<label class="radio-inline"<?php print get_help_text_str("_Free_text(edit)");?>>
											 <input type="radio" id="frm_style7" name="frm_style" value=7 <?php print $style_checked[7];?> onclick="inc_num_by_counter_attributes_disable(7);"><?php print get_text("Free_text(edit)");?>
										</label>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</form>				
		</div>
	</body>
	<script>

		inc_num_by_counter_attributes_disable(<?php print $inc_num_array[0];?>);

	</script>
</html>
	<?php
	}
	break;
case "api_update":
	if (is_super()) {
		$updated_rows = 0;
		foreach ($_POST as $key => $value) {
			if ($key != "function") {

				$query = "UPDATE `settings` " .
					"SET `value` = '" . $value . "' " .
					"WHERE `name` = '" . $key . "';";

				$result = db_query($query, __FILE__, __LINE__);
				$updated_rows = $updated_rows + db_affected_rows($result);
			}
		}
		if ($updated_rows != 0) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("API settings updated") . ": " .  $updated_rows . "  ", 0, "", "", "");
			print get_text("API settings updated") . ": " .  $updated_rows . "<br>";
		}
	}
	exit;
case "api":
	if (is_super()) {
	?>
		<script>

		function change_textfields(tag_id_prefix, option) {
			switch (option) {
			case "0":
				$("#" + tag_id_prefix + "mess").hide();
				$("#" + tag_id_prefix + "rece").hide();
				break;
			case "1":
				$("#" + tag_id_prefix + "mess").hide();
				$("#" + tag_id_prefix + "rece").hide();
				break;
			case "2":
				$("#" + tag_id_prefix + "mess").hide();
				$("#" + tag_id_prefix + "rece").show();
				break;
			case "3":
				$("#" + tag_id_prefix + "mess").show();
				$("#" + tag_id_prefix + "rece").hide();
				break;
			case "4":
				$("#" + tag_id_prefix + "mess").hide();
				$("#" + tag_id_prefix + "rece").hide();
				break;
			case "5":
				$("#" + tag_id_prefix + "mess").show();
				$("#" + tag_id_prefix + "rece").hide();
				break;
			case "6":
				break;
			default:
			}
		}

		function submit_api_settings() {
			var error_message = "";
			if (
				($("#status_select_0").val() == $("#status_select_1").val()) ||
				($("#status_select_0").val() == $("#status_select_2").val()) ||
				($("#status_select_1").val() == $("#status_select_2").val())
			) {
				error_message = "<?php print get_text("Status-Values must be different.");?><br>";
			}
			if (error_message != "") {
				show_infobox("<?php print get_text("Please correct the following and re-submit");?>", error_message);
			} else {
				send_configuration_form("frm_api_config");
			}
		}

		</script>
		<div class="container-fluid" id="main_container">
			<form id="frm_api_config" name="frm_api_config">
				<input type="hidden" id="function" name="function" value="api_update">
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Application Interface") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="document.frm_api_config.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="submit_api_settings();"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_api_settings");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr class="form-group" style="height: 44px;">
									<th style="width: 15%;"<?php print get_help_text_str("_api_setting");?>><?php print get_text("_api_setting");?></th>
									<th style="width: 25%;"<?php print get_help_text_str("_api_encoding");?>><?php print get_text("_api_encoding");?></th>
									<th style="width: 15%;"<?php print get_help_text_str("_api_status");?>><?php print get_text("_api_status");?></th>
									<th style="width: 15%;"<?php print get_help_text_str("_api_action");?>><?php print get_text("_api_action");?></th>
									<th style="width: 30%;"<?php print get_help_text_str("_api_message_text");?>><?php print get_text("_api_message_text");?></th>
								</tr>
								<tr class="form-group"<?php print get_help_text_str("_api_hosts");?>>
									<th><?php print get_text("_api_hosts");?>:</th>
									<td>
										<input" type="text" id="_api_hosts" name="_api_hosts" class="form-control" size=70 maxlength=70 value="<?php print get_variable("_api_hosts");?>">
									</td>
									<td colspan=3></td>
								</tr>
								<tr class="form-group"<?php print get_help_text_str("_api_user_id");?>>
									<th><?php print get_text("_api_user_id");?>:</th>
									<td>
										<select class="form-control" name="_api_user_id">
	<?php

	$query = "SELECT `id`, " .
		"`name` AS `user_name` " .
		"FROM `users` " .
		"WHERE `level` = '2' " .
		"OR `id` = " . get_variable("_api_user_id") . " " .
		"ORDER BY `id` ASC;";

	$result = db_query($query, __FILE__, __LINE__);
	while ($row = stripslashes_deep(db_fetch_array($result))) {
		if (get_variable("_api_user_id") == $row['id']) {
			$unit_type_selectr_str = " selected";
		} else {
			$unit_type_selectr_str = "";
		}
		print "<option value=" . $row['id'] . $unit_type_selectr_str . ">" . remove_nls($row['user_name']) . "</option>";
	}
	?>
										</select>
									</td>
									<td colspan=3></td>
								</tr>
								<tr class="form-group"<?php print get_help_text_str("_api_destination_host");?>>
									<th><?php print get_text("_api_destination_host");?>:</th>
									<td>
										<input type="text" id="_api_destination_host" name="_api_destination_host" class="form-control" size=70 maxlength=70 value="<?php print get_variable("_api_destination_host");?>">
									</td>
									<td colspan=3></td>
								</tr>
								<tr class="form-group"<?php print get_help_text_str("_api_phone_host");?>>
									<th><?php print get_text("_api_phone_host");?>:</th>
									<td>
										<input type="text" id="_api_phone_host" name="_api_phone_host" class="form-control" size=70 maxlength=70 value="<?php print get_variable("_api_phone_host");?>">
									</td>
									<td colspan=3></td>
								</tr>
								<tr class="form-group"<?php print get_help_text_str("_api_destination_password");?>>
									<th><?php print get_text("_api_destination_password");?>:</th>
									<td>
										<input type="text" id="_api_destination_password" name="_api_destination_password" class="form-control" size=70 maxlength=70 value="<?php print get_variable("_api_destination_password");?>">
									</td>
									<td colspan=3></td>
								</tr>
								<tr class="form-group"<?php print get_help_text_str("_api_connection_test_configuration");?>>
									<th><?php print get_text("_api_connection_test_configuration");?>:</th>
									<td>
										<input type="text" id="_api_connection_test_configuration" name="_api_connection_test_configuration" class="form-control" size=70 maxlength=140 value="<?php print get_variable("_api_connection_test_configuration");?>">
									</td>
									<td colspan=3></td>
								</tr>
	<?php
	$api_array = array ();

	$query = "SELECT `name` " .
		"FROM `settings` " .
		"WHERE `name` LIKE '_api_%_encdg' " .
		"OR `name` LIKE '_api_%_capt' " .
		"OR `name` LIKE '_api_%_regexp' " .
		"OR `name` LIKE '_api_%_setng' " .
		"OR `name` LIKE '_api_email_%' " .
		"ORDER BY `id` ASC;";

	$result = db_query($query, __FILE__, __LINE__);
	$i = 0;
	while ($row = stripslashes_deep(db_fetch_array($result))) {
		$api_array[$i] = substr($row['name'], 1);
		$i++;
	}
	$i = 0;
	foreach ($api_array as $value) {
		print "<tr class='form-group'" . get_help_text_str("_" . $value) . ">";
		print "<th>" . call_progression_captions("_" . $value) . ":</th>";
		print "<td><input type='text' id='_" . $value . "' name='_" . $value . "' class='form-control' size=70 maxlength=512 value='" . get_variable("_" . $value) . "'></td>";
		if (get_variable("_" . substr($value, 0 , -5) . "stat") != "") {

			$query_status = "SELECT `status_name`, " .
				"`id`, " .
				"`dispatch`, " .
				"`bg_color`, " .
				"`text_color` " .
				"FROM `unit_status` " .
				"ORDER BY `id` ASC;";

			$result_status = db_query($query_status, __FILE__, __LINE__);
			if (db_affected_rows($result_status) > 0) {
				
				$query = "SELECT `bg_color`, " .
					"`text_color` " .
					"FROM `unit_status` " .
					"WHERE `id` = " . get_variable("_" . substr($value,0 , -5) . "stat");

				$result = db_query($query, __FILE__, __LINE__);
				$row = stripslashes_deep(db_fetch_assoc($result));
				print "<td><select id='status_select_" . $i . "' name='_" . substr($value, 0 , -5) . "stat' class='form-control' style=' background-color: " . $row['bg_color'] .
					"; color: " . $row['text_color'] . ";' onchange='this.style.backgroundColor=" .
					"this.options[this.selectedIndex].style.backgroundColor; this.style.color=this.options[this.selectedIndex].style.color;'>";
				while ($row_status = stripslashes_deep(db_fetch_array($result_status))) {
					$unit_type_selectr_str = "";
					if (get_variable("_" . substr($value, 0, -5) . "stat") == $row_status['id']) {
						$unit_type_selectr_str = " selected";
					}
					print "<option value=" . $row_status['id'] . $unit_type_selectr_str . " style=' background-color: " . $row_status['bg_color'] .
					"; color: " . $row_status['text_color'] . ";'>" . remove_nls($row_status['status_name']) . "</option>";
				}
			} else {
				print "<td><select class='form-control' name='_" . substr($value, 0 , -5) . "stat'  style='color: #FFFFFF; background-color: #000000;'>";
				print "<option value=0 selected>" . get_text("No data") . "</option>";
			}
			print "</select></td>";
			$i++;
		} else {
			print "<td>&nbsp;</td>";
		}
		if (get_variable("_" . substr($value, 0 , -5) . "repl") != "") {
			if (get_variable("_" . substr($value, 0, -5) . "repl") == 1) {
				$select_on_str = " selected";
				$select_off_str = "";
			} else {
				$select_on_str = "";
				$select_off_str = " selected";
			}
			$selected_0 = $selected_1 = $selected_1 = $selected_2 = $selected_2 = $selected_3 = $selected_4 = $selected_5 = $selected_6 = "";
			$show_hide_message = $show_hide_receipt = "none";
			switch (get_variable("_" . substr($value, 0, -5) . "repl")) {
			case 0:
				$selected_0 = " selected";
				break;
			case 1:
				$selected_1 = " selected";
				break;
			case 2:
				$selected_2 = " selected";
				$show_hide_receipt = "inline";
				break;
			case 3:
				$selected_3 = " selected";
				$show_hide_message = "inline";
				break;
			case 4:
				$selected_4 = " selected";
				break;
			case 5:
				$selected_5 = " selected";
				$show_hide_message = "inline";
				break;
			case 6:
				$selected_6 = " selected";
				break;
			default;
			}
			print "<td><select id='_" . substr($value, 0, -5) . "repl' name='_" . substr($value, 0, -5) . "repl'  class='form-control'" .
				"onchange='change_textfields(\"_" . substr($value, 0, -5) . "\", this.options[this.selectedIndex].value);'>";
			print "<option value=0" . $selected_0 . ">" . get_text("Off") . "</option>";
			print "<option value=1" . $selected_1 . ">" . get_text("Status") . "</option>";
			print "<option value=2" . $selected_2 . ">" . get_text("Receipt") . "</option>";
			print "<option value=3" . $selected_3 . ">" . get_text("Message") . "</option>";
			print "<option value=4" . $selected_4 . ">" . get_text("Private Call") . "</option>";
			print "<option value=5" . $selected_5 . ">" . get_text("Message and Private Call") . "</option>";
//			print "<option value=6" . $selected_4 . ">" . get_text("Stored Audio and Private Call") . "</option>";
			print "</select></td>";
		} else {
			print "<td>&nbsp;</td>";
		}
		print "<td>";
		if (get_variable("_" . substr($value, 0, -5) . "mess") != "") {
			print get_textblock_select_str("fixtext", "_" . substr($value, 0, -5) . "mess", "_" . substr($value, 0, -5) . 
				"mess", get_variable("_" . substr($value, 0, -5) . "mess"), $show_hide_message);
		}
		if (get_variable("_" . substr($value, 0, -5) . "rece") != "") {
			print "<input class='form-control' id='_" . substr($value, 0, -5) . "rece' name='_" . substr($value,0 , -5) . "rece' " .
				"style='display: " . $show_hide_receipt . ";' value='" . get_variable("_" . substr($value, 0, -5) . "rece") . "'>";
		}
		print "</td></tr>\n";
	}
	?>
							</table>
						</div>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "facilities_status_reset_update":
	if (is_super() || is_admin()) {	
		$message_str = "";
		$log_str = NULL;
		if (isset ($_POST['facility_type'])) {
			$facility_types_where_str = "";
			foreach ($_POST['facility_type'] as $VarName => $VarValue) {
				$facility_types_where_str .= " OR `type` = " . $VarValue;
			}
			$facility_types_where_str = " WHERE" . substr($facility_types_where_str, 3);

			$query = "UPDATE `facilities` " .
				"SET `facility_status_id` = " . quote_smart($_POST['frm_status']) . ", " .
				"`updated` = NOW()" . $facility_types_where_str . ";";

			$result = db_query($query, __FILE__, __LINE__);
			if ($result) {
				$message_str .= get_text("Facility status values set to") . ": " . get_facilities_status_name($_POST['frm_status']) . "<br>";
				$log_str .= get_text("Facility status values set to") . ": " . get_facilities_status_name($_POST['frm_status']) . "  ";
			} else {
				$message_str .= get_text("Could not set facility status values to") . ": " . get_facilities_status_name($_POST['frm_status']) . "<br>";
				$log_str .= get_text("Could not set facility status values to") . ": " . get_facilities_status_name($_POST['frm_status']) . "  ";
			}
		} else {
			$message_str .= get_text("Nothing to do!") . "<br>";
			$log_str = NULL;
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
		}
		print get_text($message_str) . "<br>";
	}
	exit;
case "facilities_status_reset":
	if (is_super() || is_admin()) {
		$unit_type_selectr_str = "";

		$query = "SELECT `id`, " .
			"`name` " .
			"FROM `facility_types`;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			$unit_type_selectr_str .= "<select id='facility_type' name='facility_type[]' multiple='multiple'>";
			while ($row =  stripslashes_deep(db_fetch_array($result))) {
				$unit_type_selectr_str .= "<option value=" . $row['id'] . ">" . remove_nls($row['name']) . "</option>";
			}
			$unit_type_selectr_str .= "</select>";
			} else {
			$unit_type_selectr_str .= "<select class='form-control' style='background-color: #000000; color: #FFFFFF;'><option>" . get_text("No data") . "</option></select>";
		}

		$the_status_sel = "";
		
		$query = "SELECT `bg_color`, " .
			"`text_color` " .
			"FROM `facility_status` " .
			"ORDER BY `sort` ASC, `id` ASC " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			$row = stripslashes_deep(db_fetch_assoc($result));
			$bg_color = $row['bg_color'];
			$text_color = $row['text_color'];
		} else {
			$bg_color = "#000000";
			$text_color = "#FFFFFF";
		}
		$the_status_sel .= "<select id='frm_status' name='frm_status' class='form-control' style='max-width: 200px; background-color: " .
			$bg_color .  "; color: " . $text_color . ";' onchange='this.style.backgroundColor=" .
			"this.options[this.selectedIndex].style.backgroundColor; this.style.color=this.options[this.selectedIndex].style.color;'>";

		$query = "SELECT * " .
			"FROM `facility_status` " .
			"ORDER BY `sort` ASC, `id` ASC;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			while ($row = stripslashes_deep(db_fetch_assoc($result))) {
				$i = $row['id'];
				$the_status_sel .= "<option value=" . $i . " style='background-color:" . $row['bg_color'] . "; color:" .
					$row['text_color'] . ";'>" . $row['status_name'] . "</option>";
			}
		} else {
			$the_status_sel .= "<option value=0>" . get_text("No data") . "</option>";
		}
		$the_status_sel .= "</select>";
	?>
		<style>
			.table, td {
				overflow: visible !important;
			}
		</style>
		<script>

		$(document).ready(function() {
			$("#facility_type").multiselect ({
				buttonWidth: "100%",
				nonSelectedText: "<?php print html_entity_decode(get_text("None selected"));?>",
				nSelectedText: "<?php print html_entity_decode(get_text("selected"));?>",
				allSelectedText: "<?php print html_entity_decode(get_text("All selected"));?>",
				numberDisplayed: 0,
				includeSelectAllOption: true,
				selectAllText: "<?php print html_entity_decode(get_text("Select all"));?>"
			});
		});

		</script>
		<div id="main_container" class="container-fluid">
			<form id="frm_def_status" name="frm_def_status">
				<input type="hidden" id="function" name="function" value="facilities_status_reset_update">
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Set facilities to a common status") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="send_configuration_form('frm_def_status');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_facilities_common_status");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
								<tr style="height: 44px;">
									<th style="width: 20%;"><?php print get_text("Facility type");?></th>
									<th style="width: 20%;"><?php print get_text("Status");?></th>
									<td></td>
									</tr>
								<tr>
									<td style="text-align: left;">
										<?php print $unit_type_selectr_str;?>
									</td>
									<td style="text-align: left;"><?php print $the_status_sel;?></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "facility_types_update":
	if (is_super()) {
		$message_str = "";
		$log_str = NULL;
		if (isset ($_POST['name_new']) && ($_POST['name_new'] != "")) {	
			$result = insert_into_facility_types($_POST['name_new'], $_POST['description_new'], "#" . $_POST['bg_color_new'], 
				"#" . $_POST['text_color_new'],	$_SESSION['user_id'], $datetime_now);
			if (db_affected_rows($result) > 0) {
				$message_str .= get_text("Dataset fac_types added") . ": " . db_affected_rows($result) . "<br>";
				$log_str .= get_text("Dataset fac_types added") . ": " . db_affected_rows($result) . "  ";
			}
		}
		if (isset ($_POST['facility_types_id'][0])) {
			$updated_rows = 0;
			$deleted_rows = 0;
			foreach ($_POST['facility_types_id'] as $VarName => $VarValue) {
				if (isset ($_POST['delete_' . $VarValue]) && ($_POST['delete_' . $VarValue]) == "on") {

					$query = "DELETE FROM `facility_types` " .
						"WHERE `id`= " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
					if (db_affected_rows($result) > 0) {
						$deleted_rows++;
					}
				} else {

					$query = "UPDATE `facility_types` SET " .
						"`description` = ". quote_smart($_POST['description'][$VarName]) . ", " .
						"`bg_color` = ". quote_smart("#" . $_POST['bg_color'][$VarName]) . ", " .
						"`text_color` = ". quote_smart("#" . $_POST['text_color'][$VarName]) . " " .
						"WHERE `id`= " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
					if (db_affected_rows($result) > 0) {
						$updated_rows++;

						$query = "UPDATE `facility_types` SET " .
							"`updated` = " . quote_smart($datetime_now) . ", " .
							"`client_address` = ". quote_smart($_SERVER['REMOTE_ADDR']) . ", " .
							"`user_id`= ". $_SESSION['user_id'] . " " .
							"WHERE `id`= " . $VarValue . ";";

						$result = db_query($query, __FILE__, __LINE__);
					}
				}
			}
			if ($updated_rows != 0) {
				$message_str .= get_text("Dataset fac_types updated") . ": " . $updated_rows . "<br>";
				$log_str .= get_text("Dataset fac_types updated") . ": " . $updated_rows . "  ";
			}
			if ($deleted_rows != 0) {
				$message_str .= get_text("Dataset fac_types deleted") . ": " . $deleted_rows . "<br>";
				$log_str .= get_text("Dataset fac_types deleted") . ": " . $deleted_rows . "  ";
			}
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
			print get_text($message_str) . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "facility_types":
	if (is_super()) {
	?>
		<div id="main_container" class="container-fluid">
			<form id="facility_types" name="facility_types">
			<input type="hidden" id="function" name="function" value="facility_types_update">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Facility types configuration") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="document.facility_types.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="send_configuration_form('facility_types');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_facilities_category");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
								<tr style="height: 44px;">
									<th style="width: 30%;"><?php print get_text("Type");?></th>
									<th style="width: 44%;"><?php print get_text("Description");?></th>
									<th style="width: 11%;"><?php print get_text("Background Color");?></th>
									<th style="width: 10%;"><?php print get_text("Text Color");?></th>
									<th style="width: 5%;"></th>
								</tr>
								<tr class="form-group">
									<td><input type="text" id="name_new" name="name_new" class="form-control" placeholder="<?php print get_text("New entry");?>"></input></td>
									<td><input type="text" id="description_new" name="description_new" class="form-control"></input></td>
									<td><input type="text" id="back" name="bg_color_new" class="form-control color" value="FFFFFF"></input></td>
									<td><input type="text" id="text" name="text_color_new" class="form-control color" value="000000"></input></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;"></div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
								<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
									<tr style="height: 44px;">
										<th style="width: 30%;"><?php print get_text("Type");?></th>
										<th style="width: 44%;"><?php print get_text("Description");?></th>
										<th style="width: 11%;"><?php print get_text("Background Color");?></th>
										<th style="width: 10%;"><?php print get_text("Text Color");?></th>
										<th style="width: 5%; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></th>
									</tr>
	<?php

		$query = "SELECT * " .
			"FROM `facility_types`;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			while ($row =  stripslashes_deep(db_fetch_array($result))) {

				$query = "SELECT count(`al`.`resource_id`) AS `quantity` " .
					"FROM `facilities` `f` " .
					"LEFT JOIN `allocates` `al` ON `al`.`type` = " . $GLOBALS['TYPE_FACILITY'] . " AND `al`.`resource_id` = `f`.`id` " .
					"WHERE `f`.`type` = " . $row['id'] . ";";

				$row_type_id = db_fetch_array(db_query($query, __FILE__, __LINE__));
				$delete_disabled_str = "";
				if ($row_type_id["quantity"] > 0) {
					$delete_disabled_str = " disabled";
				}
	?>
									<tr class="form-group">
										<td><input type="text" id="namel[]" name="namel[]" class="form-control" value="<?php print $row['name'];?>" disabled></input></td>
										<td><input type="text" id="description[]" name="description[]" class="form-control" value="<?php print $row['description'];?>"></input></td>
										<td><input type="text" id="bg_color[]" name="bg_color[]" class="form-control color" value="<?php print substr(color_name_to_hex($row['bg_color']), -6);?>"></input></td>
										<td><input type="text" id="text_color[]" name="text_color[]" class="form-control color" value="<?php print substr(color_name_to_hex($row['text_color']), -6);?>"></input></td>
										<td style="text-align: center;" <?php if ($delete_disabled_str != "") print get_help_text_str("not_deletable");?>>
											<input type="checkbox" id="delete_<?php print $row['id'];?>" name="delete_<?php print $row['id'];?>"<?php print $delete_disabled_str;?>>
											<input type="hidden" id="facility_types_id[]" name="facility_types_id[]" value="<?php print $row['id'];?>">
										</td>
									</tr>
	<?php
			}
		} else {
	?>
									<tr class="form-group" style="height: 44px;">
										<th style="text-align: center;" colspan=5><?php print get_text("No data");?></th>
									</tr>
	<?php
		}
	?>
								</table>
							</div>
						</div>
						<div class="col-md-1"></div>
					</div>	
				</div>
			</form>
		</body>
	</html>
	<?php
	}
	break;
case "facility_status_update":
	if (is_super()) {
		$message_str = "";
		$log_str = NULL;
		if (isset ($_POST['status_val_new']) && ($_POST['status_val_new'] != "")) {
			$display_new = 0;
			foreach ($_POST['display_new'] as $VarName=>$VarValue) {
				$display_new = $display_new | $VarValue;
			}
			$result = insert_into_facility_status($_POST['status_val_new'], $_POST['description_new'], 
				intval($_POST['sort_new']), $display_new, "#" . $_POST['bg_color_new'], 
				"#" . $_POST['text_color_new'], $_SESSION['user_id'], $datetime_now);
			if (db_affected_rows($result) > 0) {
				$message_str .= get_text("Dataset fac_status added") . ": " . db_affected_rows($result) . "<br>";
				$log_str .= get_text("Dataset fac_status added") . ": " . db_affected_rows($result) . "  ";
			}
		}
		$updated_rows = 0;
		$deleted_rows = 0;
		foreach ($_POST['fac_status_id'] as $VarName => $VarValue) {
			if (isset ($_POST['delete_' . $VarValue]) && ($_POST['delete_' . $VarValue]) == "on") {

				$query = "DELETE FROM `facility_status` " .
					"WHERE `id` = " . $VarValue . ";";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$deleted_rows++;
				}
			} else {
				$display = 0;
				if (isset ($_POST['display_' . $VarValue])) {
					foreach ($_POST['display_' . $VarValue] as $VarName2 => $VarValue2) {
						$display = $display | $VarValue2;
					}
				}

				$query = "UPDATE `facility_status` SET " .
					"`description` = ". quote_smart($_POST['description'][$VarName]) . ", " .
					"`display` = ". $display . ", " .
					"`sort` = ". quote_smart($_POST['sort'][$VarName]) . ", " .
					"`bg_color` = ". quote_smart("#" . $_POST['bg_color'][$VarName]) . ", " .
					"`text_color` = ". quote_smart("#" . $_POST['text_color'][$VarName]) . " " .
					"WHERE `id` = " . $VarValue . ";";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$updated_rows++;

					$query = "UPDATE `facility_status` SET " .
						"`updated` = " . quote_smart($datetime_now) . ", " .
						"`client_address`= ". quote_smart($_SERVER['REMOTE_ADDR']) . ", " .
						"`user_id` = ". $_SESSION['user_id'] . " " .
						"WHERE `id` = " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
				}
			}
		}
		if ($updated_rows != 0) {
			$message_str .= get_text("Dataset fac_status updated") . ": " . $updated_rows . "<br>";
			$log_str .= get_text("Dataset fac_status updated") . ": " . $updated_rows . "  ";
		}
		if ($deleted_rows != 0) {
			$message_str .= get_text("Dataset fac_status deleted") . ": " . $deleted_rows . "<br>";
			$log_str .= get_text("Dataset fac_status deleted") . ": " . $deleted_rows . "  ";
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
			print get_text($message_str) . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "facility_status":
	if (is_super()) {
	?>
		<style>
			.table, td {
			  overflow: visible !important;
			}
		</style>
		<script>

			$(document).ready(function() {
				$("#display_new").multiselect ({
					buttonWidth: "100%",
					nonSelectedText: "<?php print html_entity_decode(get_text("None selected"));?>",
					nSelectedText: "<?php print html_entity_decode(get_text("selected"));?>",
					allSelectedText: "<?php print html_entity_decode(get_text("All selected"));?>",
					numberDisplayed: 0
				});
			});

		</script>
		<div id="main_container" class="container-fluid">
			<form id="facility_status" name="facility_status">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Facility status configuration") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<input type="hidden" id="function" name="function" value="facility_status_update">
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="document.facility_status.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="send_configuration_form('facility_status');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_facilities_status_value");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">	
								<tr style="height: 44px;">
									<th style="width: 20%;"><?php print get_text("Status");?></th>
									<th style="width: 30%;"><?php print get_text("Description");?></th>
									<th style="width: 10%;"><?php print get_text("Display");?></th>
									<th style="width: 10%;"><?php print get_text("Sort");?></th>
									<th style="width: 10%;"><?php print get_text("Background Color");?></th>
									<th style="width: 10%;"><?php print get_text("Text Color");?></th>
									<th style="width: 5%;"></th>
								</tr>
								<tr class="form-group">
									<td><input type="text" id="status_val_new" name="status_val_new" class="form-control" placeholder="<?php print get_text("New entry");?>"></input></td>
									<td><input type="text" id="description_new" name="description_new" class="form-control"></input></td>
									<td>
										<select id="display_new" name="display_new[]" multiple="multiple">
											<option value=1><?php print get_text("Incident location");?></option>
											<option value=2><?php print get_text("Reported by");?></option>
											<option value=4><?php print get_text("On-Scene location");?></option>
											<option value=8><?php print get_text("Receiving location");?></option>
											<option value=16><?php print get_text("Action");?></option>
											<option value=32><?php print get_text("Log report");?></option>
										</select>
									</td>
									<td><input type="text" id="sort_new" name="sort_new" class="form-control"></input></td>
									<td><input type="text" id="back" name="bg_color_new" class="form-control color" value="FFFFFF"></input></td>
									<td><input type="text" id="text" name="text_color_new" class="form-control color" value="000000"></input></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;"></div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
								<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
									<tr style="height: 44px;">
										<th style="width: 20%;"><?php print get_text("Status");?></th>
										<th style="width: 30%;"><?php print get_text("Description");?></th>
										<th style="width: 10%;"><?php print get_text("Display");?></th>
										<th style="width: 10%;"><?php print get_text("Sort");?></th>
										<th style="width: 10%;"><?php print get_text("Background Color");?></th>
										<th style="width: 10%;"><?php print get_text("Text Color");?></th>
										<th style="width: 5%; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></th>
									</tr>
	<?php

		$query = "SELECT * " .
			"FROM `facility_status` " .
			"ORDER BY `sort` ASC;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			while ($row =  stripslashes_deep(db_fetch_array($result))) {

				$query = "SELECT count(`al`.`resource_id`) AS `quantity` " .
					"FROM `facilities` `f` " .
					"LEFT JOIN `allocates` `al` ON `al`.`type` = " . $GLOBALS['TYPE_FACILITY'] . " AND `al`.`resource_id` = `f`.`id` " .
					"WHERE `f`.`facility_status_id` = " . $row['id'] . ";";

				$row_facility_status = db_fetch_array(db_query($query, __FILE__, __LINE__));
				$delete_disabled_str = "";
				if ($row_facility_status["quantity"] > 0) {
					$delete_disabled_str = " disabled";
				}
				$selected1 = $selected2 = $selected4 = $selected8 = $selected16 = $selected32 = "";
				if ($row['display'] & 1) {
					$selected1 = " selected";
				}
				if ($row['display'] & 2) {
					$selected2 = " selected";
				}
				if ($row['display'] & 4) {
					$selected4 = " selected";
				}
				if ($row['display'] & 8) {
					$selected8 = " selected";
				}
				if ($row['display'] & 16) {
					$selected16 = " selected";
				}
				if ($row['display'] & 32) {
					$selected32 = " selected";
				}
	?>
								<tr class="form-group">
									<td><input type="text" id="status_val[]" name="status_val[]" class="form-control" value="<?php print $row['status_name'];?>" disabled></input></td>
									<td><input type="text" id="description[]" name="description[]" class="form-control" value="<?php print $row['description'];?>"></input></td>
									<td>
										<select id="display_<?php print $row['id'];?>" name="display_<?php print $row['id'];?>[]" multiple="multiple">
											<option value=1<?php print $selected1;?>><?php print get_text("Incident location");?></option>
											<option value=2<?php print $selected2;?>><?php print get_text("Reported by");?></option>
											<option value=4<?php print $selected4;?>><?php print get_text("On-Scene location");?></option>
											<option value=8<?php print $selected8;?>><?php print get_text("Receiving location");?></option>
											<option value=16<?php print $selected16;?>><?php print get_text("Action");?></option>
											<option value=32<?php print $selected32;?>><?php print get_text("Log report");?></option>
										</select>
									</td>
									<td><input type="text" id="sort[]" name="sort[]" class="form-control" value="<?php print $row['sort'];?>"></input></td>
									<td><input type="text" id="bg_color[]" name="bg_color[]" class="form-control color" value="<?php print substr(color_name_to_hex($row['bg_color']), -6);?>"></input></td>
									<td><input type="text" id="text_color[]" name="text_color[]" class="form-control color" value="<?php print substr(color_name_to_hex($row['text_color']), -6);?>"></input></td>
									<td style="text-align: center;" <?php if ($delete_disabled_str != "") print get_help_text_str("not_deletable");?>>
										<input type="checkbox" id="delete_<?php print $row['id'];?>" name="delete_<?php print $row['id'];?>"<?php print $delete_disabled_str;?>>
										<input type="hidden" id="fac_status_id[]" name="fac_status_id[]" value="<?php print $row['id'];?>">
									</td>
								</tr>
								<script>

									$(document).ready(function() {
										$("#display_<?php print $row['id'];?>").multiselect ({
											buttonWidth: "100%",
											nonSelectedText: "<?php print html_entity_decode(get_text("None selected"));?>",
											nSelectedText: "<?php print html_entity_decode(get_text("selected"));?>",
											allSelectedText: "<?php print html_entity_decode(get_text("All selected"));?>",
											numberDisplayed: 0
										});
									});

								</script>
	<?php
			}
		} else {
	?>
									<tr class="form-group" style="height: 44px;">
										<th colspan=6 style="text-align: center;"><?php print get_text("No data");?></th>
									</tr>
	<?php
		}
	?>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "unit_status_reset_update":
	if (is_super() || is_admin()) {
		$message_str = "";
		$log_str = NULL;
		if (isset ($_POST['unit_type'])) {
			$unit_types_where_str = "";
			foreach ($_POST['unit_type'] as $VarName => $VarValue) {
				$unit_types_where_str .= " OR `type` = " . $VarValue;
			}
			$unit_types_where_str = substr($unit_types_where_str, 4);
			$subscribed = array ();
			$unsubscribed = array ();

			$query = "SELECT `id` " .
				"FROM `units` " .
				"WHERE `units`.`id` NOT IN (SELECT `unit_id` FROM `assigns` WHERE `clear` IS NULL);";

			$result = db_query($query, __FILE__, __LINE__);
			while ($row = stripslashes_deep(db_fetch_array($result))) {
				$subscribed[$row['id']] = false;
				$unsubscribed[$row['id']] = true;

				$query_old_status = "SELECT `u_s`.`dispatch` " .
					"FROM `units` " .
					"LEFT JOIN `unit_status` `u_s` ON `units`.`unit_status_id` = `u_s`.`id` " .
					"WHERE `units`.`id` = " . $row['id'] . " " .
					"LIMIT 1;";

				$result_old_status = db_query($query_old_status, __FILE__, __LINE__);
				if (db_num_rows($result_old_status) > 0) {
					$row_old_status = stripslashes_deep(db_fetch_assoc($result_old_status));
					if ($row_old_status['dispatch'] < 3) {
						$subscribed[$row['id']] = true;
						$unsubscribed[$row['id']] = false;
					}
				}
			}

			$query = "UPDATE `units` " .
				"SET `unit_status_id` = " . quote_smart($_POST['frm_status']) . ", " .
				"`status_updated` = NOW() " .
				"WHERE (`units`.`id` NOT IN (SELECT `unit_id` FROM `assigns` WHERE `clear` IS NULL)) " .
				"AND (" . $unit_types_where_str . ");";

			$result = db_query($query, __FILE__, __LINE__);
			if ($result) {
				$message_str .= get_text("Units status values set to") . ": " . get_units_status_name($_POST['frm_status']) . "<br>";
				$log_str .= get_text("Units status values set to") . ": " . get_units_status_name($_POST['frm_status']) . "  ";
			} else {
				$message_str .= get_text("Could not set units status values to") . ": " . get_units_status_name($_POST['frm_status']) . "<br>";
				$log_str .= get_text("Could not set units status values to") . ": " . get_units_status_name($_POST['frm_status']) . "  ";
			}

			$query_un_status = "SELECT `status_name`, " .
				"`description`, " .
				"`dispatch` " .
				"FROM `unit_status` " .
				"WHERE `id` = " . $_POST['frm_status'] . ";";

			$result_un_status = db_query($query_un_status, __FILE__, __LINE__);
			$row_un_status = stripslashes_deep(db_fetch_assoc($result_un_status));

			$query = "SELECT `id` " .
				"FROM `units` " .
				"WHERE `units`.`id` NOT IN (SELECT `unit_id` " .
					"FROM `assigns` " .
					"WHERE `clear` IS NULL);";

			$result = db_query($query, __FILE__, __LINE__);
			while ($row = stripslashes_deep(db_fetch_array($result))) {
				$subscribe_value = "";
				$subscr_unsubscr_settings = explode(",", get_variable("_api_subscr_unsubscr_setng"));
				if (($row_un_status['dispatch'] < 3) && ($unsubscribed[$row['id']])) {
					$subscribe_value = trim($subscr_unsubscr_settings[0]);
				}
				if (($row_un_status['dispatch'] >= 3) && ($subscribed[$row['id']])) {
					$subscribe_value = trim($subscr_unsubscr_settings[1]);
				}
				if ($subscribe_value != "") {
					do_api_infomessage($row['id'], $subscribe_value, "");
				}
				if (($row_un_status['dispatch'] < 3) && ($row['id'] != 0)) {
					do_receipt_message($row['id']);
				}
			}
		} else {
			$message_str .= get_text("Nothing to do!") . "<br>";
			$log_str = NULL;
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
		}
		print get_text($message_str) . "<br>";
	}
	exit;
case "unit_status_reset":
	if (is_super() || is_admin()) {
		$unit_type_selectr_str = "";

		$query = "SELECT `id`, " .
			"`name` " .
			"FROM `unit_types`;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			$unit_type_selectr_str .= "<select id='unit_type' name='unit_type[]' multiple='multiple'>";
			while ($row =  stripslashes_deep(db_fetch_array($result))) {
				$unit_type_selectr_str .= "<option value=" . $row['id'] . ">" . remove_nls($row['name']) . "</option>";
			}
			$unit_type_selectr_str .= "</select>";
		} else {
			$unit_type_selectr_str .= "<select class='form-control' style='background-color: #000000; color: #FFFFFF;'><option>" . get_text("No data") . "</option></select>";
		}
		$the_status_sel = "";

		$query = "SELECT `bg_color`, " .
			"`text_color` " .
			"FROM `unit_status` " .
			"ORDER BY `sort` DESC " .
			"LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			$row = stripslashes_deep(db_fetch_assoc($result));
			$bg_color = $row['bg_color'];
			$text_color = $row['text_color'];
		} else {
			$bg_color = "#000000";
			$text_color = "#FFFFFF";
		}
		$the_status_sel .= "<select id='frm_status' name='frm_status' class='form-control' style='max-width: 200px; background-color: " .
				$bg_color . "; color: " . $text_color . ";' onchange='this.style.backgroundColor=" .
				"this.options[this.selectedIndex].style.backgroundColor; this.style.color=this.options[this.selectedIndex].style.color;'>";

		$query = "SELECT * " .
			"FROM `unit_status` " .
			"ORDER BY `sort` DESC;";

		$result = db_query($query);
		if (db_affected_rows($result) > 0) {
			while ($row = stripslashes_deep(db_fetch_assoc($result))) {
				$the_status_sel .= "<option value=" . $row['id'] . " style='background-color:" . $row['bg_color'] . "; color:" . $row['text_color'] . ";'>" . $row['status_name'] . "</option>";
			}
		} else {
			$the_status_sel .= "<option value=0>" . get_text("No data") . "</option>";
		}
		$the_status_sel .= "</select>";
	?>
		<style>
			.table, td {
				overflow: visible !important;
			}
		</style>
		<script>

		$(document).ready(function() {
			$("#unit_type").multiselect ({
				buttonWidth: "100%",
				nonSelectedText: "<?php print html_entity_decode(get_text("None selected"));?>",
				nSelectedText: "<?php print html_entity_decode(get_text("selected"));?>",
				allSelectedText: "<?php print html_entity_decode(get_text("All selected"));?>",
				numberDisplayed: 0,
				includeSelectAllOption: true,
				selectAllText: "<?php print html_entity_decode(get_text("Select all"));?>"
			});
		});

		</script>
		<div id="main_container" class="container-fluid">
			<form id="frm_def_status" name="frm_def_status">
				<input type="hidden" id="function" name="function" value="unit_status_reset_update">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Set units to a common status") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="send_configuration_form('frm_def_status');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_units_common_status");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
								<tr style="height: 44px;">
									<th style="width: 20%;"><?php print get_text("Unit type");?></th>
									<th style="width: 20%;"><?php print get_text("Status");?></th>
									<td></td>
								</tr>
								<tr>
									<td style="text-align: left;">
										<?php print $unit_type_selectr_str;?>
									</td>
									<td style="text-align: left;"><?php print $the_status_sel;?></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>	
	</body>
</html>
	<?php
	}
	break;
case "unit_types_update":
	if (is_super()) {
		$message_str = "";
		$log_str = NULL;
		if (isset ($_POST['name_new']) && ($_POST['name_new'] != "")) {
			$result = insert_into_unit_types($_POST['name_new'], $_POST['description_new'], "#" . $_POST['bg_color_new'], 
				"#" . $_POST['text_color_new'],	$_SESSION['user_id'], $datetime_now);
			if (db_affected_rows($result) > 0) {
				$message_str .= get_text("Dataset unit_types added") . ": " . db_affected_rows($result) . "<br>";
				$log_str .= get_text("Dataset unit_types added") . ": " . db_affected_rows($result) . "  ";
			}
		}
		if (isset ($_POST['unit_types_id'][0])) {
			$updated_rows = 0;
			$deleted_rows = 0;
			foreach ($_POST['unit_types_id'] as $VarName => $VarValue) {
				if (isset ($_POST['delete_' . $VarValue]) && ($_POST['delete_' . $VarValue]) == "on") {

					$query = "DELETE FROM `unit_types` " .
						"WHERE `id` = " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
					if (db_affected_rows($result) > 0) {
						$deleted_rows++;
					}
				} else {

					$query = "UPDATE `unit_types` SET " .
						"`description` = ". quote_smart($_POST['description'][$VarName]) . ", " .
						"`bg_color` = ". quote_smart("#" . $_POST['bg_color'][$VarName]) . ", " .
						"`text_color` = ". quote_smart("#" . $_POST['text_color'][$VarName]) . " " .
						"WHERE `id` = " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
					if (db_affected_rows($result) > 0) {
						$updated_rows++;

						$query = "UPDATE `unit_types` SET " .
							"`updated` = " . quote_smart($datetime_now) . ", " .
							"`client_address`= ". quote_smart($_SERVER['REMOTE_ADDR']) . ", " .
							"`user_id` = ". $_SESSION['user_id'] . " " .
							"WHERE `id` = " . $VarValue . ";";

						$result = db_query($query, __FILE__, __LINE__);
					}
				}
			}
			if ($updated_rows != 0) {
				$message_str .= get_text("Dataset unit_types updated") . ": " . $updated_rows . "<br>";
				$log_str .= get_text("Dataset unit_types updated") . ": " . $updated_rows . "  ";
			}
			if ($deleted_rows != 0) {
				$message_str .= get_text("Dataset unit_types deleted") . ": " . $deleted_rows . "<br>";
				$log_str .= get_text("Dataset unit_types deleted") . ": " . $deleted_rows . "  ";
			}
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
			print get_text($message_str) . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "unit_types":
	if (is_super()) {
	?>
		<div id="main_container" class="container-fluid">
			<form id="unit_types" name="unit_types">
				<input type="hidden" id="function" name="function" value="unit_types_update">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Unit types configuration") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="document.unit_types.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="send_configuration_form('unit_types');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_units_category");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">	
								<tr style="height: 44px;">
									<th style="width: 30%;"><?php print get_text("Type");?></th>
									<th style="width: 45%;"><?php print get_text("Description");?></th>
									<th style="width: 10%;"><?php print get_text("Background Color");?></th>
									<th style="width: 10%;"><?php print get_text("Text Color");?></th>
									<th style="width: 5%;"></th>
								</tr>
								<tr class="form-group">
									<td><input type="text" id="name_new" name="name_new" class="form-control" placeholder="<?php print get_text("New entry");?>"></input></td>
									<td><input type="text" id="description_new" name="description_new" class="form-control"></input></td>
									<td><input type="text" id="back" name="bg_color_new" class="form-control color" value="FFFFFF"></input></td>
									<td><input type="text" id="text" name="text_color_new" class="form-control color" value="000000"></input></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;"></div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
								<table class="table table-striped table-condensed" style="text-align: left;">
									<tr style="height: 44px;">
										<th style="width: 30%;"><?php print get_text("Type");?></th>
										<th style="width: 45%;"><?php print get_text("Description");?></th>
										<th style="width: 10%;"><?php print get_text("Background Color");?></th>
										<th style="width: 10%;"><?php print get_text("Text Color");?></th>
										<th style="width: 5%; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></th>
									</tr>
	<?php
		$query = "SELECT * " .
			"FROM `unit_types`;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			while ($row =  stripslashes_deep(db_fetch_array($result))) {

				$query = "SELECT count(`al`.`resource_id`) AS `quantity` " .
					"FROM `units` `u` " .
					"LEFT JOIN `allocates` `al` ON `al`.`type` = " . $GLOBALS['TYPE_UNIT'] . " AND `al`.`resource_id` = `u`.`id` " .
					"WHERE `u`.`type` = " . $row['id'] . ";";

				$row_units = db_fetch_array(db_query($query, __FILE__, __LINE__));
				$delete_disabled_str = "";
				if ($row_units["quantity"] > 0) {
					$delete_disabled_str = " disabled";
				}
	?>
									<tr class="form-group">
										<td><input type="text" id="namel[]" name="namel[]" class="form-control" value="<?php print $row['name'];?>" disabled></input></td>
										<td><input type="text" id="description[]" name="description[]" class="form-control" value="<?php print $row['description'];?>"></input></td>
										<td><input type="text" id="bg_color[]" name="bg_color[]" class="form-control color" value="<?php print substr(color_name_to_hex($row['bg_color']), -6);?>"></input></td>
										<td><input type="text" id="text_color[]" name="text_color[]" class="form-control color" value="<?php print substr(color_name_to_hex($row['text_color']), -6);?>"></input></td>
										<td style="text-align: center;" <?php if ($delete_disabled_str != "") print get_help_text_str("not_deletable");?>>
											<input type="checkbox" id="delete_<?php print $row['id'];?>" name="delete_<?php print $row['id'];?>"<?php print $delete_disabled_str;?>>
											<input type="hidden" id="unit_types_id[]" name="unit_types_id[]" value="<?php print $row['id'];?>">
										</td>
									</tr>
	<?php
			}
		} else {
	?>
									<tr class="form-group" style="height: 44px;">
										<th colspan=5 style="text-align: center;"><?php print get_text("No data");?></th>
									</tr>
	<?php
		}
	?>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "unit_status_update":
	if (is_super()) {
		$message_str = "";
		$log_str = NULL;
		if (isset ($_POST['status_val_new']) && ($_POST['status_val_new'] != "")) {
			$result = insert_into_unit_status($_POST['status_val_new'], $_POST['description_new'], 
				$_POST['dispatch_new'], $_POST['sort_new'], "#" . $_POST['bg_color_new'], 
				"#" . $_POST['text_color_new'], $_SESSION['user_id'], $datetime_now);
			if (db_affected_rows($result) > 0) {
				$message_str .= get_text("Dataset un_status added") . ": " . db_affected_rows($result) . "<br>";
				$log_str .= get_text("Dataset un_status added") . ": " . db_affected_rows($result) . "  ";
			}
		}
		$updated_rows = 0;
		$deleted_rows = 0;
		foreach ($_POST['un_status_id'] as $VarName => $VarValue) {
			if (dont_delete_unit_status($VarValue) == false) {
				if (isset ($_POST['delete_' . $VarValue]) && ($_POST['delete_' . $VarValue] == "on")) {

					$query = "DELETE FROM `unit_status` " .
						"WHERE `id` = " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
					if (db_affected_rows($result) > 0) {
						$deleted_rows++;
					}
				} else {

					$query = "UPDATE `unit_status` SET " .
						"`description` = ". quote_smart($_POST['description'][$VarName]) . ", " .
						"`dispatch` = ". $_POST['dispatch'][$VarName] . ", " .
						"`sort` = ". quote_smart($_POST['sort'][$VarName]) . ", " .
						"`bg_color` = ". quote_smart("#" . $_POST['bg_color'][$VarName]) . ", " .
						"`text_color` = ". quote_smart("#" . $_POST['text_color'][$VarName]) . " " .
						"WHERE `id` = " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
					if (db_affected_rows($result) > 0) {
						$updated_rows++;

						$query = "UPDATE `unit_status` SET " .
							"`updated` = " . quote_smart($datetime_now) . ", " .
							"`client_address` = ". quote_smart($_SERVER['REMOTE_ADDR']) . ", " .
							"`user_id` = ". $_SESSION['user_id'] . " " .
							"WHERE `id` = " . $VarValue . ";";

						$result = db_query($query, __FILE__, __LINE__);
					}
				}
			}
		}
		if ($updated_rows != 0) {
			$message_str .= get_text("Dataset un_status updated") . ": " . $updated_rows . "<br>";
			$log_str .= get_text("Dataset un_status updated") . ": " . $updated_rows . "  ";
		}
		if ($deleted_rows != 0) {
			$message_str .= get_text("Dataset un_status deleted") . ": " . $deleted_rows . "<br>";
			$log_str .= get_text("Dataset un_status deleted") . ": " . $deleted_rows . "  ";
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
			print get_text($message_str) . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "unit_status":
	if (is_super()) {
	?>
		<div id="main_container" class="container-fluid">
			<form id="unit_status" name="unit_status">
				<input type="hidden" id="function" name="function" value="unit_status_update">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Unit status configuration") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="document.unit_status.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="send_configuration_form('unit_status');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_units_status_value");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr style="height: 44px;">
									<th style="width: 20%;"><?php print get_text("Status");?></th>
									<th style="width: 30%;"><?php print get_text("Description");?></th>
									<th style="width: 15%;"><?php print get_text("Able to dispatch");?></th>
									<th style="width: 10%;"><?php print get_text("Sort");?></th>
									<th style="width: 10%;"><?php print get_text("Background Color");?></th>
									<th style="width: 10%;"><?php print get_text("Text Color");?></th>
									<th style="width: 5%;"></th>
								</tr>
								<tr class="form-group">
									<td><input type="text" id="status_val_new" name="status_val_new" class="form-control" placeholder="<?php print get_text("New entry");?>"></input></td>
									<td><input type="text" id="description_new" name="description_new" class="form-control"></input></td>
									<td>
										<select id="dispatch_new" name="dispatch_new" class="form-control">
											<option value=<?php print $GLOBALS['DISPATCH_YES'];?> selected><?php print get_text("Can dispatch");?></option>
											<option value=<?php print $GLOBALS['DISPATCH_ENFORCEABLE'];?>><?php print get_text("No, enforceable");?></option>
											<option value=<?php print $GLOBALS['DISPATCH_NOT_ENFORCEABLE'];?>><?php print get_text("Not enforceable");?></option>
<!-- 										<option value=<?php print $GLOBALS['DISPATCH_MONITOR'];?>><?php print get_text("Only monitor");?></option>		-->
											<option value=<?php print $GLOBALS['DISPATCH_NO_EVALUATION'];?>><?php print get_text("No evaluation");?></option>
										</select>
									</td>
									<td><input type="text" id="sort_new" name="sort_new" class="form-control"></input></td>
									<td><input type="text" id="back" name="bg_color_new" class="form-control color" value="FFFFFF"></input></td>
									<td><input type="text" id="text" name="text_color_new" class="form-control color" value="000000"></input></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;"></div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
								<table class="table table-striped table-condensed" style="text-align: left;">
									<tr style="height: 44px;">
										<th style="width: 20%;"><?php print get_text("Status");?></th>
										<th style="width: 30%;"><?php print get_text("Description");?></th>
										<th style="width: 15%;"><?php print get_text("Able to dispatch");?></th>
										<th style="width: 10%;"><?php print get_text("Sort");?></th>
										<th style="width: 10%;"><?php print get_text("Background Color");?></th>
										<th style="width: 10%;"><?php print get_text("Text Color");?></th>
										<th style="width: 5%; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></th>
									</tr>
	<?php
		$dispatch_select_str = array ("", "", "", "", "");

		$query = "SELECT * " .
			"FROM `unit_status` " .
			"ORDER BY `sort` ASC;";	

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			while ($row = stripslashes_deep(db_fetch_array($result))) {
				$dispatch_select_str = array ("", "", "", "", "");
				$dispatch_select_str[0] = $dispatch_select_str[1] = $dispatch_select_str[2] = $dispatch_select_str[3] = $dispatch_select_str[4] = "";
				$dispatch_select_str[$row['dispatch']] = " selected";
				$delete_disabled_str = "";
				$change_readonly_str = "";
				if (dont_delete_unit_status($row['id'])) {
					$delete_disabled_str = " disabled";
					$change_readonly_str = " readonly";
				}
	?>
									<tr class="form-group">
										<td><input type="text" id="status_val[]" name="status_val[]" class="form-control" value="<?php print $row['status_name'];?>" readonly></input></td>
										<td><input type="text" id="description[]" name="description[]" class="form-control" value="<?php print $row['description'];?>"<?php print $change_readonly_str;?>></input></td>
										<td>
											<select id="dispatch[]" name="dispatch[]" class="form-control"<?php print $change_readonly_str;?>>
												<option value=<?php print $GLOBALS['DISPATCH_YES'] . $dispatch_select_str[0];?>><?php print get_text("Can dispatch");?></option>
												<option value=<?php print $GLOBALS['DISPATCH_ENFORCEABLE'] . $dispatch_select_str[1];?>><?php print get_text("No, enforceable");?></option>
												<option value=<?php print $GLOBALS['DISPATCH_NOT_ENFORCEABLE'] . $dispatch_select_str[2];?>><?php print get_text("Not enforceable");?></option>
<!-- 											<option value=<?php print $GLOBALS['DISPATCH_MONITOR'] . $dispatch_select_str[3];?>><?php print get_text("Only monitor");?></option>		-->
												<option value=<?php print $GLOBALS['DISPATCH_NO_EVALUATION'] . $dispatch_select_str[4];?>><?php print get_text("No evaluation");?></option>
											</select>
										</td>
										<td><input type="text" id="sort[]" name="sort[]" class="form-control" value="<?php print $row['sort'];?>"<?php print $change_readonly_str;?>></input></td>
										<td><input type="text" id="bg_color[]" name="bg_color[]" class="form-control color" value="<?php print substr(color_name_to_hex($row['bg_color']), -6);?>"<?php print $change_readonly_str;?>></input></td>
										<td><input type="text" id="text_color[]" name="text_color[]" class="form-control color" value="<?php print substr(color_name_to_hex($row['text_color']), -6);?>"<?php print $change_readonly_str;?>></input></td>
										<td style="text-align: center;" <?php if ($delete_disabled_str != "") print get_help_text_str("not_deletable");?>>
											<input type="checkbox" id="delete_<?php print $row['id'];?>" name="delete_<?php print $row['id'];?>"<?php print $delete_disabled_str;?>>
											<input type="hidden" id="un_status_id[]" name="un_status_id[]" value="<?php print $row['id'];?>">
										</td>
									</tr>
	<?php
			}
		} else {
	?>
									<tr class="form-group" style="height: 44px;">
										<th colspan=7 style="text-align: center;"><?php print get_text("No data");?></th>
									</tr>
	<?php
		}
	?>
								</table>
							</div>
						</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "presentation_tab_update":
	$tab_id = 0;
	$type_id = 0;
	$message_str = "";
	$log_str = "";
	if (isset ($_POST['tab_id'])) {
		$tab_id = $_POST['tab_id'];
	}
	if ($tab_id != 0) {
		$custom_overview = get_custom_overview($tab_id);
		$type_id = $custom_overview[1]['type_id'];
		$item_list = get_item_list($type_id, false);
		if (is_super() || (is_admin() && ($custom_overview[0]['item_id_3'] == $GLOBALS['TAB_CONFIG_ADD_EDIT']))) {
			$new_overview = array ();
			foreach ($_POST['field'] as $VarValue) {
				if ($_POST['column'][$VarValue] == 0) {
					$new_overview[$_POST['row'][$VarValue]]['row'] = $_POST['row'][$VarValue];
					$new_overview[$_POST['row'][$VarValue]]['type_id'] = $custom_overview[$_POST['row'][$VarValue]]['type_id'];
					$new_overview[$_POST['row'][$VarValue]]["user_id"] = $_SESSION['user_id'];
					$new_overview[$_POST['row'][$VarValue]]["client_address"] = $_SERVER['REMOTE_ADDR'];
					$new_overview[$_POST['row'][$VarValue]]["updated"] = $datetime_now;
				}
				$new_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]] = NULL;
				$new_overview[$_POST['row'][$VarValue]]["label_" . $_POST['column'][$VarValue]] = "";
				switch ($_POST['switch'][$VarValue]) {
				case "LIST":
					if ($_POST['item_id'][$VarValue] != 0) {
						$new_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]] = $_POST['item_id'][$VarValue];
						$new_overview[$_POST['row'][$VarValue]]["label_" . $_POST['column'][$VarValue]] = "";
					}
					break;
				case "LABEL":
					if ($_POST['label'][$VarValue] != "") {
						$new_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]] = 0;
						$new_overview[$_POST['row'][$VarValue]]["label_" . $_POST['column'][$VarValue]] = $_POST['label'][$VarValue];
					}
					break;
				default:
				}
				$old_value_log_str = "";
				$new_value_log_str = "";
				if (
					($new_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]] != 
						$custom_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]]) || 
					($new_overview[$_POST['row'][$VarValue]]["label_" . $_POST['column'][$VarValue]] != 
						$custom_overview[$_POST['row'][$VarValue]]["label_" . $_POST['column'][$VarValue]])
					) {
					if ($custom_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]] == 0) {
						$old_value_log_str = $custom_overview[$_POST['row'][$VarValue]]["label_" . $_POST['column'][$VarValue]];
					}
					if ($custom_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]] > 0) {
						foreach ($item_list as $item) {
							if ($item["option_value"] == $custom_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]]) {
								$old_value_log_str = $item["option_text"];
							}
						}
					}
					if ($new_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]] == 0) {
						$new_value_log_str = $new_overview[$_POST['row'][$VarValue]]["label_" . $_POST['column'][$VarValue]];
					}
					if ($new_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]] > 0) {
						foreach ($item_list as $item) {
							if ($item["option_value"] == $new_overview[$_POST['row'][$VarValue]]["item_id_" . $_POST['column'][$VarValue]]) {
								$new_value_log_str = $item["option_text"];
							}
						}
					}
					$message_str .= get_text("Row") . " " . $_POST['row'][$VarValue] . "  " . get_text("Column") . " " . $_POST['column'][$VarValue] . ": " . $old_value_log_str . " => " . $new_value_log_str . "<br>";
					$log_str .= get_text("Row") . " " . $_POST['row'][$VarValue] . "  " . get_text("Column") . " " . $_POST['column'][$VarValue] . ": " . $old_value_log_str . " => " . $new_value_log_str . ". ";
				}
			}
			if ($message_str != "") {
				switch ($type_id) {
				case $GLOBALS['TYPE_FACILITY']:
					$message_str = get_text("Edit custom facilities representation") . "<br>" . $top_notice_str;
					$log_str = get_text("Edit custom facilities representation") . "  " . $top_notice_log_str;
					break;
				case $GLOBALS['TYPE_UNIT']:
					$message_str = get_text("Edit custom units representation") . "<br>" . $top_notice_str;
					$log_str = get_text("Edit custom units representation") . "  " . $top_notice_log_str;
					break;
				default:
				}
				set_custom_overview($tab_id, $new_overview);
				do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
				print get_text($message_str) . "<br>&function=presentation_list&type_id=". $type_id;
			} else {
				print get_text("Nothing to do!") . "<br>&function=presentation_list&type_id=" . $type_id;
			}
		}
	}
	exit;
case "presentation_tab":
	$tab_id = 0;
	$type_id = 0;
	if (isset ($_GET['tab_id'])) {
		$tab_id = $_GET['tab_id'];
	}
	if ($tab_id != 0) {
		$custom_overview = get_custom_overview($tab_id);
		$type_id = $custom_overview[0]["type_id"];
		$item_list = array ();
		$option_0 = "";
		$no_elements = "";
		$focus_id = "";
		if (is_super() || ($custom_overview[0]["item_id_3"] == $GLOBALS['TAB_CONFIG_ADD_EDIT'])) {
			$helptext = "";
			$page_caption = "";
			switch ($type_id) {
			case $GLOBALS['TYPE_FACILITY']:
				$page_caption = get_text("Edit custom facilities representation");
				$item_list = get_item_list($GLOBALS['TYPE_FACILITY'], true);
				$option_0 = get_text("Facility");
				$no_elements = get_text("No facilities available!");
				break;
			case $GLOBALS['TYPE_UNIT']:
				$page_caption = get_text("Edit custom units representation");
				$item_list = get_item_list($GLOBALS['TYPE_UNIT'], true);
				$option_0 = get_text("Unit");
				$no_elements = get_text("No units available!");
				break;
			default:
			}
			$field_id = 0;
	?>
	<script>
		function change_presentation_tab_field(symbol, row, column) {
			select = "EMPTY";
			switch ($("#switch_id_" + row + "_" + column).val()) {
			case "EMPTY":
				if (symbol == "left") {
					select = "LIST";
				}
				if (symbol == "right") {
					select = "LABEL";
				}
				break;
			case "LABEL":
				if (symbol == "left") {
					select = "LIST";
				}
				if (symbol == "right") {
					select = "EMPTY";
				}
				break;
			case "LIST":
				if (symbol == "left") {
					select = "LABEL";
				}
				if (symbol == "right") {
					select = "EMPTY";
				}
			default:
			}
			switch (select) {
			case "LABEL":
				$("#symbol_left_" + row + "_" + column).removeClass();
				$("#symbol_right_" + row + "_" + column).removeClass();
				$("#symbol_left_" + row + "_" + column).attr("class", "glyphicon glyphicon-list");
				$("#symbol_right_" + row + "_" + column).attr("class", "glyphicon glyphicon-trash");
				$("#input_id_" + row + "_" + column).show();
				$("#select_id_" + row + "_" + column).hide();
				$("#switch_id_" + row + "_" + column).val("LABEL");
				$("#input_id_" + row + "_" + column).focus();
				break;
			case "LIST":
				$("#symbol_left_" + row + "_" + column).removeClass();
				$("#symbol_right_" + row + "_" + column).removeClass();
				$("#symbol_left_" + row + "_" + column).attr("class", "glyphicon glyphicon-pencil");
				$("#symbol_right_" + row + "_" + column).attr("class", "glyphicon glyphicon-trash");
				$("#input_id_" + row + "_" + column).hide();
				$("#select_id_" + row + "_" + column).show();
				$("#switch_id_" + row + "_" + column).val("LIST");
				$("#select_id_" + row + "_" + column).focus();
				break;
			case "EMPTY":
			default:
				$("#symbol_left_" + row + "_" + column).removeClass();
				$("#symbol_right_" + row + "_" + column).removeClass();
				$("#symbol_left_" + row + "_" + column).attr("class", "glyphicon glyphicon-list");
				$("#symbol_right_" + row + "_" + column).attr("class", "glyphicon glyphicon-pencil");
				$("#input_id_" + row + "_" + column).hide();
				$("#select_id_" + row + "_" + column).hide();
				$("#switch_id_" + row + "_" + column).val("EMPTY");
			}
		}
	</script>
		<div class="container-fluid" id="main_container">
			<div class="row infostring">
				<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
					<?php print $page_caption . ": "  . remove_nls(substr($custom_overview[0]["label_0"], 0, 15)) . get_tab_id($tab_id) . " - " . get_variable("page_caption");?>
				</div>
			</div>
			<form id="presentation_tab" name="presentation_tab">
				<input type="hidden" name="function" value="presentation_tab_update">
				<input type="hidden" name="tab_id" value="<?php print $tab_id;?>">
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" tabindex=84 onclick="goto_window('configuration.php?function=presentation_list&type_id=<?php print $type_id;?>');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" tabindex=83 onClick="document.presentation_tab.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" tabindex=82 onClick="send_configuration_form('presentation_tab');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" tabindex=81 onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("custom_overview");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">	
	<?php 
				$tabindex = 1;
				for ($i = 1; $i <= 20; $i++) {
	?>
								<tr class="form-group" style="height: 45px;">
	<?php
					$with_2_str = " style=\"text-align: center;\"";
					$with_18_str = " style=\"text-align: center;\"";
					if ($i == 1) {
						$with_2_str = " style=\"width: 2%;text-align: center;\"";
						$with_18_str = " style=\"width: 18%; text-align: center;\"";
					}
					if (isset ($custom_overview[$i]["type_id"])) {
						for ($j = 0; $j <= 3; $j++) {
							$switch_symbol_str = "";
							$switch_value = "";
							$label_display_str = " style=\"display: none;\"";
							$select_display_str = "display: none;";
							switch ($custom_overview[$i]["item_id_" . $j]) {
							case "":
								$switch_symbol_str = "<span id=\"symbol_left_" . $i . "_" . $j. "\" class=\"glyphicon glyphicon-list\" aria-hidden=\"true\" style=\"margin: 4px;\" onClick=\"change_presentation_tab_field('left', " . $i . ", " . $j. ");\"></span><span id=\"symbol_right_" . $i . "_" . $j. "\" class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\" style=\"margin: 4px;\" onClick=\"change_presentation_tab_field('right', " . $i . ", " . $j. ");\"></span>";
								$switch_value = "EMPTY";
								break;
							case 0:
								$switch_symbol_str = "<span id=\"symbol_left_" . $i . "_" . $j. "\" class=\"glyphicon glyphicon-list\" aria-hidden=\"true\" style=\"margin: 4px;\" onClick=\"change_presentation_tab_field('left', " . $i . ", " . $j. ");\"></span><span id=\"symbol_right_" . $i . "_" . $j. "\" class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\" style=\"margin: 4px;\" onClick=\"change_presentation_tab_field('right', " . $i . ", " . $j. ");\"></span>";
								$switch_value = "LABEL";
								$label_display_str = " style=\"display: inline;\"";
								break;
							default:
								$switch_symbol_str = "<span id=\"symbol_left_" . $i . "_" . $j. "\" class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\" style=\"margin: 4px;\" onClick=\"change_presentation_tab_field('left', " . $i . ", " . $j. ");\"></span><span id=\"symbol_right_" . $i . "_" . $j. "\" class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\" style=\"margin: 4px;\" onClick=\"change_presentation_tab_field('right', " . $i . ", " . $j. ");\"></span>";
								$switch_value = "LIST";
								$select_display_str = "display: inline;";
							}
							if (($focus_id == "") && ($switch_value == "LABEL")) {
								$focus_id = "input_id_" . $i . "_" . $j;
							}
							if (($focus_id == "") && ($switch_value == "LIST")) {
								$focus_id = "select_id_" . $i . "_" . $j;
							}
	?>
									<td<?php print $with_2_str;?>>
										<input type="hidden" name="field[]" value=<?php print $field_id;?>>
										<input type="hidden" name="row[]" value=<?php print $i ;?>></input>
										<input type="hidden" name="column[]" value=<?php print $j ;?>></input>
										<input type="hidden" id="switch_id_<?php print $i . "_" . $j;?>" name="switch[]" value="<?php print $switch_value;?>"></input>
										<?php print $switch_symbol_str;?>
									</td>
									<td<?php print $with_18_str;?>>
										<input type="text" class="form-control" id="input_id_<?php print $i . "_" . $j;?>" name="label[]" value="<?php print $custom_overview[$i]["label_" . $j];?>"<?php print $label_display_str;?> tabindex=<?php print $tabindex;?>></input>
										<?php print get_select_str($item_list, "select_id_" . $i . "_" . $j, "item_id[]", "form-control", "", $select_display_str, "", $option_0, $custom_overview[$i]["item_id_" . $j], $no_elements, $tabindex);?>
									</td>
	<?php
							$tabindex++;
							$field_id++;
						}
	?>
								</tr>
	<?php
					}
				}
	?>
							</table>
							<script>
								$("#<?php print $focus_id;?>").focus();
							</script>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
		}
	}
	break;
case "presentation_list_update":
	$result_admin_can_add = false;
	$result_tab_new = false;
	$type_id = 0;
	$message_str = "";
	$log_str = "";
	if (isset ($_POST['type_id'])) {
		$type_id = $_POST['type_id'];
	}
	if (($type_id == $GLOBALS['TYPE_UNIT']) || ($type_id == $GLOBALS['TYPE_FACILITY'])) {
		$tab_list = get_tab_list($type_id);
		if ((isset ($_POST['admin_can_add'])) && (($_POST['admin_can_add'] == $GLOBALS['TAB_CONFIG_NO']) || ($_POST['admin_can_add'] == $GLOBALS['TAB_CONFIG_ADD_EDIT']))) {
			if ($_POST['admin_can_add'] != $tab_list[0]["admin_can_add"]) {
				$result_admin_can_add = set_admin_can_add_presentation($type_id, $_POST['admin_can_add'], $datetime_now);
			}
		}
		if ((isset ($_POST['tab_name_new'])) && (preg_match("/^[0-9a-zA-Z\.\-_ " . get_variable("_vowel_mutation") . "]{4,24}$/", $_POST['tab_name_new']))) {
			$visible_new = $GLOBALS['TAB_VISIBLE_NO'];
			if ((isset ($_POST['visible_new'])) && ($_POST['visible_new'] >= $GLOBALS['TAB_VISIBLE_NO']) && ($_POST['visible_new'] <= $GLOBALS['TAB_VISIBLE_YES'])) {
				$visible_new = $_POST['visible_new'];
			}
			$add_tickets_new = $GLOBALS['TAB_ADDITIONAL_TICKETS_NO'];
			if ((isset ($_POST['add_tickets_new'])) && ($_POST['add_tickets_new'] >= $GLOBALS['TAB_ADDITIONAL_TICKETS_NO']) && ($_POST['add_tickets_new'] <= $GLOBALS['TAB_ADDITIONAL_TICKETS_YES'])) {
				$add_tickets_new = $_POST['add_tickets_new'];
			}
			$sort_new = 0;
			switch ($type_id) {
			case $GLOBALS['TYPE_FACILITY']:
				$sort_new = 60;
				break;
			case $GLOBALS['TYPE_UNIT']:
				$sort_new = 50;
				break;
			default:
			}
			if ((isset ($_POST['sort_new'])) && (preg_match("/^[0-9]{1,3}/", $_POST['sort_new']))) {
				$sort_new = $_POST['sort_new'];
			}
			$admin_edit_new = $GLOBALS['TAB_CONFIG_NO'];
			if ((isset ($_POST['admin_edit_new'])) && ($_POST['admin_edit_new'] >= $GLOBALS['TAB_ADDITIONAL_TICKETS_NO']) && ($_POST['admin_edit_new'] <= $GLOBALS['TAB_ADDITIONAL_TICKETS_YES'])) {
				$admin_edit_new = $_POST['admin_edit_new'];
			}
			$result_tab_new = insert_presentation_tab($type_id, $_POST['tab_name_new'], $visible_new, $add_tickets_new, $sort_new, $admin_edit_new, $datetime_now, $tab_list[0]["admin_can_add"]);
			//additional parameters switch to new tab
		}
		$updated_tabs = 0;
		$changed_tab_names_str = "";
		$deleted_tabs = 0;
		$deleted_tab_names_str = "";
		if (isset ($_POST['tab_id'])) {
			foreach ($_POST['tab_id'] as $VarName => $VarValue) {
				if (isset ($_POST['delete_' . $VarValue]) && ($_POST['delete_' . $VarValue] == "on")) {
					if (is_super() || (is_admin() && $tab_list[$VarValue]["admin_can_config"] == $GLOBALS['TAB_CONFIG_ADD_EDIT'])) {
						$statement = $GLOBALS['STATEMENTS']['CONFIG_TAB_DELETE'];
						$statement->bindParam(':tab_id', $VarValue);
						if ($statement->execute() > 0) {
							$deleted_tabs++;
							$deleted_tab_names_str .= $tab_list[$VarValue]["tab_name"] . ", ";
						}
					}
				} else {
					$updated_values = 0;
					if ((is_super() || (is_admin() && $tab_list[$VarValue]["admin_can_config"] >= $GLOBALS['TAB_CONFIG_VISIBILITY'])) && ($VarValue > 4)) {
						if ((isset ($_POST['tab_name_' . $VarValue])) && (preg_match("/^[0-9a-zA-Z\.\-_ " . get_variable("_vowel_mutation") . "]{4,24}$/", $_POST['tab_name_' . $VarValue]))) {
							if ($_POST['tab_name_' . $VarValue] != $tab_list[$VarValue]["tab_name"]) {
								$tab_name = $_POST['tab_name_' . $VarValue];
								$statement = $GLOBALS['STATEMENTS']['CONFIG_TAB_UPDATE_NAME'];
								$statement->bindParam(':label_0', $tab_name);
								$statement->bindParam(':tab_id', $VarValue);
								$statement->execute();
								$updated_values++;
								$changed_tab_names_str .= $tab_list[$VarValue]["tab_name"] . " => " . $_POST['tab_name_' . $VarValue] . ". ";
							}
						}
					}
					$visible = $tab_list[$VarValue]["visible"];
					if (is_super() || ((is_admin() && $tab_list[$VarValue]["admin_can_config"] >= $GLOBALS['TAB_CONFIG_VISIBILITY']) && $VarValue > 4)) {
						if (($VarValue != 3) && ($VarValue != 4)) {
							if ((isset ($_POST['visible_' . $VarValue])) && ($_POST['visible_' . $VarValue] >= $GLOBALS['TAB_VISIBLE_NO']) && ($_POST['visible_' . $VarValue] <= $GLOBALS['TAB_VISIBLE_YES'])) {
								if ($_POST['visible_' . $VarValue] != $tab_list[$VarValue]["visible"]) {
									$visible = $_POST['visible_' . $VarValue];
									$statement = $GLOBALS['STATEMENTS']['CONFIG_TAB_UPDATE_VISIBLE'];
									$statement->bindParam(':item_id_0', $visible);
									$statement->bindParam(':tab_id', $VarValue);
									$statement->execute();
									$updated_values++;
								}
							}
						}
					}
					if (is_super() || (is_admin() && ($tab_list[$VarValue]["admin_can_config"] == $GLOBALS['TAB_CONFIG_ADD_EDIT']) && $VarValue > 4)) {
						if ((isset ($_POST['add_tickets_' . $VarValue])) && ($_POST['add_tickets_' . $VarValue] >= $GLOBALS['TAB_ADDITIONAL_TICKETS_NO']) && ($_POST['add_tickets_' . $VarValue] <= $GLOBALS['TAB_ADDITIONAL_TICKETS_YES'])) {
							if ($type_id == $GLOBALS['TYPE_UNIT']) {
								$add_tickets = get_additional_tickets_change($visible, $_POST['add_tickets_' . $VarValue]);
								if ($add_tickets != $tab_list[$VarValue]["add_tickets"]) {
									$statement = $GLOBALS['STATEMENTS']['CONFIG_TAB_UPDATE_ADDITIONAL_TICKETS'];
									$statement->bindParam(':item_id_1', $add_tickets);
									$statement->bindParam(':tab_id', $VarValue);
									$statement->execute();
									$updated_values++;
								}
							}
						}
						if ((isset ($_POST['sort_' . $VarValue])) && (preg_match("/^[0-9]{1,3}/", $_POST['sort_' . $VarValue]))) {
							if ($_POST['sort_' . $VarValue] != $tab_list[$VarValue]["sort"]) {
								$statement = $GLOBALS['STATEMENTS']['CONFIG_TAB_UPDATE_SORT'];
								$statement->bindParam(':item_id_2', $_POST['sort_' . $VarValue]);
								$statement->bindParam(':tab_id', $VarValue);
								$statement->execute();
								$updated_values++;
							}
						}
					}
					if (is_super() && $VarValue > 4) {
						if ((isset ($_POST['admin_can_config_' . $VarValue])) && ($_POST['admin_can_config_' . $VarValue] >= $GLOBALS['TAB_CONFIG_NO']) && ($_POST['admin_can_config_' . $VarValue] <= $GLOBALS['TAB_CONFIG_ADD_EDIT'])) {
							if ($_POST['admin_can_config_' . $VarValue] != $tab_list[$VarValue]["admin_can_config"]) {
								$statement = $GLOBALS['STATEMENTS']['CONFIG_TAB_UPDATE_ADMIN_CAN_CONFIG'];
								$statement->bindParam(':item_id_3', $_POST['admin_can_config_' . $VarValue]);
								$statement->bindParam(':tab_id', $VarValue);
								$statement->execute();
								$updated_values++;
							}
						}
					}
					if ($updated_values > 0) {
						$statement = $GLOBALS['STATEMENTS']['CONFIG_TAB_UPDATE_USER_CLIENT_DATETIME'];
						$statement->bindParam(':user_id', $_SESSION['user_id']);
						$statement->bindParam(':client_address', $_SERVER['REMOTE_ADDR']);
						$statement->bindParam(':updated', $datetime_now);
						$statement->bindParam(':tab_id', $VarValue);
						if ($statement->execute() >0) {
							$updated_tabs++;
						}
					}
				}
			}
		}
		$log_admin_can_add_str = "";
		$log_result_tab_new_str = "";
		$log_updated_tabs_str = "";
		$log_deleted_tabs_str = "";
		switch ($type_id) {
		case $GLOBALS['TYPE_FACILITY']:
			if ($_POST['admin_can_add'] == $GLOBALS['TAB_CONFIG_ADD_EDIT']) {
				$log_admin_can_add_str = get_text("Set to admin can add facility tabs");
			} else {
				$log_admin_can_add_str = get_text("Set to admin can not add facility tabs");
			}
			$log_tab_new_str = get_text("New facility tab added") . ": " . remove_nls($_POST['tab_name_new']);
			$log_updated_tabs_str = get_text("Facilty tabs updated") . ": " . $updated_tabs;
			$log_deleted_tabs_str = get_text("Facility tabs deleted") . ": " . $deleted_tabs;
			break;
		case $GLOBALS['TYPE_UNIT']:
			if ($_POST['admin_can_add'] == $GLOBALS['TAB_CONFIG_ADD_EDIT']) {
				$log_admin_can_add_str = get_text("Set to admin can add unit tabs");
			} else {
				$log_admin_can_add_str = get_text("Set to admin can not add unit tabs");
			}
			$log_tab_new_str = get_text("New unit tab added") . ": " . remove_nls($_POST['tab_name_new']);
			$log_updated_tabs_str = get_text("Unit tabs updated") . ": " . $updated_tabs;
			$log_deleted_tabs_str = get_text("Unit tabs deleted") . ": " . $deleted_tabs;
			break;
		default:
		}
		if ($result_admin_can_add == TRUE) {
			$message_str .= $log_admin_can_add_str . "<br>";
			$log_str .= $log_admin_can_add_str . ". ";
		}
		if ($result_tab_new == TRUE) {
			$message_str .= $log_tab_new_str . "<br>";
			$log_str .= $log_tab_new_str . ". ";
		}
		if ($updated_tabs != 0) {
			$message_str .= $log_updated_tabs_str . "<br>";
			$log_str .= $log_updated_tabs_str . ". ";
		}
		if ($changed_tab_names_str != "") {
			$log_str .= get_text("Changed Tab name") . ": " . $changed_tab_names_str . " ";
		}
		if ($deleted_tabs != 0) {
			$message_str .= $log_deleted_tabs_str . "<br>";
			$log_str .= $log_deleted_tabs_str . ". ";
		}
		if ($deleted_tab_names_str != "") {
			$log_str .= get_text("Deleted Tab name") . ": " . substr($deleted_tab_names_str, 0, -2) . ". ";
		}
		if ($log_str != "") {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
			print get_text($message_str) . "<br>";
			//additional parameters switch to new tab
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "presentation_list":
	$type_id = 0;
	if (isset ($_GET['type_id'])) {
		$type_id = $_GET['type_id'];
	}
	$tab_list = get_tab_list($type_id);
	if ((is_super() || get_admin_can_config_presentation($tab_list)) && (($type_id == $GLOBALS['TYPE_UNIT']) || ($type_id == $GLOBALS['TYPE_FACILITY']))) {
		$helptext = "";
		$page_caption = "";
		$admin_can_add_select_caption = get_text("No");
		$tabindex = 1;
		switch ($type_id) {
		case $GLOBALS['TYPE_FACILITY']:
			$helptext = "facility_presentation_tab_list";
			$page_caption = get_text("Facilities presentation configuration");
			$admin_can_add_select_caption = get_text("Facility tab");
			break;
		case $GLOBALS['TYPE_UNIT']:
			$helptext = "unit_presentation_tab_list";
			$page_caption = get_text("Units presentation configuration");
			$admin_can_add_select_caption = get_text("Unit tab");
			break;
		default:
		}
		$can_add_presentation_no = " selected";
		$can_add_presentation_yes = "";
		if ($tab_list[0]["admin_can_add"] == $GLOBALS['TAB_CONFIG_ADD_EDIT']) {
			$can_add_presentation_no = "";
			$can_add_presentation_yes = " selected";
		}
	?>
		<div class="container-fluid" id="main_container">
			<div class="row infostring">
				<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
					<?php print $page_caption . " - " . get_variable("page_caption");?>
				</div>
			</div>
			<form id="presentation" name="presentation">
				<input type="hidden" name="function" value="presentation_list_update">
				<input type="hidden" name="type_id" value="<?php print $type_id;?>">
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button id="cancel_button" type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button id="reset_button" type="button" class="btn btn-xs btn-default" onClick="document.presentation.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button id="submit_button" type="button" class="btn btn-xs btn-default" onClick="send_configuration_form('presentation');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button id="help_button" type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text($helptext);?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
								<tr style="height: 44px;">
									<th style="width: 75%;"<?php print get_help_text_str("tab_order_preview");?>><?php print get_text("Tab order preview");?></th>
									<th style="width: 15%;">
										<?php print get_text("Admin can add");?>
									</th>
									<th style="width: 10%;"></th>
								</tr>
								<tr class="form-group">
									<td<?php print get_help_text_str("tab_order_preview");?> style="overflow: visible !important;"><?php show_tab_preview();?></td>
									<td>
										<select name="admin_can_add" class="form-control"<?php if (!is_super()) print " disabled";?>>
											<option value=<?php print $GLOBALS['TAB_CONFIG_NO'] . " " . $can_add_presentation_no;?>><?php print get_text("No");?></option>
											<option value=<?php print $GLOBALS['TAB_CONFIG_ADD_EDIT'] . " " . $can_add_presentation_yes;?>><?php print $admin_can_add_select_caption;?></option>
										</select>
									</td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1"></div>
					<?php if (is_super() || $tab_list[0]["admin_can_add"] == $GLOBALS['TAB_CONFIG_ADD_EDIT']) { ?>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr style="height: 44px;">
									<th style="width: 20%;"><?php print get_text("Tab name");?></th>
									<th style="width: 17.5%;"><?php print get_text("Visible");?></th>
									<th style="width: 17.5%;"><?php if ($type_id == $GLOBALS['TYPE_UNIT']) print get_text("Add tickets");?></th>
									<th style="width: 10%;"><?php print get_text("Sort");?></th>
									<th style="width: 5%;"></th>
									<th style="width: 5%;"></th>
									<th style="width: 15%;"><?php if (is_super()) print get_text("Admin can config");?></th>
									<th style="width: 5%;"></th>
									<th style="width: 5%;"></th>
								</tr>
								<tr class="form-group">
									<td><input type="text" class="form-control" tabindex="<?php print $tabindex++;?>" name="tab_name_new" placeholder="<?php print get_text("New entry");?>"></input></td>
									<td>
										<select name="visible_new" class="form-control" tabindex="<?php print $tabindex++;?>">
											<option value=<?php print $GLOBALS['TAB_VISIBLE_NO'];?> selected><?php print get_text("No");?></option>
											<option value=<?php print $GLOBALS['TAB_VISIBLE_SINGLE_ONLY'];?>><?php print get_text("Singlemonitor only");?></option>
											<option value=<?php print $GLOBALS['TAB_VISIBLE_MULTI_ONLY'];?>><?php print get_text("Multimonitor only");?></option>
											<option value=<?php print $GLOBALS['TAB_VISIBLE_YES'];?>><?php print get_text("Yes");?></option>
										</select>
									</td>
									<td>
										<?php if ($type_id == $GLOBALS['TYPE_UNIT']) { ?>
										<select name="add_tickets_new" class="form-control" tabindex="<?php print $tabindex++;?>">
											<option value=<?php print $GLOBALS['TAB_ADDITIONAL_TICKETS_NO'];?> selected><?php print get_text("No");?></option>
											<option value=<?php print $GLOBALS['TAB_ADDITIONAL_TICKETS_SINGLE_ONLY'];?>><?php print get_text("Singlemonitor only");?></option>
											<option value=<?php print $GLOBALS['TAB_ADDITIONAL_TICKETS_MULTI_ONLY'];?>><?php print get_text("Multimonitor only");?></option>
											<option value=<?php print $GLOBALS['TAB_ADDITIONAL_TICKETS_YES'];?>><?php print get_text("Yes");?></option>
										</select>
										<?php } ?>
									</td>
									<td><input type="text" class="form-control" tabindex="<?php print $tabindex++;?>" name="sort_new"></input></td>
									<td></td>
									<td></td>
									<td>
										<?php if (is_super()) { ?>
										<select name="admin_edit_new" class="form-control" tabindex="<?php print $tabindex++;?>">
											<option value=<?php print $GLOBALS['TAB_CONFIG_NO'];?> selected><?php print get_text("No");?></option>
											<option value=<?php print $GLOBALS['TAB_CONFIG_VISIBILITY'];?>><?php print get_text("Tab show/hide");?></option>
											<option value=<?php print $GLOBALS['TAB_CONFIG_ADD_EDIT'];?>><?php print get_text("Tab edit/delete");?></option>
										</select>
										<?php } ?>
									</td>
									<td></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div> 
					<?php } ?>
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;"></div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" id="table_top" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<?php if ($tab_list[0]["tab_number"] > 0) { ?>
								<tr style="height: 44px;">
									<th style="width: 20%;"><?php print get_text("Tab name");?></th>
									<th style="width: 17.5%;"><?php print get_text("Visible");?></th>
									<th style="width: 17.5%;"><?php if ($type_id == $GLOBALS['TYPE_UNIT']) print get_text("Add tickets");?></th>
									<th style="width: 10%;"><?php print get_text("Sort");?></th>
									<th style="width: 5%;"><?php print get_text("Columns");?></th>
									<th style="width: 5%;"><?php print get_text("Rows");?></th>
									<th style="width: 15%;"><?php print get_text("Admin can config");?></th>
									<th style="width: 5%;"></th>
									<th style="width: 5%; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></th>
								</tr>
								<?php } ?>
	<?php 
		$visible_select_str = $add_tickets_select_str = $admin_can_config_select_str = array ("", "", "", "", "");
		if ($tab_list[0]["tab_number"] > 0) {
			foreach ($tab_list as $tab_id => $tab_value) {
				if ($tab_id > 0) {
					if (($tab_id >= 1) && ($tab_id <= 4)) {
						$tab_value["tab_name"] = get_text($tab_value["tab_name"]);
					}
					$visible_select_str[$GLOBALS['TAB_VISIBLE_NO']] = $visible_select_str[$GLOBALS['TAB_VISIBLE_SINGLE_ONLY']] = $visible_select_str[$GLOBALS['TAB_VISIBLE_MULTI_ONLY']] = $visible_select_str[$GLOBALS['TAB_VISIBLE_YES']] = "";
					$visible_select_str[$tab_value["visible"]] = " selected";
					$add_tickets_select_str[$GLOBALS['TAB_ADDITIONAL_TICKETS_NO']] = $add_tickets_select_str[$GLOBALS['TAB_ADDITIONAL_TICKETS_SINGLE_ONLY']] = $add_tickets_select_str[$GLOBALS['TAB_ADDITIONAL_TICKETS_MULTI_ONLY']] = $add_tickets_select_str[$GLOBALS['TAB_ADDITIONAL_TICKETS_YES']] = "";
					$add_tickets_select_str[$tab_value["add_tickets"]] = " selected";
					$admin_can_config_select_str[$GLOBALS['TAB_CONFIG_NO']] = $admin_can_config_select_str[$GLOBALS['TAB_CONFIG_VISIBILITY']] = $admin_can_config_select_str[$GLOBALS['TAB_CONFIG_ADD_EDIT']] = "";
					$admin_can_config_select_str[$tab_value["admin_can_config"]] = " selected";
					$edit_disabled_str = "";
					if ((!is_super()) && ($tab_value["admin_can_config"] < $GLOBALS['TAB_CONFIG_ADD_EDIT'])) {
						$edit_disabled_str = " disabled";
					}
					$hide_disabled_str = "";
					if ((!is_super()) && ($tab_value["admin_can_config"] < $GLOBALS['TAB_CONFIG_VISIBILITY'])) {
						$hide_disabled_str = " disabled";
					}
	?>
										<tr class="form-group">
											<td>
												<input type="hidden" name="tab_id[]" value="<?php print $tab_id;?>">
												<input type="text" class="form-control" value="<?php print remove_nls($tab_value["tab_name"]) . "\" name=\"tab_name_" . $tab_id . "\""; if (($tab_id < 5) || ($edit_disabled_str != "")) {print " readonly";} else {print " tabindex=" . $tabindex++;}?>></input>
											</td>
											<td>
												<?php if (($tab_id != 3) && ($tab_id != 4)) { ?>
												<select name="visible_<?php print $tab_id;?>" class="form-control" <?php if ($hide_disabled_str != "") {print get_help_text_str("not_editable") . $hide_disabled_str;} else {print " tabindex=" . $tabindex++;}?>>
													<option value=<?php print $GLOBALS['TAB_VISIBLE_NO'] . " " . $visible_select_str[$GLOBALS['TAB_VISIBLE_NO']];?>><?php print get_text("No");?></option>
													<option value=<?php print $GLOBALS['TAB_VISIBLE_SINGLE_ONLY'] . " " . $visible_select_str[$GLOBALS['TAB_VISIBLE_SINGLE_ONLY']];?>><?php print get_text("Singlemonitor only");?></option>
													<option value=<?php print $GLOBALS['TAB_VISIBLE_MULTI_ONLY'] . " " . $visible_select_str[$GLOBALS['TAB_VISIBLE_MULTI_ONLY']];?>><?php print get_text("Multimonitor only");?></option>
													<option value=<?php print $GLOBALS['TAB_VISIBLE_YES'] . " " . $visible_select_str[$GLOBALS['TAB_VISIBLE_YES']];?>><?php print get_text("Yes");?></option>
												</select>
												<?php } ?>
											</td>
											<td>
												<?php if (($type_id == $GLOBALS['TYPE_UNIT']) && (($tab_id == 1) || ($tab_id > 4))) { ?>
												<select name="add_tickets_<?php print $tab_id;?>" class="form-control" <?php if ($edit_disabled_str != "") {print get_help_text_str("not_editable") . $edit_disabled_str;} else {print " tabindex=" . $tabindex++;}?>>
													<option value=<?php print $GLOBALS['TAB_ADDITIONAL_TICKETS_NO'] . " " . $add_tickets_select_str[$GLOBALS['TAB_ADDITIONAL_TICKETS_NO']];?>><?php print get_text("No");?></option>
													<option value=<?php print $GLOBALS['TAB_ADDITIONAL_TICKETS_SINGLE_ONLY'] . " " . $add_tickets_select_str[$GLOBALS['TAB_ADDITIONAL_TICKETS_SINGLE_ONLY']];?>><?php print get_text("Singlemonitor only");?></option>
													<option value=<?php print $GLOBALS['TAB_ADDITIONAL_TICKETS_MULTI_ONLY'] . " " . $add_tickets_select_str[$GLOBALS['TAB_ADDITIONAL_TICKETS_MULTI_ONLY']];?>><?php print get_text("Multimonitor only");?></option>
													<option value=<?php print $GLOBALS['TAB_ADDITIONAL_TICKETS_YES'] . " " . $add_tickets_select_str[$GLOBALS['TAB_ADDITIONAL_TICKETS_YES']];?>><?php print get_text("Yes");?></option>
												</select>
												<?php } ?>
											</td>
											<td><input type="text" class="form-control" <?php if ($edit_disabled_str == "") {print " tabindex=" . $tabindex++;}?> name="sort_<?php print $tab_id;?>" value="<?php print remove_nls($tab_value["sort"]);?>"<?php print $edit_disabled_str;?>></input></td>
											<td>
												<?php if (($tab_id == 1) || ($tab_id > 4)) { ?>
												<input type="text" class="form-control" value="<?php print remove_nls($tab_value["column"]);?>" disabled></input>
												<?php } ?>
											</td>
											<td>
												<?php if ($tab_id > 4) { ?>
												<input type="text" class="form-control" value="<?php print remove_nls($tab_value["row"]);?>" disabled></input>
												<?php } ?>
											</td>
											<td>
												<?php if ($tab_id > 4) { ?>
												<select name="admin_can_config_<?php print $tab_id;?>" class="form-control" <?php if (!is_super()) {print get_help_text_str("not_editable") . " disabled";} else {print " tabindex=" . $tabindex++;}?>>
													<option value=<?php print $GLOBALS['TAB_CONFIG_NO'] . " " . $admin_can_config_select_str[$GLOBALS['TAB_CONFIG_NO']];?>><?php print get_text("No");?></option>
													<option value=<?php print $GLOBALS['TAB_CONFIG_VISIBILITY'] . " " . $admin_can_config_select_str[$GLOBALS['TAB_CONFIG_VISIBILITY']];?>><?php print get_text("Tab show/hide");?></option>
													<option value=<?php print $GLOBALS['TAB_CONFIG_ADD_EDIT'] . " " . $admin_can_config_select_str[$GLOBALS['TAB_CONFIG_ADD_EDIT']];?>><?php print get_text("Tab edit/delete");?></option>
												</select>
												<?php } ?>
											</td>
											<td style="text-align: center;">
												<?php if ($tab_id > 4) { ?>
												<span <?php if ($edit_disabled_str != "") {print get_help_text_str("not_editable") . " style=\"color: grey;\"";} else {print "onclick=\"goto_window('configuration.php?function=presentation_tab&tab_id=" . $tab_id . "')\"";}?> class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
												<?php } ?>
											</td>
											<td style="text-align: center;" <?php if ($edit_disabled_str != "") print get_help_text_str("not_editable");?>>
												<?php if ($tab_id > 4) { ?>
												<input type="checkbox" <?php if ($edit_disabled_str == "") {print " tabindex=" . $tabindex++;}?> name="delete_<?php print $tab_id;?>"<?php print $edit_disabled_str;?>>
												<?php } ?>
											</td>
										</tr>
	<?php
				}
			}
		} else {
	?>
									<tr class="form-group" style="height: 44px;">
										<th colspan=9 style="text-align: center;"><?php print get_text("No data");?></th>
									</tr>
	<?php
		}
	?>
								</table>
								<script>
									$(document).ready(function() {
										tabindex="<?php print $tabindex;?>";
										$("#help_button").attr("tabindex",tabindex++);
										$("#submit_button").attr("tabindex",tabindex++);
										$("#reset_button").attr("tabindex",tabindex++);
										$("#cancel_button").attr("tabindex",tabindex++);
										$('[tabindex=1]').focus();
									});
								</script>
							</div>
						</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
/*
case "regions_update":
	if (is_super()) {
		if (isset ($_POST['name_new']) && ($_POST['name_new'] != "")) {

			$query = "INSERT INTO `regions` (`region_name`, `group`, `sort`, `description`, " .
				"`owner_id`, `def_city`, `def_lat`, `def_lng`, " .
				"`def_zoom`, `boundary`, `user_id`, `client_address`, " .
				"`updated`) " .
				"VALUES ('" . $_POST['name_new'] . "', '', 0, '" . $_POST['description_new'] . "', " .
				$_SESSION['user_id'] . ", '', 0.999999, 0.999999, " .
				"10, 10, " . $_SESSION['user_id'] . ", '" . $_SERVER['REMOTE_ADDR'] . "', " .
				"'" . $datetime_now . "');";

			$result = db_query($query, __FILE__, __LINE__);
			if (db_affected_rows($result) > 0) {
				$top_notice_str .= get_text("regions added") . ": " . db_affected_rows($result) . "<br>";
				$top_notice_log_str .= get_text("regions added") . ": " . db_affected_rows($result) . "  ";
			}
		}
		$deleted_rows = 0;
		foreach ($_POST['region_id'] as $VarName => $VarValue) {
			if (isset ($_POST['delete_' . $VarValue]) && ($_POST['delete_' . $VarValue]) == "on") {

				$query = "DELETE FROM `regions` " .
					"WHERE `id` = " . $VarValue . ";";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$deleted_rows++;
				}
			} else {

				$query = "UPDATE `regions` SET " .
					"`description` = ". quote_smart($_POST['description'][$VarName]) . " " .
					"WHERE `id` = " . $VarValue . ";";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$updated_rows++;
				}
			}
		}
		if ($updated_rows != 0) {
			$top_notice_str .= get_text("regions updated") . ": " . $updated_rows . "<br>";
			$top_notice_log_str .= get_text("regions updated") . ": " . $updated_rows . "  ";
		}
		if ($deleted_rows != 0) {
			$top_notice_str .= get_text("regions deleted") . ": " . $deleted_rows . "<br>";
			$top_notice_log_str .= get_text("regions deleted") . ": " . $deleted_rows . "  ";
		}
	}
	break;
case "regions":
	if (is_super()) {
	?>
		<div id="main_container" class="container-fluid">
			<div class="row infostring">
				<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
					<?php print get_text("Regions Configuration") . " - "  . get_variable("page_caption");?>
				</div>
			</div>
			<form id="regions" name="regions" method="post" action="configuration.php?function=regions_update">
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="document.regions.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="document.regions.submit();"><?php print get_text("Save");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr style="height: 44px;">
									<th style="width: 25%;"><?php print get_text("Name");?></th>
									<th style="width: 50%;"><?php print get_text("Description");?></th>
									<th style="width: 20%;"></th>
									<th style="width: 5%;"></th>
								</tr>
								<tr class="form-group">
									<td><input type="text" id="name_new" name="name_new" class="form-control" placeholder="<?php print get_text("New entry");?>"></input></td>
									<td><input type="text" id="description_new" name="description_new" class="form-control"></input></td>
									<td></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;"></div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
								<table class="table table-striped table-condensed" style="text-align: left;">
									<tr style="height: 44px;">
										<th style="width: 25%;"><?php print get_text("Name");?></th>
										<th style="width: 50%;"><?php print get_text("Description");?></th>
										<th style="width: 20%;"></th>
										<th style="width: 5%; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></th>
									</tr>
	<?php

		$query = "SELECT * " .
			"FROM `regions`";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			while ($row =  stripslashes_deep(db_fetch_array($result))) {

				$query = "SELECT * " .
					"FROM `allocates` " .
					"WHERE `group` = " . $row['id'] . ";";

				$result_group = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result_group) > 0) {
					$delete_disabled_str = " disabled";
				} else {
					$delete_disabled_str = "";
				}
	?>
								<tr class="form-group">
									<td><input type="text" id="name[]" name="name[]" class="form-control" value="<?php print $row['region_name'];?>" disabled></input></td>
									<td><input type="text" id="description[]" name="description[]" class="form-control" value="<?php print $row['description'];?>"></input></td>
									<td></td>
									<td style="text-align: center;">
										<input type="checkbox" id="delete_<?php print $row['id'];?>" name="delete_<?php print $row['id'];?>"<?php print $delete_disabled_str;?>>
										<input type="hidden" id="region_id[]" name="region_id[]" value="<?php print $row['id'];?>">
									</td>
								</tr>
	<?php
			}
		} else {
	?>
									<tr class="form-group" style="height: 44px;">
										<th colspan=6 style="text-align: center;"><?php print get_text("No data");?></th>
									</tr>
	<?php
		}
	?>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "cleanse_regions_update":
	if (is_super()) {
		// Declare arrays for all resource ids
		$region_ids = array ();
		$ticket_ids = array ();
		$user_ids = array ();
		$unit_ids = array ();
		$facility_ids = array ();

		$query = "SELECT * " .
			"FROM `regions`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$region_ids[] = $row['id'];
		}

		$query = "SELECT * " .
			"FROM `tickets`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$ticket_ids[] = $row['id'];
		}

		$query = "SELECT * " .
			"FROM `users`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$user_ids[] = $row['id'];
		}

		$query = "SELECT * " .
			"FROM `units`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$unit_ids[] = $row['id'];
		}

		$query = "SELECT * " .
			"FROM `facilities`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$facility_ids[] = $row['id'];
		}

		$text_output = "";
		// cleanse entries for Users
		$counter1 = 0;
		foreach ($region_ids as $value) {
			foreach ($user_ids as $value2) {

				$query = "SELECT * " .
					"FROM `allocates` " .
					"WHERE `resource_id` = '" . $value2 . "' " .
					"AND `group` = " . $value . " " .
					"AND `type` = " . $GLOBALS['TYPE_USER'] . ";";

				$result = db_query($query, __FILE__, __LINE__);
				$num_entries = db_num_rows($result);
				if ($num_entries > 1) {
					$counter1++;
					for ($i = 1; $i < $num_entries; $i++) {

						$query_d  = "DELETE FROM `allocates` " .
							"WHERE `resource_id` = '" . $value2 . "' " .
							"AND `group` = " . $value . " " .
							"AND `type` = " . $GLOBALS['TYPE_USER'] . " " .
							"LIMIT 1;";

						db_query($query_d, __FILE__, __LINE__);
					}
				}
			}
		}
// cleanse entries for Tickets
		$counter2 = 0;
		foreach ($region_ids as $value) {
			foreach ($ticket_ids as $value2) {

				$query = "SELECT * " .
					"FROM `allocates` " .
					"WHERE `resource_id` = '" . $value2 . "' " .
					"AND `group` = " . $value . " " .
					"AND `type` = " . $GLOBALS['TYPE_TICKET'] . ";";

				$result = db_query($query, __FILE__, __LINE__);
				$num_entries = db_num_rows($result);
				if ($num_entries > 1) {
					$counter2++;
					for ($i = 1; $i < $num_entries; $i++) {

						$query_d  = "DELETE FROM `allocates` " .
							"WHERE `resource_id` = '" . $value2 . "' " .
							"AND `group` = " . $value . " " .
							"AND `type` = " . $GLOBALS['TYPE_TICKET'] . " " .
							"LIMIT 1;";

						$result_d = db_query($query_d, __FILE__, __LINE__);
					}
				}
			}
		}

		// cleanse entries for Responders
		$counter3 = 0;
		foreach ($region_ids as $value) {
			foreach ($unit_ids as $value2) {

				$query = "SELECT * " .
					"FROM `allocates` " .
					"WHERE `resource_id` = '" . $value2 . "' " .
					"AND `group` = " . $value . " " .
					"AND `type` = " . $GLOBALS['TYPE_UNIT'] . ";";

				$result = db_query($query, __FILE__, __LINE__);
				$num_entries = db_num_rows($result);
				if ($num_entries > 1) {
					$counter3++;
					for ($i = 1; $i < $num_entries; $i++) {

						$query_d  = "DELETE FROM `allocates` " .
							"WHERE `resource_id` = '" . $value2 . "' " .
							"AND `group` = " . $value . "' " .
							"AND `type` = " . $GLOBALS['TYPE_UNIT'] . " " .
							"LIMIT 1;";

						$result_d = db_query($query_d);
					}
				}
			}
		}

		// cleanse entries for Facilities
		$counter4 = 0;
		foreach ($region_ids as $value) {
			foreach ($facility_ids as $value2) {

				$query = "SELECT * " .
					"FROM `allocates` " .
					"WHERE `resource_id` = '" . $value2 . "' " .
					"AND `group` = " . $value . " " .
					"AND `type` = " . $GLOBALS['TYPE_FACILITY'] . ";";

				$result = db_query($query, __FILE__, __LINE__);
				$num_entries = db_num_rows($result);
				if ($num_entries > 1) {
					$counter4++;
					for ($i = 1; $i < $num_entries; $i++) {

						$query_d  = "DELETE FROM `allocates` " .
							"WHERE `resource_id` = '" . $value2 . "' " .
							"AND `group` = " . $value . " " .
							"AND `type` = " . $GLOBALS['TYPE_FACILITY'] . " " .
							"LIMIT 1;";

						$result_d = db_query($query_d, __FILE__, __LINE__);
					}
				}
			}
		}

		$text_output .= $counter1 >= 1 ? "User Allocations Cleansed<br>" : "User Allocation Cleansing not required<br>";
		$text_output .= $counter2 >= 1 ? "Ticket Allocations Cleansed<br>" : "Ticket Allocation Cleansing not required<br>";
		$text_output .= $counter3 >= 1 ? "Responder Allocations Cleansed<br>" : "Responder Allocation Cleansing not required<br>";
		$text_output .= $counter4 >= 1 ? "Facility Allocations Cleansed<br>" : "Facility Allocation Cleansing not required<br>";
		$top_notice_str .= get_text("Cleanse Regions") . $text_output . "<br>";
		$top_notice_log_str .= get_text("Cleanse Regions") . $text_output . "  ";
	}
	break;
case "cleanse_regions":
	if (is_super()) {
		$region_ids = array ();
		$ticket_ids = array ();
		$user_ids = array ();
		$unit_ids = array ();
		$facility_ids = array ();
		// get region ids.

		$query = "SELECT * " .
			"FROM `regions`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) 	{
			$region_ids[] = $row['id'];
		}
		// get ticket ids.

		$query = "SELECT * " .
			"FROM `tickets`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) 	{
			$ticket_ids[] = $row['id'];
		}
		// get user ids

		$query = "SELECT * " .
			"FROM `users`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) 	{
			$user_ids[] = $row['id'];
		}
		// get responder / unit ids
		
		$query = "SELECT * " .
			"FROM `units`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) 	{
			$unit_ids[] = $row['id'];
		}
		// get facility ids

		$query = "SELECT * " .
			"FROM `facilities`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) 	{
			$facility_ids[] = $row['id'];
		}
		// end of facility ids
	?>
				<body onload="check_frames();">
				<div style="font-size: 20px; font-weight: bold; width:70%;">
				<div>
				Region Table Allocation List<div class="button_bar">
				<a class="buttons" href="configuration.php?function=cleanse_regions_update">Cleanse / Sanitize</a>
				<a class="buttons" href="configuration.php">Cancel / Return to Config</a></div></div>
				<div id="flag" class="flag"></div>
				<div style="width: 100%;">
	<?php
		$counter = 0;
		print "<table style='width: 100%; border: 1px;'>";
		print "<tr class='table_header'>";
		print "<td class='table_hdr_cell'>Region</td><td class='table_hdr_cell'>Users</td><td class='table_hdr_cell'>Tickets</td><td class='table_hdr_cell'>Responders</td><td class='table_hdr_cell'>Facilities</td></tr>";
		// list all allocations
			foreach ($region_ids as $value) {
				print "<tr>";
				print "<td class='table_cell'>" . $value . "</td>";
				print "<td class='table_cell'>";
				if (count($user_ids) > 0) {
					foreach ($user_ids as $value2) {

						$query = "SELECT * " .
							"FROM `allocates` " .
							"WHERE `resource_id` = '" . $value2 . "' " .
							"AND `group` = " . $value . " " .
							"AND `type` = " . $GLOBALS['TYPE_USER'] . ";";

						$result = db_query($query, __FILE__, __LINE__);
						$num_entries = db_num_rows($result);
						if ($num_entries == 1) {
							print "User ID: " . $value2 . "<br>";
							} elseif ($num_entries >= 2) {
								$counter++;						
								print "<font color='red'>User ID: " . $value2 . "&nbsp;&nbsp;&nbsp;" . "Duplicate Entries</font>";
							}
						}
					} else {
						print "No Users Allocated to Regions";
					}
				print "</td>";
				print "<td class='table_cell'>";
				if (count($ticket_ids) > 0) {
					foreach ($ticket_ids as $value2) {

						$query = "SELECT * " .
							"FROM `allocates` " .
							"WHERE `resource_id` = " . $value2 . " " .
							"AND `group` = " . $value . " " .
							"AND `type` = " . $GLOBALS['TYPE_TICKET'] . ";";

						$result = db_query($query, __FILE__, __LINE__);
						$num_entries = db_num_rows($result);
						if ($num_entries == 1) {
							print "Ticket ID: " . $value2 . "<br>";
							} elseif ($num_entries >=2) {
								$counter++;
								print "<font color='red'>Ticket ID: " . $value2 . "&nbsp;&nbsp;&nbsp;" . "Duplicate Entries</font>";
							}
						}
					} else {
						print "No Tickets Allocated to Regions";
					}
				print "</td>";	
				print "<td class='table_cell'>";
				if (count($unit_ids) > 0) {
					foreach ($unit_ids as $value2) {

						$query = "SELECT * " .
							"FROM `allocates` " .
							"WHERE `resource_id` = " . $value2 . " " .
							"AND `group` = " . $value . " " .
							"AND `type` = " . $GLOBALS['TYPE_UNIT'] . ";";

						$result = db_query($query, __FILE__, __LINE__);
						$num_entries = db_num_rows($result);
						if ($num_entries == 1) {				
								print "Responder ID: " . $value2 . "<br>";
							} elseif($num_entries >= 2) {
								$counter++;						
								print "<font color='red'>Responder ID: " . $value2 . "&nbsp;&nbsp;&nbsp;" . "Duplicate Entries</font>";
							}
						}
					} else {
						print "No Responders Allocated to Regions";
					}
				print "</td>";
				print "<td class='table_cell'>";
				if (count($facility_ids) > 0) {
					foreach ($facility_ids as $value2) {

						$query = "SELECT * " .
							"FROM `allocates` " .
							"WHERE `resource_id` = '" . $value2 . "' " .
							"AND `group` = '" . $value . "' " .
							"AND `type` = " . $GLOBALS['TYPE_FACILITY'];

						$result = db_query($query, __FILE__, __LINE__);
						$num_entries = db_num_rows($result);
						if($num_entries == 1) {		
								print "Facility ID: " . $value2 . "<br>";
							} elseif($num_entries >= 2) {
								$counter++;
								print "<font color='red'>Facility ID: " . $value2 . "&nbsp;&nbsp;&nbsp;" . "Duplicate Entries</font>";
							}
						}
					} else {
					print "No Facilities Allocated to Regions";
					}
				print "</td></tr>";
			}
		if ($counter >= 1) {
				$output_text = "<font color='red'>THERE ARE ERRORS</font>";
			} else {
				$output_text = "<font color='green'>NO ERRORS</font>";
			}
// end of allocations list
	?>
			</div>
		</div>
		<script>
			$('flag').innerHTML = "<?php print $output_text; ?>";
		</script>
	</body>
</html>
	<?php
	}
	break;
case "reset_regions_update":
	if (is_super()) {

		$query = "DROP TABLE IF EXISTS `allocates`;";

		$result = db_query($query, __FILE__, __LINE__);
		//keine gelöschten Einheiten zurücksetzen!

		$query = "CREATE TABLE IF NOT EXISTS `allocates` " .
			"(`id` int(8) NOT NULL auto_increment, `group` int(8) NOT NULL default '1', `type` tinyint(1) NOT NULL default '1', `updated` datetime default NULL, `resource_id` int(8) default NULL, `user_id` int(4) NOT NULL default '0', PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

		$result = db_query($query, __FILE__, __LINE__);
		$tickets_reset = 0;

		$query_insert = "SELECT * " .
			"FROM `tickets`;";

		$result_insert = db_query($query_insert, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result_insert))) {
			$new_id = insert_into_allocates(1, $GLOBALS['TYPE_TICKET'], $row['id'], $_SESSION['user_id'], $datetime_now);
			if ($new_id > 0) {
				$tickets_reset++;
			}
		}
		$units_reset = 0;

		$query_insert = "SELECT * " .
			"FROM `units`;";

		$result_insert = db_query($query_insert, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result_insert))) {
			$new_id = insert_into_allocates(1, $GLOBALS['TYPE_UNIT'], $row['id'], $_SESSION['user_id'], $datetime_now);
			if ($new_id > 0) {
				$units_reset++;
			 }
		}
		$facilities_reset = 0;

		$query_insert = "SELECT * " .
			"FROM `facilities`;";

		$result_insert = db_query($query_insert, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result_insert))) {
			$new_id = insert_into_allocates(1, $GLOBALS['TYPE_FACILITY'], $row['id'], $_SESSION['user_id'], $datetime_now);
			 if ($new_id > 0) {
				$facilities_reset++;
			 }
		}
		$users_reset = 0;

		$query_insert = "SELECT * " .
			"FROM `users`;";

		$result_insert = db_query($query_insert, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result_insert))) {
			$new_id = insert_into_allocates(1, $GLOBALS['TYPE_USER'], $row['id'], $_SESSION['user_id'], $datetime_now);
			 if ($new_id > 0) {
				$users_reset++;
			 }
		}
		$top_notice_str .= get_text("Reset Regions") . $tickets_reset . $units_reset . $facilities_reset . $users_reset . "<br>";
		$top_notice_log_str .= get_text("Reset Regions") . $tickets_reset . $units_reset . $facilities_reset . $users_reset . "  ";
	}
	break;
case "reset_regions":
	if (is_super()) {
	?>
			<form id="reset_regions" name="reset_regions" method="post" action="configuration.php?function=reset_regions_update">
				<div style="font-size: 14px; position: absolute; top: 20px; left: 30%;">
					<div class="heading" style="font-size: 24px; text-align: center;"><?php print get_text("Reset Regions");?></div>
					<br><br>
					<div style="padding: 20px; border:1px outset #FFFFFF; position: relative; background-color: #F8F8F8;">
						<b><?php print get_text("Reset all resources back to first Region?");?></b><br>
						<br><br>
						<button onclick="window.location.href='configuration.php';"><?php print get_text("Cancel");?></button>
						<button type="submit"><?php print get_text("Reset");?></button>
						<br><br>
					</div>
				</div>
			</form>
		</body>
	</html>	
	<?php
	}
	break;*/
case "incident_types_update":
	if (is_super()) {
		$message_str = "";
		$log_str = NULL;
		if (isset ($_POST['nature_new']) && ($_POST['nature_new'] != "")) {
			$result = insert_into_incident_types($_POST['nature_new'], $_POST['description_new'], $_POST['protocol_new'], 
				$_POST['severity_new'],	$_POST['group_new'], $_POST['sort_new'], $_SESSION['user_id'], $datetime_now);
			if (db_affected_rows($result) > 0) {
				$message_str .= get_text("Dataset in_types added") . ": " . db_affected_rows($result) . "<br>";
				$log_str .= get_text("Dataset in_types added") . ": " . db_affected_rows($result) . "  ";
			}
		}
		$updated_rows = 0;
		$deleted_rows = 0;
		foreach ($_POST['incident_types_id'] as $VarName=>$VarValue) {
			if (isset ($_POST['delete_' . $VarValue]) && ($_POST['delete_' . $VarValue]) == "on") {

				$query = "UPDATE `incident_types` " .
					"SET `group` = 'DELETED' " .
					"WHERE `id` = " . $VarValue . ";";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$deleted_rows++;

					$query = "UPDATE `incident_types` SET " .
						"`updated` = " . quote_smart($datetime_now) . ", " .
						"`client_address` = ". quote_smart($_SERVER['REMOTE_ADDR']) . ", " .
						"`user_id` = ". $_SESSION['user_id'] . " " .
						"WHERE `id` = " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
				}
			} else {

				$query = "UPDATE `incident_types` SET " .
					"`description` = ". quote_smart($_POST['description'][$VarName]) . ", " .
					"`set_severity` = ". $_POST['severity'][$VarName] . ", " .
					"`group` = ". quote_smart($_POST['group'][$VarName]) . ", " .
					"`protocol` = ". quote_smart($_POST['protocol'][$VarName]) . ", " .
					"`sort` = ". quote_smart($_POST['sort'][$VarName]) . " " .
					"WHERE `id` = " . $VarValue . ";";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$updated_rows++;

					$query = "UPDATE `incident_types` SET " .
						"`updated` = " . quote_smart($datetime_now) . ", " .
						"`client_address` = ". quote_smart($_SERVER['REMOTE_ADDR']) . ", " .
						"`user_id` = ". $_SESSION['user_id'] . " " .
						"WHERE `id` = " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
				}
			}
		}
		if ($updated_rows != 0) {
			$message_str .= get_text("Dataset in_types updated") . ": " . $updated_rows . "<br>";
			$log_str .= get_text("Dataset in_types updated") . ": " . $updated_rows . "  ";
		}
		if ($deleted_rows != 0) {
			$message_str .= get_text("Dataset in_types deleted") . ": " . $deleted_rows . "<br>";
			$log_str .= get_text("Dataset in_types deleted") . ": " . $deleted_rows . "  ";
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
			print get_text($message_str) . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "incident_types":
	if (is_super()) {
	?>
		<div id="main_container" class="container-fluid">
			<form id="incident_types" name="incident_types">
				<input type="hidden" id="function" name="function" value="incident_types_update">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Incident Types Configuration") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="document.incident_types.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="send_configuration_form('incident_types');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_incident_types");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">	
								<tr style="height: 44px;">
									<th style="width: 15%;"><?php print get_text("Incident type");?></th>
									<th style="width: 10%;"><?php print get_text("Severity");?></th>
									<th style="width: 25%;"><?php print get_text("Description");?></th>
									<th style="width: 25%;"><?php print get_text("Protocol");?></th>
									<th style="width: 10%;"><?php print get_text("Sort group");?></th>
									<th style="width: 10%;"><?php print get_text("Sort");?></th>
									<th style="width: 5%;"></th>
								</tr>
								<tr class="form-group">
									<td><input type="text" id="nature_new" name="nature_new" class="form-control" placeholder="<?php print get_text("New entry");?>"></input></td>
									<td>
										<select id="severity_new" name="severity_new" class="form-control">
											<option value=0 selected><?php print get_text("Normal");?></option>
											<option value=1><?php print get_text("Medium");?></option>
											<option value=2><?php print get_text("High");?></option>
										</select>
									</td>
									<td><textarea type="text" id="description_new" name="description_new" class="form-control"></textarea></td>
									<td><textarea type="text" id="back" name="protocol_new" class="form-control" value=""></textarea></td>
									<td><input type="text" id="group_new" name="group_new" class="form-control"></input></td>
									<td><input type="text" id="text" name="sort_new" class="form-control" value=""></input></td>
									<td></td>
								</tr>
							</table>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1">
					<div class="container-fluid" style="position: fixed;"></div>
				</div>
				<div class="col-md-10">
					<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">	
								<tr style="height: 44px;">
									<th style="width: 15%;"><?php print get_text("Incident type");?></th>
									<th style="width: 10%;"><?php print get_text("Severity");?></th>
									<th style="width: 25%;"><?php print get_text("Description");?></th>
									<th style="width: 25%;"><?php print get_text("Protocol");?></th>
									<th style="width: 10%;"><?php print get_text("Sort group");?></th>
									<th style="width: 10%;"><?php print get_text("Sort");?></th>
									<th style="width: 5%; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></th>
								</tr>
	<?php
		$severity_select_str = array ("", "", "", "", "");
		$delete_disabled_str = "";

		$query = "SELECT * " .
			"FROM `incident_types` " .
			"WHERE `group` != 'DELETED';";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			while ($row =  stripslashes_deep(db_fetch_array($result))) {
				$severity_select_str = array ("", "", "", "");
				$severity_select_str[0] = $severity_select_str[1] = $severity_select_str[2] = "";
				$severity_select_str[$row['set_severity']] = " selected";
	?>
								<tr class="form-group">
									<td><input type="text" id="nature[]" name="nature[]" class="form-control" value="<?php print $row['type'];?>" disabled></input></td>
									<td>
										<select id="severity[]" name="severity[]" class="form-control">
											<option value=0<?php print $severity_select_str[0];?>><?php print get_text("Normal");?></option>
											<option value=1<?php print $severity_select_str[1];?>><?php print get_text("Medium");?></option>
											<option value=2<?php print $severity_select_str[2];?>><?php print get_text("High");?></option>
										</select>
									</td>
									<td><textarea type="text" id="description[]" name="description[]" class="form-control"><?php print $row['description'];?></textarea></td>
									<td><textarea type="text" id="protocol[]" name="protocol[]" class="form-control"><?php print $row['protocol'];?></textarea></td>
									<td><input type="text" id="group[]" name="group[]" class="form-control" value="<?php print $row['group'];?>"></input></td>
									<td><input type="text" id="sort[]" name="sort[]" class="form-control" value="<?php print $row['sort'];?>"></input></td>
									<td style="text-align: center;">
										<input type="checkbox" id="delete_<?php print $row['id'];?>" name="delete_<?php print $row['id'];?>"<?php print $delete_disabled_str;?>>
										<input type="hidden" id="incident_types_id[]" name="incident_types_id[]" value="<?php print $row['id'];?>">
									</td>
								</tr>
	<?php
			}
		} else {
	?>
									<tr class="form-group" style="height: 44px;">
										<th style="text-align: center;" colspan=7><?php print get_text("No data");?></th>
									</tr>
	<?php
		}
	?>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "textblocks_update":
	if (is_super()) {
		$message_str = "";
		$log_str = NULL;
		if (isset ($_POST['frm_textblock_new']) && ($_POST['frm_textblock_new'] != "")) {
			$group = "";
			if (isset ($_POST['group_new']) && ($_POST['group_new'] != "")) {
				$group = $_POST['group_new'];
			}
			$code = "";
			if (isset ($_POST['frm_apicode_new']) && ($_POST['frm_apicode_new'] != "")) {
				$code = $_POST['frm_apicode_new'];
			}
			$report_channels = 0;
			if (isset ($_POST['reporting_channel_new']) && ($_POST['reporting_channel_new'] != "")) {
				foreach ($_POST['reporting_channel_new'] as $VarValue) {
					$report_channels = $report_channels | $VarValue;
				}
			}
			$result = insert_into_textblocks($_POST['type'], $group, $_POST['frm_textblock_new'], $code,
				$report_channels, $_POST['sort_new'], $_SESSION['user_id'], $datetime_now);
			if (db_affected_rows($result) > 0) {
				$message_str .= get_text("Dataset textblocks " . $_POST['type'] . " added") . ": " . db_affected_rows($result) . "<br>";
				$log_str .= get_text("Dataset textblocks " . $_POST['type'] . " added") . ": " . db_affected_rows($result) . "  ";
			}
		}
		$updated_rows = 0;
		$deleted_rows = 0;
		foreach ($_POST['textblocks_id'] as $VarName => $VarValue) {
			if (isset ($_POST['delete_' . $VarValue]) && ($_POST['delete_' . $VarValue]) == "on") {

				$query = "DELETE FROM `textblocks` " .
					"WHERE `id` = " . $VarValue . ";";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$deleted_rows++;
				}
			} else {
				$code_str = "";
				if (isset ($_POST['apicode'][$VarName]) && ($_POST['apicode'][$VarName] != "")) {
					$code_str = "`code` = '" . $_POST['apicode'][$VarName] . "', ";
				}
				$report_channels = 0;
				$report_channels_str = "";
				if (isset ($_POST['reporting_channel_' . $VarValue])) {
					foreach ($_POST['reporting_channel_' . $VarValue] as $VarName2 => $VarValue2) {
						$report_channels = $report_channels | $VarValue2;
					}
					$report_channels_str = "`report_channels` = " . $report_channels . ", ";
				}
				$group_str = "";
				if (isset ($_POST['group'][$VarName]) && ($_POST['group'][$VarName] != "")) {
					$group_str = "`group` = '" . $_POST['group'][$VarName] . "', ";
				}
				$sort_str = "";
				if (isset ($_POST['sort'][$VarName]) && ($_POST['sort'][$VarName] != "")) {
					$sort_str = "`sort` = '" . $_POST['sort'][$VarName] . "', ";
				}

				$query = "UPDATE `textblocks` SET " .
					"`type` = '" . $_POST['type'] . "', " .
					$group_str .
					$code_str .
					$report_channels_str .
					$sort_str .
					"`text` = '" . $_POST['textblock'][$VarName] . "' " .
					"WHERE `id` = " . $VarValue . ";";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$updated_rows++;

					$query = "UPDATE `textblocks` SET " .
						"`updated` = '" . $datetime_now . "', " .
						"`client_address` = '" . $_SERVER['REMOTE_ADDR'] . "', " .
						"`user_id` = " . $_SESSION['user_id'] . " " .
						"WHERE `id` = " . $VarValue . ";";

					$result = db_query($query, __FILE__, __LINE__);
				}
			}
		}
		if ($updated_rows != 0) {
			$message_str .= get_text("Dataset textblocks " . $_POST['type'] . " updated") . ": " . $updated_rows . "<br>";
			$log_str .= get_text("Dataset textblocks " . $_POST['type'] . " updated") . ": " . $updated_rows . "  ";
		}
		if ($deleted_rows != 0) {
			$message_str .= get_text("Dataset textblocks " . $_POST['type'] . " deleted") . ": " . $deleted_rows . "<br>";
			$log_str .= get_text("Dataset textblocks " . $_POST['type'] . " deleted") . ": " . $deleted_rows . "  ";
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
			print get_text($message_str) . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "textblocks":
	$header_text = "";
	$textinput_caption = get_text("Textblock");
	$textinput_width = "75%";
	$show_additional_options = false;
	$configured_fixtexts = array ();
	if (is_super()) {
		switch ($_GET['textblocks']) {
		case "synopsis":
			$header_text = get_text("Textblocks synopsis");
			break;
		case "description":
			$header_text = get_text("Textblocks description");
			break;
		case "action":
			$header_text = get_text("Textblocks action");
			break;
		case "assign":
			$header_text = get_text("Textblocks assign");
			break;
		case "close":
			$header_text = get_text("Textblocks incident close");
			break;
		case "log":
			$header_text = get_text("Textblocks log");
			break;
		case "message":
			$header_text = get_text("Textblocks message");
			break;
		case "fixtext":
			$header_text = get_text("Message fixtexts");
			$textinput_caption = get_text("Message fixtext");
			$textinput_width = "63%";
			$show_additional_options = true;

			$query = "SELECT `value` " .
				"FROM `settings` " .
				"WHERE `name` " .
				"LIKE '_api_%_mess';";

			$result = db_query($query, __FILE__, __LINE__);
			if (db_affected_rows($result) > 0) {
				while ($row = stripslashes_deep(db_fetch_array($result))) {
					array_push($configured_fixtexts, $row['value']);
				}
			}
			break;
		default:
			$header_text = "Error";
		}
		if ($_GET['textblocks'] == "fixtext") {
	?>
			<style>
				.table, td {
					overflow: visible !important;
				}
			</style>
			<script>

				$(document).ready(function() {
					$("#reporting_channel_new").multiselect ({
						buttonWidth: "100%",
						nonSelectedText: "<?php print html_entity_decode(get_text("None selected"));?>",
						nSelectedText: "<?php print html_entity_decode(get_text("selected"));?>",
						allSelectedText: "<?php print html_entity_decode(get_text("All selected"));?>",
						numberDisplayed: 0
					});
				});

			</script>
	<?php
		}
	?>
		<div id="main_container" class="container-fluid">
			<form id="textblocks" name="textblocks">
				<input type="hidden" id="function" name="function" value="textblocks_update">
				<input type="hidden" id="type" name="type" value="<?php print $_GET['textblocks'];?>">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Textblocks") . " " . $header_text . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="document.textblocks.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="send_configuration_form('textblocks');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_fixtexts");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr style="height: 44px;">
									<th style="width: <?php print $textinput_width;?>;"><?php print $textinput_caption;?></th>
	<?php if ($show_additional_options) { ?>
									<th style="width: 10%;"><?php print get_text("API-Code");?></th>
									<th style="width: 12%;"><?php print get_text("Reporting channel");?></th>
	<?php } else { ?>
									<th style="width: 10%;"><?php print get_text("Sort group");?></th>
	<?php } ?>
									<th style="width: 10%;"><?php print get_text("Sort");?></th>
									<th style="width: 5%;"></th>
								</tr>
								<tr class="form-group">
									<td><input type="text" id="frm_textblock_new" name="frm_textblock_new" class="form-control" placeholder="<?php print get_text("New entry");?>"></input></td>
	<?php if ($show_additional_options) { ?>
									<td><input type="text" id="frm_apicode_new" name="frm_apicode_new" class="form-control"></input></td>
									<td>
										<select id="reporting_channel_new" name="reporting_channel_new[]" multiple="multiple">
											<option value=1><?php print get_text("Cellular phone");?></option>
											<option value=2><?php print get_text("Email");?></option>
											<option value=4><?php print get_text("Printer");?></option>
											<option value=8><?php print get_text("Reporting channel 1");?></option>
											<option value=16><?php print get_text("Reporting channel 2");?></option>
											<option value=32><?php print get_text("Reporting channel 3");?></option>
											<option value=64><?php print get_text("Reporting channel 4");?></option>
											<option value=128><?php print get_text("Reporting channel 5");?></option>
										</select>
									</td>
	<?php } else { ?>
									<td><input type="text" id="group_new" name="group_new" class="form-control" value=""></input></td>
	<?php } ?>
									<td><input type="text" id="sort_new" name="sort_new" class="form-control" value=""></input></td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;"></div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr style="height: 44px;">
									<th style="width: <?php print $textinput_width;?>;"><?php print $textinput_caption;?></th>
	<?php if ($show_additional_options) { ?>
									<th style="width: 10%;"><?php print get_text("API-Code");?></th>
									<th style="width: 12%;"><?php print get_text("Reporting channel");?></th>
	<?php } else { ?>
									<th style="width: 10%;"><?php print get_text("Sort group");?></th>
	<?php } ?>
									<th style="width: 10%;"><?php print get_text("Sort");?></th>
									<th style="width: 5%; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></th>
								</tr>
	<?php

		$query = "SELECT * " .
			"FROM `textblocks` " .
			"WHERE `type` = '" . $_GET['textblocks'] . "'" .
			"ORDER BY `sort` ASC;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) > 0) {
			while ($row = stripslashes_deep(db_fetch_array($result))) {
				$disabled = "";
				$readonly = "";
				if (in_array($row['id'], $configured_fixtexts)) {
					$disabled = " disabled";
					$readonly = " readonly";
				}
				$selected1 = $selected2 = $selected4 = $selected8 = $selected16 = $selected32 = $selected64 = $selected128 = "";
				if ($row['report_channels'] & 1) {
					$selected1 = " selected";
				}
				if ($row['report_channels'] & 2) {
					$selected2 = " selected";
				}
				if ($row['report_channels'] & 4) {
					$selected4 = " selected";
				}
				if ($row['report_channels'] & 8) {
					$selected8 = " selected";
				}
				if ($row['report_channels'] & 16) {
					$selected16 = " selected";
				}
				if ($row['report_channels'] & 32) {
					$selected32 = " selected";
				}
				if ($row['report_channels'] & 64) {
					$selected64 = " selected";
				}
				if ($row['report_channels'] & 128) {
					$selected128 = " selected";
				}
	?>
								<tr class="form-group">
									<td><input type="text" id="textblock[]" name="textblock[]" class="form-control" value="<?php print $row['text'];?>"<?php print $readonly;?>></input></td>
	<?php
					if ($show_additional_options) {
	?>
									<td><input type="text" id="apicode[]" name="apicode[]" class="form-control" value="<?php print $row['code'];?>"<?php print $readonly;?>></input></td>
									<td>
										<select id="reporting_channel_<?php print $row['id'];?>" name="reporting_channel_<?php print $row['id'];?>[]" multiple="multiple"<?php print $readonly;?>>
											<option value=1<?php print $selected1;?>><?php print get_text("Cellular phone");?></option>
											<option value=2<?php print $selected2;?>><?php print get_text("Email");?></option>
											<option value=4<?php print $selected4;?>><?php print get_text("Printer");?></option>
											<option value=8<?php print $selected8;?>><?php print get_text("Reporting channel 1");?></option>
											<option value=16<?php print $selected16;?>><?php print get_text("Reporting channel 2");?></option>
											<option value=32<?php print $selected32;?>><?php print get_text("Reporting channel 3");?></option>
											<option value=64<?php print $selected32;?>><?php print get_text("Reporting channel 4");?></option>
											<option value=128<?php print $selected32;?>><?php print get_text("Reporting channel 5");?></option>
										</select>
									</td>
									<script>

										$(document).ready(function() {
											$("#reporting_channel_<?php print $row['id'];?>").multiselect ({
												buttonWidth: "100%",
												nonSelectedText: "<?php print html_entity_decode(get_text("None selected"));?>",
												nSelectedText: "<?php print html_entity_decode(get_text("selected"));?>",
												allSelectedText: "<?php print html_entity_decode(get_text("All selected"));?>",
												numberDisplayed: 0
											});
										});

									</script>
	<?php 		} else { ?>
									<td><input type="text" id="group[]" name="group[]" class="form-control" value="<?php print $row['group'];?>"<?php print $readonly;?>></input></td>
	<?php 		} ?>
									<td><input type="text" id="sort[]" name="sort[]" class="form-control" value="<?php print $row['sort'];?>"<?php print $readonly;?>></input></td>
									<td style="text-align: center;" <?php if ($disabled != "") print get_help_text_str("not_deletable");?>>
										<input type="checkbox" id="delete_<?php print $row['id'];?>" name="delete_<?php print $row['id'];?>"<?php print $disabled;?>>
										<input type="hidden" id="textblocks_id[]" name="textblocks_id[]" value="<?php print $row['id'];?>">
									</td>
								</tr>
	<?php
			}
		} else {
	?>
									<tr class="form-group" style="height: 44px;">
										<th style="text-align: center;" colspan=4><?php print get_text("No data");?></th>
									</tr>
	<?php
		}
	?>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "captions_update":
	if (is_super()) {
		$message_str = "";
		$log_str = NULL;
		$updated_rows = 0;
		foreach ($_POST as $VarName => $VarValue) {
			if ($VarName != "function") {
				
				$query = "UPDATE `captions` " .
					"SET `repl` = " . db_real_escape_string($VarValue) . " " .
					"WHERE `id` = ". $VarName . ";";
				
				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$updated_rows++;
				}
			}
		}
		if ($updated_rows != 0) {
			$message_str .= get_text("Dataset captions updated") . ": " . $updated_rows . "<br>";
			$log_str .= get_text("Dataset captions updated") . ": " . $updated_rows . ", ";
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
			print get_text($message_str) . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "captions":
	if (is_super()) {
	?>
		<div id="main_container" class="container-fluid">
			<form id="captions" name="captions">
				<input type="hidden" id="function" name="function" value="captions_update">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Incident Add/Edit captions - enter revisions") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="document.captions.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="send_configuration_form('captions');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_captions");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-10">
						<div id="table_top" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr style="height: 44px;">
									<th><?php print get_text("Text");?></th>
									<th><?php print get_text("Tag");?></th>
								</tr>
	<?php

		$query = "SELECT * " .
			"FROM `captions` " .
			"ORDER BY `repl` ASC;";

		$result = db_query($query, __FILE__, __LINE__);
		$i = 1;
		while ($row =  stripslashes_deep(db_fetch_array($result))) {
			if (substr($row['capt'], 0, 1) != "_" ) {
				print "<tr class='form-group'><td>" . $i . "<input type='text' name='" . $row['id'] . "' class='form-control' size=100 value='" . trim($row['repl']) . "'></input></td><th>" . $row['capt'] . "</th></tr>\n";
				$i++;
			}
		}
	?>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "hints_update":
	if (is_super()) {
		$message_str = "";
		$log_str = NULL;
		$updated_rows = 0;
		foreach ($_POST as $VarName => $VarValue) {
			if ($VarName != "function") {

				$query = "UPDATE `hints` " .
					"SET `hint` = " . db_real_escape_string($VarValue) . " " .
					"WHERE `id` = " . $VarName . ";";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					$updated_rows++;
				}
			}
		}
		if ($updated_rows != 0) {
			$message_str .= get_text("Dataset hints updated") . ": " . $updated_rows . "<br>";
			$log_str .= get_text("Dataset hints updated") . ": " . $updated_rows . ", ";
		}
		if ($log_str != NULL) {
			do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text($log_str), 0, "", "", "");
			print get_text($message_str) . "<br>";
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "hints":
	if (is_super()) {
	?>
		<div id="main_container" class="container-fluid">
			<form id="hints" name="hints">
				<input type="hidden" id="function" name="function" value="hints_update">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Incident Add/Edit hints - enter revisions") . " - "  . get_variable("page_caption");?>
					</div>
				</div>		
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onClick="document.hints.reset();"><?php print get_text("Reset");?></button>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="send_configuration_form('hints');"><?php print get_text("Save");?></button>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("set_hints");?>');"><?php print get_text("Helptext");?></button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-10">
					<div id="table_top" class="panel panel-default" style="padding: 0px;">
						<table class="table table-striped table-condensed" style="text-align: left;">	
							<tr style="height: 44px;">
								<th><?php print get_text("Text");?></th>
								<th><?php print get_text("Tag");?></th>
							</tr>
	<?php

		$query = "SELECT * " .
			"FROM `hints` " .
			"ORDER BY `hint` ASC;";	

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			print "<tr name='" . $row['id'] . "' class='form-group'><td><textarea class='form-control' name='" . $row['id'] . "' cols=100 rows=2>" . trim($row['hint']) . "</textarea></td><th>" . $row['tag'] . "</th></tr>\n";
		}
	?>
							</table>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "optimize":
	if (is_super()) {
		db_query("OPTIMIZE TABLE ticket, action, user, settings", __FILE__, __LINE__);
		do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("Database optimization complete."), 0, "", "", "");
		print get_text("Database optimization complete.") . "<br>";
	}
	exit;
case "do_reset":
	if (is_super()) {
		if ((isset ($_POST['frm_random_captcha'])) && ($_POST['frm_input_captcha'] == $_POST['frm_random_captcha'])) {
			install(get_version(), $_POST['frm_locale'], $_POST['frm_option'], $_POST['frm_db_host'], $_POST['frm_db_dbname'], $_POST['frm_db_user'], $_POST['frm_db_password']);
			$first_start_str = "";
			switch ($_POST['frm_option']) {
			case "install":
				print "FIRST_START";
				break;
			case "reset_settings":
				do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("Reseted settings."), 0, "", "", "");
				print get_text("Settings reseted.") . "<br>";	
				break;
			case "write_credentials":
				do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, get_text("Reseted database-credentials only."), 0, "", "", "");
				print get_text("Reseted database-credentials only.") . "<br>";
				break;
			default:
			}
		} else {
			print get_text("Nothing to do!") . "<br>";
		}
	}
	exit;
case "reset":
	if (is_super()) {
		$charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$base = strlen($charset);
		$result = "";
		$microtime_array = array ();
		$microtime = explode(" ", microtime());
		$pseudo_randomtime = $microtime[1];
		while ($pseudo_randomtime >= $base){
			$i = (int) $pseudo_randomtime % $base;
			$result = $charset[$i] . $result;
			$pseudo_randomtime /= $base;
		}
		$captcha = substr($result, -5);
		$im = imagecreatetruecolor(155, 21);
		$red = imagecolorallocate($im, 0xFF, 0x00, 0x00);
		$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
		imagefilledrectangle($im, 0, 0, 154, 20, $red);
		imagefttext($im, 13, 0, 5, 16, $black, getcwd() . DIRECTORY_SEPARATOR . "fonts"  . DIRECTORY_SEPARATOR . "FreeMono.ttf", $captcha);
		ob_start();
		imagepng($im);
		$image = ob_get_contents();
		ob_end_clean();
	?>	
		<div id="main_container" class="container-fluid">
			<form id="frm_reset_db" name="frm_reset_db">
				<input type="hidden" id="function" name="function" value="do_reset">
				<input type="hidden" id="frm_random_captcha" name="frm_random_captcha" value="<?php print $captcha;?>">
				<div class="row infostring">
					<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Reset Database functions") . " - " . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="document.frm_reset_db.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" style="margin-top: 10px;" onclick="send_configuration_form('frm_reset_db');"><?php print get_text("Save");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("db_reset");?>');"><?php print get_text("Helptext");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-1"></div>
					<div class="col-md-8">
						<div class="panel panel-default">
							<div class="panel-heading" style="text-align: center; font-size: large;">
								<?php print get_text("Database Configuration");?>
							</div>
							<div class="panel-body" style="padding: 0;">
								<table class="table table-striped">
									<tr>
										<td><?php print get_text("Database");?>:</td>
										<td><input type="text" id="frm_db_dbname" name="frm_db_dbname" size="30" maxlength="255" value="<?php print $GLOBALS['db_name'];?>"></td>
									</tr>
									<tr>
										<td><?php print get_text("Username");?>: </td>
										<td><input type="text" id="frm_db_user" name="frm_db_user" size="30" maxlength="255" value="<?php print $GLOBALS['db_user'];?>"></td>
									</tr>
									<tr>
										<td><?php print get_text("Password");?>: </td>
										<td><input type="password" id="frm_db_password" name="frm_db_password" size="30" maxlength="255" value="<?php print $GLOBALS['db_password'];?>"></td>
									</tr>
									<tr>
										<td><?php print get_text("DB-Host");?>: </td>
										<td><input type="text" id="frm_db_host" name="frm_db_host" size="30" maxlength="255" value="<?php print $GLOBALS['db_host'];?>"></td>
									</tr>
									<tr>
										<td><?php print get_text("Localization");?>:</td>
										<td>
											<?php print get_locale_select_str(get_current_path("sql/"), get_variable("_locale"));?>
										</td>
									</tr>
									<tr>
										<td><?php print get_text("Install Option");?>:</td>
										<td>
											<label class="radio-inline">
												<input type="radio" id="frm_option1" name="frm_option" value="install"<?php if (((isset ($_POST['frm_option'])) && ($_POST['frm_option'] == "install"))) {print " checked";}?>>&nbsp;
													<?php print get_text("Install database tables new (drop tables if exist)");?>
											</label>
											<br>
											<label class="radio-inline">
												<input type="radio" id="frm_option2" name="frm_option" value="reset_settings"<?php if (((isset ($_POST['frm_option'])) && ($_POST['frm_option'] == "reset_settings")) ||
													(!isset ($_POST['frm_option']))) {print " checked";}?>>&nbsp;
												<?php print get_text("Reset settings (do not touch user data)");?>
											</label>
											<br>
											<label class="radio-inline">
												<input type="radio" id="frm_option3" name="frm_option" value="write_credentials"<?php if (((isset ($_POST['frm_option'])) &&
													($_POST['frm_option'] == "write_credentials"))) {print " checked";}?>>&nbsp;
													<?php print get_text("Write db-configuration file only");?>
											</label>
										</td>
									</tr>
									<tr>
										<td><?php print get_text("random text");?>:</td>
										<td><img src="data:image/png;base64,<?php print base64_encode($image);?>"></td>
									</tr>
										<td><?php print get_text("Confirm CAPTCHA");?>:</td>
										<td><input type="text" id="frm_input_captcha" name="frm_input_captcha"></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-2"></div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
case "do_update":
	$simulation = "";
	$simulated_str = "";
	if (isset ($_GET['simulate'])) {
		$simulation = $_GET['simulate'];
		if ($simulation == "true") {
			$simulated_str = get_text("[Simulation]") . " ";
		}
	}
	do_log($GLOBALS['LOG_CONFIGURATION_EDIT'], 0, 0, $simulated_str . get_text("Update started to version") . ": " . $_GET['version'], 0, "", "", "");

	$query_set_update_progress_time = "UPDATE `settings` " .
		"SET `value` = '" . $_GET['update_progress_time'] . "' " .
		"WHERE `name` = '_update_progress_time';";

	db_query($query_set_update_progress_time, __FILE__, __LINE__);

	$query_logout_other_users = "UPDATE `users` SET `session_id` = '', " .
		"`expires` = '2017-01-01 00:00:00', " .
		"`current_radio` = NULL, " .
		"`login_datetime` = '2017-01-01 00:00:00', " .
		"`login_address` = '0.0.0.0', " .
		"`browser` = '' " .
		"WHERE `id` != " . $_SESSION['user_id'] . ";";

	db_query($query_logout_other_users, __FILE__, __LINE__);
	$json_array = array ();
	if (file_put_contents(get_current_path("update/OpenTacticalDispatcher.zip"), fopen($_GET["zip_link"], "rb", false, FOPEN_CONTEXT)) == false) {
		$json_array = array (
			"result" => "error",
			"text" => "Download of zip-file failed.",
			"simulation" => $simulation
		);
		print json_encode($json_array);
		break;
	}

	if (file_put_contents(get_current_path("update/md5sum.txt"), fopen($_GET["md5_link"], "rb", false, FOPEN_CONTEXT)) == false) {
		$json_array = array (
			"result" => "error",
			"text" => "Download of md5sum-file failed.",
			"simulation" => $simulation
		);
		print json_encode($json_array);
		break;
	}
	if (md5(file_get_contents(get_current_path("update/OpenTacticalDispatcher.zip"))) == trim(file_get_contents(get_current_path("update/md5sum.txt")))) {
		copy(get_current_path("incs/db_credentials.inc.php"), get_current_path("db_credentials_old.inc.php"));
		copy(get_current_path("update/update.php"), get_current_path("update.php"));
		$json_array = array (
			"result" => "success",
			"text" => "Download finished.",
			"simulation" => $simulation
		);
	} else {
		$json_array = array (
			"result" => "error",
			"text" => "integrity check failed.",
			"simulation" => $simulation
		);
	}
	if ($simulation) {
		sleep($_GET['simulate_time']);
	}
	$auto_poll_settings = explode(",", get_variable("auto_poll"));
	$auto_poll_time_in_seconds = (1/10) * trim($auto_poll_settings[0]);
	sleep($auto_poll_time_in_seconds * 3);
	print json_encode($json_array);
	break;
case "updates":
	if (is_super()) {
		$new_updates = false;
		$next_update = -1;
		$current_version = -1;
		$release_notes_memory = $release_note_size = 1;
		$release_notes_download_time = $release_note_download_time = 0;
		$release_list_array = get_release_list();
		$release_list_array[$next_update][VERSION] = $release_list_array[$next_update][ZIP_LINK] = $release_list_array[$next_update][MD5SUM] = "";
		if (!(array_key_exists(1, $release_list_array)) || ($release_list_array[1][VERSION] != "false")) {
			foreach ($release_list_array as $key => $value) {
				if ($release_list_array[$key][VERSION] >= get_version()) {
					if ($release_list_array[$key][VERSION] > get_version()) {
						$new_updates = true;
						if ($next_update == -1) {
							$next_update = $key;
						}
					}
					if ($release_list_array[$key][VERSION] == get_version()) {
						if ($current_version == -1) {
							$current_version = $key;
						}
					}
					$release_notes_link = fopen($release_list_array[$key][RELEASE_TXT_LINK], "rb", false, FOPEN_CONTEXT);
					if (!$release_notes_link) {
						$release_list_array[$key][RELEASE_TXT] = get_text("No release-notes found.");
					} else {
						$release_note_download_time = microtime(true);
						$start_memory = memory_get_usage();
						$release_note_size = 0;
						$release_list_array[$key][RELEASE_TXT] = nl2br(strip_tags(stream_get_contents($release_notes_link)));
						$release_note_size = memory_get_usage() - $start_memory;
						fclose($release_notes_link);
						$release_note_download_time = microtime(true) - $release_note_download_time;
					}
				}
				$release_notes_download_time += $release_note_download_time;
				$release_notes_memory += $release_note_size;
			}
		}
		$update_download_time = ($release_notes_download_time/$release_notes_memory) * 3000000;
		$not_writable_array = array ();
		$not_writable_array = update_is_writable();
		if (!(isset ($not_writable_array[0]))) {
			if (file_exists(get_current_path("update/OpenTacticalDispatcher.zip"))) {
				unlink(get_current_path("update/OpenTacticalDispatcher.zip"));
			}
			if (file_exists(get_current_path("update/md5sum.txt"))) {
				unlink(get_current_path("update/md5sum.txt"));
			}
			if (file_exists(get_current_path("db_credentials_old.inc.php"))) {
				unlink(get_current_path("db_credentials_old.inc.php"));
			}
			if ((file_exists(get_current_path("install.php")) && !(get_working_in_development_environement()))) {
				unlink(get_current_path("install.php"));
			}
			if (file_exists(get_current_path("update.php"))) {
				unlink(get_current_path("update.php"));
			}
		}
		$auto_poll_settings = explode(",", get_variable("auto_poll"));
		$auto_poll_time_seconds = (1/10) * trim($auto_poll_settings[0]);
		$show_release_note = $current_version;
		if ($new_updates) {
			$show_release_note = $next_update;
		}
	?>
			<script>
				var current_release_note = <?php print $show_release_note;?> + 0;

				function show_release_note(note) {
					if (current_release_note != -1) {
						$("#release_note_" + current_release_note).css("display", "none");
						$("#release_note_" + note).css("display", "inline-block");
						current_release_note = note;
					}
				}

				var download_progressbar_time = 0;
				var download_progressbar_percent = 0;
				var unzip_progressbar_time = 0;
				var unzip_progressbar_percent = 0;
				var changes_progressbar_time = 0;
				var changes_progressbar_percent = 0;

				function do_progress() {
					if (
						($("#download_progressbar").hasClass("progress-bar-warning")) &&
						(
							($("#download_progressbar").css("display") == "block") ||
							($("#download_progressbar").css("display") == "inline")
						)
					) {
						if (download_progressbar_percent < 100) {
							download_progressbar_percent += Math.floor((100/download_progressbar_time)/10);
						} else {
							download_progressbar_percent = 100;
						}
						$("#download_progressbar").attr("aria-valuenow", download_progressbar_percent);
						$("#download_progressbar").css("width", download_progressbar_percent + "%");
					}
					if (
						($("#unzip_progressbar").hasClass("progress-bar-warning")) &&
						(
							($("#unzip_progressbar").css("display") == "block") ||
							($("#unzip_progressbar").css("display") == "inline")
						)
					) {
						if (
							($("#unzip_progressbar").hasClass("progress-bar-warning")) &&
							(
								($("#unzip_progressbar").css("display") == "block") ||
								($("#unzip_progressbar").css("display") == "inline")
							)
						) {
							if (unzip_progressbar_percent < 100) {
								unzip_progressbar_percent += Math.floor((100/unzip_progressbar_time)/10);
							} else {
								unzip_progressbar_percent = 100;
							}
							$("#unzip_progressbar").attr("aria-valuenow", unzip_progressbar_percent);
							$("#unzip_progressbar").css("width", unzip_progressbar_percent + "%");
						}
					}
					if (
						($("#changes_progressbar").hasClass("progress-bar-warning")) &&
						(
							($("#changes_progressbar").css("display") == "block") ||
							($("#changes_progressbar").css("display") == "inline")
						)
					) {
						if (
							($("#changes_progressbar").hasClass("progress-bar-warning")) &&
							(
								($("#changes_progressbar").css("display") == "block") ||
								($("#changes_progressbar").css("display") == "inline")
							)
						) {
							if (changes_progressbar_percent < 100) {
								changes_progressbar_percent += Math.floor((100/changes_progressbar_time)/10);
							} else {
								changes_progressbar_percent = 100;
							}
							$("#changes_progressbar").attr("aria-valuenow", changes_progressbar_percent);
							$("#changes_progressbar").css("width", changes_progressbar_percent + "%");
						}
					}
				}

				var progession_timer;
				function start_progessbars() {
					progession_timer = window.setInterval(do_progress, 100);
				}

				function stop_progessbars() {
					if (progession_timer) {
						window.clearInterval(progession_timer);
						download_progressbar_time = 0;
						download_progressbar_percent = 0;
						unzip_progressbar_time = 0;
						unzip_progressbar_percent = 0;
						changes_progressbar_time = 0;
						changes_progressbar_percent = 0;
					}
				}

				function do_update_progression_info_box(modus, type, time) {
					switch (modus) {
					case "show":
						$("#download_progressbar").removeClass();
						$("#download_progressbar").addClass("progress-bar progress-bar-warning");
						$("#download_progressbar").attr("aria-valuenow", 0);
						$("#download_progressbar").css("width", "0%");
						$("#download_progressbar").html("<?php print get_text("Downloading update");?>");
						$("#download_progressbar").css("display", "none");
						$("#unzip_progressbar").removeClass();
						$("#unzip_progressbar").addClass("progress-bar progress-bar-warning");
						$("#unzip_progressbar").attr("aria-valuenow", 0);
						$("#unzip_progressbar").css("width", "0%");
						$("#unzip_progressbar").html("<?php print get_text("Unpacking files");?>");
						$("#unzip_progressbar").css("display", "none");
						$("#changes_progressbar").removeClass();
						$("#changes_progressbar").addClass("progress-bar progress-bar-warning");
						$("#changes_progressbar").attr("aria-valuenow", 0);
						$("#changes_progressbar").css("width", "0%");
						$("#changes_progressbar").html("<?php print get_text("Make changes");?>");
						$("#changes_progressbar").css("display", "none");
						start_progessbars();
						$("#update_infobox").modal("show");
						break;
					case "download":
						switch (type) {
						case "start":
							$("#download_progressbar").css("display", "inline");
							download_progressbar_time = time;
							break;
						case "finish":
							$("#download_progressbar").removeClass("progress-bar-warning");
							$("#download_progressbar").addClass("progress-bar-success");
							$("#download_progressbar").attr("aria-valuenow", 100);
							$("#download_progressbar").css("width", "100%");
							$("#download_progressbar").html("<?php print get_text("Downloading update finished");?>");
							break;
						case "fail":
							$("#download_progressbar").removeClass("progress-bar-warning");
							$("#download_progressbar").removeClass("progress-bar-success");
							$("#download_progressbar").addClass("progress-bar-danger");
							$("#download_progressbar").attr("aria-valuenow", 100);
							$("#download_progressbar").css("width", "100%");
							$("#download_progressbar").html("<?php print get_text("Downloading update failed");?>");
							break;
						default:
						}
						break;
					case "unzip":
						switch (type) {
						case "start":
							$("#unzip_progressbar").css("display", "inline");
							unzip_progressbar_time = time;
							break;
						case "finish":
							$("#unzip_progressbar").removeClass("progress-bar-warning");
							$("#unzip_progressbar").addClass("progress-bar-success");
							$("#unzip_progressbar").attr("aria-valuenow", 100);
							$("#unzip_progressbar").css("width", "100%");
							$("#unzip_progressbar").html("<?php print get_text("Unpacking files finished");?>");
							break;
						case "fail":
							$("#unzip_progressbar").removeClass("progress-bar-warning");
							$("#unzip_progressbar").removeClass("progress-bar-success");
							$("#unzip_progressbar").addClass("progress-bar-danger");
							$("#unzip_progressbar").attr("aria-valuenow", 100);
							$("#unzip_progressbar").css("width", "100%");
							$("#unzip_progressbar").html("<?php print get_text("Unpacking files failed");?>");
							break;
						default:
						}
						break;
					case "changes":
						switch (type) {
						case "start":
							$("#changes_progressbar").css("display", "inline");
							changes_progressbar_time = time;
							break;
						case "finish":
							$("#changes_progressbar").removeClass("progress-bar-warning");
							$("#changes_progressbar").addClass("progress-bar-success");
							$("#changes_progressbar").attr("aria-valuenow", 100);
							$("#changes_progressbar").css("width", "100%");
							$("#changes_progressbar").html("<?php print get_text("Make changes finished");?>");
							break;
						case "fail":
							$("#changes_progressbar").removeClass("progress-bar-warning");
							$("#changes_progressbar").removeClass("progress-bar-success");
							$("#changes_progressbar").addClass("progress-bar-danger");
							$("#changes_progressbar").attr("aria-valuenow", 100);
							$("#changes_progressbar").css("width", "100%");
							$("#changes_progressbar").html("<?php print get_text("Make changes failed");?>");
							break;
						default:
						}
						break;
					case "hide":
						$("#update_infobox").modal("hide");
						stop_progessbars();
						break;
					default:
					}
				}

				function do_update(version, zip_link, md5_link) {
					var simulate = <?php print get_working_in_development_environement();?> + 0;
					var update_download_time = <?php print $update_download_time + ($auto_poll_time_seconds * 3);?> + 1;
					var unzip_time = 4;
					var changes_time = 3;
					var update_progress_time = update_download_time + unzip_time + changes_time;
					try {
						control_polling("stop");
					} catch (e) {
					}
					setTimeout(function() {
						var simulate_query_part_download = "";
						var simulate_query_part_unzip = "";
						var simulate_query_part_changes = "";
						if (simulate == 1) {
							simulate_query_part_download = "&simulate=true&simulate_time=" + update_download_time;
							simulate_query_part_unzip = "&simulate=true&simulate_time=" + unzip_time;
							simulate_query_part_changes = "&simulate=true&simulate_time=" + changes_time;
						}
						do_update_progression_info_box("show", "", 0);
						do_update_progression_info_box("download", "start", update_download_time);
						$.get("configuration.php?function=do_update&version=" + encodeURI(version) + "&zip_link=" + encodeURI(zip_link) + "&md5_link=" + encodeURI(md5_link) +
							"&update_progress_time=" + update_progress_time + simulate_query_part_download, function(data) {
						})
						.done(function(data) {
							var return_array = JSON.parse(data);
							if (return_array["result"].valueOf() == "success") {
								do_update_progression_info_box("download", "finish", update_download_time);
							} else {
								console.log(return_array["text"]);
								do_update_progression_info_box("download", "fail", update_download_time);
								return;
							}
							do_update_progression_info_box("unzip", "start", unzip_time);
							$.get("update.php?function=do_unzip" + simulate_query_part_unzip, function(data) {
							})
							.done(function(data) {
								var return_array = JSON.parse(data);
								if (return_array["result"].valueOf() == "success") {
									do_update_progression_info_box("unzip", "finish", unzip_time);
								} else {
									console.log(return_array["text"]);
									do_update_progression_info_box("unzip", "fail", unzip_time);
									return;
								}
								do_update_progression_info_box("changes", "start", changes_time);
								$.get("update.php?function=do_changes" + simulate_query_part_changes, function(data) {
								})
								.done(function(data) {
									var return_array = JSON.parse(data);
									if (return_array["result"].valueOf() == "success") {
										do_update_progression_info_box("changes", "finish", changes_time);
									} else {
										console.log(return_array["text"]);
										do_update_progression_info_box("changes", "fail", changes_time);
										return;
									}
									setTimeout(function() {	
										do_update_progression_info_box("hide", "", 0);
										setTimeout(function() {
											try {
												window.location.reload();
											} catch (e) {
											}
										}, 1000);
										setTimeout(function() {
											try {
												reload_window();
											} catch (e) {
											}
											try {
												control_polling("start");
											} catch (e) {
											}
										}, 1000);
									}, 2000);
								})
								.fail(function () {
									do_update_progression_info_box("changes", "fail", changes_time);
								});
							})
							.fail(function () {
								do_update_progression_info_box("unzip", "fail", unzip_time);
							});
						})
						.fail(function () {
							do_update_progression_info_box("download", "fail", update_download_time);
						});
					}, 1000);
				}

			</script>
			<div id="main_container" class="container-fluid">
				<form id="update" name="update">
					<div class="row infostring">
						<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
							<?php print get_text("Updates") . " - "  . get_variable("page_caption");?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-1">
							<div class="container-fluid" style="position: fixed;">
								<div class="row" style="margin-top: 10px;">
									<div class="col-md-12">
										<button type="button" class="btn btn-xs btn-default" onclick="goto_window('configuration.php');"><?php print get_text("Cancel");?></button>
									</div>
								</div>
								<div class="row" style="margin-top: 10px;">
									<div class="col-md-12">
										<button type="button" class="btn btn-xs btn-default" onClick="show_infobox('<?php print get_text("Helptext");?>', '<?php print get_help_text("Updates");?>');"><?php print get_text("Helptext");?></button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<div id="table_left_top" class="panel panel-default" style="padding: 0px;">
								<table class="table table-striped table-condensed" style="text-align: left; table-layout: fixed;">
									<tr>
										<th style="width: 70%;"><?php print get_text("Pending updates");?></th>
										<th style="width: 30%;"></th>
									</tr>
	<?php
		if (($release_list_array[0][VERSION] != null) && ($release_list_array[0][VERSION] != "false")) {
			if ($new_updates) {
				$class_str = " class=\"text_green_bold\"";
				foreach ($release_list_array as $key => $value) {
					if ($key >= $next_update) {
						print "<tr><td onclick=\"show_release_note(" .  $key . ");\"><span" . $class_str . ">OpenTacticalDispatcher-" .
							$value[VERSION] . "</span>&nbsp;&nbsp;&nbsp;<span style=\"color: #337ab7\">" . get_text("Show release notes") . "</span></td>" .
							"<td><a href=\"" . $value[ZIP_LINK] . "\" download=\"OpenTacticalDispatcher-" .
							$value[VERSION] . ".zip\">" . get_text("Download zip-file") . "</a></td></tr>";
						$class_str = " class=\"text_red_bold\"";
					}
				}
			} else {
				print "<tr><td colspan=2>" . get_text("No updates pending.") . "</td></tr>";
			}
			
		} else {
		print "<tr><td><span class=\"text_red_bold\">" . get_text("No connection to the update server.") . "</span></td>" . "<td></td></tr>";
		}
		$click_to_show_current_release_notes_str = "";
		if ($new_updates) {
			$click_to_show_current_release_notes_str = "&nbsp;&nbsp;&nbsp;<span style=\"color: #337ab7\">" . get_text("Show release notes") . "</span>";
		}
	?>
							</table>
						</div>
						<div id="table_left_bottom" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed text_black" style="text-align: left; table-layout: fixed;">
							
								<tr>
									<th style="width: 25%;"><?php print get_text("Current version") . ": ";?></th>
									<td onclick="show_release_note('<?php print $current_version;?>');" style="width: 75%;"><?php print get_version() . $click_to_show_current_release_notes_str;?></td>
								</tr>
	<?php
		if ($new_updates) {
	?>
								<tr>
									<th><?php print get_text("Next version") . ": ";?></th>
									<td><?php print $release_list_array[$next_update][VERSION] . "&nbsp;&nbsp;&nbsp;" . get_text("An update is only possible to the next version.");?></td>
								</tr>
	<?php
		}
	?>
								<tr>
									<th><?php print get_text("Update-Server") . ": ";?></th>
									<td<?php print get_title_str(get_variable("release_file"));?>><?php print get_variable("release_file");?></td>
								</tr>
								<tr>
									<td style="white-space: normal !important;" colspan=2>
										<?php print get_help_text("Update-hint", true);?>
									</td>
								</tr>
	<?php
		if (get_working_in_development_environement() && $new_updates) {
	?>
								<tr>
									<td class="text_red_bold" colspan=2><?php print get_text("Version control files still exist. Update is only simulated.");?></td>
								</tr>
	<?php
		}
		if (isset ($not_writable_array[0])) {
			$title_text = "";
			foreach ($not_writable_array as $value) {
				$title_text .= $value . ", ";
			}
			$title_text = substr($title_text, 0, -2);
	?>
								<tr>
									<td class="text_red_bold" colspan=2><?php print get_text("For an update, write permission is missing for") . ":";?></td>
								</tr>
								<tr>
									<td<?php print get_title_str($title_text);?> colspan=2>
	<?php
			$text = "";
			$info_text = "";
			$i = 1;
			foreach ($not_writable_array as $value) {
				$text .= $value . ", ";
				if ($i == 10) {
					$info_text = " " . get_text("and additional") . " " . (count($not_writable_array) - 10);
					break;
				}
				$i++;
			}
			$text = substr($text, 0, -2) . $info_text;
			print wordwrap($text, 100, "<br>", true);
	?>
									</td>
								</tr>
	<?php
		}
		$disabled_str = " disabled";
		if ((!(isset ($not_writable_array[0]))) && ($new_updates)) {
			$disabled_str = "";
		}
	?>
								<tr>
									<td style="text-align: center;" colspan=2>
										<button type="button" class="btn btn-xs btn-default" onClick="do_update('<?php print $release_list_array[$next_update][VERSION];?>', '<?php print $release_list_array[$next_update][ZIP_LINK];?>', '<?php print $release_list_array[$next_update][MD5SUM];?>');"<?php print $disabled_str;?>><?php print get_text("Start update");?></button>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-5">
						<div id="table_right" class="panel panel-default" style="padding: 0px;">
							<table class="table table-striped table-condensed" style="text-align: left;">
								<tr style="height: 44px;">
									<th><?php print get_text("Release notes");?></th>
								</tr>
							</table>
	<?php
		$display_str = "display: none; ";
		foreach ($release_list_array as $key => $value) {
			if (($key >= $next_update) || ($key == $current_version)) {
				if ($key == $show_release_note) {
					$display_str = "display: inline-block; ";
				}
	?>
								<div id="release_note_<?php print $key;?>" class="text_black" style="<?php print $display_str;?> width: 100%; height: 300px; max-height: 300px; overflow-y: scroll; padding-left: 10px; padding-right: 10px; padding-top: 5px;">
	<?php
				if ((isset ($value[RELEASE_TXT])) && ($value[RELEASE_TXT] != "")) {
					print $value[RELEASE_TXT];
				} else {
					print get_text("No release-notes found.");
				}
	?>
								</div>
	<?php
				$display_str = "display: none; ";
			}
		}
	?>
							</div>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</div>
			<div id="update_infobox" class="modal fade" role="dialog" aria-labelledby="myModalLabel" tabindex="-1">
				<div class="modal-dialog" style="width: 1000px;" role="document">
					<div class="modal-content">
						<div class="modal-header">
<!--							<button type="button" class="close" aria-label="Close" onclick="$('#update_infobox').modal('hide');"><span aria-hidden="true">&times;</span></button>	-->
							<h4 class="modal-title">
								<div class="infobox-head"><?php print get_text("Update progress");?></div>
							</h4>
						</div>
						<div id="update_infobox_body" class="modal-body text_black">
							<div class="progress">
								<div id="download_progressbar" class="progress-bar progress-bar-warning" style="width: 70%;" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
									<?php print get_text("Downloading update");?>
								</div>
							</div>
							<div class="progress">
								<div id="unzip_progressbar" class="progress-bar progress-bar-warning" style="width: 70%;" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
									<?php print get_text("Unpacking files");?>
								</div>
							</div>
							<div class="progress">
								<div id="changes_progressbar" class="progress-bar progress-bar-warning" style="width: 10%;" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
									<?php print get_text("Change database");?>
								</div>
							</div>
						</div>
						<div class="modal-footer">
<!--						<button id="update_cancel_button" type="button" class="btn btn-default" onclick="$('#update_infobox').modal('hide');"><?php print get_text("Cancel");?></button>	-->
						</div>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
	<?php
	}
	break;
default:
}
switch ($function) {
case "profile":
case "user_add":
case "user_edit":
case "audio":
case "settings":
case "incident_numbers":
case "api":
case "facilities_status_reset":
case "facility_types":
case "facility_status":
case "unit_status_reset":
case "unit_types":
case "unit_status":
case "presentation_tab":
case "presentation_list":
case "regions":
case "cleanse_regions":
case "reset_regions":
case "incident_types":
case "textblocks":
case "captions":
case "hints":
case "reset":
case "do_update":
case "updates":
	break;
default:
	set_session_expire_time("on");
	$top_notice_str = "";
	$top_notice_head = "";
	if (!empty ($_GET['top_notice'])) {
		$top_notice_str .= $_GET['top_notice'] . "<br>";
	}
	if (!empty ($_POST['top_notice'])) {
		$top_notice_str .= $_POST['top_notice'] . "<br>";
	}
	if ($top_notice_str != "") {
		$top_notice_head = get_text("Configuration");
	}
	?>
		<script>
			show_infobox("<?php print $top_notice_head;?>", "<?php print $top_notice_str;?>");
		</script>
		<div id="main_container" class="container-fluid">
			<div class="row infostring">
				<div id="infostring_middle" class="col-md-12" style="text-align: center; margin-bottom: 10px;">
					<?php print get_text("Configuration") . " - " . get_variable("page_caption");?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-1">
					<div class="container-fluid" style="position: fixed;">
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="goto_window('situation.php?screen_id=' + new_infos_array['screen']['screen_id']);"><?php print get_text("Cancel");?></button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-10">
<!--=========================================================================================================================================-->
					<h5>
						<li>
							<?php print get_text("Users");?>
						</li>
					</h5>
	<?php
	if (is_super() || is_admin() || is_operator()) {
		show_userlist();
	}
	?>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 10px;">
						<div class="row">
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=profile');">
											<?php print get_text("Edit My Profile");?>
										</a>
									</li>
								</ul>
							</div>
	<?php
	if (is_super() || is_admin()) {
	?>
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=user_add');">
											<?php print get_text("Add user");?>
										</a>
									</li>
								</ul>
							</div>
	<?php
	}
	if (is_super()) {
	?>
						</div>
						<div class="row">
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a onclick="location.href='export.php?do_export=user';" style="white-space: nowrap;">
											<?php print get_text("Export");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-6" style="padding: 4px;">	
								<form action="import.php" method="post" enctype="multipart/form-data">
									<input type="file" id="users_upload" name="file" class="file" data-show-preview="false">
									<input type="hidden" name="function" value="users">
									<script>
										$("#users_upload").fileinput({
											language: "<?php print $language;?>",
											allowedFileExtensions: ["csv"]
										});
									</script>
								</form>
							</div>
	<?php
				}
	?>
						</div>
					</div>
<!--=========================================================================================================================================-->
					<h5>
						<li>
							<?php print get_text("General");?>
						</li>
					</h5>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 0px;">
						<div class="row">
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_audio");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=audio');">
											<?php print get_text("Alarm audio files");?>
										</a>
									</li>
								</ul>
							</div>
	<?php
	if (is_super()) {
	?>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_settings");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=settings');">
											<?php print get_text("Edit Settings");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_incident_names");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=incident_numbers');">
											<?php print get_text("Incident Numbers");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_api_settings");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=api');">
											<?php print get_text("Application Interface");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_fixtexts");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=textblocks&textblocks=fixtext');">
											<?php print get_text("Message fixtexts");?>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 0px;">
						<div class="row">
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a onclick="location.href='export.php?do_export=settings';" style="white-space: nowrap;">
											<?php print get_text("Export");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-6" style="padding: 4px;">
								<form action="import.php" method="post" enctype="multipart/form-data">
									<input type="file" id="settings_upload" name="file" class="file" data-show-preview="false">
									<input type="hidden" name="function" value="settings">
									<script>
										$("#settings_upload").fileinput({
											language: "<?php print $language;?>",
											allowedFileExtensions: ["csv"]
										});
									</script>
								</form>
							</div>
	<?php
	}
	?>
						</div>
					</div>
<!--=========================================================================================================================================-->
	<?php
	if (is_admin() || is_super()) {	
	?>
					<h5>
						<li>
							<?php print get_text("Facilities Configuration");?>
						</li>
					</h5>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 10px;">
						<div class="row">
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_facilities_common_status");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=facilities_status_reset');">
											<?php print get_text("Set facilities status to a common setting");?>
										</a>
									</li>
								</ul>
							</div>
	<?php
		if (is_super()) {
	?>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_facilities_category");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=facility_types');">
											<?php print get_text("Facility type");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_facilities_status_value");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=facility_status');">
											<?php print get_text("Facility Status");?>
										</a>
									</li>
								</ul>
							</div>
	<?php
		}
		if (is_super() || get_admin_can_config_presentation(get_tab_list($GLOBALS['TYPE_FACILITY']))) {
	?>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("facility_presentation");?> class="nav nav-pills" class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=presentation_list&type_id=<?php print $GLOBALS['TYPE_FACILITY'];?>');">
											<?php print (get_text("Presentation"));?>
										</a>
									</li>
								</ul>
							</div>
	<?php
		}
		if (is_super()) {
	?>
						</div>
						<div class="row">
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a onclick="location.href='export.php?do_export=facilities';" style="white-space: nowrap;">
											<?php print get_text("Export");?>
										</a>
									</li>
								</ul>
							</div>
							<div<?php print get_help_text_str("set_facilities_status_after_import");?> class="col-xs-6" style="padding: 4px;">	
								<form method="post" action="import.php" enctype="multipart/form-data">
									<input type="file" id="facilties_upload" name="file" class="file" data-show-preview="false">
									<input type="hidden" name="function" value="facilities">
									<script>
										$("#facilties_upload").fileinput({
											language: "<?php print $language;?>",
											allowedFileExtensions: ["csv"]
										});
									</script>
								</form>
							</div>
	<?php
		}
		if (is_admin() || is_super()) {	
	?>
						</div>
					</div>
	<?php
		}
	}
	?>
<!--=========================================================================================================================================-->
	<?php
	if (is_admin() || is_super()) {
	?>
					<h5>
						<li>
							<?php print get_text("Units Configuration");?>
						</li>
					</h5>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 10px;">
						<div class="row">
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_units_common_status");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=unit_status_reset');">
											<?php print get_text("Set units status to a common setting");?>
										</a>
									</li>
								</ul>
							</div>
	<?php
		if (is_super()) {
	?>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_units_category");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=unit_types');">
											<?php print get_text("Unit type");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_units_status_value");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=unit_status');">
											<?php print get_text("Unit status");?>
										</a>
									</li>
								</ul>
							</div>
	<?php
		}
		if (is_super() || get_admin_can_config_presentation(get_tab_list($GLOBALS['TYPE_UNIT']))) {
	?>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("unit_presentation");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=presentation_list&type_id=<?php print $GLOBALS['TYPE_UNIT'];?>');">	
											<?php print (get_text("Presentation"));?>
										</a>
									</li>
								</ul>
							</div>
	<?php
		}
		if (is_super()) {
	?>
						</div>
						<div class="row">
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a onclick="location.href='export.php?do_export=units';" style="white-space: nowrap;">
											<?php print get_text("Export");?>
										</a>
									</li>
								</ul>
							</div>
							<div<?php print get_help_text_str("set_units_status_after_import");?> class="col-xs-6" style="padding: 4px;">	
								<form action="import.php" method="post" enctype="multipart/form-data">
									<input type="file" id="units_upload" name="file" class="file" data-show-preview="false">
									<input type="hidden" name="function" value="units">
									<script>
										$("#units_upload").fileinput({
											language: "<?php print $language;?>",
											allowedFileExtensions: ["csv"]
										});
									</script>
								</form>
							</div>
	<?php
		}
		if (is_admin() || is_super()) {	
	?>
						</div>
					</div>
	<?php
		}
	}
	?>
<!--=========================================================================================================================================
	<?php
	if (is_super() && false) {
	?>
					<h5>
						<li>
							<?php print get_text("Regions");?>
						</li>
					</h5>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 10px;">
						<div class="row">
							<div class="col-xs-1">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a href="<?php print basename(__FILE__);?>?function=regions" style="white-space: nowrap;">
											<?php print get_text("Regions");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-1">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a href="<?php print basename(__FILE__);?>?function=cleanse_regions" style="white-space: nowrap;">
											<?php print get_text("List and Cleanse Region Allocations");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-1">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a href="<?php print basename(__FILE__);?>?function=reset_regions" style="white-space: nowrap;">
											<?php print get_text("Reset");?>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
	<?php
	}
	?>
    =========================================================================================================================================-->
	<?php
	if (is_super()) {
		$default_incident_types_file = "default_incident_types." . get_variable("_locale") . ".csv";
		$default_incident_types = get_default_incident_types(get_current_path("sql/" . $default_incident_types_file));
		$default_incident_types_display_str = " style='display: none;'";
		if ($default_incident_types != "") {
			$default_incident_types_display_str = " style='display: inline;'";
		}
	?>
					<h5>
						<li>
							<?php print get_text("Incident types");?>
						</li>
					</h5>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 10px;">
						<div class="row">
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_incident_types");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=incident_types');">
											<?php print get_text("Incident types");?>
										</a>
									</li>
								</ul>
							</div>
							<div<?php print $default_incident_types_display_str . get_help_text_str("set_incident_types_default");?> class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a onclick="show_default_import_infobox('default-incident-types', '<?php print $default_incident_types_file;?>')";">
											<?php print get_text("Default incident types");?>
										</a>
									</li>
								</ul>
							</div>
						</div>
						<div id="default-incident-types_head" style="display: none;">
							<?php print get_text("Install default incident types");?>
						</div>
						<div id="default-incident-types_content" style="display: none;">
							<?php print $default_incident_types;?>
						</div>
						<div class="row">
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a onclick="location.href='export.php?do_export=nature';" style="white-space: nowrap;">
											<?php print get_text("Export");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-6" style="padding: 4px;">
								<form action="import.php" method="post" enctype="multipart/form-data">
									<input type="file" id="incident-types_upload_file" name="file" class="file" data-show-preview="false">
									<input type="hidden" name="function" value="incident-types">
									<script>
										$("#incident-types_upload_file").fileinput({
											language: "<?php print $language;?>",
											allowedFileExtensions: ["csv"]
										});
									</script>
								</form>
							</div>
						</div>
					</div>
	<?php
	}
	?>
<!--=========================================================================================================================================-->
	<?php
	if (is_super()) {
		$default_textblocks_file = "default_textblocks." . get_variable("_locale") . ".csv";
		$default_textblocks = get_default_textblocks(get_current_path("sql/" . $default_textblocks_file));
		$default_textblocks_display_str = " style='display: none;'";
		if ($default_textblocks != "") {
			$default_textblocks_display_str = " style='display: inline;'";
		}
	?>
					<h5>
						<li>
							<?php print get_text("Textblocks");?>
						</li>
					</h5>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 10px;">
						<div class="row">
							<div<?php print get_help_text_str("set_textblocks");?> class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=textblocks&textblocks=synopsis');">
											<?php print get_text("Textblocks synopsis");?>
										</a>
									</li>
								</ul>
							</div>
							<div<?php print get_help_text_str("set_textblocks");?> class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=textblocks&textblocks=description');">
											<?php print get_text("Textblocks description");?>
										</a>
									</li>
								</ul>
							</div>
							<div<?php print get_help_text_str("set_textblocks");?> class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=textblocks&textblocks=action');">
											<?php print get_text("Textblocks action");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=textblocks&textblocks=assign');">
											<?php print get_text("Textblocks assign");?>
										</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="row">
							<div<?php print get_help_text_str("set_textblocks");?> class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=textblocks&textblocks=close');">
											<?php print get_text("Textblocks incident close");?>
										</a>
									</li>
								</ul>
							</div>
							<div<?php print get_help_text_str("set_textblocks");?> class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=textblocks&textblocks=log');">
											<?php print get_text("Textblocks log");?>
										</a>
									</li>
								</ul>
							</div>
							<div<?php print get_help_text_str("set_textblocks");?> class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=textblocks&textblocks=message');">
											<?php print get_text("Textblocks message");?>
										</a>
									</li>
								</ul>
							</div>
							<div<?php print $default_textblocks_display_str . get_help_text_str("set_textblocks_default");?> class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a onclick="show_default_import_infobox('default-textblocks', '<?php print $default_textblocks_file;?>')";">
											<?php print get_text("Default textblocks");?>
										</a>
									</li>
								</ul>
							</div>
						</div>
						<div id="default-textblocks_head" style="display: none;">
							<?php print get_text("Install default textblocks");?>
						</div>
						<div id="default-textblocks_content" style="display: none;">
							<?php print $default_textblocks;?>
						</div>
						<div class="row">
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a onclick="location.href='export.php?do_export=textblocks';" style="white-space: nowrap;">
											<?php print get_text("Export");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-6" style="padding: 4px;">
								<form method="post" action="import.php" enctype="multipart/form-data">	
									<input type="file" id="textblocks_upload_file" name="file" class="file" data-show-preview="false">
									<input type="hidden" name="function" value="textblocks">
									<script>
										$("#textblocks_upload_file").fileinput({
											language: "<?php print $language;?>",
											allowedFileExtensions: ["csv"]
										});
									</script>
								</form>
							</div>
						</div>
					</div>
	<?php
	}
	?>
<!--=========================================================================================================================================-->
	<?php
	if (is_super()) {
	?>
					<h5>
						<li>
							<?php print get_text("Captions and Hints");?>
						</li>
					</h5>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 10px;">
						<div class="row">
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_captions");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=captions');">
											<?php print get_text("Captions");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("set_hints");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=hints');">
											<?php print get_text("Hints");?>
										</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-2">
								<ul class="nav nav-pills">
									<li role="presentation">
										<a onclick="location.href='export.php?do_export=captions';" style="white-space: nowrap;">
											<?php print get_text("Export");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-6" style="padding: 4px;">	
								<form method="post" action="import.php" enctype="multipart/form-data">	
									<input type="file" id="captions_hints_upload" name="file" class="file" data-show-preview="false">
								<input type="hidden" name="function" value="captions">
									<script>
										$("#captions_hints_upload").fileinput({
											language: "<?php print $language;?>",
											allowedFileExtensions: ["csv"]
										});
									</script>
								</form>
							</div>
						</div>
					</div>
	<?php
	}
	?>
<!--=========================================================================================================================================-->
	<?php
	if (is_super()) {
	?>
					<h5>
						<li>
							<?php print get_text("Database functions and updates");?>
						</li>
					</h5>
					<div class="container-fluid" style="padding-left: 0px; margin-bottom: 10px;">
						<div class="row">
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("db_optimize");?> class="nav nav-pills">
									<li role="presentation">	
										<form id="do_optimize" name="do_optimize">
											<input type="hidden" id="function" name="function" value="optimize">
											<a style="white-space: nowrap;" onclick="send_configuration_form('do_optimize');">
												<?php print get_text("Optimize Database");?>
											</a>
										</form>
									</li>
								</ul>
							</div>
							<div class="col-xs-2">
								<ul<?php print get_help_text_str("db_reset");?> class="nav nav-pills">
									<li role="presentation">
										<a style="white-space: nowrap;" onclick="goto_window('configuration.php?function=reset');">
											<?php print get_text("Reset Database");?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-xs-2">
								<ul <?php print get_help_text_str("Updates");?> class="nav nav-pills">
									<li role="presentation" onclick="$('#update_button').prop('disabled', true); $('#update_button').html('<?php print get_text("Wait");?>');">
										<a id="update_button" style="white-space: nowrap;" onclick="goto_window('configuration.php?function=updates');">
											<?php print get_text("Updates");?>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
	<?php
	}
	?>
<!--=========================================================================================================================================-->
					<h5>
						<li>
							<?php print get_text("System Summary");?>
						</li>
					</h5>
					<?php show_stats();?>
<!--=========================================================================================================================================-->
				</div>
				<div class="col-md-1"></div>
			</div>
		</div>
	</body>
</html>
<?php
}
?>