<?php
// /assets/registrar-accounts.php
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
include("../_includes/auth/auth-check.inc.php");
include("../_includes/timestamps/current-timestamp.inc.php");

$page_title = "Domains Registrar Accounts";
$software_section = "registrar-accounts";

$rid = $_GET['rid'];
$raid = $_GET['raid'];
$oid = $_GET['oid'];
$export = $_GET['export'];

if ($rid != "") { $rid_string = " AND ra.registrar_id = '$rid' "; } else { $rid_string = ""; }
if ($raid != "") { $raid_string = " AND ra.id = '$raid' "; } else { $raid_string = ""; }
if ($oid != "") { $oid_string = " AND ra.owner_id = '$oid' "; } else { $oid_string = ""; }

$sql = "SELECT ra.id AS raid, ra.username, ra.password, ra.owner_id, ra.registrar_id, ra.reseller, o.id AS oid, o.name AS oname, r.id AS rid, r.name AS rname, ra.notes, ra.insert_time, ra.update_time
		FROM registrar_accounts AS ra, owners AS o, registrars AS r, domains AS d
		WHERE ra.owner_id = o.id
		  AND ra.registrar_id = r.id
		  AND ra.id = d.account_id
		  AND d.active not in ('0', '10')
		  $rid_string
		  $raid_string
		  $oid_string
		  AND (SELECT count(*) 
		  	   FROM domains 
			   WHERE account_id = ra.id 
			     AND active NOT IN ('0', '10')) 
				 > 0
		GROUP BY ra.username, oname, rname
		ORDER BY rname, username, oname";

if ($export == "1") {

	$result = mysql_query($sql,$connection) or die(mysql_error());

	$current_timestamp_unix = strtotime($current_timestamp);
	$export_filename = "registrar_account_list_" . $current_timestamp_unix . ".csv";
	include("../_includes/system/export/header.inc.php");

	$row_content[$count++] = $page_title;
	include("../_includes/system/export/write-row.inc.php");

	fputcsv($file_content, $blank_line);

	$row_content[$count++] = "Status";
	$row_content[$count++] = "Registrar";
	$row_content[$count++] = "Username";
	$row_content[$count++] = "Password";
	$row_content[$count++] = "Owner";
	$row_content[$count++] = "Domains";
	$row_content[$count++] = "Default Account?";
	$row_content[$count++] = "Reseller Account?";
	$row_content[$count++] = "Notes";
	$row_content[$count++] = "Inserted";
	$row_content[$count++] = "Updated";
	include("../_includes/system/export/write-row.inc.php");

	if (mysql_num_rows($result) > 0) {
		
		$has_active = 1;
	
		while ($row = mysql_fetch_object($result)) { 
	
			$new_raid = $row->raid;
		
			if ($current_raid != $new_raid) {
				$exclude_account_string_raw .= "'" . $row->raid . "', ";
			}
	
			$sql_domain_count = "SELECT count(*) AS total_domain_count
								 FROM domains
								 WHERE account_id = '" . $row->raid . "'
								   AND active NOT IN ('0', '10')";
			$result_domain_count = mysql_query($sql_domain_count,$connection);
			while ($row_domain_count = mysql_fetch_object($result_domain_count)) {
				$total_domain_count = $row_domain_count->total_domain_count;
			}
		
			if ($row->raid == $_SESSION['default_registrar_account']) {
			
				$is_default = "1";
				
			} else {
			
				$is_default = "";
			
			}
			
			if ($row->reseller == "0") {
				
				$is_reseller = "";
				
			} else {
				
				$is_reseller = "1";
	
			}

			$row_content[$count++] = "Active";
			$row_content[$count++] = $row->rname;
			$row_content[$count++] = $row->username;
			$row_content[$count++] = $row->password;
			$row_content[$count++] = $row->oname;
			$row_content[$count++] = $total_domain_count;
			$row_content[$count++] = $is_default;
			$row_content[$count++] = $is_reseller;
			$row_content[$count++] = $row->notes;
			$row_content[$count++] = $row->insert_time;
			$row_content[$count++] = $row->update_time;
			include("../_includes/system/export/write-row.inc.php");
	
			$current_raid = $row->raid;
		
		}
		
	}
	
	$exclude_account_string = substr($exclude_account_string_raw, 0, -2); 
	
	if ($exclude_account_string != "") { 
	
		$raid_string = " AND ra.id not in ($exclude_account_string) "; 
		
	} else { 
	
		$raid_string = ""; 
		
	}
	
	$sql = "SELECT ra.id AS raid, ra.username, ra.password, ra.owner_id, ra.registrar_id, ra.reseller, o.id AS oid, o.name AS oname, r.id AS rid, r.name AS rname, ra.notes, ra.insert_time, ra.update_time
			FROM registrar_accounts AS ra, owners AS o, registrars AS r
			WHERE ra.owner_id = o.id
			  AND ra.registrar_id = r.id
			  " . $rid_string . "
			  " . $raid_string . "
			  " . $oid_string . "
			GROUP BY ra.username, oname, rname
			ORDER BY rname, username, oname";
	
	$result = mysql_query($sql,$connection) or die(mysql_error());
	
	if (mysql_num_rows($result) > 0) { 
	
		$has_inactive = "1";
	
		while ($row = mysql_fetch_object($result)) {
	
			if ($row->raid == $_SESSION['default_registrar_account']) {
			
				$is_default = "1";
				
			} else {
			
				$is_default = "";
			
			}
	
			if ($row->reseller == "0") {
				
				$is_reseller = "";
				
			} else {
				
				$is_reseller = "1";
	
			}

			$row_content[$count++] = "Inactive";
			$row_content[$count++] = $row->rname;
			$row_content[$count++] = $row->username;
			$row_content[$count++] = $row->password;
			$row_content[$count++] = $row->oname;
			$row_content[$count++] = 0;
			$row_content[$count++] = $is_default;
			$row_content[$count++] = $is_reseller;
			$row_content[$count++] = $row->notes;
			$row_content[$count++] = $row->insert_time;
			$row_content[$count++] = $row->update_time;
			include("../_includes/system/export/write-row.inc.php");

		}
	
	}

	include("../_includes/system/export/footer.inc.php");

}
?>
<?php include("../_includes/doctype.inc.php"); ?>
<html>
<head>
<title><?=$software_title?> :: <?=$page_title?></title>
<?php include("../_includes/layout/head-tags.inc.php"); ?>
</head>
<body>
<?php include("../_includes/layout/header.inc.php"); ?>
Below is a list of all the Domain Registrar Accounts that are stored in your <?=$software_title?>.<BR><BR>
[<a href="<?=$PHP_SELF?>?export=1&rid=<?=$rid?>&raid=<?=$raid?>&oid=<?=$oid?>">EXPORT</a>]<?php

$result = mysql_query($sql,$connection) or die(mysql_error());

if (mysql_num_rows($result) > 0) {
	
	$has_active = 1; ?>
    <table class="main_table" cellpadding="0" cellspacing="0">
    <tr class="main_table_row_heading_active">
        <td class="main_table_cell_heading_active">
            <font class="main_table_heading">Registrar Name</font>
        </td>
        <td class="main_table_cell_heading_active">
            <font class="main_table_heading">Active Accounts (<?=mysql_num_rows($result)?>)</font>
        </td>
        <td class="main_table_cell_heading_active">
            <font class="main_table_heading">Owner</font>
        </td>
        <td class="main_table_cell_heading_active">
            <font class="main_table_heading">Domains</font>
        </td>
    </tr><?php 

    while ($row = mysql_fetch_object($result)) { 

	    $new_raid = $row->raid;
    
        if ($current_raid != $new_raid) {
			$exclude_account_string_raw .= "'" . $row->raid . "', ";
		} ?>

		<tr class="main_table_row_active">
			<td class="main_table_cell_active">
                <a class="invisiblelink" href="edit/registrar-account.php?raid=<?=$row->raid?>"><?=$row->rname?></a>
			</td>
			<td class="main_table_cell_active" valign="top">
				<a class="invisiblelink" href="edit/registrar-account.php?raid=<?=$row->raid?>"><?=$row->username?></a><?php if ($_SESSION['default_registrar_account'] == $row->raid) echo "<a title=\"Default Account\"><font class=\"default_highlight\">*</font></a>"; ?><?php if ($row->reseller == "1") echo "<a title=\"Reseller Account\"><font class=\"reseller_highlight\">*</font></a>"; ?>
			</td>
			<td class="main_table_cell_active">
				<a class="invisiblelink" href="edit/registrar-account.php?raid=<?=$row->raid?>"><?=$row->oname?></a>
			</td>
			<td class="main_table_cell_active"><?php
				$sql_domain_count = "SELECT count(*) AS total_domain_count
									 FROM domains
									 WHERE account_id = '" . $row->raid . "'
									   AND active NOT IN ('0', '10')";
				$result_domain_count = mysql_query($sql_domain_count,$connection);

				while ($row_domain_count = mysql_fetch_object($result_domain_count)) { 
					echo "<a class=\"nobold\" href=\"../domains.php?oid=$row->oid&rid=$row->rid&raid=$row->raid\">" . number_format($row_domain_count->total_domain_count) . "</a>"; 
				} ?>
			</td>
		</tr><?php 

		$current_raid = $row->raid;
	
	}
	
}

$exclude_account_string = substr($exclude_account_string_raw, 0, -2); 

if ($exclude_account_string != "") { 

	$raid_string = " AND ra.id not in ($exclude_account_string) "; 
	
} else { 

	$raid_string = ""; 
	
}

$sql = "SELECT ra.id AS raid, ra.username, ra.owner_id, ra.registrar_id, ra.reseller, o.id AS oid, o.name AS oname, r.id AS rid, r.name AS rname
		FROM registrar_accounts AS ra, owners AS o, registrars AS r
		WHERE ra.owner_id = o.id
		  AND ra.registrar_id = r.id
		  " . $rid_string . "
		  " . $raid_string . "
		  " . $oid_string . "
		GROUP BY ra.username, oname, rname
		ORDER BY rname, username, oname";

$result = mysql_query($sql,$connection) or die(mysql_error());

if (mysql_num_rows($result) > 0) { 

	$has_inactive = "1";
	if ($has_active == "1") echo "<BR>";
	if ($has_active != "1" && $has_inactive == "1") echo "<table class=\"main_table\" cellpadding=\"0\" cellspacing=\"0\">"; ?>

    <tr class="main_table_row_heading_inactive">
        <td class="main_table_cell_heading_inactive">
            <font class="main_table_heading">Registrar Name</font>
        </td>
        <td class="main_table_cell_heading_inactive">
            <font class="main_table_heading">Inactive Accounts (<?=mysql_num_rows($result)?>)</font>
        </td>
        <td class="main_table_cell_heading_inactive">
            <font class="main_table_heading">Owner</font>
        </td>
    </tr><?php 

	while ($row = mysql_fetch_object($result)) { ?>

        <tr class="main_table_row_inactive">
            <td class="main_table_cell_inactive">
	                <a class="invisiblelink" href="edit/registrar-account.php?raid=<?=$row->raid?>"><?=$row->rname?></a>
            </td>
            <td class="main_table_cell_inactive" valign="top">
				<a class="invisiblelink" href="edit/registrar-account.php?raid=<?=$row->raid?>"><?=$row->username?></a><?php if ($_SESSION['default_registrar_account'] == $row->raid) echo "<a title=\"Default Account\"><font class=\"default_highlight\">*</font></a>"; ?><?php if ($row->reseller == "1") echo "<a title=\"Reseller Account\"><font class=\"reseller_highlight\">*</font></a>"; ?>
            </td>
            <td class="main_table_cell_inactive">
                <a class="invisiblelink" href="edit/registrar-account.php?raid=<?=$row->raid?>"><?=$row->oname?></a>
            </td>
        </tr><?php 

	}

}

if ($has_active == "1" || $has_inactive == "1") echo "</table>";

if ($has_active || $has_inactive) { ?>
		<BR><font class="default_highlight">*</font> = Default Account&nbsp;&nbsp;<font class="reseller_highlight">*</font> = Reseller Account<?php 
}

if (!$has_active && !$has_inactive) {
	
	$sql = "SELECT id
			FROM registrars
			LIMIT 1";
	$result = mysql_query($sql,$connection);
	
	if (mysql_num_rows($result) == 0) {  ?>

		<BR>Before adding a Registrar Account you must add at least one Registrar. <a href="add/registrar.php">Click here to add a Registrar</a>.<BR><?php 

	} else { ?>

		<BR>You don't currently have any Registrar Accounts. <a href="add/registrar-account.php">Click here to add one</a>.<BR><?php 

	}

} ?>
<?php include("../_includes/layout/footer.inc.php"); ?>
</body>
</html>
