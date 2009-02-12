<?php
// new_feedback.php - Feedback report menu
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Report Type: Feedback

// Author:  Paul Heaney Paul Heaney <paulheaney[at]users.sourceforge.net>

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 37; // Run Reports

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

$type = cleanvar($_REQUEST['type']);
$dates = cleanvar($_REQUEST['dates']);
$startdate = strtotime(cleanvar($_REQUEST['startdate']));
$enddate = strtotime(cleanvar($_REQUEST['enddate']));

$formid = $CONFIG['feedback_form'];

/// echo "Start: {$startdate}";

include ('./inc/htmlheader.inc.php');

echo "<h2>Feedback Reports</h2>";

function feedback_between_dates()
{
    global $dates, $startdate, $enddate, $CONFIG;
    if (!empty($startdate))
    {
        if (!empty($enddate))
        {
            if ($dates == 'feedbackin')
            {
                $str = "<p>Feedback between ".ldate($CONFIG['dateformat_date'], $startdate)." and ".ldate($CONFIG['dateformat_date'], $enddate)."</p>";
            }
            elseif ($dates == 'closedin')
            {
                $str = "<p>Closed between ".ldate($CONFIG['dateformat_date'], $startdate)." and ".ldate($CONFIG['dateformat_date'], $enddate)."</p>";
            }
        }
        else
        {
            if ($dates == 'feedbackin')
            {
                $str = "<p>Feedback after ".ldate($CONFIG['dateformat_date'], $startdate)."</p>";
            }
            elseif ($dates == 'closedin')
            {
                $str = "<p>Closed after ".ldate($CONFIG['dateformat_date'], $startdate)."</p>";
            }
        }
    }
    elseif (!empty($enddate))
    {
        if ($dates == 'feedbackin')
        {
            $str = "<p>Feedback before ".ldate($CONFIG['dateformat_date'], $enddate)."</p>";
        }
        elseif ($dates == 'closedin')
        {
            $str = "<p>Closed before ".ldate($CONFIG['dateformat_date'], $enddate)."</p>";
        }
    }
    return $str;
}

if (empty($type))
{
    include ('./inc/report_feedback_form.inc.php');
}
elseif ($type == 'byengineer')
{
    include ('./inc/report_feedback_engineer.inc.php');
}
elseif ($type == 'bycustomer')
{
    include ('./inc/report_feedback_contact.inc.php');
}
elseif ($type == 'bysite')
{
    include ('./inc/report_feedback_site.inc.php');
}
elseif ($type == 'byproduct')
{
    include ('./inc/report_feedback_product.inc.php');
}

include ('./inc/htmlfooter.inc.php');

?>