<?php
// strings.inc.php - Set up strings
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


//
// Main Menu
//
$hmenu[0] = array (10=> array ( 'perm'=> 0, 'name'=> $CONFIG['application_shortname'], 'url'=>"{$CONFIG['application_webpath']}main.php", 'submenu'=>"10"),
                   20=> array ( 'perm'=> 11, 'name'=> $strCustomers, 'url'=>"{$CONFIG['application_webpath']}browse_sites.php", 'submenu'=>"20"),
                   30=> array ( 'perm'=> 6, 'name'=> $strSupport, 'url'=>"{$CONFIG['application_webpath']}incidents.php?user=current&amp;queue=1&amp;type=support", 'submenu'=>"30"),
                   40=> array ( 'perm'=> 0, 'name'=> $strTasks, 'url'=>"{$CONFIG['application_webpath']}tasks.php", 'submenu'=>"40"),
                   50=> array ( 'perm'=> 54, 'name'=> $strKnowledgeBase, 'url'=>"{$CONFIG['application_webpath']}browse_kb.php", 'submenu'=>"50"),
                   60=> array ( 'perm'=> 37, 'name'=> $strReports, 'url'=>"{$CONFIG['application_webpath']}reports.php", 'submenu'=>"60"),
                   70=> array ( 'perm'=> 0, 'name'=> $strHelp, 'url'=>"{$CONFIG['application_webpath']}help.php", 'submenu'=>"70")
);
$hmenu[10] = array (1=> array ( 'perm'=> 0, 'name'=> $strDashboard, 'url'=>"{$CONFIG['application_webpath']}main.php"),
                    10=> array ( 'perm'=> 60, 'name'=> $strSearch, 'url'=>"{$CONFIG['application_webpath']}search.php"),
                    20=> array ( 'perm'=> 4, 'name'=> $strMyDetails, 'url'=>"{$CONFIG['application_webpath']}edit_profile.php", 'submenu'=>"1020"),
                    30=> array ( 'perm'=> 4, 'name'=> $strControlPanel, 'url'=>"{$CONFIG['application_webpath']}control_panel.php", 'submenu'=>"1030"),
                    40=> array ( 'perm'=> 14, 'name'=> $strUsers, 'url'=>"{$CONFIG['application_webpath']}users.php", 'submenu'=>"1040"),
                    50=> array ( 'perm'=> 0, 'name'=> $strLogout, 'url'=>"{$CONFIG['application_webpath']}logout.php")
);
$hmenu[1020] = array (10=> array ( 'perm'=> 4, 'name'=> $strMyProfile, 'url'=>"{$CONFIG['application_webpath']}edit_profile.php"),
                      20=> array ( 'perm'=> 58, 'name'=> $strMySkills, 'url'=>"{$CONFIG['application_webpath']}edit_user_skills.php"),
                      30=> array ( 'perm'=> 58, 'name'=> $strMySubstitutes, 'url'=>"{$CONFIG['application_webpath']}edit_backup_users.php"),
                      40=> array ( 'perm'=> 27, 'name'=> $strMyHolidays, 'url'=>"{$CONFIG['application_webpath']}holidays.php"),
                      50=> array ( 'perm'=> 4, 'name'=> $strMyDashboard, 'url'=>"{$CONFIG['application_webpath']}manage_user_dashboard.php")
);
// configure
$hmenu[1030] = array (10=> array ( 'perm'=> 22, 'name'=> $strUsers, 'url'=>"{$CONFIG['application_webpath']}manage_users.php", 'submenu'=>"103010"),
                      20=> array ( 'perm'=> 0, 'name'=> $strEmailSettings, 'url'=>"", 'submenu'=>"103020"),
                      30=> array ( 'perm'=> 22, 'name'=> $strSetPublicHolidays, 'url'=>"{$CONFIG['application_webpath']}holiday_calendar.php?type=10"),
                      40=> array ( 'perm'=> 22, 'name'=> $strFTPFilesDB, 'url'=>"{$CONFIG['application_webpath']}ftp_list_files.php"),
                      50=> array ( 'perm'=> 22, 'name'=> $strServiceLevels, 'url'=>"{$CONFIG['application_webpath']}service_levels.php"),
                      60=> array ( 'perm'=> 7, 'name'=> $strBulkModify, 'url'=>"{$CONFIG['application_webpath']}bulk_modify.php?action=external_esc"),
                      70=> array ( 'perm'=> 64, 'name'=> $strEscalationPaths, 'url'=>"{$CONFIG['application_webpath']}escalation_paths.php"),
                      80=> array ( 'perm'=> 66, 'name'=> $strManageDashboardComponents, 'url'=>"{$CONFIG['application_webpath']}manage_dashboard.php"),
                      90=> array ( 'perm'=> 70, 'name'=> $strNotices, 'url'=>"{$CONFIG['application_webpath']}notices.php"),
                      100=> array ( 'perm'=> 49, 'name'=> $strFeedbackForms, 'url'=>"", 'submenu'=>"103090")
);
$hmenu[103010] = array (10=> array ( 'perm'=> 22, 'name'=> $strManageUsers, 'url'=>"{$CONFIG['application_webpath']}manage_users.php"),
                        20=> array ( 'perm'=> 20, 'name'=> $strAddUser, 'url'=>"{$CONFIG['application_webpath']}add_user.php?action=showform"),
                        30=> array ( 'perm'=> 9, 'name'=> $strSetPermissions, 'url'=>"{$CONFIG['application_webpath']}edit_user_permissions.php"),
                        40=> array ( 'perm'=> 23, 'name'=> $strUserGroups, 'url'=>"{$CONFIG['application_webpath']}usergroups.php"),
                        50=> array ( 'perm'=> 22, 'name'=> $strEditHolidayEntitlement, 'url'=>"{$CONFIG['application_webpath']}edit_holidays.php")
);
$hmenu[103020] = array (10=> array ( 'perm'=> 16, 'name'=> $strAddTemplate, 'url'=>"{$CONFIG['application_webpath']}add_emailtype.php?action=showform"),
                        20=> array ( 'perm'=> 17, 'name'=> $strEditTemplate, 'url'=>"{$CONFIG['application_webpath']}edit_emailtype.php?action=showform"),
                        30=> array ( 'perm'=> 43, 'name'=> $strGlobalSignature, 'url'=>"{$CONFIG['application_webpath']}edit_global_signature.php")
);
$hmenu[103090] = array (10=> array ( 'perm'=> 49, 'name'=> $strAddFeedbackForm, 'url'=>"{$CONFIG['application_webpath']}edit_feedback_form.php?action=new"),
                        20=> array ( 'perm'=> 49, 'name'=> $strBrowseFeedbackForms, 'url'=>"{$CONFIG['application_webpath']}browse_feedback_forms.php")
);
$hmenu[1040] = array (10=> array ( 'perm'=> 0, 'name'=> $strViewUsers, 'url'=>"{$CONFIG['application_webpath']}users.php"),
                      20=> array ( 'perm'=> 0, 'name'=> $strListSkills, 'url'=>"{$CONFIG['application_webpath']}user_skills.php"),
                      21=> array ( 'perm'=> 0, 'name'=> $strSkillsMatrix, 'url'=>"{$CONFIG['application_webpath']}skills_matrix.php"),
                      30=> array ( 'perm'=> 27, 'name'=> $strHolidayPlanner, 'url'=>"{$CONFIG['application_webpath']}holiday_calendar.php?display=month"),
                      40=> array ( 'perm'=> 50, 'name'=> $strApproveHolidays, 'url'=>"{$CONFIG['application_webpath']}holiday_request.php?user=all&amp;mode=approval")
);



// Customers
$hmenu[20] = array (10=> array ( 'perm'=> 0, 'name'=> $strSites, 'url'=>"{$CONFIG['application_webpath']}browse_sites.php", 'submenu'=>"2010"),
                    20=> array ( 'perm'=> 0, 'name'=> $strContacts, 'url'=>"{$CONFIG['application_webpath']}browse_contacts.php?search_string=A", 'submenu'=>"2020"),
                    30=> array ( 'perm'=> 0, 'name'=> $strMaintenance, 'url'=>"{$CONFIG['application_webpath']}browse_contract.php?search_string=A", 'submenu'=>"2030"),
                    40=> array ( 'perm'=> 0, 'name'=> $strBrowseFeedback, 'url'=>"{$CONFIG['application_webpath']}browse_feedback.php")
);

$hmenu[2010] = array (10=> array ( 'perm'=> 11, 'name'=> $strBrowse, 'url'=>"{$CONFIG['application_webpath']}browse_sites.php"),
                      20=> array ( 'perm'=> 2, 'name'=> $strNewSite, 'url'=>"{$CONFIG['application_webpath']}add_site.php?action=showform")
);
$hmenu[2020] = array (10=> array ( 'perm'=> 11, 'name'=> $strBrowse, 'url'=>"{$CONFIG['application_webpath']}browse_contacts.php?search_string=A"),
                      20=> array ( 'perm'=> 1, 'name'=> $strNewContact, 'url'=>"{$CONFIG['application_webpath']}add_contact.php?action=showform")
);

$hmenu[2030] = array (10=> array ( 'perm'=> 19, 'name'=> $strBrowse, 'url'=>"{$CONFIG['application_webpath']}browse_contract.php?search_string=A"),
                      20=> array ( 'perm'=> 39, 'name'=> $strNewContract, 'url'=>"{$CONFIG['application_webpath']}add_contract.php?action=showform"),
                      30=> array ( 'perm'=> 21, 'name'=> $strEditContract, 'url'=>"{$CONFIG['application_webpath']}edit_contract.php?action=showform"),
                      40=> array ( 'perm'=> 2, 'name'=> $strNewReseller, 'url'=>"{$CONFIG['application_webpath']}add_reseller.php"),
                      50=> array ( 'perm'=> 19, 'name'=> $strShowRenewals, 'url'=>"{$CONFIG['application_webpath']}search_renewals.php?action=showform"),
                      60=> array ( 'perm'=> 19, 'name'=> $strShowExpired, 'url'=>"{$CONFIG['application_webpath']}search_expired.php?action=showform"),
                      70=> array ( 'perm'=> 0, 'name'=> "{$strProducts} &amp; {$strSkills}", 'url'=>"{$CONFIG['application_webpath']}products.php", 'submenu'=>"203010"),
);

$hmenu[203010] = array (10=> array ( 'perm'=> 56, 'name'=> $strAddVendor, 'url'=>"{$CONFIG['application_webpath']}add_vendor.php"),
                        20=> array ( 'perm'=> 24, 'name'=> $strAddProduct, 'url'=>"{$CONFIG['application_webpath']}add_product.php"),
                        30=> array ( 'perm'=> 28, 'name'=> $strListProducts, 'url'=>"{$CONFIG['application_webpath']}products.php"),
                        35=> array ( 'perm'=> 28, 'name'=> $strListSkills, 'url'=>"{$CONFIG['application_webpath']}products.php?display=skills"),
                        40=> array ( 'perm'=> 56, 'name'=> $strAddSkill, 'url'=>"{$CONFIG['application_webpath']}add_software.php"),
                        50=> array ( 'perm'=> 24, 'name'=> $strLinkProducts, 'url'=>"{$CONFIG['application_webpath']}add_product_software.php"),
                        60=> array ( 'perm'=> 25, 'name'=> $strAddProductQuestion, 'url'=>"{$CONFIG['application_webpath']}add_productinfo.php"),
                        70=> array ('perm'=> 56, 'name'=> $strEditVendor, 'url'=>"{$CONFIG['application_webpath']}edit_vendor.php")
);


// Support
$hmenu[30] = array (10=> array ( 'perm'=> 5, 'name'=> $strAddIncident, 'url'=>"{$CONFIG['application_webpath']}add_incident.php"),
                    20=> array ( 'perm'=> 0, 'name'=> $strViewIncidents, 'url'=>"{$CONFIG['application_webpath']}incidents.php?user=current&amp;queue=1&amp;type=support"),
                    30=> array ( 'perm'=> 0, 'name'=> $strWatchIncidents, 'url'=>"{$CONFIG['application_webpath']}incidents.php?user=all&amp;queue=1&amp;type=support"),
                    40=> array ( 'perm'=> 42, 'name'=> $strHoldingQueue, 'url'=>"{$CONFIG['application_webpath']}review_incoming_updates.php"),
                    50=> array ( 'perm'=> 0, 'name'=> $strJumpToIncident,
                                 'url'=>"javascript:var id = prompt('{$strEnterTheIncidentID}'); if (!isNaN(id)) window.location = '{$CONFIG['application_webpath']}incident_details.php?id=' + id + '&amp;win=jump';")
);


// Tasks
$hmenu[40] = array (10=> array ( 'perm'=> 0, 'name'=> $strAddTask, 'url'=>"{$CONFIG['application_webpath']}add_task.php"),
                    20=> array ( 'perm'=> 0, 'name'=> $strViewTasks, 'url'=>"{$CONFIG['application_webpath']}tasks.php")
);

// KB
$hmenu[50] = array (10=> array ( 'perm'=> 54, 'name'=> $strNewKBArticle, 'url'=>"{$CONFIG['application_webpath']}kb_add_article.php"),
                    20=> array ( 'perm'=> 54, 'name'=> $strBrowse, 'url'=>"{$CONFIG['application_webpath']}browse_kb.php")
);



// Reports
        $hmenu[60] = array (10=> array ( 'perm'=> 37, 'name'=>"{$strMarketingMailshot}",                                                                             'url'=>"{$CONFIG['application_webpath']}reports/marketing.php"),
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
));

$hmenu[70] = array (10=> array ( 'perm'=> 0, 'name'=> "{$strHelpContents}...", 'url'=>"{$CONFIG['application_webpath']}help.php"),
                    20=> array ( 'perm'=> 0, 'name'=> "{$strTranslate}", 'url'=>"{$CONFIG['application_webpath']}translate.php"),
                    30=> array ( 'perm'=> 0, 'name'=> "{$strReportBug}", 'url'=>$CONFIG['bugtracker_url']),
                    40=> array ( 'perm'=> 0, 'name'=> "{$strReleaseNotes}", 'url'=>"{$CONFIG['application_webpath']}releasenotes.php"),
                    50=> array ( 'perm'=> 41, 'name'=> $strHelpAbout, 'url'=>"{$CONFIG['application_webpath']}about.php")
);



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
$updatetypes['externalinfo'] = array('icon' => 'externalinfo.png', 'text' => sprintf($strExternalinfoaddedby,'updateuser'));
$updatetypes['probdef'] = array('icon' => 'probdef.png', 'text' => sprintf($strProblemDefinitionby,'updateuser'));
$updatetypes['research'] = array('icon' => 'research.png', 'text' => sprintf($strResearchedby,'updateuser'));
$updatetypes['reassigning'] = array('icon' => 'reassign.png', 'text' => sprintf($srtReassignedToBy,'currentowner','updateuser'));
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
$slatypes['opened'] = array('icon' => 'open.png', 'text' => sprintf($strOpenedby,'updateuser'));
$slatypes['initialresponse'] = array('icon' => 'initialresponse.png', 'text' => $strInitialResponse);
$slatypes['probdef'] = array('icon' => 'probdef.png', 'text' => $strProblemDefinition);
$slatypes['actionplan'] = array('icon' => 'actionplan.png', 'text' => $strActionPlan);
$slatypes['solution'] = array('icon' => 'solution.png', 'text' => $strSolution);
$slatypes['closed'] = array('icon' => 'close.png', 'text' => $strClosed);


// List of *Available* languages, must match files in includes/i18n
$availablelanguages = array('en-GB' => 'English (British)',
                            'en-US' => 'English (US)',
                            'fr-FR' => 'Français',
                            'lt-LT' => 'Lietuvių',
                            'cy-GB' => 'Welsh'
                           );


?>