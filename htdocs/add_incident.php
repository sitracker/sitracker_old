<?php
// add_incident.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>, Tom Gerrard
// 7Oct02 INL  Added support for maintenanceid to be put into incidents table

$permission=5;
$title='Add Incident';
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$action = $_REQUEST['action'];
$context = cleanvar($_REQUEST['context']);
$updateid = cleanvar($_REQUEST['updateid']);
$query = cleanvar($_REQUEST['query']);
$siteid = cleanvar($_REQUEST['siteid']);
$contactid = cleanvar($_REQUEST['contactid']);
$search_string = cleanvar($_REQUEST['search_string']);
$type = cleanvar($_REQUEST['type']);
$maintid = cleanvar($_REQUEST['maintid']);
$productid = cleanvar($_REQUEST['productid']);

if (empty($action) OR $action=='showform')
{
    // This Page Is Valid XHTML 1.0 Transitional! 27Oct05
    include('htmlheader.inc.php');
    echo "<h2>Add Incident - Find Contact</h2>";
    if (empty($siteid))
    {
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>?action=findcontact" method="post">
        <input type="hidden" name="context" value="<?php echo $context ?>" />
        <input type="hidden" name="updateid" value="<?php echo $updateid ?>" />
        <table class='vertical'>
        <tr><th>Contact:</th><td>
        <?php
        echo "<input type='text' name='search_string' size='30' value='{$query}' />\n";
        ?>
        <input name="submit" type="submit" value="Find Contact" />
        </td></tr>
        </table>
        <p align='center'><a href='browse_contacts.php'>Browse contacts</a>...</p>
        <?php
        echo "<input name='siteid' type='hidden' value='$siteid' />";
        echo "</form>\n";
    }
    else
    {
        echo "<p align='center'>Contact $contactid</p>";
    }
    include('htmlfooter.inc.php');
}
elseif ($action=='findcontact')
{
    //  Search for the contact specified in the maintenance contracts and display a list of choices
    // This Page Is Valid XHTML 1.0 Transitional! 27Oct05

    $search_string=mysql_escape_string(urldecode($_REQUEST['search_string']));
    // check for blank or very short search field - otherwise this would find too many results
    if (empty($contactid) && strlen($search_string)<2)
    {
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    }
    $sql  = "SELECT *, products.name AS productname, products.id AS productid, contacts.surname AS surname, ";
    $sql .= "maintenance.id AS maintid, maintenance.incident_quantity, maintenance.incidents_used ";
    $sql .= "FROM supportcontacts, contacts, maintenance, products, sites ";
    $sql .= "WHERE supportcontacts.contactid=contacts.id ";
    $sql .= "AND supportcontacts.maintenanceid=maintenance.id ";
    $sql .= "AND maintenance.product=products.id ";
    $sql .= "AND maintenance.site=sites.id ";

    if (empty($contactid))
    {
        $sql .= "AND (contacts.surname LIKE '%$search_string%' OR contacts.forenames LIKE '%$search_string%' ";
        $sql .= "OR SOUNDEX('$search_string') = SOUNDEX((CONCAT_WS(' ', contacts.forenames, contacts.surname))) ";
        $sql .= "OR sites.name LIKE '%$search_string%') ";
    }
    else
    {
        $sql .= "AND supportcontacts.contactid = '$contactid' ";
    }

    $sql .= "ORDER by contacts.forenames, contacts.surname ";
    $result=mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result)>0)
    {
        include('htmlheader.inc.php')
        ?>
        <script type="text/javascript">
        function confirm_support()
        {
            return window.confirm("This incident will be logged against the customers CONTRACT. Are you sure you want to continue?");
        }

        function confirm_free()
        {
            return window.confirm("This incident will be logged against the customers SITE and NOT against a contract, you will be prompted to choose a service level. Are you sure you want to continue?");
        }
        </script>

        <h2>Add Incident - Select Person / Contract</h2>
        <h3>Contracts</h3>
        <p align='center'>This list shows contracts, i.e. supported people and the products they are supported for.<br />
        Click on the appropriate 'Add Incident' link to begin adding the incident.</p>

        <?php
        echo "<table align='center'>";
        ?>
        <tr>
            <th>&nbsp;</th>
            <th>Name</th>
            <th>Site</th>
            <th>Contract</th>
            <th>Service Level</th>
            <th>Expiry</th>
        </tr>
        <?php
        while($contactrow=mysql_fetch_array($result))
        {
            if ($contactrow['expirydate']<$now || $contactrow['term']=='yes') $class = 'expired';
            else $class = "shade2";

            $incidents_remaining = $contactrow['incident_quantity'] - $contactrow['incidents_used'];

            echo "<tr class='$class'>";
            if ($contactrow['expirydate']<$now) echo "<td>Expired</td>";
            elseif ($contactrow['term']=='yes') echo "<td>Terminated</td>";
            elseif ($contactrow['incident_quantity'] >= 1 AND $contactrow['incidents_used'] >= $contactrow['incident_quantity'])
                echo "<td>Run out ({$contactrow['incidents_used']}/{$contactrow['incident_quantity']} Used)</td>";
            else
            {
                echo "<td><a href=\"{$_SERVER['PHP_SELF']}?action=incidentform&amp;type=support&amp;contactid=".$contactrow['contactid']."&amp;maintid=".$contactrow['maintenanceid']."&amp;producttext=".urlencode($contactrow['productname'])."&amp;productid=".$contactrow['productid']."&amp;updateid=$updateid&amp;siteid=".$contactrow['siteid']."\" onclick=\"return confirm_support();\">Add Incident</a> ";
                if ($contactrow['incident_quantity']==0) echo "(Unlimited)";
                else echo "({$incidents_remaining} Left)";
            }
            echo "</td>";
            echo '<td>'.$contactrow['forenames'].' '.$contactrow['surname'].'</td>';
            echo '<td>'.$contactrow['name'].'</td>';
            echo '<td><strong>'.$contactrow['maintid'].'</strong>&nbsp;'.$contactrow['productname'].'</td>';
            echo '<td>'.servicelevel_id2tag($contactrow['servicelevelid']).'</td>';
            echo '<td>'.date("jS M Y", $contactrow['expirydate']).'</td>';
            echo "</tr>\n";
        }
        echo "</table>\n";

        // Select the contact from the list of contacts as well
        $sql = "SELECT *, contacts.id AS contactid FROM contacts, sites WHERE contacts.siteid=sites.id ";
        if (empty($contactid))
        {
            $sql .= "AND (surname LIKE '%$search_string%' OR forenames LIKE '%$search_string%' OR sites.name LIKE '%$search_string%' ";
            $sql .= "OR CONCAT_WS(' ', forenames, surname) LIKE '$search_string') ";
        }
        else $sql .= "AND contacts.id = '$contactid' ";

        $sql .= "ORDER by contacts.surname, contacts.forenames ";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (mysql_num_rows($result)>0)
        {
            echo "<h3>People</h3>\n";
            echo "<p align='center'>This list shows people that matched your search, if site-support is available you can add incidents for the site.</p>";
            echo "<table align='center'>";
            echo "<tr>";
            echo "<th>&nbsp;</th>";
            echo "<th>Name</th>";
            echo "<th>Site</th>";
            echo "</tr>\n";

            while($contactrow=mysql_fetch_array($result))
            {
                echo "<tr class='shade2'>";
                $site_incident_pool=db_read_column('freesupport', 'sites', $contactrow['siteid']);
                if ($site_incident_pool > 0)
                {
                    echo "<td><a href=\"{$_SERVER['PHP_SELF']}?action=incidentform&amp;type=free&amp;contactid=".$contactrow['contactid']."&amp;updateid=$updateid\" onclick=\"return confirm_free();\">";
                    echo "Add Site Support Incident</a> ({$site_incident_pool} Left)</td>";
                }
                else
                {
                    echo "<td class='expired'>Zero remaining</td>";
                }
                echo '<td>'.$contactrow['forenames'].' '.$contactrow['surname'].'</td>';
                echo '<td>'.site_name($contactrow['siteid']).'</td>';
                echo "</tr>\n";
            }
            echo "</table>\n";
            echo "<p align='center'><a href='add_contact.php'>Add a contact</a></p>";
        }
        else
        {
            echo "<h3>No matching contacts found</h3>";
            echo "<p align='center'><a href=\"add_contact.php\">Add a contact</a></p>";
        }
        echo "<p align='center'><a href=\"{$_SERVER['PHP_SELF']}?updateid=$updateid\">Search again</a></p>";
        include('htmlfooter.inc.php');
    }
    else
    {
        // This Page Is Valid XHTML 1.0 Transitional! 27Oct05
        include('htmlheader.inc.php');
        echo "<h2>No contract found matching ";
        if (!empty($search_string)) echo "'$search_string' ";
        if (!empty($contactid)) echo "contact id $contactid ";
        echo "</h2>\n";
        echo "<p align='center'><a href=\"add_incident.php?updateid=$updateid\">Try again</a></p>";
        // Select the contact from the list of contacts as well
        $sql = "SELECT *, contacts.id AS contactid FROM contacts, sites WHERE contacts.siteid=sites.id ";
        if (empty($contactid))
        {
            $sql .= "AND (surname LIKE '%$search_string%' OR forenames LIKE '%$search_string%' OR sites.name LIKE '%$search_string%' ";
            $sql .= "OR CONCAT_WS(' ', forenames, surname) = '$search_string' )";
        }
        else $sql .= "AND contacts.id = '$contactid' ";
        $sql .= "ORDER by contacts.surname, contacts.forenames ";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (mysql_num_rows($result)>0)
        {
            echo "<h3>People</h3>\n";
            echo "<p align='center'>This list shows people that matched your search, if site-support is available you can add incidents for the site.</p>";
            echo "<table align='center'>";
            echo "<tr>";
            echo "<th>&nbsp;</th>";
            echo "<th>Name</th>";
            echo "<th>Site</th>";
            echo "</tr>\n";

            while($contactrow=mysql_fetch_array($result))
            {
                echo "<tr class='shade2'>";
                $site_incident_pool=db_read_column('freesupport', 'sites', $contactrow['siteid']);
                if ($site_incident_pool > 0)
                {
                    echo "<td><a href=\"{$_SERVER['PHP_SELF']}?action=incidentform&amp;type=free&amp;contactid=".$contactrow['contactid']."&amp;updateid=$updateid\" onclick=\"return confirm_free();\">";
                    echo "Add Site Support Incident</a> ({$site_incident_pool})</td>";
                }
                else echo "<td class='expired'>Zero remaining</td>";
                echo '<td>'.$contactrow['forenames'].' '.$contactrow['surname'].'</td>';
                echo '<td>'.site_name($contactrow['siteid']).'</td>';
                echo "</tr>\n";
            }
            echo "</table>\n";

            echo "<p align='center'><a href='add_contact.php'>Add a contact</a></p>\n";
        }
        else
        {
            echo "<h3>No matching contacts found</h3>";
            echo "<p align='center'><a href=\"add_contact.php\">Add a contact</a></p>\n";
        }
        include('htmlfooter.inc.php');
    }
}
elseif ($action=='incidentform')
{
    // Display form to get details of the actual incident
    include('htmlheader.inc.php');

    echo "<h2>Add Incident - Get Details</h2>";
    ?>
    <script type="text/javascript">
    function validateForm(form)
    {
        if (form.incidenttitle.value == "")
        {
            alert("You must enter an incident title.");
            form.incidenttitle.focus( );
            return false;
        }
    }
    </script>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>?action=assign" method="post" name="supportdetails" onsubmit="return validateForm(this)">
    <input type="hidden" name="type" value="<?php echo $type ?>" />
    <input type="hidden" name="contactid" value="<?php echo $contactid ?>" />
    <input type="hidden" name="productid" value="<?php echo $productid ?>" />
    <input type="hidden" name="maintid" value="<?php echo $maintid ?>" />
    <input type="hidden" name="siteid" value="<?php echo $siteid ?>" />
    <?php
    if (!empty($updateid)) echo "<input type='hidden' name='updateid' value='$updateid' />";
    ?>
    <table align='center' class='vertical' width='60%'>
    <tr><th>Name:<br /><a href="edit_contact.php?action=edit&contact=<?php echo $contactid; ?>">Edit</a></th><td><h3><?php echo contact_realname($contactid); ?></h3></td></tr>
    <tr><th>Email:</th><td><?php echo contact_email($contactid); ?></td></tr>
    <tr><th>Telephone:</th><td><?php echo contact_phone($contactid); ?></td></tr>
    <tr><th>Fax:</th><td><?php echo contact_fax($contactid); ?></td></tr>
    <?php
    if ($type == 'free')
    {
        echo "<tr><th>Service Level:</th><td>".serviceleveltag_drop_down('servicelevel',$CONFIG['default_service_level'], TRUE)."</td></tr>";
        echo "<tr><th>Software:</th><td>".software_drop_down('software', 0)."</td></tr>";
    }
    else
    {
        echo "<tr><th>Contract:</th><td>{$maintid} - ".urldecode($producttext)."</td></tr>";
        echo "<tr><th>Software:</th><td>".softwareproduct_drop_down('software', 1, $productid)."</td></tr>";
    }

    plugin_do('new_incident_form');
    ?>
    <tr><th>Software Version:</th><td><input maxlength='50' name='productversion' size='40' type='text' /></td></tr>
    <tr><th>Service Packs Applied:</th><td><input maxlength='100' name="productservicepacks" size=40 type="text" /></td></tr>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <?php
    if (empty($updateid))
    {
        ?>
        <tr><th>Incident Title: <sup class='red'>*</sup></th><td><input maxlength='150' name="incidenttitle" size='40' type="text" /></td></tr>
        <tr><th>Problem Description:<br />
        As much information as possible about the problem, enough for an engineer to begin researching the issue without
        having to contact the customer first.</td>
        <td><textarea name='probdesc' rows='10' cols='60'></textarea></td></tr>
        <?php
        // Insert pre-defined per-product questions from the database, these should be required fields
        // These 'productinfo' questions don't have a GUI as of 27Oct05
        $sql = "SELECT * FROM productinfo WHERE productid='$productid'";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while ($productinforow=mysql_fetch_array($result))
        {
            echo "<tr><th>{$productinforow['information']}";
            if ($productinforow['moreinformation']!='')
                echo "<br />\n".$productinforow['moreinformation'];
            echo ": <sup class='red'>*</sup>";
            echo "</th>";
            echo "<td><input maxlength='100' name='{$productinforow['id']}' size='40' type='text' /></td></tr>\n";
        }
        ?>
        <tr><th>Workarounds Attempted:
            What has the customer done to try and solve the issue?  What config changes were made?  What patches/service packs were installed?</th>
        <td><textarea name='workarounds' rows='10' cols='60'></textarea></td></tr>

        <tr><th>Problem Reproduction:
            Does the problem exist with more than one machine?  What steps will an engineer need to take to reproduce this issue?</th>
        <td><textarea name='probreproduction' rows='10' cols='60'></textarea></td></tr>

        <tr><th>Customer Impact:<br />
            How many users are affected by this issue?  Is this an issue of downtime or a minor niggle?  Does the issue prevent people from working?</th>
        <td><textarea name='custimpact' rows='10' cols='60'></textarea></td></tr>
        <?php
    }
    else
    {
        $sql="SELECT bodytext from updates where id=$updateid";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $updaterow=mysql_fetch_array($result);
        $mailed_body_text=$updaterow['bodytext'];

        $sql="SELECT subject from tempincoming where updateid=$updateid";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $updaterow=mysql_fetch_array($result);
        $mailed_subject=$updaterow['subject'];

        echo "<tr><th>Incident Title>: <sup class='red'>*</sup></th><td><input name='incidenttitle' size='40' type='text' value='".htmlspecialchars($mailed_subject,ENT_QUOTES)."'></td></tr>\n";
        echo "<tr><td colspan='2'>&nbsp;</td></tr>\n";

        echo "<tr><th>Problem Description:<br />This information was received in the email.  It is not editable.</th>";
        echo "<td>".nl2br($mailed_body_text)."</td></tr>\n";
        echo "<tr><td class='shade1' colspan=2>&nbsp;</td></tr>\n";
    }
    ?>
    <tr><th>Next Action:<br />If there is a time limit on the next action, please specify it here.</td>
    <td>
    <input type="text" name="nextaction" maxlength="50" size="30" value="Initial Response" /><br /><br />
    <input type="radio" name="timetonextaction_none" value="none" checked='checked' />None<br />
    <input type="radio" name="timetonextaction_none" value="time" />In <em>x</em> days, hours, minutes<br />&nbsp;&nbsp;&nbsp;
    <input maxlength="3" name="timetonextaction_days" onclick="window.document.supportdetails.timetonextaction_none[1].checked = true;" size="3"> Days&nbsp;
    <input maxlength="2" name="timetonextaction_hours" onclick="window.document.supportdetails.timetonextaction_none[1].checked = true;" size="3"> Hours&nbsp;
    <input maxlength="2" name="timetonextaction_minutes" onclick="window.document.supportdetails.timetonextaction_none[1].checked = true;" size="3"> Minutes<br />
    <input type="radio" name="timetonextaction_none" value="date">On specified Date<br />&nbsp;&nbsp;&nbsp;
    <?php
    // Print Listboxes for a date selection
    ?><select name='day' onclick="window.document.supportdetails.timetonextaction_none[2].checked = true;"><?php
    for ($t_day=1;$t_day<=31;$t_day++)
    {
            echo "<option value=\"$t_day\"";
            if ($t_day==date("j")) echo " selected='selected'";
            echo ">$t_day</option>\n";
    }
    ?></select><select name='month' onclick="window.document.supportdetails.timetonextaction_none[2].checked = true;"><?php
    for ($t_month=1;$t_month<=12;$t_month++)
    {
            echo "<option value=\"$t_month\"";
            if ($t_month==date("n")) echo " selected='selected'";
            echo ">". date ("F", mktime(0,0,0,$t_month,1,2000)) ."</option>\n";
    }
    ?></select><select name='year' onclick="window.document.supportdetails.timetonextaction_none[2].checked = true;"><?php
    for ($t_year=(date("Y")-1);$t_year<=(date("Y")+5);$t_year++)
    {
            echo "<option value=\"$t_year\"";
            if ($t_year==date("Y")) echo " selected='selected'";
            echo ">$t_year</option>\n";
    }
    ?></select>
    </td></tr>
    <?php
    if (empty($updateid))
    {
        echo "<tr><th>Visible To Customer:</th>\n";
        echo "<td><input name='cust_vis' type=radio value='no' /> No <input name='cust_vis' type='radio' value='yes' checked='checked' /> Yes</td></tr>\n";
    }
    ?>
    <tr><th>Send Opening Email: <sup class='red'>*</sup></th>
    <td><input name="send_email" checked='checked' type="radio" value="no" />No <input name="send_email" type="radio" value="email" />Yes, "Re: Email" <input name="send_email" type="radio" value="call" />Yes, "Re: Call"</td></tr>
    <tr><th>Incident Priority:</th><td><?php echo priority_drop_down("priority", 1, 4, FALSE); ?> Based on the customers Business Impact</td></tr>
    </table>

    <p align='center'><input name="submit" type="submit" value="Add Incident" /></p>
    </form>
    <?php
    include('htmlfooter.inc.php');
}
elseif ($action=='assign')
{
    include('htmlheader.inc.php');
    if ($type == "support" || $type == "free")
    {
        echo "<h2>Add Incident - Assign</h2>";

        // Assign SUPPORT incident
        // The incident will be added to the database assigned to the current user, and then a list of engineers
        // is displayed so that the incident can be redirected

        // External vars
        $servicelevel = cleanvar($_REQUEST['servicelevel']);
        $type = cleanvar($_REQUEST['type']);
        $incidenttitle = cleanvar($_REQUEST['incidenttitle']);
        $probdesc = cleanvar($_REQUEST['probdesc']);
        $workarounds = cleanvar($_REQUEST['workarounds']);
        $probreproduction = cleanvar($_REQUEST['probreproduction']);
        $custimpact = cleanvar($_REQUEST['custimpact']);
        $other = cleanvar($_REQUEST['other']);
        $priority = cleanvar($_REQUEST['priority']);
        $software = cleanvar($_REQUEST['software']);
        $productversion = cleanvar($_REQUEST['productversion']);
        $productservicepacks = cleanvar($_REQUEST['productservicepacks']);
        $bodytext = cleanvar($_REQUEST['bodytext']);
        $send_email = cleanvar($_REQUEST['send_email']);

        // check form input
        $errors = 0;
        // check for blank contact
        if ($contactid == 0) { $errors = 1; $error_string .= "You must select a contact"; }
        // check for blank title
        if ($incidenttitle == '')  { $incidenttitle='Untitled'; }
        // check for blank priority
        if ($priority == 0){ $priority=1; }
        // check for blank type
        if ($type == "") { $errors = 1;   $error_string .= "Incident type was blank"; }

        if ($type == 'free' AND $servicelevel=='' ) { $errors++; $error_string .= "You must select a service level";  }

        if ($errors==0)
        {
            // add incident (assigned to current user)

            // Calculate the time to next action
            switch ($timetonextaction_none)
            {
                case 'none': $timeofnextaction = 0;  break;
                case 'time':
                    $timeofnextaction = calculate_time_of_next_action($timetonextaction_days, $timetonextaction_hours, $timetonextaction_minutes);
                break;

                case 'date':
                    // $now + ($days * 86400) + ($hours * 3600) + ($minutes * 60);
                    $unixdate=mktime(9,0,0,$month,$day,$year);
                    $now = time();
                    $timeofnextaction = $unixdate;
                    if ($timeofnextaction<0) $timeofnextaction=0;
                break;

                default: $timeofnextaction = 0; break;
            }

            // Set the service level the contract
            if ($servicelevel=='') $servicelevel = servicelevel_id2tag(maintenance_servicelevel($maintid));
            // Use default service level if we didn't find one above
            if ($servicelevel=='') $servicelevel = $CONFIG['default_service_level'];

            // Check the service level priorities, look for the highest possible and reduce the chosen priority if needed
            $sql = "SELECT priority FROM servicelevels WHERE tag='$servicelevel' ORDER BY priority DESC LIMIT 1";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(). "--$sql--",E_USER_ERROR);
            list($highestpriority) = mysql_fetch_row($result);
            if ($priority > $highestpriority)
            {
                $prioritychangedmessage = " (Reduced from ".priority_name($priority)." according to SLA)";
                $priority = $highestpriority;
            }

            $sql  = "INSERT INTO incidents (title, owner, contact, priority, servicelevel, status, type, maintenanceid, ";
            $sql .= "product, softwareid, productversion, productservicepacks, opened, lastupdated, timeofnextaction) ";
            $sql .= "VALUES ('$incidenttitle', '".$sit[2]."', '$contactid', '$priority', '$servicelevel', '1', 'Support', '$maintid', ";
            $sql .= "'$productid', '$software', '$productversion', '$productservicepacks', '$now', '$now', '$timeofnextaction')";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            $incidentid = mysql_insert_id();
            $_SESSION['incidentid'] = $incidentid;

            // We use <b> tags in updatetext, not <strong>
            $updatetext = "Opened as Priority: <b>" . priority_name($priority) . "</b>";
            if (!empty($prioritychangedmessage)) $updatetext .= $prioritychangedmessage;
            $updatetext .= "\n\n" . $bodytext;
            if ($probdesc != "") $updatetext .= "<b>Problem Description</b>\n" . $probdesc . "\n\n";
            if ($workarounds != "") $updatetext .= "<b>Workarounds Attempted</b>\n" . $workarounds . "\n\n";
            if ($probreproduction != "") $updatetext .= "<b>Problem Reproduction</b>\n" . $probreproduction . "\n\n";
            if ($custimpact != "") $updatetext .= "<b>Customer Impact</b>\n" . $custimpact . "\n\n";
            if ($other != "") $updatetext .= "<b>Other Details</b>\n" . $other . "\n";
            if ($cust_vis == "yes") { $customervisibility='show'; }
            else { $customervisibility='hide'; }

            if (!empty($updateid))
            {
                // Assign existing update to new incident if we have one
                $sql="UPDATE updates SET incidentid='$incidentid', userid='".$sit[2]."' WHERE id='$updateid'";
                $result=mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                // + move any attachments we may have received
                $update_path=$CONFIG['attachment_fspath'] .'updates/'.$updateid;
                if (file_exists($update_path))
                {
                    if (!file_exists($CONFIG['attachment_fspath'] ."$incidentid"))
                    {
                        $umask=umask(0000);
                        mkdir($CONFIG['attachment_fspath'] ."$incidentid", 0770);
                        umask($umask);
                    }
                    $sym=symlink($update_path, $CONFIG['attachment_fspath'] . "$incidentid/" . $now);
                    if (!$sym) throw_error('!Error creating symlink for update','');
                }
            }
            else
            {
                // Create a new update from details entered
                $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, currentowner, ";
                $sql .= "currentstatus, customervisibility, nextaction) ";
                $sql .= "VALUES ('$incidentid', '".$sit[2]."', 'opening', '$updatetext', '$now', '".$sit[2]."', ";
                $sql .= "'1', '$customervisibility', '$nextaction')";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            }

            // get the service level
            // find out when the initial response should be according to the service level
            if (empty($servicelevel) OR $servicelevel==0)
            {
                // FIXME: for now we use id but in future use tag, once maintenance uses tag
                $servicelevel=maintenance_servicelevel($maintid);
                $sql = "SELECT * FROM servicelevels WHERE id='$servicelevel' AND priority='$priority' ";
            }
            else $sql = "SELECT * FROM servicelevels WHERE tag='$servicelevel' AND priority='$priority' ";

            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $level = mysql_fetch_object($result);

            $targetval = $level->initial_response_mins * 60;
            $initialresponse=$now + $targetval;

            // Insert the first SLA update, this indicates the start of an incident
            // This insert could possibly be merged with another of the 'updates' records, but for now we keep it seperate for clarity
            $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
            $sql .= "VALUES ('$incidentid', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '1', 'show', 'opened','The incident is open and awaiting action.')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            // Insert the first Review update, this indicates the review period of an incident has started
            // This insert could possibly be merged with another of the 'updates' records, but for now we keep it seperate for clarity
            $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
            $sql .= "VALUES ('$incidentid', '0', 'reviewmet', '$now', '".$sit[2]."', '1', 'hide', '','')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            plugin_do('incident_created');

            // Decrement free support, where appropriate
            if ($type=='free')
            {
                decrement_free_incidents(contact_siteid($contactid));
                plugin_do('incident_created_site');
            }
            else
            {
                // decrement contract incident by incrementing the number of incidents used
                increment_incidents_used($maintid);
                plugin_do('incident_created_contract');
            }

            // Send opening email, where selected
            if ($send_email == "call") send_template_email('INCIDENT_LOGGED_CALL', $incidentid);
            elseif ($send_email == "email") send_template_email('INCIDENT_LOGGED_EMAIL', $incidentid);


            echo "<h3>Incident: $incidentid</h3>";
            echo "<p align='center'>An incident has now been added as incident number $incidentid and logged to your account, ";
            echo "you should now assign an engineer who is to deal with the incident</p>\n";

            // List Engineers
            // We need a user type 'engineer' so we don't just list everybody
            // Status zero means account disabled
            $sql = "SELECT * FROM users WHERE status!=0 ORDER BY realname";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            ?><h3>Users</h3>
            <table align="center">
            <tr>
                <th>&nbsp;</th>
                <th>Name</th>
                <th>Telephone</th>
                <th>Status</th>
                <th>Message</th>
		<th colspan='5'>Incidents in Queue</th>
                <!--<th align='center'>Action Needed</th>
		<th align='center'>Other</th>
    		<th align='center'>Critical</th>
    		<th align='center'>High</th>
    		<th align='center'>Medium</th>
    		<th align='center'>Low</th>-->
                <th>Accepting?</th>
            </tr>
	    <tr>
		<th colspan='5'></th>
		<th align='center'>Action Needed / Other</th>
    		<?php
    		echo "<th align='center'>".priority_icon(4)."</th>";
    		echo "<th align='center'>".priority_icon(3)."</th>";
    		echo "<th align='center'>".priority_icon(2)."</th>";
    		echo "<th align='center'>".priority_icon(1)."</th>";
    		?>
		<th></th>
	    </tr>
            <?php
            $shade='shade2';
            while ($userrow = mysql_fetch_array($result))
            {
                echo "<tr class='$shade'>";
                // display reassign link only if person is accepting or if the current user has 'reassign when not accepting' permission
                if ($userrow['accepting']=='Yes')
                {
                    echo "<td align='right'><a href=\"{$_SERVER['PHP_SELF']}?action=reassign&userid=".$userrow['id']."&incidentid=$incidentid&nextaction=".urlencode($nextaction)."\" ";
                    // if ($priority >= 3) echo " onclick=\"alertform.submit();\"";
                    echo ">Assign To</a></td>";
                }
                elseif (user_permission($sit[2],40))
                {
                    echo "<td align='right'><a href=\"{$_SERVER['PHP_SELF']}?action=reassign&userid=".$userrow['id']."&incidentid=$incidentid&nextaction=".urlencode($nextaction)."\" ";
                    // if ($priority >= 3) echo " onclick=\"alertform.submit();\"";
                    echo ">Force To</a></td>";
                }
                else
                {
                    echo "<td class='expired'>&nbsp;</td>";
                }
                echo "<td>";

                // Have a look if this user has skills with this software
                $ssql = "SELECT softwareid FROM usersoftware WHERE userid='{$userrow['id']}' AND softwareid='{$software}' ";
                $sresult = mysql_query($ssql);
                if (mysql_num_rows($sresult) >=1 ) echo "<strong>{$userrow['realname']}</strong>";
                else echo $userrow['realname'];
                echo "</td>";
                echo "<td>".$userrow['phone']."</td>";
                echo "<td>".userstatus_name($userrow['status'])."</td>";
                echo "<td>".$userrow['message']."</td>";
                echo "<td align='center'>";

    		$incpriority = user_incidents($userrow['id']);
    		$countincidents = ($incpriority['1']+$incpriority['2']+$incpriority['3']+$incpriority['4']);

                if ($countincidents >= 1) $countactive=user_activeincidents($userrow['id']);
                else $countactive=0;

                $countdiff=$countincidents-$countactive;

    		echo "$countactive / {$countdiff}</td>";
    		echo "<td align='center'>".$incpriority['4']."</td>";
    		echo "<td align='center'>".$incpriority['3']."</td>";
    		echo "<td align='center'>".$incpriority['2']."</td>";
    		echo "<td align='center'>".$incpriority['1']."</td>";

                echo "<td align='center'>";
                echo $userrow['accepting']=='Yes' ? 'Yes' : "<span class='error'>No</span>";
                echo "</td>";
                echo "</tr>\n";
                if ($shade=='shade2') $shade='shade1';
                else $shade='shade2';
            }
            echo "</table>";
            echo "<p align='center'>Users shows in <strong>bold</strong> typeface are known to have relevant software skills.</p>";
        }
        else
        {
            throw_error('User input error:', $error_string);
        }
    }
    include('htmlfooter.inc.php');
}
elseif ($action=='reassign')
{
    // External variables
    $incidentid = cleanvar($_REQUEST['incidentid']);
    $uid = cleanvar($_REQUEST['userid']);
    $nextaction = cleanvar($_REQUST['nextaction']);

    include('htmlheader.inc.php');
    echo "<h2>Incident Added - Summary</h2>";
    echo "<p align='center'>Incident <a href=\"javascript:incident_details_window('$incidentid','incident{$incidentid}');\">$incidentid</a> has been moved to ".user_realname($uid)."'s <strong style='color: red'>Action Needed</strong> queue</p>";
    $userphone = user_phone($userid);
    if ($userphone!='') echo "<p align='center'>Telephone: {$userphone}</p>";
    $sql = "UPDATE incidents SET owner='$uid', lastupdated='$now' WHERE id='$incidentid'";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    if(user_notification_on_reassign($uid)=='true')
    {
        send_template_email('INCIDENT_REASSIGNED_USER_NOTIFY', $incidentid);
    }

    // add update
    $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, nextaction) ";
    $sql .= "VALUES ('$incidentid', '$sit[2]', 'reassigning', '$now', '$uid', '1', '$nextaction')";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    include('htmlfooter.inc.php');
}
?>
