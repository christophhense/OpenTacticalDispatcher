function check_frames() {
	try {
		if (self.location.href == parent.location.href) {
			window.location.href = "index.php";
		}
	} catch (e) {
	}
}

setInterval(blink,0.7 * 1000);
function blink() {
	var el = document.getElementsByClassName("textblink");
	for(var i = 0; i< el.length; i++) {
		if (el[i].style.visibility == "hidden") {
			el[i].style.visibility = "visible";
		} else {
			el[i].style.visibility = "hidden";
		}
	}
}

setInterval(call_progression_timer, 1000);
function call_progression_timer() {
	var el = document.getElementsByClassName("callprogression_timer");
	var elapsed = 0;
	var count_mins = 0;
	var count_secs = 0;
	for (var i = 0; i < el.length; i++) {
		elapsed = el[i].getAttribute("data-counter");
		if ((elapsed < 5940) && (elapsed >= 0)) {
			el[i].setAttribute("data-counter", ++elapsed);
			count_mins = Math.floor(elapsed / 60);
			if (count_mins < 10) {
				count_mins = "0" + count_mins.toString();
			}
			count_secs = elapsed % 60;
			if (count_secs < 10) {
				count_secs = "0" + count_secs.toString();
			}
			el[i].innerHTML = "&nbsp;" + el[i].getAttribute("data-progression") + "&nbsp;" + count_mins + ":" + 
			count_secs + "</div>";
		} else {
			if (elapsed < 5942) {
				el[i].innerHTML = "&nbsp;" + el[i].getAttribute("data-progression") + 
					"&nbsp;<span style=\'text-decoration: line-through;\'>" + 
					"99:00</span>";
				el[i].setAttribute("data-counter", ++elapsed);
			}
		}
	}
}

String.prototype.trim = function () {
	return this.replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
};

function set_regions_control(groups) {
	var reg_control = "1";
	var regions_showing = groups;
	if (regions_showing) {
		if (reg_control == 0) {
			$("#top_reg_box").css("display", "none");
			$("#regions_outer").css("display", "block");
		} else {
			$("#top_reg_box").css("display", "block");
			$("#regions_outer").css("display", "none");	
		}
	}
}

function get_cursor_position(ctrl) {
	var cursort_position = 0;	// IE Support
	if (document.selection) {
		ctrl.focus ();
		var Sel = document.selection.createRange ();
		Sel.moveStart ("character", -ctrl.value.length);
		cursort_position = Sel.text.length;
	}
	// Firefox support
	else if (ctrl.selectionStart || ctrl.selectionStart == "0")
		cursort_position = ctrl.selectionStart;
	return (cursort_position);
}

function set_cursor_position(ctrl, pos) {
	if (ctrl.setSelectionRange) {
		ctrl.focus();
		ctrl.setSelectionRange(pos,pos);
	} else {
		if (ctrl.createTextRange) {
			var range = ctrl.createTextRange();
			range.collapse(true);
			range.moveEnd("character", pos);
			range.moveStart("character", pos);
			range.select();
		}
	}
}

function set_textblock(text, field_name) {
	var separator_whitespace = "";
	if ((field_name.value.trim().length > 0) && (text.trim().length > 0)) {
		separator_whitespace = "  ";
	}
	field_name.value += separator_whitespace + text;
	field_name.focus();	
	var textlength = field_name.value.length;
	set_cursor_position(field_name, textlength);
}

function do_severity_protocol(index) {
	if (($("#incident_type").val() == 0) && (index != 0)) {
		$("#frm_severity").val(severities[index]).change();
		switch (severities[index]) {
		case 2:
			$("#frm_severity").css({"background-color": "#FF0000"});	//Red
			break;
		case 1:
			$("#frm_severity").css({"background-color": "#008000"});	//Green
			break;
		default:
			$("#frm_severity").css({"background-color": "#0000FF"});	//Blue
		}
	}
	if (protocols[index]) {
		$("#incident_type_protocol").html(protocols[index]);
	} else {
		$("#incident_type_protocol").html("");
	}
}
function do_facility_to_ticket_location(index) {
	if (index > 0) {
		$("#frm_location").val(facility_adress[index]);
		$("#frm_location").prop("readonly", true);
		$("#frm_lat").val(fac_lat[index]);
		$("#frm_lng").val(fac_lng[index]);
	} else {
		$("#frm_location").val("");
		$("#frm_location").prop("readonly", false);
		$("#frm_lat").val("");
		$("#frm_lng").val("");
		$("#frm_location").focus();
	}
	return;
}

function set_reported_by_infos(select, text, phone) {
	$("#frm_contact").val(text);
	switch (select) {
	case "ticket_edit_form":
		$("#frm_phone").val(phone);	//overwrite_with_content_or_blank
		break;
	case "none":
		if (phone.trim().length !== 0) $("#frm_phone").val(phone);	//overwrite_only_with_content
		break;
	case "ticket_add_form":
	default:
		var phone_form_content = $("#frm_phone").val();
		if ((phone_form_content.trim().length !== 0) && (phone.trim().length !== 0)) phone_form_content = phone_form_content + ', ';
		$("#frm_phone").val(phone_form_content + phone);	//append_content_if_exist
	}
}

function do_unlock_readonly(form_id) {
	$("#lock_" + form_id).css("visibility", "hidden");
	$("#" + form_id).prop("readonly", false);
	$("#" + form_id).focus();
}

function do_lock_readonly(form_id) {
	$("#lock_" + form_id).css("visibility", "visible");
	$("#" + form_id).prop("readonly", true);
}

function do_unlock_disabled(form_id) {
	$("#lock_" + form_id).css("visibility", "hidden");
	$("#" + form_id).prop("disabled", false);
	$("#" + form_id).focus();
}

function do_lock_disabled(form_id) {
	$("#lock_" + form_id).css("visibility", "visible");
	$("#" + form_id).prop("disabled", true);
}

var callback_function = "";
var current_unit_id = 0;
function show_assigns(unit_id) {
	current_unit_id = unit_id;
	UnTip();
	show_kontext_menue();
	var info_text_head = "";
	var info_text_body = "";
	var info_input = "";
	$.get("assign.php?function=multiple&unit_id=" + unit_id, function() {
	})
	.done(function(data) {
		var get_infos_array = JSON.parse(data);
		var count_assigns = get_infos_array["assigns"].length;
		info_text_body += "<div class='panel panel-default' style='padding: 0px; background-color: " + get_infos_array["additional_infos"]["background-color"] + ";'>";
		info_text_body += "<table class='table table-striped table-condensed' style='table-layout: fixed;'>";
		for (var index = 0; index < count_assigns; ++index) {
			info_text_body += get_infos_array["assigns"][index];
		}
		info_text_body += "</table>";
		info_text_body += "</div>";
		info_text_head = get_infos_array["additional_infos"]["head_text"];
		if ((typeof info_text_head != "undefined") && (info_text_head != "")) {
			$("#infobox_head_large").html(info_text_head);
			if ((typeof info_text_body != "undefined") && (info_text_body != "")) {
				$("#infobox_body_large").html(info_text_body);
				$("#infobox_body_large").css("display", "inline-block");
			}
			if ((typeof info_input != "undefined") && (info_input == "form")) {
				$("#infobox_input_large").css("display", "block");
				setTimeout(function() {
					$("#infobox_input_large").focus();
				}, 500);
			}
			$("#cancel_button_large").css("display", "inline-block");
			$("#close_button_large").css("display", "none");
			$("#confirm_button_large").css("display", "none");
			$("#infobox_large").modal("show");
			show_kontext_menue();
		}
	})
	.fail(function() {
		alert("error");
	});
}

var import_type = "";
var import_file = "";
function install_default_csv_file(result) {
	if (result) {
		$.post("import.php", "function=" + import_type + "&filename=" + import_file)
		.done(function(data) {
			var get_infos_array = JSON.parse(data);
			window.location.href = window.location.href.replace( /[\?#].*|$/, "/configuration.php?top_notice=" + get_infos_array['top_notice_str'] + "&top_notice_logstr=" + get_infos_array['top_notice_log_str']);
		})
		.fail(function() {alert("error");});
	} else {
		import_file = "";
	}
}

function show_default_import_infobox(type, file) {
	UnTip();
	switch (type) {
	case "default-incident-types":
		import_type = type;
		import_file = file;
		callback_function = install_default_csv_file;
		$("#infobox_head_large").html($("#default-incident-types_head").html());
		$("#infobox_body_large").html($("#default-incident-types_content").html());
		$("#cancel_button_large").css("display", "inline-block");
		$("#close_button_large").css("display", "none");
		$("#confirm_button_large").css("display", "inline-block");
		break;
	case "default-textblocks":
		import_type = type;
		import_file = file;
		callback_function = install_default_csv_file;
		$("#infobox_head_large").html($("#default-textblocks_head").html());
		$("#infobox_body_large").html($("#default-textblocks_content").html());
		$("#cancel_button_large").css("display", "inline-block");
		$("#close_button_large").css("display", "none");
		$("#confirm_button_large").css("display", "inline-block");
		break;
	case "default-incident-types_preview":
		$("#infobox_head_large").html($("#default-incident-types_head").html());
		$("#infobox_body_large").html($("#default-incident-types_content").html());
		$("#cancel_button_large").css("display", "none");
		$("#close_button_large").css("display", "inline-block");
		$("#confirm_button_large").css("display", "none");
		break;
	case "default-textblocks_preview":
		$("#infobox_head_large").html($("#default-textblocks_head").html());
		$("#infobox_body_large").html($("#default-textblocks_content").html());
		$("#cancel_button_large").css("display", "none");
		$("#close_button_large").css("display", "inline-block");
		$("#confirm_button_large").css("display", "none");
		break;
	}
	$("#infobox_body_large").css("display", "inline-block");
	$("#infobox_large").modal("show");
}

function show_dispatch_infobox(head, data) {
	UnTip();
	$("#infobox_head_large").html(head);
	$("#infobox_body_large").html(data);
	$("#cancel_button_large").css("display", "inline-block");
	$("#close_button_large").css("display", "none");
	$("#confirm_button_large").css("display", "none");
	$("#infobox_body_large").css("display", "inline-block");
	$("#infobox_large").modal("show");
}

function hide_infobox_large(result) {
	current_unit_id = 0;
	$("#infobox_large").modal("hide");
	$("#infobox_head_large").html("");
	$("#infobox_body_large").html("");
	$("#infobox_body_large").css("display", "none");
	$("#cancel_button_large").css("display", "none");
	$("#close_button_large").css("display", "inline-block");
	$("#confirm_button_large").css("display", "none");
	clearTimeout(infobox_showtime);
	if ((typeof result != "undefined") && (callback_function != "")) {
		if ((result == true) && ($("#infobox_input_large").val() != "")) {
			result = $("#infobox_input_large").val();
			$("#infobox_input_large").css("display", "none");
		}
		$("#infobox_input_large").val("");
		callback_function(result);
	}
}

var infobox_showtime;
function show_infobox(info_text_head, info_text_body, info_input, callback) {
	timeout = 0;
	timeout_min = 1100;
	timeout_factor = 30;
	if ((typeof info_text_head != "undefined") && (info_text_head != "")) {
		timeout += info_text_head.length  * timeout_factor;
		$("#infobox_head").html(info_text_head);
		if ((typeof info_text_body != "undefined") && (info_text_body != "")) {
			timeout += info_text_body.length  * timeout_factor;
			$("#infobox_body").html(info_text_body);
			$("#infobox_body").css("display", "inline-block");
		}
		if ((typeof info_input != "undefined") && (info_input == "form")) {
			$("#infobox_input").css("display", "block");
			setTimeout(function() {
				$("#infobox_input").focus();
			}, 500);
		}
		if (typeof callback != "undefined") {
			$("#cancel_button").css("display", "inline-block");
			$("#close_button").css("display", "none");
			if ((typeof info_input != "undefined") && (info_input == "select")) {
				$("#confirm_button").css("display", "none");
			} else {
				$("#confirm_button").css("display", "inline-block");
			}
			callback_function = callback;
			timeout = 0;
		}
		$("#infobox").modal("show");
		if (timeout > 0) {
			if (timeout < timeout_min) {
				timeout = timeout_min;
			}
			infobox_showtime = setTimeout(function() {$("#infobox").modal("hide");}, timeout);
		}
	}
}

function hide_infobox(result) {
	$("#infobox").modal("hide");
	$("#infobox_head").html("");
	$("#infobox_body").html("");
	$("#infobox_body").css("display", "none");
	$("#cancel_button").css("display", "none");
	$("#close_button").css("display", "inline-block");
	$("#confirm_button").css("display", "none");
	clearTimeout(infobox_showtime);
	if ((typeof result != "undefined") && (callback_function != "")) {
		if ((result == true) && ($("#infobox_input").val() != "")) {
			result = $("#infobox_input").val();
			$("#infobox_input").css("display", "none");
		}
		$("#infobox_input").val("");
		callback_function(result);
	}
}

function show_to_top_button(caption) {
	var back_to_top_button = ["<div class=\"row\" style=\"margin-top: 10px;\"><div class=\"col-md-12\">" + 
		"<button type=\"button\" class=\"btn btn-xs btn-default back-to-top\" href=\"#top\">" + caption + 
		"</button></div></div>"].join("");
		$("#button_container").append(back_to_top_button)
	$(".back-to-top").hide();

	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$(".back-to-top").fadeIn();
			} else {
				$(".back-to-top").fadeOut();
			}
		});

		$(".back-to-top").click(function () {
			$("body,html").animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
}

function get_reporting_channel(unit_id, type) {
	$.get("./get_data.php?function=reporting_channel&unit_id=" + unit_id, function(data) {
		if (data) {
			var get_reporting_channel_array = JSON.parse(data);
			switch (type) {
			case "smsg_id":
				if (get_reporting_channel_array['smsg_id'].valueOf() != "") {
					$("#frm_smsg_id").html(get_reporting_channel_array['smsg_id']);
				} else {
					$("#frm_smsg_id").html("");
				}
				break;
			case "phone":
				if (get_reporting_channel_array['phone'].valueOf() != "") {
					$("#frm_phone").val(get_reporting_channel_array['phone']);
				} else {
					$("#frm_phone").val("");
				}
				break;
			case "email":
				if (get_reporting_channel_array['email'].valueOf() != "") {
					$("#frm_unit_email").val(get_reporting_channel_array['email']);
				} else {
					$("#frm_unit_email").val("");
				}
				break;
			default:
				if (get_reporting_channel_array['smsg_id'].valueOf() != "") {
					$("#frm_smsg_id").html(get_reporting_channel_array['smsg_id']);
				} else {
					$("#frm_smsg_id").html("");
				}
				if (get_reporting_channel_array['phone'].valueOf() != "") {
					$("#frm_phone").val(get_reporting_channel_array['phone']);
				} else {
					$("#frm_phone").val("");
				}
				if (get_reporting_channel_array['email'].valueOf() != "") {
					$("#frm_unit_email").val(get_reporting_channel_array['email']);
				} else {
					$("#frm_unit_email").val("");
				}
			}
		}
	});
}

function split_api_receiver_str(receiver_str) {
	var result_array = ["", ""];
	if ((typeof (receiver_str) != "undefined") && (receiver_str != null)) {
		var receiver_array = receiver_str.split(":");
		var prefix_length = receiver_array[0].length;
		result_array[0] = receiver_array[0];
		result_array[1] = receiver_str.substr(prefix_length + 1);
	}
	return result_array;
}

function wait(ms) {
	var start = new Date().getTime();
	var end = start;
	while(end < start + ms) {
		end = new Date().getTime();
	}
}

function send_post_message(changes_data) {
	window.parent.navigationbar.postMessage(changes_data, window.location.origin);
}

function goto_window(url) {
	var changes_data = '{"type":"script","item":"main","action":"' + url + '"}';
	send_post_message(changes_data);
}

function show_top_notice(appearance, message) {
	var changes_data = '{"type":"message","item":"' + appearance + '","action":"' + message + '"}';
	send_post_message(changes_data);
}

function save_parked_form_data(form, action, data) {
	var changes_data = {"type":"set_parked_form_data","item":form,"action":action};
	if (form == "ticket_add_form_data") {
		changes_data.ticket_add_form_data = data;
	}
	switch (form) {
	case "ticket_add_form_data":
		changes_data.ticket_add_form_data = data;
		break;
	case "ticket_close_form_data":
		changes_data.ticket_close_form_data = data;
		break;
	case "ticket_close_timestamp":
		changes_data.datetime = data;
		break;
	case "action_form_data":
		changes_data.action_form_data = data;
		break;
	case "log_report_form_data":
		changes_data.log_report_form_data = data;
		break;
	default:
	}
	changes_data = JSON.stringify(changes_data);
	send_post_message(changes_data);
}

function set_window_present(current_script) {
	var changes_data = '{"type":"current_script","item":"script","action":"' + current_script + '"}';
	send_post_message(changes_data);
}

function do_api_connection_test(periodic, done_message) {
	var periodic_parameter = "&periodic=false";
	if (periodic) {
		periodic_parameter = "&periodic=true";
	}
		$.get("set_data.php?function=api_connection_test" + periodic_parameter, function(data) {
		}) 
		.done(function() {
			if (!periodic) {
				var changes_data ='{"type":"message","item":"info","action":"' + done_message + '"}';
				send_post_message(changes_data);
			}
		})
		.fail(function() {
			alert("error");
	});
}

function do_send_message(select, targets_ids, ticket_id) {
	var parameters = "";
	switch (select) {
	case "unit_all":
		parameters = "function=send_message&message_group=unit_all";
		break;
	case "unit_service":
		parameters = "function=send_message&message_group=unit_service&targets_ids=" + targets_ids;
		break;
	case "unit_ticket":
		parameters = "function=send_message&message_group=unit_ticket&targets_ids=" + targets_ids + "&ticket_id=" + ticket_id;
		break;
	case "unit_tickets":
		parameters = "function=send_message&message_group=unit_tickets";
		break;
	case "unit":
		parameters = "function=send_message&message_group=unit&targets_ids=" + targets_ids;
		break;
	case "facility_all":
		parameters = "function=send_message&message_group=facility_all";
		break;
	case "facility":
		parameters = "function=send_message&message_group=facility&targets_ids=" + targets_ids;
		break;
	case "user_all":
		parameters = "function=send_message&message_group=user_all";
		break;
	case "user":
		parameters = "function=send_message&message_group=user&targets_ids=" + targets_ids;
		break;
	default:
	}
	var changes_data ={"type":"script","item":"main","action":"communication.php?" + parameters};
	changes_data = JSON.stringify(changes_data);
	send_post_message(changes_data);
}

function prevent_browser_back_button() {
	/*
	if (window.history && window.history.pushState) {
		window.history.pushState("forward", null, "./#forward");
		$(window).on("popstate", function(event) {
			console.log("popstate event");
			if ((typeof (document.location) != "undefined") && (document.location.toString().charAt(0).charAt(document.location.toString().charAt(0).length - 1) != "#")) {
				goto_window("situation.php?screen_id=" + screen_id_main);
			}
		});
	}
	*/
}

function activate_show_hide_password() {
	$("#frm_passwd").on("focus", function() {
		if ($("#frm_passwd").val() == "!!!!!!!!") {
			$("#frm_passwd").val("");
		}
	});

	$("#frm_passwd_confirm").on("focus", function() {
		if ($("#frm_passwd_confirm").val() == "!!!!!!!!") {
			$("#frm_passwd_confirm").val("");
		}
	});

	$(".pw_show").on("click", function() {
		if ($(".pw_show").hasClass("glyphicon-eye-open")) {
			if ($("#frm_passwd").val() == "!!!!!!!!") {
				$("#frm_passwd").val("");
			}
			if ($("#frm_passwd_confirm").val() == "!!!!!!!!") {
				$("#frm_passwd_confirm").val("");
			}
			$("#frm_passwd").attr("type", "text");
			$("#frm_passwd_confirm").attr("type", "text");
			$(".pw_show").removeClass("glyphicon-eye-open");
			$(".pw_show").addClass("glyphicon-eye-close");
		} else {
			$("#frm_passwd").attr("type", "password");
			$("#frm_passwd_confirm").attr("type", "password");
			$(".pw_show").removeClass("glyphicon-eye-close");
			$(".pw_show").addClass("glyphicon-eye-open");
		}
	});
}