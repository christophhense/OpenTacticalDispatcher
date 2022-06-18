<?php
ini_set('session.cookie_samesite', 'Strict');
@session_start();
require_once ("./incs/units.inc.php");

function show_ticketlist($function = "", $screen_id = 0, $unit_id = 0) {
	$border_top_str = " border-top: 0px;";
	$my_offset = 0;
	$col_width_blank = 1;
	$col_width_dispatch_button = 0;
	$col_width_location = 25;
	$col_width_type = 15;
	$col_width_synopsis = 39;
	$col_width_name = 6;
	$col_width_units = 3;
	$col_width_actions = 3;
	$col_width_as_of = 8;
	switch ($function) {
	case "dispatch":
		$border_top_str = "";
		$col_width_blank = 0;
		$col_width_dispatch_button = 15;
		$col_width_location = 25;
		$col_width_type = 15;
		$col_width_synopsis = 35;
		$col_width_name = 10;
		$col_width_units = 0;
		$col_width_actions = 0;
		$col_width_as_of = 0;
		$result = get_units_data("WHERE `u`.`id` = " . $unit_id, "", "", "");
		$row = stripslashes_deep(db_fetch_assoc($result));
		break;
	default:
	}
	?>
<table class="table table-striped table-condensed" style="table-layout:fixed;">
	<tr>
		<?php if ($col_width_blank != 0) { ?>
		<th style="width: 1%; text-align: left;<?php print $border_top_str;?>"></th>
		<?php }  if ($function == "dispatch" && $col_width_dispatch_button != 0) { ?>
		<th style="width: <?php print $col_width_dispatch_button;?>%; text-align: left;<?php print $border_top_str;?>" <?php print  get_title_unit_str($row);?>>
			<span class="label" style="background-color: <?php print $row['background_color'];?>; color: <?php print $row['text_color'];?>; font-weight: bold; font-size: 12px;">
				<?php print	remove_nls($row['handle']);?>
			</span>
		</th>
		<?php } ?>
		<th style="width: <?php print $col_width_location;?>%; text-align: left;<?php print $border_top_str;?>" <?php print get_help_text_str("_loca");?>>
			<?php print get_text("Incident location");?>
		</th>
		<th style="width: <?php print $col_width_type;?>%; text-align: left;<?php print $border_top_str;?>" <?php print get_help_text_str("_incident_type");?>>
			<?php print get_text("Incident type");?>
		</th>
		<th style="width: <?php print $col_width_synopsis;?>%; text-align: left;<?php print $border_top_str;?>" <?php print get_help_text_str("_synop");?>>
			<?php print get_text("Synopsis");?>
		</th>
		<th style="width: <?php print $col_width_name;?>%; text-align: left;<?php print $border_top_str;?>" <?php print get_help_text_str("_name");?>>
			<?php print get_text("inc_name_short");?>
		</th>
		<?php if ($col_width_units != 0) { ?>
		<th style="width: <?php print $col_width_units;?>%; text-align: center;<?php print $border_top_str;?>" <?php print get_title_str(get_text("Units"));?>>
			<?php print get_text("U");?>
		</th>
		<?php } ?>
		<?php if ($col_width_actions != 0) { ?>
		<th style="width: <?php print $col_width_actions;?>%; text-align: center;<?php print $border_top_str;?>" <?php print get_title_str(get_text("Actions"));?>>
			<?php print get_text("A");?>
		</th>
		<?php } ?>
		<th style="width: <?php print $col_width_as_of;?>%; text-align: center;<?php print $border_top_str;?>" <?php print get_help_text_str("_asof");?>>
			<?php print get_text("As of");?>
		</th>
	</tr>
	<?php if ($function == "dispatch") { ?>
	<tr onclick="edit_ticket(0, <?php print $unit_id;?>);">
		<td>
			<div class="label dispatched col-md-12" style="height: auto; text-align: left;"><?php print get_text("New");?></div>
		</td>
		<td colspan=4></td>
	</tr>
	<?php }
	$closed_interval_settings = explode(",", get_variable("closed_interval"));
	$closed_ticket_time = trim($closed_interval_settings[0]);
	$time_back = mysql_datetime(time() - ($closed_ticket_time * 60));
	$allocates_where_str = get_allocates_where_str($GLOBALS['TYPE_USER'], $GLOBALS['TYPE_TICKET'], "AND");
	$hide_booked = get_variable("hide_booked");
	$situation_type = "tickets_units";
	if (isset ($_SESSION["screen_id_" . $screen_id]['situation_type'])) {
		$situation_type = $_SESSION["screen_id_" . $screen_id]['situation_type'];
	}
	if (($situation_type != "tickets_scheduled") && ($situation_type != "tickets_closed")) {
		$situation_type = "tickets_open";
	}
	switch ($situation_type) {
		case "tickets_open":
			$where_str = "WHERE (`tickets`.`status` = '" . $GLOBALS['STATUS_OPEN'] . "' " .
				"OR (`tickets`.`status` = '" . $GLOBALS['STATUS_SCHEDULED'] . "' " .
				"AND `tickets`.`booked_date` <= (NOW() + INTERVAL " . $hide_booked . " MINUTE)) " .
				"OR (`tickets`.`status` = '" . $GLOBALS['STATUS_CLOSED'] . "'  " .
				"AND `tickets`.`problemend` >= '" . $time_back . "') " .
				"OR (`tickets`.`status` = '" . $GLOBALS['STATUS_SCHEDULED'] . "' " .
				"AND `tickets`.`booked_date` IS NULL)) " . $allocates_where_str;
			$order_str = "ORDER BY `severity` DESC, `booked_date` ASC, `tickets`.`datetime` DESC,`status` DESC,`tickets`.`id` ASC";
			break;
		case "tickets_scheduled":
			$where_str = "WHERE (`tickets`.`status`='" . $GLOBALS['STATUS_SCHEDULED'] . "' " .
				"AND `tickets`.`booked_date` >= (NOW() + INTERVAL " . $hide_booked . " MINUTE)) " . $allocates_where_str . "";
			$order_str = "ORDER BY `severity` DESC, `booked_date` ASC, `tickets`.`datetime` DESC,`status` DESC,`tickets`.`id` ASC";
			break;
		case "tickets_closed":
			$report_last_settings = explode(",", get_variable("report_last"));
			$start_date = mysql_datetime(time() - trim($report_last_settings[0]) * 60);
			if ((isset ($_SESSION["screen_id_" . $screen_id]['closed_interval_start'])) && ($_SESSION["screen_id_" . $screen_id]['closed_interval_start'] != 0)) {
				$start_date = $_SESSION["screen_id_" . $screen_id]['closed_interval_start'];
			}
			$end_date = mysql_datetime();
			if ((isset ($_SESSION["screen_id_" . $screen_id]['closed_interval_end'])) && ($_SESSION["screen_id_" . $screen_id]['closed_interval_end'] != 0)) {
				$end_date = $_SESSION["screen_id_" . $screen_id]['closed_interval_end'];
			}
			$where_str = " WHERE (`tickets`.`status` = '" . $GLOBALS['STATUS_CLOSED'] . "' " .
				"AND `tickets`.`problemend` " .
				"BETWEEN '" . $start_date . "' AND '" . $end_date . "') " . $allocates_where_str . " ";
			$order_str = "ORDER BY `tickets`.`problemend` DESC";
			break;
		default:
			print "error - error - error - error " . __LINE__;
	}

	$query = "SELECT UNIX_TIMESTAMP(`problemstart`) AS `problemstart`, " .
		"UNIX_TIMESTAMP(`problemend`) AS `problemend`, " .
		"UNIX_TIMESTAMP(`booked_date`) AS `booked_date`, " .
		"UNIX_TIMESTAMP(`tickets`.`datetime`) AS `date`, " .
		"`tickets`.`location` AS `ticket_street`, " .
		"`tickets`.`phone` AS `tick_phone`, " .
		"`tickets`.`problemstart` AS `tick_problemstart`, " .
		"UNIX_TIMESTAMP(`tickets`.`updated`) AS `updated`, " .
		"`tickets`.`id` AS `ticket_id`, " .
		"`tickets`.`comments` AS `tick_comm`, " .
		"`incident_types`.type AS `type`, " .
		"`incident_types`.`id` AS `t_id`, " .
		"`incident_types`.`description` AS `t_des`, " .
		"`incident_types`.`protocol` AS `t_proto`, " .
		"`tickets`.`description` AS `ticket_description`, " .
		"`tickets`.`lat` AS `lat`, " .
		"`tickets`.`lng` AS `lng`, " .
		"`tickets`.`severity`, " .
		"`tickets`.`status`, " .
		"`tickets`.`contact`, " .
		"`tickets`.`incident_name`, " .
		"`facilities`.`lat` AS `fac_lat`, " .
		"`facilities`.`lng` AS `fac_lng`, " .
		"`facilities`.`name` AS `fac_name`, " .
		"`facilities`.`handle` AS `fac_handle`, " .
		"(SELECT  COUNT(*) as `numfound` FROM `assigns` " .
		"WHERE `assigns`.`ticket_id` = `tickets`.`id` " .
		"AND `clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00') " .
		"AS `units_assigned` " .
		"FROM `tickets` " .
		"LEFT JOIN `allocates` ON `tickets`.`id` = `allocates`.`resource_id` " .
		"LEFT JOIN `incident_types` ON `tickets`.`incident_type_id` = `incident_types`.`id` " .
		"LEFT JOIN `facilities` ON `tickets`.`facility_id` = `facilities`.`id` " . $where_str . " " .
		"GROUP BY `ticket_id` " . $order_str . " LIMIT 1000 OFFSET " . $my_offset . ";";

	$result = db_query($query, __FILE__, __LINE__);
	$count_tickets = 0;
	$actions = array ();

	$query = "SELECT DISTINCT `ticket_id`, " .
		"COUNT(*) AS `the_count` " .
		"FROM `actions` " .
		"GROUP BY `ticket_id`;";

	$result_actions = db_query($query, __FILE__, __LINE__);
	while ($row = stripslashes_deep(db_fetch_assoc($result_actions))) {
		$actions[$row['ticket_id']] = $row['the_count'];
	}
	unset ($result_actions);
	$count_severity = array (0, 0, 0);
	while ($row = stripslashes_deep(db_fetch_assoc($result))) {
		$count_severity[$row['severity']]++;
		if (($row['units_assigned'] == 0) && ($row['status'] != $GLOBALS['STATUS_CLOSED']) && ($row['severity'] != 0) && ($situation_type != "tickets_scheduled")) {
			$blink_str = "<span class='textblink'>";
			$blink_end_str = "</span>";
		} else {
			$blink_str = $blink_end_str = "";
		}
		$scheduled_on_situation_marker = "";
		if (($row['status'] == $GLOBALS['STATUS_SCHEDULED']) && ($situation_type != "tickets_scheduled")) {
			$scheduled_on_situation_marker = "*";
		}
		$ticket_description = get_text("[No description]");
		if ($row['ticket_description']) {
			$ticket_description = remove_nls($row['ticket_description']);
		}
		switch ($row['severity']) {
	 	case $GLOBALS['SEVERITY_MEDIUM']:
			$severityclass = "severity_medium";
			break;
		case $GLOBALS['SEVERITY_HIGH']:
			$severityclass = "severity_high";
			break;
		default:
			$severityclass = "severity_normal";
			break;
		}
		if (isset($actions) && array_key_exists($row['ticket_id'], $actions)) {
			$count_actions = $actions[$row['ticket_id']];
		} else {
			$count_actions = 0;
		}
		if ($row['status'] == $GLOBALS['STATUS_CLOSED']) {
			$strike = "<span style='text-decoration: line-through;'>"; $strikend = "</span>";
		} else {
			$strike = $strikend = "";
		}
		$address_street = get_text("[No Address]");
		if ($row['ticket_street']) {
			$address_street = remove_nls($row['ticket_street']);
		}
		$title_ticket = remove_nls(wordwrap(get_title_ticket($row), 80, "<br>", true));
		$title_units_str = get_title_dispatched_str($row);
		if (!$title_units_str[2]) {
			$blink_str = $blink_end_str = "";
			$title_ticket .= "------------------------------<br>";
			$title_ticket .= get_text("Units") . ":<br>";
			$title_ticket .= $title_units_str[0];
		}
		$title_action_str = get_title_action_str($row);
		if (!$title_action_str[1]) {
			$title_ticket .= "------------------------------<br>";
			$title_ticket .= get_text("Actions") . ":<br>";
			$title_ticket .= $title_action_str[0];
		}
		?>
	<tr>
		<?php if ($col_width_blank != 0) { ?>
		<td></td>
		<?php } if ($function == "dispatch" && $col_width_dispatch_button != 0) { ?>
		<td<?php print get_nowrap_title_str($title_ticket);?> onclick="edit_ticket(<?php print $row['ticket_id'];?>, <?php print $unit_id;?>);">
			<div class="label dispatched col-md-12" style="height: auto; text-align: left;"><?php print get_text("Dispatch_Units_short");?></div>
		</td>
		<?php } ?>
		<td style="text-align: left;"<?php print get_nowrap_title_str($title_ticket);?> class="<?php print $severityclass;?>" onclick="edit_ticket(<?php print $row['ticket_id'];?>, <?php print $unit_id;?>);">
			<?php print $blink_str;?><nobr><?php print $strike;?>
				<div style="overflow: hidden; text-overflow: ellipsis;">
					<?php print $address_street;?>
				</div>
			<?php print $strikend;?></nobr><?php print $blink_end_str;?>
		</td>
		<td style="text-align: left;"<?php print get_title_type_str($row);?> class="<?php print $severityclass;?>" onclick="edit_ticket(<?php print $row['ticket_id'];?>, <?php print ($unit_id);?>);">
			<?php print $blink_str;?><nobr><?php print $strike;?>
				<div style="overflow: hidden; text-overflow: ellipsis;">
					<?php print remove_nls($row['type']);?>
				</div>
			<?php print $strikend;?></nobr><?php print $blink_end_str;?>
		</td>
		<td style="text-align: left;"<?php print get_nowrap_title_str($title_ticket);?> class="<?php print $severityclass;?>" onclick="edit_ticket(<?php print $row['ticket_id'];?>, <?php print ($unit_id);?>);">
			<?php print $blink_str;?><nobr><?php print $strike;?>
				<div style="overflow: hidden; text-overflow: ellipsis;">
					<?php print $ticket_description;?>
				</div>
			<?php print $strikend;?></nobr><?php print $blink_end_str;?>
		</td>
		<td style="text-align: left;"<?php print get_nowrap_title_str($title_ticket);?> class="<?php print $severityclass;?>" onclick="edit_ticket(<?php print $row['ticket_id'];?>, <?php print ($unit_id);?>);">
			<?php print $blink_str;?><nobr><?php print $strike;?>
				<div style="overflow: hidden; text-overflow: ellipsis;">
					<?php print $scheduled_on_situation_marker . remove_nls($row['incident_name']);?>
				</div>
			<?php print $strikend;?></nobr><?php print $blink_end_str;?>
		</td>
		<?php if ($col_width_units != 0) { ?>
		<td style="text-align: center;"<?php print $title_units_str[1];?> class="<?php print $severityclass;?>" onclick="edit_ticket(<?php print $row['ticket_id'];?>, <?php print ($unit_id);?>);">
			<?php print $blink_str . $row['units_assigned'] . $blink_end_str;?>
		</td>
		<?php } ?>
		<?php if ($col_width_actions != 0) { ?>
		<td style="text-align: center;"<?php print $title_action_str[1];?> class="<?php print $severityclass;?>" onclick="edit_ticket(<?php print $row['ticket_id'];?>, <?php print ($unit_id);?>);">
			<?php print $blink_str . $count_actions . $blink_end_str;?>
		</td>
		<?php } ?>
		<?php
		$as_of_datetime = $row['updated'];
		$utc = gmdate("U");
		if (abs($utc - $as_of_datetime) > (get_variable("tolerance") * 60)) {
			$strike = "<span style=\"text-decoration: line-through;\">";
			$strike_end = "</span>";
			$strike_date_time = "<span style='text-decoration: line-through;'>";
			$strike_end_date_time = "</span>";
		} else {
			$strike = $strike_end = "";
			$strike_date_time = $strike_end_date_time = "";
		}
		if ($function == "dispatch") {
	?>
		<td></td>
	<?php } else { ?>
		<td<?php print get_title_str($strike . date(get_variable("date_format"), $as_of_datetime) . $strike_end);?> align='center' onclick='edit_ticket(<?php print $row['ticket_id'];?>, <?php print ($unit_id);?>);'>
			<?php print $blink_str . " " . $strike_date_time . date(get_variable("date_format_time_only"), $as_of_datetime) . $strike_end_date_time . " " . $blink_end_str;?>
		</td>
	</tr>
	<?php
		}
		$count_tickets++;
	}
	$no_tickets_text = get_text("No current tickets!");
	if ($count_tickets == 0) {
		if (
			($situation_type == "tickets_closed") ||
			($situation_type == "tickets_scheduled")
		) {
			$no_tickets_text = get_text("No closed tickets this period!");
		}
	?>
	<tr><th colspan=8 style="text-align: center;"><?php print $no_tickets_text;?></th></tr>
	<?php } ?>
</table>
<div style="display: none;" id="count_severity_normal"><?php print $count_severity[$GLOBALS['SEVERITY_NORMAL']];?></div>
<div style="display: none;" id="count_severity_medium"><?php print $count_severity[$GLOBALS['SEVERITY_MEDIUM']];?></div>
<div style="display: none;" id="count_severity_high"><?php print $count_severity[$GLOBALS['SEVERITY_HIGH']];?></div>
<?php } ?>