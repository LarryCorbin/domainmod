<?php
// /system/change-password.php
// 
// DomainMOD is an open source application written in PHP & MySQL used to track and manage your web resources.
// Copyright (C) 2010 Greg Chetcuti
// 
// DomainMOD is free software; you can redistribute it and/or modify it under the terms of the GNU General
// Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
// 
// DomainMOD is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
// implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
// for more details.
// 
// You should have received a copy of the GNU General Public License along with DomainMOD. If not, please see
// http://www.gnu.org/licenses/
?>
<?php
include("../_includes/start-session.inc.php");
include("../_includes/config.inc.php");
include("../_includes/database.inc.php");
include("../_includes/software.inc.php");
include("../_includes/timestamps/current-timestamp.inc.php");
include("../_includes/auth/auth-check.inc.php");

$page_title = "Change Password";
$software_section = "system-change-password";

// Form Variables
$new_password = $_POST['new_password'];
$new_password_confirmation = $_POST['new_password_confirmation'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $new_password != "" && $new_password_confirmation != "" && $new_password == $new_password_confirmation) {

	$sql = "SELECT id 
			FROM users 
			WHERE id = '" . $_SESSION['user_id'] . "' 
			  AND email_address = '" . $_SESSION['email_address'] . "'";
	$result = mysql_query($sql,$connection);

   if (mysql_num_rows($result) == 1) {

		$sql_update = "UPDATE users 
					   SET password = password('$new_password'), 
					   	   new_password = '0', 
						   update_time = '$current_timestamp'
					   WHERE id = '" . $_SESSION['user_id'] . "' 
					     AND email_address = '" . $_SESSION['email_address'] . "'";
		$result_update = mysql_query($sql_update,$connection) or die("Your password could not be updated. Please try again later.");

		$_SESSION['result_message'] .= "Your password has been changed<BR>";

		header("Location: index.php");
		exit;

   } else {

		$_SESSION['result_message'] .= "Your password could not be updated<BR>";
		$_SESSION['result_message'] .= "If the problem persists please contact your administrator<BR>";

   }


} else {


	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
		if ($new_password == "" && $new_password_confirmation == "") {
		
			$_SESSION['result_message'] .= "Your passwords were left blank<BR>";

		} else {

			$_SESSION['result_message'] .= "Your passwords didn't match<BR>";
		
		}
		
	}
}
?>
<?php include("../_includes/doctype.inc.php"); ?>
<html>
<head>
<title><?=$software_title?> :: <?=$page_title?></title>
<?php include("../_includes/layout/head-tags.inc.php"); ?>
</head>
<body onLoad="document.forms[0].elements[0].focus()";>
<?php include("../_includes/layout/header.inc.php"); ?>
<form name="change_password_form" method="post" action="<?=$PHP_SELF?>">
<strong>New Password (255)</strong><BR><BR><input type="password" name="new_password" size="20" maxlength="255">
<BR><BR>
<strong>Confirm New Password</strong><BR><BR><input type="password" name="new_password_confirmation" size="20" maxlength="255">
<BR><BR>
<input type="submit" name="button" value="Change Password &raquo;">
</form>
<?php include("../_includes/layout/footer.inc.php"); ?>
</body>
</html>
