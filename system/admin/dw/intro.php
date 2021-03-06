<?php
// /system/admin/dw/intro.php
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
include("../../../_includes/start-session.inc.php");

// If the user isn't an administrator, redirect them to $full_redirect
$full_redirect = "../../../invalid.php";
include("../../../_includes/auth/admin-user-check.inc.php");

include("../../../_includes/config.inc.php");
include("../../../_includes/database.inc.php");
include("../../../_includes/software.inc.php");
include("../../../_includes/auth/auth-check.inc.php");

$page_title = "Data Warehouse";
$software_section = "admin-dw-intro";
?>
<?php include("../../../_includes/doctype.inc.php"); ?>
<html>
<head>
<title><?=$software_title?> :: <?=$page_title?></title>
<?php include("../../../_includes/layout/head-tags.inc.php"); ?>
</head>
<body>
<?php include("../../../_includes/layout/header.inc.php"); ?>
&raquo; <a href="index.php">Proceed to Data Warehouse</a><BR><BR>

<?=$software_title?> has a data warehouse framework built right into it, which allows you to import the data stored on your web server. Currently the only web servers that are supported are ones that run WHM/cPanel, but I also intend on adding support for Plesk once I've ironed out all the kinks in the framework (as well as figured out Plesk's ridiculous API documentation).<BR><BR>
If you don't run a server that uses WHM, or you don't want to import your WHM data into <?=$software_title?>, you can ignore this section.<BR><BR>
<font class="default_highlight">NOTE:</font> Importing your server(s) into the data warehouse will <strong>not</strong> modify any of your other data. The data warehouse is used for informational purposes only, and you will see its data referenced throughout the system where applicable. For example, if a domain you're editing has information stored in your data warehouse, the system will automatically match them up and display the additional information for you, giving you even more insight into your data.<BR><BR>

<font class="subheadline">Automating Builds</font><BR><BR>
If you're going to use the data warehouse, it's recommended that you setup a cron job up to execute /cron/dw.php at regular intervals in order to automate your builds. There's a lot of work being done in the background during a build, and more often than not a web browser will timeout if you try to build through the UI instead of using a cron job, leading to incomplete and missing information in your data warehouse. I would recommend setting the cron job up to run daily, preferably while you're asleep, so that way you'll always start the day with the freshest data possible.<BR><BR>

<font class="subheadline">Data Structure</font><BR><BR>
The following data is currently imported into the data warehouse.<BR><BR>
<strong>Accounts</strong><BR>
Domain, IP Address, Owner, User, Contact Email, Plan, Theme, Shell, Partition, Disk Limit, Disk Usage, Max Addons, Max FTP Accounts, Max Email Lists, Max Parked Domains, Max POP Accounts, Max SQL Accounts, Max Subdomains, Creation Date, Suspend Status, Suspend Reason, Suspend Time, Max Email Per Hour, Failed Email % Before Defer, Min Failed Email # Before Defer<BR><BR>

<strong>DNS Zones</strong><BR>
Zone File Name, Original/Primary Source of Zone Data, Admin Email, Serial #, Refresh, Retry, Expiry, Minimum TTL, Authoritative Name Server<BR><BR>

<strong>DNS Records</strong><BR>
TTL, Class, Type, IP Address, CNAME, Mail Server, Mail Server Priority, TXT Data, Line # of Zone, # of Lines, RAW Data
<?php include("../../../_includes/layout/footer.inc.php"); ?>
</body>
</html>
