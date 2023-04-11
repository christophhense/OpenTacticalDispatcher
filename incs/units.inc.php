<?php

function get_units_data($where_str, $order_str = "", $limit_str = "", $offset_str = "") {

	$query = "SELECT " .
		"UNIX_TIMESTAMP(`u`.`updated`) AS `updated`, " .
		"`t`.`id` AS `type_id`, " .
		"`t`.`bg_color` AS `background_color`, " .
		"`t`.`text_color` AS `text_color`, " .
		"`t`.`description` AS `type_descr`, " .
		"`t`.`name` AS `type_name`, " .
		"`u`.`id` AS `unit_id`, " .
		"`u`.`name` AS `unit_name`, " .
		"`u`.`handle` AS `handle`, " .
		"`u`.`description` AS `unit_descr`, " .
		"`u`.`capabilities`, " .
		"`u`.`multi`, " .
		"`u`.`contact_name`, " .
		"`u`.`unit_phone`, " .
		"`u`.`unit_email`, " .
		"`u`.`remote_data_services`, " .
		"`u`.`unit_status_id`, " .
		"`u`.`admin_only`, " .
		"`s`.`description` AS `stat_descr`, " .
		"`s`.`sort` AS `stat_sort`, " .
		"`s`.`dispatch` AS `stat_dispatch`, " .
		"(SELECT COUNT(*) as `numfound` FROM `assigns` " .
		"WHERE `assigns`.`unit_id` = `unit_id` " .
		"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00')) AS `nr_assigned`, " .
		"`f`.`handle` AS `guard_house_handle`, " .
		"`f`.`street` AS `guard_house_street`, " .
		"`f`.`city` AS `guard_house_city` " .
		"FROM `units` `u` " .
		"LEFT JOIN `allocates` `a` ON (`u`.`id` = `a`.`resource_id`) " .
		"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
		"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
		"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
		$where_str . " " .
		"GROUP BY `unit_id`" . $order_str . $limit_str . $offset_str . ";";

	return db_query($query, __FILE__, __LINE__);
}

function show_units_legend() {

	$query = "SELECT DISTINCT `type`, " .
		"`unit_types`.`bg_color`, " .
		"`unit_types`.`text_color`, " .
		"`unit_types`.`name` AS `unit_types_name`, " .
		"`unit_types`.`description` AS `unit_types_description` " .
		"FROM `units` " .
		"LEFT JOIN `unit_types` ON `unit_types`.`id` = `units`.`type` " .
		"ORDER BY `unit_types_name`;";

	$result = db_query($query, __FILE__, __LINE__);

	$output_str = "<span align='center'><span align='center'> " . get_text("Units legend") . ": </span>&nbsp;";
	while ($row = stripslashes_deep(db_fetch_assoc($result))) {
		$output_str .= "<span class='label' style='background-color: " . $row['bg_color'] . "; color: " . $row['text_color'] . ";'" .
			get_title_str($row['unit_types_description']) . "> " . remove_nls($row['unit_types_name']) . " </span>&nbsp;";
	}
	print $output_str . "</span>";
}

function show_units_sortbar() {
	if (empty ($_SESSION['units_sort_order'])) {
		$_SESSION['units_sort_order'] = get_variable("sort_units");
	}
	$checked = array ("", "", "", "");
	$checked[$_SESSION['units_sort_order']] = " checked";
	?>
	<div style="text-align: center;">
		<div>
			<?php show_units_legend();?>
		</div>
		<div style="margin: 2px;">
			<label class="radio-inline"><?php print get_text("Sort");?>: </label>
			<label class="radio-inline">
				<input type=radio name="frm_units_order" value=1 <?php print $checked[1];?> onclick="do_sort_units(this.value);"><?php print get_text("Name");?>
			</label>
			<label class="radio-inline">
				<input type=radio name="frm_units_order" value=2 <?php print $checked[2];?> onclick="do_sort_units(this.value);"><?php print get_text("Type");?>
			</label>
			<label class="radio-inline">
				<input type=radio name="frm_units_order" value=3 <?php print $checked[3];?> onclick="do_sort_units(this.value);"><?php print get_text("Status");?>
			</label>
		</div>
	</div>
	<?php
}

function show_unit_types_select($unit_type = 0) {

	$query_unit_types = "SELECT * " .
		"FROM `unit_types` " .
		"ORDER BY `id`;";

	$result_unit_types = db_query($query_unit_types, __FILE__, __LINE__);
	$style_str = "";
	if (db_affected_rows($result_unit_types) > 0) {
		$style_str = "";
		if ($unit_type) {

			$query_selected_unit_type = "SELECT `bg_color`, " .
				"`text_color` " .
				"FROM `unit_types` " .
				"WHERE `id` = " . $unit_type . ";";

			$result_selected_unit_type = db_query($query_selected_unit_type, __FILE__, __LINE__);
			$row_selected_unit_type = stripslashes_deep(db_fetch_assoc($result_selected_unit_type));
			$style_str = " style=' background-color: " . $row_selected_unit_type['bg_color'] . "; color: " . $row_selected_unit_type['text_color'] . ";'";
		}
		?>
<select id="frm_type" name="frm_type" class="form-control mandatory" tabindex=11<?php print $style_str;?> onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor; this.style.color=this.options[this.selectedIndex].style.color;">
	<?php
		if ($unit_type == 0) {
	?>
	<option value=0 selected><?php print get_text("Select");?></option>
	<?php
		}
		while ($row_unit_types = stripslashes_deep(db_fetch_assoc($result_unit_types))) {
			$selected_str = "";
			if ($unit_type == $row_unit_types['id']) {
				$selected_str = " selected";
			}
			print "\t\t<option value=" . $row_unit_types['id'] . $selected_str . " style='background-color: " . $row_unit_types['bg_color'] . "; color: " . $row_unit_types['text_color'] . ";' >" . remove_nls($row_unit_types['name']) . "</option>\n";
		}
		unset ($result_selected_unit_type);
	} else {
	?>
<select id="frm_type" name="frm_type" class="form-control" style="background-color: #000000; color: #FFFFFF;">
	<option value=0><?php print get_text("No data");?></option>
	<?php
	}
	unset ($result_unit_types);
	?>
</select>
	<?php
}

function show_unit_status_select($unit_status, $num_of_tickets = 0, $multi = 1) {

	$query_unit_status = "SELECT * " .
		"FROM `unit_status` " .
		"ORDER BY `status_name` ASC, `sort` ASC;";

	$result_unit_status = db_query($query_unit_status, __FILE__, __LINE__);
	if (db_affected_rows($result_unit_status) > 0) {
		$frm_status_updated_str = "";
		$style_str = "";
		if ($num_of_tickets == 0) {
			if ($unit_status > 0) {

				$query_selected_unit_status = "SELECT `bg_color`, " .
					"`text_color` " .
					"FROM `unit_status` " .
					"WHERE `id` = " . $unit_status . ";";

				$result_selected_unit_status = db_query($query_selected_unit_status, __FILE__, __LINE__);
				$row_selected_unit_status = stripslashes_deep(db_fetch_assoc($result_selected_unit_status));
				$style_str = " style=' background-color: " . $row_selected_unit_status['bg_color'] . "; color: " . $row_selected_unit_status['text_color'] . ";'";
				$frm_status_updated_str = " document.units_edit_form.frm_status_update.value='1';";
				unset ($result_selected_unit_status);
			}
		}
	?>
<select id="frm_un_status_id" name="frm_un_status_id" class="form-control mandatory" tabindex=12<?php print $style_str;?> onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor; this.style.color=this.options[this.selectedIndex].style.color;<?php print $frm_status_updated_str;?>;" <?php print $num_of_tickets;?>>
	<?php
		if ($unit_status == 0) {
	?>
	<option value=0 selected><?php print get_text("Select");?></option>
	<?php
		}
		$i = 0;
		while ($row_unit_status = stripslashes_deep(db_fetch_array($result_unit_status))) {
			$selected_str = "";
			if (($unit_status == $row_unit_status['id']) && ($num_of_tickets == 0)) {
				$selected_str = " selected";
			}
			if ($num_of_tickets > 0) {
				if ($i == 0) {
					if ($num_of_tickets > 1) {
						print "\t\t<option selected>" . $num_of_tickets . " " . get_text("Tickets") . "</option>\n";
					} else {
						if ($multi == 2) {
							print "\t\t<option selected>1 " . get_text("Incident") . "</option>\n";
						} else {
							print "\t\t<option selected>" . get_text("Dispatched") . "</option>\n";
						}
					}	
				}
			} else {
				print "\t\t<option value=" . $row_unit_status['id'] . $selected_str . " " .
					"style='background-color: " . $row_unit_status['bg_color'] . "; " .
					"color: " . $row_unit_status['text_color'] . ";' >" .
					remove_nls($row_unit_status['status_name']) . "</option>\n";
			}
			$i++;
		}
	} else {
	?>
<select id="frm_un_status_id" name="frm_un_status_id" class="form-control" style="background-color: #000000; color: #FFFFFF;">
	<option value=0><?php print get_text("No data");?></option>
	<?php
	}
	unset ($result_unit_status);
	?>
</select>
	<?php
}

function show_multiple_select($selected = 1) {
	$select_str_0 = $select_str_1 = $select_str_2 = "";
	switch ($selected) {
	case 0:
		$select_str_0 = " selected";
		break;
	case 1:
		$select_str_1 = " selected";
		break;
	case 2:
		$select_str_2 = " selected";
		break;
	default:
	}
	?>
<select id="frm_multi" name="frm_multi" class="form-control" tabindex=13>
	<option value=0<?php print $select_str_0;?>><?php print get_text("Not dispatchable");?></option>
	<option value=1<?php print $select_str_1;?>><?php print get_text("Once dispatchable");?></option>
	<option value=2<?php print $select_str_2;?>><?php print get_text("Multiple dispatchable");?></option>
</select>
	<?php
}

function get_unit_edit_log_text($function, $id, $values_new, $values_old) {
	$log_text = "";
	switch ($function) {
	case "add":
		$log_text .= get_text("TBL_ID") . ": #" . $id;
		if (trim($values_new['frm_handle']) != "") {
			$log_text .= "  " . get_text("Unit handle") . ": " . trim($values_new['frm_handle']);
		}
		if (trim($values_new['frm_name']) != "") {
			$log_text .= "  " . get_text("Unit name") . ": " . trim($values_new['frm_name']);
		}
		if (trim($values_new['frm_smsg_id']) != "") {
			$log_text .= "  " . get_text("Remote data services") . ": " . trim($values_new['frm_smsg_id']);
		}
		if (trim($values_new['frm_phone']) != "") {
			$log_text .= "  " . get_text("Cellular phone") . ": " . trim($values_new['frm_phone']);
		}
		if (trim($values_new['frm_unit_email']) != "") {
			$log_text .= "  " . get_text("Email") . ": " . trim($values_new['frm_unit_email']);
		}
		if ($values_new['frm_type'] != 0) {
			$log_text .= "  " . get_text("Type") . ": " . get_unit_type_name($values_new['frm_type']);
		}
		if ($values_new['frm_un_status_id'] != 0) {
			$log_text .= "  " . get_text("Unit status") . ": " . get_unit_status_name($values_new['frm_un_status_id']);
		}
		$log_text .= "  " . get_text("Dispatchable") . ": ";
		switch ($values_new['frm_multi']) {
		case 1:
			$log_text .= get_text("Once dispatchable");
			break;
		case 2:
			$log_text .= get_text("Multiple dispatchable");
			break;
		default:
			$log_text .= get_text("Not dispatchable");
		}
		if ($values_new['frm_guard_house'] != 0) {
			$log_text .= "  ". get_text("Guard house") . ": " . get_facility_handle($values_new['frm_guard_house']);
		}
		if (trim($values_new['frm_descr']) != "") {
			$log_text .= "  " . get_text("Description") . ": " . trim($values_new['frm_descr']);
		}
		if (trim($values_new['frm_capab']) != "") {
			$log_text .= "  " . get_text("Capability") . ": " . trim($values_new['frm_capab']);
		}
		if (trim($values_new['frm_contact_name']) != "") {
			$log_text .= "  " . get_text("Contact name") . ": " . trim($values_new['frm_contact_name']);
		}
		$log_text .= "  " . get_text("Admin permission") . ": ";
		switch ($values_new['frm_adminperms']) {
		case 1:
			$log_text .= get_text("Superadmin only");
			break;
		default:
			$log_text .= get_text("Admin and superadmin");
		}
		break;
	case "update":
		if (trim($values_new['frm_handle']) != trim($values_old['handle'])) {
			$log_text .= "  " . get_text("Unit handle") . ": " . $values_old['handle'] . " => " . trim($values_new['frm_handle']);
		}
		if (trim($values_new['frm_name']) != trim($values_old['name'])) {
			$log_text .= "  " . get_text("Unit name") . ": " . $values_old['name'] . " => " . trim($values_new['frm_name']);
		}
		if (trim($values_new['frm_smsg_id']) != trim($values_old['remote_data_services'])) {
			$log_text .= "  " . get_text("Remote data services") . ": " . $values_old['remote_data_services'] . " => " . trim($values_new['frm_smsg_id']);
		}
		if (trim($values_new['frm_phone']) != trim($values_old['unit_phone'])) {
			$log_text .= "  " . get_text("Cellular phone") . ": " . $values_old['unit_phone'] . " => " . trim($values_new['frm_phone']);
		}
		if (trim($values_new['frm_unit_email']) != trim($values_old['unit_email'])) {
			$log_text .= "  " . get_text("Email") . ": " . $values_old['unit_email'] . " => " . trim($values_new['frm_unit_email']);
		}
		if ($values_new['frm_type'] != $values_old['type']) {
			$log_text .= "  " . get_text("Type") . ": " . get_unit_type_name($values_old['type']) . " => " . get_unit_type_name($values_new['frm_type']);
		}
		if ($values_new['frm_un_status_id'] != $values_old['unit_status_id']) {
			$log_text .= "  " . get_text("Unit status") . ": " . get_unit_status_name($values_old['unit_status_id']) . " => " . get_unit_status_name($values_new['frm_un_status_id']);
		}
		if ($values_new['frm_multi'] != $values_old['multi']) {
			$log_text .= "  " . get_text("Dispatchable") . ": ";
			switch ($values_old['multi']) {
			case 1:
				$log_text .= get_text("Once dispatchable") . " => ";
				break;
			case 2:
				$log_text .= get_text("Multiple dispatchable") . " => ";
				break;
			default:
				$log_text .= get_text("Not dispatchable") . " => ";
			}
			switch ($values_new['frm_multi']) {
			case 1:
				$log_text .= get_text("Once dispatchable");
				break;
			case 2:
				$log_text .= get_text("Multiple dispatchable");
				break;
			default:
				$log_text .= get_text("Not dispatchable");
			}
		}
		if ($values_new['frm_guard_house'] != $values_old['guard_house_id']) {
			$log_text .= "  ". get_text("Guard house") . ": " . get_facility_handle($values_old['guard_house_id']) . " => " . get_facility_handle($values_new['frm_guard_house']);
		}
		if (trim($values_new['frm_descr']) != trim($values_old['description'])) {
			$log_text .= "  " . get_text("Description") . ": " . $values_old['description'] . " => " . trim($values_new['frm_descr']);
		}
		if (trim($values_new['frm_capab']) != trim($values_old['capabilities'])) {
			$log_text .= "  " . get_text("Capability") . ": " . $values_old['capabilities'] . " => " . trim($values_new['frm_capab']);
		}
		if (trim($values_new['frm_contact_name']) != trim($values_old['contact_name'])) {
			$log_text .= "  " . get_text("Contact name") . ": " . $values_old['contact_name'] . " => " . trim($values_new['frm_contact_name']);
		}
		if ($values_new['frm_adminperms'] != $values_old['admin_only']) {
			$log_text .= "  " . get_text("Admin permission") . ": ";
			switch ($values_new['frm_adminperms']) {
			case 1:
				$log_text .= get_text("Admin and superadmin") . " => " . get_text("Superadmin only");
				break;
			default:
				$log_text .= get_text("Superadmin only") . " => " . get_text("Admin and superadmin");
			}
		}
		if ($log_text != "") {
			$log_text = get_text("TBL_ID") . ": #" . $id . "  " . get_text("Edited") . "  " . $log_text;
		}
		break;
	case "delete":
		$log_text .= get_text("TBL_ID") . ": #" . $id;
		if (trim($values_old['handle']) != "") {
			$log_text .= "  " . get_text("Unit handle") . ": " . trim($values_old['handle']);
		}
		if (trim($values_old['name']) != "") {
			$log_text .= "  " . get_text("Unit name") . ": " . trim($values_old['name']);
		}
		break;
	default:
	}
	return remove_nls($log_text);
}

function show_units_list($function = "situation", $page = 1, $pages = 1, $ticket_id = 0) {
	$table_side = ($page == 1)? "left" : "right";
//==================================== Regions

	$query = "SELECT * " .
		"FROM `allocates` " .
		"WHERE `type` = " . $GLOBALS['TYPE_USER'] . " " .
		"AND `resource_id` = '" . $_SESSION['user_id'] . "';";

	$result = db_query($query, __FILE__, __LINE__);
	$al_groups = array ();
	while ($row = stripslashes_deep(db_fetch_assoc($result))) {
		$al_groups[] = $row['group'];
	}
	if (isset ($_SESSION['viewed_groups'])) {
		$curr_viewed = explode(",",$_SESSION['viewed_groups']);
	}
	if (!isset($al_groups[0])) {	// catch for errors - no entries in allocates for the user.
		$where2 = "WHERE `a`.`type` = " . $GLOBALS['TYPE_UNIT'];
	} else {
		if (!isset ($curr_viewed)) {
			$x = 0;
			$where2 = "WHERE (";
			foreach ($al_groups as $grp) {
				$where3 = (count($al_groups) > ($x + 1))? " OR " : ")";
				$where2 .= "`a`.`group` = '" . $grp . "'";
				$where2 .= $where3;
				$x++;
			}
		} else {
			$x = 0;
			$where2 = "WHERE (";
			foreach ($curr_viewed as $grp) {
				$where3 = (count($curr_viewed) > ($x + 1))? " OR " : ")";
				$where2 .= "`a`.`group` = '" . $grp . "'";
				$where2 .= $where3;
				$x++;
			}
		}
		$where2 .= " AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'];
	}
//==================================== Units order
	$units_order_values = array (
		1 => "`u`.`handle` ASC, `u`.`name` ASC",
		2 => "`type_name` ASC, `u`.`handle` ASC",
		3 => "`stat_sort` ASC, `u`.`handle` ASC",
		4 => "`nr_assigned` DESC, `stat_sort` ASC, `u`.`handle` ASC"
	);
	switch ($function) {
	case "situation":
		$where2 .= " AND (`s`.`dispatch` < 3 OR `u`.`unit_status_id` = 0 " .
			"OR (SELECT COUNT(*) as `numfound` " .
			"FROM `assigns` " .
			"WHERE `assigns`.`unit_id` = `u`.`id` " .
			"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00')) > 0)";
		$order_str = " ORDER BY " . $units_order_values[get_variable("sort_units")];
		$no_units = get_text("No units in service!");
		break;
	case "units":
		$order_str = " ORDER BY " . $units_order_values[$_SESSION['units_sort_order']];
		$no_units = get_text("No units created!");
		break;
	default:
		$no_units = "";
	}
	$assign_order_str =	"ORDER BY `assign_id` ASC";
	switch ($function) {
	case "assigns":

		$query = "SELECT `assigns`.`id` AS `assign_id`, " .
			"`assigns`.`unit_id`, " .
			"`assigns`.`dispatched`, " .
			"`assigns`.`clear`, " .
			"UNIX_TIMESTAMP(`assigns`.`updated`) AS `updated`, " .
			"`u`.`id` AS `unit_id`, " .
			"`u`.`handle`, " .
			"`u`.`name` AS `unit_name`, " .
			"`u`.`multi` AS `multi`, " .
			"`u`.`description` AS `unit_descr`, " .
			"`u`.`guard_house_id`, " .
			"`u`.`unit_phone`, " .
			"`u`.`unit_email`, " .
			"`u`.`capabilities`, " .
			"`u`.`contact_name`, " .
			"`u`.`remote_data_services`, " .
			"`u`.`unit_status_id`, " .
			"`unit_types`.`id` AS `type_id`, " .
			"`unit_types`.`id` AS `type_id`, " .
			"`unit_types`.`bg_color` AS `background_color`, " .
			"`unit_types`.`text_color` AS `text_color`, " .
			"`unit_types`.`name` AS `type_name`, " .
			"`unit_types`.`description` AS `type_descr`, " .
			"`f`.`handle` AS `guard_house_handle`, " .
			"`f`.`street` AS `guard_house_street`, " .
			"`f`.`city` AS `guard_house_city` " .
			"FROM `assigns` " .
			"LEFT JOIN `units` `u` ON `assigns`.`unit_id` = `u`.`id` " .
			"LEFT JOIN `unit_types` ON `u`.`type` = `unit_types`.`id` " .
			"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
			"WHERE `ticket_id` = " . $ticket_id . " " .
			"ORDER BY `assigns`.`clear` ASC, `assigns`.`dispatched` DESC;";

		$result = db_query($query, __FILE__, __LINE__);
		$assigns_ary_where_str = "";
		$assign_order_str =	"ORDER BY FIELD(`ticket_id`, '" . $ticket_id . "') DESC, `assign_id` ASC";
		break;
	default:

		$query = "SELECT `u`.`id` AS `unit_id`, " .
			"(SELECT COUNT(*) as `numfound` FROM `assigns` " .
			"WHERE `assigns`.`unit_id` = `unit_id` " .
			"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00')) AS `nr_assigned` " .
			"FROM `units` `u` " .
			"LEFT JOIN `allocates` `a` ON (`u`.`id` = `a`.`resource_id`) " .
			"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
			$where2 . ";";

		$result = db_query($query, __FILE__, __LINE__);
		$units_count = db_affected_rows($result, __FILE__, __LINE__);
		unset ($result);
		if ($table_side == "left") {
			$limit_str = " LIMIT " . round($units_count/2);
			$offset_str = "";
		} else {
			$limit_str = " LIMIT " . $units_count;
			$offset_str = " OFFSET " . round($units_count/2);
		}
		$result = get_units_data($where2, $order_str, $limit_str, $offset_str);
		$assigns_ary_where_str = "WHERE ((`clear` IS  NULL) OR (DATE_FORMAT(`clear`,'%y') = '00'))";
	}

//	$result = db_query($query, __FILE__, __LINE__);
	$units_count = db_affected_rows($result, __FILE__, __LINE__);

//==================================== Assigns Array
	$assigns_ary = array ();

	$query_assigns = "SELECT * " .
		"FROM `assigns` " .
		$assigns_ary_where_str . ";";

	$result_assigns = db_query($query_assigns, __FILE__, __LINE__);
	while ($row_assigns = stripslashes_deep(db_fetch_assoc($result_assigns))) {
		$assigns_ary[$row_assigns['unit_id']] = true;
	}
	?>
<?php print show_day_night_style();?>
<script type="text/javascript">

	function do_assign_reset(result) {
		if ((typeof result != "undefined") && (result == "show_promt")) {
			show_infobox("<?php print get_text("Reset dispatch");?>","<?php print get_text("Enter r to Reset dispatch times.") . "<br>" . get_text("Enter d to Delete this dispatch.");?>", "form", do_assign_reset);
		} else {
			if ((typeof result != "undefined") && (result != false) && (result != true)) {
				switch (result.toLowerCase()) {
				case "r":
					$.post("set_data.php", "function=assign_reset&assign_id=" + $("#callprogression_assign_id").html(), function(data) {
					})
					.done(function() {
						show_top_notice("success", "<?php print get_text("Assign calls deleted");?>");
						get_units();
						hide_infobox_large();
					})
					.fail(function() {
						show_top_notice("danger", "<?php print get_text("Error");?>");
					});
					break;
				case "d":
					setTimeout(function() {
						show_infobox("<?php print get_text("Delete this dispatch record?");?>", "", false, do_delete_assign);
					}, 500);
					break;
				default:
				}
			}
		}
	}

	function do_delete_assign(result) {
		if (result == true) {
			$.post("set_data.php", "function=assign_delete&assign_id=" + $("#callprogression_assign_id").html(), function(data) {
			})
			.done(function() {
				show_top_notice("success", "<?php print get_text("Assign deleted");?>");
				get_units();
				hide_infobox_large();
			})
			.fail(function() {
				show_top_notice("danger", "<?php print get_text("Error");?>");
			});
		}
	}

	var status_name, x, y, y_max, y_oldPageOffset, menue_height, menue_margin, menue_hide_locked;
	$(document).ready(function() {
	<?php if (is_super() || is_admin() || is_operator()) {	?>
		show_kontext_menue();
	<?php }	?>

		$("#callprogression_table td").mouseover(function(){
			$(this).addClass("hover_set_callprogression");
		});

		$("#callprogression_table td").mouseout(function(){
			$(this).removeClass("hover_set_callprogression");
		});

		$("#unit_status_table td").mouseover(function(){
			$(this).addClass("hover_set_callprogression");
		});

		$("#unit_status_table td").mouseout(function(){
			$(this).removeClass("hover_set_callprogression");
		});
	});

	function show_kontext_menue() {
		hide_kontext_menue();
		$("div[name^=callprogression]").contextmenu(function(e) {
			$("#callprogression_assign_id").html($(this).attr('data-assign_id'));
			$("#current_max_callprogression").html($(this).attr('data-status'));
			e.preventDefault();
			x = e.clientX;
			y = e.clientY;
			y_max = window.innerHeight;
			y_abs = y + window.pageYOffset;
			y_abs_max = document.documentElement.scrollHeight;
			y_oldPageOffset = window.pageYOffset;
			menue_margin = 10;
			menue_height = $("#kontext_menue").outerHeight() + menue_margin;
			if ((x + 10 + $("#kontext_menue").outerWidth()) > window.innerWidth) {
				x = x - $("#kontext_menue").outerWidth();
			}
			if ((y + menue_height) > y_max) {
				if ((y - menue_height) > 0) {
					y = y - menue_height + menue_margin;
				} else {
					if ((y_abs + menue_height) > y_abs_max) {
						if ((y_abs - menue_height) > 0) {
							y = y - menue_height + menue_margin;
						} else {
							if ((menue_height - menue_margin) > y_abs_max) {
								y = 0;
								alert("<?php print get_text("Window too low for menu!")?>");
							} else {
								menue_half_height = Math.round((menue_height - menue_margin) / 2);
								y_abs_max_half_height = Math.round(y_abs_max / 2);
								y = y_abs_max_half_height - menue_half_height - y_oldPageOffset;
							}
						}
					}
				}
			}
			$("#kontext_menue").css("left", x + "px");
			$("#kontext_menue").css("top", y + "px");
			switch($("#current_max_callprogression").html()) {
			case "dispatched":
				break;
			case "responding":
				$("#set_responding").css("color", "#696969");
				break;
			case "on_scene":
				$("#set_responding").css("color", "#696969");
				$("#set_on_scene").css("color", "#696969");
				break;
			case "facility_enroute":
				$("#set_responding").css("color", "#696969");
				$("#set_on_scene").css("color", "#696969");
				$("#set_facility_enroute").css("color", "#696969");
				break;
			case "facility_arrived":
				$("#set_responding").css("color", "#696969");
				$("#set_on_scene").css("color", "#696969");
				$("#set_facility_enroute").css("color", "#696969");
				$("#set_facility_arrived").css("color", "#696969");
				break;
			case "clear":
				$("#set_responding").css("color", "#696969");
				$("#set_on_scene").css("color", "#696969");
				$("#set_facility_enroute").css("color", "#696969");
				$("#set_facility_arrived").css("color", "#696969");
				$("#set_clear").css("color", "#696969");
				break;
			default:
			}
			UnTip();
			$("#kontext_menue").show();
		});
		$("div[name^=unit_status]").contextmenu(function(e) {
			$("#unit_id").html($(this).attr('data-unit_id'));
			e.preventDefault();
			x = e.clientX;
			y = e.clientY;
			y_max = window.innerHeight;
			y_abs = y + window.pageYOffset;
			y_abs_max = document.documentElement.scrollHeight;
			y_oldPageOffset = window.pageYOffset;
			menue_margin = 10;
			menue_height = $("#unit_status_menue").outerHeight() + menue_margin;
			if ((x + 10 + $("#unit_status_menue").outerWidth()) > window.innerWidth) {
				x = x - $("#unit_status_menue").outerWidth();
			}
			if ((y + menue_height) > y_max) {
				if ((y - menue_height) > 0) {
					y = y - menue_height + menue_margin;
				} else {
					if ((y_abs + menue_height) > y_abs_max) {
						if ((y_abs - menue_height) > 0) {
							y = y - menue_height + menue_margin;
						} else {
							if ((menue_height - menue_margin) > y_abs_max) {
								y = 0;
								alert("<?php print get_text("Window too low for menu!")?>");
							} else {
								menue_half_height = Math.round((menue_height - menue_margin) / 2);
								y_abs_max_half_height = Math.round(y_abs_max / 2);
								y = y_abs_max_half_height - menue_half_height - y_oldPageOffset;
							}
						}
					}
				}
			}
			$("#unit_status_menue").css("left", x + "px");
			$("#unit_status_menue").css("top", y + "px");
			UnTip();
			$("#unit_status_menue").show();
		});
	}

	function hide_kontext_menue() {
		$("#kontext_menue").hide();
		$("#set_responding").css("color", "#000000");
		$("#set_on_scene").css("color", "#000000");
		$("#set_facility_enroute").css("color", "#000000");
		$("#set_facility_arrived").css("color", "#000000");
		$("#set_clear").css("color", "#000000");
		$("#set_reset").css("color", "#000000");
		$("#unit_status_menue").hide();
		menue_hide_locked = false;
	}

	$(document).mousedown(function(e) {
		if (($("#kontext_menue").is(":visible")) &&
			((!(e.clientX >= x && e.clientX <= (x + $("#kontext_menue").width()) &&
			e.clientY >= y && e.clientY <= (y + $("#kontext_menue").height()))))) {
			hide_kontext_menue();
		}
		if (($("#unit_status_menue").is(":visible")) &&
			((!(e.clientX >= x && e.clientX <= (x + $("#unit_status_menue").width()) &&
			e.clientY >= y && e.clientY <= (y + $("#unit_status_menue").height()))))) {
			hide_kontext_menue();
		}
	});

	$(window).scroll(function () {
		if ($("#kontext_menue").is(":visible")) {
			if ((y >= 0) && (y + (menue_height - menue_margin) <= y_max) && menue_hide_locked == false) {
				hide_kontext_menue();
			} else {
				menue_hide_locked = true;
				y_newPageOffset = window.pageYOffset;
				y = y + (y_oldPageOffset - y_newPageOffset);
				y_oldPageOffset = y_newPageOffset;
				$("#kontext_menue").css("top", y);
			}
		}
		if ($("#unit_status_menue").is(":visible")) {
			if ((y >= 0) && (y + (menue_height - menue_margin) <= y_max) && menue_hide_locked == false) {
				hide_kontext_menue();
			} else {
				menue_hide_locked = true;
				y_newPageOffset = window.pageYOffset;
				y = y + (y_oldPageOffset - y_newPageOffset);
				y_oldPageOffset = y_newPageOffset;
				$("#unit_status_menue").css("top", y);
			}
		}
	});

	function handle_status_select(assign_id, status) {
		hide_kontext_menue();
		var query_part_str = "";
		switch (status) {
		case "responding":
			if (
				$("#current_max_callprogression").html().valueOf() == "dispatched"
			) {
				query_part_str = "&frm_callprogression=frm_responding";
			} else {
				return false;
			}
			break;
		case "on_scene":
			if (
				($("#current_max_callprogression").html().valueOf() == "dispatched") ||
				($("#current_max_callprogression").html().valueOf() == "responding")
			) {
				query_part_str = "&frm_callprogression=frm_on_scene";
			} else {
				return false;
			}
			break;
		case "facility_enroute":
			if (
				($("#current_max_callprogression").html().valueOf() == "dispatched") ||
				($("#current_max_callprogression").html().valueOf() == "responding") ||
				($("#current_max_callprogression").html().valueOf() == "on_scene")
			) {
				query_part_str = "&frm_callprogression=frm_u2fenr";
			} else {
				return false;
			}
			break;
		case "facility_arrived":
			if (
				($("#current_max_callprogression").html().valueOf() == "dispatched") ||
				($("#current_max_callprogression").html().valueOf() == "responding") ||
				($("#current_max_callprogression").html().valueOf() == "on_scene") ||
				($("#current_max_callprogression").html().valueOf() == "facility_enroute")
			) {
				query_part_str = "&frm_callprogression=frm_u2farr";
			} else {
				return false;
			}
			break;
		case "clear":
			if (
				($("#current_max_callprogression").html().valueOf() == "dispatched") ||
				($("#current_max_callprogression").html().valueOf() == "responding") ||
				($("#current_max_callprogression").html().valueOf() == "on_scene") ||
				($("#current_max_callprogression").html().valueOf() == "facility_enroute") ||
				($("#current_max_callprogression").html().valueOf() == "facility_arrived")
			) {
				query_part_str = "&frm_callprogression=frm_clear";
			} else {
				return false;
			}
			break;
		case "reset":
			$("#infobox_large").modal("hide");
			do_assign_reset("show_promt");
			break;
		default:
		}
		if (query_part_str.valueOf() != "") {
			$.post("set_data.php", "function=call_progression&assign_id=" + assign_id + query_part_str, function(data) {
				if (data) {
					goto_window(data);
				}
			})
			.done(function() {
				show_top_notice("success", "<?php print get_text("Status update applied");?>");
			})
			.fail(function() {
				show_top_notice("danger", "<?php print get_text("Error");?>");
			});
		}
	}

	function unit_status_select(unit_id, status_id) {
		hide_kontext_menue();
		$.get("set_data.php", "function=unit_status&frm_unit_id=" + unit_id + "&frm_status_id=" + status_id, function(data) {
		})
		.done(function() {
			show_top_notice("success", "<?php print get_text("Status update applied");?>");
			get_units();
		})
		.fail(function() {
			show_top_notice("danger", "<?php print get_text("Error");?>");
		});
	}

</script>
<div id="kontext_menue" class="panel panel-default" style="padding: 0px; position: fixed; display: none; z-index: 2000;">
	<div id="callprogression_assign_id" style="display:none;"></div>
	<div id="current_max_callprogression" style="display:none;"></div>
	<table id="callprogression_table" class="table table-striped table-condensed" style="width: 120px; background-color: #FFFFFF; font-weight: bold;">
		<tr><td id="set_responding" onclick="handle_status_select($('#callprogression_assign_id').html(), 'responding');"><?php print get_text("Responding");?></td></tr>
		<tr><td id="set_on_scene" onclick="handle_status_select($('#callprogression_assign_id').html(), 'on_scene');"><?php print get_text("On-scene");?></td></tr>
		<tr><td id="set_facility_enroute" onclick="handle_status_select($('#callprogression_assign_id').html(), 'facility_enroute');"><?php print get_text("Fac en-route");?></td></tr>
		<tr><td id="set_facility_arrived" onclick="handle_status_select($('#callprogression_assign_id').html(), 'facility_arrived');"><?php print get_text("Fac arr");?></td></tr>
		<tr><td id="set_clear" onclick="handle_status_select($('#callprogression_assign_id').html(), 'clear');"><?php print get_text("Clear");?></td></tr>
		<tr><td id="set_reset" onclick="handle_status_select($('#callprogression_assign_id').html(), 'reset');"><?php print get_text("Reset");?></td></tr>
	</table>
</div>
<?php show_unit_facility_status_select($GLOBALS['TYPE_UNIT']);?>
<table class="table table-striped table-condensed" style="table-layout: fixed;">
	<tr>
		<th style="width: 6%; border-top: 0px;"></th>
		<th style="width: 5%; border-top: 0px; text-align: center;"><?php print get_message_click_str($function, 0, $ticket_id, "", "", "", "", true);?></th>
		<th style="width: 40%; border-top: 0px;">&nbsp;<?php print get_text("Unit");?> (<?php print $units_count;?>)</th>
		<th style="width: 35%; border-top: 0px;">&nbsp;<?php print get_text("Status");?></th>
		<th style="width: 14%; border-top: 0px; text-align: center;"><?php print get_text("As of");?></th>
	</tr>
	<?php
	if ($units_count == 0) {
	?>
	<tr>
		<td colspan=5 style="text-align: center;">
			<strong><?php print $no_units;?></strong>
		</td>
	</tr>
	<?php
	}
//====================================================== begin major while() for Units
	$utc = gmdate("U");
	$row_index = 1;
	while ($row = stripslashes_deep(db_fetch_array($result))) {
//==================================== Flag/Mail/Edit
		switch ($function) {
		case "units":
			if (is_admin() || is_super()) {
				$flag_and_edit_col_str = "<td style='text-align: center;'>" . "<span onclick='goto_window(\"units.php?function=edit&id=" .
					$row['unit_id'] . "\");' class='glyphicon glyphicon-pencil' aria-hidden='true' style='font-size: 12px; padding-right: 2px;'></span></td>";
			} else {
				$flag_and_edit_col_str = "<td></td>";
			}
			$mail_column_str = "<td style='text-align: center;'>" . get_message_click_str("unit", $row['unit_id'],
				0, $row['handle'], $row['unit_phone'], $row['unit_email'], $row['remote_data_services']) . "</td>";
			break;
		default:
			if ($row['unit_id'] == $_SESSION['unit_flag_1']) {
				$flag_and_edit_col_str = "<td>&nbsp;<span" . get_help_text_str("flag_unit") . " class='glyphicon glyphicon-flag' aria-hidden='true' style='font-size: 12px; color: red;'></span></td>";
				$_SESSION['unit_flag_1'] = 0;
			} else {
				$flag_and_edit_col_str = "<td></td>";
			}
			$mail_column_str = "<td style='text-align: center;'>" . get_message_click_str("unit", $row['unit_id'],
				$ticket_id, $row['handle'], $row['unit_phone'], $row['unit_email'], $row['remote_data_services']) . "</td>";
		}
//==================================== Status
		$status_values = array ();

		$query = "SELECT * " .
			"FROM `unit_status`;";

		$result_unit_status = db_query($query, __FILE__, __LINE__);
		while ($row_st = stripslashes_deep(db_fetch_array($result_unit_status))) {
			$status_values[$row_st['id']] = $row_st['description'];
		}
		if (array_key_exists($row['unit_status_id'], $status_values)) {
			$unit_st_val = $status_values[$row["unit_status_id"]];
		} else {
			$unit_st_val = "";
		}
//================== Assigns
		$assign_old = "false";
		if (!(array_key_exists($row['unit_id'], $assigns_ary))) {
			$row_assign = false;
			$disp_inc_stat = 0;
		} else {
		if (isset ($row['clear']) && is_datetime($row['clear']) && $function == "assigns") {
				$assign_old = "true";
				$disp_inc_stat = 0;
			} else {

				$query = "SELECT *, " .
					"`assigns`.`id` AS `assign_id`, " .
					"`assigns`.`ticket_id` AS `ticket_id`, " .
					"`assigns`.`updated` AS `assign_updated`, " .
					"`assigns`.`on_scene_facility_id` AS `assign_facility_id`, " .
					"`assigns`.`on_scene_location` AS `assign_on_scene_location`, " .
					"`assigns`.`comments` AS `assign_comments`, " .
					"`f_a_o`.`handle` AS `assign_on_scene_facility_handle`, " .
					"`assigns`.`receiving_location` AS `assign_receiving_location`, " .
					"`f_a_r`.`handle` AS `assign_rec_facility_handle`, " .
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
					"WHERE `unit_id` = '" . $row['unit_id'] . "' " .
					"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00') " .
					$assign_order_str . ";";

				$result_assigns = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result_assigns) == 0) {
					$row_assign = false;
				} else {
					$row_assign = stripslashes_deep(db_fetch_assoc($result_assigns));
				}
				$disp_inc_stat = db_affected_rows($result_assigns);
				unset ($result_assigns);
			}
		}
//================== Status, Call progression, Multiple
		$title_status_assign = $title_status_assign = html_entity_decode(remove_nls(wordwrap($unit_st_val), 80, "<br>", true));
		switch ($disp_inc_stat) {
		case 0:
			if ($assign_old == "true") {
				$unit_status_time_raw = $row['updated'];
				$status_select = "<div style=\"margin: 2px;\"><span class='label closed col-md-12' style='height: auto; text-align: left;' onClick='edit_assign(" .
					$row['assign_id'] . ")'>" . get_text("Clear") .
					"&nbsp;&nbsp;" . date(get_variable("date_format_time_only"), strtotime($row['clear'])) . "</span></div>";
				$unit_st_val = "";
				$title_status_assign = get_text("Cleared") . "<br>" . date(get_variable("date_format"), strtotime($row['clear'])) . "<br><br>" . get_text("Click to edit assign");
			} else {
				$unit_status_time_raw = $row['updated'];
				if (is_super() || is_admin() || is_operator()) {
					$title_status_assign = html_entity_decode(remove_nls(wordwrap($unit_st_val . "<br><br>" . get_text("Click to edit log report") . "<br>" . get_text("Click right to set status"), 80, "<br>", true)));
				}
				$status_select = get_status_select_str($GLOBALS['TYPE_UNIT'], $row['unit_id'], $row['unit_status_id'], $function);
			}
			break;
		case 1:
			$unit_status_time_raw = strtotime($row_assign['assign_updated']);
			$title_status_assign = get_title_ticket($row_assign);
			$title_action_str = get_title_action_str($row_assign);
			if (!$title_action_str[1]) {
				$title_status_assign .= "------------------------------<br>";
				$title_status_assign .= get_text("Actions") . ":<br>";
				$title_status_assign .= $title_action_str[0];
			}
			if (is_super() || is_admin() || is_operator()) {
				$title_status_assign .= "<br>" . get_text("Click to edit assign") . "<br>" . get_text("Click right to set callprogression");
			}
			$title_status_assign = html_entity_decode($title_status_assign);
			$status_select = get_status_display_str($row_assign, " onClick='edit_assign(" . $row_assign['assign_id'] . ");'", $disp_inc_stat);
			break;
		default:
			$unit_status_time_raw = strtotime($row_assign['assign_updated']);
			$title_status_assign = get_title_ticket($row_assign);
			$title_action_str = get_title_action_str($row_assign);
			if (!$title_action_str[1]) {
				$title_status_assign .= "------------------------------<br>";
				$title_status_assign .= get_text("Actions") . ":<br>";
				$title_status_assign .= $title_action_str[0];
			}
			if (is_super() || is_admin() || is_operator()) {
				$title_status_assign .= "<br>" . get_text("Multidispatch - click to select assign") . "<br>" . get_text("Click right to set callprogression");
			}
			$title_status_assign = html_entity_decode($title_status_assign);
			$status_select = get_status_display_str($row_assign, " onClick='show_assigns(" . $row['unit_id'] . ");'", $disp_inc_stat);
		}
		$unit_status_time_title_str = $unit_status_time_str = "";
		if ($unit_status_time_raw != 0) {
			$unit_status_time_title = date(get_variable("date_format"), $unit_status_time_raw);
			$unit_status_time = date(get_variable("date_format_time_only"), $unit_status_time_raw);
			if (abs($utc - $unit_status_time_raw) > (get_variable("tolerance") * 60)) {
				$strike = "<span style=\"text-decoration: line-through;\">";
				$strike_end = "</span>";
			} else {
				$strike = $strike_end = "";
			}
			$unit_status_time_title_str = get_title_str( $strike . $unit_status_time_title . $strike_end);
			$unit_status_time_str = $strike . $unit_status_time . $strike_end;
		}
	?>
	<tr id="<?php print "tr_id_" . $row_index;?>" style="height: 35px;">
		<?php print $flag_and_edit_col_str;?>
		<?php print $mail_column_str;?>
		<td<?php print get_title_unit_str($row);?> style="vertical-align: middle;">
			<span class="label" style="background-color: <?php print $row['background_color']?>; color: <?php print $row['text_color'];?>; font-weight: bold; font-size: 12px;">
				<?php print remove_nls($row['handle']);?>
			</span>
		</td>
		<td>
			<div style="margin: 2.5px;"<?php print get_title_str($title_status_assign);?>><?php print $status_select;?></div>
		</td>
		<td style="text-align: center;"<?php print $unit_status_time_title_str;?>>
			<?php print $unit_status_time_str;?>
		</td>
	</tr>
	<?php
		$row_index++;
	}
	?>
</table>
	<?php
}
?>