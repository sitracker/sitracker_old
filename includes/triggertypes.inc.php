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


$triggerarray['TRIGGER_INCIDENT_CREATED'] =
array('name' => 'Incident Created',
      'description' => 'Occurs when a new incident has been created',
      'required' => array('incidentid'),
      'params' => array('contactid', 'siteid', 'priority', 'contractid', 'slaid', 'sitesalespersonid'),
      );

$triggerarray['TRIGGER_INCIDENT_ASSIGNED'] =
array('name' => 'Incident Assigned',
      'description' => 'Occurs when a new incident is assigned to you',
      'required' => array('incidentid', 'userid'),
      'params' => array('userid'),
      );

$triggerarray['TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY'] =
array('name' => 'Incident Assigned While Away',
      'description' => 'Occurs when a new incident is assigned to you and you are set to not accepting',
      'required' => array('incidentid', 'userid'),
      'params' => array(),
     );

$triggerarray['TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE'] =
array('name' => 'Incident Assigned While Offline',
      'description' => 'Occurs when a new incident is assigned to you and your status is offline',
      'required' => array('incidentid', 'userid'),
      'params' => array(),
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
      'params' => array(),
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
      'required' => array('maintid'),
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
      
?>