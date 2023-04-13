<?php
$GLOBALS['DATABASE_LINK'] = null;

function update_db_query($query_str, $file, $line) {
	$result = $GLOBALS['DATABASE_LINK']->query($query_str);
	if ($result != false) {
		return $result;
	} else {
		$error_message = $GLOBALS['DATABASE_LINK']->errorInfo();
		@error_log($error_message[2] . " in " . basename($file) . " line " . $line . "\r\n" . $query_str);
		return false;
	}
}

function update_get_current_path($filename) {
	if (DIRECTORY_SEPARATOR === "\\") {
		return str_replace("/", "\\", getcwd() . DIRECTORY_SEPARATOR . $filename);	//to windows
	} else {
		return str_replace("\\", "/", getcwd() . DIRECTORY_SEPARATOR . $filename);	//to *nix
	}
}

$json_array = array ();
$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
}
$simulation = "";
if (isset ($_GET['simulate'])) {
	$simulation = $_GET['simulate'];
}
$simulation_time = 2;
if (isset ($_GET['simulate_time'])) {
	$simulation_time = $_GET['simulate_time'];
}
switch ($function) {
case "do_unzip":
	if ($simulation == "") {
		$zip = new ZipArchive;
		if ($zip->open(update_get_current_path("update/OpenTacticalDispatcher.zip")) === true) {
			$zip->extractTo(update_get_current_path("/"));
			$zip->close();
			unlink(update_get_current_path("update/OpenTacticalDispatcher.zip"));
			unlink(update_get_current_path("update/md5sum.txt"));
			$json_array = array (
				"result" => "success",
				"text" => "Unzip update-file finished.",
				"simulation" => $simulation
			);
		} else {
			$json_array = array (
				"result" => "error",
				"text" => "Unzip update-file failed.",
				"simulation" => $simulation
			);
		}	
	} else {
		sleep($simulation_time);
		$json_array = array (
			"result" => "success",
			"text" => "Unzip update-file finished.",
			"simulation" => $simulation
		);
	}
	break;
case "do_changes":
	copy(update_get_current_path("db_credentials_old.inc.php"), update_get_current_path("incs/db_credentials.inc.php"));
	unlink(update_get_current_path("db_credentials_old.inc.php"));
	require (update_get_current_path("incs/db_credentials.inc.php"));
	require (update_get_current_path("incs/install.inc.php"));
	open_database($GLOBALS['db_host'], $GLOBALS['db_name'], $GLOBALS['db_user'], $GLOBALS['db_password']);
	if ($simulation == "") {
		$json_array = array (
			"result" => "success",
			"text" => "Make changes failed.",
			"simulation" => $simulation
		);
		require_once (update_get_current_path("update/changes.php"));
		if (do_changes_sql() && do_changes_files()) {
			unlink(update_get_current_path("install.php"));
			$json_array = array (
				"result" => "success",
				"text" => "Make changes finished.",
				"simulation" => $simulation
			);
		}
	} else {
		sleep($simulation_time);
		$json_array = array (
			"result" => "success",
			"text" => "Make changes finished.",
			"simulation" => $simulation
		);
	}

	$query_reset_update_progress_time = "UPDATE `settings` SET `value` = '' " .
		"WHERE `name` = '_update_progress_time';";

	update_db_query($query_reset_update_progress_time, __FILE__, __LINE__);
	close_database();
	break;
default:
}
print json_encode($json_array);
?>