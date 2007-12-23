<?php
// autocomplete.php - Page to aid in the auto completion of fields
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 0; // not required
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$action = $_REQUEST['action'];

switch ($action)
{
    case 'tags':
        $sql = "SELECT DISTINCT t.name FROM `{$dbSetTags}` AS st, `{$dbTags}` AS t WHERE st.tagid = t.tagid GROUP BY t.name";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        if (mysql_num_rows($result) > 0)
        {
            while ($obj = mysql_fetch_object($result))
            {
                $str .= "[".$obj->name."],";
            }
        }
        break;
    case 'contact' :
        $sql = "SELECT DISTINCT forenames, surname FROM `{$dbContacts}` WHERE active='true'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        if (mysql_num_rows($result) > 0)
        {
            while ($obj = mysql_fetch_object($result))
            {
                $str .= "[\"".$obj->surname."\"],";
                $str .= "[\"".$obj->forenames." ".$obj->surname."\"],";
            }
        }
        break;
    case 'sites':
        $sql = "SELECT DISTINCT name FROM `{$dbSites}` WHERE active='true'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        if (mysql_num_rows($result) > 0)
        {
            while ($obj = mysql_fetch_object($result))
            {
                $str .= "[\"".$obj->name."\"],";
            }
        }
        break;
    default : break;
}

echo "[".substr($str,0,-1)."]";

?>
