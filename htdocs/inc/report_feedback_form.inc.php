<?php
// form.php - Feedback selection form
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:  Paul Heaney Paul Heaney <paulheaney[at]users.sourceforge.net>

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}


echo "<form action='{$_SERVER['PHP_SELF']}' method='post' id='feedback'>";
echo "<table align='center'>";
echo "<tr><th>Start Date:</th>";
echo "<td><input type='text' name='startdate' id='startdate' size='10' /> ";
echo date_picker('feedback.startdate');
echo "</td></tr>\n";
echo "<tr><th>End Date:</th>";
echo "<td><input type='text' name='enddate' id='enddate' size='10' /> ";
echo date_picker('feedback.enddate');
echo "</td></tr>\n";

echo "<tr><th>Dates:</th><td>";
echo "<input type='radio' name='dates' value='closedin' selected />Closed In ";
echo "<input type='radio' name='dates' value='feedbackin' />Feed Back In ";
echo "</td></tr>";

echo "<tr><th>Type:</th><td>";
echo "<input type='radio' name='type' value='byengineer' />Engineer ";
echo "<input type='radio' name='type' value='bycustomer' />Customer ";
echo "<input type='radio' name='type' value='bysite' />Site ";
echo "<input type='radio' name='type' value='byproduct' />Product ";
echo "</td></tr>";

echo "</table>";

echo "<p align='center'><input type='submit' name='runreport' value='Run Report' /></p>";
echo "</form>";

?>
