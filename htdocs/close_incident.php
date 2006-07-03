<?php
// close_incident.php - Display a form for closing an incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=18; //  Close Incidents

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External Variables
$id = cleanvar($_REQUEST['id']);


// No submit detected show closure form
if (empty($_REQUEST['process']))
{
    $incident_title=incident_title($id);
    $title = 'Close: '.$id . " - " . $incident_title;
    include('incident_html_top.inc.php');
    ?>
    <script type="text/javascript">
    <!--
    function enablekb()
    {
        if (document.closeform.kbtitle.disabled==true)
        {
            // Enable KB
            document.closeform.kbtitle.disabled=false;
            //document.closeform.cust_vis1.disabled=true;
            //document.closeform.cust_vis1.checked=true;
            //document.closeform.cust_vis2.checked=true;
            //document.closeform.cust_vis2.disabled=true;
            // Enable KB includes
            //document.closeform.incsummary.disabled=false;
            document.closeform.summary.disabled=false;
            document.closeform.incsymptoms.disabled=false;
            document.closeform.symptoms.disabled=false;
            document.closeform.inccause.disabled=false;
            document.closeform.cause.disabled=false;
            document.closeform.incquestion.disabled=false;
            document.closeform.question.disabled=false;
            document.closeform.incanswer.disabled=false;
            document.closeform.answer.disabled=false;
            //document.closeform.incsolution.disabled=false;
            document.closeform.solution.disabled=false;
            document.closeform.incworkaround.disabled=false;
            document.closeform.workaround.disabled=false;
            document.closeform.incstatus.disabled=false;
            document.closeform.status.disabled=false;
            document.closeform.incadditional.disabled=false;
            document.closeform.additional.disabled=false;
            document.closeform.increferences.disabled=false;
            document.closeform.references.disabled=false;
            if (document.all)
            {
            document.all('helptext').innerHTML = "Select the sections you'd like to include in the article by checking the boxes beside each heading, you can add further sections later.  You don't need to include all sections, just use the ones that are relevant.<br /><strong>Knowledge Base Article</strong>:";
            }
            else if (document.getElementById)
            {
            document.getElementById('helptext').innerHTML = "Select the sections you'd like to include in the article by checking the boxes beside each heading, you can add further sections later.  You don't need to include all sections, just use the ones that are relevant.<br /><strong>Knowledge Base Article</strong>:";
            }
        }
        else
        {
            // Disable KB
            document.closeform.kbtitle.disabled=true;
            //document.closeform.cust_vis1.disabled=false;
            //document.closeform.cust_vis2.disabled=false;
            // Disable KB includes
            document.closeform.incsymptoms.checked=false;
            document.closeform.incsymptoms.disabled=true;
            document.closeform.symptoms.disabled=true;
            document.closeform.inccause.checked=false;
            document.closeform.inccause.disabled=true;
            document.closeform.cause.disabled=true;
            document.closeform.incquestion.checked=false;
            document.closeform.incquestion.disabled=true;
            document.closeform.question.disabled=true;
            document.closeform.incanswer.checked=false;
            document.closeform.incanswer.disabled=true;
            document.closeform.answer.disabled=true;
            // document.closeform.incsolution.checked=false;
            // document.closeform.incsolution.disabled=true;
            // document.closeform.solution.disabled=true;
            document.closeform.incworkaround.checked=false;
            document.closeform.incworkaround.disabled=true;
            document.closeform.workaround.disabled=true;
            document.closeform.incstatus.checked=false;
            document.closeform.incstatus.disabled=true;
            document.closeform.status.disabled=true;
            document.closeform.incadditional.checked=false;
            document.closeform.incadditional.disabled=true;
            document.closeform.additional.disabled=true;
            document.closeform.increferences.checked=false;
            document.closeform.increferences.disabled=true;
            document.closeform.references.disabled=true;
            document.closeform.incworkaround.checked=false;
            document.closeform.incworkaround.disabled=true;
            document.closeform.workaround.disabled=true;
            if (document.all)
            {
            document.all('helptext').innerHTML = "Enter some details about the incident to be stored in the incident log for future use.  You should provide a summary of the problem and information about how it was resolved.<br /><strong>Final Update</strong>:";
            }
            else if (document.getElementById)
            {
            document.getElementById('helptext').innerHTML = "Enter some details about the incident to be stored in the incident log for future use.  You should provide a summary of the problem and information about how it was resolved.<br /><strong>Final Update</strong>:";
            }

        }
    }

    function editbox(object, boxname)
    {
        var boxname;
        object.boxname.disabled=true;
    }

    -->
    </script>

    <form name="closeform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <table class='vertical'>
    <tr><th>Mark for Closure:<br />
    Mark the incident as 'Awaiting Closure' and wait for a short while before closing this incident.<br />
    </th>
    <td><input type='radio' name='wait' value='yes' checked='checked' />Mark the incident for closure.<br />
        <input type='radio' name='wait' value='no' />Close the incident immediately.
    </td></tr>
    <tr><th>Knowledge Base<br />
    Check here <input type='checkbox' name='kbarticle' onchange='enablekb();' value='yes' /> and enter
    a title for an article to be created in the knowledge base.
    </th><td><input type="text" name="kbtitle" id="kbtitle" size="30" value="<?php echo $incident_title; ?>" disabled='disabled' /></td>
    </tr>
    <tr><th>&nbsp;</th><td>
    <span name='helptext' id='helptext'>Enter some details about the incident to be stored in the incident log for future use.
    You should provide a summary of the problem and information about how it was resolved.<br /><strong>Final Update</strong>:</span></td></tr>

    <tr><th>Summary:<br />
        A concise but full summary of the problem(s) that were encountered.<sup class='red'>*</sup>
        <input type='checkbox' name='incsummary' onclick="if (this.checked) {document.closeform.summary.disabled = false; document.closeform.summary.style.display='';} else { saveValue=document.closeform.summary.value; document.closeform.summary.disabled = true; document.closeform.summary.style.display='none';}" checked='checked' disabled='disabled' /></td>
    <td><textarea id="summary" name="summary" cols='40' rows='8' onfocus="if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.summary.blur()',1); } else saveValue=this.value;"><?php
    //  style="display: none;"
    $sql = "SELECT * FROM updates WHERE incidentid='$id' AND type='probdef' ORDER BY timestamp ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($row = mysql_fetch_object($result))
    {
        echo stripslashes($row->bodytext);
        echo "\n\n";
    }
    echo "</textarea>\n";
    ?></td></tr>

    <tr><th>Symptoms: <input type='checkbox' name='incsymptoms' onclick="if (this.checked) {document.closeform.symptoms.disabled = false; document.closeform.symptoms.style.display=''} else { saveValue=document.closeform.symptoms.value; document.closeform.symptoms.disabled = true; document.closeform.symptoms.style.display='none'}" disabled='disabled' /></th>
    <td><textarea id="symptoms" name="symptoms" cols='40' style="display: none"; rows='8' onfocus="if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.symptoms.blur()',1); } else saveValue=this.value;"></textarea></td></tr>

    <tr><th>Cause: <input type='checkbox' name='inccause' onclick="if (this.checked) {document.closeform.cause.disabled = false; document.closeform.cause.style.display=''} else { saveValue=document.closeform.cause.value; document.closeform.cause.disabled = true; document.closeform.cause.style.display='none'}" disabled='disabled' /></th>
    <td><textarea id="cause" name="cause" cols='40' rows='8' style="display: none;" onfocus="if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.cause.blur()',1); } else saveValue=this.value;"></textarea></td></tr>

    <tr><th>Question: <input type='checkbox' name='incquestion' onclick="if (this.checked) {document.closeform.question.disabled = false; document.closeform.question.style.display=''} else { saveValue=document.closeform.question.value; document.closeform.question.disabled = true; document.closeform.question.style.display='none'}" disabled='disabled' /></th>
    <td><textarea id="question" name="question" cols='40' rows='8' style="display: none;" onfocus="if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.question.blur()',1); } else saveValue=this.value;"></textarea></td></tr>

    <tr><th>Answer: <input type='checkbox' name='incanswer' onclick="if (this.checked) {document.closeform.answer.disabled = false; document.closeform.answer.style.display=''} else { saveValue=document.closeform.answer.value; document.closeform.answer.disabled = true; document.closeform.answer.style.display='none'}" disabled='disabled' /></th>
    <td><textarea id="answer" name="answer" cols='40' rows='8' style="display: none;" onfocus="if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.answer.blur()',1); } else saveValue=this.value;"></textarea></td></tr>

    <tr><th>Solution: <sup class='red'>*</sup><input type='checkbox' name='incsolution' onclick="if (this.checked) {document.closeform.solution.disabled = false; document.closeform.solution.style.display=''} else { saveValue=document.closeform.solution.value; document.closeform.solution.disabled = true; document.closeform.solution.style.display='none'}" checked='checked' disabled='disabled' /></th>

    <?php
    echo "<td><textarea id='solution' name='solution' cols='40' rows='8' onfocus='if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.solution.blur()',1); } else saveValue=this.value;'>";
    $sql = "SELECT * FROM updates WHERE incidentid='$id' AND type='solution' ORDER BY timestamp ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($row = mysql_fetch_object($result))
    {
        echo stripslashes(trim($row->bodytext));
        echo "\n\n";
    }
    echo "</textarea>\n";
    echo "</td></tr>";
    ?>
    <tr><th>Workaround: <input type='checkbox' name='incworkaround' onclick="if (this.checked) {document.closeform.workaround.disabled = false; document.closeform.workaround.style.display=''} else { saveValue=document.closeform.workaround.value; document.closeform.workaround.disabled = true; document.closeform.workaround.style.display='none'}" disabled='disabled' /></th>
    <td><textarea id="workaround" name="workaround" cols='40' rows='8' style="display: none;" onfocus="if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.workaround.blur()',1); } else saveValue=this.value;"></textarea></td></tr>

    <tr><th>Status: <input type='checkbox' name='incstatus' onclick="if (this.checked) {document.closeform.status.disabled = false; document.closeform.status.style.display=''} else { saveValue=document.closeform.status.value; document.closeform.status.disabled = true; document.closeform.status.style.display='none'}" disabled='disabled' /></th>
    <td><textarea id="status" name="status" cols='40' rows='8' style="display: none;" onfocus="if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.status.blur()',1); } else saveValue=this.value;"></textarea></td></tr>

    <tr><th>Additional Info: <input type='checkbox' name='incadditional' onclick="if (this.checked) {document.closeform.additional.disabled = false; document.closeform.additional.style.display=''} else { saveValue=document.closeform.additional.value; document.closeform.additional.disabled = true; document.closeform.additional.style.display='none'}" disabled='disabled' /></th>
    <td><textarea id="additional" name="additional" cols='40' rows='8' style="display: none;" onfocus="if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.additional.blur()',1); } else saveValue=this.value;"></textarea></td></tr>

    <tr><th>References: <input type='checkbox' name='increferences' onclick="if (this.checked) {document.closeform.references.disabled = false; document.closeform.references.style.display=''} else { saveValue=document.closeform.references.value; document.closeform.references.disabled = true; document.closeform.references.style.display='none'}" disabled='disabled' /></th>
    <td><textarea id="references" name="references" cols='40' rows='8' style="display: none;" onfocus="if (this.enabled) { this.value = saveValue; setTimeout('document.articlform.references.blur()',1); } else saveValue=this.value;"></textarea></td></tr>

    <tr><th>Closing Status: <sup class='red'>*</sup></th><td><?php closingstatus_drop_down("closingstatus", 0) ?></td></tr>
    <tr><th>Inform Customer:<br />
    Send an email to the customer explaining that the incident has been (or will be) closed.</th>
    <td><input name="send_email" checked type="radio" value="no" />No <input name="send_email" type="radio" value="yes" />Yes</td></tr>
    <?php
    $externalemail=incident_externalemail($id);
    if ($externalemail)
    {
        ?>
        <tr><th>Inform External Engineer:<br />
        Send an email to <em><?php echo $externalemail; ?></em> asking for the external incident to be closed.
        </th><td class='shade2'><input name="send_engineer_email" type="radio" value="no" />No <input name="send_engineer_email" type="radio" value="yes" checked='checked' />Yes</td></tr>
        <?php
    }
    echo "</table>\n";
    echo "<p align='center'>";
    echo "<input name='type' type='hidden' value='Support' />";
    echo "<input name='id' type='hidden' value='$id' />";
    echo "<input type='hidden' name='process' value='closeincident' />";
    echo "<input name='submit' type='submit' value='Close Incident' /></p>";
    echo "</form>";
    include('incident_html_bottom.inc.php');
}
else
{
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
            $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, customervisibility, sla, bodytext, timesincesla) ";
            $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '{$sit[2]}', 'show', 'solution','', '0')";
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
}
?>