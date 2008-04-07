<?php
/*
portal/entitlement.inc.php - Lists contacts entitlments in the portal included by ../portal.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

include 'portalheader.inc.php';

echo "<h2>{$strYourSupportEntitlement}</h2>";

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
        echo "<td>";
        if($contract->expirydate == -1)
            echo $strUnlimited;
        else
            echo ldate($CONFIG['dateformat_date'],$contract->expirydate);
            
        echo "</td>";
        echo "<td>";
        if ($contract->expirydate > $now OR $contract->expirydate == -1)
        {
            echo "<a href='add.php?contractid={$contract->id}&amp;product={$contract->product}'>{$strAddIncident}</a>";
        }
        echo "</td></tr>\n";
        if ($shade == 'shade1') $shade = 'shade2';
        else $shade = 'shade1';
    }
    echo "</table>";
}
else
{
    echo "<p class='info'>{$strNone}</p>";
}

include 'htmlfooter.inc.php';
?>