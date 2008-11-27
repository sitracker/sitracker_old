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
define ('LDAP_USERTYPE_USER',1);
define ('LDAP_USERTYPE_CUSTOMER',2);

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
    global $CONFIG, $dbUsers, $dbContacts, $dbUserPermissions, $dbPermissions, $ldap_conn, $now;

    if( customerExistsInDB($username) ) {
        ldapSyncCustomer($username, $password);
        return;
    }

    // If the user/password are not valid then return gracefully
    if( ! ldapUserPassValid($username,$password) ) return 0;

    // Get user type - if it's not a customer then return
    if( ldapGetUserType($username) != LDAP_USERTYPE_CUSTOMER ) return 0;

    // Get User Details
    $u = ldapGetUserDetails($username);

    // Create vars for the userdetails
    foreach ($u as $key=>$value) {
        eval("\${$key} = \"{$value}\";");  
    }

    // Customer
    $department = "";
    $siteid = $CONFIG["ldap_default_customer_siteid"];
    $address1 = "";
    $md5password = md5($password);

    // TODO: Contact creation should be in it's own function and 
    //       shared between the whole codebase

    $sql  = "INSERT INTO `{$dbContacts}` (username, password, forenames, ";
    $sql .= "surname, jobtitle, email, phone, mobile, fax, department, ";
    $sql .= "siteid, timestamp_added, timestamp_modified, address1) ";
    $sql .= "VALUES ('$username', '$md5password', '$forenames', '$surname', ";
    $sql .= "'$jobtitle', '$email', '$phone', '$mobile', '$fax', ";
    $sql .= "'$department', $siteid, '$now', '$now', '$address1')";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

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
    $ldap_conn = ldapOpen();

    $result = ldap_search($ldap_conn, $dn, $query) 
            or trigger_error("Error in search query: $query ", E_USER_ERROR);
    $info = ldap_get_entries($ldap_conn, $result);

    ldapClose();

    return $info;
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
    * @retval 0 (LDAP_INVALID_USER) int. the username is not valid
    * @retval 1 (LDAP_USERTYPE_USER) int. the username in in the users group
    * @retval 2 (LDAP_USERTYPE_CUSTOMER) int. the username in in the customers group
*/
function ldapGetUserType($username)
{
    global $CONFIG;

    $result = LDAP_INVALID_USER;

    if( ldapIsUser($username) ) $result = LDAP_USERTYPE_USER;
    elseif( ldapIsCustomer($username) ) $result = LDAP_USERTYPE_CUSTOMER;

    return $result;

}


/**
    * Updates the user record in the database with details from LDAP
    * @author Lea Anthony
    * @param $username String. Username
    * @param $password String. Password
*/
function ldapSyncUser($username, $password) {
    // TODO: Update the DB record with the user details
}


/**
    * Updates the customer record in the database with details from LDAP
    * @author Lea Anthony
    * @param $username String. Username
    * @param $password String. Password
*/
function ldapSyncCustomer($username, $password) {
    // TODO: Update the DB record with the customer details
}

/**
    * Opens a connection to the LDAP host
    * @author Lea Anthony
    * @return the handle of the opened connection
*/
function ldapOpen()
{
    // TODO: Secure binding to host using TLS/SSL
    
    global $CONFIG, $ldap_conn;
    $host = $CONFIG['ldap_host'];
    $ldap_conn = ldap_connect($host)
                or trigger_errror("Could not connect to server", E_USER_ERROR);
//     $r = ldap_bind($ldap_conn);

    return $ldap_conn;
}


/**
    * Closes the connection to the LDAP host
    * @author Lea Anthony
*/
function ldapClose()
{
    global $ldap_conn;

    if( $ldap_conn != 0 ) {
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
                        "fax","phone");

    foreach ( $attributes as $attr ) {
        $mapattr = $CONFIG['ldap_attr_map'][$attr];
        ( isset($mapattr) ? $r[$attr] = $info[0][$mapattr][0] : 
        $r[$attr] = "" );
    }

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
    global $CONFIG;

    $ldap_conn = ldapOpen();

    // Attempt bind
    $user_attr = $CONFIG['ldap_user_attr'];
    $dn_base = $CONFIG['ldap_dn_base'];
    $dn = "$user_attr=$username,$dn_base";

    // If the user not in LDAP then return FALSE
    if( ! $r = @ldap_bind($ldap_conn, $dn, $password) ) return FALSE;

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
    global $CONFIG, $dbUsers, $dbContacts, $dbUserPermissions, 
        $dbPermissions, $ldap_conn, $now;

    // If the user/password are not valid then return gracefully
    if( ! ldapUserPassValid($username,$password) ) return 0;

    // Get user type - if it's not a user then return
    if( ldapGetUserType($username) != LDAP_USERTYPE_USER ) return 0;

    // Get User Details
    $u = ldapGetUserDetails($username);

    // Create vars for the userdetails
    foreach ($u as $key=>$value) {
        eval("\${$key} = \"{$value}\";");  
    }

    // Default role, group, 
    $default_role = $CONFIG["ldap_default_user_role"];
    $default_status = $CONFIG["ldap_default_user_status"]; 

    $md5password = md5($password);

    // TODO: User creation should be in it's own function and 
    //       shared between the whole codebase

    // Create User
    $sql  = "INSERT INTO `{$dbUsers}` (username, password, realname, title, roleid, status, ";
    $sql .= "email, phone, mobile, fax) ";
    $sql .= "VALUES ('$username', '$md5password', '$realname', '$jobtitle', ";
    $sql .= "$default_role, $default_status, '$email', '$phone', '$mobile', '$fax')";

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



?>