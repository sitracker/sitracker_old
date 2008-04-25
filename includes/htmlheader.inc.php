<?php
// htmlheader.inc.php - Header html to be included at the top of pages
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
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
echo "     Copyright (C) 2000-2008 Salford Software Ltd. and Contributors\n\n";
echo "     This software may be used and distributed according to the terms\n";
echo "     of the GNU General Public License, incorporated herein by reference. -->\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html;charset={$i18ncharset}\" />\n";
echo "<meta name=\"GENERATOR\" content=\"{$CONFIG['application_name']} {$application_version_string}\" />\n";
echo "<title>";
if (isset($title))
{
    echo "$title - {$CONFIG['application_shortname']}";
}
else
{
    echo "{$CONFIG['application_name']}{$extratitlestring}";
}

echo "</title>\n";
echo "<link rel='SHORTCUT ICON' href='{$CONFIG['application_webpath']}images/sit_favicon.png' />\n";
echo "<style type='text/css'>@import url('{$CONFIG['application_webpath']}styles/sitbase.css');</style>\n";
if ($_SESSION['auth'] == TRUE)
{
    $styleid = $_SESSION['style'];
}
else
{
    $styleid = $CONFIG['default_interface_style'];
}

$csssql = "SELECT cssurl, iconset FROM `{$GLOBALS['dbInterfaceStyles']}` WHERE id='{$styleid}'";
$cssresult = mysql_query($csssql);
if (mysql_error())trigger_error(mysql_error(),E_USER_WARNING);

list($cssurl, $iconset) = mysql_fetch_row($cssresult);
unset($styleid);
echo "<link rel='stylesheet' href='{$CONFIG['application_webpath']}styles/{$cssurl}' />\n";

if (isset($refresh) && $refresh != 0)
{
   echo "<meta http-equiv='refresh' content='{$refresh}' />\n";
}

echo "<script src='{$CONFIG['application_webpath']}scripts/prototype/prototype.js' type='text/javascript'></script>\n";
echo "<script src='{$CONFIG['application_webpath']}webtrack.js' type='text/javascript'></script>\n";
// To include a script for a single page, add the filename to the $pagescripts variable before including htmlheader.inc.php
if (is_array($pagescripts))
{
    foreach ($pagescripts AS $pscript)
    {
        echo "<script src='{$CONFIG['application_webpath']}scripts/{$pscript}' type='text/javascript'></script>\n";
    }
    unset($pagescripts, $pscript);
}
// javascript popup date library
echo "<script src='{$CONFIG['application_webpath']}calendar.js' type='text/javascript'></script>\n";

if ($sit[0] != '')
{
    echo "<link rel='search' type='application/opensearchdescription+xml' title='{$CONFIG['application_shortname']} Search' href='{$CONFIG['application_webpath']}opensearch.php' />\n";
}

echo "</head>\n";
echo "<body>\n";
echo "<h1 id='apptitle'>{$CONFIG['application_name']}</h1>\n";
// Show menu if logged in
if ($sit[0] != '')
{
    // Build a heirarchical top menu
    $hmenu;
    if (!is_array($hmenu))
    {
        echo "<p class='error'>{$strErrorMenuNotDefined}</p>";
    }

//     if ($CONFIG['debug'])
//     {
//         $dbg .= 'permissions'.print_r($_SESSION['permissions'],true);
//     }
    echo "<div id='menu'>\n";
    echo "<ul id='menuList'>\n";
    foreach ($hmenu[0] as $top => $topvalue)
    {
        if ((!empty($topvalue['enablevar']) AND $CONFIG[$topvalue['enablevar']])
            OR empty($topvalue['enablevar']))
        {
            echo "<li class='menuitem'>";
            // Permission Required: ".permission_name($topvalue['perm'])."
            if ($topvalue['perm'] >=1 AND !in_array($topvalue['perm'], $_SESSION['permissions']))
            {
                echo "<a href='javascript:void(0);' class='greyed'>{$topvalue['name']}</a>";
            }
            else
            {
                echo "<a href='{$topvalue['url']}'>{$topvalue['name']}</a>";
            }

            // Do we need a submenu?
            if ($topvalue['submenu'] > 0 AND in_array($topvalue['perm'], $_SESSION['permissions']))
            {
                echo "\n<ul>"; //  id='menuSub'
                foreach ($hmenu[$topvalue['submenu']] as $sub => $subvalue)
                {
                    if((!empty($subvalue['enablevar']) AND $CONFIG[$subvalue['enablevar']])
                        OR empty($subvalue['enablevar']))
                    {
                        if ($subvalue['submenu'] > 0)
                        {
                            echo "<li class='submenu'>";
                        }
                        else
                        {
                            echo "<li>";
                        }

                        if ($subvalue['perm'] >=1 AND !in_array($subvalue['perm'], $_SESSION['permissions']))
                        {
                            echo "<a href='javascript:void(0);' class='greyed'>{$subvalue['name']}</a>";
                        }
                        else
                        {
                            echo "<a href=\"{$subvalue['url']}\">{$subvalue['name']}</a>";
                        }

                        if ($subvalue['submenu'] > 0 AND in_array($subvalue['perm'], $_SESSION['permissions']))
                        {
                            echo "<ul>"; // id ='menuSubSub'
                            foreach ($hmenu[$subvalue['submenu']] as $subsub => $subsubvalue)
                            {
                                if((!empty($subsubvalue['enablevar']) AND $CONFIG[$subsubvalue['enablevar']])
                                    OR empty($subsubvalue['enablevar']))
                                {
                                    if ($subsubvalue['submenu'] > 0)
                                    {
                                        echo "<li class='submenu'>";
                                    }
                                    else
                                    {
                                        echo "<li>";
                                    }

                                    if ($subsubvalue['perm'] >=1 AND !in_array($subsubvalue['perm'], $_SESSION['permissions']))
                                    {
                                        echo "<a href=\"javascript:void(0);\" class='greyed'>{$subsubvalue['name']}</a>";
                                    }
                                    else
                                    {
                                        echo "<a href='{$subsubvalue['url']}'>{$subsubvalue['name']}</a>";
                                    }

                                    if ($subsubvalue['submenu'] > 0 AND in_array($subsubvalue['perm'], $_SESSION['permissions']))
                                    {
                                        echo "<ul>"; // id ='menuSubSubSub'
                                        foreach ($hmenu[$subsubvalue['submenu']] as $subsubsub => $subsubsubvalue)
                                        {
                                             if((!empty($subsubsubvalue['enablevar']) AND $CONFIG[$subsubsubvalue['enablevar']])
                                                OR empty($subsubsubvalue['enablevar']))
                                            {
                                                if ($subsubsubvalue['submenu'] > 0)
                                                {
                                                    echo "<li class='submenu'>";
                                                }
                                                else
                                                {
                                                    echo "<li>";
                                                }

                                                if ($subsubsubvalue['perm'] >=1 AND !in_array($subsubsubvalue['perm'], $_SESSION['permissions']))
                                                {
                                                    echo "<a href='javascript:void(0);' class='greyed'>{$subsubsubvalue['name']}</a>";
                                                }
                                                else
                                                {
                                                    echo "<a href='{$subsubsubvalue['url']}'>{$subsubsubvalue['name']}</a>";
                                                }
                                                echo "</li>\n";
                                            }
                                        }
                                        echo "</ul>\n";
                                    }
                                    echo "</li>\n";
                                }
                            }
                            echo "</ul>\n";
                        }
                        echo "</li>\n";
                    }
                }
               echo "</ul>\n";
            }
            echo "</li>\n";
        }
    }
    echo "</ul>\n\n";

    echo "<div id='topsearch'>";
    echo "<form name='jumptoincident' action='search.php' method='get'>";
    echo "<input type='text' name='q' id='searchfield' size='20' value='{$strIncidentNumOrSearchTerm}'
    onblur=\"if(this.value == '') { this.style.color='#888;'; this.value='{$strIncidentNumOrSearchTerm}';}\" onfocus=\"if(this.value == '{$strIncidentNumOrSearchTerm}') { this.style.color='#000;'; this.value=''; }\"
    onclick='clearjumpto()'/> ";
    // echo "<input type='image' src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/find.png' alt='{$strGo}' onclick='jumpto()' />";
    echo "</form>";
    echo "</div>";
    echo "</div>\n";
}

if (!isset($refresh))
{
    //update last seen (only if this is a page that does not auto-refresh)
    $lastseensql = "UPDATE LOW_PRIORITY `{$GLOBALS['dbUsers']}` SET lastseen=NOW() WHERE id='{$_SESSION['userid']}' LIMIT 1";
    mysql_query($lastseensql);
    if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
}



//dismiss any notices
$noticeaction = cleanvar($_REQUEST['noticeaction']);
$noticeid = cleanvar($_REQUEST['noticeid']);

if ($noticeaction == 'dismiss_notice')
{
    if (is_numeric($noticeid))
    {
        $sql = "DELETE FROM `{$GLOBALS['dbNotices']}` WHERE id={$noticeid} AND userid={$sit[2]}";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    }
    elseif ($noticeid == 'all')
    {
        $sql = "DELETE FROM `{$GLOBALS['dbNotices']}` WHERE userid={$sit[2]} LIMIT 20"; // only delete 20 max as we only show 20 max
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    }
}
if ($sit[0] != '')
{
    // Check users email address
    if (empty($_SESSION['email']) OR !preg_match('/^[a-z0-9_\+-]+(\.[a-z0-9_\+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4})$/',$_SESSION['email']))
    {
        echo "<p class='error'>{$strInvalidEmailAddress} - <a href='edit_profile.php'>{$strEditEmail}</a></p>";
    }


    //display global notices
    $noticesql = "SELECT * FROM `${GLOBALS['dbNotices']}` ";
    // Don't show more than 20 notices, saftey cap
    $noticesql .= "WHERE userid={$sit[2]} ORDER BY timestamp DESC LIMIT 20";
    $noticeresult = mysql_query($noticesql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($noticeresult) > 0)
    {
        $keys = array_keys($_GET);

        foreach ($keys AS $key)
        {
            if ($key != 'noticeid')
            {
                $url .= "&amp;{$key}=".$_GET[$key];
            }
        }

        while ($notice = mysql_fetch_object($noticeresult))
        {
            $notice->text = bbcode($notice->text);
            //check for the notice types
            if ($notice->type == WARNING_NOTICE_TYPE)
            {
                $class = 'warning';
            }
            elseif ($notice->type == CRITICAL_NOTICE_TYPE)
            {
                echo "<div class='error'><p class='error'>";
                echo $notice->text;

                if($notice->resolutionpage)
                {
                    $redirpage = $CONFIG['application_webpath'].$notice->resolutionpage;
                }
            }
            else
            {
                $class = 'info';
            }

            echo "<div class='{$class}'><p class='info'>";
            echo "<span>(<a href='{$_SERVER[PHP_SELF]}?noticeaction=dismiss_notice&amp;noticeid={$notice->id}{$url}'>{$strDismiss}</a>)</span>";
            if (substr($notice->text, 0, 4) == '$str')
            {
                $v = substr($notice->text, 1);
                echo $GLOBALS[$v];
            }
            else
            {
                echo $notice->text;
            }

            if (!empty($notice->link))
            {
                echo " - <a href='{$notice->link}'>";
                if (substr($notice->linktext, 0, 4)=='$str')
                {
                    $v = substr($notice->linktext, 1);
                    echo $GLOBALS[$v];
                }
                else
                {
                    echo $notice->linktext;
                }
                echo "</a>";
            }

        echo "<sub>";
        echo "<em> ".format_date_friendly(strtotime($notice->timestamp))."</em>";
        echo "</sub></p></div>";
        }

        if (mysql_num_rows($noticeresult) > 1)
        {
            //fix the GET keys to stop breaking urls
            $keys = array_keys($_GET);

            $file = $_SERVER[PHP_SELF];
            $end = "noticeaction=dismiss_notice&amp;noticeid=all";

            foreach ($keys AS $key)
            {
                if ($key != 'sit' AND $key != 'SiTsessionID')
                {
                    //$url[]= "{$key}=".$_REQUEST[$key];
                    $link .= $key."=".$_REQUEST[$key]."&amp;";
                }
            }
            $alink = $file."?".$link.$end;
            echo "<p id='dismissall'><a href='{$alink}'>{$strDismissAll}</a></p>";
        }
    }
}
$headerdisplayed = TRUE; // Set a variable so we can check to see if the header was included
echo "<div id='mainframe'>";
?>
