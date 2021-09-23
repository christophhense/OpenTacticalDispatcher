<?php
require_once ("./incs/functions.inc.php");
require_once ("./incs/configuration.inc.php");

if ((empty ($_POST)) && (empty ($_GET))) {	//	checks to make sure script is not run directly.
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$file = "index.php";
	header("Location: http://" . $host . $uri . "/" . $file);
	exit ();
}

$function = "";
if (isset ($_GET['function'])) {
	$function = $_GET['function'];
} else {
	if (isset ($_POST['function'])) {
		$function = $_POST['function'];
	}
}
if (count_units_and_facilities_and_users() != 0) {
	$function = "warning-page";
}
$default_incident_types_file = "default_incident_types." . get_variable("_locale") . ".csv";
$default_incident_types = get_default_incident_types(get_current_path("sql/" . $default_incident_types_file));
$default_incident_types_display_str = " style='display: none;'";
if ($default_incident_types != "") {
	$default_incident_types_display_str = " style='display: inline;'";
}
$default_textblocks_file = "default_textblocks." . get_variable("_locale") . ".csv";
$default_textblocks = get_default_textblocks(get_current_path("sql/" . $default_textblocks_file));
$default_textblocks_display_str = " style='display: none;'";
if ($default_textblocks != "") {
	$default_textblocks_display_str = " style='display: inline;'";
}
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
		<script src="./js/bootstrap-checkbox.min.js" type="text/javascript"></script>
		<script src="./js/jscolor/jscolor.js"></script>
		<script src="./js/functions.js" type="text/javascript"></script>
		<script>

			var count_unit_element = 1;
			var count_unit_type_element = 1;
			var count_facility_element = 1;

			function new_line_unit(handle, name) {
				if (typeof handle == "undefined") {
					handle = "";
				}
				if (typeof name == "undefined") {
					name = "";
				}
				count_unit_element++;
				var div_unit = document.createElement("tr");
				div_unit.id = "unit" + count_unit_element;
				var markup = "<tr class='form-group'>";
				markup += "<td><input class='form-control' name='frm_unit_handle[]' type='text' size='24' maxlength='24' value='" + handle + "'></td>";
				markup += "<td><input class='form-control' name='frm_unit_name[]' type='text' size='50' maxlength='50' value='" + name + "'></td>";
				markup += '<td style="text-align: center;"><input type="checkbox" onclick="delete_line_unit(\'unit' + count_unit_element + '\')"></td>';
				markup += "</tr>";
				div_unit.innerHTML = markup;
				document.getElementById("formline_unit").appendChild(div_unit);
				jscolor.init();
			}

			function delete_line_unit(element_id) {
				var element = document.getElementById(element_id);
				var parent_element = document.getElementById("formline_unit");
				parent_element.removeChild(element);
			}

			function new_line_unit_type(name, descr, bgcolor, txtcolor) {
				if (typeof name == "undefined") {
					name = "";
				}
				if (typeof descr == "undefined") {
					descr = "";	
				}
				if (typeof bgcolor == "undefined") {
					bgcolor = 0;
				}
				if (typeof txtcolor == "undefined") {
					btxtcolor = 1000;
				}
				sel_str = new Array("", "", "", "", "", "", "", "", "");
				sel_str[bgcolor] = " SELECTED";
				count_unit_type_element++;
				var div_unit_type = document.createElement('tr');
				div_unit_type.id = "unit_type" + count_unit_type_element;
				var markup = "<tr class='form-group'>";
				markup += "<td><input class='form-control' name='frm_unit_type_name[]' type='text' size='16' maxlength='16' value='" + name + "'></td>";
				markup += "<td><input class='form-control' name='frm_unit_type_description[]' value='" + descr + "'></input></td>";
				markup += "<td><input class='form-control color' name='frm_unit_type_background_color[]' type='text' size=6 value='" + bgcolor + "'></td>";
				markup += "<td><input class='form-control color' name='frm_unit_type_text_color[]' type='text' size=6 value='" + txtcolor + "'></td>";
				markup += '<td style="text-align: center;"><input type="checkbox" onclick="delete_line_unit_type(\'unit_type' + count_unit_type_element + '\')"></td>';
				markup += "</tr>";	
				div_unit_type.innerHTML = markup;
				document.getElementById("formline_unit_type").appendChild(div_unit_type);
				jscolor.init();
			}

			function delete_line_unit_type(element_id) {
				var element = document.getElementById(element_id);
				var parent_element = document.getElementById("formline_unit_type");
				parent_element.removeChild(element);
			}

			function new_line_facility(handle, name, street, city) {
				if (typeof handle == "undefined") {
					handle = "";
				}
				if (typeof name == "undefined") {
					name = "";
				}
				if (typeof street == "undefined") {
					street = "";
				}
				if (typeof city == "undefined") {
					city = "";
				}
				count_facility_element++;
				var div_facility = document.createElement('tr');
				div_facility.id = "facility" + count_facility_element;
				var markup = "<tr class='form-group'>";
				markup += "<td><input class='form-control' name='frm_facility_handle[]' type='text' size='24' maxlength='24' value='" + handle + "'></td>";
				markup += "<td><input class='form-control' name='frm_facility_name[]' type='text' size='50' maxlength='50' value='" + name + "'></td>";
				markup += "<td><textarea class='form-control' name='frm_facility_street[]'>" + street + "</textarea></td>";
				markup += "<td><input class='form-control' name='frm_facility_city[]' type='text' size='50' maxlength='50' value='" + city + "'></td>";
				markup += '<td style="text-align: center;"><input type="checkbox" onclick="delete_line_facility(\'facility' + count_facility_element + '\')"></td>';
				markup += "</tr>";	
				div_facility.innerHTML = markup;
				document.getElementById("formline_facility").appendChild(div_facility);
				jscolor.init();
			}

			function delete_line_facility(element_id) {
				var element = document.getElementById(element_id);
				var parent_element = document.getElementById('formline_facility');
				parent_element.removeChild(element);
			}

			function page_changer(next_page_div_id) {
				var helptext = "";
				var titlestring = "";
				switch (next_page_div_id) {
				case "intro":
					helptext = "<?php print get_help_text("help1", true);?>";
					titlestring = "<?php print get_text("1 of 6 - Introduction");?>";
					$("#back_button").css("display", "none");
					$("#next_page_button").css("display", "inline");
					$("#next_page_button").click(function () { page_changer("titles"); });
					break;
			    case "titles":
					helptext = "<?php print get_help_text("help2", true);?>";
					titlestring = "<?php print get_text("2 of 6 - Titles");?>";
					$("#back_button").css("display", "inline");
					$("#back_button").click(function () { page_changer("intro"); });
					$("#next_page_button").click(function () { page_changer("incident_types_and_textblocks"); });
					if ($("#helptext_box").css("display") == "none") {
						$("#helptext_box").css("display", "block");
					}
					break;
				case "incident_types_and_textblocks":
					helptext = "<?php print get_help_text("help3", true);?>";
					titlestring = "<?php print get_text("3 of 6 - Incident-types and textblocks");?>";
					$("#back_button").click(function () { page_changer("titles"); });
					$("#next_page_button").click(function () { page_changer("units"); });
					break;
				case "units":
					helptext = "<?php print get_help_text("help4", true);?>";
					titlestring = "<?php print get_text("4 of 6 - Units");?>";
					$("#back_button").click(function () { page_changer("incident_types_and_textblocks"); });
					$("#next_page_button").click(function () { page_changer("unit_types"); });
					break;
				case "unit_types":
					helptext = "<?php print get_help_text("help5", true);?>";
					titlestring = "<?php print get_text("5 of 6 - Unit types");?>";
					$("#back_button").click(function () { page_changer("units"); });
					$("#next_page_button").html("<?php print get_text("Next Page")?>");
					$("#next_page_button").off();
					$("#next_page_button").click(function () { page_changer("facilities"); });
					break;
				case "facilities":
					helptext = "<?php print get_help_text("help6", true);?>";
					titlestring = "<?php print get_text("6 of 6 - Facilities");?>";
					$("#back_button").click(function () { page_changer("unit_types"); });
					$("#next_page_button").html("<?php print get_text("Save")?>");
					$("#next_page_button").click(function () { insert_defaults(); });
					break;
				}
				if ($("#helptext_box")) {
					$("#helptext_box").html(helptext);
				}
				if ($("#intro")) {
					$("#intro").css("display", "none");
				}
				if ($("#titles")) {
					$("#titles").css("display", "none");
				}
				if ($("#incident_types_and_textblocks")) {
					$("#incident_types_and_textblocks").css("display", "none");
				}
				if ($("#units")) {
					$("#units").css("display", "none");
				}
				if ($("#unit_types")) {
					$("#unit_types").css("display", "none");
				}
				if ($("#facilities")) {
					$("#facilities").css("display", "none");
				}	
				if ($("#" + next_page_div_id)||true) {
		 			$("#" + next_page_div_id).css("display", "block");
					if (next_page_div_id == "intro") {
						$("#helptext_box").css("display", "none");
					}
				}
				$("#infostring_middle").html(titlestring);
			}

			function insert_incident_types () {
				if ($("#install_default-incident-types").prop("checked") == true) {
					$.post("import.php", "function=default-incident-types&filename=<?php print $default_incident_types_file;?>")
					.done(function() {return;})
					.fail(function() {alert("error");});
					$("#frm_install_default-incident-types").val("yes");
				} else {
					return;
				}
			}

			function insert_textblocks () {
				if ($("#install_default-textblocks").prop("checked") == true) {
					$.post("import.php", "function=default-textblocks&filename=<?php print $default_textblocks_file;?>")
					.done(function() {return;})
					.fail(function() {alert("error");});
					$("#frm_install_default-textblocks").val("yes");
				} else {
					return;
				}
			}
			function insert_defaults() {
				insert_incident_types ();
				insert_textblocks ();
				document.forms['wizard_form'].submit();
			}

			$(document).ready(function() {
				$("#install_default-incident-types").checkboxpicker();
				$("#install_default-textblocks").checkboxpicker();
			});

		</script>
	</head>
	<?php

function do_setting($name, $value) {

	$query = "SELECT * " .
		"FROM `settings` " .
		"WHERE `name` = '" . $name . "' " .
		"LIMIT 1;";

	$result = db_query($query, __FILE__, __LINE__);
	if (db_affected_rows($result) != 0) {

		$query = "UPDATE `settings` " .
			"SET `value` = '" . $value . "' " .
			"WHERE `name` = '" . $name . "';";

		$result = db_query($query, __FILE__, __LINE__);
	}
	unset ($result);
	return true;
}
switch ($function) {
	case "warning-page":
	?>
	<body onload="page_changer('titles');">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<h3><span class="label label-primary">OpenTacticalDispatcher</span></h3>
				</div>
				<div class="col-md-1"></div>
			</div>	
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<?php print get_variable("_version");?>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row" style="margin-top: 100px;">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="alert alert-danger" role="alert" style="text-align: center; padding: 10%;">
						<h4><?php print get_text("Units and / or facilitys have already been created in this installation. The configuration wizard can only be executed after a new installation!");?></h4>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<button class="btn btn-default btn-lg btn-block" onclick="location.href='index.php';"><?php print get_text("Cancel");?></button>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
	<?php
	break;
case "insert":
	$datetime_now = mysql_datetime();
	$from = $_SERVER['REMOTE_ADDR'];
	$user = 1;
	$output_text = "";
	$user_id = 1;
	if ((isset ($_POST['frm_title'])) && ($_POST['frm_title'] != "")) {
		do_setting("title_string", $_POST['frm_title']);
		$output_text .= get_text("Title String Set") . ": " . $_POST['frm_title'] . "<br>";
	}
	if ((isset ($_POST['frm_page_caption'])) && ($_POST['frm_page_caption'] != "")) {
		do_setting("page_caption", $_POST['frm_page_caption']);
		$output_text .= get_text("Page Caption Set") . ": " . $_POST['frm_page_caption'] . "<br>";
	}
	if ((isset ($_POST['frm_install_default-incident-types'])) && ($_POST['frm_install_default-incident-types'] == "yes")) {
		$output_text .= get_text("Incident-types inserted") . ": " . get_text("Default incident types") . "<br>";
	} else {
		$output_text .= get_text("Incident-types inserted") . ": " . get_text("None") . "<br>";
	}
	if ((isset ($_POST['frm_install_default-textblocks'])) && ($_POST['frm_install_default-textblocks'] == "yes")) {
		$output_text .= get_text("Textblocks inserted") . ": " . get_text("Default textblocks") . "<br>";
	} else {
		$output_text .= get_text("Textblocks inserted") . ": " . get_text("None") . "<br>";
	}
	if (isset ($_POST['frm_unit_type_name'])) {
		$i = 0;
		foreach ($_POST['frm_unit_type_name'] as $val) {
			if ($val != "") {
				$unit_type_name = substr($_POST['frm_unit_type_name'][$i], 0, 47);
				$description = "";
				if ($_POST['frm_unit_type_description'][$i] != "") {
					$description = substr($_POST['frm_unit_type_description'][$i], 0, 95);
				}
				$bg_color = "#FFFFFF";
				if ($_POST['frm_unit_type_background_color'][$i] != 99) {
					$bg_color = "#" . $_POST['frm_unit_type_background_color'][$i];
				}
				$text_color = "#000000";
				if ($_POST['frm_unit_type_text_color'][$i] != 99) {
					$text_color = "#" . $_POST['frm_unit_type_text_color'][$i];
				}
				$result = insert_into_unit_types($unit_type_name, $description, $bg_color, $text_color,
					$user, $datetime_now);
				if ($result) {
					$output_text .= get_text("Unit type inserted") . ": " . $val . "<br>";
				}
			}
			$i++;
		}
	}
	if (isset ($_POST['frm_unit_handle'])) {
		$i = 0;
		foreach ($_POST['frm_unit_handle'] as $handle) {
			$name = $_POST['frm_unit_name'][$i];
			if ($handle != "") {
				$new_id = insert_into_units($name, $handle, "", "",
					"", 1, 1, 1,
					1, 0, 0, "",
					"", "", 0, "",
					"0.999999", "0.999999", "", "",
					1, "");
				if ($new_id > 0) {
					insert_into_allocates(1, $GLOBALS['TYPE_UNIT'], $new_id, $user_id, $datetime_now);
					$output_text .= get_text("Unit inserted") . ": " . $handle . "<br>";
				}
			}
			$i++;
		}
	}
	if (isset($_POST['frm_facility_handle'])) {
		$i = 0;
		
		insert_into_facility_types(html_entity_decode(get_text("_example_facility_type_name")), html_entity_decode(get_text("_example_facility_type_description")), "#00FF00", "#000000",
			$user_id, $datetime_now);

		foreach ($_POST['frm_facility_handle'] as $handle) {
			if ($handle != "") {
				$new_id = insert_into_facilities($_POST['frm_facility_name'][$i], $handle, "", "",
					"", $_POST['frm_facility_street'][$i], $_POST['frm_facility_city'][$i], "",
					"", "", 1, 1,
					"", "", "", "",
					"", "", "", "",
					"", "", "0.999999", "0.999999",
					1, "");
				if ($new_id > 0) {
					insert_into_allocates(1, $GLOBALS['TYPE_FACILITY'], $new_id, $user_id, $datetime_now);
					$output_text .= get_text("Facility inserted") . ": " . $handle . "<br>";
				}
			}
			$i++;
		}
	}
	?>
	<body>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<h3><span class="label label-primary">OpenTacticalDispatcher</span></h3>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<?php print get_variable("_version");?>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row" style="margin-top: 100px;">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<div class="panel panel-success">
						<div class="panel-heading" style="padding: 1px; text-align: center;">
							<h4><?php print get_text("Setup Complete");?></h4>
						</div>
						<div class="panel-body">
							<?php print $output_text;?>
						</div>
					</div>
				</div>
				<div class="col-md-4"></div>
			</div>
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-3"></div>
								<div class="col-md-6">
									<button class="btn btn-default btn-lg btn-block" style="align: center;" onclick="location.href='index.php';"><?php print get_text("Go to login");?></button>
								</div>
							<div class="col-md-3"></div>
						</div>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
		</div>
	<?php
	break;
case "first_start":
	show_infobox("large");
	?>
	<body onload="page_changer('intro');">
		<script type="text/javascript" src="./js/wz_tooltip.js"></script>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<h3><span class="label label-primary">OpenTacticalDispatcher</span></h3>
				</div>
				<div class="col-md-1"></div>
			</div>	
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<?php print get_variable("_version");?>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row infostring" style="margin-top: 15px;">
				<div class="col-md-12" id="infostring_middle" style="text-align: center; margin-bottom: 10px;"></div>
			</div>
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-md-1">
					<div class="container-fluid" style="position: fixed;">
						<div class="row">
							<div class="col-md-12">
								<button type="button" class="btn btn-xs btn-default" onclick="window.location.href='index.php';"><?php print get_text("Cancel");?></button>
							</div>
						</div>
						<div class="row" style="">
							<div class="col-md-12">
								<button id="back_button" type="button" class="btn btn-xs btn-default" onClick="" style="display: none; margin-top: 10px;"><?php print get_text("Back");?></button>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12">
								<button id="next_page_button" type="button" class="btn btn-xs btn-default" onClick=""><?php print get_text("Next Page");?></button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-10">	
					<form name="wizard_form" method="post" action="<?php print basename( __FILE__); ?>">
						<input type="hidden" name="function" value="insert">

<!-- First page display - Introduction -->

						<div id="intro">
							<div class="alert alert-info" role="alert" style="padding: 1%; text-align: left; font-size: 14px;">
								<?php print get_help_text("help1", true);?>
							</div>
						</div>

<!-- Second page display - Titles -->

						<div id="titles">
							<div class="panel panel-default">
								<div class="form-group">
									<table class="table table-striped table-condensed" style="width: 100%;">
										<tr>
											<th style="border-top: 0px;">
												<?php print get_text("Head Caption");?>
											</th>
											<th style="border-top: 0px;">
												<?php print get_text("Page Caption");?>
											</th>
										</tr>
										<tr>
											<td>
												<input type="text" class="form-control" name="frm_title" style="width: 100%;" value="<?php print get_variable("title_string");?>">
											</td>
											<td>
												<input type="text" class="form-control" name="frm_page_caption" style="width: 100%;" value="<?php print get_variable("page_caption");?>">
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>

<!-- Third page display - incident types and textblocks-->

						<div id="incident_types_and_textblocks">
							<div class="panel panel-default" style="padding: 0px;">
								<div class="form-group">
									<table class="table table-striped table-condensed" style="width: 100%;">
										<tr>
											<th style="border-top: 0px;">
												<?php print get_text("Install default incident types");?>
											</th>
											<th style="border-top: 0px;">
												<?php print get_text("Install default textblocks");?>
											</th>
										</tr>
										<tr style="height: 44px;">
											<td style="padding-top: 10px;">
												<input type="checkbox" style="display: inline;" id="install_default-incident-types" data-off-label="false" data-on-label="false" data-off-icon-cls="glyphicon-thumbs-down" data-on-icon-cls="glyphicon-thumbs-up" checked>
												<input type="hidden" id="frm_install_default-incident-types" name="frm_install_default-incident-types" value="">	
												&nbsp;&nbsp;&nbsp;
												<button type="button" class="btn btn-default" style="display: inline;" onclick="show_default_import_infobox('default-incident-types_preview', '<?php print $default_incident_types_file;?>')";"<?php print $default_incident_types_display_str;?>>
													<?php print get_text("Preview defaults");?>
												</button>
												<div id="default-incident-types_head" style="display: none;">
													<?php print get_text("Default incident types");?>
												</div>
												<div id="default-incident-types_content" style="display: none;">
													<?php print $default_incident_types;?>
												</div>
											</td>
											<td style="padding-top :10px;">
												<input type="checkbox" style="display: inline;" id="install_default-textblocks" data-off-label="false" data-on-label="false" data-off-icon-cls="glyphicon-thumbs-down" data-on-icon-cls="glyphicon-thumbs-up" checked>
												<input type="hidden" id="frm_install_default-textblocks" name="frm_install_default-textblocks" value="">
												&nbsp;&nbsp;&nbsp;
												<button type="button" class="btn btn-default" style="display: inline;" onclick="show_default_import_infobox('default-textblocks_preview', '<?php print $default_incident_types_file;?>')";"<?php print $default_textblocks_display_str;?>>
													<?php print get_text("Preview defaults");?>
												</button>
												<div id="default-textblocks_head" style="display: none;">
													<?php print get_text("Default textblocks");?>
												</div>
												<div id="default-textblocks_content" style="display: none;">
													<?php print $default_textblocks;?>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>

<!-- Fourth page display - units-->

						<div id="units">
							<div class="panel panel-default" style="padding: 0px;">
								<table class="table table-striped table-condensed" style="width: 100%;">
								<tbody id="formline_unit">
									<tr>
										<td style="width: 30%; font-weight: bold; font-size: 12px;"><?php print get_text("Unit handle");?></td>
										<td style="width: 65%; font-weight: bold; font-size: 12px;"><?php print get_text("Unit name");?></td>
										<td style="width: 5%; font-weight: bold; font-size: 12px; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></td>
									</tr>
									<tr>
										<td><input class="form-control" name='frm_unit_handle[]' type='text' size='24' maxlength='24' value='<?php print get_text("_example_unit_handle_1");?>'></td>
										<td><input class="form-control" name='frm_unit_name[]' type='text' size='50' maxlength='50' value='<?php print get_text("_example_unit_name_1");?>'></td>
										<td></td>
									</tr>
								</tbody>
								</table>
								<table class="table table-condensed" style="width: 100%;">
									<tr>
										<td style="width: 80%;"></td>
										<td style="width: 20%; text-align: right;"><button type="button" id="add_newline4" class="btn btn-xs btn-default" style="margin-top: 5px; margin-bottom: 10px;" onclick="new_line_unit();"><?php print get_text("Add Line");?></button></td>
									</tr>
								</table>
								<script>
	<?php
	$i = 2;
	while (strcmp("_example_unit_handle_" . $i, get_text("_example_unit_handle_" . $i)) != 0) {
		print "new_line_unit('" . get_text("_example_unit_handle_" . $i) . "', '" . get_text("_example_unit_name_" . $i) . "');";
		$i++;
	}
	?>
								</script>
							</div>
						</div>

<!-- Fifth page display - Responder Types -->

						<div id="unit_types">
							<div class="panel panel-default" style="padding: 0px;">
								<table class="table table-striped table-condensed" style="width: 100%;">
								<tbody id="formline_unit_type">
									<tr>
										<td style="font-weight: bold; width: 30%; font-size: 12px;"><?php print get_text("Type");?></td>
										<td style="font-weight: bold; width: 45%; font-size: 12px;"><?php print get_text("Description");?></td>
										<td style="font-weight: bold; width: 10%; font-size: 12px;"><?php print get_text("Background Color");?></td>
										<td style="font-weight: bold; width: 10%; font-size: 12px;"><?php print get_text("Text Color");?></td>
										<td style="width: 5%; font-size: 12px; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></td>
									</tr>
									<tr>
										<td><input class="form-control" name='frm_unit_type_name[]' type='text' size='16' maxlength='16' value='<?php print get_text("_example_unit_type_name_1");?>'></td>
										<td><input class="form-control" name='frm_unit_type_description[]' value="<?php print get_text("_example_unit_type_descr_1");?>"></input></td>
										<td><input class="form-control color" name="frm_unit_type_background_color[]" type="text" size=6 value="<?php print get_text("_example_unit_type_bgcolor_1");?>"></td>
										<td><input class="form-control color" name="frm_unit_type_text_color[]" type="text" size=6 value="<?php print get_text("_example_unit_type_txtcolor_1");?>"></td>
										<td></td>
									</tr>
								</tbody>
								</table>
								<table class="table table-condensed" style="width: 100%;">
									<tr>
										<td style="width: 80%;"></td>
										<td style="width: 20%; text-align: right;"><button type="button" id="add_newline5" class="btn btn-xs btn-default" style="margin-top: 5px; margin-bottom: 10px;" onclick="new_line_unit_type('', '', 'DEDEDE', '000000');"><?php print get_text("Add Line");?></button></td>
									</tr>
								</table>
								<script>
	<?php
	$i = 2;
	while (strcmp("_example_unit_type_name_" . $i, get_text("_example_unit_type_name_" . $i)) != 0) {
		print "new_line_unit_type('" . get_text("_example_unit_type_name_" . $i) . "', '" . get_text("_example_unit_type_descr_" . $i) . "', '" . get_text("_example_unit_type_bgcolor_" . $i) . "', '" . get_text("_example_unit_type_txtcolor_" . $i) . "');";
		$i++;
	}
	?>
								</script>
							</div>
						</div>

<!-- Sixth page display - Facilities-->

						<div id="facilities">
							<div class="panel panel-default">
								<table class="table table-striped table-condensed" style="width: 100%;">
								<tbody id="formline_facility">
									<tr class="form-group">
										<td style="width: 20%; font-weight: bold; font-size: 12px;"><?php print get_text("Facility handle");?></td>
										<td style="width: 35%; font-weight: bold; font-size: 12px;"><?php print get_text("Facility name");?></td>
										<td style="width: 25%; font-weight: bold; font-size: 12px;"><?php print get_text("Facility address");?></td>
										<td style="width: 15%; font-weight: bold; font-size: 12px;"><?php print get_text("City");?></td>
										<td style="width: 5%; font-size: 12px; text-align: center;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></td>
									</tr>
									<tr class="form-group">
										<td><input class="form-control" name="frm_facility_handle[]" type="text" size='24' maxlength='24' value='<?php print get_text("_example_facility_handle_1");?>'></td>
										<td><input class="form-control" name="frm_facility_name[]" type="text" size="50" maxlength="50" value="<?php print get_text("_example_facility_name_1");?>"></td>
										<td><textarea class="form-control" name="frm_facility_street[]"><?php print get_text("_example_facility_street_1");?></textarea></td>
										<td><input class="form-control" name="frm_facility_city[]" type="text" size="50" maxlength="50" value="<?php print get_text("_example_facility_city_1");?>"></td>
										<td></td>
								    </tr>
								</tbody>
								</table>
								<table class="table table-condensed" style="width: 100%;">
									<tr>
										<td style="width: 80%;"></td>
										<td style="width: 20%; text-align: right;"><button type="button" id="add_newline6" class="btn btn-xs btn-default" style="margin-top: 5px; margin-bottom: 10px;" onclick="new_line_facility();"><?php print get_text("Add Line");?></button></td>
									</tr>
								</table>
								<script>
	<?php
	$i = 2;
	while (strcmp("_example_facility_handle_" . $i, get_text("_example_facility_handle_" . $i)) != 0) {
		print "new_line_facility('" . get_text("_example_facility_handle_" . $i) . "', '" . get_text("_example_facility_name_" . $i) . "', '" . get_text("_example_facility_street_" . $i) . "', '" . get_text("_example_facility_city_" . $i) . "');";
		$i++;
	}
	?>
								</script>
							</div>
						</div>
					</form>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div id="helptext_box" class="alert alert-info" role="alert" style="padding: 1%; font-size: 14px;">
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
		</div>
	</body>
</html>
	<?php
	break;
default:
}