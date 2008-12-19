<?php
// ldap.inc.php - LDAP function library and defines for SiT -Support Incident Tracker
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Lea Anthony <stonk[at]users.sourceforge.net>

$ldap_conn = "";

// Defines
define ('LDAP_INVALID_USER',0);
define ('LDAP_USERTYPE_ADMIN',1);
define ('LDAP_USERTYPE_MANAGER',2);
define ('LDAP_USERTYPE_USER',3);
define ('LDAP_USERTYPE_CUSTOMER',4);

// Time settings
$now = time();


/**
    * Authenticate a customer.
    * If successful and the customer is new, the customer is created in the database
    * If successful and the customer is returning, the customer record is resynced
    * @author Lea Anthony
    * @param $username String. Username
    * @param $password String. Password
    * @return an integer to indicate whether the user authenticated against the LDAP backend
    * @retval 0 the credentials were wrong or the user was not found.
    * @retval 1 to indicate user is authenticated and allowed to continue.
*/
function authenticateLDAPCustomer($username, $password)
{
    global $CONFIG;

    // If the user/password are not valid then return gracefully
    if( ! ldapUserPassValid($username,$password) ) return 0;

    // Get user type - if it's not a customer then return
    if( ldapGetUserType($username) != LDAP_USERTYPE_CUSTOMER ) return 0;

    // Get User Details
    $details = ldapGetUserDetails($username);

    // Customer
    $details["department"] = "";
    $details["siteid"] = $CONFIG["ldap_default_customer_siteid"];
    $details["address1"] = "";
    $details["md5password"] = md5($password);

    if( customerExistsInDB($username) )
    {
        ldapUpdateContact($details);
    }
    else
    {
        ldapCreateContact($details);
    }
}

/**
    * Creates the Contact Record in the database
    * @author Lea Anthony
    * @param $details Array. The details of the user
*/
function ldapCreateContact($details)
{
    debug_log("LDAP CreateContact $details");
    global $CONFIG, $dbContacts, $now;

    // Create vars for the userdetails
    foreach ($details as $key=>$value)
    {
        eval("\${$key} = \"{$value}\";");
    }

    if ( !isset($siteid) )
    {
        $siteid = $CONFIG["ldap_default_customer_siteid"];
    }

    $sql  = "INSERT INTO `{$dbContacts}` (username, password, forenames, ";
    $sql .= "surname, jobtitle, email, phone, mobile, fax, department, ";
    $sql .= "siteid, timestamp_added, timestamp_modified, address1) ";
    $sql .= "VALUES ('$username', '$md5password', '$forenames', '$surname', ";
    $sql .= "'$jobtitle', '$email', '$phone', '$mobile', '$fax', ";
    $sql .= "'$department', $siteid, '$now', '$now', '$address1')";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    return 1;
}

/**
    * Updates the Contact Record in the database
    * @author Lea Anthony
    * @param $details Array. The details of the user
*/
function ldapUpdateContact($details)
{
    global $CONFIG, $dbContacts, $now;

    // Create vars for the userdetails
    foreach ($details as $key=>$value)
    {
        eval("\${$key} = \"{$value}\";");
    }

    // TODO: Check DB for existing attributes that are NOT mapped and
    //       use them if the ldap versions are blank

    $sql  = "UPDATE `{$dbContacts}` SET password='$md5password', ";
    $sql .= "forenames='$forenames', surname='$surname', jobtitle='$jobtitle', ";
    $sql .= "email='$email', phone='$phone', mobile='$mobile', fax='$fax', ";
    $sql .= "department='$department', siteid=$siteid, timestamp_modified='$now', ";
    $sql .= "address1='$address1' where username='$username'";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    return 1;
}


/**
    * Performs a search on the LDAP tree
    * @author Lea Anthony
    * @param $dn String. The DN of the tree to search
    * @param $query String. The query
    * @return an array of results from the search
*/
function ldapSearch($dn, $query)
{
    debug_log("ldapSearch DN: $dn    QUERY: $query");
    $ldap_conn = ldapOpen();

    $result = ldap_search($ldap_conn, $dn, $query)
            or trigger_error("Error in search dn: $dn query: $query ", E_USER_ERROR);
    $info = ldap_get_entries($ldap_conn, $result);

    ldapClose();

    return $info;
}

/**
    * Test if the given username is in the admin ldap group
    * @author Lea Anthony
    * @param $username String. Username
    * @return an integer to indicate whether the username is in the group
    * @retval 0 int. The username is not in the group
    * @retval 1 int. The username in in the group
*/
function ldapIsAdmin($username)
{
    debug_log("ldapIsAdmin $username");
    // Is user?
    global $CONFIG;

    $dn = $CONFIG["ldap_admin_group"];
    $attr = $CONFIG["ldap_admin_group_attr"];
    $query = "($attr=$username)";

    $info = ldapSearch($dn, $query);
    if(  $info["count"] == 1 ) return TRUE;

    return FALSE;
}

/**
    * Test if the given username is in the managers ldap group
    * @author Lea Anthony
    * @param $username String. Username
    * @return an integer to indicate whether the username is in the group
    * @retval 0 int. The username is not in the group
    * @retval 1 int. The username in in the group
*/
function ldapIsManager($username)
{
    debug_log("ldapIsManager $username");
    // Is user?
    global $CONFIG;

    $dn = $CONFIG["ldap_manager_group"];
    $attr = $CONFIG["ldap_manager_group_attr"];
    $query = "($attr=$username)";

    $info = ldapSearch($dn, $query);
    if(  $info["count"] == 1 ) return TRUE;

    return FALSE;
}

/**
    * Test if the given username is in the user ldap group
    * @author Lea Anthony
    * @param $username String. Username
    * @return an integer to indicate whether the username is in the group
    * @retval 0 int. The username is not in the group
    * @retval 1 int. The username in in the group
*/
function ldapIsUser($username)
{
    debug_log("ldapIsUser $username");
    // Is user?
    global $CONFIG;

    $dn = $CONFIG["ldap_user_group"];
    $attr = $CONFIG["ldap_user_group_attr"];
    $query = "($attr=$username)";

    $info = ldapSearch($dn, $query);
    if(  $info["count"] == 1 ) return TRUE;

    return FALSE;
}

/**
    * Test if the given username is in the customer ldap group
    * @author Lea Anthony
    * @param $username String. Username
    * @return an integer to indicate whether the username is in the group
    * @retval 0 int. The username is not in the group
    * @retval 1 int. The username in in the group
*/
function ldapIsCustomer($username)
{
    debug_log("ldapIsCustomer $username");
    // Is customer?
    global $CONFIG;

    $dn = $CONFIG["ldap_customer_group"];
    $attr = $CONFIG["ldap_customer_group_attr"];
    $query = "($attr=$username)";

    $info = ldapSearch($dn, $query);
    if(  $info["count"] == 1 ) return TRUE;

    return FALSE;
}

/**
    * Gets the type of user for the given username
    * @author Lea Anthony
    * @param $username String. Username
    * @return an integer to indicate what group the user is in
    * @retval LDAP_INVALID_USER int. the username is not valid
    * @retval LDAP_USERTYPE_CUSTOMER int. the username in in the customers group
    * @retval LDAP_USERTYPE_USER int. the username in in the users group
    * @retval LDAP_USERTYPE_MANAGER int. the username in in the managers group
    * @retval LDAP_USERTYPE_ADMIN int. the username in in the admin group
*/
function ldapGetUserType($username)
{
    global $CONFIG;
    debug_log("ldapGetUserType $username");

    $result = LDAP_INVALID_USER;

    if (ldapIsAdmin($username)) $result = LDAP_USERTYPE_ADMIN;
    elseif (ldapIsManager($username)) $result = LDAP_USERTYPE_MANAGER;
    elseif (ldapIsUser($username)) $result = LDAP_USERTYPE_USER;
    elseif (ldapIsCustomer($username)) $result = LDAP_USERTYPE_CUSTOMER;

    return $result;
}


/**
    * Opens a connection to the LDAP host
    * @author Lea Anthony
    * @return the handle of the opened connection
*/
function ldapOpen()
{
    // TODO: Secure binding to host using TLS/SSL
    debug_log("ldapOpen");
    global $CONFIG, $ldap_conn;
    $host = $CONFIG['ldap_host'];
    $ldap_conn = ldap_connect($host)
                 or trigger_error("Could not connect to server", E_USER_ERROR);

    $bind_user = $CONFIG["ldap_bind_user"];
    $bind_pass = $CONFIG["ldap_bind_pass"];

    // Set protocol version
    $protocol = $CONFIG["ldap_protocol"];
    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, $protocol);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS,0);

    if ( $CONFIG["ldap_use_tls"] )
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

    if ( isset($bind_user) && strlen($bind_user) > 0 )
    {
        $r = ldap_bind($ldap_conn, $bind_user, $bind_pass);
        if ( ! $r )
        {
            // Could not bind!
//             $setupvars['use_ldap'] = FALSE;
//             cfgSave($setupvars);
            trigger_error("Could not bind to LDAP server with credentials", E_USER_ERROR);
        }
    }

    return $ldap_conn;
}


/**
    * Closes the connection to the LDAP host
    * @author Lea Anthony
*/
function ldapClose()
{
    debug_log("ldapClose");
    global $ldap_conn;

    if( $ldap_conn != 0 )
    {
        ldap_close($ldap_conn);
        $ldap_conn = 0;
    }
}


/**
    * Gets the user details from LDAP for the given username
    * @author Lea Anthony
    * @param $username String. Username
    * @return an array containing the user details
*/
function ldapGetUserDetails($username)
{
    debug_log("ldapGetUserDetails $username");
    // Get user details
    global $CONFIG;

    $user_attr = $CONFIG['ldap_user_attr'];
    $dn_base = $CONFIG['ldap_dn_base'];
    $dn = "$user_attr=$username,$dn_base";

    $query = "($user_attr=$username)";

    $info = ldapSearch($dn_base, $query)
            or trigger_error("Error in search query", E_USER_ERROR);

    $userdata = $info[0];

    // The Result is an array with the users details
    $r = array();

    $attributes = array("realname","forenames","jobtitle","email","mobile",
                        "surname", "fax","phone");

    foreach ( $attributes as $attr )
    {
        $mapattr = $CONFIG['ldap_attr_map'][$attr];
        ( isset($mapattr) ? $r[$attr] = $info[0][$mapattr][0] :
        $r[$attr] = "" );
    }

    $r["username"] = $info[0][$user_attr][0];

    return $r;
}

/**
    * Gets the user details from LDAP for the given email
    * @author Lea Anthony
    * @param $username String. Email
    * @return an array containing the user details
*/
function ldapGetCustomerDetailsFromEmail($email)
{
    debug_log("ldapGetCustomerDetailsFromEmail $email");
    // Get user details
    global $CONFIG;

    $dn_base = $CONFIG['ldap_dn_base'];
    $user_attr = $CONFIG['ldap_user_attr'];

    $query = "(mail=$email)";

    $info = ldapSearch($dn_base, $query)
            or trigger_error("Error in search query", E_USER_ERROR);

    $userdata = $info[0];

    // The Result is an array with the users details
    $r = array();

    $attributes = array("realname","forenames","jobtitle","email","mobile",
                        "surname", "fax","phone");

    foreach ( $attributes as $attr )
    {
        $mapattr = $CONFIG['ldap_attr_map'][$attr];
        ( isset($mapattr) ? $r[$attr] = $userdata[$mapattr][0] :
        $r[$attr] = "" );
    }

    // Extract the username from the matched dn
    $dn = $userdata['dn'];
    $rep = $user_attr."=";
    $dn = str_replace($rep,"",$dn);
    $rep = ",".$dn_base;
    $username = str_replace($rep,"",$dn);
    $r["username"] = $username;

    return $r;
}


/**
    * Checks if the given username and password are valid against the LDAP tree
    * @author Lea Anthony
    * @param $username String. Username
    * @param $password String. Password
    * @return an integer to indicate what group the user is in
    * @retval 0 (FALSE) int. The user/pass is not valid
    * @retval 1 (TRUE) int. The user/pass is valid
*/
function ldapUserPassValid($username, $password)
{
    debug_log("ldapUserPassValid $username");
    global $CONFIG;

    $ldap_conn = ldapOpen();

    // Attempt bind
    $user_attr = $CONFIG['ldap_user_attr'];
    $dn_base = $CONFIG['ldap_dn_base'];
    $dn = "$user_attr=$username,$dn_base";

    // If the user not in LDAP then return FALSE
    if (!$r = @ldap_bind($ldap_conn, $dn, $password)) return FALSE;

    ldapClose();

    return TRUE;
}


/**
    * Authenticate a user
    * If successful and the user is new, the user is created in the database
    * If successful and the user is returning, the user record is resynced
    * @author Lea Anthony
    * @param $username String. Username
    * @param $password String. Password
    * @return an integer to indicate whether the user authenticated against the LDAP backend
    * @retval 0 the credentials were wrong or the user was not found.
    * @retval 1 to indicate user is authenticated and allowed to continue.
*/
function authenticateLDAP($username, $password)
{
    debug_log("authenticateLDAP $username");
    global $CONFIG, $dbUsers, $dbContacts, $dbUserPermissions,
        $dbPermissions, $ldap_conn, $now;

    // If the user/password are not valid then return gracefully
    if(!ldapUserPassValid($username,$password)) return 0;

    // Get user type - if it's not a user then return
    $usertype = ldapGetUserType($username);
    if( $usertype != LDAP_USERTYPE_USER &&
        $usertype != LDAP_USERTYPE_MANAGER &&
        $usertype != LDAP_USERTYPE_ADMIN ) return 0;

    // Get User Details
    $details = ldapGetUserDetails($username);

    $details["md5password"] = md5($password);

    return ldapCreateUser($details);
}


/**
    * Creates the User Record in the database
    * @author Lea Anthony
    * @param $details Array. The details of the user
*/
function ldapCreateUser($details)
{
    debug_log("ldapCreateUser".print_r($details, true));
    global $CONFIG, $dbUsers, $dbContacts, $dbUserPermissions,
        $dbPermissions, $ldap_conn, $now;

    // Create vars for the userdetails
    foreach ($details as $key=>$value)
    {
        eval("\${$key} = \"{$value}\";");
    }

    // Defaults
    $default_status = $CONFIG["ldap_default_user_status"];
    $default_style = $CONFIG['default_interface_style'];
    $default_lang = $CONFIG['default_i18n'];

    // Get user type
    $usertype = ldapGetUserType($username);

    // Create User
    $sql  = "INSERT INTO `{$dbUsers}` (username, password, realname, title, roleid, status, ";
    $sql .= "email, phone, mobile, fax, var_style, var_i18n ) ";
    $sql .= "VALUES ('$username', '$md5password', '$realname', '$jobtitle', ";
    $sql .= "$usertype, $default_status, '$email', '$phone', '$mobile', '$fax', ";
    $sql .= "$default_style, '$default_lang')";

    $result = mysql_query($sql);

    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $newuserid = mysql_insert_id();

    // Create permissions (set to none)
    $sql = "SELECT * FROM `{$dbPermissions}`";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    while ($perm = mysql_fetch_object($result))
    {
        $psql  = "INSERT INTO `{$dbUserPermissions}` (userid, permissionid, granted) ";
        $psql .= "VALUES ('$newuserid', '{$perm->id}', 'false')";
        mysql_query($psql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    }

    setup_user_triggers($newuserid);
    trigger('TRIGGER_NEW_USER', array('userid' => $newuserid));

    journal(4,'User Authenticated',"$username authenticated from ".getenv('REMOTE_ADDR'),1,0);
    return 1;
}

/**
    * Updates the user record in the database with details from LDAP
    * @author Lea Anthony
    * @param $username String. Username
    * @param $password String. Password
*/
function ldapSyncUser($username, $password)
{
    global $CONFIG, $dbUsers, $dbContacts, $dbUserPermissions,
        $dbPermissions, $ldap_conn, $now;

    // Get User Details
    $details = ldapGetUserDetails($username);

    $details["md5password"] = md5($password);

    return ldapUpdateUser($details);
}

/**
    * Updates the User Record in the database
    * @author Lea Anthony
    * @param $details Array. The details of the user
*/
function ldapUpdateUser($details)
{
    global $CONFIG, $dbUsers, $now;

    // Create vars for the userdetails
    foreach ($details as $key=>$value)
    {
        eval("\${$key} = \"{$value}\";");
    }

    // Get user type
    $usertype = ldapGetUserType($username);


    // TODO: Check DB for existing attributes that are NOT mapped and
    //       use them if the ldap versions are blank

    $sql  = "UPDATE `{$dbUsers}` SET password='$md5password', realname='$realname', ";
    $sql .= "title='$jobtitle', roleid=$usertype, email='$email', phone='$phone', ";
    $sql .= "mobile='$mobile', fax='$fax' where username='$username'";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    return 1;
}

/**
    * Gets the details of a user from the database from their email
    * @author Lea Anthony
    * @param $email String. Email
    * @return An array of the user data (if found)
*/
function getUserDetailsFromDBByEmail($email)
{
    global $dbUsers;

    $sql = "SELECT * FROM `{$dbUsers}` where email='$email'";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);


    return mysql_fetch_array($result);

}

/**
    * Gets the details of a contact from the database from their email
    * @author Lea Anthony
    * @param $email String. Email
*/
function getContactDetailsFromDBByEmail($email)
{
    global $dbContacts;

    $sql = "SELECT * FROM `{$dbContacts}` where email='$email'";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    return mysql_fetch_array($result);

}

/**
    * Checks that the email address given is a contact that has not yet
    * been imported into the DB, then imports them.
    * @author Lea Anthony
    * @param $email String. Email
    * @return An array of the user data (if found)
*/
function ldapImportCustomerFromEmail($email)
{
    global $dbContacts;

    // Check if "customer" is actually a User
    $r = getUserDetailsFromDBByEmail($email);

    if( ! empty($r) )
    {
        // This is actually a User. Can users be customers?
        return;
    }

    $r = getContactDetailsFromDBByEmail($email);

    if( ! empty($r) )
    {
        // This contact already exists
        return;
    }

    // Create user
    $details = ldapGetCustomerDetailsFromEmail(email);


    ldapCreateContact($details);

}

?>
