<?php

function callprogression_date_str($datetime_callprogression, $datetime_now) {
	$datetime_display = $datetime_now;
	if (is_datetime($datetime_callprogression)) {
		$datetime_display = $datetime_callprogression;
	}
	return date(trim(get_variable("date_format")), strtotime($datetime_display));
}

function callprogression_checked_str($callprogression) {
	if (is_datetime($callprogression)) {
		return " checked";
	} else {
		return "";
	}
}

function callprogression_lock_visible_str($callprogression) {
	if (is_datetime($callprogression)) {
		return "";
	} else {
		return " style=\"visibility: hidden\"";
	}
}

function callprogression_disabled_str($callprogression) {
	if (is_datetime($callprogression)) {
		return "";
	} else {
		return " disabled";
	}
}

function callprogression_input_type_str($callprogression) {
	if (is_datetime($callprogression)) {
		return "text";
	} else {
		return "hidden";
	}
}

function callprogression_is_cleared($row) {
	if (is_datetime($row)) {
		return "<font color='red'><b>" . get_text("Cleared") . "</b></font>";
	} else {
		return "";
	}
}

function get_start_end_facility_select_array($function, $facility_id, $start_end_location, $incident_location) {
	$return_array = array ();
	$return_array["select_str"] = "";
	$return_array["facility_address"] = "";
	$return_array["facility_coordinates"] = "";
	$id_str = "";
	$name_str = "";
	$onchange_str = "";
	$tabindex_str = "";
	$and_value = 0;
	$help_text_str = "";
	$use_incident_location_str = "";
	$facility_address_array_prefix = "";
	if ($facility_id =="") {
		$facility_id = 0;
	}
	switch ($function) {
		case "on_scene":
			$id_str = " id=\"frm_on_scene_facility_id\"";
			$name_str = " name=\"frm_on_scene_facility_id\"";
			$onchange_str = " onchange=\"do_facility_to_on_scene_location(this.options[selectedIndex].value.trim());\"";
			$tabindex_str = "";
			$and_value = 4;
			$help_text_str = get_help_text_str("_facy_on_scene");
			$selected_str = "";
			if ($facility_id == -1) {
				$selected_str = " SELECTED ";
			}
			$use_incident_location_str = "<option value=-1" . $selected_str . ">" . get_text("Use incident location") . "</option>\n";
			$facility_address_array_prefix = "on_scene";
			break;
		case "receiving":
			$id_str = " id=\"frm_receiving_facility_id\"";
			$name_str = " name=\"frm_receiving_facility_id\"";
			$onchange_str = " onchange=\"do_facility_to_receiving_location(this.options[selectedIndex].value.trim());\"";
			$tabindex_str = " tabindex=2";
			$and_value = 8;
			$help_text_str = get_help_text_str("_facy_rec");
			$use_incident_location_str = "";
			$facility_address_array_prefix = "receiving";
			break;
		default;
	}

	$query_facilities = "SELECT DISTINCT " .
		"`f`.`id` AS `fac_id`, " .
		"`f`.`handle` AS `handle`, " .
		"`f`.`street` AS `street`, " .
		"`f`.`city` AS `city`, " .
		"`f`.`type`, " .
		"`f`.`lat` AS `lat`, " .
		"`f`.`lng` AS `lng`, " .
		"`facility_types`.`name` AS `fac_type` " .
		"FROM `facilities` `f` " .
		"LEFT JOIN `allocates` ON (`f`.`id` = `allocates`.`resource_id`) " .
		"LEFT JOIN `facility_status` ON (`f`.`facility_status_id` = `facility_status`.`id`) " .
		"LEFT JOIN `facility_types` ON (`f`.`type` = `facility_types`.`id`) " .
		"WHERE ((`allocates`.`type` = " . $GLOBALS['TYPE_FACILITY'] . ") " .
		"AND (`facility_status`.`display` & " . $and_value . ")) " .
		"OR (`f`.`id` = " . $facility_id . ") " .
		"ORDER BY `f`.`type` ASC, `handle` ASC;";

	$result_facilities = db_query($query_facilities, __FILE__, __LINE__);
	$return_array["select_str"] .= "<select" . $help_text_str . " style=\"margin-top: 5px;\" class=\"sit label\"" . $id_str . $name_str . $onchange_str . $tabindex_str . ">";
	$return_array["select_str"] .= $use_incident_location_str;
	$selected_str = "";
	if ($facility_id == 0) {
		$selected_str = " SELECTED ";
	}
	$return_array["select_str"] .= "<option value=0" . $selected_str . ">" . get_text("Free input") . "</option>\n";
	$return_array["facility_coordinates"] .= $facility_address_array_prefix . "_fac_lat[" . 0 . "] = " . get_variable('_def_lat') . " + 0;\n"; //+0 against syntax error, in case of DB-row==null
	$return_array["facility_coordinates"] .= $facility_address_array_prefix . "_fac_lng[" . 0 . "] = " . get_variable('_def_lng') . " + 0;\n"; //+0 against syntax error, in case of DB-row==null
	if (db_num_rows($result_facilities) > 0) {
		$option_group = strval(rand());			//  force initial optgroup value
		$i = 0;
		while ($row_facility = db_fetch_array($result_facilities)) {
			if ($option_group != $row_facility['fac_type']) {
				if ($i != 0) {	
					$return_array["select_str"] .= "</optgroup>\n";
				}
				$option_group = $row_facility['fac_type'];
				$group_caption = get_text("No group name");
				if (remove_nls(trim($row_facility['fac_type'])) != "") {
					$group_caption = remove_nls($row_facility['fac_type']);
				}
				$return_array["select_str"] .= "<optgroup label='" . $group_caption . "'>\n";
			}
			$selected_str = "";
			if ($facility_id == $row_facility['fac_id']) {
				$selected_str = " SELECTED ";
			}
			$return_array["select_str"] .= "<option value=\"" . $row_facility['fac_id'] . "\"" . $selected_str . ">" . remove_nls($row_facility['handle']) . "</option>\n";
			$return_array["facility_address"] .= "\t" . $facility_address_array_prefix . "_facility_adress[" . $row_facility['fac_id'] . "] = '" . remove_nls($row_facility['street'] . ", " . $row_facility['city']) . "';\n";
			$return_array["facility_coordinates"] .= $facility_address_array_prefix . "_fac_lat[" . $row_facility['fac_id'] . "] = " . remove_nls($row_facility['lat']) . " + 0;\n"; //+0 against syntax error, in case of DB-row==null
			$return_array["facility_coordinates"] .= $facility_address_array_prefix . "_fac_lng[" . $row_facility['fac_id'] . "] = " . remove_nls($row_facility['lng']) . " + 0;\n"; //+0 against syntax error, in case of DB-row==null
			$i++;
		}
		$return_array["select_str"] .= "\n</optgroup>\n";
		$return_array["select_str"] .= "\n</select>\n";
		unset ($result_facilities);
	} else {
		$return_array["select_str"] .= "\t<option disabled>" . get_text("No facilities available!") . "</option>\n";
	}
	if ($facility_id > 0) {
		$return_array["start_end_location"] = remove_nls($start_end_location);
		$return_array["readonly_str"] = " readonly";
	} else {
		if ($facility_id == 0) {
			$return_array["start_end_location"] = remove_nls($start_end_location);
			$return_array["readonly_str"] = "";
		} else {
			$return_array["start_end_location"] = remove_nls($incident_location);
			$return_array["readonly_str"] = " readonly";
		}
	}
	return $return_array;
}
?>