<?php
// html.inc.php - functions that return generic HTML elements, e.g. input boxes
//                or convert plain text to HTML ...
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

/**
    * Generate HTML for a redirect/confirmation page
    * @author Ivan Lucas
    * @param string $url. URL to redirect to
    * @param bool $success. TRUE = Success, FALSE = Failure
    * @param string $message. HTML message to display on the page before redirection
    * @returns string HTML page with redirect
    * @note Replaces confirmation_page() from versions prior to 3.35
    * @note If a header HTML has already been displayed a continue link is printed
    * @note a meta redirect will also be inserted, which is invalid HTML but appears
    * @note to work in most browswers.
    * @note The recommended way to use this function is to call it without headers/footers
    * @note already displayed.
*/
function html_redirect($url, $success = TRUE, $message='')
{
    global $CONFIG, $headerdisplayed;

    if (!empty($_REQUEST['dashboard']))
    {
        $headerdisplayed = TRUE;
    }

    if (empty($message))
    {
        $refreshtime = 1;
    }
    elseif ($success == FALSE)
    {
        $refreshtime = 3;
    }
    else
    {
        $refreshtime = 6;
    }

    $refresh = "{$refreshtime}; url={$url}";

    $title = $GLOBALS['strPleaseWaitRedirect'];
    if (!$headerdisplayed)
    {
        include ('inc/htmlheader.inc.php');
    }
    else
    {
        echo "<meta http-equiv=\"refresh\" content=\"$refreshtime; url=$url\" />\n";
    }

    echo "<h3>";
    if ($success)
    {
        echo "<span class='success'>{$GLOBALS['strSuccess']}</span>";
    }
    else
    {
        echo "<span class='failure'>{$GLOBALS['strFailed']}</span>";
    }

    if (!empty($message))
    {
        echo ": {$message}";
    }

    echo "</h3>";
    if (empty($_REQUEST['dashboard']))
    {
        echo "<h4>{$GLOBALS['strPleaseWaitRedirect']}</h4>";
        if ($headerdisplayed)
        {
            echo "<p align='center'><a href=\"{$url}\">{$GLOBALS['strContinue']}</a></p>";
        }
    }
    // TODO 3.35 Add a link to refresh the dashlet if this is run inside a dashlet

    if ($headerdisplayed)
    {
        include ('inc/htmlfooter.inc.php');
    }
}


/**
    * Prints the HTML for a checkbox, the 'state' value should be a 1, yes, true or 0, no, false
    * @author Ivan Lucas
    * @param string $name The HTML name attribute
    * @param mixed $state
    * @param bool $return. Return HTML as a string when TRUE, echo when FALSE
    * @returns string HTML
*/
function html_checkbox($name, $state, $return = FALSE)
{
    if ($state === TRUE) $state = 'TRUE';
    if ($state === FALSE) $state = 'FALSE';
    if ($state === 1 OR $state === 'Yes' OR $state === 'yes' OR
        $state === 'true' OR $state === 'TRUE')
    {
        $html = "<input type='checkbox' checked='checked' name='{$name}' id='{$name}' value='{$state}' />" ;
    }
    else
    {
        $html = "<input type='checkbox' name='{$name}' id='{$name}' value='{$state}' />" ;
    }

    if ($return)
    {
        return $html;
    }
    else
    {
        echo $html;
    }
}


/**
 * Returns HTML for a gravatar (Globally recognised avatar)
 * @author Ivan Lucas
 * @param string $email - Email address
 * @param int $size - Size in pixels (Default 32)
 * @param bool $hyperlink - Make a link back to gravatar.com, default TRUE
 * @returns string - HTML img tag
 */
function gravatar($email, $size = 32, $hyperlink = TRUE)
{
    global $CONFIG, $iconset;
    $default = $CONFIG['default_gravatar'];

    if (isset( $_SERVER['HTTPS']) && (strtolower( $_SERVER['HTTPS'] ) != 'off' ))
    {
        // Secure
        $grav_url = "https://secure.gravatar.com";
    }
    else
    {
        $grav_url = "http://www.gravatar.com";
    }
    $grav_url .= "/avatar.php?";
    $grav_url .= "gravatar_id=".md5(strtolower($email));
    $grav_url .= "&default=".urlencode($CONFIG['default_gravatar']);
    $grav_url .= "&size=".$size;
    $grav_url .= "&rating=G";

    if ($hyperlink) $html = "<a href='http://site.gravatar.com/'>";
    $html .= "<img src='{$grav_url}' width='{$size}' height='{$size}' alt='' />";
    if ($hyperlink) $html .= "</a>";

    return $html;
}


/**
    * Produces HTML for a percentage indicator
    * @author Ivan Lucas
    * @param int $percent. Number between 0 and 100
    * @returns string HTML
*/
function percent_bar($percent)
{
    if ($percent == '') $percent = 0;
    if ($percent < 0) $percent = 0;
    if ($percent > 100) $percent = 100;
    // #B4D6B4;
    $html = "<div class='percentcontainer'>";
    $html .= "<div class='percentbar' style='width: {$percent}%;'>  {$percent}&#037;";
    $html .= "</div></div>\n";
    return $html;
}


// Return HTML for a table column header (th and /th) with links for sorting
// Filter parameter can be an assocative array containing fieldnames and values
// to pass on the url for data filtering purposes
function colheader($colname, $coltitle, $sort = FALSE, $order='', $filter='', $defaultorder='a', $width='')
{
    global $CONFIG;
    if ($width !=  '')
    {
        $html = "<th width='".intval($width)."%'>";
    }
    else
    {
        $html = "<th>";
    }

    $qsappend='';
    if (!empty($filter) AND is_array($filter))
    {
        foreach ($filter AS $key => $var)
        {
            if ($var != '') $qsappend .= "&amp;{$key}=".urlencode($var);
        }
    }
    else
    {
        $qsappend='';
    }

    if ($sort==$colname)
    {
        //if ($order=='') $order=$defaultorder;
        if ($order=='a')
        {
            $html .= "<a href='{$_SERVER['PHP_SELF']}?sort=$colname&amp;order=d{$qsappend}'>{$coltitle}</a> ";
            $html .= "<img src='{$CONFIG['application_webpath']}images/sort_a.png' width='5' height='5' alt='{$GLOBALS['SortAscending']}' /> ";
        }
        else
        {
            $html .= "<a href='{$_SERVER['PHP_SELF']}?sort=$colname&amp;order=a{$qsappend}'>{$coltitle}</a> ";
            $html .= "<img src='{$CONFIG['application_webpath']}images/sort_d.png' width='5' height='5' alt='{$GLOBALS['SortDescending']}' /> ";
        }
    }
    else
    {
        if ($sort === FALSE) $html .= "{$coltitle}";
        else $html .= "<a href='{$_SERVER['PHP_SELF']}?sort=$colname&amp;order={$defaultorder}{$qsappend}'>{$coltitle}</a> ";
    }
    $html .= "</th>";
    return $html;
}


/**
    * Takes an array and makes an HTML selection box
    * @author Ivan Lucas
*/
function array_drop_down($array, $name, $setting='', $enablefield='', $usekey = '')
{
    $html = "<select name='$name' id='$name' $enablefield>\n";

    if ($usekey == '')
    {
        if ((array_key_exists($setting, $array) AND
            in_array((string)$setting, $array) == FALSE) OR
            $usekey == TRUE)
        {
            $usekey = TRUE;
        }
        else
        {
            $usekey = FALSE;
        }
    }

    foreach ($array AS $key => $value)
    {
        $value = htmlentities($value, ENT_COMPAT, $GLOBALS['i18ncharset']);
        if ($usekey)
        {
            $html .= "<option value='$key'";
            if ($key == $setting)
            {
                $html .= " selected='selected'";
            }

        }
        else
        {
            $html .= "<option value='$value'";
            if ($value == $setting)
            {
                $html .= " selected='selected'";
            }
        }

        $html .= ">{$value}</option>\n";
    }
    $html .= "</select>\n";
    return $html;
}


?>
