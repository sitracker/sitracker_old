<?php
// browse_journal.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional!   4Nov05

$permission=22; // administrate
require('db_connect.inc.php');
require('functions.inc.php');
$title="Browse Journal";
// This page requires authentication
require('auth.inc.php');

// External variables
$offset = cleanvar($_REQUEST['offset']);
$perpage = cleanvar($_REQUEST['perpage']);
$search_string = cleanvar($_REQUEST['search_string']);
$type = cleanvar($_REQUEST['type']);
$sort = cleanvar($_REQUEST['sort']);
$order = cleanvar($_REQUEST['order']);

if (empty($search_string)) $search_string='a';

include('htmlheader.inc.php');
echo "<h2>{$title}</h2>";

if (empty($perpage)) $perpage = 50;
if ($offset=='') $offset=0;

$sql = "SELECT * FROM journal ";
if (!empty($type)) $sql .= "WHERE journaltype='{$type}' ";
// Create SQL for Sorting
if (!empty($sort))
{
    if ($order=='a' OR $order=='ASC' OR $order='') $sortorder = "ASC";
    else $sortorder = "DESC";
    switch($sort)
    {
        case 'userid': $sql .= " ORDER BY userid $sortorder"; break;
        case 'timestamp': $sql .= " ORDER BY timestamp $sortorder"; break;
        case 'refid': $sql .= " ORDER BY contacts.surname $sortorder, contacts.forenames $sortorder"; break;
        default:   $sql .= " ORDER BY timestamp DESC"; break;
    }
}
$sql .= " LIMIT $offset, $perpage ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

$journaltype[1]='Logon/Logoff';
$journaltype[2]='Support Incidents';
$journaltype[3]='Sales Incidents';  // Obsolete
$journaltype[4]='Sites';
$journaltype[5]='Contacts';
$journaltype[6]='Admin';
$journaltype[7]='User Management';
$journaltype[8]='Maintenance';
$journaltype[9]='Products';
$journaltype[10]='Tasks';

$journal_count = mysql_num_rows($result);
if ($journal_count >= 1)
{
    echo "<table align='center'>";
    echo "<tr>";
    echo colheader('userid','User',$sort, $order, $filter);
    echo colheader('timestamp','Time/Date',$sort, $order, $filter);
    echo colheader('event','Event');
    echo colheader('action','Action');
    echo colheader('type','Type');
    echo "</tr>\n";
    $shade = 0;
    while ($journal = mysql_fetch_object($result))
    {
        // define class for table row shading
        if ($shade) $class = "shade1";
        else $class = "shade2";
        echo "<tr class='$class'>";
        echo "<td>".user_realname($journal->userid,TRUE)."</td>";
        echo "<td>".date($CONFIG['dateformat_datetime'], mysqlts2date($journal->timestamp))."</td>";
        echo "<td>{$journal->event}</td>";
        echo "<td>";
        switch ($journal->journaltype)
        {
            case 2: echo "<a href='incident_details.php?id={$journal->refid}' target='_blank'>{$journal->bodytext}</a>"; break;
            case 5: echo "<a href='contact_details.php?id={$journal->refid}' target='_blank'>{$journal->bodytext}</a>"; break;
            default: echo "{$journal->bodytext} (Ref: {$journal->refid})"; break;
        }
        echo "</td>";
        echo "<td><a href='{$_SERVER['PHP_SELF']}?type={$journal->journaltype}'>{$journaltype[$journal->journaltype]}</a></td>";
        echo "</tr>\n";
        // invert shade
        if ($shade == 1) $shade = 0;
        else $shade = 1;
    }
    echo "</table>\n";
    $prev=$offset-$perpage;
    $next=$offset+$perpage;
    echo "<p align='center'>";
    if ($prev > 0) echo "<a href='{$_SERVER['PHP_SELF']}?offset={$prev}'>&lt;</a>";
    echo "&nbsp;";
    echo "<a href='{$_SERVER['PHP_SELF']}?offset={$next}'>&gt;</a>";
    echo "</p>";
}
else
{
    echo "<p>No matching journal entries</p>";
}
include('htmlfooter.inc.php');
?>