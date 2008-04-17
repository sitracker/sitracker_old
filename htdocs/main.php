<?php
// main.php - Front page
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>
// This Page Is *NOT* Valid XHTML 1.0 Transitional!

@include('set_include_path.inc.php');

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// --------------------------------------------------------------------------------------------
// Dashboard widgets

$sql = "SELECT * FROM `{$dbDashboard}` WHERE enabled='true' ORDER BY id";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
while ($dashboard = mysql_fetch_object($result))
{
   include ("{$CONFIG['application_fspath']}dashboard{$fsdelim}dashboard_{$dashboard->name}.php");
   $DASHBOARDCOMP["dashboard_{$dashboard->name}"]="dashboard_{$dashboard->name}";
}

// Valid user
include('htmlheader.inc.php');
echo "<script type=\"text/javascript\" src=\"scripts/dojo/dojo.js\"></script>";

$sql = "SELECT dashboard FROM users WHERE id = '".$_SESSION['userid']."'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

if(mysql_num_rows($result) > 0)
{
    $obj = mysql_fetch_object($result);
    $dashboardcomponents = explode(",",$obj->dashboard);
}

$col0 = 0;
$col1 = 0;
$col2 = 0;

$cols0 = "";
$cols1 = "";
$cols2 = "";

foreach ($dashboardcomponents AS $db)
{
    $c = explode("-",$db);
    switch ($c[0])
    {
        case 0: $col0++;
            $cols0 .= $c[1].",";
            break;
        case 1: $col1++;
            $cols1 .= $c[1].",";
            break;
        case 2: $col2++;
            $cols2 .= $c[1].",";
            break;
    }
}

$colstr = $col0.",".$col1.",".$col2;

$cols0 = substr($cols0, 0, -1);
$cols1 = substr($cols1, 0, -1);
$cols2 = substr($cols2, 0, -1);
echo "<p id='pageoptions'>".help_link("Dashboard")." <a href='manage_user_dashboard.php' title='{$strManageYourDashboard}'>";
echo $strManageYourDashboard;
echo " <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/dashboardadd.png' width='16' height='16' alt='' /></a> ";
// FIXME i18n Save Dashboard Layout Manually
echo "<a href=\"javascript:save_layout();\" id='savelayout' title='Save Dashboard Layout Manually'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/save.png' width='16' height='16' alt='' /></a></p>";
?>
<script type="text/javascript">
/* <![CDATA[ */
    dojo.require("dojo.dnd.*");
    dojo.require("dojo.event.*");

    function byId(id){
        return document.getElementById(id);
    }

    function resetborder()
    {
        $('col0').style.border = '0px';
        $('col1').style.border = '0px';
        $('col2').style.border = '0px';
    }

    function init(){

        //var cols = [1,3,1];
        var cols = [<?php echo $colstr; ?>];
        var cols0 = [<?php echo $cols0; ?>];
        var cols1 = [<?php echo $cols1; ?>];
        var cols2 = [<?php echo $cols2; ?>];

        // list one
        var dl = byId("col0");
        var dt1=new dojo.dnd.HtmlDropTarget(dl, ["li1"]);

        dojo.event.connect(dt1, "onDragOver", function(e) {
        if ($(e.target.id)) $(e.target.id).style.border = '2px dashed #cccccc;';
        });

        dojo.event.connect(dt1, "onDrop", function(e) {
        $('savelayout').style.display='inline';
            resetborder();
            //      window.alert(e.dragObject.domNode.id + ' was dropped on ' + e.target.id);
            save_layout();
        });
        for(var x=0; x<cols0.length; x++){
            new dojo.dnd.HtmlDragSource(byId('db_0-'+cols0[x]),"li1");
        }

        // list two
        var dl = byId("col1");
        var dt2 = new dojo.dnd.HtmlDropTarget(dl, ["li1"]);
        dojo.event.connect(dt2, "onDragOver", function(e) {
        if ($(e.target.id)) $(e.target.id).style.border = '2px dashed #cccccc;';
        });

        dojo.event.connect(dt2, "onDrop", function(e) {
            $('savelayout').style.display='inline';
            resetborder();
            save_layout();
        });
        for(var x=0; x<cols1.length; x++){
            new dojo.dnd.HtmlDragSource(byId('db_1-'+cols1[x]),"li1");
        }

        // list three
        var dl = byId("col2");
        var dt3 = new dojo.dnd.HtmlDropTarget(dl, ["li1"]);
        dojo.event.connect(dt3, "onDragOver", function(e) {
        if ($(e.target.id)) $(e.target.id).style.border = '2px dashed #cccccc;';
        });

        dojo.event.connect(dt3, "onDrop", function(e) {
            $('savelayout').style.display='inline';
            resetborder();
            save_layout();
        });
        for(var x=0; x<cols2.length; x++){
            new dojo.dnd.HtmlDragSource(byId('db_2-'+cols2[x]),"li1");
        }
    }

    dojo.event.connect(dojo, "loaded", "init");

    /*
        Not directly dojo related
    */

    function save_layout(){
        var xmlhttp=false;
        /*@cc_on @*/
        /*@if (@_jscript_version >= 5)
        // JScript gives us Conditional compilation, we can cope with old IE versions.
        // and security blocked creation of the objects.
        try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
        try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
        xmlhttp = false;
        }
        }
        @end @*/
        if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
            try {
                xmlhttp = new XMLHttpRequest();
            } catch (e) {
                xmlhttp=false;
            }
        }
        if (!xmlhttp && window.createRequest) {
            try {
                xmlhttp = window.createRequest();
            } catch (e) {
                xmlhttp=false;
            }
        }

        var toPass = "";
        for(var i = 0; i < 3; i++){
            var col = byId("col"+i).childNodes;
            var s = "";
            for(var x = 0; x < col.length; x++){
                // s = s+col.item(x).id.substr(5)+"-";
                s = s+i+"-"+col.item(x).id.substr(5)+",";
            }
            toPass = toPass+s.substr(0,s.length-1)+",";
        }


        xmlhttp.open("GET", "storedashboard.php?id="+<?php echo $_SESSION['userid']; ?>+"&val="+escape(toPass), true);

        xmlhttp.onreadystatechange=function() {
            //remove this in the future after testing
            if (xmlhttp.readyState==4) {
                if(xmlhttp.responseText != ""){
                    //alert(xmlhttp.responseText);
                }
            }
        }
        xmlhttp.send(null);
        $('savelayout').style.display='none';
    }
    window.onunload = save_layout;
    $('savelayout').style.display='none';

/* ]]> */
</script>
<?php
echo "<table id='dashboardlayout' border=\"0\" width=\"99%\" id='cols'><tr>";
echo "<td width=\"33%\" valign='top' id='col0'>";

$arr = explode(",",$cols0);
foreach ($arr AS $a)
{
    show_dashboard_component(0,$a);
}

echo "</td><td width=\"33%\" valign='top' id='col1'>";

$arr = explode(",",$cols1);
foreach ($arr AS $a)
{
    show_dashboard_component(1,$a);
}

echo "</td><td width=\"33%\" valign=\"top\" id='col2'>";

$arr = explode(",",$cols2);
foreach ($arr AS $a)
{
    show_dashboard_component(2,$a);
}

echo "</td></tr></table>\n";

//  Users Login Details
echo "<div id='userbar'>".sprintf($strLoggedInAs, "<strong>{$sit[0]}</strong>");
echo " currently <strong>".userstatus_name(user_status($sit[2]))."</strong> and ";

if (user_accepting($sit[2])!='Yes')
{
    echo "<span class=\"error\">{$strNotAccepting}</span>";
}
else
{
    echo "<strong>{$strAccepting}</strong>";
}

echo " calls";// FIXME i18n

if ($sit[3] == 'public')
{
    echo "- Public/Shared Computer (Increased Security)"; // FIXME i18n
}

echo "</div>\n<br />\n";
echo "<div id='footerbar'>";
echo "<form action='{$_SERVER['PHP_SELF']}'>";
echo "{$strSetYourStatus}: ";
if (isset($sit[2]))
{
   echo userstatus_bardrop_down("status", user_status($sit[2])).help_link("SetYourStatus");
}
echo "</form>\n";
echo "</div>\n";
include('htmlfooter.inc.php');
?>