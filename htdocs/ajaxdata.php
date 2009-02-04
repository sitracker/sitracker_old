<?php
// ajaxdata.php - Return data for AJAX calls
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 0; // not required
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
if ($_REQUEST['action'] == 'contexthelp' AND $_REQUEST['auth'] == 'portal')
{
    // Special exception for contexthelp, use the portal authentication for
    // portal help tips
    $accesslevel = 'any';
    require('portalauth.inc.php');
}
else
{
    require ($lib_path.'auth.inc.php');
}
$action = cleanvar($_REQUEST['action']);
$selected = cleanvar($_REQUEST['selected']);

if ($_SESSION['auth'] == TRUE)
{
    $styleid = $_SESSION['style'];
}
else
{
    $styleid = $CONFIG['default_interface_style'];
}
$iconsql = "SELECT iconset FROM `{$GLOBALS['dbInterfaceStyles']}` WHERE id='{$styleid}'";
$iconresult = mysql_query($iconsql);
if (mysql_error())trigger_error(mysql_error(),E_USER_WARNING);

list($iconset) = mysql_fetch_row($iconresult);

switch ($action)
{
    case 'auto_save':
        $userid = cleanvar($_REQUEST['userid']);
        $incidentid = cleanvar($_REQUEST['incidentid']);
        $type = cleanvar($_REQUEST['type']);
        $draftid = cleanvar($_REQUEST['draftid']);
        $meta = cleanvar($_REQUEST['meta']);
        $content = cleanvar($_REQUEST['content']);

        if ($userid == $_SESSION['userid'])
        {
            if ($draftid == -1)
            {
                $sql = "INSERT INTO `{$dbDrafts}` (userid,incidentid,type,meta,content,lastupdate) VALUES ('{$userid}','{$incidentid}','{$type}','{$meta}','{$content}','{$now}')";
            }
            else
            {
                $sql = "UPDATE `{$dbDrafts}` SET content = '{$content}', meta = '{$meta}', lastupdate = '{$now}' WHERE id = {$draftid}";
            }
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            echo mysql_insert_id();
        }
    break;

    case 'servicelevel_timed':
        $sltag = servicelevel_id2tag(cleanvar($_REQUEST['servicelevel']));
        if (servicelevel_timed($sltag))
        {
            echo "TRUE";
        }
        else
        {
            echo "FALSE";
        }
    break;

    case 'contexthelp':
        $context = cleanvar($_REQUEST['context']);
        $helpfile = "{$CONFIG['application_fspath']}htdocs/help/{$_SESSION['lang']}/{$context}.txt";
        // Default back to english if language helpfile isn't found
        if (!file_exists($helpfile)) $helpfile = "{$CONFIG['application_fspath']}htdocs/help/en-GB/{$context}.txt";
        if (file_exists($helpfile))
        {
            $fp = fopen($helpfile, 'r', TRUE);
            $helptext = fread($fp, 1024);
            fclose($fp);
            echo nl2br($helptext);
        }
        else echo "Error: Missing helpfile '{$_SESSION['lang']}/{$context}.txt'";
    break;

    case 'dismiss_notice':
        require ($lib_path.'auth.inc.php');
        $noticeid = cleanvar($_REQUEST['noticeid']);
        $userid = cleanvar($_REQUEST['userid']);
        if (is_numeric($noticeid))
        {
            $sql = "DELETE FROM `{$GLOBALS['dbNotices']}` WHERE id='{$noticeid}' AND userid='{$sit[2]}'";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            else echo "deleted {$noticeid}";
        }
        elseif ($noticeid == 'all')
        {
            $sql = "DELETE FROM `{$GLOBALS['dbNotices']}` WHERE userid={$userid} LIMIT 20"; // only delete 20 max as we only show 20 max
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            else echo "deleted {$noticeid}";
        }
    break;

    case 'dashboard_display':
        require ($lib_path.'auth.inc.php');
        $dashboard = cleanvar($_REQUEST['dashboard']);
        $dashletid = 'win'.cleanvar($_REQUEST['did']);
        // FIXME need some sanitation here
        include ("{$CONFIG['application_fspath']}htdocs{$fsdelim}plugins{$fsdelim}dashboard_{$dashboard}.php");
        $dashfn = "dashboard_{$dashboard}_display";
        echo $dashfn($dashletid);
    break;

    case 'dashboard_save':
    case 'dashboard_edit':
        require ($lib_path.'auth.inc.php');

        $dashboard = cleanvar($_REQUEST['dashboard']);
        $dashletid = 'win'.cleanvar($_REQUEST['did']);
        // FIXME need some sanitation here
        include ("{$CONFIG['application_fspath']}htdocs{$fsdelim}plugins{$fsdelim}dashboard_{$dashboard}.php");
        $dashfn = "dashboard_{$dashboard}_edit";
        echo $dashfn($dashletid);
    break;

    case 'autocomplete_sitecontact':
    break;

    case 'tags':
        $sql = "SELECT DISTINCT t.name FROM `{$dbSetTags}` AS st, `{$dbTags}` AS t WHERE st.tagid = t.tagid GROUP BY t.name";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($result) > 0)
        {
            while ($obj = mysql_fetch_object($result))
            {
                $str .= "[".$obj->name."],";
            }
        }
        echo "[".substr($str,0,-1)."]";
        break;

    case 'contact' :
        $sql = "SELECT DISTINCT forenames, surname FROM `{$dbContacts}` WHERE active='true'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($result) > 0)
        {
            while ($obj = mysql_fetch_object($result))
            {
                $str .= "[\"".$obj->surname."\"],";
                $str .= "[\"".$obj->forenames." ".$obj->surname."\"],";
            }
        }
        echo "[".substr($str,0,-1)."]";
        break;

    case 'sites':
        $sql = "SELECT DISTINCT name FROM `{$dbSites}` WHERE active='true'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($result) > 0)
        {
            while ($obj = mysql_fetch_object($result))
            {
                $str .= "[\"".$obj->name."\"],";
            }
        }
        echo "[".substr($str,0,-1)."]";
        break;

    case 'slas':
        $sql = "SELECT DISTINCT tag FROM `{$dbServiceLevels}`";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($obj = mysql_fetch_object($result))
        {
            $strIsSelected = '';
            if ($obj->tag == $selected)
            {
                $strIsSelected = "selected='selected'";
            }
            echo "<option value='{$obj->tag}' $strIsSelected>{$obj->tag}</option>";
        }
        break;

    case 'products':
        $sql = "SELECT id, name FROM `{$dbProducts}` ORDER BY name ASC";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($obj = mysql_fetch_object($result))
        {
            $strIsSelected = '';
            if ($obj->id == $selected)
            {
                $strIsSelected = "selected='selected'";
            }
            echo "<option value='{$obj->id}' $strIsSelected>{$obj->name}</option>";
        }
        break;

    case 'skills':
        $sql = "SELECT id, name FROM `{$dbSoftware}` ORDER BY name ASC";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($obj = mysql_fetch_object($result))
        {
            $strIsSelected = '';
            if ($obj->id == $selected)
            {
                $strIsSelected = "selected='selected'";
            }
            echo "<option value='{$obj->id}' $strIsSelected>{$obj->name}</option>";
        }
        break;

    case 'storedashboard':
        $id = $_REQUEST['id'];
        $val = $_REQUEST['val'];

        if ($id == $_SESSION['userid'])
        {
            //check you're changing your own
            $sql = "UPDATE `{$dbUsers}` SET dashboard = '$val' WHERE id = '$id'";
            $contactresult = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }
        break;

    default : break;
}


?>
