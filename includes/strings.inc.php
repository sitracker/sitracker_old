<?php
// strings.inc.php - Set up strings
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Hierarchical Menus
/* Arrays containing menu options for the top menu, each menu has an associated permission number and this is used */
/* to decide which menu to display.  In addition each menu item has an associated permission   */
/* This is so we can decide whether a user should see the menu option or not.                                     */
/* perm = permission number */
/*
$hmenu[1031] = array (10=> array ( 'perm'=> 0, 'name'=> "Option1", 'url'=>""),
                      20=> array ( 'perm'=> 0, 'name'=> "Option2", 'url'=>""),
                      30=> array ( 'perm'=> 0, 'name'=> "Option3", 'url'=>"")
);
*/

//
// Main Menu
//
if (!is_array($hmenu[0])) $hmenu[0] = array();
$hmenu[0] = array_merge(array (10=> array ( 'perm'=> 0, 'name'=> $CONFIG['application_shortname'], 'url'=>"{$CONFIG['application_webpath']}main.php", 'submenu'=>"10"),
                   20=> array ( 'perm'=> 11, 'name'=> $strCustomers, 'url'=>"{$CONFIG['application_webpath']}browse_sites.php", 'submenu'=>"20"),
                   30=> array ( 'perm'=> 6, 'name'=> $strSupport, 'url'=>"{$CONFIG['application_webpath']}incidents.php?user=current&amp;queue=1&amp;type=support", 'submenu'=>"30"),
                   40=> array ( 'perm'=> 0, 'name'=> $strTasks, 'url'=>"{$CONFIG['application_webpath']}tasks.php", 'submenu'=>"40"),
                   50=> array ( 'perm'=> 54, 'name'=> $strKnowledgeBase, 'url'=>"{$CONFIG['application_webpath']}browse_kb.php", 'submenu'=>"50"),
                   60=> array ( 'perm'=> 37, 'name'=> $strReports, 'url'=>"", 'submenu'=>"60"),
                   70=> array ( 'perm'=> 0, 'name'=> $strHelp, 'url'=>"{$CONFIG['application_webpath']}help.php", 'submenu'=>"70")
), $hmenu[0]);
if (!is_array($hmenu[10])) $hmenu[10] = array();
$hmenu[10] = array_merge(array (1=> array ( 'perm'=> 0, 'name'=> $strDashboard, 'url'=>"{$CONFIG['application_webpath']}main.php"),
                    10=> array ( 'perm'=> 60, 'name'=> $strSearch, 'url'=>"{$CONFIG['application_webpath']}search.php"),
                    20=> array ( 'perm'=> 4, 'name'=> $strMyDetails, 'url'=>"{$CONFIG['application_webpath']}edit_profile.php", 'submenu'=>"1020"),
                    30=> array ( 'perm'=> 4, 'name'=> $strControlPanel, 'url'=>"{$CONFIG['application_webpath']}control_panel.php", 'submenu'=>"1030"),
                    40=> array ( 'perm'=> 14, 'name'=> $strUsers, 'url'=>"{$CONFIG['application_webpath']}users.php", 'submenu'=>"1040"),
                    50=> array ( 'perm'=> 0, 'name'=> $strLogout, 'url'=>"{$CONFIG['application_webpath']}logout.php")
), $hmenu[10]);
$hmenu[1020] = array (10=> array ( 'perm'=> 4, 'name'=> $strMyProfile, 'url'=>"{$CONFIG['application_webpath']}edit_profile.php"),
                      20=> array ( 'perm'=> 58, 'name'=> $strMySkills, 'url'=>"{$CONFIG['application_webpath']}edit_user_skills.php"),
                      30=> array ( 'perm'=> 58, 'name'=> $strMySubstitutes, 'url'=>"{$CONFIG['application_webpath']}edit_backup_users.php"),
                      40=> array ( 'perm'=> 27, 'name'=> $strMyHolidays, 'url'=>"{$CONFIG['application_webpath']}holidays.php"),
                      50=> array ( 'perm'=> 4, 'name'=> $strMyDashboard, 'url'=>"{$CONFIG['application_webpath']}manage_user_dashboard.php")
);
// configure

// TODO v3.40 set a permission for triggers
if (!is_array($hmenu[1030])) $hmenu[1030] = array();
$hmenu[1030] = array_merge(array (10=> array ( 'perm'=> 22, 'name'=> $strUsers, 'url'=>"{$CONFIG['application_webpath']}manage_users.php", 'submenu'=>"103010"),
                      20=> array ( 'perm'=> 0, 'name'=> $strEmailSettings, 'url'=>"", 'submenu'=>"103020"),
                      30=> array ( 'perm'=> 22, 'name'=> $strSetPublicHolidays, 'url'=>"{$CONFIG['application_webpath']}holiday_calendar.php?type=10"),
                      40=> array ( 'perm'=> 22, 'name'=> $strFTPFilesDB, 'url'=>"{$CONFIG['application_webpath']}ftp_list_files.php"),
                      50=> array ( 'perm'=> 22, 'name'=> $strServiceLevels, 'url'=>"{$CONFIG['application_webpath']}service_levels.php"),
                      60=> array ( 'perm'=> 7, 'name'=> $strBulkModify, 'url'=>"{$CONFIG['application_webpath']}bulk_modify.php?action=external_esc"),
                      70=> array ( 'perm'=> 64, 'name'=> $strEscalationPaths, 'url'=>"{$CONFIG['application_webpath']}escalation_paths.php"),
                      80=> array ( 'perm'=> 66, 'name'=> $strManageDashboardComponents, 'url'=>"{$CONFIG['application_webpath']}manage_dashboard.php"),
                      90=> array ( 'perm'=> 69, 'name'=> $strNotices, 'url'=>"{$CONFIG['application_webpath']}notices.php"),
                      100=> array ( 'perm'=> 22, 'name'=> $strTriggers, 'url'=>"{$CONFIG['application_webpath']}triggers.php"),
                      110=> array ( 'perm'=> 22, 'name'=> $strScheduler, 'url'=>"{$CONFIG['application_webpath']}scheduler.php"),
                      120=> array ( 'perm'=> 49, 'name'=> $strFeedbackForms, 'url'=>"", 'submenu'=>"103090")
), $hmenu[1030]);
if (!is_array($hmenu[103010])) $hmenu[103010] = array();
$hmenu[103010] = array_merge(array (10=> array ( 'perm'=> 22, 'name'=> $strManageUsers, 'url'=>"{$CONFIG['application_webpath']}manage_users.php"),
                        20=> array ( 'perm'=> 20, 'name'=> $strAddUser, 'url'=>"{$CONFIG['application_webpath']}add_user.php?action=showform"),
                        30=> array ( 'perm'=> 9, 'name'=> $strSetPermissions, 'url'=>"{$CONFIG['application_webpath']}edit_user_permissions.php"),
                        40=> array ( 'perm'=> 23, 'name'=> $strUserGroups, 'url'=>"{$CONFIG['application_webpath']}usergroups.php"),
                        50=> array ( 'perm'=> 22, 'name'=> $strEditHolidayEntitlement, 'url'=>"{$CONFIG['application_webpath']}edit_holidays.php")
), $hmenu[103010]);
if (!is_array($hmenu[103020])) $hmenu[103020] = array();
$hmenu[103020] = array_merge(array (10=> array ( 'perm'=> 16, 'name'=> $strAddTemplate, 'url'=>"{$CONFIG['application_webpath']}add_emailtype.php?action=showform"),
                        20=> array ( 'perm'=> 17, 'name'=> $strEditTemplate, 'url'=>"{$CONFIG['application_webpath']}templates.php"),
                        30=> array ( 'perm'=> 43, 'name'=> $strGlobalSignature, 'url'=>"{$CONFIG['application_webpath']}edit_global_signature.php")
), $hmenu[103020]);
if (!is_array($hmenu[103090])) $hmenu[103090] = array();
$hmenu[103090] = array_merge(array (10=> array ( 'perm'=> 49, 'name'=> $strAddFeedbackForm, 'url'=>"{$CONFIG['application_webpath']}edit_feedback_form.php?action=new"),
                        20=> array ( 'perm'=> 49, 'name'=> $strBrowseFeedbackForms, 'url'=>"{$CONFIG['application_webpath']}browse_feedback_forms.php")
), $hmenu[103090]);
if (!is_array($hmenu[1040])) $hmenu[1040] = array();
$hmenu[1040] = array_merge(array (10=> array ( 'perm'=> 0, 'name'=> $strViewUsers, 'url'=>"{$CONFIG['application_webpath']}users.php"),
                      20=> array ( 'perm'=> 0, 'name'=> $strListSkills, 'url'=>"{$CONFIG['application_webpath']}user_skills.php"),
                      21=> array ( 'perm'=> 0, 'name'=> $strSkillsMatrix, 'url'=>"{$CONFIG['application_webpath']}skills_matrix.php"),
                      30=> array ( 'perm'=> 27, 'name'=> $strHolidayPlanner, 'url'=>"{$CONFIG['application_webpath']}holiday_calendar.php?display=month"),
                      40=> array ( 'perm'=> 50, 'name'=> $strApproveHolidays, 'url'=>"{$CONFIG['application_webpath']}holiday_request.php?user=all&amp;mode=approval")
), $hmenu[1040]);



// Customers
if (!is_array($hmenu[20])) $hmenu[20] = array();
$hmenu[20] = array_merge(array (10=> array ( 'perm'=> 0, 'name'=> $strSites, 'url'=>"{$CONFIG['application_webpath']}browse_sites.php", 'submenu'=>"2010"),
                    20=> array ( 'perm'=> 0, 'name'=> $strContacts, 'url'=>"{$CONFIG['application_webpath']}browse_contacts.php?search_string=A", 'submenu'=>"2020"),
                    30=> array ( 'perm'=> 0, 'name'=> $strMaintenance, 'url'=>"{$CONFIG['application_webpath']}browse_contract.php?search_string=A", 'submenu'=>"2030"),
                    40=> array ( 'perm'=> 0, 'name'=> $strBrowseFeedback, 'url'=>"{$CONFIG['application_webpath']}browse_feedback.php")
), $hmenu[20]);

if (!is_array($hmenu[2010])) $hmenu[2010] = array();
$hmenu[2010] = array_merge(array (10=> array ( 'perm'=> 11, 'name'=> $strBrowse, 'url'=>"{$CONFIG['application_webpath']}browse_sites.php"),
                      20=> array ( 'perm'=> 2, 'name'=> $strNewSite, 'url'=>"{$CONFIG['application_webpath']}add_site.php?action=showform")
), $hmenu[2010]);
if (!is_array($hmenu[2020])) $hmenu[2020] = array();
$hmenu[2020] = array_merge(array (10=> array ( 'perm'=> 11, 'name'=> $strBrowse, 'url'=>"{$CONFIG['application_webpath']}browse_contacts.php?search_string=A"),
                      20=> array ( 'perm'=> 1, 'name'=> $strNewContact, 'url'=>"{$CONFIG['application_webpath']}add_contact.php?action=showform")
), $hmenu[2020]);
if (!is_array($hmenu[2030])) $hmenu[2030] = array();
$hmenu[2030] = array_merge(array (10=> array ( 'perm'=> 19, 'name'=> $strBrowse, 'url'=>"{$CONFIG['application_webpath']}browse_contract.php?search_string=A"),
                      20=> array ( 'perm'=> 39, 'name'=> $strNewContract, 'url'=>"{$CONFIG['application_webpath']}add_contract.php?action=showform"),
                      30=> array ( 'perm'=> 21, 'name'=> $strEditContract, 'url'=>"{$CONFIG['application_webpath']}edit_contract.php?action=showform"),
                      40=> array ( 'perm'=> 2, 'name'=> $strNewReseller, 'url'=>"{$CONFIG['application_webpath']}add_reseller.php"),
                      50=> array ( 'perm'=> 19, 'name'=> $strShowRenewals, 'url'=>"{$CONFIG['application_webpath']}search_renewals.php?action=showform"),
                      60=> array ( 'perm'=> 19, 'name'=> $strShowExpired, 'url'=>"{$CONFIG['application_webpath']}search_expired.php?action=showform"),
                      70=> array ( 'perm'=> 0, 'name'=> "{$strProducts} &amp; {$strSkills}", 'url'=>"{$CONFIG['application_webpath']}products.php", 'submenu'=>"203010"),
), $hmenu[2030]);
if (!is_array($hmenu[203010])) $hmenu[203010] = array();
$hmenu[203010] = array_merge(array (10=> array ( 'perm'=> 56, 'name'=> $strAddVendor, 'url'=>"{$CONFIG['application_webpath']}add_vendor.php"),
                        20=> array ( 'perm'=> 24, 'name'=> $strAddProduct, 'url'=>"{$CONFIG['application_webpath']}add_product.php"),
                        30=> array ( 'perm'=> 28, 'name'=> $strListProducts, 'url'=>"{$CONFIG['application_webpath']}products.php"),
                        35=> array ( 'perm'=> 28, 'name'=> $strListSkills, 'url'=>"{$CONFIG['application_webpath']}products.php?display=skills"),
                        40=> array ( 'perm'=> 56, 'name'=> $strAddSkill, 'url'=>"{$CONFIG['application_webpath']}add_software.php"),
                        50=> array ( 'perm'=> 24, 'name'=> $strLinkProducts, 'url'=>"{$CONFIG['application_webpath']}add_product_software.php"),
                        60=> array ( 'perm'=> 25, 'name'=> $strAddProductQuestion, 'url'=>"{$CONFIG['application_webpath']}add_productinfo.php"),
                        70=> array ('perm'=> 56, 'name'=> $strEditVendor, 'url'=>"{$CONFIG['application_webpath']}edit_vendor.php")
), $hmenu[203010]);


// Support
if (!is_array($hmenu[30])) $hmenu[30] = array();
$hmenu[30] = array_merge(array (10=> array ( 'perm'=> 5, 'name'=> $strAddIncident, 'url'=>"{$CONFIG['application_webpath']}add_incident.php"),
                    20=> array ( 'perm'=> 0, 'name'=> $strViewIncidents, 'url'=>"{$CONFIG['application_webpath']}incidents.php?user=current&amp;queue=1&amp;type=support"),
                    30=> array ( 'perm'=> 0, 'name'=> $strWatchIncidents, 'url'=>"{$CONFIG['application_webpath']}incidents.php?user=all&amp;queue=1&amp;type=support"),
                    40=> array ( 'perm'=> 42, 'name'=> $strHoldingQueue, 'url'=>"{$CONFIG['application_webpath']}review_incoming_updates.php"),
                    50=> array ( 'perm'=> 0, 'name'=> $strJumpToIncident,
                                 'url'=>"javascript:var id = prompt('{$strEnterTheIncidentID}'); if (!isNaN(id)) window.location = '{$CONFIG['application_webpath']}incident_details.php?id=' + id + '&amp;win=jump';")
), $hmenu[30]);


// Tasks
if (!is_array($hmenu[40])) $hmenu[40] = array();
$hmenu[40] = array_merge(array (10=> array ( 'perm'=> 0, 'name'=> $strAddTask, 'url'=>"{$CONFIG['application_webpath']}add_task.php"),
                    20=> array ( 'perm'=> 0, 'name'=> $strViewTasks, 'url'=>"{$CONFIG['application_webpath']}tasks.php")
), $hmenu[40]);

// KB
if (!is_array($hmenu[50])) $hmenu[50] = array();
$hmenu[50] = array_merge(array (10=> array ( 'perm'=> 54, 'name'=> $strNewKBArticle, 'url'=>"{$CONFIG['application_webpath']}kb_add_article.php"),
                    20=> array ( 'perm'=> 54, 'name'=> $strBrowse, 'url'=>"{$CONFIG['application_webpath']}browse_kb.php")
), $hmenu[50]);


if (!is_array($hmenu[60])) $hmenu[60] = array();
// Reports
        $hmenu[60] = array_merge(array (10=> array ( 'perm'=> 37, 'name'=>"{$strMarketingMailshot}", 'url'=>"{$CONFIG['application_webpath']}reports/marketing.php"),
                    20=> array ( 'perm'=> 37, 'name'=> "{$strCustomerExport}", 'url'=>"{$CONFIG['application_webpath']}reports/cust_export.php"),
                    30=> array ( 'perm'=> 37, 'name'=> "{$strQueryByExample}", 'url'=>"{$CONFIG['application_webpath']}reports/qbe.php"),
                    50=> array ( 'perm'=> 37, 'name'=> "{$strIncidentsBySite}", 'url'=>"{$CONFIG['application_webpath']}reports/yearly_customer_export.php"),
                    55=> array ( 'perm'=> 37, 'name'=> "{$strIncidentsByEngineer}", 'url'=>"{$CONFIG['application_webpath']}reports/yearly_engineer_export.php"),
                    60=> array ( 'perm'=> 37, 'name'=> "{$strSiteProducts}", 'url'=>"{$CONFIG['application_webpath']}reports/site_products.php"),
                    65=> array ( 'perm'=> 37,  'name'=> "{$strCountContractsByProduct}", 'url'=>"{$CONFIG['application_webpath']}reports/count_contracts_by_product.php"),
                    70=> array ( 'perm'=> 37, 'name'=> "{$strSiteContracts}", 'url'=>"{$CONFIG['application_webpath']}reports/supportbycontract.php"),
                    80=> array ( 'perm'=> 37, 'name'=> "{$strCustomerFeedback}", 'url'=>"{$CONFIG['application_webpath']}reports/feedback.php"),
                    90=> array ( 'perm'=> 37, 'name'=> "{$strSiteIncidents}", 'url'=>"{$CONFIG['application_webpath']}reports/site_incidents.php"),
                    100=> array ( 'perm'=> 37, 'name'=> "{$strRecentIncidents}", 'url'=>"{$CONFIG['application_webpath']}reports/recent_incidents_table.php"),
                    110=> array ( 'perm'=> 37, 'name'=> "{$strIncidentsLoggedOpenClosed}", 'url'=>"{$CONFIG['application_webpath']}reports/incident_graph.php"),
                    120=> array ( 'perm'=> 37, 'name'=> "{$strAverageIncidentDuration}", 'url'=>"{$CONFIG['application_webpath']}reports/average_incident_duration.php"),
                    130=> array ( 'perm'=> 37, 'name'=> "{$strIncidentsBySkill}", 'url'=>"{$CONFIG['application_webpath']}reports/incidents_by_software.php"),
                    140=> array ( 'perm'=> 37, 'name'=> "{$strIncidentsByVendor}", 'url'=>"{$CONFIG['application_webpath']}reports/incidents_by_vendor.php"),
                    150=> array ( 'perm'=> 37, 'name'=> "{$strEscalatedIncidents}",
                    'url'=>"{$CONFIG['application_webpath']}reports/external_engineers.php",
)), $hmenu[60]);

if (!is_array($hmenu[70])) $hmenu[70] = array();
$hmenu[70] = array_merge(array (10=> array ( 'perm'=> 0, 'name'=> "{$strHelpContents}...", 'url'=>"{$CONFIG['application_webpath']}help.php"),
                    20=> array ( 'perm'=> 0, 'name'=> "{$strTranslate}", 'url'=>"{$CONFIG['application_webpath']}translate.php"),
                    30=> array ( 'perm'=> 0, 'name'=> "{$strReportBug}", 'url'=>$CONFIG['bugtracker_url']),
                    40=> array ( 'perm'=> 0, 'name'=> "{$strReleaseNotes}", 'url'=>"{$CONFIG['application_webpath']}releasenotes.php"),
                    50=> array ( 'perm'=> 41, 'name'=> $strHelpAbout, 'url'=>"{$CONFIG['application_webpath']}about.php")
), $hmenu[70]);

// Sort the top level menu, so that plugin menus appear in the right place
ksort($hmenu[0]);

//
// Non specific update types
//
$updatetypes['actionplan'] = array('icon' => 'actionplan.png', 'text' => sprintf($strActionPlanBy,'updateuser'));
$updatetypes['auto'] = array('icon' => 'auto.png', 'text' => sprintf($strUpdatedAutomaticallyBy, 'updateuser'));
$updatetypes['closing'] = array('icon' => 'close.png', 'text' => sprintf($strMarkedforclosureby,'updateuser'));
$updatetypes['editing'] = array('icon' => 'edit.png', 'text' => sprintf($strEditedBy,'updateuser'));
$updatetypes['email'] = array('icon' => 'emailout.png', 'text' => sprintf($strEmailsentby,'updateuser'));
$updatetypes['emailin'] = array('icon' => 'emailin.png', 'text' => sprintf($strEmailreceivedby,'updateuser'));
$updatetypes['emailout'] = array('icon' => 'emailout.png', 'text' => sprintf($Emailsentby,'updateuser'));
$updatetypes['externalinfo'] = array('icon' => 'externalinfo.png', 'text' => sprintf($strExternalInfoAddedBy,'updateuser'));
$updatetypes['probdef'] = array('icon' => 'probdef.png', 'text' => sprintf($strProblemDefinitionby,'updateuser'));
$updatetypes['research'] = array('icon' => 'research.png', 'text' => sprintf($strResearchedby,'updateuser'));
$updatetypes['reassigning'] = array('icon' => 'reassign.png', 'text' => sprintf($strReassignedToBy,'currentowner','updateuser'));
$updatetypes['reviewmet'] = array('icon' => 'review.png', 'text' => sprintf($strReviewby, 'updatereview', 'updateuser')); // conditional
$updatetypes['tempassigning'] = array('icon' => 'tempassign.png', 'text' => sprintf($strTemporarilyAssignedto,'currentowner','updateuser'));
$updatetypes['opening'] = array('icon' => 'open.png', 'text' => sprintf($strOpenedby,'updateuser'));
$updatetypes['phonecallout'] = array('icon' => 'callout.png', 'text' => sprintf($strPhonecallmadeby,'updateuser'));
$updatetypes['phonecallin'] = array('icon' => 'callin.png', 'text' => sprintf($strPhonecalltakenby,'updateuser'));
$updatetypes['reopening'] = array('icon' => 'reopen.png', 'text' => sprintf($strReopenedby,'updateuser'));
$updatetypes['slamet'] = array('icon' => 'sla.png', 'text' => sprintf($strSLAby,'updatesla', 'updateuser'));
$updatetypes['solution'] = array('icon' => 'solution.png', 'text' => sprintf($strResolvedby, 'updateuser'));
$updatetypes['webupdate'] = array('icon' => 'webupdate.png', 'text' => sprintf($strWebupdate));
$updatetypes['auto_chase_phone'] = array('icon' => 'chase.png', 'text' => $strChase);
$updatetypes['auto_chase_manager'] = array('icon' => 'chase.png', 'text' => $strChase);
$updatetypes['auto_chase_email'] = array('icon' => 'chased.png', 'text' => $strChased);
$updatetypes['auto_chased_phone'] = array('icon' => 'chased.png', 'text' => $strChased);
$updatetypes['auto_chased_manager'] = array('icon' => 'chased.png', 'text' => $strChased);
$updatetypes['auto_chased_managers_manager'] = array('icon' => 'chased.png', 'text' => $strChased);
$updatetypes['customerclosurerequest'] = array('icon' => 'close.png', 'text' => $strCustomerRequestedClosure);
$updatetypes['fromtask'] = array('icon' => 'webupdate.png', text => sprintf($strUpdatedFromActivity, 'updateuser'));
$slatypes['opened'] = array('icon' => 'open.png', 'text' => $strOpened);
$slatypes['initialresponse'] = array('icon' => 'initialresponse.png', 'text' => $strInitialResponse);
$slatypes['probdef'] = array('icon' => 'probdef.png', 'text' => $strProblemDefinition);
$slatypes['actionplan'] = array('icon' => 'actionplan.png', 'text' => $strActionPlan);
$slatypes['solution'] = array('icon' => 'solution.png', 'text' => $strSolution);
$slatypes['closed'] = array('icon' => 'close.png', 'text' => $strClosed);


// List of *Available* languages, must match files in includes/i18n
// TODO allow this list to be configured via config.inc.php
$availablelanguages = array('en-GB' => 'English (British)',
                            'en-US' => 'English (US)',
                            'zh-CN' => '简体中文',
                            'de-DE' => 'Deutsch',
                            'es-ES' => 'Español',
                            'es-CO' => 'Español (Colombia)',
                            'fr-FR' => 'Français',
                            'ja-JP' => '日本語',
                            'it-IT' => 'Italiano',
                            'lt-LT' => 'Lietuvių',
                            'cy-GB' => 'Cymraeg'
                           );


// List of timezones, with UTC offset in minutes
// Source: http://en.wikipedia.org/wiki/List_of_time_zones (where else?)
$availabletimezones = array('-720' => 'UTC-12',
                            '-660' => 'UTC-11',
                            '-600' => 'UTC-10',
                            '-570' => 'UTC-9:30',
                            '-540' => 'UTC-9',
                            '-480' => 'UTC-8',
                            '-420' => 'UTC-7',
                            '-360' => 'UTC-6',
                            '-300' => 'UTC-5',
                            '-270' => 'UTC-4:30',
                            '-240' => 'UTC-4',
                            '-210' => 'UTC-3:30',
                            '-180' => 'UTC-3',
                            '-120' => 'UTC-2',
                            '-60' => 'UTC-1',
                            '0' => 'UTC',
                            '60' => 'UTC+1',
                            '120' => 'UTC+2',
                            '180' => 'UTC+3',
                            '210' => 'UTC+3:30',
                            '240' => 'UTC+4',
                            '300' => 'UTC+5',
                            '330' => 'UTC+5:30',
                            '345' => 'UTC+5:45',
                            '360' => 'UTC+6',
                            '390' => 'UTC+6:30',
                            '420' => 'UTC+7',
                            '480' => 'UTC+8',
                            '525' => 'UTC+8:45',
                            '540' => 'UTC+9',
                            '570' => 'UTC+9:30',
                            '600' => 'UTC+10',
                            '630' => 'UTC+10:30',
                            '660' => 'UTC+11',
                            '690' => 'UTC+11:30',
                            '720' => 'UTC+12',
                            '765' => 'UTC+12:45',
                            '780' => 'UTC+13',
                            '840' => 'UTC+14',
                           );


/**
    * Template variables (Alphabetical order)
    * description - Friendly label
    * replacement - Quoted PHP code to be run to perform the template var replacement
    * requires -Optional field. single string or array. Specifies the 'required' params from the trigger that is needed for this replacement
    * action - Optional field, when set the var will only be available for that action
*/
$ttvararray['{applicationname}'] = array('description' => $CONFIG['application_name'],
                                     'replacement' => '$CONFIG[\'application_name\'];');

$ttvararray['{applicationshortname}'] = array('description' => $CONFIG['application_shortname'],
                                     'replacement' => '$CONFIG[\'application_shortname\'];');

$ttvararray['{applicationversion}'] = array('description' => $application_version_string,
                                     'replacement' => '$application_version_string;');

$ttvararray['{contactemail}'] = array('description' => $strIncidentsContactEmail,
                                      'requires' => 'contactid',
                                     'replacement' => 'contact_email($contactid);',
                                     'action' => 'ACTION_EMAIL');

$ttvararray['{contactfirstname}'] = array('description' => 'First Name of contact',
                                     'requires' => 'contactid',
                                     'replacement' => "strtok(contact_realname(\$contactid),' ');");

$ttvararray['{contactname}'] = array('description' => 'Full Name of contact',
                                     'requires' => 'contactid',
                                     'replacement' => 'contact_realname($contactid);');

$ttvararray['{contactnotify}'] = array('description' => 'The Notify Contact email address (if set)',
                                      'requires' => 'contactid',
                                     'replacement' => 'contact_notify_email($contactid);');

$ttvararray['{contactphone}'] = array('description' => 'Contact phone number',
                                     'requires' => 'contactid',
                                     'replacement' => 'contact_site($contactid);');

$ttvararray['{contactsite}'] = array('description' => 'Site name',
                                     'requires' => 'siteid',
                                     'replacement' => 'contact_site($contactid);');

$ttvararray['{feedbackurl}'] = array('description' => '',
                                     'requires' => 'incidentid',
                                     'replacement' => '$baseurl.\'feedback.php?ax=\'.urlencode(trim(base64_encode(gzcompress(str_rot13(urlencode($CONFIG[\'feedback_form\']).\'&&\'.urlencode($contactid).\'&&\'.urlencode($incidentid))))));');

$ttvararray['{globalsignature}'] = array('description' => $strGlobalSignature,
                                     'replacement' => 'global_signature();');

$ttvararray['{incidentccemail}'] = array('description' => $strIncidentCCList,
                                     'requires' => 'incidentid',
                                     'replacement' => 'incident_ccemail($paramarray[incidentid]);');

$ttvararray['{incidentexternalemail}'] = array('description' => $strExternalEngineerEmail,
                                     'requires' => 'incidentid',
                                     'replacement' => 'incident_externalemail($paramarray[incidentid]);');

$ttvararray['{incidentexternalengineer}'] = array('description' => $strExternalEngineer,
                                     'requires' => 'incidentid',
                                     'replacement' => 'incident_externalengineer($paramarray[incidentid]);');


$ttvararray['{incidentexternalengineerfirstname}'] = array('description' => $strExternalEngineersFirstName,
                                     'requires' => 'incidentid',
                                     'replacement' => 'strtok(incident_externalengineer($paramarray[incidentid]),\' \');');

$ttvararray['{incidentexternalid}'] = array('description' => "{$GLOBALS['strExternalID']}",
                                     'requires' => 'incidentid',
                                     'replacement' => '$incident->externalid;');

$ttvararray['{incidentfirstupdate}'] = array('description' => $strFirstCustomerVisibleUpdate,
                                     'replacement' => '');

$ttvararray['{incidentid}'] = array('description' => $GLOBALS['strIncidentID'],
                                     'requires' => 'incidentid',
                                     'replacement' => '$paramarray[incidentid];');

$ttvararray['{incidentowner}'] = array('description' => $strIncidentOwnersFullName,
                                     'requires' => 'incidentid',
                                     'replacement' => 'user_realname(incident_owner($paramarray[incidentid]));');

$ttvararray['{incidentowneremail}'] = array('description' => 'Incident Owners Email Address',
                                     'requires' => 'incidentid',
                                     'replacement' => 'user_email(incident_owner($paramarray[incidentid]));');

$ttvararray['{incidentpriority}'] = array('description' => $strIncidentPriority,
                                     'requires' => 'incidentid',
                                     'replacement' => 'priority_name(incident_priority($paramarray[incidentid]));');

$ttvararray['{incidentsoftware}'] = array('description' => $strSkillAssignedToIncident,
                                     'requires' => 'incidentid',
                                     'replacement' => 'software_name(db_read_column("softwareid", $GLOBALS["dbIncidents"], $paramarray[incidentid]));');

$ttvararray['{incidenttitle}'] = array('description' => $strIncidentTitle,
                                     'requires' => 'incidentid',
                                     'replacement' => 'incident_title($paramarray[incidentid]);');

$ttvararray['{salesperson}'] = array('description' => 'Salesperson',
                                     'requires' => 'siteid',
                                     'replacement' => 'user_realname(db_read_column(\'owner\', $GLOBALS[\'dbSites\'], $siteid));');

$ttvararray['{salespersonemail}'] = array('description' => $strSalespersonAssignedToContactsSiteEmail,
                                     'requires' => 'siteid',
                                     'replacement' => 'user_email(db_read_column(\'owner\', $GLOBALS[\'dbSites\'], $siteid));');

$ttvararray['{signature}'] = array('description' => $strCurrentUsersSignature,
                                     'replacement' => 'user_signature($_SESSION[\'userid\']);');

$ttvararray['{supportemail}'] = array('description' => $strSupportEmailAddress,
                                     'replacement' => '$CONFIG[\'support_email\'];');

$ttvararray['{supportmanageremail}'] = array('description' => $strSupportManagersEmailAddress,
                                     'replacement' => '$CONFIG[\'support_manager_email\'];');

$ttvararray['{todaysdate}'] = array('description' => $strCurrentDate,
                                     'replacement' => 'ldate("jS F Y");');

$ttvararray['{useremail}'] = array('description' => $strCurrentUserEmailAddress,
                                     'replacement' => 'user_email($_SESSION[\'userid\']);');

$ttvararray['{userrealname}'] = array('description' => $strFullNameCurrentUser,
                                     'replacement' => 'user_realname($_SESSION[\'userid\']);');


?>