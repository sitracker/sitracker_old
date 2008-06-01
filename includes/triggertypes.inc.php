<?php
// triggertypes.inc.php - Create the trigger definitions
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net>


// Define a list of available triggers, trigger() will need to be called in the appropriate
// place in the code for each of these
//
// id - trigger name
// description - when the trigger is fired
// required - parameters the triggers needs to fire, 'provides' these to templates
// params - Rules the trigger can check, mimics 'subscription'-type events
// type - Trigger type (eg. incident, contact etc)

$triggerarray['TRIGGER_CONTACT_RESET_PASSWORD'] =
array('name' => 'Contact reset password',
      'description' => 'strTriggerContactResetPasswordDesc',
      'required' => array('contactid', 'passwordreseturl'),
      'type' => 'system'
      );

$triggerarray['TRIGGER_HOLIDAY_REQUESTED'] =
array('name' => 'Holiday Requested',
      'description' => 'strTriggerHolidayRequestedDesc',
      'required' => array('userid', 'approvaluseremail', 'listofholidays'),
      'permission' => 'user_permission($_SESSION[\'userid\'], 50);'
      );

$triggerarray['TRIGGER_INCIDENT_ASSIGNED'] =
array('name' => 'Incident Assigned',
      'description' => 'strTriggerNewIncidentAssignedDesc',
      'required' => array('incidentid', 'userid'),
      'params' => array('userid', 'userstatus'),
      );

$triggerarray['TRIGGER_INCIDENT_CLOSED'] =
array('name' => 'Incident closed',
      'description' => 'strTriggerIncidentClosedDesc',
      'required' => array('incidentid', 'userid'),
      'params' => array('userid', 'externalid', 'externalengineer')
      );

$triggerarray['TRIGGER_INCIDENT_CREATED'] =
array('name' => 'Incident Created',
      'description' => 'strTriggerNewIncidentCreatedDesc',
      'required' => array('incidentid'),
      'params' => array('contactid', 'siteid', 'priority', 'contractid', 'slaid', 'sitesalespersonid')
      );

$triggerarray['TRIGGER_INCIDENT_NEARING_SLA'] =
array('name' => 'Incident Nearing SLA',
      'description' => 'strTriggerIncidentNearingSLADesc',
      'required' => array('incidentid', 'nextslatime', 'nextsla'),
      'params' => array('ownerid', 'townerid'),
      );

$triggerarray['TRIGGER_INCIDENT_REVIEW_DUE'] =
array('name' => 'Incident Review Due',
      'description' => 'strTriggerIncidentReviewDueDesc',
      'required' => array('incidentid', 'time'),
      'params' => array('incidentid'),
      );

$triggerarray['TRIGGER_KB_CREATED'] =
array('name' => 'Knowledgebase Article Created',
      'description' => 'strTriggerKBArticleCreatedDesc',
      'required' => array('kbid', 'userid'),
      'params' => array('userid'),
      );

$triggerarray['TRIGGER_LANGUAGE_DIFFERS'] =
array('name' => 'Current Language Differs',
      'description' => 'strTriggerLanguageDiffersDesc',
      'required' => array('currentlang', 'profilelang'),
      'params' => array(),
     );

$triggerarray['TRIGGER_NEW_CONTACT'] =
array('name' => 'New contact added',
      'description' => 'strTriggerNewContactDesc',
      'required' => array('contactid', 'prepassword', 'userid'),
      'params' => array('siteid')
      );

$triggerarray['TRIGGER_NEW_CONTRACT'] =
array('name' => 'New contract added',
      'description' => 'strTriggerNewContractDesc',
      'required' => array('contractid'),
      'params' => array('productid', 'slaid')
      );

$triggerarray['TRIGGER_NEW_HELD_EMAIL'] =
array('name' => 'New Held Email',
      'description' => 'strTriggerNewHeldEmailDesc',
      'required' => array('holdingemailid'),
      'params' => array(),
      );

$triggerarray['TRIGGER_NEW_SITE'] =
array('name' => 'New site added',
      'description' => 'strTriggerNewSiteDesc',
      'required' => array('siteid')
      );

$triggerarray['TRIGGER_NEW_USER'] =
array('name' => 'New user added',
      'description' => 'strTriggerNewUserDesc',
      'required' => array('userid')
      );

$triggerarray['TRIGGER_SIT_UPGRADED'] =
array('name' => 'SiT! Upgraded',
      'description' => 'strTriggerSitUpgradedDesc',
      'required' => array('applicationversion'),
      'params' => array(),
      );

$triggerarray['TRIGGER_USER_CHANGED_STATUS'] =
array('name' => 'User Changes Status',
      'description' => 'strTriggerUserChangedStatusDesc',
      'required' => array('userid'),
      'params' => array('userid', 'userstatus', 'useraccepting'),
      );

$triggerarray['TRIGGER_USER_RESET_PASSWORD'] =
array('name' => 'User reset password',
      'description' => 'strTriggerUserResetPasswordDesc',
      'required' => array('userid', 'passwordreseturl'),
      'type' => 'system'
      );

$triggerarray['TRIGGER_WAITING_HELD_EMAIL'] =
array('name' => 'Waiting Held Email',
      'description' => 'strTriggerNewHeldEmailMinsDesc',
      'required' => array('holdingmins'),
      'params' => array(),
      );

plugin_do('trigger_types');


/**
    * Template variables (Alphabetical order)
    * description - Friendly label
    * replacement - Quoted PHP code to be run to perform the template var replacement
    * requires -Optional field. single string or array. Specifies the 'required' params from the trigger that is needed for this replacement
    * action - Optional field, when set the var will only be available for that action
    * type - Optional field, defines where a variable can be used, system, incident or user
*/
$ttvararray['{applicationname}'] =
array('description' => $CONFIG['application_name'],
      'replacement' => '$CONFIG[\'application_name\'];'
      );

$ttvararray['{applicationpath}'] =
array('description' => 'System base path',
      'replacement' => '$CONFIG[\'application_webpath\'];'
      );

$ttvararray['{applicationshortname}'] =
array('description' => $CONFIG['application_shortname'],
      'replacement' => '$CONFIG[\'application_shortname\'];'
      );

$ttvararray['{applicationurl}'] =
array('description' => 'System URL',
      'replacement' => 'application_url();'
      );

$ttvararray['{applicationversion}'] =
array('description' => $application_version_string,
      'replacement' => 'application_version_string();'
      );

$ttvararray['{approvaluseremail}'] =
array('description' => 'Email address of the holiday approver',
      'replacement' => '$paramarray[\'approvaluseremail\'];',
      'requires' => 'approvaluseremail'
      );

$ttvararray['{contactid}'][] =
array('description' => 'Contact ID',
      'requires' => 'incidentid',
      'replacement' => 'incident_contact($paramarray[\'incidentid\']);'
      );

$ttvararray['{contactemail}'][] =
array('description' => $strIncidentsContactEmail,
      'requires' => 'contactid',
      'replacement' => 'contact_email($paramarray[\'contactid\']);',
      'action' => 'ACTION_EMAIL'
      );

$ttvararray['{contactemail}'][] =
array('description' => $strIncidentsContactEmail,
      'requires' => 'incidentid',
      'replacement' => 'contact_email(incident_contact($paramarray[\'incidentid\']));',
      'action' => 'ACTION_EMAIL'
      );

$ttvararray['{contactfirstname}'][] =
array('description' => 'First Name of contact',
      'requires' => 'contactid',
      'replacement' => 'strtok(contact_realname($paramarray[\'contactid\'])," ");'
      );

$ttvararray['{contactfirstname}'][] =
array('description' => 'First Name of contact',
      'requires' => 'incidentid',
      'replacement' => 'strtok(contact_realname(incident_contact($paramarray[\'incidentid\']))," ");'
      );

$ttvararray['{contactid}'][] =
array('description' => 'Contact ID',
      'requires' => 'contactid',
      'replacement' => '$paramarray[\'contactid\'];'
      );

$ttvararray['{contactname}'][] =
array('description' => 'Full Name of contact',
      'requires' => 'contactid',
      'replacement' => 'contact_realname($paramarray[\'contactid\']);'
      );

$ttvararray['{contactname}'][] =
array('description' => 'Full Name of contact',
      'requires' => 'incidentid',
      'replacement' => 'contact_realname(incident_contact($paramarray[\'incidentid\']));'
      );

$ttvararray['{contactnotify}'] =
array('description' => 'The Notify Contact email address (if set)',
      'requires' => 'contactid',
      'replacement' => 'contact_notify_email($paramarray[\'contactid\']);'
      );

$ttvararray['{contactphone}'] =
array('description' => 'Contact phone number',
      'requires' => 'contactid',
      'replacement' => 'contact_site($paramarray[\'contactid\']);'
      );

$ttvararray['{contactusername}'] =
array('description' => 'The portal username of a contact',
      'requires' => 'contactid',
      'replacement' => 'contact_username($paramarray[\'contactid\']);'
      );

$ttvararray['{contractid}'] =
array('description' => 'Contact ID',
      'requires' => 'contractid',
      'replacement' => '$paramarray[\'contractid\'];'
      );

$ttvararray['{contractproduct}'] =
array('description' => 'Contact Product',
      'replacement' => 'contract_product($paramarray[\'contractid\']);',
      'requires' => 'contractid'
      );

$ttvararray['{contractsla}'] =
array('description' => 'SLA of the maintenance',
      'replacement' => 'maintenance_servicelevel($paramarray[\'contractid\']);',
      'requires' => 'contractid'
      );

$ttvararray['{currentlang}'] =
array('description' => 'The language the user has selected to login using',
      'replacement' => '$paramarray[\'currentlang\'];',
      'requires' => 'currentlang'
      );

$ttvararray['{feedbackurl}'] =
array('description' => 'Feedback URL',
      'requires' => 'incidentid',
      'replacement' => '$baseurl.\'feedback.php?ax=\'.urlencode(trim(base64_encode(gzcompress(str_rot13(urlencode($CONFIG[\'feedback_form\']).\'&&\'.urlencode($contactid).\'&&\'.urlencode($incidentid))))));'
      );

$ttvararray['{globalsignature}'] =
array('description' => $strGlobalSignature,
      'replacement' => 'global_signature();'
      );

$ttvararray['{holdingemailid}'] =
array('description' => 'ID of the new email in the holding queue',
      'replacement' => '$paramarray[\'holdingemailid\'];',
      'requires' => 'holdingemailid'
      );

$ttvararray['{holdingmins}'] =
array('description' => 'Number of minutes the email has been in the holding queue',
      'replacement' => '$paramarray[\'holdingmins\'];',
      'requires' => 'holdingmins'
      );

$ttvararray['{incidentccemail}'] =
array('description' => $strIncidentCCList,
      'requires' => 'incidentid',
      'replacement' => 'incident_ccemail($paramarray[\'incidentid\']);'
      );

$ttvararray['{incidentexternalemail}'] =
array('description' => $strExternalEngineerEmail,
      'requires' => 'incidentid',
      'replacement' => 'incident_externalemail($paramarray[incidentid]);'
      );

$ttvararray['{incidentexternalengineer}'] =
array('description' => $strExternalEngineer,
      'requires' => 'incidentid',
      'replacement' => 'incident_externalengineer($paramarray[incidentid]);'
      );

$ttvararray['{incidentexternalengineerfirstname}'] =
array('description' => $strExternalEngineersFirstName,
      'requires' => 'incidentid',
      'replacement' => 'strtok(incident_externalengineer($paramarray[\'incidentid\']), " ");'
      );

$ttvararray['{incidentexternalid}'] =
array('description' => "{$GLOBALS['strExternalID']}",
      'requires' => 'incidentid',
      'replacement' => 'incident_externalid($paramarray[\'incidentid\']);'
      );

$ttvararray['{incidentid}'] =
array('description' => $GLOBALS['strIncidentID'],
      'requires' => 'incidentid',
      'replacement' => '$paramarray[\'incidentid\'];'
      );

$ttvararray['{incidentowner}'] =
array('description' => $strIncidentOwnersFullName,
      'requires' => 'incidentid',
      'replacement' => 'user_realname(incident_owner($paramarray[incidentid]));'
      );

$ttvararray['{incidentowneremail}'] =
array('description' => 'Incident Owners Email Address',
      'requires' => 'incidentid',
      'replacement' => 'user_email(incident_owner($paramarray[incidentid]));'
      );

$ttvararray['{incidentpriority}'] =
array('description' => $strIncidentPriority,
      'requires' => 'incidentid',
      'replacement' => 'priority_name(incident_priority($paramarray[incidentid]));'
      );

$ttvararray['{incidentsoftware}'] =
array('description' => $strSkillAssignedToIncident,
      'requires' => 'incidentid',
      'replacement' => 'software_name(db_read_column(\'softwareid\', $GLOBALS[\'dbIncidents\'], $paramarray[\'incidentid\']));'
      );

$ttvararray['{incidenttitle}'] =
array('description' => $strIncidentTitle,
      'requires' => 'incidentid',
      'replacement' => 'incident_title($paramarray[incidentid]);'
      );

$ttvararray['{kbid}'] =
array('description' => 'KB ID',
      'requires' => 'kbid',
      'replacement' => '$paramarray[\'kbid\'];'
      );

$ttvararray['{kbprefix}'] =
array('description' => $CONFIG['kb_id_prefix'],
      'requires' => array(),
      'replacement' => '$CONFIG[\'kb_id_prefix\'];'
     );

$ttvararray['{kbtitle}'] =
array('description' => $strKnowledgeBase,
      'requires' => 'kbid',
      'replacement' => 'kb_name($paramarray[\'kbid\']);'
      );

$ttvararray['{listofholidays}'] =
array('description' => 'List of holidays',
      'replacement' => '$paramarray[\'listofholidays\'];',
      'requires' => 'listofholidays'
      );

$ttvararray['{nextslatime}'] =
array('description' => $strTimeToNextAction,
      'replacement' => 'format_workday_minutes($GLOBALS[\'now\'] - $paramarray[\'nextslatime\']);',
      'requires' => 'nextslatime'
      );

$ttvararray['{nextsla}'] =
array('description' => 'Next SLA name',
      'replacement' => '$paramarray[\'nextsla\'];',
      'requires' => 'nextsla'
      );

$ttvararray['{ownerid}'] =
array('description' => 'Incident owner ID',
      'replacement' => 'incident_owner($paramarray[\'incidentid\']);',
      'requires' => 'incidentid'
      );

$ttvararray['{passwordreseturl}'] =
array('description' => 'Hashed URL to reset a password',
      'replacement' => '$paramarray[\'passwordreseturl\'];',
      'requires' => 'passwordreseturl',
      'type' => 'system'
      );

$ttvararray['{prepassword}'] =
array('description' => 'The plaintext contact password',
      'replacement' => '$paramarray[\'prepassword\'];',
      'requires' => 'prepassword',
      'type' => 'system'
      );

$ttvararray['{profilelang}'] =
array('description' => 'The language the user has stored in their profile',
      'replacement' => '$paramarray[\'profilelang\'];',
      'requires' => 'profilelang'
      );

$ttvararray['{salesperson}'] =
array('description' => 'Salesperson',
      'requires' => 'siteid',
      'replacement' => 'user_realname(db_read_column(\'owner\', $GLOBALS[\'dbSites\'], $siteid));'
      );

$ttvararray['{salespersonemail}'] =
array('description' => $strSalespersonAssignedToContactsSiteEmail,
      'requires' => 'siteid',
      'replacement' => 'user_email(db_read_column(\'owner\', $GLOBALS[\'dbSites\'], $siteid));'
      );

$ttvararray['{signature}'] =
array('description' => $strCurrentUsersSignature,
      'replacement' => 'user_signature($_SESSION[\'userid\']);'
      );

$ttvararray['{siteid}'] =
array('description' => 'Site name',
      'requires' => 'siteid',
      'replacement' => '$paramarray[\'siteid\'];'
      );

$ttvararray['{sitename}'][] =
array('description' => 'Site name',
      'requires' => 'incidentid',
      'replacement' => 'contact_site(incident_contact($paramarray[\'incidentid\']));'
      );

$ttvararray['{sitename}'][] =
array('description' => 'Site name',
      'requires' => 'contactid',
      'replacement' => 'contact_site($paramarray[\'contactid\']);'
      );

$ttvararray['{sitename}'][] =
array('description' => 'Site name',
      'requires' => 'contractid',
      'replacement' => 'contract_site($paramarray[\'contractid\']);'
      );

$ttvararray['{sitename}'][] =
array('description' => 'Site name',
      'requires' => 'siteid',
      'replacement' => 'site_name($paramarray[\'siteid\']);'
      );

$ttvararray['{sitesalespersonid}'] =
array('description' => 'The ID of the site\'s salesperson',
      'replacement' => 'site_salespersonid($paramarray[\'siteid\']);',
      'requires' => 'siteid'
      );

$ttvararray['{sitesalesperson}'] =
array('description' => 'The name of the site\'s salesperson',
      'replacement' => 'site_salesperson($paramarray[\'siteid\']);',
      'requires' => 'siteid'
      );

$ttvararray['{slaid}'] =
array('description' => 'ID of the SLA',
      'replacement' => 'contract_slaid($paramarray[\'contractid\']);',
      'requires' => 'contractid'
      );

$ttvararray['{slatag}'] =
array('description' => 'The SLA tag',
      'replacement' => 'servicelevel_id2tag(contract_slaid($paramarray[\'contractid\']));',
      'requires' => 'contractid'
      );

$ttvararray['{supportemail}'] =
array('description' => $strSupportEmailAddress,
      'replacement' => '$CONFIG[\'support_email\'];'
      );

$ttvararray['{supportmanageremail}'] =
array('description' => $strSupportManagersEmailAddress,
      'replacement' => '$CONFIG[\'support_manager_email\'];'
      );

$ttvararray['{todaysdate}'] =
array('description' => $strCurrentDate,
      'replacement' => 'ldate("jS F Y");'
      );

$ttvararray['{townerid}'] =
array('description' => 'Incident temp owner ID',
      'replacement' => 'incident_towner($paramarray[\'incidentid\']);',
      'requires' => 'incidentid'
      );

$$ttvararray['{triggersfooter}'] =
array('description' => 'The footer at the end of an email which explains where it has come from',
      'replacement' => '$SYSLANG[\'strTriggerFooter\'];',
      'requires' => 'triggersfooter'
     );

$ttvararray['{triggeruseremail}'] =
array('description' => 'Email address to send an user trigger email to',
      'replacement' => 'user_email($paramarray[\'triggeruserid\']);'
      );

$ttvararray['{useraccepting}'] =
array('description' => 'Whether the user is accepting or not',
      'replacement' => 'user_accepting_status($paramarray[\'userid\']);',
      'requires' => 'userid'
      );

$ttvararray['{useremail}'] =
array('description' => $strCurrentUserEmailAddress,
      'replacement' => 'user_email($paramarray[\'userid\']);'
      );

$ttvararray['{userid}'] =
array('description' => 'UserID the trigger passes',
      'replacement' => '$paramarray[\'userid\'];'
      );

$ttvararray['{userrealname}'] =
array('description' => $strFullNameCurrentUser,
      'replacement' => 'user_realname($paramarray[\'userid\']);'
      );

$ttvararray['{userstatus}'] =
array('description' => 'Status of the user',
      'replacement' => 'user_status_name($paramarray[\'userid\']);',
      'requires' => 'userid'
      );

plugin_do('trigger_variables');
?>
