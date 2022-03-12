<?php
function show_facilities_legend() {

	$query = "SELECT DISTINCT `type`, " .
		"`facility_types`.`name` AS `mytype`, " .
		"`facility_types`.`bg_color`, " .
		"`facility_types`.`text_color`, " .
		"`facility_types`.`description` AS `mydescription` " .
		"FROM `facilities` " .
		"LEFT JOIN `facility_types` ON `facility_types`.`id` = `facilities`.`type` " .
		"ORDER BY `mytype`;";

	$result = db_query($query, __FILE__, __LINE__);

	$output_str = "<span align='center'><span align='center'> " . get_text("Facilities legend") . ": </span>&nbsp;";
	while ($row = stripslashes_deep(db_fetch_array($result))) {
		$title_type_str = get_title_str($row['mydescription']);
		$output_str .= "<span class='label' style='background-color: " . $row['bg_color'] . "; color: " . $row['text_color'] . ";'" . $title_type_str . "> " . remove_nls($row['mytype']) . " </span>&nbsp;";
	}
	print $output_str .= "</span>";
}

function show_facilities_sortbar() {
	if (empty ($_SESSION['facilities_sort_order'])) {
		$_SESSION['facilities_sort_order'] = get_variable("sort_facilities");
	}
	$checked = array ("", "", "", "");
	$checked[$_SESSION['facilities_sort_order']] = " checked";
	?>
	<div style="text-align: center;">
		<div>
			<?php show_facilities_legend();?>
		</div>
		<div style="margin: 2px;">
			<label class="radio-inline"><?php print get_text("Sort");?>: </label>
			<label class="radio-inline">
				<input type=radio name="frm_facilities_order" value=1 <?php print $checked[1];?> onclick="do_sort_facilities(this.value);"><?php print get_text("Name");?>
			</label>
			<label class="radio-inline">
				<input type=radio name="frm_facilities_order" value=2 <?php print $checked[2];?> onclick="do_sort_facilities(this.value);"><?php print get_text("Type");?>
			</label>
			<label class="radio-inline">
				<input type=radio name="frm_facilities_order" value=3 <?php print $checked[3];?> onclick="do_sort_facilities(this.value);"><?php print get_text("Status");?>
			</label>
		</div>
	</div>
	<?php
}

function get_facility_edit_log_text($function, $id, $values_new, $values_old) {
	$log_text = "";
	switch ($function) {
	case "add":
		$log_text .= get_text("TBL_ID") . ": #" . $id;
		if (trim($values_new['frm_handle']) != "") {
			$log_text .= "  " . get_text("Facility handle") . ": " . trim($values_new['frm_handle']);
		}
		if (trim($values_new['frm_name']) != "") {
			$log_text .= "  " . get_text("Facility name") . ": " . trim($values_new['frm_name']);
		}
		if (trim($values_new['frm_object_id']) != "") {
			$log_text .= "  " . get_text("Object id") . ": " . trim($values_new['frm_object_id']);
		}
		if (trim($values_new['frm_direct_dialing_1']) != "") {
			$log_text .= "  " . get_text("Direct dialing 1") . ": " . trim($values_new['frm_direct_dialing_1']);
		}
		if (trim($values_new['frm_direct_dialing_2']) != "") {
			$log_text .= "  " . get_text("Direct dialing 2") . ": " . trim($values_new['frm_direct_dialing_2']);
		}
		if ($values_new['frm_street'] != 0) {
			$log_text .= "  " . get_text("Facility address") . ": " . trim($values_new['frm_street']);
		}
		if ($values_new['frm_city'] != 0) {
			$log_text .= "  " . get_text("City") . ": " . trim($values_new['frm_city']);
		}
		if ($values_new['frm_opening_hours'] != 0) {
			$log_text .= "  ". get_text("Opening hours") . ": " . trim($values_new['frm_opening_hours']);
		}
		if (trim($values_new['frm_access_rules']) != "") {
			$log_text .= "  " . get_text("Access rules") . ": " . trim($values_new['frm_access_rules']);
		}
		if (trim($values_new['frm_security_contact']) != "") {
			$log_text .= "  " . get_text("Security contact") . ": " . trim($values_new['frm_security_contact']);
		}
		if (trim($values_new['frm_security_phone']) != "") {
			$log_text .= "  " . get_text("Security phone") . ": " . trim($values_new['frm_security_phone']);
		}
		if (trim($values_new['frm_security_email']) != "") {
			$log_text .= "  " . get_text("Security email") . ": " . trim($values_new['frm_security_email']);
		}
		if (trim($values_new['frm_type']) != "") {
			$log_text .= "  " . get_text("Type") . ": " . get_facility_type_name($values_new['frm_type']);
		}
		if (trim($values_new['frm_status_id']) != "") {
			$log_text .= "  " . get_text("Status") . ": " . get_facility_status_name($values_new['frm_status_id']);
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
		if (trim($values_new['frm_contact_phone']) != "") {
			$log_text .= "  " . get_text("Contact phone") . ": " . trim($values_new['frm_contact_phone']);
		}
		if (trim($values_new['frm_contact_email']) != "") {
			$log_text .= "  " . get_text("Contact email") . ": " . trim($values_new['frm_contact_email']);
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
			$log_text .= "  " . get_text("Facility handle") . ": " . trim($values_old['handle']) . " => " . trim($values_new['frm_handle']);
		}
		if (trim($values_new['frm_name']) != trim($values_old['name'])) {
			$log_text .= "  " . get_text("Facility name") . ": " . trim($values_old['name']) . " => " . trim($values_new['frm_name']);
		}
		if (trim($values_new['frm_object_id']) != trim($values_old['object_id'])) {
			$log_text .= "  " . get_text("Object id") . ": " . trim($values_old['object_id']) . " => " . trim($values_new['frm_object_id']);
		}
		if (trim($values_new['frm_direct_dialing_1']) != trim($values_old['direct_dialing_1'])) {
			$log_text .= "  " . get_text("Direct dialing 1") . ": " . trim($values_old['direct_dialing_1']) . " => " . trim($values_new['frm_direct_dialing_1']);
		}
		if (trim($values_new['frm_direct_dialing_2']) != trim($values_old['direct_dialing_2'])) {
			$log_text .= "  " . get_text("Direct dialing 2") . ": " . trim($values_old['direct_dialing_2']) . " => " . trim($values_new['frm_direct_dialing_2']);
		}
		if (trim($values_new['frm_street']) != trim($values_old['street'])) {
			$log_text .= "  " . get_text("Facility address") . ": " . trim($values_old['street']) . " => " . trim($values_new['frm_street']);
		}
		if (trim($values_new['frm_city']) != trim($values_old['city'])) {
			$log_text .= "  " . get_text("City") . ": " . trim($values_old['city']) . " => " . trim($values_new['frm_city']);
		}
		if (trim($values_new['frm_opening_hours']) != trim($values_old['opening_hours'])) {
			$log_text .= "  ". get_text("Opening hours") . ": " . trim($values_old['opening_hours']) . " => " . trim($values_new['frm_opening_hours']);
		}
		if (trim($values_new['frm_access_rules']) != trim($values_old['access_rules'])) {
			$log_text .= "  " . get_text("Access rules") . ": " . trim($values_old['access_rules']) . " => " . trim($values_new['frm_access_rules']);
		}
		if (trim($values_new['frm_security_contact']) != trim($values_old['security_contact'])) {
			$log_text .= "  " . get_text("Security contact") . ": " . trim($values_old['security_contact']) . " => " . trim($values_new['frm_security_contact']);
		}
		if (trim($values_new['frm_security_phone']) != trim($values_old['security_phone'])) {
			$log_text .= "  " . get_text("Security phone") . ": " . trim($values_old['security_phone']) . " => " . trim($values_new['frm_security_phone']);
		}
		if (trim($values_new['frm_security_email']) != trim($values_old['security_email'])) {
			$log_text .= "  " . get_text("Security email") . ": " . trim($values_old['security_email']) . " => " . trim($values_new['frm_security_email']);
		}
		if ($values_new['frm_type'] != $values_old['type']) {
			$log_text .= "  " . get_text("Type") . ": " . get_facility_type_name($values_old['type']) . " => " . get_facility_type_name($values_new['frm_type']);
		}
		if ($values_new['frm_status_id'] != $values_old['facility_status_id']) {
			$log_text .= "  " . get_text("Status") . ": " . get_facility_status_name($values_old['facility_status_id']) . " => " . get_facility_status_name($values_new['frm_status_id']);
		}
		if (trim($values_new['frm_descr']) != trim($values_old['description'])) {
			$log_text .= "  " . get_text("Description") . ": " . trim($values_old['description']) . " => " . trim($values_new['frm_descr']);
		}
		if (trim($values_new['frm_capab']) != trim($values_old['capabilities'])) {
			$log_text .= "  " . get_text("Capability") . ": " . trim($values_old['capabilities']) . " => " . trim($values_new['frm_capab']);
		}
		if (trim($values_new['frm_contact_name']) != trim($values_old['contact_name'])) {
			$log_text .= "  " . get_text("Contact name") . ": " . trim($values_old['contact_name']) . " => " . trim($values_new['frm_contact_name']);
		}
		if (trim($values_new['frm_contact_phone']) != trim($values_old['contact_phone'])) {
			$log_text .= "  " . get_text("Contact phone") . ": " . trim($values_old['contact_phone']) . " => " . trim($values_new['frm_contact_phone']);
		}
		if (trim($values_new['frm_contact_email']) != trim($values_old['contact_email'])) {
			$log_text .= "  " . get_text("Contact email") . ": " . trim($values_old['contact_email']) . " => " . trim($values_new['frm_contact_email']);
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
			$log_text .= "  " . get_text("Facility handle") . ": " . trim($values_old['handle']);
		}
		if (trim($values_old['name']) != "") {
			$log_text .= "  " . get_text("Facility name") . ": " . trim($values_old['name']);
		}
		break;
	default:
	}
	return remove_nls($log_text);
}

function show_facilities_list($table_side = "left", $split = 0) {
	if (empty($_SESSION['facilities_sort_order'])) {
		$_SESSION['facilities_sort_order'] = get_variable("sort_facilities");
	}
	$facilities_order_values = array (
		1 => "`handle`,`fac_type_name` ASC",
		2 => "`fac_type_name`, `handle` ASC",
		3 => "`fac_status_val`, `fac_type_name` ASC");
	$fac_order_str = $facilities_order_values[$_SESSION['facilities_sort_order']];
	$where_str = get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_FACILITY'], "WHERE");

	$query_fac = "SELECT `facilities`.`id` AS `fac_id` FROM `facilities` " .
		"LEFT JOIN `allocates` ON (`facilities`.`id` = `allocates`.`resource_id`) " .
		"LEFT JOIN `facility_types` ON `facilities`.`type` = `facility_types`.`id` " .
		"LEFT JOIN `facility_status` ON `facilities`. `facility_status_id` = `facility_status`.`id` " . $where_str . ";";

	$result_fac = db_query($query_fac, __FILE__, __LINE__);
	$facilities_count = db_affected_rows($result_fac);
	unset ($result_fac);
	if ($table_side == "left") {
		$limit_str = " LIMIT " . round($facilities_count/2);
		$offset_str = "";
	} else {
		$limit_str = " LIMIT " . $facilities_count;
		$offset_str = " OFFSET " . round($facilities_count/2);
	}

	$query_fac = "SELECT `facilities`.`updated` AS `updated`, " .
		"`facilities`.`id` AS `fac_id`, " .
		"`facilities`.`name` AS `facility_name`, " .
		"`facilities`.`description` AS `facility_description`, " .
		"`facilities`.`handle`, " .
		"`facilities`.`contact_email`, " .
		"`facilities`.`security_email`, " .
		"`facilities`.`direct_dialing_1`, " .
		"`facilities`.`direct_dialing_2`, " .
		"`facilities`.`street`, " .
		"`facilities`.`city`, " .
		"`facilities`.`object_id`, " .
		"`facilities`.`opening_hours`, " .
		"`facilities`.`security_contact`, " .
		"`facilities`.`security_phone`, " .
		"`facilities`.`capabilities`, " .
		"`facilities`.`access_rules`, " .
		"`facilities`.`contact_phone`, " .
		"`facilities`.`admin_only`, " .
		"`facilities`.`facility_status_id` AS `fac_status_id` ," .
		"`facility_types`.`name` AS `fac_type_name`, " .
		"`facility_types`.`bg_color` AS `fac_background_color`, " .
		"`facility_types`.`text_color` AS `fac_text_color`, " .
		"`facility_status`.`status_name` AS `fac_status_val`, " .
		"`facility_status`.`description` AS `fac_status_desc` " .
	"FROM `facilities` " .
		"LEFT JOIN `allocates` ON (`facilities`.`id` = `allocates`.`resource_id`) " .
		"LEFT JOIN `facility_types` ON `facilities`.`type` = `facility_types`.`id` " .
		"LEFT JOIN `facility_status` ON `facilities`.`facility_status_id` = `facility_status`.`id` " .
	$where_str .
		"GROUP BY `fac_id` " .
		"ORDER BY " . $fac_order_str . $limit_str . $offset_str . ";";

	$result_fac = db_query($query_fac, __FILE__, __LINE__);
	$facilities_count = db_affected_rows($result_fac);

	?>
<?php print show_day_night_style();?>
<script type="text/javascript">

	var status_name, x, y, y_max, y_oldPageOffset, menue_height, menue_margin, menue_hide_locked;
	$(document).ready(function() {
	<?php if (is_super() || is_admin() || is_operator()) {	?>
		show_kontext_menue();
	<?php }	?>
		$("#facility_status_table td").mouseover(function(){
			$(this).addClass("hover_set_callprogression");
		});

		$("#facility_status_table td").mouseout(function(){
			$(this).removeClass("hover_set_callprogression");
		});
	});

	function show_kontext_menue() {
		$("div[name^=facility_status]").contextmenu(function(e) {
			$("#facility_id").html($(this).attr('data-facility_id'));
			e.preventDefault();
			x = e.clientX;
			y = e.clientY;
			y_max = parent.frames["main"].window.innerHeight;
			y_abs = y + parent.frames["main"].window.pageYOffset;
			y_abs_max = document.documentElement.scrollHeight;
			y_oldPageOffset = parent.frames["main"].window.pageYOffset;
			menue_margin = 10;
			menue_height = $("#facility_status_menue").outerHeight() + menue_margin;
			if ((x + 10 + $("#facility_status_menue").outerWidth()) > parent.frames["main"].window.innerWidth) {
				x = x - $("#facility_status_menue").outerWidth();
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
			$("#facility_status_menue").css("left", x + "px");
			$("#facility_status_menue").css("top", y + "px");
			UnTip();
			$("#facility_status_menue").show();
		});
	}

	function hide_kontext_menue() {
		$("#facility_status_menue").hide();
		menue_hide_locked = false;
	}

	$(document).mousedown(function(e) {
		if (($("#facility_status_menue").is(":visible")) &&
			((!(e.clientX >= x && e.clientX <= (x + $("#facility_status_menue").width()) &&
			e.clientY >= y && e.clientY <= (y + $("#facility_status_menue").height()))))) {
			hide_kontext_menue();
		}
	});

	$(window).scroll(function () {
		if ($("#facility_status_menue").is(":visible")) {
			if ((y >= 0) && (y + (menue_height - menue_margin) <= y_max) && menue_hide_locked == false) {
				hide_kontext_menue();
			} else {
				menue_hide_locked = true;
				y_newPageOffset = parent.frames["main"].window.pageYOffset;
				y = y + (y_oldPageOffset - y_newPageOffset);
				y_oldPageOffset = y_newPageOffset;
				$("#facility_status_menue").css("top", y);
			}
		}
	});

	function facility_status_select(facility_id, status_id) {
		hide_kontext_menue();
		$.get("set_data.php", "function=facility_status&frm_facility_id=" + facility_id + "&frm_status_id=" + status_id, function(data) {
				if (data) {
					//parent.frames["main"].get_units();
				}
			})
			.done(function() {
				parent.frames["navigation"].show_message("<?php print get_text("Status update applied");?>", "success");
				parent.frames["main"].get_facilities();
			})
			.fail(function() {
				alert("error");
			});
	}

</script>
<?php show_unit_facility_status_select($GLOBALS['TYPE_FACILITY']);?>
<table class="table table-striped table-condensed" style="table-layout: fixed;">
	<tr>
		<th style="width: 6%; border-top: 0px;"></th>
		<th style="width: 5%; border-top: 0px; text-align: center;"><?php print get_message_click_str("facilities", 0, 0, "", "", "", "");?></th>
		<th style="width: 40%; border-top: 0px;">&nbsp;<?php print get_text("Facility");?> (<?php print $facilities_count;?>)</th>
		<th style="width: 35%; border-top: 0px;">&nbsp;<?php print get_text("Status");?></th>
		<th style="width: 14%; border-top: 0px; text-align: center;"><?php print get_text("As of");?>
	</tr>
	<?php
	if ($facilities_count == 0) {
	?>
	<tr>
		<td colspan=5 style="text-align: center;"><b><?php print get_text("No facilities created!");?></b></td>
	</tr>
	<?php
	}
	while ($row_fac = db_fetch_assoc($result_fac)) {
		if (is_admin() || is_super()) {
	?>
	<tr style="height: 35px;">
		<td style="text-align: center; vertical-align: middle;">
			<span onclick="window.location.href='facilities.php?function=edit&id=<?php print $row_fac['fac_id'];?>'" class="glyphicon glyphicon-pencil" aria-hidden="true" style="font-size: 12px;"></span>&nbsp;
		</td>
	<?php
		} else {
	?>
		<td></td>
	<?php
		}
		$title_status = html_entity_decode(remove_nls(wordwrap($row_fac['fac_status_desc']), 80, "<br>", true));
		if (is_super() || is_admin() || is_operator()) {
			$title_status = html_entity_decode(remove_nls(wordwrap($row_fac['fac_status_desc'] . "<br><br>" . get_text("Click to edit log report") . "<br>" . get_text("Click right to set status"), 80, "<br>", true)));
		}
	?>
		<td style="text-align: center; vertical-align: middle;"><?php print get_message_click_str("facility", $row_fac['fac_id'], 0, $row_fac['handle'], $row_fac['contact_email'], $row_fac['security_email'], "");?></td>
		<td<?php print get_title_facility_str($row_fac);?> style="text-align: left; vertical-align: middle;">
			<span class="label" style="background-color: <?php print $row_fac['fac_background_color'];?>; color: <?php print $row_fac['fac_text_color'];?>; font-weight: bold; font-size: 12px;"><?php print remove_nls($row_fac['handle']);?></span>
		</td>
		<td style="vertical-align: middle;">
			<div<?php print get_title_str($title_status);?>><?php print get_status_select_str($GLOBALS['TYPE_FACILITY'], $row_fac['fac_id'], $row_fac['fac_status_id'], "facilities");?></div>
		</td>
	<?php
		$facility_status_time_title_str = $facility_status_time_str = "";
		$the_time_fac = $row_fac['updated'];
		if ($the_time_fac != 0) {
			if (abs(gmdate ("U") - strtotime($the_time_fac)) > (get_variable("tolerance") * 60)) {
				$strike = "<span style=\"text-decoration: line-through;\">";
				$strike_end = "</span>";
			} else {
				$strike = $strike_end = "";
			}
			$facility_status_time_title_str = get_title_str($strike . date(get_variable("date_format"), strtotime($the_time_fac)) . $strike_end);
			$facility_status_time_str = $strike . " " . date(get_variable("date_format_time_only"), strtotime($the_time_fac)) . " " . $strike_end;
		}
	?>
		<td style="text-align: center; vertical-align: middle;"<?php print $facility_status_time_title_str;?>>
			<?php print $facility_status_time_str;?>
		</td>
	</tr>
	<?php
		}
	?>	
</table>
	<?php
}

function show_facility_types_select($facility_type = 0) {

	$query_facility_types = "SELECT * " .
		"FROM `facility_types` " .
		"ORDER BY `id`;";

	$result_facility_types = db_query($query_facility_types, __FILE__, __LINE__);
	$style_str = "";
	if (db_affected_rows($result_facility_types) > 0) {
		$style_str = "";
		if ($facility_type) {

			$query_selected_facility_type = "SELECT `bg_color`, " .
				"`text_color` " .
				"FROM `facility_types` " .
				"WHERE `id` = " . $facility_type . ";";

			$result_selected_facility_type = db_query($query_selected_facility_type, __FILE__, __LINE__);
			$row_selected_facility_type = stripslashes_deep(db_fetch_assoc($result_selected_facility_type));
			$style_str = " style='background-color: " . $row_selected_facility_type['bg_color'] . "; color: " . $row_selected_facility_type['text_color'] . ";'";
		}
	?>
				<select name="frm_type" class="form-control mandatory" tabindex=13 <?php print $style_str;?> onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor; this.style.color=this.options[this.selectedIndex].style.color;">
	<?php
		if (!$facility_type) {
	?>
					<option value=0><?php print get_text("Select");?></option>
	<?php
		}
		while ($row_f_types = stripslashes_deep(db_fetch_assoc($result_facility_types))) {
			$selected_str = ($facility_type == $row_f_types['id'])? " selected" : "";
			print "\t\t<option value=" . $row_f_types['id'] . $selected_str . " style='background-color: " . $row_f_types['bg_color'] . "; color: " . $row_f_types['text_color'] . ";' >" . $row_f_types['name'] . "</option>\n";
		}
		unset ($result_selected_facility_type);
	} else {
	?>
				<select name="frm_type" class="form-control"  style="background-color: #000000; color: #FFFFFF;">
					<option value=0><?php print get_text("No data");?></option>
	<?php
	}
	unset ($result_facility_types);
	?>
				</select>
	<?php
}

function show_facility_status_select($facility_status = 0) {

	$query_facility_status = "SELECT * " .
		"FROM `facility_status` " .
		"ORDER BY `sort` ASC, `status_name` ASC;";

	$result_facility_status = db_query($query_facility_status, __FILE__, __LINE__);
	if (db_affected_rows($result_facility_status) > 0) {
		$frm_status_updated_str = "";
		$style_str = "";
		if ($facility_status > 0) {

			$query_selected_facility_status = "SELECT `bg_color`, " .
				"`text_color` " .
				"FROM `facility_status` " .
				"WHERE `id` = " . $facility_status . ";";

			$result_selected_facility_status = db_query($query_selected_facility_status, __FILE__, __LINE__);
			$row_selected_facility_status = stripslashes_deep(db_fetch_assoc($result_selected_facility_status));
			$style_str = " style=' background-color: " . $row_selected_facility_status['bg_color'] . "; color: " . $row_selected_facility_status['text_color'] . ";'";
			$frm_status_updated_str = " document.edit_form.frm_status_update.value='1';";
			unset ($result_selected_facility_status);
		}
	?>
					<select name="frm_status_id" class="form-control mandatory" tabindex=14 <?php print $style_str;?> onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor; this.style.color=this.options[this.selectedIndex].style.color;<?php print $frm_status_updated_str;?>">
	<?php
		if (!$facility_status == 0) {
	?>
						<option value=0 selected><?php print get_text("Select");?></option>
	<?php
		}
		while ($row_facility_status = stripslashes_deep(db_fetch_array($result_facility_status))) {
			$selected_str = "";
			if ($facility_status == $row_facility_status['id']) {
				$selected_str = " selected";
			}
			print "\t\t<option value=" . $row_facility_status['id'] . $selected_str . " " .
				"style='background-color: " . $row_facility_status['bg_color'] . "; " .
				"color: " . $row_facility_status['text_color'] . ";' >" .
				$row_facility_status['status_name'] . "</option>\n";
		}

	} else {
	?>
					<select name="frm_status_id" class="form-control"  style="background-color: #000000; color: #FFFFFF;">
						<option value=0><?php print get_text("No data");?></option>
	<?php
	}
	unset ($result_facility_status);
	?>
					</select>
	<?php
}
?>