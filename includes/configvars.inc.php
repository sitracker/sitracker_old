<?php
// configvars.inc.php - List of SiT configuration variables
//                      and functions to manage them
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas, <ivanlucas[at]users.sourceforge.net

$CFGCAT['application'] = array('application_fspath',
                               'application_webpath',
                               'application_name',
                               'demo',
                               'debug');

$CFGCAT['locale'] = array('home_country',
                          'timezone',
                          'dateformat_datetime');


$CFGCAT['ftp'] = array('ftp_hostname', 'ftp_username', 'ftp_password', 'ftp_pasv', 'ftp_path');

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



// TODO, this code was ripped out of setup.php, need to make setup.php use this
// INL 28Nov08
function cfgVarInput($setupvar)
{
    global $CONFIG, $CFGVAR;
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

    return $html;
}










?>