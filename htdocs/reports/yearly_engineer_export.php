<?php
// yearly_enginer_export.php - List the numbers and titles of incidents logged to each engineer in the past year.
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// Notes:
//  Lists incidents that have been logged to specified engineers over the past 12 months
//  Note that this will be inaccurate to a degree because it's only looking at the current owner
//  not the past owners.  ie. it doesn't take into account any reassignments.

// Requested by Rob Shepley, 3 Oct 05

$permission=37; // Run Reports
$title='Yearly Engineer/Incident Report';
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

if (empty($_REQUEST['mode']))
{
    include('htmlheader.inc.php');
    echo "<h2>$title</h2>";
    echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
    echo "<table>";
    echo "<tr><th colspan='2' align='center' class='shade1'>Include</th>";
    echo "<tr><td align='center' colspan='2' class='shade1'>";
    $sql = "SELECT * FROM users WHERE status > 0 ORDER BY username";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    echo "<select name='inc[]' multiple='multiple' size='20'>";
    while ($row = mysql_fetch_object($result))
    {
        echo "<option value='{$row->id}'>$row->realname</option>\n";
    }
    echo "</select>";
    echo "</td>";
    echo "</tr>\n";
    echo "<tr colspan='2'><td>&nbsp;</td></tr>\n";
    echo "<tr colspan='2'><td>&nbsp;</td></tr>\n";
    echo "<tr><td align='right' width='200' class='shade1'><b>Output</b>:</td>";
    echo   "<td width=400 class='shade2'>";
    echo "<select name='output'>";
    echo "<option value='screen'>Screen</option>";
    echo "<option value='csv'>Disk - Comma Seperated (CSV) file</option>";
    echo "</select>";
    echo "</td></tr>";
    echo "</table>";
    echo "<p align='center'>";
    echo "<input type='hidden' name='table1' value='{$_POST['table1']}' />";
    echo "<input type='hidden' name='mode' value='report' />";
    echo "<input type='submit' value='report' />";
    echo "</p>";
    echo "</form>";
    include('htmlfooter.inc.php');
}
elseif ($_REQUEST['mode']=='report')
{
    if (is_array($_POST['exc']) && is_array($_POST['exc'])) $_POST['inc']=array_values(array_diff($_POST['inc'],$_POST['exc']));  // don't include anything excluded
    $includecount=count($_POST['inc']);
    if ($includecount >= 1)
    {
        // $html .= "<strong>Include:</strong><br />";
        $incsql .= "(";
        for ($i = 0; $i < $includecount; $i++)
        {
            // $html .= "{$_POST['inc'][$i]} <br />";
            $incsql .= "users.id={$_POST['inc'][$i]}";
            if ($i < ($includecount-1)) $incsql .= " OR ";
        }
        $incsql .= ")";
    }

    $sql = "SELECT incidents.id AS incid, incidents.title AS title,users.realname AS realname, users.id AS userid, ";
    $sql .= "incidents.opened as opened FROM users, incidents ";
    $sql .= "WHERE users.id=incidents.owner AND incidents.opened > ($now-60*60*24*365.25) ";

    if (empty($incsql)==FALSE OR empty($excsql)==FALSE) $sql .= " AND ";
    if (!empty($incsql)) $sql .= "$incsql";
    if (empty($incsql)==FALSE AND empty($excsql)==FALSE) $sql .= " AND ";
    if (!empty($excsql)) $sql .= "$excsql";

    $sql .= " ORDER BY realname, incid ASC ";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $numrows = mysql_num_rows($result);

    $html .= "<p align='center'>This report is a list of ($numrows) incidents for your selections</p>";
    $html .= "<table width='99%' align='center'>";
    $html .= "<tr class='shade1'><th>Opened</th><th>Incident</th><th>Title</th><th>Engineer</th></tr>";
    $csvfieldheaders .= "opened,id,title,engineer\r\n";
    $rowcount=0;
    while ($row = mysql_fetch_object($result))
    {
        $nicedate=date('d/m/Y',$row->opened);
        $html .= "<tr class='shade2'><td>$nicedate</td><td><a href='/incident_details.php?id={$row->incid}'>{$row->incid}</a></td><td>{$row->title}</td><td>{$row->realname}</td></tr>";
        $csv .="'".$nicedate."', '{$row->incid}','{$row->title}','{$row->realname}'\n";
    }
    $html .= "</table>";

    //  $html .= "<p align='center'>SQL Query used to produce this report:<br /><code>$sql</code></p>\n";

    if ($_POST['output']=='screen')
    {
        include('htmlheader.inc.php');
        echo $html;
        include('htmlheader.inc.php');
    }
    elseif ($_POST['output']=='csv')
    {
        // --- CSV File HTTP Header
        header("Content-type: text/csv\r\n");
        header("Content-disposition-type: attachment\r\n");
        header("Content-disposition: filename=yearly_incidents.csv");
        echo $csvfieldheaders;
        echo $csv;
    }
}
?>
