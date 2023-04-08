<?php
error_reporting(E_ALL);
require_once ("db_credentials.inc.php");
require_once ("phpcoord.inc.php");				// UTM converter

//====== misc-codes

$GLOBALS['TYPE_TICKET']									= 1;
$GLOBALS['TYPE_UNIT']									= 2;
$GLOBALS['TYPE_FACILITY']								= 3;
$GLOBALS['TYPE_USER']									= 4;

$GLOBALS['LEVEL_SUPER'] 								= 0;
$GLOBALS['LEVEL_ADMINISTRATOR']							= 1;
$GLOBALS['LEVEL_OPERATOR']								= 2;
$GLOBALS['LEVEL_GUEST']									= 3;
$GLOBALS['LEVEL_MEMBER']								= 4;
$GLOBALS['LEVEL_UNIT']									= 5;
$GLOBALS['LEVEL_STATS']									= 6;
$GLOBALS['LEVEL_SERVICE_USER']							= 7;

$GLOBALS['STATUS_RESERVED']								= 0;
$GLOBALS['STATUS_CLOSED']								= 1;
$GLOBALS['STATUS_OPEN']									= 2;
$GLOBALS['STATUS_SCHEDULED']							= 3;

$GLOBALS['SEVERITY_NORMAL']								= 0;
$GLOBALS['SEVERITY_MEDIUM']								= 1;
$GLOBALS['SEVERITY_HIGH']								= 2;

$GLOBALS['DISPATCH_YES']								= 0;
$GLOBALS['DISPATCH_ENFORCEABLE']						= 1;
$GLOBALS['DISPATCH_NOT_ENFORCEABLE']					= 2;
$GLOBALS['DISPATCH_MONITOR']							= 3;
$GLOBALS['DISPATCH_NO_EVALUATION']						= 4;

$GLOBALS['ACTION_COMMENT']								= 1;

//====== log-codes

$GLOBALS['LOG_SIGN_IN']									= 1;
$GLOBALS['LOG_SIGN_OUT']								= 2;
$GLOBALS['LOG_INFO']									= 3;
$GLOBALS['LOG_COMMENT']									= 4;
$GLOBALS['LOG_CONFIGURATION_EDIT']						= 5;
$GLOBALS['LOG_ERROR']									= 6;

$GLOBALS['LOG_INCIDENT_ADDED']							= 10;
$GLOBALS['LOG_INCIDENT_SCHEDULED']						= 11;
$GLOBALS['LOG_INCIDENT_OPEN']							= 12;
$GLOBALS['LOG_INCIDENT_CHANGE']							= 13;
$GLOBALS['LOG_INCIDENT_CLOSE']							= 14;

$GLOBALS['LOG_ACTION_ADD']								= 20;
$GLOBALS['LOG_ACTION_EDIT']								= 21;

$GLOBALS['LOG_UNIT_ADD']								= 30;
$GLOBALS['LOG_UNIT_STATUS']								= 31;
$GLOBALS['LOG_UNIT_TO_QUARTERS']						= 32;
$GLOBALS['LOG_UNIT_NO_SERVICE']							= 33;
$GLOBALS['LOG_UNIT_CHANGE']								= 34;
//$GLOBALS['LOG_UNIT_ADDITIONAL_INFO']					= 35;
$GLOBALS['LOG_UNIT_DELETED']							= 36;

$GLOBALS['LOG_CALL_EDIT']								= 40;
$GLOBALS['LOG_CALL_DISPATCHED']							= 41;
$GLOBALS['LOG_CALL_RESPONDING']							= 42;
$GLOBALS['LOG_CALL_ON_SCENE']							= 43;
$GLOBALS['LOG_CALL_FACILITY_ENROUTE']					= 44;
$GLOBALS['LOG_CALL_FACILITY_ARRIVED']					= 45;
$GLOBALS['LOG_CALL_CLEAR']								= 46;
$GLOBALS['LOG_CALL_RESET']								= 47;
$GLOBALS['LOG_CALL_DELETED']							= 48;

$GLOBALS['LOG_FACILITY_ADD']							= 50;
$GLOBALS['LOG_FACILITY_STATUS']							= 51;
$GLOBALS['LOG_FACILITY_DISPATCHED']						= 52;
$GLOBALS['LOG_FACILITY_CHANGE']							= 53;
$GLOBALS['LOG_FACILITY_DELETED']						= 54;

$GLOBALS['LOG_FACILITY_INCIDENT_OPEN']					= 60;
$GLOBALS['LOG_FACILITY_INCIDENT_CHANGE']				= 61;
$GLOBALS['LOG_FACILITY_INCIDENT_UNSET']					= 62;

$GLOBALS['LOG_CALL_FACILITY_SET']						= 70;
$GLOBALS['LOG_CALL_FACILITY_CHANGE']					= 71;
$GLOBALS['LOG_CALL_FACILITY_UNSET']						= 72;

$GLOBALS['LOG_CALL_RECEIVING_FACILITY_SET']				= 80;
$GLOBALS['LOG_CALL_RECEIVING_FACILITY_CHANGE']			= 81;
$GLOBALS['LOG_CALL_RECEIVING_FACILITY_UNSET']			= 82;

$GLOBALS['LOG_CALL_REQ']								= 90;
$GLOBALS['LOG_EMGCY_LO']								= 91;
$GLOBALS['LOG_EMGCY_HI']								= 92;
$GLOBALS['LOG_CALL_MANACKN']							= 93;
$GLOBALS['LOG_PTT']										= 94;
$GLOBALS['LOG_PTT_RELEASE']								= 95;
$GLOBALS['LOG_MESSAGE_RECEIVE']							= 96;

$GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']			= 100;
$GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']			= 101;
$GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET']	= 102;
$GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']	= 103;

$GLOBALS['LOG_SMS_MESSAGE_SEND']						= 110;
$GLOBALS['LOG_SMS_MESSAGE_ERROR']						= 111;
$GLOBALS['LOG_PRINT_JOB_SEND']							= 112;
$GLOBALS['LOG_PRINT_JOB_ERROR']							= 113;
$GLOBALS['LOG_EMAIL_MESSAGE_SEND']						= 114;
//$GLOBALS['LOG_EMAIL_MESSAGE_RECEIVED']				= 115;
$GLOBALS['LOG_EMAIL_MESSAGE_ERROR']						= 116;
$GLOBALS['LOG_CURRENT_RADIO']							= 117;
$GLOBALS['LOG_NO_ACTION']								= 118;

$GLOBALS['LOG_POSITION']								= 120;
/*$GLOBALS['LOG_POSITION_REQUESTED']					= 121;
$GLOBALS['LOG_POSITION_EVENT_ARRIVED']					= 122;
$GLOBALS['LOG_POSITION_EVENT_LEAVE']					= 123;*/

$GLOBALS['LOG_API_CONNECTED']							= 130;
$GLOBALS['LOG_API_DISCONNECTED']						= 131;
$GLOBALS['LOG_API_DEVICE_TEXT']							= 132;

/*$GLOBALS['LOG_TIMEOUT_RESPONDING_1']					= 140;
$GLOBALS['LOG_TIMEOUT_RESPONDING_2']					= 141;
$GLOBALS['LOG_TIMEOUT_RESPONDING_3']					= 142;

$GLOBALS['LOG_TIMEOUT_ON_SCENE_1']						= 150;
$GLOBALS['LOG_TIMEOUT_ON_SCENE_2']						= 151;
$GLOBALS['LOG_TIMEOUT_ON_SCENE_3']						= 152;

$GLOBALS['LOG_TIMEOUT_FACILITY_ENROUTE_1']				= 160;
$GLOBALS['LOG_TIMEOUT_FACILITY_ENROUTE_2']				= 161;
$GLOBALS['LOG_TIMEOUT_FACILITY_ENROUTE_3']				= 162;

$GLOBALS['LOG_TIMEOUT_FACILITY_ARRIVED_1']				= 170;
$GLOBALS['LOG_TIMEOUT_FACILITY_ARRIVED_2']				= 171;
$GLOBALS['LOG_TIMEOUT_FACILITY_ARRIVED_3']				= 172;

$GLOBALS['LOG_TIMEOUT_CLEAR_1']							= 1680;
$GLOBALS['LOG_TIMEOUT_CLEAR_2']							= 181;
$GLOBALS['LOG_TIMEOUT_CLEAR_3']							= 182;*/

//====== database

function show_db_error_message() {
	print "<br><span style='color:red'>The database may not exist, the credentials are incorrect or the tables are not installed.<br>" .
		"Check if the database actually exists, run </span><a href=install.php?write_credentials_checked=true>install.php</a><span style='color:red'>, " .
		"enter the correct credentials and<br>install with option 'Write db-configuration file only' checked.<br>If fails, " .
		"install database tables new with option 'Install database tables new' checked.</span><br>";
}

$GLOBALS['DATABASE_LINK'] = null;
try {
	$GLOBALS['DATABASE_LINK'] = new PDO("mysql:host=" . $GLOBALS['db_host'] . ";dbname=" . $GLOBALS['db_name'], $GLOBALS['db_user'], $GLOBALS['db_password'], array (PDO::ATTR_PERSISTENT => true));
	db_query("SET NAMES 'utf8'");
} catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br>";
	@error_log("Error!: " . $e->getMessage());
	show_db_error_message();
	die ();
}

$GLOBALS['LAST_RESULT'] = null;
$GLOBALS['LAST_STATEMENT'] = null;
function db_query($query_str, $file = "", $line = "") {
	$result = $GLOBALS['DATABASE_LINK']->query($query_str);
	if ($result != false) {
		$GLOBALS['LAST_RESULT'] = $result;
		$GLOBALS['LAST_STATEMENT'] = $GLOBALS['DATABASE_LINK']->prepare($query_str);	//needed in export.php
		return $result;
	} else {
		$error_message = $GLOBALS['DATABASE_LINK']->errorInfo();
		@error_log($error_message[2] . " in " . basename($file) . " line " . $line . "\r\n" . $query_str);
		print "<span style='color:red'>" . $error_message[2] . " in " . basename($file) . " line " . $line . "</span><br>";
		show_db_error_message();
		return false;
	}
}

function db_num_rows($result) {
	return $result->rowCount();
}

function db_affected_rows($result, $file = "", $line = "") {
	return $GLOBALS['LAST_RESULT']->rowCount();
//	$back = $result->rowCount();
//http://php.net/manual/de/pdostatement.rowcount.php
//http://php.net/manual/de/pdostatement.fetchcolumn.php
	$error_message = $GLOBALS['DATABASE_LINK']->errorInfo();
	@error_log($error_message[2] . " in " . basename($file) . " line " . $line . "\r\n");
	return $back;
//	return $result->rowCount();
}

function db_num_fields($query) {	//needed in export.php
	$GLOBALS['LAST_STATEMENT']->execute();
	return $GLOBALS['LAST_STATEMENT']->columnCount();
/*	$statement = $GLOBALS['DATABASE_LINK']->prepare($query);
	$statement->execute();
	return $GLOBALS['LAST_STATEMENT']->columnCount();*/
}

function db_fetch_array($result) {
	return $result->fetch(PDO::FETCH_BOTH);	//needed in export.php
}

function db_fetch_assoc($result) {
	return $result->fetch(PDO::FETCH_ASSOC);
}

function db_real_escape_string($string) {
	return $GLOBALS['DATABASE_LINK']->quote($string);
}

function db_get_server_info() {
//	return $GLOBALS['DATABASE_LINK']->getAttribute(constant("PDO::ATTR_SERVER_INFO"));
	return $GLOBALS['DATABASE_LINK']->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
}

function check_for_rows($query) {
	if ($sql = db_query($query, __FILE__, __LINE__)) {
		if (db_num_rows($sql) !== 0) {
			return db_num_rows($sql);
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function stripslashes_deep($value) {
	if (is_array($value)) {
		$value = array_map("stripslashes_deep", $value);
	} else {
		$value = stripslashes($value);
	}
	return $value;
}

function quote_smart($value) {
	if (!is_int($value)) {
		$value = db_real_escape_string($value);
	}
	return $value;
}

function trim_quote($string) {
	return db_real_escape_string(trim($string));
}

function insert_into_allocates($group = 1, $type = 0, $resource_id = 0, $user_id = 0, $updated = "") {
	if ($user_id == 0) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query = "INSERT INTO `allocates` (`group`, `type`, `resource_id`, `user_id`, " .
		"`client_address`, `updated`) " .
		"VALUES (" . intval($group) . ", " . intval($type) . ", " . intval($resource_id) . ", ". intval($user_id) . ", " .
		"'" . $_SERVER['REMOTE_ADDR'] . "', " . trim_quote($updated) . ");";

	db_query($query, __FILE__, __LINE__);

	$query = "SELECT MAX(`id`) FROM `allocates`;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_array($result));
	return $row[0];
}

function get_working_in_development_environement() {
	if (
		file_exists(get_current_path(".project")) ||
		file_exists(get_current_path(".gitignore")) ||
		file_exists(get_current_path(".git"))
	) {
		return true;
	} else {
		return false;
	}
}

function insert_into_facilities($name = "", $handle = "", $object_id = "", $direct_dialing_1 = "",
	$direct_dialing_2 = "", $street = "", $city = "", $security_contact = "",
	$security_phone = "", $security_email = "", $type = 0, $facility_status_id = 0,
	$description = "", $capabilities = "", $opening_hours = "", $access_rules = "",
	$contact_name = "", $contact_phone = "", $contact_email = "", $admin_only = 0,
	$icon_url = "", $boundary = "", $lat = 0.999999, $lng = 0.999999,
	$user_id = 0, $updated = "") {
	if ($type == "") {
		$type = 0;
	}
	if ($facility_status_id == "") {
		$facility_status_id = 0;
	}
	if ($user_id == 0) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query = "INSERT INTO `facilities` (`name`, `handle`, `object_id`, `direct_dialing_1`, " .
		"`direct_dialing_2`, `street`, `city`, `security_contact`, " . 
		"`security_phone`, `security_email`, `type`, `facility_status_id`, " .
		"`description`, `capabilities`, `opening_hours`, `access_rules`, " .
		"`contact_name`, `contact_phone`, `contact_email`, `admin_only`, " .
		"`icon_url`, `boundary`, `lat`, `lng`, " .
		"`user_id`, `client_address`, `updated`) " .
		"VALUES (" . trim_quote($name) . ", " . trim_quote($handle) . ", " . trim_quote($object_id) . ", ". trim_quote($direct_dialing_1) . ", " .
		trim_quote($direct_dialing_2) . ", " . trim_quote($street) . ", " . trim_quote($city) . ", " . trim_quote($security_contact) . ", " .
		trim_quote($security_phone) . ", " . trim_quote($security_email) . ", " . intval($type) . ", " . $facility_status_id . ", " .
		trim_quote($description) . ", " . trim_quote($capabilities) . ", " . trim_quote($opening_hours) . ", " . trim_quote($access_rules) . ", " .
		trim_quote($contact_name) . ", " . trim_quote($contact_phone) . ", " . trim_quote($contact_email) . ", " . intval($admin_only) . ", " .
		trim_quote($icon_url) . ", " . trim_quote($boundary) . ", " . floatval($lat) . ", " . floatval($lng) . ", " .
		intval($user_id) . ", '" . $_SERVER['REMOTE_ADDR'] . "', " . trim_quote($updated) . ");";

	db_query($query, __FILE__, __LINE__);

	$query = "SELECT MAX(`id`) FROM `facilities`;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_array($result));
	return $row[0];
}

function insert_into_facility_status($status_name = "", $description = "", $sort = 0, $display = "",
	$bg_color = "", $text_color = "", $user_id = 0, $updated = "") {
	if (!is_int($sort)) {
		$sort = 0;
	}
	if ($user_id == 0) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query = "INSERT INTO `facility_status` (`status_name`, `description`, `sort`, `display`, " .
		"`bg_color`, `text_color`, `user_id`, `client_address`, " .
		"`updated`) " .
		"VALUES (" . trim_quote($status_name) . ", " . trim_quote($description) . ", " . intval($sort) . ", ". trim_quote($display) . ", " .
		trim_quote($bg_color) . ", " . trim_quote($text_color) . ", " . intval($user_id) . ", '" . $_SERVER['REMOTE_ADDR'] . "', " .
		trim_quote($updated) . ");";

	return db_query($query, __FILE__, __LINE__);
}

function insert_into_facility_types($name = "", $description = "", $bg_color = "", $text_color = "",
	$user_id = 0, $updated = "") {
	if ($user_id == 0) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query = "INSERT INTO `facility_types` (`name`, `description`, `bg_color`, `text_color`, " .
		"`user_id`, `client_address`, `updated`) " .
		"VALUES (" . trim_quote($name) . ", " . trim_quote($description) . ", " . trim_quote($bg_color) . ", " . trim_quote($text_color) . ", " .
		intval($user_id) . ", '" . $_SERVER['REMOTE_ADDR'] . "', " . trim_quote($updated) . ");";

	return db_query($query, __FILE__, __LINE__);
}

function insert_into_incident_types($type = "", $description = "", $protocol = "", $set_severity = 0,
	$group = "", $sort = 0, $user_id = 0, $updated = "") {
	if ($user_id == 0) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
	$updated = mysql_datetime();
}

	$query = "INSERT INTO `incident_types` (`type`, `description`, `protocol`, `set_severity`, " .
		"`group`, `sort`, `user_id`, `client_address`, " .
		"`updated`) " .
		"VALUES (" . trim_quote($type) . ", " . trim_quote($description) . ", " . trim_quote($protocol) . ", " . intval($set_severity) . ", " .
		trim_quote($group) . ", " . intval($sort) . ", " . intval($user_id) . ", '" . $_SERVER['REMOTE_ADDR'] . "', " .
		trim_quote($updated) . ");";

	return db_query($query, __FILE__, __LINE__);
}

function insert_into_textblocks($type = "", $group = "", $text = "", $code = "",
	$report_channels = 0, $sort = 0, $user_id = 0, $updated = "") {
	$report_channels = intval($report_channels);
	if ($user_id == 0) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query = "INSERT INTO `textblocks` (`type`, `group`, `text`, `code`, " .
		"`report_channels`, `sort`, `user_id`, `client_address`, " .
		"`updated`) " .
		"VALUES (" . trim_quote($type) . ", " . trim_quote($group) . ", " . trim_quote($text) . ", " . trim_quote($code) . ", " .
		intval($report_channels) . ", " . intval($sort) . ", " . intval($user_id) . ", '" . $_SERVER['REMOTE_ADDR'] . "', " .
		trim_quote($updated) . ");";

	return db_query($query, __FILE__, __LINE__);
}

function insert_into_units($name = "", $handle = "", $remote_data_services = "", $unit_phone = "",
	$unit_email = "", $type = 0, $unit_status_id = 0, $multi = 0,
	$mobile = "", $parent_unit_id = "", $guard_house_id = "", $description = "",
	$capabilities = "", $contact_name = "", $admin_only = 0, $icon_url = "",
	$lat = "", $lng = "", $lat_lng_updated = "", $status_updated = "",
	$user_id = 0, $updated = "") {
	if ($lat == "") {
		$lat = "0.999999";
	}
	if ($lng == "") {
		$lng = "0.999999";
	}
	if ($lat_lng_updated == "") {
		$lat_lng_updated = mysql_datetime();
	}
	if ($status_updated == "") {
		$status_updated = mysql_datetime();
	}
	if ($user_id == 0) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query = "INSERT INTO `units` (`name`, `handle`, `remote_data_services`, `unit_phone`, " .
		"`unit_email`, `type`, `unit_status_id`, `multi`, " .
		"`mobile`, `parent_unit_id`, `guard_house_id`, `description`, " .
		"`capabilities`, `contact_name`, `admin_only`, `icon_url`, " .
		"`lat`, `lng`, `lat_lng_updated`, `status_updated`, " .
		"`user_id`, `client_address`, `updated`) " .
		"VALUES (" . trim_quote($name) . ", " . trim_quote($handle) . ", " . trim_quote($remote_data_services) . ", " . trim_quote($unit_phone) . ", " .
		trim_quote($unit_email) . ", " . intval($type) . ", " . intval($unit_status_id) . ", " . intval($multi) . ", " .
		trim_quote($mobile) . ", " . trim_quote($parent_unit_id) . ", " . trim_quote($guard_house_id) . ", " . trim_quote($description) . ", " .
		trim_quote($capabilities) . ", " . trim_quote($contact_name) . ", " . intval($admin_only) . ", " . trim_quote($icon_url) . ", " .
		floatval($lat) . ", " . floatval($lng) . ", " . trim_quote($lat_lng_updated) . ", " . trim_quote($status_updated) . ", " .
		intval($user_id) . ", " . "'" . $_SERVER['REMOTE_ADDR'] . "', " . trim_quote($updated) . ");";

	db_query($query, __FILE__, __LINE__);

	$query = "SELECT MAX(`id`) FROM `units`;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_array($result));
	return $row[0];
}

function insert_into_unit_status($status_name = "", $description = "", $dispatch = 0, $sort = 0,
	$bg_color = "#FFFFFF", $text_color = "#000000", $user_id = 0, $updated = "") {
	if (!preg_match("/^#[0-9a-fA-F]{6}/", trim($bg_color))) {
		$bg_color = "#FFFFFF";
	}
	if (!preg_match("/^#[0-9a-fA-F]{6}/", trim($bg_color))) {
		$text_color = "#000000";
	}
	if ($user_id == 0) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query  = "INSERT INTO `unit_status` (`status_name`, `description`, `dispatch`, `sort`, " .
		"`bg_color`, `text_color`, `user_id`, `client_address`, " .
		"`updated`) " .
		"VALUES (" . trim_quote($status_name) . ", " . trim_quote($description) . ", " . intval($dispatch) . ", " . intval($sort) . ", " .
		trim_quote($bg_color) . ", " . trim_quote($text_color) . ", " . intval($user_id) . ", '" . $_SERVER['REMOTE_ADDR'] . "', " .
		trim_quote($updated) . ");";

	return db_query($query, __FILE__, __LINE__);
}

function insert_into_unit_types($name = "", $description = "", $bg_color = "#FFFFFF", $text_color = "#000000",
	$user_id = 0, $updated = "") {
	if (!preg_match("/^#[0-9a-fA-F]{6}/", trim($bg_color))) {
		$bg_color = "#FFFFFF";
	}
	if (!preg_match("/^#[0-9a-fA-F]{6}/", trim($bg_color))) {
		$text_color = "#000000";
	}
	if ($user_id == 0) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query  = "INSERT INTO `unit_types` (`name`, `description`, `bg_color`, `text_color`, " .
		"`user_id`, `client_address`, `updated`) " .
		"VALUES (" . trim_quote($name) . ", " . trim_quote($description) . ", " . trim_quote($bg_color) . ", " . trim_quote($text_color) . ", " .
		intval($user_id) . ", '" . $_SERVER['REMOTE_ADDR'] . "', " . trim_quote($updated) . ");";

	return db_query($query, __FILE__, __LINE__);
}

function insert_into_users($name = "", $password = "", $level = 0, $email = "",
	$updated = "") {
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query = "INSERT INTO `users` (`name`, `password`, `level`, `email`, " .
		"`expires`, `session_id`, `current_radio`, `browser`, " .
		"`individual`, `login_datetime`, `login_address`, `client_address`, " .
		"`user_id`, `updated`) " .
		"VALUES (" . trim_quote($name) . ", " . trim_quote($password) . ", " . $level . ", " . trim_quote($email) . ", " .
		"'2017-01-01 00:00:00', NULL, NULL, '', " .
		"'', '2017-01-01 00:00:00', '0.0.0.0', '" . $_SERVER['REMOTE_ADDR'] . "', " .
		$_SESSION['user_id'] . ", " . trim_quote($updated) . ");";

	db_query($query, __FILE__, __LINE__);

	$query = "SELECT MAX(`id`) FROM `users`;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_array($result));
	return $row[0];
}

//====== ticket-data

require_once ("login.inc.php");

function show_dispatch_text($ticket_id, $search = false, $last = false) {
	require_once "incs/communication.inc.php";
	$page_beak_str = " page-break-after: always;";
	if ($last) {
		$page_beak_str = " page-break-after: avoid;";
	}
	$default_subjects = explode(",", get_variable("_api_default_subject_setng"));
	?>
	<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;<?php print $page_beak_str;?>">
		<tr>
			<td style="text-align: left; width: 0%;"></td>
			<td style="text-align: left; width: 100%;" class="big"><?php print remove_nls(get_variable("title_string")) . "<br>" . get_text("Incident dispatch system");?></td>
			<td style="text-align: right; width: 0%;"></td>
		</tr>
		<tr>
			<td style="text-align: left; width: 0%;"></td>
			<th style="text-align: left; width: 100%;"><h5><strong style="white-space: nowrap;" class="big"><?php print remove_nls($default_subjects[0]);?></strong></h5></th>
			<td style="text-align: right; width: 0%;"></td>
		</tr>
		<?php print get_dispatch_message($ticket_id, "message_text", "print")[0];?>
		<tr style="height: 26px;"><td></td><td></td><td></td></tr>
		<tr>
			<td></td>
			<td class="big"><?php print get_text("Printed at") . " " . date(get_variable("date_format")) . " " . get_text("by") . " " . $_SESSION['user_name'];?></td>
			<td></td>
		</tr>
	</table>
	<?php
}

function show_ticket($ticket_id, $search = false, $last = false) {
	$page_beak_str = " page-break-after: always;";
	if ($last) {
		$page_beak_str = " page-break-after: avoid;";
	}
	?>
	<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;">
		<tr>
			<th style="text-align: center;"><h3><strong style="white-space: nowrap;"><?php print get_text("Incident Report");?></strong></h3></th>
		</tr>
	</table>
	<table class="table table-striped table-condensed" style="table-layout: fixed; text-align: left;<?php print $page_beak_str;?>">
		<tr style="heigth: 0px;">
			<td style="text-align: left; width: 15%;"></td>
			<td style="text-align: left; width: 15%;"></td>
			<th style="text-align: right; width: 25%;"></th>
			<td style="text-align: left; width: 40%;"></td>
			<td style="text-align: left; width: 5%;"></td>
		</tr>
			<?php show_head($ticket_id, $search, true);?>
			<?php show_assigns($ticket_id, 0);?>
		<tr><th colspan=5 style="text-align: center;"><h5><strong style="white-space: nowrap;"><?php print get_text("Actions");?></strong></h5></th></tr>
			<?php show_actions($ticket_id, true);?>
			<?php show_ticket_log($ticket_id);?>
		</table>
	<?php
}

function show_head($ticket_id, $search = false, $ticket_report = false) {
	if ($ticket_report) {
		$border_top_str = "";
	} else {
		$border_top_str = " border-top: 0px;";
	}

	$query = "SELECT *, " .
		"`problemstart`, " .
		"`problemend`, " .
		"`tickets`.`datetime`, " .
		"`booked_date`, " . 	
		"`tickets`.`updated`, " . 	
		"`tickets`.`description` AS `ticket_description`, " .
		"`tickets`.`location` AS `ticket_location`, " .
		"`tickets`.`lat` AS `lat`, " .
		"`tickets`.`lng` AS `lng`, " .
		"`tickets`.`call_taker_id` AS `call_taker`, " .
		"`tickets`.`user_id` AS `user_id`, " .
		"`facilities`.`name` AS `fac_name`,	" .
		"`facilities`.`lat` AS `fac_lat`, " .
		"`facilities`.`lng` AS `fac_lng` " .
		"FROM `tickets` " .
			"LEFT JOIN `incident_types` `ty` ON (`tickets`.`incident_type_id` = `ty`.`id`) " .
			"LEFT JOIN `facilities` ON `facilities`.`id` = `tickets`.`facility_id` " .
		"WHERE `tickets`.`id` = " . $ticket_id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	if (!db_num_rows($result)) {
		print "<font class=\"warn\">Internal error " . basename(__FILE__) . "/" . __LINE__ . ". Notify developers of this message.</font>" . $ticket_id;
		exit ();
	}
	$row = stripslashes_deep(db_fetch_array($result));
	switch ($row['severity']) {
	case $GLOBALS['SEVERITY_MEDIUM']:
		$severityclass = " class='severity_medium'";
		break;
	case $GLOBALS['SEVERITY_HIGH']:
		$severityclass = " class='severity_high'";
		break;
	default:
		$severityclass = " class='severity_normal'";
	}
	?>
<tr>
	<th style="width: 20%;<?php print $border_top_str;?>"><?php print get_text("Incident location");?>:</th>
	<td style="width: 80%;<?php print $border_top_str;?>" colspan=4><div class="td-div"<?php print get_title_str($row['ticket_location']);?>><?php print highlight($search, breakspace(remove_nls($row['ticket_location']), 30));?></div></td>
</tr>
	<?php
	if ($row['fac_name']) {
	?>
<tr>
	<th><?php print get_text("Facility");?>:</th>
	<td colspan=4><div class="td-div"<?php print get_title_str($row['fac_name']);?>><?php print highlight($search, breakspace(remove_nls($row['fac_name']), 30));?></div></td>
</tr>
	<?php
	}
	$coords =  $row['lat'] . "," . $row['lng'];
	$grid_type = "&nbsp;&nbsp;&nbsp;&nbsp;UTM&nbsp;&nbsp;" . toUTM($coords);
	//  if (($row['lat'] != 0.999999) || ($row['lng'] != 0.999999)) {
	if (false) {
	?>
<tr>
	<th onclick="do_coords(<?php print $row['lat'] . "," . $row['lng'];?>);"><?php print get_text("Position");?>:</th>
	<td colspan=4<?php print get_title_str(get_lat($row['lat']) . "&nbsp;&nbsp;&nbsp;" . get_lng($row['lng']));?>><?php print get_lat($row['lat']) . "&nbsp;&nbsp;&nbsp;" . get_lng($row['lng']) . $grid_type;?></td>
</tr>
	<?php
	}
	?>
<tr>
	<th><?php print get_text("Callback phone");?>:</th>
	<td colspan=4<?php print get_title_str($row['phone']);?>><?php print $row['phone'];?></td>
</tr>
<tr>
	<th><?php print get_text("Synopsis");?>:</th>
	<td colspan=4><div class="td-div"<?php print get_title_str($row['ticket_description']);?>><?php print highlight($search, nl2br(breakspace(remove_nls($row['ticket_description']), 30)));?></div></td>
</tr>
<tr>
	<th><?php print get_text("Reported by");?>:</th>
	<td colspan=4<?php print get_title_str($row['contact']);?>><?php print highlight($search,$row['contact']);?></td>
</tr>
<tr>
	<th><?php print get_text("Incident type");?>:</th>
	<td colspan=4><span<?php print $severityclass . get_title_str(get_type($row['incident_type_id']));?>><?php print get_type($row['incident_type_id']);?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight: bold;"><?php print get_text("Severity");?>:</span>&nbsp;&nbsp;<span<?php print $severityclass . get_title_str(get_text(get_severity($row['severity'])));?>><?php print get_text(get_severity($row['severity']));?></span></td>
</tr>
<tr>
	<th><?php print get_text("Protocol");?>:</th>
	<td colspan=4<?php print get_title_str($row['protocol']);?>><?php print $row['protocol'];?></td>
</tr>
	<?php
	if ($row['comments']) {
	?>
<tr>
	<th><?php print get_text("Comments");?>:</th><td colspan=4<?php print get_title_str($row['comments']);?>><div class="td-div"><?php print highlight($search, nl2br(breakspace(remove_nls($row['comments']), 30)));?></div></td>
</tr>
	<?php
	}
	?>
<tr>
	<th><?php print get_text("Incident name");?>:</th>
	<td colspan=4<?php print get_title_str($row['incident_name']);?>><?php print highlight($search,$row['incident_name']);?></td>
</tr>
	<?php
	$elapsed = get_elapsed_time($row);
	$elaped_str = "";
	if (!(intval($row['problemend']) > 1)) {
		$elaped_str = " (" . $elapsed . ")";
	}
	?>
<tr>
	<th><?php print get_text("Status");?>:</th>
	<td colspan=4<?php print get_title_str(get_text(get_status($row['status'])) . $elaped_str);?>><?php print get_text(get_status($row['status'])) . $elaped_str;?></td>
</tr>
<tr>
	<th><?php print get_text("Run Start");?>:</th>
	<th colspan=4<?php print get_title_str(format_date($row['problemstart']));?>><?php print format_date($row['problemstart']);?></th>
</tr>
	<?php
	if ($row['booked_date'] != null) {
	?>
<tr>
	<th><?php print get_text("Scheduled Date");?>:</th>
	<td colspan=4<?php print get_title_str(format_date($row['booked_date']));?>><?php print format_date($row['booked_date']);?></td>
</tr>
	<?php
	}
	$by_str = "";
	if ($row['call_taker'] != 0) {
		$by_str = "&nbsp;" . get_text("by") . "&nbsp;" . get_user_name($row['call_taker']);
	}
	?>
<tr>
	<th><?php print get_text("Incident added");?>:</th>
	<td colspan=4<?php print get_title_str(format_date($row['datetime']) . $by_str);?>><?php print format_date($row['datetime']) . $by_str;?></td>
</tr>
	<?php
	if (intval($row['problemend']) > 1) {
		$elaped_str = " (" . $elapsed . ")";
	} else {
		$elaped_str = "";
	}
	$problem_end_str = "";
	if (intval($row['problemend']) > 1) {
		$problem_end_str = format_date($row['problemend']);
	}
	?>
<tr>
	<th><?php print get_text("Run End");?>:</th>
	<th colspan=4<?php print get_title_str($problem_end_str . $elaped_str);?>><?php print $problem_end_str . $elaped_str;?></th>
</tr>
 	<?php
 	$by_str = "";
	if ($row['updated']) {
		if ($row['user_id'] != 0) {
			$by_str = "&nbsp;" . get_text("by") . "&nbsp;" . get_user_name($row['user_id']);
		}
	?>
<tr>
	<th><?php print get_text("Edited");?>:</th>
	<td colspan=4<?php print get_title_str(format_date($row['updated']) . $by_str);?>><?php print highlight($search, nl2br(format_date($row['updated']))) . $by_str;?></td>
</tr>
	<?php
	}
}

function show_assigns($id, $ticket_or_unit) {
	$ticket_unit_array = array ("ticket_id", "unit_id");

	$query = "SELECT *, " .
		"dispatched AS dispatched_i, " .
		"responding AS responding_i, " .
		"on_scene AS on_scene_i, " .
		"u2fenr AS u2fenr_i, " .
		"u2farr AS u2farr_i, " .
		"clear AS clear_i, " .
		"start_miles AS start_m, " .
		"on_scene_miles AS os_miles, " .
		"end_miles AS end_m, " .
		"miles AS miles, " .
		"`osf`.`name` AS `on_scene_facility_name`, " .
		"`recf`.`name` AS `receiving_facility_name`, " .
		"`a`.`comments`, " .
		"`r`.`handle`, " .
		"`r`.`name`, " .
		"`t`.`problemstart` AS `problemstart_i` " .
		"FROM `assigns` `a` " .
			"LEFT JOIN `units` `r` ON (`r`.`id` = `a`.`unit_id`) " .
			"LEFT JOIN `tickets` `t` ON (`t`.`id` = `a`.`ticket_id`) " .
			"LEFT JOIN `facilities` `osf` ON (`osf`.`id` = `a`.`on_scene_facility_id`) " . 
			"LEFT JOIN `facilities` `recf` ON (`recf`.`id` = `a`.`receiving_facility_id`) " .
		"WHERE `a`.`" . $ticket_unit_array[$ticket_or_unit] . "` = " . $id . " " .
		"ORDER BY `problemstart_i` ASC;";

	$result = db_query($query, __FILE__, __LINE__);

	$output_str = "";
	if (db_num_rows($result)) {
//------------------------------------------------------------------------------------------------------------------------------
		$output_str = "\n<tr><td colspan=5 style='text-align: center;'><h5><strong  style=\"white-space: nowrap;\">" . get_text("Dispatched Units") . "</strong></h5></td></tr>\n";
//------------------------------------------------------------------------------------------------------------------------------
		$output_str .= "<tr>";
		$output_str .= "<th>" . get_text("DateTime") . "</th>";
		$output_str .= "<th>" . get_text("Status") . "</th>";
		$output_str .= "<th>" . get_text("Unit") . "</th>";
		$output_str .= "<th>" . get_text("Text") . "</th>";
		$output_str .= "<th>" . get_text("by") . "</th>";
		$output_str .= "</tr>";
//------------------------------------------------------------------------------------------------------------------------------
		$day_part_log_time = "";
		$i = 0;
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
//------------------------------------------------------------------------------------------------------------------------------
			if ($i == 0) {
				$temp = preg_split("/ /", $row['problemstart_i']); // date and time
				if ($temp[0] == $day_part_log_time) {
					$the_date = $temp[1];
				} else {
					$the_date = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['problemstart_i'])) . "</span><br> " . $temp[1];
				$day_part_log_time = $temp[0];
				}
				$output_str .= "<tr><td" . get_title_str(date(get_variable("date_format"), strtotime($row['problemstart_i']))) . ">" .
					$the_date . "</td><td>" . get_text("Run Start") . "</td><td colspan=3></td></tr>\n";
				$i++;
			}
//------------------------------------------------------------------------------------------------------------------------------
			$start_miles = ($row['start_m'] != null)? $row['start_m'] : "";
			$on_scene_miles = ($row['os_miles'] != null)? $row['os_miles'] : "";
			$end_miles = ($row['end_m'] != null) ? $row['end_m'] : "";
			if ($row['miles'] != null) {
				$tot_miles = $row['miles'];
			} else {
				if (($row['miles'] == null) && (($start_miles != "") && ($end_miles != ""))) {
					$tot_miles = intval($end_miles) - intval($start_miles);
				} else {
					$tot_miles = "";
				}
			}
//------------------------------------------------------------------------------------------------------------------------------
			if (is_datetime($row['dispatched']) || $row['comments'] != "") {
				$dispatched_datetime_title_str = "";
				if (is_datetime($row['dispatched_i'])) {
					$dispatched_datetime_title_str = get_title_str(date(get_variable("date_format"), strtotime($row['dispatched_i'])));
				}
				$the_date = "";
				if (is_datetime($row['dispatched'])) {
					$temp = preg_split("/ /", $row['dispatched_i']);
					if ($temp[0] == $day_part_log_time) {
						$the_date = $temp[1];
					} else {
						$the_date = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['dispatched_i'])) . "</span><br>" . $temp[1];
						$day_part_log_time = $temp[0];
					}
				}
				$dispatched_diff_title_str = "";
				if (is_datetime($row['problemstart_i']) && is_datetime($row['dispatched_i'])) {
					$dispatched_diff_title_str = get_title_str(datetime_difference($row['problemstart_i'], $row['dispatched_i']));
				}
				$comments_title_str = "";
				$comments_str = "";	
				if ($row['comments'] != "") {
					$comments_title_str = get_title_str(get_text("Comments") . ": " .$row['comments']);
					$comments_str = breakspace(get_text("Comments") . ": " . html_entity_decode(remove_nls($row['comments'])), 30);
				}
//				$output_str .= "<tr><td colspan=5 style=\"background-color: black;\"></td></tr>\n";
				$output_str .= "<tr><td" . $dispatched_datetime_title_str . ">" . $the_date . "</td><td" . $dispatched_diff_title_str . ">" . get_text("Dispatched") . "</td>" .
					"<td" . get_title_str($row['name']) . "><nobr>" . remove_nls($row['handle']) . "</nobr></td><td" . $comments_title_str . "><div class='td-div'>" .
					$comments_str . "</div></td><td>" . get_user_name($row['dispatching_user_id']) . "</td></tr>\n";
			}
//------------------------------------------------------------------------------------------------------------------------------
			if (is_datetime($row['responding'])) {
				$temp = preg_split("/ /", $row['responding_i']);
				if ($temp[0] == $day_part_log_time) {
					$the_date = $temp[1];
				} else {
					$the_date = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['responding_i'])) . "</span><br>" . $temp[1];
					$day_part_log_time = $temp[0];
				}
				$output_str .= "<tr><td" . get_title_str(date(get_variable("date_format"), strtotime($row['responding_i']))) . ">" .
					$the_date . "</td><td" . get_title_str(datetime_difference($row['problemstart_i'], $row['responding_i'])) . ">" .
					get_text("Responding") . "</td><td" . get_title_str($row['name']) . "></td><td></td><td></td></tr>\n";
			}
//------------------------------------------------------------------------------------------------------------------------------
			if (is_datetime($row['on_scene']) || $row['on_scene_location'] != "") {
				$on_scene_datetime_title_str = "";
				if (is_datetime($row['on_scene_i'])) {
					$on_scene_datetime_title_str = get_title_str(date(get_variable("date_format"), strtotime($row['on_scene_i'])));
				}
				$the_date = "";
				if (is_datetime($row['on_scene'])) {
					$temp = preg_split("/ /", $row['on_scene_i']);
					if ($temp[0] == $day_part_log_time) {
						$the_date = $temp[1];
					} else {
						$the_date = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['on_scene_i'])) . "</span><br>" . $temp[1];
						$day_part_log_time = $temp[0];
					}
				}
				$on_scene_diff_title_str = "";
				if (is_datetime($row['problemstart_i']) && is_datetime($row['on_scene_i'])) {
					$on_scene_diff_title_str = get_title_str(datetime_difference($row['problemstart_i'], $row['on_scene_i']));
				}
				$on_scene_title_str = "";
				$on_scene_str = "";
				if ($row['on_scene_location'] != "") {
					if ($row['on_scene_facility_name'] != "") {
						$on_scene_facility_name_str = $row['on_scene_facility_name'] . ", ";
					} else {
						$on_scene_facility_name_str = "";
					}
					$on_scene_title_str = get_title_str(get_text("On-Scene location") . ": " . $on_scene_facility_name_str . $row['on_scene_location']);
					$on_scene_str = breakspace(get_text("On-Scene location") . ": " . $on_scene_facility_name_str .  $row['on_scene_location'], 30);
				}
				$output_str .= "<tr><td" . $on_scene_datetime_title_str . ">" . $the_date . "</td><td" . $on_scene_diff_title_str . ">" . get_text("On-scene") . "</td>" .
					"<td" . get_title_str($row['name']) . "></td><td colspan=2" . $on_scene_title_str . "><div class='td-div'>" . $on_scene_str . "</div></td></tr>\n";
			}
//------------------------------------------------------------------------------------------------------------------------------
			if (is_datetime($row['u2fenr'])) {
				$temp = preg_split("/ /", $row['u2fenr_i']);
				if ($temp[0] == $day_part_log_time) {
					$the_date = $temp[1];
				} else {
					$the_date = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['u2fenr_i'])) . "</span><br>" . $temp[1];
					$day_part_log_time = $temp[0];
				}
				$output_str .= "<tr><td" . get_title_str(date(get_variable("date_format"), strtotime($row['u2fenr_i']))) . ">" . $the_date . "</td><td" .
					get_title_str(datetime_difference($row['problemstart_i'], $row['u2fenr_i'])) . ">" . get_text("Fac en-route") . "</td><td" .
					get_title_str($row['name']) . "></td><td><td></td></tr>\n";
			}
//------------------------------------------------------------------------------------------------------------------------------
			if (is_datetime($row['u2farr']) || $row['receiving_location'] != "") {
				$facility_arrived_datetime_title_str = "";
				if (is_datetime($row['u2farr_i'])) {
					$facility_arrived_datetime_title_str = get_title_str(date(get_variable("date_format"), strtotime($row['u2farr_i'])));
				}
				$the_date = "";
				if (is_datetime($row['u2farr'])) {
					$temp = preg_split("/ /", $row['u2farr_i']);
					if ($temp[0] == $day_part_log_time) {
						$the_date = $temp[1];
					} else {
						$the_date = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['u2farr_i'])) . "</span><br>" . $temp[1];
						$day_part_log_time = $temp[0];
					}
				}
				$facility_arrived_diff_title_str = "";
				if (is_datetime($row['problemstart_i']) && is_datetime($row['u2farr_i'])) {
					$facility_arrived_diff_title_str = get_title_str(datetime_difference($row['problemstart_i'], $row['u2farr_i']));
				}
				$receiving_title_str = "";
				$receiving_str = "";
				if ($row['receiving_location'] != "") {
					if ($row['receiving_facility_name'] != "") {
						$receiving_facility_name_str = $row['receiving_facility_name'] . ", ";
					} else {
						$receiving_facility_name_str = "";
					}
					$receiving_title_str = get_title_str(wordwrap(get_text("Receiving location") . ": " . $receiving_facility_name_str . $row['receiving_location']));
					$receiving_str = breakspace(get_text("Receiving location") . ": " . $receiving_facility_name_str . $row['receiving_location'], 30);
				}
				$output_str .= "<tr><td" . $facility_arrived_datetime_title_str . ">" . $the_date . "</td><td" . $facility_arrived_diff_title_str . ">" . get_text("Fac arr") . "</td>" .
					"<td" . get_title_str($row['name']) . "></td><td colspan=2" . $receiving_title_str . "><div class='td-div'>" . $receiving_str . "</div></td></tr>\n";
			}
//------------------------------------------------------------------------------------------------------------------------------
			if (is_datetime($row['clear'])) {
				$temp = preg_split("/ /", $row['clear_i']);
				if ($temp[0] == $day_part_log_time) {
					$the_date = $temp[1];
				} else {
					$the_date = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['clear_i'])) . "</span><br>" . $temp[1];
					$day_part_log_time = $temp[0];
				}
				$output_str .= "<tr><td" . get_title_str(date(get_variable("date_format"), strtotime($row['clear_i']))) . ">" . $the_date . "</td><td" .
					get_title_str(datetime_difference($row['problemstart_i'], $row['clear_i'])) . ">" . get_text("Clear") . "</td><td" .get_title_str($row['name']) .
					"></td><td></td><td></td></tr>\n";
			}
//------------------------------------------------------------------------------------------------------------------------------
			if (($start_miles != "") || ($on_scene_miles != "") || ($end_miles != "")) {
				$output_str .= "<tr><td colspan='6'>";
				if ($start_miles != "") {
					$output_str .= get_text("Start Miles") .": " . $start_miles . "&nbsp;&nbsp;";
				}
				if ($on_scene_miles != "") {
					$output_str .= get_text("On Scene Miles") . ": " . $on_scene_miles . "&nbsp;&nbsp;";
				}
				if ($end_miles != "") {
					$output_str .= get_text("End Miles") . ": " . $end_miles;
				}
				$output_str .= "</td></tr>\n";
 			}
//------------------------------------------------------------------------------------------------------------------------------
			if ($tot_miles != "") {
				$output_str .= "<tr><td colspan='5'>" . get_text("TOTAL MILES") . ": " . $tot_miles . "</td></tr>\n";
			}
//------------------------------------------------------------------------------------------------------------------------------
		}	
	}
	print $output_str;
}

function show_actions($ticket_id, $ticket_report = false) {
	$click_to_edit_str = "<br><br>" . get_text("Click to edit.");
	$border_top_str = " border-top: 0px;";
	$sort_order_str = " DESC";
	if ($ticket_report) {
		$click_to_edit_str = "";
		$border_top_str = "";
		$sort_order_str = " ASC";
	}
	?>
	<tr>
		<th style="width: 15%;<?php print $border_top_str;?>"><nobr><?php print get_text("DateTime");?></nobr></th>
		<th style="width: 0%;<?php print $border_top_str;?>"></th>
		<th style="width: 25%;<?php print $border_top_str;?>"><nobr><?php print get_text("Unit");?></nobr></th>
		<th style="width: 45%;<?php print $border_top_str;?>"><nobr><?php print get_text("Action");?></nobr></th>
		<th style="width: 10%;<?php print $border_top_str;?>"><nobr><?php print get_text("by");?></nobr></th>
	</tr>
	<?php

	$query = "SELECT `actions`.`id` AS `action_id`, " .
		"`actions`.`datetime` AS `action_date`, " .
		"`actions`.`unit_id` AS `action_unit` , " .
		"`actions`.`description` AS `action_description`, " .
		"`actions`.`updated` AS `action_updated`, " .
		"`actions`.`user_id` AS `action_user`, " .
		"`units`.`handle` AS `unit_handle`, " .
		"`units`.`name` AS `unit_name` " .
		"FROM `actions` " .
		"LEFT JOIN `units` ON (`units`.`id` = `actions`.`unit_id`) " .
		"WHERE `actions`.`ticket_id` = " . $ticket_id . " ORDER BY `action_updated`" . $sort_order_str . ";";

	$result = db_query($query, __FILE__, __LINE__);
	if ((db_num_rows($result)) > 0) {
		$day_part_log_time = "";
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			if ($ticket_report) {
				$onclick_str = "";
			} else {
				$onclick_str = " onclick=\"location.href='action.php?back=ticket&ticket_id=" . $ticket_id . "&action_id=" . $row['action_id'] . "&function=edit'\"";
			}
			$temp_date_time = preg_split("/ /", $row['action_updated']);
			if ($temp_date_time[0] == $day_part_log_time) {
				$log_date_time_str = $temp_date_time[1];
			} else {
				$log_date_time_str = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['action_updated'])) . "</span><br> " . $temp_date_time[1];
				$day_part_log_time = $temp_date_time[0];
			}
	?>
	<tr<?php print $onclick_str;?>>
		<td<?php print get_title_str(date(get_variable("date_format"), strtotime($row['action_updated'])) . $click_to_edit_str);?> nowrap><?php print $log_date_time_str;?></td>
		<td></td>
		<td<?php print get_title_str($row['unit_name'] . $click_to_edit_str);?>><?php print remove_nls($row['unit_handle']);?></td>
		<td<?php print get_title_str($row['action_description'] . "  " . get_text("Written") . ": " . date(get_variable("date_format"), strtotime($row['action_date'])) . $click_to_edit_str);?>>
			<div class="td-div"><?php print breakspace(remove_nls($row['action_description']), 30) . "  " . get_text("Written") . ": " . date(get_variable("date_format"), strtotime($row['action_date']));?></div>
		</td>
		<td<?php print get_title_str(get_user_name($row['action_user']) . $click_to_edit_str);?>><?php print remove_nls(get_user_name($row['action_user']));?></td>
	</tr>
	<?php
		}
	} else {
	?>
	<tr>
		<th colspan=5 style="text-align: center;"><?php print get_text("No actions.");?></th>
	</tr>
	<?php
	}
}

function show_ticket_log($ticket_id) {
	require ("./incs/log_codes.inc.php");

	$query = "SELECT `l`.`code`, " .
		"`l`.`client_address`, " .
		"`l`.`datetime`, " .
		"`l`.`text` AS `log_text`, " .
		"`r`.`name` AS `unit_name`, " .
		"`r`.`handle` AS `unit_handle`, " .
		"`u`.`name` AS `user_name` " .
		"FROM `log` `l` " .
		"LEFT JOIN `tickets` `t` ON (`l`.`ticket_id` = `t`.`id`) " .
		"LEFT JOIN `users` `u` ON (`l`.`user_id` = `u`.`id`) " .
		"LEFT JOIN `units` `r` ON (`l`.`unit_id` = `r`.`id`) " .
		"LEFT JOIN `unit_status` `s` ON (`l`.`text` = `s`.`id`) " .
		"WHERE `l`.`ticket_id` = " . $ticket_id . " " .
		"ORDER BY `datetime` ASC;";		

	$result = db_query($query, __FILE__, __LINE__);

	?>
	<tr>
		<th colspan=5 style="text-align: center;"><h5><strong style="white-space: nowrap;"><?php print get_text("Ticket Log");?></strong></h5></th>
	</tr>
	<tr>
		<th style="text-align: left;"><nobr><?php print get_text("DateTime");?></nobr></th>
		<th style="text-align: left;"><nobr><?php print get_text("Code");?></nobr></th>
		<th style="text-align: left;"><nobr><?php print get_text("Unit");?></nobr></th>
		<th style="text-align: left;"><nobr><?php print get_text("Text");?></nobr></th>
		<th style="text-align: left;"><nobr><?php print get_text("by");?></nobr></th>
	</tr>
	<?php
	$day_part_log_time = "";
	while ($row = stripslashes_deep(db_fetch_assoc($result))) {
		$temp_log_time = preg_split("/ /", $row['datetime']);
		if ($temp_log_time[0] == $day_part_log_time) {
			$log_date_time = $temp_log_time[1];
		} else {
			$log_date_time = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['datetime'])) . "</span><br> " . $temp_log_time[1];
			$day_part_log_time = $temp_log_time[0];
		}
		if ($row['log_text']) {
			$log_text_title_str = get_title_str($row['log_text']);
			$log_text_str = breakspace(remove_nls($row['log_text']), 30);
		} else {
			$log_text_title_str = "";
			$log_text_str = "";
		}
		?>
		<tr>
			<td<?php print get_nowrap_title_str(date(get_variable("date_format"), strtotime($row['datetime'])));?>><?php print $log_date_time;?></td>
			<td<?php print get_nowrap_title_str($types[$row['code']]);?>><?php print $types[$row['code']];?></td>
			<td<?php print get_nowrap_title_str(remove_nls($row['unit_name']));?>><nobr><?php print remove_nls($row['unit_handle']);?></nobr></td>
			<td<?php print $log_text_title_str;?>><div class="td-div"><?php print $log_text_str;?></div></td>
			<td<?php print get_nowrap_title_str(remove_nls($row['user_name']));?>><?php print remove_nls($row['user_name']);?></td>
		</tr>
		<?php
	}
}

function generate_log_where_str($function, $start_date, $end_date, $custom_where = "", $filter = "") {
	if (empty ($filter) || $filter == "") {
		$filter = array ("communication" => "false", "status" => "false", "settings" => "false");
	}
	$or_str = "(`code` = " . $GLOBALS['LOG_COMMENT'] . ")";
	$or_str .= " OR (`code` = " . $GLOBALS['LOG_INCIDENT_OPEN'] . ")";
	$or_str .= " OR (`code` = " . $GLOBALS['LOG_INCIDENT_CLOSE'] . ")";
	$or_str .= " OR (`code` = " . $GLOBALS['LOG_INCIDENT_SCHEDULED'] . ")";
	if ($filter["communication"] != "true") {
		$or_str .= " OR ((`code` = " . $GLOBALS['LOG_EMGCY_HI'] . ") AND (`l`.`ticket_id` = ''))";
		$or_str .= " OR ((`code` = " . $GLOBALS['LOG_EMGCY_LO'] . ") AND (`l`.`ticket_id` = ''))";
		$or_str .= " OR ((`code` = " . $GLOBALS['LOG_CALL_REQ'] . ") AND (`l`.`ticket_id` = ''))";
		$or_str .= " OR ((`code` = " . $GLOBALS['LOG_CALL_MANACKN'] . ") AND (`l`.`ticket_id` = ''))";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_MESSAGE_RECEIVE'] . ")";
		$or_str .= " OR ((`code` = " . $GLOBALS['LOG_SMS_MESSAGE_SEND'] . ") AND (`l`.`ticket_id` = ''))";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_SMS_MESSAGE_ERROR'] . ")";
		$or_str .= " OR ((`code` = " . $GLOBALS['LOG_EMAIL_MESSAGE_SEND'] . ") AND (`l`.`ticket_id` = ''))";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_EMAIL_MESSAGE_ERROR'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_PRINT_JOB_SEND'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_PRINT_JOB_ERROR'] . ")";
	}
	if ($filter["status"] != "true") {
		$or_str .= " OR ((`code` = " . $GLOBALS['LOG_UNIT_STATUS'] . ") AND (`l`.`ticket_id` = ''))";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_UNIT_TO_QUARTERS'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_UNIT_NO_SERVICE'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_FACILITY_STATUS'] . ")";
	}
	if ((is_super() || is_admin()) && $filter["settings"] != "true") {
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_UNIT_ADD'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_UNIT_CHANGE'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_UNIT_DELETED'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_FACILITY_ADD'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_FACILITY_CHANGE'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_FACILITY_DELETED'] . ")";
	}
	if (is_super() && $filter["settings"] != "true") {
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_SIGN_IN'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_SIGN_OUT'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_INFO'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_ERROR'] . ")";
		$or_str .= " OR (`code` = " . $GLOBALS['LOG_CONFIGURATION_EDIT'] . ")";
	}
	$report_log_settings = explode(",", get_variable("report_log"));
	$report_log_log = trim($report_log_settings[0]);
	if ($report_log_log < 5) {
		$report_log_log = 5;
	}
	$where_str = "";
	switch ($function) {
	case "reports":
		$where_str .= " WHERE ((`l`.`datetime` >= '" . date("Y-m-d H:i:s", $start_date) . "') AND (`l`.`datetime` < '" . date("Y-m-d H:i:s", $end_date) . "')) AND (";
		$where_str .= $or_str;
		$where_str .= ") " . $custom_where;
		$where_str .= " ORDER BY `l`.`datetime` ASC";
		break;
	case "get_infos":
		$where_str .= " WHERE (DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " . $report_log_log . " MINUTE) <= `l`.`datetime`) AND (";
		$where_str .= $or_str;
		$where_str .= ") ORDER BY `l`.`datetime` DESC LIMIT 1";
		break;
	default:
		$where_str .= " WHERE (DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " . $report_log_log . " MINUTE) <= `l`.`datetime`) AND (";
		$where_str .= $or_str;
		$where_str .= ") ORDER BY `l`.`datetime` DESC";	
	}
	return $where_str;
}

function show_log_report($function, $start_date = 0, $end_date = 0, $custom_where = "", $filter) {
	global $types;
	$caption_no_data = get_text("No data for this period!");
	if ($function == "reports") {
		$caption_no_data = get_text("No data for this filter!");
	}
	$where_str = generate_log_where_str($function, $start_date, $end_date, $custom_where, $filter);

	$query = " SELECT DISTINCT `l`.`client_address`, " .
		"`l`.`code`, " .
		"`l`.`datetime` AS `datetime`, " .
		"`u`.`name` AS `user_name`, " .
		"`l`.`text` AS `log_text`, " .
		"`l`.`ticket_id`, " .
		"`r`.`handle` AS `unit_handle`, " .
		"`r`.`name` AS `unit_name`, " .
		"`f`.`handle` AS `facility_handle`, " .
		"`f`.`name` AS `facility_name`, " .
		"`t`.`incident_name` AS `incident_name`, " .
		"`t`.`incident_type_id` AS `incident_type` " .
		"FROM `log` `l` " .
		"LEFT JOIN `tickets` `t` ON (`l`.`ticket_id` = `t`.`id`) " .
		"LEFT JOIN `users` `u` ON (`l`.`user_id` = `u`.`id`) " .
		"LEFT JOIN `units` `r` ON (`l`.`unit_id` = `r`.`id`) " .
		"LEFT JOIN `assigns` `a` ON (`l`.`ticket_id` = `a`.`ticket_id`) " .
		"LEFT JOIN `facilities` `inc_fac` ON `inc_fac`.`id` = `t`.`facility_id` " .
		"LEFT JOIN `facilities` `on_sc_fac` ON `on_sc_fac`.`id` = `a`.`on_scene_facility_id` " .
		"LEFT JOIN `facilities` `rec_fac` ON `rec_fac`.`id` = `a`.`receiving_facility_id` " .
		"LEFT JOIN `facilities` `f` ON (`l`.`facility_id` = `f`.`id`)" . $where_str . ";";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_affected_rows($result) == 0) {
	?>
	<tr>
		<th colspan=6 style="text-align: center;"><?php print $caption_no_data;?></th>
	</tr>
	<?php
	} else {
	?>
	<tr>
		<th style="text-align: left; width: 15%;"><nobr><?php print get_text("DateTime");?></nobr></th>
		<th style="text-align: left; width: 15%;"><nobr><?php print get_text("Code");?></nobr></th>
		<th style="text-align: left; width: 10%;"><nobr><?php print get_text("inc_name_short");?></nobr></th>
		<th style="text-align: left; width: 20%;"><nobr><?php print get_text("Unit") . "&nbsp;/&nbsp;" . get_text("Facility");?></nobr></th>
		<th style="text-align: left; width: 35%;"><nobr><?php print get_text("Text");?></nobr></th>
		<th style="text-align: left; width: 5%;"><nobr><?php print get_text("by");?></nobr></th>
	</tr>
	<?php
		$day_part_log_time = "";
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$temp_log_time = preg_split("/ /", $row['datetime']);
			if ($temp_log_time[0] == $day_part_log_time) {
				$log_date_time = $temp_log_time[1];
			} else {
				$log_date_time = "<span style='text-decoration: underline;'>" . date(get_variable("date_format_date_only"), strtotime($row['datetime'])) . "</span><br> " . $temp_log_time[1];
				$day_part_log_time = $temp_log_time[0];
			}
			$br_str = "";
			if ($row['unit_name'] || $row['facility_name']) {
				if (!empty ($row['unit_name']) && !empty ($row['facility_name'])) {
					$br_str = "<br>";
				}
				$unit_facility_title_str = get_nowrap_title_str("<nobr>" . remove_nls($row['unit_name']) . "</nobr>" . $br_str . " <nobr>" . remove_nls($row['facility_name']) . "</nobr>");
			} else {
				$unit_facility_title_str = "";
			}
			if (empty ($row['user_name'])) {
				$row['user_name'] = get_text("System");
			}
	?>
	<tr>
		<td class="td-div"<?php print get_nowrap_title_str(date(get_variable("date_format"), strtotime($row['datetime'])));?>><?php print $log_date_time;?></td>
		<td class="td-div"<?php print get_nowrap_title_str(remove_nls($types[$row['code']]));?>><?php print $types[$row['code']];?></td>	
		<td class="td-div"<?php print get_nowrap_title_str(remove_nls($row['incident_name']));?>><?php ($row['incident_name'] != "")? print $row['incident_name'] : "";?></td>
		<td class="td-div"<?php print $unit_facility_title_str;?>><?php print remove_nls($row['unit_handle']) . "" . $br_str . remove_nls($row['facility_handle']);?></td>
		<td class="td-div"<?php print get_title_str($row['log_text']);?>><?php print breakspace(html_entity_decode(remove_nls($row['log_text'])), 30);?></td>	<!-- 	//TODO html_entities aus log-text nehmen -->
		<td class="td-div"<?php print get_nowrap_title_str(remove_nls($row['user_name']));?>><?php print remove_nls($row['user_name']);?></td>
	</tr>
	<?php
		}
	}
}

//====== misc

function count_units_and_facilities_and_users() {

	$query = "SELECT * " .
		"FROM `units`;";

	$result = db_query($query, __FILE__, __LINE__);
	$count_units = db_num_rows($result);

	$query = "SELECT * " .
		"FROM `facilities`;";

	$result = db_query($query, __FILE__, __LINE__);
	$count_facilties = db_num_rows($result);

	$query = "SELECT * " .
		"FROM `users`;";

	$result = db_query($query, __FILE__, __LINE__);
	$count_users = db_num_rows($result);
	if ($count_users <= 2) {
		$count_users = 0;
	}
	return $count_units + $count_facilties + $count_users;
}

function get_current_path($filename) {
	if (DIRECTORY_SEPARATOR === "\\") {
		return str_replace("/", "\\", getcwd() . DIRECTORY_SEPARATOR . $filename);	//to windows
	} else {
		return str_replace("\\", "/", getcwd() . DIRECTORY_SEPARATOR . $filename);	//to *nix
	}
}

function check_browser() {
	$browsers = "mozilla msie gecko firefox ";
	$browsers.= "konqueror safari netscape navigator ";
	$browsers.= "opera mosaic lynx amaya omniweb chrome chromium edge edg";
	$browsers = explode(" ", $browsers);
	$user_agent = @strtolower($_SERVER['HTTP_USER_AGENT']);
	$user_agent_length = strlen($user_agent);
	for ($i = 0; $i < count($browsers); $i++) {
		$browser = $browsers[$i];
		if (strlen(stristr($user_agent, $browser)) > 0) {
			$version = "";
			$navigator = $browser;
			$j = strpos($user_agent, $navigator) + strlen($navigator) + 1;
			for (; $j <= $user_agent_length; $j++) {
				$s = substr($user_agent, $j, 1);
				if (is_numeric($version.$s)) {
					$version .= $s;
				} else {
					break;
				}
			}
		}
	}
	if ($navigator == "edg") $navigator = "edge";
	return $navigator . " " . $version;
}

function dont_delete_unit_status($unit_status_id) {
	$no_evaluation_id = 0;

	$query_no_evaluation = "SELECT `id` " .
		"FROM `unit_status` " .
		"WHERE `dispatch` > 3 " .
		"ORDER BY `id` ASC " .
		"LIMIT 1;";

	$result_no_evaluation = db_query($query_no_evaluation, __FILE__, __LINE__);
	if (db_affected_rows($result_no_evaluation) > 0) {
		$row_no_evaluation = stripslashes_deep(db_fetch_array($result_no_evaluation));
		$no_evaluation_id = $row_no_evaluation['id'];
	}

	$query_units = "SELECT count(`al`.`resource_id`) AS `quantity` " .
		"FROM `units` `u` " .
		"LEFT JOIN `allocates` `al` ON `al`.`type` = " . $GLOBALS['TYPE_UNIT'] . " AND `al`.`resource_id` = `u`.`id` " .
		"WHERE `u`.`unit_status_id` = " . $unit_status_id . ";";

	$row_status_id = db_fetch_array(db_query($query_units, __FILE__, __LINE__));
	if (($row_status_id["quantity"] > 0) ||
		(get_variable("_api_clr_stat") == $unit_status_id) ||
		(get_variable("_api_quat_stat") == $unit_status_id) ||
		(get_variable("_api_off_duty_stat") == $unit_status_id) ||
		($no_evaluation_id == $unit_status_id)) {
		return true;
	} else {
		return false;
	}
}

function get_assigns($unit_id, $ticket_id) {
	$ticket_id_str = "";
	if ($ticket_id != 0) {
		$ticket_id_str = "AND `ticket_id` = " . $ticket_id . " ";
	}

	$query = "SELECT `assigns`.`id` AS `id`, " .
		"`assigns`.`dispatched`, " .
		"`assigns`.`responding`, " .
		"`assigns`.`on_scene`, " .
		"`assigns`.`u2fenr`, " .
		"`assigns`.`u2farr`, " .
		"`ticket_id` " .
		"FROM `assigns` " .
		"LEFT JOIN `tickets` ON `assigns`.`ticket_id` = `tickets`.`id` " .
		"WHERE `unit_id` = " . $unit_id . " " .
		$ticket_id_str .
		"AND `clear` IS NULL " .
		"AND `tickets`.`status` = 2 " .
		"ORDER BY `id` ASC " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) != 0) {
		$row = stripslashes_deep(db_fetch_array($result));
		$code = 0;
		if (is_datetime($row['dispatched'])) {
			$code = $GLOBALS['LOG_CALL_DISPATCHED'];
		}
		if (is_datetime($row['responding'])) {
			$code = $GLOBALS['LOG_CALL_RESPONDING'];
		}
		if (is_datetime($row['on_scene'])) {
			$code = $GLOBALS['LOG_CALL_ON_SCENE'];
		}
		if (is_datetime($row['u2fenr'])) {
			$code = $GLOBALS['LOG_CALL_FACILITY_ENROUTE'];
		}
		if (is_datetime($row['u2farr'])) {
			$code = $GLOBALS['LOG_CALL_FACILITY_ARRIVED'];
		}
		$assign_data = array ($row['id'], $row['ticket_id'], $code);
	} else {
		$assign_data = array (false, false, false);
	}
	return $assign_data;
}

function get_handle_array() {
	$return_array = array ();

	$query = "SELECT `id`, " .
		"`handle` " .
		"FROM `units`;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) != 0) {
		while ($row = stripslashes_deep(db_fetch_assoc($result))){
			$id = $row['id'];
			$handle = $row['handle'];
			$return_array["unit"][$id] = trim($handle);
		}
	}

	$query = "SELECT `id`, " .
		"`handle` FROM " .
		"`facilities`;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) != 0) {
		while ($row = stripslashes_deep(db_fetch_assoc($result))){
			$id = $row['id'];
			$handle = $row['handle'];
			$return_array["facility"][$id] = trim($handle);
		}
	}

	$query = "SELECT `id`, " .
		"`name` AS `user_name` " .
		"FROM `users`;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) != 0) {
		while ($row = stripslashes_deep(db_fetch_assoc($result))){
			$id = $row['id'];
			$handle = $row['user_name'];
			$return_array["user"][$id] = trim($handle);
		}
	}
	return $return_array;
}

function get_type($id) {
	if ($id == 0) {
		return "";
	}

	$query = "SELECT * " .
		"FROM `incident_types` " .
		"WHERE `id`= " . $id . " " .
		"LIMIT 1";

	$result_type = db_query($query, __FILE__, __LINE__);
	$row_type = stripslashes_deep(db_fetch_assoc($result_type));
	if (isset ($row_type['type'])) {
		return $row_type['type'];
	} else {
		return "?";
	}
}

function show_day_night_style() {
	if ((!(isset ($_SESSION['day_night']))) || (isset ($_SESSION['day_night']) && ($_SESSION['day_night'] == "day"))) {
		?>
<style>
	body, text, h4 {
		background-color: #FFFFFF;
		color: #000000;
	}
		table th, td {
		background-color: transparent;
	}
	.panel {
		background-color: transparent;
	}
</style>
	<?php
	} else {
	?>
<style>
	body, text, h4 {
		background-color: #000000;
		color: #FFFFFF;
	}
	table th, td {
		background-color: <?php print get_variable("night_color");?>;
	}
	.panel {
		background-color: <?php print get_variable("night_color");?>;
	}
	.modal-content {
		background-color: <?php print get_variable("night_color");?>;
	}
	.modal-body {
		background-color: <?php print get_variable("night_color");?>;
	}
	.infobox-head {
		background-color: <?php print get_variable("night_color");?>;
	}
</style>
	<?php
	}
}

function get_status($status) {
	switch ($status)	{
	case 1:
		return get_text("Closed");
		break;
	case 2:
		return get_text("Open");
		break;
	case 3:
		return get_text("Scheduled");
		break;
	default:
		return get_text("Status error");
	}
}

function get_user_name($unit_id) {
	
	$result	= db_query("SELECT `name` AS `user_name` " .
		"FROM `users` " .
		"WHERE `id` = " . $unit_id . " " .
		"LIMIT 1;", __FILE__, __LINE__);
	
	$row = stripslashes_deep(db_fetch_assoc($result));
	if (db_affected_rows($result) == 0) {	
		return "unknow";
	} else {
		return remove_nls($row['user_name']);
	}
}

function get_severity($severity) {
	switch($severity) {
	case $GLOBALS['SEVERITY_NORMAL']:
		return get_text("Normal");
		break;
	case $GLOBALS['SEVERITY_MEDIUM']:
		return get_text("Medium");
		break;
	case $GLOBALS['SEVERITY_HIGH']:
		return get_text("High");
		break;
	default:
		return "Severity error";
		break;
	}
}

function get_callboard_height() {
	$callboard_settings = explode(",", get_variable("callboard"));
	$callboard_fixed_part = trim($callboard_settings[1]);
	$callboard_per_line = trim($callboard_settings[2]);
	$callboard_min = trim($callboard_settings[3]);
	$callboard_max = trim($callboard_settings[4]);

	$query = "SELECT * " .
		"FROM `assigns` " .
		"WHERE `clear` IS NULL " .
		"OR DATE_FORMAT(`clear`,'%y') = '00';";

	$result = db_query($query, __FILE__, __LINE__);
	$lines = db_num_rows($result);
	unset ($result);
	$height = (($lines * $callboard_per_line ) + $callboard_fixed_part);
	$height = ($height < $callboard_min)? $callboard_min: $height;
	$height = ($height > $callboard_max)? $callboard_max: $height;
	return (integer) $height;
}

function set_unit_updated($assign_id) {

	$query = "SELECT `unit_id` " .
		"FROM `assigns` " .
		"WHERE `id` = " . $assign_id . " " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	$row = db_fetch_assoc($result);

	$query = "UPDATE `units` " .
		"SET `updated` = " . quote_smart(mysql_datetime()) . ", " .
		"`user_id` = " . $_SESSION['user_id'] . " " .
		"WHERE `id` = " . $row['unit_id'] . ";";

	db_query($query, __FILE__, __LINE__);
	return true;
}

function do_log($code = 0, $ticket_id = 0, $unit_id = 0, $text = "", $facility_id = 0, $updated = "", $lat = 0.99999, $lng = 0.99999) {
	if ($code == "") {
		$code = 0;
	}
	if ($ticket_id == "") {
		$ticket_id = 0;
	}
	if ($unit_id == "") {
		$unit_id = 0;
	}
	if ($facility_id == "") {
		$facility_id = 0;
	}
	$text = substr($text, 0, 2047);
	if ($lat == "") {
		$lat = 0.999999;
	}
	if ($lng == "") {
		$lng = 0.999999;
	}
	$user_id = 0;
	if (array_key_exists("user_id", $_SESSION)) {
		$user_id = $_SESSION['user_id'];
	}
	if ($updated == "") {
		$updated = mysql_datetime();
	}

	$query = "INSERT INTO `log` (`code`, `ticket_id`, `unit_id`, `facility_id`, " .
		"`text`, `lat`, `lng`, `user_id`, " .
		"`client_address`, `datetime`) " .
		"VALUES(" . $code . ", " . $ticket_id . ", " . $unit_id . ", " . $facility_id . ", " .
		trim_quote($text) . ", " . $lat . ", " . $lng . ", " . $user_id . ", " .
		trim_quote($_SERVER['REMOTE_ADDR']) . ", " . trim_quote($updated) . ");";

	db_query($query, __FILE__, __LINE__);
}

function get_call_progression_time($elapsed, $data_progression, $additional_assigns_str) {
	$count_mins = 0;
	$count_secs = 0;
	$progression = "";
	if (($elapsed < 5940) && ($elapsed >= 0)) {
		$count_mins = floor($elapsed / 60);
		if ($count_mins < 10) {
			$count_mins = "0" . $count_mins;
		}
		$count_secs = floor($elapsed - $count_mins * 60);
		if ($count_secs < 10) {
			$count_secs = "0" . $count_secs;
		}
		$progression = "<div class='callprogression_timer' data-counter=" . $elapsed . " data-progression='" . $data_progression . "' style='float: left; width: 30%;'>&nbsp;" . $data_progression . "&nbsp;" . $count_mins . ":" . $count_secs . "</div>" .
			"<div style='float: right; width: 30%;'>" . $additional_assigns_str . "</div>";
	} else {
		$progression = "<div class='callprogression_timer' data-counter=" . $elapsed . " data-progression='" . $data_progression . "' style='float: left; width: 30%;'>&nbsp;" . $data_progression . "&nbsp;" . "<span style='text-decoration: line-through;'>99:00</span></div>" .
			"<div style='float: right; width: 30%;'>" . $additional_assigns_str . "</div>";
	}
	return $progression;
}

function get_status_display_str($row, $click_str = "", $disp_inc_stat) {
	$additional_assigns_str = "";
	$incidents_str = get_text("Incident");
	if ($disp_inc_stat > 1) {
		$additional_incidents = $disp_inc_stat - 1;
		if ($disp_inc_stat > 2) {
			$incidents_str = get_text("Tickets");
		}
		$additional_assigns_str = "+" . $additional_incidents . " " . $incidents_str;
	}
	if (is_datetime($row['u2farr'])) {
		$inner_html_str = get_call_progression_time(elapsed($row['u2farr']), get_text("Fac arr"), $additional_assigns_str);
		return "<div name='callprogression' class='label u2farr col-md-12' style='height: auto; float: left;'" . $click_str . " data-status='facility_arrived' " .
			"data-assign_id=" . $row['assign_id'] . " data-info-text='" . $additional_assigns_str . "'>" . $inner_html_str . "</div>";
	}
	if (is_datetime($row['u2fenr'])) {
		$inner_html_str = get_call_progression_time(elapsed($row['u2fenr']), get_text("Fac en-route"), $additional_assigns_str);
		return "<div name='callprogression' class='label u2fenr col-md-12' style='height: auto; float: left;'" . $click_str . " data-status='facility_enroute' " .
			"data-assign_id=" . $row['assign_id'] . " data-info-text='" . $additional_assigns_str . "'>" . $inner_html_str . "</div>";
	}
	if (is_datetime($row['on_scene'])) {
		$inner_html_str = get_call_progression_time(elapsed($row['on_scene']), get_text("On-scene"), $additional_assigns_str);
		return "<div name='callprogression' class='label on_scene col-md-12' style='height: auto; float: left;'" . $click_str . " data-status='on_scene' " .
			"data-assign_id=" . $row['assign_id'] . " data-info-text='" . $additional_assigns_str . "'>" . $inner_html_str . "</div>";
	}
	if (is_datetime($row['responding'])) {
		$inner_html_str = get_call_progression_time(elapsed($row['responding']), get_text("Responding"), $additional_assigns_str);
		return "<div name='callprogression' class='label responding col-md-12' style='height: auto; auto; float: left;'" . $click_str . " data-status='responding' " .
			"data-assign_id=" . $row['assign_id'] . " data-info-text='" . $additional_assigns_str . "'>" . $inner_html_str . "</div>";
	}
	if (is_datetime($row['dispatched'])) {
		$inner_html_str = get_call_progression_time(elapsed($row['dispatched']), get_text("Dispatched"), $additional_assigns_str);
		return "<div name='callprogression' class='label dispatched col-md-12' style='height: auto; float: left;'" . $click_str . " data-status='dispatched' " .
			"data-assign_id=" . $row['assign_id'] . " data-info-text='" . $additional_assigns_str . "'>" . $inner_html_str . "</div>";
	}
}

function show_infobox($size = "") {
	$large_id_str = "";
	$width_str = "";
	if ($size == "large") {
		$large_id_str = "_large";
		$width_str = " style='width: 1000px;'";
	}
	?>
	<div class="modal fade" id="infobox<?php print get_text("$large_id_str");?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog"<?php print $width_str;?> role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" aria-label="close" onclick="hide_infobox<?php print get_text("$large_id_str");?>();"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<div id="infobox_head<?php print get_text("$large_id_str");?>" class="infobox-head"></div>
					</h4>
				</div>
				<div id="infobox_body<?php print get_text("$large_id_str");?>" class="modal-body text_black" style="display: none; margin-bottom: 2px;"></div>
				<input id="infobox_input<?php print get_text("$large_id_str");?>" style="display: none; color: #000000; background-color: #FFFFFF; margin-left: 10px; margin-top: 5px; margin-bottom: 5px; width: 30%;" type="text" class="form-control" value="">
				<div class="modal-footer">
					<button id="cancel_button<?php print get_text("$large_id_str");?>" type="button" class="btn btn-default" style="display: none;" onclick="hide_infobox<?php print get_text("$large_id_str");?>(false);"><?php print get_text("Cancel");?></button>
					<button id="close_button<?php print get_text("$large_id_str");?>" type="button" class="btn btn-default" onclick="hide_infobox<?php print get_text("$large_id_str");?>(false);"><?php print get_text("Close");?></button>
					<button id="confirm_button<?php print get_text("$large_id_str");?>" type="button" class="btn btn-primary" style="display: none;" onclick="hide_infobox<?php print get_text("$large_id_str");?>(true);"><?php print get_text("OK");?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function csv2raw($csvstr) {
	$charset = mb_detect_encoding($csvstr, "UTF-8, ISO-8859-1, ISO-8859-15", true);
	return mb_convert_encoding($csvstr, "UTF-8", $charset);
}

function csv2mysql($csvstr) {
	$csvstr = csv2raw($csvstr);
	$mysqlstr = db_real_escape_string($csvstr);
	return ($mysqlstr);
}

function show_prevent_browser_back_button() {
	$auto_dispatch_settings = explode(",", get_variable("auto_dispatch"));
	$prevent_browser_back_button = trim($auto_dispatch_settings[2]);
	if ($prevent_browser_back_button == 1) {
		print "prevent_browser_back_button();\r";
	}
}

//====== selects

function get_select_str($query, $form_id, $form_name, $class, $title, $style, $onchange, $option_0, $element_id = 0, $no_options, $tabindex) {
	$form_id_str = "";
	if ($form_id != "") {
		$form_id_str = " id=\"" . $form_id . "\"";
	}
	$form_name_str = "";
	if ($form_name != "") {
		$form_name_str = " name=\"" . $form_name . "\"";;
	}
	$class_str = "";
	if ($class != "") {
		$class_str = " class=\"" . $class . "\"";
	}
	$title_str = "";
	if ($title != "") {
		$title_str = get_nowrap_title_str($title);
	}
	$style_str = "";
	if ($style != "") {
		$style_str = " style=\"" . $style . "\"";
	}
	$onchange_str = "";
	if ($onchange != "") {
		$onchange_str = " onchange=\"" . $onchange . "\"";
	}
	$option_0_str = "";
	if ($option_0 != "") {
		$option_0_str = "<option value=0 selected>" . $option_0 . "</option>";
	}
	$tabindex_str = "";
	if ($tabindex != "") {
		$tabindex_str = " tabindex=" . $tabindex;
	}
	$return_str = "<select" . $form_id_str . $form_name_str . $class_str . $title_str . $style_str . $onchange_str . $tabindex_str . ">";
	$return_str .= $option_0_str;
	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) > 0) {
		$option_group = strval(rand());
		$i = 0;
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			if ((isset ($row['option_group'])) && ($option_group != $row['option_group'])) {
				if (($i != 0) && (trim($option_group) != "")) {
					$return_str .= "</optgroup>\n";
				}
				$option_group = $row['option_group'];
				if (trim($option_group) != "") {
					$return_str .= "<optgroup label='" . remove_nls($row['option_group']) . "'>\n";
				}
			}
			$selected_str = "";
			if ((isset ($row['option_value'])) && ($row['option_value'] == $element_id)) {
				$selected_str = " selected";
			}
			if (isset ($row['option_value'])) {
				$return_str .= "\t<option value=" . $row['option_value'] . " " . $selected_str . ">" . remove_nls($row['option_text']) . "</option>\n";
			} else {
				$return_str .= "\t<option>" . remove_nls($row['option_text']) . "</option>\n";
			}
			$i++;
		}
	} else {
		$return_str .= "\t<option disabled>" . $no_options . "</option>\n";
	}
	$return_str .= "</select>";
	return $return_str;
}

function get_unit_select_str($select_type = "report", $unit_id = 0, $ticket_id = 0) {
	$option_0 = "";
	$query = "";
	$form_id = "";
	$form_name = "";
	$class = "";
	$title = "";
	$style = "";
	$onchange = "";
	$no_elements = "";
	$tabindex = "";
	switch ($select_type) {
	case "report":
		$option_0 = get_text("Select");

		$query = "SELECT `r`.`handle` AS `option_text`, " .
			"`r`.`id` AS `option_value`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `units` `r` " .
			"LEFT JOIN `unit_types` `t` ON (`r`.`type` = `t`.`id`) " .
			"WHERE `r`.`id` IN (SELECT DISTINCT `unit_id` FROM `assigns`) " .
			"OR `r`.`id` IN (SELECT DISTINCT `unit_id` FROM `log`) " .
			"ORDER BY `option_group` ASC, `option_text` ASC;";

		$form_id = "frm_unit";
		$form_name = "frm_unit";
		$class = "form-control";
		$title = "";
		$style = "";
		$onchange = "query_changed();";
		$no_elements = get_text("No units available!");
		$tabindex = "";
		break;
	case "log":
		$option_0 = get_text("Unit");

		$query = "SELECT `r`.`handle` AS `option_text`, " .
			"`r`.`id` AS `option_value`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `units` `r` " .
			"LEFT JOIN `allocates` `a` ON (`r`.`id` = `a`.`resource_id`) " .
			"LEFT JOIN `unit_types` `t` ON (`r`.`type` = `t`.`id`) " .
			"LEFT JOIN `unit_status` `s` ON (`r`.`unit_status_id` = `s`.`id`) " .
			"WHERE `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"AND (`s`.`dispatch` < 3 OR `r`.`unit_status_id` = 0 " .
			"OR (SELECT COUNT(*) as `numfound` FROM `assigns` " .
			"WHERE `assigns`.`unit_id` = `r`.`id` " .
			"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00')) > 0) " .
			"ORDER BY `option_group` ASC, `option_text` ASC;";

		$form_id = "unit_id";
		$form_name = "unit_id";
		$class = "sit label";
		$title = "";
		$style = "width: 40%; float: left; margin-top: 5px;";
		$onchange = "";
		$no_elements = get_text("No units in service!");
		$tabindex = "3";
		break;
	case "action":
		$option_0 = get_text("Unit");

		$query = "SELECT DISTINCT `unit_id` AS `option_value`, " .
			"`u`.`handle` AS `option_text`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `assigns` " .
			"LEFT JOIN `units` `u` ON (`assigns`.`unit_id` = `u`.`id`) " .
			"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
			"WHERE `ticket_id` = " . $ticket_id . " " .
			"ORDER BY `option_group` ASC, `option_text` ASC;";

		$class = "sit label";
		$title = "";
		$style = "margin-top: 5px;";
		$form_id = "frm_unit";
		$form_name = "frm_unit";
		$onchange = "";
		$no_elements = get_text("No units dispatched!");
		$tabindex = "3";
		break;
	case "reporting_channel":
		$option_0 = get_text("Select");

		$query = "SELECT `r`.`handle` AS `option_text`, " .
			"`r`.`id` AS `option_value`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `units` `r` " .
			"LEFT JOIN `unit_types` `t` ON (`r`.`type` = `t`.`id`) " .
			"LEFT JOIN `allocates` `a` ON `r`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"WHERE (`r`.`remote_data_services` <> '' OR `r`.`unit_phone` <> '' OR `r`.`unit_email` <> '') AND (`a`.`id` IS NOT NULL) " .
			"ORDER BY `option_group` ASC, `option_text` ASC;";

		$form_id = "frm_reporting_channel";
		$form_name = "frm_reporting_channel";
		$class = "form-control";
		$title = get_help_text("_ResRepChan");
		$style = "";
		$onchange = "get_reporting_channel(this.options[this.selectedIndex].value, ''); this.options[0].selected=true;";
		$no_elements = get_text("No units available!");
		$tabindex = "10";
		break;
	case "reporting_channel_smsg_id":
		$option_0 = get_text("Select");

		$query = "SELECT `r`.`handle` AS `option_text`, " .
			"`r`.`id` AS `option_value`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `units` `r` " .
			"LEFT JOIN `unit_types` `t` ON (`r`.`type` = `t`.`id`) " .
			"LEFT JOIN `allocates` `a` ON `r`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"WHERE `r`.`remote_data_services` <> '' AND `a`.`id` IS NOT NULL " .
			"ORDER BY `option_group` ASC, `option_text` ASC;";

		$form_id = "frm_reporting_channel_smsg_id";
		$form_name = "frm_reporting_channel_smsg_id";
		$class = "sit label";
		$title = get_help_text("_ResRepChan");
		$style = "margin-top: 5px;";
		$onchange = "get_reporting_channel(this.options[this.selectedIndex].value, 'smsg_id'); this.options[0].selected=true;";
		$no_elements = get_text("No units available!");
		$tabindex = "5";
		break;
	case "reporting_channel_phone":
		$option_0 = get_text("Select");

		$query = "SELECT `r`.`handle` AS `option_text`, " .
			"`r`.`id` AS `option_value`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `units` `r` " .
			"LEFT JOIN `unit_types` `t` ON (`r`.`type` = `t`.`id`) " .
			"LEFT JOIN `allocates` `a` ON `r`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"WHERE `r`.`unit_phone` <> '' AND `a`.`id` IS NOT NULL " .
			"ORDER BY `option_group` ASC, `option_text` ASC;";

		$form_id = "frm_reporting_channel_phone";
		$form_name = "frm_reporting_channel_phone";
		$class = "sit label";
		$title = get_help_text("_ResRepChan");
		$style = "margin-top: 5px;";
		$onchange = "get_reporting_channel(this.options[this.selectedIndex].value, 'phone'); this.options[0].selected=true;";
		$no_elements = get_text("No units available!");
		$tabindex = "7";
		break;
	case "reporting_channel_email":
		$option_0 = get_text("Select");

		$query = "SELECT `r`.`handle` AS `option_text`, " .
			"`r`.`id` AS `option_value`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `units` `r` " .
			"LEFT JOIN `unit_types` `t` ON (`r`.`type` = `t`.`id`) " .
			"LEFT JOIN `allocates` `a` ON `r`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"WHERE `r`.`unit_email` <> '' AND `a`.`id` IS NOT NULL " .
			"ORDER BY `option_group` ASC, `option_text` ASC;";

		$form_id = "frm_reporting_channel_email";
		$form_name = "frm_reporting_channel_email";
		$class = "sit label";
		$title = get_help_text("_ResRepChan");
		$style = "margin-top: 5px;";
		$onchange = "get_reporting_channel(this.options[this.selectedIndex].value, 'email'); this.options[0].selected=true;";
		$no_elements = get_text("No units available!");
		$tabindex = "9";
		break;
	default:
	}
	return get_select_str($query, $form_id, $form_name, $class, $title, $style, $onchange, $option_0, $unit_id, $no_elements, $tabindex);
}

function get_facility_select_str($select_type = "report_on_scene_location", $facility_id = 0) {
	$option_0 = "";
	$query = "";
	$form_id = "";
	$form_name = "";
	$class = "";
	$title = "";
	$style = "";
	$onchange = "";
	$no_elements = "";
	$tabindex = "";
	switch ($select_type) {
	case "log":
		$option_0 = get_text("Facilities");

		$query = "SELECT DISTINCT `f`.`id` AS `option_value`, " .
			"`f`.`handle` AS `option_text`, " .
			"`f`.`type`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `facilities` `f` " .
//			"LEFT JOIN `allocates` ON (`facilities`.`id` = `allocates`.`resource_id`) " .
			"LEFT JOIN `facility_status` `s` ON (`f`.`facility_status_id` = `s`.`id`) " .
			"LEFT JOIN `facility_types` `t` ON (`f`.`type` = `t`.`id`) " .
			"WHERE `s`.`display` & 32 OR `f`.`id` = " . $facility_id . " " .
			"ORDER BY `f`.`type` ASC, `f`.`handle` ASC;";

		$form_id = "facility_id";
		$form_name = "facility_id";
		$class = "sit label";
		$title = "";
		$style = "width: 40%; float: right; margin-top: 5px;";
		$onchange = "";
		$no_elements = get_text("No facilities available!");
		$tabindex = "4";
		break;
	case "report_on_scene_location":
		$option_0 = get_text("Facilities");

		$query = "SELECT `f`. `id` AS `option_value`, " .
			"`f`.`handle` AS `option_text`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `facilities` `f`" .
			"LEFT JOIN `facility_types` `t` ON (`f`.`type` = `t`.`id`) " .
			"WHERE `f`.`id` IN (SELECT DISTINCT `facility_id` FROM `tickets`) " .
			"OR `f`.`id` IN (SELECT DISTINCT `on_scene_facility_id` FROM `assigns`) " .
			"ORDER BY `option_group` ASC, `option_text` ASC;";

		$form_id = "frm_incident_facility";
		$form_name = "frm_incident_facility";
		$class = "sit label";
		$title = "";
		$style = "margin-top: 5px;";
		$onchange = "query_changed();";
		$no_elements = get_text("No facilities available!");
		$tabindex = "";
		break;
	case "report_receiving_location":
		$option_0 = get_text("Facilities");

		$query = "SELECT `f`. `id` AS `option_value`, " .
			"`f`.`handle` AS `option_text`, " .
			"`t`.`name` AS `option_group` " .
			"FROM `facilities` `f`" .
			"LEFT JOIN `facility_types` `t` ON (`f`.`type` = `t`.`id`) " .
			"WHERE `f`.`id` IN (SELECT DISTINCT `receiving_facility_id` FROM `assigns`) " .
			"ORDER BY `option_group` ASC, `option_text` ASC;";

		$form_id = "frm_receiving_facility";
		$form_name = "frm_receiving_facility";
		$class = "sit label";
		$title = "";
		$style = "margin-top: 5px;";
		$onchange = "query_changed();";
		$no_elements = get_text("No facilities available!");
		$tabindex = "";
		break;
	default:
	}
	return get_select_str($query, $form_id, $form_name ,$class, $title, $style, $onchange, $option_0, $facility_id, $no_elements, $tabindex);
}

function get_textblock_select_str($select_type = "synopsis", $form_name, $form_id, $selected = 0, $show_hide_select = "") {
	$option_0 = get_text("Textblocks");
	$class = "sit label";
	$title = "";
	$style = "";
	$onchange = "";
	$no_elements = get_text("No textblocks available!");
	$tabindex = "";
	switch ($select_type) {
	case "action":
		$tabindex = "2";
		break;
	case "assign":
		$tabindex = "4";
		break;
	case "close":
		$tabindex = "2";
		break;
	case "description":
		$tabindex = "10";
		break;
	case "log":
		$tabindex = "2";
		break;
	case "message":
		$tabindex = "6";
		break;
	case "synopsis":
		$tabindex = "5";
		break;
	default:
	}
	switch ($select_type) {
	case "fixtext":
		$option_0 = get_text("Message fixtexts");
		if ($show_hide_select != "") {
			$style = "display: " . $show_hide_select . ";";
		}

		$query = "SELECT `group` AS `option_group`, " .
			"`id` AS `option_value`, " .
			"`text` AS `option_text` " .
			"FROM `textblocks` " .
			"WHERE `type`  = 'fixtext' " .
			"ORDER BY `sort` ASC;";

		$class = "form-control";
		$title = "";
		$onchange = "";
		break;
	default:
		$option_0 = get_text("Textblocks");

		$query = "SELECT `group` AS `option_group`, " .
			"`text` AS `option_text` " .
			"FROM `textblocks` " .
			"WHERE `type` = '" . $select_type . "' " .
			"ORDER BY `option_group` ASC, `sort` ASC;";

		$class = "sit label";
		$title = "";
		$style = "margin-top: 5px;";
		$onchange = "set_textblock(this.options[this.selectedIndex].text, " . $form_name . "); this.options[0].selected=true;";
	}
	return get_select_str($query, $form_id, $form_name ,$class, $title, $style, $onchange, $option_0, $selected, $no_elements, $tabindex);
}

function get_user_select_str($select_type = "report", $form_name = "frm_user") {
	$option_0 = "";
	$query = "";
	$class = "";
	$title = "";
	$style = "";
	$onchange = "";
	$no_elements = "";
	switch ($select_type) {
	case "report":
		$option_0 = get_text("Select");

		$query = "SELECT `id` AS `option_value`, " .
			"`name` AS `option_text` " .
			"FROM `users` " .
			"WHERE `id` IN (SELECT DISTINCT `call_taker_id` FROM `tickets`) " .
			"OR `id` IN (SELECT DISTINCT `user_id` FROM `tickets`) " .
			"OR `id` IN (SELECT DISTINCT `user_id` FROM `log`);";

		$class = "form-control";
		$title = "";
		$style = "";
		$onchange = "query_changed();";
		$no_elements = "";
		break;
	default:
	}
	return get_select_str($query, $form_name, $form_name ,$class, $title, $style, $onchange, $option_0, 0, $no_elements, "");
}

function get_guard_house_select_str($select_type = "unit", $guard_house_id = 0) {
	$option_0 = "";
	$query = "";
	$class = "";
	$title = "";
	$style = "";
	$onchange = "";
	$no_elements = "";
	$tabindex = "";
	switch ($select_type) {
	case "unit":
		$option_0 = get_text("Select");

		$query = "SELECT `facilities`.`id` AS `option_value`, " .
			"`handle` AS `option_text`, " .
			"`facility_types`.`name` AS `option_group` " .
			"FROM `facilities` " .
			"LEFT JOIN `facility_types` ON (`facilities`.`type` = `facility_types`.`id`);";

		$class = "form-control";
		$title = "";
		$style = "";
		$onchange = "";
		$no_elements = get_text("No facilities available!");
		$tabindex = "14";
		break;
	case "report":
		$option_0 = get_text("Select");

		$query = "SELECT `facilities`.`id` AS `option_value`, " .
			"`handle` AS `option_text`, " .
			"`facility_types`.`name` AS `option_group` " .
			"FROM `facilities` " .
			"LEFT JOIN `facility_types` ON (`facilities`.`type` = `facility_types`.`id`) " .
			"WHERE `facilities`.`id` IN (SELECT DISTINCT `guard_house_id` FROM `units`);";

		$class = "form-control";
		$title = "";
		$style = "";
		$onchange = "query_changed();";
		$no_elements = get_text("No facilities available!");
		$tabindex = "";
		break;
	default:
	}
	return get_select_str($query, "frm_guard_house", "frm_guard_house" ,$class, $title, $style, $onchange, $option_0, $guard_house_id, $no_elements, $tabindex);
}

function get_incident_type_select_str($select_type = "add", $form_name = "frm_in_types_id", $selected_inc_type = 0) {
	$option_0 = "";
	$query = "";
	$class = "";
	$title = "";
	$style = "";
	$onchange = "";
	$no_elements = "";
	$tabindex = "";
	switch ($select_type) {
	case "add":
		$option_0 = get_text("Select");

		$query = "SELECT `id` AS `option_value`, " .
			"`group` AS `option_group`, " .
			"`type` AS `option_text`, " .
			"`protocol` " .
			"FROM `incident_types` " .
			"WHERE (`group` != 'DELETED') " .
			"ORDER BY `group` ASC, `sort` ASC, `type` ASC;";

		$class = "form-control mandatory";
		$title = "";
		$style = "";
		$onchange = "do_severity_protocol(this.options[selectedIndex].value.trim());";
		$no_elements = get_text("No data");	
		$tabindex = "8";
		break;
	case "edit":
		if ($selected_inc_type == 0) {
			$option_0 = get_text("Select");
		}

		$query = "SELECT `id` AS `option_value`, " .
			"`group` AS `option_group`, " .
			"`type` AS `option_text`, " .
			"`protocol` " .
			"FROM `incident_types` " .
			"WHERE (`group` != 'DELETED') " .
			"OR (`id` = " . $selected_inc_type . ") " .
			"ORDER BY `group` ASC, `sort` ASC, `type` ASC;";

		$class = "form-control mandatory";
		$title = "";
		$style = "";
		$onchange = "do_severity_protocol(this.options[selectedIndex].value.trim());";
		$no_elements = get_text("No data");
		$tabindex = "8";
		break;
	case "report":
		$option_0 = get_text("Select");

		$query = "SELECT `id` AS `option_value`, " .
			"`group` AS `option_group`, " .
			"`type` AS `option_text`, " .
			"`protocol` " .
			"FROM `incident_types` " .
			"WHERE `id` IN (SELECT DISTINCT `incident_type_id` FROM `tickets`) " .
			"ORDER BY `group` ASC, `sort` ASC, `type` ASC;";

		$class = "form-control";
		$title = "";
		$style = "";
		$onchange = "query_changed();";
		$no_elements = get_text("No data");	
		break;
	default:
	}
	return get_select_str($query, $form_name, $form_name ,$class, $title, $style, $onchange, $option_0, $selected_inc_type, $no_elements, $tabindex);
}

function get_severity_protocol_array_str() {

	$query = "SELECT `id`, `set_severity`, `protocol` FROM `incident_types`;";

	$result = db_query($query, __FILE__, __LINE__);
	$severities_str = $protocols_str = "\n";
	while ($row = db_fetch_array($result)) {
	$severities_str .= "\t\t    severities[" . $row['id'] . "] = " . $row['set_severity'] . " + 0;\n"; //+0 against syntax error, in case of DB-row==null
	$protocols_str  .= "\t\t    protocols[" . $row['id'] . "] = '" . remove_nls($row['protocol']) . "';\n";
	}
	return $severities_str . $protocols_str;
}

function get_priority_select_str($select_type = "add", $form_name = "frm_severity", $selected_severity = 0) {
	$return_str = "";
	$where_str = "";
	$onchange_str = "";
	$selected_normal_str = "";
	$selected_medium_str = "";
	$selected_high_str = "";
	$bgcolor = "#0000FF";
	switch ($select_type) {
	case "add":
		$selected_normal_str = " selected";
		$bgcolor = "#0000FF";
		$onchange_str = "do_set_severity(this.selectedIndex); do_inc_name(this.options[selectedIndex].value.trim());";
		break;
	case "edit":
		switch ($selected_severity) {
		case $GLOBALS['SEVERITY_NORMAL']:
			$selected_normal_str = " selected";
			$bgcolor = "#0000FF";
			break;
		case $GLOBALS['SEVERITY_MEDIUM']:
			$selected_medium_str = " selected";
			$bgcolor = "#008000";
			break;
		case $GLOBALS['SEVERITY_HIGH']:
			$selected_high_str = " selected";
			$bgcolor = "#FF0000";
			break;
		default:
		}
		$onchange_str = "do_protocol(this.options[selectedIndex].value.trim());";
		break;
	default:
	}
	$return_str .= "<select" . get_help_text_str("_prio") . " id=\"" . $form_name . "\" name=\"" . $form_name . "\" style=\"color: #FFFFFF; background-color: " .
		$bgcolor . ";\" class=\"form-control\" ";
	$return_str .= "onchange=\"this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor;\">";
	$return_str .= "<option style=\"color: #FFFFFF; background-color: #0000FF;\" value=\"" .
		$GLOBALS['SEVERITY_NORMAL'] . "\"" . $selected_normal_str . ">" . get_text("Normal") . "</option>";
	$return_str .= "<option style=\"color: #FFFFFF; background-color: #008000;\" value=\"" .
		$GLOBALS['SEVERITY_MEDIUM'] . "\"" . $selected_medium_str . ">" . get_text("Medium") . "</option>";
	$return_str .= "<option style=\"color: #FFFFFF; background-color: #FF0000;\" value=\"" .
		$GLOBALS['SEVERITY_HIGH'] . "\"" . $selected_high_str . ">" . get_text("High") . "</option>";
	$return_str .= "</select>";
	return $return_str;
}

function get_reported_by_select_str($function) {
	$return_array = array ();
	$return_array["reported_by_select"] = "";
	$return_array["reported_by_phone"] = "\n";
	$reported_by_phone_settings = explode(",", get_variable("reported_by_phone"));
	$onchange_str = "set_reported_by_infos('" . trim($function) . "', this.options[this.selectedIndex].text, reported_by_phone[this.options[this.selectedIndex].value]); this.options[0].selected=true;";
	$tabindex_str = " tabindex=7";
	$where2 = get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_FACILITY'], "WHERE");

	$query_facs = "SELECT `handle`, " .
		"`facilities`.`direct_dialing_1` AS `reported_by_phone_0`, " .
		"`facilities`.`direct_dialing_2` AS `reported_by_phone_1`, " .
		"`facilities`.`security_phone` AS `reported_by_phone_2`, " .
		"`facilities`.`contact_phone` AS `reported_by_phone_3`, " .
		"`facility_types`.`name` AS `facility_type` " .
		"FROM `facilities` " .
		"LEFT JOIN `allocates` ON (`facilities`.`id` = `allocates`.`resource_id`) " .
		"LEFT JOIN `facility_status` ON (`facilities`.`facility_status_id` = `facility_status`.`id`) " .
		"LEFT JOIN `facility_types` ON (`facilities`.`type` = `facility_types`.`id`) " .
		$where2 . " AND `facility_status`.`display` & 2 " .
		"ORDER BY `facilities`.`type` ASC, `handle` ASC;";

	$result_facs = db_query($query_facs, __FILE__, __LINE__);

	$query_units = "SELECT `r`.`handle`, " .
		"`r`.`unit_phone` AS `reported_by_phone_4`, " .
		"`t`.`name` AS `unit_type` " .
		"FROM `units` `r` " .
		"LEFT JOIN `allocates` `a` ON (`r`.`id` = `a`.`resource_id`) " .
		"LEFT JOIN `unit_types` `t` ON (`r`.`type` = `t`.`id`) " .
		"LEFT JOIN `unit_status` `s` ON (`r`.`unit_status_id` = `s`.`id`) " .
		"WHERE `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
		"AND (`s`.`dispatch` < 3 OR `r`.`unit_status_id` = 0 " .
		"OR (SELECT COUNT(*) as `numfound` FROM `assigns` " .
			"WHERE `assigns`.`unit_id` = `r`.`id` " .
			"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00')) > 0) " .
		"ORDER BY `unit_type` ASC, `handle` ASC;";

	$result_units = db_query($query_units, __FILE__, __LINE__);

	$return_array["reported_by_select"] .= "<select style=\"margin-top: 5px;\" class=\"sit label\" name=\"reported_by_select\" onchange=\"" . $onchange_str . "\"" . $tabindex_str . ">";
	$return_array["reported_by_select"] .= "<option value=0 selected>" . get_text("Textblocks") . "</option>";
	$option_value = 1;
	if (db_num_rows($result_facs) > 0) {
		$option_group = strval(rand());
		$i = 0;
		while ($row_facility = db_fetch_array($result_facs)) {
			if ($option_group != remove_nls($row_facility['facility_type'])) {
				if ($i != 0) {
					$return_array["reported_by_select"] .= "</optgroup>\n";
				}
				$option_group = remove_nls($row_facility['facility_type']);
				$group_caption = get_text("No group name");
				if (remove_nls(trim($row_facility['facility_type'])) != "") {
					$group_caption = remove_nls($row_facility['facility_type']);
				}
				$return_array["reported_by_select"] .= "<optgroup label='" . $group_caption . "'>\n";
			}
			$return_array["reported_by_select"] .= "<option value=\"" . $option_value . "\">" . remove_nls($row_facility['handle']) . "</option>\n";
			$reported_by_phone = "";
			if ((trim($reported_by_phone_settings[0]) == 1) && (trim($row_facility['reported_by_phone_0']) != "")) {
				$reported_by_phone .= $row_facility['reported_by_phone_0'] . ", ";
			}
			if ((trim($reported_by_phone_settings[1]) == 1) && (trim($row_facility['reported_by_phone_1']) != "")) {
				$reported_by_phone .= $row_facility['reported_by_phone_1'] . ", ";
			}
			if ((trim($reported_by_phone_settings[2]) == 1) && (trim($row_facility['reported_by_phone_2']) != "")) {
				$reported_by_phone .= $row_facility['reported_by_phone_2'] . ", ";
			}
			if ((trim($reported_by_phone_settings[3]) == 1) && (trim($row_facility['reported_by_phone_3']) != "")) {
				$reported_by_phone .= $row_facility['reported_by_phone_3'] . ", ";
			}
			if (trim($reported_by_phone) != "") {
				$reported_by_phone = remove_nls(substr($reported_by_phone, 0, -2));
			}
			if (trim($reported_by_phone) != "") {
				$return_array["reported_by_phone"] .= "\t\t    reported_by_phone[" . $option_value . "] = '" . $reported_by_phone . "'\n";
			} else {
				$return_array["reported_by_phone"] .= "\t\t    reported_by_phone[" . $option_value . "] = ''\n";
			}
			$option_value++;
			$i++;
		}
		$return_array["reported_by_select"] .= "\n</optgroup>\n";
	} else {
		$return_array["reported_by_select"] .= "\t<option disabled>" . get_text("No facilities available!") . "</option>\n";
	}
	if (db_num_rows($result_units) > 0) {
		$option_group = strval(rand());
		$i = 0;
		while ($row_unit = db_fetch_array($result_units)) {
			if ($option_group != remove_nls($row_unit['unit_type'])) {
				if ($i != 0) {	
					$return_array["reported_by_select"] .= "</optgroup>\n";
				}
				$option_group = remove_nls(trim($row_unit['unit_type']));
				$group_caption = get_text("No group name");
				if (remove_nls(trim($row_unit['unit_type'])) != "") {
					$group_caption = remove_nls($row_unit['unit_type']);
				}
				$return_array["reported_by_select"] .= "<optgroup label='" . $group_caption . "'>\n";
			}
			$return_array["reported_by_select"] .= "<option value=\"" . $option_value . "\">" . remove_nls($row_unit['handle']) . "</option>\n";
			if (trim($reported_by_phone_settings[4]) == 1) {
				$return_array["reported_by_phone"] .= "\t\t    reported_by_phone[" . $option_value . "] = '" . remove_nls($row_unit['reported_by_phone_4']) . "'\n";
			} else {
				$return_array["reported_by_phone"] .= "\t\t    reported_by_phone[" . $option_value . "] = ''\n";
			}
			$option_value++;
			$i++;
		}
		$return_array["reported_by_select"] .= "\n</optgroup>\n";
	} else {
		$return_array["reported_by_select"] .= "\t<option disabled>" . get_text("No units in service!") . "</option>\n";
	}
	return $return_array;
}

function get_ticket_status_select_str($select_type = "report", $form_id = "frm_status", $form_name = "", $selected_status = 0) {
	$return_str = "";
	$option_0_str = "";
	$onchange_str = "";
	$form_id_str = "";
 	if ($form_id != "") {
 		$form_id_str = " id=\"" . $form_id . "\"";
 	}
	$form_name_str = "";
	if ($form_name != "") {
		$form_name_str = " name=\"" . $$form_name . "\"";
 	}
	$used_status = array ();
	$disabled_str = "";
	switch ($select_type) {
	case "edit":
		$onchange_str = " onchange=\"do_status_change();";
		$disabled_str = " disabled";
		break;
	case "report":
		$option_0_str = "<option value=0 selected>" . get_text("Select") . "</option>";

		$query = "SELECT DISTINCT `status` " .
			"FROM `tickets`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$used_status[] = $row['status'];
		}
		$onchange_str = " onchange=\"query_changed();";
		break;
	default:
	}
	$select_open_str = "";
	$select_closed_str = "";
	$select_scheduled_str = "";
	switch ($selected_status) {
	case $GLOBALS['STATUS_OPEN']:
		$select_open_str = " selected";
		break;
	case $GLOBALS['STATUS_CLOSED']:
		$select_closed_str = " selected";
		break;
	case $GLOBALS['STATUS_SCHEDULED']:
		$select_scheduled_str = " selected";
		break;
	default:
	}
	$return_str .= "<select" . $form_name_str . $form_id_str . " class=\"form-control\"" . $onchange_str . "\"" . $disabled_str . ">";
	$return_str .= $option_0_str;
	if ((in_array($GLOBALS['STATUS_OPEN'], $used_status)) || ($select_type != "report")) {
		$return_str .= "<option value=\"" . $GLOBALS['STATUS_OPEN'] . "\"" . $select_open_str . ">" . get_text("Open") . "</option>";
	}
	if ((in_array($GLOBALS['STATUS_CLOSED'], $used_status)) || ($select_type != "report")) {
		$return_str .= "<option value=\"" . $GLOBALS['STATUS_CLOSED'] . "\"" . $select_closed_str . ">" . get_text("Closed") . "</option>";
	}
	if ((in_array($GLOBALS['STATUS_SCHEDULED'] ,$used_status)) || ($select_type != "report")) {
		$return_str .= "<option value=\"" . $GLOBALS['STATUS_SCHEDULED'] . "\"" . $select_scheduled_str . ">" . get_text("Scheduled") . "</option>";
	}
	$return_str .= "</select>";
	return $return_str;
}

$status_settings_array = array ();
function get_status_settings($select_type, $status_id) {
	global $status_settings_array;
	if (empty ($status_settings_array)) {

		$result = db_query("SELECT * FROM `unit_status` ORDER BY `sort` ASC, `status_name` ASC", __FILE__, __LINE__);

		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$status_settings_array[$GLOBALS['TYPE_UNIT']][$row['id']] = array (
				"bg_color"		=> $row['bg_color'],
				"text_color"	=> $row['text_color'],
				"status_name"	=> $row['status_name']
			);
		}

		$result = db_query("SELECT * FROM `facility_status` ORDER BY `sort` ASC, `status_name` ASC", __FILE__, __LINE__);

		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$status_settings_array[$GLOBALS['TYPE_FACILITY']][$row['id']] = array (
				"bg_color"		=> $row['bg_color'],
				"text_color"	=> $row['text_color'],
				"status_name"	=> $row['status_name']
			);
		}
	}
	if ($status_id == 0) {
		return $status_settings_array[$select_type];
	} else {
		return $status_settings_array[$select_type][$status_id];
	}
}

function get_status_select_str($select_type, $unit_facility, $status_id, $back) {
	$type_key = "unit_id";
	$name_value = "unit_status";
	if ($select_type == $GLOBALS['TYPE_FACILITY']) {
		$type_key = "facility_id";
		$name_value = "facility_status";
	}
	$click_str = "";
	if (is_super() || is_admin() || is_operator()) {
		$back_value = "situation";
		switch ($back) {
		case "units":
			$back_value = "units";
			break;
		case "facilities":
			$back_value = "facilities";
			break;
		default:
		}
		$click_str = " onClick='window.location.href=\"log_report.php?back=" . $back_value . "&" . $type_key . "=" . $unit_facility ."\"'";
	}
	$status_settings = get_status_settings($select_type, $status_id);
	return "<div name='" . $name_value . "' class='label status col-md-12' style='height: auto; text-align: left; " .
		"background-color:" . $status_settings['bg_color'] . "; color:" . $status_settings['text_color'] . ";' " . $click_str .
		"data-" . $type_key . "=" . $unit_facility . ">" . remove_nls($status_settings["status_name"]) . "</div>";
}

function show_unit_facility_status_select($unit_facility) {
	if (is_super() || is_admin() || is_operator()) {
		$key_str = "unit";
		if ($unit_facility == $GLOBALS['TYPE_FACILITY']) {
			$key_str = "facility";
		}
		$status_menue_str = "";
		$status_settings = get_status_settings($unit_facility, 0);
		$i = 0;
		foreach ($status_settings as $key => $value) {
			$status_menue_str .= "\t\t\t<tr class=\"status_table\"><td class=\"status_table\" id=\"set_" . $key_str . "_status_" . $key . "\" onclick=\"" . $key_str . "_status_select($('#" . $key_str . "_id').html(), " .
			$key . ");\" style=\"background-color:" . $value['bg_color'] . "; color:" . $value['text_color'] . ";\">" . remove_nls($value["status_name"]) . "</td></tr>";
			$i++;
		}
	?>
<div id="<?php print $key_str;?>_status_menue" name="<?php print $key_str;?>_status_menue" class="panel panel-default status_table" style="padding: 0px; position: fixed; display: none; z-index: 2000;">
	<div id="<?php print $key_str;?>_status_items" style="display:none;"><?php print $i;?></div>
	<div id="<?php print $key_str;?>_id" style="display:none;"></div>
	<table id="<?php print $key_str;?>_status_table" class="table table-striped table-condensed status_table" style="width: 120px; background-color: #FFFFFF; font-weight: bold;">
		<?php print $status_menue_str;?>
	</table>
</div>
	<?php
	}
}

function get_incident_location_select_str($function, $facility_id) {
	$return_array = array ();
	$return_array["select_str"] = "";
	$return_array["facility_address"] = "\n";
	$return_array["facility_coordinates"] = "\n";
	$tabindex_str = "";
	switch ($function) {
	case "add":
		$onchange_str = " onchange=\"do_facility_to_ticket_location(this.options[selectedIndex].value.trim());\"";
		$tabindex_str = " tabindex=2";
		break;
	case "edit":
		$onchange_str = " onchange=\"document.edit.frm_facility_changed.value = parseInt(document.edit.frm_facility_changed.value) + 1; do_facility_to_ticket_location(this.options[selectedIndex].value.trim());\"";
		$tabindex_str = " tabindex=2";
		break;
	default:
	}
	if (($facility_id != null) || ($function == "add")) {

		$query_facilities = "SELECT DISTINCT " .
			"`f`.`id` AS `fac_id`, " .
			"`f`.`handle` AS `handle`, " .
			"`f`.`street` AS `street`, " .
			"`f`.`city` AS `city`, " .
			"`f`.`lat` AS `lat`, " .
			"`f`.`lng` AS `lng`, " .
			"`f`.`type`, " .
			"`facility_types`.`name` AS `facility_type` " .
			"FROM `facilities` `f` " .
			"LEFT JOIN `allocates` ON (`f`.`id` = `allocates`.`resource_id`) " .
			"LEFT JOIN `facility_status` ON (`f`.`facility_status_id` = `facility_status`.`id`) " .
			"LEFT JOIN `facility_types` ON (`f`.`type` = `facility_types`.`id`) " .
			"WHERE ((`allocates`.`type` = " . $GLOBALS['TYPE_FACILITY'] . ") " .
			"AND (`facility_status`.`display` & 1)) " .
			"OR (`f`.`id` = " . $facility_id . ") " .
			"ORDER BY `f`.`type` ASC, `handle` ASC;";

		$result_facilities = db_query($query_facilities, __FILE__, __LINE__);
		$return_array["select_str"] .= "<select" . get_help_text_str("_facy") . " name=\"frm_facility_id\" id=\"frm_facility_id\" class=\"sit label\" style=\"margin-top: 5px;\"" . $onchange_str . $tabindex_str . ">";
		if (db_num_rows($result_facilities) > 0) {
			$option_group = strval(rand());
			$i = 0;
			$return_array["select_str"] .= "<option value=0>" . get_text("Free input") . "</option>";
			$return_array["facility_coordinates"] .= "\t\t    fac_lat[" . 0 . "] = " . get_variable('_def_lat') . " + 0;\n"; //+0 against syntax error, in case of DB-row==null
			$return_array["facility_coordinates"] .= "\t\t    fac_lng[" . 0 . "] = " . get_variable('_def_lng') . " + 0;\n"; //+0 against syntax error, in case of DB-row==null
			while ($row_facility = db_fetch_array($result_facilities)) {
				if ($option_group != $row_facility['facility_type']) {
					if ($i != 0) {
						$return_array["select_str"] .= "</optgroup>\n";
					}
					$option_group = $row_facility['facility_type'];
					$group_caption = get_text("No group name");
					if (remove_nls(trim($row_facility['facility_type'])) != "") {
						$group_caption = remove_nls($row_facility['facility_type']);
					}
					$return_array["select_str"] .= "<optgroup label='" . $group_caption . "'>\n";
				}
				$selected_str = "";
				if (($facility_id == $row_facility['fac_id']) && ($function == "edit")) {
					$selected_str = " SELECTED ";
				}
				$return_array["select_str"] .= "<option value=" . $row_facility['fac_id'] . " " . $selected_str . ">" . remove_nls($row_facility['handle']) . "</option>";			
				$return_array["facility_address"] .= "\t\t    facility_adress[" . $row_facility['fac_id'] . "] = '" . remove_nls($row_facility['street'] . ", " . $row_facility['city']) . "';\n";
				$return_array["facility_coordinates"] .= "\t\t    fac_lat[" . $row_facility['fac_id'] . "] = " . remove_nls($row_facility['lat']) . " + 0;\n"; //+0 against syntax error, in case of DB-row==null
				$return_array["facility_coordinates"] .= "\t\t    fac_lng[" . $row_facility['fac_id'] . "] = " . remove_nls($row_facility['lng']) . " + 0;\n"; //+0 against syntax error, in case of DB-row==null
				$i++;
			}
			$return_array["select_str"] .= "\n</optgroup>\n";
			unset ($result_facilities);
		} else {
			$return_array["select_str"] .= "\t<option value=0 readonly>" . get_text("No facilities available!") . "</option>\n";
		}
	}
	$return_array["select_str"] .= "\t</select>\n";
	return $return_array;
}

function get_admin_permission_select_str($function, $selected = 0) {
	$tabindex = "";
	switch ($function) {
	case "unit":
		$tabindex = "18";
		break;
	case "facility":
		$tabindex = "20";
		break;
	default:
	}
	if ($tabindex != "") {
		$tabindex = " tabindex=" . $tabindex . " ";
	}
	$select_str_0 = $select_str_1 = "";
	switch ($selected) {
	case 0:
		$select_str_0 = " selected";
		break;
	case 1:
		$select_str_1 = " selected";
		break;
	default:
	}
	$disabled_str =" disabled";
	if (is_super()) {
	$disabled_str ="";
	}
	?>
<select class="form-control"<?php print $tabindex;?> name="frm_adminperms"<?php print $disabled_str;?>>
	<option value=0<?php print $select_str_0;?>><?php print get_text("Admin and superadmin");?></option>
	<option value=1<?php print $select_str_1;?>><?php print get_text("Superadmin only");?></option>
</select>
	<?php
}
//====== configuration

$variables = array ();
function get_variable($which) {
	global $variables;
	if (empty ($variables)) {

		$result = db_query("SELECT * " .
			"FROM `settings`;", __FILE__, __LINE__);

		while ($row = stripslashes_deep(db_fetch_assoc($result))){
			$name = $row['name'];
			$value = $row['value'];
			$variables[$name] = trim($value);
		}
	}
	$value = false;
	switch ($which) {
	case "auto_dispatch":
		$value = "1, 1, 1";
		if (preg_match("/^\s?[01]{1}\s?,\s?[0-2]{1}\s?,\s?[0-1]{1}\s?$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "auto_poll":
		$value = "10";
		$values = explode(",", $variables[$which]);
		if (preg_match("/^\s?[0-9]{1,3}\s?$/", $variables[$which]) &&
			$values[0] > 1 && $values[0] < 99
		) {
			$value = $variables[$which];
		}
		break;
	case "callboard":
		$value = "1, 80, 35, 80, 300";
		if (preg_match("/^[0-2]{1}\s?,\s?[0-9]{2,3}\s?,\s?[0-9]{2,3}\s?,\s?[0-9]{2,3}\s?,\s?[0-9]{2,3}\s?$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "closed_interval":
		$value = "0, 1440";
		if (preg_match("/^\s?[0-9]{1,4}\s?,\s?[0-9]{1,4}\s?$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "date_format":
		$value = "Y-m-d H:i:s";
		if (preg_match("/^((\\[a-zA-Z]\s?)|[:_\-,;.\/\|\s]|[dDjlNSwzWFmMntLoYyaABgGhHisuIOPTZcrU]){1,40}$/",  $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "date_format_time_only":
		$value = "H:i:s";
		if (preg_match("/^((\\[a-zA-Z]\s?)|[:_\-,;.\/\|\s]|[dDjlNSwzWFmMntLoYyaABgGhHisuIOPTZcrU]){1,40}$/",  $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "date_format_time_only_clock":
		$value = "H:i";
		if (preg_match("/^((\\[a-zA-Z]\s?)|[:_\-,;.\/\|\s]|[dDjlNSwzWFmMntLoYyaABgGhHisuIOPTZcrU]){1,40}$/",  $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "date_format_date_only":
		$value = "Y-m-d";
		if (preg_match("/^((\\[a-zA-Z]\s?)|[:_\-,;.\/\|\s]|[dDjlNSwzWFmMntLoYyaABgGhHisuIOPTZcrU]){1,40}$/",  $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "date_format_year_only":
		$value = "Y";
		if (preg_match("/^((\\[a-zA-Z]\s?)|[:_\-,;.\/\|\s]|[dDjlNSwzWFmMntLoYyaABgGhHisuIOPTZcrU]){1,40}$/",  $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "framesize":
		$value = 115;
		if (preg_match("/^[0-9]{1,3}$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "heading_blink":
		$value = 10;
		if (preg_match("/^[0-9]{1,2}$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "hide_booked":
		$value = 30;
		if (preg_match("/^[0-9]{1,4}$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "page_caption":
		$value = "String too long or wrong characters used!";
		if (preg_match("/^[0-9a-zA-Z\\-\\_\\ " . $variables["_vowel_mutation"] . "]{1,40}$/", $variables[$which])) {
			$value = remove_nls($variables[$which]);
		}
		break;
	case "parking_form_data":
		$value = "10, 90, 10, 90, 10, 90, 10, 90";
		if (preg_match("/^\s?[0-9]{1,2}\s?,\s?[0-9]{1,3}\s?,\s?[0-9]{1,2}\s?,\s?[0-9]{1,3}\s?,\s?[0-9]{1,2}\s?,\s?[0-9]{1,3}\s?,\s?[0-9]{1,2}\s?,\s?[0-9]{1,3}\s?$/", $variables[$which])) {
			$value = remove_nls($variables[$which]);
		}
		break;
	case "reported_by_phone":
		$value = "0, 0, 1, 0, 1";
		if (preg_match("/^\s?[01]{1}\s?,\s?[01]{1}\s?,\s?[01]{1}\s?,\s?[01]{1}\s?,\s?[01]{1}\s?$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "report_last":
		$value = "720, 0, 1, 1";
		if (preg_match("/^\s?[0-9]{1,5}\s?,\s?[0-1]{1}\s?,\s?[0-1]{1}\s?,\s?[0-1]{1}\s?$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "report_log":
		$value = "720, 720, 720, 0, 1, 1";
		if (preg_match("/^\s?[0-9]{1,5}\s?,\s?[0-9]{1,5}\s?,\s?[0-9]{1,5}\s?,\s?[0-1]{1}\s?,\s?[0-1]{1}\s?,\s?[0-1]{1}\s?$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "session_time_limit":
		$value = "30, 5";
		$values = explode(",", $variables[$which]);
		if (preg_match("/^\s?[0-9]{1,2}\s?,\s?[0-9]{1,2}\s?$/", $variables[$which])) {
			if ($values[0] != 0) {
				if (($values[0] > $values[1]) && ($values[0] <= 99)) {
					$value = $variables[$which];
				}
			} else {
				$value = "0, 0";
			}
		}
		break;
	case "sort_units":
		$value = 2;
		if (preg_match("/^\s?[1-4]{1}\s?$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "sort_facilities":
		$value = 1;
		if (preg_match("/^\s?[1-4]{1}\s?$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "title_string":
		$value = "String too long or wrong characters used!";
		if (preg_match("/^[0-9a-zA-Z\\-\\_\\ " . $variables["_vowel_mutation"] . "]{1,40}$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "tolerance":
		$value = 90;
		if (preg_match("/^[0-9]{1,4}$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
	case "night_color":
		$value = "#C0C0C0";
		if (preg_match("/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $variables[$which])) {
			$value = $variables[$which];
		}
		break;
//	case "_api_hosts":		
	case "_api_destination_host":
		if (trim($variables[$which]) == "") {
			$value = "";
		} else {
			$value = "localhost:3142";
			if (preg_match("/^(http[s]*\\:\\/\\/)*([0-9a-zA-Z\\-\\_\\ ]{1,40})|([0-9\\.]{7,16})|([\\[0-9a-fA-F\\:\]]{3,39})(\\:[0-9]{1,4})*(\\/{1}[a-zA-Z\\-\\_\\.\\/])*$/", $variables[$which])) {
				$value = $variables[$which];
			}
		}
		break;
	case "_api_phone_host":
		if (trim($variables[$which]) == "") {
			$value = "";
		} else {
			$value = "localhost:3142";
			if (preg_match("/^(http[s]*\\:\\/\\/)*([0-9a-zA-Z\\-\\_\\ ]{1,40})|([0-9\\.]{7,16})|([\\[0-9a-fA-F\\:\]]{3,39})(\\:[0-9]{1,4})*(\\/{1}[a-zA-Z\\-\\_\\.\\/])*$/", $variables[$which])) {
				$value = $variables[$which];
			}
		}
		break;
	case "_api_destination_password":
		$value = "743894a0e4a801fc3";
//		if (preg_match("/^[0-9a-zA-Z\\-\\_\\ ]{1,40}$/", $variables[$which])) {
		if (true) {
			$value = $variables[$which];
		}
		break;

	case "_api_log_max_display_setng":
		$value = "30";
		if (($variables[$which] >= 1) && ($variables[$which] <= 10080)) {
			$value = $variables[$which];
		}
		break;
	case "_api_log_max_age_setng":
		$value = "1440";
		if ((($variables[$which] >= 60) && ($variables[$which] <= 10080)) || ($variables[$which] == 0)) {
			$value = $variables[$which];
		}
		break;
	case "_locale":
		$value = "de-DE";
		if ($variables[$which] != "") {
			$value = $variables[$which];
		}
		break;
	default:
		if (array_key_exists($which, $variables)) {
			$value = $variables[$which];
		}
	}
	return $value;
}

function get_sound_array() {
	$sound_array = array ();

	$query = "SELECT `name` " .
		"FROM `settings` " .
		"WHERE `name` LIKE '_audio_%' " .
		"ORDER BY `id` ASC;";

	$result = db_query($query, __FILE__, __LINE__);
	$i = 0;
	while ($row = stripslashes_deep(db_fetch_array($result))) {
		$sound_array[$i] = substr($row['name'], 1);
		$i++;
	}
	return $sound_array;
}

function split_api_receiver_str($receiver_str) {
	$result_array = array ("", "");
	if (($receiver_str != null) && ($receiver_str != "")) {
		$receiver_array = explode(":", $receiver_str);
		$prefix_length = strlen($receiver_array[0]);
		$result_array[0] = $receiver_array[0];
		$result_array[1] = substr($receiver_str, $prefix_length + 1);
	}
	return $result_array;
}

function get_fixtext() {
	$options = array ();

	$query = "SELECT * FROM `textblocks` " .
		"WHERE `type` = 'fixtext'" .
		"ORDER BY `sort` ASC;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_affected_rows($result) > 0) {
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$options["FIXTEXT_" . $row['id']]["code"] = remove_nls($row['code']);
			$options["FIXTEXT_" . $row['id']]["Text"] = remove_nls($row['text']);
			$options["FIXTEXT_" . $row['id']]["report_channels"] = remove_nls($row['report_channels']);
		}
	}	
	return $options;
}

function get_fixtext_option_str() {
	$options = array ();
	$options = get_fixtext();
	$fixtext_option_str = "";
	foreach ($options as $key => $value) {
		$fixtext_option_str .= "<option value=\"" . $key . "\">" . $value["Text"] . "</option>\r\n";
	}
	return $fixtext_option_str;
}

function get_fixtext_report_channels_str() {
	$report_channels = array ();
	$report_channels = get_fixtext();
	$fixtext_report_channels_str = "var fixtext_report_channels = Array ();\r\n";
	foreach ($report_channels as $key => $value) {
		$fixtext_report_channels_str .= "fixtext_report_channels[\"" . $key . "\"] = " . $value["report_channels"] . " + 0;\r\n";
	}
	return $fixtext_report_channels_str;
}

function get_api_configuration($message_group = "") {
	$match_array = array ();
	switch ($message_group) {
	case "facility_all":
	case "facility":
	case "user_all":
	case "user":
		$match_array["EMAIL"]["REGEXP"] = "^EMAIL:" . get_regexp_email() . "$";
		$match_array["EMAIL"]["CAPTION"] = get_text("Email");
		$match_array["EMAIL"]["SELECT_FIXTEXT"] = false;
		$match_array["EMAIL"]["SELECT_MESSAGE"] = true;
		break;
	default:
		$match_array["EMAIL"]["REGEXP"] = "^EMAIL:" . get_regexp_email() . "$";
		$match_array["EMAIL"]["CAPTION"] = get_text("Email");
		$match_array["EMAIL"]["SELECT_FIXTEXT"] = false;
		$match_array["EMAIL"]["SELECT_MESSAGE"] = true;

		$match_array[get_variable("_api_prefix_phone_encdg")]["REGEXP"] = "^PHONE:" . get_regexp_phone() . "$";
		$match_array[get_variable("_api_prefix_phone_encdg")]["CAPTION"] = get_variable("_api_prefix_phone_capt");
		$match_array[get_variable("_api_prefix_phone_encdg")]["SELECT_FIXTEXT"] = true;
		$match_array[get_variable("_api_prefix_phone_encdg")]["SELECT_MESSAGE"] = true;
		
		$match_array[get_variable("_api_prefix_printer_encdg")]["REGEXP"] = "^PRINTER:ipp:\/\/.*$";
		$match_array[get_variable("_api_prefix_printer_encdg")]["CAPTION"] = get_variable("_api_prefix_printer_capt");
		$match_array[get_variable("_api_prefix_printer_encdg")]["SELECT_FIXTEXT"] = false;
		$match_array[get_variable("_api_prefix_printer_encdg")]["SELECT_MESSAGE"] = true;
		for ($i = 1; $i < 5; $i++) {
			if (
				(get_variable("_api_prefix_reporting_channel_" . $i . "_regexp") != "") &&
				(get_variable("_api_prefix_reporting_channel_" . $i . "_encdg") != "") &&
				(get_variable("_api_prefix_reporting_channel_" . $i . "_capt") != "")
			) {
				$match_array[get_variable("_api_prefix_reporting_channel_" . $i . "_encdg")]["REGEXP"] = "^" .
					get_variable("_api_prefix_reporting_channel_" . $i . "_encdg") . ":" .
					get_variable("_api_prefix_reporting_channel_" . $i . "_regexp") . "$";
				$match_array[get_variable("_api_prefix_reporting_channel_" . $i . "_encdg")]["CAPTION"] = get_variable("_api_prefix_reporting_channel_" . $i . "_capt");
				$match_array[get_variable("_api_prefix_reporting_channel_" . $i . "_encdg")]["SELECT_FIXTEXT"] = true;
				$match_array[get_variable("_api_prefix_reporting_channel_" . $i . "_encdg")]["SELECT_MESSAGE"] = true;
			}
		}
	}
	return $match_array;
}

function get_connection_test_configuration() {
	//Defaults: 60, 300, CONNECTION_TEST, success, warning, error, phone, HTTP/1.1 200 OK
	$variable_array = array ();
	$variable_array = explode(",", get_variable("_api_connection_test_configuration"));
	$connection_test_array = array();
	$connection_test_array['retry_time'] = 60;
	if (isset ($variable_array[0]) && ((round($variable_array[0]) >= 10) || (round($variable_array[0]) <= 600))) {
		$connection_test_array['retry_time'] = $variable_array[0];
	}
	$connection_test_array['keepalive_time'] = 300;
	if (isset ($variable_array[1]) && ((round($variable_array[1]) >= 10) || (round($variable_array[1])) <= 600)) {
		$connection_test_array['keepalive_time'] = $variable_array[1];
	}
	$connection_test_array['code'] = "CONNECTION_TEST";
	if (isset ($variable_array[2]) && (($variable_array[2] == "") || (strlen($variable_array[2]) > 20))) {
		$connection_test_array['code'] = $variable_array[2];
	}
	$connection_test_array['source_success_code'] = "success";
	if (isset ($variable_array[3]) && (($variable_array[3] == "") || (strlen($variable_array[3]) > 20))) {
		$connection_test_array['source_success_code'] = $variable_array[3];
	}
	$connection_test_array['source_warning_code'] = "warning";
	if (isset ($variable_array[4]) && (($variable_array[4] == "") || (strlen($variable_array[4]) > 20))) {
		$connection_test_array['source_warning_code'] = $variable_array[4];
	}
	$connection_test_array['source_error_code'] = "error";
	if (isset ($variable_array[5]) && (($variable_array[5] == "") || (strlen($variable_array[5]) > 20))) {
		$connection_test_array['source_error_code'] = $variable_array[5];
	}
	$connection_test_array['destination_phone_code'] = "phone";
	if (isset ($variable_array[6]) && (($variable_array[6] == "") || (strlen($variable_array[6]) > 20))) {
		$connection_test_array['destination_phone_code'] = $variable_array[6];
	}
	$connection_test_array['response_code'] = "HTTP/1.1 200 OK";
	if (isset ($variable_array[7]) && (($variable_array[7] == "") || (strlen($variable_array[7]) > 20))) {
		$connection_test_array['response_code'] = $variable_array[7];
	}
	return $connection_test_array;
}

function get_is_auto_ticket_line($line) {
	$setting = get_auto_ticket_configuration("pattern");
	$return_array = array ("MATCH" => false, "TYPE_NO" => 0);
	foreach ($setting as $setting_key => $setting_value) {
			if (preg_match("/^" . $setting_value["REGEXP"] . "(nullnull)?$/", trim($line))) {   //(nullnull)? in Regex is patch for deviceLat/Lon-Bug in OTDConnetor up to Version 18.9.1.2
			$return_array = array ("MATCH" => true, "TYPE_NO" => $setting_key);
			break;
		}
	}
	return $return_array;
}

function get_auto_ticket_configuration($function = "pattern") {
/*
	Provide profiles for different command and control centers
	Park settings in global variable (only read everything once)
	Set profile for pattern based on the settings
	Possibly Set profile based on the transmitted interface
	Distribute data to text fields in ticket_add.php from line 86 
*/
	$auto_ticket_configuration = array ();
	switch ($function) {
	case "settings":
		$auto_ticket_configuration["profile_name"] = "ACME-town";
		$auto_ticket_configuration["contact"] = "Control center ACME-town";
		$auto_ticket_configuration["phone"] = "+00 12345 67890";
		$auto_ticket_configuration["severity"] = $GLOBALS['SEVERITY_MEDIUM'];
			break;
	default:
		$auto_ticket_configuration[0]["CAPTION"] = "ACME-town_0";
		$auto_ticket_configuration[0]["REGEXP"] = "[0-3][0-9]\-[0-1][0-9]\-[0-9]{2}\s[0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]\s([^;]*;){3}([0-9]*);([^;]*;){3}([0-9]*);([0-3][0-9]\.[0-1][0-9]\.[0-9][0-9][0-9][0-9]);([0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]);";
		$auto_ticket_configuration[0]["TEXT_ALLOCATION"] = "";
		$auto_ticket_configuration[1]["CAPTION"] = "ACME-town_1";
		$auto_ticket_configuration[1]["REGEXP"] = "[0-3][0-9]\-[0-1][0-9]\-[0-9]{2}\s[0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]\s([^;]*;){4}([0-9]*);([^;]*;){3}([0-9]*);([0-3][0-9]\.[0-1][0-9]\.[0-9][0-9][0-9][0-9]);([0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]);";
		$auto_ticket_configuration[1]["TEXT_ALLOCATION"] = "";
		$auto_ticket_configuration[2]["CAPTION"] = "ACME-town_2";
		$auto_ticket_configuration[2]["REGEXP"] = "[0-3][0-9]\-[0-1][0-9]\-[0-9]{2}\s[0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]\s([^;]*;){2}([0-9]*);([^;]*;){2}([0-9]*);([^;]*;){3}([0-9]*);([0-3][0-9]\.[0-1][0-9]\.[0-9][0-9][0-9][0-9]);([0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]);";
		$auto_ticket_configuration[2]["TEXT_ALLOCATION"] = "";
		$auto_ticket_configuration[3]["CAPTION"] = "ACME-town_3";
		$auto_ticket_configuration[3]["REGEXP"] = "[0-3][0-9]\-[0-1][0-9]\-[0-9]{2}\s[0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]\s([^;]*;){2}([0-9]*);([^;]*;){3}([0-9]*);([^;]*;){3}([0-9]*);([0-3][0-9]\.[0-1][0-9]\.[0-9][0-9][0-9][0-9]);([0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]);";
		$auto_ticket_configuration[3]["TEXT_ALLOCATION"] = "";
		$auto_ticket_configuration[4]["CAPTION"] = "ACME-town_4";
		$auto_ticket_configuration[4]["REGEXP"] = "[0-3][0-9]\-[0-1][0-9]\-[0-9]{2}\s[0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]\s([^;]*;){2}([0-9]*);([^;]*;){4}([0-9]*);([^;]*;){3}([0-9]*);([0-3][0-9]\.[0-1][0-9]\.[0-9][0-9][0-9][0-9]);([0-2][0-9]\:[0-6][0-9]\:[0-6][0-9]);";
		$auto_ticket_configuration[4]["TEXT_ALLOCATION"] = "";
	}
	return $auto_ticket_configuration;
}

function get_units_addresses($smsg, $phone, $email) {
	$match_array = get_api_configuration("unit");
	$smsg_ids = array ();
	$smsg_ids = explode(",", remove_nls($smsg));
	$addresses = array ();
	$i = 1;
	foreach ($smsg_ids as $value) {
		foreach ($match_array as $key => $value2) {
			if (preg_match("/" . $value2["REGEXP"] . "/", trim($value))) {
				$addresses[$key][$i] = trim($value);
				$i++;
			}
		}
	}
	$phones_addresses = array ();
	$phones_addresses = explode(",", remove_nls($phone));
	foreach ($phones_addresses as $address) {
		if (is_phone(trim($address))) {
			//if (preg_match("/^" . $match_array["PHONE"]["REGEXP"] . "$/", trim($address))) {
			$addresses["PHONE"][$i] = "PHONE:" . trim($address);
			$i++;
		}
	}
	$mail_addresses = array ();
	$mail_addresses = explode(",", remove_nls($email));
	foreach ($mail_addresses as $address) {
		if (is_email(trim($address))) {
			$addresses["EMAIL"][$i] = "EMAIL:" . trim($address);
			$i++;
		}
	}
	return $addresses;
}

//====== communication
//TODO Regex phone, email, smsg_id via api_settings

function get_regex_ipv4_ipv6() {
	return "(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]" .
		"|25[0-5])$|^(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*" .
		"[A-Za-z0-9])$|^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}" .
		"(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))" .
		"|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)" .
		"(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})" .
		"|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))" .
		"|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)" .
		"(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|" .
		"((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|" .
		"(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)" .
		"(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:" .
		"((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*";
}

function get_regexp_plain_url() {
	return "(([a-zA-Z0-9]+([\.-][a-zA-Z0-9]+)*)+\\.[a-zA-Z]{2,})|(" . get_regex_ipv4_ipv6() . ")";
}

function get_regexp_full_url() {
	return "([a-zA-Z]{3,6}:\/\/)?" . get_regexp_plain_url() . "(:[0-9]+)?";
}

function get_regexp_email() {
	return "[a-zA-Z0-9]+([_\\.-][a-zA-Z0-9]+)*@([a-zA-Z0-9]+([\.-][a-zA-Z0-9]+)*)+\\.[a-zA-Z]{2,}";
}

$regexp_smsg_id = "";
function get_regexp_smsg_id() {
	global $regexp_smsg_id;
	if ($regexp_smsg_id == "") {
		$api_settings = array ();

		$result = db_query("SELECT `name`, `value` " .
			"FROM `settings` " .
			"WHERE `name` LIKE '_api_prefix_reporting_channel_%'", __FILE__, __LINE__);

		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$api_settings[$row['name']] = trim($row['value']);
		}
		$return_value = "";
		for ($i = 1; $i <= 5; $i++) {
			if (($api_settings["_api_prefix_reporting_channel_" . $i . "_encdg"] != "") && ($api_settings["_api_prefix_reporting_channel_" . $i . "_regexp"] != "")) {
				$return_value .= "(" . $api_settings["_api_prefix_reporting_channel_" . $i . "_encdg"] . ":" . $api_settings["_api_prefix_reporting_channel_" . $i . "_regexp"] . ")|";
			}
		}

		$result = db_query("SELECT `name`, `value` " .
			"FROM `settings` " .
			"WHERE `name` = '_api_prefix_printer_encdg'", __FILE__, __LINE__);

		$row = stripslashes_deep(db_fetch_assoc($result));
		$row['value'] = trim($row['value']);
		if ($row['value'] != "") {
			$return_value .= "(" . $row['value'] .":ipp:\/\/.*)";
		} else {
			if ($return_value != "") {
				$return_value = substr($return_value, 0, -1);
			}
		}
		$regexp_smsg_id = ".*(" . $return_value . ").*";
	}
	return $regexp_smsg_id;
}

function get_regexp_phone() {
	return "[\d\\(\\)\\/\\+\\-\\s]+";
}

function is_phone($phone) {
	if (preg_match("/^" . get_regexp_phone() . "$/", $phone)) {
		return true;
	} else {
		return false;
	}
}

function is_email($email) {
	if (preg_match("/^" . get_regexp_email() . "$/", $email)) {
		return true;
	} else {
		return false;
	}
}

function is_smsg_id($smsg_id) {
	if (preg_match("/^" . get_regexp_smsg_id() . "$/", $smsg_id)) {
		return true;
	} else {
		return false;
	}
}

function get_message_to_unit_available($contact_1, $contact_2, $contact_3) {
	if (is_phone($contact_1) || (is_email($contact_2) && valid_mailserver()) || is_smsg_id($contact_3)) {
		return true;
	} else {
		return false;
	}
}

function get_message_to_facility_available($contact_1, $contact_2) {
	if (((preg_match("/" . get_regexp_email() . "/", $contact_1)) && valid_mailserver()) || ((preg_match("/" . get_regexp_email() . "/", $contact_2)) && valid_mailserver())) {
		return true;
	} else {
		return false;
	}
}

function get_message_to_user_available($contact_1) {
	if ((preg_match("/" . get_regexp_email() . "/", $contact_1)) && valid_mailserver()) {
		return true;
	} else {
		return false;
	}
}

function valid_mailserver() {
	if (preg_match("/^" .get_regexp_full_url() . "$/", trim(remove_nls(get_variable("_api_email_smtp_host"))))) {
		$temp = explode(",", remove_nls(get_variable("_api_email_from")));
		if (preg_match("/^" . get_regexp_email() . "$/", trim($temp[0]))) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function get_message_click_str($function, $targets_ids = 0, $ticket_id = 0, $handle = "", $contact_1 = "", $contact_2 = "", $contact_3 = "") {
	$message_to_all = false;
	$contact_where_str = "";
	$title = "";
	switch ($function) {
	case "units":
		$title = get_help_text("message_all_units");
		$send_message_to = "'unit_all'";
		break;
	case "situation":
		$title = get_help_text("message_all_units");
		$contact_where_str = " AND (`dispatch` < 3)";
		$send_message_to = "'unit_service'";
		break;
	case "assigns":
		$title = get_help_text("message_all_ticket");
		$contact_where_str = " AND (`u`.`id` IN (SELECT `unit_id` FROM `assigns` WHERE `ticket_id` = " . $ticket_id . "))";
		$send_message_to = "'unit_ticket'";
		break;
	case "callboard":
		$title = get_help_text("message_all_tickets");
		$contact_where_str = " AND (`u`.`id` IN (SELECT `unit_id` FROM `assigns` WHERE `clear` IS NULL))";
		$send_message_to = "'unit_tickets'";
		break;
	case "unit":
		$send_message_to = "'unit'";
		$title = get_text("Click to message") . ": " . $handle;
		break;
	case "facilities":
		$send_message_to = "'facility_all'";
		$title = get_help_text("message_all_facilities");
		break;
	case "facility":
		$send_message_to = "'facility'";
		$title = get_text("Click to message") . ": " . $handle;
		break;
	case "user_all":
		$send_message_to = "'user_all'";
		$title = get_help_text("message_all_users");
		break;
	case "user":
		$send_message_to = "'user'";
		$title = get_text("Click to message") . ": " . $handle;
		break;
	default:
	}
	switch ($function) {
	case "units":
	case "situation":
	case "assigns":
	case "callboard":

		$query = "SELECT * " .
			"FROM `units` `u` " .
			"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
			"LEFT JOIN `allocates` `a` ON (`u`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . ") " .
			"WHERE ((`unit_phone` REGEXP '" . get_regexp_phone() . "') " .
			"OR (`remote_data_services` REGEXP '" . get_regexp_smsg_id() . "') " .
			"OR (`unit_email` REGEXP '" . get_regexp_email() . "')) " .
			$contact_where_str . " AND (`a`.`id` IS NOT NULL);";

		$result = db_query($query, __FILE__, __LINE__);
		if ((db_affected_rows($result)) && valid_mailserver()) {
			$message_to_all = true;
		}
		break;
	case "facilities":

		$query = "SELECT * " .
			"FROM `facilities` `f` " .
			"LEFT JOIN `allocates` `a` ON (`f`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_FACILITY'] . ") " .
			"WHERE (CONCAT(`security_email`, `contact_email`) " .
			"REGEXP '" . get_regexp_email() . "') AND (`a`.`id` IS NOT NULL);";

		$result = db_query($query, __FILE__, __LINE__);
		if ((db_affected_rows($result)) && valid_mailserver()) {
			$message_to_all = true;
		}
		break;
	case "user_all":

		$query = "SELECT * " .
			"FROM `users` " .
			"WHERE `email` " .
			"REGEXP '" . get_regexp_email() . "' " .
			"AND `password` != '55606758fdb765ed015f0612112a6ca7';";

		$result = db_query($query, __FILE__, __LINE__);
		if ((db_affected_rows($result)) && valid_mailserver()) {
			$message_to_all = true;
		}
		break;
	default:
	}
	switch ($function) {
	case "units":
	case "situation":
	case "assigns":
	case "callboard":
	case "unit":
		if ((get_message_to_unit_available($contact_1, $contact_2, $contact_3) || $message_to_all) && (is_operator() || is_admin() || is_super())) {
			return "<span class=\"glyphicon glyphicon-envelope\" aria-hidden=\"true\" style=\"font-size: 12px; " .
				"padding-right: 2px;\"" . get_title_str($title) . "onclick=\"do_send_message(" . $send_message_to .
				", " . $targets_ids . ", " . $ticket_id . ");\"></span>";
		}
	case "facility":
	case "facilities":
		if ((get_message_to_facility_available($contact_1, $contact_2) || $message_to_all) && (is_operator() || is_admin() || is_super())) {
			return "<span class=\"glyphicon glyphicon-envelope\" aria-hidden=\"true\" style=\"font-size: 12px; " .
				"padding-right: 2px;\"" . get_title_str($title) . "onclick=\"do_send_message(" . $send_message_to .
				", " . $targets_ids . ", " . $ticket_id . ");\"></span>";
		}
		break;
	case "user":
	case "user_all":
		if ((get_message_to_user_available($contact_1) || $message_to_all) && (is_operator() || is_admin() || is_super())) {
			return "<span class=\"glyphicon glyphicon-envelope\" aria-hidden=\"true\" style=\"font-size: 12px; " .
				"padding-right: 2px;\"" . get_title_str($title) . "onclick=\"do_send_message(" . $send_message_to .
				", " . $targets_ids . ");\"></span>";
		}
		break;
	default:
	}
}

//====== user

function get_level_text($level) {
	switch ($level) {
	case $GLOBALS['LEVEL_SUPER']:
		return get_text("permission_super");
		break;
	case $GLOBALS['LEVEL_ADMINISTRATOR']:
		return get_text("permission_admin");
		break;
	case $GLOBALS['LEVEL_OPERATOR']:
		return get_text("permission_operator");
		break;
	case $GLOBALS['LEVEL_GUEST']:
		return get_text("permission_guest");
		break;
	}
}

function is_super() {
	if (isset ($_SESSION['level'])) {
		return ($_SESSION['level'] == $GLOBALS['LEVEL_SUPER']);
	} else {
		return false;
	}
}

function is_admin() {
	if (isset ($_SESSION['level'])) {
		return (($_SESSION['level'] == $GLOBALS['LEVEL_ADMINISTRATOR']));
	} else {
		return false;
	}
}

function is_operator() {
	if (isset ($_SESSION['level'])) {
		return ($_SESSION['level'] == $GLOBALS['LEVEL_OPERATOR']);
	} else {
		return false;
	}
}

function is_guest() {
	if (isset ($_SESSION['level'])) {
		return (($_SESSION['level'] == $GLOBALS['LEVEL_GUEST']) || ($_SESSION['level'] == $GLOBALS['LEVEL_MEMBER']));
	} else {
		return false;
	}
}

function set_session_expire_time($timeout = "on") {
	@session_start();
	if (isset ($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	} else {
		$user_id = 0;
	}
	$session_time_limit_settings = explode(",", get_variable("session_time_limit"));
	$session_time_limit = trim($session_time_limit_settings[0]);
	if ($session_time_limit != 0) {
		$_SESSION['timeout'] = $timeout;
	} else {
		$_SESSION['timeout'] = "disabled";
		$session_time_limit = 1;
	}

	$query = "UPDATE `users` " .
		"SET `expires` = '" . mysql_datetime(time() + ($session_time_limit * 60)) . "' " .
		"WHERE `id` = " . $user_id . " " .
		"LIMIT 1;";

	db_query($query, __FILE__, __LINE__);
}

//====== units
function get_unit_type_name($id) {

	$query = "SELECT `name` FROM `unit_types` WHERE `id`= " . $id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_assoc($result));
	if (isset ($row['name'])) {
		return remove_nls($row['name']);
	} else {
		return "No Unit type with ID: #" . $id;
	}
}

function get_unit_status_name($id) {

	$query = "SELECT `status_name` FROM `unit_status` WHERE `id`= " . $id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_assoc($result));
	if (isset ($row['status_name'])) {
		return remove_nls($row['status_name']);
	} else {
		return "No Unit status with ID: #" . $id;
	}
}

//====== facilities
function get_facility_type_name($id) {

	$query = "SELECT `name` FROM `facility_types` WHERE `id`= " . $id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_assoc($result));
	if (isset ($row['name'])) {
		return remove_nls($row['name']);
	} else {
		return "No Facility type with ID: #" . $id;
	}
}

function get_facility_status_name($id) {

	$query = "SELECT `status_name` FROM `facility_status` WHERE `id`= " . $id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_assoc($result));
	if (isset ($row['status_name'])) {
		return remove_nls($row['status_name']);
	} else {
		return "No Facility status with ID: #" . $id;
	}
}

function get_facility_handle($id) {

	$query = "SELECT `handle` FROM `facilities` WHERE `id`= " . $id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$row = stripslashes_deep(db_fetch_assoc($result));
	if (isset ($row['handle'])) {
		return remove_nls($row['handle']);
	} else {
		return "No Facility with ID: #" . $id;
	}
}

//====== gis
require_once ("phpcoord.inc.php");				// UTM converter
function toUTM($coordsIn, $from = "") {
	$temp = explode(",", $coordsIn);
	$coords = new LatLng(trim($temp[0]), trim($temp[1]));
	$utm = $coords -> toUTMRef();
	$temp = $utm -> toString();
	$temp1 = explode (" ", $temp);
	$temp2 = explode (".", $temp1[1]);
	$temp3 = explode (".", $temp1[2]);
	return $temp1[0] . " " . $temp2[0] . " " . $temp3[0];
}

function lat2dms($inlat) {
	$nors = ($inlat < 0.0)? "S." : "N.";
	$d = floor(abs($inlat));	// degrees
	$mu = (abs($inlat) - $d) * 60;	// min's unrounded
	$m = floor($mu);			// min's
	$su = ($mu - $m) * 60;		// sec's unrounded
	$s = (round($su, 1));		// seconds
	return $d . "&deg; " . abs($m) . "&#39; " . abs($s) . "&#34;" . $nors;
}

function lng2dms($inlng) {
	$wore = ($inlng < 0.0)? "W." : "E.";
	$d = floor(abs($inlng));	// degrees
	$mu = (abs($inlng) - $d) * 60;	// min's unrounded
	$m = floor($mu);			// min's
	$su = ($mu - $m) * 60;		// sec's unrounded
	$s = (round($su, 1));		// seconds
	return $d . "&deg; " . abs($m) . "&#39; " . abs($s) . "&#34;" . $wore;
}

function lat2ddm($inlat) {
	$nors = ($inlat < 0.0)? "S." : "N.";
	$deg = floor(abs($inlat));
	return $deg . "&deg; " . round(abs($inlat-$deg)*60, 1) . "' " . $nors;
}

function lng2ddm($inlng) {
	$wore = ($inlng < 0.0)? "W." : "E.";
	$deg = floor(abs($inlng));
	return $deg . "&deg; " . round((abs($inlng)-$deg)*60, 1) . "' " . $wore;
}

function get_lat($in_lat) {
	if (empty ($in_lat)) {
		return "";
	}
	$format = get_variable("_lat_lng");
	switch ($format) {
	case 0:
		return $in_lat;
		break;
	case 1:
		return lat2dms($in_lat);
		break;
	case 2:
		return lat2ddm($in_lat);
		break;
	}
}

function get_lng($in_lng) {
	if (empty ($in_lng)) {
		return "";
	}
	$format = get_variable("_lat_lng");
	switch ($format) {
	case 0:
		return $in_lng;
		break;
	case 1:	
		return lng2dms($in_lng);
		break;
	case 2:
		return lng2ddm($in_lng);
		break;
	}
}

//====== date and time

function is_datetime($date) {
	return (
		is_string($date) &&
		preg_match("/^[0-9]{4}\\-[0-9]{2}\\-[0-9]{2}\\ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $date) &&
		(!($date == "0000-00-00 00:00:00")) &&
		(!($date == "2017-01-01 00:00:00"))
	);
}

function datetime_difference($d1_in, $d2_in) {
	$d1 = strtotime($d1_in);
	$d2 = strtotime($d2_in);
	if ($d1 < $d2) {
		$temp = $d2;
		$d2 = $d1;
		$d1 = $temp;
	} else {
		$temp = $d1;
	}
	$d1 = date_parse(date("Y-m-d H:i:s", (integer)$d1));
	$d2 = date_parse(date("Y-m-d H:i:s", (integer)$d2));
	if ($d1['second'] >= $d2['second']) {
		$diff['second'] = $d1['second'] - $d2['second'];
	} else {
		$d1['minute']--;
		$diff['second'] = 60 - $d2['second'] + $d1['second'];
	}
	if ($d1['minute'] >= $d2['minute']) {
		$diff['minute'] = $d1['minute'] - $d2['minute'];
	} else {
		$d1['hour']--;
		$diff['minute'] = 60 - $d2['minute'] + $d1['minute'];
	}
	if ($d1['hour'] >= $d2['hour']) {
		$diff['hour'] = $d1['hour'] - $d2['hour'];
	} else {
		$d1['day']--;
		$diff['hour'] = 24 - $d2['hour'] + $d1['hour'];
	}
	if ($d1['day'] >= $d2['day']) {
		$diff['day'] = $d1['day'] - $d2['day'];
	} else {
		$d1['month']--;
		$diff['day'] = date("t",$temp) - $d2['day'] + $d1['day'];
	}
	if ($d1['month'] >= $d2['month']) {
		$diff['month'] = $d1['month'] - $d2['month'];
	} else {
		$d1['year']--;
		$diff['month'] = 12-$d2['month'] + $d1['month'];
	}
	$diff['year'] = $d1['year'] - $d2['year'];
	$output_str = "";
	$plural = ($diff['year'] == 1)? get_text("yr"): get_text("yrs");
	$output_str .= empty ($diff['year'])? "" : $diff['year'] . " " . $plural . " ";

	$plural = ($diff['month'] == 1)? get_text("mo"): get_text("mos");
	$output_str .= empty ($diff['month'])? "" : $diff['month']. " " .  $plural . " ";

	$plural = ($diff['day'] == 1)? get_text("Day"): get_text("days");
	$output_str .= empty ($diff['day'])? "" : $diff['day'] . " " . $plural . " ";

	$plural = ($diff['hour'] == 1)? get_text("hr"): get_text("hrs");
	$output_str .= empty ($diff['hour'])? "" : $diff['hour'] . "  " . $plural . " ";

	$plural = ($diff['minute'] == 1)? get_text("min"): get_text("mins");
	$output_str .= empty ($diff['minute'])? "" : $diff['minute'] . " " . $plural . " ";

	$plural = ($diff['second'] == 1)? get_text("Second") : get_text("Seconds");
	if (empty ($diff['second'])) {
		$output_str .= "";
	} else {
		$output_str .= $diff['second'] . " " . $plural;
	}
	return  $output_str;
}

function get_elapsed_time($in_row) {
	$start_date = format_date($in_row['problemstart']);
	if ($in_row['status'] == $GLOBALS['STATUS_SCHEDULED']) {
		$start_date = format_date($in_row['booked_date']);
	}
	$end_date = mysql_datetime();
	if (is_datetime($in_row['problemend'])) {
		$end_date = $in_row['problemend'];
	}
	return datetime_difference ($start_date, $end_date);
}

function elapsed($in_time) {
	$secs = (integer) (round(time() - strtotime($in_time)));
	if ($secs > 5940) {
		$secs = 5940;
	}
	return $secs;
}

function mysql_datetime($datetime_input = "") {
	if (empty ($datetime_input)) {
		$datetime_input = time();
	}
	return @date("Y-m-d H:i:s", $datetime_input);
}

function format_date($date_in) {
	$date_wk = trim($date_in);	
	if (strlen(trim($date_in)) == 19) {	
		$date_wk = strtotime(trim($date_in));
	}
	return date(get_variable("date_format"), intval($date_wk));
}

function php_to_moment($php_format) {
	$replacement_tags = array (
		'd' => 'DD',
		'D' => 'ddd',
		'j' => 'D',
		'l' => 'dddd',
		'N' => 'E',
		'S' => 'o',
		'w' => 'e',
		'z' => 'DDD',
		'W' => 'W',
		'F' => 'MMMM',
		'm' => 'MM',
		'M' => 'MMM',
		'n' => 'M',
		't' => '',
		'L' => '',
		'o' => 'YYYY',
		'Y' => 'YYYY',
		'y' => 'YY',
		'a' => 'a',
		'A' => 'A',
		'B' => '',
		'g' => 'h',
		'G' => 'H',
		'h' => 'hh',
		'H' => 'HH',
		'i' => 'mm',
		's' => 'ss',
		'u' => 'SSS',
		'I' => '',
		'O' => '',
		'P' => '',
		'T' => '',
		'Z' => '',
		'c' => '',
		'r' => '',
		'U' => 'X',
	);
	$moment_format = strtr($php_format, $replacement_tags);
	return $moment_format;
}

function set_database_timezone() {
	$now = new DateTime();
	$mins = $now->getOffset() / 60;
	$sgn = 1;
	if ($mins < 0) {
		$sgn = -1;
	}
	$mins = abs($mins);
	$hrs = floor($mins / 60);
	$mins -= $hrs * 60;
	$offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
	db_query("SET time_zone='" . $offset . "';", __FILE__, __LINE__);
}

//====== titles

function get_current_log_infos($type, $id) {
	$return_str = "";
	$where_part_str = "unit_id";
	$setting_index = 1;
	if ($type == "facility") {
		$where_part_str = "facility_id";
		$setting_index = 2;
	}
	$report_log_settings = explode(",", get_variable("report_log"));
	$report_log_infos = trim($report_log_settings[$setting_index]);
	if ($report_log_infos != 0) {

		$query_log_infos = "SELECT `text`, `datetime` " .
			"FROM `log` " .
			"WHERE `" . $where_part_str . "` = " . $id . " " .
			"AND `ticket_id` = 0 " .
			"AND `code` = " . $GLOBALS['LOG_COMMENT'] . " " .
			"AND (DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " . $report_log_infos . " MINUTE) <= `datetime`) " .
			"ORDER BY `datetime` DESC;";

		$result_log_infos = db_query($query_log_infos, __FILE__, __LINE__);
		if (db_affected_rows($result_log_infos) != 0) {
			$return_str .= "------------------------------<br>";
			while ($row_log_infos = stripslashes_deep(db_fetch_assoc($result_log_infos))) {
				$return_str .= date(get_variable("date_format_time_only"), strtotime($row_log_infos['datetime'])) . " " . remove_nls($row_log_infos['text']) . "<br>";
			}
		}
	}
	return $return_str;
}

function get_title_unit_str($row) {
	$title_unit = "";
	if ($row['unit_name'] != "") {
		$title_unit .= "<nobr>" . get_text("Unit name") . ":&nbsp;" . remove_nls($row['unit_name']) . "</nobr><br>";
	}
	if ($row['type_name'] != "") {
		$title_unit .= "<nobr>" . get_text("Type") . ":&nbsp;" . remove_nls($row['type_name']) . "</nobr><br>";
	}
	if ($row['multi'] != "") {
		switch ($row['multi']) {
		case 0:
			$title_unit .= "<nobr>" . get_text("Dispatchable") . ":&nbsp;" . get_text("Not dispatchable") . "</nobr><br>";
			break;
		case 2:
			$title_unit .= "<nobr>" . get_text("Dispatchable") . ":&nbsp;" . get_text("Multiple dispatchable") . "</nobr><br>";
			break;
		default:
		}
	}
	if ((get_num_groups()) && (count(get_allocates(4, $_SESSION['user_id'])) > 1)) {
		$title_unit .= get_group_names($row['unit_id'], 2) . "<br>";
	}
	if ((isset ($row['guard_house_handle'])) && ($row['guard_house_handle'] != "")) {
		$title_unit .= "<nobr>" . get_text("Guard house") . ":&nbsp;" . remove_nls($row['guard_house_handle']) . "</nobr><br>";
	}
	if ((isset ($row['guard_house_street'])) && ($row['guard_house_street'] != "")) {
		$title_unit .= "<nobr>" . get_text("Facility address") . ":&nbsp;" . remove_nls($row['guard_house_street']) . "</nobr><br>";
	}
	if ((isset ($row['guard_house_city'])) && ($row['guard_house_city'] != "")) {
		$title_unit .= "<nobr>" . get_text("City") . ":&nbsp;" . remove_nls($row['guard_house_city']) . "</nobr><br>";
	}
	if ((isset ($row['remote_data_services'])) && ($row['remote_data_services'] != "")) {
		$title_unit .= "<nobr>" . get_text("Remote data services") . ":&nbsp;" . remove_nls($row['remote_data_services']) . "</nobr><br>";
	}
	if ((isset ($row['unit_phone'])) && ($row['unit_phone'] != "")) {
		$title_unit .= "<nobr>" . get_text("Cellular phone") . ":&nbsp;" . remove_nls($row['unit_phone']) . "</nobr><br>";
	}
	if ((isset ($row['unit_email'])) && ($row['unit_email'] != "")) {
		$title_unit .= "<nobr>" . get_text("Email") . ":&nbsp;" . remove_nls($row['unit_email']) . "</nobr><br>";
	}
	if ((isset ($row['unit_descr'])) && ($row['unit_descr'] != "")) {
		$title_unit .= "<nobr>" . get_text("Description") . ":&nbsp;" . remove_nls($row['unit_descr']) . "</nobr><br>";
	}
	if ((isset ($row['capabilities'])) && ($row['capabilities'] != "")) {
		$title_unit .= "<nobr>" . get_text("Capability") . ":&nbsp;" . remove_nls($row['capabilities']) . "</nobr><br>";
	}
	if ((isset ($row['contact_name'])) && ($row['contact_name'] != "")){
		$title_unit .= "<nobr>" . get_text("Contact name") . ":&nbsp;" . remove_nls($row['contact_name']) . "</nobr><br>";
	}
	if ((isset ($row['admin_only'])) && ($row['admin_only'] == 1)){
		$title_unit .= "<nobr>" . get_text("Admin permission") . ":&nbsp;" . get_text("Superadmin only") . "</nobr><br>";
	}
	$title_unit .= get_current_log_infos("unit", $row['unit_id']);
	return get_title_str($title_unit);
}

function get_title_facility_str($row) {
	$title_facility = "";
	if ($row['facility_name'] != "") {
		$title_facility .= "<nobr>" . get_text("Facility name") . ":&nbsp;" . $row['facility_name'] . "</nobr><br>";
	}
	if ($row['fac_type_name'] != "") {
		$title_facility .= "<nobr>" . get_text("Type") . ":&nbsp;" . remove_nls($row['fac_type_name']) . "</nobr><br>";
	}
	if ((get_num_groups()) && (COUNT(get_allocates(3, $_SESSION['user_id'])) > 1)) {
		$title_facility .= get_group_names($row['fac_id'], 3) . "<br>";
	}
	if ((isset ($row['object_id'])) && ($row['object_id'] != "")) {
		$title_facility .= "<nobr>" . get_text("Object id") . ":&nbsp;" . $row['object_id'] . "</nobr><br>";
	}
	if ((isset ($row['direct_dialing_1'])) && ($row['direct_dialing_1'] != "")) {
		$title_facility .= "<nobr>" . get_text("Direct dialing 1") . ":&nbsp;" . $row['direct_dialing_1'] . "</nobr><br>";
	}
	if ((isset ($row['direct_dialing_2'])) && ($row['direct_dialing_2'] != "")) {
		$title_facility .= "<nobr>" . get_text("Direct dialing 2") . ":&nbsp;" . $row['direct_dialing_2'] . "</nobr><br>";
	}
	if ((isset ($row['street'])) && ($row['street'] != "")) {
		$title_facility .= "<nobr>" . get_text("Facility address") . ":&nbsp;" . $row['street'] . "</nobr><br>";
	}
	if ((isset ($row['city'])) && ($row['city'] != "")) {
		$title_facility .= "<nobr>" . get_text("City") . ":&nbsp;" . $row['city'] . "</nobr><br>";
	}
	if ((isset ($row['opening_hours'])) && ($row['opening_hours'] != "")) {
		$title_facility .= "<nobr>" . get_text("Opening hours") . ":&nbsp;" . $row['opening_hours'] . "</nobr><br>";
	}
	if ((isset ($row['access_rules'])) && ($row['access_rules'] != "")) {
		$title_facility .= "<nobr>" . get_text("Access rules") . ":&nbsp;" . $row['access_rules'] . "</nobr><br>";
	}
	if ((isset ($row['security_contact'])) && ($row['security_contact'] != "")) {
		$title_facility .= "<nobr>" . get_text("Security contact") . ":&nbsp;" . $row['security_contact'] . "</nobr><br>";
	}
	if ((isset ($row['security_phone'])) && ($row['security_phone'] != "")) {
		$title_facility .= "<nobr>" . get_text("Security phone") . ":&nbsp;" . $row['security_phone'] . "</nobr><br>";
	}
	if ((isset ($row['security_email'])) && ($row['security_email'] != "")) {
		$title_facility .= "<nobr>" . get_text("Security email") . ":&nbsp;" . $row['security_email'] . "</nobr><br>";
	}
	if ((isset ($row['facility_description'])) && ($row['facility_description'] != "")) {
		$title_facility .= get_text("Description") . ":&nbsp;" . $row['facility_description'] . "<br>";
	}
	if ((isset ($row['capabilities'])) && ($row['capabilities'] != "")) {
		$title_facility .= "<nobr>" . get_text("Capability") . ":&nbsp;" . $row['capabilities'] . "</nobr><br>";
	}
	if ((isset ($row['contact_name'])) && ($row['contact_name'] != "")) {
		$title_facility .= "<nobr>" . get_text("Contact name") . ":&nbsp;" . $row['contact_name'] . "</nobr><br>";
	}
	if ((isset ($row['contact_phone'])) && ($row['contact_phone'] != "")) {
		$title_facility .= "<nobr>" . get_text("Phone") . ":&nbsp;" . $row['contact_phone'] . "</nobr><br>";
	}
	if ((isset ($row['contact_email'])) && ($row['contact_email'] != "")) {
		$title_facility .= "<nobr>" . get_text("Email") . ":&nbsp;" . $row['contact_email'] . "<br>";
	}
	if ((isset ($row['admin_only'])) && ($row['admin_only'] == 1)) {
		$title_facility .= "<nobr>" . get_text("Admin permission") . ":&nbsp;" . get_text("Superadmin only") . "</nobr><br>";
	}
	$title_facility .= get_current_log_infos("facility", $row['fac_id']);
	return get_title_str($title_facility);
}

function get_title_ticket($row) {
	$title = "";
	if ($row['booked_date'] != "") {
		$title .= get_text("Scheduled Date") . ":&nbsp;" . date(get_variable("date_format"), $row['booked_date']) . "<br>";
	}
	if ($row['ticket_street']) {
		$title .= get_text("Incident location") . ":&nbsp;" . remove_nls($row['ticket_street']) . "<br>";
	} else {
		$title .= get_text("Incident location") . ":&nbsp;" . get_text("[No Address]") . "<br>";
	}
	if ((isset ($row['fac_handle'])) && ($row['fac_handle'] != "")) {
		$title .= get_text("Facility") . ":&nbsp;" . remove_nls($row['fac_handle']) . "<br>";
	}
	if ($row['ticket_description'] != "") {
		$title .= get_text("Synopsis") . ": " . remove_nls($row['ticket_description']) . "<br>";
	} else {
		$title .= get_text("Synopsis") . ": " .  get_text("[No description]") . "<br>";
	}
	if ($row['contact'] != "") {
		$title .= get_text("Reported by") . ":&nbsp;" . remove_nls($row['contact']) . "<br>";
	}
	if ($row['incident_name'] != "") {
		$title .= get_text("Incident name") . ":&nbsp;" . remove_nls($row['incident_name']) . "<br>";
	}
	if ($row['tick_phone'] != "") {
		$title .= get_text("Callback phone") . ":&nbsp;" . remove_nls($row['tick_phone']) . "<br>";
	}
	if ($row['tick_comm'] != "") {
		$title .= get_text("Comments") . ": " . remove_nls($row['tick_comm']) . "<br>";
	}
	$title_disposition = "";
	if ((isset ($row['assign_facility_id'])) && ($row['assign_facility_id'] != -1)) {
		if ((isset ($row['assign_on_scene_location'])) && ($row['assign_on_scene_location'] != "")) {
			$title_disposition .= get_text("On-Scene location") . ":&nbsp;" . remove_nls($row['assign_on_scene_location']) . "<br>";
		} else {
			$title_disposition .= get_text("On-Scene location") . ":&nbsp;" . get_text("[No on-scene location]") . "<br>";
		}
		if ($row['assign_on_scene_facility_handle'] != "") {
			$title_disposition .= get_text("Facility") . ":&nbsp;" . remove_nls($row['assign_on_scene_facility_handle']) . "<br>";
		}
	}
	if ((isset ($row['assign_receiving_location'])) && ($row['assign_receiving_location'] != "")) {
		$title_disposition .= get_text("Receiving location") . ":&nbsp;" . remove_nls($row['assign_receiving_location']) . "<br>";
	}
	if ((isset ($row['assign_rec_facility_handle'])) && ($row['assign_rec_facility_handle'] != "")) {
		$title_disposition .= get_text("Facility") . ":&nbsp;" . remove_nls($row['assign_rec_facility_handle']) . "<br>";
	}
	if ((isset ($row['assign_comments'])) && ($row['assign_comments'] != "")) {
		$title_disposition .= get_text("Comments") . ":&nbsp;" . remove_nls($row['assign_comments']) . "<br>";
	}
	if ($title_disposition != "") {
		$title .= "------------------------------<br>";
		$title .= $title_disposition;
	}
	return $title;
}

function get_title_type_str($row) {
	$title_type = "";
	if ($row['type'] != "") {
		$title_type .= get_text("Incident type") . ": " . remove_nls($row['type']) . "<br>";
	}
	if ($row['t_des'] != "") {
		$title_type .= get_text("Description") . ": " . remove_nls($row['t_des']) . "<br>";
	}
	if ($row['t_proto'] != "") {
		$title_type .= get_text("Protocol") . ": " . remove_nls($row['t_proto']) . "<br>";
	}
	return get_title_str($title_type);
}

function get_current_tickets_title($unit_id) {
	$title_ticket_str = "";

	$query = "SELECT *, " .
		"`assigns`.`id` AS `assign_id`, " .
		"`assigns`.`updated` AS `assign_updated`, " .
		"`assigns`.`on_scene_facility_id` AS `assign_facility_id`, " .
		"`assigns`.`on_scene_location` AS `assign_on_scene_location`, " .
		"`assigns`.`comments` AS `assign_comments`, " .
		"`f_a_o`.`handle` AS `assign_on_scene_facility_handle`, " .
		"`assigns`.`receiving_location` AS `assign_receiving_location`, " .
		"`f_a_r`.`handle` AS `assign_rec_facility_handle`, " .
		"`t`.`id`, " .
		"`t`.`location` AS `ticket_street`, " .
		"`t`.`description` AS `ticket_description`, " .
		"`t`.`phone` AS `tick_phone`, " .
		"`t`.`comments` AS `tick_comm`, " .
		"`f`.`handle` AS `fac_handle`, " .
		"UNIX_TIMESTAMP(`t`.`booked_date`) AS `booked_date` " .
		"FROM `assigns` " .
		"LEFT JOIN `tickets` `t` ON (`assigns`.`ticket_id` = `t`.`id`) " .
		"LEFT JOIN `facilities` `f` ON (`t`.`facility_id` = `f`.`id`) " .
		"LEFT JOIN `facilities` `f_a_o` ON `assigns`.`on_scene_facility_id` = `f_a_o`.`id` " .
		"LEFT JOIN `facilities` `f_a_r` ON `assigns`.`receiving_facility_id` = `f_a_r`.`id` " .
		"WHERE `unit_id` = " . $unit_id . " " .
		"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00') " .
		"ORDER BY `t`.`id` ASC;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_affected_rows($result) != 0) {
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$title_ticket_str .= get_title_ticket($row) . "<br>";
		}
	}
	return substr($title_ticket_str, 0, -4);
}

function get_title_action_str($row) {
	$title_action = "";
	$no_action = false;

	$where_str = "WHERE `ticket_id` = '" . $row['ticket_id'] . "' ";
	if (isset($row['unit_id'])) {
		$where_str .= "AND `unit_id` = '" . $row['unit_id'] . "' ";
	}

	$query_actions = "SELECT * " .
		"FROM `actions` " .
		$where_str .
		"ORDER BY `updated` DESC;";

	$result_actions = db_query($query_actions, __FILE__, __LINE__);
	if (db_num_rows($result_actions) > 0) {
		while ($row_action = stripslashes_deep(db_fetch_assoc($result_actions))) {
			$title_action .= remove_nls($row_action['description']) . "<br>";
			if (($row_action['unit_id'] != null) && ($row_action['unit_id'] > 0)) {

				$query_responder = "SELECT `handle`, " .
					"`name` " .
					"FROM `units` " .
					"WHERE `id` = " . $row_action['unit_id'];

				$result_responder = db_query($query_responder, __FILE__, __LINE__);
				$row_responder = stripslashes_deep(db_fetch_assoc($result_responder));
				if (isset ($row_responder['handle'])) {
					$title_action .= "<nobr>" . remove_nls($row_responder['handle']) . "</nobr><br>";
				} else {
					$title_action .= "<br>";
				}
			}
			$title_action .= get_text("As of") . ":&nbsp;" . preg_replace("/\s+/", "&nbsp;", date(get_variable("date_format"), strtotime($row_action['updated']))) . "&nbsp;";
			$query_user = "SELECT `name` AS `user_name` " .
				"FROM `users` " .
				"WHERE `id` = " . $row_action['user_id'] . " " .
				"LIMIT 1;";

			$result_user = db_query($query_user, __FILE__, __LINE__);
			$row_user = stripslashes_deep(db_fetch_assoc($result_user));
			$title_action .= get_text("by") . "&nbsp;" . get_text("User") . ":&nbsp;" . remove_nls($row_user['user_name']) . "<br>";
		}
	} else {
		$title_action .= "<nobr>" . get_text("No actions.") . "</nobr><br>";
		$no_action = true;
	}
	return array ($title_action, get_nowrap_title_str($title_action), $no_action);
}

function get_title_dispatched_str($row) {
	$title_unit = "";
	$blink = false;

	$query_assign = "SELECT `assigns`.`unit_id`, " .
		"`assigns`.`clear`, " .
		"`units`.`handle` " .
		"FROM `assigns` " .
		"LEFT JOIN `units` ON `assigns`.`unit_id` = `units`.`id` " .
		"WHERE `ticket_id` = " . $row['ticket_id'] . " " .
		"ORDER BY `dispatched` DESC;";

	$result_assign = db_query($query_assign, __FILE__, __LINE__);
	if (db_num_rows($result_assign) > 0) {
		while ($row_assign = stripslashes_deep(db_fetch_assoc($result_assign))) {
			if (empty ($row_assign['clear'])) {
				$title_unit .= "<span style=\'white-space: pre;\'>" . remove_nls($row_assign['handle']) . "</span><br>";
			} else {
				$title_unit .= "<span style=\'white-space: pre; text-decoration: line-through;\'>" . remove_nls($row_assign['handle']) . "</span><br>";
			}
		}
	} else {
		$title_unit .= "<span style=\'white-space: pre;\'>" . get_text("No assigned Responder.") . "</span><br>";
		$blink = true;
	}
	return array ($title_unit, get_nowrap_title_str($title_unit), $blink);
}

function get_table_id($id) {
	$return_str = "";
	if (is_super()) {
		$return_str = ": #" . $id;
	}
	return $return_str;
}

function get_table_id_title_str($table, $id) {
	$return_str = "";
	if (is_super()) {
		switch ($table) {
		case "action":
			$return_str = get_title_str(get_text("Database-Table") . ": actions - " . get_text("Table-ID") . ": #" . $id);
			break;
		case "assign":
			$return_str = get_title_str(get_text("Database-Table") . ": assigns - " . get_text("Table-ID") . ": #" . $id);
			break;
		case "user":
			$return_str = get_title_str(get_text("Database-Table") . ": users - " . get_text("Table-ID") . ": #" . $id);
			break;
		case "facility":
			$return_str = get_title_str(get_text("Database-Table") . ": facilities - " . get_text("Table-ID") . ": #" . $id);
			break;
		case "ticket":
			$return_str = get_title_str(get_text("Database-Table") . ": tickets - " . get_text("Table-ID") . ": #" . $id);
			break;
		case "unit":
			$return_str = get_title_str(get_text("Database-Table") . ": units - " . get_text("Table-ID") . ": #" . $id);
			break;
		default:
		}
	}
	return $return_str;
}

$help_texts = array ();
function get_help_text($tag, $raw = false) {
	global $help_texts;
	if (empty ($help_texts)) {

		$result = db_query("SELECT * FROM `hints`", __FILE__, __LINE__);

		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$help_texts[$row['tag']] = $row['hint'];
		}
	}
	if (array_key_exists($tag, $help_texts)) {
		if ($raw) {
			return $help_texts[$tag];
		} else {
			return remove_nls(wordwrap($help_texts[$tag], 80, "<br>", true));
		}
	} else {
		return get_text("No help Text available.");
	}
}

function get_help_text_str($tag) {
	return " onmouseover=\"Tip('" . get_help_text($tag) . "');\"  onmouseout=\"UnTip();\"";

}

function get_title_str($text) {
	if ($text) {
		return " onmouseover=\"Tip('" . remove_nls(wordwrap($text, 80, "<br>", true)) . "');\"  onmouseout=\"UnTip();\"";
	}
}

function get_nowrap_title_str($text) {
	if ($text) {
		return " onmouseover=\"Tip('" . $text . "');\" onmouseout=\"UnTip();\"";
	}
}

//====== text format

function remove_nls($instr) {
	$nls = array ("\r\n", "\n", "\r", "'", "\"");
	$nonls = str_replace($nls, " ", $instr);
	$return_str = htmlspecialchars($nonls, ENT_COMPAT, "UTF-8");	//ENT_QUOTES	ENT_COMPAT
	return $return_str;
}

$text_array = array ();
function get_text($label) {
	global $text_array;
	if (empty ($text_array)) {

		$result = db_query("SELECT * FROM `captions`", __FILE__, __LINE__);

		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$text_array[$row['capt']] = $row['repl'];
		}
	}
	$caption = $label;
	if (array_key_exists($label, $text_array)) {
		$caption = $text_array[$label];
	}
	return remove_nls($caption);
}

function breakspace($text, $after_chars) {
	$textlaenge = strlen($text);
	$textarray = array ();
	$char_counter = 0;
	$array_counter = 0;
	for ($i = 0; $i < $textlaenge; $i++) {
		$textarray[$array_counter] = substr($text, $i, 1);
		$array_counter++;
		$char_counter++;
		if (substr($text, $i, 1) == " ") {
			$char_counter = 0;
		}
		if ($char_counter > $after_chars) {
			$textarray[$array_counter] = " ";
			$array_counter++;
			$char_counter = 0;
		}
	}
	return implode("", $textarray);
}

function shorten($instring, $limit) {
	$return_str = $instring;
	if (strlen($instring) > $limit) {
		$return_str = substr($instring, 0, $limit - 4) . "...";
	}
	return $return_str;
}

function highlight($term, $string) {
	$replace = "<span class='found'>" . $term . "</span>";
	if (function_exists('str_ireplace')) {
		return str_ireplace($term,  $replace, $string);
	} else {
		return str_replace($term,  $replace, $string);
	}
}

//====== regions

function get_allocates_where_str($type1, $type2, $statement = "WHERE") {
	$user_id = 0;
	if (isset ($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	}

	$query = "SELECT * " .
		"FROM `allocates` " .
		"WHERE `type` = " . $type1 . " " .
		"AND `resource_id` = " . $user_id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$al_groups = array ();
	while ($row = stripslashes_deep(db_fetch_assoc($result))) {
		$al_groups[] = $row['group'];
	}
	if (isset ($_SESSION['viewed_groups'])) {
		$curr_viewed = explode(",", $_SESSION['viewed_groups']);
	}
	if (!isset ($curr_viewed)) {
		if (!isset($al_groups[0])) {	// catch for errors - no entries in allocates for the user.
			$where2 = $statement . " `allocates`.`type` = " . $type2;
		} else {
			$x = 0;
			$where2 = $statement . " (";
			foreach ($al_groups as $group) {
				$where3 = (count($al_groups) > ($x + 1))? " OR " : ")";
				$where2 .= "`allocates`.`group` = '" . $group . "'";
				$where2 .= $where3;
				$x++;
			}
			$where2 .= "AND `allocates`.`type` = " . $type2;
		}
	} else {
		if (count($curr_viewed == 0)) {	//	catch for errors - no entries in allocates for the user.
			$where2 = $statement . " `allocates`.`type` = " . $type2;
		} else {
			$x = 0;
			$where2 = $statement . " (";
			foreach ($curr_viewed as $group) {
				$where3 = (count($curr_viewed) > ($x + 1))? " OR " : ")";
				$where2 .= "`allocates`.`group` = '" . $group . "'";
				$where2 .= $where3;
				$x++;
			}
			$where2 .= "AND `allocates`.`type` = " . $type2;
		}
	}
	$where2 .= " ";
	return $where2;
}

function get_group_names($id, $type) {
	if ((get_num_groups()) && (count(get_allocates($type, $_SESSION['user_id'])) > 1)) {
		$groups = get_allocates($type, $id);
		$group_names = get_text("Region assigned") . ": ";
		$y = 0;
		foreach ($groups as $value) {
			$counter = (count($groups) > ($y + 1)) ? ", " : "";
			$group_names .= get_groupname($value);
			$group_names .= $counter;
			$y++;
		}
		return $group_names;
	} else {
		return "";
	}
}

$allocates_array = array ();
function get_allocates($type, $resource) {
	global $allocates_array;
	$al_groups = array ();
	if (empty ($allocates_array)) {

		$query = "SELECT `type`, " .
			"`resource_id`, " .
			"`group` " .
			"FROM `allocates` " .
			"ORDER BY `type`, `resource_id`, `group`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$allocates_array[$row['type']][$row['resource_id']][] = $row['group'];
		}
	}
	if (isset ($allocates_array[$type][$resource][0])) {
		foreach ($allocates_array[$type][$resource] as $value) {
			if (isset ($row['group'])) {
				$al_groups[] = $row['group'];
			}
		}
	} else {

		$query = "SELECT `type`, " .
			"`resource_id`, " .
			"`group` " .
			"FROM `allocates` " .
			"ORDER BY `type`, `resource_id`, `group`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$allocates_array[$row['type']][$row['resource_id']][] = $row['group'];
		}
		if (isset ($allocates_array[$type][$resource][0])) {
			foreach ($allocates_array[$type][$resource] as $value) {
				if (isset ($row['group'])) {
					$al_groups[] = $row['group'];
				}
			}
		} else {
			while ($row = stripslashes_deep(db_fetch_assoc($result))) {
				if (isset ($row['group'])) {
					$al_groups[] = $row['group'];
				}
			}
		}
	}
	return $al_groups;
}

function get_groupname($groupid) {

	$query = "SELECT * " .
		"FROM `regions` " .
		"WHERE `id` = " . $groupid . ";";

	$result = db_query($query, __FILE__, __LINE__);
	while ($row = stripslashes_deep(db_fetch_assoc($result))) {
		$groupname = remove_nls($row['region_name']);
	}
	return $groupname;
}

function get_num_groups() {

	$query = "SELECT * " .
		"FROM `regions`;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) >= 2) {
		return true;
	} else {
		return false;
	}
}

function test_allocates($resource, $al_group, $type) {

	$query = "SELECT * " .
		"FROM `allocates` " .
		"WHERE `resource_id` = " . $resource . " " .
		"AND `group` = " . $al_group . " " .
		"AND `type` = " . $type . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$found = db_num_rows($result);
	if ($found == 0) {
		return true;
	} else {
		return false;
	}
}
?>