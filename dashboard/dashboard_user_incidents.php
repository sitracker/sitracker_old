<?php
// dashboard_user_incidents.php - List of users active incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$dashboard_user_incidents_version = 1;

function dashboard_user_incidents($row,$dashboardid)
{
    global $user;
    global $sit;
    global $now;
    global $CONFIG;
    global $iconset;
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


    $queue = 1; //we still need this for the included page so the incidents are coloured correctly
    //the only case we're really interested in
    $sql .= "AND (status!='2') ";  // not closed
    // the "1=2" obviously false else expression is to prevent records from showing unless the IF condition is true
    $sql .= "AND ((timeofnextaction > 0 AND timeofnextaction < $now) OR ";
    $sql .= "(IF ((status >= 5 AND status <=8), ($now - lastupdated) > ({$CONFIG['regular_contact_days']} * 86400), 1=2 ) ";  // awaiting
    $sql .= "OR IF (status='1' OR status='3' OR status='4', 1=1 , 1=2) ";  // active, research, left message - show all
    $sql .= ") AND timeofnextaction < $now ) ";
    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><a href='incidents.php?user=current&amp;queue=1&amp;type=support'>
        <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/support.png' width='16' height='16' alt='' /> ";
    if ($CONFIG['debug']) printf($GLOBALS['strUserIncidents'], user_realname($user,TRUE));
    echo "</a> ({$GLOBALS['strActionNeeded']})</div>";
    echo "<div class='window'>";

    $selectsql = "SELECT i.id, externalid, title, owner, towner, priority, status, siteid, forenames, surname, email, i.maintenanceid, ";
    $selectsql .= "servicelevel, softwareid, lastupdated, timeofnextaction, ";
    $selectsql .= "(timeofnextaction - $now) AS timetonextaction, opened, ($now - opened) AS duration, closed, (closed - opened) AS duration_closed, type, ";
    $selectsql .= "($now - lastupdated) AS timesincelastupdate ";
    $selectsql .= "FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c, `{$dbPriority}` AS p ";
    // Create SQL for Sorting
    switch ($sort)
    {
        case 'id': $sql .= " ORDER BY id $sortorder"; break;
        case 'title': $sql .= " ORDER BY title $sortorder"; break;
        case 'contact': $sql .= " ORDER BY c.surname $sortorder, c.forenames $sortorder"; break;
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
    $mode = "min";
    // Print message if no incidents were listed
    if (mysql_num_rows($result) >= 1)
    {
        // Incidents Table
        $incidents_minimal = true;
        //include ('incidents_table.inc.php');
        $shade='shade1';
        echo "<table style='width: 100%;'>";
        while ($row = mysql_fetch_array($result))
        {
            list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($row['id']);
            $update_body = parse_updatebody($update_body);
            echo "<tr><td class='$shade'>";
            echo "<a href='javascript:incident_details_window({$row['id']})' class='info'>"."{$row['id']} - {$row['title']} for {$row['forenames']}   {$row['surname']}"; // FIXME i18n 'for'
            if (!empty($update_body) AND $update_body!='...') echo "<span>{$update_body}</span>";
            echo "</a></td></tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        echo "</table>";
    }
    else
    {
        echo "<p align='center'>{$GLOBALS['strNoRecords']}</p>";
    }
    echo "</div>";
    echo "</div>";
    //echo "</div>";
}

?>