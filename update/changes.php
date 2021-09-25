<?php

function do_changes_sql() {
	open_database($GLOBALS['db_host'], $GLOBALS['db_name'], $GLOBALS['db_user'], $GLOBALS['db_password']);

	$query_locale = "SELECT `value` " .
		"FROM `settings` " .
		"WHERE `name` = '_locale';";

	$result_locale = update_db_query($query_locale, __FILE__, __LINE__);
	$row_locale = $result_locale->fetch(PDO::FETCH_BOTH);
	if (isset ($row_locale["value"]) && (file_exists("update/changes." . $row_locale["value"] . ".sql"))) {
		do_sql_file(update_get_current_path("update/changes." . $row_locale["value"] . ".sql"));
	} else {
		do_sql_file(update_get_current_path("update/changes.de-DE.sql"));
	}
	write_version(get_version(), $row_locale["value"]);
	return true;
}

function do_changes_files() {
	$unlink_array = array (
		"incs/usng.inc.php",	//main-folder: to_unlink_file.php
		""	//sub-folder: folder/to_unlink_file.php
	);
	foreach ($unlink_array as $filename) {
		if (($filename != "") && (file_exists(update_get_current_path($filename)))) {
			unlink(update_get_current_path($filename));
		}
	}
	return true;
}
?>