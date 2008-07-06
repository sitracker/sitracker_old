<?
// get_bits_and_pieces.inc.php - Page to get items to be displayed else where
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>


@include ('set_include_path.inc.php');
$permission=0; // not required
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$toget = cleanvar($_REQUEST['toget']);
$selected = cleanvar($_REQUEST['selected']);

if (!empty($toget))
{
    switch ($toget)
    {
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
    }

}

?>
