<?php
// htmlheader.inc.php - Header html to be included at the top of pages
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

// Use session language if available, else use default language
if (!empty($_SESSION['lang'])) $lang = $_SESSION['lang'];
else $lang = $CONFIG['default_i18n'];

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
echo "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"{$lang}\" lang=\"{$lang}\">\n";
echo "<head>\n";
echo "<!-- SiT (Support Incident Tracker) - Support call tracking system\n";
echo "     Copyright (C) 2000-2007 Salford Software Ltd. and Contributors\n\n";
echo "     This software may be used and distributed according to the terms\n";
echo "     of the GNU General Public License, incorporated herein by reference. -->\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html;charset={$i18ncharset}\" />\n";
echo "<meta name=\"GENERATOR\" content=\"{$CONFIG['application_name']} {$application_version_string}\" />\n";
echo "<title>";
if (isset($title)) { echo "$title - {$CONFIG['application_shortname']}"; } else { echo "{$CONFIG['application_name']}{$extratitlestring}"; }
echo "</title>\n";
echo "<link rel='SHORTCUT ICON' href='{$CONFIG['application_webpath']}images/sit_favicon.png' />\n";
echo "<style type='text/css'>@import url('{$CONFIG['application_webpath']}styles/webtrack.css');</style>\n";
if ($_SESSION['auth'] == TRUE) $styleid = $_SESSION['style'];
else $styleid= $CONFIG['default_interface_style'];
$csssql = "SELECT cssurl, iconset FROM interfacestyles WHERE id='{$styleid}'";
$cssresult = mysql_query($csssql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
else list($cssurl, $iconset) = mysql_fetch_row($cssresult);
unset($styleid);
echo "<link rel='stylesheet' href='{$CONFIG['application_webpath']}styles/{$cssurl}' />\n";

if (isset($refresh) && $refresh != 0)
{
   echo "<meta http-equiv=\"refresh\" content=\"$refresh\" />\n";
}
echo "<script src='{$CONFIG['application_webpath']}scripts/prototype/prototype.js' type='text/javascript'></script>\n";
echo "<script src='{$CONFIG['application_webpath']}webtrack.js' type='text/javascript'></script>\n";
// javascript popup date library
echo "<script src='{$CONFIG['application_webpath']}calendar.js' type='text/javascript'></script>\n";

if ($sit[0] != '')
{
    echo "<link rel=\"search\" type=\"application/opensearchdescription+xml\" title=\"{$CONFIG['application_shortname']} Search\" href=\"{$CONFIG['application_webpath']}opensearch.php\" />";
}

echo "</head>\n";
echo "<body>\n";
echo "<h1 id='apptitle'>{$CONFIG['application_name']}</h1>\n";
// Show menu if logged in
if ($sit[0]!='')
{
    // Build a heirarchical top menu
    $hmenu;
    if (!is_array($hmenu))
    {
        echo "<p class='error'>Error. Menu not defined</p>";
    }

//     if ($CONFIG['debug'])
//     {
//         $dbg .= 'permissions'.print_r($_SESSION['permissions'],true);
//     }
    echo "<div id='menu'>\n";
    echo "<ul id='menuList'>\n";
    foreach ($hmenu[0] as $top => $topvalue)
    {
        echo "<li class='menuitem'>";
        // Permission Required: ".permission_name($topvalue['perm'])."
        if ($topvalue['perm'] >=1 AND !in_array($topvalue['perm'], $_SESSION['permissions'])) echo "<a href='javascript:void();' class='greyed'>{$topvalue['name']}</a>";
        else echo "<a href=\"{$topvalue['url']}\">{$topvalue['name']}</a>";
        // Do we need a submenu?
        if ($topvalue['submenu'] > 0 AND in_array($topvalue['perm'], $_SESSION['permissions']))
        {
            echo "\n<ul>"; //  id='menuSub'
            foreach ($hmenu[$topvalue['submenu']] as $sub => $subvalue)
            {
                if ($subvalue['submenu'] > 0) echo "<li class='submenu'>";
                else echo "<li>";
                if ($subvalue['perm'] >=1 AND !in_array($subvalue['perm'], $_SESSION['permissions'])) echo "<a href='javascript:void();' class='greyed'>{$subvalue['name']}</a>";
                else echo "<a href=\"{$subvalue['url']}\">{$subvalue['name']}</a>";
                if ($subvalue['submenu'] > 0 AND in_array($subvalue['perm'], $_SESSION['permissions']))
                {
                    echo "<ul>"; // id ='menuSubSub'
                    foreach ($hmenu[$subvalue['submenu']] as $subsub => $subsubvalue)
                    {
                        if ($subsubvalue['submenu'] > 0) echo "<li class='submenu'>";
                        else echo "<li>";
                        if ($subsubvalue['perm'] >=1 AND !in_array($subsubvalue['perm'], $_SESSION['permissions'])) echo "<a href=\"javascript:void();\" class='greyed'>{$subsubvalue['name']}</a>";
                        else echo "<a href=\"{$subsubvalue['url']}\">{$subsubvalue['name']}</a>";
                        if ($subsubvalue['submenu'] > 0 AND in_array($subsubvalue['perm'], $_SESSION['permissions']))
                        {
                            echo "<ul>"; // id ='menuSubSubSub'
                            foreach ($hmenu[$subsubvalue['submenu']] as $subsubsub => $subsubsubvalue)
                            {
                                if ($subsubsubvalue['submenu'] > 0) echo "<li class='submenu'>";
                                else echo "<li>";
                                if ($subsubsubvalue['perm'] >=1 AND !in_array($subsubsubvalue['perm'], $_SESSION['permissions'])) echo "<a href='javascript:void();' class='greyed'>{$subsubsubvalue['name']}</a>";
                                else echo "<a href=\"{$subsubsubvalue['url']}\">{$subsubsubvalue['name']}</a>";
                                echo "</li>\n";
                            }
                            echo "</ul>\n";
                        }
                        echo "</li>\n";
                    }
                    echo "</ul>\n";
                }
                echo "</li>\n";
            }
           echo "</ul>\n";
        }
        echo "</li>\n";
    }
    echo "</ul>\n\n";
    echo "</div>\n";
}

if (!isset($refresh))
{
    //update last seen (only if this is a page that does not auto-refresh)
    $lastseensql = "UPDATE LOW_PRIORITY users SET lastseen=NOW() WHERE id='{$_SESSION['userid']}' LIMIT 1";
    mysql_query($lastseensql);
    if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
}



//dismiss any notices
$noticeaction = cleanvar($_REQUEST['noticeaction']);
$noticeid = cleanvar($_REQUEST['noticeid']);

if ($noticeaction=='dismiss_notice')
{
    if (is_numeric($noticeid))
    {
        $sql = "DELETE FROM notices WHERE id={$noticeid} AND userid={$sit[2]}";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    }
    elseif ($noticeid == 'all')
    {
        $sql = "DELETE FROM notices WHERE userid={$sit[2]} LIMIT 20"; // only delete 20 max as we only show 20 max
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    }
}


//display global notices
if ($sit[0] != '')
{
    $noticesql = "SELECT * FROM notices ";
    $noticesql .= "WHERE userid={$sit[2]} ORDER BY timestamp DESC LIMIT 20"; // Don't show more than 20 notices, saftey cap
    $noticeresult = mysql_query($noticesql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($noticeresult) > 0)
    {
        while ($notice = mysql_fetch_object($noticeresult))
        {
            $notice->text = bbcode($notice->text);
            //check for the notice types
            if ($notice->type == SIT_UPGRADED_NOTICE)
            {
                $notice->text = str_replace('$strSitUpgraded', sprintf($strSitUpgraded, $CONFIG['application_shortname'], "v{$application_version} {$application_revision}"), $notice->text);
            }
            elseif ($notice->type == WARNING_NOTICE_TYPE)
            {
                echo "<div class='warning'><p class='warning'>";
                echo "<span>(<a href='{$_SERVER[PHP_SELF]}?noticeaction=dismiss_notice&amp;noticeid={$notice->id}'>$strDismiss</a>)</span>";
                echo $notice->text;
            }
            elseif ($notice->type == CRITICAL_NOTICE_TYPE)
            {
                echo "<div class='error'><p class='error'>";
                echo $notice->text;
                if ($notice->resolutionpage) $redirpage = $CONFIG['application_webpath'].$notice->resolutionpage;
            }
            elseif ($notice->type == OUT_OF_SLA_TYPE OR $notice->type == NEARING_SLA_TYPE)
            {
                echo "<div class='error'><p class='warning'>";
                echo "<span>(<a href='{$_SERVER[PHP_SELF]}?noticeaction=dismiss_notice&amp;noticeid={$notice->id}'>$strDismiss</a>)</span>";
                echo "{$notice->text}";
                if (!empty($notice->link))
                {
                    echo " - <a href=\"{$notice->link}\">";
                    if (substr($notice->linktext, 0, 4)=='$str')
                    {
                        $v = substr($notice->linktext, 1);
                        echo $GLOBALS[$v];
                    }
                    else echo "{$notice->linktext}";
                    echo "</a>";
                }
            }
            else
            {
                echo "<div class='info'><p class='info'>";
                echo "<span>(<a href='{$_SERVER[PHP_SELF]}?noticeaction=dismiss_notice&amp;noticeid={$notice->id}'>$strDismiss</a>)</span>";
                if (substr($notice->text, 0, 4)=='$str')
                {
                    $v = substr($notice->text, 1);
                    echo $GLOBALS[$v];
                }
                else echo "{$notice->text}";
                if (!empty($notice->link))
                {
                    echo " - <a href=\"{$notice->link}\">";
                    if (substr($notice->linktext, 0, 4)=='$str')
                    {
                        $v = substr($notice->linktext, 1);
                        echo $GLOBALS[$v];
                    }
                    else echo "{$notice->linktext}";
                    echo "</a>";
                }
            }
            echo "</p></div>";
        }
        if (mysql_num_rows($noticeresult) > 1)
        {
            echo "<p align='right' style='padding-right:32px'><a href='{$_SERVER[PHP_SELF]}?noticeaction=dismiss_notice&amp;noticeid=all'>{$strDismissAll}</a></p>";
        }
        //echo "</div>";
    }
    if ($redirpage && ($_SERVER[SCRIPT_NAME] != $redirpage))
    {
        // Note, this uses FALSE which prints 'Failed' not sure this is the most appropriate,
        // but html_redirect only supports success/failure currently (INL 1dec07)
        html_redirect($redirpage, FALSE, "You are being redirected to fix an error"); // FIXME i18n
        exit;
    }
}
$headerdisplayed=TRUE; // Set a variable so we can check to see if the header was included
echo "<div id='mainframe'>";
?>