<?php
// /system/admin/defaults.php
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
include("../../_includes/start-session.inc.php");

// If the user isn't an administrator, redirect them to $full_redirect
$full_redirect = "../../invalid.php";
include("../../_includes/auth/admin-user-check.inc.php");

include("../../_includes/config.inc.php");
include("../../_includes/database.inc.php");
include("../../_includes/software.inc.php");
include("../../_includes/timestamps/current-timestamp.inc.php");
include("../../_includes/auth/auth-check.inc.php");

$page_title = "System Defaults";
$software_section = "admin-system-defaults";

// Form Variables
$new_default_category_domains = $_POST['new_default_category_domains'];
$new_default_category_ssl = $_POST['new_default_category_ssl'];
$new_default_dns = $_POST['new_default_dns'];
$new_default_host = $_POST['new_default_host'];
$new_default_ip_address_domains = $_POST['new_default_ip_address_domains'];
$new_default_ip_address_ssl = $_POST['new_default_ip_address_ssl'];
$new_default_owner_domains = $_POST['new_default_owner_domains'];
$new_default_owner_ssl = $_POST['new_default_owner_ssl'];
$new_default_registrar = $_POST['new_default_registrar'];
$new_default_registrar_account = $_POST['new_default_registrar_account'];
$new_default_ssl_provider_account = $_POST['new_default_ssl_provider_account'];
$new_default_ssl_type = $_POST['new_default_ssl_type'];
$new_default_ssl_provider = $_POST['new_default_ssl_provider'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$_SESSION['result_message'] .= "The System Defaults were updated<BR>";

	$sql = "UPDATE settings
			SET default_category_domains = '$new_default_category_domains',
				default_category_ssl = '$new_default_category_ssl',
				default_dns = '$new_default_dns',
				default_host = '$new_default_host',
				default_ip_address_domains = '$new_default_ip_address_domains',
				default_ip_address_ssl = '$new_default_ip_address_ssl',
				default_owner_domains = '$new_default_owner_domains',
				default_owner_ssl = '$new_default_owner_ssl',
				default_registrar = '$new_default_registrar',
				default_registrar_account = '$new_default_registrar_account',
				default_ssl_provider_account = '$new_default_ssl_provider_account',
				default_ssl_type = '$new_default_ssl_type',
				default_ssl_provider = '$new_default_ssl_provider',
				update_time = '$current_timestamp'";
	$result = mysql_query($sql,$connection) or die(mysql_error());

	$_SESSION['system_default_category_domains'] = $new_default_category_domains;
	$_SESSION['system_default_category_ssl'] = $new_default_category_ssl;
	$_SESSION['system_default_dns'] = $new_default_dns;
	$_SESSION['system_default_host'] = $new_default_host;
	$_SESSION['system_default_ip_address_domains'] = $new_default_ip_address_domains;
	$_SESSION['system_default_ip_address_ssl'] = $new_default_ip_address_ssl;
	$_SESSION['system_default_owner_domains'] = $new_default_owner_domains;
	$_SESSION['system_default_owner_ssl'] = $new_default_owner_ssl;
	$_SESSION['system_default_registrar'] = $new_default_registrar;
	$_SESSION['system_default_registrar_account'] = $new_default_registrar_account;
	$_SESSION['system_default_ssl_provider_account'] = $new_default_ssl_provider_account;
	$_SESSION['system_default_ssl_type'] = $new_default_ssl_type;
	$_SESSION['system_default_ssl_provider'] = $new_default_ssl_provider;
	
	header("Location: ../index.php");
	exit;

}
?>
<?php include("../../_includes/doctype.inc.php"); ?>
<html>
<head>
<title><?=$software_title?> :: <?=$page_title?></title>
<?php include("../../_includes/layout/head-tags.inc.php"); ?>
</head>
<body>
<?php include("../../_includes/layout/header.inc.php"); ?>
<form name="default_settings_form" method="post" action="<?=$PHP_SELF?>">
<BR><font class="subheadline">Domain Defaults</font><BR><BR>
<strong>Default Domain Registrar</strong><BR><BR>
<select name="new_default_registrar">
<?php
$sql = "SELECT id, name
		FROM registrars
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_registrar'] == $row->id) echo " selected"; ?>><?=$row->name?></option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default Domain Registrar Account</strong><BR><BR>
<select name="new_default_registrar_account">
<?php
$sql = "SELECT ra.id, ra.username, r.name AS r_name, o.name AS o_name
		FROM registrars AS r, registrar_accounts AS ra, owners AS o
		WHERE r.id = ra.registrar_id
		  AND ra.owner_id = o.id
		ORDER BY r.name, o.name, ra.username";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_registrar_account'] == $row->id) echo " selected"; ?>><?=$row->r_name?> :: <?=$row->o_name?> :: <?=$row->username?></option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default DNS Profile</strong><BR><BR>
<select name="new_default_dns">
<?php
$sql = "SELECT id, name
		FROM dns
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_dns'] == $row->id) echo " selected"; ?>><?=$row->name?></option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default Web Hosting Provider</strong><BR><BR>
<select name="new_default_host">
<?php
$sql = "SELECT id, name
		FROM hosting
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_host'] == $row->id) echo " selected"; ?>><?=$row->name?></option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default IP Address</strong><BR><BR>
<select name="new_default_ip_address_domains">
<?php
$sql = "SELECT id, ip, name
		FROM ip_addresses
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_ip_address_domains'] == $row->id) echo " selected"; ?>><?=$row->name?> (<?=$row->ip?>)</option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default Category</strong><BR><BR>
<select name="new_default_category_domains">
<?php
$sql = "SELECT id, name
		FROM categories
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_category_domains'] == $row->id) echo " selected"; ?>><?=$row->name?></option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default Account Owner</strong><BR><BR>
<select name="new_default_owner_domains">
<?php
$sql = "SELECT id, name
		FROM owners
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_owner_domains'] == $row->id) echo " selected"; ?>><?=$row->name?></option>
    <?php
}
?>
</select>
<BR><BR><BR>
<font class="subheadline">SSL Defaults</font><BR><BR>
<strong>Default SSL Provider</strong><BR><BR>
<select name="new_default_ssl_provider">
<?php
$sql = "SELECT id, name
		FROM ssl_providers
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_ssl_provider'] == $row->id) echo " selected"; ?>><?=$row->name?></option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default SSL Provider Account</strong><BR><BR>
<select name="new_default_ssl_provider_account">
<?php
$sql = "SELECT sslpa.id, sslpa.username, sslp.name AS p_name, o.name AS o_name
		FROM ssl_providers AS sslp, ssl_accounts AS sslpa, owners AS o
		WHERE sslp.id = sslpa.ssl_provider_id
		  AND sslpa.owner_id = o.id
		ORDER BY sslp.name, o.name, sslpa.username";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_ssl_provider_account'] == $row->id) echo " selected"; ?>><?=$row->p_name?> :: <?=$row->o_name?> :: <?=$row->username?></option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default SSL Type</strong><BR><BR>
<select name="new_default_ssl_type">
<?php
$sql = "SELECT id, type
		FROM ssl_cert_types
		ORDER BY type";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_ssl_type'] == $row->id) echo " selected"; ?>><?=$row->type?></option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default IP Address</strong><BR><BR>
<select name="new_default_ip_address_ssl">
<?php
$sql = "SELECT id, ip, name
		FROM ip_addresses
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_ip_address_ssl'] == $row->id) echo " selected"; ?>><?=$row->name?> (<?=$row->ip?>)</option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default Category</strong><BR><BR>
<select name="new_default_category_ssl">
<?php
$sql = "SELECT id, name
		FROM categories
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_category_ssl'] == $row->id) echo " selected"; ?>><?=$row->name?></option>
    <?php
}
?>
</select>
<BR><BR>
<strong>Default Account Owner</strong><BR><BR>
<select name="new_default_owner_ssl">
<?php
$sql = "SELECT id, name
		FROM owners
		ORDER BY name";
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_object($result)) {
	?>
	<option value="<?=$row->id?>"<?php if ($_SESSION['system_default_owner_ssl'] == $row->id) echo " selected"; ?>><?=$row->name?></option>
    <?php
}
?>
</select>
<BR><BR><BR>
<input type="submit" name="button" value="Update System Defaults &raquo;">
</form>
<?php include("../../_includes/layout/footer.inc.php"); ?>
</body>
</html>
