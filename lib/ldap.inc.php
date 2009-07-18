<?php
// ldapv2.inc.php - LDAP function library and defines for SiT -Support Incident Tracker
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Lea Anthony <stonk[at]users.sourceforge.net>
//              Paul heaney <paul[at]sitracker.org - heavily modified to support more directories

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}


$ldap_conn = "";

// Defines
define ('LDAP_INVALID_USER',0);
define ('LDAP_USERTYPE_ADMIN',1);
define ('LDAP_USERTYPE_MANAGER',2);
define ('LDAP_USERTYPE_USER',3);
define ('LDAP_USERTYPE_CUSTOMER',4);

// LDAP ATTRIBUTES
/*
$attributes = array("realname","forenames","jobtitle","email","mobile",
                        "surname", "fax","phone");
 */

define ('LDAP_EDIR_SURNAME', 'sn');
define ('LDAP_EDIR_FORENAMES', 'givenName');
define ('LDAP_EDIR_REALNAME', 'fullName');
define ('LDAP_EDIR_JOBTITLE', 'title');
define ('LDAP_EDIR_EMAILADDRESS', 'mail');
define ('LDAP_EDIR_MOBILE', 'mobile');
define ('LDAP_EDIR_TELEPHONE', 'telephoneNumber');
define ('LDAP_EDIR_FAX', 'facsimileTelephoneNumber');
define ('LDAP_EDIR_DESCRIPTION', 'description');
define ('LDAP_EDIR_GRPONUSER', TRUE); // Is group membership contained on the user (more optimal) 
define ('LDAP_EDIR_GRPFULLDN', TRUE); // Is the membership stored as a full DN or just the CN? ONLY Used when checking group
define ('LDAP_EDIR_USERATTRIBUTE', 'cn'); // Attribute to locate user with
define ('LDAP_EDIR_USEROBJECTTYPE', 'inetOrgPerson');
define ('LDAP_EDIR_USERGRPTYPE', 'groupOfNames');
define ('LDAP_EDIR_GRPATTRIBUTE', 'groupMembership');
define ('LDAP_EDIR_ADDRESS1', 'street');
define ('LDAP_EDIR_CITY', 'physicalDeliveryOfficeName');
define ('LDAP_EDIR_COUNTY', 'st'); // State in the US
define ('LDAP_EDIR_POSTCODE', 'postalCode');
define ('LDAP_EDIR_COURTESYTITLE', 'generationQualifier');

define ('LDAP_AD_SURNAME', 'sn');
define ('LDAP_AD_FORENAMES', 'givenName');
define ('LDAP_AD_REALNAME', 'displayName');
define ('LDAP_AD_JOBTITLE', 'title');
define ('LDAP_AD_EMAILADDRESS', 'mail');
define ('LDAP_AD_MOBILE', 'mobile');
define ('LDAP_AD_TELEPHONE', 'telephoneNumber');
define ('LDAP_AD_FAX', 'facsimileTelephoneNumber');
define ('LDAP_AD_DESCRIPTION', 'description');
define ('LDAP_AD_GRPONUSER', TRUE); // Is group membership contained on the user (more optimal) 
define ('LDAP_AD_GRPFULLDN', TRUE); // Is the membership stored as a full DN or just the CN?
define ('LDAP_AD_USERATTRIBUTE', 'sAMAccountName'); // Attribute to locate user with
define ('LDAP_AD_USEROBJECTTYPE', 'user');
define ('LDAP_AD_USERGRPTYPE', 'group');
define ('LDAP_AD_GRPATTRIBUTE', 'memberOf');
define ('LDAP_AD_ADDRESS1', 'streetAddress');
define ('LDAP_AD_CITY', 'l');
define ('LDAP_AD_COUNTY', 'st');
define ('LDAP_AD_POSTCODE', 'postalCode');
define ('LDAP_AD_COURTESYTITLE', 'generationQualifier'); // Doesn't seem to have' 

// TODO check
define ('LDAP_OPENLDAP_SURNAME', 'sn');
define ('LDAP_OPENLDAP_FORENAMES', 'givenName');
define ('LDAP_OPENLDAP_REALNAME', 'cn');
define ('LDAP_OPENLDAP_JOBTITLE', 'title');
define ('LDAP_OPENLDAP_EMAILADDRESS', 'mail');
define ('LDAP_OPENLDAP_MOBILE', 'mobile');
define ('LDAP_OPENLDAP_TELEPHONE', 'telephoneNumber');
define ('LDAP_OPENLDAP_FAX', 'facsimileTelephoneNumber');
define ('LDAP_OPENLDAP_DESCRIPTION', 'description');
define ('LDAP_OPENLDAP_GRPONUSER', FALSE); // Is group membership contained on the user (more optimal) 
define ('LDAP_OPENLDAP_GRPFULLDN', FALSE); // Is the membership stored as a full DN or just the CN?
define ('LDAP_OPENLDAP_USERATTRIBUTE', 'uid'); // Attribute to locate user with
define ('LDAP_OPENLDAP_USEROBJECTTYPE', 'inetOrgPerson');
define ('LDAP_OPENLDAP_USERGRPTYPE', 'posixGroup');
define ('LDAP_OPENLDAP_GRPATTRIBUTE', 'memberUID');
define ('LDAP_OPENLDAP_ADDRESS1', 'postalAddress');
define ('LDAP_OPENLDAP_CITY', 'l');
define ('LDAP_OPENLDAP_COUNTY', 'st'); // NOT PRESENT all in one attribute
define ('LDAP_OPENLDAP_POSTCODE', 'postalCode'); // NOT PRESENT all in one attribute
define ('LDAP_OPENLDAP_COURTESYTITLE', 'personalTitle');

$CONFIG['ldap_type'] = strtoupper($CONFIG['ldap_type']);

if ($CONFIG['use_ldap'] AND $CONFIG['ldap_type'] != 'CUSTOM')
{
    $CONFIG['ldap_surname'] = constant("LDAP_{$CONFIG['ldap_type']}_SURNAME");
    $CONFIG['ldap_forenames'] = constant("LDAP_{$CONFIG['ldap_type']}_FORENAMES");
    $CONFIG['ldap_realname'] = constant("LDAP_{$CONFIG['ldap_type']}_REALNAME");
    $CONFIG['ldap_jobtitle'] = constant("LDAP_{$CONFIG['ldap_type']}_JOBTITLE");
    $CONFIG['ldap_email'] = constant("LDAP_{$CONFIG['ldap_type']}_EMAILADDRESS");
    $CONFIG['ldap_mobile'] = constant("LDAP_{$CONFIG['ldap_type']}_MOBILE");
    $CONFIG['ldap_telephone'] = constant("LDAP_{$CONFIG['ldap_type']}_TELEPHONE");
    $CONFIG['ldap_fax'] = constant("LDAP_{$CONFIG['ldap_type']}_FAX");
    $CONFIG['ldap_description'] = constant("LDAP_{$CONFIG['ldap_type']}_DESCRIPTION");
    $CONFIG['ldap_grponuser'] = constant("LDAP_{$CONFIG['ldap_type']}_GRPONUSER");
    $CONFIG['ldap_grpfulldn'] = constant("LDAP_{$CONFIG['ldap_type']}_GRPFULLDN");
    $CONFIG['ldap_userattribute'] = constant("LDAP_{$CONFIG['ldap_type']}_USERATTRIBUTE");
    $CONFIG['ldap_userobjecttype'] = constant("LDAP_{$CONFIG['ldap_type']}_USEROBJECTTYPE");
    $CONFIG['ldap_grpobjecttype'] = constant("LDAP_{$CONFIG['ldap_type']}_USERGRPTYPE");
    $CONFIG['ldap_grpattribute'] = constant("LDAP_{$CONFIG['ldap_type']}_GRPATTRIBUTE");
    $CONFIG['ldap_address1'] = constant("LDAP_{$CONFIG['ldap_type']}_ADDRESS1");
    $CONFIG['ldap_city'] = constant("LDAP_{$CONFIG['ldap_type']}_CITY");
    $CONFIG['ldap_county'] = constant("LDAP_{$CONFIG['ldap_type']}_COUNTY"); // State in the US
    $CONFIG['ldap_postcode'] = constant("LDAP_{$CONFIG['ldap_type']}_POSTCODE");
    $CONFIG['ldap_courtesytitle'] = constant("LDAP_{$CONFIG['ldap_type']}_COURTESYTITLE");
}
elseif ($CONFIG['use_ldap'])
{
	// Handle custom
    // TODO todo change from hardcoded to DB
    $CONFIG['ldap_surname'] = "surname";
    $CONFIG['ldap_forenames'] = "forenames";
    $CONFIG['ldap_realname'] = "realname";
    $CONFIG['ldap_jobtitle'] = "jobtitle";
    $CONFIG['ldap_email'] = "email";
    $CONFIG['ldap_mobile'] = "mobile";
    $CONFIG['ldap_telephone'] = "phone";
    $CONFIG['ldap_fax'] = "fax";
    $CONFIG['ldap_description'] = "notes";
    $CONFIG['ldap_grponuser'] = false;
    $CONFIG['ldap_grpfulldn'] = false;
    $CONFIG['ldap_userattribute'] = "cn";
    $CONFIG['ldap_userobjecttype'] = "person";
    $CONFIG['ldap_grpobjecttype'] = "group";
    $CONFIG['ldap_grpattribute'] = "member";
    $CONFIG['ldap_address1'] = "address1";
    $CONFIG['ldap_city'] = "city";
    $CONFIG['ldap_county'] = "county"; // State in the US
    $CONFIG['ldap_postcode'] = "postalCode";
    $CONFIG['ldap_courtesytitle'] = "generationQualifier";
}


/**
    * Opens a connection to the LDAP host
    * @author Lea Anthony
    * @return the handle of the opened connection
*/
function ldapOpen($host='', $port='', $protocol='', $tls='', $user='', $password='')
{
    // TODO: Secure binding to host using TLS/SSL
    debug_log("ldapOpen");
    global $CONFIG, $ldap_conn;

    if (empty($host)) $host = $CONFIG['ldap_host'];
    if (empty($port)) $port = $CONFIG['ldap_port'];
    if (empty($protocol)) $protocol = $CONFIG['ldap_protocol'];
    if (empty($tls)) $tls = $CONFIG['ldap_use_tls'];
    if (empty($user)) $user = $CONFIG['ldap_bind_user'];
    if (empty($password)) $password = $CONFIG['ldap_bind_pass'];

    $toReturn = -1;

    $ldap_conn = @ldap_connect($host, $port);


    if ($ldap_conn)
    {
        // Set protocol version
        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, $protocol);
        ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS,0);
    
        if ( $tls )
        {
            // Protocol V3 required for start_tls
            if ( $protocol == 3 )
            {
                if ( !ldap_start_tls($ldap_conn) )
                {
                    trigger_error("Ldap_start_tls failed", E_USER_ERROR);
                }
            }
            else
            {
                trigger_error("LDAP Protocol v3 required for TLS", E_USER_ERROR);
            }
        }
    
        if ( isset($user) && strlen($user) > 0 )
        {
            $r = @ldap_bind($ldap_conn, $user, $password);
            if ( ! $r )
            {
                // Could not bind!
    //             $setupvars['use_ldap'] = FALSE;
    //             cfgSave($setupvars);
                trigger_error("Could not bind to LDAP server with credentials", E_USER_WARNING);
            }
            else
            {
            	$toReturn = $ldap_conn;
            }
        }
    }

    return $toReturn;
}

/**
    * Authenticate a user
    * If successful and the user is new, the user is created in the database
    * If successful and the user is returning, the user record is resynced
    * @author Lea Anthony and Paul Heaney
    * @param string $username. Username
    * @param string $password. Password
    * @param int $id. The userid or contactid, > 0 if you wish to update, else creates new
    * @param bool $user. True for user, false for customer
    * @return mixed, true if sucessful, false if unsucessful or -1 if connection to LDAP server failed
    * @retval 0 the credentials were wrong or the user was not found.
    * @retval 1 to indicate user is authenticated and allowed to continue.
*/
function authenticateLDAP($username, $password, $id = 0, $user=TRUE, $populateOnly=FALSE, $searchOnEmail=FALSE)
{
    debug_log("authenticateLDAP {$username}");

    global $CONFIG;

    $toReturn = false;
    $ldap_conn = ldapOpen();

    if ($ldap_conn != -1)
    {
       /*
        * Search for user DN
        * Authenticate
        * Verify roles
        */
        
        if (!$searchOnEmail)
        {
            $filter = "(&(ObjectClass={$CONFIG['ldap_userobjecttype']})({$CONFIG['ldap_userattribute']}={$username}))";
        }
        else
        {
        	$filter = "(&(ObjectClass={$CONFIG['ldap_userobjecttype']})({$CONFIG['ldap_email']}={$username}))";
        }
        
        $attributes= array ($CONFIG['ldap_surname'], $CONFIG['ldap_forenames'],
                                    $CONFIG['ldap_realname'],$CONFIG['ldap_jobtitle'], 
                                    $CONFIG['ldap_email'], $CONFIG['ldap_mobile'],  $CONFIG['ldap_telephone'],
                                    $CONFIG['ldap_fax'],  $CONFIG['ldap_description'], 
                                    $CONFIG['ldap_address1'], $CONFIG['ldap_city'] , $CONFIG['ldap_county'], 
                                    $CONFIG['ldap_postcode'], $CONFIG['ldap_courtesytitle'], $CONFIG['ldap_userattribute']);
        if ($CONFIG['ldap_grponuser'])
        {
            $attributes[] = $CONFIG['ldap_grpattribute'];
        }
        debug_log ("Filter: {$filter}");
        debug_log ("Base: {$CONFIG['ldap_dn_base']}");
        $sr = ldap_search($ldap_conn, $CONFIG['ldap_dn_base'], $filter, $attributes);
        
        if (ldap_count_entries($ldap_conn, $sr) != 1)
        {
        	// Multiple or zero
            trigger_error("Unable to locate user"); // FIXME i18n
            $toReturn = false;
        }
        else
        {            
        	// just one
            debug_log ("One entry found");
            $first = ldap_first_entry($ldap_conn, $sr);

            $_SESSION['ldap_user_dn'] = ldap_get_dn($ldap_conn, $first);
            $user_attributes = ldap_get_attributes($ldap_conn, $first);

            if ($populateOnly)
            {
            	$user_bind = true; 
            }
            else
            {
                // Authentocate
                $user_bind = @ldap_bind($ldap_conn, $_SESSION['ldap_user_dn'], $password);
            }
            
            if ($searchOnEmail)
            {
            	$username = $user_attributes[$CONFIG['ldap_userattribute']][0];
            }
            
            if (!$user_bind)
            {
            	// Auth failed
                debug_log ("Invalid credentials {$_SESSION['ldap_user_dn']} pwd: '{$password}'");
                $toReturn = false;
            }
            else
            {
            	// Sucessfull
                debug_log ("Valid Credentials");
                $usertype = LDAP_INVALID_USER;

                if ($CONFIG['ldap_grponuser'])
                {
                    if (is_array($user_attributes[$CONFIG['ldap_grpattribute']]))
                    {
                    	// Group stored on user
                        foreach ($user_attributes[$CONFIG['ldap_grpattribute']] AS $group)
                        {
                            if ($user)
                            {
                                // User/Staff
                                // NOTE: we dont have to check about overwriting ADMIN type as we break
                                if (strtolower($group) == strtolower($CONFIG['ldap_admin_group']))
                                {
                                	$usertype = LDAP_USERTYPE_ADMIN;
                                    break;
                                }
                                elseif (strtolower($group) == strtolower($CONFIG['ldap_manager_group']))
                                {
                                	$usertype = LDAP_USERTYPE_MANAGER;
                                }
                                elseif (strtolower($group) == strtolower($CONFIG['ldap_user_group']))
                                {
                                	if ($usertype != LDAP_USERTYPE_MANAGER) $usertype = LDAP_USERTYPE_USER;
                                }
                            }
                            else
                            {
                            	//Customer
                                if (strtolower($group) == strtolower($CONFIG['ldap_customer_group']))
                                {
                                    $usertype = LDAP_USERTYPE_CUSTOMER;
                                    break;
                                }
                            }
                        }
                    }
                }
                else
                {
                	ldap_close($ldap_conn);
                    $ldap_conn = ldapOpen(); // Need to get an admin thread
                    
                    if ($CONFIG['ldap_grpfulldn'])
                    {
                        $filter = "(&(objectClass={$CONFIG['ldap_grpobjecttype']})({$CONFIG['ldap_grpattribute']}={$_SESSION['ldap_user_dn']}))";
                    }
                    else
                    {
                        $filter = "(&(objectClass={$CONFIG['ldap_grpobjecttype']})({$CONFIG['ldap_grpattribute']}={$username}))";
                    }
                    
                    
                    if ($user)
                    {
                        debug_log ("USER: ");
                        /* 
                         * Locate 
                         */
                        if (ldap_count_entries($ldap_conn, ldap_search($ldap_conn, $CONFIG['ldap_admin_group'], $filter)))
                        {
                            $usertype = LDAP_USERTYPE_ADMIN;
                            debug_log ("ADMIN");
                        }
                        elseif (ldap_count_entries($ldap_conn, ldap_search($ldap_conn, $CONFIG['ldap_manager_group'], $filter)))
                        {
                        	$usertype = LDAP_USERTYPE_MANAGER;
                            debug_log ("MANAGER");
                        }
                        elseif (ldap_count_entries($ldap_conn, ldap_search($ldap_conn, $CONFIG['ldap_user_group'], $filter)))
                        {
                        	$usertype = LDAP_USERTYPE_USER;
                            debug_log ("USER");
                        }
                        else
                        {
                        	debug_log ("INVALID");
                        }
                    }
                    else
                    {
                        // get back customer group    
                        $result = ldap_search($ldap_conn, $CONFIG['ldap_customer_group'], $filter);
                        if (ldap_count_entries($ldap_conn, $result))
                        {
                        	$usertype = LDAP_USERTYPE_CUSTOMER;
                            debug_log ("CUSTOMER");
                        }
                        else
                        {
                        	debug_log ("INVALID");
                        }
                    }
                }
                
                if ($usertype != LDAP_INVALID_USER AND $user)
                {
                	// get attributes
                    $user = new User();
                    $user->username = $username;
                    if ($CONFIG['ldap_cache_passwords']) $user->password = $password;
                    $user->realname = $user_attributes[$CONFIG['ldap_realname']][0];
                    $user->jobtitle = $user_attributes[$CONFIG['ldap_jobtitle']][0];
                    $user->email = $user_attributes[$CONFIG['ldap_email']][0];
                    $user->phone = $user_attributes[$CONFIG['ldap_telephone']][0];
                    $user->mobile = $user_attributes[$CONFIG['ldap_mobile']][0];
                    $user->fax = $user_attributes[$CONFIG['ldap_fax']][0];
                    $user->message = $user_attributes[$CONFIG['ldap_description']][0];
                    $user->status = $CONFIG['ldap_default_user_status'];
                    $user->holiday_entitlement = $CONFIG['default_entitlement'];
                    $user->source = 'ldap';
                    
                    // TODO FIXME this doesn't take into account custom roles'
                    switch ($usertype)
                    {
                        case LDAP_USERTYPE_ADMIN: $user->roleid =  1;
                            break;
                        case LDAP_USERTYPE_MANAGER: $user->roleid = 2;
                            break;
                        default: $user->roleid = 3;    
                    }
                    
                    if ($id == 0)
                    {
                        $status = $user->add();
                    }
                    else
                    {
                    	// Modify
                        $user->id = $id;
                        $status = $user->update();
                    }
                    
                    if ($status) $toReturn = true;
                    else $toReturn = false;
                }
                elseif ($usertype == LDAP_USERTYPE_CUSTOMER AND !$user)
                {
                    // Contact	
                    debug_log("Adding contact TYPE {$usertype} {$user}");
                    $contact = new Contact();
                    $contact->username = $username;
                    if ($CONFIG['ldap_cache_passwords']) $contact->password = $password;
                    $contact->surname = $user_attributes[$CONFIG['ldap_surname']][0];
                    $contact->forenames = $user_attributes[$CONFIG['ldap_forenames']][0];
                    $contact->jobtitle = $user_attributes[$CONFIG['ldap_jobtitle']][0];
                    $contact->email = $user_attributes[$CONFIG['ldap_email']][0];
                    $contact->phone = $user_attributes[$CONFIG['ldap_telephone']][0];
                    $contact->mobile = $user_attributes[$CONFIG['ldap_mobile']][0];
                    $contact->fax = $user_attributes[$CONFIG['ldap_fax']][0];
                    $contact->siteid = $CONFIG['ldap_default_customer_siteid'];
                    $contact->address1 = $user_attributes[$CONFIG['ldap_address1']][0];
                    $contact->city = $user_attributes[$CONFIG['ldap_city']][0];
                    $contact->county = $user_attributes[$CONFIG['ldap_county']][0];
                    $contact->postcode = $user_attributes[$CONFIG['ldap_postcode']][0];
                    $contact->courtesytitle = $user_attributes[$CONFIG['ldap_courtesytitle']][0];
                    
                    $contact->source = 'ldap';
                
                    if ($id == 0)
                    {
                    	$status = $contact->add();
                    }
                    else
                    {
                        debug_log ("MODIFY CONTACT {$id}");
                    	$contact->id = $id;
                        $status = $contact->update();
                    }
                    
                    if ($status)  $toReturn = true;
                    else $toReturn = false;
                }
                else
                {
                	$toReturn = false;
                }
                
                ldap_close($ldap_conn);
            }
        }
    }
    else
    {
    	$toReturn = -1;
    }
    
    return $toReturn;
}

/**
    * Gets the details of a contact from the database from their email
    * @author Lea Anthony
    * @param string $email. Email
*/
function getContactDetailsFromDBByEmail($email)
{
    global $dbContacts;

    $sql = "SELECT * FROM `{$dbContacts}` WHERE email='$email'";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    return mysql_fetch_array($result);

}

/**
    * Checks that the email address given is a contact that has not yet
    * been imported into the DB, then imports them.
    * @author Lea Anthony
    * @param string $email. Email
    * @return An array of the user data (if found)
*/
function ldapImportCustomerFromEmail($email)
{
    global $CONFIG;
    $toReturn = false;
    /*
    global $dbContacts;

    $r = getContactDetailsFromDBByEmail($email);

    if( ! empty($r) )
    {
        // This contact already exists
        return;
    }

    // Create user
    $details = ldapGetCustomerDetailsFromEmail(email);


    ldapCreateContact($details);
    */
    
    /*
     * Check if contact exists
     * is contact sit
     *   return
     * if ldap enabled
     *   is contact ldap
     *     sync
     *   else
     *     try and find in LDAP
     * 
     */
     debug_log ("ldapImportCustomerFromEmail {$email}");
     if (!empty($email))
     {
        $sql = "SELECT id, username, contact_source FROM `{$GLOBALS['dbContacts']}` WHERE email = '{$email}'";
        debug_log ($sql);
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        if (mysql_num_rows($result) == 1)
        {
            debug_log ("just one");
        	// Can only deal with the case where one exists, if multiple contacts have the same email address its difficult to deal with
            $obj = mysql_fetch_object($result);
            
            if ($obj->contact_source == 'sit')
            {
            	$toReturn = true;
            }
            elseif ($obj->contact_source == 'ldap')
            {
            	if (authenticateLDAP($obj->username, '', $obj->id, false, true, false)) $toReturn = true;
            }
            else
            {
            	// Exists but of some other type
                $toReturn = true;
            }
        }
        elseif (mysql_num_rows($result) > 1)
        {
            debug_log ("More than one");
            // Contact does exists with these details, just theres more than one of them
        	$toReturn = true;
        }
        else
        {
            debug_log ("Zero");
        	// Zero found
            if ($CONFIG['use_ldap'])
            {
            	// Try and search
                if (authenticateLDAP($email, '', 0, false, true, true)) $toReturn = true;
            } 
        }
     }
     
     return $toReturn;
}

?>
