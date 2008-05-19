<?php
// portal/admin.php - Perform admin tasks
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
@include ('set_include_path.inc.php');
require 'db_connect.inc.php';
require 'functions.inc.php';

$accesslevel = 'admin';

include 'portalauth.inc.php';
include 'portalheader.inc.php';

if($_POST['submit'])
{
    $errors = 0;
    foreach(array_keys($_POST['visibility']) as $id)
    {
    	$id = intval($id);

        if ($id != 0)
        {
            switch ($_POST['visibility'][$id])
            {
                case 'all':
                    $visiblesql = "SET var_incident_visible_all = 'yes', ";
                    $visiblesql .= "var_incident_visible_contacts = 'no' ";
                    break;

                case 'named':
                    $visiblesql = "SET var_incident_visible_contacts = 'yes', ";
                    $visiblesql .= "var_incident_visible_all = 'no' ";
                    break;

                case 'no-one':
                default:
                    $visiblesql = "SET var_incident_visible_contacts = 'no', ";
                    $visiblesql .= "var_incident_visible_all = 'no' ";
                    break;
            }
        }

        $sql = "UPDATE `{$dbMaintenance}` ";
        $sql .= $visiblesql;
        $sql .= "WHERE id='{$id}'";

        $result = mysql_query($sql);
        if (mysql_error())
        {
            trigger_error(mysql_error(),E_USER_ERROR);
            $errors++;
        }
    }

    if ($errors == 0)
    {
        html_redirect('admin.php', TRUE);
        exit;
    }
    else
    {
        html_redirect('admin.php', FALSE);
        exit;
    }
}

echo "<h2>".icon('settings', 32, $strAdmin)." ";
echo $strAdmin."</h2>";

if ($CONFIG['portal_site_incidents'])
{
    $contracts = admin_contact_contracts($_SESSION['contactid'], $_SESSION['siteid']);

    echo "<p align='center'>{$strAdminContactForContracts}</p>";

    echo "<table align='center' width='60%'><tr>";
    //echo colheader('id', $strID);
    echo colheader('product', $strContract);
    echo colheader('expiry', $strExpiryDate);
    echo colheader('visbility', $strVisibility);
    echo colheader('actions', $strActions);

    echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
    foreach($contracts as $contract)
    {
        $sql = "SELECT *, m.id AS id ";
        $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbProducts}` AS p ";
        $sql .= "WHERE m.id={$contract} ";
        $sql .= "AND (m.expirydate > UNIX_TIMESTAMP(NOW()) OR m.expirydate = -1) ";
        $sql .= "AND m.product=p.id ";

        $result = mysql_query($sql);

        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        $shade = 'shade1';
        if($row = mysql_fetch_object($result))
        {
            if($row->expirydate == -1)
            {
                $row->expirydate = $strUnlimited;
            }
            else
            {
                $row->expirydate = ldate("jS F Y", $row->timestamp);
            }
            echo "<tr class='{$shade}'>";
            //echo "<td>{$row->id}</td>";
            echo "<td>{$row->name}</td><td>{$row->expirydate}</td>";
            echo "<td>";

            if ($row->allcontactssupported == 'yes')
            {
            	echo "<select disabled='disabled'>";
            	echo "<option>{$strAllSiteContactsSupported}</option>";
            	echo "</select>";
                echo "</td>";
            }
            else
            {
	            echo "<select name='visibility[$row->id]'>";
	            echo "<option value='no-one'";
	            if ($row->var_incident_visible_contacts == 'no' AND $row->var_incident_visible_all == 'no')
	            {
	            	echo " selected='selected'";
	            }
	            echo ">No-one</option>";
	            echo "<option value='named'";
                    if ($row->var_incident_visible_contacts == 'yes')
	            {
	            	echo " selected='selected'";
	            }
	            echo ">Named Contacts</option>";
	            echo "<option value='all'";
                    if ($row->var_incident_visible_all == 'yes')
	            {
	            	echo " selected='selected'";
	            }
	            echo ">All Contacts</option></select>";
	            echo " ".help_link('SiteIncidentVisibility');
	            echo "</td>";
            }
            echo "<td><a href='contracts.php?id={$row->id}'>{$strViewContract}</a></td></tr>";
        }

        if ($shade == 'shade1')
        {
        	$shade = 'shade2';
        }
        else
        {
        	$shade = 'shade1';
        }
    }
    echo "</table>";
    echo "<p align='center'><input type='submit' id='submit' name='submit'  value='{$strUpdate}' /></form></p>";

}

include 'htmlfooter.inc.php';
?>