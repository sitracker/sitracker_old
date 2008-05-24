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

//used for replacing email templates in incidents
$triggerarray['TRIGGER_DUMMY_INCIDENT'] =
array('required' => array('incidentid'),
      'type' => 'dummy');

$triggerarray['TRIGGER_INCIDENT_CREATED'] =
array('name' => 'Incident Created',
      'description' => 'Occurs when a new incident has been created',
      'required' => array('incidentid'),
      'params' => array('contactid', 'siteid', 'priority', 'contractid', 'slaid', 'sitesalespersonid')
      );

$triggerarray['TRIGGER_INCIDENT_ASSIGNED'] =
array('name' => 'Incident Assigned',
      'description' => 'Occurs when a new incident is assigned to a user',
      'required' => array('incidentid', 'userid'),
      'params' => array('userid'),
      );

$triggerarray['TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY'] =
array('name' => 'Incident Assigned While Away',
      'description' => 'Occurs when a new incident is assigned to a user and the user is set to not accepting',
      'required' => array('incidentid', 'userid'),
      'params' => array('userid'),
     );

$triggerarray['TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE'] =
array('name' => 'Incident Assigned While Offline',
      'description' => 'Occurs when a new incident is assigned to a user and their status is offline',
      'required' => array('incidentid', 'userid'),
      'params' => array('userid'),
      );

$triggerarray['TRIGGER_INCIDENT_NEARING_SLA'] =
array('name' => 'Incident Nearing SLA',
      'description' => 'Occurs when an incidents nears an SLA',
      'required' => array('incidentid', 'nextslatime', 'nextsla'),
      'params' => array('ownerid', 'townerid'),
      );

$triggerarray['TRIGGER_INCIDENT_REVIEW_DUE'] =
array('name' => 'Incident Review Due',
      'description' => 'Occurs when an incident is due a review',
      'required' => array('revieweruserid'),
      'params' => array(),
      );

$triggerarray['TRIGGER_KB_CREATED'] =
array('name' => 'Knowledgebase Article Created',
      'description' => 'Occurs when a new Knowledgebase article is created',
      'required' => array('kbid', 'userid'),
      'params' => array(),
      );

$triggerarray['TRIGGER_NEW_HELD_EMAIL'] =
array('name' => 'New Held Email',
      'description' => 'Occurs when there is a new email in the holding queue',
      'required' => array('holdingemailid'),
      'params' => array(),
      );

$triggerarray['TRIGGER_WAITING_HELD_EMAIL'] =
array('name' => 'Waiting Held Email',
      'description' => 'Occurs when there is a new email in the holding queue for x minutes',
      'required' => array('holdingmins'),
      'params' => array(),
      );

$triggerarray['TRIGGER_USER_SET_TO_AWAY'] =
array('name' => 'User Set To Away',
      'description' => 'Occurs when one of your watched engineer goes away',
      'required' => array('userid'),
      'params' => array(),
      );
$triggerarray['TRIGGER_USER_RETURNS'] =
array('name' => 'User Returns',
      'description' => 'Occurs when one of your watched engineers returns',
      'required' => array('userid'),
      'params' => array(),
      );

$triggerarray['TRIGGER_SIT_UPGRADED'] =
array('name' => 'SiT! Upgraded',
      'description' => 'Occurs when the system is upgraded',
      'required' => array('sitversion'),
      'params' => array(),
      );

$triggerarray['TRIGGER_LANGUAGE_DIFFERS'] =
array('name' => 'Current Language Differs',
      'description' => 'Occurs when your current language setting is different to your profile setting',
      'required' => array('currentlang', 'profilelang'),
      'params' => array()
     );

$triggerarray['TRIGGER_CONTACT_RESET_PASSWORD'] =
array('name' => 'Contact reset password',
      'description' => 'Occurs when a contact wants their password resetting',
      'required' => array('contactid', 'passwordreseturl'),
      );

$triggerarray['TRIGGER_USER_RESET_PASSWORD'] =
array('name' => 'User reset password',
      'description' => 'Occurs when a user wants their password resetting',
      'required' => array('userid', 'passwordreseturl'),
      );

$triggerarray['TRIGGER_NEW_CONTACT'] =
array('name' => 'New contact added',
      'description' => 'Occurs when a new contact is added',
      'required' => array('contactid', 'prepassword'),
      );

$triggerarray['TRIGGER_INCIDENT_CLOSED'] =
array('name' => 'New contact added',
      'description' => 'Occurs when an incident is closed',
      'required' => array('incidentid'),
      );

$triggerarray['TRIGGER_INCIDENT_CLOSED'] =
array('name' => 'Incident closed',
      'description' => 'Occurs when an incident is closed',
      'required' => array('incidentid', 'userid'),
      'params' => array('userid'),
      );

$triggerarray['TRIGGER_CONTACT_ADDED'] =
array('name' => 'Contact added',
      'description' => 'Occurs when an new contact is added',
      'required' => array('contactid', 'userid'),
      );

$triggerarray['TRIGGER_NEW_CONTRACT'] =
array('name' => 'New contract added',
      'description' => 'Occurs when a new contract is added',
      'required' => array('contractid'),
      'params' => array('productid', 'slaid')
      );

$triggerarray['TRIGGER_NEW_USER'] = 
array('name' => 'New user added',
      'description' => 'Occurs when a new user is added',
      'required' => array('userid')
      );

$triggerarray['TRIGGER_NEW_SITE'] = 
array('name' => 'New site added',
      'description' => 'Occurs when a new site is added',
      'required' => array('siteid')
      );
      
$triggerarray['TRIGGER_CLOSE_EXTERNAL_INCIDENT'] =
array('name' => 'Close external incident',
      'description' => 'Occurs when an incident with an external ID is closed',
      'required' => 'incidentid',
      'params' => 'escalation',
      );

$triggerarray['TRIGGER_HOLIDAY_REQUESTED'] =
array('name' => 'Holiday Requested',
      'description' => 'Occurs when a user requests a holiday',
      'params' => array('userid', 'approvaluserid')
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
      'requires' => 'incidentid',
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

$ttvararray['{contractid}'] =
array('description' => 'Contact ID',
      'requires' => 'contractid',
      'replacement' => '$paramarray[\'contractid\']);'
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
      'replacement' => 'strtok(incident_externalengineer($paramarray[incidentid]),\' \');'
      );

$ttvararray['{incidentexternalid}'] =
array('description' => "{$GLOBALS['strExternalID']}",
      'requires' => 'incidentid',
      'replacement' => '$incident->externalid;'
      );

$ttvararray['{incidentfirstupdate}'] =
array('description' => $strFirstCustomerVisibleUpdate,
      'replacement' => ''
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
      'replacement' => 'software_name(db_read_column("softwareid", $GLOBALS["dbIncidents"], $paramarray[incidentid]));'
      );

$ttvararray['{incidenttitle}'] =
array('description' => $strIncidentTitle,
      'requires' => 'incidentid',
      'replacement' => 'incident_title($paramarray[incidentid]);'
      );

$ttvararray['{kbname}'] =
array('description' => $strKnowledgeBase,
      'requires' => 'kbid',
      'replacement' => 'kb_name($paramarray[\'kbid\']);'
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
      
$ttvararray['{useremail}'] =
array('description' => $strCurrentUserEmailAddress,
      'replacement' => 'user_email($paramarray[\'userid\']);'
      );

$ttvararray['{userrealname}'] =
array('description' => $strFullNameCurrentUser,
      'replacement' => 'user_realname($paramarray[\'userid\']);'
      );

$ttvararray['{userid}'] =
array('description' => 'UserID the trigger passes',
      'replacement' => '$paramarray[\'userid\'];'
      );
      
plugin_do('trigger_variables');
?>