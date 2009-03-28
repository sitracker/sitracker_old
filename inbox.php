<?php
// review_incoming_updates.php - Review/Delete Incident Updates
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Tom Gerrard, Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//                       Paul Heaney <paulheaney[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional! 31Oct05


$permission = 42;
require ('core.php');
require (APPLICATION_LIBPATH . 'functions.inc.php');

// This page requires authentication
require (APPLICATION_LIBPATH . 'auth.inc.php');

include (APPLICATION_INCPATH . 'htmlheader.inc.php');


function contact_info($contactid, $email, $name)
{
    global $strUnknown;

    if (!empty($email)) $info .= "<a href=\"mailto:{$email}\">";
    if (!empty($name)) $info .= "{$name}";
    elseif (!empty($email)) $info .= "{$email}";
    else $info .= "{$strUnknown}";
    if (!empty($email)) $info .= "</a>";

    return $info;
}













echo "<h2>".icon('email', 32)." {$strHoldingQueue}</h2>";

$sql = "SELECT * FROM `$dbTempIncoming` ORDER BY id DESC";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
$countresults = mysql_num_rows($result);

echo "<p align='center'>{$strIncomingEmailText}</p>";
$shade = 'shade1';
echo "<table align='center' style='width: 95%'>";
echo "<tr>";
echo colheader('select', '', FALSE, '', '', '', '1%');
echo colheader('date', $strDate, FALSE);
echo colheader('from', $strFrom, FALSE);
echo colheader('subject', $strSubject, FALSE);
echo colheader('operation', $strOperation, FALSE);
echo "</tr>";
while ($incoming = mysql_fetch_object($result))
{
    echo "<tr class='{$shade}'>";
    echo "<td>".html_checkbox('item', FALSE)."</td>";
    echo "<td>...</td>";
    echo "<td>".contact_info($incoming->contactid, $incoming->from, $incoming->emailfrom)."</td>";
    echo "</td>";
    echo "<td>{$incoming->subject}</td>";
    // echo "<td><pre>".print_r($incoming,true)."</pre><hr /></td>";
    echo "</tr>";
    if ($shade == 'shade1') $shade = 'shade2';
    else $shade = 'shade1';
}
echo "</table>";


include (APPLICATION_INCPATH . 'htmlfooter.inc.php');
?>