<?php
error_reporting(E_ALL);
session_start();	
require_once ("./incs/functions.inc.php");
require_once ("./incs/configuration.inc.php");
do_login(basename(__FILE__));

$delimiter_text = "\"";
$delimiter_field = ";";

if (empty ($_GET['do_export']) || !(is_super())) {
	exit ();
} else {
	$output = "OpenTacticalDispatcher ". get_variable("_version") . "\n" .
		"Export-file " . $_GET['do_export'] . "\n\n";
	$filename = "";
	switch ($_GET['do_export']) {
	case ("units"):
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "type" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "description" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "bg_color" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "text_color" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "delete(yes)" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `name`, " .
			"`description`, " .
			"`bg_color`, " .
			"`text_color`, " .
			"`updated` " .
			"FROM `unit_types`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$output .= $delimiter_text . "unit_types" . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $row["name"] . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $row["description"] . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . color_name_to_hex($row["bg_color"]) . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . color_name_to_hex($row["text_color"]) . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $delimiter_text; //empty delete(yes) column
			$output .= "\n";
		}
		$output .= "\n";
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "status" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "description" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "dispatch" . $delimiter_text . $delimiter_field;	
		$output .= $delimiter_text . "not_used" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "sort" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "bg_color" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "text_color" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "delete(yes)" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `status_name`, " .
			"`description`, " .
			"`dispatch`, " .
			"`sort`, " .
			"`bg_color`, " .
			"`text_color` " .
			"FROM `unit_status`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$output .= $delimiter_text . "unit_status" . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $row["status_name"] . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $row["description"] . $delimiter_text . $delimiter_field;
			switch ($row["dispatch"]) {
			case $GLOBALS['DISPATCH_YES']:
				$output .= $delimiter_text . "yes" . $delimiter_text . $delimiter_field;
				break;
			case $GLOBALS['DISPATCH_ENFORCEABLE']:
				$output .= $delimiter_text . "dispatch_enforceable" . $delimiter_text . $delimiter_field;
				break;
			case $GLOBALS['DISPATCH_NOT_ENFORCEABLE']:
				$output .= $delimiter_text . "no_dispatch" . $delimiter_text . $delimiter_field;
				break;
			case $GLOBALS['DISPATCH_MONITOR']:
				$output .= $delimiter_text . "monitor" . $delimiter_text . $delimiter_field;
				break;
			case $GLOBALS['DISPATCH_NO_EVALUATION']:
				$output .= $delimiter_text . "no_evaluation" . $delimiter_text . $delimiter_field;
				break;
			default:
				$output .= $delimiter_text . "yes" . $delimiter_text . $delimiter_field;
			}
			$output .= $delimiter_text . "" . $delimiter_text . $delimiter_field;	
			$output .= $delimiter_text . $row["sort"] . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . color_name_to_hex($row["bg_color"]) . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . color_name_to_hex($row["text_color"]) . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $delimiter_text; //empty delete(yes) column
			$output .= "\n";
		}
		$output .= "\n";
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "handle" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "remote data service" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "name" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "icon" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "type" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "mobile" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "multiple" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "regions" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "guard_house" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "parent" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "admin_only" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "description" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "capability" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "phone" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "contact name" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "email" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "delete(yes)" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `u`.`id`, " .
			"`u`.`handle`, " .
			"`u`.`remote_data_services`, " .
			"`u`.`name`, " .
			"`u`.`icon_url`, " .
			"`u_t`.`name` AS `typename`, " .
			"`u`.`mobile`, " .
			"`u`.`multi`, " .
			"`u`.`guard_house_id`, " .
			"`u`.`admin_only`, " .
			"`u`.`unit_phone`, " .
			"`u`.`description`, " .
			"`u`.`capabilities`, " .
			"`u`.`contact_name`, " .
			"`u`.`unit_email`, " .
			"`u`.`updated`, " .
			"`f`.`handle` AS `guard_house_handle` " .
			"FROM `units` `u` " .
			"LEFT JOIN `unit_types` `u_t` on (`u`.`type` = `u_t`.`id`) " .
			"LEFT JOIN `facilities` `f` on (`u`.`guard_house_id` = `f`.`id`);";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_array($result))) {

			$query_allocates = "SELECT `r`.`region_name` " .
				"FROM `allocates` `a` " .
				"LEFT JOIN `regions` `r` ON (`r`.`id` = `a`.`group`) " .
				"WHERE (`a`.`resource_id` = " . $row["id"] . ") " .
				"AND (`a`.`type` = " . $GLOBALS['TYPE_UNIT'] . ");";

			$result_allocates = db_query($query_allocates, __FILE__, __LINE__);
			$columns_allocates = db_num_rows($result_allocates);
			if ($columns_allocates > 0) {
				$output .= $delimiter_text . "units" . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["handle"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["remote_data_services"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["name"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["icon_url"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["typename"] . $delimiter_text . $delimiter_field;
				if ($row["mobile"] == 1) {
					$output .= $delimiter_text . "yes" . $delimiter_text . $delimiter_field;
				} else {
					$output .= $delimiter_text . "no" . $delimiter_text . $delimiter_field;
				}
				$output_multi = $delimiter_text . "none" . $delimiter_text . $delimiter_field;
				if ($row["multi"] == 1) {
					$output_multi = $delimiter_text . "no" . $delimiter_text . $delimiter_field;
				}
				if ($row["multi"] == 2) {
					$output_multi = $delimiter_text . "yes" . $delimiter_text . $delimiter_field;
				}
				$output .= $output_multi;
				$output .= $delimiter_text;
				$regions = "";
				while ($row_allocates = stripslashes_deep(db_fetch_array($result_allocates))) {
					$regions .= $row_allocates['region_name'] . "/";
				}
				$output .= substr($regions, 0, -1);
				$output .= $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["guard_house_handle"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . "" . $delimiter_text . $delimiter_field;	//parent_id
				if ($row["admin_only"] == 1) {
					$output .= $delimiter_text . "yes" . $delimiter_text . $delimiter_field;
				} else {
					$output .= $delimiter_text . "no" . $delimiter_text . $delimiter_field;
				}
				$output .= $delimiter_text . $row["description"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["capabilities"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["unit_phone"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["contact_name"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["unit_email"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $delimiter_text; //empty delete(yes) column
				$output .= "\n";
			}
		}
		$filename = "units.csv";
		break;
	case "facilities":
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "type" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "description" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "bg_color" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "text_color" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "delete(yes)" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `name`, " .
			"`description`, " .
			"`bg_color`, " .
			"`text_color`, " .
			"`updated` " .
			"FROM `facility_types`;";

		$result = db_query($query, __FILE__, __LINE__);

		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$output .= $delimiter_text . "facility_types" . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $row["name"] . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $row["description"] . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . color_name_to_hex($row["bg_color"]) . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . color_name_to_hex($row["text_color"]) . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $delimiter_text; //empty delete(yes) column
			$output .= "\n";
		}
		$output .= "\n";
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "status" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "description" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "display" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "sort" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "bg_color" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "text_color" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "delete(yes)" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `status_name`, " .
			"`description`, " .
			"`display`, " .
			"`sort`, " .
			"`bg_color`, " .
			"`text_color` " .
			"FROM `facility_status`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$output .= '"facility_status";';
			$output .= $delimiter_text . $row["status_name"] . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $row["description"] . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $row["display"] . $delimiter_text . $delimiter_field;	
			$output .= $delimiter_text . $row["sort"] . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . color_name_to_hex($row["bg_color"]) . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . color_name_to_hex($row["text_color"]) . $delimiter_text . $delimiter_field;
			$output .= $delimiter_text . $delimiter_text; //empty delete(yes) column
			$output .= "\n";
		}
		$output .= "\n";
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "handle" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "name" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "icon" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "type" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "regions" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "location" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "city" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "admin_only" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "description" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "capability" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "contact name" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "contact email" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "contact phone" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "security contact" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "security email" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "security phone" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "opening hours" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "access rules" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "boundary" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "direct dialing 1" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "direct dialing 2" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "lat" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "lng" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "object_id" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "updated" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "delete(yes)" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `f`.`id`, " .
			"`f`.`handle`, " .
			"`f`.`name`, " .
			"`f`.`icon_url`, " .
			"`f_t`.`name` as `typename`, " .
			"`f`.`street`, " .
			"`f`.`city`, " .
			"`f`.`admin_only`, " .
			"`f`.`description`, " .
			"`f`.`capabilities`, " .
			"`f`.`contact_name`, " .
			"`f`.`contact_email`, " .
			"`f`.`contact_phone`, " .
			"`f`.`security_contact`, " .
			"`f`.`security_email`, " .
			"`f`.`security_phone`, " .
			"`f`.`opening_hours`, " .
			"`f`.`access_rules`, " .
			"`f`.`boundary`, " .
			"`f`.`direct_dialing_1`, " .
			"`f`.`direct_dialing_2`, " .
			"`f`.`lat`, " .
			"`f`.`lng`, " .
			"`f`.`object_id`, " .
			"`f`.`updated` " .
			"FROM `facilities` `f` " .
			"LEFT JOIN `facility_types` `f_t` ON (`f`.`type` = `f_t`.`id`);";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_array($result))) {

			$query_allocates = "SELECT `r`.`region_name` " .
				"FROM `allocates` `a` " .
				"LEFT JOIN `regions` `r` ON (`r`.`id` = `a`.`group`) " .
				"WHERE (`a`.`resource_id` = " . $row["id"] . ") " .
				"AND (`a`.`type` = " . $GLOBALS['TYPE_FACILITY'] . ");";

			$result_allocates = db_query($query_allocates, __FILE__, __LINE__);
			$columns_allocates = db_num_rows($result_allocates);
			if ($columns_allocates > 0) {
				$output .= $delimiter_text . "facilities" . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["handle"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["name"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["icon_url"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["typename"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text;
				$regions = "";
				while ($row_allocates = stripslashes_deep(db_fetch_array($result_allocates))) {
					$regions .= $row_allocates['region_name'] . "/";
				}
				$output .= substr($regions, 0, -1);
				$output .= $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["street"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["city"] . $delimiter_text . $delimiter_field;
				if ($row["admin_only"] == 1) {
					$output .= $delimiter_text . "yes" . $delimiter_text . $delimiter_field;
				} else {
					$output .= $delimiter_text . "no" . $delimiter_text . $delimiter_field;
				}
				$output .= $delimiter_text . $row["description"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["capabilities"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["contact_name"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["contact_email"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["contact_phone"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["security_contact"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["security_email"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["security_phone"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["opening_hours"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["access_rules"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["boundary"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["direct_dialing_1"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["direct_dialing_2"] . $delimiter_text . $delimiter_field;
				if ($row["lat"] == '0.999999') {
					$output .= '"";';
				} else {
					$output .= $delimiter_text . $row["lat"] . $delimiter_text . $delimiter_field;
				}
				if ($row["lng"] == '0.999999') {
					$output .= '"";';
				} else {
					$output .= $delimiter_text . $row["lng"] . $delimiter_text . $delimiter_field;
				}
				$output .= $delimiter_text . $row["object_id"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row["updated"] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $delimiter_text; //empty delete(yes) column
				$output .= "\n";
			}
		}
		$filename = "facilities.csv";
		break;
	case "textblocks":

		function get_textblocks ($name, $type) {
			global $delimiter_text, $delimiter_field;

			$query = "SELECT `text`, " .
				"`group`, " .
				"`sort` " .
				"FROM `textblocks` " .
				"WHERE `type`= '" . $type . "';";

			$result = db_query($query, __FILE__, __LINE__);
			$columns_total = db_num_fields($query);
			$lines = "";
			while ($row = stripslashes_deep(db_fetch_array($result))) {
				$lines  .= $delimiter_text . $name . $delimiter_text . $delimiter_field;
				for ($i = 0; $i < $columns_total; $i++) {
					$lines .= $delimiter_text . $row["$i"] . $delimiter_text . $delimiter_field;
				}
				$lines .= $delimiter_text . $delimiter_text; //empty delete(yes) column
				$lines .= "\n";
			}
			return ($lines);
		}

		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "textblock" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "group" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "sort" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "delete(yes)" . $delimiter_text . $delimiter_field;
		$output .="\n";
		$output .= get_textblocks("textblocks_syn", "synopsis");
		$output .= get_textblocks("textblocks_desc", "description");
		$output .= get_textblocks("textblocks_act", "action");
		$output .= get_textblocks("textblocks_ass", "assigns");
		$output .= get_textblocks("textblocks_clo", "close");
		$output .= get_textblocks("textblocks_log", "log");
		$output .= get_textblocks("textblocks_msg", "message");
		$filename = "textblocks.csv";
		break;
	case "nature":
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "incident_type" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "severity" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "group" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "sort" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "description" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "protocol" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "delete(yes)" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `type`, " .
			"`set_severity`, " .
			"`group`, " .
			"`sort`, " .
			"`description`, " .
			"`protocol` " .
			"FROM `incident_types`;";

		$result = db_query($query, __FILE__, __LINE__);
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			if ($row['group'] != "DELETED") {
				$output .= $delimiter_text . "incident_types" . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row['type'] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text;
				switch($row['set_severity']) {
				case 0:
					$output .= "normal";
					break;
				case 1:
					$output .= "medium";
					break;
				case 2:
					$output .= "high";
					break;
				default:
					$output .= "normal";
					break;
				}
				$output .= $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row['group'] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row['sort'] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row['description'] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $row['protocol'] . $delimiter_text . $delimiter_field;
				$output .= $delimiter_text . $delimiter_text; //empty delete(yes) column
				$output .= "\n";
			}
		}
		$filename = "incident_types.csv";
		break;
	case "captions":
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "tag" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "text" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `capt`, " .
			"`repl` " .
			"FROM `captions`;";

		$result = db_query($query, __FILE__, __LINE__);
		$columns_total = db_num_fields($query);
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$output  .= $delimiter_text . "captions" . $delimiter_text . $delimiter_field;
			for ($i = 0; $i < $columns_total; $i++) {
				$output .= $delimiter_text . $row["$i"] . $delimiter_text . $delimiter_field;
			}
			$output .= $delimiter_text . $delimiter_text;
			$output .= "\n";
		}

		$query = "SELECT `tag`, " .
			"`hint` " .
			"FROM `hints`;";

		$result = db_query($query, __FILE__, __LINE__);
		$columns_total = db_num_fields($query);
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$output  .= $delimiter_text . "hints" . $delimiter_text . $delimiter_field;
			for ($i = 0; $i < $columns_total; $i++) {
				$output .= $delimiter_text . $row["$i"] . $delimiter_text . $delimiter_field;
			}
			$output .= "\n";
		}
		$filename = "captions_hints.csv";
		break;
	case "settings":
		$do_not_export = array ("_update_progress_time", "_version", "_locale", "_vowel_mutation", "_api_status", "_api_phone_status");
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "variable" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "value" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `name`, " .
			"`value` " .
			"FROM `settings`;";

		$result = db_query($query, __FILE__, __LINE__);
		$columns_total = db_num_fields($query);		
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			if (in_array($row['name'], $do_not_export) == false) {
				$output  .= $delimiter_text . "settings" . $delimiter_text . $delimiter_field;
				for ($i = 0; $i < $columns_total; $i++) {
					$output .= $delimiter_text . $row["$i"] . $delimiter_text . $delimiter_field;
				}
				$output .= "\n";
			}
		}
		$filename = "settings" . ".csv";
		break;
	case "user":
		$output .= $delimiter_text . "import" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "user" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "password" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "level" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "email" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "regions" . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . "delete(yes)" . $delimiter_text . $delimiter_field;
		$output .= "\n";

		$query = "SELECT `name`, " .
			"`password`, " .
			"`level`, " .
			"`email` " .
			"FROM `users` " .
			"WHERE `password` != '55606758fdb765ed015f0612112a6ca7';";

		$result = db_query($query, __FILE__, __LINE__);
		$columns_total = db_num_fields($query);
		while ($row = stripslashes_deep(db_fetch_array($result))) {
			$output  .= $delimiter_text . "users" . $delimiter_text . $delimiter_field;
			for ($i = 0; $i < $columns_total; $i++) {
				$output .= $delimiter_text . $row["$i"] . $delimiter_text . $delimiter_field;
			}
			$output .= $delimiter_text . $delimiter_text;
			$output .= "\n";
		}
		$filename = "users" . ".csv";
		break;
	case "log":
//Datenbankabfrage mit oder ohne einsätze (nur ETB oder alles=incidents=yes), definition von anfang und ende nötig? wenn nicht, dann alles!
		$output .= $delimiter_text . get_text("Table") . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . get_text("DateTime") . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . get_text("Code") . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . get_text("inc_name_short") . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . get_text("Unit") . "/" . get_text("Facility") . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . get_text("Text") . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . get_text("by") . $delimiter_text . $delimiter_field;
		$output .= $delimiter_text . get_text("Client address") . $delimiter_text . $delimiter_field;
		$output .= "\n";
		if (empty ($_GET['start'])) {

		} else {

		}
		if (empty ($_GET['end'])) {

		} else {

		}
//abfrage
//zusammenbauen
		if (empty ($_GET['incidents'])) {

		} else {

		}
//ende zusammenbauen
		$filename = get_text("log") . ".csv";
		break;
	default:
		exit;
	}
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$filename = html_entity_decode($filename);
	$output = html_entity_decode($output, ENT_QUOTES, "UTF-8");
	if (preg_match('/Win/', $agent)) {
		$filename = mb_convert_encoding($filename, "ISO-8859-15", "UTF-8");
		$output = mb_convert_encoding($output, "ISO-8859-15", "UTF-8");
	}
	header("Pragma: public");
	header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
	header('Content-type: text/csv');
	header('Content-Disposition: attachment; filename=' . $filename);
	header("Content-Description: File Transfer");
	header('Content-length: ' . @strlen($output));
	echo $output;
}
exit;
?>