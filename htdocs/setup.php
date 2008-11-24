<?php
// setup.php - Install/Upgrade and set up plugins
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
// Load config defaults
@include ("defaults.inc.php");
// Keep the defaults as a seperate array
$DEFAULTS = $CONFIG;

// Load config file with customisations
// @include ("config.inc-dist.php");
@include ("config.inc.php");
// Server Configuration
@include ('/etc/webtrack.conf'); // for legacy systems
@include ('/etc/sit.conf');

// Some actions require authentication
if ($_REQUEST['action'] == 'reconfigure')
{
    $permission = 22;
    $_REQUEST['config'] = 'advanced'; // set advanced mode
    require ('functions.inc.php');
    require ('auth.inc.php');
}

// These are the required variables we want to configure during installation
$SETUP = array('db_hostname','db_database','db_username','db_password','db_tableprefix','application_fspath','application_webpath', 'attachment_fspath', 'attachment_webpath', 'enable_inbound_mail', 'email_username', 'email_password', 'email_address', 'email_server', 'email_servertype', 'email_options', 'email_port');
$CFGVAR['email_username']['title'] = "Incoming email account username";
$CFGVAR['email_password']['title'] = "Incoming email account password";
$CFGVAR['email_address']['title'] = "Incoming email account address";
$CFGVAR['email_server']['title'] = "Incoming email account server URL";
$CFGVAR['email_servertype']['title'] = "Incoming email account server type";
$CFGVAR['email_servertype']['type'] = 'select';
$CFGVAR['email_servertype']['options'] = 'imap|pop';
// Descriptions of all the config variables
$CFGVAR['db_hostname']['title'] = 'MySQL Database Hostname';
$CFGVAR['db_hostname']['help']="The Hostname or IP address of the MySQL Database Server, usually 'localhost'";
$CFGVAR['db_username']['title'] = 'MySQL Database Username';
$CFGVAR['db_password']['title'] = 'MySQL Database Password';
$CFGVAR['db_database']['title'] = 'MySQL Database Name';
$CFGVAR['db_tableprefix']['title'] = 'MySQL Database Table Prefix';
$CFGVAR['db_tableprefix']['help']="Prefix database tables with the a string (e.g. 'sit_', use this if the database you are using is shared with other applications";
$CFGVAR['application_fspath']['title'] = 'Filesystem Path';
$CFGVAR['application_fspath']['help']="The full absolute filesystem path to the SiT! directory with trailing slash. e.g. '/var/www/sit/'";
$CFGVAR['application_webpath']['title'] = 'The path to SiT! from the browsers perspective with a trailing slash. e.g. /sit/';
$CFGVAR['application_name']['title'] = 'The application name';
$CFGVAR['application_name']['help'] = 'You should not normally need to change this';
$CFGVAR['application_shortname']['title'] = 'A short version of the application name';
$CFGVAR['application_shortname']['help'] = 'You should not normally need to change this';
$CFGVAR['application_uriprefix']['title'] = 'URI Prefix';
$CFGVAR['application_uriprefix']['help'] = 'The URI prefix to use when referring to this application (in emails etc.) e.g. http://{$_SERVER[\'HTTP_HOST\']}';
$CFGVAR['home_country']['title'] = "The default country in capitals. e.g. 'UNITED KINGDOM'";
$CFGVAR['support_email']['title'] = 'Emails sent by SiT will come from this address';
$CFGVAR['sales_email']['title'] = 'Your sales departments email address';
$CFGVAR['support_manager_email']['title'] = 'The email address of the person in charge of your support service';
$CFGVAR['bugtracker_name']['title'] = 'Bug tracker name';
$CFGVAR['bugtracker_url']['title'] = 'Bug tracker url';
$CFGVAR['timezone']['title'] = 'Timezone';
$CFGVAR['hide_closed_incidents_older_than']['title'] = 'Hide closed incidents older than';
$CFGVAR['hide_closed_incidents_older_than']['help'] = "Incidents closed more than this number of days ago aren't show in the incident queue, -1 means disabled";
$CFGVAR['dateformat_datetime']['title'] = 'Date and Time format';
$CFGVAR['dateformat_datetime']['help'] = "See <a href='http://www.php.net/manual/en/function.date.php'>http://www.php.net/manual/en/function.date.php</a> for help with date formats";
$CFGVAR['dateformat_filedatetime']['title'] = 'Date and Time format to use for files';
$CFGVAR['dateformat_shortdate']['title'] = 'Short date format';
$CFGVAR['dateformat_shorttime']['title'] = 'Short time format';
$CFGVAR['dateformat_date']['title'] = 'Normal date format';
$CFGVAR['dateformat_time']['title'] = 'Normal time format';
$CFGVAR['dateformat_longdate']['title'] = 'Long date format';
$CFGVAR['dateformat_longdate']['help'] = 'Including the day of the week';
$CFGVAR['working_days']['title'] = 'Array containing working days (0=Sun, 1=Mon ... 6=Sat)';
$CFGVAR['start_working_day']['title'] = 'Time of the start of the working day (in seconds)';
$CFGVAR['start_working_day']['help'] = 'Seconds since midnight';
$CFGVAR['end_working_day']['title'] = 'Time of the end of the working day (in seconds)';
$CFGVAR['end_working_day']['help'] = 'Seconds since midnight';
$CFGVAR['attachment_fspath']['title'] = "The full absolute file system path to the attachments directory (with a trailing slash)";
$CFGVAR['attachment_fspath']['help'] = "This directory should be writable";
$CFGVAR['attachment_webpath']['title'] = "The path to the attachments directory from the browsers perspective";
$CFGVAR['mailin_spool_path']['title'] = "Incoming mail spool directory, the location of mail processed by mailfilter shell script";
$CFGVAR['upload_max_filesize']['title'] = "The maximum file upload size (in bytes)";
$CFGVAR['ftp_hostname']['title'] = 'The ftp hostname or IP address';
$CFGVAR['ftp_username']['title'] = 'Ftp username';
$CFGVAR['ftp_password']['title'] = 'Ftp password';
$CFGVAR['ftp_pasv']['title'] = 'Set to TRUE to enable ftp PASSV mode or FALSE to disable';
$CFGVAR['ftp_pasv']['type'] = 'select';
$CFGVAR['ftp_pasv']['options'] = 'TRUE|FALSE';
$CFGVAR['ftp_path']['title'] = 'The path to the directory where we store files on the ftp server';
$CFGVAR['ftp_path']['help'] = '(e.g. /pub/support/) the trailing slash is important';
$CFGVAR['enable_spellchecker']['title'] = 'Set to TRUE to enable spellchecking or FALSE to disable';
$CFGVAR['enable_spellchecker']['type'] = 'select';
$CFGVAR['enable_spellchecker']['options'] = 'TRUE|FALSE';
$CFGVAR['main_dictionary_file']['title'] = 'Spell check main dictionary file';
$CFGVAR['custom_dictionary_file']['title'] = 'Spell check custom words dictionary file';
$CFGVAR['default_css_url']['title'] = 'The CSS file to use when no other is configured';
$CFGVAR['default_interface_style']['title'] = 'The interface style that new users should use (user default style)';
$CFGVAR['kb_id_prefix']['title'] = 'Knowledgebase ID prefix';
$CFGVAR['kb_id_prefix']['help'] = 'inserted before the ID to give it uniqueness';
$CFGVAR['kb_disclaimer_html']['title'] = 'Knowledgebase disclaimer, displayed at the bottom of every article';
$CFGVAR['kb_disclaimer_html']['help']  = 'Simple HTML is allowed';
$CFGVAR['default_service_level']['title'] = 'The service level to use in case the contact does not specify (text not the tag)';
$CFGVAR['regular_contact_days']['title'] = 'The number of days to elapse before we are prompted to contact the customer (usually overridden by SLA)';
$CFGVAR['free_support_limit']['title'] = 'Number of free (site) support incidents that can be logged to a site';
$CFGVAR['incident_pools']['title'] = 'Comma seperated list specifying the numbers of incidents to assign to contracts';
$CFGVAR['feedback_form']['title'] = 'Incident feedback form (the id number of the feedback form to use or empty to disable sending feedback forms out)';
$CFGVAR['trusted_server']['title'] = 'Enable trusted server mode';
$CFGVAR['trusted_server']['help'] = 'If you set this to TRUE, passwords will no longer be used or required, this assumes that you are using another mechanism for authentication';
$CFGVAR['trusted_server']['type'] = 'select';
$CFGVAR['trusted_server']['options'] = 'TRUE|FALSE';
$CFGVAR['record_lock_delay']['title'] = 'Lock records for (number of seconds)';
$CFGVAR['max_incoming_email_perday']['title'] = 'maximum no. of incoming emails per incident before a mail-loop is detected';
$CFGVAR['spam_email_subject']['title'] = 'String to look for in email message subject to determine a message is spam';
$CFGVAR['spam_forward']['title'] = 'Email address to forward spam messages that are to be marked as spam';
$CFGVAR['feedback_max_score']['title'] = 'The max score to use in rating fields for feedback forms';
$CFGVAR['changelogfile']['title'] = 'Path to the Changelog file';
$CFGVAR['licensefile']['title'] = 'Path to the License file';
$CFGVAR['creditsfile']['title'] = 'Path to the Credits file';
$CFGVAR['session_name']['title'] = 'The session name for use in cookies and URLs, Must contain alphanumeric characters only';
$CFGVAR['notice_threshold']['title'] = 'Flag items as notice when they are this percentage complete.';
$CFGVAR['notice_threshold']['help'] = 'Enter a number between 0 and 100.';
$CFGVAR['notice_threshold']['type'] = 'percent';
$CFGVAR['urgent_threshold']['title'] = 'Flag items as urgent when they are this percentage complete.';
$CFGVAR['urgent_threshold']['help'] = 'Enter a number between 0 and 100.';
$CFGVAR['urgent_threshold']['type'] = 'percent';
$CFGVAR['critical_threshold']['title'] = 'flag items as critical when they are this percentage complete.';
$CFGVAR['critical_threshold']['help'] = 'Enter a number between 0 and 100.';
$CFGVAR['critical_threshold']['type'] = 'percent';
$CFGVAR['demo']['title'] = 'Demo Mode';
$CFGVAR['demo']['help'] = 'Set to TRUE to run in demo mode, some features are disabled or replaced with mock-ups';
$CFGVAR['demo']['type'] = 'select';
$CFGVAR['demo']['options'] = 'TRUE|FALSE';
$CFGVAR['debug']['title'] = 'Debug Mode';
$CFGVAR['debug']['help'] = 'Set to TRUE to output extra debug information, some as HTML comments and some in the page footer, FALSE to disable';
$CFGVAR['debug']['type'] = 'select';
$CFGVAR['debug']['options'] = 'TRUE|FALSE';
$CFGVAR['portal']['title'] = 'Enable user portal';
$CFGVAR['portal']['type'] = 'select';
$CFGVAR['portal']['options'] = 'TRUE|FALSE';
$CFGVAR['journal_loglevel']['title'] = 'Journal Logging Level';
$CFGVAR['journal_loglevel']['help'] = '0 = none, 1 = minimal, 2 = normal, 3 = full, 4 = maximum/debug';
$CFGVAR['journal_purge_after']['title'] = 'How long should we keep journal entries (in seconds), entries older than this will be purged (deleted)';
$CFGVAR['logout_url']['title'] = "The URL to redirect the user too after he/she logs out";
$CFGVAR['logout_url']['help'] = "When left blank this defaults to \$CONFIG['application_webpath'], setting that here will take the value of the default";
$CFGVAR['error_logfile']['title'] = "Path to a file to log error messages";
$CFGVAR['error_logfile']['help'] = "This file must be writable of course";
$CFGVAR['access_logfile']['title'] = 'Filename to log authentication failures';
$CFGVAR['access_logfile']['help'] = "This file must be writable of course";
$CFGVAR['plugins']['title'] = "An array of plugin names";
$CFGVAR['plugins']['help'] = "e.g. 'array('magic_plugin', 'lookup_plugin')'";
$CFGVAR['error_notavailable_url']['title']="The URL to redirect too for pages that do not exist yet.";
$CFGVAR['tag_icons']['title'] = "An array of tags and associated icons";
$CFGVAR['tag_icons']['help'] = "Set up an array to use an icon for specified tags, format: array('tag' => 'icon', 'tag2' => 'icon2')";
$CFGVAR['no_feedback_contracts']['title'] = "An array of contracts to not request feedback for";
$CFGVAR['no_feedback_contracts']['help'] = "eg. array(123, 765) would withhold feedback requests for contract 123 and 765";
$CFGVAR['preferred_maintenance']['title'] = "An array of SLA's to indicate order of preference when logging incidents against them";
$CFGVAR['preferred_maintenance']['help'] = "e.g. array('standard', 'high')";
$CFGVAR['default_i18n']['title'] = "Default Language";
$CFGVAR['default_i18n']['help'] = "The system language, or the language that will be used when no other language is selected by the user, see <a href='http://sitracker.sourceforge.net/Translation'>http://sitracker.sourceforge.net/Translation</a> for a list of supported languages. ";
$CFGVAR['timezone']['title'] = "System Time Zone";
$CFGVAR['timezone']['help'] = "See <a href='http://www.php.net/timezones'>http://www.php.net/timezones</a> for a list of supported Timezones";
$CFGVAR['kb_enabled']['title'] = "Knowledge base Enabled/Disabled";
$CFGVAR['kb_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['kb_enabled']['type'] = 'select';
$CFGVAR['kb_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['portal_kb_enabled']['title'] = "Portal Knowledge base Enabled/Disabled";
$CFGVAR['portal_kb_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['portal_kb_enabled']['type'] = 'select';
$CFGVAR['portal_kb_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['tasks_enabled']['title'] = "Tasks Enabled/Disabled";
$CFGVAR['tasks_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['tasks_enabled']['type'] = 'select';
$CFGVAR['tasks_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['calendar_enabled']['title'] = "Calendar Enabled/Disabled";
$CFGVAR['calendar_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['calendar_enabled']['type'] = 'select';
$CFGVAR['calendar_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['holidays_enabled']['title'] = "Holidays Enabled/Disabled";
$CFGVAR['holidays_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['holidays_enabled']['type'] = 'select';
$CFGVAR['holidays_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['feedback_enabled']['title'] = "Feedback Enabled/Disabled";
$CFGVAR['feedback_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['feedback_enabled']['type'] = 'select';
$CFGVAR['feedback_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['timesheets_enabled']['title'] = "Timesheets Enabled/Disabled";
$CFGVAR['timesheets_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['timesheets_enabled']['type'] = 'select';
$CFGVAR['timesheets_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['portal_site_incidents']['title'] = "Show site incidents in portal";
$CFGVAR['portal_site_incidents']['help'] = "Users in the portal can view site incidents based on the contract options";
$CFGVAR['portal_site_incidents']['type'] = 'select';
$CFGVAR['portal_site_incidents']['options'] = 'TRUE|FALSE';
$CFGVAR['portal_usernames_can_be_changed']['title'] = "Allow portal users to change usernames";
$CFGVAR['portal_usernames_can_be_changed']['type'] = 'select';
$CFGVAR['portal_usernames_can_be_changed']['options'] = 'TRUE|FALSE';
$CFGVAR['portal_interface_style']['title'] = "Portal interface style";
$CFGVAR['auto_assign_incidents']['title'] = "Auto-assign incidents";
$CFGVAR['auto_assign_incidents']['type'] = 'select';
$CFGVAR['auto_assign_incidents']['options'] = 'TRUE|FALSE';
$CFGVAR['auto_assign_incidents']['help'] = "incidents are automatically assigned based on a lottery weighted towards who are less busy, assumes everyone set to accepting is an engineer and willing to take incidents";
$CFGVAR['default_roleid']['title'] = "Default role id";
$CFGVAR['default_roleid']['help'] = "Role given to new users by default";
$CFGVAR['default_gravatar']['title'] = "Default Gravatar";
$CFGVAR['default_gravatar']['help'] = "can be 'wavatar', 'identicon', 'monsterid' a URL to an image, or blank for a blue G. see <a href='http://www.gravatar.com/'>www.gravatar.com</a> to learn about gravatars";
$CFGVAR['enable_inbound_mail']['title'] = "Enable incoming mail to SiT";
$CFGVAR['enable_inbound_mail']['help'] = "Normal users should choose 'POP/IMAP' and fill in the details below', advanced users can choose to pipe straight to SiT from their MTA, please read the docs for help on this.";
$CFGVAR['enable_inbound_mail']['type'] = 'select';
$CFGVAR['enable_inbound_mail']['options'] = "POP/IMAP|MTA|disabled";
$CFGVAR['email_username']['title'] = "Incoming email account username";
$CFGVAR['email_username']['help'] = "Only fill in this and the following options if you have selected 'POP/IMAP email retrieval";
$CFGVAR['email_password']['title'] = "Incoming email account password";
$CFGVAR['email_address']['title'] = "Incoming email account address";
$CFGVAR['email_server']['title'] = "Incoming email account server URL";
$CFGVAR['email_servertype']['title'] = "Incoming email account server type";
$CFGVAR['email_servertype']['type'] = 'select';
$CFGVAR['email_servertype']['options'] = 'imap|pop';
// e.g. Gmail needs '/ssl', secure Groupwise needs /novalidate-cert etc.
// see http://uk2.php.net/imap_open for examples
$CFGVAR['email_options']['title'] = "Extra options to pass to the mailbox";
$CFGVAR['email_options']['help'] = "e.g. Gmail needs '/ssl', secure Groupwise needs /novalidate-cert etc. See http://uk2.php.net/imap_open for examples";
$CFGVAR['email_port']['title'] = "Incoming email account port";


$upgradeok = FALSE;
$config_filename='../includes/config.inc.php';

$configfiles = get_included_files();

function filterconfigfiles($var)
{
    $poss_config_files = array('config.inc.php', 'sit.conf', 'webtrack.conf');
    $recognised = FALSE;
    foreach ($poss_config_files AS $poss)
    {
        if (substr($var, strlen($var)-strlen($poss)) == $poss) $recognised = TRUE;
    }
    return $recognised;
}
$configfiles = array_filter($configfiles, 'filterconfigfiles');
$configfiles = array_values($configfiles);
$numconfigfiles = count($configfiles);
if ($numconfigfiles == 1) $config_filename = $configfiles[0];
elseif ($numconfigfiles < 1) $configfiles[] = '../includes/config.inc.php';

$cfg_file_exists = FALSE;
$cfg_file_writable = FALSE;
foreach ($configfiles AS $conf_filename)
{
    if (file_exists($conf_filename)) $cfg_file_exists = TRUE;
    if (is_writable($conf_filename)) $cfg_file_writable = TRUE;
}


// Detect whether an array is associative
// From http://uk.php.net/manual/en/function.is-array.php#77744
function is_assoc($array)
{
    return is_array($array) && count($array) !== array_reduce(array_keys($array), 'is_assoc_callback', 0);
}


function is_assoc_callback($a, $b)
{
    return $a === $b ? $a + 1 : 0;
}

function setup_configure()
{
    global $SETUP, $CFGVAR, $CONFIG, $configfiles, $config_filename, $cfg_file_exists, $cfg_file_writable, $numconfigfiles;
    $html = '';

    if ($cfg_file_exists)
    {
        if ($numconfigfiles < 2)
        {
            $html .= "<h2>Found an existing config file <var>{$config_filename}</var></h2>";
        }
        else
        {
            $html .= "<p class='error'>Found more than one existing config file</p>";
            if ($cfg_file_writable)
            {
                $html .= "<ul>";
                foreach ($configfiles AS $conf_filename)
                {
                    $html .= "<li><var>{$conf_filename}</var></li>";
                }
                $html .= "</ul>";
            }
        }
        //$html .= "<p>Since you already have a config file we assume you are upgrading or reconfiguring, if this is not the case please delete the existing config file.</p>";
        if ($cfg_file_writable)
        {
            $html .= "<p class='warning'>Important: The file permissions on the configuration file allow it to be modified, we recommend you make this file read-only once SiT! is configured.</p>";
        }
        else
        {
            $html .= "<p class='error'>A config file already exists but it is not writable</p>";
            $html .= "<p>For security we won't show your existing settings here unless the configuration file is writable.</p>";
        }
    }
    else $html .= "<h2>New Configuration</h2><p>Please complete this form to create a new configuration file for SiT!</p>";
    $html .= "\n<form action='setup.php' method='post'>\n";

    if ($_REQUEST['config'] == 'advanced')
    {
        $html .= "<input type='hidden' name='config' value='advanced' />\n";
        foreach ($CFGVAR AS $setupvar => $setupval)
        {
            $SETUP[] = $setupvar;
        }
    }

    $c=1;
    foreach ($SETUP AS $setupvar)
    {
        $html .= "<div class='configvar{$c}'>";
        if ($CFGVAR[$setupvar]['title']!='') $title = $CFGVAR[$setupvar]['title'];
        else $title = $setupvar;
        $html .= "<h4>{$title}</h4>";
        if ($CFGVAR[$setupvar]['help']!='') $html .= "<p class='helptip'>{$CFGVAR[$setupvar]['help']}</p>\n";

        $html .= "<var>\$CONFIG['$setupvar']</var> = ";

        $value = '';
        if (!$cfg_file_exists OR ($cfg_file_exists AND $cfg_file_writable))
        {
            $value = $CONFIG[$setupvar];
            if (is_bool($value))
            {
                if ($value==TRUE) $value='TRUE';
                else $value='FALSE';
            }
            elseif (is_array($value))
            {
                if (is_assoc($value))
                {
                    $value = "array(".implode_assoc('=>',',',$value).")";
                }
                else
                {
                    $value="array(".implode(',',$value).")";
                }
            }
            if ($setupvar=='db_password' AND $_REQUEST['action']!='reconfigure') $value='';
        }
        switch ($CFGVAR[$setupvar]['type'])
        {
            case 'select':
                $html .= "<select name='$setupvar'>";
                if (empty($CFGVAR[$setupvar]['options'])) $CFGVAR[$setupvar]['options'] = "TRUE|FALSE";
                $options = explode('|', $CFGVAR[$setupvar]['options']);
                foreach ($options AS $option)
                {
                    $html .= "<option value=\"{$option}\"";
                    if ($option == $value) $html .= " selected='selected'";
                    $html .= ">{$option}</option>\n";
                }
                $html .= "</select>";
            break;

            case 'percent':
                $html .= "<select name='$setupvar'>";
                for($i = 0; $i <= 100; $i++)
                {
                    $html .= "<option value=\"{$i}\"";
                    if ($i == $value) $html .= " selected='selected'";
                    $html .= ">{$i}</option>\n";
                }
                $html .= "</select>";
            break;

            case 'text':
            default:
                if (strlen($CONFIG[$setupvar]) < 65)
                {
                    $html .= "<input type='text' name='$setupvar' size='60' value=\"{$value}\" />";
                }
                else
                {
                    $html .= "<textarea name='$setupvar' cols='60' rows='10'>{$value}</textarea>";
                }
        }
        if ($setupvar=='db_password' AND $_REQUEST['action']!='reconfigure' AND $value != '') $html .= "<p class='info'>The current password setting is not shown</p>";
        $html .= "</div>";
        $html .= "<br />\n";
        if ($c==1) $c==2; else $c=1;
    }
    $html .= "<input type='hidden' name='action' value='save_config' />";
    $html .= "<br /><input type='submit' name='submit' value='Save Configuration' />";
    $html .= "</form>\n";
    return $html;
}


function setup_exec_sql($sqlquerylist)
{
    global $CONFIG, $dbSystem, $installed_schema, $application_version;
    if (!empty($sqlquerylist))
    {
        if (!is_array($sqlquerylist)) $sqlquerylist = array($sqlquerylist);

//         echo "<pre>".print_r($sqlquerylist,true)."</pre>";

        // Loop around the queries
        foreach ($sqlquerylist AS $schemaversion => $queryelement)
        {
            if ($schemaversion != '0') $schemaversion = substr($schemaversion, 1);
//             echo "<p>Schema version $schemaversion, installed schema $installed_schema, query $queryelement</p>";
            if ($schemaversion == 0 OR $installed_schema < $schemaversion)
            {
                $sqlqueries = explode( ';', $queryelement);
                // We don't need the last entry it's blank, as we end with a ;
                array_pop($sqlqueries);
                $errors = 0;
                foreach ($sqlqueries AS $sql)
                {
                    //$html .= "<p style='border: 1px solid red;'>&bull; <code>".nl2br($sql)."</code></p>\n"; // FIXME
                    if (!empty($sql))
                    {
                        mysql_query($sql);
                        if (mysql_error())
                        {
                            $errno = mysql_errno();
                            $errstr = '';
                            // See http://dev.mysql.com/doc/refman/5.0/en/error-messages-server.html
                            // For list of mysql error numbers
                            switch ($errno)
                            {
                                case 1022:
                                case 1050:
                                case 1060:
                                case 1061:
                                case 1062:
                                    $severity = 'info';
                                    $errstr = "This could be because this part of the database schema is already up to date.";
                                break;

                                case 1058:
                                    $severity = 'error';
                                    $errstr = "This looks suspiciously like a bug, if you think this is the case please report it.";
                                break;

//                                 case 1054:
//                                     if (preg_match("/ALTER TABLE/", $sql) >= 1)
//                                     {
//                                         $severity = 'info';
//                                         $errstr = "This could be because this part of the database schema is already up to date.";
//                                     }
//                                 break;

                                case 1051:
                                case 1091:
                                    if (preg_match("/DROP/", $sql) >= 1)
                                    {
                                        $severity = 'info';
                                        $errstr = "We expected to find something in order to remove it but it doesn't exist. This could be because this part of the database schema is already up to date..";
                                    }
                                break;



                                case 1044:
                                case 1045:
                                case 1142:
                                case 1143:
                                case 1227:
                                    $severity = 'error';
                                    $errstr = "This could be because the MySQL user '{$CONFIG['db_username']}' does not have appropriate permission to modify the database schema.<br />";
                                    $errstr .= "<strong>Check your MySQL permissions allow the schema to be modified</strong>.";

                                default:
                                    $severity = 'error';
                                    $errstr = "You may have found a bug, if you think this is the case please report it.";
                            }
                            $html .= "<p class='$severity'>";
                            if ($severity == 'info')
                            {
                                $html .= "<strong>Information:</strong>";
                            }
                            else
                            {
                                $html .= "<strong>A MySQL error occurred:</strong>";
                                $errors ++;
                            }
                            $html .= " [".mysql_errno()."] ".mysql_error()."<br />";
                            if (!empty($errstr)) $html .= $errstr."<br />";
                            $html .= "Raw SQL: <code class='small'>".htmlspecialchars($sql)."</code>";
                        }
                    }
                }
            }
        }
    }
    echo $html;
    return $errors;
}

// Returns TRUE if an admin account exists, or false if not
function setup_check_adminuser()
{
    global $dbUsers;
    $sql = "SELECT id FROM `{$dbUsers}` WHERE id=1 OR username='admin' OR roleid='1'";
    $result = @mysql_query($sql);
    if (mysql_num_rows($result) >= 1) return TRUE;
    else FALSE;
}


session_name($CONFIG['session_name']);
session_start();

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
echo " \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\" />\n";
echo "<style type=\"text/css\">\n";
echo "body { background-color: #FFF; font-family: Tahoma, Helvetica, sans-serif; font-size: 10pt;}\n";
echo "h1,h2,h3,h4,h5 { background-color: #203894; padding: 0.1em; border: 1px solid #203894; color: #FFF; }\n";
echo "h4 {background-color: transparent; color: #000; border: 0px; margin: 2px 0px 3px 0px; }\n";
echo "div.configvar1 {background-color: #F7FAFF; border: 1px solid black; margin-bottom: 10px; padding: 0px 5px 10px 5px;} ";
echo "div.configvar2 {background-color: green; border: 1px solid black; margin-bottom: 10px;} ";
echo ".error {background-position: 3px 2px;
  background-repeat: no-repeat;
  padding: 3px 3px 3px 22px;
  min-height: 16px;
  -moz-border-radius: 5px;
  /* display: inline; */
  border: 1px solid #000;
  margin-left: 2em;
  margin-right: 2em;
  width: auto;
  text-align: left;
    background-image: url('images/icons/sit/16x16/warning.png');
  color: #5A3612;
  border: 1px solid #A26120;
  background-color: #FFECD7;
}

.info {
 background-position: 3px 2px;
  background-repeat: no-repeat;
  padding: 3px 3px 3px 22px;
  min-height: 16px;
  -moz-border-radius: 5px;
  /* display: inline; */
  border: 1px solid #000;
  margin-left: 2em;
  margin-right: 2em;
  width: auto;
  text-align: left;
}
p.info {
  background-image: url('images/icons/sit/16x16/info.png');
  color: #17446B;
  border: 1px solid #17446B;
  background-color: #F4F6FF;
}

a.button:link, a.button:visited
{
  float: left;
  margin: 2px 5px 2px 5px;
  padding: 2px;
  width: 100px;
  border-top: 1px solid #ccc;
  border-bottom: 1px solid black;
  border-left: 1px solid #ccc;
  border-right: 1px solid black;
  background: #ccc;
  text-align: center;
  text-decoration: none;
  font: normal 10px Verdana;
  color: black;
}

a.button:hover
{
  background: #eee;
}

a.button:active
{
  border-bottom: 1px solid #eee;
  border-top: 1px solid black;
  border-right: 1px solid #eee;
  border-left: 1px solid black;
}

var { font-family: Andale Mono, monospace; font-style: normal; }
code.small { font-size: 75%; color: #555; }
}

";
echo ".help {background: #F7FAFF; border: 1px solid #3165CD; color: #203894; padding: 2px;}\n";
echo ".helptip { color: #203894; }\n";
echo ".warning {background: #FFFFE6; border: 2px solid #FFFF31; color: red; padding: 2px;}\n";
echo "pre {background:#FFF; border:#999; padding: 1em;}\n";
echo "a.button { border: 1px outset #000; padding: 2px; background-color: #EFEFEF;} ";
echo "a:link,a:visited { color: #000099; }\n";
echo "a:hover { background: #99CCFF; }\n";
echo "hr { background-color: #203894; margin-top: 3em; }\n";
echo "</style>\n";
echo "<title>Support Incident Tracker Setup</title>\n";
echo "</head>\n<body>\n";

echo "<h1>Support Incident Tracker - Installation &amp; Setup</h1>";
$include_path = ini_get('include_path');

//
// Pre-flight Checks
//

if (!empty($_REQUEST['msg']))
{
    $msg = strip_tags(base64_decode(urldecode($_REQUEST['msg'])));
    echo "<p class='error'><strong>Configuration Problem</strong>: {$msg}</p>";
    echo "<p class='info'>You have been redirected to this setup page because a serious configuration problem has prevented SiT from running. ";
    echo "Correct the issue reported and try running SiT again.</p>";
}



// Check that includes worked and that we have some config variables set, these two should always be set
if ($CONFIG['application_name'] == '' AND $CONFIG['application_shortname'] == '')
{
    echo "<p class='error'>We couldn't find configuration defaults, this probably means your include_path is wrong. ";
    echo "Your current include path is <code>{$include_path}</code><br />";
    echo "SiT! Requires its libraries to be in the include path which is specified in your php.ini file, modify your php.ini and set the new include path ";
    if (file_exists('../includes'))
    {
        $curdir = getcwd();
        $include_path .= ":".substr($curdir,0,strrpos($curdir,'/'))."/includes";
        // $include_path .= ":../includes";
        echo "to be <code>$include_path</code></p>";
    }
    else echo "to point to the includes directory";
    echo "</p>";
}

// Check we have the mysql extension
if (!extension_loaded('mysql'))
{
    echo "<p class='error'>Error: Could not find the mysql extension, SiT! requires MySQL to be able to run, you should install and enable the MySQL PHP Extension then run setup again.</p>";
}

if (version_compare(PHP_VERSION, "5.0.0", "<"))
{
    echo "<p class='error'>You are running an older PHP version (< PHP 5), SiT v3.35 and later require PHP 5.0.0 or newer, some features may not work properly.</p>";
}

switch ($_REQUEST['action'])
{
    case 'save_config':
        $newcfgfile = "<";
        $newcfgfile .= "?php\n";
        $newcfgfile .= "# config.inc.php - SiT! Config file generated automatically by setup.php on ".date('r')."\n\n";

        if ($_REQUEST['config'] == 'advanced')
        {
            foreach ($CFGVAR AS $setupvar => $setupval)
            {
                $SETUP[] = $setupvar;
            }
        }

        // Keep the posted setup
        foreach ($SETUP AS $setupvar)
        {
            if ($_POST[$setupvar]==='TRUE') $_POST[$setupvar] = TRUE;
            if ($_POST[$setupvar]==='FALSE') $_POST[$setupvar] = FALSE;
            $CONFIG[$setupvar]=$_POST[$setupvar];
        }

        // Extract the differences between the defaults and the newly configured items
        $CFGDIFF = array_diff_assoc($CONFIG, $DEFAULTS);

        if (count($CFGDIFF) > 0)
        {
            foreach ($CFGDIFF AS $setupvar => $setupval)
            {
                if ($CFGVAR[$setupvar]['title'] != '')
                {
                    $newcfgfile .= "# {$CFGVAR[$setupvar]['title']}\n";
                }

                if ($CFGVAR[$setupvar]['help']!='')
                {
                    $newcfgfile .= "# {$CFGVAR[$setupvar]['help']}\n";
                }

                $newcfgfile .= "\$CONFIG['$setupvar'] = ";

                if (is_numeric($setupval))
                {
                    $newcfgfile .= "{$setupval}";
                }
                elseif (is_bool($setupval))
                {
                    $newcfgfile .= $setupval == TRUE ? "TRUE" : "FALSE";
                }
                elseif (substr($setupval, 0, 6)=='array(')
                {
                    $newcfgfile .= stripslashes("{$setupval}");
                }
                else
                {
                    $newcfgfile .= "'{$setupval}'";
                }
                $newcfgfile .= ";\n\n";
            }
        }
        else
        {
            $newcfgfile .= "# Nothing configured. This will mean the defaults are used.\n\n";
        }
        $newcfgfile .= "?";
        $newcfgfile .= ">";

        $fp = @fopen($config_filename, 'w');
        if (!$fp)
        {
            echo "<p class='error'>Could not write {$config_filename}</p>";
            echo "<p class='help'>Copy this text and paste it into a <var>config.inc.php</var> file in the includes directory or <var>sit.conf</var> in the <var>/etc</var> directory<br />";
            echo "Or change the permissions on the file so that it is writable and refresh this page to try again (if you do this remember to make it ";
            echo "read-only again afterwards)</p>";
            echo "<div style='margin-left: 5%; margin-right: 5%; background-color: white; padding: 1em; border: 1px dashed #ccc; '>";
            highlight_string($newcfgfile);
            echo "</div>";
            echo "<p>After creating your <var>{$config_filename}</var> file click the 'Next' button below.</p>";
        }
        else
        {
            echo "<p>Writing to {$config_filename}</p>";
            fwrite($fp, $newcfgfile);
            fclose($fp);
            echo "<p>Config file modified</p>";
            if (!@chmod($config_filename, 0640))
            {
                echo "<p class='warning'>Important: The file permissions on the file <var>{$config_filename}</var> allow the file to be modified, we recommend you now make this file read-only.</p>";
            }
        }
        echo "<p><a href='setup.php' class='button'>Next</a></p>";
    break;

    case 'reconfigure':
        echo "<h2>Reconfigure</h2>";
        echo "<p>With this page administrators can set every available config file setting.  Please take care or you may break your SiT! installation.</p>";
        echo setup_configure();
    break;


    default:
        require ('tablenames.inc.php');
        // Connect to Database server
        $db = @mysql_connect($CONFIG['db_hostname'], $CONFIG['db_username'], $CONFIG['db_password']);
        if (@mysql_error())
        {
            echo setup_configure();
        }
        else
        {
            // Connected to database
            // Select database
            mysql_select_db($CONFIG['db_database'], $db);
            if (mysql_error())
            {
            	if (!empty($CONFIG['db_username']) AND !empty($CONFIG['db_password']))
            	{
	                echo "<p class='error'>".mysql_error()."<br />Could not select database";
	                if ($CONFIG['db_database']!='')
	                {
	                    echo " '{$CONFIG['db_database']}', check the database name,";
	                }
	                else
	                {
	                    echo ", the database name was not configured, please set the <code>\$CONFIG['db_database'] config variable";
	                    $CONFIG['db_database'] = 'sit';
	                }
	                echo "</p>";
            	}
                $sql = "CREATE DATABASE `{$CONFIG['db_database']}` DEFAULT CHARSET utf8";
                if ($_REQUEST['action'] == 'createdatabase')
                {
                    echo "<h2>Creating database...</h2>";
                    $result = mysql_query($sql);
                    if ($result)
                    {
                        echo "<p><strong>OK</strong> Database '{$CONFIG['db_database']}' created.</p>";
                        echo "<p><a href='setup.php' class='button'>Next</a></p>";
                    }
                    else
                    {
                        echo "<p class='error'>".mysql_error()."<br />The database could not be created automatically, ";
                        echo "you can create it manually by executing the SQL statement <br /><code>{$sql};</code></p>";
                    }
                }
                else
                {
                    echo "<p class='help'>If this is the first time you have used SiT! you may need to create the database, ";
                    echo "if you have the necessary MySQL permissions you can create the database automatically.<br />";
                    echo "Alternatively you can create it manually by executing the SQL statement <br /><code>{$sql};</code></p";
                    echo "<p><a href='setup.php?action=createdatabase' class='button'>Create Database</a></p>";
                }
                //echo "<p>After creating the database run <a href='setup.php' class='button'>setup</a> again to create the database schema</p>";
                if (empty($CONFIG['db_database']) OR empty($CONFIG['db_username']))
                {
                    echo "<p>You may need to make configuration changes in order for SiT to be able access MySQL.</p>";
                    echo setup_configure();
                }
            }
            else
            {
                require ('functions.inc.php');

                // Load the empty schema
                require ('setup-schema.php');

                // Connected to database and db selected
                echo "<p>Connected to database - ok</p>";
                // Check to see if we're already installed
                $sql = "SHOW TABLES LIKE '{$dbUsers}'";
                $result = mysql_query($sql);
                if (mysql_error())
                {
                    echo "<p class='error'>Could not find a users table, an error occurred ".mysql_error()."</p>";
                    exit;
                }
                if (mysql_num_rows($result) < 1)
                {
                    echo "<h2>Creating new database schema...</h2>";
                    // No users table or empty users table, proceed to install
//                     $installed_schema = 0;
//                     $installed_schema = substr(end(array_keys($upgrade_schema[$application_version*100])),1);
                    $errors = setup_exec_sql($schema);
                    // Update the system version
                    if ($errors < 1)
                    {
                        $vsql = "REPLACE INTO `{$dbSystem}` ( `id`, `version`) VALUES (0, $application_version)";
                        mysql_query($vsql);
                        if (mysql_error())
                        {
                            $html .= "<p class='error'>Could not store new schema version number '{$application_version}'. ".mysql_error()."</p>";
                        }
                        else
                        {
                            $html .= "<p>Schema successfully created as version {$application_version}</p>";
                        }
                    }
                    else
                    {
                        $html .= "<p class='error'><strong>Summary</strong>: {$errors} Error(s) occurred while creating the schema, ";
                        $html .= "please resolve the problems reported and then try running setup again.</p>";
                    }
                    echo $html;
/*                    // Set the system version number
                    $sql = "REPLACE INTO `{$dbSystem}` ( id, version) VALUES (0, $application_version)";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);*/
                    $installed_version = $application_version;
                    echo "<h2>Database schema created</h2>";
                    echo "<p>If no errors were reported above you should continue and check the installation.</p>";
                    echo "<p>If there are errors, please log a bug <a href='http://sitracker.sourceforge.net/Bugs'>here</a>";
                    echo ", with the full error message.</p>";
                    echo "<p><a href='setup.php?action=checkinstallcomplete' class='button'>Next</a></p>";
                }
                else
                {
                    // users table exists and has at least one record, must be already installed
                    // Do upgrade

                    // Have a look what version is installed
                    // First look to see if the system table exists
                    $exists = mysql_query("SELECT 1 FROM `{$dbSystem}` LIMIT 0");
                    if (!$exists)
                    {
                        echo "<p class='error'>Could not find a 'system' table which probably means you have a version prior to v3.21</p>";
                        $installed_version = 3.00;
                    }
                    else
                    {
                        $sql = "SELECT `version` FROM `{$dbSystem}` WHERE id = 0";
                        $result = mysql_query($sql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                        list($installed_version) = mysql_fetch_row($result);

/*                        if ($installed_version >= 3.35)
                        {
                            $sql = "SELECT `schemaversion` FROM `{$dbSystem}` WHERE id = 0";
                            $result = mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                            list($installed_schema) = mysql_fetch_row($result);
                        }
                        else
                        {
                            $installed_schema = 334;
                            $sql = "SHOW COLUMNS FROM `{$dbSystem}` WHERE Field='schema'";
                            $result = mysql_query($sql);
                            if (mysql_num_rows($result) < 1)
                            {
                                $sql = "ALTER TABLE `{$dbSystem}` ADD `schemaversion` BIGINT UNSIGNED NOT NULL COMMENT 'DateTime in YYYYMMDDHHMM format'";
                                $result = mysql_query($sql);
                                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                            }
                        }*/
                    }

                    if (empty($installed_version))
                    {
                        die ("<p class='error'>Fatal setup error - Could not determine version of installed software.  Try wiping your installation and installing from clean. (sorry)</p>");
                    }

                    echo "<h2>Installed OK</h2>";

                    if ($_REQUEST['action'] == 'upgrade')
                    {
                        /*****************************
                         * Do pre-upgrade tasks here *
                         *****************************/

                        if ($installed_version < 3.35)
                        {
                            //Get anyone with var_notify_on_reassign on so we can add them a trigger later
                            $sql = "SELECT * FROM `{$dbUsers}` WHERE var_notify_on_reassign='true'";
                            if ($result = @mysql_query($sql))
                            {
                                while ($row = mysql_fetch_object($result))
                                {
                                    $assign_notify_users[] = $row->id;
                                }
                            }

                            //any kbarticles with private content, change whole type
                            $sql = "SELECT docid, distribution FROM `{$dbKBContent} WHERE distribution!='public'";
                            if ($result = @mysql_query($sql))
                            {
                                while ($row = @mysql_fetch_object($result))
                                {
                                    if ($row->distribution == 'private')
                                    {
                                        $kbprivate[] = $row->docid;
                                    }
                                    elseif (!in_array($row->docid, $kbprivate))
                                    {
                                        $kbrestricted[] = $row->docid;
                                    }
                                }
                            }
                        }

                        // Upgrade schema
                        for ($v=(($installed_version*100)+1); $v<=($application_version*100); $v++)
                        {
                            $html = '';
                            if (!empty($upgrade_schema[$v]))
                            {
                                $newversion = number_format(($v/100),2);
                                echo "<p>Updating schema from {$installed_version} to v{$newversion}&hellip;</p>";
                                $errors = setup_exec_sql($upgrade_schema[$v]);
                                // Update the system version
                                if ($errors < 1)
                                {
                                    $vsql = "REPLACE INTO `{$dbSystem}` ( `id`, `version`) VALUES (0, $newversion)";
                                    mysql_query($vsql);
                                    if (mysql_error())
                                    {
                                        $html .= "<p class='error'>Could not store new schema version number '{$newversion}'. ".mysql_error()."</p>";
                                    }
                                    else
                                    {
                                        $html .= "<p>Schema successfully updated to version {$newversion}.</p>";
                                    }
                                    $installed_version = $newversion;
                                    $upgradeok = TRUE;
                                }
                                else
                                {
                                    $html .= "<p class='error'><strong>Summary</strong>: {$errors} Error(s) occurred while updating the schema, ";
                                    $html .= "please resolve the problems reported and then try running setup again.</p>";
                                }
                                echo $html;
                            }
                        }

                        // Other special tasks
                        if ($installed_version < 3.21)
                        {
                            echo "<p>Upgrading incidents data from version prior to 3.21...</p>";
                            // Fill the new servicelevel field in the incidents table using information from the maintenance contract
                            echo "<p>Upgrading incidents table to store service level...</p>";
                            $sql = "SELECT *,i.id AS incidentid FROM `{$dbIncidents}` AS i, `{$dbMaintenance}` AS m, {$dbServiceLevels}` AS sl WHERE i.maintenanceid=m.id AND ";
                            $sql .= "m.servicelevelid = sl.id ";
                            $result = mysql_query($sql);
                            while ($row = mysql_fetch_object($result))
                            {
                                $sql = "UPDATE `{$dbIncidents}` SET servicelevel='{$row->tag}' WHERE id='{$row->incidentid}' AND servicelevel IS NULL LIMIT 1";
                                mysql_query($sql);
                                if (mysql_error())
                                {
                                    trigger_error(mysql_error(),E_USER_WARNING);
                                    echo "<p><strong>FAILED:</strong> $sql</p>";
                                    $upgradeok = FALSE;
                                }
                                else echo "<p><strong>OK:</strong> $sql</p>";
                            }
                            echo "<p>".mysql_num_rows($result)." incidents upgraded</p>";
                        }

                        if ($installed_version < 3.35)
                        {
                            if ($CONFIG['closure_delay'] > 0 AND $CONFIG['closure_delay'] != 554400)
                            {
                                echo "<p>Inserting value from deprecated config variable <var>closure_delay</var> into scheduler</p>";
                                $sql = "UPDATE `{$dbScheduler}` SET params = '{$CONFIG['closure_delay']}' WHERE action = 'CloseIncidents' LIMIT 1";
                                mysql_query($sql);
                                if (mysql_error())
                                {
                                    trigger_error(mysql_error(),E_USER_WARNING);
                                    echo "<p><strong>FAILED:</strong> $sql</p>";
                                    $upgradeok = FALSE;
                                }
                                else echo "<p><strong>OK:</strong> $sql</p>";
                            }

                            //add trigger to users, NOTE we do user 1(admin's) in the schema
                            $sql = "SELECT id FROM `{$dbUsers}` WHERE id > 1";
                            if ($result = @mysql_query($sql))
                            {
                                echo '<p>Adding default triggers to existing users.</p>';
                                while ($row = mysql_fetch_row($result))
                                {
                                    setup_user_triggers($row->id);
                                }
                            }

                            //add the triggers for var_notify users from above
                            if (is_array($assign_notify_users))
                            {
                                echo '<p>Replacing "Notify on reassign" option with triggers</p>';
                                foreach ($assign_notify_users as $assign_user)
                                {
                                    $sql = "INSERT INTO `{$dbTriggers}`(triggerid, userid, action, template, checks) ";
                                    $sql .= "VALUES('TRIGGER_INCIDENT_ASSIGNED', '$assign_user', 'ACTION_EMAIL', 'EMAIL_INCIDENT_REASSIGNED_USER_NOTIFY', '{userstatus} ==  {$assign_user}')";
                                    mysql_query($sql);
                                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                                }
                            }

                            //fix the visibility for KB articles
                            if (is_array($kbprivate))
                            {
                                foreach ($kbprivate as $article)
                                {
                                    $articleID = intval($article);
                                    $sql = "UPDATE `{$dbKBArticles}` SET visibility='private' WHERE id='{$articleID}'";
                                    mysql_query($sql);
                                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                                }
                            }

                            if (is_array($kbrestricted))
                            {
                                foreach ($kbrestricted as $article)
                                {
                                    $articleID = intval($article);
                                    $sql = "UPDATE `{$dbKBArticles}` SET visibility='restricted' WHERE id='{$articleID}'";
                                    mysql_query($sql);
                                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                                }
                            }
                        }

                        if ($installed_version < 3.40)
                        {
                        	//remove any brackets from checks as per mantis 197
                        	$sql = "UPDATE `triggers` SET `checks` = REPLACE(`checks`, '(', ''); ";
                        	$sql .= "UPDATE `triggers` SET `checks` = REPLACE(`checks`, ')', '')";
                        	mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                        }

                        if ($installed_version == $application_version)
                        {
                            echo "<p>Everything is up to date</p>";
                            echo "<p>See the <code>doc/UPGRADE</code> file for further upgrade notes.<br />";
                        }
                        else
                        {
                            $upgradeok = TRUE;
                            echo "<p>See the <code>doc/UPGRADE</code> file for further upgrade instructions and help.<br />";

                        }

                        if ($installed_version >= 3.24)
                        {
                            //upgrade dashboard components.

                            $sql = "SELECT * FROM `{$dbDashboard}` WHERE enabled = 'true'";
                            $result = mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

                            //echo "<h2>Dashboard</h2>";
                            while ($dashboardnames = mysql_fetch_object($result))
                            {
                                $version = 1;
                                include ("{$CONFIG['application_fspath']}dashboard/dashboard_{$dashboardnames->name}.php");
                                $func = "dashboard_{$dashboardnames->name}_get_version";

                                if (function_exists($func))
                                {
                                    $version = $func();
                                }

                                if ($version > $dashboardnames->version)
                                {
                                    echo "<p>Upgrading {$dashboardnames->name}</p>>";
                                    // apply all upgrades since running version
                                    $upgrade_func = "dashboard_{$dashboardnames->name}_upgrade";

                                    if (function_exists($upgrade_func))
                                    {
                                        $dashboard_schema = $func();
                                        for ($i = $dashboardnames->version; $i <= $version; $i++)
                                        {
                                            setup_exec_sql($dashboard_schema[$i]);
                                        }

                                        $upgrade_sql = "UPDATE `{$dbDashboard}` SET version = '{$version}' WHERE id = {$dashboardnames->id}";
                                        mysql_query($upgrade_sql);
                                        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

                                        echo "<p>{$dashboardnames->name} upgraded</p>";
                                    }
                                    else
                                    {
                                        echo "<p>No upgrade function for {$dashboardnames->name}</p>";
                                    }
                                }
                            }
                        }

                        if ($upgradeok)
                        {
                            // Update the system version number
                            $sql = "REPLACE INTO `{$dbSystem}` ( id, version) VALUES (0, $application_version)";
                            mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                            $installed_version = $application_version;
                            echo "<h2>Upgrade complete</h2>";
                            echo "<p>Upgraded to v{$application_version}</p>";

                            trigger("TRIGGER_SIT_UPGRADED", array('applicationversion' => $application_version));
                        }
                        else
                        {
                            echo "<p class='error'>Upgrade failed.  Maybe you could try a fresh installation?</p>";
                        }
                    }
                    else
                    {
//                         $latest_schema = substr(end(array_keys($upgrade_schema[$application_version*100])),1);
                        echo "<p>Your database schema is v".number_format($installed_version,2);
//                          . "-{$installed_schema}";
                        //if ($installed_schema < $latest_schema)
//                         echo ", the latest available schema is v".number_format($installed_version,2) . "-{$latest_schema}";
                        if ($installed_version < $application_version) echo ", after making a backup you should upgrade your schema to v{$application_version}";
                        echo "</p>";

                        if (is_array($upgrade_schema[$installed_version*100]))
                        {
                            foreach ($upgrade_schema[$installed_version*100] AS $possible_schema_updates => $nothing)
                            {
                                $possible_schema_updates = substr($possible_schema_updates, 1);
                                if ($possible_schema_updates > $installed_schema) $schemaupgradeneeded = TRUE;
                                else $schemaupgradeneeded = FALSE;
                            }
                        }
                        if ($installed_version < $application_version OR $schemaupgradeneeded == TRUE)
                        {
                            echo "<p><a href='setup.php?action=upgrade' class='button'>Upgrade Schema</a></p><br />";
                        }
                    }

                    if ($_REQUEST['action'] == 'createadminuser' AND setup_check_adminuser() == FALSE)
                    {
                        $password = mysql_real_escape_string($_POST['newpassword']);
                        $passwordagain = mysql_real_escape_string($_POST['passwordagain']);
                        if ($password == $passwordagain)
                        {
                            $password = md5($password);
                            $email = mysql_real_escape_string($_POST['email']);
                            $sql = "INSERT INTO `{$dbUsers}` (`id`, `username`, `password`, `realname`, `roleid`, `title`, `signature`, `email`, `status`, `var_style`, `lastseen`) ";
                            $sql .= "VALUES (1, 'admin', '$password', 'Administrator', 1, 'Administrator', 'Regards,\r\n\r\nSiT Administrator', '$email', '1', '8', NOW());";
                            mysql_query($sql);
                            if (mysql_error())
                            {
                               trigger_error(mysql_error(),E_USER_WARNING);
                               echo "<p><strong>FAILED:</strong> $sql</p>";
                            }
                        }
                        else
                        {
                            echo "<p class='error'>Admin account not created, the passwords you entered did not match.</p>";
                        }
                    }
                    // Check installation
                    echo "<h2>Checking installation...</h2>";
                    if ($cfg_file_writable)
                    {
                        echo "<p class='warning'>Important: The file permissions on the configuration file <var>{$config_filename}</var> file allow it to be modified, we recommend you make this file read-only.</p>";
                    }

                    if ($CONFIG['attachment_fspath'] == '')
                    {
                        echo "<p class='error'>Attachment path must not be empty, please set the \$CONFIG['attachment_fspath'] configuration variable</p>";
                    }
                    elseif (file_exists($CONFIG['attachment_fspath']) == FALSE)
                    {
                        echo "<p class='error'>The attachment path that you have configured ({$CONFIG['attachment_fspath']}) does not exist, please create this directory or alter the \$CONFIG['attachment_fspath'] configuration variable to point to a directory that does exist.</p>";
                        echo "<p>After fixing this problem, you must run <a href='{$_SERVER['PHP_SELF']}?checkinstallcomplete' class='button'>setup</a> again to create an admin account.</p>";
                    }
                    elseif (is_writable($CONFIG['attachment_fspath']) == FALSE)
                    {
                        echo "<p class='error'>Attachment path '{$CONFIG['attachment_fspath']}' not writable<br />";
                        echo "Permissions:  <code>{$CONFIG['attachment_fspath']} ".file_permissions_info(fileperms($CONFIG['attachment_fspath']));
                        echo " (".substr(sprintf('%o', fileperms($CONFIG['attachment_fspath'])), -4).")</code><br />";
                        echo "If installing on Linux you can run the following command at the console (or set other appropriate permissions to allow write access)<br /><br />";
                        echo "<code>chmod -R 777 {$CONFIG['attachment_fspath']}</code>";
                        echo "</p>";
                        echo "<p>After fixing this, please re-run <a href='{$_SERVER[PHP_SELF]}?checkinstallcomplete' class='button'>setup</a> to create an admin account.</p>";
                    }
                    elseif (!isset($_REQUEST))
                    {
                        echo "<p class='error'>SiT! requires PHP 5.0.0 or later</p>";
                    }
                    elseif (@ini_get('register_globals')==1 OR strtolower(@ini_get('register_globals'))=='on')
                    {
                        echo "<p class='error'>SiT! strongly recommends that you change your php.ini setting <code>register_globals</code> to OFF.</p>";
                    }
                    elseif (setup_check_adminuser() == FALSE)
                    {
                        echo "<p><span style='color: red; font-weight: bolder;'>Important:</span> you <strong>must</strong> create an admin account before you can use SiT</p>";
                        echo "<form action='setup.php' method='post'>\n";
                        echo "<p>Username:<br /><input type='text' name='username' value='admin' disabled='disabled' /> (cannot be changed)</p>";
                        echo "<p><label>Password:<br /><input type='password' name='newpassword' size='30' maxlength='50' /></label></p>";
                        echo "<p><label>Confirm Password:<br /><input type='password' name='passwordagain' size='30' maxlength='50' /></label></p>";
                        echo "<p><label>Email:<br /><input type='text' name='email' size='30' maxlength='255' /></label></p>";
                        echo "<p><input type='submit' value='Create Admin User' />";
                        echo "<input type='hidden' name='action' value='createadminuser' />";
                        echo "</form>";
                    }
                    else
                    {
                        echo "<p>SiT! v".number_format($installed_version,2)." is installed and ready.<br /><br /> <a href='index.php' class='button'>Run SiT!</a></p>";
                        if ($_SESSION['userid'] == 1)
                        {
                            echo "<br /><p>As administrator you can <a href='{$_SERVER['PHP_SELF']}?action=reconfigure'>reconfigure</a> SiT!</p>";
                        }
                    }
                }
            }
        }
}
echo "<hr />";
echo "<p><a href='http://sourceforge.net/projects/sitracker'>{$CONFIG['application_name']}</a> Setup</p>";
echo "<p></p>";

echo "\n</body>\n</html>";
?>
