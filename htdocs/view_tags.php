<?php
// view_tags.php - Page to view the tags on either a record or in general
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$tagid = cleanvar($_REQUEST['tagid']);
$orderby = cleanvar($_REQUEST['orderby']);

if(empty($orderby)) $orderby = "name";

if(empty($tagid))
{
    //show all tags
    include('htmlheader.inc.php');
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/tag.png' width='32' height='32' alt='' /> ";
    echo "{$strTags}</h2>";
    echo show_tag_cloud($orderby,TRUE);
    include('htmlfooter.inc.php');
}
else
{
    $sql = "SELECT name FROM tags WHERE tagid = '$tagid' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    list($tagname)=mysql_fetch_row($result);

    include('htmlheader.inc.php');
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/tag.png' width='32' height='32' alt='' /> <a href='view_tags.php'>{$strTag}</a>: $tagname";
    if (array_key_exists($tagname, $CONFIG['tag_icons'])) echo "&nbsp;<img src='images/icons/sit/32x32/{$CONFIG['tag_icons'][$tagname]}.png' style='border:0px;' alt='' />";
    echo "</h2>";


    //show only this tag
    $sql = "SELECT * FROM set_tags WHERE tagid = '$tagid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    $col = 0;
    $count = 0;
    $num_tags = mysql_num_rows($result);
    if($num_tags > 0)
    {
        echo "<table align='center'>";
        while($obj = mysql_fetch_object($result))
        {
            if ($col == 0) echo "<tr style='text-align: left;'>";

            switch($obj->type)
            {
                case TAG_CONTACT: //contact
                    $sql = "SELECT forenames, surname FROM contacts WHERE id = '{$obj->id}'";
                    $resultcon = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resultcon) > 0)
                    {
                        $objcon = mysql_fetch_object($resultcon);
                        echo "<th><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contact.png' width='16' height='16' alt='' /> {$strContact}</th><td><a href='contact_details.php?id={$obj->id}'>";
                        echo stripslashes($objcon->forenames." ".$objcon->surname)."</a></td>";
                    }
                break;

                case TAG_INCIDENT: //incident
                    $sql = "SELECT title FROM incidents WHERE id = '$obj->id'";
                    $resultinc = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resultinc) > 0)
                    {
                        $objinc = mysql_fetch_object($resultinc);
                        echo "<th><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/support.png' width='16' height='16' alt='' /> {$strIncident}</th><td><a href=\"javascript:incident_details_window('{$obj->id}','incident{$obj->id}')\">";
                        echo "{$obj->id}: ".stripslashes($objinc->title)."</a></td>";
                    }
                break;

                case TAG_SITE: //site
                    $sql = "SELECT name FROM sites WHERE id = '{$obj->id}'";
                    $resultsite = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resultsite) > 0)
                    {
                        $objsite = mysql_fetch_object($resultsite);
                        echo "<th><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/site.png' width='16' height='16' alt='' /> {$strSite}</th><td><a href='site_details.php?id={$obj->id}&amp;action=show'>";
                        echo stripslashes($objsite->name)."</a></td>";
                    }
                break;

                case TAG_TASK: // task
                    $sql = "SELECT name FROM tasks WHERE id = '{$obj->id}'";
                    $resulttask = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resulttask) > 0)
                    {
                        $objtask = mysql_fetch_object($resulttask);
                        echo "<th><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/task.png' width='16' height='16' alt='' /> {$strTask}</th><td><a href='view_task.php?id={$obj->id}'>";
                        echo stripslashes($objtask->name)."</a></td>";
                    }
                break;

                case TAG_SKILL:
                    $sql = "SELECT name FROM software WHERE id = '{$obj->id}'";
                    $resultskill = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resultskill) > 0)
                    {
                        $objtask = mysql_fetch_object($resultskill);
                        echo "<th><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/skill.png' width='16' height='16' alt='' /> {$strSkill}</th><td>";
                        echo stripslashes($objtask->name)."</td>";
                    }
                break;

                case TAG_PRODUCT:
                    $sql = "SELECT name FROM products WHERE id = '{$obj->id}'";
                    $resultprod = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resultprod) > 0)
                    {
                        $objtask = mysql_fetch_object($resultprod);
                        echo "<th><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/product.png' width='16' height='16' alt='' /> {$strProduct}</th>";
                        echo "<td><a href='products.php?productid={$obj->id}'>".stripslashes($objtask->name)."</a></td>";
                    }
                break;

                default:
                    echo "<th>{$strOther}</th><td>{$obj->id}/{$obj->type}</td>";
            }
            $col++;
            $count++;
            if ($col >= 3 OR $count == $num_tags)
            {
                 echo "</tr>\n";
                $col=0;
            }
        }
        echo "</table>";
        echo "<p align='center'>".sprintf($strTagsMulti, $num_tags)."</p>";
    }
    include('htmlfooter.inc.php');
}

?>