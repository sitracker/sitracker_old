<?php

function users_incidents()
{
        global $user;
        global $sit;
        global $now;
        global $CONFIG;
        $user = "current";
        // Create SQL for chosen queue
        // If you alter this SQL also update the function user_activeincidents($id)
        if ($user=='current') $user=$sit[2];
        // If the user is passed as a username lookup the userid
        if (!is_number($user) AND $user!='current' AND $user!='all')
        {
            $usql = "SELECT id FROM users WHERE username='$user' LIMIT 1";
            $uresult = mysql_query($usql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            if (mysql_num_rows($uresult) >= 1) list($user) = mysql_fetch_row($uresult);
            else $user=$sit[2]; // force to current user if username not found
        }
        $sql = $selectsql . "WHERE contact=contacts.id AND incidents.priority=priority.id ";
        if ($user!='all') $sql .= "AND (owner='$user' OR towner='$user') ";
        echo "<h4>";
        if ($user!='all') echo user_realname($user) . "'s Incidents: ";
        else echo "Watching All ";
        $queue = 1;
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
                trigger_error("Invalid queue ($queue) on query string",E_USER_NOTICE);
            break;
        }
        echo "</h4>\n";

        $selectsql = "SELECT incidents.id, externalid, title, owner, towner, priority, status, siteid, forenames, surname, email, incidents.maintenanceid, ";
        $selectsql .= "servicelevel, softwareid, lastupdated, timeofnextaction, ";
        $selectsql .= "(timeofnextaction - $now) AS timetonextaction, opened, ($now - opened) AS duration, closed, (closed - opened) AS duration_closed, type, ";
        $selectsql .= "($now - lastupdated) AS timesincelastupdate ";
        $selectsql .= "FROM incidents, contacts, priority ";
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
        $sql = $selectsql.$sql;
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $rowcount = mysql_num_rows($result);
        // Toggle Sorting Order
        if ($sortorder=='ASC')  { $newsortorder='DESC'; }
        else { $newsortorder='ASC'; }

        // build querystring for hyperlinks
        $querystring = "?user=$user&amp;queue=$queue&amp;type=$type&amp;";

        if ($user=='all') echo "<p align='center'>There are <strong>{$rowcount}</strong> incidents in this list.</p>";
        else echo "<br />";
        $mode = "min";
        // Print message if no incidents were listed
        if (mysql_num_rows($result) >= 1)
        {
            // Incidents Table
            include('incidents_table.inc.php');
        }
}

?>