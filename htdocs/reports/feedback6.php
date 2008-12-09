<?php
// feedback6.php - Feedback scores by software
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Report Type: Feedback

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('../set_include_path.inc.php');
$permission = 37; // Run Reports

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');

$formid = $CONFIG['feedback_form'];

echo "<div style='margin: 20px'>";
echo "<h2><a href='/reports/feedback.php'>Feedback</a> Scores: By Skill</h2>";
echo "<p>This report shows average customer responses and a percentage figure indicating the overall positivity of sites regarding ";
echo "incidents logged:</p>";

$rcount = 1;

$msql = "SELECT *,  \n";
$msql .= "fr.id AS reportid, \n";
$msql .= "s.id AS softwareid, s.name AS softwarename ";
$msql .= "FROM `{$dbFeedbackRespondents}` AS fr, `{$dbIncidents}` AS i, `{$dbSoftware}` AS s ";
$msql .= "WHERE fr.incidentid = i.id \n";
$msql .= "AND i.softwareid = s.id ";
$msql .= "AND fr.incidentid > 0 \n";
$msql .= "ORDER BY s.name, i.id ASC \n";
$mresult = mysql_query($msql);
if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
while ($mrow = mysql_fetch_object($mresult))
{
    $totalresult = 0;
    $numquestions = 0;
    $html = "<h3><a href='#?id={$mrow->softwareid}' title='Jump to software'>{$mrow->softwarename}</a></h3>";
    $qsql = "SELECT * FROM `{$dbFeedbackQuestions}` WHERE formid='{$formid}' AND type='rating' ORDER BY taborder";
    $qresult = mysql_query($qsql);
    if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
    while ($qrow = mysql_fetch_object($qresult))
    {
        $numquestions++;
        $html .= "Q{$qrow->taborder}: {$qrow->question} &nbsp;";
        $sql = "SELECT * FROM `{$dbFeedbackRespondents}` AS fr, `{$dbIncidents}` AS i, `{$dbUsers}` AS u, `{$dbFeedbackResults}` AS r ";
        $sql .= "WHERE fr.incidentid = i.id ";
        $sql .= "AND i.owner = u.id ";
        $sql .= "AND fr.id = r.respondentid ";
        $sql .= "AND r.questionid = '$qrow->id' ";
        $sql .= "AND fr.id = '$mrow->reportid' ";
        $sql .= "ORDER BY i.owner, i.id";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
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
                // echo "===== Result: {$row->result}<br />";
            }
        }
        if ($numresults>0) $average=($cumul/$numresults);

        $percent =number_format((($average / $CONFIG['feedback_max_score']) * 100), 0);
        $totalresult+=$average;
        $qanswer[$qrow->taborder]+=$average;
        $qavgavg=$qanswer[$qrow->taborder];
        $rowresult=number_format(($qavgavg/$rcount),2);
        $rowpercent =number_format((($rowresult / $CONFIG['feedback_max_score']) * 100), 0);
        $totalrowresult=$rowresult;
        ## {$average} <strong>({$percent}%)</strong>   ...
        $html .= "($rowresult) <strong>({$rowpercent}%)</strong><br />";
    }
    $total_average=number_format($totalresult/$numquestions,2);
    $total_rowaverage=number_format(($totalrowresult/$numquestions)*10,2);
    $total_percent=number_format((($total_average / $CONFIG['feedback_max_score']) * 100), 0);

    $qcount = (count($qanswer)-1);
    for ($i=1;$i<=$qcount;$i++)
    {
        $qtotal+=$qanswer[$i];
    }
    $qtotal = number_format((($qtotal / $qcount) / $rcount),2);
    $qtotal_percent=number_format((($qtotal / $CONFIG['feedback_max_score']) * 100), 0);

    $html .= "<p>Positivity: {$qtotal} <strong>({$qtotal_percent}%)</strong>, after $rcount survey(s).</p>";
    ## ... ($rcount -- $total_rowaverage)
    $html .= "<hr />\n";

    if ($total_average > 0) $rcount++;


    if ($mrow->softwareid!=$prevprod AND $prevprod!='')
    {
        if ($total_average > 0)
        {
            echo $html;
        }
        $rcount=1;
        unset($qavgavg);
        unset($qanswer);
        unset($dbg);
    }
    $prevprod = $mrow->softwareid;
}


echo "</div>\n";
include ('htmlfooter.inc.php');

?>
