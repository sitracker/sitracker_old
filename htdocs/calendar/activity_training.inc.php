<?php
// activity_training.inc.php - Training
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Tom Gerrard <tom.gerrard[at]salfordsoftware.co.uk>
//
// Included by timesheet.inc.php

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

$activity_types['Training'] = '';

echo "<script type='text/javascript'>
\n//<![CDATA[\n
    function activityTraining(level)
    {
        $('newactivityalias').value = 'Training';
    }

    activityTypes['Training'] = activityTraining;
\n//]]>\n
</script>
";

?>



