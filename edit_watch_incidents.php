<?php
// edit_watch_incidents.php - Interface to allow users to change the preferences of the watch incidents on the dashboard
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 0; // not required
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');


$action = $_REQUEST['action'];

switch ($action)
{
    case 'add':
        include ('./inc/htmlheader.inc.php');
        $type = $_REQUEST['type'];
        echo "<h2>{$strWatchAddSet}</h2>";
        echo "<form action='{$_SERVER['PHP_SELF']}?action=do_add&type={$type}' method='post'>";
        echo "<table class='vertical'>";
        echo "<tr><td>";

        switch ($type)
        {
            case '0': //site
                echo "{$strSite}: ";
                echo site_drop_down('id','');
                break;
            case '1': //contact
                echo "{$strContact}: ";
                echo contact_drop_down('id','');
                break;
            case '2': //engineer
                echo "{$strEngineer}: ";
                echo user_drop_down('id','',FALSE);
                break;
            case '3': //Incident
                echo "{$strIncident}:";
                echo "<input class='textbox' name='id' size='30' />";
                break;
        }

        echo "</td><tr>";
        echo "</table>";
        echo "<p align='center'><input name='submit' type='submit' value='{$strAdd}' /></p>";
        include ('./inc/htmlfooter.inc.php');
        break;
    case 'do_add':
        $id = $_REQUEST['id'];
        $type = $_REQUEST['type'];
        $sql = "INSERT INTO `{$dbDashboardWatchIncidents}` VALUES ({$sit[2]},'{$type}','{$id}')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if (!$result)
        {
            echo "<p class='error'>{$strWatchAddFailed}</p>";
        }
        else
        {
            html_redirect("edit_watch_incidents.php", TRUE, $strAddedSuccessfully);
        }
        break;
    case 'delete':
        $id = $_REQUEST['id'];
        $type = $_REQUEST['type'];
        $sql = "DELETE FROM `{$dbDashboardWatchIncidents}` WHERE id = '{$id}' AND userid = {$sit[2]} AND type = '{$type}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if (!$result)
        {
            echo "<p class='error'>{$strWatchDeleteFailed}</p>";
        }
        else
        {
            html_redirect("edit_watch_incidents.php", TRUE, $strRemovedSuccessful);
        }
        break;
    default:
        include ('./inc/htmlheader.inc.php');
        echo "<h2>{$strEditWatchedIncidents}</h2>";

        echo "<table align='center'>";
        for($i = 0; $i < 4; $i++)
        {
            $sql = "SELECT * FROM `{$dbDashboardWatchIncidents}` WHERE userid = {$sit[2]} AND type = {$i}";

            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

            echo "<tr><td align='left'><strong>";
            switch ($i)
            {
                case 0: echo $strSites;
                    break;
                case 1: echo $strContacts;
                    break;
                case 2: echo $strEngineers;
                    break;
                case 3: echo $strIncidents;
                    break;
            }
            echo "</strong></td><td align='right'>";
            echo "<a href='{$_SERVER['PHP_SELF']}?type={$i}&amp;action=add'>";
            switch ($i)
            {
                case 0: echo $strAddSite;
                    break;
                case 1: echo $strAddContact;
                    break;
                case 2: echo $strAddUser;
                    break;
                case 3: echo $strAddIncident;
                    break;
            }
            echo "</a></td></tr>";

            if (mysql_num_rows($result) > 0)
            {
                $shade = 'shade1';
                while ($obj = mysql_fetch_object($result))
                {
                    $name = '';
                    switch ($obj->type)
                    {
                        case 0: //site
                            $sql = "SELECT name FROM `{$dbSites}` WHERE id = {$obj->id}";
                            $iresult = mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                            $iobj = mysql_fetch_object($iresult);
                            $name = $iobj->name;
                            break;
                        case 1: //contact
                            $sql = "SELECT forenames, surname FROM `{$dbContacts}` WHERE id = {$obj->id}";
                            $iresult = mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                            $iobj = mysql_fetch_object($iresult);
                            $name = $iobj->forenames.' '.$iobj->surname;
                            break;
                        case 2: //Engineer
                            $sql = "SELECT realname FROM `{$dbUsers}` WHERE id = {$obj->id}";
                            $iresult = mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                            $iobj = mysql_fetch_object($iresult);
                            $name = $iobj->realname;
                            break;
                        case 3: //Incident
                            $sql = "SELECT title FROM `{$dbIncidents}` WHERE id = {$obj->id}";
                            $iresult = mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                            $iobj = mysql_fetch_object($iresult);
                            $name = "<a href=\"javascript:incident_details_window('{$obj->id}','incident{$obj->id}')\" class='info'>[{$obj->id}] {$iobj->title}</a>";
                            break;

                    }

                    echo "<tr class='$shade'><td>{$name}</td><td><a href='{$_SERVER['PHP_SELF']}?type={$obj->type}&amp;id={$obj->id}&amp;action=delete'>{$strRemove}</a></td></tr>";
                    if ($shade == 'shade1') $shade = 'shade2';
                    else $shade = 'shade1';
                }
            }
            else
            {
                echo "<tr><td colspan='2'>{$strNoIncidentsBeingWatchOfType}</td></tr>";
            }
        }
        echo "</table>";
        include ('./inc/htmlfooter.inc.php');
        break;
}

?>
