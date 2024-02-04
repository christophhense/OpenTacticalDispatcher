<?php
error_reporting(E_ALL);
session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/facilities.inc.php");
do_login(basename(__FILE__));

$datetime_now = mysql_datetime();
$function = "";

if (isset ($_POST['function']) && (is_admin() || is_super())) {
	$function = $_POST['function'];
}
if (isset ($_POST['frm_remove']) && ($_POST['frm_remove'] == "true") && (is_admin() || is_super())) {
	$function = "delete";
}
if (isset($_POST['frm_id'])) {

	$query = "SELECT `admin_only` " .
		"FROM `facilities` " .
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
		set_session_expire_time("on");
		$frm_lat = "0.999999";
		if (!empty ($_POST['frm_lat'])) {
			$frm_lat = $_POST['frm_lat'];
		}
		$frm_lng = "0.999999";
		if (!empty ($_POST['frm_lng'])) {
			$frm_lng = $_POST['frm_lng'];
		}
		if (!is_super()) {
			$_POST['frm_adminperms'] = 0;
		}
		$new_id = insert_into_facilities($_POST['frm_name'], $_POST['frm_handle'], $_POST['frm_object_id'], $_POST['frm_direct_dialing_1'],
			$_POST['frm_direct_dialing_2'], $_POST['frm_street'], $_POST['frm_city'], $_POST['frm_security_contact'],
			$_POST['frm_security_phone'], $_POST['frm_security_email'], $_POST['frm_type'], $_POST['frm_status_id'],
			$_POST['frm_descr'], $_POST['frm_capab'], $_POST['frm_opening_hours'], $_POST['frm_access_rules'],
			$_POST['frm_contact_name'], $_POST['frm_contact_phone'], $_POST['frm_contact_email'], $_POST['frm_adminperms'],
			$_POST['frm_icon_url'], "", $frm_lat, $frm_lng,
			0, "");
		foreach ($_POST['frm_group'] as $grp_val) {		
			insert_into_allocates($grp_val, $GLOBALS['TYPE_FACILITY'], $new_id, $_SESSION['user_id'], $datetime_now);
		}
		do_log($GLOBALS['LOG_FACILITY_ADD'], 0, 0, get_facility_edit_log_text("add", $new_id, $_POST, ""), $new_id, "", "", "");
		print json_encode(array (
			"message" => get_text("Saved"),	
			"appearance" => "success"
		));
		exit;
	case "update":
		set_session_expire_time("on");

		$query = "SELECT * FROM `facilities` WHERE `id`= " . $_POST['frm_id'] . ";";

		$result = db_query($query, __FILE__, __LINE__);
		$old_data = stripslashes_deep(db_fetch_assoc($result));
		$admin_only_query_str = "";
		if (isset ($_POST['frm_adminperms'])) {
			$admin_only_query_str = "`admin_only` = " . $_POST['frm_adminperms'] . ", ";
		} else {
			$_POST['frm_adminperms'] = $old_data['admin_only'];
		}
		$lat = empty ($_POST['frm_lat'])? "0.999999" : quote_smart(trim($_POST['frm_lat']));
		$lng = empty ($_POST['frm_lng'])? "0.999999" : quote_smart(trim($_POST['frm_lng']));
		$curr_groups = $_POST['frm_exist_groups'];
		$groups = isset ($_POST['frm_group'])? ", " . implode(',', $_POST['frm_group']) . "," : $_POST['frm_exist_groups'];
		$fac_id = $_POST['frm_id'];

		$query = "UPDATE `facilities` SET " .
			"`name` = " . 		quote_smart(trim($_POST['frm_name'])) . ", " .
			"`street` = " . 		quote_smart(trim($_POST['frm_street'])) . ", " .
			"`city` = " . 		quote_smart(trim($_POST['frm_city'])) . ", " .
			$admin_only_query_str .
			"`handle` = " . 		quote_smart(trim($_POST['frm_handle'])) . ", " .
			"`icon_url` = " . 	quote_smart(trim($_POST['frm_icon_url'])) . ", " .
			"`description` = " . quote_smart(trim($_POST['frm_descr'])) . ", " .
			"`capabilities` = " . 		quote_smart(trim($_POST['frm_capab'])) . ", " .
			"`facility_status_id` = " . 	quote_smart(trim($_POST['frm_status_id'])) . ", " .
			"`lat` = " . 		$lat . ", " .
			"`lng` = " . 		$lng . ", " .
			"`object_id` = " . 		quote_smart(trim($_POST['frm_object_id'])) . ", " .
			"`contact_name` = " . quote_smart(trim($_POST['frm_contact_name'])) . ", " .
			"`contact_email` = " . 	quote_smart(trim($_POST['frm_contact_email'])) . ", " .
			"`contact_phone` = " . 	quote_smart(trim($_POST['frm_contact_phone'])) . ", " .
			"`security_contact` = " . quote_smart(trim($_POST['frm_security_contact'])) . ", " .
			"`security_email` = " . 	quote_smart(trim($_POST['frm_security_email'])) . ", " .
			"`security_phone` = " . 	quote_smart(trim($_POST['frm_security_phone'])) . ", " .
			"`opening_hours` = " . 	quote_smart(trim($_POST['frm_opening_hours'])) . ", " .
			"`access_rules` = " . 	quote_smart(trim($_POST['frm_access_rules'])) . ", " .
			"`direct_dialing_1` = " . 	quote_smart(trim($_POST['frm_direct_dialing_1'])) . ", " .
			"`direct_dialing_2` = " . 	quote_smart(trim($_POST['frm_direct_dialing_2'])) . ", " .
			"`type` = " . 		quote_smart(trim($_POST['frm_type'])) . ", " .
			"`user_id` = " . 	quote_smart(trim($_SESSION['user_id'])) . ", " .
			"`updated` = " . 	quote_smart(trim($datetime_now)) . " " .
			"WHERE `id` = " . 	quote_smart(trim($_POST['frm_id'])) . ";";

		db_query($query, __FILE__, __LINE__);

		$list = $_POST['frm_exist_groups'];
		$ex_grps = explode(",", $list);

		if ($curr_groups != $groups) {
			foreach ($_POST['frm_group'] as $posted_grp) {
				if (!in_array($posted_grp, $ex_grps)) {
					insert_into_allocates($posted_grp, $GLOBALS['TYPE_FACILITY'], $fac_id, $_SESSION['user_id'], $datetime_now);
				}
			}
			foreach ($ex_grps as $existing_grps) {
				if (!in_array($existing_grps, $_POST['frm_group'])) {
					if (empty ($existing_grps)) {
						$existing_grps = 0;
					}

					$query = "DELETE FROM `allocates` " .
						"WHERE `type` = " . $GLOBALS['TYPE_FACILITY'] . " " .
						"AND `group` = " . $existing_grps . " " .
						"AND `resource_id` = " . $fac_id . ";";

					db_query($query, __FILE__, __LINE__);
				}
			}
		}
		$log_text = get_text("TBL_ID") . ": #" . $_POST['frm_id'];
		do_log($GLOBALS['LOG_FACILITY_CHANGE'], 0, 0, get_facility_edit_log_text("update", $_POST['frm_id'], $_POST, $old_data), $_POST['frm_id'], "", "", "");
		if (!empty ($_POST['frm_status_update'])) {

			$query_fac_status = "SELECT `status_name`, " .
				"`description` " .
				"FROM `facility_status` " .
				"WHERE `id` = " . $_POST['frm_status_id'] . ";";

			$result_fac_status = db_query($query_fac_status, __FILE__, __LINE__);
			$row_fac_status = stripslashes_deep(db_fetch_assoc($result_fac_status));
			$fac_status_upd_val = $row_fac_status['status_name'] . ", " . $row_fac_status['description'];
			do_log($GLOBALS['LOG_FACILITY_STATUS'], 0, 0, $fac_status_upd_val, $_POST['frm_id'], "", "", "");
		}
		print json_encode(array (
			"message" => get_text("Saved"),	
			"appearance" => "success"
		));
		exit;
	case "delete":

		$query = "SELECT * FROM `facilities` WHERE `id`= " . $_POST['frm_id'] . ";";

		$result = db_query($query, __FILE__, __LINE__);
		$old_data = stripslashes_deep(db_fetch_assoc($result));

		$query = "DELETE FROM `allocates` " .
			"WHERE `resource_id` = " . $_POST['frm_id'] . " " .
			"AND `type` = " . $GLOBALS['TYPE_FACILITY'] . ";";

		$result = db_query($query, __FILE__, __LINE__);
		$log_text = get_text("TBL_ID") . ": #" . $_POST['frm_id'];
		do_log($GLOBALS['LOG_FACILITY_DELETED'], 0, 0, get_facility_edit_log_text("delete", $_POST['frm_id'], $_POST, $old_data), $_POST['frm_id'], "", "", "");
		print json_encode(array (
			"message" => get_text("Deleted"),	
			"appearance" => "success"
		));
		exit;
	default:
}
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
switch ($function) {
	case "table_left":
	case "table_right":
		break;
	case "add":
	case "edit":
	default:
		set_session_expire_time("on");
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

				function validate_facility_form() {
					var error_message = "";
					if ($("#frm_handle").val() == "") {
						error_message += "<?php print get_text("Facility HANDLE is required.");?><br>";
					}
					if ($("#frm_name").val() == "") {
						error_message += "<?php print get_text("Facility NAME is required.");?><br>";
					}
					if ($("#frm_type").val() == 0) {
						error_message += "<?php print get_text("Facility type selection is required.");?><br>";
					}
					if ($("#frm_status_id").val() == 0) {
						error_message += "<?php print get_text("Facility STATUS is required.");?><br>";
					}
					if (error_message != "") {
						show_infobox("<?php print get_text("Please correct the following and re-submit");?>", error_message);
						return false;
					} else {
						return true;
					}
				}

				function submit_facility_form(facility_form, copie_form) {
					if (validate_facility_form()) {
						$.post("facilities.php", $(facility_form).serialize())
						.done(function (data) {
							var return_array = JSON.parse(data);
							var destination_window = "facilities.php";
							if (copie_form != "" && copie_form != "delete") {
								$("#function").val("add");
								destination_window = "facilities.php?" + $.param($(copie_form).serializeArray());
							}
							show_top_notice(return_array["appearance"], return_array["message"]);
							goto_window(destination_window);
						})
						.fail(function () {
							show_top_notice("danger", "<?php print get_text("Error");?>");
							goto_window("facilities.php");
						});
					}
				}

				function copy_facility_form() {
					show_top_notice("success", "<?php print get_text("Copied");?>");
					$("#function").val("add");
					goto_window("facilities.php?" + $.param($("#facility_edit_form").serializeArray()));
				}

				function do_remove_facility(result) {
					if (result == true) {
						$("#frm_remove").val("true");
						submit_facility_form("#facility_edit_form", "delete");
					}
				}

				function remove_facility() {
					show_infobox("<?php print get_text("Please confirm removing");?>", false, "", do_remove_facility);
					return;
				}

			</script>
		<?php
	}
	$reported_by_phone_settings = explode(",", get_variable("reported_by_phone"));
	$help_text_phone_direct_dialin_1 = get_help_text("_FacDirectDial1", true);
	if (trim($reported_by_phone_settings[0]) == 1) {
		$help_text_phone_direct_dialin_1 .= " " . get_text("Selected for transfer in the Callback number field on the New-ticket and Edit-ticket page.");
	}
	$help_text_phone_direct_dialin_1 = get_title_str($help_text_phone_direct_dialin_1);
	$help_text_phone_direct_dialin_2 = get_help_text("_FacDirectDial2", true);
	if (trim($reported_by_phone_settings[1]) == 1) {
		$help_text_phone_direct_dialin_2 .= " " . get_text("Selected for transfer in the Callback number field on the New-ticket and Edit-ticket page.");
	}
	$help_text_phone_direct_dialin_2 = get_title_str($help_text_phone_direct_dialin_2);
	$help_text_phone_security = get_help_text("_FacSecP", true);
	if (trim($reported_by_phone_settings[2]) == 1) {
		$help_text_phone_security .= " " . get_text("Selected for transfer in the Callback number field on the New-ticket and Edit-ticket page.");
	}
	$help_text_phone_security = get_title_str($help_text_phone_security);
	$help_text_phone_contact = get_help_text("_FacConP", true);
	if (trim($reported_by_phone_settings[3]) == 1) {
		$help_text_phone_contact .= " " . get_text("Selected for transfer in the Callback number field on the New-ticket and Edit-ticket page.");
	}
	$help_text_phone_contact = get_title_str($help_text_phone_contact);
	switch ($function) {
		case "sort":
			if (isset ($_GET['order'])) {
				$_SESSION['facilities_sort_order'] = $_GET['order'];
			} else {
				if (empty ($_SESSION['facilities_sort_order'])) {
					$_SESSION['facilities_sort_order'] = get_variable("sort_facilities");
				}
			}
			break;
		case "add":
			$unit_type_id = 0;
			if (isset ($_GET['frm_type'])) {
				$unit_type_id =  $_GET['frm_type'];
			}
			$frm_status_id = 0;
			if (isset ($_GET['frm_status_id'])) {
				$frm_status_id =  $_GET['frm_status_id'];
			}
			?>
				<script>
					var new_infos_array = [];
					var screen_id_main = 0;

					$(document).ready(function() {
						set_cursor_position(frm_handle, $("#frm_handle").val().length);
						set_window_present("facilities_add");
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
					<form id="facility_add_form" name="facility_add_form">
						<input id="function" name="function" type="hidden" value="insert">
						<input id="frm_group[]" name="frm_group[]" type="hidden" value="1">
						<input id="frm_lat" name="frm_lat" type="hidden" value="<?php if (isset ($_GET['frm_lat'])) {print $_GET['frm_lat'];}?>">
						<input id="frm_lng" name="frm_lng" type="hidden" value="<?php if (isset ($_GET['frm_lng'])) {print $_GET['frm_lng'];}?>">
						<input id="frm_icon_url" name="frm_icon_url" type="hidden" value="">
						<div class="row infostring">
							<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
								<?php print get_text("Add Facility") . " - " . get_variable("page_caption");?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-1">
								<div class="container-fluid" style="position: fixed; z-index: 1000;">
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" tabindex=21 onclick="goto_window('facilities.php');"><?php print get_text("Cancel");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" tabindex=22 onclick="$('#facility_add_form').trigger('reset');"><?php print get_text("Reset");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-12">
											<div<?php print get_help_text_str("_save_and_copy");?> class="btn-group">
												<button type="button" class="btn btn-xs btn-default" tabindex=23 onclick="submit_facility_form('#facility_add_form', '');"><?php print get_text("Save");?></button>
												<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu">
													<li><a onclick="submit_facility_form('#facility_add_form', '#facility_add_form');"><?php print get_text("Save and copy dataset");?></a></li>
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
												<th style="border-top: 0px;"<?php print get_help_text_str("_FacHand");?>>
													<?php print get_text("Facility handle");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
												</th>
												<td colspan=3 style="border-top: 0px;">
													<input id="frm_handle" name="frm_handle" class="form-control mandatory" tabindex=1 type="text" value="<?php if (isset ($_GET['frm_handle'])) {print $_GET['frm_handle'];}?>">
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacName");?>>
													<?php print get_text("Facility name");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
												</th>
												<td colspan=3>
													<input id="frm_name" name="frm_name" class="form-control mandatory" tabindex=2 type="text"value="<?php if (isset ($_GET['frm_name'])) {print $_GET['frm_name'];}?>">
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacObId");?>><?php print get_text("Object id"); ?>:</th>
												<td colspan=3><input id="frm_object_id" name="frm_object_id" class="form-control" tabindex=3 value="<?php if (isset ($_GET['frm_object_id'])) {print $_GET['frm_object_id'];}?>"></td>
											</tr>
											<tr>
												<th<?php print $help_text_phone_direct_dialin_1;?>><?php print get_text("Direct dialing 1");?>:</th>
												<td colspan=3><input id="frm_direct_dialing_1" name="frm_direct_dialing_1" class="form-control" tabindex=4 type="text" value="<?php if (isset ($_GET['frm_direct_dialing_1'])) {print $_GET['frm_direct_dialing_1'];}?>"></td>
											</tr>
											<tr>
												<th<?php print $help_text_phone_direct_dialin_2;?>><?php print get_text("Direct dialing 2");?>:</th>
												<td colspan=3><input id="frm_direct_dialing_2" name="frm_direct_dialing_2" class="form-control" tabindex=5 type="text" value="<?php if (isset ($_GET['frm_direct_dialing_2'])) {print $_GET['frm_direct_dialing_2'];}?>"></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacLoca");?>><?php print get_text("Facility address"); ?>:</th>
												<td colspan=3>
													<textarea id="frm_street" name="frm_street" class="form-control" tabindex=6 rows="2"><?php if (isset ($_GET['frm_street'])) {print $_GET['frm_street'];}?></textarea>
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacCity");?>><?php print get_text("City");?>:</th>
												<td colspan=3>
													<input id="frm_city" name="frm_city" class="form-control" tabindex=7 type="text" value="<?php if (isset ($_GET['frm_city'])) {print $_GET['frm_city'];}?>">
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacOpen");?>><?php print get_text("Opening hours");?>:</th>
												<td colspan=3>
													<textarea id="frm_opening_hours" name="frm_opening_hours" class="form-control" tabindex=8 rows=1><?php if (isset ($_GET['frm_opening_hours'])) {print $_GET['frm_opening_hours'];}?></textarea>
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacAcce");?>><?php print get_text("Access rules");?>:</th>
												<td colspan=3>
													<textarea id="frm_access_rules" name="frm_access_rules" class="form-control" tabindex=9 rows=1><?php if (isset ($_GET['frm_access_rules'])) {print $_GET['frm_access_rules'];}?></textarea>
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacSecC");?>><?php print get_text("Security contact");?>:</th>
												<td colspan=3><input id="frm_security_contact" name="frm_security_contact" class="form-control" tabindex=10 type="text" value="<?php if (isset ($_GET['frm_security_contact'])) {print $_GET['frm_security_contact'];}?>"></td>
											</tr>
											<tr>
												<th<?php print $help_text_phone_security;?>><?php print get_text("Security phone");?>:</th>
												<td colspan=3><input id="frm_security_phone" name="frm_security_phone" class="form-control" tabindex=11 type="text" value="<?php if (isset ($_GET['frm_security_phone'])) {print $_GET['frm_security_phone'];}?>"></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacSecE");?>><?php print get_text("Security email");?>:</th>
												<td colspan=3><input id="frm_security_email" name="frm_security_email" class="form-control" tabindex=12 type="email" value="<?php if (isset ($_GET['frm_security_email'])) {print $_GET['frm_security_email'];}?>"></td>
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
												<th style="border-top: 0px;"<?php print get_help_text_str("_FacType");?>>
													<?php print get_text("Type");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
												</th>
												<td style="border-top: 0px;" colspan=3>
													<?php show_facility_types_select($unit_type_id);?>
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacStat");?>>
													<?php print get_text("Status"); ?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
												</th>
												<td colspan=3><?php show_facility_status_select($frm_status_id);?></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacDesc");?>><?php print get_text("Description");?>: </th>
												<td colspan=3>
													<textarea id="frm_descr" name="frm_descr" class="form-control" tabindex=15 rows=2><?php if (isset ($_GET['frm_descr'])) {print $_GET['frm_descr'];}?></textarea>
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacCapa");?>><?php print get_text("Capability");?>:</th>
												<td colspan=3>
													<textarea id="frm_capab" name="frm_capab" class="form-control" tabindex=16 rows=2><?php if (isset ($_GET['frm_capab'])) {print $_GET['frm_capab'];}?></textarea>
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacConN");?>><?php print get_text("Contact name");?>:</th>
												<td colspan=3><input id="frm_contact_name" name="frm_contact_name" class="form-control" tabindex=17 type="text" value="<?php if (isset ($_GET['frm_contact_name'])) {print $_GET['frm_contact_name'];}?>"></td>
											</tr>
											<tr>
												<th<?php print $help_text_phone_contact;?>><?php print get_text("Contact phone");?>:</th>
												<td colspan=3><input id="frm_contact_phone" name="frm_contact_phone" class="form-control" tabindex=18 type="text" value="<?php if (isset ($_GET['frm_contact_phone'])) {print $_GET['frm_contact_phone'];}?>"></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacConE");?>><?php print get_text("Contact email");?>:</th>
												<td colspan=3><input id="frm_contact_email" name="frm_contact_email" class="form-control" tabindex=19 type="email" value="<?php if (isset ($_GET['frm_contact_email'])) {print $_GET['frm_contact_email'];}?>"></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("AdminPerms");?>><?php print get_text("Admin permission");?>:</th>
												<td colspan=3><?php get_admin_permission_select_str("facility", 0);?></td>
											</tr>
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
			break;
		case "edit":

			$query = "SELECT *, " .
				"UNIX_TIMESTAMP(`f`.`updated`) AS `updated`, " .
				"`f`.`name` AS `facility_name`, " .
				"`users`.`name` AS `user_name` " .
				"FROM `facilities` `f`" .
				"LEFT JOIN `users` ON (`f`.`user_id` = `users`.`id`) " .
				"WHERE `f`.`id` = " . $_GET['id'] . ";";

			$result	= db_query($query, __FILE__, __LINE__);
			$row = db_fetch_assoc($result);
			$edit_disabled_str = "";
			$save_title_text_str = get_help_text_str("_copie_or_save_and_copy");
			$remove_title_text_str = get_help_text_str("_FacRemo");
			$copy_button = false;
			if ($row['admin_only'] == 1 && !(is_super())) {
				$edit_disabled_str = " disabled";
				$copy_button = true;
				$remove_title_text_str = get_title_str(get_text("NA - superadmin only"));
				$save_title_text_str = "";
			}
			?>
				<script>
					var new_infos_array = [];
					var screen_id_main = 0;

					$(document).ready(function() {
						set_cursor_position(frm_name, $("#frm_name").val().length);
						set_window_present("facilities_edit");
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
					<form id="facility_edit_form" name="facility_edit_form">
						<input id="function" name="function" type="hidden" value="update">
						<input id="frm_group[]" name="frm_group[]" type="hidden" value="1">
						<input id="frm_id" name="frm_id" type="hidden" value="<?php print $_GET['id'];?>">
						<input id="frm_lat" name="frm_lat" type="hidden" value="<?php print $row['lat'];?>">
						<input id="frm_lng" name="frm_lng" type="hidden" value="<?php print $row['lng'];?>">
						<input id="frm_icon_url" name="frm_icon_url" type="hidden" value="<?php print remove_nls($row['icon_url']);?>">
						<input id="frm_status_update" name="frm_status_update" type="hidden" value="">
						<input id="frm_exist_groups" name="frm_exist_groups" type="hidden" value="<?php print (isset($alloc_groups))? $alloc_groups : 1;?>">
						<input id="frm_remove" name="frm_remove" type="hidden" value="false">
						<div class="row infostring">
							<div id="infostring_middle"<?php print get_table_id_title_str("facility", $_GET['id']);?> class="col-md-12" style="text-align: center; margin-bottom: 10px;">
								<?php print get_text("Edit Facility data") . get_table_id($_GET['id']) . " - " . get_variable("page_caption");?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-1">
								<div class="container-fluid" style="position: fixed; z-index: 1000;">
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" tabindex=21 onclick="goto_window('facilities.php');"><?php print get_text("Cancel");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" tabindex=22 onclick="$('#facility_edit_form').trigger('reset');"><?php print get_text("Reset");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;">
										<div<?php print $save_title_text_str;?> class="col-md-12">
											<div class="btn-group">
											<?php if ($copy_button == true) { ?>
												<button type="button" class="btn btn-xs btn-default" tabindex=23 onclick="copy_facility_form();"><?php print get_text("Copy dataset");?></button>
											<?php } else { ?>
												<button type="button" class="btn btn-xs btn-default" tabindex=23 onclick="submit_facility_form('#facility_edit_form', '');"><?php print get_text("Save");?></button>
												<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu">
													<li><a onclick="copy_facility_form();"><?php print get_text("Copy dataset");?></a></li>
													<li><a onclick="submit_facility_form('#facility_edit_form', '#facility_edit_form');"><?php print get_text("Save and copy dataset");?></a></li>
												</ul>
											<?php } ?>
											</div>
										</div>
									</div>
									<div class="row" style="margin-top: 20px;">
										<div<?php print $remove_title_text_str;?> class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" tabindex=24 onclick="remove_facility();"<?php print $edit_disabled_str;?>><?php print get_text("Delete");?></button>
										</div>
									</div>
								</div>
							</div>	
							<div class="col-md-5">
								<div class="panel panel-default" style="padding: 0px;">
									<div id="table_left">
										<table id="data" class="table table-striped table-condensed" style="table-layout: fixed;">
											<tr>
												<th style="border-top: 0px;"<?php print get_help_text_str("_FacHand");?>>
													<?php print get_text("Facility handle");?>:
												</th>
												<td style="border-top: 0px;" colspan=3>
													<input id="frm_handle" name="frm_handle" class="form-control" type="text" value="<?php print remove_nls(wordwrap($row['handle'], 80, "<br>", true));?>" readonly>
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacName");?>>
													<?php print get_text("Facility name");?>: <span style="font-size: small; vertical-align: top; color: red;">*</span>
												</th>
												<td colspan=3>
													<input id="frm_name" name="frm_name" class="form-control mandatory" tabindex=2 type="text" value="<?php print remove_nls($row['facility_name']);?>">
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacObId");?>><?php print get_text("Object id");?>:</th>
												<td colspan=3><input id="frm_object_id" name="frm_object_id" class="form-control" tabindex=3 type="text" value="<?php print remove_nls($row['object_id']);?>"></td>
											</tr>
											<tr>
												<th<?php print $help_text_phone_direct_dialin_1;?>><?php print get_text("Direct dialing 1");?>:</th>
												<td colspan=3><input id="frm_direct_dialing_1" name="frm_direct_dialing_1" class="form-control" tabindex=4 type="text" value="<?php print remove_nls($row['direct_dialing_1']);?>"></td>
											</tr>
											<tr>
												<th<?php print $help_text_phone_direct_dialin_2;?>><?php print get_text("Direct dialing 2");?>:</th>
												<td colspan=3><input id="frm_direct_dialing_2" name="frm_direct_dialing_2" class="form-control" tabindex=5 type="text" value="<?php print remove_nls($row['direct_dialing_2']);?>"></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacLoca");?>><?php print get_text("Facility address");?>:</th>
												<td colspan=3><textarea id="frm_street" name="frm_street" class="form-control" tabindex=6><?php print remove_nls($row['street']);?></textarea></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacCity");?>><?php print get_text("City");?>:</th>
												<td colspan=3>
													<input id="frm_city" name="frm_city" class="form-control" tabindex=7 type="text" value="<?php print remove_nls($row['city']);?>">
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacOpen");?>><?php print get_text("Opening hours");?>:</th>
												<td colspan=3><textarea id="frm_opening_hours" name="frm_opening_hours" class="form-control" tabindex=8 rows=1><?php print remove_nls($row['opening_hours']);?></textarea></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacAcce");?>><?php print get_text("Access rules");?>:</th>
												<td colspan=3><textarea id="frm_access_rules" name="frm_access_rules" class="form-control" tabindex=9 rows=1><?php print remove_nls($row['access_rules']);?></textarea></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacSecC");?>><?php print get_text("Security contact");?>:</th>
												<td colspan=3><input id="frm_security_contact" name="frm_security_contact" class="form-control" tabindex=10 size="48" maxlength="48" type="text" value="<?php print remove_nls($row['security_contact']);?>"></td>
											</tr>
											<tr>
												<th<?php print $help_text_phone_security;?>><?php print get_text("Security phone");?>:</th>
												<td colspan=3><input id="frm_security_phone" name="frm_security_phone" class="form-control" tabindex=11 type="text" value="<?php print remove_nls($row['security_phone']);?>"></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacSecE");?>><?php print get_text("Security email");?>:</th>
												<td colspan=3><input id="frm_security_email" name="frm_security_email" class="form-control" tabindex=12 type="email" value="<?php print remove_nls($row['security_email']);?>"></td>
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
												<th style="border-top: 0px;"<?php print get_help_text_str("_FacType");?>><?php print get_text("Type");?>:</th>
												<td style="border-top: 0px;" colspan=3>
													<?php show_facility_types_select($row['type']);?>
												</td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacStat");?>><?php print get_text("Status");?>:</th>
												<td colspan=3><?php show_facility_status_select($row['facility_status_id']);?></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacDesc");?>><?php print get_text("Description");?>:</th>
												<td colspan=3><textarea id="frm_descr" name="frm_descr" class="form-control" tabindex=15 rows=2><?php print remove_nls($row['description']);?></textarea></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacCapa");?>><?php print get_text("Capability");?>:</th>
												<td colspan=3><textarea id="frm_capab" name="frm_capab" class="form-control" tabindex=16 rows=2><?php print remove_nls($row['capabilities']);?></textarea></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacConN");?>><?php print get_text("Contact name");?>:</th>
												<td colspan=3><input id="frm_contact_name" name="frm_contact_name" class="form-control" tabindex=17 type="text" value="<?php print remove_nls($row['contact_name']);?>"></td>
											</tr>
											<tr>
												<th<?php print $help_text_phone_contact;?>><?php print get_text("Contact phone");?>:</th>
												<td colspan=3><input id="frm_contact_phone" name="frm_contact_phone" class="form-control" tabindex=18 type="text" value="<?php print remove_nls($row['contact_phone']);?>"></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("_FacConE");?>><?php print get_text("Contact email"); ?>:</th>
												<td colspan=3><input id="frm_contact_email" name="frm_contact_email" class="form-control" tabindex=19 type="email" value="<?php print remove_nls($row['contact_email']);?>"></td>
											</tr>
											<tr>
												<th<?php print get_help_text_str("AdminPerms");?>><?php print get_text("Admin permission");?>:</th>
												<td colspan=3><?php get_admin_permission_select_str("facility", $row['admin_only']);?></td>
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
				<?php show_infobox("small");?>
				<?php show_accesskeys();?>
			</body>
		</html>
			<?php
			break;
		case "table_left":
			show_facilities_list("left");
			break;
		case "table_right":
			show_facilities_list("right");
			break;
		default:
	?>
			<script>
				var new_infos_array = [];
				var screen_id_main = 0;

				function get_facilities() {
					$.get("facilities.php?function=table_left", function(data) {
						$("#table_left").html(data);
					});
					$.get("facilities.php?function=table_right", function(data) {
						$("#table_right").html(data);
					});
				}

				function do_sort_facilities(sort_order) {
					$.get("facilities.php?function=sort&order=" + sort_order)
					.done(function() {
						get_facilities();
					});
				}

				$(document).ready(function() {
					get_facilities();
					show_to_top_button("<?php print get_text("To top");?>");
					set_window_present("facilities");
					<?php show_prevent_browser_back_button();?>
					window.addEventListener("message", function(event) {
						if (event.origin != window.location.origin) return;
						new_infos_array = JSON.parse(event.data);
						screen_id_main = new_infos_array['screen']['screen_id'];
						if (new_infos_array['reload_flags']['facilities']) {
							get_facilities();
						}
					});
				});

			</script>
		</head>
		<body onload="check_frames(); set_regions_control('<?php print get_num_groups();?>');">
			<script type="text/javascript" src="./js/wz_tooltip.js"></script>
			<div class="container-fluid" id="main_container">
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Facilities") . " - " . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div id="button_container" class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('situation.php?screen_id=' + new_infos_array['screen']['screen_id']);"><?php print get_text("Cancel");?></button>
								</div>
							</div>
			<?php
			if (is_admin() || is_super()) {
			?>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onClick="goto_window('facilities.php?function=add');"><?php print get_text("Add Facility");?></button>
								</div>
							</div>
			<?php
			}
			?>
						</div>
					</div>
					<div class="col-md-10">
						<div class="panel panel-default" style="padding: 0px;">
							<div class="panel-heading" id="table_top" style="padding: 0px;">
								<?php show_facilities_sortbar();?>
							</div>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
				<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_left"></div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" style="padding: 0px;">
							<div id="table_right"></div>
						</div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</div>
			<?php show_accesskeys();?>
		</body>
	</html>
	<?php
}
?>