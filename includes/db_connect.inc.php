<?php
// db_connect.inc.php - Initiate a database connection
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Load config defaults
include ("defaults.inc.php");
// Load config file with customisations
@include ("config.inc.php");
// Server Configuration
@include ('/etc/webtrack.conf');  // Legacy, for compatibility
@include ('/etc/sit.conf');
// TODO determine which language to use, for now we're hardcoded to English (British)
// i18n
@include ('i18n/en-gb.inc.php');

if ($CONFIG['debug'] > 0)
{
    // Set Start Time for Execution Timing
    function getmicrotime()
    {
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }
    $exec_time_start = getmicrotime();
}

if ($CONFIG['db_username'] == '' OR $CONFIG['db_database'] == '')
{
    header("Location: setup.php");
    exit;
}

// Connect to Database server
$db = @mysql_connect($CONFIG['db_hostname'], $CONFIG['db_username'], $CONFIG['db_password']);
if (mysql_error())
{
    header("Location: setup.php");
    exit;
}
// mysql_query("SET CHARACTER SET utf8");
mysql_query("SET NAMES utf8");

// Select database
mysql_select_db($CONFIG['db_database'], $db);
if (mysql_error())
{
    // TODO add some detection for missing database
    // Attempt socket connection to database to check if server is alive
    if (!fsockopen($CONFIG['db_hostname'], 3306, $errno, $errstr, 5))
    {
        trigger_error("!Error: No response from database server within 5 seconds, Database Server ({$CONFIG['db_hostname']}) is probably down - contact a Systems Administrator",E_USER_ERROR);
    }
    else
    {
        header("Location: setup.php");
        exit;
    }
}

// Table Names
$dbBillingPeriods = "{$CONFIG['db_tableprefix']}billing_periods";
$dbClosingStatus = "{$CONFIG['db_tableprefix']}closingstatus";
$dbContacts = "{$CONFIG['db_tableprefix']}contacts";
$dbDashboard = "{$CONFIG['db_tableprefix']}dashboard";
$dbDashboardRSS = "{$CONFIG['db_tableprefix']}dashboard_rss";
$dbDashboardWatchIncidents = "{$CONFIG['db_tableprefix']}dashboard_watch_incidents";
$dbDrafts = "{$CONFIG['db_tableprefix']}drafts";
$dbEmailSig = "{$CONFIG['db_tableprefix']}emailsig";
$dbEmailType = "{$CONFIG['db_tableprefix']}emailtype";
$dbEscalationPaths = "{$CONFIG['db_tableprefix']}escalationpaths";
$dbFeedbackForms = "{$CONFIG['db_tableprefix']}feedbackforms";
$dbFeedbackQuestions = "{$CONFIG['db_tableprefix']}feedbackquestions";
$dbFeedbackReport = "{$CONFIG['db_tableprefix']}feedbackreport";
$dbFeedbackRespondents = "{$CONFIG['db_tableprefix']}feedbackrespondents";
$dbFeedbackResults = "{$CONFIG['db_tableprefix']}feedbackresults";
$dbFiles = "{$CONFIG['db_tableprefix']}files";
$dbFlags = "{$CONFIG['db_tableprefix']}flags";
$dbGroups = "{$CONFIG['db_tableprefix']}groups";
$dbHolidays = "{$CONFIG['db_tableprefix']}holidays";
$dbIncidentPools = "{$CONFIG['db_tableprefix']}incidentpools";
$dbIncidentProductInfo = "{$CONFIG['db_tableprefix']}incidentproductinfo";
$dbIncidents = "{$CONFIG['db_tableprefix']}incidents";
$dbIncidentStatus = "{$CONFIG['db_tableprefix']}incidentstatus";
$dbInterfaceStyles = "{$CONFIG['db_tableprefix']}interfacestyles";
$dbJournal = "{$CONFIG['db_tableprefix']}journal";
$dbKBArticles = "{$CONFIG['db_tableprefix']}kbarticles";
$dbKBContent = "{$CONFIG['db_tableprefix']}kbcontent";
$dbKBSoftware = "{$CONFIG['db_tableprefix']}kbsoftware";
$dbLicenceTypes = "{$CONFIG['db_tableprefix']}licencetypes";
$dbLinks = "{$CONFIG['db_tableprefix']}links";
$dbLinkTypes = "{$CONFIG['db_tableprefix']}linktypes";
$dbMaintenance = "{$CONFIG['db_tableprefix']}maintenance";
$dbNotes = "{$CONFIG['db_tableprefix']}notes";
$dbNotices = "{$CONFIG['db_tableprefix']}notices";
$dbNoticeTemplates = "{$CONFIG['db_tableprefix']}noticetemplates";
$dbPermissions = "{$CONFIG['db_tableprefix']}permissions";
$dbPriority = "{$CONFIG['db_tableprefix']}priority";
$dbProductInfo = "{$CONFIG['db_tableprefix']}productinfo";
$dbProducts = "{$CONFIG['db_tableprefix']}products";
$dbRelatedIncidents = "{$CONFIG['db_tableprefix']}relatedincidents";
$dbResellers = "{$CONFIG['db_tableprefix']}resellers";
$dbRolePermissions = "{$CONFIG['db_tableprefix']}rolepermissions";
$dbRoles = "{$CONFIG['db_tableprefix']}roles";
$dbServiceLevels = "{$CONFIG['db_tableprefix']}servicelevels";
$dbSetTags = "{$CONFIG['db_tableprefix']}set_tags";
$dbSiteContacts = "{$CONFIG['db_tableprefix']}sitecontacts";
$dbSites = "{$CONFIG['db_tableprefix']}sites";
$dbSiteTypes = "{$CONFIG['db_tableprefix']}sitetypes";
$dbSoftware = "{$CONFIG['db_tableprefix']}software";
$dbSoftwareProducts = "{$CONFIG['db_tableprefix']}softwareproducts";
$dbSpellcheck = "{$CONFIG['db_tableprefix']}spellcheck";
$dbSupportContacts = "{$CONFIG['db_tableprefix']}supportcontacts";
$dbSystem = "{$CONFIG['db_tableprefix']}system";
$dbTags = "{$CONFIG['db_tableprefix']}tags";
$dbTasks = "{$CONFIG['db_tableprefix']}tasks";
$dbTempAssigns = "{$CONFIG['db_tableprefix']}tempassigns";
$dbTempIncoming = "{$CONFIG['db_tableprefix']}tempincoming";
$dbUpdates = "{$CONFIG['db_tableprefix']}updates";
$dbUserGroups = "{$CONFIG['db_tableprefix']}usergroups";
$dbUserPermissions = "{$CONFIG['db_tableprefix']}userpermissions";
$dbUsers = "{$CONFIG['db_tableprefix']}users";
$dbUserSoftware = "{$CONFIG['db_tableprefix']}usersoftware";
$dbUserStatus = "{$CONFIG['db_tableprefix']}userstatus";
$dbVendors = "{$CONFIG['db_tableprefix']}vendors";

?>