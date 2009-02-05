<?php
// browse_journal.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional!   4Nov05

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 22; // administrate
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

$title = $strBrowseJournal;

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External variables
$offset = cleanvar($_REQUEST['offset']);
$perpage = cleanvar($_REQUEST['perpage']);
$search_string = cleanvar($_REQUEST['search_string']);
$type = cleanvar($_REQUEST['type']);
$sort = cleanvar($_REQUEST['sort']);
$order = cleanvar($_REQUEST['order']);

if (empty($search_string)) $search_string='a';

include ('./inc/htmlheader.inc.php');
echo "<h2>{$title}</h2>";

if (empty($perpage)) $perpage = 50;
if ($offset=='') $offset=0;

$sql = "SELECT * FROM `{$dbJournal}` ";
if (!empty($type)) $sql .= "WHERE journaltype='{$type}' ";
// Create SQL for Sorting
if (!empty($sort))
{
    if ($order=='a' OR $order=='ASC' OR $order='') $sortorder = "ASC";
    else $sortorder = "DESC";
    switch ($sort)
    {
        case 'userid': $sql .= " ORDER BY userid $sortorder"; break;
        case 'timestamp': $sql .= " ORDER BY timestamp $sortorder"; break;
        case 'refid': $sql .= " ORDER BY c.surname $sortorder, c.forenames $sortorder"; break;
        default:   $sql .= " ORDER BY timestamp DESC"; break;
    }
} else $sql .= " ORDER BY timestamp DESC";
$sql .= " LIMIT $offset, $perpage ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

$journaltype[1] = 'Logon/Logoff';
$journaltype[2] = 'Support Incidents';
$journaltype[3] = 'Sales Incidents';  // Obsolete
$journaltype[4] = 'Sites';
$journaltype[5] = 'Contacts';
$journaltype[6] = 'Admin';
$journaltype[7] = 'User Management';
$journaltype[8] = 'Maintenance';
$journaltype[9] = 'Products';
$journaltype[10] = 'Tasks';
$journaltype[11] = 'Triggers';

$journal_count = mysql_num_rows($result);
if ($journal_count >= 1)
{
    echo "<table align='center'>";
    echo "<tr>";
    echo colheader('userid',$strUser,$sort, $order, $filter);
    echo colheader('timestamp',"{$strTime}/{$strDate}",$sort, $order, $filter);
    echo colheader('event',$strEvent);
    echo colheader('action',$strOperation);
    echo colheader('type',$strType);
    echo "</tr>\n";
    $shade = 0;
    while ($journal = mysql_fetch_object($result))
    {
        // define class for table row shading
        if ($shade) $class = "shade1";
        else $class = "shade2";
        echo "<tr class='$class'>";
        echo "<td>".user_realname($journal->userid,TRUE)."</td>";
        echo "<td>".ldate($CONFIG['dateformat_datetime'], mysqlts2date($journal->timestamp))."</td>";
        echo "<td>{$journal->event}</td>";
        echo "<td>";
        switch ($journal->journaltype)
        {
            case 2: echo "<a href='incident_details.php?id={$journal->refid}' target='_blank'>{$journal->bodytext}</a>"; break;
            case 5: echo "<a href='contact_details.php?id={$journal->refid}' target='_blank'>{$journal->bodytext}</a>"; break;
            default:
                echo "{$journal->bodytext}";
                if (!empty($journal->refid)) echo "(Ref: {$journal->refid})";
                break;
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
    if ($prev > 0) echo "<a href='{$_SERVER['PHP_SELF']}?offset={$prev}'>&lt; {$strPrev}</a>";
    echo "&nbsp;";
    echo "<a href='{$_SERVER['PHP_SELF']}?offset={$next}'>{$strNext} &gt;</a>";
    echo "</p>";
}
else
{
    echo "<p>{$strNoResults}</p>";
}
include ('./inc/htmlfooter.inc.php');
?>
