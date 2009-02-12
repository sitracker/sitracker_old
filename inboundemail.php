<?php
// inboundemail.php - Process incoming emails
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Note: if performance is poor with attachments, we should download attachments
//       with the function in fetchSitMail.class.php
// Note2: to be called from auto.php

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
require ($lib_path . 'mime_parser.inc.php');
require ($lib_path . 'rfc822_addresses.inc.php');
require ($lib_path . 'fetchSitMail.class.php');

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    require ($lib_path.'db_connect.inc.php');
    include ($lib_path.'strings.inc.php');
    require ($lib_path.'functions.inc.php');
    require ($lib_path . 'base.inc.php');
}
else
{
    global $CONFIG, $dbFiles, $dbUpdates, $dbTempIncoming, $dbIncidents, $now;
    $fsdelim = DIRECTORY_SEPARATOR;
}

//hack as we have no session
function populate_syslang2()
{
    global $CONFIG;
    // Populate $SYSLANG with system lang
    $file = dirname( __FILE__ ).DIRECTORY_SEPARATOR."i18n/{$CONFIG['default_i18n']}.inc.php";
    if (file_exists($file))
    {
        $fh = fopen($file, "r");

        $theData = fread($fh, filesize($file));
        fclose($fh);
        $lines = explode("\n", $theData);
        foreach ($lines as $values)
        {
            $badchars = array("$", "\"", "\\", "<?php", "?>");
            $values = trim(str_replace($badchars, '', $values));
            if (substr($values, 0, 3) == "str")
            {
                $vars = explode("=", $values);
                $vars[0] = trim($vars[0]);
                $vars[1] = trim(substr_replace($vars[1], "",-2));
                $vars[1] = substr_replace($vars[1], "",0, 1);
                $SYSLANG[$vars[0]] = $vars[1];
            }
        }
        return $SYSLANG;
    }
    else
    {
        trigger_error("File specified in \$CONFIG['default_i18n'] can't be found", E_USER_ERROR);
    }
}

$SYSLANG = populate_syslang2();

if ($CONFIG['enable_inbound_mail'] == 'MTA')
{
    // read the email from stdin (it should be piped to us by the MTA)
    $fp = fopen("php://stdin", "r");
    $rawemail = '';
    while (!feof($fp))
    {
        $rawemail .= fgets($fp); // , 1024
    }
    fclose($fp);
    $emails = 1;
}
elseif ($CONFIG['enable_inbound_mail'] == 'POP/IMAP')
{
    $mailbox = new fetchSitMail($CONFIG['email_username'], $CONFIG['email_password'],
                                $CONFIG['email_address'], $CONFIG['email_server'],
                                $CONFIG['email_servertype'], $CONFIG['email_port'],
                                $CONFIG['email_options']);


    if (!$mailbox->connect())
    {
	if ($CONFIG['debug'])
	{
	    echo "Connection error, see debug log for details, if enabled.\n";
	}
	exit;
    }

    $emails = $mailbox->getNumUnreadEmails();
//     $size = $mailbox->getTotalSize($emails);
}
else
{
    return FALSE;
}

if ($emails > 0)
{
    if ($CONFIG['debug'])
    {
        debug_log("Found {$emails} email(s) to fetch, Archive folder set to: '{$CONFIG['email_archive_folder']}'\n");
    }
    for ($i = 0; $i < $emails; $i++)
    {
        if ($CONFIG['enable_inbound_mail'] == 'POP/IMAP')
        {
            $rawemail = $mailbox->getMessageHeader($i + 1);
            $rawemail .= "\n".$mailbox->messageBody($i + 1);

            if ($mailbox->servertype == 'imap')
            {
                if (!empty($CONFIG['email_archive_folder']))
                {
                    if ($CONFIG['debug'])
                    {
                        debug_log("Archiving email");
                    }
                    $mailbox->archiveEmail($i + 1);
                }
                else
                {
                    $mailbox->deleteEmail($i + 1);
                }
            }
        }

        $mime = new mime_parser_class();
        $mime->mbox = 0;
        $mime->decode_bodies = 1;
        $mime->ignore_syntax_errors = 1;

        $parameters = array('Data'=>$rawemail);

        $mime->Decode($parameters, $decoded);
        $mime->Analyze($decoded[0], $results);
        $to = $cc = $from = $from_name = $from_email = "";

        if ($CONFIG['debug'])
        {
            debug_log("Message $i Email Type: '{$results['Type']}', Encoding: '{$results['Encoding']}'");
            debug_log(print_r($results,true));
        }

        // Attempt to recognise contact from the email address
        $from_email = strtolower($results['From'][0]['address']);
        $sql = "SELECT id FROM `{$GLOBALS['dbContacts']}` ";
        $sql .= "WHERE email = '{$from_email}'";
        if ($result = mysql_query($sql))
        {
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            $row = mysql_fetch_object($result);
            $contactid = $row->id;
        }

        if (!empty($results['From'][0]['name']))
        {
            $from_name = $results['From'][0]['name'];
            $from =  $from_name . " <". $from_email . ">";
        }
        else
        {
            $from = $from_email;
        }

        $subject = $results['Subject'];
        $date = $results['Date'];

        switch ($results['Type'])
        {
            case 'html':
                if (is_array($results['To']))
                {
                    foreach ($results['To'] as $var)
                    {
                        $num = sizeof($results['To']);
                        $cur = 1;
                        if (!empty($var['name']))
                        {
                            $to .= $var['name']. " <".$var['address'].">";
                            if ($cur != $num) $cc .= ", ";
                        }
                        else
                        {
                            $to .= $var['address'];
                        }
                        $cur++;
                    }
                }

                if (is_array($results['Cc']))
                {
                    foreach ($results['Cc'] as $var)
                    {
                        $num = sizeof($results['Cc']);
                        $cur = 1;
                        if (!empty($var['name']))
                        {
                            $cc .= $var['name']. " <".$var['address'].">";
                            if ($cur != $num) $cc .= ", ";
                        }
                        else
                        {
                            $cc .= $var['address'];
                        }
                        $cur++;
                    }
                }

                $message = $results['Alternative'][0]['Data'];
                break;

            case 'text':
                if (is_array($results['To']))
                    foreach ($results['To'] as $var)
                    {
                        $num = sizeof($results['To']);
                        $cur = 1;
                        if (!empty($var['name']))
                        {
                            $to .= $var['name']. " <".$var['address'].">";
                            if ($cur != $num) $cc .= ", ";
                        }
                        else
                        {
                            $to .= $var['address'];
                        }
                    }
                }

                if (is_array($results['Cc']))
                {
                    $num = sizeof($results['Cc']);
                    $cur = 1;
                    foreach ($results['Cc'] as $var)
                    {
                        if (!empty($var['name']))
                        {
                            $cc .= $var['name']. " <".$var['address'].">";
                            if ($cur != $num) $cc .= ", ";
                        }
                        else
                        {
                            $cc .= $var['address'];
                        }
                    }
                }
                $message = $results['Data'];
                break;

            default:
                break;
        }

        // Extract Incident ID
        if (preg_match('/\[(\d{1,5})\]/', $subject, $m))
        {
            $incidentid = $m[1];
        }
        if ($incidentid > 0) debug_log("Incident ID found in email: '{$incidentid}'");

        $customer_visible = 'No';
        $part = 1;
        //process attachments
        if (!empty($incidentid))
        {
            $fa_dir = $CONFIG['attachment_fspath'].$incidentid.$fsdelim;
        }
        else
        {
            $fa_dir = $CONFIG['attachment_fspath']."updates{$fsdelim}";
        }

        if (!file_exists($fa_dir))
        {
            if (!mkdir($fa_dir, 0775, TRUE)) trigger_error("Failed to create incident update attachment directory $fa_dir",E_USER_WARNING);
        }
        $attachments = array();
        if (is_array($results[Attachments]))
        {
            foreach ($results[Attachments] as $attachment)
            {
                $data = $attachment[Data];
                $filename = $attachment[FileName];
                $filename = str_replace(' ', '_', $filename);
                if (empty($filename))
                {
                    $filename = 'part'.$part;
                    $part++;
                }
                $sql = "INSERT into `{$GLOBALS['dbFiles']}` ";
                $sql .= "( `id` ,`category` ,`filename` ,`size` ,`userid` ,`usertype` ,`shortdescription` ,`longdescription` ,`webcategory` ,`path` ,`downloads` ,`filedate` ,`expiry` ,`fileversion` ,`published` ,`createdby` ,`modified` ,`modifiedby` ) ";
                $sql .= "VALUES('', '', '{$filename}', '0', '0', '', '', '', '', '', '', NOW(), '', '', '', '0', '', '')";
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                $fileid = mysql_insert_id();
                $attachments[] = array('filename' => $filename, 'fileid' => $fileid);
                $filename = $fileid."-".$filename;

                if (is_writable($fa_dir))
                {
                    $fwp = fopen($fa_dir.$filename, 'a');
                    fwrite($fwp, $data);
                    fclose($fwp);
                }
                $sql = "INSERT INTO `{$GLOBALS['dbLinks']}` (`linktype`, `origcolref`, `linkcolref`, `direction`, `userid`) ";
                $sql .= "VALUES('5', '{$updateid}', '{$fileid}', 'left', '0') ";
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            }
        }

        //** BEGIN UPDATE INCIDENT **//
        $headertext = '';
        // Build up header text to append to the incident log
        if (!empty($from))
        {
            $headertext = "From: [b]".htmlentities(mysql_real_escape_string($from), ENT_NOQUOTES)."[/b]\n";
        }

        if (!empty($to))
        {
            $headertext .= "To: [b]".htmlentities(mysql_real_escape_string($to))."[/b]\n";
        }

        if (!empty($cc))
        {
            $headertext .= "CC: [b]".htmlentities(mysql_real_escape_string($cc))."[/b]\n";
        }

        if (!empty($subject))
        {
            $headertext .= "Subject: [b]".mysql_real_escape_string($subject)."[/b]\n";
        }

        $count_attachments = count($attachments);
        if ($count_attachments >= 1)
        {
            $headertext .= $SYSLANG['strAttachments'].": [b]{$count_attachments}[/b] - ";
            $c = 1;
            foreach ($attachments AS $att)
            {
                $headertext .= "[[att={$att['fileid']}]]{$att['filename']}[[/att]]";
                if ($c < $count_attachments) $headertext .= ", ";
                $c++;
            }
            $headertext .= "\n";
        }
        //** END UPDATE INCIDENT **//

        //** BEGIN UPDATE **//
        // Convert the encoding to UTF-8 if it isn't already
        if (!empty($results['Encoding']) AND !strcasecmp('UTF-8', $results['Encoding']))
        {
            $message = mb_convert_encoding($message, "UTF-8", strtoupper($results['Encoding']));
        }
        $bodytext = $headertext . "<hr>" . mysql_real_escape_string($message);

        // Strip excessive line breaks
        $message = str_replace("\n\n\n\n","\n", $message);
        $message = str_replace(">\n>\n>\n>\n",">\n", $message);

        if (empty($incidentid))
        {
            // Add entry to the incident update log
            $owner = incident_owner($incidentid);
            $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, customervisibility, currentowner, currentstatus) ";
            $sql .= "VALUES ('{$incidentid}', 0, 'emailin', '{$bodytext}', '{$now}', '{$customer_visible}', '{$owner}', 1 )";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            $updateid = mysql_insert_id();

            //new call
            $sql = "INSERT INTO `{$dbTempIncoming}` (updateid, incidentid, `from`, emailfrom, subject, reason, contactid) ";
            $sql.= "VALUES ('{$updateid}', '0', '".mysql_real_escape_string($from_email)."', ";
            $sql .= "'".mysql_real_escape_string($from_name)."', ";
            $sql .= "'".mysql_real_escape_string($subject)."', ";
            $sql .= "'{$SYSLANG['strPossibleNewIncident']}', '{$contactid}' )";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            $holdingemailid = mysql_insert_id();

            trigger('TRIGGER_NEW_HELD_EMAIL', array('holdingemailid' => $holdingemailid));

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
            $error = 0;
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

            $owner = incident_owner($incidentid);
            if ($error != 1)
            {
                // Existing incident, new update:
                // Add entry to the incident update log
                $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, customervisibility, currentowner, currentstatus) ";
                $sql .= "VALUES ('{$incidentid}', 0, 'emailin', '{$bodytext}', '{$now}', '{$customer_visible}', '{$owner}', 1 )";
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                $updateid = mysql_insert_id();

                if ($incident_open == "Yes")
                {
                    // Mark the incident as active
                    $sql = "UPDATE `{$GLOBALS['dbIncidents']}` SET status='1', lastupdated='".time()."', timeofnextaction='0' ";
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
                        $reason = sprintf($SYSLANG['strIncidentXIsClosed'], $oldincidentid);
                        $sql = "INSERT INTO `{$GLOBALS['dbTempIncoming']}` (updateid, incidentid, emailfrom, subject, reason, reason_id, incident_id, contactid) ";
                        $sql.= "VALUES ('{$updateid}', '0', '{$from_name}', '".mysql_real_escape_string($subject)."', '{$reason}', ".REASON_INCIDENT_CLOSED.", '{$oldincidentid}', '$contactid' )";
                        mysql_query($sql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                    }
                    else
                    {
                        //new call
                        $sql = "INSERT INTO `{$dbTempIncoming}` (updateid, incidentid, emailfrom, subject, reason, contactid) ";
                        $sql.= "VALUES ('{$updateid}', '0', '{$from_name}', '".mysql_real_escape_string($subject)."', '{$SYSLANG['strPossibleNewIncident']}', '{$contactid}' )";
                        mysql_query($sql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                    }
                    $holdingemailid = mysql_insert_id();
                }
                trigger('TRIGGER_INCIDENT_UPDATED_EXTERNAL', array('incident' => $incidentid));

            }
            else
            {
                if ($incidentid != 0)
                {
                    $bodytext = "[i]Received duplicate email within 15 minutes. Message not stored. Possible mail loop.[/i]";
                    $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, customervisibility, currentowner, currentstatus) ";
                    $sql .= "VALUES ('{$incidentid}', 0, 'emailin', '{$bodytext}', '{$now}', '{$customer_visible}', '{$owner}', 1)";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                }
            }
        }

        //** END UPDATE **//

        // We need to update the links table here as otherwise we have a blank
        //
        foreach ($attachments AS $att)
        {
            $sql = "UPDATE `{$GLOBALS['dbLinks']}` SET origcolref = '{$updateid}' ";
            $sql .= "WHERE linkcolref = '{$att['fileid']}' ";
            $sql .= "AND linktype = 5 ";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        }

        unset($headertext, $newupdate, $attachments, $attachment, $updateobj,
            $bodytext, $message, $incidentid);
    }

    if ($CONFIG['enable_inbound_mail'] == 'POP/IMAP')
    {
        // Delete the message from the mailbox
        if ($mailbox->servertype == 'imap')
        {
            imap_expunge($mailbox->mailbox);
        }
        elseif ($mailbox->servertype == 'pop')
        {
            imap_delete($mailbox->mailbox, '1:*');
            imap_expunge($mailbox->mailbox);
        }

        if ($CONFIG['enable_inbound_mail'] == 'POP/IMAP')
        {
            imap_close($mailbox->mailbox);
        }
    }
}
?>