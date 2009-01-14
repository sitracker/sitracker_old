<?php
// engineer.inc.php - Feedback report by engineer
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//          Paul Heaney Paul Heaney <paulheaney[at]users.sourceforge.net>


$formid = $CONFIG['feedback_form'];

echo "<div style='margin: 20px'>";
echo "<h2>Average <a href='{$CONFIG['application_webpath']}reports/feedback.php'>Feedback</a> Scores: By Engineer</h2>";
echo feedback_between_dates();
echo "<p>This report shows average customer responses and a percentage figure indicating the overall positivity of customers toward ";
echo "incidents logged by the user(s) shown:</p>";

$usql = "SELECT * FROM `{$dbUsers}` WHERE status > 0 ";
if ($_REQUEST['userid'] > 0) $usql .= "AND id='".mysql_real_escape_string($_REQUEST['userid'])."' ";
else $usql .= "ORDER BY username";
$uresult = mysql_query($usql);
if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
if (mysql_num_rows($uresult) >= 1)
{
    while ($user = mysql_fetch_object($uresult))
    {
        $totalresult=0;
        $numquestions=0;
        $html = "<h2>".ucfirst($user->realname)."</h2>";
        $qsql = "SELECT * FROM `{$dbFeedbackQuestions}` WHERE formid='{$formid}' AND type='rating' ORDER BY taborder";
        $qresult = mysql_query($qsql);
        if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);

        if (mysql_num_rows($qresult) >= 1)
        {
            while ($qrow = mysql_fetch_object($qresult))
            {
                $numquestions++;
                $html .= "Q{$qrow->taborder}: {$qrow->question} &nbsp;";
                $sql = "SELECT * FROM `{$dbFeedbackRespondents}` AS fr, `{$dbIncidents}` AS i, `{$dbUsers}` AS u, `{$dbFeedbackResults}` AS r ";
                $sql .= "WHERE fr.incidentid = i.id ";
                $sql .= "AND i.owner = u.id ";
                $sql .= "AND fr.id = r.respondentid ";
                $sql .= "AND r.questionid = '$qrow->id' ";
                $sql .= "AND u.id = '$user->id' ";
                $sql .= "AND fr.completed = 'yes' \n"; ///////////////////////

                if (!empty($startdate))
                {
                    if ($dates == 'feedbackin')
                    {
                        $sql .= "AND fr.created >= '{$startdate}' ";
                    }
                    elseif ($dates == 'closedin')
                    {
                        $sql .= "AND i.closed >= '{$startdate}' ";
                    }

                    //echo "DATES {$dates}";
                }

                if (!empty($enddate))
                {
                    if ($dates == 'feedbackin')
                    {
                        $sql .= "AND fr.created <= '{$enddate}' ";
                    }
                    elseif ($dates == 'closedin')
                    {
                        $sql .= "AND i.closed <= '{$enddate}' ";
                    }
                }

                $sql .= "ORDER BY i.owner, i.id";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
                $numresults = 0;
                $cumul = 0;
                $percent = 0;
                $average = 0;
                ## echo "=== $sql<br /> ";
                while ($row = mysql_fetch_object($result))
                {
                    if (!empty($row->result))
                    {
                        $cumul += $row->result;
                        $numresults++;
                        ## echo "===== Result: {$row->result}<br />";
                    }
                }
                if ($numresults > 0)
                {
                    $average = number_format(($cumul/$numresults), 2);
                }

                $percent = number_format((($average -1) * (100 / ($CONFIG['feedback_max_score'] -1))), 0);
                if ($percent < 0)
                {
                    $percent = 0;
                }

                $totalresult += $average;
                $html .= "{$average} <strong>({$percent}%)</strong><br />";
            }
            $total_average = number_format($totalresult/$numquestions,2);
            $total_percent = number_format((($total_average -1) * (100 / ($CONFIG['feedback_max_score'] -1))), 0);
            if ($total_percent < 0) $total_percent=0;
            $html .= "<p>Positivity: {$total_average} <strong>({$total_percent}%)</strong> after $numresults surveys.</p>";
            $surveys += $numresults;
            $html .= "<hr />\n";

            //if ($total_average>0)
            echo $html;
            echo "\n\n\n<!-- $surveys -->\n\n\n";
        }
        else
        {
            echo "<p class='error'>No feedback found for ".ucfirst($user->realname)."</p>";
        }
    }
}
else
{
    echo "<p class='error'>Found no users to report on</p>";
}
echo "</div>\n";

?>
