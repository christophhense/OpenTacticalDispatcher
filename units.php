<?php
error_reporting(E_ALL);
session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/units.inc.php");
do_login(basename(__FILE__));

$datetime_now = mysql_datetime();
$caption = "";
$function = "";

if (isset ($_POST['function']) && (is_admin() || is_super())) {
	$function = $_POST['function'];
}
if (isset ($_POST['frm_remove']) && ($_POST['frm_remove'] == "true") && (is_admin() || is_super())) {
	$function = "delete";
}
if (isset($_POST['frm_id'])) {

	$query = "SELECT `admin_only` " .
		"FROM `units` " .
		"WHERE `id` = " . $_POST['frm_id'] . ";";

	$result	= db_query($query, __FILE__, __LINE__);
	$row = db_fetch_assoc($result);
	if ((($row['admin_only'] == 1) && !(is_super())) && ((is_admin()) && !($function == "add"))) {
		$function = "";
	}
	unset($row);
}
switch ($function) {
case "insert":
	set_session_expire_time();
	if (!is_super()) {
		$_POST['frm_adminperms'] = 0;
	}
	$new_id = insert_into_units($_POST['frm_name'], $_POST['frm_handle'], $_POST['frm_smsg_id'], $_POST['frm_phone'],
		$_POST['frm_unit_email'], $_POST['frm_type'], $_POST['frm_un_status_id'], $_POST['frm_multi'],
		$_POST['frm_mobile'], 0, $_POST['frm_guard_house'], $_POST['frm_descr'],
		$_POST['frm_capab'], $_POST['frm_contact_name'], $_POST['frm_adminperms'], $_POST['frm_icon_url'],
		$_POST['frm_lat'], $_POST['frm_lng'], $datetime_now, $datetime_now,
		$_SESSION['user_id'], $datetime_now);
	foreach ($_POST['frm_group'] as $grp_val) {
		insert_into_allocates($grp_val, $GLOBALS['TYPE_UNIT'], $new_id, $_SESSION['user_id'], $datetime_now);
	}
	do_log($GLOBALS['LOG_UNIT_ADD'], 0, $new_id, get_unit_edit_log_text("add", $new_id, $_POST, ""));
	$caption = get_text("Saved");
	break;
case "update":
	set_session_expire_time();

	$query = "SELECT * FROM `units` WHERE `id`= " . $_POST['frm_id'] . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$old_data = stripslashes_deep(db_fetch_assoc($result));
	$admin_only_query_str = "";
	if (isset ($_POST['frm_adminperms'])) {
		$admin_only_query_str = "`admin_only` = " . $_POST['frm_adminperms'] . ", ";
	} else {
		$_POST['frm_adminperms'] = $old_data['admin_only'];
	}
	if (empty ($_POST['frm_lat'])) {
		$lat = "0.999999";
	} else {
		$lat = $_POST['frm_lat'];
	}
	if (empty ($_POST['frm_lng'])) {
		$lng = "0.999999";
	} else {
		$lng = $_POST['frm_lng'];
	}
	$status_updated = $datetime_now;
	if ($_POST['frm_status_update'] != 1) {
		$status_updated = $_POST['frm_status_updated'];
	}
	$curr_groups = $_POST['frm_exist_groups'];
	$groups = $_POST['frm_exist_groups'];
	if ($_POST['frm_group']) {
		$groups = ", " . implode(',', $_POST['frm_group']) . ",";
	}
	if (isset ($_POST['frm_un_status_id'])) {
		$unit_status_id = $_POST['frm_un_status_id'];
	} else {
		$unit_status_id = $_POST['frm_un_status_last'];
	}

	$query = "UPDATE `units` SET " .
		"`name` = " . 					quote_smart(trim($_POST['frm_name'])) . ", " .
		"`guard_house_id` = " . 		quote_smart(trim($_POST['frm_guard_house'])) . ", " .
		"`unit_phone` = " . 			quote_smart(trim($_POST['frm_phone'])) . ", " .
		"`handle` = " . 				quote_smart(trim($_POST['frm_handle'])) . ", " .
		"`icon_url` = " . 				quote_smart(trim($_POST['frm_icon_url'])) . ", " .
		"`description` = " . 			quote_smart(trim($_POST['frm_descr'])) . ", " .
		"`capabilities` = " . 			quote_smart(trim($_POST['frm_capab'])) . ", " .
		"`unit_status_id` = " . 		$unit_status_id . ", " .
		"`mobile` = " . 				quote_smart(trim($_POST['frm_mobile'])) . ", " .
		"`multi` = " . 					quote_smart(trim($_POST['frm_multi'])) . ", " .
		"`lat` = " . 					floatval($lat) . ", " .
		"`lng` = " . 					floatval($lng) . ", " .
		"`contact_name` = " . 			quote_smart(trim($_POST['frm_contact_name'])) . ", " .
		$admin_only_query_str .
		"`unit_email` = " .	 			quote_smart(trim($_POST['frm_unit_email'])) . ", " .
		"`remote_data_services` = " . 	quote_smart(trim($_POST['frm_smsg_id'])) . ", " .
		"`type` = " . 					$_POST['frm_type'] . ", " .
		"`user_id` = " . 				$_SESSION['user_id'] . ", " .
		"`updated` = " . 				quote_smart(trim($datetime_now)) . ", " .
		"`status_updated` = " . 		quote_smart(trim($status_updated)) . " " .
		"WHERE `id` = " . 				$_POST['frm_id'] . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$list = $_POST['frm_exist_groups'];
	$ex_grps = explode("," ,$list);
	if ($curr_groups != $groups) {
		foreach ($_POST['frm_group'] as $posted_grp) {
			if (!in_array($posted_grp, $ex_grps)) {	
				insert_into_allocates($posted_grp, $GLOBALS['TYPE_UNIT'], $_POST['frm_id'], $_SESSION['user_id'], $datetime_now);
			}
		}
		foreach ($ex_grps as $existing_grps) {
			if (!in_array($existing_grps, $_POST['frm_group'])) {
				if (empty ($existing_grps)) {
					$existing_grps = 0;
				}

				$query  = "DELETE FROM `allocates` " .
					"WHERE `type` = " . $GLOBALS['TYPE_UNIT'] . " " .
					"AND `group` = " . $existing_grps . " " .
					"AND `resource_id` = " . $_POST['frm_id'] . ";";

				db_query($query, __FILE__, __LINE__);
			}
		}
	}
	$caption = get_text("Saved");
	do_log($GLOBALS['LOG_UNIT_CHANGE'], 0, $_POST['frm_id'], get_unit_edit_log_text("update", $_POST['frm_id'], $_POST, $old_data));
	if (!empty ($_POST['frm_status_update'])) {

		$query_un_status = "SELECT `status_name`, " .
			"`description` " .
			"FROM `unit_status` " .
			"WHERE `id` = " . $unit_status_id . ";";

		$result_un_status = db_query($query_un_status, __FILE__, __LINE__);
		$row_un_status = stripslashes_deep(db_fetch_assoc($result_un_status));
		$un_status_upd_val = $row_un_status['status_name'] . ", " . $row_un_status['description'];
		do_log($GLOBALS['LOG_UNIT_STATUS'], 0, $_POST['frm_id'], $un_status_upd_val);
	}
	break;
case "delete":
	set_session_expire_time();

	$query = "SELECT * FROM `units` WHERE `id`= " . $_POST['frm_id'] . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$old_data = stripslashes_deep(db_fetch_assoc($result));

	$query_assigns	= "SELECT * " .
		"FROM `assigns` " .
		"WHERE `unit_id` = " . $_POST['frm_id'] . " " .
		"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00');";

	$result_assigns = db_query($query_assigns, __FILE__, __LINE__);
	$count_assigns = db_affected_rows($result_assigns);
	unset ($result_assigns);
	if ($count_assigns == 0) {

		$query = "DELETE FROM `allocates` " .
			"WHERE `resource_id` = " . $_POST['frm_id'] . " " .
			"AND `type` = " . $GLOBALS['TYPE_UNIT'] . ";";

		$result = db_query($query, __FILE__, __LINE__);
		$caption = get_text("Deleted");
		do_log($GLOBALS['LOG_UNIT_DELETED'], 0, $_POST['frm_id'], get_unit_edit_log_text("delete", $_POST['frm_id'], $_POST, $old_data));
	}
	break;
	default:
}
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
switch ($function) {
case "table_left":
case "table_right":
	break;
case "printers":
	$result = "";
	if (DIRECTORY_SEPARATOR !== "\\") {
		require_once ("./lib/phpprinttip/php_classes/CupsPrintIPP.php");
		$ipp = new CupsPrintIPP;
		$ipp->setUserName("foo bar");
		$ipp->debug_level = 3; // Debugging very verbose
		$ipp->setLog('/tmp/printipp','file',3); // logging very verbose
		$ipp->getPrinters();
		$result = $ipp->available_printers;
		unset ($ipp);
	}
	print json_encode($result);
	break;
case "add":
case "edit":
default:
	set_session_expire_time();
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
			try {
				var changes_data ='{"type":"div","item":"script","action":"<?php print basename(__FILE__);?>"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
				var changes_data ='{"type":"button","item":"units","action":"highlight"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
			} catch(e) {
			}

			function printers() {
				$.get("units.php?function=printers").done(function(data) {
					var get_infos_array = JSON.parse(data);
					var message = "";
					if (get_infos_array.length > 0) {
						for (var i = 0; i < get_infos_array.length; i++) {
							message += "<div style='font-weight: bold;' onclick='hide_infobox(\"" + get_infos_array[i] + "\");'>" + get_infos_array[i] + "</div>";
						}
						show_infobox("<?php print get_text("Select printer");?>", message, "select", set_printer);
					} else {
						message += "<div><?php print get_text("No printers found!");?></div>";
						show_infobox("<?php print get_text("Select printer");?>", message);
					}
				});
			}

			function set_printer(printer_url) {
				if (printer_url != false) {
					var separator = ($("#frm_smsg_id").html().trim().length > 0)? ", " : "";
					var new_addresses = $("#frm_smsg_id").html() + separator + "PRINTER:" + printer_url;
					$("#frm_smsg_id").focus();
					set_cursor_position(frm_smsg_id, $("#frm_smsg_id").html().length);
					$("#frm_smsg_id").html(new_addresses);	
					set_cursor_position(frm_smsg_id, $("#frm_smsg_id").html().length);
				}
			}

			function do_remove(result) {
				if (result == true) {
					edit_form.submit();
				}
			}

			function validate(theForm) {
				if (theForm.frm_remove) {
					if (theForm.frm_remove.checked) {
						show_infobox("<?php print get_text("Please confirm removing");?>", false, theForm.frm_handle.value, do_remove);
						return;
					}
				}
				theForm.frm_mobile.value = (theForm.frm_mob_disp.checked)? 1 : 0;
				var errmsg = "";
				if (theForm.frm_handle.value.trim()=="") {
					errmsg += "<?php print get_text("Unit HANDLE is required.");?><br>";
				}
				if (theForm.frm_name.value.trim()=="") {
					errmsg += "<?php print get_text("Unit NAME is required.");?><br>";
				}
				if (theForm.frm_type.options[theForm.frm_type.selectedIndex].value==0) {
					errmsg += "<?php print get_text("Unit type selection is required.");?><br>";
				}	
				if (theForm.frm_un_status_id.options[theForm.frm_un_status_id.selectedIndex].value==0) {
					errmsg += "<?php print get_text("Units STATUS is required.");?><br>";
				}
				if (errmsg != "") {
					show_infobox("<?php print get_text("Please correct the following and re-submit");?>", errmsg);
					return false;
				} else {
					return true;
				}
			}

			function submit_form(form_name) {
				if (validate(form_name)) {
					form_name.submit();
				}
			}

			function copy_unit() {
				$("#function").val("add");
				var changes_data ='{"type":"message","item":"success","action":"<?php print get_text("Copied");?>"}';
				window.parent.navigationbar.postMessage(changes_data, window.location.origin);
				$("#edit_form").submit();
			}

			function save_and_copy_unit(add) {
				if (add) {
					if (validate(add_form)) {
						$.post("units.php", $("#add_form").serialize(), function(data) {
						})
						.done(function() {
							$("#function").val("add");
							var changes_data ='{"type":"message","item":"success","action":"<?php print get_text("Saved and copied");?>"}';
							window.parent.navigationbar.postMessage(changes_data, window.location.origin);
							$("#add_form").submit();
						})
						.fail(function() {
							alert("error");
						});	
					}
				} else {
					if (validate(edit_form)) {
						$.post("units.php", $("#edit_form").serialize(), function(data) {
						})
						.done(function() {
							$("#function").val("add");
							var changes_data ='{"type":"message","item":"success","action":"<?php print get_text("Saved and copied");?>"}';
							window.parent.navigationbar.postMessage(changes_data, window.location.origin);
							$("#edit_form").submit();
						})
						.fail(function() {
							alert("error");
						});
					}
				}
			}

			function do_remove_unit(result) {
				if (result == true) {
					$("#frm_remove").val("true");
					$("#edit_form").submit();
				}
			}

			function remove_unit() {
				show_infobox("<?php print get_text("Please confirm removing");?>", false, "", do_remove_unit);
				return;
			}

		</script>
	<?php
}
$reported_by_phone_settings = explode(",", get_variable("reported_by_phone"));
$help_text_phone = get_help_text("_ResPhon", true);
if (trim($reported_by_phone_settings[4]) == 1) {
	$help_text_phone .= " " . get_text("Selected for transfer in the Callback number field on the New-ticket and Edit-ticket page.");
}
$help_text_phone = get_title_str($help_text_phone);
switch ($function) {
case "sort":
	if (isset ($_GET['order'])) {
		$_SESSION['units_sort_order'] = $_GET['order'];
	} else {
		if (empty ($_SESSION['units_sort_order'])) {
			$_SESSION['units_sort_order'] = get_variable("sort_units");
		}
	}
	break;
case "add":
	$unit_type_id = 0;
	if (isset ($_POST['frm_type'])) {
		$unit_type_id = $_POST['frm_type'];
	}
	$unit_status_id = 0;
	if (isset ($_POST['frm_un_status_id'])) {
		$unit_status_id = $_POST['frm_un_status_id'];
	}
	$multi_dispachable = 1;
	if (isset ($_POST['frm_multi'])) {
		$multi_dispachable = $_POST['frm_multi'];
	}
	$guard_house_id = 0;
	if (isset ($_POST['frm_guard_house'])) {
		$guard_house_id = $_POST['frm_guard_house'];
	}
	?>
	<script>

		$(document).ready(function() {
			set_cursor_position(frm_handle, $("#frm_handle").val().length);
			<?php show_prevent_browser_back_button();?>
		});

	</script>
	</head>
	<body onload="check_frames();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" id="main_container">
			<form id="add_form" name="add_form" method="post" action="<?php echo basename(__FILE__);?>">
				<input type="hidden" id="function" name="function" value="insert">
				<input type="hidden" name="frm_group[]" value="1">
				<input type="hidden" name="frm_lat" value="">
				<input type="hidden" name="frm_lng" value="">
				<input type="hidden" name="frm_icon_url" size=3 maxlength=3 value="">
				<input type="hidden" name="frm_mobile" value=0>
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Add Unit") . " - " . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed; z-index: 1000;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" tabindex=19 onclick="window.location.href='units.php';"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" tabindex=20 onclick="document.add_form.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<div<?php print get_help_text_str("_save_and_copy");?> class="btn-group">
										<button type="button" class="btn btn-xs btn-default" tabindex=21 onclick="submit_form(document.add_form);"><?php print get_text("Save");?></button>
										<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="caret"></span>
										</button>
											<ul class="dropdown-menu">
											<li><a onclick="save_and_copy_unit(true);"><?php print get_text("Save and copy dataset");?></a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_left">
								<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
									<tr>
										<th style="border-top: 0px;"<?php print get_help_text_str("_ResHand");?>><?php print get_text("Unit handle");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
										<td style="border-top: 0px;" colspan=3>
											<input type="text" id="frm_handle" name="frm_handle" class="form-control mandatory" tabindex=1 value="<?php if (isset ($_POST['frm_handle'])) {print $_POST['frm_handle'];}?>">
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResName");?>><?php print get_text("Unit name");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
										<td colspan=3>
											<input type="text" name="frm_name" class="form-control mandatory" tabindex=2 value="<?php if (isset ($_POST['frm_name'])) {print $_POST['frm_name'];}?>">
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResSMSR");?>>
											<?php print get_text("Remote data services");?>:<br>
											<button type="button" class="btn btn-xs btn-default" tabindex=3 style="margin-top: 5px;" onclick="printers();"><?php print get_text("Get printers");?></button>
										</th>
										<td colspan=3>
											<div>
												<textarea id="frm_smsg_id" name="frm_smsg_id" rows=7 class="form-control" tabindex=4 value=""><?php if (isset ($_POST['frm_smsg_id'])) {print $_POST['frm_smsg_id'];}?></textarea>
											</div>
											<div>
												<?php print get_unit_select_str("reporting_channel_smsg_id");?>
											</div>
										</td>
									</tr>
									<tr>
										<th<?php print $help_text_phone;?>><?php print get_text("Cellular phone");?>:</th>
										<td colspan=3>
											<div>
												<input type="text" id="frm_phone" name="frm_phone" class="form-control" tabindex=6 value="<?php if (isset ($_POST['frm_phone'])) {print $_POST['frm_phone'];}?>">
											</div>
											<div>
												<?php print get_unit_select_str("reporting_channel_phone");?>
											</div>	
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResConV");?>><?php print get_text("Email");?>:</th>
										<td colspan=3>
											<div>
												<input type="email" id="frm_unit_email" name="frm_unit_email" class="form-control" tabindex=8 value="<?php if (isset ($_POST['frm_unit_email'])) {print $_POST['frm_unit_email'];}?>">
											</div>
											<div>
												<?php print get_unit_select_str("reporting_channel_email");?>
											</div>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResRepChan");?>><?php print get_text("Get links");?>:</th>
										<td colspan=3>
											<?php print get_unit_select_str("reporting_channel");?>
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
									<tr>
										<th style="border-top: 0px;"<?php print get_help_text_str("_ResType");?>><?php print get_text("Type");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
										<td style="border-top: 0px;" colspan=3>
											<?php show_unit_types_select($unit_type_id);?>
											<div style="float: right;">
												<span style="display: none;"<?php print get_help_text_str("_ResMobi");?>>
													<strong><?php print get_text("Mobile");?>:</strong> <input type="checkbox" name="frm_mob_disp">
												</span>
											</div>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResStat");?>><?php print get_text("Status");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span></th>
										<td colspan=3><?php show_unit_status_select(0, 0, 1);?></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResMult");?>><?php print get_text("Dispatchable");?>:</th>
										<td colspan=3><?php show_multiple_select($multi_dispachable);?></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResGuHo");?>><?php print get_text("Guard house");?>:</td>
										<td colspan=3><?php print get_guard_house_select_str("unit", $guard_house_id);?></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResDesc");?>><?php print get_text("Description");?>:</th>
										<td colspan=3>
											<textarea name="frm_descr" class="form-control" tabindex=15 rows=2><?php if (isset ($_POST['frm_descr'])) {print $_POST['frm_descr'];}?></textarea>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResCapa");?>><?php print get_text("Capability");?>:</th>
										<td colspan=3>
											<textarea name="frm_capab" class="form-control"  tabindex=16 rows=2><?php if (isset ($_POST['frm_capab'])) {print $_POST['frm_capab'];}?></textarea>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResConN");?>><?php print get_text("Contact name");?>:</th>
										<td colspan=3>
											<input type="text" name="frm_contact_name" class="form-control" tabindex=17 value="<?php if (isset ($_POST['frm_contact_name'])) {print $_POST['frm_contact_name'];}?>">
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("AdminPerms");?>><?php print get_text("Admin permission");?>:</th>
										<td colspan=3><?php get_admin_permission_select_str("unit", 0);?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
		<?php show_infobox();?>
	</body>
</html>
	<?php
	break;
case "edit":

	$query = "SELECT *, " .
		"UNIX_TIMESTAMP(`u`.`updated`) AS `updated`, " .
		"`u`.`name` AS `unit_name`, " .
		"`users`.`name` AS `user_name` " .
		"FROM `units` `u` " .
		"LEFT JOIN `users` ON (`u`.`user_id` = `users`.`id`) " .
		"WHERE `u`.`id` = " . $_GET['id'] . ";";

	$result	= db_query($query, __FILE__, __LINE__);
	$row = db_fetch_array($result);
	$mobile_checked = "";
	if ($row['mobile'] == 1) {
		$mobile_checked = " checked";
	}

	$query_assigns	= "SELECT * " .
		"FROM `assigns` " .
		"WHERE `unit_id` = " . $_GET['id'] . " " .
		"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00');";

	$result_assigns = db_query($query_assigns, __FILE__, __LINE__);
	$count_assigns = db_affected_rows($result_assigns);
	unset ($result_assigns);
	$edit_disabled_str = "";
	$save_title_text_str = get_help_text_str("_copie_or_save_and_copy");
	$remove_title_text_str = get_help_text_str("_ResRemo");
	if ($count_assigns > 0) {	
		$edit_disabled_str = " disabled";
		$save_title_text_str = $remove_title_text_str = get_title_str(get_text("NA - calls in progress"));
	}
	$copy_button = false;
	if ($row['admin_only'] == 1 && !(is_super())) {
		$edit_disabled_str = " disabled";
		$copy_button = true;
		$remove_title_text_str = get_title_str(get_text("NA - superadmin only"));
		$save_title_text_str = "";
	}
	?>
		<script>

			$(document).ready(function() {
				set_cursor_position(frm_name, $("#frm_name").val().length);
				<?php show_prevent_browser_back_button();?>
			});

		</script>
	</head>
	<body onload="check_frames();">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" id="main_container">
			<form method="post" id="edit_form" name="edit_form" action="<?php echo basename(__FILE__);?>">
				<input type="hidden" id="function" name="function" value="update">
				<input type="hidden" name="frm_un_status_last" value="<?php print $row['unit_status_id'];?>"></input>
				<input type="hidden" name="frm_group[]" value="1">
				<input type="hidden" name="frm_id" value="<?php print $_GET['id'];?>">
				<input type="hidden" name="frm_lat" value="<?php print $row['lat'];?>">
				<input type="hidden" name="frm_lng" value="<?php print $row['lng'];?>">
				<input type="hidden" name="frm_icon_url" size=3 maxlength=3 value="<?php print remove_nls($row['icon_url']);?>">
				<input type="hidden" name="frm_mobile" value=<?php print $row['mobile'];?>>
				<input type="hidden" name="frm_exist_groups" value="<?php print (isset($alloc_groups))? $alloc_groups : 1;?>">	
				<input type="hidden" name="frm_status_updated" value="<?php print $row['status_updated'];?>">		
				<input type="hidden" name="frm_status_update" value=0>	
				<input type="hidden" id="frm_remove" name="frm_remove" value="false">
				<div class="row infostring">
					<div<?php print get_table_id_title_str("unit", $_GET['id']);?> class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Edit unit data") . get_table_id($_GET['id']) . " - " . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div class="container-fluid" style="position: fixed; z-index: 1000;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" tabindex=19 onclick="window.location.href='units.php';"><?php print get_text("Cancel");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" tabindex=20 onclick="document.edit_form.reset(); this.form.reset();"><?php print get_text("Reset");?></button>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div<?php print $save_title_text_str;?> class="col-md-12">
									<div class="btn-group">
									<?php if ($copy_button == true) { ?>
										<button type="button" class="btn btn-xs btn-default" tabindex=21 onclick="copy_unit();"><?php print get_text("Copy dataset");?></button>
									<?php } else { ?>
										<button type="button" class="btn btn-xs btn-default" tabindex=21 onclick="submit_form(document.edit_form);"<?php print $edit_disabled_str;?>><?php print get_text("Save");?></button>
										<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"<?php print $edit_disabled_str;?>>
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li><a onclick="copy_unit();"><?php print get_text("Copy dataset");?></a></li>
											<li><a onclick="save_and_copy_unit();"><?php print get_text("Save and copy dataset");?></a></li>
										</ul>
									<?php } ?>
									</div>
								</div>
							</div>
							<div class="row" style="margin-top: 20px;">
								<div<?php print $remove_title_text_str;?> class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" tabindex=22 onclick="remove_unit();"<?php print $edit_disabled_str;?>><?php print get_text("Delete");?></button>
								</div>
							</div>
						</div>
					</div>	
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_left">
								<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
									<tr>
										<th style="border-top: 0px;"<?php print get_help_text_str("_ResHand");?>>
											<?php print get_text("Unit handle");?>:
										</th>
										<td style="border-top: 0px;" colspan=3>
											<input type="text" name="frm_handle" class="form-control" value="<?php print remove_nls($row['handle']);?>" readonly>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResName");?>>
											<?php print get_text("Unit name");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
										</th>
										<td colspan=3>
											<input type="text" name="frm_name" id="frm_name" class="form-control mandatory" tabindex=2 value="<?php print remove_nls($row['unit_name']);?>">
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResSMSR");?>>
											<?php print get_text("Remote data services");?>:<br>
											<button type="button" class="btn btn-xs btn-default" tabindex=3 style="margin-top: 5px;" onclick="printers();"><?php print get_text("Get printers");?></button>
										</th>
										<td colspan=3>
											<div>
												<textarea id="frm_smsg_id" name="frm_smsg_id" rows=7 class="form-control" tabindex=4 ><?php print remove_nls($row['remote_data_services']);?></textarea>
											</div>
											<div>
												<?php print get_unit_select_str("reporting_channel_smsg_id");?>
											</div>
										</td>
									</tr>
									<tr>
										<th<?php print $help_text_phone;?>><?php print get_text("Cellular phone");?>:</th>
										<td colspan=3>
											<div>
												<input type="text" id="frm_phone" name="frm_phone" class="form-control" tabindex=6 value="<?php print remove_nls($row['unit_phone']);?>">
											</div>
											<div>
												<?php print get_unit_select_str("reporting_channel_phone");?>
											</div>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResConV");?>><?php print get_text("Email");?>:</th>
										<td colspan=3>
											<div>
												<input type="email" id="frm_unit_email" name="frm_unit_email" class="form-control" tabindex=8 value="<?php print remove_nls($row['unit_email']);?>">
											</div>
											<div>
												<?php print get_unit_select_str("reporting_channel_email");?>
											</div>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResRepChan");?>><?php print get_text("Get links");?>:</th>
										<td colspan=3>
											<?php print get_unit_select_str("reporting_channel");?>
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
									<tr>
										<th style="border-top: 0px;"<?php print get_help_text_str("_ResType");?>><?php remove_nls(print get_text("Type"));?>:</th>
										<td style="border-top: 0px;" colspan=3>
											<?php show_unit_types_select($row['type']);?>
											<div style="float: right;">
												<span style="display: none;"<?php print get_help_text_str("_ResMobi");?>>
													<strong><?php print get_text("Mobile");?>:</strong> <input type="checkbox" name="frm_mob_disp" <?php print $mobile_checked;?>>
												</span>
											</div>
										</td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResStat");?>><?php print get_text("Status");?>:</th>
										<td<?php print get_title_str(get_current_tickets_title($_GET['id']));?> colspan=3><?php show_unit_status_select($row['unit_status_id'], $count_assigns, $row['multi']);?></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResMult");?>><?php print get_text("Dispatchable");?>:</th>
										<td colspan=3><?php show_multiple_select($row['multi']);?></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResGuHo");?>><?php print get_text("Guard house");?>:</td>
										<td colspan=3><?php print get_guard_house_select_str("unit", $row['guard_house_id']);?></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResDesc");?>><?php print get_text("Description");?>:</th>
										<td colspan=3><textarea name="frm_descr" class="form-control" tabindex=15 rows=2><?php print remove_nls($row['description']);?></textarea></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResCapa");?>><?php print get_text("Capability");?>:</th>	
										<td colspan=3><textarea name="frm_capab" class="form-control" tabindex=16 rows=2><?php print remove_nls($row['capabilities']);?></textarea></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("_ResConN");?>><?php print get_text("Contact name");?>:</th>
										<td colspan=3><input type="text" name="frm_contact_name" class="form-control" tabindex=17 value="<?php print remove_nls($row['contact_name']);?>" /></td>
									</tr>
									<tr>
										<th<?php print get_help_text_str("AdminPerms");?>><?php print get_text("Admin permission");?>:</th>
										<td colspan=3><?php get_admin_permission_select_str("unit", $row['admin_only']);?></td>
									</tr>
									<tr style="height: 44px;">
										<th>
											<div<?php print get_help_text_str("_asof");?>><?php print get_text("Edited");?>:</div>
										</th>
										<td colspan=3>
											<div>
												<?php print date(get_variable("date_format"), $row['updated']) . " " . get_text("by") . " " . remove_nls($row['user_name']);?>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</form>
		</div>
		<?php show_infobox();?>
	</body>
</html>
	<?php
	break;
case "table_left":
	show_units_list("units", 1, 2, 0);
	break;
case "table_right":
	show_units_list("units", 2, 2, 0);
	break;
case "printers":
	break;
default:
	$auto_poll_settings = explode(",", get_variable("auto_poll"));
	$auto_poll_time = trim($auto_poll_settings[0]);
	?>
		<script>
	<?php if ($caption) { ?>
			var changes_data ='{"type":"message","item":"success","action":"<?php print $caption;?>"}';
			window.parent.navigationbar.postMessage(changes_data, window.location.origin);
	<?php } ?>

			function edit_assign(assign_id) {
				<?php if (is_operator() || is_admin() || is_super()) { ?>
				window.location.href="assign.php?back=units&assign_id=" + assign_id;
				<?php } ?>
			}

			function get_units() {
				$.get("units.php?function=table_left", function(data) {
					$("#table_left").html(data);
				});
				$.get("units.php?function=table_right", function(data) {
					$("#table_right").html(data);
				});
			}

			function do_sort_units(sort_order) {
				$.get("units.php?function=sort&order=" + sort_order)
					.done(function() {
						get_units();
				});
			}

			$(document).ready(function() {
				get_units();
				show_to_top_button("<?php print get_text("To top");?>");
				<?php show_prevent_browser_back_button();?>
				window.addEventListener("message", function(event) {
					if (event.origin != window.location.origin) return;
					get_infos_array = JSON.parse(event.data);
					if (get_infos_array['reload_flags']['units']) {
						if ((typeof current_unit_id != "undefined") && (current_unit_id > 0)) {
							show_assigns(current_unit_id);
						}
						get_units();
						var changes_data ='{"type":"button","item":"situation","action":"highlight"}';
						window.parent.navigationbar.postMessage(changes_data, window.location.origin);
					}
				});
			});

		</script>
	</head>
	<body onload="check_frames(); set_regions_control('<?php print get_num_groups();?>');"">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid" id="main_container">
			<div class="row infostring">
				<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
					<?php print get_text("Units") . " - " . get_variable("page_caption");?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-1">
					<div id="button_container" class="container-fluid" style="position: fixed;">
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="cancel_button('', '');"><?php print get_text("Cancel");?></button>
							</div>
						</div>
	<?php
	if (is_admin() || is_super()) {
	?>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="window.location.href='units.php?function=add';"><?php print get_text("Add Unit");?></button>
							</div>
						</div>
	<?php
	}
	?>
					</div>
				</div>
				<div class="col-md-10">
					<div class="panel panel-default" id="table_top" style="padding: 0px;">
						<div class="panel-heading" id="table_top" style="padding: 0px;">
							<?php print show_units_sortbar();?>
						</div>	
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-5">
					<div class="panel panel-default" style="padding: 0px;">
						<div id="table_left">
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<div class="panel panel-default" style="padding: 0px;">
						<div id="table_right">
						</div>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
		</div>
		<?php show_infobox();?>
		<?php show_infobox("large");?>
	</body>
</html>
	<?php
}