<?php
// browse_feedback.php - View a list of feedback
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// by Ivan Lucas, June 2004

$permission=17; // Edit Email Template

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

$title = "Browse Feedback";
include('htmlheader.inc.php');

// External variables
$formid = cleanvar($_REQUEST['id']);
$sort = cleanvar($_REQUEST['sort']);
$order = cleanvar($_REQUEST['order']);

if (empty($formid)) $formid=1;

$sql  = "SELECT *, feedbackrespondents.id AS respid FROM feedbackrespondents, feedbackforms ";
$sql .= "WHERE feedbackrespondents.formid=feedbackforms.id ";
//  AND completed='no'
if (!empty($formid)) $sql .= "AND formid='$formid'";
//, feedbackforms ";
//$sql .= "WHERE feedbackrespondents.formid=feedbackforms.id ";
//$sql .= "AND formid='{$formid}' ";
//$sql .= "AND completed='no' ";
//$sql .= "ORDER BY respondent, respondentref";

if ($sort=='email') $sql .= "ORDER BY email ";
if ($sort=='date') $sql .= "ORDER BY created ";
if ($sort=='respondent') $sql .= "ORDER BY respondent ";
if ($sort=='a') $sql .= "ASC";
elseif ($_REQUEST['order']=='d') $sql .= "DESC";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

$countrows = mysql_num_rows($result);

if (!empty($formid))
{
    echo "<h3>Responses to Feedback Form: $formid</h3>";
    echo "<p align='center'><a href='edit_feedback_form.php?id={$formid}'>Edit this form</a></p>";
}
else echo "<h3>Responses to all Feedback Forms</h3>";

if ($countrows >= 1)
{
    echo "<table summary='feedback forms' width='95%' align='center'>";
    echo "<tr>";
    echo "<th>Created ";
    if ($sort=='date' AND $order=='a') echo "<img src='/images/sort_a.gif' border='0' /> ";
    else echo "<a href='?sort=date&amp;order=a' title='Sort by Date, Ascending'><img src='/images/sort_a_off.gif' border='0' /></a> ";
    if ($sort=='date' AND $order=='d') echo "<img src='/images/sort_d.gif' border='0' /> ";
    else echo "<a href='?sort=date&amp;order=d' title='Sort by Date, Descending'><img src='/images/sort_d_off.gif' border='0' /></a> ";
    echo "</th>";
    echo "<th>Respondent ";
    if ($sort=='respondent' AND $order=='a') echo "<img src='/images/sort_a.gif' border='0' /> ";
    else echo "<a href='?sort=respondent&amp;order=a' title='Sort by Respondent, Ascending'><img src='/images/sort_a_off.gif' border='0' /></a> ";
    if ($sort=='respondent' AND $order=='d') echo "<img src='/images/sort_d.gif' border='0' /> ";
    else echo "<a href='?sort=respondent&amp;order=d' title='Sort by Respondent, Descending'><img src='/images/sort_d_off.gif' border='0' /></a> ";
    echo "</th>";
    echo "<th>Response Reference</th><th>URL</th>";
    echo "<th>Action</th>";
    echo "</tr>\n";
    while ($resp = mysql_fetch_object($result))
    {
        $respondentarr=explode('-',$resp->respondent);
        $responserefarr=explode('-',$resp->responseref);

        $hashtext=urlencode($resp->contactid)."&&".urlencode($resp->incidentid);
        // $hashcode=urlencode(trim(base64_encode(gzcompress(str_rot13($hashtext)))));
        $hashcode4=str_rot13($hashtext);
        $hashcode3=gzcompress($hashcode4);
        $hashcode2=base64_encode($hashcode3);
        $hashcode1=trim($hashcode2);
        $hashcode=urlencode($hashcode1);
        echo "<tr>";
        echo "<td>".date($CONFIG['dateformat_datetime'],mysqlts2date($resp->created))."</td>";
        echo "<td><a href='contact_details.php?id={$resp->contactid}' title='{$resp->email}'>".contact_realname($resp->contactid)."</a></td>";
        echo "<td><a href=\"javascript:incident_details_window('{$resp->incidentid}','incident{$resp->incidentid}')\">Incident [{$resp->incidentid}]</a></td>";
        $url = "pub/feedback.php?id={$resp->formid}&amp;ax={$hashcode}";
        if ($resp->multi=='yes') $url .= "&amp;rr=1";

        echo "<td>";
        if ($resp->completed=='no') echo "<a href='$url' title='$url' target='_blank'>URL</a>";
        else echo "-";
        // echo "<br />\n(".str_rot13(gzuncompress(base64_decode(urldecode($hashcode)))).")";
        echo "</td>";
        echo "<td>";
        $eurl=urlencode($url);
        $eref=urlencode($resp->responseref);
        if ($resp->completed=='no')
        {
            if ($resp->remind<1) echo "<a href='formactions.php?action=remind&amp;id={$resp->respid}&amp;url={$eurl}&amp;ref={$eref}' title='Send a reminder by email'>Remind</a>";
            elseif ($resp->remind==1) echo "<a href='formactions.php?action=remind&amp;id={$resp->respid}&amp;url={$eurl}&amp;ref={$eref}' title='Send a Second reminder by email'>Remind Again</a>";
            elseif ($resp->remind==2) echo "<a href='formactions.php?action=callremind&amp;id={$resp->respid}&amp;url={$eurl}&amp;ref={$eref}' title='Send a Third reminder by phone call, click here when its done'>Remind by Phone</a>";
            else echo "<strike title='Already sent 3 reminders'>Remind</strike>";
            echo " &bull; ";
            echo "<a href='formactions.php?action=delete&amp;id={$resp->respid}' title='Remove this form'>Delete</a>";
        }
        else
        {
            echo "View results";
        }
        echo "</td>";
        echo "</tr>\n";
    }

    echo "</table>\n";
}
else
{
    echo "<p class='error' align='center'>No feedback responses</p>";
}
include('htmlfooter.inc.php');
?>