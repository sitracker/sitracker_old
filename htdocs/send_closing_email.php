<?php
// send_closing_email.php - Send an email when incident is closed
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// This script is run after confirmation in close_incident.php

$permision=33; // send emails
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// Valid user

$id = cleanvar($_REQUEST['id']);

if (empty($id))
{
    // no id specified, should not happen unless someone is playing silly buggers
    include('includes/incident_html_top.inc');
    echo "<p class='error'>No incident id specified</p>\n";
    include('includes/incident_html_bottom.inc');
}
else
{
    // send "Incident Closed"
    // extract details from emailtype
    $to_field = emailtype_replace_specials(emailtype_to(13), $id, $sit[2]);
    $from_field = emailtype_replace_specials(emailtype_from(13), $id, $sit[2]);
    $replyto_field = emailtype_replace_specials(emailtype_replyto(13), $id, $sit[2]);
    $cc_field = emailtype_replace_specials(emailtype_cc(13), $id, $sit[2]);
    $bcc_field = emailtype_replace_specials(emailtype_bcc(13), $id, $sit[2]);
    $subject_field = emailtype_replace_specials(emailtype_subject(13), $id, $sit[2]);
    $bodytext = emailtype_replace_specials(emailtype_body(13), $id, $sit[2]);


    // build the extra headers string for email
    $extra_headers = "From: $fromfield\nReply-To: $replytofield\nErrors-To: {$CONFIG['support_email']}\n";
    if ($ccfield != "")

    $extra_headers .= "CC: $ccfield\n";

    if ($bccfield != "")

    $extra_headers .= "BCC: $bccfield\n";
    $extra_headers .= "X-Originating-IP: {$_SERVER['REMOTE_ADDR']}\n";

    // send email
    $success = mail($tofield, stripslashes($subjectfield), stripslashes($bodytext), $extra_headers);

    if (!$success)
    {
        // show error
        include('includes/incident_html_top.inc');
        echo "<p class='error'>Error sending email</p>\n";
        include('includes/incident_html_bottom.inc');
    }
    else
    {
        // add update
        $time = time();
        $updatebody .= "To: <b>" . $tofield . "</b>\nFrom: <b>" . $fromfield . "</b>\nReply-To: <b>" . $replytofield . "</b>\nCC: <b>" . $ccfield . "</b>\nBCC: <b>" . $bccfield . "</b>\nSubject: <b>" . $subjectfield . "</b>\n\n" . $bodytext;
        $sql  = "INSERT INTO updates (incidentid, userid, bodytext, type, timestamp) ";
        $sql .= "VALUES ($id, $sit[2], '$updatebody', 'email', $time)";
        mysql_query($sql);

        // show success and redirect
        confirmation_page("2", "incident_details.php?id=$id", "<h2>Email Sent Successfully</h2><p align='center'>Please wait while you are redirected...</p>");
    }
}
?>