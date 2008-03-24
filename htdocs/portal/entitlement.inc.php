<?php
/*
portal/entitlement.inc.php - Lists contacts entitlments in the portal included by ../portal.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}


echo "<h2>{$strYourSupportEntitlement}</h2>";
$sql = "SELECT m.*, p.*, ";
$sql .= "(m.incident_quantity - m.incidents_used) AS availableincidents ";
$sql .= "FROM `{$dbSupportContacts}` AS sc, `{$dbMaintenance}` AS m, `{$dbProducts}` AS p ";
$sql .= "WHERE sc.maintenanceid=m.id ";
$sql .= "AND m.product=p.id ";
$sql .= "AND sc.contactid='{$_SESSION['contactid']}'";
if ($numcontracts >= 1)
{
    echo "<table align='center'>";
    echo "<tr>";
    echo colheader('id',$strContractID);
    echo colheader('name',$strProduct);
    echo colheader('availableincidents',$strIncidentsAvailable);
    echo colheader('usedincidents',$strIncidentsUsed);
    echo colheader('expirydate', $strExpiryDate);
    echo colheader('actions', $strOperation);
    echo "</tr>";
    $shade = 'shade1';
    while ($contract = mysql_fetch_object($result))
    {
        echo "<tr class='$shade'><td>{$contract->id}</td><td>{$contract->name}</td>";
        echo "<td>";
        if ($contract->incident_quantity==0)
        {
            echo "&#8734; {$strUnlimited}";
        }
        else
        {
            echo "{$contract->availableincidents}";
        }
        echo "</td>";
        echo "<td>{$contract->incidents_used}</td>";
        echo "<td>".ldate($CONFIG['dateformat_date'],$contract->expirydate)."</td>";
        echo "<td><a href='$_SERVER[PHP_SELF]?page=add&amp;contractid={$contract->id}'>{$strAddIncident}</a></td></tr>\n";
        if ($shade == 'shade1') $shade = 'shade2';
        else $shade = 'shade1';
    }
    echo "</table>";
}
else
{
    echo "<p class='info'>{$strNone}</p>";
}

?>