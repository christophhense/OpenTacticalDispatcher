<?php
error_reporting(E_ALL);
require_once ("./incs/install.inc.php");

function install_get_current_path($filename) {
	if (DIRECTORY_SEPARATOR === "\\") {
		return str_replace("/", "\\", getcwd() . DIRECTORY_SEPARATOR . $filename);	//to windows
	} else {
		return str_replace("\\", "/", getcwd() . DIRECTORY_SEPARATOR . $filename);	//to *nix
	}
}

	?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html;">
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Content-Script-Type" content="text/javascript">
		<link href="./css/bootstrap.min.css" rel="stylesheet">
		<link href="./css/bootstrap-theme.min.css" rel="stylesheet">
	</head>
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
				<div style="font-size: 12px;" class="col-md-10">
					<?php print get_version();?>
				</div>
				<div class="col-md-1"></div>
			</div>
	<?php
if ((isset ($_GET['install_complete'])) && ($_GET['install_complete'] == "yes")) {
	try {
		$database_link = new PDO("mysql:host=" . $_POST['frm_db_host'] . ";dbname=" . $_POST['frm_db_dbname'] , $_POST['frm_db_user'], $_POST['frm_db_password']);
		$database_link = null;
	} catch (PDOException $e) {
		$password = "<i>none entered</i>";
		if ((isset ($_POST['frm_db_password'])) && ($_POST['frm_db_password'] != "")) {
			$password = $_POST['frm_db_password'];
		}
	?>
			<div class="row" style="margin-top: 10%;">
				<div class="col-md-4"></div>
					<div class="col-md-4">
						<div class="alert alert-danger">
							<b>Connection to Database failed using the following entered values:</b><br><br>
							Error-message::<b> <?php print $e->getMessage();?></b><br>
							Database Name:<b> <?php print $_POST['frm_db_dbname'];?></b><br>
							Username:<b> <?php print $_POST['frm_db_user'];?></b><br>
							Password:<b> <?php print $password;?></b><br>
							Host:<b> <?php print $_POST['frm_db_host'];?></b><br>
							Database:<b> <?php print $_POST['frm_db_dbname'];?></b><br><br>
							Please correct these entries and try again.
						</div>
					</div>
				<div class="col-md-4" style="margin-bottom: 10%;"></div>
			</div>
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
				<form name="db_error" method="post" action="install.php">
					<button class="btn btn-default btn-lg btn-block" style="align: center;" type="submit">Back</button>
				 </form>
				</div>
				<div class="col-md-4"></div>
			</div>
	<?php
		die ();
	}
	$output_text = install(get_version(), $_POST['frm_locale'], $_POST['frm_option'], $_POST['frm_db_host'], $_POST['frm_db_dbname'], $_POST['frm_db_user'], $_POST['frm_db_password']);
	$first_start_str = "";
	if ($_POST['frm_option'] == "install") {
		$first_start_str = "?first_start=yes";
	}
	?>	
			<div class="row" style="margin-top: 100px;">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<div class="panel panel-success">
						<div class="panel-heading" style="padding: 1px; text-align: center;">
							<h4>Your installation is now complete</h4>
						</div>
						<div class="panel-body" style="padding: 0;">
							<div class="container-fluid" style="margin-top: 20px; margin-bottom: 20px;">
								<div class="row">
									<div class="col-md-1"></div>
									<div class="col-md-10">
										<?php print $output_text;?>
										<li>The start page is 'index.php'.</li>
										<li><font class="warn">It is strongly recommended that you move/delete/change rights on install.php after this.</font></li>
									</div>
									<div class="col-md-1"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-3"></div>
								<div class="col-md-6">
									<button class="btn btn-default btn-lg btn-block" onclick="location.href='index.php<?php print $first_start_str;?>';">Next</button>
								</div>
							<div class="col-md-3"></div>
						</div>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
	<?php
} else {
		$filename = "./incs";
		if (!is_writable($filename)) {
			die ("ERROR! Directory '" . $filename . "' is not writable. 'Write' permissions must be corrected for installation.");
		}
		$filename = './incs/db_credentials.inc.php';
		$dir = "./";
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			if (is_dir($filename)) {
				$files[] = $filename;
			}
		}
		$dirsOK = true;
		if (!in_array("incs", $files)) {
			$dirsOK = false;
		}
		if (!$dirsOK) {
	?>
				<div class="row" style="margin-top: 10%;">
					<div class="col-md-4"></div>
						<div class="col-md-4">
							<div class="alert alert-danger">
							At least one of the subdirectories is missing and this needs to be corrected. You might check into how the zip file was unzipped or otherwise installed.
							</div>
						</div>
					<div class="col-md-4"></div>
				</div>
				<div class="row">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<form name="db_error" method="post" action="install.php">
							<button class="btn btn-default btn-lg btn-block" style="align: center;" type="submit">Back</button>
						 </form>
					</div>
					<div class="col-md-4"></div>
				</div>
	<?php
		} else {
			if (file_exists("./incs/db_credentials.inc.php")) {
				include_once ("./incs/db_credentials.inc.php");
				$my_host = $GLOBALS['db_host'];
				$my_user = $GLOBALS['db_user'];
				$my_passwd = $GLOBALS['db_password'];
				$my_db = $GLOBALS['db_name'];
			} else {
				$my_host = "";
				$my_user = "";
				$my_passwd = "";
				$my_db = "";
			}
			$install_checked_str = "";
			$reset_credentials_checked_str = "";
			if ((isset ($_GET['write_credentials_checked'])) && ($_GET['write_credentials_checked'] == "true")) {
				$reset_credentials_checked_str = " checked";
			} else {
				$install_checked_str = " checked";
			}
	?>
			<form name="install_frm" method="post" action="install.php?install_complete=yes">
				<div class="row" style="margin-top: 20px;">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<div class="panel panel-default">
							<div class="panel-heading" style="padding: 1px; text-align: center;">
								<h4>Database Configuration</h4>
							</div>
							<div class="panel-body" style="padding: 0;">
								<table class="table table-striped">
									<tr>
										<td>Database:</td>
										<td><input type="text" size="30" maxlength="255" name="frm_db_dbname" value="<?php print $my_db;?>"></td>
									</tr>
									<tr>
										<td>Username: </td>
										<td><input type="text" size="30" maxlength="255" name="frm_db_user" value="<?php print $my_user;?>"></td>
									</tr>
									<tr>
										<td>Password: </td>
										<td><input type="password" size="30" maxlength="255" name="frm_db_password" value="<?php print $my_passwd;?>"></td>
									</tr>
									<tr>
										<td>Host: </td>
										<td><input type="text" size="30" maxlength="255" name="frm_db_host" value="<?php print $my_host;?>"></td>
									</tr>
									<tr>
										<td>Select locale:</td>
										<td>
											<?php print get_locale_select_str(install_get_current_path("sql/"), "de-DE");?>
										</td>
									</tr>
									<tr>
										<td>Install Option:</td>
										<td>
											<label class="radio-inline"><input type="radio" value="install" name="frm_option"<?php print $install_checked_str;?>>&nbsp;Install database tables new (drop tables if exist)</label><br>
											<label class="radio-inline"><input type="radio" value="reset_settings" name="frm_option">&nbsp;Reset settings (do not touch user data)</label><br>
											<label class="radio-inline"><input type="radio" value="write_credentials" name="frm_option"<?php print $reset_credentials_checked_str;?>>&nbsp;Write db-configuration file only</label>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-4"></div>
				</div>
				<div class="row">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<div class="container-fluid">
							<div class="row">
								<div class="col-md-6">
									<button class="btn btn-default btn-lg btn-block" style="align: center;" type="reset">Reset</button>
								</div>
								<div class="col-md-6">
									<button class="btn btn-default btn-lg btn-block" style="align: center;" type="submit">Install</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4"></div>
				</div>	
				<div class="row" style="margin-top: 20px;">
					<div class="col-md-4"></div>
						<div class="col-md-4">
							<div class="alert alert-info">	
								<li>The file <b>'db_credentials.inc.php'</b> in the <b>'incs'</b> subdirectory, <b>must be write-able in any install option</b>.</li>
								<li>The <b>Install database tables new</b> option <font class="warn">drops all data</font> in the specified database and re-installs them.</li>	
								<li>The <b>Reset settings</b> option reset the settings in the specified database.</li>
								<li>The <b>Write db-configuration file only</b> option writes the specified mysql settings to the file <b>'db_credentials.inc.php'</b>in the <b>'incs'</b> subdirectory but doesn't alter the database in any way.</li>
								<li>It is strongly recommended that you move/delete/change rights on install.php after this</font></li>
							</div>
						</div>
					<div class="col-md-4"></div>
				</div>
			</form>
		</div>
	<?php
	}
}
	?>
	</body>
</html>