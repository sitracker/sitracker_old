<?php
// contact_support.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas

// This Page Is Valid XHTML 1.0 Transitional!   4Nov05
// 24Apr02 INL Fixed a divide by zero bug

$permission=6; // view incidents

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);
$mode = $_REQUEST['mode'];
if (!empty($_REQUEST['start'])) $start = strtotime($_REQUEST['start']);
else $start=0;
if (!empty($_REQUEST['end'])) $start = strtotime($_REQUEST['end']);
else $end=0;
$status = $_REQUEST['status'];

include('htmlheader.inc.php');

if ($mode=='site') echo "<h2>".site_name($id)."</h2>";
else echo "<h2>".contact_realname($id)."'</h2>";

if ($mode=='site')
{
    $sql = "SELECT *, (closed - opened) AS duration_closed, incidents.id AS incidentid FROM incidents, contacts ";
    $sql .= "WHERE incidents.contact=contacts.id ";
    if (!empty($id) AND ($id != 'all' AND !empty($start)))$sql .= "AND contacts.siteid='$id' ";
    if ($status=='open') $sql .= "AND incidents.status!=2 ";
    elseif ($status=='closed') $sql .= "AND incidents.status=2 ";
    if ($start > 0) $sql .= "AND opened >= $start ";
    if ($end > 0) $sql .= "AND opened <= $end ";
    $sql .= "ORDER BY opened DESC";
}
else
{
    $sql = "SELECT *, (closed - opened) AS duration_closed, incidents.id AS incidentid FROM incidents WHERE ";
    $sql .= "contact='$id' ";
    if ($status=='open') $sql .= "AND incidents.status!=2 ";
    elseif ($status=='closed') $sql .= "AND incidents.status=2 ";
    $sql .= "ORDER BY opened DESC";
}
$result = mysql_query($sql);

echo "<h3>All Incidents</h3>";

echo "<table align='center'>";
echo "<tr>";
echo "<th>Incident ID</th>";
echo "<th>Title</th>";
if ($mode=='site') echo "<th>Contact</th>";
echo "<th>Product</th>";
echo "<th>Status</th>";
echo "<th>Engineer</th>";
echo "<th>Opened</th>";
echo "<th>Closed</th>";
echo "<th>Duration</th>";
echo "<th>SLA</th>";
echo "</tr>";
$shade='shade1';
$totalduration=0;
$countclosed=0;
$countincidents=0;
$countextincidents=0;
$countslaexceeded=0;
$productlist = array();
if ($mode=='site') $contactlist = array();
while ($row=mysql_fetch_object($result))
{
    $targetmet = TRUE;
    if ($row->status==2) $shade='expired';
    else $shade='shade1';
    echo "<tr class='$shade'>";
    echo "<td>".$row->incidentid."</td>";
    // title
    echo "<td>";
    echo "<a href=\"javascript:incident_details_window('".$row->incidentid."','incident".$row->incidentid."')\">";
    if (trim($row->title) !='') echo htmlspecialchars($row->title); else echo 'Untitled';
    echo "</a>";
    echo "</td>";
    if ($mode=='site')
    {
        $contactrealname = contact_realname($row->contact);
        echo "<td>{$contactrealname}</td>";
        if ($mode=='site')
        {
            if (!array_key_exists($contactrealname, $contactlist)) $contactlist[$contactrealname] = 1;
            else { $contactlist[$contactrealname]++; }
        }
    }
    echo "<td>".product_name($row->product)."</td>";
    if ($row->status==2) echo "<td>Closed, ".closingstatus_name($row->closingstatus)."</td>";
    else echo "<td>".incidentstatus_name($row->status)."</td>";
    echo "<td>".user_realname($row->owner)."</td>";
    echo "<td>".date($CONFIG['dateformat_date'],$row->opened)."</td>";
    if ($row->closed > 0)
    {
        echo "<td>".date($CONFIG['dateformat_date'], $row->closed)."</td>";
        echo "<td>".format_seconds($row->duration_closed)."</td>";
    }
    else echo "<td colspan='2'>-</td>";
    echo "<td>";
    $slahistory = incident_sla_history($row->incidentid);
    foreach($slahistory AS $history)
    {
        if ($history['targetmet'] == FALSE) $targetmet = FALSE;
    }
    if ($targetmet == TRUE) echo "Met";
    else { $countslaexceeded++; echo "Exceeded"; }
    echo "</td>";

    if (!array_key_exists($row->product, $productlist)) $productlist[$row->product] = 1;
    else { $productlist[$row->product]++; }
    $countincidents++;
    if (!empty($row->externalid)) $countextincidents++;
    $totalduration=$totalduration+$row->duration_closed;
    $countclosed++;
    echo "</tr>\n";
}
echo "</table>\n";
if (mysql_num_rows($result)>=1 && $countclosed >= 1)
{
    echo "<p align='center'>Average incident duration: ".format_seconds($totalduration/$countclosed)."</p>";
}
echo "<p align='center'>Show: ";
echo "<a href=\"{$_SERVER['PHP_SELF']}?id=$id&amp;mode=$mode&amp;status=open\">Open Only</a> | ";
echo "<a href=\"{$_SERVER['PHP_SELF']}?id=$id&amp;mode=$mode&amp;status=closed\">Closed  Only</a> | ";
echo "<a href=\"{$_SERVER['PHP_SELF']}?id=$id&amp;mode=$mode\">All</a>";
echo "</p>";

$countproducts = array_sum($productlist);
if ($mode=='site') $countcontacts = array_sum($contactlist);

if ($countproducts >= 1 OR $contactcontacts >= 1)
{
    foreach ($productlist AS $product => $quantity)
    {
        $productpercentage = number_format($quantity * 100 / $countproducts, 1);
        $productlegends[] = urlencode(product_name($product)." ({$productpercentage}%)");
    }

    if ($mode=='site')
    {
        foreach ($contactlist AS $contact => $quantity)
        {
            $contactpercentage = number_format($quantity * 100 / $countcontacts, 1);
            $contactlegends[] = urlencode("$contact ({$contactpercentage}%)");
        }
    }

    if (extension_loaded('gd'))
    {
        // Incidents by product chart
        $data = implode('|',$productlist);
        $keys = array_keys($productlist);
        $legends = implode('|', $productlegends);
        $title = urlencode('Incidents by Product');
        //$data="1,2,3";
        echo "<div style='text-align:center;'>";
        echo "<img src='chart.php?type=pie&data=$data&legends=$legends&title=$title' />";
        echo "</div>";

        if ($mode=='site')
        {
            // Incidents by contacts chart
            $data = implode('|',$contactlist);
            $keys = array_keys($contactlist);
            $legends = implode('|', $contactlegends);
            $title = urlencode('Incidents by Contact');
            //$data="1,2,3";
            echo "<div style='text-align:center;'>";
            echo "<img src='chart.php?type=pie&data=$data&legends=$legends&title=$title' />";
            echo "</div>";
        }

        // Escalation chart
        $countinternalincidents = ($countincidents - $countextincidents);
        $externalpercent = number_format(($countextincidents / $countincidents * 100),1);
        $internalpercent = number_format(($countinternalincidents / $countincidents * 100),1);
        $data = "$countinternalincidents|$countextincidents";
        $keys = "a|b";
        $legends = "Not Escalated ({$internalpercent}%)|Escalated ({$externalpercent}%)";
        $title = urlencode('Incidents by Escalation');
        echo "<div style='text-align:center;'>";
        echo "<img src='chart.php?type=pie&data=$data&legends=$legends&title=$title' />";
        echo "</div>";

        // SLA chart
        $countslamet = ($countincidents - $countslaexceeded);
        $metpercent = number_format(($countslamet / $countincidents * 100),1);
        $exceededpercent = number_format(($countslaexceeded / $countincidents * 100),1);
        $data = "$countslamet|$countslaexceeded";
        $keys = "a|b";
        $legends = "SLA Met ({$metpercent}%)|SLA Exceeded ({$exceededpercent}%)";
        $title = urlencode('Incident Service Level Performance');
        echo "<div style='text-align:center;'>";
        echo "<img src='chart.php?type=pie&data=$data&legends=$legends&title=$title' />";
        echo "</div>";


    }
}

include('htmlfooter.inc.php');
?>