<?php
error_reporting(E_ALL);
require_once ("./incs/functions.inc.php");
require_once ("./incs/api.inc.php");
require_once ("./incs/log_codes.inc.php");

$datetime_now = mysql_datetime();
$http_status_code = 200;
$result_str = "";
// GET data =================================================
$password = false;
if (isset ($_GET['password'])) {
	$password = urldecode($_GET['password']);
} else {
	$http_status_code = 400;
}
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
switch ($function) {
case "example_data":
	break;
default:
	$source = "";
	$source_regexp = "";
	if ((isset ($_GET['source'])) && ($_GET['source'] != "")) {
		$source = $_GET['source'];
		$source = urldecode($_GET['source']);
		if ((isset ($_GET['source_regexp'])) && ($_GET['source_regexp'] != "")) {
			$source_regexp = urldecode($_GET['source_regexp']);
		}
	} else {
		$http_status_code = 400;
	}
	$destination = "";
	if (isset ($_GET['destination'])) {
		$destination = urldecode($_GET['destination']);
	} else {
		$http_status_code = 400;
	}
	$destination_alias = null;
	if (isset ($_GET['destination_alias'])) {
		$destination_alias = urldecode($_GET['destination_alias']);
	}
	$audio_link = null;
	if (isset ($_GET['audio_link'])) {
		$audio_link = urldecode($_GET['audio_link']);
	}
	$code = false;
	if (isset ($_GET['code'])) {
		$code = urldecode($_GET['code']);
	} else {
		$http_status_code = 400;
	}
	$text = false;
	if (isset ($_GET['text'])) {
		$text = urldecode($_GET['text']);
	}
	$lat = null;
	$lon = null;
	if ((isset ($_GET['lat'])) && (isset ($_GET['lon']))) {
		$lat = urldecode($_GET['lat']);
		$lon = urldecode($_GET['lon']);
	}
	// Validate password ========================================
	$gateway_user = false;
	$host_allowed = false;
	if ($password) {

		$query 	= "SELECT * " .
			"FROM `users` " .
			"WHERE `id` = " . get_variable("_api_user_id") . " " .
			"AND (`password` = PASSWORD('" . $password . "') " .
			"OR `password` = MD5('" . $password . "')) LIMIT 1;";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_affected_rows($result) == 1) {
			$gateway_user = true;
			$_SESSION['user_id'] = get_variable("_api_user_id");
		} else {
			$http_status_code = 401;
		}
	}
	$allowed_hosts = explode(",", get_variable("_api_hosts"));
	foreach ($allowed_hosts as $value) {
		if (isset ($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] == strtolower(str_replace(' ','', $value)))) {
			$host_allowed = true;
		}
		if (isset ($_SERVER['REMOTE_HOST']) && ($_SERVER['REMOTE_HOST'] == strtolower(str_replace(' ','', $value)))) {
			$host_allowed = true;
		}
	}
	if ($host_allowed == false) {
		$http_status_code = 403;
	}
	if ($host_allowed && $gateway_user) {
	// Get code settings ========================================
		if ($code) {

			$query = "SELECT `name`, " .
				"`value` " .
				"FROM `settings` " .
				"WHERE `name` LIKE '_api_%_encdg' " .
				"ORDER BY `id` ASC;";

			$result = db_query($query, __FILE__, __LINE__);
			$function = false;
			$i = 0;
			while ($row = stripslashes_deep(db_fetch_array($result))) {
				$value_array = explode(",", $row['value']);
				foreach ($value_array as $value) {
					if ((strcmp(strtolower($code), strtolower(str_replace(' ','', $value))) == 0) && ($function == ""))  {
						$function = $row['name'];
					}
					$i++;
				}
			}
			$connection_test_array = get_connection_test_configuration();
			if ($code == $connection_test_array["code"]) {
				$function = "_connection_test";
			}
	// Unit =====================================================
			$unit_data = get_unit_data($function, $source, $source_regexp);
			$result_str .=  "Einheit-ID: " . $unit_data["unit_id"] . "<br>";
			$result_str .=  "Funk-Code: " . $unit_data["unit_handle"] . "<br>";
			$result_str .=  "Funkrufname: " . $unit_data["unit_name"] . "<br>";
			$result_str .=  "Status: " . $unit_data["unit_status"] . "<br>";
			$result_str .=  "Disponierbar: " . $unit_data["unit_dispatchable"] . "<br>";
			if ($unit_data["multiple_address_error"] != "") {
				$function = "_multiple_address";
				$result_str .= $unit_data["multiple_address_error"] . "<br>";
			}
			if ($unit_data["store_unknown_unit_data"]) {
				$result_str .=  "Daten von unbekannter Einheit werden gespeichert<br>";
			}
			if ($unit_data["store_unknown_unit_position_report"] != "") {
				$result_str .=  "Ortsbericht von unbekannter Einheit speichern: " . $unit_data["store_unknown_unit_position_report"] . "<br>";
			}
	// Disposition ==============================================
			$assign_data = get_assigns($unit_data["unit_id"], 0);
			$result_str .=  "Disposition-ID: " . $assign_data[0] . "<br>";
			$result_str .=  "Einsatz-ID: " . $assign_data[1] . "<br>";
	// Logging ==================================================
			$do_receipt = false;
			switch ($function) {
			case "_api_emgcy_hi_encdg":
				$result_str .= "Notruf" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_log($GLOBALS['LOG_EMGCY_HI'], $assign_data[1], $unit_data["unit_id"], "", 0, $datetime_now);
					} else {
						do_log($GLOBALS['LOG_EMGCY_HI'], 0, $unit_data["unit_id"], "", 0, $datetime_now);
					}
					do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_EMGCY_HI'], $types[$GLOBALS['LOG_EMGCY_HI']], $lat, $lon);
					$result_str .= "TRUE";
				}
				$do_receipt = false;
				break;
			case "_api_emgcy_lo_encdg":
				$result_str .= "Hilferuf(0)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_log($GLOBALS['LOG_EMGCY_LO'], $assign_data[1], $unit_data["unit_id"], "", 0, $datetime_now);
					} else {
						do_log($GLOBALS['LOG_EMGCY_LO'], 0, $unit_data["unit_id"], "", 0, $datetime_now);
					}
					do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_EMGCY_LO'], $types[$GLOBALS['LOG_EMGCY_LO']], $lat, $lon);
					$result_str .= "TRUE";
				}
				$do_receipt = false;
				break;
			case "_api_callreq_encdg":
				$result_str .= "Sprechwunsch(5)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_log($GLOBALS['LOG_CALL_REQ'], $assign_data[1], $unit_data["unit_id"], "", 0, $datetime_now);
					} else {
						do_log($GLOBALS['LOG_CALL_REQ'], 0, $unit_data["unit_id"], "", 0, $datetime_now);
					}
					do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_REQ'], $types[$GLOBALS['LOG_CALL_REQ']], $lat, $lon);
					$result_str .= "TRUE";
				}
				$do_receipt = false;
				break;
			case "_api_manackn_encdg":
				$result_str .= "Handquittung(9)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_log($GLOBALS['LOG_CALL_MANACKN'], $assign_data[1], $unit_data["unit_id"], "", 0, $datetime_now);
					} else {
						do_log($GLOBALS['LOG_CALL_MANACKN'], 0, $unit_data["unit_id"], "", 0, $datetime_now);
					}
					do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_MANACKN'], $types[$GLOBALS['LOG_CALL_MANACKN']], $lat, $lon);
					$result_str .= "TRUE";
				}
				$do_receipt = false;
				break;
			case "_api_resp_encdg":
				$result_str .= "Aus(3)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_assigns($assign_data[0], "responding", $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_CALL_RESPONDING'], $assign_data[1], $unit_data["unit_id"], "", 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_RESPONDING'], $types[$GLOBALS['LOG_CALL_RESPONDING']], $lat, $lon);
					} else {
						do_unit($unit_data["unit_id"], get_variable("_api_clr_stat"), $datetime_now, $lat, $lon);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET'], $types[$GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']], $lat, $lon);
					}
					$result_str .= "TRUE";
				}
				$do_receipt = true;
				break;
			case "_api_onsc_encdg":
				$result_str .= "Ein(4)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_assigns($assign_data[0], "on_scene", $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_CALL_ON_SCENE'], $assign_data[1], $unit_data["unit_id"], "", 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_ON_SCENE'], $types[$GLOBALS['LOG_CALL_ON_SCENE']], $lat, $lon);
					} else {
						do_unit($unit_data["unit_id"], get_variable("_api_clr_stat"), $datetime_now, $lat, $lon);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET'], $types[$GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']], $lat, $lon);
					}
					$result_str .= "TRUE";
				}
				$do_receipt = true;
				break;
			case "_api_fcen_encdg":
				$result_str .= "Ab(7)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_assigns($assign_data[0], "u2fenr", $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_CALL_FACILITY_ENROUTE'], $assign_data[1], $unit_data["unit_id"], "", 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_FACILITY_ENROUTE'], $types[$GLOBALS['LOG_CALL_FACILITY_ENROUTE']], $lat, $lon);
					} else {
						do_unit($unit_data["unit_id"], get_variable("_api_clr_stat"), $datetime_now, $lat, $lon);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET'], $types[$GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']], $lat, $lon);
					}
					$result_str .= "TRUE";
				}
				$do_receipt = true;
				break;
			case "_api_fcar_encdg":
				$result_str .= "An(8)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_assigns($assign_data[0], "u2farr", $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_CALL_FACILITY_ARRIVED'], $assign_data[1], $unit_data["unit_id"], "", 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_FACILITY_ARRIVED'], $types[$GLOBALS['LOG_CALL_FACILITY_ARRIVED']], $lat, $lon);
					} else {
						do_unit($unit_data["unit_id"], get_variable("_api_clr_stat"), $datetime_now, $lat, $lon);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET'], $types[$GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']], $lat, $lon);
					}
					$result_str .= "TRUE";
				}
				$do_receipt = true;
				break;
			case "_api_clr_encdg":
				$result_str .= "Frei(1)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_assigns($assign_data[0], "clear", $datetime_now, $lat, $lon);
						do_unit($unit_data["unit_id"], get_variable("_api_clr_stat"), $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_CALL_CLEAR'], $assign_data[1], $unit_data["unit_id"], "", 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_UNIT_STATUS'], get_status_description(get_variable("_api_clr_stat")), $lat, $lon);
					} else {
						do_unit($unit_data["unit_id"], get_variable("_api_clr_stat"), $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_UNIT_STATUS'], 0, $unit_data["unit_id"], get_status_description(get_variable("_api_clr_stat")), 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_UNIT_STATUS'], get_status_description(get_variable("_api_clr_stat")), $lat, $lon);
					}
					$result_str .= "TRUE";
				}
				$do_receipt = true;
				break;
			case "_api_quat_encdg":
				$result_str .= "Wache(2)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_assigns($assign_data[0], "clear", $datetime_now, $lat, $lon);
						do_unit($unit_data["unit_id"], get_variable("_api_quat_stat"), $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_UNIT_STATUS'], $assign_data[1], $unit_data["unit_id"], get_status_description(get_variable("_api_quat_stat")), 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_UNIT_STATUS'], get_status_description(get_variable("_api_quat_stat")), $lat, $lon);
					} else {
						do_unit($unit_data["unit_id"], get_variable("_api_quat_stat"), $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_UNIT_STATUS'], 0, $unit_data["unit_id"], get_status_description(get_variable("_api_quat_stat")), 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_UNIT_STATUS'], get_status_description(get_variable("_api_quat_stat")), $lat, $lon);
					}
					$result_str .= "TRUE";
				}
				$do_receipt = true;
				break;
			case "_api_off_duty_encdg":
				$result_str .= "Ausser Dienst(6)" . "<br>";
				if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"]) {
					if ($assign_data[0]) {
						do_assigns($assign_data[0], "clear", $datetime_now, $lat, $lon);
						do_unit($unit_data["unit_id"], get_variable("_api_off_duty_stat"), $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_UNIT_STATUS'], $assign_data[1], $unit_data["unit_id"], get_status_description(get_variable("_api_off_duty_stat")), 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_UNIT_STATUS'], get_status_description(get_variable("_api_off_duty_stat")), $lat, $lon);
					} else {
						do_unit($unit_data["unit_id"], get_variable("_api_off_duty_stat"), $datetime_now, $lat, $lon);
						do_log($GLOBALS['LOG_UNIT_STATUS'], 0, $unit_data["unit_id"], get_status_description(get_variable("_api_off_duty_stat")), 0, $datetime_now);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_UNIT_STATUS'], get_status_description(get_variable("_api_off_duty_stat")), $lat, $lon);
					}
					$result_str .= "TRUE";
				}
				$do_receipt = true;
				break;
			case "_api_position_encdg":
				if ((isset ($_GET['lat'])) && (isset ($_GET['lon']))) {
					$result_str .= "Positionsdaten" . "<br>";
					if (($unit_data["unit_id"] != 0) || $unit_data["store_unknown_unit_data"] || $unit_data["store_unknown_unit_position_report"]) {
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_POSITION'], $types[$GLOBALS['LOG_POSITION']], $lat, $lon);
						if ($unit_data["unit_id"] != 0) {
							do_lat_lng($unit_data["unit_id"], $lat, $lon);
						}
						$result_str .= "TRUE";
					}
				} else {
					$http_status_code = 400;
				}
				break;
			case "_api_ptt_encdg":
				$result_str .= "Sprechtaste" . "<br>";
				if ($unit_data["unit_handle"] != "") {
					do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_PTT'], $types[$GLOBALS['LOG_PTT']], $lat, $lon);
					$result_str .= "TRUE";
				}
				break;
			case "_api_ptt_release_encdg":
				$result_str .= "Sprechtaste loslassen" . "<br>";
				if ($unit_data["unit_handle"] != "") {
					do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_PTT_RELEASE'], $types[$GLOBALS['LOG_PTT_RELEASE']], $lat, $lon);
					$result_str .= "TRUE";
				}
				break;
			case "_api_current_radio_encdg":
				$result_str .= "Geschalteter Funkkreis" . "<br>";
				if (($source != "") && ($destination != "")) {
					set_current_radio($source, $destination);
					do_api_log($datetime_now, $source, $source_regexp, 0, $destination, $destination_alias, $audio_link, $GLOBALS['LOG_CURRENT_RADIO'], $types[$GLOBALS['LOG_CURRENT_RADIO']], $lat, $lon);
					$result_str .= "TRUE";
				}
				break;
			case "_api_log_encdg":
				if (isset ($_GET['text'])) {
					$result_str .= "Log-Eintrag" . "<br>";
					if ($unit_data["unit_id"] != 0) {
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_INFO'], $text, $lat, $lon);
					} else {
						do_api_log($datetime_now, $source, $source_regexp, 0, $destination, $destination_alias, $audio_link, $GLOBALS['LOG_INFO'], $text, $lat, $lon);
					}
					$result_str .= "TRUE";
				} else {
					$http_status_code = 400;
				}
				break;
			case "_api_errlog_encdg":
				if (isset ($_GET['text'])) {
					$result_str .= "Fehlermeldung" . "<br>";
					if ($unit_data["unit_id"] != 0) {
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_ERROR'], $text, $lat, $lon);
					} else {
						do_api_log($datetime_now, $source, $source_regexp, 0, $destination, $destination_alias, $audio_link, $GLOBALS['LOG_ERROR'], $text, $lat, $lon);
					}
					$result_str .= "TRUE";
				} else {
					$http_status_code = 400;
				}
				break;
			case "_api_message_encdg":
				if (isset ($_GET['text'])) {
					$result_str .= "Neue Nachricht" . "<br>";
					if ($unit_data["unit_id"] != 0) {
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_MESSAGE_RECEIVE'], $text, $lat, $lon);
					} else {
						do_api_log($datetime_now, $source, $source_regexp, 0, $destination, $destination_alias, $audio_link, $GLOBALS['LOG_MESSAGE_RECEIVE'], $text, $lat, $lon);
					}
					$result_str .= "TRUE";
				} else {
					$http_status_code = 400;
				}
				break;
			case "_multiple_address":
//				Unterscheidung nach ISSI/OPTA
				$text = $unit_data["multiple_address_error"] . "  " . $text;
				do_api_log($datetime_now, $source, $source_regexp, 0, $destination, $destination_alias, $audio_link, $GLOBALS['LOG_ERROR'], $text, $lat, $lon);
				$result_str .= "TRUE";
				break;
			default:
				if ($code == $connection_test_array['code']) {
					$api_type = "api";
					if ($destination == $connection_test_array['destination_phone_code']) {
						$api_type = "phone";
					}
					set_api_availability($api_type, "", $source, $text);
				} else {
					$result_str .= "no function" . "<br>";
					if ($unit_data["unit_id"] && $lat) {
						do_lat_lng($unit_data["unit_id"], $lat, $lon);
						do_api_log($datetime_now, $source, $source_regexp, $unit_data["unit_id"], $destination, $destination_alias, $audio_link, $GLOBALS['LOG_COMMENT'], $text, $lat, $lon);
						$result_str .= "TRUE";
					} else {
						//Debug_mode:
							//Aufmerksamkeit: Niedrig mit Koordinaten
							//$result_str = "TRUE";
					}
				}
				break;
			}
			if ($do_receipt && ($unit_data["unit_id"] != 0)) {
				do_receipt_message($unit_data["unit_id"]);
			}
		}
	}
	switch ($http_status_code) {
	case 400:
		header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
		print "<h1>HTTP-Error 400 Bad Request</h1>";
		break;
	case 401:
		header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
		print "<h1>HTTP-Error 401 Unauthorized</h1>";
		break;
	case 403:
		header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
		print "<h1>HTTP-Error 403 Forbidden</h1>";
		break;
	case 505:
		header($_SERVER["SERVER_PROTOCOL"] . " 505 HTTP Version not supported");//z.B. HTTP2
		print "<h1>HTTP-Error 505 HTTP Version not supported</h1>";
		break;
	default:
		print "<br>" . $result_str;
	}
	$result_str = "";
}
$availability_array = get_api_availability("api");
$availability_array_phone = get_api_availability("phone");
if (($availability_array['available'] == "false") || ($availability_array_phone['available'] == "false")) {
	do_api_connection_test();
}
/*
$_SERVER["SERVER_PROTOCOL"] => HTTP/1.1
get_variable("_api_subscr_unsubscr_setng")
get_variable("_api_subscr_unsubscr_repl")

Acknowledge status
Status quittieren
RECEIPT an alle Funkgeräte
nur wenn in Dispo
get_variable("_api_emgcy_hi_repl")
get_variable("_api_emgcy_lo_repl")
get_variable("_api_callreq_repl")
get_variable("_api_manackn_repl")
get_variable("_api_resp_repl")
get_variable("_api_onsc_repl")
get_variable("_api_fcen_repl")
get_variable("_api_fcar_repl")
get_variable("_api_clr_repl")
get_variable("_api_quat_repl")
get_variable("_api_off_duty_repl")
*/
?>