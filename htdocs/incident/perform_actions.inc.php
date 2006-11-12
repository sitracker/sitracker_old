<?php
// perform_actions.inc.php - Perfrom actions on the incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>
//         Ivan Lucas  <ivanlucas[at]users.sourceforge.net>

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

switch($action)
{
    case 'update':
        // Update the incident
    
        $time = time();
        // External variables
        $bodytext = cleanvar($_REQUEST['bodytext'],FALSE,FALSE);
        $target = cleanvar($_POST['target']);
        $updatetype = cleanvar($_POST['updatetype']);
        $newstatus = cleanvar($_POST['newstatus']);
        $nextaction = cleanvar($_POST['nextaction']);
        $newpriority = cleanvar($_POST['newpriority']);
        $cust_vis = cleanvar($_POST['cust_vis']);
        $timetonextaction_none = cleanvar($_POST['timetonextaction_none']);
        $timetonextaction_days = cleanvar($_POST['timetonextaction_days']);
        $timetonextaction_hours = cleanvar($_POST['timetonextaction_hours']);
        $timetonextaction_minutes = cleanvar($_POST['timetonextaction_minutes']);
        $year = cleanvar($_POST['year']);
        $month = cleanvar($_POST['month']);
        $day = cleanvar($_POST['day']);
    
        if (empty($newpriority)) $newpriority  = incident_priority($id);
    
        // update incident
        switch ($timetonextaction_none)
        {
            case 'none':
                $timeofnextaction = 0;
            break;
    
            case 'time':
                if ($timetonextaction_days<1 && $timetonextaction_hours<1 && $timetonextaction_minutes<1)
                {
                    $timeofnextaction = 0;
                }
                else
                {
                    $timeofnextaction = calculate_time_of_next_action($timetonextaction_days, $timetonextaction_hours, $timetonextaction_minutes);
                }
            break;
    
            case 'date':
                // $now + ($days * 86400) + ($hours * 3600) + ($minutes * 60);
                $unixdate=mktime(9,0,0,$month,$day,$year);
                $now = time();
                $timeofnextaction = $unixdate;
                if ($timeofnextaction<0) $timeofnextaction=0;
            break;
    
            default:
                $timeofnextaction = 0;
            break;
        }
    
        // Put text into body of update for field changes (reverse order)
        // delim first
        $bodytext = "<hr>" . $bodytext;
        $oldstatus=incident_status($id);
        $oldtimeofnextaction=incident_timeofnextaction($id);
        if ($newstatus != $oldstatus)
            $bodytext = "Status: ".incidentstatus_name($oldstatus)." -&gt; <b>" . incidentstatus_name($newstatus) . "</b>\n\n" . $bodytext;
        if ($newpriority != incident_priority($id))
            $bodytext = "New Priority: <b>" . priority_name($newpriority) . "</b>\n\n" . $bodytext;
        if ($timeofnextaction > ($oldtimeofnextaction+60))
        {
            $timetext = "Next Action Time: ";
            if (($oldtimeofnextaction-$now)<1) $timetext.="None";
            else $timetext.=date("D jS M Y @ g:i A", $oldtimeofnextaction);
            $timetext.=" -&gt; <b>";
            if ($timeofnextaction<1) $timetext.="None";
            else $timetext.=date("D jS M Y @ g:i A", $timeofnextaction);
                $timetext.="</b>\n\n";
            $bodytext=$timetext.$bodytext;
        }
        // was '$attachment'
        if ($_FILES['attachment']['name']!='' && isset($_FILES['attachment']['name'])==TRUE)
        {
            $bodytext = "Attachment: [[att]]{$_FILES['attachment']['name']}[[/att]]\n".$bodytext;
        }
        // Debug
        ## if ($target!='') $bodytext = "Target: $target\n".$bodytext;
    
        // Check the updatetype field, if it's blank look at the target
        if (empty($updatetype))
        {
            switch($target)
            {
                case 'actionplan': $updatetype='actionplan';  break;
                case 'probdef': $updatetype='probdef';  break;
                case 'solution': $updatetype='solution';  break;
                default: $updatetype='research';  break;
            }
        }
    
        // Force reviewmet to be visible
        if ($updatetype=='reviewmet') $cust_vis='yes';
    
        // visible update
        if ($cust_vis == "yes")
        {
            $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, currentstatus, customervisibility, nextaction) ";
            $sql .= "VALUES ('$id', '$sit[2]', '$updatetype', '$bodytext', '$time', '$newstatus', 'show' , '$nextaction')";
        }
        // invisible update
        else
        {
            $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, currentstatus, nextaction) ";
            $sql .= "VALUES ($id, $sit[2], '$updatetype', '$bodytext', $time, '$newstatus', '$nextaction')";
        }
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
        $sql = "UPDATE incidents SET status='$newstatus', priority='$newpriority', lastupdated='$time', timeofnextaction='$timeofnextaction' WHERE id='$id'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
        // Handle meeting of service level targets
        switch ($target)
        {
            case 'none':
                // do nothing
                $sql = '';
            break;
    
            case 'initialresponse':
                $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'initialresponse','The Initial Response has been made.')";
            break;
    
            case 'probdef':
                $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'probdef','The problem has been defined.')";
            break;
    
            case 'actionplan':
                $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'actionplan','An action plan has been made.')";
            break;
    
            case 'solution':
                $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'solution','The incident has been resolved or reprioritised.\nThe issue should now be brought to a close or a new problem definition created within the service level.')";
            break;
        }
        if (!empty($sql))
        {
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }
        if ($target!='none')
        {
            // Reset the slaemail sent column, so that email reminders can be sent if the new sla target goes out
            $sql = "UPDATE incidents SET slaemail='0' WHERE id='$id' LIMIT 1";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }
    
        // attach file
        $att_max_filesize = return_bytes($CONFIG['upload_max_filesize']);
        $incident_attachment_fspath = $CONFIG['attachment_fspath'] . $id;
        if ($_FILES['attachment']['name'] != "")
        {
            // make incident attachment dir if it doesn't exist
            $umask=umask(0000);
            if (!file_exists($CONFIG['attachment_fspath'] . "$id"))
            {
                $mk=@mkdir($CONFIG['attachment_fspath'] ."$id", 0770);
                if (!$mk) throw_error('Failed creating incident attachment directory: ',$incident_attachment_fspath .$id);
            }
            $mk=@mkdir($CONFIG['attachment_fspath'] .$id . "/$now", 0770);
            if (!$mk) throw_error('Failed creating incident attachment (timestamp) directory: ',$incident_attachment_fspath .$id . "/$now");
            umask($umask);
            $newfilename = $incident_attachment_fspath.'/'.$now.'/'.$_FILES['attachment']['name'];
    
            // Move the uploaded file from the temp directory into the incidents attachment dir
            $mv=move_uploaded_file($_FILES['attachment']['tmp_name'], $newfilename);
            if (!$mv) trigger_error('!Error: Problem moving attachment from temp directory to: '.$newfilename, E_USER_WARNING);
    
            //$mv=move_uploaded_file($attachment, "$filename");
            //if (!mv) throw_error('!Error: Problem moving attachment from temp directory:',$filename);
    
            // Check file size before attaching
            if ($_FILES['attachment']['size'] > $att_max_filesize)
            {
                throw_error('User Error: Attachment too large or file upload error - size:',$_FILES['attachment']['size']);
                // throwing an error isn't the nicest thing to do for the user but there seems to be no guaranteed
                // way of checking file sizes at the client end before the attachment is uploaded. - INL
            }
        }
        if (!$result)
        {
            include('includes/incident_html_top.inc');
            echo "<p class='error'>Update Failed</p>\n";
            include('includes/incident_html_bottom.inc');
        }
        else
        {
            journal(4,'Incident Updated', "Incident $id Updated", 2, $id);
            confirmation_page("2", "incident_details.php?id=" . $id, "<h2>Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
        }
        break;
    case 'close':
        // External variables
        $closingstatus = cleanvar($_POST['closingstatus']);
        $summary = cleanvar($_POST['summary']);
        $id = cleanvar($_POST['id']);
        $solution = cleanvar($_POST['solution']);
        $kbarticle = cleanvar($_POST['kbarticle']);
        $kbtitle = cleanvar($_POST['kbtitle']);
        $symptoms = cleanvar($_POST['symptoms']);
        $cause = cleanvar($_POST['cause']);
        $question = cleanvar($_POST['question']);
        $answer = cleanvar($_POST['answer']);
        $workaround = cleanvar($_POST['workaround']);
        $status = cleanvar($_POST['status']);
        $additional = cleanvar($_POST['additional']);
        $references = cleanvar($_POST['references']);
        $wait = cleanvar($_POST['wait']);
        $send_email = cleanvar($_POST['send_email']);
    
        // Close the incident
        $errors = 0;
    
        // check for blank closing status field
        if ($closingstatus == 0)
        {
            $errors = 1;
            $error_string = "<p class='error'>You must select a closing status</p>\n";
        }
        if ($_REQUEST['summary']=='' && $_REQUEST['solution']=='')
        {
            $errors = 1;
            $error_string = "<p class='error'>You must enter some text for both summary and solution</P>\n";
        }
    
        if ($errors == 0)
        {
            $addition_errors = 0;
    
            // update incident
            if ($wait=='yes')
            {
                // mark incident as awaiting closure
                $timeofnextaction=$now + $CONFIG['closure_delay'];
                $sql = "UPDATE incidents SET status='7', lastupdated='$now', timeofnextaction='$timeofnextaction' WHERE id='$id'";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            }
            else
            {
                // mark incident as closed
                $sql = "UPDATE incidents SET status='2', closingstatus='$closingstatus', lastupdated='$now', closed='$now' WHERE id='$id'";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            }
            if (!$result)
            {
                $addition_errors = 1;
                $addition_errors_string .= "<p class='error'>Update of incident failed</p>\n";
            }
    
            // add update(s)
            if ($addition_errors == 0)
            {
                ## if ($cust_vis == "yes") $show='show'; else $show='hide';
                if ($_REQUEST['kbarticle']!='yes')
                {
                    // No KB Article, so just add updates to log for Summary and Solution
                    if (strlen($_REQUEST['summary'])>3)
                    {
                        // Problem Definition
                        $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, customervisibility) ";
                        $sql .= "VALUES ('$id', '$sit[2]', 'probdef', '$summary', '$now', 'hide')";
                        $result = mysql_query($sql);
                        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                    }
                    if (strlen($_REQUEST['solution'])>3)
                    {
                        // Final Solution
                        $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, customervisibility) ";
                        $sql .= "VALUES ('$id', '$sit[2]', 'solution', '$solution', '$now', 'hide')";
                        $result = mysql_query($sql);
                        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                    }
                }
    
                // Meet service level 'solution'
                $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, customervisibility, sla, bodytext) ";
                $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '{$sit[2]}', 'show', 'solution','')";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
                //
                if ($wait=='yes')
                {
                    // Update - mark for closure
                    $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp) ";
                    $sql .= "VALUES ('$id', '{$sit[2]}', 'closing', 'Marked for Closure', '$now')";
                    $result = mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }
                else
                {
                    // Update - close immediately
                    $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp) ";
                    $sql .= "VALUES ('$id', '{$sit[2]}', 'closing', 'Incident Closed', '$now')";
                    $result = mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }
    
                // Make Journal Entry
                journal(CFG_LOGGING_NORMAL,'Incident Closed',"Incident $id was closed",CFG_JOURNAL_SUPPORT,$id);
    
                if (!$result)
                {
                    $addition_errors = 1;
                    $addition_errors_string .= "<p class='error'>Addition of incident update failed</p>\n";
                }
    
                //notify related inicdents this has been closed
                $sql = "SELECT distinct (relatedid) FROM relatedincidents,incidents WHERE incidentid = '$id' ";
                $sql .= "AND incidents.id = relatedincidents.relatedid AND incidents.status != 2";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
                $relatedincidents;
    
                while($a = mysql_fetch_array($result))
                {
                    $relatedincidents[] = $a[0];
                }
    
                $sql = "SELECT distinct (incidentid) FROM relatedincidents, incidents WHERE relatedid = '$id' ";
                $sql .= "AND incidents.id = relatedincidents.incidentid AND incidents.status != 2";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
                while($a = mysql_fetch_array($result))
                {
                    $relatedincidents[] = $a[0];
                }
                if (is_array($relatedincidents))
                {
                    $uniquearray = array_unique($relatedincidents);
    
                    foreach($uniquearray AS $relatedid)
                    {
                        //dont care if I'm related to myself
                        if($relatedid != $id)
                        {
                            $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp) ";
                            $sql .= "VALUES ('$relatedid', '{$sit[2]}', 'research', 'New Status: [b]Active[/b]<hr>\nRelated incident [$id] has been closed', '$now')";
                            $result = mysql_query($sql);
                            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
                            $sql = "UPDATE incidents SET status = 1 WHERE id = '$relatedid'";
                            $result = mysql_query($sql);
                            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                        }
                    }
                }
                //tidy up temp reassigns
                $sql = "DELETE FROM tempassigns WHERE incidentid = '$id'";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            }
            $bodytext = "Closing Status: <b>" . closingstatus_name($closingstatus) . "</b>\n\n" . $bodytext;
    
            if ($addition_errors == 0)
            {
                if ($CONFIG['feedback_form'] != '' AND $CONFIG['feedback_form'] > 0)
                    create_incident_feedback($CONFIG['feedback_form'], $id);
    
                plugin_do('incident_closed');
    
                if ($send_engineer_email == 'yes')
                {
                    $eml=send_template_email('INCIDENT_CLOSED_EXTERNAL', $id);  // close with external engineer
                    if (!$eml) throw_error('!Error: Failed while sending close with engineer email, error code: ', $eml);
                }
    
                if ($send_email == 'yes')
                {
                    if ($wait=='yes')
                    {
                        // send awaiting closure email
                        $eml=send_template_email('INCIDENT_CLOSURE', $id);  // awaiting closure
                        if (!$eml) throw_error('!Error: Failed while sending awaiting closure email to customer, error code:', $eml);
                        // confirmation_page("2", "incident_details.php?id=" . $id, "<p class=pagetitle>Incident $id Marked for Closure and Email Sent</p><p align='center'>Please wait while you are redirected...</p>");
                    }
                    else
                    {
                        // send incident closed email
                        $eml=send_template_email('INCIDENT_CLOSED', $id);  // incident closed
                        if (!$eml) throw_error('!Error: Failed while sending incident closed email to customer, error code:', $eml);
                    }
                }
    
                // Check for knowledge base stuff, prior to confirming:
                if ($_REQUEST['kbarticle']=='yes')
                {
                    $sql = "INSERT INTO kbarticles (doctype, title, distribution, author, published, keywords) VALUES ";
                    $sql .= "('1', ";
                    $sql .= "'{$kbtitle}', ";
                    $sql .= "'public', ";
                    $sql .= "'".mysql_escape_string($sit[2])."', ";
                    $sql .= "'".date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y')))."', ";
                    $sql .= "'[$id]') ";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                    $docid = mysql_insert_id();
    
                    // Update the incident to say that a KB article was created, with the KB Article number
                    $update = "<b>Knowledge base article</b> created from this incident, see: {$CONFIG['kb_id_prefix']}".leading_zero(4,$docid);
                    $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp) ";
                    $sql .= "VALUES ('$id', '$sit[2]', 'default', '$update', '$now')";
                    $result = mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
    
                    // Get softwareid from Incident record
                    $sql = "SELECT softwareid FROM incidents WHERE id='$id'";
                    $result=mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                    list($softwareid)=mysql_fetch_row($result);
    
                    if (!empty($_POST['summary'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Summary', '1', '{$summary}', 'private') ";
                    if (!empty($_POST['symptoms'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Symptoms', '1', '{$symptoms}', 'private') ";
                    if (!empty($_POST['cause'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Cause', '1', '{$cause}', 'private') ";
                    if (!empty($_POST['question'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Question', '1', '{$question}', 'private') ";
                    if (!empty($_POST['answer'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Answer', '1', '{$answer}', 'private') ";
                    if (!empty($_POST['solution'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Solution', '1', '{$solution}', 'private') ";
                    if (!empty($_POST['workaround'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Workaround', '1', '{$workaround}', 'private') ";
                    if (!empty($_POST['status'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Status', '1', '{$status}', 'private') ";
                    if (!empty($_POST['additional'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Additional Information', '1', '{$additional}', 'private') ";
                    if (!empty($_POST['references'])) $query[]="INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'References', '1', '{$references}', 'private') ";
    
                    if (count($query) < 1) $query[] = "INSERT INTO kbcontent (docid, ownerid, headerstyle, header, contenttype, content, distribution) VALUES ('$docid', '".mysql_escape_string($sit[2])."', 'h1', 'Summary', '1', 'Enter details here...', 'restricted') ";
    
                    foreach ($query AS $sql)
                    {
                        mysql_query($sql);
                        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                    }
    
                    // Add Software Record
                    if ($softwareid>0)  $sql="INSERT INTO kbsoftware (docid,softwareid) VALUES ('$docid', '$softwareid')";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
                    journal(CFG_LOGGING_NORMAL, 'KB Article Added', "KB Article $docid was added", CFG_JOURNAL_KB, $docid);
    
                    confirmation_page("5", "incident_details.php?id=" . $id, "<h2>Incident $id Closure Requested<h2><p align='center'>Knowledge Base Article {$CONFIG['kb_id_prefix']}{$docid} created.</h2><p align='center'>Please wait while you are redirected...</p>");
                }
                else confirmation_page("2", "incident_details.php?id=" . $id, "<h2>Incident $id Closure Requested</h2><p align='center'>Please wait while you are redirected...</p>");
            }
            else
            {
                include('incident_html_top.inc.php');
                echo $addition_errors_string;
                include('incident_html_bottom.inc.php');
            }
        }
        else
        {
            include('incident_html_top.inc.php');
            echo $error_string;
            include('incident_html_bottom.inc.php');
        }

        break;
    case 'save-edit':
        // External variables
        $externalid = cleanvar($_POST['externalid']);
        $type = cleanvar($_POST['type']);
        $ccemail = stripslashes(cleanvar($_POST['ccemail']));
        $escalationpath = cleanvar($_POST['escalationpath']);
        $externalengineer = stripslashes(cleanvar($_POST['externalengineer']));
        $externalemail = stripslashes(cleanvar($_POST['externalemail']));
        $title = stripslashes(cleanvar($_POST['title']));
        $contact = stripslashes(cleanvar($_POST['contact']));
        $software = cleanvar($_POST['software']);
        $productversion = cleanvar($_POST['productversion']);
        $productservicepacks = cleanvar($_POST['productservicepacks']);
        $id = cleanvar($_POST['id']);
        $oldtitle = stripslashes(cleanvar($_POST['oldtitle']));
        $oldcontact = stripslashes(cleanvar($_POST['oldcontact']));
        $maintid = cleanvar($_POST['maintid']);
        $oldescalationpath = cleanvar($_POST['oldescalationpath']);
        $oldexternalid = cleanvar($_POST['oldexternalid']);
        $oldexternalemail = stripslashes(cleanvar($_POST['oldexternalemail']));
        $oldproduct = cleanvar($_POST['oldproduct']);
        $oldproductversion = cleanvar($_POST['oldproductversion']);
        $oldproductservicepacks = cleanvar($_POST['oldproductservicepacks']);
        $oldccemail = stripslashes(cleanvar($_POST['oldccemail']));
        $oldexternalengineer = stripslashes(cleanvar($_POST['oldexternalengineer']));
        $oldsoftware = cleanvar($_POST['oldsoftware']);
    
        // Edit the incident
        if ($type == "Support")  // FIXME: This IF might not be needed since sales incidents are obsolete INL 29Apr03
        {
            // check form input
            $errors = 0;
    
            // check for blank contact
            if ($contact == 0)
            {
                $errors = 1;
                $error_string .= "<p class='error'>You must select a contact</p>\n";
            }
            // check for blank title
            if ($title == "")
            {
                $errors = 1;
                $error_string .= "<p class='error'>You must enter a title</p>\n";
            }
    
            if ($errors > 0)
            {
                echo "<div>$bodytext</div>";
            }
    
            if ($errors == 0)
            {
                $addition_errors = 0;
    
                // update support incident
                $sql = "UPDATE incidents SET externalid='$externalid', ccemail='$ccemail', ";
                $sql .= "escalationpath='$escalationpath', externalengineer='$externalengineer', externalemail='$externalemail', title='$title', ";
                $sql .= "contact='$contact', softwareid='$software', productversion='$productversion', ";
                $sql .= "productservicepacks='$productservicepacks' WHERE id='$id'";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                if (!$result)
                {
                    $addition_errors = 1;
                    $addition_errors_string .= "<p class='error'>Update of incident failed</p>\n";
                }
    
                if ($addition_errors == 0)
                {
                    // dump details to incident update
                    if ($oldtitle != $title) $header .= "Title: $oldtitle -&gt; <b>$title</b>\n";
                    if ($oldcontact != $contact)
                    {
                        $contactname = contact_realname($contact);
                        $contactsite = contact_site($contact);
                        $header .= "Contact: " . contact_realname($oldcontact) . " -&gt; <b>{$contactname}</b>\n";
                        $maintsiteid = maintenance_siteid(incident_maintid($id));
                        if ($maintsiteid > 0 AND contact_siteid($contact) != $maintsiteid)
                        {
                            $maintcontactsite = site_name($maintsiteid);
                            $header .= "Assigned to <b>{$contactname} of {$contactsite}</b> on behalf of {$maintcontactsite} (The contract holder)\n";
                        }
                    }
                    if ($oldexternalid != $externalid)
                    {
                        $header .= "External ID: ";
                        if ($oldexternalid != "")
                            $header .= $oldexternalid;
                        else
                            $header .= "None";
                        $header .= " -&gt; <b>";
                        if ($externalid != "")
                            $header .= $externalid;
                        else
                            $header .= "None";
                        $header .= "</b>\n";
                    }
                    $escalationpath=db_read_column('name', 'escalationpaths', $escalationpath);
                    if ($oldccemail != $ccemail) $header .= "CC Email: " . $oldccemail . " -&gt; <b>" . $ccemail . "</b>\n";
                    if ($oldescalationpath != $escalationpath) $header .= "Escalation: " . $oldescalationpath . " -&gt; <b>" . $escalationpath . "</b>\n";
                    if ($oldexternalengineer != $externalengineer) $header .= "External Engineer: " . $oldexternalengineer . " -&gt; <b>" . $externalengineer . "</b>\n";
                    if ($oldexternalemail != $externalemail) $header .= "External email: " . $oldexternalemail . " -&gt; <b>" . $externalemail . "</b>\n";
                    if ($oldsoftware != $software) $header .= "Software: ".software_name($oldsoftware)." -&gt; <b>".software_name($software)."</b>\n";
                    if ($oldproductversion != $productversion) $header .= "Software Version: $oldproductversion -&gt; <b>$productversion</b>\n";
                    if ($oldproductservicepacks != $productservicepacks) $header .= "Service Packs Applied: $oldproductservicepacks -&gt; <b>$productservicepacks</b>\n";
    
                    if (!empty($header)) $header .= "<hr>";
                    $bodytext = $header . $bodytext;
                    $bodytext = mysql_escape_string($bodytext);
                    $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp) ";
                    $sql .= "VALUES ('$id', '$sit[2]', 'editing', '$bodytext', '$now')";
                    $result = mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
                    if (!$result)
                    {
                        $addition_errors = 1;
                        $addition_errors_string .= "<p class='error'>Addition of incident update failed</p>\n";
                    }
    
                    plugin_do('incident_edited');
                }
    
                if ($addition_errors == 0)
                {
                    journal(CFG_LOGGING_NORMAL, 'Incident Edited', "Incident $id was edited", CFG_JOURNAL_INCIDENTS, $id);
                    confirmation_page("2", "incident_details.php?id=" . $id, "<h2>Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
                }
                else
                {
                    include('incident_html_top.inc.php');
                    echo $addition_errors_string;
                    include('incident_html_bottom.inc.php');
                }
            }
            else
            {
                include('incident_html_top.inc.php');
                echo $error_string;
                include('incident_html_bottom.inc.php');
            }
        }

        break;
    case 'reassign':
        $bodytext = cleanvar($_REQUEST['bodytext']);
        $backupid = cleanvar($_REQUEST['backupid']);
        $originalid = cleanvar($_REQUEST['originalid']);
        $reason = cleanvar($_REQUEST['reason']);
        // External variables
        $tempnewowner = cleanvar($_REQUEST['tempnewowner']);
        $permnewowner = cleanvar($_REQUEST['permnewowner']);
        $newstatus = cleanvar($_REQUEST['newstatus']);
        $id = cleanvar($_REQUEST['id']);
    
        // Reassign the incident
        if (($_REQUEST['assign']=='tempassign' AND user_accepting($tempnewowner) == "Yes")
            OR ($_REQUEST['assign']=='permassign' AND user_accepting($permnewowner) == "Yes")
            OR ($_REQUEST['assign']=='permassign' AND $permnewowner == $sit[2])
            OR ($_REQUEST['assign']=='tempassign' AND $tempnewowner == $sit[2])
            OR ($_REQUEST['assign']=='deltempassign')
            OR (user_permission($sit[2],40)==TRUE))  // Force reassign
        {
            $oldstatus=incident_status($id);
            if ($newstatus != $oldstatus)
            $bodytext = "Status: ".incidentstatus_name($oldstatus)." -&gt; <b>" . incidentstatus_name($newstatus) . "</b>\n\n" . $bodytext;
    
            // update incident
            $sql = "UPDATE incidents SET ";
            if ($_REQUEST['assign']=='tempassign') $sql .= "towner='{$tempnewowner}', ";
            elseif ($_REQUEST['assign']=='deltempassign') $sql .= "towner='0', ";
            elseif ($_REQUEST['assign']=='permassign') $sql .= "owner='{$permnewowner}', towner='0', "; // perm assign removed temp one
            else $sql .= "owner='{$permnewowner}', towner='0', "; // perm assign removed temp one
            $sql .= "status='$newstatus', lastupdated='$now' WHERE id='$id' LIMIT 1";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
            // add update
            if ($_REQUEST['assign']=='tempassign')
            {
                $assigntype='tempassigning';
                if (strtolower(user_accepting($tempnewowner)) != "yes")
                    $bodytext = "(Incident temp assignment was forced because the user was not accepting)<hr>\n" . $bodytext;
            }
            else
            {
                $assigntype='reassigning';
                if (strtolower(user_accepting($permnewowner)) != "yes")
                    $bodytext = "(Incident assignment was forced because the user was not accepting)<hr>\n" . $bodytext;
            }
            if ($_REQUEST['cust_vis']=='yes') $customervisibility='show';
            else $customervisibility='hide';
    
    
            $sql  = "INSERT INTO updates (incidentid, userid, bodytext, type, timestamp, currentowner, currentstatus, customervisibility) ";
            $sql .= "VALUES ($id, $sit[2], '$bodytext', '$assigntype', '$now', ";
            if ($_REQUEST['assign']=='permassign') $sql .= "'$permnewowner', ";
            elseif ($_REQUEST['assign']=='deltempassign') $sql .= "'{$sit[2]}', ";
            else $sql .= "'$tempnewowner', ";
            $sql .= "'$newstatus', '$customervisibility')";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
            // Remove any tempassigns that are pending for this incident
            $sql = "DELETE FROM tempassigns WHERE incidentid='$id'";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
            $newowner = '';
            if($_REQUEST['assign']=='permassign') $newowner = $permnewowner;
            else if($_REQUESR['assign']<>'deltempassign') $newowner = $tempnewowner; //not interested in deltemp state
    
            if(!empty($newowner))
            {
                if(user_notification_on_reassign($newowner)=='true')
                {
                    send_template_email('INCIDENT_REASSIGNED_USER_NOTIFY', $id);
                }
            }
    
            journal(CFG_LOGGING_FULL,'Incident Reassigned', "Incident $id reassigned to user id $newowner", CFG_JOURNAL_SUPPORT, $id);
    
            if (!$result)
            {
                include('includes/incident_html_top.inc');
                echo "<p class='error'>Reassignment Failed</p>\n";
                include('includes/incident_htmlfooter.inc.php');
            }
            else  confirmation_page("2", "incident_details.php?id=" . $id, "<h2>Reassignment Successful</h2><h5>Please wait while you are redirected...</h5>");
        }
        else
        {
            confirmation_page("4", "reassign_incident.php?id={$id}", "<h2 class='error'>Error</h2><h3>That user is not accepting incidents.</h3><h5>Please wait while you are returned...</h5>");
        }

        break;


}

?>