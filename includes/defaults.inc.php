<?php
// defaults.inc.php - Provide configuration defaults
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
//  Author: Ivan Lucas
//  Notes: These variables are overwritten by config.inc.php and/or webtrack.conf

$CONFIG['application_name'] = 'SiT! Support Incident Tracker';
$CONFIG['application_shortname'] = 'SiT!';

// The path to SiT! in your filesystem (e.g. /var/www/vhtdocs/sit/
$CONFIG['application_fspath'] = '';
$CONFIG['application_webpath'] = '/';

$CONFIG['application_uriprefix'] = "http://{$_SERVER['HTTP_HOST']}";

$CONFIG['db_hostname'] = 'localhost';
$CONFIG['db_username'] = '';
$CONFIG['db_password'] = '';
// the name of the database to use
$CONFIG['db_database'] = 'sit';

$CONFIG['home_country'] = 'UNITED KINGDOM';

$CONFIG['support_email'] = 'support@localhost';
$CONFIG['sales_email'] = 'sales@localhost';
$CONFIG['support_manager_email'] = 'support_manager@localhost';

$CONFIG['bugtracker_name'] = 'SourceForge Bug Tracker';
$CONFIG['bugtracker_url'] = 'http://sourceforge.net/tracker/?group_id=160319&amp;atid=815372';

// See http://www.php.net/manual/en/function.date.php for help with date formats
$CONFIG['dateformat_datetime'] = 'jS M Y @ g:ia';
$CONFIG['dateformat_filedatetime'] = 'd/m/Y H:i';
$CONFIG['dateformat_shortdate'] = 'd/m/y';
$CONFIG['dateformat_shorttime'] = 'H:i';
$CONFIG['dateformat_date'] = 'jS M Y';
$CONFIG['dateformat_time'] = 'g:ia';

// The amount of time (in seconds) to wait before closing when an incident is marked for closure
$CONFIG['closure_delay'] = 554400; // close after six days and 10 hours

// Array containing working days (0=Sun, 1=Mon ... 6=Sat)
$CONFIG['working_days'] = array(1,2,3,4,5);
// Times of the start and end of the working day (in seconds)
$CONFIG['start_working_day'] = (9 * 3600);
$CONFIG['end_working_day'] = (17 * 3600);

$CONFIG['attachment_fspath'] = "/var/www/sit/attachments/";
$CONFIG['attachment_webpath'] = "attachments/";

// Incoming mail spool directory, the location of mail processed by mailfilter shell script
$CONFIG['mailin_spool_path'] = "{$CONFIG['application_fspath']}mailin/";

$CONFIG['upload_max_filesize'] = get_cfg_var('upload_max_filesize');
// Convert a PHP.INI integer value into a byte value

// FTP Server details, for file upload functionality
$CONFIG['ftp_hostname'] = '';
$CONFIG['ftp_username'] = '';
$CONFIG['ftp_password'] = '';

// Set whether to use passive mode ftp
$CONFIG['ftp_pasv'] = TRUE;
// The path to the directory where we store files, (e.g. /pub/support/) the trailing slash is important
$CONFIG['ftp_path'] = '/';

// Set to TRUE to enable spellchecking or FALSE to disable
$CONFIG['enable_spellchecker'] = FALSE;
// Spell check dictionaries
$CONFIG['main_dictionary_file'] = '/usr/share/dict/linux.words';
$CONFIG['custom_dictionary_file'] = "{$CONFIG['application_fspath']}dictionary/custom.words";

// The CSS file to use when no other is configured
$CONFIG['default_css_url'] = 'styles/webtrack1.css';

// The interface style that new users should use (user default style)
$CONFIG['default_interface_style'] = 8;

// Knowledgebase ID prefix, inserted before the ID to give it uniqueness
$CONFIG['kb_id_prefix'] = 'KB';
// Knowledgebase disclaimer, displayed at the bottom of every article
$CONFIG['kb_disclaimer_html']  = '<strong>THE INFORMATION IN THIS DOCUMENT IS PROVIDED ON AN AS-IS BASIS WITHOUT WARRANTY OF ANY KIND.</strong> ';
$CONFIG['kb_disclaimer_html'] .= 'PROVIDER SPECIFICALLY DISCLAIMS ANY OTHER WARRANTY, EXPRESS OR IMPLIED, INCLUDING ANY WARRANTY OF MERCHANTABILITY ';
$CONFIG['kb_disclaimer_html'] .= 'OR FITNESS FOR A PARTICULAR PURPOSE. IN NO EVENT SHALL PROVIDER BE LIABLE FOR ANY CONSEQUENTIAL, INDIRECT, SPECIAL ';
$CONFIG['kb_disclaimer_html'] .= 'OR INCIDENTAL DAMAGES, EVEN IF PROVIDER HAS BEEN ADVISED BY USER OF THE POSSIBILITY OF SUCH POTENTIAL LOSS OR DAMAGE. ';
$CONFIG['kb_disclaimer_html'] .= 'USER AGREES TO HOLD PROVIDER HARMLESS FROM AND AGAINST ANY AND ALL CLAIMS, LOSSES, LIABILITIES AND EXPENSES.';

// The service level to use in case the contact does not specify (text not the tag)
$CONFIG['default_service_level'] = 'SLA1';
// The number of days to elapse before we are prompted to contact the customer (usually overridden by SLA)
$CONFIG['regular_contact_days'] = 7;

// Number of free support incidents that can be logged to a site
$CONFIG['free_support_limit'] = 2;

// Comma seperated list specifying the numbers of incidents to assign to contracts
$CONFIG['incident_pools'] = '1,2,3,4,5,10,20,25,50,100,150,200,250,500,1000';

// Incident feedback form (the id number of the feedback form to use or empty to disable sending feedback forms out)
$CONFIG['feedback_form'] = '';

// If you set 'trusted_server' to TRUE, passwords will no longer be used or required, this assumes that you are using
// another mechanism for authentication
$CONFIG['trusted_server'] = FALSE;

// Lock records for (number of seconds)
$CONFIG['record_lock_delay'] = 1800;  // 30 minutes

// maximum no. of incoming emails per incident before a mail-loop is detected
$CONFIG['max_incoming_email_perday']=15;

$CONFIG['spam_forward']='';

// String to look for in email message subject to determine a message is spam
$CONFIG['spam_email_subject']='SPAMASSASSIN';

$CONFIG['feedback_max_score']=9;

// Paths to various required files
$CONFIG['tipsfile']= '../doc/tips.txt';
$CONFIG['licensefile']= '../doc/LICENSE';
$CONFIG['changelogfile']= '../doc/Changelog';
$CONFIG['creditsfile']= '../doc/CREDITS';

// The session name for use in cookies and URL's, Must contain alphanumeric characters only
$CONFIG['session_name'] = 'SiTsessionID';


// Notice Threshold, flag items as 'notice' when they are this percentage complete.
$CONFIG['notice_threshold'] = 85;

// Urgent Threshold, flag items as 'urgent' when they are this percentage complete.
$CONFIG['urgent_threshold'] = 90;

// Urgent Threshold, flag items as 'critical' when they are this percentage complete.
$CONFIG['critical_threshold'] = 95;


// Run in demo mode, some features are disabled or replaced with mock-ups
$CONFIG['demo'] = FALSE;

// Output extra debug information, some as HTML comments and some in the page footer
$CONFIG['debug'] = FALSE;

// Enable user portal
$CONFIG['portal'] = FALSE;

// Journal Logging Level
//      0 = No logging
//      1 = Minimal Logging
//      2 = Normal Logging
//      3 = Full Logging
//      4 = Maximum/Debug Logging
$CONFIG['journal_loglevel'] = 3;

// How long should we keep journal entries, entries older than this will be purged (deleted)
$CONFIG['journal_purge_after'] = 60 * 60 * 24 * 180;  // 180 Days

$CONFIG['logout_url'] = $CONFIG['application_webpath'];

$CONFIG['error_logfile'] = "{$CONFIG['application_fspath']}logs/sit.log";

// Filename to log authentication failures
$CONFIG['access_logfile'] = '';

// The plugins configuration is an array
//$CONFIG['plugins'] = array();
$CONFIG['plugins'] =array('');

// The URL for pages that do not exist yet.
$CONFIG['error_notavailable_url']="/?msg=not+available";

//external escalation partners, used for linking from incidents page to partners support site and identification of update origin
$CONFIG['ext_esc_partners'] = array('novell' => array('name' => 'Novell',
                                            'ext_callid_regexp' => '/^[0-9]{11}$/',
                                            'ext_url' => 'https://secure-support.novell.com/eService_enu/',
                                            'ext_url_title' => 'Novell support',
                                            'email_domain' => 'novell.com'),
                                    'microsoft' => array('name' => 'Microsoft',
                                            'ext_callid_regexp' => '/^SR/',
                                            'ext_url' => 'https://support.microsoft.com/oas/default.aspx?tp=re&amp;incno=%externalid%',
                                            'ext_url_title' => 'Microsoft Help and Support',
                                            'email_domain' => 'microsoft.com'));

$CONFIG['no_feedback_contracts'] = array(1 => 2);

$CONFIG['preferred_maintenance'] = array(1 => "Dedicated");

// Use an icon for specified tags, format: array('tag' => 'icon', 'tag2' => 'icon2')";
$CONFIG['tag_icons'] = array ('redflag' => 'redflag', 'yellowflag' => 'yellowflag', 'blueflag' => 'blueflag', 'cyanflag' => 'cyanflag', 'greenflag' => 'greenflag', 'whiteflag' => 'whiteflag', 'blackflag' => 'blackflag');

// Default Internationalisation tag (rfc4646/rfc4647/ISO 639 code), note the corresponding i18n file must exist in includes/i18n before you can use it
$CONFIG['default_i18n'] = 'en-GB';

$CONFIG['timezone'] = 'Europe/London';

// Following is still BETA
$CONFIG['auto_chase'] = FALSE;
$CONFIG['chase_email_minutes'] = 0; // number of minutes incident has been 'awaiting customer action' before sending a chasing email, 0 is disabled
$CONFIG['chase_phone_minutes'] = 0; // number of minutes incident has been 'awaiting customer action' before putting in the 'chase by phone queue', 0 is disabled
$CONFIG['chase_manager_minutes'] = 0; // number of minutes incident has been 'awaiting customer action' before putting in the 'chase manager queue', 0 is disabled
$CONFIG['chase_managers_manager_minutes'] = 0; // number of minutes incident has been 'awaiting customer action' before putting in the 'chase managers_manager queue', 0 is disabled
$CONFIG['chase_email_template'] = ''; // The template to use to send chase email
$CONFIG['dont_chase_maintids'] = array(1 => 1); // maintence IDs not to chase

?>
