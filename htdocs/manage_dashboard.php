<?php
// manager_dashboard.php - Page to install a new dashboard component
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=66; // Install dashboard components
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

function beginsWith( $str, $sub ) {
   return ( substr( $str, 0, strlen( $sub ) ) === $sub );
}
function endsWith( $str, $sub ) {
   return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
}

switch($_REQUEST['action'])
{
    case 'install':
        include('htmlheader.inc.php');

        $sql = "SELECT name FROM dashboard";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/dashboard.png' width='32' height='32' alt='' /> ";
        echo "Install new dashboard component</h2>";
        echo "<p align='center'>Please note the component must has been placed in the dashboard directory and named <var>dashboard_NAME</var></p>";
        while($dashboardnames = mysql_fetch_object($result))
        {
            $dashboard[$dashboardnames->name] = $dashboardnames->name;
        }

        $path = "{$CONFIG['application_fspath']}dashboard/";

        $dir_handle = @opendir($path) or die("Unable to open dashboard directory $path");

        while($file = readdir($dir_handle))
        {
            if(beginsWith($file, "dashboard_") && endsWith($file, ".php"))
            {
                //echo "file name ".$file."<br />";
                if(empty($dashboard[substr($file, 10, strlen($file)-14)]))  //this is 14 due to .php =4 and dashboard_ = 10
                {
                    //echo "file name ".$file." - ".substr($file, 10, strlen($file)-14)."<br />";
                    //$html .= "echo "<option value='{$row->id}'>$row->realname</option>\n";";
                    $html .= "<option value='".substr($file, 10, strlen($file)-14)."'>".substr($file, 10, strlen($file)-14)." ({$file})</option>";
                }
            }
        }

        closedir($dir_handle);

        if(empty($html))
        {
            echo "<p align='center'>No new dashboard components available</p>";
            echo "<p align='center'><a href='manage_dashboard.php'>Back to list</p>";
        }
        else
        {
            echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>\n";
            echo "<table align='center' class='vertical'><tr><td>\n";
            echo "<select name='comp[]' multiple='multiple' size='20'>\n";
            echo $html;
            echo "</select>\n";
            echo "</td></tr></table>\n";
            echo "<input type='hidden' name='action' value='installdashboard' />";
            echo "<p align='center'><input type='submit' value='{$strInstall}' /></p>";
            echo "</form>\n";
        }

        include('htmlfooter.inc.php');

        break;
    case 'installdashboard':
        $dashboardcomponents = $_REQUEST['comp'];
        if(is_array($dashboardcomponents))
        {
            $count = count($dashboardcomponents);

            $sql = "INSERT INTO dashboard (name) VALUES ";
            for($i = 0; $i < $count; $i++)
            {
                $sql .= "('{$dashboardcomponents[$i]}'), ";
            }
            $result = mysql_query(substr($sql, 0, strlen($sql)-2));
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

            if(!$result) echo "<p class='error'>Instalation of plugin(s) failed</p>";
            else
            {
                // run the post install compoents
                foreach($dashboardcomponents AS $comp)
                {
                    include("{$CONFIG['application_fspath']}dashboard/dashboard_{$comp}.php");
                    $func = "dashboard_".$comp."_install";
                    if(function_exists($func)) $func();
                }

                confirmation_page("2", "manage_dashboard.php", "<h2>Dashboard components installed</h2><h5>Please wait while you are redirected...</h5>");
            }
        }
        break;
    case 'enable':
        $id = $_REQUEST['id'];
        $enable = $_REQUEST['enable'];
        $sql = "UPDATE dashboard SET enabled = '{$enable}' WHERE id = '{$id}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) echo "<p class='error'>Changed enabled state failed</p>";
        else
        {
            confirmation_page("2", "manage_dashboard.php", "<h2>Dashboard change succeded</h2><h5>Please wait while you are redirected...</h5>");
        }
        break;
    default:
        include('htmlheader.inc.php');

        $sql = "SELECT * FROM dashboard";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/dashboard.png' width='32' height='32' alt='' /> ";
        echo "View dashboard components</h2>";
        echo "<table class='vertical' align='center'><tr>";
        echo colheader('id','ID');
        echo colheader('name',$strName);
        echo colheader('enabled',$strEnabled);
        echo "</tr>";
        while($dashboardnames = mysql_fetch_object($result))
        {
            if($dashboardnames->enabled == "true") $opposite = "false";
            else $opposite = "true";
            echo "<tr><td class='shade2'>{$dashboardnames->id}</td>";
            echo "<td class='shade2'>{$dashboardnames->name}</td>";
            echo "<td class='shade2'><a href='".$_SERVER['PHP_SELF']."?action=enable&amp;id={$dashboardnames->id}&amp;enable={$opposite}'>{$dashboardnames->enabled}</a></td></tr>";
        }
        echo "</table>";

        echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?action=install'>{$strInstall}</a></p>";

        include('htmlfooter.inc.php');
        break;
}

?>