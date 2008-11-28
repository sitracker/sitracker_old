<?php
// inboundemail.php - Process incoming emails
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Note: if performance is poor with attachments, we should download attachments
//       with the function in fetchSitMail.class.php
// Note2: to be called from auto.php

require ('mime_email.class.php');
require ('fetchSitMail.class.php');
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    @include ('set_include_path.inc.php');
    require ('db_connect.inc.php');
    include ('strings.inc.php');
    require ('functions.inc.php');
}
else
{
    global $CONFIG, $dbFiles, $dbUpdates, $dbTempIncoming, $dbIncidents, $now;
}

//FIXME
$fsdelim = (strstr($CONFIG['attachment_fspath'],"/")) ? "/" : "\\";

function subjectdecode($subject)
{
    if (ereg("=\?.{0,}\?[Bb]\?", $subject))
    {
        $subject = split("=\?.{0,}\?[Bb]\?", $subject);

        foreach ($subject as $key => $value)
        {
            if (ereg("\?=",$value))
            {
                $arrTemp = split("\?=", $value);
                $arrTemp[0] = base64_decode($arrTemp[0]);
                $subject[$key ]= join("", $arrTemp);
            }
        }
        $subject=join("",$subject);
    }

    if (ereg("=\?.{0,}\?Q\?",$subject))
    {
        $subject = imap_utf7_encode($subject);
        $subject = quoted_printable_decode($subject);
        $subject = ereg_replace("=\?.{0,}\?[Qq]\?", "", $subject);
        $subject = ereg_replace("\?=", "", $subject);
    }
    //TODO does this break anything?
    $subject = str_replace("_", " ", $subject);
    return trim($subject);
}

//hack as we have no session
function populate_syslang2()
{
    global $CONFIG;
    // Populate $SYSLANG with system lang
    $file = "{$CONFIG['application_fspath']}includes/i18n/{$CONFIG['default_i18n']}.inc.php";
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
        $rawemail[] = fgets($fp); // , 1024
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


    $mailbox->connect();
    $emails = $mailbox->getNumUnreadEmails();
//     $size = $mailbox->getTotalSize($emails);
}
else
{
    return FALSE;
}
if ($emails > 0)
{
    for ($i = 0; $i < $emails; $i++)
    {
        if ($CONFIG['enable_inbound_mail'] == 'POP/IMAP')
        {
            $rawemail = $mailbox->getMessageHeader($i + 1)."\n";
            $rawemail .= $mailbox->messageBody($i + 1);
            $rawemail = explode("\n", $rawemail);
            if ($mailbox->servertype == 'imap')
            {
				if ($CONFIG['debug'])
				{
					echo 'Archive folder set to: '.$CONFIG['email_archive_folder'];
				}
				if (!empty($CONFIG['email_archive_folder']))
				{
					if ($CONFIG['debug'])
					{
						echo 'Archiving email\n';
					}
					$mailbox->archiveEmail($i + 1);
				}
				else
				{
                	$mailbox->deleteEmail($i + 1);
				}
            }
        }
        // Create and populate the email object
        $email = new mime_email;
        $email->set_emaildata($rawemail);
        unset($rawemail);

        $decoded_email = $email->go_decode();
        if ($CONFIG['debug']) $email->dump(false);
        unset($email);

        //fix for ISO-8859-1 subjects
        $decoded_email->subject = subjectdecode($decoded_email->subject);

		// Extract Incident ID
        if (preg_match('/\[(\d{1,5})\]/',$decoded_email->subject,$m))
        {
       		$incidentid = $m[1];
        }
        
        $customer_visible = 'No';
        $part = 1;

        //** BEGIN WRITE ATTACHMENTS **//
        $attachment = array();

        if (strcasecmp($decoded_email->contenttype, 'multipart/mixed') OR
            strcasecmp($decoded_email->contenttype, 'multipart/alternative'))
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
                if ($block->mime_contentdisposition == 'inline' OR $block->mime_contentdisposition == '')
                {
                    switch (strtolower($block->mime_contenttype))
                    {
                        case 'text/plain':
                            $message .= $block->mime_content;
                        break;

                        case 'text/html':
                            // Only use HTML version if we have no text version
                            if (empty($message)) $message = strip_tags($block->mime_content);
                        break;

                        default:
                            $fsdelim = (strstr($CONFIG['attachment_fspath'],"/")) ? "/" : "\\";
                            $filename = str_replace(' ','_',$block->mime_contentdispositionname);
                            if (empty($filename)) $filename = "part{$part}";

                            $sql = "INSERT into `{$GLOBALS['dbFiles']}` ";
                            $sql .= "( `id` ,`category` ,`filename` ,`size` ,`userid` ,`usertype` ,`shortdescription` ,`longdescription` ,`webcategory` ,`path` ,`downloads` ,`filedate` ,`expiry` ,`fileversion` ,`published` ,`createdby` ,`modified` ,`modifiedby` ) ";
                            $sql .= "VALUES('', '', '{$filename}', '0', '0', '', '', '', '', '', '', NOW(), '', '', '', '0', '', '')";
                            mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                            $fileid = mysql_insert_id();

                            $attachment[] = array('filename' => $filename, 'fileid' => $fileid);
                            $filename = $fileid."-".$filename;

                            // Write the attachment
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

                            if ($CONFIG['debug'])
                            {
                                echo "default:About to write to ".$fa_dir.$filename."\n";
                            }

                            if (is_writable($fa_dir)) //File doesn't exist yet .$filename
                            {
                                $fwp = fopen($fa_dir.$filename, 'a');
                                // FIXME not actually writing content here yet
                                //fwrite($fwp, "This is a test\n");
                                fwrite($fwp, $block->mime_content);
                                fclose($fwp);
                            }
                            elseif ($CONFIG['debug'])
                            {
                                echo "NOT WRITABLE $filename\n";
                            }

                            $sql = "INSERT INTO `{$GLOBALS['dbLinks']}` (`linktype`, `origcolref`, `linkcolref`, `direction`, `userid`) ";
                            $sql .= "VALUES('5', '{$updateid}', '{$fileid}', 'left', '0') ";
                            mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                    }
                }
                else
                {
                    //FIXME 3.41 functionise this and above without breaking globals etc
                    $fsdelim = (strstr($CONFIG['attachment_fspath'],"/")) ? "/" : "\\";;

                    $filename = str_replace(' ','_',$block->mime_contentdispositionname);
                    if (empty($filename)) $filename = "part{$part}";

                    $sql = "INSERT into `{$GLOBALS['dbFiles']}` ";
                    $sql .= "( `id` ,`category` ,`filename` ,`size` ,`userid` ,`usertype` ,`shortdescription` ,`longdescription` ,`webcategory` ,`path` ,`downloads` ,`filedate` ,`expiry` ,`fileversion` ,`published` ,`createdby` ,`modified` ,`modifiedby` ) ";
                    $sql .= "VALUES('', '', '{$filename}', '0', '0', '', '', '', '', '', '', NOW(), '', '', '', '0', '', '')";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    $fileid = mysql_insert_id();

                    $attachment[] = array('filename' => $filename, 'fileid' => $fileid);
                    $filename = $fileid."-".$filename;

                    // Write the attachment
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

                    if ($CONFIG['debug'])
                    {
                        echo "else:About to write to ".$fa_dir.$filename."\n";
                    }

                    if (is_writable($fa_dir)) //File doesn't exist yet .$filename
                    {
                        $fwp = fopen($fa_dir.$filename, 'a');
                        // FIXME not actually writing content here yet
                        //fwrite($fwp, "This is a test\n");
                        fwrite($fwp, $block->mime_content);
                        fclose($fwp);
                    }
                    elseif ($CONFIG['debug'])
                    {
                        echo "NOT WRITABLE $filename\n";
                    }

                    $sql = "INSERT INTO `{$GLOBALS['dbLinks']}` (`linktype`, `origcolref`, `linkcolref`, `direction`, `userid`) ";
                    $sql .= "VALUES('5', '{$updateid}', '{$fileid}', 'left', '0') ";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                }
            }
        }
        //** END WRITE ATTACHMENTS **//

        //** BEING UPDATE INCIDENT **//
        $headertext = '';
        // Build up header text to append to the incident log
        if (!empty($decoded_email->from))
        {
            $headertext = "From: [b]".htmlentities(mysql_real_escape_string($decoded_email->from), ENT_NOQUOTES)."[/b]\n";
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

        $count_attachments = count($attachment);
        if ($count_attachments >= 1)
        {
            $headertext .= $SYSLANG['strAttachments'].": [b]{$count_attachments}[/b] - ";
            $c = 1;
            foreach ($attachment AS $att)
            {
                $headertext .= "[[att={$att['fileid']}]]{$att['filename']}[[/att]]";
                if ($c < $count_attachments) $headertext .= ", ";
                $c++;
            }
            $headertext .= "\n";
        }
        //** END UPDATE INCIDENT **//

        //** BEGIN UPDATE **//
        if (empty($message)) $message = $decoded_email->emailtextplain;
        $bodytext = $headertext . "<hr>" . mysql_real_escape_string($message);

        // Strip excessive line breaks
        $message = str_replace("\n\n\n\n","\n", $message);
        $message = str_replace(">\n>\n>\n>\n",">\n", $message);

        if (empty($incidentid))
        {
            // Add entry to the incident update log
            $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, customervisibility, currentstatus) ";
            $sql .= "VALUES ('{$incidentid}', 0, 'emailin', '{$bodytext}', '{$now}', '$customer_visible', 1 )";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            $updateid = mysql_insert_id();

            //new call
            $sql = "INSERT INTO `{$dbTempIncoming}` (updateid, incidentid, `from`, emailfrom, subject, reason, contactid) ";
            $sql.= "VALUES ('{$updateid}', '0', '{$decoded_email->from_email}', '{$decoded_email->from_name}', '".mysql_real_escape_string($decoded_email->subject)."', '{$SYSLANG['strPossibleNewIncident']}', '{$contactid}' )";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            trigger('TRIGGER_INCIDENT_UPDATED_EXTERNAL', array('incident' => $incidentid));
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
                        $sql = "INSERT INTO `{$GLOBALS['dbTempIncoming']}` (updateid, incidentid, emailfrom, subject, reason, contactid) ";
                        $sql.= "VALUES ('{$updateid}', '0', '{$decoded_email->from_name}', '".mysql_real_escape_string($decoded_email->subject)."', '{$reason}', '$contactid' )";
                        mysql_query($sql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                    }
                    else
                    {
                        //new call
                        $sql = "INSERT INTO `{$dbTempIncoming}` (updateid, incidentid, emailfrom, subject, reason, contactid) ";
                        $sql.= "VALUES ('{$updateid}', '0', '{$decoded_email->from_name}', '".mysql_real_escape_string($decoded_email->subject)."', '{$SYSLANG['strPossibleNewIncident']}', '{$contactid}' )";
                        mysql_query($sql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                    }
                    $holdingemailid = mysql_insert_id();
                    trigger('TRIGGER_NEW_HELD_EMAIL', array('holdingemailid' => $holdingemailid));
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

        //** END UPDATE **//

        // We need to update the links table here as otherwise we have a blank
        //
        foreach ($attachment AS $att)
        {
            $sql = "UPDATE `{$GLOBALS['dbLinks']}` SET origcolref = '{$updateid}'";
            $sql .= "WHERE linkcolref = '{$att['fileid']}' ";
            $sql .= "AND linktype = 5 ";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        }

        unset($headertext, $newupdate, $attachments, $attachment, $updateobj,
            $bodytext, $message, $incidentid);
    }

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
?>
