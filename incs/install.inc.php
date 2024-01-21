<?php

function get_version() {
	return "24.04.1";
}

function open_database($host, $database, $user, $password) {
	try {
		$GLOBALS['DATABASE_LINK'] = null;
		$GLOBALS['DATABASE_LINK'] = new PDO("mysql:host=" . $host . ";dbname=" . $database , $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
		$GLOBALS['DATABASE_LINK']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
		$GLOBALS['DATABASE_LINK']->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br>";
		@error_log("Error!: " . $e->getMessage());
		die ();
	}
}

function close_database() {
	$GLOBALS['DATABASE_LINK'] = null;
}

function write_conf($host, $db, $user, $password) {
	if (!$fp = fopen('./incs/db_credentials.inc.php', 'a')) {
		return "<li><font class='warn'>Cannot open db_credentials.inc.php for writing</font></li>";
	} else {
		ftruncate($fp, 0);
		fwrite($fp, "<?php\n");
		fwrite($fp, '	$GLOBALS[\'db_host\'] 		= ' . "'" . $host . "';\n");
		fwrite($fp, '	$GLOBALS[\'db_name\'] 		= ' . "'" . $db . "';\n");
		fwrite($fp, '	$GLOBALS[\'db_user\'] 		= ' . "'" . $user . "';\n");
		fwrite($fp, '	$GLOBALS[\'db_password\'] 	= ' . "'" . $password . "';\n");
		fwrite($fp, '?>');
	}
	fclose($fp);
	return "<li>Wrote configuration to <b>./incs/db_credentials.inc.php</b></li>";
}

function do_sql_file($filename) {
	$import = file_get_contents($filename);
	$stmt = $GLOBALS['DATABASE_LINK']->prepare($import);
	$stmt->execute();
	$i = 0;
	do {
		$i++;
	} while ($stmt->nextRowset());
	$error_message = $stmt->errorInfo();
	if ($error_message[0] != "00000") {
		$line = __LINE__ - 7;
		$error_message = $GLOBALS['DATABASE_LINK']->errorInfo();
		@error_log($filename . ": query " . $i . "failed: " . $error_message[2] . " in " . basename(__FILE__) . " line " . $line . "\r\n" . $import);
		print "<span style='color:red'>" . $filename . ": query $i failed: " . $error_message[2] . " in " . basename(__FILE__) . " line " . $line . "</span><br>";
		die ();
	}
	
}

function write_version($version, $language) {
	if ($version != "") {

		$query = "UPDATE `settings` " .
			"SET `value` = '" . $version . "' " .
			"WHERE `name` = '_version';";

		$query .= "UPDATE `settings` " .
			"SET `value` = '" . $language . "' " .
			"WHERE `name` = '_locale';";

		$stmt = $GLOBALS['DATABASE_LINK']->prepare($query);
		$stmt->execute();
		$stmt->nextRowset();
		$error_message = $stmt->errorInfo();
		if ($error_message[0] != "00000") {
			$line = __LINE__ - 1;
			$error_message = $GLOBALS['DATABASE_LINK']->errorInfo();
			@error_log($error_message[2] . " in " . basename(__FILE__) . " line " . $line . "\r\n" . $query);
			print "<span style='color:red'>" . $error_message[2] . " in " . basename(__FILE__) . " line " . $line . "</span><br>";
			die ();
		}
	}
}

function install($version, $locale, $option, $host, $name, $user, $password) {
	if ($locale == "") {
		$option = "No locale available";
	}
	open_database($host, $name, $user, $password);
	$output_text = "";
	switch ($option) {
		case "install":	
			do_sql_file("./sql/database_settings.sql");
			do_sql_file("./sql/database_userdata.sql");	
			do_sql_file("./sql/drop_database_settings.sql");
			do_sql_file("./sql/drop_database_userdata.sql");
			do_sql_file("./sql/database_settings.sql");
			do_sql_file("./sql/database_userdata.sql");
			$output_text .= "<li> Installation of Database complete!";
			do_sql_file("./sql/settings." . $locale . ".sql");
			do_sql_file("./sql/captions." . $locale . ".sql");
			do_sql_file("./sql/hints." . $locale . ".sql");
			do_sql_file("./sql/regions." . $locale . ".sql");
			do_sql_file("./sql/default_users_and_tab_presets.sql");
			write_version($version, $locale);
			$output_text .= "<li> Installation of tables-data complete!";
			$output_text .= write_conf($host, $name, $user, $password);
			break;
		case "reset_settings":
			do_sql_file("./sql/drop_database_settings.sql");
			do_sql_file("./sql/database_settings.sql");
			write_version($version, $locale);
			do_sql_file("./sql/settings." . $locale . ".sql");
			do_sql_file("./sql/captions." . $locale . ".sql");
			do_sql_file("./sql/hints." . $locale . ".sql");
			$output_text .= "<li> Reset settings done!</li>";
			write_version($version, $locale);
			$output_text .= "<li> Write config done!</li>";
			$output_text .= write_conf($host, $name, $user, $password);
			break;
		case "write_credentials":
			$output_text .= write_conf($host, $name, $user, $password);
			break;
		default:
			$output_text .= "<li> <font class=\"warn\">'" . $option . "' is not a valid option!</font></li>";
			@error_log($output_text . "\r\n");
	}
	return $output_text;
}

function get_locale_select_str($current_sql_path, $current_locale) {
	$return_str = "<select name=\"frm_locale\">\r\n";
	$selected_str = "";
	$locale_array = array ();
	$locale = "";
	if (
		file_exists($current_sql_path . "database_settings.sql") &&
		file_exists($current_sql_path . "database_userdata.sql") &&
		file_exists($current_sql_path . "drop_database_settings.sql") &&
		file_exists($current_sql_path . "drop_database_userdata.sql") &&
		file_exists($current_sql_path . "default_users_and_tab_presets.sql")
	) {
		if ($dh = opendir($current_sql_path)) {
			if ($current_sql_path[strlen($current_sql_path) - 1] != "/") {
				$current_sql_path = $current_sql_path . "/";
			}
			while (($file = readdir($dh)) !== false) {
				if (
					($file != ".") && ($file != "..") && (!(is_dir($current_sql_path . $file))) &&
					(preg_match("/.*\.[a-z]{2,3}-((Cyrl-)|(Latn-)|(Mong-)|(Cans-))?([A-Z]{2}|(0-9){1,3})\.sql/", $file) == 1)
				) {
					$locale = substr(substr($file, 0, -4), -5);
					if (!(in_array($locale, $locale_array))) {
						array_push($locale_array, $locale);
					}
				}
			}
			closedir($dh);
		}
		foreach ($locale_array as $locale) {
			if (
					file_exists($current_sql_path . "captions." . $locale . ".sql") &&
					file_exists($current_sql_path . "hints." . $locale . ".sql") &&
					file_exists($current_sql_path . "regions." . $locale . ".sql") &&
					file_exists($current_sql_path . "settings." . $locale . ".sql")
			) {
				if ($locale == $current_locale) {
					$selected_str = " selected";
				}
				$return_str .= "<option value='" . $locale . "' " . $selected_str . ">" . $locale . "</option>\r\n";
				$selected_str = "";
			}
		}
	}
	$return_str .= "</select>\r\n";
	return $return_str;
}
?>