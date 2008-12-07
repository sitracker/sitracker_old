<?php
// billable_incidents.php - Report for billing incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:  Paul Heaney Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('../set_include_path.inc.php');
$permission = 37; // Run Reports

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$startdate = strtotime(cleanvar($_REQUEST['startdate']));
$enddate = strtotime(cleanvar($_REQUEST['enddate']));
$mode = cleanvar($_REQUEST['mode']);
$output = cleanvar($_REQUEST['output']);
if (empty($output)) $output = 'html';

if (empty($mode))
{
    include ('htmlheader.inc.php');

    echo "<h2>{$strBillableIncidentsReport}<h2>";

    echo "<form action='{$_SERVER['PHP_SELF']}' method='post' id='billableincidents'>";
    echo "<table align='center'>";
    echo "<tr><th>{$strStartDate}:</th>";
    echo "<td><input type='text' name='startdate' id='startdate' size='10' /> ";
    echo date_picker('billableincidents.startdate');
    echo "</td></tr>\n";
    echo "<tr><th>{$strEndDate}:</th>";
    echo "<td><input type='text' name='enddate' id='enddate' size='10' /> ";
    echo date_picker('billableincidents.enddate');
    echo "</td></tr>\n";

    echo "</table>";

    echo "<p align='center'><input type='submit' name='runreport' value='{$strRunReport}' /></p>";
    echo "<input type='hidden' name='mode' id='mode' value='report' />";
    echo "</form>";

    include ('htmlfooter.inc.php');
}
elseif ($mode == 'report')
{
    // Loop around all active sites - those with contracts

    // Need a breakdown of incidents so loop though each site and list the incidents

    /*
     SITE (total: x):
        Incident a - c
        Incident b - d
    */

    if ($output == 'html')
    {
        include ('htmlheader.inc.php');
    }

    $sqlsite = "SELECT DISTINCT m.site FROM `{$dbMaintenance}` AS m WHERE expirydate >= {$startdate}";
    $resultsite = mysql_query($sqlsite);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

    if (mysql_num_rows($resultsite) > 0)
    {
        while ($objsite = mysql_fetch_object($resultsite))
        {
            $str = "<p>".site_name($objsite->site)."</p>";

            $used = false;

            $sql = "SELECT i.* FROM `{$GLOBALS['dbIncidents']}` AS i, `{$GLOBALS['dbContacts']}` AS c WHERE c.id = i.contact AND c.siteid = {$objsite->site} ";
            if ($startdate != 0)
            {
                $sql .= "AND closed >= {$startdate} ";
            }

            if ($enedate != 0)
            {
                $sql .= "AND closed <= {$enedate} ";
            }

            $result = mysql_query($sql);
            if (mysql_error())
            {
                trigger_error(mysql_error(),E_USER_WARNING);
                return FALSE;
            }

            $units = 0;

            if (mysql_num_rows($result) > 0)
            {
                while ($obj = mysql_fetch_object($result))
                {
                    $a = make_incident_billing_array($obj->id);

                    if ($a[-1]['totalcustomerperiods'] > 0)
                    {
                        $str .= "{$obj->id} - {$obj->title} {$a[-1]['totalcustomerperiods']}<br />";
                        $used = true;
                    }
                }
            }

            $str .= "<br /><br />";

            if ($used)
            {
                if ($output == 'html')
                {
                    echo $str;
                }
            }
        }
    }

    if ($output == 'html')
    {
        include ('htmlfooter.inc.php');
    }

}

?>