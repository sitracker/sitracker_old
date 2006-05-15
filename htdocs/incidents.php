<?php
// incidents.php - Main Incidents Queue Display
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional!   31Oct05

$permission=6; // View Incidents
$title='Incidents List';

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$type = cleanvar($_REQUEST['type']);
$user = cleanvar($_REQUEST['user']);
$queue = cleanvar($_REQUEST['queue']);
$sort = cleanvar($_REQUEST['sort']);
$sortorder = cleanvar($_REQUEST['sortorder']);

// Defaults
if (empty($type)) $type='support';
if (empty($user)) $user='current';

$refresh = $_SESSION['incident_refresh'];
include('htmlheader.inc.php');
?>
<script type="text/javascript">
function statusform_submit(user)
{
    URL = "incidents.php?status=" + window.document.statusform.status.options[window.document.statusform.status.selectedIndex].value + "&amp;user=" + user;
    window.confirm(URL);
    window.location.href = URL;
}
</script>
<?php


switch($type)
{
    case 'support':
        // Create SQL for chosen queue
        // If you alter this SQL also update the function user_activeincidents($id)
        if ($user=='current') $user=$sit[2];

        $sql = "SELECT incidents.id, externalid, title, owner, towner, priority, status, siteid, forenames, surname, email, incidents.maintenanceid, ";
        $sql .= "servicelevel, softwareid, lastupdated, timeofnextaction, ";
        $sql .= "(timeofnextaction - $now) AS timetonextaction, opened, ($now - opened) AS duration, closed, (closed - opened) AS duration_closed, type, ";
        $sql .= "($now - lastupdated) AS timesincelastupdate ";
        $sql .= "FROM incidents, contacts, priority WHERE contact=contacts.id AND incidents.priority=priority.id ";
        if ($user!='all') $sql .= "AND (owner='$user' OR towner='$user') ";

        echo "<h2>";
        if ($user!='all') echo user_realname($user) . "'s Incidents: ";
        else echo "Watching All ";

        switch($queue)
        {
            case 1: // Action Needed
                echo "<span style='color: Red'>Action Needed</span>";

                $sql .= "AND (status!='2') ";  // not closed
                // the "1=2" obviously false else expression is to prevent records from showing unless the IF condition is true
                $sql .= "AND ((timeofnextaction > 0 AND timeofnextaction < $now) OR ";
                $sql .= "(IF ((status >= 5 AND status <=8), ($now - lastupdated) > ({$CONFIG['regular_contact_days']} * 86400), 1=2 ) ";  // awaiting
                $sql .= "OR IF (status='1' OR status='3' OR status='4', 1=1 , 1=2) ";  // active, research, left message - show all
                $sql .= ") AND timeofnextaction < $now ) ";
            break;

            case 2: // Waiting
                echo "<span style='color: Green'>Waiting</span>";
                $sql .= "AND (status >= 4 AND status <= 8) ";
            break;

            case 3: // All Open
                echo "<span style='color: Blue'>All Open</span>";
                $sql .= "AND status!='2' ";
            break;

            case 4: // All Closed
                echo "<span style='color: Gray'>All Closed</span>";
                $sql .= "AND status='2' ";
            break;

            default:
                throw_error("Error invalid queue on query string:",$queue);
            break;
        }
        echo "</h2>\n";

        // Create SQL for Sorting
        switch($sort)
        {
            case 'id': $sql .= " ORDER BY id $sortorder"; break;
            case 'title': $sql .= " ORDER BY title $sortorder"; break;
            case 'contact': $sql .= " ORDER BY contacts.surname $sortorder, contacts.forenames $sortorder"; break;
            case 'priority': $sql .=  " ORDER BY priority $sortorder, lastupdated ASC"; break;
            case 'status': $sql .= " ORDER BY status $sortorder"; break;
            case 'lastupdated': $sql .= " ORDER BY lastupdated $sortorder"; break;
            case 'duration': $sql .= " ORDER BY duration $sortorder"; break;
            case 'nextaction': $sql .= " ORDER BY timetonextaction $sortorder"; break;
            default:   $sql .= " ORDER BY priority DESC, lastupdated ASC"; break;
        }

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $rowcount = mysql_num_rows($result);

        // Toggle Sorting Order
        if ($sortorder=='ASC')  { $newsortorder='DESC'; }
        else { $newsortorder='ASC'; }

        // build querystring for hyperlinks
        $querystring = "?user=$user&amp;queue=$queue&amp;type=$type&amp;";

        // show drop down of incident status
        echo "<form action='{$_SERVER['PHP_SELF']}' style='text-align: center;'>";
        echo "Queue: <select class='dropdown' name='queue' onchange='window.location.href=this.options[this.selectedIndex].value'>\n";
        echo "<option ";
        if ($queue == 1) echo "selected='selected' ";
        echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;type=$type&amp;queue=1'>Action Needed</option>\n";
        echo "<option ";
        if ($queue == 2) echo "selected='selected' ";
        echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;type=$type&amp;queue=2'>Waiting</option>\n";
        echo "<option ";
        if ($queue == 3) echo "selected='selected' ";
        echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;type=$type&amp;queue=3'>All Open</option>\n";
        if ($user!='all')
        {
            echo "<option ";
            if ($queue == 4) echo "selected='selected' ";
            echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;type=$type&amp;queue=4'>All Closed</option>\n";
        }
        echo "</select>\n";
        echo "</form>";

        if ($user=='all') echo "<p align='center'>There are <strong>{$rowcount}</strong> incidents in this list.</p>";
        else echo "<br />";

        // Print message if no incidents were listed
        if (mysql_num_rows($result) >= 1)
        {
            // Incidents Table
            include('incidents_table.inc.php');
        }
        else echo "<h5>No incidents in this queue</h5>";

        if ($user=='all') echo "<p align='center'>There are <strong>{$rowcount}</strong> incidents in this list.</p>";


        // *********************************************************
        // EXPERTISE QUEUE
        // ***
        if ($user=='current') $user=$sit[2];
        $softsql = "SELECT * FROM usersoftware WHERE userid='$user' ";
        $softresult = mysql_query($softsql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        $softcount = mysql_num_rows($softresult);

        if ($softcount >= 1)
        {
            // list expertise queus
            while ($software = mysql_fetch_object($softresult))
            {
                $expertise[]=$software->softwareid;
            }

            $incsql .= "(";
            for ($i = 0; $i < $softcount; $i++)
            {
                $incsql .= "softwareid='{$expertise[$i]}'";
                if ($i < ($softcount-1)) $incsql .= " OR ";
            }
            $incsql .= ")";

            // Create SQL for chosen queue
            $sql = "SELECT incidents.id, externalid, title, owner, towner, priority, status, siteid, forenames, surname, incidents.maintenanceid, servicelevel, softwareid, lastupdated, timeofnextaction, ";
            $sql .= "(timeofnextaction - $now) AS timetonextaction, opened, ($now - opened) AS duration, closed, (closed - opened) AS duration_closed, type, ";
            $sql .= "($now - lastupdated) AS timesincelastupdate ";
            $sql .= "FROM incidents, contacts, priority WHERE contact=contacts.id AND incidents.priority=priority.id ";
            $sql .= "AND owner!='$user' AND towner!='$user' ";
            $sql .= "AND $incsql ";

            // echo "queue sql = $incsql ;";

            //   $sql .= "AND

            switch($queue)
            {
                case 1: // Action Needed
                    echo "<h2>Other Incidents: <span style='color: Red'>Action Needed</span></h2>\n";
                    $sql .= "AND (status!='2') ";  // not closed
                    // the "1=2" obviously false else expression is to prevent records from showing unless the IF condition is true
                    $sql .= "AND ((timeofnextaction > 0 AND timeofnextaction < $now) OR ";
                    $sql .= "(IF ((status >= 5 AND status <=8), ($now - lastupdated) > ({$CONFIG['regular_contact_days']} * 86400), 1=2 ) ";  // awaiting
                    $sql .= "OR IF (status='1' OR status='3' OR status='4', 1=1 , 1=2) ";  // active, research, left message - show all
                    $sql .= ") AND timeofnextaction < $now ) ";
                    // outstanding
                break;

                case 2: // Waiting
                    echo "<h2>Other Incidents: <span style='color: Green'>Waiting</span></h2>\n";
                    $sql .= "AND (status >= 4 AND status <= 8) ";
                break;

                case 3: // All Open
                    echo "<h2>Other Incidents: <span style='color: Blue'>All Open</span></h2>\n";
                    $sql .= "AND status!='2' ";
                break;

                case 4: // All Closed
                    echo "<h2>Other Incidents: <span style='color: Gray'>All Closed</span></h2>\n";
                    $sql .= "AND status='2' ";
                break;

                default:
                    trigger_error("Invalid queue ($queue) on query string",E_USER_NOTICE);
                break;
            }

            // Create SQL for Sorting
            switch($sort)
            {
                case 'id': $sql .= " ORDER BY id $sortorder"; break;
                case 'title': $sql .= " ORDER BY title $sortorder"; break;
                case 'contact': $sql .= " ORDER BY contacts.surname $sortorder, contacts.forenames $sortorder"; break;
                case 'priority': $sql .=  " ORDER BY priority $sortorder, lastupdated ASC"; break;
                case 'status': $sql .= " ORDER BY status $sortorder"; break;
                case 'lastupdated': $sql .= " ORDER BY lastupdated $sortorder"; break;
                case 'duration': $sql .= " ORDER BY duration $sortorder"; break;
                case 'nextaction': $sql .= " ORDER BY timetonextaction $sortorder"; break;
                default:   $sql .= " ORDER BY priority DESC, lastupdated ASC"; break;
            }
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $rowcount = mysql_num_rows($result);

            // expertise incident listing goes here
            include('incidents_table.inc.php');

                // end of expertise queue
                // ***
            }
        }
        include('htmlfooter.inc.php');
?>
