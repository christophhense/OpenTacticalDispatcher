<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function do_email($addresses, $subject, $text, $attachment) {
	require "./lib/PHPMailer-6.8.0/src/PHPMailer.php";
    require "./lib/PHPMailer-6.8.0/src/SMTP.php";
    require "./lib/PHPMailer-6.8.0/src/Exception.php";
	$result_data = array ();
	$configuration_complete = true;
	$valid_smtp_host = true;
	$mail = new PHPMailer(true);
	$mail->IsSMTP();
	$mail->CharSet = "utf-8";
	$mail->setLanguage(get_language(), './lib/PHPMailer-6.8.0/language/');
	$temp = trim(get_variable("_api_email_smtp_host"));
	if ($temp != "mail.example.com") {
		$mail->Timeout = 3;	//seconds
		if (preg_match("/[a-zA-Z]{3,6}:\/\//", $temp, $match)) {
			$protocol = substr($match[0], 0, -3);
			if (strcasecmp($protocol, "starttls") == 0) {
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->Port = 587;
			}
			if ((strcasecmp($protocol, "tls") == 0) || (strcasecmp($protocol, "smtps") == 0)) {
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				$mail->Port = 465;
			}
		}
		if (preg_match("/:[0-9]{1,5}/", $temp, $match)) {
			$mail->Port = substr($match[0], 1);
		}
		if (preg_match("/" . get_regexp_plain_url() . "/", $temp, $match)) {
			$mail->Host = $match[0];
		}
		$temp_variable = remove_nls(get_variable("_api_email_smtp_authentication"));
		$temp = explode(",", $temp_variable);
		$temp[1] = substr($temp_variable, strlen($temp[0]) + 1);
		if (trim($temp[0]) != "") {
			$mail->SMTPAuth = true;
			$mail->Username = trim($temp[0]);
			if (trim($temp[1]) != "") {
				$mail->Password = trim($temp[1]);
			} else {
				$configuration_complete = false;
			}
		}
		$temp = explode(",", remove_nls(get_variable("_api_email_from")));
		if (preg_match("/^" . get_regexp_email() . "$/", trim($temp[0]))) {
			$mail->From = $temp[0];
			if (trim($temp[1]) != "") {
				$mail->FromName = $temp[1];
			}
		} else {
			$configuration_complete = false;
		}
		$temp = explode(",", remove_nls(get_variable("_api_email_reply_to")));
		if (preg_match("/^" . get_regexp_email() . "$/", trim($temp[0]))) {
			$replytoname = "";
			if (trim($temp[1]) != "") {
				$replytoname = trim($temp[1]);
			}
			$mail->addReplyTo(trim($temp[0]), $replytoname);
		}
		$temp = explode(",", remove_nls(get_variable("_api_email_cc")));
		if (preg_match("/^" . get_regexp_email() . "$/", trim($temp[0]))) {
			$ccname = "";
			if (trim($temp[1]) != "") {
				$ccname = trim($temp[1]);
			}
			$mail->addCC(trim($temp[0]), $ccname);
		}
		$temp = explode(",", remove_nls(get_variable("_api_email_bcc")));
		if (preg_match("/^" . get_regexp_email() . "$/", trim($temp[0]))) {
			$bccname = "";
			if (trim($temp[1]) != "") {
				$bccname = trim($temp[1]);
			}
			$mail->addBCC(trim($temp[0]), $bccname);
		}
		foreach ($addresses as $key => $value) {
			$mail->addAddress(trim(substr($value["address"], 6)), $value["handle"]);
		}
		$mail->Subject = $subject;
		$mail->Body = $text;
		if ($attachment != "") {
			$mail->addAttachment($attachment);	// Ex.: 'images/phpmailer_mini.png'
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
		}
		/*	$mail->isHTML(true);// Send mail as HTML replace line breaks with <br> in html-email
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = 'Here is the subject';
			$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
		*/
		//	print_r(get_object_vars($mail)); exit ();
	} else {
		$configuration_complete = false;
		$valid_smtp_host = false;
	}
	if ($configuration_complete) {
		try {
			$mail->send();
			$result_data[0] = "OK";
			$result_data[1] = "";
		} catch (Exception $e) {
			$result_data[0] = "MAIL_SENT_ERROR";
			$result_data[1] = $mail->ErrorInfo;
		}
	} else {
		$result_data[0] = "INVALID_CONFIG_ERROR";
		if ($valid_smtp_host) {
			$result_data[1] = "";
		} else {
			$result_data[1] = get_text("invalid smtp-host");
		}
	}
	unset ($mail);
	return $result_data;
}

function do_print($url, $text) {
	require_once ("./lib/phpprinttip/php_classes/PrintIPP.php");
	$ipp = new PrintIPP();
	$url_array = parse_url($url);
	if (isset ($url_array['host'])) {
		$ipp->setHost($url_array['host']);
	} else {
		$ipp->setHost("127.0.0.1");
	}
	if (isset ($url_array['port'])) {
		$ipp->setPort($url_array['port']);
	} else {
		$ipp->setPort("631");
	}
	if (isset ($url_array['path'])) {
		$ipp->setPrinterURI($url_array['path']);
	} else {
		$ipp->setPrinterURI("/printers/default");
	}
	$ipp->setCharset("utf-8");
	$ipp->setMimeMediaType("application/postscript");
	$ipp->setDocumentName(get_text("Dispatch text"));
	$ipp->setSides(1);
	$ipp->setData($text);
	$ipp->setUserName(get_variable("title_string"));
	$ipp->debug_level = 0; // Debugging very verbose
	$ipp->setLog("/tmp/printipp","file",0);
//	$ipp->setAuthentication($username,$password); //Set system user name and password when server needs authentication for operation. (e.g. cancelJob() on CUPS with standard settings). If the server do not support Basic nor Digest authentication, you need to install SASL to use authentication. See INSTALL
	$result_data = array ("", "", "");
	$result_data[0] = $ipp->printJob();
	$result_data[1] = $ipp->getDebug();
	return $result_data;
}

function update_communication($api_log_id, $api_log_action) {
	global $types;
	$result_array = array (get_text("No message to edit"), "warning", "", 0, "", "");
	$url_str = "";
	if (($api_log_id != 0) && ($api_log_action != "")) {

		$query = "SELECT `cleared_datetime`, " .
			"`cleared_user_id`, " .
			"`text`, " .
			"`source`, " .
			"`unit_id` " .
			"FROM `api_log` " .
			"WHERE `id` = " . $api_log_id . ";";

		$result = db_query($query, __FILE__, __LINE__);
		if (db_num_rows($result)) {
			$row = stripslashes_deep(db_fetch_assoc($result));
			if (($row['cleared_datetime'] == null) && ($row['cleared_user_id'] == null)) {
				//======================================
				//Mark message as processed
				$who = (array_key_exists('user_id', $_SESSION))? $_SESSION['user_id'] : 0;

				$query = "UPDATE `api_log` " .
					"SET `cleared_datetime` = '" . mysql_datetime() . "', " .
					"`cleared_user_id`=" . $who . " " .
					"WHERE `id` = " . $api_log_id . ";";

				db_query($query, __FILE__, __LINE__);
				//======================================
				//Known entity or ITSI? Known unit assigned to ticket? 
				$unit_id = 0;
				$unit_handle = "";
				$smsg_id = "";
				$phone = "";
				$email = "";
				$ticket_id = 0;
				$source = "";
				$code = 0;
				$api_log_datetime = 0;

				$query_unit = "SELECT DISTINCT `u`.`id` AS `unit_id`, " .
					"`u`.`handle` AS `unit_handle`, " .
					"`u`.`remote_data_services` AS `unit_remote_data_services`, " .
					"`u`.`unit_phone` AS `unit_phone`, " .
					"`u`.`unit_email` AS `unit_email`, " .
					"`al`.`code` AS `api_log_code`, " .
					"`al`.`source` AS `api_log_source`, " .
					"`al`.`unit_id` AS `api_log_unit_id`, " .
					"`al`.`datetime` AS `api_log_datetime` " .
					"FROM `units` `u` " .
					"LEFT JOIN `allocates` `a` ON `a`.`resource_id` = `u`.`id`" .
					"LEFT JOIN `api_log` `al` ON `al`.`unit_id` = `u`.`id`" .
					"WHERE `u`.`id` = (SELECT `unit_id` FROM `api_log` WHERE `id` = " . $api_log_id . ") " .
					"AND `u`.`id` IN (SELECT `resource_id` FROM `allocates` WHERE `type` = " . $GLOBALS['TYPE_UNIT'] . ") " .
					"ORDER BY `al`.`datetime` DESC LIMIT 1;";

				$result_unit = db_query($query_unit, __FILE__, __LINE__);
				if (db_num_rows($result_unit)) {	
					$row_unit = stripslashes_deep(db_fetch_assoc($result_unit));
					$unit_id = $row_unit['unit_id'];
					$unit_handle = $row_unit['unit_handle'];
					$smsg_id = $row_unit['unit_remote_data_services'];
					$phone = $row_unit['unit_phone'];
					$email = $row_unit['unit_email'];
					$source = $row_unit['api_log_source'];
					$code = $row_unit['api_log_code'];
					$api_log_datetime = $row_unit['api_log_datetime'];

					$query_ticket = "SELECT `assigns`.`id` AS `assign_id`, " .
						"`ticket_id` FROM `assigns` " .
						"LEFT JOIN `tickets` ON `assigns`.`ticket_id` = `tickets`.`id` " .
						"WHERE `unit_id` = (SELECT `unit_id` FROM `api_log` WHERE `id` = '" . $api_log_id . "' LIMIT 1) " .
						"AND `tickets`.`status` = 2 ORDER BY `assign_id` ASC LIMIT 1;";

					$result_ticket = db_query($query_ticket, __FILE__, __LINE__);
					$row_ticket = stripslashes_deep(db_fetch_assoc($result_ticket));
					if ($row_ticket) {
						$ticket_id = $row_ticket['ticket_id'];
					}
					unset ($result_ticket);
				} else {

					$query_source = "SELECT `source`, " .
						"`unit_id`, " .
						"`code`, " .
						"`datetime` " .
						"FROM `api_log` " .
						"WHERE `id` = " . $api_log_id . " " .
						"LIMIT 1;";

					$result_source = db_query($query_source, __FILE__, __LINE__);
					if (db_num_rows($result_source)) {
						$row_source = stripslashes_deep(db_fetch_assoc($result_source));
						$source = $row_source['source'];
						$code = $row_source['code'];
						$api_log_datetime = $row_source['datetime'];
					}
					unset ($result_source);
				}
				unset ($result_unit);
				//======================================
				$text = "";
				switch ($api_log_action) {
				case "api_log_voice_promt":
				case "api_log_private_call":
					$url_str =  "ticket_edit.php?ticket_id=" . $ticket_id . "&unit_id=" . $unit_id;
					if ($ticket_id == 0) {
						switch ($code) {
						case $GLOBALS['LOG_EMGCY_LO']:
						case $GLOBALS['LOG_EMGCY_HI']:
						case $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']:
						case $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']:
						case $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET']:
						case $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']:
						default:
							$url_str = "log_report.php";
							if ($unit_id != 0) {
								$url_str = "log_report.php?unit_id=" . $unit_id;
							}
						}
					}
					switch ($api_log_action) {
					case "api_log_voice_promt":
						$message_array = get_receipt_message($code);
						if ($message_array["code"] != "") {
							$do_api_result = do_api_message(get_assign_id($unit_id), $source, $message_array["code"], $message_array["text"], "", "");
							$result_array[0] = get_text("Message not sent");
							$result_array[1] = "danger";
							$message_type = $GLOBALS['LOG_SMS_MESSAGE_ERROR'];
							if ($do_api_result[0] == "OK") {
								$result_array[0] = get_text("Message sent");
								$result_array[1] = "success";
								$message_type = $GLOBALS['LOG_SMS_MESSAGE_SEND'];
							}
							do_log($message_type, $ticket_id, $unit_id, get_text("Receiver") . ": " . $source . " " . $message_array["text"], 0, "", "", "");
						}
						break;
					case "api_log_private_call":
						$do_api_result = do_api_message($_SESSION['user_id'], $source, get_variable("_api_private_call_encdg"), $_SESSION['user_name'], "", "");
						$result_array[0] = get_text("Message not sent");
						$result_array[1] = "danger";
						$message_type = $GLOBALS['LOG_SMS_MESSAGE_ERROR'];
						if ($do_api_result[0] == "OK") {
							$result_array[0] = get_text("Private call requested");
							$result_array[1] = "success";
							$message_type = $GLOBALS['LOG_SMS_MESSAGE_SEND'];
						}
						do_log($message_type, $ticket_id, $unit_id, get_text("Private Call") . " " . get_text("Receiver") . ": " . $source, 0, "", "", "");
						break;
					default:
					}
					$result_array[2] = $url_str;
					break;
				case "api_log_reply":
					if ($row['text'] != "") {
						$text = "  " . get_text("Text") . ": " . $row['text'];
					}
					if (($row['unit_id'] == 0) || ($row['unit_id'] == "")) {
						$text = "  " . $row['source'] . $text;
					}
					do_log($GLOBALS['LOG_COMMENT'], $ticket_id, $unit_id, $types[$code] . "  " . date(get_variable("date_format"), strtotime($api_log_datetime)) . $text, 0, "", "", "");
					$result_array[0] = "";
					$result_array[1] = "";
					$result_array[2] = "communication.php?function=send_message&message_group=unit&target_api_log_id=" . urlencode($api_log_id) . "&ticket_id=" . $ticket_id;
					break;
				case "api_log_add_to_log":
					$ticket_id = 0;
				case "api_log_add_to_ticket_log":
					$text = "";

					switch ($code) {
					case $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']:
					case $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']:
					case $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET']:
					case $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']:
					case $GLOBALS['LOG_UNIT_STATUS']:

						$query_source = "SELECT `source`, " .
							"`unit_id`, " .
							"`code`, " .
							"`datetime` " .
							"FROM `api_log` " .
							"WHERE `id` = " . $api_log_id . " " .
							"LIMIT 1;";

						$result_source = db_query($query_source, __FILE__, __LINE__);
						if (db_num_rows($result_source)) {
							$row_source = stripslashes_deep(db_fetch_assoc($result_source));
							$unit_id = $row_source['unit_id'];
							$code = $row_source['code'];
							$api_log_datetime = $row_source['datetime'];
						}
						unset ($result_source);
						break;
					default:
						if ($row['text'] != "") {
							$text = "  " . get_text("Text") . ": " . $row['text'];
						}
						if (($row['unit_id'] == 0) || ($row['unit_id'] == "")) {
							$text = "  " . $row['source'] . $text;
						}
					}
					do_log($GLOBALS['LOG_COMMENT'], $ticket_id, $unit_id, $types[$code] . "  " . date(get_variable("date_format"), strtotime($api_log_datetime)) . $text, 0, "", "", "");
					$result_array[0] = get_text("Saved");
					$result_array[1] = "success";
					break;
				case "api_log_no_action":
					do_log($GLOBALS['LOG_NO_ACTION'], $ticket_id, $unit_id, $types[$code] . "  " . date(get_variable("date_format"), strtotime($api_log_datetime)) . $text, 0, "", "", "");
					$result_array[0] = get_text("Saved");
					$result_array[1] = "success";
					break;
				case "api_log_new_ticket":
					$result_array[0] = get_text("Saved");
					$result_array[1] = "success";
					break;
				case "api_log_update_call_progression":

					$query = "SELECT `id` " .
						"FROM `assigns` " .
						"WHERE `unit_id` = " . $unit_id . " " .
						"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00') " .
						"ORDER BY `id` ASC " .
						"LIMIT 1;";

					$result = db_query($query, __FILE__, __LINE__);

					if (db_num_rows($result)) {
						$row_assigns = db_fetch_assoc($result);

						$query = "SELECT `datetime`, " .
							"`code` " .
							"FROM `api_log` " .
							"WHERE `id` = " . $api_log_id . ";";

						$result = db_query($query, __FILE__, __LINE__);

						if (db_num_rows($result)) {
							$row_api_log = db_fetch_assoc($result);
							switch ($row_api_log['code']) {
							case $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']:
								$result_array[0] = get_text("Saved");
								$result_array[1] = "success";
								$result_array[3] = $row_assigns['id'];
								$result_array[4] = $row_api_log['datetime'];
								$result_array[5] = "frm_responding";
								break;
							case $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']:
								$result_array[0] = get_text("Saved");
								$result_array[1] = "success";
								$result_array[3] = $row_assigns['id'];
								$result_array[4] = $row_api_log['datetime'];
								$result_array[5] = "frm_on_scene";
								break;
							case $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET']:
								$result_array[0] = get_text("Saved");
								$result_array[1] = "success";
								$result_array[3] = $row_assigns['id'];
								$result_array[4] = $row_api_log['datetime'];
								$result_array[5] = "frm_u2fenr";
								break;
							case $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']:
								$result_array[0] = get_text("Saved");
								$result_array[1] = "success";
								$result_array[3] = $row_assigns['id'];
								$result_array[4] = $row_api_log['datetime'];
								$result_array[5] = "frm_u2farr";
								break;
							default:
							}
						}
					}
					break;
				default:
				}
			} else {
				$result_array = array (get_text("Message already edited"), "warning", "");
			}
		}
	}
	return $result_array;
}

function show_communication_table_left() {

	$oldest_call_id = array ();
	function previous_call_present($unit_id, $api_log_id) {
		global $oldest_call_id;
		if (empty ($oldest_call_id[$unit_id])) {

			$query = "SELECT `id` " .
				"FROM `api_log` " .
				"WHERE (`code` = " . $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET'] . " " .
				"OR `code` = " . $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET'] . " " .
				"OR `code` = " . $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET'] . " " .
				"OR `code` = " . $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET'] .
				") AND (SELECT DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " . get_variable("_api_log_max_age_setng") . " MINUTE) <= `datetime`) " .
				"AND (`api_log`.`cleared_datetime` IS NULL) " .
				"AND (`unit_id` = " . $unit_id . ") " .
				"ORDER BY `datetime` ASC " .
				"LIMIT 1;";

			$result = db_query($query, __FILE__, __LINE__);
			$oldest_call_id[$unit_id] = 0;
			if (db_num_rows($result)) {
				$row = db_fetch_assoc($result);
				$oldest_call_id[$unit_id] = $row['id'];
			}
//			@error_log("function previous_call_present(" . $unit_id . ", " . $api_log_id . ") Data from database: " . (($oldest_call_id[$unit_id] == $api_log_id)? "false":"true"));
		} else {
//			@error_log("function previous_call_present(" . $unit_id . ", " . $api_log_id . ") Data from variable: " . (($oldest_call_id[$unit_id] == $api_log_id)? "false":"true"));
		}
		if ($oldest_call_id[$unit_id] == $api_log_id) {
			return false;
		} else {
			return true;
		}
	}

	$oldest_assign_id = array ();
	function get_oldest_assign($unit_id) {
		global $oldest_assign_id;
		if (empty ($oldest_assign_id[$unit_id])) {

			$query = "SELECT `id` " .
				"FROM `assigns` " .
				"WHERE `unit_id` = " . $unit_id . " " .
				"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00') " .
				"ORDER BY `id` ASC " .
				"LIMIT 1;";

			$result = db_query($query, __FILE__, __LINE__);
			$oldest_assign_id[$unit_id] = 0;
			if (db_num_rows($result)) {
				$row = db_fetch_assoc($result);
				$oldest_assign_id[$unit_id] = $row['id'];
			}
//			@error_log("function get_oldest_assign(" . $unit_id . ") Data from database: " . $oldest_assign_id[$unit_id]);
		} else {
//			@error_log("function get_oldest_assign(" . $unit_id . ") Data from variable: " . $oldest_assign_id[$unit_id]);
		}
		return $oldest_assign_id[$unit_id];
	}
	?>
	<?php print show_day_night_style();?>
	<style>
		.table, td {
			overflow: visible !important;
		}
	</style>
	<table class="table table-striped table-condensed" style="table-layout: fixed;">
		<tr>
			<th style="width: 15%; border-top: 0px;"><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("DateTime");?></div></th>
			<th style="width: 25%; border-top: 0px;"><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("Code");?></div></th>
			<th style="width: 40%; border-top: 0px;"><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("Unit");?></div></th>
			<th style="width: 20%; border-top: 0px;"><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("Handle request");?></div></th> 
		</tr>
	<?php
	global $types;

	$is_super_part = "";
	if (is_admin() || is_super()) {
		$is_super_part = "OR `code` = " . $GLOBALS['LOG_API_CONNECTED'] . " " .
			"OR `code` = " . $GLOBALS['LOG_API_DISCONNECTED'] . " " .
			"OR `code` = " . $GLOBALS['LOG_API_DEVICE_TEXT'] . " ";
	}

	$query = "SELECT DISTINCT `api_log`.`datetime` AS `datetime`, " .
		"`api_log`.`code` AS `code`, " .
		"`api_log`.`source` AS `api_log_source`, " .
		"`api_log`.`destination` AS `api_log_destination`, " .
		"`api_log`.`id` AS `api_log_id`, " .
		"`api_log`.`cleared_datetime` AS `api_log_cleared_datetime`, " .
		"`api_log`.`cleared_user_id` AS `api_log_cleared_user`, " . 
		"`api_log`.`text` AS `api_log_text`, " .
		"`units`.`id` AS `unit_id`, " .
		"`units`.`handle` AS `unit_handle`, " .
		"`units`.`name` AS `unit_name`, " .
		"`u`.`name` AS `user_name` FROM `api_log` " .
		"LEFT JOIN `users` `u` 	ON (`api_log`.`cleared_user_id` = `u`.`id`) " .
		"LEFT JOIN `units` ON (`api_log`.`unit_id` = `units`.`id`) " .
		"WHERE ((`code` = " . $GLOBALS['LOG_EMGCY_HI'] . " " .
			"OR `code` = " . $GLOBALS['LOG_EMGCY_LO'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_REQ'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET'] . " " .
			"OR `code` = " . $GLOBALS['LOG_CALL_MANACKN'] . " " .
			"OR `code` = " . $GLOBALS['LOG_INFO'] . " " .
			"OR `code` = " . $GLOBALS['LOG_ERROR'] . " " .
			"OR `code` = " . $GLOBALS['LOG_MESSAGE_RECEIVE'] . " " .
			$is_super_part .
		") AND ((DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " . get_variable("_api_log_max_display_setng") . " MINUTE) <= `api_log`.`datetime`) " .
			"OR (`api_log`.`cleared_datetime` IS NULL))) " .
		"ORDER BY `api_log`.`cleared_datetime` IS NULL DESC, " .
		"`api_log`.`datetime` DESC, `api_log`.`id` DESC;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result)) {
		$log_time_array = array("", "");
		$i = 0;
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$unit_id = 0;
			$auto_ticket = false;
			$valid_reply_address = false;
			$unit_dispached = false;
			$unit_dispached_array = array ();
			$done_text = "";
			$is_auto_ticket = get_is_auto_ticket_line($row['api_log_text']);
			if ($is_auto_ticket["MATCH"]) {
				$auto_ticket = true;
			}
			if ($row['unit_id'] > 0) {
				$valid_reply_address = true;
				$unit_id = $row['unit_id'];
			} else {
				if (is_smsg_id($row['api_log_source'])) {
					$valid_reply_address = true;
				}
			}
			$unit_dispached_array = get_assigns($unit_id, 0);
			if ($unit_dispached_array[0]) {
				$unit_dispached = true;
			}
			$log_time_array = get_date_and_time_part($row['datetime'], $log_time_array[0]);
			if (($row['api_log_cleared_datetime'] == null) || ($row['api_log_cleared_user'] == null)) {
				$button_type = $row['code'];
			} else {
				$button_type = 0;
			}
			$action_button = "<div class='btn-group'><button id='button_" . $i . "' type='button' class='btn btn-info dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> ";
			$action_button .= get_text("Select") . " <span class='caret'></span></button><ul class='dropdown-menu'>";
			$action_button_disabled = "<div class='btn-group' " . get_help_text_str("com_action_button_disabled") . "><button id='button_" . $i . "' type='button' class='btn btn-info dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' disabled> ";
			$action_button_disabled .= get_text("Select") . " <span class='caret'></span></button><ul class='dropdown-menu'>";//style='background-color: #ccc; border-color: #ccc; background-image: none;'
			$severity_blink_str = " class='severity_normal'";
			$action_ticket_select = "<li onclick='$(\"#button_" . $i . "\").prop(\"disabled\", true); $(\"#button_" . $i . "\").html(\"" . get_text("Wait") . "\"); send_data(" .  $row['api_log_id'] . ", \"api_log_select_ticket\", 0, " . $unit_id . ");'><a href='#'>" . get_text("Dispatch_Units_short") . "</a></li>";
			$action_update_call_progression = "<li onclick='$(\"#button_" . $i . "\").prop(\"disabled\", true); $(\"#button_" . $i . "\").html(\"" . get_text("Wait") . "\"); send_data(" .  $row['api_log_id'] . ", \"api_log_update_call_progression\", 0, " . $unit_id . ");'><a href='#'>" . get_text("Update call progression") . "</a></li>";
			$action_voice_promt = "<li onclick='$(\"#button_" . $i . "\").prop(\"disabled\", true); $(\"#button_" . $i . "\").html(\"" . get_text("Wait") . "\"); send_data(" .  $row['api_log_id'] . ", \"api_log_voice_promt\", 0, " . $unit_id . ");'><a href='#'>" . get_text("Voice promt") . "</a></li>";
			$action_private_call = "<li onclick='$(\"#button_" . $i . "\").prop(\"disabled\", true); $(\"#button_" . $i . "\").html(\"" . get_text("Wait") . "\"); send_data(" .  $row['api_log_id'] . ", \"api_log_private_call\", 0, " . $unit_id . ");'><a href='#'>" . get_text("Private Call") . "</a></li>";;
			$action_new_ticket = "<li onclick='$(\"#button_" . $i . "\").prop(\"disabled\", true); $(\"#button_" . $i . "\").html(\"" . get_text("Wait") . "\"); send_data(" .  $row['api_log_id'] . ", \"api_log_new_ticket\", 0, " . $unit_id . ");'><a href='#'>" . get_text("New") . "</a></li>";
			$action_reply = "<li onclick='$(\"#button_" . $i . "\").prop(\"disabled\", true); $(\"#button_" . $i . "\").html(\"" . get_text("Wait") . "\"); send_data(" .  $row['api_log_id'] . ", \"api_log_reply\", 0, " . $unit_id . ");'><a href='#'>" . get_text("Reply") . "</a></li>";
			$action_log = "<li onclick='$(\"#button_" . $i . "\").prop(\"disabled\", true); $(\"#button_" . $i . "\").html(\"" . get_text("Wait") . "\"); send_data(" .  $row['api_log_id'] . ", \"api_log_add_to_log\", 0, " . $unit_id . ");'><a href='#'>" . get_text("Add to log") . "</a></li>";
			$action_ticket_log = "<li onclick='$(\"#button_" . $i . "\").prop(\"disabled\", true); $(\"#button_" . $i . "\").html(\"" . get_text("Wait") . "\"); send_data(" .  $row['api_log_id'] . ", \"api_log_add_to_ticket_log\", 0, " . $unit_id . ");'><a href='#'>" . get_text("Add to ticket-log") . "</a></li>";
			$action_no_action = "<li onclick='$(\"#button_" . $i . "\").prop(\"disabled\", true); $(\"#button_" . $i . "\").html(\"" . get_text("Wait") . "\"); send_data(" .  $row['api_log_id'] . ", \"api_log_no_action\", 0, " . $unit_id . ");'><a href='#'>" . get_text("No action") . "</a></li>";
			$done_title_str = "";
			switch ($button_type) {
			case $GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']:
			case $GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']:
			case $GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET']:
			case $GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']:
				$unit_situation = "not_in_dispatch";
				if (previous_call_present($unit_id, $row['api_log_id'])) {
					$unit_situation = "pre-calls_available";
				} else {
					if (get_oldest_assign($unit_id) != 0) {
						$unit_situation = "in_dispatch";
					}
				}
				switch ($unit_situation) {
				case "pre-calls_available":
					$action_button = $action_button_disabled;
					break;
				case "in_dispatch":
					$action_button .= $action_update_call_progression;
					$action_button .= $action_no_action;
					break;
				default:
					$action_button .= $action_ticket_select;
					$action_button .= $action_voice_promt;
					$action_button .= $action_log;
					$action_button .= $action_no_action;
				}
				break;
			case $GLOBALS['LOG_CALL_REQ']:
				$action_button .= $action_voice_promt;
				if (get_variable("_api_callreq_repl") > 3) {
					$action_button .= $action_private_call;
				}
				$action_button .= $action_no_action;
				break;
			case $GLOBALS['LOG_EMGCY_LO']:
				$action_button = "<div class='btn-group'><button type='button' class='btn btn-warning dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> ";
				$action_button .= get_text("Select") . " <span class='caret'></span></button><ul class='dropdown-menu'>";
				$action_button .= $action_voice_promt;
				if (get_variable("_api_emgcy_lo_repl") > 3) {
					$action_button .= $action_private_call;
				}
				$action_button .= $action_no_action;
				$severity_blink_str = " class='severity_priority textblink'";
				break;
			case $GLOBALS['LOG_EMGCY_HI']:
				$action_button = "<div class='btn-group'><button type='button' class='btn btn-danger dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> ";
				$action_button .= get_text("Select") . " <span class='caret'></span></button><ul class='dropdown-menu'>";
				$action_button .= $action_voice_promt;
				if (get_variable("_api_emgcy_hi_repl") > 3) {
					$action_button .= $action_private_call;
				}
				$action_button .= $action_no_action;
				$severity_blink_str = " class='severity_high textblink'";
				break;
			case $GLOBALS['LOG_MESSAGE_RECEIVE']:
				if ($auto_ticket) {
					$action_button = "<div class='btn-group'><button type='button' class='btn btn-danger dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> ";
					$action_button .= get_text("Select") . " <span class='caret'></span></button><ul class='dropdown-menu'>";
					$severity_blink_str = " class='severity_high textblink'";
				}
				if ($valid_reply_address) {
					$action_button .= $action_reply;
				}
				if ($auto_ticket) {
					$action_button .= $action_new_ticket;
				}
				if ($unit_dispached) {
					$action_button .= $action_ticket_log;
				}
				$action_button .= $action_log;
				$action_button .= $action_no_action;
				break;
			case $GLOBALS['LOG_INFO']:
				$action_button .= $action_log;
				$action_button .= $action_no_action;
				
				break;
			case $GLOBALS['LOG_ERROR']:
				$action_button = "<div class='btn-group'><button type='button' class='btn btn-warning dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> ";
				$action_button .= get_text("Select") . " <span class='caret'></span></button><ul class='dropdown-menu'>";
				$action_button .= $action_log;
				$action_button .= $action_no_action;
				$severity_blink_str = " class='severity_priority'";
				break;
			case $GLOBALS['LOG_CALL_MANACKN']:
				$action_button .= $action_voice_promt;
				$action_button .= $action_no_action;
				break;
			default:
				$done_text = get_text("Edited") . " " . date(get_variable("date_format_time_only"), strtotime($row['api_log_cleared_datetime'])) . "<br>" . get_text("by") . " " .  get_user_name($row['api_log_cleared_user']);
				$done_title_str = get_title_str(date(get_variable("date_format"), strtotime($row['api_log_cleared_datetime'])));
				$severity_blink_str = "";
				$hide_text_row = true;
			}
			$action_button .= "</ul></div>";
			if ($done_text != "") {
				$action_button = $done_text;
			}
			if ((isset ($row['unit_handle'])) && ($row['unit_handle'] != "")) {
				$unit_name = $row['unit_name'];
				$unit_handle = remove_nls($row['unit_handle']);
			} else {
				$receiver_array = split_api_receiver_str($row['api_log_source']);
				$unit_name = $unit_handle = $receiver_array[1];
			}
			if ((($row['code'] == $GLOBALS['LOG_API_CONNECTED']) || ($row['code'] == $GLOBALS['LOG_API_DISCONNECTED']) || ($row['code'] == $GLOBALS['LOG_API_DEVICE_TEXT']) && is_super())) {
				$unit_name = $unit_handle  = get_text("Application Interface");
				if ($row['api_log_destination'] != "api") {
					$unit_name = $unit_handle  = get_text("Cellular phone");
				}
			}
	?>
		<tr style="height: 44px;">
			<td <?php print get_title_str(date(get_variable("date_format"), strtotime($row['datetime'])));?>><div<?php print $severity_blink_str;?> style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print $log_time_array[1];?></div></td>
			<td <?php print get_title_str($types[$row['code']]);?>><div<?php print $severity_blink_str;?> style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print $types[$row['code']];?></div></td>
			<td <?php print get_title_str($unit_name);?>><div<?php print $severity_blink_str;?> style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><nobr><?php print $unit_handle;?></nobr></div></td>
			<td <?php print $done_title_str;?>><div style='overflow: visible; border-top: 0px;'><?php print $action_button;?></div></td>
		</tr>
	<?php
			if ($row['api_log_text'] &&
				((($row['code'] == $GLOBALS['LOG_MESSAGE_RECEIVE']) || ($row['code'] == $GLOBALS['LOG_INFO']) || ($row['code'] == $GLOBALS['LOG_ERROR'])) ||
				(($row['code'] == $GLOBALS['LOG_API_DEVICE_TEXT']) && (is_admin() || is_super())))
			) {
	?>
		<tr style="height: 44px;">
			<td <?php print get_title_str($row['api_log_text']);?>>
				<div<?php print $severity_blink_str;?> style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;">
					<?php print get_text("Message text");?>:
				</div>
			</td>
			<td style="height: 44px;" colspan=3 <?php print get_title_str($row['api_log_text']);?>>
				<div<?php print $severity_blink_str;?> style="white-space: normal; overflow: hidden; text-overflow: ellipsis; border-top: 0px;">
					<?php print remove_nls($row['api_log_text']);?>
				</div>
			</td>
		</tr>
	<?php
			}
			$i++;
		}
	} else {
	?>
		<tr style="height: 44px;">
			<th colspan=4 style="text-align: center;"><?php print get_text("No data for this period!");?></th>
		</tr>
	<?php
	}
	?>
	</table>
	<?php
}

function show_communication_table_right() {
	?>
	<?php print show_day_night_style();?>
	<style>
		.table, td {
			overflow: visible !important;
		}
	</style>
	<table class="table table-striped table-condensed" style="table-layout: fixed;">
		<tr>
			<th style="width: 15%; border-top: 0px;"><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("DateTime");?></div></th>
			<th style="width: 20%; border-top: 0px;"><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("Call type");?></div></th>
			<th style="width: 45%; border-top: 0px;"><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("Caller");?></div></th>
			<th style="width: 20%; border-top: 0px;"><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("Call destination");?></div></th>
		</tr>
	<?php
	global $types;

	$query = "SELECT `api_log`.`id` AS `api_log_id`, " .
		"`api_log`.`datetime` AS `api_log_datetime`, " .
		"`api_log`.`cleared_datetime` AS `api_log_cleared_datetime`, " .
		"`api_log`.`cleared_user_id` AS `api_log_cleared_user`, " .
		"`api_log`.`unit_id` AS `api_log_unit_id`, " .
		"`api_log`.`source` AS `api_log_source`, " .
		"`api_log`.`destination` AS `api_log_destination`, " .
		"`api_log`.`destination_alias` AS `api_log_destination_alias`, " .
		"`api_log`.`audio_link` AS `api_log_audio_link`, " .
		"`api_log`.`code` AS `api_log_code`, " .
		"`units`.`handle` AS `unit_handle`, " .
		"`units`.`name` AS `unit_name`, " .
		"`u`.`name` AS `user_name` FROM `api_log` " .
		"LEFT JOIN `users` `u` 	ON (`api_log`.`cleared_user_id` = `u`.`id`) " .
		"LEFT JOIN `units` ON (`api_log`.`unit_id` = `units`.`id`) " .
		"WHERE ((`code` = " . $GLOBALS['LOG_PTT'] . " " .
//			"OR `code` = " . $GLOBALS['LOG_PHONE_CALL'] . " " .
//			"OR `code` = " . $GLOBALS['LOG_GROUP_CALL'] . " " .
//			"OR `code` = " . $GLOBALS['LOG_PRIVATE_CALL'] .
		") AND ((DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " . get_variable("_api_log_max_display_setng") . " MINUTE) <= `api_log`.`datetime`))) " .
		"ORDER BY `api_log`.`id` DESC;";						//TODO Über Variable steuern => immer alle unbearbeiteten!!!
	//TODO Control via variable => 5 after 3 before 5 after 1?
	//TODO Control interval via variable => always all unprocessed!!!

	$result = db_query($query, __FILE__, __LINE__);
	if (db_num_rows($result)) {
		$log_time_array = array("", "");
		while ($row = stripslashes_deep(db_fetch_assoc($result))) {
			$log_time_array = get_date_and_time_part($row['api_log_datetime'], $log_time_array[0]);
			if ($row['api_log_unit_id'] > 0) {
				$unit_name = $row['unit_name'];
				$unit_handle = remove_nls($row['unit_handle']);
			} else {
				$receiver_array = split_api_receiver_str($row['api_log_source']);
				$unit_name = $unit_handle = $receiver_array[1];
			}
			$destination = remove_nls($row['api_log_destination']);
			if ($row['api_log_destination_alias'] != null) {
				$destination = remove_nls($row['api_log_destination_alias']);
			}
	?>
		<tr style="height: 44px;">
			<td <?php print get_title_str(date(get_variable("date_format"), strtotime($row['api_log_datetime'])));?>><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print $log_time_array[1];?></div></td>
			<td <?php print get_title_str($types[$row['api_log_code']]);?>><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print $types[$row['api_log_code']];?></div></td>
			<td <?php print get_title_str($unit_name);?>><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><nobr><?php print $unit_handle;?></nobr></div></td>
		  	<td <?php print get_title_str($row['api_log_destination']);?>><div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print $destination;?></div></td>
		</tr>
	<?php
			if ($row['api_log_audio_link'] != null) {
	?>
		<tr style="height: 44px;">
			<td></td>
			<td colspan=4><audio src="<?php print $row['api_log_audio_link'];?>" controls ></audio></td>
		</tr>
	<?php
			}
		}
	} else {
	?>
		<tr style="height: 44px;">
			<th colspan=4 style="text-align: center;"><?php print get_text("No data for this period!");?></th>
		</tr>
	<?php
	}
	?>
	</table>
	<?php
}

//===================================send message

function send_message($addresses, $text_type, $subject, $text, $shorttext, $ticket_id) {
	require_once ("./incs/api.inc.php");
	$sent_ok = 0;
	$sent_error = 0;
	$code = "MESSAGE";
	switch ($text_type) {
	case "DISPATCH_MESSAGE":
		$code = "DISPATCH_MESSAGE";
		break;
	case "INDIVIDUAL_MESSAGE":
		$code = "MESSAGE";
		break;
	default:
		$fixtexts = array ();
		$fixtexts = get_fixtext();
		$subject = $text = $shorttext = $fixtexts[$text_type]["Text"];
		$code = $fixtexts[$text_type]["code"];
	}
//	print_r($addresses); print "<br>Ticket-ID: " . $ticket_id . "<br>Code: " . $code . "<br>Subject: " . $subject . "<br>Text: " . $text . "<br>Shorttext:  " . $shorttext; exit ();
	$result = array ();
	//========================= API
	$destination_prefix = "";
	$report_channels = array (
		get_variable("_api_prefix_reporting_channel_1_encdg"),
		get_variable("_api_prefix_reporting_channel_2_encdg"),
		get_variable("_api_prefix_reporting_channel_3_encdg"),
		get_variable("_api_prefix_reporting_channel_4_encdg"),
		get_variable("_api_prefix_reporting_channel_5_encdg"),
		get_variable("_api_prefix_phone_encdg")
	);
	foreach ($report_channels as $destination_prefix) {
		if (array_key_exists($destination_prefix, $addresses)) {
			$batch_start_stop_settings = explode(",", get_variable("_api_batch_start_stop_setng"));
			$batch_start_setting = trim($batch_start_stop_settings[0]);
			$batch_stop_setting = trim($batch_start_stop_settings[1]);
			if ((count($addresses[$destination_prefix]) > 1) && ($batch_start_setting != "") && ($batch_stop_setting != "")) {
				do_api_message("", $destination_prefix, $batch_start_setting, "", "", "");
			}
			foreach ($addresses[$destination_prefix] as $key) {
				$result = do_api_message(get_assign_id($key["address"]), $key["address"], $code, $shorttext, "", "");
				if ($result[0] == "OK") {
					$message_type = $GLOBALS['LOG_SMS_MESSAGE_SEND'];
					$sent_ok++;
				} else {
					$message_type = $GLOBALS['LOG_SMS_MESSAGE_ERROR'];
					$sent_error++;
				}
				do_log($message_type, $ticket_id, $key["id"], get_text("Receiver") . ": " . $key["address"] . " " . $shorttext, 0, "", "", "");
			}
			if ((count($addresses[$destination_prefix]) > 1) && ($batch_start_setting != "") && ($batch_stop_setting != "")) {
				do_api_message("", $destination_prefix, $batch_stop_setting, "", "", "");
			}
		}
	}
	//========================= Print
	/*Paper size
	DIN A4	595 x 842
	Letter	612 x 792
	0 position in the bottom left corner*/
	$ps_pt_align_left = 40;
	$ps_pt_align_right = 545;
	$ps_font_size_big = 12;
	$ps_font_size_verybig = 15;
	if (array_key_exists(get_variable("_api_prefix_printer_encdg"), $addresses)) {
		$text_lines_array = explode("\n", wordwrap($text, 80, "\n", true));
		$text_postscript_first_part = "%!\n" .
			"/Helvetica findfont\n" .
			"dup length dict begin\n" .
			"{def} forall\n" .
			"/Encoding ISOLatin1Encoding def\n" .
			"currentdict\n" .
			"end\n" .
			"/Helvetica-ISOLatin1 exch definefont\n" .
			$ps_font_size_big . " scalefont\n" .
			"setfont\n" .
			$ps_pt_align_left . " 799 moveto \n" .
			"(" . remove_nls(get_variable("title_string")) . ") show\n" .
			$ps_pt_align_left . " 782 moveto \n" .
			"(" . get_text("Incident dispatch system") . ") show\n";
		$text_postscript_last_part = "/Helvetica-Bold findfont\n" .
			"dup length dict begin\n" .
			"{def} forall\n" .
			"/Encoding ISOLatin1Encoding def\n" .
			"currentdict\n" .
			"end\n" .
			"/Helvetica-ISOLatin1 exch definefont\n" .
			$ps_font_size_verybig . " scalefont\n" .
			"setfont\n" .
			$ps_pt_align_left . " 755 moveto \n" .
			"(" . wordwrap($subject, 40, "\n", true) . ") show\n" .
			"/Helvetica findfont\n" .
			"dup length dict begin\n" .
			"{def} forall\n" .
			"/Encoding ISOLatin1Encoding def\n" .
			"currentdict\n" .
			"end\n" .
			"/Helvetica-ISOLatin1 exch definefont\n" .
			$ps_font_size_big . " scalefont\n" .
			"setfont\n";
		$i = 731;
		foreach ($text_lines_array as $key) {
			$text_postscript_last_part .= $ps_pt_align_left . " " . $i . " moveto\n" .
				"(" . $key . ") show\n";
			$i = $i - 18;
		}
		$text_postscript_last_part .= $ps_pt_align_left . " " . $i . " moveto\n(" . get_text("Printed at") . " " . 
			date(get_variable("date_format")) . " " . get_text("by") . " " . $_SESSION['user_name'] . ") show\n";
		$text_postscript_last_part .= "showpage\n";
		foreach ($addresses[get_variable("_api_prefix_printer_encdg")] as $key) {
			$subscriber_url = substr($key["address"], 8);
			$subscriber_message = mb_convert_encoding($text_postscript_first_part . "(" . $key["handle"] . ") dup stringwidth pop\n" . 
				$ps_pt_align_right . " exch sub\n799 moveto show\n" . $text_postscript_last_part, 'ISO-8859-1', mb_list_encodings());
			$result = do_print($subscriber_url, $subscriber_message);
			if ($result[0] == "successfull-ok") {
				$message_type = $GLOBALS['LOG_PRINT_JOB_SEND'];
				$sent_ok++;
			} else {
				$message_type = $GLOBALS['LOG_PRINT_JOB_ERROR'];
				$sent_error++;
			}
			do_log($message_type, $ticket_id, $key["id"], get_text("Receiver") . ": " . substr($key["address"], 8) . " " . $text, 0, "", "", "");
		}
	}
	//========================= E-Mail
	if (array_key_exists("EMAIL", $addresses)) {
		$result = do_email($addresses["EMAIL"], $_POST['frm_subject'], $_POST['frm_text'], $_POST['frm_attachment']);
		$message_text = "";
		if ($result[0] == "OK") {
			$message_type = $GLOBALS['LOG_EMAIL_MESSAGE_SEND'];
			$sent_ok++;
		} else {
			$message_type = $GLOBALS['LOG_EMAIL_MESSAGE_ERROR'];
			$message_text .= $result[1] . "  ";
			$sent_error++;
		}
		$message_text .= get_text("Subject") . ": " . $subject . "  " . get_text("Message text")  . ": " . $text;
		foreach ($addresses["EMAIL"] as $key => $value) {
			$log_text = "";
			$unit_id = 0;
			$facility_id = 0;
			switch ($value["type"]) {
			case "unit":
				$unit_id = $value["id"];
				$log_text .= substr($value["address"], 6) . "  " . $message_text;
				break;
			case "facility":
				$facility_id = $value["id"];
				$log_text .= substr($value["address"], 6) . "  " . $message_text;
				break;
			default:
				//Specify recipient in the log text
				$log_text .= $value["handle"] . "  " . substr($value["address"], 6) . "  " . $message_text;
			}
			do_log($message_type, $ticket_id, $unit_id, $log_text, $facility_id, "", "", "");
		}
	}
	//========================= Return info
	$return_array = array (get_text("No receiver available"), "danger");
	if (($sent_ok > 0) && ($sent_error < 1)) {
		$return_array[0] = get_text("Message sent");
		$return_array[1] = "success";
	}
	if (($sent_ok > 0) && ($sent_error > 0)) {
		$return_array[0] = get_text("Not reach all recipients");
		$return_array[1] = "warning";
	}
	if (($sent_ok < 1) && ($sent_error > 0)) {
		$return_array[0] = get_text("Message not sent");
		$return_array[1] = "danger";
	}
	if (($sent_ok == 0) && ($sent_error == 0)) {
		do_log($GLOBALS['LOG_SMS_MESSAGE_ERROR'], $ticket_id, 0, get_text("No receiver available") . ":  " . $shorttext, 0, "", "", "");
	}
	$return_array[2] = $sent_ok;
	$return_array[3] = $sent_error;
	return $return_array;
}

/*
Actions		A
ADDRESS		B	UPPERCASE on facility
Priority	C
Inc type	D
Written		E
Updated		F
Reporte		G
Phone 		H
Status		I
Address		J
Descrip'n	K
Dispos'n	L
Position	M
Name		N
==========  O	
Start/end	S
Facility 	T	Row hidden if no facility
Handle		U
Scheduled	V
Maxchar		Z	only for shorttext
*/

function get_dispatch_message($ticket_id, $text_sel, $text_type) {
	$match_str = "";
	$short_message = false;
	switch ($text_sel) {
	case null:
	case "message_text":
		$match_str = strtoupper(get_variable("_api_dispatch_text_setng"));
		break;
	case "message_shorttext":
		$match_str = strtoupper(get_variable("_api_dispatch_shorttext_setng"));
		$short_message = true;
		break;
	default:
	}
	$pre_delimiter = $mid_delimiter = $post_delimiter = "";
	switch ($text_type) {
	case "hypertext":
		$pre_delimiter = "<tr><td></td><td class='big'>";
		$mid_delimiter = "";
		$post_delimiter = "</td><td></td></tr>";
		break;
	case "postscript":
	case "plaintext":
		$pre_delimiter = "";
		$mid_delimiter = "";
		$post_delimiter = "\\r\\n";
		break;
	default:
	}
	$text_settings_string = array ();
	$text_setting = array ();
	$text_selects = array ();
	$text_start = array ();
	$text_chars = array ();
	$message_text = "";
	$max_chars_shorttext = 0;
	$text_settings_string = explode(";", remove_nls($match_str));
	foreach ($text_settings_string as $value) {
		preg_match("/^[a-zA-Z]{1}\s?[0-9]{1,3}\s?,\s?[0-9]{1,3}/", trim($value), $text_setting);
		if (isset ($text_setting[0])) {
			array_push($text_selects, substr($text_setting[0], 0, 1));
			$text_setting = explode(",", substr($text_setting[0], 1));
			array_push($text_start, trim($text_setting[0]));
			array_push($text_chars, trim($text_setting[1]));
		}
	}
	if ($ticket_id > 0) {

		$query = "SELECT `t`.`incident_name`, " .
			"`t`.`severity`, " .
			"`t`.`incident_type_id`, " .
			"`t`.`datetime`, " .
			"`t`.`updated`, " .
			"`t`.`contact`, " .
			"`t`.`phone`, " .
			"`t`.`status`, " .
			"`t`.`location`, " .
			"`t`.`description`, " .
			"`t`.`comments`, " .
			"`t`.`call_taker_id`, " .
			"`t`.`description`, " .
			"`t`.`lat`, " .
			"`t`.`lng`, " .
			"`t`.`problemstart`, " .
			"`t`.`booked_date`, " .
			"`t`.`facility_id`, " .
			"`f`.`handle` AS `facilitiy_handle` " .
			"FROM `tickets` `t` " .
			"LEFT JOIN `facilities` `f` ON (`t`.`facility_id` = `f`.`id`) " .
			"WHERE `t`.`id` = " . $ticket_id . " LIMIT 1";

		$result = db_query($query, __FILE__, __LINE__);
		$row = stripslashes_deep(db_fetch_array($result));
		$_problemend = "";
		if ((isset ($row['problemend'])) && is_datetime($row['problemend'])) {
			$_problemend = "  " . get_text("Run End") . ":" . $row['problemend'];
		}
		for ($i = 0; $i < count($text_selects); $i++) {
			$caption = "";
			$text = "";
			$field_empty = false;
			switch ($text_selects[$i]) {
			case "A":
				$caption = get_text("Actions") . ": ";

				$query = "SELECT * " .
					"FROM `actions` " .
					"WHERE `ticket_id` = " . $ticket_id;

				$result = db_query($query, __FILE__, __LINE__);
				if (db_affected_rows($result) > 0) {
					while ($act_row = stripslashes_deep(db_fetch_array($result))) {
						$text .= date(get_variable("date_format"), strtotime($act_row['updated'])) . " - " . wordwrap($act_row['description'], 70)."\n";
					}
				}
				unset ($result);
				break;
			case "B":
				$caption = get_text("Addr") . ": ";
				if (isset ($row['location'])) {
					if ($row['facility_id'] > 0) {
						$text = strtoupper(remove_nls($row['location']));
					} else {
						$text = remove_nls($row['location']);
					}
				}
				break;
			case "C":
				$caption = get_text("Severity") . ": ";
				$text = get_text(get_severity($row['severity']));
				break;
			case "D":
				$caption = get_text("Incident type") . ": ";
				$text = get_type($row['incident_type_id']);
				break;
			case "E":
				$caption = get_text("Written") . ": ";
				$text = (empty ($row['datetime']))? "" : format_date($row['datetime']) . " " . get_text("by") . " " . get_user_name($row['call_taker_id']);
				break;
			case "F":
				$caption = get_text("Updated") . ": ";
				$text = format_date($row['updated']);
				break;
			case "G":
				$caption = get_text("Reported by") . ": ";
				$text = $row['contact'];
				break;
			case "H":
				$caption = get_text("Callback phone") . ": ";
				$text = (empty ($row['phone']))?  "" : $row['phone'];
				break;
			case "I":
				$caption = get_text("Status") . ": ";
				$text = get_status($row['status']);
				break;
			case "J":
				$caption = get_text("Addr") . ": ";
				if (isset ($row['location'])) {
					$text = remove_nls($row['location']);
				}
				break;
			case "K":
				$caption = get_text("Synopsis") . ": ";
				$text = (empty ($row['description']))? "" : remove_nls($row['description']);
				break;
			case "L":
				$caption = get_text("Comments") . ": ";
				$text = (empty ($row['comments']))? "" : remove_nls($row['comments']);
				break;
			case "M":
				$caption = get_text("Position") . ": ";
				$utm = toUTM($row['lat'], $row['lng']);
				$text = $row['lat'] . " " . $row['lng'] . ", " . $utm[3] . $utm[2] . $utm[0] . $utm[1] . "\n";
				break;
			case "N":
				$caption = get_text("Incident name") . ": ";
				if (isset ($row['incident_name'])) {
					$text = remove_nls($row['incident_name']);
				}
				break;
			case "O":
				$caption = "============================================================";
				break;
			case "S":
				$caption = get_text("Run Start") . ": ";
				$text = format_date($row['problemstart']) . $_problemend;
				break;
			case "T":
				if ($row['facility_id'] > 0) {
					$caption = get_text("Facility") . ": ";
					$text = remove_nls($row['facilitiy_handle']);
				} else {
					$field_empty = true;
				}
				break;
			case "U":

				$query_u = "SELECT `r`.`handle` AS `unit_handle`, `r`.`name` AS `unit_name`, `a`.`dispatched`, `a`.`responding`, `a`.`on_scene`, " . 
					"`a`.`u2fenr`, `a`.`u2farr`, `a`.`receiving_facility_id`, `a`.`receiving_location`, `f`.`handle` AS `facility_handle`, " . 
					"`f`.`name` AS `facility_name`, `f`.`street` AS `facility_street`, `f`.`city` AS `facility_city` FROM `assigns` `a` " . 
					"LEFT JOIN `units` `r` ON (`a`.`unit_id` = `r`.`id`) " . 
					"LEFT JOIN `facilities` `f` ON (`a`.`receiving_facility_id` = `f`.`id`) " . 
					"WHERE `a`.`ticket_id` = " . $ticket_id . " AND `clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00' ORDER BY `a`.`dispatched` ASC";

				$result_u = db_query($query_u, __FILE__, __LINE__);
				if (db_num_rows($result_u) > 0) {
					$caption = get_text("Units") . "(" . db_num_rows($result_u) . "): ";
					$text = "";
					while ($u_row = stripslashes_deep(db_fetch_assoc($result_u))) {
						$text .= remove_nls($u_row['unit_handle']) . ", ";
					}
					$text = substr($text, 0, -2);
				}
				unset ($result_u);
				break;
			case "V":
				if (is_datetime($row['booked_date'])) {
					$caption = get_text("Scheduled Date");
					$text = format_date($row['booked_date']) . $_problemend;
				}
				break;
			case "Z":
				$text = "";
				break;
			default:
				$err_str = "mail error: '" . $match_str[$i] . "' @ " .  __LINE__;
				if (!(array_key_exists($err_str, $_SESSION))) {
					do_log($GLOBALS['LOG_ERROR'], 0, 0, $err_str, 0, "", "", "");
					$_SESSION[$err_str] = true;
				}
			}
			$text = substr($text, $text_start[$i], $text_chars[$i]);
			if ($short_message) {
				$message_text .= $text . " ";
			} else {
				if (!$field_empty) {
					$message_text .= $pre_delimiter . html_entity_decode($caption) . $text . $post_delimiter;
				}
			}
		}
	}
	for ($i = 0; $i < count($text_selects); $i++) {
		switch ($text_selects[$i]) {
		case "Z":
			$max_chars_shorttext = $text_chars[$i];
			break;
		}
	}
	$message = array ();
	$message[0] = html_entity_decode($message_text);
	if (($max_chars_shorttext > 0) && ($max_chars_shorttext < 1024)) {
		$message[1] = $max_chars_shorttext;
	} else {
		$message[1] = 1024;
	}
	return $message;
}

function show_send_message_table_left($message_group, $target_id, $target_api_log_id, $ticket_id) {
	$on_scene_receiving_location_disabled_str = " hidden";
	$subject_text_default = "";
	$dispatch_text = array ("", 0);
	$dispatch_shorttext = array ("", 0);
	$subject_text_dispatch = "";
	$textblocks = "message";
	$default_subjects = array ("no setting dispatch", "no setting unit", "no setting facility", "no setting user");
	$default_subjects = explode(",", get_variable("_api_default_subject_setng"));
	$show_fixtext = false;
	switch ($message_group) {
	case "unit_ticket":
//		$on_scene_receiving_location_disabled_str = "";
	case "unit":	
		if ($target_id[0] != 0) {
			$assign_data = get_assigns($target_id[0], $ticket_id);
			$dispatch_shorttext = get_dispatch_message($assign_data[1], "message_shorttext", "plaintext");
			if (($assign_data[1] > 0) && ($target_api_log_id == 0)) {
				$dispatch_text = get_dispatch_message($assign_data[1], "message_text", "plaintext");
				$ticket_id = $assign_data[1];
			} else {
				$message_group = "unit_all";
			}
		} else {
			$dispatch_shorttext = get_dispatch_message($ticket_id, "message_shorttext", "plaintext");
			if (($ticket_id > 0) && ($target_api_log_id == 0)) {
				$dispatch_text = get_dispatch_message($ticket_id, "message_text", "plaintext");
			} else {
				$message_group = "unit_all";
			}
		}
		$subject_text_dispatch = $default_subjects[0];
		$subject_text_default = $default_subjects[1];
		$show_fixtext = true;
		break;	
	case "unit_all":
	case "unit_service":
	case "unit_tickets":
		$dispatch_shorttext = get_dispatch_message($ticket_id, "message_shorttext", "plaintext");
		$subject_text_default = $default_subjects[1];
		$show_fixtext = true;
		$textblocks = "message";
		break;
	case "facility_all":
	case "facility":
		$subject_text_default = $default_subjects[2];
		$textblocks = "message";
		break;
	case "user_all":
	case "user":
		$subject_text_default = $default_subjects[3];
		$textblocks = "message";
		break;
	default:
	}
	$dispatch_message_disabled_str = " disabled";
	$individual_message_selected_str = " selected";
	$dispatch_message_selected_str = "";
	if ($dispatch_text[0] != "") {
		$dispatch_message_disabled_str = "";
		$individual_message_selected_str = "";
		$dispatch_message_selected_str = " selected";
	}
	$text_type_dropdown_option_str = "";
	$optgroup_start_message = $optgroup_start_fixtext = $optgroup_stop = $report_channels_str = "";
	if ($show_fixtext) {
		$text_type_dropdown_option_str = get_fixtext_option_str();
		$optgroup_start_message = "<optgroup label=\"" . get_text("Message texts") . "\">";
		$optgroup_start_fixtext = "<optgroup label=\"" . get_text("Message fixtexts") . "\">";
		$optgroup_stop = "</optgroup>";
		$report_channels_str = get_fixtext_report_channels_str();
	}
	$match_array = get_api_configuration("unit");
	$api_reporting_channel_regexp = array ();
	if (isset ($match_array[get_variable("_api_prefix_phone_encdg")]["REGEXP"])) {
		$api_reporting_channel_regexp[0] = $match_array[get_variable("_api_prefix_phone_encdg")]["REGEXP"];
	}
	if (isset ($match_array["EMAIL"]["REGEXP"])) {
		$api_reporting_channel_regexp[1] = $match_array["EMAIL"]["REGEXP"];
	}
	if (isset ($match_array[get_variable("_api_prefix_printer_encdg")]["REGEXP"])) {
		$api_reporting_channel_regexp[2] = $match_array[get_variable("_api_prefix_printer_encdg")]["REGEXP"];
	}
	for ($i = 1; $i <= 5; $i++) {
		$api_reporting_channel_regexp[$i + 2] = "";
		if (isset ($match_array[get_variable("_api_prefix_reporting_channel_" . $i . "_encdg")]["REGEXP"])) {
			$api_reporting_channel_regexp[$i + 2] = $match_array[get_variable("_api_prefix_reporting_channel_" . $i . "_encdg")]["REGEXP"];
		}
	}
	?>
	<?php print show_day_night_style();?>
	<style>
		.table, td {
			overflow: visible !important;
		}
	</style>
	<script>
		<?php print $report_channels_str;?>
		var initial_message_group = "<?php print $message_group;?>";
		var current_message_group = "<?php print $message_group;?>";

		var shorttext_max_char = <?php print $dispatch_shorttext[1];?>;

		function count_chars() {
			var chars_left = shorttext_max_char - $("#frm_shorttext").val().length;
			$("#chars_nr").html(chars_left);
			if (chars_left <= 0) {
				$("#frm_shorttext").css("backgroundColor", "#FFB8B8");
			} else {
				if (chars_left < (shorttext_max_char / 10)) {
					$("#frm_shorttext").css("backgroundColor", "#FFFF88");
				} else {
					$("#frm_shorttext").css("backgroundColor", "#E6E6E6");
				}
			}
		}

		setInterval(fill_shorttext, 100);
		function fill_shorttext() {
			if (current_message_group == "unit_all") {
				$("#frm_shorttext").val($("#frm_text").val().substr(0, shorttext_max_char));
				count_chars();
			}
		}

		function set_message_group(message_group) {
			switch (message_group) {
			case "unit_all":
			case "unit_service":
			case "unit_tickets":
				$("#options_bar").css("visibility", "visible");
				$("#frm_subject").prop("readonly", false);
				$("#frm_text").prop("readonly", false);
				$("#textblocks_dropdown").prop("disabled", false);
				$("#textblocks_dropdown").css("backgroundColor", "#FFFFFF");
				$("#input_shorttext").css("visibility", "visible");
				$("#frm_subject").val("<?php print $subject_text_default;?>");
				$("#caption_message_text").html("<?php print get_text("Message text");?>:");
				$("#frm_text").val("");
				$("#frm_shorttext").val("");
				$("#frm_text").focus();
				count_chars();
				current_message_group = "unit_all";
				break;
			case "unit":
			case "unit_ticket":
				$("#options_bar").css("visibility", "visible");
				$("#frm_subject").prop("readonly", true);
				$("#frm_text").prop("readonly", true);
				$("#textblocks_dropdown").prop("disabled", true);
				$("#textblocks_dropdown").css("backgroundColor", "#EEE");
				$("#input_shorttext").css("visibility", "visible");
				$("#frm_subject").val("<?php print $subject_text_dispatch;?>");
				$("#caption_message_text").html("<?php print get_text("Dispatch text");?>:");
				$("#frm_text").val("<?php print $dispatch_text[0];?>");
				$("#frm_shorttext").val("<?php print $dispatch_shorttext[0];?>".substr(0, shorttext_max_char));
				count_chars();
				current_message_group = "unit";
				break;
			case "facility_all":
			case "facility":
				$("#frm_subject").prop("readonly", false);
				$("#frm_text").prop("readonly", false);
				$("#textblocks_dropdown").prop("disabled", false);
				$("#textblocks_dropdown").css("backgroundColor", "#FFFFFF");
				$("#input_shorttext").css("visibility", "hidden");
				$("#frm_subject").val("<?php print $subject_text_default;?>");
				$("#caption_message_text").html("<?php print get_text("Message text");?>:");
				$("#frm_text").focus();
				current_message_group = "facility_all";
				break;
			case "user_all":
			case "user":
				$("#frm_subject").prop("readonly", false);
				$("#frm_text").prop("readonly", false);
				$("#textblocks_dropdown").prop("disabled", false);
				$("#textblocks_dropdown").css("backgroundColor", "#FFFFFF");
				$("#input_shorttext").css("visibility", "hidden");
				$("#frm_subject").val("<?php print $subject_text_default;?>");
				$("#caption_message_text").html("<?php print get_text("Message text");?>:");
				$("#frm_text").focus();
				current_message_group = "user_all";
				break;
			case "fixtext":
				$("#frm_subject").prop("readonly", true);
				$("#frm_text").prop("readonly", true);
				$("#textblocks_dropdown").prop("disabled", true);
				$("#textblocks_dropdown").css("backgroundColor", "#EEE");
				$("#input_shorttext").css("visibility", "visible");
				$("#frm_subject").val($("#text_type option:selected").text());
				$("#caption_message_text").html("<?php print get_text("Message fixtexts");?>:");
				$("#frm_text").val($("#text_type option:selected").text());
				$("#frm_shorttext").val($("#text_type option:selected").text().substr(0, shorttext_max_char));
				count_chars();
				current_message_group = "fixtext";
				break;
			default:
			}
		}

		function set_message_type(message_type) {
			disable_reporting_channel(".*", false);
			switch (message_type) {
			case "DISPATCH_MESSAGE":
				set_message_group("unit");
				break;
			case "INDIVIDUAL_MESSAGE":
				set_message_group("unit_all");
				break;
			default:
				if (message_type.substr(0, 8) == "FIXTEXT_") {
					set_message_group("fixtext");
					if ((fixtext_report_channels[message_type] & 128) == 0) {		
						if ("<?php print $api_reporting_channel_regexp[7];?>" != "") {
							disable_reporting_channel("<?php print $api_reporting_channel_regexp[7];?>", true);
						}
					}
					if ((fixtext_report_channels[message_type] & 64) == 0) {
						if ("<?php print $api_reporting_channel_regexp[6];?>" != "") {
							disable_reporting_channel("<?php print $api_reporting_channel_regexp[6];?>", true);
						}
					}
					if ((fixtext_report_channels[message_type] & 32) == 0) {
						if ("<?php print $api_reporting_channel_regexp[5];?>" != "") {
							disable_reporting_channel("<?php print $api_reporting_channel_regexp[5];?>", true);
						}
					}
					if ((fixtext_report_channels[message_type] & 16) == 0) {
						if ("<?php print $api_reporting_channel_regexp[4];?>" != "") {
							disable_reporting_channel("<?php print $api_reporting_channel_regexp[4];?>", true);
						}
					}
					if ((fixtext_report_channels[message_type] & 8) == 0) {
						if ("<?php print $api_reporting_channel_regexp[3];?>" != "") {
							disable_reporting_channel("<?php print $api_reporting_channel_regexp[3];?>", true);
						}
					}
					if ((fixtext_report_channels[message_type] & 4) == 0) {
						if ("<?php print $api_reporting_channel_regexp[2];?>" != "") {
							disable_reporting_channel("<?php print $api_reporting_channel_regexp[2];?>", true);
						}
					}
					if ((fixtext_report_channels[message_type] & 2) == 0) {
						if ("<?php print $api_reporting_channel_regexp[1];?>" != "") {
							disable_reporting_channel("<?php print $api_reporting_channel_regexp[1];?>", true);
						}
					}
					if ((fixtext_report_channels[message_type] & 1) == 0) {
						if ("<?php print $api_reporting_channel_regexp[0];?>" != "") {
							disable_reporting_channel("<?php print $api_reporting_channel_regexp[0];?>", true);
						}
					}
				} else {
					set_message_group("unit_all");
				}
			}
		}

		$(document).ready(function() {
			set_message_group(initial_message_group);
		});

	</script>
	<input type="hidden" name="ticket_id" value=<?php print $ticket_id;?>>
	<input type="hidden" name="frm_attachment" value="">
	<table class="table table-striped table-condensed" style="table-layout: fixed;">
		<tr id="options_bar" style="visibility: hidden;">
			<th style="width: 33%; border-top: 0px;">
				<?php print get_text("Message text");?>:
				<div>
					<select id="text_type" name="text_type" class="sit label" style="margin-top: 5px;" onchange="set_message_type(this.options[this.selectedIndex].value);" tabindex=1>
						<?php print $optgroup_start_message;?>
						<option value="INDIVIDUAL_MESSAGE"<?php print $individual_message_selected_str;?>><?php print get_text("Message text");?></option>
						<option value="DISPATCH_MESSAGE"<?php print $dispatch_message_selected_str . $dispatch_message_disabled_str;?>><?php print get_text("Dispatch text");?></option>
						<?php print $optgroup_stop;?>
						<?php print $optgroup_start_fixtext;?>
						<?php print $text_type_dropdown_option_str;?>
						<?php print $optgroup_stop;?>
					</select>
				</div>
			</th>
			<th style="width: 33%; border-top: 0px;">
				<div<?php print $on_scene_receiving_location_disabled_str;?>>
					<?php print get_text("On-Scene location");?>:
					<div>
						<select class="sit label" style="margin-top: 5px;" tabindex=2>
							<option><?php print get_text("Test");?></option>
						</select>
					</div>
				</div>
			</th>
			<th style="width: 33%; border-top: 0px;">
				<div<?php print $on_scene_receiving_location_disabled_str;?>>
					<?php print get_text("Receiving location");?>:
					<div>
						<select class="sit label" style="margin-top: 5px;" tabindex=3>
							<option><?php print get_text("Test");?></option>
						</select>
					</div>
				</div>
			</th>
		</tr>
		<tr>
			<td colspan=3>
				<strong><?php print get_text("Subject");?>:</strong>
				<input name="frm_subject" id="frm_subject" class="form-control" style="margin-top: 5px;" cols="48" readonly tabindex=4>
			</td>
		</tr>
		<tr>
			<td colspan=3>
				<strong id="caption_message_text"></strong>
				<textarea name="frm_text" id="frm_text" class="form-control" style="margin-top: 5px;" cols="48" rows="13" readonly tabindex=5></textarea>
				<?php print get_textblock_select_str($textblocks, "document.message_form.frm_text", "textblocks_dropdown", 0, "");?>
			</td>
		</tr>
		<tr id="input_shorttext" style="visibility: hidden;">
			<td colspan=3>
				<strong><?php print get_text("Message shorttext");?>:</strong>
				<textarea style="margin-top: 5px;" name="frm_shorttext" id="frm_shorttext" class="form-control" cols="48" rows="3" readonly tabindex=7></textarea>
				<div style="margin-top: 5px;"><?php print get_text("Characters left");?>:&nbsp;<span id="chars_nr"></span></div>
			</td>
		</tr>
	</table>
	<?php
	show_infobox("small");
}

function show_unit($row, $assign, $count_receiver, $destination_regexp) {
	$checked_str = "";
	if ($row['group'] == 0) {
		$checked_str = " checked";
	}
	$flag_str = "";
	if ((($row['group'] == 0) && ($assign != 0)) || ($row['group'] == 1)) {
		$flag_str = "<span" . get_title_str("<nobr>" . get_help_text("flag_ticket") . "</nobr>") . " class=\"glyphicon glyphicon-flag\" aria-hidden=\"true\" style=\"font-size: 12px; padding-left: 2px;\"></span>";
	}
	?>
		<tr>
			<th><div style="overflow: hidden; text-overflow: ellipsis;"><?php print $flag_str;?></div></th>
			<td>
				<div>
	 				<input type="hidden" name="receiver[]" value="unit_id:<?php print $row['unit_id'];?>">
					<input type="checkbox" id="receiver_<?php print $count_receiver;?>_checkbox_0" onclick="checkbox_clicked(this.id);" <?php print $checked_str;?>>
				</div>
			</td>
			<td<?php print get_title_unit_str($row);?> style="text-align: left; font-weight: bold; font-size: 16px; vertical-align: top;">
				<div style="overflow:hidden; text-overflow:ellipsis;">
					<span class="label" style="background-color: <?php print $row['background_color'];?>; color: <?php print $row['text_color'];?>;"><?php print $row['handle'];?></span>
				</div>
			</td>
			<th>
	<?php
	$match_array = get_api_configuration("unit");
	$addresses = array ();
	$addresses = get_units_addresses($row['remote_data_services'], $row['unit_phone'], $row['unit_email']);
	$i = array ();
	foreach ($match_array as $key => $value) {
		$i[$key] = 1;
	}
	$count_rows = 0;
	foreach ($addresses as $key => $value) {
		foreach ($value as $key2 => $value2) {
			$count_rows++;
		}
	}
	$new_column = round($count_rows/2) + 1;
	$first_column = true;
	$count_rows = 1;
	foreach ($addresses as $key => $value) {
		$caption = $match_array[$key]["CAPTION"];
		foreach ($value as $key2 => $value2) {
			if ((preg_match("/^" . $destination_regexp . "$/", trim($value2))) || (($destination_regexp == "") && ($checked_str != ""))) {
				$checked_str = " checked";
			} else {
				$checked_str = "";
			}
			if (preg_match("/^" . $destination_regexp . "$/", trim($value2))) {
	?>
			<script>
				$("#receiver_<?php print $count_receiver;?>_checkbox_0").prop("checked", false);
				$("#receiver_<?php print $count_receiver;?>_checkbox_0").prop("indeterminate", true);
			</script>
	<?php
			}
			if (count($addresses[$key]) > 1) {
				$caption = $match_array[$key]["CAPTION"] . " " . $i[$key];
			}
	?>
				<div style="overflow: hidden; text-overflow: ellipsis;">
					<input type="checkbox" name="receiver[]" id="receiver_<?php print $count_receiver . "_checkbox_" . $count_rows;?>" value="<?php print $value2;?>" onclick="checkbox_clicked(this.id);"<?php print $checked_str;?>>
					<span id="receiver_<?php print $count_receiver . "_caption_" . $count_rows;?>" style="vertical-align: top;"<?php print get_title_str($value2);?>><?php print $caption;?></span>	
				</div>
	<?php
			$i[$key]++;
			$count_rows++;
			if ($count_rows == $new_column) {
				$first_column = false;
				print "</th><th>";
			}
		}
	}
	if ($first_column) {
		print "</th><th>";
	}
	?>
			</th>
		</tr>
	<?php
	$checkboxes = 0;
	foreach ($i as $key => $value) {
		$checkboxes = $checkboxes + $value - 1;
	}
	return $checkboxes;
}

function show_facility($row, $count_receiver) {
	$checked_str = "";
	if ($row['group'] == 0) {
		$checked_str = " checked";
	}

	$mail_addresses_security = array ();
	if (trim(remove_nls($row['security_email']))) {
		$mail_addresses_security = explode(",", remove_nls($row['security_email']));
	}
	$mail_addresses_contact = array ();
	if (trim(remove_nls($row['contact_email']))) {
		$mail_addresses_contact = explode(",", remove_nls($row['contact_email']));
	}
	$count_rows = 0;
	foreach ($mail_addresses_security as $value) {
		$count_rows++;
	}
	foreach ($mail_addresses_contact as $value) {
		$count_rows++;
	}
	$new_column = round($count_rows/2) + 1;
	$first_column = true;
	$count_rows = 1;
	?>
		<tr>
			<th><div style="overflow: hidden; text-overflow: ellipsis;"></div></th>
			<td>
				<div>
					<input type="hidden" name="receiver[]" value="facility_id:<?php print $row['fac_id'];?>">
					<input type="checkbox" id="receiver_<?php print $count_receiver;?>_checkbox_0" onclick="checkbox_clicked(this.id);" <?php print $checked_str;?>>
				</div>
			</td>
			<td<?php print get_title_facility_str($row);?> style="text-align: left; font-weight: bold; font-size: 16px; vertical-align: top;">
				<div style="overflow:hidden; text-overflow:ellipsis;">
					<span class="label" style="background-color: <?php print $row['fac_background_color'];?>; color: <?php print $row['fac_text_color'];?>;"><?php print $row['handle'];?></span>
				</div>
			</td>
			<th>
	<?php
	$i = 1;
	$contact_caption = get_text("Security email");
	foreach ($mail_addresses_security as $address) {
		if (is_email(trim($address))) {
			if (count($mail_addresses_security) > 1) {
				$contact_caption = get_text("Security email") . " " . $i;
			}
	?>
				<div style="overflow: hidden; text-overflow: ellipsis;">
					<input type="checkbox" name="receiver[]" id="<?php print "receiver_" . $count_receiver . "_checkbox_" . $i;?>" value="EMAIL:<?php print trim($address);?>" onclick="checkbox_clicked(this.id);"<?php print $checked_str;?>>
					<span<?php print get_title_str(trim($address));?> style="vertical-align: top;"><?php print $contact_caption;?></span>
				</div>
	<?php
			$i++;
			$count_rows++;
			if ($count_rows == $new_column) {
				$first_column = false;
				print "</th><th>";
			}
		}
	}
	$contact_caption = get_text("Contact email");
	foreach ($mail_addresses_contact as $address) {
		if (is_email(trim($address))) {
			if (count($mail_addresses_contact) > 1) {
				$contact_caption = get_text("Contact email") . " " . $i;
			}
	?>
				<div style="overflow: hidden; text-overflow: ellipsis;">
					<input type="checkbox" name="receiver[]" id="<?php print "receiver_" . $count_receiver . "_checkbox_" . $i;?>" value="EMAIL:<?php print trim($address);?>" onclick="checkbox_clicked(this.id);"<?php print $checked_str;?>>
					<span<?php print get_title_str(trim($address));?> style="vertical-align: top;"><?php print $contact_caption;?></span>
				</div>
	<?php
			$i++;
			$count_rows++;
			if ($count_rows == $new_column) {
				$first_column = false;
				print "</th><th>";
			}
		}
	}
	if ($first_column) {
		print "</th><th>";
	}
	?>
		</tr>
	<?php
	return $i - 1;
}

function show_user($row, $count_receiver) {
	switch ($row['level']) {
	case 0:
		$background_color = "#FF0000";
		break;
	case 1:
		$background_color = "#008000";
		break;
	case 2:
		$background_color = "#0000FF";
		break;
	case 3:
		$background_color = "#808080";
		break;
	default:
		$background_color = "#808080";
	}
	$checked_str = "";
	if ($row['group'] == 0) {
		$checked_str = " checked";
	}
	$mail_addresses = array ();
	$mail_addresses = explode(",", remove_nls($row['email']));

	?>
		<tr>
			<th><div style="overflow: hidden; text-overflow: ellipsis;"></div></th>
			<td>
				<div>
					<input type="hidden" name="receiver[]" value="user_id:<?php print $row['id'];?>">
					<input type="checkbox" id="receiver_<?php print $count_receiver;?>_checkbox_0" onclick="checkbox_clicked(this.id);"<?php print $checked_str;?>>
				</div>
			</td>
		  	<td<?php print get_help_text_str("level_" . $row['level']);?> style="text-align: left; font-weight: bold; font-size: 16px; vertical-align: top;">
				<div style="overflow:hidden; text-overflow:ellipsis;">
					<span class="label" style="background-color: <?php print $background_color;?>; color: #FFFFFF;"><?php print remove_nls($row['user_name']);?></span>
				</div>
			</td>
			<th>
	<?php
	$count_rows = 0;
	foreach ($mail_addresses as $mail_address) {
		$count_rows++;
	}
	$new_column = round($count_rows/2) + 1;
	$first_column = true;
	$count_rows = 1;
	$i = 1;
	$contact_caption = get_text("Email");
	foreach ($mail_addresses as $address) {
		if (is_email(trim($address))) {
			if (count($mail_addresses) > 1) {
				$contact_caption = get_text("Email") . " " . $i;
			}
	?>
				<div style="overflow: hidden; text-overflow: ellipsis;">
					<input type="checkbox" name="receiver[]" id="<?php print "receiver_" . $count_receiver . "_checkbox_" . $i;?>" value="EMAIL:<?php print trim($address);?>" onclick="checkbox_clicked(this.id);"<?php print $checked_str;?>>
					<span<?php print get_title_str(trim($address));?> style="vertical-align: top;"><?php print $contact_caption;?></span>
				</div>
	<?php
			$i++;
			$count_rows++;
			if ($count_rows == $new_column) {
				$first_column = false;
				print "</th><th>";
			}
		}
	}
	?>
			</th>
			<th>
			</th>
		</tr>
	<?php
	if ($first_column) {
		print "</th><th>";
	}
	return $i - 1;
}

function get_additional_receiver_str($message_group) {
	$additional_receivers_str = "";
	$i = 1;
	$match_array = get_api_configuration($message_group);
	foreach ($match_array as $key => $value) {
		$additional_receivers_str .= "<div" . get_title_str($value["CAPTION"]) . ">";
		$additional_receivers_str .= "<div style=\"margin-top: 5px;\"><strong>" . $value["CAPTION"] . ":</strong></div>";
		$additional_receivers_str .= "<input name=\"receiver[]\" type=\"hidden\" value=\"" . $key . ":\">";
		$additional_receivers_str .= "<input name=\"receiver[]\" id=\"" . $key . "\" class=\"form-control\" style=\"margin-top: 5px;\" tabindex=1 cols=48>\r\n";
		$additional_receivers_str .= "</div>";
		$i++;
	}
	return $additional_receivers_str;
}

function show_send_message_table_right($message_group, $target_id, $target_api_log_id, $ticket_id) {
	require_once ("./incs/api.inc.php");
	$flag_head_str = "";
	$checked_all_str = "";
	$assign_data = array (0, 0);
	$additional_receiver_type = "unit";
	$destination_regexp = "";
	$unknown_address = "";
	switch ($message_group) {
	case "unit_all":
	case "unit_service":
		$checked_all_str = " checked";

		$query = "SELECT DISTINCT " .
			"UNIX_TIMESTAMP(`u`.`updated`) AS `updated`, " .
			"`t`.`id` AS `type_id`, " .
			"`t`.`bg_color` AS `background_color`, " .
			"`t`.`text_color` AS `text_color`, " .
			"`u`.`id` AS `unit_id`, " .
			"`u`.`name` AS `unit_name`, " .
			"`u`.`handle` AS `handle`, " .
			"`u`.`multi` AS `multi`, " .
			"`u`.`unit_phone`, " .
			"`u`.`remote_data_services`, " .
			"`u`.`unit_email`, " .
			"'0' AS `group`, " .
			"`s`.`description` AS `stat_descr`, " .
			"`s`.`sort` AS `stat_sort`, " .
			"`s`.`dispatch` AS `stat_dispatch`, " .
			"`u`.`description` AS `unit_descr`, " .
			"`t`.`description` AS `type_descr`, " .
			"`t`.`name` AS `type_name`, " .
			"`f`.`handle` AS `guard_house_handle`, " .
			"`f`.`street` AS `guard_house_street`, " .
			"`f`.`city` AS `guard_house_city`, " .
			"(SELECT  COUNT(*) FROM `assigns` " .
				"WHERE `assigns`.`unit_id` = `unit_id` " .
				"AND (`clear` IS NULL OR DATE_FORMAT(`clear`,'%y') = '00')) AS `nr_assigned` " .
			"FROM `units` `u` " .
			"LEFT JOIN `allocates` `a` ON `u`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
			"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
			"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
			"WHERE (`dispatch` < 3) AND (`a`.`id` IS NOT NULL) AND " .
				"((`unit_phone` REGEXP '" . get_regexp_phone() . "') " .
				"OR (`remote_data_services` REGEXP '" . get_regexp_smsg_id() . "') OR " .
				"(`unit_email` REGEXP '" . get_regexp_email() . "'));";

		$result_units = db_query($query, __FILE__, __LINE__);
		$receiver_count = db_affected_rows($result_units);
		break;
	case "unit_tickets":
		$checked_all_str = " checked";

		$query = "SELECT DISTINCT UNIX_TIMESTAMP(`u`.`updated`) AS `updated`, " .
			"`t`.`id` AS `type_id`, " .
			"`t`.`bg_color` AS `background_color`, " .
			"`t`.`text_color` AS `text_color`, " .
			"`u`.`id` AS `unit_id`, " .
			"`u`.`name` AS `unit_name`, " .
			"`u`.`handle` AS `handle`, " .
			"`u`.`multi` AS `multi`, " .
			"`u`.`unit_phone`, " .
			"`u`.`remote_data_services`, " .
			"`u`.`unit_email`, " .
			"'0' AS `group`, " .
			"`s`.`description` AS `stat_descr`, " .
			"`s`.`sort` AS `stat_sort`, " .
			"`s`.`dispatch` AS `stat_dispatch`, " .
			"`u`.`description` AS `unit_descr`, " .
			"`t`.`description` AS `type_descr`, " .
			"`t`.`name` AS `type_name`, " .
			"`f`.`handle` AS `guard_house_handle`, " .
			"`f`.`street` AS `guard_house_street`, " .
			"`f`.`city` AS `guard_house_city` " .
			"FROM `units` `u` " .
			"LEFT JOIN `allocates` `a` ON `u`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
			"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
			"LEFT JOIN `assigns` `as` ON (`u`.`id` = `as`.`unit_id`) " .
			"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
			"WHERE (`dispatch` < 3) AND (`a`.`id` IS NOT NULL) AND " .
				"((`unit_phone` REGEXP '" . get_regexp_phone() . "') OR " .
				"(`remote_data_services` REGEXP '" . get_regexp_smsg_id() . "') OR " .
				"(`unit_email` REGEXP '" . get_regexp_email() . "')) AND " .
				"(`as`.`clear` IS NULL OR DATE_FORMAT(`as`.`clear`,'%y') = '00');";

		$result_units = db_query($query, __FILE__, __LINE__);
		$receiver_count = db_affected_rows($result_units);
		break;
	case "unit_ticket":
		$where_str = "(`u`.`id` IN (" . implode(",", $target_id) . "))";
		if ($target_id[0] == 0) {
			$where_str = "(`u`.`id` IN (SELECT `unit_id` FROM `assigns` WHERE `ticket_id` =  " . $ticket_id . "))";
		}
		$checked_all_str = " checked";

		$query = "SELECT DISTINCT " .
			"UNIX_TIMESTAMP(`u`.`updated`) AS `updated`, " .
			"`t`.`id` AS `type_id`, " .
			"`t`.`bg_color` AS `background_color`, " .
			"`t`.`text_color` AS `text_color`, " .
			"`u`.`id` AS `unit_id`, " .
			"`u`.`name` AS `unit_name`, " .
			"`u`.`handle` AS `handle`, " .
			"`u`.`multi` AS `multi`, " .
			"`u`.`unit_phone`, " .
			"`u`.`remote_data_services`, " .
			"`u`.`unit_email`, " .
			"'0' AS `group`, " .
			"`s`.`description` AS `stat_descr`, " .
			"`s`.`sort` AS `stat_sort`, " .
			"`s`.`dispatch` AS `stat_dispatch`, " .
			"`u`.`description` AS `unit_descr`, " .
			"`t`.`description` AS `type_descr`, " .
			"`t`.`name` AS `type_name`, " .
			"`f`.`handle` AS `guard_house_handle`, " .
			"`f`.`street` AS `guard_house_street`, " .
			"`f`.`city` AS `guard_house_city` " .
			"FROM `units` `u` " .
			"LEFT JOIN `allocates` `a` ON `u`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
			"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
			"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
			"WHERE (`dispatch` < 3) AND (`a`.`id` IS NOT NULL) AND " .
				"((`unit_phone` REGEXP '" . get_regexp_phone() . "') OR " .
				"(`remote_data_services` REGEXP '" . get_regexp_smsg_id() . "') OR " .
				"(`unit_email` REGEXP '" . get_regexp_email() . "')) AND " . $where_str . ";";

		$result_units = db_query($query, __FILE__, __LINE__);
		$receiver_count = db_affected_rows($result_units);
		break;
	case "unit":
		if ($target_api_log_id != 0) {

			$query_api_log = "SELECT `source`, " .
				"`source_regexp`, " .
				"`unit_id` " .
				"FROM `api_log` " .
				"WHERE `id` = " . $target_api_log_id;

			$result_api_log = db_query($query_api_log, __FILE__, __LINE__);
			$row_api_log = stripslashes_deep(db_fetch_array($result_api_log));
			$target_id[0] = $row_api_log['unit_id'];
			$destination_regexp = $row_api_log['source_regexp'];
			if ($row_api_log['unit_id'] == 0) {
				$unknown_address = $row_api_log['source'];
			}
		}
		$where1_str = " AND (`as`.`ticket_id` = 0)";
		$where2_str = "";
		if ($target_id[0] != 0) {
			$where2_str = " AND (`u`.`id` != " . $target_id[0] . ")";
			$assign_data = get_assigns($target_id[0], $ticket_id);
			if ($assign_data[1]) {
				$where1_str = " AND (`as`.`ticket_id` = " . $assign_data[1] . ")";
				$where2_str = " AND (`u`.`id` NOT IN (SELECT `unit_id` FROM `assigns` WHERE `ticket_id` =  " . $assign_data[1] . "))";
			}
		}

		$query = "(SELECT DISTINCT UNIX_TIMESTAMP(`u`.`updated`) AS `updated`, " .
			"`t`.`id` AS `type_id`, " .
			"`t`.`bg_color` AS `background_color`, " .
			"`t`.`text_color` AS `text_color`, " .
			"`u`.`id` AS `unit_id`, " .
			"`u`.`name` AS `unit_name`, " .
			"`u`.`handle` AS `handle`, " .
			"`u`.`multi` AS `multi`, " .
			"`u`.`unit_phone`, " .
			"`u`.`remote_data_services`, " .
			"`u`.`unit_email`, " .
			"'0' AS `group`, " .
			"`s`.`description` AS `stat_descr`, " .
			"`s`.`sort` AS `stat_sort`, " .
			"`s`.`dispatch` AS `stat_dispatch`, " .
			"`u`.`description` AS `unit_descr`, " .
			"`t`.`description` AS `type_descr`, " .
			"`t`.`name` AS `type_name`, " .
			"`f`.`handle` AS `guard_house_handle`, " .
			"`f`.`street` AS `guard_house_street`, " .
			"`f`.`city` AS `guard_house_city` " .
			"FROM `units` `u` " .
			"LEFT JOIN `allocates` `a` ON `u`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
			"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
			"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
			"WHERE ((`unit_phone` REGEXP '" . get_regexp_phone() . "') " .
				"OR (`remote_data_services` REGEXP '" . get_regexp_smsg_id() . "') OR " .
				"(`unit_email` REGEXP '" . get_regexp_email() . "')) AND " .
				"(`u`.`id` = " . $target_id[0] . ") AND (`a`.`id` IS NOT NULL)) " .
			"UNION (SELECT DISTINCT UNIX_TIMESTAMP(`u`.`updated`) AS `updated`, " .
			"`t`.`id` AS `type_id`, " .
			"`t`.`bg_color` AS `background_color`, " .
			"`t`.`text_color` AS `text_color`, " .
			"`u`.`id` AS `unit_id`, " .
			"`u`.`name` AS `unit_name`, " .
			"`u`.`handle` AS `handle`, " .
			"`u`.`multi` AS `multi`, " .
			"`u`.`unit_phone`, " .
			"`u`.`remote_data_services`, " .
			"`u`.`unit_email`, " .
			"'1' AS `group`, " .
			"`s`.`description` AS `stat_descr`, " .
			"`s`.`sort` AS `stat_sort`, " .
			"`s`.`dispatch` AS `stat_dispatch`, " .
			"`u`.`description` AS `unit_descr`, " .
			"`t`.`description` AS `type_descr`, " .
			"`t`.`name` AS `type_name`, " .
			"`f`.`handle` AS `guard_house_handle`, " .
			"`f`.`street` AS `guard_house_street`, " .
			"`f`.`city` AS `guard_house_city` " .
			"FROM `units` `u` " .
			"LEFT JOIN `allocates` `a` ON `u`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
			"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
			"LEFT JOIN `assigns` `as` ON (`u`.`id` = `as`.`unit_id`) " .
			"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
			"WHERE (`dispatch` < 3) AND (`a`.`id` IS NOT NULL) AND " .
				"((`unit_phone` REGEXP '" . get_regexp_phone() . "') " .
				"OR (`remote_data_services` REGEXP '" . get_regexp_smsg_id() . "') OR " .
				"(`unit_email` REGEXP '" . get_regexp_email() . "')) AND " .
				"(`u`.`id` != " . $target_id[0] . ") " . $where1_str . ") " .
			"UNION (SELECT DISTINCT UNIX_TIMESTAMP(`u`.`updated`) AS `updated`, " .
			"`t`.`id` AS `type_id`, " .
			"`t`.`bg_color` AS `background_color`, " .
			"`t`.`text_color` AS `text_color`, " .
			"`u`.`id` AS `unit_id`, " .
			"`u`.`name` AS `unit_name`, " .
			"`u`.`handle` AS `handle`, " .
			"`u`.`multi` AS `multi`, " .
			"`u`.`unit_phone`, " .
			"`u`.`remote_data_services`, " .
			"`u`.`unit_email`, " .
			"'2' AS `group`, " .
			"`s`.`description` AS `stat_descr`, " .
			"`s`.`sort` AS `stat_sort`, " .
			"`s`.`dispatch` AS `stat_dispatch`, " .
			"`u`.`description` AS `unit_descr`, " .
			"`t`.`description` AS `type_descr`, " .
			"`t`.`name` AS `type_name`, " .
			"`f`.`handle` AS `guard_house_handle`, " .
			"`f`.`street` AS `guard_house_street`, " .
			"`f`.`city` AS `guard_house_city` " .
			"FROM `units` `u` " .
			"LEFT JOIN `allocates` `a` ON `u`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_UNIT'] . " " .
			"LEFT JOIN `unit_status` `s` ON (`u`.`unit_status_id` = `s`.`id`) " .
			"LEFT JOIN `unit_types` `t` ON (`u`.`type` = `t`.`id`) " .
			"LEFT JOIN `assigns` `as` ON (`u`.`id` = `as`.`unit_id`) " .
			"LEFT JOIN `facilities` `f` ON (`u`.`guard_house_id` = `f`.`id`) " .
			"WHERE (`dispatch` < 3) AND (`a`.`id` IS NOT NULL) AND " .
				"((`unit_phone` REGEXP '" . get_regexp_phone() . "') " .
				"OR (`remote_data_services` REGEXP '" . get_regexp_smsg_id() . "') OR " .
				"(`unit_email` REGEXP '" . get_regexp_email() . "'))" .
				$where2_str . ");";

		$result_units = db_query($query, __FILE__, __LINE__);
		$receiver_count = db_affected_rows($result_units);
		if ($receiver_count == 1) {
			$checked_all_str = " checked";
		}
		if ($assign_data[0] != 0) {
			$flag_head_str = "<span" . get_title_str("<nobr>" . get_help_text("flag_ticket") . "</nobr>") . " class='glyphicon glyphicon-flag' aria-hidden='true' style='font-size: 12px; padding-left: 2px;'>";
		}
		break;
	case "facility_all":
		$checked_all_str = " checked";

		$query = "SELECT DISTINCT `facilities`.`updated` AS `updated`, " .
			"`facilities`.`id` AS `fac_id`, " .
			"`facilities`.`description` AS `facility_description`, " .
			"`facility_types`.`name` AS `fac_type_name`, " .
			"`facility_types`.`bg_color` AS `fac_background_color`, " .
			"`facility_types`.`text_color` AS `fac_text_color`, " .
			"`facilities`.`name` AS `facility_name`, " .
			"`facilities`.`handle`, " .
			"`facility_status`.`status_name` AS `fac_status_val`, " .
			"`facility_status`.`description` AS `fac_status_desc`, " .
			"`facilities`.`facility_status_id` AS `fac_status_id`, " .
			"`facilities`.`security_email`, " .
			"`facilities`.`contact_email`, " .
			"'0' AS `group` " .
			"FROM `facilities` " .
			"LEFT JOIN `allocates` `a` ON `facilities`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_FACILITY'] . " " .
			"LEFT JOIN `facility_types` ON `facilities`.`type` = `facility_types`.`id` " .
			"LEFT JOIN `facility_status` ON `facilities`.`facility_status_id` = `facility_status`.`id` " .
			"WHERE CONCAT(`security_email`, `contact_email`) REGEXP '" . get_regexp_email() . "' AND (`a`.`id` IS NOT NULL)  " .
			"ORDER BY `group`, `handle`;";

		$result_facilities = db_query($query, __FILE__, __LINE__);
		$receiver_count = db_affected_rows($result_facilities);
		$additional_receiver_type = "facility";
		break;
	case "facility":

		$query = "(SELECT `facilities`.`updated` AS `updated`, " .
			"`facilities`.`id` AS `fac_id`, " .
			"`facilities`.`description` AS `facility_description`, " .
			"`facility_types`.`name` AS `fac_type_name`, " .
			"`facility_types`.`bg_color` AS `fac_background_color`, " .
			"`facility_types`.`text_color` AS `fac_text_color`, " .
			"`facilities`.`name` AS `facility_name`, " .
			"`facilities`.`handle`, " .
			"`facility_status`.`status_name` AS `fac_status_val`, " .
			"`facility_status`.`description` AS `fac_status_desc`, " .
			"`facilities`.`facility_status_id` AS `fac_status_id`, " .
			"`facilities`.`security_email`, " .
			"`facilities`.`contact_email`, " .
			"`facilities`.`object_id`, " .
			"'0' AS `group` " .
			"FROM `facilities` " .
			"LEFT JOIN `allocates` `a` ON `facilities`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_FACILITY'] . " " .
			"LEFT JOIN `facility_types` ON `facilities`.`type` = `facility_types`.`id` " .
			"LEFT JOIN `facility_status` ON `facilities`.`facility_status_id` = `facility_status`.`id` " .
			"WHERE CONCAT(`security_email`, `contact_email`) REGEXP '" . get_regexp_email() . "' AND (`a`.`id` IS NOT NULL)  " .
			"AND `facilities`.`id` = " . $target_id[0] . ") " .
			"UNION (" .
			"SELECT `facilities`.`updated` AS `updated`, " .
			"`facilities`.`id` AS `fac_id`, " .
			"`facilities`.`description` AS `facility_description`, " .
			"`facility_types`.`name` AS `fac_type_name`, " .
			"`facility_types`.`bg_color` AS `fac_background_color`, " .
			"`facility_types`.`text_color` AS `fac_text_color`, " .
			"`facilities`.`name` AS `facility_name`, " .
			"`facilities`.`handle`, " .
			"`facility_status`.`status_name` AS `fac_status_val`, " .
			"`facility_status`.`description` AS `fac_status_desc`, " .
			"`facilities`.`facility_status_id` AS `fac_status_id`, " .
			"`facilities`.`security_email`, " .
			"`facilities`.`contact_email`, " .
			"`facilities`.`object_id`, " .
			"'1' AS `group` " .
			"FROM `facilities` " .
			"LEFT JOIN `allocates` `a` ON `facilities`.`id` = `a`.`resource_id` AND `a`.`type` = " . $GLOBALS['TYPE_FACILITY'] . " " .
			"LEFT JOIN `facility_types` ON `facilities`.`type` = `facility_types`.`id` " .
			"LEFT JOIN `facility_status` ON `facilities`.`facility_status_id` = `facility_status`.`id` " .
			"WHERE CONCAT(`security_email`, `contact_email`) REGEXP '" . get_regexp_email() . "' AND (`a`.`id` IS NOT NULL)  " .
			"AND `facilities`.`id` != " . $target_id[0] . ") " .
			"ORDER BY `group`, `handle`;";

		$result_facilities = db_query($query, __FILE__, __LINE__);
		$receiver_count = db_affected_rows($result_facilities);
		if ($receiver_count == 1) {
			$checked_all_str = " checked";
		}
		$additional_receiver_type = "facility";
		break;
	case "user_all":
		$checked_all_str = " checked";

		$query = "SELECT DISTINCT `id`, `level`, `email`, `level`, " .
			"`name` AS `user_name`, " .
			"'0' AS `group` " .
			"FROM `users` " .
			"WHERE `email` REGEXP '" . get_regexp_email() . "' AND `password` <> '55606758fdb765ed015f0612112a6ca7' " .
				"ORDER BY `group`, `level`, `name`;";

		$result_users = db_query($query, __FILE__, __LINE__);
		$receiver_count = db_affected_rows($result_users);
		$additional_receiver_type = "user";
		break;
	case "user":

		$query = "(SELECT DISTINCT *, " .
			"`name` AS `user_name`, " .
			"'0' AS `group` " .
			"FROM `users` " .
			"WHERE `email` REGEXP '" . get_regexp_email() . "' AND `password` <> '55606758fdb765ed015f0612112a6ca7' " .
				"AND `id` = " . $target_id[0] . ") " .
					"UNION " .
					"(SELECT *, " .
					"`name` AS `user_name`, " .
					"'1' AS `group` " .
					"FROM `users` " .
					"WHERE `email` REGEXP '" . get_regexp_email() . "' AND `password` <> '55606758fdb765ed015f0612112a6ca7' " .
				"AND `id` != " . $target_id[0] . ") " .
				"ORDER BY `group`, `level`, `name`;";

		$result_users = db_query($query, __FILE__, __LINE__);
		$receiver_count = db_affected_rows($result_users);
		if ($receiver_count == 1) {
			$checked_all_str = " checked";
		}
		$additional_receiver_type = "user";
		break;
	default:
	}
	?>
	<script>
		var count_checkboxes = new Array();

		function checkbox_clicked(element_id) {
			element_checked = false;
			receiver_number = "";
			checkbox_number = "";
			if ($("#" + element_id).prop("checked")) {
				element_checked = true;
			}
			if (element_id == "select_all") {
				for (var j = 1; j <= count_checkboxes[0]; j++) {
					for (var i = 0; i <= count_checkboxes[j]; i++) {
						$("#receiver_" + j + "_checkbox_" + i).prop("indeterminate", false);
						$("#receiver_" + j + "_checkbox_" + i).prop("checked", element_checked);
					}
				}
			} else {
				receiver_number = element_id.match(/receiver_[0-9]+/)[0];
				receiver_number = receiver_number.substr(9, receiver_number.length);
				checkbox_number = element_id.match(/checkbox_[0-9]+/)[0];
				checkbox_number = checkbox_number.substr(9, checkbox_number.length);
				if (checkbox_number == 0) {
					for (var i = 1; i <= count_checkboxes[receiver_number]; i++) {
						$("#receiver_" + receiver_number + "_checkbox_" + i).prop("checked", element_checked);
					}
				} else {
					checkboxes_checked = 0;
					for (var i = 1; i <= count_checkboxes[receiver_number]; i++) {
						if ($("#receiver_" + receiver_number + "_checkbox_" + i).prop("checked")) {
							checkboxes_checked++;
						}
					}
					if (checkboxes_checked == 0) {
						$("#receiver_" + receiver_number + "_checkbox_0").prop("indeterminate", false);
						$("#receiver_" + receiver_number + "_checkbox_0").prop("checked", false);
					} else {
						if (checkboxes_checked == count_checkboxes[receiver_number]) {
							$("#receiver_" + receiver_number + "_checkbox_0").prop("indeterminate", false);
							$("#receiver_" + receiver_number + "_checkbox_0").prop("checked", true);
						} else {
							$("#receiver_" + receiver_number + "_checkbox_0").prop("checked", false);
							$("#receiver_" + receiver_number + "_checkbox_0").prop("indeterminate", true);
						}
					}
				}
				checkboxes_checked = 0;
				checkboxes_indeterminate = 0;
				for (var i = 1; i <= count_checkboxes[0]; i++) {
					if ($("#receiver_" + i + "_checkbox_0").prop("checked")) {
						checkboxes_checked++;
					}
					if ($("#receiver_" + i + "_checkbox_0").prop("indeterminate")) {
						checkboxes_indeterminate++;
					}
				}
				if ((checkboxes_checked == 0) && (checkboxes_indeterminate == 0)) {
					$("#select_all").prop("indeterminate", false);
					$("#select_all").prop("checked", false);
				} else {
					if ((checkboxes_checked == count_checkboxes[0]) && (checkboxes_indeterminate == 0)) {
						$("#select_all").prop("indeterminate", false);
						$("#select_all").prop("checked", true);
					} else {
						$("#select_all").prop("checked", false);
						$("#select_all").prop("indeterminate", true);
					}
				}
			}
		}

		function disable_reporting_channel(channel, disable) {
			var channel_regexp = new RegExp(channel);
			var element_value = "";
			for (var j = 1; j <= count_checkboxes[0]; j++) {
				for (var i = 0; i <= count_checkboxes[j]; i++) {
					element_value = $("#receiver_" + j + "_checkbox_" + i).val();
					if (element_value.match(channel_regexp)) {
						$("#receiver_" + j + "_checkbox_" + i).prop("disabled", disable);
						if (disable) {
							$("#receiver_" + j + "_caption_" + i).css("color", "#808080");
						} else {
							$("#receiver_" + j + "_caption_" + i).css("color", "#000000");
						}
					}
				}
			}
		}

		function show_hide_additional_receiver() {
			if ($("#additional_receiver_0_checkbox_0").prop("checked") == true) {
				$("#additional_receiver").css("display", "table-row");
			} else {
				$("#additional_receiver").css("display", "none");
			}
		}

		$(document).ready(function() {
			if ($("#unknown_address").html() != "") {
				var result_array = split_api_receiver_str($("#unknown_address").html());
				$("#" + result_array[0]).val(result_array[1]);
				$("#additional_receiver_0_checkbox_0").prop("checked", true);
				show_hide_additional_receiver();
			} else {
				if (!($("#select_all").prop("checked"))) {
					$("#select_all").prop("indeterminate", true);
				}
			}
		});

	</script>
	<style>
		.table, td {
			overflow: visible !important;
		}
	</style>
	<div class="panel panel-default">
		<div id="unknown_address" style="display: none;"><?php print $unknown_address;?></div>
		<table class="table table-striped table-condensed" style="table-layout: fixed;">
			<tr>
				<th style="width: 6%; border-top: 0px;"></th>
				<th style="width: 6%; border-top: 0px;">
					<div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;">
						<input type="checkbox"  name="receiver[]" id="additional_receiver_0_checkbox_0" value="<?php print $additional_receiver_type;?>_id:0" onclick="show_hide_additional_receiver();">
					</div>
				</th>
				<th style="width: 42%; border-top: 0px;">
					<div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("Additional receiver");?></div>
				</th>
				<th style="width: 38%; border-top: 0px;"></th>
				<th style="width: 38%; border-top: 0px;"></th>
			</tr>
			<tr id="additional_receiver" style="display: none;">
				<td colspan=5><?php print get_additional_receiver_str($message_group);?></td>
			</tr>
		</table>
	</div>
	<div class="panel panel-default">
		<table class="table table-striped table-condensed" style="table-layout: fixed;">
			<tr>
				<th style="width: 6%; border-top: 0px;">
					<div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print $flag_head_str;?></div>
				</th>
				<th style="width: 6%; border-top: 0px;">
					<div style="border-top: 0px;">
						<input type="checkbox" name="" id="select_all" onclick="checkbox_clicked(this.id);" <?php print $checked_all_str;?>>
					</div>
				</th>
				<th style="width: 42%; border-top: 0px;">
					<div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("Receiver") . " (" . $receiver_count . ")";?></div>
				</th>
				<th style="width: 38%; border-top: 0px;">
					<div style="overflow: hidden; text-overflow: ellipsis; border-top: 0px;"><?php print get_text("Reporting channel");?></div>
				</th>
				<th style="width: 38%; border-top: 0px;"></th>
			</tr>
	<?php
	$count_receiver = 1;
	$checkboxes = array ();
	switch ($message_group) {
	case "unit_all":
	case "unit_service":
	case "unit_tickets":
	case "unit_ticket":
	case "unit":
		while ($row = stripslashes_deep(db_fetch_array($result_units))) {
			$checkboxes[$count_receiver] = show_unit($row, $assign_data[0], $count_receiver, $destination_regexp);
			$count_receiver++;
		}
		break;
	case "facility_all":
	case "facility":
		while ($row = stripslashes_deep(db_fetch_array($result_facilities))) {
			$checkboxes[$count_receiver] = show_facility($row, $count_receiver);
			$count_receiver++;
		}
		break;
	case "user_all":
	case "user":	
		while ($row = stripslashes_deep(db_fetch_array($result_users))) {
			$checkboxes[$count_receiver] = show_user($row, $count_receiver);
			$count_receiver++;
		}
		break;
	default:
	}
	$checkboxes[0] = $count_receiver - 1;
	?>
		</table>
	</div>
	<script>
	<?php
	foreach ($checkboxes as $key => $value) {
	?>
		count_checkboxes[<?php print $key;?>] = <?php print $value;?> + 0;
	<?php
	}
	?>
	</script>
	<?php
}
?>