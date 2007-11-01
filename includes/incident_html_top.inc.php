<?php
session_name($CONFIG['session_name']);
session_start();
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\"  xml:lang=\"{$_SESSION['lang']}\" lang=\"{$_SESSION['lang']}\">\n<head><title>";
if (!empty($incidentid)) echo "{$incidentid} - ";
if (isset($title)) echo $title;
else echo $CONFIG['application_shortname'];
echo "</title>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$i18ncharset}\" />";
echo "<meta name=\"GENERATOR\" content=\"{$CONFIG['application_name']} {$application_version_string}\" />\n";
echo "<style type='text/css'>@import url('{$CONFIG['application_webpath']}styles/webtrack.css');</style>\n";
if ($_SESSION['auth'] == TRUE)
{
    $style = interface_style($_SESSION['style']);
    $styleid = $_SESSION['style'];
    echo "<link rel='stylesheet' href='{$CONFIG['application_webpath']}styles/{$style['cssurl']}' />\n";
}
else
{
    $styleid= $CONFIG['default_interface_style'];
    echo "<link rel=\"stylesheet\" href=\"styles/webtrack1.css\" />\n";
}

$csssql = "SELECT cssurl, iconset FROM interfacestyles WHERE id='{$styleid}'";
$cssresult = mysql_query($csssql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
else list($cssurl, $iconset) = mysql_fetch_row($cssresult);
unset($styleid);

echo "<script src='{$CONFIG['application_webpath']}webtrack.js' type='text/javascript'></script>\n";
// javascript popup date library
echo "<script src='{$CONFIG['application_webpath']}calendar.js' type='text/javascript'></script>\n";

// if ($userstyle == "1") echo "<link rel=\"stylesheet\" href=\"styles/webtrack1.css\">\n";
// if ($userstyle == "2") echo "<link rel=\"stylesheet\" href=\"styles/webtrack2.css\">\n";
// FIXME put here some js to set action field then post form
?>
<script type='text/javascript'>
function gotab(tab) {
    document.actiontabs.action.value=tab;
    document.actiontabs.submit();
}
</script>


<style type='text/css'>

#detailsummary
{
    background: #F7FAFF;
    margin-left: auto;
    margin-right: auto;
    border-bottom: 3px solid #203894;
}
#detailsummary table, .detailentry table
{
    width: 100%;
    background-color: transparent;
}
#detailsummary table td
{
    vertical-align: top;
}
#detailsummary h1
{
    font-size: 150%;
    margin: 0px;
    padding: 0px;
    text-align: center;
}
#detailsummary img
{
    vertical-align: text-top;
}

#tabcontainer
{
    width: 100%;
    background: #fff;
    padding-left: 0px;
    border-bottom: 2px dotted #9C9C9C;
}
#tabnav
{
    height: 20px;
    margin: 0;
    padding-left: 10px;
}

#tabnav li
{
    margin: 0;
    padding: 0;
    display: inline;
    list-style-type: none;
}

#tabnav a:link, #tabnav a:visited, .submit
{
    float: left;
    background: #F6F2FF;
    font-size: 10px;
    line-height: 14px;
    font-weight: bold;
    padding: 2px 10px 2px 10px;
    margin-right: 4px;
    border: 1px solid #ccc;
    text-decoration: none;
    color: #666;
}

#tabnav a:link.active, #tabnav a:visited.active, .active
{
    border-bottom: 1px solid #fff;
    background: #fff;
    color: #000;
}

#tabnav a:hover
{
    background: #fff;
}


span.quoteirrel { color: grey; }
span.quote1 { color: #749BC2; }
span.quote2 { color: green; }
span.quote3 { color: magenta; }
span.quote4 { color: #FF9933; }
span.sig { color: red; }

.on {background-color:#84C1DF;}
.off {background-color:white;}

#navmenu
{
    color: white;
    background: #203894;
    padding-left: 20px;
    padding-top: 2px;
    padding-bottom: 2px;
    text-align: center;
}
#navmenu a { color: white; }
#navmenu a em { color: #ddd; }

</style>
<script type="text/javascript" src="helptip.js"></script>
<script type="text/javascript">
<!--
function confirm_addword()
{
  return window.confirm("If you add this word to the dictionary, all future spell checks will use this as the correct spelling for all users.  Are you sure you want to continue?");
}

function email_window(incidentid)
{
  URL = "email_incident.php?menu=hide&id=" + incidentid;
  window.open(URL, "email_window", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}

function close_window(incidentid)
{
  URL = "close_incident.php?menu=hide&id=" + incidentid;
  window.open(URL, "email_window", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}

function help_window(helpid)
{
  URL = "help.php?id=" + helpid;
  window.open(URL, "help_window", "toolbar=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=500,height=500");
}


if(document.layers)
{
   thisbrowser="NN4";
}
if(document.all)
{
   thisbrowser="ie"
}
if(!document.all && document.getElementById)
{
   thisbrowser="NN6";
}


function ShowHide(button,element)
{
  if (thisbrowser=="NN6" | thisbrowser=="NN4")
  {
      if (document.getElementById(element).style.display != "none") {
        document.getElementById(element).style.display = "none";
        // document.getElementById(button).title = "Double Click to Expand";
        document.getElementById(button).innerHTML = "[+]";
      }
      else {
        document.getElementById(element).style.display = "";
        document.getElementById(button).innerHTML = "[-]";
        // document.getElementById(button).title = "Double Click to Collapse";
      }
  }

  if (thisbrowser=="ie")
  {
      if (document.all[element].style.display != "none") {
        document.all[element].style.display = "none";
        // document.all[button].title = "Double Click to Expand";
        document.all[button].innerHTML = "[+]";
      }
      else {
        document.all[element].style.display = "";
        // document.all[button].title = "Double Click to Collapse";
        document.all[button].innerHTML = "[-]";
      }
  }


}

function Hide(button,element)
{
  if (thisbrowser=="NN6")
  {
      document.getElementById(element).style.display = "none";
      document.getElementById(button).innerHTML = "Expand";
  }
  if (thisbrowser=="ie")
  {
      document.all[element].style.display = "none";
      document.all[button].innerHTML = "Expand";
  }
}

//-->
</script>
</head>
<body onload="self.focus()">

<?php
$incidentid=$id;
// Retrieve incident
// extract incident details
$sql  = "SELECT *, incidents.id AS incidentid, ";
$sql .= "contacts.id AS contactid, contacts.notes AS contactnotes, servicelevel ";
$sql .= "FROM incidents, contacts ";
$sql .= "WHERE (incidents.id='{$incidentid}' AND incidents.contact=contacts.id) ";
$sql .= " OR incidents.contact=NULL ";
$incidentresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$incident = mysql_fetch_object($incidentresult);
$sitesql = "SELECT name, notes FROM sites WHERE id = '{$incident->siteid}'";
$siteresult = mysql_query($sitesql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
$site = mysql_fetch_object($siteresult);
$site_name=stripslashes($site->name);
if (!empty($site->notes)) $site_notes="<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/site.png' width='16' height='16' alt='' /> <strong>Site Notes:</strong><br />".nl2br($site->notes);
else $site_notes='';
unset($site);
if (!empty($incident->contactnotes)) $contact_notes="<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contact.png' width='16' height='16' alt='' /> <strong>Contact Notes:</strong><br />".nl2br($incident->contactnotes);
else $contact_notes='';
$product_name=product_name($incident->product);
if ($incident->softwareid > 0) $software_name=software_name($incident->softwareid);
$servicelevel_id=maintenance_servicelevel($incident->maintenanceid);
$servicelevel_tag = $incident->servicelevel;
if ($servicelevel_tag=='') $servicelevel_tag = servicelevel_id2tag(maintenance_servicelevel($incident->maintenanceid));
$servicelevel_name=servicelevel_name($servicelevelid);
if($incident->closed == 0) $closed = time();
else $closed = $incident->closed;
$opened_for=format_seconds($closed - $incident->opened);

// Lookup the service level times
$slsql = "SELECT * FROM servicelevels WHERE tag='{$servicelevel_tag}' AND priority='{$incident->priority}' ";
$slresult = mysql_query($slsql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$servicelevel = mysql_fetch_object($slresult);

// Get next target
$target = incident_get_next_target($incidentid);
// Calculate time remaining in SLA
$working_day_mins = ($CONFIG['end_working_day'] - $CONFIG['start_working_day']) / 60;
switch ($target->type)
{
    case 'initialresponse': $slatarget=$servicelevel->initial_response_mins; break;
    case 'probdef': $slatarget=$servicelevel->prob_determ_mins; break;
    case 'actionplan': $slatarget=$servicelevel->action_plan_mins; break;
    case 'solution': $slatarget=($servicelevel->resolution_days * $working_day_mins); break;
    default: $slaremain=0; $slatarget=0;
}

if ($slatarget >0) $slaremain=($slatarget - $target->since);
else $slaremain=0;
$targettype = target_type_name($target->type);

// Get next review time
$reviewsince = incident_get_next_review($incidentid);  // time since last review in minutes
$reviewtarget = ($servicelevel->review_days * $working_day_mins);          // how often reviews should happen in minutes
if ($reviewtarget > 0) $reviewremain=($reviewtarget - $reviewsince);
else $reviewremain = 0;

// Color the title bar according to the SLA and priority
$class='';
if ($slaremain <> 0 AND $incident->status!=2)
{
    if (($slaremain - ($slatarget * ((100 - $CONFIG['notice_threshold']) /100))) < 0 ) $class='notice';
    if (($slaremain - ($slatarget * ((100 - $CONFIG['urgent_threshold']) /100))) < 0 ) $class='urgent';
    if (($slaremain - ($slatarget * ((100 - $CONFIG['critical_threshold']) /100))) < 0 ) $class='critical';
    if ($incidents["priority"]==4) $class='critical';  // Force critical incidents to be critical always
}

// Print a table showing summary details of the incident

if ($_REQUEST['win']=='incomingview') echo "<h1 class='review'>Incoming</h1>";
else echo "<h1 class='$class'>{$title}: {$incidentid} - ".stripslashes($incident->title)."</h1>";

echo "<div id='navmenu'>";
if ($menu != 'hide')
{
    if ($_REQUEST['win']=='incomingview')
    {
        $insql = "SELECT emailfrom, contactid, updateid, tempincoming.id, timestamp
                FROM tempincoming, updates
                WHERE tempincoming.id={$id}
                AND tempincoming.updateid=updates.id";
        $query = mysql_query($insql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while($inupdate = mysql_fetch_object($query))
        {
            echo "<a class='barlink' href='unlock_update.php?id={$id}'>Unlock</a> | ";
            echo "<a class='barlink' href=\"javascript:window.opener.location='move_update.php?updateid={$inupdate->updateid}&amp;incidentidnumber={$update['incidentid']}'; window.close();\" >{$strAssign}</a> | ";
            echo "<a class='barlink' href=\"javascript:window.opener.location='add_incident.php?action=findcontact&amp;incomingid={$id}&amp;search_string={$inupdate->emailfrom}&amp;contactid={$inupdate->contactid}&amp;win=incomingcreate'; window.close();\">{$strCreate}</a> | ";
            echo "<a class='barlink' href=\"javascript:window.opener.location='delete_update.php?updateid={$inupdate->updateid}&amp;tempid={$inupdate->id}&amp;timestamp={$inupdate->timestamp}'; window.close(); \">{$strDelete}</a>";
        }
    }
    elseif (incident_status($id) != 2)
    {
        echo "<a class='barlink' href='update_incident.php?id={$id}&amp;popup={$popup}' accesskey='U'>{$strUpdate}</a> | ";
        echo "<a class='barlink' href='javascript:close_window({$id});' accesskey='C'>{$strClose}</a> | ";
        echo "<a class='barlink' href='reassign_incident.php?id={$id}&amp;popup={$popup}' accesskey='R'>{$strReassign}</a> | ";
        echo "<a class='barlink' href='edit_incident.php?id={$id}&amp;popup={$popup}' accesskey='T'>{$strEdit}</a> | ";
        echo "<a class='barlink' href='incident_service_levels.php?id={$id}&amp;popup={$popup}' accesskey='S'>{$strService}</a> | ";
        echo "<a class='barlink' href='incident_relationships.php?id={$id}&amp;tab=relationships' accesskey='L'>{$strRelations}</a> | ";
        echo "<a class='barlink' href='javascript:email_window({$id})' accesskey='E'>{$strEmail}</a> | ";
        echo "<a class='barlink' href='incident_attachments.php?id={$id}&amp;popup={$popup}' accesskey='F'>{$strFiles}</a> | ";
        if($servicelevel->timed =='yes') echo "<a class='barlink' href='tasks.php?incident={$id}'>Tasks</a> | ";
        echo "<a class='barlink' href='related_incidents.php?id={$id}&amp;pop={$popup}' accesskey='A'>{$strRelated}</a> | ";
        echo "<a class='barlink' href='incident_details.php?id={$id}&amp;popup={$popup}' accesskey='D'>{$strDetailsAndLog}</a> | ";

        echo "<a class='barlink' href='javascript:help_window({$permission});'>{$strHelpChar}</a>";
        if (!empty($_REQUEST['popup'])) echo " | <a class=barlink href='javascript:window.close();'>{$strCloseWindow}</a>";
    }
    else
    {
        echo "<a class='barlink' href='reopen_incident.php?id={$id}&amp;popup={$popup}'>{$strReopen}</a> | ";
        echo "<a class='barlink' href='incident_service_levels.php?id={$id}&amp;popup={$poup}' accesskey='S'>{$strService}</a> | ";
        echo "<a class='barlink' href='incident_relationships.php?id={$id}&amp;tab=relationships'>{$strRelations}</a> | ";
        echo "<a class='barlink' href='incident_attachments.php?id={$id}&amp;popup={$popup}' accesskey='F'>{$strFiles}</a> | ";
        echo "<a class='barlink' href='incident_details.php?id={$id}&amp;popup={$popup}' accesskey='D'>{$strDetailsAndLog}</a> | ";
        echo "<a class='barlink' href='javascript:help_window({$permission});'>{$strHelpChar}</a>";
        if (!empty($_REQUEST['popup'])) echo " | <a class='barlink' href='javascript:window.close();'>{$strCloseWindow}</a>";
    }
}
else
{
    echo "<a class='barlink' href='javascript:window.close();'>{$strCloseWindow}</a>";
}
echo "</div>";

?>