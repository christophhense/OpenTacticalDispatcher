<?php
error_reporting(E_ALL);
require_once ("./incs/install.inc.php");
require_once ("./incs/functions.inc.php");

function show_stats() {	
	$memb_in_db 		= db_num_rows(db_query("SELECT * FROM `users` WHERE level = " . $GLOBALS['LEVEL_MEMBER'] . " AND `password` != '55606758fdb765ed015f0612112a6ca7';", __FILE__, __LINE__));
	$oper_in_db 		= db_num_rows(db_query("SELECT * FROM `users` WHERE level = " . $GLOBALS['LEVEL_OPERATOR'] . " AND `password` != '55606758fdb765ed015f0612112a6ca7';", __FILE__, __LINE__));
	$admin_in_db 		= db_num_rows(db_query("SELECT * FROM `users` WHERE level = " . $GLOBALS['LEVEL_ADMINISTRATOR'] . " AND `password` != '55606758fdb765ed015f0612112a6ca7';", __FILE__, __LINE__));
	$guest_in_db 		= db_num_rows(db_query("SELECT * FROM `users` WHERE level = " . $GLOBALS['LEVEL_GUEST'] . " AND `password` != '55606758fdb765ed015f0612112a6ca7';", __FILE__, __LINE__));
	$super_in_db 		= db_num_rows(db_query("SELECT * FROM `users` WHERE level = " . $GLOBALS['LEVEL_SUPER'] . " AND `password` != '55606758fdb765ed015f0612112a6ca7';", __FILE__, __LINE__));
	$stats_in_db 		= db_num_rows(db_query("SELECT * FROM `users` WHERE level = " . $GLOBALS['LEVEL_STATS'] . " AND `password` != '55606758fdb765ed015f0612112a6ca7';", __FILE__, __LINE__));
	$ticket_in_db 		= db_num_rows(db_query("SELECT * FROM `tickets`;", __FILE__, __LINE__));
	$ticket_sched_in_db = db_num_rows(db_query("SELECT * FROM `tickets` WHERE status = '" . $GLOBALS['STATUS_SCHEDULED'] . "';", __FILE__, __LINE__));
	$ticket_open_in_db 	= db_num_rows(db_query("SELECT * FROM `tickets` WHERE status = '" . $GLOBALS['STATUS_OPEN'] . "';", __FILE__, __LINE__));
	$ticket_rsvd_in_db 	= db_num_rows(db_query("SELECT * FROM `tickets` WHERE status = '" . $GLOBALS['STATUS_RESERVED'] . "';", __FILE__, __LINE__));
	if ($ticket_rsvd_in_db == 0) {
		$rsvd_str = "";
	} else {
		$rsvd_str = get_text("Reserved") . ": " . $ticket_rsvd_in_db . ", ";
	}
	$ticket_closed_in_db = $ticket_in_db - $ticket_sched_in_db - $ticket_open_in_db - $ticket_rsvd_in_db;
	$tickets_str = $rsvd_str . get_text("Scheduled") . ": " . $ticket_sched_in_db . ", " . get_text("Open") . ": " . $ticket_open_in_db .
		", " . get_text("Closed") . ": " .  $ticket_closed_in_db . ", " . get_text("total") . ": " . $ticket_in_db;
	$type_color = array ();

	$query = "SELECT * " .
		"FROM `unit_types`";

	$result = db_query($query, __FILE__, __LINE__);
	while ($row = stripslashes_deep(db_fetch_assoc($result))) {
		$type_color[$row['id']]=  $row['name'];
	}
	unset ($result);

	$query = "SELECT `r`.`type`, COUNT(`a`.`id`) AS `the_count` " .
		"FROM `units` `r` " .
		"JOIN `allocates` `a` ON `r`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
		"GROUP BY `r`.`type`;";

	$result = db_query($query, __FILE__, __LINE__);
	$total = 0;
	$count_units_str = "";
	while ($row = stripslashes_deep(db_fetch_assoc($result))) {
		$total += $row['the_count'];
		$count_units_str .=  $type_color[$row['type']] . ": " . $row['the_count'] . ", ";
	}
	$count_units_str .= get_text("total") . ": " . $total;
	unset ($result);
	$count_users_str = get_text("permission_super") . ": " . $super_in_db;
	if ($admin_in_db != 0) {
		$count_users_str .= ", " . get_text("permission_admin") . ": " . $admin_in_db;
	}
	if ($oper_in_db != 0) {
		$count_users_str .= ", " . get_text("permission_operator") . ": " . $oper_in_db;
	}
	if ($guest_in_db != 0) {
		$count_users_str .= ", " . get_text("permission_guest") . ": " . $guest_in_db;
	}
	$count_users_str .= ", " . get_text("total") . ": " . ($super_in_db+$oper_in_db+$admin_in_db+$guest_in_db+$memb_in_db+$stats_in_db);

	$query = "SELECT * " .
		"FROM `log`";

	$result = db_query($query, __FILE__, __LINE__);
	$nr_logs = db_affected_rows($result);
	unset ($result);
	$moment_date_format = php_to_moment(get_variable("date_format"));
	$connection_test_button_str = "";
	$current_radio_str = "";
	if (is_super() || is_admin() || is_operator()) {
		$connection_test_button_str = "&nbsp;<button " . get_help_text_str("connection test button") . "onclick=\"do_api_connection_test(false, '" . get_text("Connection test") . "');\">" . get_text("Connection test") . "</button>";
		$current_radio_str = "<tr><th>" . get_text("Current radio") . ":</th><td" . get_title_str("") . " id=\"current_radio\"></td></tr>";
	}
	$browser = check_browser();
	?>
	<script>

		function set_current_infos() {
			$("#server_time").html(new_infos_array['screen']['date_time']);
			var current_radio = "<?php print get_text("No filter set.");?>";
			if (new_infos_array['api']['current_radio'] != "") {
				current_radio = new_infos_array['api']['current_radio'];
			}
			$("#current_radio").html(current_radio);
			var available_datetime = moment(new_infos_array['api']['host_timestamp_current_state'], "YYYY-MM-DD HH:mm:ss").format("<?php print $moment_date_format;?>");
			var available_text = "<?php print get_text("Not available since");?>: " + available_datetime;
			var info_text = "<?php print "&nbsp;&nbsp;" . get_text("No answer from foreign host.");?>";
			var text_color = "red";
			switch (new_infos_array['api']['host_available']) {
				case "true":
					text_color = "black";
					available_text = "<?php print get_text("Available since");?>: " + available_datetime;
					if (new_infos_array['api']['host_text'] != "") {
						info_text = "&nbsp;&nbsp;" + new_infos_array['api']['host_text'];
					} else {
						info_text = "<?php print "&nbsp;&nbsp;" . get_text("No info-text available.");?>";
					}
					switch (new_infos_array['api']['host_code']) {
						case "success":
							text_color = "green";
							break;
						case "warning":
							text_color = "orange";
							break;
						case "error":
							text_color = "red";
							break;
						default:
					}
					break;
				case "null":
					available_text = "<?php print get_text("Not configured.");?>";
					info_text = "";
					text_color = "black";
					break;
				default:
			}
			if ($("#application_interface").html() != (available_text + info_text)) {
				UnTip();
			}
			$("#application_interface").html(available_text + info_text);
			$("#application_interface").css("color", text_color);
			available_datetime = moment(new_infos_array['api']['phone_host_timestamp_current_state'], "YYYY-MM-DD HH:mm:ss").format("<?php print $moment_date_format;?>");
			available_text = "<?php print get_text("Not available since");?>: " + available_datetime;
			info_text = "<?php print "&nbsp;&nbsp;" . get_text("No answer from foreign host.");?>";
			text_color = "red";
			switch (new_infos_array['api']['phone_host_available']) {
				case "true":
					text_color = "black";
					available_text = "<?php print get_text("Available since");?>: " + available_datetime;
					if (new_infos_array['api']['phone_host_text'] != "") {
						info_text = "&nbsp;&nbsp;" + new_infos_array['api']['phone_host_text'];
					} else {
						info_text = "<?php print "&nbsp;&nbsp;" . get_text("No info-text available.");?>";
					}
					switch (new_infos_array['api']['phone_host_code']) {
						case "success":
							text_color = "green";
							break;
						case "warning":
							text_color = "orange";
							break;
						case "error":
							text_color = "red";
							break;
						default:
					}
					break;
				case "null":
					available_text = "<?php print get_text("Not configured.");?>";
					info_text = "";
					text_color = "black";
					break;
				default:
			}
			if ($("#application_interface_phone").html() != (available_text + info_text)) {
			UnTip();
			}
			$("#application_interface_phone").html(available_text + info_text);
			$("#application_interface_phone").css("color", text_color);
		}

		function show_api_tip() {
			Tip($("#application_interface").html().trim().replace(/(\S(.{0,78}\S)?)\s+/g, '$1<br>'));
		}

		function show_api_phone_tip() {
			Tip($("#application_interface_phone").html().trim().replace(/(\S(.{0,78}\S)?)\s+/g, '$1<br>'));
		}

	</script>
	<div class="panel panel-default" id="table_top" style="padding: 0px;">
		<table class="table table-striped table-condensed" style="table-layout: fixed;">
			<tr>
				<th style="width: 30%;"><?php print get_text("Software-Version");?>:</th>
				<td style="width: 70%;"<?php print get_title_str(get_version());?>><?php print get_version();?></td>
			</tr>
			<tr>
				<th><?php print get_text("Localization");?>:</th>
				<td<?php print get_title_str(get_variable("_locale"));?>><?php print get_variable("_locale");?></td>
			</tr>
			<tr>
				<th><?php print get_text("time zone");?>:</th>
				<td<?php print get_title_str(date_default_timezone_get());?>><?php print date_default_timezone_get();?></td>
			</tr>
			<tr>
				<th><?php print get_text("Server time");?>:</th>
				<td id="server_time"></td>
			</tr>
			<tr>
				<th><?php print get_text("Server OS");?>:</th>
				<td<?php print get_title_str(php_uname());?>><?php print php_uname();?></td>
			</tr>
			<tr>
				<th><?php print get_text("PHP-Version");?>:</th>
				<td<?php print get_title_str(phpversion() . " under " . $_SERVER['SERVER_SOFTWARE']);?>><?php print phpversion() . " under " . $_SERVER['SERVER_SOFTWARE'];?></td>
			</tr>
			<tr>
				<th><?php print get_text("Database");?>:</th>
				<td<?php print get_title_str($GLOBALS['db_name'] . " db-scheme-version" . " " . get_variable("_version") . " on " . $GLOBALS['db_host'] . " running " . db_get_server_info());?>><?php print $GLOBALS['db_name'] . " db-scheme-version" . " " . get_variable("_version") . " on " . $GLOBALS['db_host'] . " running " . db_get_server_info() . " ";?></td>
			</tr>
			<tr>
				<th><?php print get_text("Tickets in database");?>:</th>
				<td<?php print get_title_str($tickets_str);?>><?php print $tickets_str;?></td>
			</tr>
			<tr>
				<th><?php print get_text("Log records in database");?>:</th>
				<td<?php print get_title_str($nr_logs);?>><?php print $nr_logs;?></td>
			</tr>
			<tr>
				<th><?php print get_text("Units in database");?>:</th>
				<td<?php print get_title_str($count_units_str);?>><?php print $count_units_str;?></td>
			</tr>
			<tr>
				<th><?php print get_text("Users in database");?>:</th>
				<td<?php print get_title_str($count_users_str);?>><?php print $count_users_str;?></td>
			</tr>
			<tr>
				<th><?php print get_text("Application Interface");?>:<?php print $connection_test_button_str;?></th>
				<td onmouseover="show_api_tip();" onmouseout="UnTip();" id="application_interface"></td>
			</tr>
			<tr>
				<th<?php print get_help_text_str("_api_phone_host");?>><?php print get_text("Application Interface phone");?>:</th>
				<td onmouseover="show_api_phone_tip();" onmouseout="UnTip();" id="application_interface_phone"></td>
			</tr>
			<?php print $current_radio_str;?>
			<tr>
				<th><?php print get_text("Current User");?>:</th>
				<td<?php print get_title_str($_SESSION['user_name'] . ", " . get_level_text($_SESSION['level']));?>><?php print $_SESSION['user_name'];?>, <?php print get_level_text($_SESSION['level']);?></td>
			</tr>
			<tr>
				<th><?php print get_text("Visting from");?>:</th>
				<td<?php print get_title_str($_SERVER['REMOTE_ADDR'] . ", " . gethostbyaddr($_SERVER['REMOTE_ADDR']));?>><?php print $_SERVER['REMOTE_ADDR'];?>, <?php print gethostbyaddr($_SERVER['REMOTE_ADDR']);?></td>
			</tr>
			<tr>
				<th><?php print get_text("Browser");?>:</th>
				<td<?php print get_title_str($browser);?>><?php print $browser;?></td>
			</tr>
		</table>
	</div>
	<?php
}

function show_userlist() {

	$query = "SELECT * " .
		"FROM `users` " .
		"WHERE `password` != '55606758fdb765ed015f0612112a6ca7' " .
		"ORDER BY `id` ASC;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_affected_rows($result) == 0) {
		print "<strong>[no users found]</strong>";
		return;
	}
	$table_width = array ("427px", "38px", "28px", "120px", "120px", "120px", "0px", "0px", "0px", "0px");
	$display_str = " display: none;";
	if (is_super()) {
		$table_width = array ("1066px", "38px", "28px", "120px", "120px", "120px", "40px", "150px", "300px", "150px");
		$display_str = "";
	}
	?>
	<div class="panel panel-default" id="table_top" style="padding: 0px; margin-bottom: 10px; width: <?php print $table_width[0];?>;">
		<table class="table table-striped table-condensed" style="table-layout: fixed;">
			<tr>
				<th style="width: <?php print $table_width[1];?>;"></th>
				<th style="width: <?php print $table_width[2];?>; text-align: center;"><?php print get_message_click_str("user_all", 0, "An alle", 0, "", "", "");?></th>
				<th style="width: <?php print $table_width[3];?>;"><?php print get_text("User");?></th>
				<th style="width: <?php print $table_width[4];?>;"><?php print get_text("Level");?></th>
				<th style="width: <?php print $table_width[5];?>; text-align: center;"><?php print get_text("Online");?></th>
				<th style="width: <?php print $table_width[6];?>; text-align: center;<?php print $display_str;?>"><?php print get_text("ID");?></th>
				<th style="width: <?php print $table_width[7];?>; text-align: center;<?php print $display_str;?>"><?php print get_text("Last login");?></th>
				<th style="width: <?php print $table_width[8];?>; text-align: center;<?php print $display_str;?>"><?php print get_text("Client address");?></th>
				<th style="width: <?php print $table_width[9];?>; text-align: center;<?php print $display_str;?>"><?php print get_text("Browser");?></th>
			</tr>
	<?php
	$datetime_now = mysql_datetime();
	while ($row = stripslashes_deep(db_fetch_array($result))) {
		$edit_str = "";
		if (is_super() || (is_admin() && $row['level'] >= $GLOBALS['LEVEL_OPERATOR'] && $row['id'] != get_variable("_api_user_id"))) {
			$edit_str = "<span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\" style=\"font-size: 12px;\" " .
				"onclick=\"self.location.href='configuration.php?function=user_edit&id=" . $row['id'] . "' \"></span>";
		}
		$level = get_level_text($row['level']);
		$online = "";
		if ($row['expires'] > $datetime_now) {
			$online = "<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\" style=\"font-size: 12px;\"></span>";
		}
		$login = "";
		if (substr($row['login_datetime'], 0, 4) != "2017") {
			$login = date(get_variable("date_format"), strtotime($row['login_datetime']));
		}
		$client_address = "";
		if ($row['login_address'] != "0.0.0.0") {
			$client_address = $row['login_address'];
		}
	?>
			<tr>
				<td style="text-align: center;"><?php print $edit_str;?></td>
				<td style="text-align: center;"><?php print get_message_click_str("user", $row['id'], 0, $row['name'], $row['email'], "", "", "");?></td>
				<td<?php print get_help_text_str("level_" . $row['level']);?>><?php print remove_nls($row['name']);?></td>
				<td<?php print get_help_text_str("level_" . $row['level']);?>><?php print $level;?></td>
				<td<?php print get_help_text_str("level_" . $row['level']);?> style="text-align: center;"><?php print $online;?></td>
				<td<?php print get_help_text_str("level_" . $row['level']);?> style="text-align: center;<?php print $display_str;?>"><?php print $row['id'];?></td>
				<td<?php print get_help_text_str("level_" . $row['level']);?> style="text-align: center;<?php print $display_str;?>"><?php print $login;?></td>
				<td<?php print get_help_text_str("level_" . $row['level']);?> style="text-align: center;<?php print $display_str;?>"><?php print $client_address;?></td>
				<td<?php print get_help_text_str("level_" . $row['level']);?> style="text-align: center;<?php print $display_str;?>"><?php print $row['browser'];?></td>
			</tr>
	<?php
	}
	?>
		</table>
	</div>
	<?php
}

function call_progression_captions($call_progression_name) {
	global $types;
	$call_progression_caption = array (
		"_api_quat_encdg" => $types[$GLOBALS['LOG_UNIT_TO_QUARTERS']],
		"_api_resp_encdg" => $types[$GLOBALS['LOG_CALL_RESPONDING']],
		"_api_onsc_encdg" => $types[$GLOBALS['LOG_CALL_ON_SCENE']],
		"_api_fcen_encdg" => $types[$GLOBALS['LOG_CALL_FACILITY_ENROUTE']],
		"_api_fcar_encdg" => $types[$GLOBALS['LOG_CALL_FACILITY_ARRIVED']],
		"_api_clr_encdg" => $types[$GLOBALS['LOG_CALL_CLEAR']]);
	if (isset ($call_progression_caption[$call_progression_name])) {
		return $call_progression_caption[$call_progression_name];
	} else {
		return get_text($call_progression_name);
	}
}

function get_units_status_name($unit_id) {

	$query = "SELECT * " .
		"FROM `unit_status` " .
		"WHERE `id` = " . $unit_id . ";";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) > 0) {
		$row = stripslashes_deep(db_fetch_assoc($result));
		$unit_name = $row['status_name'] . " - " . $row['description'];
	} else {
		$unit_name = "";
	}
	return $unit_name;
}

function get_facilities_status_name($facility_id) {

	$query = "SELECT * " .
		"FROM `facility_status` " .
		"WHERE `id` = " . $facility_id;

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result) > 0) {
		$row = stripslashes_deep(db_fetch_assoc($result));
		$facility_name = $row['status_name'] . " - " . $row['description'];
	} else {
		$facility_name = "";
	}
	return $facility_name;
}

function color_name_to_hex($color_name) {
	// standard 147 HTML color names
	$colors  =  array(
		'aliceblue'=>'F0F8FF',
		'antiquewhite'=>'FAEBD7',
		'aqua'=>'00FFFF',
		'aquamarine'=>'7FFFD4',
		'azure'=>'F0FFFF',
		'beige'=>'F5F5DC',
		'bisque'=>'FFE4C4',
		'black'=>'000000',
		'blanchedalmond '=>'FFEBCD',
		'blue'=>'0000FF',
		'blueviolet'=>'8A2BE2',
		'brown'=>'A52A2A',
		'burlywood'=>'DEB887',
		'cadetblue'=>'5F9EA0',
		'chartreuse'=>'7FFF00',
		'chocolate'=>'D2691E',
		'coral'=>'FF7F50',
		'cornflowerblue'=>'6495ED',
		'cornsilk'=>'FFF8DC',
		'crimson'=>'DC143C',
		'cyan'=>'00FFFF',
		'darkblue'=>'00008B',
		'darkcyan'=>'008B8B',
		'darkgoldenrod'=>'B8860B',
		'darkgray'=>'A9A9A9',
		'darkgreen'=>'006400',
		'darkgrey'=>'A9A9A9',
		'darkkhaki'=>'BDB76B',
		'darkmagenta'=>'8B008B',
		'darkolivegreen'=>'556B2F',
		'darkorange'=>'FF8C00',
		'darkorchid'=>'9932CC',
		'darkred'=>'8B0000',
		'darksalmon'=>'E9967A',
		'darkseagreen'=>'8FBC8F',
		'darkslateblue'=>'483D8B',
		'darkslategray'=>'2F4F4F',
		'darkslategrey'=>'2F4F4F',
		'darkturquoise'=>'00CED1',
		'darkviolet'=>'9400D3',
		'deeppink'=>'FF1493',
		'deepskyblue'=>'00BFFF',
		'dimgray'=>'696969',
		'dimgrey'=>'696969',
		'dodgerblue'=>'1E90FF',
		'firebrick'=>'B22222',
		'floralwhite'=>'FFFAF0',
		'forestgreen'=>'228B22',
		'fuchsia'=>'FF00FF',
		'gainsboro'=>'DCDCDC',
		'ghostwhite'=>'F8F8FF',
		'gold'=>'FFD700',
		'goldenrod'=>'DAA520',
		'gray'=>'808080',
		'green'=>'008000',
		'greenyellow'=>'ADFF2F',
		'grey'=>'808080',
		'honeydew'=>'F0FFF0',
		'hotpink'=>'FF69B4',
		'indianred'=>'CD5C5C',
		'indigo'=>'4B0082',
		'ivory'=>'FFFFF0',
		'khaki'=>'F0E68C',
		'lavender'=>'E6E6FA',
		'lavenderblush'=>'FFF0F5',
		'lawngreen'=>'7CFC00',
		'lemonchiffon'=>'FFFACD',
		'lightblue'=>'ADD8E6',
		'lightcoral'=>'F08080',
		'lightcyan'=>'E0FFFF',
		'lightgoldenrodyellow'=>'FAFAD2',
		'lightgray'=>'D3D3D3',
		'lightgreen'=>'90EE90',
		'lightgrey'=>'D3D3D3',
		'lightpink'=>'FFB6C1',
		'lightsalmon'=>'FFA07A',
		'lightseagreen'=>'20B2AA',
		'lightskyblue'=>'87CEFA',
		'lightslategray'=>'778899',
		'lightslategrey'=>'778899',
		'lightsteelblue'=>'B0C4DE',
		'lightyellow'=>'FFFFE0',
		'lime'=>'00FF00',
		'limegreen'=>'32CD32',
		'linen'=>'FAF0E6',
		'magenta'=>'FF00FF',
		'maroon'=>'800000',
		'mediumaquamarine'=>'66CDAA',
		'mediumblue'=>'0000CD',
		'mediumorchid'=>'BA55D3',
		'mediumpurple'=>'9370D0',
		'mediumseagreen'=>'3CB371',
		'mediumslateblue'=>'7B68EE',
		'mediumspringgreen'=>'00FA9A',
		'mediumturquoise'=>'48D1CC',
		'mediumvioletred'=>'C71585',
		'midnightblue'=>'191970',
		'mintcream'=>'F5FFFA',
		'mistyrose'=>'FFE4E1',
		'moccasin'=>'FFE4B5',
		'navajowhite'=>'FFDEAD',
		'navy'=>'000080',
		'oldlace'=>'FDF5E6',
		'olive'=>'808000',
		'olivedrab'=>'6B8E23',
		'orange'=>'FFA500',
		'orangered'=>'FF4500',
		'orchid'=>'DA70D6',
		'palegoldenrod'=>'EEE8AA',
		'palegreen'=>'98FB98',
		'paleturquoise'=>'AFEEEE',
		'palevioletred'=>'DB7093',
		'papayawhip'=>'FFEFD5',
		'peachpuff'=>'FFDAB9',
		'peru'=>'CD853F',
		'pink'=>'FFC0CB',
		'plum'=>'DDA0DD',
		'powderblue'=>'B0E0E6',
		'purple'=>'800080',
		'red'=>'FF0000',
		'rosybrown'=>'BC8F8F',
		'royalblue'=>'4169E1',
		'saddlebrown'=>'8B4513',
		'salmon'=>'FA8072',
		'sandybrown'=>'F4A460',
		'seagreen'=>'2E8B57',
		'seashell'=>'FFF5EE',
		'sienna'=>'A0522D',
		'silver'=>'C0C0C0',
		'skyblue'=>'87CEEB',
		'slateblue'=>'6A5ACD',
		'slategray'=>'708090',
		'slategrey'=>'708090',
		'snow'=>'FFFAFA',
		'springgreen'=>'00FF7F',
		'steelblue'=>'4682B4',
		'tan'=>'D2B48C',
		'teal'=>'008080',
		'thistle'=>'D8BFD8',
		'tomato'=>'FF6347',
		'turquoise'=>'40E0D0',
		'violet'=>'EE82EE',
		'wheat'=>'F5DEB3',
		'white'=>'FFFFFF',
		'whitesmoke'=>'F5F5F5',
		'yellow'=>'FFFF00',
		'yellowgreen'=>'9ACD32'
	);

	$color_name = strtolower($color_name);
	if (isset ($colors[$color_name])) {
		return ("#" . $colors[$color_name]);
	} else {
		return (strtoupper($color_name));
	}
}

$fopen_context_options = array (
	"GET" => array (
		"timeout" => 2.0
	)
);

define("VERSION", 0);
define("ZIP_LINK", 1);
define("RELEASE_TXT_LINK", 2);
define("MD5SUM", 3);
define("OPTION", 4);
define("RELEASE_TXT", 5);
define("FOPEN_CONTEXT", stream_context_create($fopen_context_options));

function sort_releases($a, $b) {
	$version_array_a = explode(".", $a[VERSION]);
	$version_array_b = explode(".", $b[VERSION]);
	foreach ($version_array_a as $key => $value) {
		if (isset ($version_array_b[$key])) {
			if ($value != $version_array_b[$key]) {
				if ($value < $version_array_b[$key]) {
					return -1;
				} else {
					return 1;
				}
			}
		} else {
			return 1;
		}
	}
	return 0;
}

function get_release_list() {
	$i = 1;
	$release_list_array = array ();
	$release_list_file = @fopen(get_variable("release_file"), "rb", false, FOPEN_CONTEXT);
	if (!$release_list_file) {
		$release_list_array[$i][VERSION] = "false";
		return $release_list_array;
	} else {
		while (!feof($release_list_file)) {
			$line = fgetcsv($release_list_file, 4096, ";");
			if (($line != false) && (array_key_exists(ZIP_LINK, $line))) {
				$release_list_array[$i][VERSION] = $line[VERSION];
				$release_list_array[$i][ZIP_LINK] = $line[ZIP_LINK];
				$release_list_array[$i][RELEASE_TXT_LINK] = $line[RELEASE_TXT_LINK];
				$release_list_array[$i][MD5SUM] = $line[MD5SUM];
				$release_list_array[$i][OPTION] = $line[OPTION];
				$i++;
			}
		}
		fclose($release_list_file);
	}
	usort($release_list_array, "sort_releases");
	return $release_list_array;
}

$not_writable_array = array ();
function writable_test($dir) {
	global $not_writable_array;
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			if ($dir[strlen($dir) - 1] != "/") {
				$dir = $dir . "/";
			}
			while (($file = readdir($dh)) !== false) {
				if ($file != "." && $file != "..") {
					if (is_dir($dir . $file) && !is_link($dir . $file)) {
						if (!is_writable($dir . $file)) {
							array_push($not_writable_array, $dir . $file);
						}
						writable_test($dir . $file);
					} else {
						if (is_file($dir . $file)) {
							if (!is_writable($dir . $file)) {
								array_push($not_writable_array, $dir . $file);
							}
						}
					}
				}
			}
			closedir($dh);
		}
	}
}

function update_is_writable() {
	global $not_writable_array;
	if (!is_writable(getcwd())) {
		array_push($not_writable_array, getcwd());
	}
	writable_test(getcwd());
	return $not_writable_array;
}

function get_default_incident_types($file) {
	if (file_exists($file)) {
		define("INCIDENT_TYPES_TABLE", 0);
		define("INCIDENT_TYPES_TYPE", 1);
		define("INCIDENT_TYPES_SEVERITY", 2);
		define("INCIDENT_TYPES_GROUP", 3);
		define("INCIDENT_TYPES_SORT", 4);
		define("INCIDENT_TYPES_DESCRIPTION", 5);
		define("INCIDENT_TYPES_PROTOCOL", 6);
		define("INCIDENT_TYPES_DELETE", 7);
		$line = "";
		$return_str = "<table style='table-layout: fixed; width: 100%;'>";
		$return_str .= "<tr><td style='padding: 2px; vertical-align: top;width: 15%;'>" . get_text("Incident types") .
			"</td><td style='padding: 2px; vertical-align: top;width: 10%;'>" . get_text("Severity") .
			"</td><td style='padding: 2px; vertical-align: top;width: 30%;'>" . get_text("Description") .
			"</td><td style='padding: 2px; vertical-align: top;width: 25%;'>" . get_text("Protocol") .
			"</td><td style='padding: 2px; vertical-align: top;width: 10%;'>" . get_text("Sort group") .
			"</td><td style='padding: 2px; vertical-align: top;width: 10%;'>" . get_text("Sort") . "</td></tr>";
		$uploadfile = fopen($file, "rb");
		while (!feof($uploadfile)) {
			$line = fgetcsv($uploadfile, 4096, ";");
			$severity = get_text("Normal");
			if (($line != false) && ($line[INCIDENT_TYPES_TABLE] == "incident_types")) {
				if ((strtolower($line[INCIDENT_TYPES_SEVERITY]) == "notfall") || (strtolower($line[INCIDENT_TYPES_SEVERITY]) == "high")) {
					$severity = get_text("High");
				} else {
					if ((strtolower($line[INCIDENT_TYPES_SEVERITY]) == "sofort") || (strtolower($line[INCIDENT_TYPES_SEVERITY]) == "medium")) {
						$severity = get_text("Medium");
					}
				}
				$return_str .= "<tr><td style='padding: 2px; vertical-align: top;'> " . wordwrap(remove_nls($line[INCIDENT_TYPES_TYPE]), 20, "<br>", true) . "</td>";
				$return_str .= "<td style='padding: 2px; vertical-align: top;'>" . $severity . "</td>";
				$return_str .= "<td style='padding: 2px; vertical-align: top;'>" . wordwrap(remove_nls($line[INCIDENT_TYPES_DESCRIPTION]), 80, "<br>", true) . "</td>";
				$return_str .= "<td style='padding: 2px; vertical-align: top;'>" . wordwrap(remove_nls($line[INCIDENT_TYPES_PROTOCOL]), 80, "<br>", true) . "</td>";
				$return_str .= "<td style='padding: 2px; vertical-align: top;'>" . remove_nls($line[INCIDENT_TYPES_GROUP]) . "</td>";
				$return_str .= "<td style='padding: 2px; vertical-align: top;'>" . remove_nls($line[INCIDENT_TYPES_SORT]) . "</td></tr>";
			}
		}
		$return_str .= "</table>";
		return $return_str;
	} else {
		return "";
	}
}

function get_default_textblocks($file) {
	if (file_exists($file)) {
		define("TEXTBLOCKS_TYPE", 0);
		define("TEXTBLOCKS_TEXT", 1);
		define("TEXTBLOCKS_CODE", 2);
		define("TEXTBLOCKS_SORT", 3);
		define("TEXTBLOCKS_DELETE", 4);
		$data_array = array ();
		$data_array["synopsis"]["type"] = "textblocks_syn";
		$data_array["description"]["type"] = "textblocks_desc";
		$data_array["action"]["type"] = "textblocks_act";
		$data_array["assign"]["type"] = "textblocks_ass";
		$data_array["close"]["type"] = "textblocks_clo";
		$data_array["log"]["type"] = "textblocks_log";
		$data_array["message"]["type"] = "textblocks_msg";
		$line = "";
		$return_str = "<table style='table-layout: fixed; width: 100%;'>";
		$return_str .= "<tr><td style='padding: 2px; vertical-align: top;width: 15%;'>" . get_text("Type") .
			"</td><td style='padding: 2px; vertical-align: top;width: 65%;'>" . get_text("Textblocks") .
			"</td><td style='padding: 2px; vertical-align: top;width: 10%;'>" . get_text("Sort group") .
			"</td><td style='padding: 2px; vertical-align: top;width: 10%;'>" . get_text("Sort") . "</td></tr>";
		$uploadfile = fopen($file, "rb");
		while (!feof($uploadfile)) {
			$line = fgetcsv($uploadfile, 4096, ";");
			foreach ($data_array as $key => $value) {
				if (($line != false) && ($line[TEXTBLOCKS_TYPE] == $value["type"])) {
					$textblocks_type = "";
					switch ($line[TEXTBLOCKS_TYPE]) {
						case "textblocks_syn":
							$textblocks_type = get_text("Textblocks synopsis");
							break;
						case "textblocks_desc":
							$textblocks_type = get_text("Textblocks description");
							break;
						case "textblocks_act":
							$textblocks_type = get_text("Textblocks action");
							break;
						case "textblocks_ass":
							$textblocks_type = get_text("Textblocks assign");
							break;
						case "textblocks_clo":
							$textblocks_type = get_text("Textblocks incident close");
							break;
						case "textblocks_log":
							$textblocks_type = get_text("Textblocks log");
							break;
						case "textblocks_msg":
							$textblocks_type = get_text("Textblocks message");
							break;
						default:
					}
					$return_str .= "<tr><td style='padding: 2px; vertical-align: top;'>" . $textblocks_type . "</td>";
					$return_str .= "<td style='padding: 2px; vertical-align: top;'>" . wordwrap(remove_nls($line[TEXTBLOCKS_TEXT]), 80, "<br>", true) . "</td>";
					$return_str .= "<td style='padding: 2px; vertical-align: top;'>" . remove_nls($line[TEXTBLOCKS_CODE]) . "</td>";
					$return_str .= "<td style='padding: 2px; vertical-align: top;'>" . remove_nls($line[TEXTBLOCKS_SORT]) . "</td></tr>";
				}
			}
		}
		$return_str .= "</table>";
		return $return_str;
	} else {
		return "";
	}
}
?>