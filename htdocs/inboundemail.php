#!/usr/bin/php
<?php
// inboundemail.php - Process incoming emails
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//          Paul Heaney <paulheaney[at]users.sourceforge.net>

// Note: do we need to handle rfc2047 encoded words in the subject?

@include ('set_include_path.inc.php');
require ('db_connect.inc.php');
require ('functions.inc.php');
require ('mime_email.class.php');

// read the email from stdin (it should be piped to us by the MTA)
$fp = fopen("php://stdin", "r");
$rawemail = '';
while (!feof($fp))
{
    $rawemail[] = fgets($fp); // , 1024
}
fclose($fp);

// DEBUG
echo $rawemail;

// Create and populate the email object
$email = new mime_email;
$email->set_emaildata($rawemail);
unset($rawemail);
$attachment = array();

$decoded_email = $email->go_decode();

// Extract Incident ID etc.
if (preg_match('/\[(\d{1,5})\]/',$decoded_email->subject,$m)) $incidentid = $m[1];
$customer_visible = 'No';

$part = 1;
if ($decoded_email->contenttype=='multipart/mixed' OR
    $decoded_email->contenttype=='multipart/alternative')
{
    // This is a MIME message
    foreach ($decoded_email->mime_block AS $block)
    {
        if ($CONFIG['debug']) print_r($block);
        // Do the decoding
        switch ($block->mime_transferenc)
        {
            case 'quoted-printable':
                $block->mime_content = quoted_printable_decode($block->mime_content);
            break;

            case 'base64':
                $block->mime_content = base64_decode($block->mime_content);
            break;

            default:
                // Do no decoding
        }
        // Extract any inline text into the incident log (if it's HTML strip the tags first)
        if ($block->mime_contentdisposition=='inline' OR $block->mime_contentdisposition=='')
        {
            switch ($block->mime_contenttype)
            {
                case 'text/plain':
                    $message .= $block->mime_content;
                break;

                case 'text/html':
                    // Only use HTML version if we have no text version
                    if (empty($message)) $message = strip_tags($block->mime_content);
                break;

                default:
                    //$message .= "Inline content of type {$block->mime_contenttype} ommitted.\n";

                    // FIXME we should treat these blocks as attachments
                    // FIXME this code should be shared with below rather than copied on mass

                    // try to figure out what delimeter is being used (for windows or unix)...
                    $delim = (strstr($CONFIG['attachment_fspath'],"/")) ? "/" : "\\";

                    $filename = str_replace(' ','_',$block->mime_contentdispositionname);
                    if (empty($filename)) $filename = "part{$part}";
                    $attachment[] = $filename;

                    // FIXME this assumes we always have an incident number, we don't, so this will fail for new incidents

                    // Write the attachment
                    $fa_dir = $CONFIG['attachment_fspath'].$incidentid;
                    if (!file_exists($fa_dir))
                    {
                        if (!mkdir($fa_dir, 0775)) trigger_error("Failed to create incident attachment directory",E_USER_WARNING);
                    }
                    $fa_update_dir = $fa_dir . "{$delim}{$now}";
                    if (!file_exists($fa__update_dir))
                    {
                        if (!mkdir($fa_update_dir, 0775)) trigger_error("Failed to create incident update attachment directory",E_USER_WARNING);
                    }
                    if (is_writable($fa_update_dir.$delim)) //File doesn't exist yet .$filename
                    {
                        $fwp = fopen($fa_update_dir.$delim.$filename, 'a');
                        // FIXME not actually writing content here yet
                        //fwrite($fwp, "This is a test\n");
                        fwrite($fwp, $block->mime_content);
                        fclose($fwp);
                    }
                    else echo "NOT WRITABLE $filename\n";
               }
        }
        else
        {
            // try to figure out what delimeter is being used (for windows or unix)...
            $delim = (strstr($CONFIG['attachment_fspath'],"/")) ? "/" : "\\";

            $filename = str_replace(' ','_',$block->mime_contentdispositionname);
            if (empty($filename)) $filename = "part{$part}";
            $attachment[] = $filename;
            // Write the attachment
            $fa_dir = $CONFIG['attachment_fspath'].$incidentid;
            if (!file_exists($fa_dir))
            {
                if (!mkdir($fa_dir, 0775)) trigger_error("Failed to create incident attachment directory",E_USER_WARNING);
            }
            $fa_update_dir = $fa_dir . "{$delim}{$now}";
            if (!file_exists($fa__update_dir))
            {
                if (!mkdir($fa_update_dir, 0775)) trigger_error("Failed to create incident update attachment directory",E_USER_WARNING);
            }
            echo "About to write to ".$fa_update_dir.$delim.$filename."\n";
            if (is_writable($fa_update_dir.$delim)) //File doesn't exist yet .$filename
            {
                $fwp = fopen($fa_update_dir.$delim.$filename, 'a');
                // FIXME not actually writing content here yet
                //fwrite($fwp, "This is a test\n");
                fwrite($fwp, $block->mime_content);
                fclose($fwp);
            }
            else echo "NOT WRITABLE $filename\n";
        }
    }
}

$count_attachments = count($attachment);

if (empty($message)) $message = $decoded_email->emailtextplain;

// Strip excessive line breaks
$message = str_replace("\n\n\n\n","\n", $message);
$message = str_replace(">\n>\n>\n>\n",">\n", $message);

// Build up text to insert in the incident log
if (!empty($decoded_email->from))
{
    $headertext .= "From: [b]".htmlentities(mysql_real_escape_string($decoded_email->from), ENT_NOQUOTES)."[/b]\n";
}

if (!empty($decoded_email->to))
{
    $headertext .= "To: [b]".htmlentities(mysql_real_escape_string($decoded_email->to))."[/b]\n";
}

if (!empty($decoded_email->cc))
{
    $headertext .= "CC: [b]".htmlentities(mysql_real_escape_string($decoded_email->cc))."[/b]\n";
}

if (!empty($decoded_email->subject))
{
    $headertext .= "Subject: [b]".htmlentities(mysql_real_escape_string($decoded_email->subject))."[/b]\n";
}

if ($count_attachments >= 1)
{
    $headertext .= "Attachments: [b]{$count_attachments}[/b] - ";
    $c = 1;
    foreach ($attachment AS $att)
    {
        $headertext .= "[[att]]{$att}[[/att]]";
        if ($c < $count_attachments) $headertext .= ", ";
        $c++;
    }
    $headertext .= "\n";
}

if (!empty($headertext)) $bodytext .= "{$headertext}<hr>";
$bodytext .= mysql_real_escape_string($message);

if (empty($incidentid))
{
    // Add entry to the incident update log
    $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, customervisibility, currentstatus) ";
    $sql .= "VALUES ('{$incidentid}', 0, 'emailin', '{$bodytext}', '{$now}', '$customer_visible', 1 )";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $updateid = mysql_insert_id();

    //new call
    $sql = "INSERT INTO `{$dbTempIncoming}` (updateid, incidentid, emailfrom, subject, reason, contactid) ";
    $sql.= "VALUES ('{$updateid}', '0', '{$decoded_email->from_name}', '".mysql_real_escape_string($decoded_email->subject)."', 'Possible new call', '{$contactid}' )";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    // This could be a new incident or just spam
    die('Invalid incident ID or incident ID not found');
    // FIXME incomplete, we shouldn't just die like that
}
else
{
    $incident_open = incident_open($incidentid);

    if ($incident_open != "Yes")
    {
        //Dont want to associate with a closed call
        $oldincidentid = $incidentid;
        $incidentid = 0;
    }

    //this prevents duplicate emails
    $fifteenminsago = $now - 900;
    $sql = "SELECT bodytext FROM `{$dbUpdates}` ";
    $sql .= "WHERE incidentid = '{$incidentid}' AND timestamp > '{$fifteenminsago}' ";
    $sql .= "ORDER BY id DESC LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (mysql_num_rows($result) > 0)
    {
        list($lastupdate) = mysql_fetch_row($result);

        $newtext = "{$headertext}<hr>{$message}";
        if (strcmp(trim($lastupdate),trim($newtext)) == 0)
        {
            $error = 1;
        }
    }

    if ($error != 1)
    {
        // Existing incident, new update:
        // Add entry to the incident update log
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, customervisibility, currentstatus) ";
        $sql .= "VALUES ('{$incidentid}', 0, 'emailin', '{$bodytext}', '{$now}', '$customer_visible', 1 )";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        $updateid = mysql_insert_id();

        if ($incident_open == "Yes")
        {
            // Mark the incident as active
            $sql = "UPDATE `{$dbIncidents}` SET status='1', lastupdated='".time()."', timeofnextaction='0' ";
            $sql .= "WHERE id='{$incidentid}'";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        }
        else
        {
            //create record in tempincoming
            if ($incident_open == "No")
            {
                //incident closed
                $sql = "INSERT INTO `{$dbTempIncoming}` (updateid, incidentid, emailfrom, subject, reason, contactid) ";
                $sql.= "VALUES ('{$updateid}', '0', '{$decoded_email->from_name}', '".mysql_real_escape_string($decoded_email->subject)."', '".mysql_real_escape_string("Incident <a href=\"javascript:incident_details_window('{$oldincidentid}','incident{$oldincidentid}')\" class='info'>{$oldincidentid}</a> is closed")."', '$contactid' )";
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            }
            else
            {
                //new call
                $sql = "INSERT INTO `{$dbTempIncoming}` (updateid, incidentid, emailfrom, subject, reason, contactid) ";
                $sql.= "VALUES ('{$updateid}', '0', '{$decoded_email->from_name}', '".mysql_real_escape_string($decoded_email->subject)."', 'Possible new call', '{$contactid}' )";
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            }
        }
    }
    else
    {
        if ($incidentid != 0)
        {
            $bodytext = "[i]Received duplicate email within 15 minutes. Message not stored. Possible mail loop.[/i]";
            $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, customervisibility, currentstatus) ";
            $sql .= "VALUES ('{$incidentid}', 0, 'emailin', '{$bodytext}', '{$now}', '{$customer_visible}', 1 )";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        }
    }
}

?>