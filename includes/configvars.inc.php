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


// TODO we need some sort of hierarchy for the config categories
// as this is way too many

$CFGTAB['application'] = array('appmain', 'theming', 'system', 'other');
$CFGTAB['email'] = array('inboundemail', 'outboundemail');
$CFGTAB['features'] = array('incidents', 'portal', 'ftp', 'kb', 'sla', 'holidays', 'feedback');
$CFGTAB['system'] = array('paths', 'locale', 'journal');

$CFGCAT['paths'] = array('application_fspath',
                         'application_webpath',
                         'access_logfile',
                         'attachment_fspath',
                         'attachment_webpath');


$CFGCAT['appmain'] = array('application_name',
                               'application_shortname',
                               'application_uriprefix',
                               'logout_url',
                               'plugins'
                               );


$CFGCAT['system'] = array('demo', 'debug', 'bugtracker_name', 'bugtracker_url',
                          'changelogfile','creditsfile',
                          'error_logfile',
                          'error_notavailable_url',
                          'licensefile',
                          'session_name');

$CFGCAT['locale'] = array('home_country',
                          'timezone',
                          'dateformat_datetime',
                          'dateformat_date',
                          'dateformat_filedatetime',
                          'dateformat_longdate',
                          'dateformat_shortdate',
                          'dateformat_shorttime',
                          'dateformat_time',
                          'default_i18n');

$CFGCAT['sla'] = array('end_working_day',
                       'notice_threshold',
                       'regular_contact_days');


$CFGCAT['theming'] = array('default_interface_style', 'default_css_url', 'default_gravatar');

$CFGCAT['ftp'] = array('ftp_hostname', 'ftp_username', 'ftp_password', 'ftp_pasv', 'ftp_path');

$CFGCAT['portal'] = array('portal',
                          'portal_kb_enabled',
                          'portal_site_incidents',
                          'portal_usernames_can_be_changed',
                          'portal_creates_incidents',
                          'portal_interface_style');

$CFGCAT['holidays'] = array('holidays_enabled');

$CFGCAT['incidents'] = array('auto_assign_incidents',
                             'critical_threshold',
                             'free_support_limit',
                             'hide_closed_incidents_older_than',
                             'incident_pools',
                             'preferred_maintenance',
                             'record_lock_delay');


$CFGCAT['inboundemail'] = array('enable_inbound_mail',
                         'email_address',
                         'email_options',
                         'email_password',
                         'email_port',
                         'email_server',
                         'email_servertype',
                         'email_username',
                         'mailin_spool_path',
                         'max_incoming_email_perday',
                         'spam_email_subject'
                         );

$CFGCAT['feedback'] = array('feedback_enabled',
                            'feedback_form',
                            'feedback_max_score',
                            'no_feedback_contracts');

$CFGCAT['kb'] = array('kb_enabled',
                      'kb_disclaimer_html',
                      'kb_id_prefix');

$CFGCAT['outboundemail'] = array('sales_email');
$CFGCAT['journal'] = array('journal_loglevel', 'journal_purge_after');

$CFGCAT['other'] = array('main_dictionary_file', 'enable_spellchecker','custom_dictionary_file');




// Descriptions of all the config variables
$CFGVAR['access_logfile']['help'] = "This file must be writable of course";
$CFGVAR['access_logfile']['title'] = 'Filename to log authentication failures';
$CFGVAR['application_fspath']['help']="The full absolute filesystem path to the SiT! directory with trailing slash. e.g. '/var/www/sit/'";
$CFGVAR['application_fspath']['title'] = 'Filesystem Path';
$CFGVAR['application_name']['help'] = 'You should not normally need to change this';
$CFGVAR['application_name']['title'] = 'The application name';
$CFGVAR['application_shortname']['help'] = 'You should not normally need to change this';
$CFGVAR['application_shortname']['title'] = 'A short version of the application name';
$CFGVAR['application_uriprefix']['help'] = 'The URI prefix to use when referring to this application (in emails etc.) e.g. http://{$_SERVER[\'HTTP_HOST\']}';
$CFGVAR['application_uriprefix']['title'] = 'URI Prefix';
$CFGVAR['application_webpath']['title'] = 'The path to SiT! from the browsers perspective with a trailing slash. e.g. /sit/';
$CFGVAR['attachment_fspath']['help'] = "This directory should be writable";
$CFGVAR['attachment_fspath']['title'] = "The full absolute file system path to the attachments directory (with a trailing slash)";
$CFGVAR['attachment_webpath']['title'] = "The path to the attachments directory from the browsers perspective";
$CFGVAR['auto_assign_incidents']['help'] = "incidents are automatically assigned based on a lottery weighted towards who are less busy, assumes everyone set to accepting is an engineer and willing to take incidents";
$CFGVAR['auto_assign_incidents']['options'] = 'TRUE|FALSE';
$CFGVAR['auto_assign_incidents']['title'] = "Auto-assign incidents";
$CFGVAR['auto_assign_incidents']['type'] = 'select';
$CFGVAR['bugtracker_name']['title'] = 'Bug tracker name';
$CFGVAR['bugtracker_url']['title'] = 'Bug tracker url';
$CFGVAR['calendar_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['calendar_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['calendar_enabled']['title'] = "Calendar Enabled/Disabled";
$CFGVAR['calendar_enabled']['type'] = 'select';
$CFGVAR['changelogfile']['title'] = 'Path to the Changelog file';
$CFGVAR['creditsfile']['title'] = 'Path to the Credits file';
$CFGVAR['critical_threshold']['help'] = 'Enter a number between 0 and 100.';
$CFGVAR['critical_threshold']['title'] = 'flag items as critical when they are this percentage complete.';
$CFGVAR['critical_threshold']['type'] = 'percent';
$CFGVAR['custom_dictionary_file']['title'] = 'Spell check custom words dictionary file';
$CFGVAR['dateformat_datetime']['help'] = "See <a href='http://www.php.net/manual/en/function.date.php'>http://www.php.net/manual/en/function.date.php</a> for help with date formats";
$CFGVAR['dateformat_datetime']['title'] = 'Date and Time format';
$CFGVAR['dateformat_date']['title'] = 'Normal date format';
$CFGVAR['dateformat_filedatetime']['title'] = 'Date and Time format to use for files';
$CFGVAR['dateformat_longdate']['help'] = 'Including the day of the week';
$CFGVAR['dateformat_longdate']['title'] = 'Long date format';
$CFGVAR['dateformat_shortdate']['title'] = 'Short date format';
$CFGVAR['dateformat_shorttime']['title'] = 'Short time format';
$CFGVAR['dateformat_time']['title'] = 'Normal time format';
$CFGVAR['db_database']['title'] = 'MySQL Database Name';
$CFGVAR['db_hostname']['help']="The Hostname or IP address of the MySQL Database Server, usually 'localhost'";
$CFGVAR['db_hostname']['title'] = 'MySQL Database Hostname';
$CFGVAR['db_password']['title'] = 'MySQL Database Password';
$CFGVAR['db_tableprefix']['help']="Prefix database tables with the a string (e.g. 'sit_', use this if the database you are using is shared with other applications";
$CFGVAR['db_tableprefix']['title'] = 'MySQL Database Table Prefix';
$CFGVAR['db_username']['title'] = 'MySQL Database Username';
$CFGVAR['debug']['help'] = 'Set to TRUE to output extra debug information, some as HTML comments and some in the page footer, FALSE to disable';
$CFGVAR['debug']['options'] = 'TRUE|FALSE';
$CFGVAR['debug']['title'] = 'Debug Mode';
$CFGVAR['debug']['type'] = 'select';
$CFGVAR['default_css_url']['title'] = 'The CSS file to use when no other is configured';
$CFGVAR['default_gravatar']['help'] = "can be 'wavatar', 'identicon', 'monsterid' a URL to an image, or blank for a blue G. see <a href='http://www.gravatar.com/'>www.gravatar.com</a> to learn about gravatars";
$CFGVAR['default_gravatar']['title'] = "Default Gravatar";
$CFGVAR['default_i18n']['help'] = "The system language, or the language that will be used when no other language is selected by the user, see <a href='http://sitracker.sourceforge.net/Translation'>http://sitracker.sourceforge.net/Translation</a> for a list of supported languages. ";
$CFGVAR['default_i18n']['title'] = "Default Language";
$CFGVAR['default_interface_style']['title'] = 'The interface style that new users should use (user default style)';
$CFGVAR['default_interface_style']['type'] = 'interfacestyleselect';
$CFGVAR['default_roleid']['help'] = "Role given to new users by default";
$CFGVAR['default_roleid']['title'] = "Default role id";
$CFGVAR['default_service_level']['title'] = 'The service level to use in case the contact does not specify (text not the tag)';
$CFGVAR['demo']['help'] = 'Set to TRUE to run in demo mode, some features are disabled or replaced with mock-ups';
$CFGVAR['demo']['options'] = 'TRUE|FALSE';
$CFGVAR['demo']['title'] = 'Demo Mode';
$CFGVAR['demo']['type'] = 'select';
$CFGVAR['email_address']['title'] = "Incoming email account address";
$CFGVAR['email_address']['title'] = "Incoming email account address";
$CFGVAR['email_options']['help'] = "e.g. Gmail needs '/ssl', secure Groupwise needs /novalidate-cert etc. See http://uk2.php.net/imap_open for examples";
$CFGVAR['email_options']['title'] = "Extra options to pass to the mailbox";
$CFGVAR['email_password']['title'] = "Incoming email account password";
$CFGVAR['email_password']['title'] = "Incoming email account password";
$CFGVAR['email_port']['title'] = "Incoming email account port";
$CFGVAR['email_server']['title'] = "Incoming email account server URL";
$CFGVAR['email_server']['title'] = "Incoming email account server URL";
$CFGVAR['email_servertype']['options'] = 'imap|pop';
$CFGVAR['email_servertype']['options'] = 'imap|pop';
$CFGVAR['email_servertype']['title'] = "Incoming email account server type";
$CFGVAR['email_servertype']['title'] = "Incoming email account server type";
$CFGVAR['email_servertype']['type'] = 'select';
$CFGVAR['email_servertype']['type'] = 'select';
$CFGVAR['email_username']['help'] = "Only fill in this and the following options if you have selected 'POP/IMAP email retrieval";
$CFGVAR['email_username']['title'] = "Incoming email account username";
$CFGVAR['email_username']['title'] = "Incoming email account username";
$CFGVAR['enable_inbound_mail']['help'] = "Normal users should choose 'POP/IMAP' and fill in the details below', advanced users can choose to pipe straight to SiT from their MTA, please read the docs for help on this.";
$CFGVAR['enable_inbound_mail']['options'] = "POP/IMAP|MTA|disabled";
$CFGVAR['enable_inbound_mail']['title'] = "Enable incoming mail to SiT";
$CFGVAR['enable_inbound_mail']['type'] = 'select';
$CFGVAR['enable_spellchecker']['options'] = 'TRUE|FALSE';
$CFGVAR['enable_spellchecker']['title'] = 'Set to TRUE to enable spellchecking or FALSE to disable';
$CFGVAR['enable_spellchecker']['type'] = 'select';
$CFGVAR['end_working_day']['help'] = 'Seconds since midnight';
$CFGVAR['end_working_day']['title'] = 'Time of the end of the working day (in seconds)';
$CFGVAR['error_logfile']['help'] = "This file must be writable of course";
$CFGVAR['error_logfile']['title'] = "Path to a file to log error messages";
$CFGVAR['error_notavailable_url']['title']="The URL to redirect too for pages that do not exist yet.";
$CFGVAR['feedback_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['feedback_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['feedback_enabled']['title'] = "Feedback Enabled/Disabled";
$CFGVAR['feedback_enabled']['type'] = 'select';
$CFGVAR['feedback_form']['title'] = 'Incident feedback form (the id number of the feedback form to use or empty to disable sending feedback forms out)';
$CFGVAR['feedback_max_score']['title'] = 'The max score to use in rating fields for feedback forms';
$CFGVAR['free_support_limit']['title'] = 'Number of free (site) support incidents that can be logged to a site';
$CFGVAR['ftp_hostname']['title'] = 'The ftp hostname or IP address';
$CFGVAR['ftp_password']['title'] = 'Ftp password';
$CFGVAR['ftp_pasv']['options'] = 'TRUE|FALSE';
$CFGVAR['ftp_pasv']['title'] = 'Set to TRUE to enable ftp PASSV mode or FALSE to disable';
$CFGVAR['ftp_pasv']['type'] = 'select';
$CFGVAR['ftp_path']['help'] = '(e.g. /pub/support/) the trailing slash is important';
$CFGVAR['ftp_path']['title'] = 'The path to the directory where we store files on the ftp server';
$CFGVAR['ftp_username']['title'] = 'Ftp username';
$CFGVAR['hide_closed_incidents_older_than']['help'] = "Incidents closed more than this number of days ago aren't show in the incident queue, -1 means disabled";
$CFGVAR['hide_closed_incidents_older_than']['title'] = 'Hide closed incidents older than';
$CFGVAR['holidays_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['holidays_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['holidays_enabled']['title'] = "Holidays Enabled/Disabled";
$CFGVAR['holidays_enabled']['type'] = 'select';
$CFGVAR['home_country']['title'] = "The default country in capitals. e.g. 'UNITED KINGDOM'";
$CFGVAR['incident_pools']['title'] = 'Comma seperated list specifying the numbers of incidents to assign to contracts';
$CFGVAR['journal_loglevel']['help'] = '0 = none, 1 = minimal, 2 = normal, 3 = full, 4 = maximum/debug';
$CFGVAR['journal_loglevel']['title'] = 'Journal Logging Level';
$CFGVAR['journal_purge_after']['title'] = 'How long should we keep journal entries (in seconds), entries older than this will be purged (deleted)';
$CFGVAR['kb_disclaimer_html']['help']  = 'Simple HTML is allowed';
$CFGVAR['kb_disclaimer_html']['title'] = 'Knowledgebase disclaimer, displayed at the bottom of every article';
$CFGVAR['kb_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['kb_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['kb_enabled']['title'] = "Knowledge base Enabled/Disabled";
$CFGVAR['kb_enabled']['type'] = 'select';
$CFGVAR['kb_id_prefix']['help'] = 'inserted before the ID to give it uniqueness';
$CFGVAR['kb_id_prefix']['title'] = 'Knowledgebase ID prefix';
$CFGVAR['licensefile']['title'] = 'Path to the License file';
$CFGVAR['logout_url']['help'] = "When left blank this defaults to \$CONFIG['application_webpath'], setting that here will take the value of the default";
$CFGVAR['logout_url']['title'] = "The URL to redirect the user too after he/she logs out";
$CFGVAR['mailin_spool_path']['title'] = "Incoming mail spool directory, the location of mail processed by mailfilter shell script";
$CFGVAR['main_dictionary_file']['title'] = 'Spell check main dictionary file';
$CFGVAR['max_incoming_email_perday']['title'] = 'maximum no. of incoming emails per incident before a mail-loop is detected';
$CFGVAR['no_feedback_contracts']['help'] = "eg. array(123, 765) would withhold feedback requests for contract 123 and 765";
$CFGVAR['no_feedback_contracts']['title'] = "An array of contracts to not request feedback for";
$CFGVAR['notice_threshold']['help'] = 'Enter a number between 0 and 100.';
$CFGVAR['notice_threshold']['title'] = 'Flag items as notice when they are this percentage complete.';
$CFGVAR['notice_threshold']['type'] = 'percent';
$CFGVAR['plugins']['help'] = "e.g. 'array('magic_plugin', 'lookup_plugin')'";
$CFGVAR['plugins']['title'] = "An array of plugin names";
$CFGVAR['portal_creates_incidents']['help'] = "TRUE if customers can create incidents from the portal, FALSE if they can just create emails";
$CFGVAR['portal_creates_incidents']['title'] = "Portal can create Incidents?";
$CFGVAR['portal_creates_incidents']['type'] = 'select';
$CFGVAR['portal_creates_incidents']['options'] = 'TRUE|FALSE';
$CFGVAR['portal_interface_style']['title'] = "Portal interface style";
$CFGVAR['portal_interface_style']['type'] = 'interfacestyleselect';
$CFGVAR['portal_kb_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['portal_kb_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['portal_kb_enabled']['title'] = "Portal Knowledge base Enabled/Disabled";
$CFGVAR['portal_kb_enabled']['type'] = 'select';
$CFGVAR['portal']['options'] = 'TRUE|FALSE';
$CFGVAR['portal_site_incidents']['help'] = "Users in the portal can view site incidents based on the contract options";
$CFGVAR['portal_site_incidents']['options'] = 'TRUE|FALSE';
$CFGVAR['portal_site_incidents']['title'] = "Show site incidents in portal";
$CFGVAR['portal_site_incidents']['type'] = 'select';
$CFGVAR['portal']['title'] = 'Enable user portal';
$CFGVAR['portal']['type'] = 'select';
$CFGVAR['portal_usernames_can_be_changed']['options'] = 'TRUE|FALSE';
$CFGVAR['portal_usernames_can_be_changed']['title'] = "Allow portal users to change usernames";
$CFGVAR['portal_usernames_can_be_changed']['type'] = 'select';
$CFGVAR['preferred_maintenance']['help'] = "e.g. array('standard', 'high')";
$CFGVAR['preferred_maintenance']['title'] = "An array of SLA's to indicate order of preference when logging incidents against them";
$CFGVAR['record_lock_delay']['title'] = 'Lock records for (number of seconds)';
$CFGVAR['regular_contact_days']['title'] = 'The number of days to elapse before we are prompted to contact the customer (usually overridden by SLA)';
$CFGVAR['sales_email']['title'] = 'Your sales departments email address';
$CFGVAR['session_name']['title'] = 'The session name for use in cookies and URLs, Must contain alphanumeric characters only';
$CFGVAR['spam_email_subject']['title'] = 'String to look for in email message subject to determine a message is spam';
$CFGVAR['spam_forward']['title'] = 'Email address to forward spam messages thatare to be marked as spam';
$CFGVAR['start_working_day']['help'] = 'Seconds since midnight';
$CFGVAR['start_working_day']['title'] = 'Time of the start of the working day (in seconds)';
$CFGVAR['support_email']['title'] = 'Emails sent by SiT will come from this address';
$CFGVAR['support_manager_email']['title'] = 'The email address of the person incharge of your support service';
$CFGVAR['tag_icons']['help'] = "Set up an array to use an icon for specified tags, format: array('tag' => 'icon', 'tag2' => 'icon2')";
$CFGVAR['tag_icons']['title'] = "An array of tags and associated icons";
$CFGVAR['tasks_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['tasks_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['tasks_enabled']['title'] = "Tasks Enabled/Disabled";
$CFGVAR['tasks_enabled']['type'] = 'select';
$CFGVAR['timesheets_enabled']['help'] = "TRUE for enabled, FALSE for disabled";
$CFGVAR['timesheets_enabled']['options'] = 'TRUE|FALSE';
$CFGVAR['timesheets_enabled']['title'] = "Timesheets Enabled/Disabled";
$CFGVAR['timesheets_enabled']['type'] = 'select';
$CFGVAR['timezone']['help'] = "See <a href='http://www.php.net/timezones'>http://www.php.net/timezones</a> for a list of supported Timezones";
$CFGVAR['timezone']['title'] = "System Time Zone";
$CFGVAR['timezone']['title'] = 'Timezone';
$CFGVAR['trusted_server']['help'] = 'If you set this to TRUE, passwords will nolonger be used or required, this assumes that you are using another mechanism for authentication';
$CFGVAR['trusted_server']['options'] = 'TRUE|FALSE';
$CFGVAR['trusted_server']['title'] = 'Enable trusted server mode';
$CFGVAR['trusted_server']['type'] = 'select';
$CFGVAR['upload_max_filesize']['title'] = "The maximum file upload size (in bytes)";
$CFGVAR['urgent_threshold']['help'] = 'Enter a number between 0 and 100.';
$CFGVAR['urgent_threshold']['title'] = 'Flag items as urgent when they are thispercentage complete.';
$CFGVAR['urgent_threshold']['type'] = 'percent';
$CFGVAR['working_days']['title'] = 'Array containing working days (0=Sun, 1=Mon... 6=Sat)';


?>
