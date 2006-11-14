<?php
session_name($CONFIG['session_name']);
session_start();
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\"><head><title>";
echo "{$incidentid} - ";
if (isset($title)) echo $title;
else echo $CONFIG['application_shortname'];
echo "</title>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
echo "<meta name=\"GENERATOR\" content=\"{$CONFIG['application_name']} {$application_version_string}\" />\n";
echo "<style type='text/css'>@import url('{$CONFIG['application_webpath']}styles/webtrack.css');</style>\n";

if ($_SESSION['auth'] == TRUE) $styleid = $_SESSION['style'];
else $styleid= $CONFIG['default_interface_style'];
$sql = "SELECT cssurl, iconset FROM interfacestyles WHERE id='{$styleid}'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
else list($cssurl, $iconset) = mysql_fetch_row($result);
unset($styleid);
echo "<link rel='stylesheet' href='{$CONFIG['application_webpath']}styles/{$cssurl}' />\n";

echo "<script src='{$CONFIG['application_webpath']}webtrack.js' type='text/javascript'></script>\n";
// javascript popup date library
echo "<script src='{$CONFIG['application_webpath']}calendar.js' type='text/javascript'></script>\n";

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
    border-top: 3px solid #3165CD;
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
    background: black;
    padding-left: 20px;
    padding-top: 2px;
    padding-bottom: 2px;
}
#navmenu a { color: white; }

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
$sql .= "contacts.id AS contactid ";
$sql .= "FROM incidents, contacts ";
$sql .= "WHERE (incidents.id='{$incidentid}' AND incidents.contact=contacts.id) ";
$sql .= " OR incidents.contact=NULL ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$incident = mysql_fetch_object($result);

// Prepare data for output
$incident->title = stripslashes($incident->title);
$incident->externalengineer = stripslashes($incident->externalengineer);
$incident->product = stripslashes($incident->product);
$incident->productversion = stripslashes($incident->productversion);
$incident->servicepacks = stripslashes($incident->servicepacks);

$site_name=site_name($incident->siteid);
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

echo "<h1 class='$class'>{$title}: {$incidentid} - ".stripslashes($incident->title)."</h1>";

include('incident/details.inc.php');




?>
