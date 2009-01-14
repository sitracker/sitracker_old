<?php
// feedback7.php - Feedback scores by incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
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

$formid=$CONFIG['feedback_form'];
$now = time();
echo "<div style='margin: 20px'>";
echo "<h2><a href='/reports/feedback.php'>Feedback</a> Scores: By Incident</h2>";
echo "<p>This report shows average customer responses and a percentage figure indicating the overall positivity of sites regarding ";
echo "incidents logged:</p>";

$rcount = 1;
$survcount = 0;

$msql = "SELECT *,  \n";
$msql .= "rep.id AS reportid, i.id AS incidentid \n";
$msql .= "FROM `{$dbFeedbackReport}` AS rep, `{$dbIncidents}`AS i  WHERE rep.incidentid = i.id \n";
$msql .= "AND rep.incidentid > 0 \n";
$msql .= "ORDER BY i.id ASC \n";
$mresult = mysql_query($msql);
if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
while ($mrow = mysql_fetch_object($mresult))
{
    $totalresult = 0;
    $numquestions = 0;
    $html = "<h3><a href='/incident_details.php?id={$mrow->incidentid}' title='Jump to incident'>{$mrow->incidentid}</a></h3>";
    $qsql = "SELECT * FROM `{$dbFeedbackQuestions}` WHERE formid='{$formid}' AND type='rating' ORDER BY taborder";
    $qresult = mysql_query($qsql);
    if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
    while ($qrow = mysql_fetch_object($qresult))
    {
        $numquestions++;
        $html .= "Q{$qrow->taborder}: {$qrow->question} &nbsp;";
        $sql = "SELECT * FROM `{$dbFeedbackReport}` AS rep, `{$dbIncidents}` AS i, `{$dbUsers}` AS u, `{$dbFeedbackResults}` AS r ";
        $sql .= "WHERE rep.incidentid = i.id ";
        $sql .= "AND i.owner = u.id ";
        $sql .= "AND rep.id = r.respondentid ";
        $sql .= "AND r.questionid = '{$qrow->id}' ";
        $sql .= "AND rep.id='{$mrow->reportid}' ";
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
        if ($numresults>0) $average=number_format(($cumul/$numresults),2);

        $percent =number_format((($average / 9) * 100), 0);
        $totalresult+=$average;
        $qanswer[$qrow->taborder]+=$average;
        $qavgavg=$qanswer[$qrow->taborder];
        $rowresult=number_format(($qavgavg/$rcount),2);
        $rowpercent =number_format((($rowresult / 9) * 100), 0);
        $totalrowresult=$rowresult;
        ## {$average} <strong>({$percent}%)</strong>   ...
        $html .= "($rowresult) <strong>({$rowpercent}%)</strong><br />";
    }
    $total_average=number_format($totalresult/$numquestions,2);
    $total_rowaverage=number_format(($totalrowresult/$numquestions)*10,2);
    $total_percent=number_format((($total_average / 9) * 100), 0);

    $qcount = (count($qanswer)-1);
    for ($i=1;$i<=$qcount;$i++)
    {
        ## $html .= "... {$qanswer[$i]}<br />";
        $qtotal+=$qanswer[$i];
    }
    $qtotal = (($qtotal / $qcount) / $rcount);
    $qtotal_percent=number_format((($qtotal / 9) * 100), 0);

    $html .= "<p>Positivity: ".number_format($qtotal,2)." <strong>({$qtotal_percent}%)</strong>, after $rcount survey(s).</p>";
    ## ... ($rcount -- $total_rowaverage)
    $html .= "<hr />\n";

    if ($total_average > 0) $rcount++;


    if ($mrow->incidentid!=$prevprod AND $prevprod!='')
    {
        if ($total_average > 0)
        {
            echo $html;
            $survcount++;
        }
        $rcount=1;
        unset($qavgavg);
        unset($qanswer);
        unset($dbg);
    }
    $prevprod = $mrow->incidentid;
}

echo "<h2>$survcount Surveys</h2>";

echo "</div>\n";
include ('htmlfooter.inc.php');

?>
