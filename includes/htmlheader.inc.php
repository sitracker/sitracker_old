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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<!-- SiT (Support Incident Tracker) - Support call tracking system
     Copyright (C) 2000-2007 Salford Software Ltd. and Contributors

     This software may be used and distributed according to the terms
     of the GNU General Public License, incorporated herein by reference. -->
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<?php
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

if($sit[0] != '')
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

    if ($CONFIG['debug'])
    {
        echo "<!--";
        print_r($_SESSION['permissions']);
        echo "-->";
    }
    echo "<div id='menu'>\n";
    echo "<ul id='menuList'>\n";
    foreach ($hmenu[0] as $top => $topvalue)
    {
        echo "<li class='menuitem'>";
        // Permission Required: ".permission_name($topvalue['perm'])."
        if ($topvalue['perm'] >=1 AND !in_array($topvalue['perm'], $_SESSION['permissions'])) echo "<a href='javascript:void();' class='greyed'>{$topvalue['name']}</a>";
        else echo "<a href='{$topvalue['url']}'>{$topvalue['name']}</a>";
        // Do we need a submenu?
        if ($topvalue['submenu'] > 0 AND in_array($topvalue['perm'], $_SESSION['permissions']))
        {
            echo "\n<ul>"; //  id='menuSub'
            foreach ($hmenu[$topvalue['submenu']] as $sub => $subvalue)
            {
                if ($subvalue['submenu'] > 0) echo "<li class='submenu'>";
                else echo "<li>";
                if ($subvalue['perm'] >=1 AND !in_array($subvalue['perm'], $_SESSION['permissions'])) echo "<a href='javascript:void();' class='greyed'>{$subvalue['name']}</a>";
                else echo "<a href='{$subvalue['url']}'>{$subvalue['name']}</a>";
                if ($subvalue['submenu'] > 0 AND in_array($subvalue['perm'], $_SESSION['permissions']))
                {
                    echo "<ul>"; // id ='menuSubSub'
                    foreach ($hmenu[$subvalue['submenu']] as $subsub => $subsubvalue)
                    {
                        if ($subsubvalue['submenu'] > 0) echo "<li class='submenu'>";
                        else echo "<li>";
                        if ($subsubvalue['perm'] >=1 AND !in_array($subsubvalue['perm'], $_SESSION['permissions'])) echo "<a href=\"javascript:void();\" class='greyed'>{$subsubvalue['name']}</a>";
                        else echo "<a href='{$subsubvalue['url']}'>{$subsubvalue['name']}</a>";
                        if ($subsubvalue['submenu'] > 0 AND in_array($subsubvalue['perm'], $_SESSION['permissions']))
                        {
                            echo "<ul>"; // id ='menuSubSubSub'
                            foreach ($hmenu[$subsubvalue['submenu']] as $subsubsub => $subsubsubvalue)
                            {
                                if ($subsubsubvalue['submenu'] > 0) echo "<li class='submenu'>";
                                else echo "<li>";
                                if ($subsubsubvalue['perm'] >=1 AND !in_array($subsubsubvalue['perm'], $_SESSION['permissions'])) echo "<a href='javascript:void();' class='greyed'>{$subsubsubvalue['name']}</a>";
                                else echo "<a href='{$subsubsubvalue['url']}'>{$subsubsubvalue['name']}</a>";
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
//dismiss any notices
$action = cleanvar($_REQUEST['action']);
$noticeid = cleanvar($_REQUEST['noticeid']);

if($action AND $noticeid)
{
    $sql = "UPDATE usernotices SET dismissed=1 WHERE noticeid={$noticeid} AND userid={$sit[2]}";
    @mysql_query($sql);
}

//display global notices
if($sit[0] != '')
{
    $sql = "SELECT * FROM notices, usernotices ";
    $sql .= "WHERE userid={$sit[2]} AND dismissed!=1 AND notices.id=usernotices.noticeid";
    $result = mysql_query($sql);
    while($notice = @mysql_fetch_object($result))
    {
        //check for the two warning types, TODO: make this better
        //if($notice->text == '$strFirstLogin') $notice->text = $strFirstLogin;
        $notice->text = str_replace('$strFirstLogin', $strFirstLogin, $notice->text);
        $notice->text = str_replace('$strNoEmailSet', $strNoEmailSet, $notice->text);
        
        //critical error
        if($notice->type == '1') 
        {
            echo "<div class='error'><p class='error'>{$notice->text}";
            if($notice->resolutionpage) $redirpage = $CONFIG['application_webpath'].$notice->resolutionpage;
        }
        else 
        {
            echo "<div class='info'><p class='info'>{$notice->text}";
            echo " (<a href='{$_SERVER[PHP_SELF]}?action=dismiss&noticeid={$notice->id}'>$strDismiss</a>)";
        }
        echo "</p></div>";
    }
    if($redirpage && ($_SERVER[SCRIPT_NAME] != $redirpage))
    {
        confirmation_page(2, $redirpage, '<h2>Error</h2><p align="center">You are being redirected to fix an error.</p>');
        exit;
    }
}
echo "<div id='mainframe'>";
?>
