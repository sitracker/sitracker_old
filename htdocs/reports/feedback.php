<?php
// feedback.php - Feedback report menu
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=37; // Run Reports

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');
?>
<h2>Feedback Reports</h2>

<table align='center'>
<tr><td colspan="2" class='shade1' align='right'><p class=sectiontitle>FEEDBACK REPORTS</p></td></tr>

<tr>
<td class='shade2' width='*'><a href="<?php echo $CONFIG['application_webpath']; ?>reports/feedback2.php">Feedback (by Engineer)</a><br />
&nbsp;
</td></tr>

<tr>
<td class='shade2' width='*'><a href="<?php echo $CONFIG['application_webpath']; ?>reports/feedback3.php">Feedback (by Contact)</a><br />
&nbsp;
</td></tr>

<tr>
<td class='shade2' width='*'><a href="<?php echo $CONFIG['application_webpath']; ?>reports/feedback4.php">Feedback (by Site)</a><br />
&nbsp;
</td></tr>

<tr>
<td class='shade2' width='*'><a href="<?php echo $CONFIG['application_webpath']; ?>reports/feedback5.php">Feedback (by Product)</a><br />
&nbsp;
</td></tr>
<?php
plugin_do('feedback_reports_menu');
?>
</table>
<?php
include('htmlfooter.inc.php');
?>
