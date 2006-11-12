<?php

// close.inc.php - Close incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}
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
    echo "<input type='hidden' name='action' value='close' />";
    echo "<input name='submit' type='submit' value='Close Incident' /></p>";
    echo "</form>";

?>