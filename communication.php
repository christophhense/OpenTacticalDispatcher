<?php
error_reporting(E_ALL);
@session_start();
require_once ("./incs/functions.inc.php");
require_once ("./incs/log_codes.inc.php");
require_once ("./incs/communication.inc.php");
require_once ("./incs/tickets.inc.php");
do_login(basename(__FILE__));
set_session_expire_time("on");

if (is_operator() || is_admin() || is_super()) {
	$function = "";
	if (isset ($_GET['function'])) {
		$function = $_GET['function'];
	}
	if (($function == "") && isset ($_POST['function'])) {
		$function = $_POST['function'];
	}
	$message_group = "";
	if (isset ($_GET['message_group'])) {
		$message_group = $_GET['message_group'];
	}
	$targets_ids = 0;
	$target_id = array (0);
	if (isset ($_GET['targets_ids'])) {
		$targets_ids = $_GET['targets_ids'];
		$target_id = explode(",", urldecode($targets_ids));
	}
	$target_api_log_id = 0;
	if (isset ($_GET['target_api_log_id'])) {
		$target_api_log_id = $_GET['target_api_log_id'];
	}
	$api_log_id = 0;
	if (isset ($_GET['api_log_id'])) {
		$api_log_id = $_GET['api_log_id'];
	}
	$unit_id = 0;
	if (isset ($_GET['unit_id'])) {
		$unit_id = $_GET['unit_id'];
	}
	$ticket_id = 0;
	if (isset ($_GET['ticket_id'])) {
		$ticket_id = $_GET['ticket_id'];
	}
	if (($ticket_id == 0) && isset ($_POST['ticket_id'])) {
		$ticket_id = $_POST['ticket_id'];
	}
	switch ($function) {
		case "update_send_message":
			//$_POST['on_scene_location'];
			//$_POST['receiving_location'];
			$current_receiver_type = "";	//unit, facility, user
			$current_receiver_id = 0;
			$current_address_type = "";
			$receiver_0_addresses = array ();
			$addresses = array ();
			$match_array = get_api_configuration("default");
			$handles = get_handle_array();
			$i = array ();
			foreach ($match_array as $key => $value) {
				$i[$key] = 0;
			}
			foreach ($_POST['receiver'] as $VarName => $VarValue) {
				if (preg_match("/^unit_id:*/", trim($VarValue))) {
					$current_receiver_id = substr(trim($VarValue), 8);
					$current_receiver_type = "unit";
				}
				if (preg_match("/^facility_id:*/", trim($VarValue))) {
					$current_receiver_id = substr(trim($VarValue), 12);
					$current_receiver_type = "facility";
				}
				if (preg_match("/^user_id:*/", trim($VarValue))) {
					$current_receiver_id = substr(trim($VarValue), 8);
					$current_receiver_type = "user";
				}
				if ($current_receiver_id == 0) {
					$new_receiver_type = false;
					foreach ($match_array as $key => $value) {
						if (preg_match("/^" . $key . ":$/", trim($VarValue))) {	
							$current_address_type = $key;
							$new_receiver_type = true;
						}
					}
					if (!($new_receiver_type) && !(preg_match("/^(unit_id:0)|(facility_id:0)|(user_id:0)/", trim($VarValue)))) {
						$receiver_0_addresses = explode(",", trim($VarValue));
						foreach ($receiver_0_addresses as $value2) {
							//	print $current_receiver_id . "|" . $current_address_type . "|" . $i[$current_address_type] . "|" . $value2 . "|" . $match_array[$current_address_type]["REGEXP"];
							if (preg_match("/^" . $match_array[$current_address_type]["REGEXP"] . "$/", $current_address_type . ":" . trim($value2))) {
								$addresses[$current_address_type][$i[$current_address_type]]["type"] = $current_receiver_type;
								$addresses[$current_address_type][$i[$current_address_type]]["id"] = $current_receiver_id;
								$addresses[$current_address_type][$i[$current_address_type]]["handle"] = "";
								$addresses[$current_address_type][$i[$current_address_type]]["address"] = $current_address_type . ":" . trim($value2);
								$i[$current_address_type]++;
							}
						}
					}
				} else {
					foreach ($match_array as $key => $value) {
						if (preg_match("/" . $value["REGEXP"] . "/", trim($VarValue))) {
							$addresses[$key][$i[$key]]["type"] = $current_receiver_type;
							$addresses[$key][$i[$key]]["id"] = $current_receiver_id;
							$addresses[$key][$i[$key]]["handle"] = $handles[$current_receiver_type][$current_receiver_id];
							$addresses[$key][$i[$key]]["address"] = trim($VarValue);
							$i[$key]++;	
						}
					}
				}
			}
			$result_array = send_message($addresses, $_POST['text_type'], html_entity_decode($_POST['frm_subject']), html_entity_decode($_POST['frm_text']), html_entity_decode($_POST['frm_shorttext']), $ticket_id);
			print json_encode(array (
				"message" => $result_array[0],
				"appearance" => $result_array[1],
				"url" => ""
			));
			break;
		case "table_left_send_message":
			show_send_message_table_left($message_group, $target_id, $target_api_log_id, $ticket_id);
			break;
		case "table_right_send_message":
			show_send_message_table_right($message_group, $target_id, $target_api_log_id, $ticket_id);
			break;
		case "update_communication":
			set_session_expire_time("on");
			$api_log_action = "";
			if (isset ($_GET['api_log_action'])) {
				$api_log_action = $_GET['api_log_action'];
			}
			$result_array = update_communication($api_log_id, $api_log_action);
			print json_encode(array (
				"message" => $result_array[0],
				"appearance" => $result_array[1],
				"url" => $result_array[2],
				"oldest_assign_id" => $result_array[3],
				"call_progression_datetime" => $result_array[4],
				"call_progression" => $result_array[5]
			));
			break;
		case "table_left_communication":
			show_communication_table_left();
			break;
		case "infobox_large_ticket_select":
			?>
			<div class="panel panel-default">
				<?php show_ticketlist("dispatch", 0, $unit_id);?>
			</div>
			<?php
			break;
		case "table_right_communication":
			show_communication_table_right();
			break;
		default:
	}
	switch ($function) {
		case "update_send_message":
		case "table_left_send_message":
		case "table_right_send_message":
		case "update_communication":
		case "table_left_communication":
		case "infobox_large_ticket_select":
		case "table_right_communication":
			break;
		case "send_message":
		default:
	?>
<!doctype html>
<html lang="<?php print get_variable("_locale");?>">
	<head>
		<title><?php print get_variable("page_caption");?></title>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html;">
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Content-Script-Type" content="text/javascript">
		<link href="./css/bootstrap.min.css" rel="stylesheet">
		<link href="./css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="./css/stylesheet.css" rel="stylesheet">
		<script src="./js/jquery-2.1.4.min.js" type="text/javascript"></script>
		<script src="./js/bootstrap.min.js" type="text/javascript"></script>
		<script src="./js/functions.js" type="text/javascript"></script>
		<?php print show_day_night_style();?>
	<?php	
	}
	switch ($function) {
		case "update_send_message":
		case "table_left_send_message":
		case "table_right_send_message":
		case "update_communication":
		case "table_left_communication":
		case "infobox_large_ticket_select":
		case "table_right_communication":
			break;
		case "send_message":
			$display_dispatch_message = "off";
			if (isset ($_GET['display_dispatch-message'])) {
				$display_dispatch_message = $_GET['display_dispatch-message'];
			}
			$url_str = "\"situation.php?screen_id=\" + new_infos_array['screen']['screen_id']";
			if ($display_dispatch_message == "on") {
				$url_str = "\"ticket_report.php?function=dispatch_text&ticket_id=" . $ticket_id . "&back=situation\"";
			}
		?>
				<script>
					var new_infos_array = [];

					function do_send_api_message() {
						var error_message = "";
						if ($("#frm_text").val() == "") {
							error_message += "<?php print get_text("Message text required");?><br>";
						}
						if (!(($("#select_all").prop("checked")) || ($("#select_all").prop("indeterminate")) || ($("#additional_receiver_0_checkbox_0").prop("checked")))) {
							error_message += "<?php print get_text("No receiver available");?><br>";
						}
						if (error_message != "") {
							show_infobox("<?php print get_text("Please correct the following and re-submit");?>", error_message);
							return false;
						}
						$("#send_button").prop("disabled", true);
						$("#send_button").html("<?php print get_text("Wait");?>");
						$.post("communication.php", $("#message_form").serialize(), function(data) {
							var return_array = JSON.parse(data);
							show_top_notice(return_array["appearance"], return_array["message"]);
						})
						.done(function() {
							goto_window(<?php print $url_str;?>);
						})
						.fail(function() {
							show_top_notice("danger", "<?php print get_text("Error");?>");
						});
					}

					function do_cancel(result) {
						if (typeof result == "undefined") {
							show_infobox("<?php print get_text("Confirm do not send?");?>", "", false, do_cancel);
						} else {
							if (result == true) {
								goto_window(<?php print $url_str;?>);
							}
						}
					}

					function load_content() {
						$.get("communication.php?function=table_left_send_message&message_group=<?php print $message_group;?>&" +
							"targets_ids=<?php print $targets_ids;?>&target_api_log_id=<?php print $target_api_log_id;?>&ticket_id=<?php print urlencode($ticket_id);?>", function(data) {
							$("#table_left").html(data);
						});
						$.get("communication.php?function=table_right_send_message&message_group=<?php print $message_group;?>&" +
							"targets_ids=<?php print $targets_ids;?>&target_api_log_id=<?php print $target_api_log_id;?>&ticket_id=<?php print urlencode($ticket_id);?>", function(data) {
							$("#table_right").html(data);
						});
					}

					$(document).ready(function() {
						load_content();
						set_window_present("communication_send");
						<?php show_prevent_browser_back_button();?>
						window.addEventListener("message", function(event) {
							if (event.origin != window.location.origin) return;
							new_infos_array = JSON.parse(event.data);
						});
					});

				</script>
			</head>
			<body onload="check_frames();">
				<script type="text/javascript" src="./js/wz_tooltip.js"></script>
				<div class="container-fluid" id="main_container">
					<form id="message_form" name="message_form">
						<input type="hidden" name="function" value="update_send_message">
						<input type="hidden" name="display_dispatch-message" value="<?php print $display_dispatch_message;?>">
						<div class="row infostring">
							<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
								<?php print get_text("Send message") . " - "  . get_variable("page_caption");?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-1">
								<div class="container-fluid" style="position: fixed;">
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" style="min-width: 60px;" onclick="do_cancel();" tabindex=10><?php print get_text("Cancel");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-12">
											<button type="button" class="btn btn-xs btn-default" style="min-width: 60px;" onclick="load_content();" tabindex=9><?php print get_text("Reset");?></button>
										</div>
									</div>
									<div class="row" style="margin-top: 10px;">
										<div class="col-md-12">
											<button id="send_button" type="button" class="btn btn-xs btn-default" style="min-width: 60px;" onclick="do_send_api_message();" tabindex=8>
												<?php print get_text("Send");?>
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="panel panel-default" id="table_left" style="padding: 0px;"></div>
							</div>
							<div class="col-md-5">
								<div id="table_right" style="padding: 0px;"></div>
							</div>
							<div class="col-md-1"></div>
						</div>
					</form>
				</div>
			</body>
		</html>
		<?php
			break;
		default:
			set_session_expire_time("on");
		?>
			<script>
				var new_infos_array = [];
				var select_ticket_api_log_id = 0;

				function load_content() {
					$.get("communication.php?function=table_left_communication", function(data) {
						$("#table_left").html(data);
					});
					$.get("communication.php?function=table_right_communication", function(data) {
						$("#table_right").html(data);
					});
					$(document).ready(function() {
						$('.dropdown-toggle').dropdown();
					});
				}

				function edit_ticket(ticket_id, unit_id) {
					if (ticket_id == 0) {
						send_data(select_ticket_api_log_id, "api_log_new_ticket", 0, unit_id);
					} else {
						send_data(select_ticket_api_log_id, "api_log_new_dispatch", ticket_id, unit_id);
					}
				}

				function send_data(api_log_id, api_log_action, ticket_id, unit_id) {
					switch (api_log_action) {
						case "api_log_select_ticket":
							select_ticket_api_log_id = api_log_id;
							$.get("./communication.php?function=infobox_large_ticket_select&unit_id=" + unit_id, function(data) {
								show_dispatch_infobox("<?php print get_text("Dispatch to ticket - Select ticket");?>", data);
							})
							.done(function() {
								$.get("communication.php?function=update_communication&api_log_id=" + api_log_id + "&api_log_action=api_log_voice_promt").done(function(data) {
									load_content();
								});
							});
							break;
						case "api_log_new_ticket":
							$.get("ticket_add.php?function=get_reserved_ticket").done(function(data) {
								ticket_id = data.trim();
							})
							.done(function() {
								if (select_ticket_api_log_id == 0) {
									select_ticket_api_log_id = api_log_id;
									$.get("communication.php?function=update_communication&api_log_id=" + api_log_id + "&api_log_action=api_log_voice_promt").done(function(data) {
									});
									unit_id = 0;
								}
								$.post("ticket_add.php", {
									function: "insert",
									auto_ticket: true,
									ticket_id: ticket_id,
									api_log_id: select_ticket_api_log_id,
								}, function() {})
								.done(function() {
									if (unit_id != 0) {
										$.post("dispatch.php", {
											function: "insert",
											frm_ticket_id: ticket_id,
											'unit_id[]': [unit_id],
											api_log_id: select_ticket_api_log_id
										}, function() {})
										.done(function() {
											show_top_notice("success", "<?php print get_text("Saved");?>");
											var last_call = true;
											if (last_call) {
												goto_window("ticket_edit.php?ticket_id=" + ticket_id + "&unit_id=" + unit_id);
											} else {
												load_content();
												hide_infobox_large(false);
											}
										});
									} else {
										show_top_notice("success", "<?php print get_text("Saved");?>");
										goto_window("ticket_edit.php?ticket_id=" + ticket_id);
									}
								});
							});
							break;
						case "api_log_update_call_progression":
							$.get("communication.php?function=update_communication&api_log_id=" + api_log_id + "&api_log_action=api_log_update_call_progression")
								.done(function(data) {
								var return_array = JSON.parse(data);
								if (return_array["call_progression"].valueOf() != "") {
									$.post("set_data.php", {
										function: "call_progression",
										assign_id: return_array["oldest_assign_id"],
										frm_callprogression: return_array["call_progression"],
										call_progression_datetime: return_array["call_progression_datetime"]
									}, function() {})
									.done(function(data) {
										show_top_notice(return_array["appearance"], return_array["message"]);
										var last_call = false;
										if (last_call) {
											goto_window("ticket_edit.php?ticket_id=" + ticket_id + "&unit_id=" + unit_id);
										}
									})
									.fail(function() {
										show_top_notice("danger", "<?php print get_text("Error");?>");
									});
								} else {
									show_top_notice(return_array["appearance"], return_array["message"]);
								}
							})
							.fail(function() {
								show_top_notice("danger", "<?php print get_text("Error");?>");
							});
							break;
						case "api_log_new_dispatch":
							$.post("dispatch.php", {
								function: "insert",
								frm_ticket_id: ticket_id,
								'unit_id[]': [unit_id],
								api_log_id: select_ticket_api_log_id
							}, function() {})
							.done(function() {
								show_top_notice("success", "<?php print get_text("Saved");?>");
								var last_call = false;
								if (last_call) {
									goto_window("ticket_edit.php?ticket_id=" + ticket_id + "&unit_id=" + unit_id);
								} else {
									load_content();
									hide_infobox_large(false);
								}
							});
							break;
						default:
							$.get("communication.php?function=update_communication&api_log_id=" + api_log_id + "&api_log_action=" + 
								api_log_action + "&ticket_id=" + ticket_id + "&unit_id=" + unit_id).done(function(data) {
								var return_array = JSON.parse(data);
								show_top_notice(return_array["appearance"], return_array["message"]);
								if ((return_array["url"] !== undefined) && (return_array["url"] != null) && (return_array["url"] != "")) {
									goto_window(return_array["url"]);
								} else {
									load_content();
								}
							});
					}
				}

				$(document).ready(function() {
					load_content();
					show_to_top_button("<?php print get_text("To top");?>");
					set_window_present("communication_receive");
					<?php show_prevent_browser_back_button();?>
					window.addEventListener("message", function(event) {
						if (event.origin != window.location.origin) return;
						new_infos_array = JSON.parse(event.data);
						if (new_infos_array['reload_flags']['communication']) {
							load_content();
						}
					});
				});

			</script>
		</head>
		<body onload="check_frames();">
			<script type="text/javascript" src="./js/wz_tooltip.js"></script>
			<div class="container-fluid" id="main_container">
				<div class="row infostring">
					<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;">
						<?php print get_text("Communication") . " - "  . get_variable("page_caption");?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1">
						<div id="button_container" class="container-fluid" style="position: fixed;">
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<button type="button" class="btn btn-xs btn-default" onclick="goto_window('situation.php?screen_id=' + new_infos_array['screen']['screen_id']);"><?php print get_text("Cancel");?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" id="table_left" style="padding: 0px;"></div>
					</div>
					<div class="col-md-5">
						<div class="panel panel-default" id="table_right" style="padding: 0px;"></div>
					</div>
					<div class="col-md-1"></div>
				</div>
			</div>
			<?php show_infobox("large");?>
		</body>
		</html>
		<?php
	}
}
?>	