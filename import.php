<?php
error_reporting(E_ALL);
session_start();	
require_once ("./incs/functions.inc.php");
require_once ("./incs/configuration.inc.php");
if (count_units_and_facilities_and_users() != 0) {
	do_login(basename(__FILE__));
}

$filesize = 112400;
$top_notice_str = "";
$top_notice_log_str = "";
$doublette = 0;
$user_id = 0;
if (isset ($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
}
$client_address = $_SERVER['REMOTE_ADDR'];
$datetime_now = mysql_datetime();
$function = "";
if (isset ($_POST['function']) && is_super()) {
	$function = $_POST['function'];
}
$filename = "";
if (isset ($_POST['filename'])) {
	$filename = $_POST['filename'];
}
switch ($function) {
case "default-incident-types":
case "default-textblocks":
	if ($filename == "") {
		$function = "";
	}
	$user_id = 1;
	break;
default:
	if (
		(empty ($_FILES)) ||
		($_FILES['file']['error'] != 0) ||
		($_FILES['file']['size'] >= $filesize)
	) {
		$function = "";
	}
}
switch ($function) {
case "users":
	define("USERS_TABLE", 0);
	define("USERS_USER", 1);
	define("USERS_PASSWORD", 2);
	define("USERS_LEVEL", 3);
	define("USERS_MAIL", 4);
	define("USERS_REGIONS", 5);
	define("USERS_DELETE", 6);
	$line = "";
	$users = $users_updated = $users_del = 0;
	$severity = 0;
	$uploadfile = fopen($_FILES['file']['tmp_name'], "rb");
	while (!feof($uploadfile)) {
		$line = fgetcsv($uploadfile, 4096, ";");
		if (($line != false) && ($line[USERS_TABLE] == "users")) {

			$query_test = "SELECT `id` " .
				"FROM `users` " .
				"WHERE `name` = " . csv2mysql($line[USERS_USER]) . " " .
				"AND `password` != '55606758fdb765ed015f0612112a6ca7' " .
				"LIMIT 1;";

			if ((isset ($line[USERS_DELETE])) && ((strtolower($line[USERS_DELETE]) == "yes") || (strtolower($line[USERS_DELETE]) == "Ja"))) {
				$result_test = db_query($query_test, __FILE__, __LINE__);
				if (db_num_rows($result_test) > 0) {
					$row_test = stripslashes_deep(db_fetch_array($result_test));

					$user_del_query = "UPDATE `users` " .
						"SET `password` = '55606758fdb765ed015f0612112a6ca7' " .
						"WHERE `name` = " . csv2mysql($line[USERS_USER]) . " " .
						"AND `password` != '55606758fdb765ed015f0612112a6ca7' " .
						"LIMIT 1;";

					$result_update = db_query($user_del_query, __FILE__, __LINE__);
					$users_del = $users_del + db_affected_rows($result_update);

					$query_allocates = "DELETE FROM `allocates` " .
						"WHERE `resource_id` = " . $row_test['id'] . " AND " .
						"`type` = " . $GLOBALS['TYPE_USER'] . ";";

					db_query($query_allocates, __FILE__, __LINE__);
				}
			} else {
				$result_test = db_query($query_test, __FILE__, __LINE__);
				if (db_num_rows($result_test) == 0) {
					$new_id = insert_into_users(csv2raw($line[USERS_USER]), csv2raw($line[USERS_PASSWORD]), 
						csv2raw($line[USERS_LEVEL]), csv2raw($line[USERS_MAIL]), $datetime_now);
					if ($new_id > 0) {
						insert_into_allocates(1, $GLOBALS['TYPE_USER'], $new_id, $user_id, $datetime_now);
						$users++;
					}
				} else {
					$row_test = stripslashes_deep(db_fetch_array($result_test));

					$query_user = "UPDATE `users` " .
						"SET `password` = " . csv2mysql($line[USERS_PASSWORD]) . ", " .
						"`level` = " . csv2mysql($line[USERS_LEVEL]) . ", " .
						"`email` = " . csv2mysql($line[USERS_MAIL]) . " " .
						"WHERE `id` = " . $row_test['id'] . ";";

					$result_update = db_query($query_user, __FILE__, __LINE__);
					if (db_affected_rows($result_update) > 0) {
						$users_updated++;

						$query_allocates = "UPDATE `allocates` " .
							"SET `group` = 1, " .
							"`updated` = '" . $datetime_now . "', " .
							"`user_id` = " . $user_id . " " .
							"WHERE `resource_id` = " . $row_test['id'] . " AND " .
							"`type` = " . $GLOBALS['TYPE_USER'] . ";";

						db_query($query_allocates, __FILE__, __LINE__);	
					} else {
						$doublette++;
					}
				}
			}
		}
	}
	fclose($uploadfile);
	if ($users > 0) {
		$top_notice_log_str .= get_text("Dataset user added") . ": " . $users . ", ";
		$top_notice_str .= get_text("Dataset user added") . ": " . $users . "<br>";
	}
	if ($users_updated > 0) {
		$top_notice_log_str .= get_text("Dataset user updated") . ": " . $users_updated . ", ";
		$top_notice_str .= get_text("Dataset user updated") . ": " . $users_updated . "<br>";
	}
	if ($users_del > 0) {
		$top_notice_log_str .= get_text("Dataset user deleted") . ": " . $users_del . ", ";
		$top_notice_str .= get_text("Dataset user deleted") . ": " . $users_del . "<br>";
	}
	break;
case "units":
	define("UNITS_TYPE_STATUS_TABLE", 0);
	define("UNITS_TYPES_NAME", 1);
	define("UNITS_TYPES_DESCRIPTION", 2);
	define("UNITS_TYPES_BGCOLOR", 3);
	define("UNITS_TYPES_TEXTCOLOR", 4);
	define("UNITS_TYPES_DELETE", 5);
	define("UNITS_STATUS_STATUS", 1);
	define("UNITS_STATUS_DESCRIPTION", 2);
	define("UNITS_STATUS_DISPATCH", 3);
	define("UNITS_STATUS_EMPTY_COL", 4);
	define("UNITS_STATUS_SORT", 5);
	define("UNITS_STATUS_BGCOLOR", 6);
	define("UNITS_STATUS_TEXTCOLOR", 7);
	define("UNITS_STATUS_DELETE", 8);
	define("UNITS_HANDLE", 1);
	define("UNITS_REMOTE_DATA_SERVICE", 2);
	define("UNITS_NAME", 3);
	define("UNITS_ICON", 4);
	define("UNITS_TYPE", 5);
	define("UNITS_MOBILE", 6);
	define("UNITS_MULTI", 7);
	define("UNITS_REGIONS", 8);
	define("UNITS_GUARD_HOUSE", 9);
	define("UNITS_PARENT_ID", 10);
	define("UNITS_ADMIN_ONLY", 11);
	define("UNITS_DESCRIPTION", 12);
	define("UNITS_CAPABILITIES", 13);
	define("UNITS_PHONE", 14);
	define("UNITS_CONTACT", 15);
	define("UNITS_MAIL", 16);
	define("UNITS_DELETE", 17);
	$line = "";
	$resp_types = $resp_types_updated = $resp_types_del = 0;
	$resp_status = $resp_status_updated = $resp_status_del = 0;
	$unit = $unit_updated = $unit_del = 0;
	$unit_mobile = $unit_multi = $unit_admin_only = 0;
	$uploadfile = fopen($_FILES['file']['tmp_name'], "rb");
	while (!feof($uploadfile)) {
		$line = fgetcsv($uploadfile, 4096, ";");
		$delete = "";
		if ((isset ($line[UNITS_TYPES_BGCOLOR])) && (strlen($line[UNITS_TYPES_BGCOLOR]) < 7)) {
			switch ($line[UNITS_TYPES_BGCOLOR]) {
			case 0:
				$bgcolor = "#000000";
				$textcolor = "#FFFFFF";
				break;
			case 1:
				$bgcolor = "#0000FF";
				$textcolor = "#FFFFFF";
				break;
			case 2:
				$bgcolor = "#00FF00";
				$textcolor = "#000000";
				break;
			case 3:
				$bgcolor = "#FF0000";
				$textcolor = "#FFFFFF";
				break;
			case 4:
				$bgcolor = "#FFFFFF";
				$textcolor = "#000000";
				break;
			case 5:
				$bgcolor = "#FFFF00";
				$textcolor = "#000000";
				break;
			case 6:
				$bgcolor = "#DEDEDE";
				$textcolor = "#000000";
				break;
			case 7:
				$bgcolor = "#66CCFF";
				$textcolor = "#000000";
				break;
			default:
				$bgcolor = "#FFFFFF";
				$textcolor = "#000000";
			}
			if (isset ($line[UNITS_TYPES_TEXTCOLOR])) {
				$delete = $line[UNITS_TYPES_TEXTCOLOR];
			}
		} else {
			if (isset ($line[UNITS_TYPES_BGCOLOR])) {
				$bgcolor = $line[UNITS_TYPES_BGCOLOR];
			} else {
				$bgcolor = "#FFFFFF";
			}
			if (isset ($line[UNITS_TYPES_TEXTCOLOR])) {
				$textcolor = $line[UNITS_TYPES_TEXTCOLOR];
				$delete = $line[UNITS_TYPES_DELETE];
			} else {
				$textcolor = "#000000";
			}
		}
		if ($line == false) {
			$line[UNITS_TYPE_STATUS_TABLE] = "";
		}
		switch ($line[UNITS_TYPE_STATUS_TABLE]) {
		case "unit_types":
			if ((strtolower($delete) == "yes") || (strtolower($delete) == "Ja")) {

				$query_resp_types_del = "DELETE FROM `unit_types` " .
					"WHERE `name` = " . csv2mysql($line[UNITS_TYPES_NAME]) . ";";

				$result_delete = db_query($query_resp_types_del, __FILE__, __LINE__);
				if (db_affected_rows($result_delete) > 0) {
					$resp_types_del++;
				}
			} else {

				$query_test = "SELECT `id` FROM `unit_types` " .
					"WHERE `name` = " . csv2mysql($line[UNITS_TYPES_NAME]) . ";";

				$result_test = db_query($query_test, __FILE__, __LINE__);
				if (db_num_rows($result_test) == 0) {
					insert_into_unit_types(csv2raw($line[UNITS_TYPES_NAME]), 
						csv2raw($line[UNITS_TYPES_DESCRIPTION]), 
						$bgcolor, $textcolor, $user_id, $datetime_now);
					$resp_types++;
				} else {
					$row_test = stripslashes_deep(db_fetch_array($result_test));

					$query_resp_types_update = "UPDATE `unit_types` SET `name` = " . csv2mysql($line[UNITS_TYPES_NAME]) . ", `description` = " .
						csv2mysql($line[UNITS_TYPES_DESCRIPTION]) . ", `bg_color` = " . csv2mysql($bgcolor) . ", `text_color` = " .
						csv2mysql($textcolor) . " WHERE `id` = '" . $row_test['id'] . "';";

					$result_update = db_query($query_resp_types_update, __FILE__, __LINE__);
					if (db_affected_rows($result_update) > 0) {

						$query_resp_types_update = "UPDATE `unit_types` SET `user_id` = '" . $user_id . "', " .
							"`client_address`= '" . $client_address . "', " .
							"`updated` = '" . $datetime_now . "' " .
							"WHERE `id` = '" . $row_test['id'] . "';";

						db_query($query_resp_types_update, __FILE__, __LINE__);
						$resp_types_updated++;
					} else {
						$doublette++;
					}
				}
			}
			break;
		case "un_status":
		case "unit_status":
			$unit_status_id = 0;

			$query_resp_status_id = "SELECT `id` " .
				"FROM `unit_status` " .
				"WHERE `status_name` = " . csv2mysql($line[UNITS_STATUS_STATUS]) . ";";

			$result_resp_status_id = db_query($query_resp_status_id, __FILE__, __LINE__);
			if (db_affected_rows($result_resp_status_id) > 0) {
				$row_resp_status_id = stripslashes_deep(db_fetch_array($result_resp_status_id));
				$unit_status_id = $row_resp_status_id['id'];
			}
			if (!dont_delete_unit_status($unit_status_id)) {
				if ((strtolower($line[UNITS_STATUS_DELETE]) == "yes") || (strtolower($line[UNITS_STATUS_DELETE]) == "Ja")) {

					$query_resp_status_del = "DELETE FROM `unit_status` " .
						"WHERE `status_name` = " . csv2mysql($line[UNITS_STATUS_STATUS]) . ";";

					$result_delete = db_query($query_resp_status_del, __FILE__, __LINE__);
					if (db_affected_rows($result_delete) > 0) {
						$resp_status_del++;
					}
				} else {
					switch (strtolower($line[UNITS_STATUS_DISPATCH])) {
					case "Ja":
						$dispatch = $GLOBALS['DISPATCH_YES'];
						break;
					case "bedingt":
						$dispatch = $GLOBALS['DISPATCH_ENFORCEABLE'];
						break;
					case "nein":
						$dispatch = $GLOBALS['DISPATCH_NOT_ENFORCEABLE'];
						break;
					case "keine auswertung":
						$dispatch = $GLOBALS['DISPATCH_NO_EVALUATION'];
						break;
					case "yes":
						$dispatch = $GLOBALS['DISPATCH_YES'];
						break;
					case "no_inform":
						$dispatch = $GLOBALS['DISPATCH_ENFORCEABLE'];
						break;
					case "dispatch_enforceable":
						$dispatch = $GLOBALS['DISPATCH_ENFORCEABLE'];
						break;
					case "no_dispatch":
						$dispatch = $GLOBALS['DISPATCH_NOT_ENFORCEABLE'];
					break;
					case "monitor":
						$dispatch = $GLOBALS['DISPATCH_MONITOR'];
						break;
					case "no_evaluation":
						$dispatch = $GLOBALS['DISPATCH_NO_EVALUATION'];
						break;
					default:
						$dispatch = $GLOBALS['DISPATCH_YES'];
					}

					$query_test = "SELECT `id` " .
						"FROM `unit_status` " .
						"WHERE `status_name` = " . csv2mysql($line[UNITS_STATUS_STATUS]) . ";";

					$result_test = db_query($query_test, __FILE__, __LINE__);
					if (db_num_rows($result_test) == 0) {
						insert_into_unit_status(csv2raw($line[UNITS_STATUS_STATUS]), csv2raw($line[UNITS_STATUS_DESCRIPTION]), 
							$dispatch, csv2raw($line[UNITS_STATUS_SORT]), csv2raw($line[UNITS_STATUS_BGCOLOR]), 
							csv2raw($line[UNITS_STATUS_TEXTCOLOR]), $user_id, $datetime_now);
						$resp_status++;
					} else {
						$row_test = stripslashes_deep(db_fetch_array($result_test));

						$query_resp_status_update = "UPDATE `unit_status` SET `status_name` = " . csv2mysql($line[UNITS_STATUS_STATUS]) . ", `description` = " .
							csv2mysql($line[UNITS_STATUS_DESCRIPTION]) . ", `dispatch` = '" . $dispatch . "', `sort` = " . csv2mysql($line[UNITS_STATUS_SORT]) .
							", `bg_color` = " . csv2mysql($line[UNITS_STATUS_BGCOLOR]) . ", `text_color` = " . csv2mysql($line[UNITS_STATUS_TEXTCOLOR]) .
							" WHERE `id` = '" . $row_test['id'] . "';";

						$result_update = db_query($query_resp_status_update, __FILE__, __LINE__);
						if (db_affected_rows($result_update) > 0) {

							$query_resp_status_update = "UPDATE `unit_status` SET `user_id` = '" . $user_id . "', " .
								"`client_address`= '" .	$client_address . "', " .
								"`updated` = '" . $datetime_now . "' " .
								"WHERE `id` = '" . $row_test['id'] . "';";

							db_query($query_resp_status_update, __FILE__, __LINE__);
							$resp_status_updated++;
						} else {
							$doublette++;
						}
					}
				}
			}
			break;
		case "responder":
		case "unit":
		case "units":

			$query_test = "SELECT `id` " .
				"FROM `unit_types` " .
				"WHERE `name` = " . csv2mysql($line[UNITS_TYPE]) . " " .
				"LIMIT 1;";

			$result_test = db_query($query_test, __FILE__, __LINE__);
			if (db_num_rows($result_test) == 0) {

				$query_test = "SELECT `id` " .
					"FROM `unit_types` " .
					"ORDER BY 'id' ASC " .
					"LIMIT 1;";

				$result_test = db_query($query_test, __FILE__, __LINE__);
			}
			$unit_type = db_fetch_array($result_test);
			if ((strtolower($line[UNITS_DELETE]) == "yes") || (strtolower($line[UNITS_DELETE]) == "Ja")) {

				$query_unit_id = "SELECT `id` " .
					"FROM `units` " .
					"WHERE `handle` = " . csv2mysql($line[UNITS_HANDLE]) . " " .
					"AND `type` = " . $unit_type['id'] . ";";

				$result_unit_id = db_query($query_unit_id, __FILE__, __LINE__);
				$unit_id_or_str = "";
				while ($unit_row = db_fetch_array($result_unit_id)) {
					$unit_id_or_str .= " OR `resource_id` = " . $unit_row['id'];
				}

				$query_unit_del = "DELETE FROM `allocates` " .
					"WHERE (`resource_id` = 0" . $unit_id_or_str . ") " .
					"AND `type` = " . $GLOBALS['TYPE_UNIT'] . " " .
					"LIMIT 1";

				$result_delete = db_query($query_unit_del, __FILE__, __LINE__);
				if (db_affected_rows($result_delete) > 0) {
					$unit_del++;
				}
			} else {
				$do_import = true;
				$do_update = false;
				$update_id = 0;
				$updated = false;
				$guard_house_handle_id = 0;

				$query_test = "SELECT `units`.`id`, " .
					"`unit_types`.`name` " .
					"FROM `units` " .
					"LEFT JOIN `unit_types` ON `units`.`type` = `unit_types`.`id` " .
					"WHERE `handle` = " . csv2mysql($line[UNITS_HANDLE]) . " " .
					"AND `type` = " . $unit_type['id'] . ";";

				$result_test = db_query($query_test, __FILE__, __LINE__);
				if (db_num_rows($result_test) != 0) {
					while ($row_test = db_fetch_array($result_test)) {

						$query_test2 = "SELECT `id` " .
							"FROM `allocates` " .
							"WHERE (`resource_id` = " . $row_test["id"] . ") " .
							"AND (`type` = " . $GLOBALS['TYPE_UNIT'] . ")";

						$result_test2 = db_query($query_test2, __FILE__, __LINE__);

						if ((db_num_rows($result_test2) != 0) && ($row_test["name"] == trim($line[UNITS_TYPE]))) {
							$do_import = false;
							$do_update = true;
							$update_id = $row_test["id"];
						}
					}
				}

				$query = "SELECT `id` " .
					"FROM `facilities` " .
					"WHERE `handle` = '" . $line[UNITS_GUARD_HOUSE] . "' " .
					"LIMIT 1";

				$result = db_query($query, __FILE__, __LINE__);
				if (db_num_rows($result)) {
					$row = db_fetch_assoc($result);
					$guard_house_handle_id = $row['id'];
				}
				if ($do_import || $do_update) {
					$unit_mobile = 1;
					if (($line[UNITS_MOBILE] == "yes") || ($line[UNITS_MOBILE] == "Ja")) {
						$unit_mobile = 2;
					}
					$unit_multi = 0;
					if (($line[UNITS_MULTI] == "no") || ($line[UNITS_MULTI] == "Nein")) {
						$unit_multi = 1;
					}
					if (($line[UNITS_MULTI] == "yes") || ($line[UNITS_MULTI] == "Ja")) {
						$unit_multi = 2;
					}
					$unit_admin_only = 0;
					if (($line[UNITS_ADMIN_ONLY] == "yes") || ($line[UNITS_ADMIN_ONLY] == "Ja")) {
					    $unit_admin_only = 1;
					}
				}
				if ($do_import) {
					$new_id = insert_into_units(csv2raw($line[UNITS_HANDLE]), csv2raw($line[UNITS_HANDLE]), csv2raw($line[UNITS_REMOTE_DATA_SERVICE]), csv2raw($line[UNITS_PHONE]),
						csv2raw($line[UNITS_MAIL]), $unit_type['id'], 1, $unit_multi,
						$unit_mobile, 0, $guard_house_handle_id , csv2raw($line[UNITS_DESCRIPTION]),
						csv2raw($line[UNITS_CAPABILITIES]), csv2raw($line[UNITS_CONTACT]), $unit_admin_only, "",
						"0.999999", "0.999999", $datetime_now, $datetime_now,
						$user_id, $datetime_now);
					$regions = array ();
					$regions = explode ("/", csv2mysql($line[UNITS_REGIONS]));
					foreach ($regions as $region) {

						$query_test = "SELECT `id` " .
							"FROM `regions` " .
							"WHERE `region_name` = " . $region . " LIMIT 1;";

						$result_test = db_query($query_test, __FILE__, __LINE__);
						if (db_num_rows($result_test) != 0) {
							$group_id = db_fetch_array($result_test);
							insert_into_allocates($group_id['id'], $GLOBALS['TYPE_UNIT'], $new_id, $user_id, $datetime_now);
						}
					}

					$query_test = "SELECT `id` " .
						"FROM `allocates` " .
						"WHERE (`resource_id` = " . $new_id . ") " .
						"AND (`type` = " . $GLOBALS['TYPE_UNIT'] . ");";

					$result_test = db_query($query_test, __FILE__, __LINE__);
					if (db_num_rows($result_test) == 0) {

						$query_test = "SELECT `id` " .
							"FROM `regions` " .
							"ORDER BY 'id' ASC " .
							"LIMIT 1;";

						$result_test = db_query($query_test, __FILE__, __LINE__);
						$group_id = db_fetch_array($result_test);
						insert_into_allocates($group_id['id'], $GLOBALS['TYPE_UNIT'], $new_id, $user_id, $datetime_now);
					}
					$unit++;
				} else {
					if ($do_update) {

						$query_unit = "UPDATE `units` SET " .
							"`remote_data_services` = " . csv2mysql($line[UNITS_REMOTE_DATA_SERVICE]) . ", " .
							"`name` = " . csv2mysql($line[UNITS_NAME]) . ", " .
							"`icon_url` = " . csv2mysql($line[UNITS_ICON]) . ", " .
							"`type` = " . $unit_type['id'] . ", " .
							"`mobile` = " . $unit_mobile . ", `multi` = " . $unit_multi . ", " .
							"`guard_house_id` = " . csv2mysql($guard_house_handle_id) . ", " .
							"`description` = " . csv2mysql($line[UNITS_DESCRIPTION]) . ", " .
							"`capabilities` =  " . csv2mysql($line[UNITS_CAPABILITIES]) . ", " .
							"`unit_phone` = " . csv2mysql($line[UNITS_PHONE]) . ", " .
							"`contact_name` = " . csv2mysql($line[UNITS_CONTACT]) . ", " .
							"`admin_only` = " . $unit_admin_only . ", " .
							"`unit_email` = " . csv2mysql($line[UNITS_MAIL]) . " " .
							"WHERE `id` = '" . $update_id . "';";

						$result_update = db_query($query_unit, __FILE__, __LINE__);
						if (db_affected_rows($result_update) > 0) {

							$query_unit = "UPDATE `units` SET " .
								"`updated` = '" . $datetime_now . "', " .
								"`user_id` = " . $user_id . " " .
								"WHERE `id` = '" . $update_id . "';";

							db_query($query_unit, __FILE__, __LINE__);
							$updated = true;
						}
						$regions = array ();
						$regions = explode ("/", csv2mysql($line[UNITS_REGIONS]));
						foreach ($regions as $region) {

							$query_test = "SELECT `id` " .
								"FROM `regions` " .
								"WHERE `region_name` = " . $region . " " .
								"LIMIT 1;";

							$result_test = db_query($query_test, __FILE__, __LINE__);
							if (db_num_rows($result_test) != 0) {

								$query_test2 = "SELECT `id` " .
									"FROM `allocates` " .
									"WHERE `resource_id` = '" . $update_id . "' " .
									"AND `type` = " . $GLOBALS['TYPE_UNIT'] . " " .
									"LIMIT 1;";

								$result_test2 = db_query($query_test2, __FILE__, __LINE__);
								if (db_num_rows($result_test2) == 0) {
									$group_id = db_fetch_array($result_test);
									insert_into_allocates($group_id['id'], $GLOBALS['TYPE_UNIT'], $update_id, $user_id, $datetime_now);
									$updated = true;
								}
							} else {

								$query_test = "SELECT `group` " .
									"FROM `allocates` " .
									"WHERE `resource_id` = " . $update_id . " " .
									"AND `type` = " . $GLOBALS['TYPE_UNIT'] . " " .
									"AND `group` = (SELECT `id` FROM `regions` ORDER BY 'id' ASC LIMIT 1);";

								$result_test = db_query($query_test, __FILE__, __LINE__);
								$group_id = db_fetch_array($result_test);
								if (db_num_rows($result_test) == 0) {
									insert_into_allocates($group_id['id'], $GLOBALS['TYPE_UNIT'], $update_id, $user_id, $datetime_now);
 									$updated = true;
								}
							}
						}
						if ($updated) {
							$unit_updated++;
						} else {
							$doublette++;
						}
					}
				}
			}
			break;
		default:
		}
	}
	fclose($uploadfile);
	if ($resp_types > 0) {
		$top_notice_log_str .= get_text("Dataset unit_types added") . ": " . $resp_types . ", ";
		$top_notice_str .= get_text("Dataset unit_types added") . ": " . $resp_types . "<br>";
	}
	if ($resp_types_updated > 0) {
		$top_notice_log_str .= get_text("Dataset unit_types updated") . ": " . $resp_types_updated . ", ";
		$top_notice_str .= get_text("Dataset unit_types updated") . ": " . $resp_types_updated . "<br>";
	}
	if ($resp_types_del > 0) {
		$top_notice_log_str .= get_text("Dataset unit_types deleted") . ": " . $resp_types_del . ", ";
		$top_notice_str .= get_text("Dataset unit_types deleted") . ": " . $resp_types_del . "<br>";
	}
	if ($resp_status > 0) {
		$top_notice_log_str .= get_text("Dataset un_status added") . ": " . $resp_status . ", ";
		$top_notice_str .= get_text("Dataset un_status added") . ": " . $resp_status . "<br>";
	}
	if ($resp_status_updated > 0) {
		$top_notice_log_str .= get_text("Dataset un_status updated") . ": " . $resp_status_updated . ", ";
		$top_notice_str .= get_text("Dataset un_status updated") . ": " . $resp_status_updated . "<br>";
	}
	if ($resp_status_del > 0) {
		$top_notice_log_str .= get_text("Dataset un_status deleted") . ": " . $resp_status_del . ", ";
		$top_notice_str .= get_text("Dataset un_status deleted") . ": " . $resp_status_del . "<br>";
	}
	if ($unit > 0) {
		$top_notice_log_str .= get_text("Dataset unit added") . ": " . $unit . ", ";
		$top_notice_str .= get_text("Dataset unit added") . ": " . $unit . "<br>";
	}
	if ($unit_updated > 0) {
		$top_notice_log_str .= get_text("Dataset unit updated") . ": " . $unit_updated . ", ";
		$top_notice_str .= get_text("Dataset unit updated") . ": " . $unit_updated . "<br>";
	}
	if ($unit_del > 0) {
		$top_notice_log_str .= get_text("Dataset unit deleted") . ": " . $unit_del . ", ";
		$top_notice_str .= get_text("Dataset unit deleted") . ": " . $unit_del . "<br>";
	}
	break;
case "facilities":
	define("FAC_TYPE_STATUS_TABLE", 0);
	define("FACILITY_TYPES_TYPE", 1);
	define("FACILITY_TYPES_DESCRIPTION", 2);
	define("FACILITY_TYPES_BGCOLOR", 3);
	define("FACILITY_TYPES_TEXTCOLOR", 4);
	define("FACILITY_TYPES_DELETE", 5);
	define("FACILITY_STATUS_STATUS", 1);
	define("FACILITY_STATUS_DESCRIPTION", 2);
	define("FACILITY_STATUS_DISPLAY", 3);
	define("FACILITY_STATUS_SORT", 4);
	define("FACILITY_STATUS_BGCOLOR", 5);
	define("FACILITY_STATUS_TEXTCOLOR", 6);
	define("FACILITY_STATUS_DELETE", 7);
	define("FACILITY_HANDLE", 1);
	define("FACILITY_NAME", 2);
	define("FACILITY_ICON", 3);
	define("FACILITY_TYPE", 4);
	define("FACILITY_REGIONS", 5);
	define("FACILITY_STREET", 6);
	define("FACILITY_CITY", 7);
	define("FACILITY_ADMIN_ONLY", 8);
	define("FACILITY_DESCRIPTION", 9);
	define("FACILITY_CAPABILITIES", 10);
	define("FACILITY_CONTACT", 11);
	define("FACILITY_MAIL", 12);
	define("FACILITY_PHONE", 13);
	define("FACILITY_SEC_CONTACT", 14);
	define("FACILITY_SEC_MAIL", 15);
	define("FACILITY_SEC_PHONE", 16);
	define("FACILITY_OPENING", 17);
	define("FACILITY_ACCESS", 18);
	define("FACILITY_BOUNDARY", 19);
	define("FACILITY_PAGER1", 20);
	define("FACILITY_PAGER2", 21);
	define("FACILITY_LAT", 22);
	define("FACILITY_LNG", 23);
	define("FACILITY_OBJECT_ID", 24);
	define("FACILITY_UPDATED", 25);
	define("FACILITY_DELETE", 26);
	$line = "";
	$fac_types = $fac_types_updated = $fac_types_del = 0;
	$fac_status = $fac_status_updated = $fac_status_del = 0;
	$facilitiy = $facilitiy_updated = $facilitiy_del = 0;
	$uploadfile = fopen($_FILES['file']['tmp_name'], "rb");

	$query_facilitiy_type = "SELECT `id`, " .
		"`name` " .
		"FROM `facility_types`;";

	$result_facilitiy_type = db_query($query_facilitiy_type, __FILE__, __LINE__);
	$fac_type_pos = 1;
	$facilitiy_types = array ();
	while ($row_facilitiy_type = db_fetch_array($result_facilitiy_type)) {
		$facilitiy_types[$fac_type_pos]['id'] = $row_facilitiy_type['id'];
		$facilitiy_types[$fac_type_pos]['name'] = $row_facilitiy_type['name'];
		$fac_type_pos++;
	}
	while (!feof($uploadfile)) {
		$line = fgetcsv($uploadfile, 4096, ";");
		$delete = "";
		if ((isset ($line[FACILITY_TYPES_BGCOLOR])) && (strlen($line[FACILITY_TYPES_BGCOLOR]) < 7)) {
			switch ($line[FACILITY_TYPES_BGCOLOR]) {
			case 0:
				$bgcolor = "#000000";
				$textcolor = "#FFFFFF";
				break;
			case 1:
				$bgcolor = "#0000FF";
				$textcolor = "#FFFFFF";
				break;
			case 2:
				$bgcolor = "#00FF00";
				$textcolor = "#000000";
				break;
			case 3:
				$bgcolor = "#FF0000";
				$textcolor = "#FFFFFF";
				break;
			case 4:
				$bgcolor = "#FFFFFF";
				$textcolor = "#000000";
				break;
			case 5:
				$bgcolor = "#FFFF00";
				$textcolor = "#000000";
				break;
			case 6:
				$bgcolor = "#DEDEDE";
				$textcolor = "#000000";
				break;
			case 7:
				$bgcolor = "#66CCFF";
				$textcolor = "#000000";
				break;
			default:
				$bgcolor = "#FFFFFF";
				$textcolor = "#000000";
			}
			if (isset ($line[FACILITY_TYPES_TEXTCOLOR])) {
				$delete = $line[FACILITY_TYPES_TEXTCOLOR];
			}
		} else {
			if (isset ($line[FACILITY_TYPES_BGCOLOR])) {
				$bgcolor = $line[FACILITY_TYPES_BGCOLOR];
			} else {
				$bgcolor = "#FFFFFF";
			}
			if (isset ($line[FACILITY_TYPES_TEXTCOLOR])) {
				$textcolor = $line[FACILITY_TYPES_TEXTCOLOR];
				$delete = $line[FACILITY_TYPES_DELETE];
			} else {
				$textcolor = "#000000";
			}
		}
		if ($line == false) {
			$line[FAC_TYPE_STATUS_TABLE] = "";
		}
		switch ($line[FAC_TYPE_STATUS_TABLE]) {
		case "fac_types":
		case "facility_types":
			if ((strtolower($delete) == "yes") || (strtolower($delete) == "Ja")) {

				$query_fac_types_del = "DELETE FROM `facility_types` " .
					"WHERE `name` = " . csv2mysql($line[FACILITY_TYPES_TYPE]) . ";";

				$result_delete = db_query($query_fac_types_del, __FILE__, __LINE__);
				if (db_affected_rows($result_delete) > 0) {
					$fac_types_del++;
				}
			} else {

				$query_test = "SELECT `id` " .
					"FROM `facility_types` " .
					"WHERE `name` = " . csv2mysql($line[FACILITY_TYPES_TYPE]) . ";";

				$result_test = db_query($query_test, __FILE__, __LINE__);
				if (db_num_rows($result_test) == 0) {
					insert_into_facility_types(csv2raw($line[FACILITY_TYPES_TYPE]), 
						csv2raw($line[FACILITY_TYPES_DESCRIPTION]), 
						$bgcolor, $textcolor, $user_id, $datetime_now);
					$fac_types++;
				} else {
					$row_test = stripslashes_deep(db_fetch_array($result_test));

					$query_fac_types_update = "UPDATE `facility_types` " .
						"SET `name` = " . csv2mysql($line[FACILITY_TYPES_TYPE]) . ", " .
						"`description` = " . csv2mysql($line[FACILITY_TYPES_DESCRIPTION]) . ", " .
						"`bg_color` = " . csv2mysql($bgcolor) . ", " .
						"`text_color` = " . csv2mysql($textcolor) . " " .
						"WHERE `id` = '" . $row_test['id'] . "';";

					$result_update = db_query($query_fac_types_update, __FILE__, __LINE__);
					if (db_affected_rows($result_update) > 0) {

						$query_fac_types_update = "UPDATE `facility_types` " .
							"SET `user_id` = '" . $user_id . "', " .
							"`client_address`= '" . $client_address . "', " .
							"`updated` = '" . $datetime_now . "' " .
							"WHERE `id` = '" . $row_test['id'] . "';";

						db_query($query_fac_types_update, __FILE__, __LINE__);
						$fac_types_updated++;
					} else {
						$doublette++;
					}
				}
			}
			break;
		case "fac_status":
		case "facility_status":
			if ((strtolower($line[FACILITY_STATUS_DELETE]) == "yes") || (strtolower($line[FACILITY_STATUS_DELETE]) == "Ja")) {

				$query_fac_status_del = "DELETE FROM `facility_status` " .
					"WHERE `status_name` = " . csv2mysql($line[FACILITY_STATUS_STATUS]) . ";";

				$result_delete = db_query($query_fac_status_del, __FILE__, __LINE__);
				if (db_affected_rows($result_delete) > 0) {
					$fac_status_del++;
				}
			} else {

				$query_test = "SELECT `id` " .
					"FROM `facility_status` " .
					"WHERE `status_name` = " . csv2mysql($line[FACILITY_STATUS_STATUS]) . ";";

				$result_test = db_query($query_test, __FILE__, __LINE__);
				if (db_num_rows($result_test) == 0) {
					insert_into_facility_status(csv2raw($line[FACILITY_STATUS_STATUS]), csv2raw($line[FACILITY_STATUS_DESCRIPTION]), 
						csv2raw($line[FACILITY_STATUS_SORT]), csv2raw($line[FACILITY_STATUS_DISPLAY]), csv2raw($line[FACILITY_STATUS_BGCOLOR]), 
						csv2raw($line[FACILITY_STATUS_TEXTCOLOR]), $user_id, $datetime_now);
					$fac_status++;
				} else {
					$row_test = stripslashes_deep(db_fetch_array($result_test));

					$query_fac_status_update = "UPDATE `facility_status` SET " .
						"`status_name` = " . csv2mysql($line[FACILITY_STATUS_STATUS]) . ", " .
						"`description` = " . csv2mysql($line[FACILITY_STATUS_DESCRIPTION]) . ", " .
						"`display` = " . csv2mysql($line[FACILITY_STATUS_DISPLAY]) . ", " .
						"`sort` = " . csv2mysql($line[FACILITY_STATUS_SORT]) . ", " .
						"`bg_color` = " . csv2mysql($line[FACILITY_STATUS_BGCOLOR]) . ", " .
						"`text_color` = " . csv2mysql($line[FACILITY_STATUS_TEXTCOLOR]) . " WHERE " .
						"`id` = '" . $row_test['id'] . "';";

					$result_update = db_query($query_fac_status_update, __FILE__, __LINE__);
					if (db_affected_rows($result_update) > 0) {

						$query_fac_status_update = "UPDATE `facility_status` SET " .
							"`user_id` = '" . $user_id . "', " .
							"`client_address`= '" . $client_address . "', " .
							"`updated` = '" . $datetime_now . "' " .
							"WHERE `id` = '" . $row_test['id'] . "';";

						db_query($query_fac_status_update, __FILE__, __LINE__);
						$fac_status_updated++;
					} else {
						$doublette++;
					}
				}
			}
			break;
		case "facilities":

			$query_test = "SELECT `id` " .
				"FROM `facility_types` " .
				"WHERE `name` = " . csv2mysql($line[FACILITY_TYPE]) . " " .
				"LIMIT 1;";

			$result_test = db_query($query_test, __FILE__, __LINE__);
			if (db_num_rows($result_test) == 0) {

				$query_test = "SELECT `id` " .
					"FROM `facility_types` " .
					"ORDER BY 'id' ASC " .
					"LIMIT 1;";

				$result_test = db_query($query_test, __FILE__, __LINE__);
			}
			$facilitiy_type = db_fetch_array($result_test);

			if ((strtolower($line[FACILITY_DELETE]) == "yes") || (strtolower($line[FACILITY_DELETE]) == "Ja")) {

				$query_facilitiy_id = "SELECT `id` " .
					"FROM `facilities` " .
					"WHERE `handle` = " . csv2mysql($line[FACILITY_HANDLE]) . " " .
					"AND `type` = " . $facilitiy_type['id'];

				$result_facilitiy_id = db_query($query_facilitiy_id, __FILE__, __LINE__);
				$facilitiy_id_or_str = "";
				while ($facilitiy_row = db_fetch_array($result_facilitiy_id)) {
					$facilitiy_id_or_str .= " OR `resource_id` = " . $facilitiy_row['id'];
				}

				$query_facilitiy_del = "DELETE FROM `allocates` " .
					"WHERE (`resource_id` = 0" . $facilitiy_id_or_str . ") " .
					"AND `type` = " . $GLOBALS['TYPE_FACILITY'] . " " .
					"LIMIT 1;";

				$result_delete = db_query($query_facilitiy_del, __FILE__, __LINE__);
				if (db_affected_rows($result_delete) > 0) {
					$facilitiy_del++;
				}
			} else {
				$do_import = true;
				$do_update = false;
				$update_id = 0;
				$updated = false;

				$query_test = "SELECT `id` " .
					"FROM `facilities` " .
					"WHERE `handle` = " . csv2mysql($line[FACILITY_HANDLE]) . " " .
					"AND `type` = " . $facilitiy_type['id'];

				$result_test = db_query($query_test, __FILE__, __LINE__);
				if (db_num_rows($result_test) != 0) {
					while ($row_test = db_fetch_array($result_test)) {

						$query_test2 = "SELECT `id` " .
							"FROM `allocates` " .
							"WHERE (`resource_id` = " . $row_test["id"] . ") " .
							"AND (`type` = " . $GLOBALS['TYPE_FACILITY'] . ");";

						$result_test2 = db_query($query_test2, __FILE__, __LINE__);
						if (db_num_rows($result_test2) != 0) {
							$do_import = false;
							$do_update = true;
							$update_id = $row_test["id"];
						}
					}
				}
				if ($do_import || $do_update) {
					$fac_lat = "0.999999";
					if (!empty ($line[FACILITY_LAT])) {
						$fac_lat = $line[FACILITY_LAT];
					}
					$fac_lng = "0.999999";
					if (!empty ($line[FACILITY_LNG])) {
						$fac_lng = $line[FACILITY_LNG];
					}
					$facility_admin_only = 0;
					if (($line[FACILITY_ADMIN_ONLY] == "yes") || ($line[FACILITY_ADMIN_ONLY] == "Ja")) {
						$facility_admin_only = 1;
					}
				}
				if ($do_import) {
					$new_id = insert_into_facilities(csv2raw($line[FACILITY_NAME]), csv2raw($line[FACILITY_HANDLE]), csv2raw($line[FACILITY_OBJECT_ID]), csv2raw($line[FACILITY_PAGER1]),
						csv2raw($line[FACILITY_PAGER2]), csv2raw($line[FACILITY_STREET]), csv2raw($line[FACILITY_CITY]), csv2raw($line[FACILITY_SEC_CONTACT]),
						csv2raw($line[FACILITY_SEC_PHONE]), csv2raw($line[FACILITY_SEC_MAIL]), $facilitiy_type['id'], 1,
						csv2raw($line[FACILITY_DESCRIPTION]), csv2raw($line[FACILITY_CAPABILITIES]), csv2raw($line[FACILITY_OPENING]), csv2raw($line[FACILITY_ACCESS]),
						csv2raw($line[FACILITY_CONTACT]), csv2raw($line[FACILITY_PHONE]), csv2raw($line[FACILITY_MAIL]), $facility_admin_only,
						csv2raw($line[FACILITY_ICON]), "", $fac_lat, $fac_lng,
						0, csv2raw($line[FACILITY_UPDATED]));
					$regions = array ();
					$regions = explode ("/", csv2mysql($line[FACILITY_REGIONS]));
					foreach ($regions as $region) {

						$query_test = "SELECT `id` " .
							"FROM `regions` " .
							"WHERE `region_name` = " . $region . " " .
							"LIMIT 1;";

						$result_test = db_query($query_test, __FILE__, __LINE__);
						if (db_num_rows($result_test) != 0) {
							$group_id = db_fetch_array($result_test);
							insert_into_allocates($group_id['id'], $GLOBALS['TYPE_FACILITY'], $new_id, $user_id, $datetime_now);
						}
					}

					$query_test = "SELECT `id` " .
						"FROM `allocates` " .
						"WHERE (`resource_id` = (SELECT MAX(`id`) FROM `facilities`)) " .
						"AND (`type` = " . $GLOBALS['TYPE_FACILITY'] . ");";

					$result_test = db_query($query_test, __FILE__, __LINE__);
					if (db_num_rows($result_test) == 0) {

						$query_test = "SELECT `id` " .
							"FROM `regions` " .
							"ORDER BY 'id' ASC " .
							"LIMIT 1;";

						$result_test = db_query($query_test, __FILE__, __LINE__);
						$group_id = db_fetch_array($result_test);
						insert_into_allocates($group_id['id'], $GLOBALS['TYPE_FACILITY'], $new_id, $user_id, $datetime_now);
					}
					$facilitiy++;
				} else {
					if ($do_update) {

						$query_facilitiy = "UPDATE `facilities` SET " .
							"`name` = " . csv2mysql($line[FACILITY_NAME]) . ", " .
							"`icon_url` = " . csv2mysql($line[FACILITY_ICON]) . ", " .
							"`type` = '" . $facilitiy_type['id'] . "', " .
							"`street` = " . csv2mysql($line[FACILITY_STREET]) . ", " .
							"`city` = " . csv2mysql($line[FACILITY_CITY]) . ", " .
							"`description` = " . csv2mysql($line[FACILITY_DESCRIPTION]) . ", " .
							"`capabilities` =  " . csv2mysql($line[FACILITY_CAPABILITIES]) . ", " .
							"`contact_name` = " . csv2mysql($line[FACILITY_CONTACT]) . ", " .
							"`contact_email` = " . csv2mysql($line[FACILITY_MAIL]) . ", " .
							"`contact_phone` = " . csv2mysql($line[FACILITY_PHONE]) . ", " .
							"`security_contact` = " . csv2mysql($line[FACILITY_SEC_CONTACT]) . ", " .
							"`security_email` = " . csv2mysql($line[FACILITY_SEC_MAIL]) . ", " .
							"`admin_only` = " . $facility_admin_only . ", " .
							"`security_phone` = " . csv2mysql($line[FACILITY_SEC_PHONE]) . ", " .
							"`opening_hours` = " . csv2mysql($line[FACILITY_OPENING]) . ", " .
							"`access_rules` = " . csv2mysql($line[FACILITY_ACCESS]) . ", " .
							"`direct_dialing_1` = " . csv2mysql($line[FACILITY_PAGER1]) . ", " .
							"`direct_dialing_2` = " . csv2mysql($line[FACILITY_PAGER2]) . ", " .
							"`lat` = '" . $fac_lat . "', " .
							"`lng` = '" . $fac_lng . "', " .
							"`object_id` = " . csv2mysql($line[FACILITY_OBJECT_ID]) . " " .
							"WHERE `id` = '" . $update_id . "';";

						$result_update = db_query($query_facilitiy, __FILE__, __LINE__);
						if (db_affected_rows($result_update) > 0) {

							$query_facilitiy = "UPDATE `facilities` SET " .
								"`updated` = '" . $datetime_now . "', " .
								"`client_address`= '" . $client_address . "', " .
								"`user_id` = " . $user_id . " " .
								"WHERE `id` = '" . $update_id . "';";

							db_query($query_facilitiy, __FILE__, __LINE__);
							$updated = true;
						}
						$regions = array ();
						$regions = explode ("/", csv2mysql($line[FACILITY_REGIONS]));
						foreach ($regions as $region) {

							$query_test = "SELECT `id` " .
								"FROM `regions` " .
								"WHERE `region_name` = " . $region . " " .
								"LIMIT 1;";

							$result_test = db_query($query_test, __FILE__, __LINE__);
							if (db_num_rows($result_test) != 0) {

								$query_test2 = "SELECT `id` " .
									"FROM `allocates` " .
									"WHERE `resource_id` = '" . $update_id . "' " .
									"AND `type` = " . $GLOBALS['TYPE_FACILITY'] . " " .
									"LIMIT 1;";

								$result_test2 = db_query($query_test2, __FILE__, __LINE__);
								if (db_num_rows($result_test2) == 0) {
									$group_id = db_fetch_array($result_test);
									insert_into_allocates($group_id['id'], $GLOBALS['TYPE_FACILITY'], $update_id, $user_id, $datetime_now);
									$updated = true;
								}
							} else {

								$query_test = "SELECT `group` " .
									"FROM `allocates` " .
									"WHERE `resource_id` = " . $update_id . " " .
									"AND `type` = " . $GLOBALS['TYPE_FACILITY'] . " " .
									"AND `group` = (SELECT `id` FROM `regions` ORDER BY 'id' ASC LIMIT 1);";

								$result_test = db_query($query_test, __FILE__, __LINE__);
								$group_id = db_fetch_array($result_test);
								if (db_num_rows($result_test) == 0) {
									insert_into_allocates($group_id['id'], $GLOBALS['TYPE_FACILITY'], $update_id, $user_id, $datetime_now);
									$updated = true;
								}
							}
						}
						if ($updated) {
							$facilitiy_updated++;
						} else {
							$doublette++;
						}
					}
				}
			}
			break;
		default:
		}
	}
	fclose($uploadfile);
	if ($fac_types > 0) {
		$top_notice_log_str .= get_text("Dataset fac_types added") . ": " . $fac_types . ", ";
		$top_notice_str .= get_text("Dataset fac_types added") . ": " . $fac_types . "<br>";
	}
	if ($fac_types_updated > 0) {
		$top_notice_log_str .= get_text("Dataset fac_types updated") . ": " . $fac_types_updated . ", ";
		$top_notice_str .= get_text("Dataset fac_types updated") . ": " . $fac_types_updated . "<br>";
	}
	if ($fac_types_del > 0) {
		$top_notice_log_str .= get_text("Dataset fac_types deleted") . ": " . $fac_types_del . ", ";
		$top_notice_str .= get_text("Dataset fac_types deleted") . ": " . $fac_types_del . "<br>";
	}
	if ($fac_status > 0) {
		$top_notice_log_str .= get_text("Dataset fac_status added") . ": " . $fac_status . ", ";
		$top_notice_str .= get_text("Dataset fac_status added") . ": " . $fac_status . "<br>";
	}
	if ($fac_status_updated > 0) {
		$top_notice_log_str .= get_text("Dataset fac_status updated") . ": " . $fac_status_updated . ", ";
		$top_notice_str .= get_text("Dataset fac_status updated") . ": " . $fac_status_updated . "<br>";
	}
	if ($fac_status_del > 0) {
		$top_notice_log_str .= get_text("Dataset fac_status deleted") . ": " . $fac_status_del . ", ";
		$top_notice_str .= get_text("Dataset fac_status deleted") . ": " . $fac_status_del . "<br>";
	}
	if ($facilitiy > 0) {
		$top_notice_log_str .= get_text("Dataset facility added") . ": " . $facilitiy . ", ";
		$top_notice_str .= get_text("Dataset facility added") . ": " . $facilitiy . "<br>";
	}
	if ($facilitiy_updated > 0) {
		$top_notice_log_str .= get_text("Dataset facility updated") . ": " . $facilitiy_updated . ", ";
		$top_notice_str .= get_text("Dataset facility updated") . ": " . $facilitiy_updated . "<br>";
	}
	if ($facilitiy_del > 0) {
		$top_notice_log_str .= get_text("Dataset facility deleted") . ": " . $facilitiy_del . ", ";
		$top_notice_str .= get_text("Dataset facility deleted") . ": " . $facilitiy_del . "<br>";
	}
	break;
case "incident-types":
case "default-incident-types":
	define("INCIDENT_TYPES_TABLE", 0);
	define("INCIDENT_TYPES_TYPE", 1);
	define("INCIDENT_TYPES_SEVERITY", 2);
	define("INCIDENT_TYPES_GROUP", 3);
	define("INCIDENT_TYPES_SORT", 4);
	define("INCIDENT_TYPES_DESCRIPTION", 5);
	define("INCIDENT_TYPES_PROTOCOL", 6);
	define("INCIDENT_TYPES_DELETE", 7);
	$line = "";
	$in_types = $in_types_updated = $in_types_del = 0;
	if ($function == "default-incident-types") {
		$uploadfile = fopen(get_current_path("sql/" . $filename), "rb");
	} else {
		$uploadfile = fopen($_FILES['file']['tmp_name'], "rb");
	}
	while (!feof($uploadfile)) {
		$line = fgetcsv($uploadfile, 4096, ";");
		$severity = 0;
		if (($line != false) && ($line[INCIDENT_TYPES_TABLE] == "incident_types")) {
			if ((strtolower($line[INCIDENT_TYPES_DELETE]) == "yes") || (strtolower($line[INCIDENT_TYPES_DELETE]) == "Ja")) {

				$in_types_del_query = "DELETE FROM `incident_types` " .
					"WHERE `type` = " . csv2mysql($line[INCIDENT_TYPES_TYPE]) . " " .
					"LIMIT 1;";

				$result_delete = db_query($in_types_del_query, __FILE__, __LINE__);
				$in_types_del = $in_types_del + db_affected_rows($result_delete);
			} else {
				if ((strtolower($line[INCIDENT_TYPES_SEVERITY]) == "notfall") || (strtolower($line[INCIDENT_TYPES_SEVERITY]) == "high")) {
					$severity = 2;
				} else {
					if ((strtolower($line[INCIDENT_TYPES_SEVERITY]) == "sofort") || (strtolower($line[INCIDENT_TYPES_SEVERITY]) == "medium")) {
						$severity = 1;
					}
				}

				$query_test = "SELECT `id` " .
					"FROM `incident_types` " .
					"WHERE `type` = " . csv2mysql($line[INCIDENT_TYPES_TYPE]) . " " .
					"LIMIT 1;";

				$result_test = db_query($query_test, __FILE__, __LINE__);
				if (db_num_rows($result_test) == 0) {
					$result = insert_into_incident_types(csv2raw($line[INCIDENT_TYPES_TYPE]), csv2raw($line[INCIDENT_TYPES_DESCRIPTION]), 
						csv2raw($line[INCIDENT_TYPES_PROTOCOL]), $severity,	csv2raw($line[INCIDENT_TYPES_GROUP]), 
						csv2raw($line[INCIDENT_TYPES_SORT]), $user_id, $datetime_now);
					$in_types = $in_types + db_affected_rows($result);
				} else {
					$row_test = stripslashes_deep(db_fetch_array($result_test));

					$query_nature = "UPDATE `incident_types` SET " .
						"`description` = " . csv2mysql($line[INCIDENT_TYPES_DESCRIPTION]) . ", " .
						"`protocol` = " . csv2mysql($line[INCIDENT_TYPES_PROTOCOL]) . ", " .
						"`set_severity` = '" . $severity . "', " .
						"`sort` = " . csv2mysql($line[INCIDENT_TYPES_SORT]) . ", " .
						"`group` = " . csv2mysql($line[INCIDENT_TYPES_GROUP]) . " " .
						"WHERE `id` = '" . $row_test['id'] . "';";

					$result_update = db_query($query_nature, __FILE__, __LINE__);
					if (db_affected_rows($result_update) > 0) {
						$in_types_updated++;
					} else {
						$doublette++;
					}
				}
			}
		}
	}
	fclose($uploadfile);
	if ($in_types > 0) {
		$top_notice_log_str .= get_text("Dataset in_types added") . ": " . $in_types . ", ";
		$top_notice_str .= get_text("Dataset in_types added") . ": " . $in_types . "<br>";
	}
	if ($in_types_updated > 0) {
		$top_notice_log_str .= get_text("Dataset in_types updated") . ": " . $in_types_updated . ", ";
		$top_notice_str .= get_text("Dataset in_types updated") . ": " . $in_types_updated . "<br>";
	}
	if ($in_types_del > 0) {
		$top_notice_log_str .= get_text("Dataset in_types deleted") . ": " . $in_types_del . ", ";
		$top_notice_str .= get_text("Dataset in_types deleted") . ": " . $in_types_del . "<br>";
	}
	break;
case "textblocks":
case "default-textblocks":
	define("TEXTBLOCKS_TYPE", 0);
	define("TEXTBLOCKS_TEXT", 1);
	define("TEXTBLOCKS_CODE", 2);
	define("TEXTBLOCKS_SORT", 3);
	define("TEXTBLOCKS_DELETE", 4);
	$line = "";
	$data_array = array ();
	$data_array["synopsis"]["type"] = "textblocks_syn";
	$data_array["description"]["type"] = "textblocks_desc";
	$data_array["action"]["type"] = "textblocks_act";
	$data_array["assign"]["type"] = "textblocks_ass";
	$data_array["close"]["type"] = "textblocks_clo";
	$data_array["log"]["type"] = "textblocks_log";
	$data_array["message"]["type"] = "textblocks_msg";
	$data_array["synopsis"]["insert"] = $data_array["synopsis"]["update"] = $data_array["synopsis"]["delete"] = 0;
	$data_array["description"]["insert"] = $data_array["description"]["update"] = $data_array["description"]["delete"] = 0;
	$data_array["action"]["insert"] = $data_array["action"]["update"] = $data_array["action"]["delete"] = 0;
	$data_array["assign"]["insert"] = $data_array["assign"]["update"] = $data_array["assign"]["delete"] = 0;
	$data_array["close"]["insert"] = $data_array["close"]["update"] = $data_array["close"]["delete"] = 0;
	$data_array["log"]["insert"] = $data_array["log"]["update"] = $data_array["log"]["delete"] = 0;
	$data_array["message"]["insert"] = $data_array["message"]["update"] = $data_array["message"]["delete"] = 0;
	if ($function == "default-textblocks") {
		$uploadfile = fopen(get_current_path("sql/" . $filename), "rb");
	} else {
		$uploadfile = fopen($_FILES['file']['tmp_name'], "rb");
	}
	$i = 1;
	while (!feof($uploadfile)) {
		$line = fgetcsv($uploadfile, 4096, ";");
		foreach ($data_array as $key => $value) {
			if (($line != false) && ($line[TEXTBLOCKS_TYPE] == $value["type"])) {
				if ((strtolower($line[TEXTBLOCKS_DELETE]) == "yes") || (strtolower($line[TEXTBLOCKS_DELETE]) == "Ja")) {

					$query_delete = "DELETE FROM `textblocks` " .
						"WHERE `text` = " . csv2mysql($line[TEXTBLOCKS_TEXT]) . " " .
						"AND `type` = '" . $key . "'";

					$result_delete = db_query($query_delete, __FILE__, __LINE__);
					$data_array[$key]["delete"] = $data_array[$key]["delete"] + db_affected_rows($result_delete);
				} else {

					$query_test = "SELECT `id` " .
						"FROM `textblocks` " .
						"WHERE `text` = " . csv2mysql($line[TEXTBLOCKS_TEXT]) . " " .
						"AND `type` = '" . $key . "'";

					$result_test = db_query($query_test, __FILE__, __LINE__);
					if (db_num_rows($result_test) == 0) {
						$data_array[$key]["query"]["key"][$i] = $key;
						$data_array[$key]["query"]["code"][$i] = csv2raw($line[TEXTBLOCKS_CODE]);
						$data_array[$key]["query"]["text"][$i] = csv2raw($line[TEXTBLOCKS_TEXT]);
						$data_array[$key]["query"]["sort"][$i] = csv2raw($line[TEXTBLOCKS_SORT]);
						$data_array[$key]["query"]["client_address"][$i] = $client_address;
						$data_array[$key]["query"]["timestamp"][$i] = $datetime_now;
						$data_array[$key]["insert"]++;
					} else {
						$row_test = stripslashes_deep(db_fetch_array($result_test));

						$query_textblocks_update = "UPDATE  `textblocks` SET " .
							"`group` = " . csv2mysql($line[TEXTBLOCKS_CODE]) . ", " .
							"`text` = " . csv2mysql($line[TEXTBLOCKS_TEXT]) . ", " .
							"`sort` = " . csv2mysql($line[TEXTBLOCKS_SORT]) . " WHERE " .
							"`id` = '" . $row_test['id'] . "';";

						$result_update = db_query($query_textblocks_update, __FILE__, __LINE__);
						if (db_affected_rows($result_update) > 0) {

							$query_textblocks_update = "UPDATE `textblocks` SET " .
								"`user_id` = '" . $user_id . "', " .
								"`client_address`= '" . $client_address . "', " .
								"`updated` = '" . $datetime_now . "' " .
								"WHERE `id` = '" . $row_test['id'] . "';";

							db_query($query_textblocks_update, __FILE__, __LINE__);
							$data_array[$key]["update"]++;
						} else {
							$doublette++;
						}
					}
				}
			}
		}
		$i++;
	}
	fclose($uploadfile);
	foreach ($data_array as $key => $value) {
		if ($value["insert"] > 0) {
			$affected_rows = 0;
			for ($j = 1; $j <= $i; $j++) {
				if (isset ($data_array[$key]["query"]["key"][$j])) {
					$result = insert_into_textblocks($data_array[$key]["query"]["key"][$j], 
						$data_array[$key]["query"]["code"][$j], $data_array[$key]["query"]["text"][$j], "", 0, 
						$data_array[$key]["query"]["sort"][$j], $user_id, $data_array[$key]["query"]["timestamp"][$j]);
					$affected_rows += db_affected_rows($result);
				}
			}
			$top_notice_log_str .= get_text("Dataset textblocks " . $key . " added") . ": " . $affected_rows . ", ";
			$top_notice_str .= get_text("Dataset textblocks " . $key . " added") . ": " . $affected_rows . "<br>";
		}
		if ($value["update"] > 0) {
			$top_notice_log_str .= get_text("Dataset textblocks " . $key . " updated") . ": " . $value["update"] . ", ";
			$top_notice_str .= get_text("Dataset textblocks " . $key . " updated") . ": " . $value["update"] . "<br>";
		}
		if ($value["delete"] > 0) {
			$top_notice_log_str .= get_text("Dataset textblocks " . $key . " deleted") . ": " . $value["delete"] . ", ";
			$top_notice_str .= get_text("Dataset textblocks " . $key . " deleted") . ": " . $value["delete"] . "<br>";
		}
	}
	break;
case "captions":
	define("CAPTIONSHINTS_TABLE", 0);
	define("CAPTIONSHINTS_CAPTION", 1);
	define("CAPTIONSHINTS_REPLACE", 2);
	$line = "";
	$captions = $hints = 0;
	$uploadfile = fopen($_FILES['file']['tmp_name'], "rb");
	while (!feof($uploadfile)) {
		$line = fgetcsv($uploadfile, 4096, ";");
		if ((is_array($line)) && ($line[CAPTIONSHINTS_TABLE] == "captions")) {

			$query_captions = "UPDATE `captions` SET " .
				"`repl` = " . csv2mysql($line[CAPTIONSHINTS_REPLACE]) . " " .
				"WHERE `capt` = " . csv2mysql($line[CAPTIONSHINTS_CAPTION]) . ";";

			$result_update = db_query($query_captions, __FILE__, __LINE__);
			if (db_affected_rows($result_update) > 0) {
				$captions++;
				//$top_notice_str .= csv2mysql($line[CAPTIONSHINTS_REPLACE]) . "<br>";

				$query_captions = "UPDATE `captions` SET " .
					"`user_id` = " . $user_id . ", `client_address`= '" . $client_address . "', " .
					"`updated` = '" . $datetime_now . "' WHERE " .
					"`capt` = " . csv2mysql($line[CAPTIONSHINTS_CAPTION]) . ";";

				db_query($query_captions, __FILE__, __LINE__);
			} else {
				$doublette++;
			}
		}
		if ((is_array($line)) && ($line[CAPTIONSHINTS_TABLE] == "hints")) {

			$query_hints = "UPDATE `hints` SET " .
				"`hint` = " . csv2mysql($line[CAPTIONSHINTS_REPLACE]) . " " .
				"WHERE `tag` = " . csv2mysql($line[CAPTIONSHINTS_CAPTION]) . ";";

			$result_update = db_query($query_hints, __FILE__, __LINE__);
			if (db_affected_rows($result_update) > 0) {
				$hints++;

				$query_hints = "UPDATE `hints` SET " .
					"`user_id` = '" . $user_id . "', `client_address`= '" . $client_address . "', " .
					"`updated` = '" . $datetime_now . "' WHERE " .
					"`tag` = " . csv2mysql($line[CAPTIONSHINTS_CAPTION]) . ";";

				db_query($query_hints, __FILE__, __LINE__);
			} else {
				$doublette++;
			}
		}
	}
	fclose($uploadfile);
	if ($captions > 0) {
		$top_notice_log_str .= get_text("Dataset captions updated") . ": " . $captions . ", ";
		$top_notice_str .= get_text("Dataset captions updated") . ": " . $captions . "<br>";
	}
	if ($hints > 0) {
		$top_notice_log_str .= get_text("Dataset hints updated") . ": " . $hints . ", ";
		$top_notice_str .= get_text("Dataset hints updated") . ": " . $hints . "<br>";
	}
	break;
case "settings":
	$do_not_import = array ("_version", "_locale");
	define("SETTINGS_TABLE", 0);
	define("SETTINGS_VARIABLE", 1);
	define("SETTINGS_VALUE", 2);
	$line = "";
	$settings = 0;
	$uploadfile = fopen($_FILES['file']['tmp_name'], "rb");
	while (!feof($uploadfile)) {
		$line = fgetcsv($uploadfile, 4096, ";");
		if (($line != false) && ($line[SETTINGS_TABLE] == "settings") && (in_array($line[SETTINGS_VARIABLE], $do_not_import) == false)) {

			$query_settings = "UPDATE `settings` SET " .
				"`value` = " . csv2mysql($line[SETTINGS_VALUE]) . " " .
				"WHERE `name` = " . csv2mysql($line[SETTINGS_VARIABLE]) . ";";

			$result_update = db_query($query_settings, __FILE__, __LINE__);
			if (db_affected_rows($result_update) > 0) {
				$settings++;
				//$top_notice_str .= csv2mysql($line[CAPTIONSHINTS_REPLACE]) . "<br>";
			} else {
				$doublette++;
			}
		}
	}
	fclose($uploadfile);
	if ($settings > 0) {
		$top_notice_log_str .= get_text("Dataset settings updated") . ": " . $settings . ", ";
		$top_notice_str .= get_text("Dataset settings updated") . ": " . $settings . "<br>";
	}
	break;
default:
}
if ($doublette > 0) {
	$top_notice_log_str .= get_text("Unsaved duplicates") . ": " . $doublette . ", ";
	$top_notice_str .= get_text("Unsaved duplicates") . ": " . $doublette . "<br>";
}
if ((!empty ($_FILES)) && ($_FILES['file']['size'] >= $filesize)) {
	$top_notice_log_str .= get_text("Max. Filesize(kb)") . ": " . $filesize . ", ";
	$top_notice_str .= get_text("Max. Filesize(kb)") . ": " . $filesize . "<br>";
}
if ($top_notice_str == "") {
	$top_notice_log_str .= get_text("Nothing to do - Caution: Use CSV-Fileformat with Semicolon as Field delimiter!") . ", ";
	$top_notice_str .= get_text("Nothing to do - Caution: Use CSV-Fileformat with Semicolon as Field delimiter!") . "<br>";
}
$top_notice_log_str = substr(get_text("Data Import") . ": " . $top_notice_log_str, 0, -2);
$top_notice_str = get_text("Data Import") . ": <br>" . $top_notice_str;
switch ($function) {
case "default-incident-types":
case "default-textblocks":
	$json_top_notice = array (
		"top_notice_log_str" => $top_notice_log_str,
		"top_notice_str" => $top_notice_str
	);
	print json_encode($json_top_notice);
	break;
default:
	?>
<html>
	<body onload="document.frm1.submit();">
		<form action="configuration.php" name="frm1" method="post">
			<input type="hidden" name="top_notice" value="<?php print $top_notice_str;?>" />
			<input type="hidden" name="top_notice_logstr" value="<?php print $top_notice_log_str;?>" />
		</form>
	</body>
</html>
	<?php
}
?>