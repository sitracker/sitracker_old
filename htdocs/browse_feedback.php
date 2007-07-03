<?php
// browse_feedback.php - View a list of feedback
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// by Ivan Lucas, June 2004

$permission=51; // View Feedback

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

$title = "Browse Feedback";
include('htmlheader.inc.php');

// External variables
$formid = cleanvar($_REQUEST['id']);
$responseid = cleanvar($_REQUEST['responseid']);
$sort = cleanvar($_REQUEST['sort']);
$order = cleanvar($_REQUEST['order']);
$mode = cleanvar($_REQUEST['mode']);
$completed = cleanvar($_REQUEST['completed']);

switch($mode)
{
    case 'viewresponse':
        echo "<h2>Feedback</h2>";
        $sql = "SELECT * FROM feedbackrespondents WHERE id='{$responseid}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        $response = mysql_fetch_object($result);
        echo "<table class='vertical' align='center'>";
        echo "<tr><th>Contact</th><td>{$response->contactid} - ".contact_realname($response->contactid)."</td></tr>\n";
        echo "<tr><th>Incident</th><td><a href=\"javascript:incident_details_window('{$response->incidentid}','incident{$response->incidentid}')\">{$response->incidentid} - ".incident_title($response->incidentid)."</a></td>\n";
        echo "<tr><th>Form</th><td>{$response->formid}</td>\n";
        echo "<tr><th>Date</th><td>{$response->created}</td>\n";
        echo "<tr><th>Completed</th><td>{$response->completed}</td>\n";
        echo "</table>\n";

        echo "<h3>Response</h3>";
        $totalresult=0;
        $numquestions=0;
        $qsql = "SELECT * FROM feedbackquestions WHERE formid='{$response->formid}' AND type='rating' ORDER BY taborder";
        $qresult = mysql_query($qsql);
        if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);

        if (mysql_num_rows($qresult) >= 1)
        {
            $html .= "<table align='center' class='vertical'>";
            while ($qrow = mysql_fetch_object($qresult))
            {
                $numquestions++;
                $html .= "<tr><th>Q{$qrow->taborder}: {$qrow->question}</th>";
                $sql = "SELECT * FROM feedbackrespondents, incidents, users, feedbackresults ";
                $sql .= "WHERE feedbackrespondents.incidentid=incidents.id ";
                $sql .= "AND incidents.owner=users.id ";
                $sql .= "AND feedbackrespondents.id=feedbackresults.respondentid ";
                $sql .= "AND feedbackresults.questionid='$qrow->id' ";
                $sql .= "AND feedbackrespondents.id='$responseid' ";
                $sql .= "AND feedbackrespondents.completed = 'yes' \n";
                $sql .= "ORDER BY incidents.owner, incidents.id";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
                $numresults=0;
                $cumul=0;
                $percent=0;
                $average=0;
                while ($row = mysql_fetch_object($result))
                {
                    if (!empty($row->result))
                    {
                        $cumul+=$row->result;
                        $numresults++;
                    }
                }
                if ($numresults>0) $average=number_format(($cumul/$numresults), 2);
                $percent =number_format((($average / $CONFIG['feedback_max_score']) * 100), 0);
                $totalresult+=$average;
                $html .= "<td>{$average}</td></tr>";
                // <strong>({$percent}%)</strong><br />";
            }
            $html .= "</table>\n";
            $total_average=number_format($totalresult/$numquestions,2);
            $total_percent=number_format((($total_average / $CONFIG['feedback_max_score']) * 100), 0);
            $html .= "<p align='center'>Positivity: {$total_average} <strong>({$total_percent}%)</strong></p>";
            $surveys+=$numresults;
            $html .= "<hr />\n";

            //if ($total_average>0)
            echo $html;
            echo "\n\n\n<!-- $surveys -->\n\n\n";
        }
        else echo "<p class='error'>No response found</p>";

        echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}'>Back to list of feedback</p>";
    break;

    default:
    if (empty($formid) AND !empty($CONFIG['feedback_form'])) $formid=$CONFIG['feedback_form'];
    else $formid=1;

    $sql  = "SELECT *, feedbackrespondents.id AS respid FROM feedbackrespondents, feedbackforms ";
    $sql .= "WHERE feedbackrespondents.formid=feedbackforms.id ";
    if ($completed=='no') $sql .= "AND completed='no' ";
    else $sql .= "AND completed='yes' ";
    if (!empty($formid)) $sql .= "AND formid='$formid'";
    //, feedbackforms ";
    //$sql .= "WHERE feedbackrespondents.formid=feedbackforms.id ";
    //$sql .= "AND formid='{$formid}' ";
    //$sql .= "AND completed='no' ";
    //$sql .= "ORDER BY respondent, respondentref";


    if ($order=='a' OR $order=='ASC' OR $order='') $sortorder = "ASC";
    else $sortorder = "DESC";
    switch($sort)
    {
        case 'created': $sql .= " ORDER BY created $sortorder"; break;
        case 'contactid': $sql .= " ORDER BY contactid $sortorder"; break;
        case 'incidentid': $sql .= " ORDER BY incidentid $sortorder"; break;
        default:   $sql .= " ORDER BY created DESC"; break;
    }
    //if ($sort=='email') $sql .= "ORDER BY email ";
    //if ($sort=='date') $sql .= "ORDER BY created ";
    //if ($sort=='respondent') $sql .= "ORDER BY respondent ";
    //if ($sort=='a') $sql .= "ASC";
    //elseif ($_REQUEST['order']=='d') $sql .= "DESC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    $countrows = mysql_num_rows($result);

    if (!empty($formid))
    {
        if ($completed=='no') echo "<h3>Feedback requested but not yet received for form: $formid</h3>";
        else echo "<h3>Responses to Feedback Form: $formid</h3>";
        echo "<p align='center'><a href='edit_feedback_form.php?formid={$formid}'>Edit this form</a></p>";
    }
    else echo "<h3>Responses to all Feedback Forms</h3>";

    if ($countrows >= 1)
    {
        echo "<table summary='feedback forms' width='95%' align='center'>";
        echo "<tr>";
        echo colheader('created','Feedback Requested',$sort, $order, $filter);
        echo colheader('contactid','Contact',$sort, $order, $filter);
        echo colheader('incidentid','Incident',$sort, $order, $filter);
        echo "<th>Action</th>";
        echo "</tr>\n";
        $shade='shade1';
        while ($resp = mysql_fetch_object($result))
        {
            $respondentarr=explode('-',$resp->respondent);
            $responserefarr=explode('-',$resp->responseref);

            $hashtext=urlencode($resp->formid)."&&".urlencode($resp->contactid)."&&".urlencode($resp->incidentid);
            // $hashcode=urlencode(trim(base64_encode(gzcompress(str_rot13($hashtext)))));
            $hashcode4=str_rot13($hashtext);
            $hashcode3=gzcompress($hashcode4);
            $hashcode2=base64_encode($hashcode3);
            $hashcode1=trim($hashcode2);
            $hashcode=urlencode($hashcode1);
            echo "<tr class='$shade'>";
            echo "<td>".date($CONFIG['dateformat_datetime'],mysqlts2date($resp->created))."</td>";
            echo "<td><a href='contact_details.php?id={$resp->contactid}' title='{$resp->email}'>".contact_realname($resp->contactid)."</a></td>";
            echo "<td><a href=\"javascript:incident_details_window('{$resp->incidentid}','incident{$resp->incidentid}')\">Incident [{$resp->incidentid}]</a> - ";
            echo incident_title($resp->incidentid)."</td>";
            $url = "feedback.php?ax={$hashcode}";
            if ($resp->multi=='yes') $url .= "&amp;rr=1";

            echo "<td>";
            if ($resp->completed=='no') echo "<a href='$url' title='$url' target='_blank'>URL</a>";
            $eurl=urlencode($url);
            $eref=urlencode($resp->responseref);
            if ($resp->completed=='no')
            {
                //if ($resp->remind<1) echo "<a href='formactions.php?action=remind&amp;id={$resp->respid}&amp;url={$eurl}&amp;ref={$eref}' title='Send a reminder by email'>Remind</a>";
                //elseif ($resp->remind==1) echo "<a href='formactions.php?action=remind&amp;id={$resp->respid}&amp;url={$eurl}&amp;ref={$eref}' title='Send a Second reminder by email'>Remind Again</a>";
                //elseif ($resp->remind==2) echo "<a href='formactions.php?action=callremind&amp;id={$resp->respid}&amp;url={$eurl}&amp;ref={$eref}' title='Send a Third reminder by phone call, click here when its done'>Remind by Phone</a>";
                //else echo "<strike title='Already sent 3 reminders'>Remind</strike>";
                //echo " &bull; ";
                //echo "<a href='formactions.php?action=delete&amp;id={$resp->respid}' title='Remove this form'>Delete</a>";
            }
            else
            {
                echo "<a href='{$_SERVER['PHP_SELF']}?mode=viewresponse&amp;responseid={$resp->respid}'>View response</a>";
            }
            echo "</td>";
            echo "</tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        echo "</table>\n";
    }
    else
    {
        echo "<p class='error' align='center'>No feedback responses</p>";
    }
    if ($completed=='no')
    {
        $sql = "SELECT COUNT(id) FROM feedbackrespondents WHERE formid='{$formid}' AND completed='yes'";
        $result = mysql_query($sql);
        list($completedforms) = mysql_fetch_row($result);
        if ($completedforms > 0) echo "<p align='center'>There are <a href='{$_SERVER['PHP_SELF']}'>{$completedforms} feedback forms</a> that have been returned already.</p>";
    }
    else
    {
        $sql = "SELECT COUNT(id) FROM feedbackrespondents WHERE formid='{$formid}' AND completed='no'";
        $result = mysql_query($sql);
        list($waiting) = mysql_fetch_row($result);
        if ($waiting > 0) echo "<p align='center'>There are <a href='{$_SERVER['PHP_SELF']}?completed=no'>{$waiting} feedback forms</a> that have not been returned yet.</p>";
    }
}
include('htmlfooter.inc.php');
?>
