<?php
// functions.inc.php - Function library and defines for SiT -Support Incident Tracker
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Ivan Lucas, Tom Gerrard - 2001 onwards
//          Martin Kilcoyne - 2000

// Many functions here simply extract various snippets of information from
// the database
// Most are legacy and can replaced by improving the pages that call them to
// use SQL joins.

// Version number of the application, (numbers only)
$application_version='3.25';
// Revision string, e.g. 'beta2' or ''
$application_revision='dev1';

// Clean PHP_SELF server variable to avoid potential XSS security issue
$_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0, (strlen($_SERVER['PHP_SELF']) - @strlen($_SERVER['PATH_INFO'])));

// Journal Logging
// 0 = No logging
// 1 = Minimal Logging
// 2 = Normal Logging
// 3 = Full Logging
// 4 = Maximum/Debug Logging
define('CFG_LOGGING_OFF',0);
define('CFG_LOGGING_MIN',1);
define('CFG_LOGGING_NORMAL',2);
define('CFG_LOGGING_FULL',3);
define('CFG_LOGGING_MAX',4);

define('CFG_JOURNAL_DEBUG', 0);     // 0 = for internal debugging use
define('CFG_JOURNAL_LOGIN', 1);     // 1 = Logon/Logoff
define('CFG_JOURNAL_SUPPORT', 2);   // 2 = Support Incidents
define('CFG_JOURNAL_SALES', 3);     // 3 = Sales Incidents
define('CFG_JOURNAL_SITES', 4);     // 4 = Sites
define('CFG_JOURNAL_CONTACTS', 5);  // 5 = Contacts
define('CFG_JOURNAL_ADMIN', 6);     // 6 = Admin
define('CFG_JOURNAL_USER', 7);       // 7 = User Management
define('CFG_JOURNAL_MAINTENANCE', 8);  // 8 = Maintenance Contracts
define('CFG_JOURNAL_PRODUCTS', 9);
define('CFG_JOURNAL_OTHER', 10);
define('CFG_JOURNAL_KB', 11);    // Knowledge Base


// Time settings
$now = time();
$soon = $now-900; // in 15 minutes time (bugbug: think this should be a plus, need to check - 28aug01 INL)
$today=$now+(16*3600);  // next 16 hours, based on reminders being run at midnight this is today
$lastweek=$now - (7 * 86400); // the previous seven days
$todayrecent=$now-(16*3600);  // past 16 hours

$CONFIG['upload_max_filesize'] = return_bytes($CONFIG['upload_max_filesize']);

// Non specific update types
$updatetypes['actionplan'] = array('icon' => 'actions/enum_list.png', 'text' => 'Action Plan by updateuser');
$updatetypes['auto'] = array('icon' => 'actions/info.png', 'text' => 'Updated automatically by updateuser');
$updatetypes['closing'] = array('icon' => 'actions/fileclose.png', 'text' => 'Marked for closure by updateuser');
$updatetypes['editing'] = array('icon' => 'actions/edit.png', 'text' => 'Edited by updateuser');
$updatetypes['email'] = array('icon' => 'actions/mail_send.png', 'text' => 'Email sent by updateuser');
$updatetypes['emailin'] = array('icon' => 'actions/mail_generic.png', 'text' => 'Email received by updateuser');
$updatetypes['emailout'] = array('icon' => 'actions/mail_send.png', 'text' => 'Email sent by updateuser');
$updatetypes['externalinfo'] = array('icon' => 'actions/ktip.png', 'text' => 'External info added by updateuser');
$updatetypes['probdef'] = array('icon' => 'actions/enum_list.png', 'text' => 'Problem Definition by updateuser');
$updatetypes['research'] = array('icon' => 'actions/idea.png', 'text' => 'Researched by updateuser');
$updatetypes['reassigning'] = array('icon' => 'actions/2rightarrow.png', 'text' => 'Reassigned to currentowner by updateuser');
$updatetypes['reviewmet'] = array('icon' => 'actions/flag.png', 'text' => 'Review Period by updateuser'); // conditional
$updatetypes['tempassigning'] = array('icon' => 'actions/1rightarrow.png', 'text' => 'Temporarily assigned to currentowner by updateuser');
$updatetypes['opening'] = array('icon' => 'actions/filenew.png', 'text' => 'Opened by updateuser');
$updatetypes['phonecallout'] = array('icon' => 'actions/forward.png', 'text' => 'Phone call made by updateuser');
$updatetypes['phonecallin'] = array('icon' => 'actions/back.png', 'text' => 'Phone call taken by updateuser');
$updatetypes['reopening'] = array('icon' => 'actions/filenew.png', 'text' => 'Reopened by updateuser');
$updatetypes['slamet'] = array('icon' => 'actions/flag.png', 'text' => 'SLA Met by updateuser');
$updatetypes['solution'] = array('icon' => 'actions/endturn.png', 'text' => 'Resolved by updateuser');
$updatetypes['webupdate'] = array('icon' => 'actions/comment.png', 'text' => 'Web update');

// Set a string to be the full version number and revision of the application
$application_version_string=trim("v{$application_version} {$application_revision}");

// Email template settings
$template_openincident_email=12;

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
//////////////////
$hmenu[0] = array (10=> array ( 'perm'=> 0, 'name'=> "{$CONFIG['application_shortname']}", 'url'=>"main.php", 'submenu'=>"10"),
                   20=> array ( 'perm'=> 11, 'name'=> "Customers", 'url'=>"browse_sites.php?search_string=A", 'submenu'=>"20"),
                   30=> array ( 'perm'=> 6, 'name'=> "Support", 'url'=>"incidents.php?user=current&amp;queue=1&amp;type=support", 'submenu'=>"30"),
                   40=> array ( 'perm'=> 0, 'name'=> "Tasks", 'url'=>"tasks.php", 'submenu'=>"40"),
                   50=> array ( 'perm'=> 54, 'name'=> "KnowledgeBase", 'url'=>"browse_kb.php", 'submenu'=>"50"),
                   60=> array ( 'perm'=> 37, 'name'=> "Reports", 'url'=>"reports.php", 'submenu'=>"60"),
                   70=> array ( 'perm'=> 0, 'name'=> "Help", 'url'=>"help.php", 'submenu'=>"70")
);
$hmenu[10] = array (1=> array ( 'perm'=> 0, 'name'=> "Main Page", 'url'=>"main.php"),
                    10=> array ( 'perm'=> 60, 'name'=> "Search", 'url'=>"search.php"),
                    20=> array ( 'perm'=> 4, 'name'=> "My Details", 'url'=>"edit_profile.php", 'submenu'=>"1020"),
                    30=> array ( 'perm'=> 4, 'name'=> "Control Panel", 'url'=>"control_panel.php", 'submenu'=>"1030"),
                    40=> array ( 'perm'=> 14, 'name'=> "Users", 'url'=>"users.php", 'submenu'=>"1040"),
                    50=> array ( 'perm'=> 0, 'name'=> "Logout", 'url'=>"logout.php")
);
$hmenu[1020] = array (10=> array ( 'perm'=> 4, 'name'=> "My Profile", 'url'=>"edit_profile.php"),
                      20=> array ( 'perm'=> 58, 'name'=> "My Skills", 'url'=>"edit_user_software.php"),
                      30=> array ( 'perm'=> 58, 'name'=> "My Substitutes", 'url'=>"edit_backup_users.php"),
                      40=> array ( 'perm'=> 27, 'name'=> "My Holidays", 'url'=>"holidays.php")
);
// configure
$hmenu[1030] = array (10=> array ( 'perm'=> 22, 'name'=> "Users", 'url'=>"manage_users.php", 'submenu'=>"103010"),
                      20=> array ( 'perm'=> 0, 'name'=> "Email Settings", 'url'=>"", 'submenu'=>"103020"),
                      30=> array ( 'perm'=> 22, 'name'=> "Set Public Holidays", 'url'=>"holiday_calendar.php?type=10"),
                      40=> array ( 'perm'=> 22, 'name'=> "FTP Files DB", 'url'=>"ftp_list_files.php"),
                      50=> array ( 'perm'=> 22, 'name'=> "Service Levels", 'url'=>"service_levels.php"),
                      60=> array ( 'perm'=> 7, 'name'=> "Bulk Modify", 'url'=>"bulk_modify.php?action=external_esc"),
                      70=> array ( 'perm'=> 64, 'name'=> "Escalation Paths", 'url'=>"escalation_paths.php"),
);
$hmenu[103010] = array (10=> array ( 'perm'=> 22, 'name'=> "Manage Users", 'url'=>"manage_users.php"),
                        20=> array ( 'perm'=> 20, 'name'=> "Add User", 'url'=>"add_user.php?action=showform"),
                        30=> array ( 'perm'=> 9, 'name'=> "Set Permissions", 'url'=>"edit_user_permissions.php"),
                        40=> array ( 'perm'=> 23, 'name'=> "User Groups", 'url'=>"usergroups.php")
);
$hmenu[103020] = array (10=> array ( 'perm'=> 16, 'name'=> "Add Template", 'url'=>"add_emailtype.php?action=showform"),
                        20=> array ( 'perm'=> 17, 'name'=> "Edit Template", 'url'=>"edit_emailtype.php?action=showform"),
                        30=> array ( 'perm'=> 43, 'name'=> "Global Signature", 'url'=>"edit_global_signature.php")
);
$hmenu[1040] = array (10=> array ( 'perm'=> 0, 'name'=> "View Users", 'url'=>"users.php"),
                      20=> array ( 'perm'=> 0, 'name'=> "List Skills", 'url'=>"user_skills.php"),
                      21=> array ( 'perm'=> 0, 'name'=> "Skills Matrix", 'url'=>"skills_matrix.php"),
                      30=> array ( 'perm'=> 27, 'name'=> "Holiday Planner", 'url'=>"holiday_calendar.php"),
                      40=> array ( 'perm'=> 50, 'name'=> "Approve Holidays", 'url'=>"holiday_request.php?user=all&amp;mode=approval")
);



// Customers
$hmenu[20] = array (10=> array ( 'perm'=> 0, 'name'=> "Sites", 'url'=>"browse_sites.php?search_string=A", 'submenu'=>"2010"),
                    20=> array ( 'perm'=> 0, 'name'=> "Contacts", 'url'=>"browse_contacts.php?search_string=A", 'submenu'=>"2020"),
                    30=> array ( 'perm'=> 0, 'name'=> "Maintainance", 'url'=>"browse_maintenance.php?search_string=A", 'submenu'=>"2030"),
                    40=> array ( 'perm'=> 0, 'name'=> "Browse Feedback", 'url'=>"browse_feedback.php")
);

$hmenu[2010] = array (10=> array ( 'perm'=> 11, 'name'=> "Browse", 'url'=>"browse_sites.php?search_string=A"),
                      20=> array ( 'perm'=> 2, 'name'=> "New Site", 'url'=>"add_site.php?action=showform")
);
$hmenu[2020] = array (10=> array ( 'perm'=> 11, 'name'=> "Browse", 'url'=>"browse_contacts.php?search_string=A"),
                      20=> array ( 'perm'=> 1, 'name'=> "New Contact", 'url'=>"add_contact.php?action=showform")
);

$hmenu[2030] = array (10=> array ( 'perm'=> 19, 'name'=> "Browse", 'url'=>"browse_maintenance.php?search_string=A"),
                      20=> array ( 'perm'=> 39, 'name'=> "New Contract", 'url'=>"add_maintenance.php?action=showform"),
                      30=> array ( 'perm'=> 21, 'name'=> "Edit Contract", 'url'=>"edit_maintenance.php?action=showform"),
                      40=> array ( 'perm'=> 2, 'name'=> "New Reseller", 'url'=>"add_reseller.php"),
                      50=> array ( 'perm'=> 19, 'name'=> "Show Renewals", 'url'=>"search_renewals.php?action=showform"),
                      60=> array ( 'perm'=> 19, 'name'=> "Show Expired", 'url'=>"search_expired.php?action=showform"),
                      70=> array ( 'perm'=> 0, 'name'=> "Products &amp; Software", 'url'=>"products.php", 'submenu'=>"203010"),
);

$hmenu[203010] = array (10=> array ( 'perm'=> 56, 'name'=> "Add Vendor", 'url'=>"add_vendor.php"),
                        20=> array ( 'perm'=> 24, 'name'=> "Add Product", 'url'=>"add_product.php"),
                        30=> array ( 'perm'=> 28, 'name'=> "List Products", 'url'=>"products.php"),
                        40=> array ( 'perm'=> 56, 'name'=> "Add Software", 'url'=>"add_software.php"),
                        50=> array ( 'perm'=> 24, 'name'=> "Link Products", 'url'=>"add_product_software.php"),
                        60=> array ( 'perm'=> 25, 'name'=> "Add Product Question", 'url'=>"add_productinfo.php"),
                        70=> array ('perm'=> 56, 'name'=> "Edit Vendor", 'url'=>"edit_vendor.php")
);


// Support
$hmenu[30] = array (10=> array ( 'perm'=> 5, 'name'=> "Add Incident", 'url'=>"add_incident.php"),
                    20=> array ( 'perm'=> 0, 'name'=> "View Incidents", 'url'=>"incidents.php?user=current&amp;queue=1&amp;type=support"),
                    30=> array ( 'perm'=> 0, 'name'=> "Watch Incidents", 'url'=>"incidents.php?user=all&amp;queue=1&amp;type=support"),
                    40=> array ( 'perm'=> 42, 'name'=> "Holding Queue", 'url'=>"review_incoming_updates.php")
);


// Tasks
$hmenu[40] = array (10=> array ( 'perm'=> 0, 'name'=> "Add Task", 'url'=>"add_task.php"),
                    20=> array ( 'perm'=> 0, 'name'=> "View Tasks", 'url'=>"tasks.php")
);

// KB
$hmenu[50] = array (10=> array ( 'perm'=> 54, 'name'=> "New Article", 'url'=>"kb_add_article.php"),
                    20=> array ( 'perm'=> 54, 'name'=> "Browse", 'url'=>"browse_kb.php")
);



// Reports
$hmenu[60] = array (10=> array ( 'perm'=> 37, 'name'=> "Marketing Mailshot", 'url'=>"reports/marketing.php"),
                    20=> array ( 'perm'=> 37, 'name'=> "Customer Export", 'url'=>"reports/cust_export.php"),
                    30=> array ( 'perm'=> 37, 'name'=> "Query by Example", 'url'=>"reports/qbe.php"),
                    50=> array ( 'perm'=> 37, 'name'=> "Incidents by Site", 'url'=>"reports/yearly_customer_export.php"),
                    55=> array ( 'perm'=> 37, 'name'=> "Incidents by Engineer", 'url'=>"reports/yearly_engineer_export.php"),
                    60=> array ( 'perm'=> 37, 'name'=> "Site Products", 'url'=>"reports/site_products.php"),
                    70=> array ( 'perm'=> 37, 'name'=> "Site Contracts", 'url'=>"reports/supportbycontract.php"),
                    80=> array ( 'perm'=> 37, 'name'=> "Customer Feedback", 'url'=>"reports/feedback.php"),
                    90=> array ( 'perm'=> 37, 'name'=> "Site Incidents", 'url'=>"reports/site_incidents.php"),
                    100=> array ( 'perm'=> 37, 'name'=> "Recent Incidents", 'url'=>"reports/recent_incidents_table.php"),
                    110=> array ( 'perm'=> 37, 'name'=> "Incidents Logged (Open/Closed)", 'url'=>"reports/incident_graph.php"),
                    120=> array ( 'perm'=> 37, 'name'=> "Average Incident Duration", 'url'=>"reports/average_incident_duration.php"),
                    130=> array ( 'perm'=> 37, 'name'=> "Incidents by Software", 'url'=>"reports/incidents_by_software.php")
);


$hmenu[70] = array (10=> array ( 'perm'=> 0, 'name'=> "Help Contents...", 'url'=>"help.php"),
                    20=> array ( 'perm'=> 41, 'name'=> "About", 'url'=>"about.php")
);

if (!function_exists('authenticate'))
{

    // This function returns an integer depending on whether the
    // given user exists in the users table of the database.
    // 1 = User exists
    // 0 = User does not exist
    function authenticate($username, $password)
    {
        if ($_SESSION['auth']==TRUE)
        {
            // Already logged in
            return 1;
        }

        // extract user
        $sql  = "SELECT id FROM users ";
        $sql .= "WHERE username='$username' AND password='$password' AND status!=0 ";
        // a status of 0 means the user account is disabled
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

        // return appropriate value
        if (mysql_num_rows($result) == 0)
        {
            mysql_free_result($result);
            return 0;
        }
        else
        {
            journal(4,'User Authenticated',"$username authenticated from ".getenv('REMOTE_ADDR'),1,0);
            return 1;
        }
    }
}


// Returns a specified column from a specified table in the database
// given an ID primary key
function db_read_column($column, $table, $id)
{
    $sql = "SELECT $column FROM $table WHERE id='$id' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    list($column)=mysql_fetch_row($result);
    $column=stripslashes($column);
    return $column;
}


function user_permission($userid,$permission)
{
    // Default is no access
    $grantaccess = FALSE;

    if (!is_array($permission)) { $permission = array($permission); }

    foreach($permission AS $perm)
    {
        if (@in_array($perm, $_SESSION['permissions']) == TRUE) $accessgranted = TRUE;
        else $accessgranted = FALSE;
        // Permission 0 is always TRUE (general acess)
        if ($perm == 0) $accessgranted = TRUE;
    }
    return $accessgranted;
}

function permission_name($permissionid)
{
    return db_read_column('name', 'permissions', $permissionid);
}


function software_name($softwareid)
{
    return db_read_column('name', 'software', $softwareid);
}


// This function returns an integer representing the id of
// given user. Returns 0 of the user does not exist
function user_id($username, $password)
{
    // extract user
    $sql  = "SELECT id FROM users ";
    $sql .= "WHERE username='$username' AND password='$password'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) == 0)
    {
        $userid=0;
    }
    else
    {
        $user = mysql_fetch_array($result);
        $userid=$user["id"];
    }
    return $userid;
}


function user_password($id)
{
    return db_read_column('password', 'users', $id);
}


function user_realname($id,$allowhtml=FALSE)
{
    global $update_body;
    global $incidents;
    global $CONFIG;
    if ($id >= 1)
    {
        if ($id == $_SESSION['userid']) return $_SESSION['realname'];
        else
        {
            // return db_read_column('realname', 'users', $id);
            $sql = "SELECT realname, status FROM users WHERE id='$id' LIMIT 1";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            list($realname, $status)=mysql_fetch_row($result);
            if ($allowhtml==FALSE OR $status > 0) return $realname;
            else return ("<span class='deleted'>$realname</span>");
        }
    }
    elseif(!empty($incidents['email']))
    {
        //an an incident
        preg_match('/From:[ A-Za-z@\.]*/', $update_body, $from);
        if(!empty($from))
        {
            $frommail = strtolower(substr(strstr($from[0], '@'), 1));
            $customerdomain = strtolower(substr(strstr($incidents['email'], '@'), 1));
            if($frommail == $customerdomain) return "Customer";
            foreach($CONFIG['ext_esc_partners'] AS $partner)
            {
                if(strstr(strtolower($frommail), strtolower($partner['email_domain'])))
                {
                    return $partner['name'];
                }
            }
        }
    }

    //Got this far not returned anything so
    return($CONFIG['application_shortname']); // No from email address
}


function user_email($id)
{
    if ($id == $_SESSION['userid']) return $_SESSION['email'];
    else return db_read_column('email', 'users', $id);
}


function user_phone($id)
{
    return db_read_column('phone', 'users', $id);
}


function user_mobile($id)
{
    return db_read_column('mobile', 'users', $id);
}


function user_signature($id)
{
    return db_read_column('signature', 'users', $id);
}


function user_message($id)
{
    return db_read_column('message', 'users', $id);
}

function user_status($id)
{
    return db_read_column('status', 'users', $id);
}


// Returns a string containging "Yes" or "No"
// Returns "NoSuchUser" if the user does not exist
function user_accepting($id)
{
    $accepting = db_read_column('accepting', 'users', $id);
    if ($accepting == '')  $accepting = "NoSuchUser";

    return($accepting);
}


function user_activeincidents($userid)
{
    global $CONFIG, $now;
    // This SQL must match the SQL in incidents.php
    $sql = "SELECT incidents.id  ";
    $sql .= "FROM incidents, contacts, priority WHERE contact=contacts.id AND incidents.priority=priority.id ";
    $sql .= "AND (owner='$userid' OR towner='$userid') ";
    $sql .= "AND (status!='2') ";  // not closed
    // the "1=2" obviously false else expression is to prevent records from showing unless the IF condition is true
    $sql .= "AND ((timeofnextaction > 0 AND timeofnextaction < $now) OR ";
    $sql .= "(IF ((status >= 5 AND status <=8), ($now - lastupdated) > ({$CONFIG['regular_contact_days']} * 86400), 1=2 ) ";  // awaiting
    $sql .= "OR IF (status='1' OR status='3' OR status='4', 1=1 , 1=2) ";  // active, research, left message - show all
    $sql .= ") AND timeofnextaction < $now ) ";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    return(mysql_num_rows($result));
}


// counts a users open incidents
function user_countincidents($id)
{
    // this number will never match the number shown in the active queue and is not meant to
    $sql = "SELECT id FROM incidents WHERE (owner='$id' OR towner='$id') AND (status!=2)";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    return(mysql_num_rows($result));
}

// counts number of incidents and priorty
function user_incidents($id){
    $sql = "SELECT priority, count(priority) AS num FROM incidents where (owner = $id OR towner = $id) AND status != 2";
    $sql .= " GROUP BY priority";

    $result = mysql_query($sql);
    if(mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    $arr = array('1' => '0', '2' => '0', '3' => '0', '4' => '0');

    if(mysql_num_rows($result) > 0){
	while ($count = mysql_fetch_array($result)){
		$arr[$count['priority']] = $count['num'];
	}
    }
    return $arr;
}


// gets users holiday information for a certain day given an optional type
// and optional length returns both type and length and approved as an array
function user_holiday($userid, $type=0, $year, $month, $day, $length=FALSE)
{
    $startdate=mktime(0,0,0,$month,$day,$year);
    $sql = "SELECT * FROM holidays WHERE startdate='$startdate' ";
    if ($type!=0)
    {
        $sql .= "AND (type='$type' OR type='10' OR type='5') ";
        $sql .= "AND IF(type!=10, userid='$userid', 1=1) ";
    }
    else
    {
        $sql .=" AND userid='$userid' ";
    }
    if ($length!=FALSE)
    {
        $sql .= "AND length='$length' ";
    }
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) == 0)
    {
        return FALSE;
    }
    else
    {
        $totallength=0;
        while ($holiday=mysql_fetch_object($result))
        {
            $type=$holiday->type;
            $length=$holiday->length;
            $approved=$holiday->approved;
            $approvedby=$holiday->approvedby;
            // hmm... not sure these next lines are required.
            if ($length=='am' && $totallength==0) $totallength='am';
            if ($length=='pm' && $totallength==0) $totallength='pm';
            if ($length=='am' && $totallength=='pm') $totallength='day';
            if ($length=='pm' && $totallength=='am') $totallength='day';
            if ($length=='day') $totallength='day';
        }
        return array($type, $totallength, $approved, $approvedby);
    }
}


function user_count_holidays($userid, $type)
{
    $sql = "SELECT id FROM holidays WHERE userid='$userid' AND type='$type' AND length='day' AND approved >= 0 AND approved < 2 ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $full_days=mysql_num_rows($result);

    $sql = "SELECT id FROM holidays WHERE userid='$userid' AND type='$type' AND (length='pm' OR length='am') AND approved >= 0 AND approved < 2 ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $half_days=mysql_num_rows($result);

    $days_holiday=$full_days+($half_days/2);
    return $days_holiday;
}


function user_holiday_entitlement($userid)
{
    return db_read_column('holiday_entitlement', 'users', $userid);
}


function contact_realname($id)
{
    $sql = "SELECT forenames, surname FROM contacts WHERE id='$id'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (mysql_num_rows($result) == 0)
    {
        mysql_free_result($result);
        return('Unknown');
    }
    else
    {
        $contact = mysql_fetch_array($result);
        $realname=stripslashes($contact['forenames'].' '.$contact['surname']);
        mysql_free_result($result);
        return($realname);
    }
}


function contact_site($id)
{
    // note: this returns the site _NAME_ not the siteid - INL 17Apr02
    $sql = "SELECT sites.name FROM contacts, sites WHERE contacts.siteid=sites.id AND contacts.id='$id'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (mysql_num_rows($result) == 0)
    {
        mysql_free_result($result);
        return('Unknown');
    }
    else
    {
        list($contactsite) = mysql_fetch_row($result);
        mysql_free_result($result);
        return($contactsite);
    }
}


function contact_siteid($id)
{
    return db_read_column('siteid', 'contacts', $id);
}


function contact_email($id)
{
    return db_read_column('email', 'contacts', $id);
}


function contact_phone($id)
{
    return db_read_column('phone', 'contacts', $id);
}


function contact_fax($id)
{
    return db_read_column('fax', 'contacts', $id);
}


function contact_count_incidents($id)
{
    $sql = "SELECT id FROM incidents WHERE contact='$id'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    $count=mysql_num_rows($result);

    mysql_free_result($result);
    return($count);
}


// Returns a number representing the total number of currently
// OPEN incidents submitted by a given contact.
function contact_count_open_incidents($id)
{
    $sql = "SELECT id FROM incidents WHERE contact=$id AND status<>2";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    $count=mysql_num_rows($result);

    mysql_free_result($result);

    return($count);
}


// Returns a string depending on whether the given contact has support for the given product.
// Returns "yes" = Contact has support for product
// Returns "no" = Contact doesn't have support for product
// Returns "expired" = Contact did have support for product but it has now expired
function contact_productsupport($contactid, $productid)
{
    // check support
    $sql = "SELECT id, expirydate FROM contactproducts WHERE contactid=$contactid AND productid=$productid";
    $result = mysql_query($sql);

    if (mysql_num_rows($result) == 0)
        return("no");
    else
    {
        $now = time();
        $product = mysql_fetch_array($result);
        if ($product["expirydate"] <= $now)
            return("expired");
        else if ($product["expirydate"] > $now)
            return("yes");
    }
}


// Returns an integer representing the expiry day of the month for the given contact's product support.
// Returns 0 if the contact or product does not exist or if
// the contact does not have support for the given product.
function contact_productsupport_expiryday($contactid, $productid)
{
    // check support
    $sql = "SELECT id, expirydate FROM contactproducts WHERE contactid=$contactid AND productid=$productid";
    $result = mysql_query($sql);

    if (mysql_num_rows($result) == 0)
        return(0);
    else
    {
        $productsupport = mysql_fetch_array($result);
        $date_array = getdate($productsupport["expirydate"]);
        return($date_array["mday"]);
    }
}



// Returns an integer representing the expiry month of the year for the given contact's product support.
// Returns 0 if the contact or product does not exist or if the contact does not have support for the given product.
function contact_productsupport_expirymonth($contactid, $productid)
{
    // check support
    $sql = "SELECT id, expirydate FROM contactproducts WHERE contactid=$contactid AND productid=$productid";
    $result = mysql_query($sql);

    if (mysql_num_rows($result) == 0)
        return(0);
    else
    {
        $productsupport = mysql_fetch_array($result);
        $date_array = getdate($productsupport["expirydate"]);
        return($date_array["mon"]);
    }
}


// Returns an integer representing the expiry year for the given contact's product support.
// Returns 0 if the contact or product does not exist or if the contact does not have support for the given product.
function contact_productsupport_expiryyear($contactid, $productid)
{
    // check support
    $sql = "SELECT id, expirydate FROM contactproducts WHERE contactid=$contactid AND productid=$productid";
    $result = mysql_query($sql);

    if (mysql_num_rows($result) == 0)
        return(0);
    else
    {
        $productsupport = mysql_fetch_array($result);
        $date_array = getdate($productsupport["expirydate"]);
        return($date_array["year"]);
    }
}


function contact_vcard($id)
{
    $sql = "SELECT *, sites.name AS sitename, sites.address1 AS siteaddress1, sites.address2 AS siteaddress2, ";
    $sql .= "sites.city AS sitecity, sites.county AS sitecounty, sites.country AS sitecountry, sites.postcode AS sitepostcode ";
    $sql .= "FROM contacts, sites ";
    $sql .= "WHERE contacts.siteid=sites.id AND contacts.id='$id' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $contact = mysql_fetch_object($result);
    $vcard = "BEGIN:VCARD\r\n";
    $vcard .= "N:{$contact->surname};{$contact->forenames};{$contact->salutation}\r\n";
    $vcard .= "FN:{$contact->forenames} {$contact->surname}\r\n";
    if (!empty($contact->jobtitle)) $vcard .= "TITLE:{$contact->jobtitle}\r\n";
    if (!empty($contact->sitename)) $vcard .= "ORG:{$contact->sitename}\r\n";
    if ($contact->dataprotection_phone!='Yes') $vcard .= "TEL;TYPE=WORK:{$contact->phone}\r\n";
    if ($contact->dataprotection_phone!='Yes' && !empty($contact->fax)) $vcard .= "TEL;TYPE=WORK;TYPE=FAX:{$contact->fax}\r\n";
    if ($contact->dataprotection_phone!='Yes' && !empty($contact->mobile)) $vcard .= "TEL;TYPE=WORK;TYPE=CELL:{$contact->mobile}\r\n";
    if ($contact->dataprotection_email!='Yes' && !empty($contact->email)) $vcard .= "EMAIL;TYPE=INTERNET:{$contact->email}\r\n";
    if ($contact->dataprotection_address!='Yes')
    {
        if ($contact->address1 != '') $vcard .= "ADR;WORK:{$contact->address1};{$contact->address2};{$contact->city};{$contact->county};{$contact->postcode};{$contact->country}\r\n";
        else $vcard .= "ADR;WORK:{$contact->siteaddress1};{$contact->siteaddress2};{$contact->sitecity};{$contact->sitecounty};{$contact->sitepostcode};{$contact->sitecountry}\r\n";
    }
    if (!empty($contact->notes)) $vcard .= "NOTE:{$contact->notes}\r\n";
    $vcard .= "REV:".iso_8601_date($contact->timestamp_modified)."\r\n";
    $vcard .= "END:VCARD\r\n";
    return $vcard;
}


function emailtype_to($id)
{
    return db_read_column('tofield', 'emailtype', $id);
}


function emailtype_from($id)
{
    return db_read_column('fromfield', 'emailtype', $id);
}


function emailtype_replyto($id)
{
    return db_read_column('replytofield', 'emailtype', $id);
}


function emailtype_cc($id)
{
    return db_read_column('ccfield', 'emailtype', $id);
}


function emailtype_bcc($id)
{
    return db_read_column('bccfield', 'emailtype', $id);
}


function emailtype_subject($id)
{
    return db_read_column('subjectfield', 'emailtype', $id);
}


function emailtype_body($id)
{
    return db_read_column('body', 'emailtype', $id);
}

function emailtype_customervisibility($id)
{
    return db_read_column('customervisibility', 'emailtype', $id);
}

function emailtype_storeinlog($id)
{
    return db_read_column('storeinlog', 'emailtype', $id);
}

function incident_owner($id)
{
    return db_read_column('owner', 'incidents', $id);
}


function incident_contact($id)
{
    return db_read_column('contact', 'incidents', $id);
}


function incident_maintid($id)
{
    $maintid = db_read_column('maintenanceid', 'incidents', $id);
    if ($maintid == '')
        throw_error("!Error: No matching record while reading in incident_maintid() Incident ID:", $id);

    else return($maintid);
}


function incident_title($id)
{
    return db_read_column('title', 'incidents', $id);
}


function incident_email($id)
{
    return db_read_column('email', 'incidents', $id);
}


function incident_status($id)
{
    return db_read_column('status', 'incidents', $id);
}


function incident_priority($id)
{
    return db_read_column('priority', 'incidents', $id);
}


function incident_externalid($id)
{
    return db_read_column('externalid', 'incidents', $id);
}


function incident_externalengineer($id)
{
    return db_read_column('externalengineer', 'incidents', $id);
}


function incident_externalemail($id)
{
    return db_read_column('externalemail', 'incidents', $id);
}

function incident_ccemail($id)
{
    return db_read_column('ccemail', 'incidents', $id);
}

function incident_timeofnextaction($id)
{
    return db_read_column('timeofnextaction', 'incidents', $id);
}



// Returns a string of HTML nicely formatted for the incident details page containing any additional
// product info for the given incident.
function incident_productinfo_html($incidentid)
{
    // extract appropriate product info
    $sql  = "SELECT *, TRIM(incidentproductinfo.information) AS info FROM productinfo, incidentproductinfo ";
    $sql .= "WHERE incidentid=$incidentid AND productinfoid=productinfo.id AND TRIM(productinfo.information) !='' ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (mysql_num_rows($result) == 0)
    {
        return('<tr><td>No product info</td><td>No product info</td></tr>');
    }
    else
    {
        // generate HTML
        while ($productinfo = mysql_fetch_array($result))
        {
            if (!empty($productinfo['info']))
            {
                $html = "<tr><th>{$productinfo['moreinformation']}:</th><td>";
                $html .= urlencode($productinfo['info']);
                $html .= "</td></tr>\n";
            }
        }
        echo $html;
   }
}


// Create an array containing the service level history
function incident_sla_history($incidentid)
{
    global $CONFIG;
    $working_day_mins = ($CONFIG['end_working_day'] - $CONFIG['start_working_day']) / 60;

    // Not the most efficient but..
    $sql = "SELECT * FROM incidents WHERE id='{$incidentid}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $incident = mysql_fetch_object($result);

    // Get service levels
    $sql = "SELECT * FROM servicelevels WHERE tag='{$incident->servicelevel}' AND priority='{$incident->priority}' ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $level = mysql_fetch_object($result);

    // Loop through the updates in ascending order looking for service level events
    $sql = "SELECT * FROM updates WHERE type='slamet' AND incidentid='{$incidentid}' ORDER BY id ASC, timestamp ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $prevtime=0;
    $idx=0;
    while ($history = mysql_fetch_object($result))
    {
        $slahistory[$idx]['targetsla'] = $history->sla;
        switch ($history->sla)
        {
            case 'initialresponse': $slahistory[$idx]['targettime'] = $level->initial_response_mins; break;
            case 'probdef': $slahistory[$idx]['targettime'] = $level->prob_determ_mins; break;
            case 'actionplan': $slahistory[$idx]['targettime'] = $level->action_plan_mins; break;
            case 'solution': $slahistory[$idx]['targettime'] = ($level->resolution_days * $working_day_mins); break;
            default:
                $slahistory[$idx]['targettime'] = 0;
        }
        if ($prevtime > 0) $slahistory[$idx]['actualtime'] = calculate_incident_working_time($incidentid, $prevtime, $history->timestamp);
        else $slahistory[$idx]['actualtime'] = 0;
        $slahistory[$idx]['timestamp'] = $history->timestamp;
        $slahistory[$idx]['userid'] = $history->userid;
        if ($slahistory[$idx]['actualtime'] <= $slahistory[$idx]['targettime']) $slahistory[$idx]['targetmet'] = TRUE;
        else $slahistory[$idx]['targetmet'] = FALSE;
        $prevtime=$history->timestamp;
        $idx++;
    }
    // Get next target, but only if incident is still open
    if ($incident->status != 2 AND $incident->status != 7)
    {
        $target = incident_get_next_target($incidentid);
        $slahistory[$idx]['targetsla'] = $target->type;
        switch ($target->type)
        {
            case 'initialresponse': $slahistory[$idx]['targettime'] = $level->initial_response_mins; break;
            case 'probdef': $slahistory[$idx]['targettime'] = $level->prob_determ_mins; break;
            case 'actionplan': $slahistory[$idx]['targettime'] = $level->action_plan_mins; break;
            case 'solution': $slahistory[$idx]['targettime'] = ($level->resolution_days * $working_day_mins); break;
            default:
                $slahistory[$idx]['targettime'] = 0;
        }
        $slahistory[$idx]['actualtime'] = $target->since;
        if ($slahistory[$idx]['actualtime'] <= $slahistory[$idx]['targettime']) $slahistory[$idx]['targetmet'] = TRUE;
        else $slahistory[$idx]['targetmet'] = FALSE;
        $slahistory[$idx]['timestamp'] = 0;
    }
    return $slahistory;
}



/*============================================================*/
/*                                                            */
/*                    DROP DOWN FUNCTIONS                     */
/*                                                            */
/*============================================================*/
function array_drop_down($array, $name, $setting='', $enablefield='')
{
    $html = "<select name='$name' id='$name' $enablefield>";
    if (array_key_exists($setting, $array) AND in_array($setting, $array)==FALSE) $usekey=TRUE;
    else $usekey=FALSE;
    foreach($array AS $key => $value)
    {
        $value=htmlentities($value);
        if ($usekey) $html .= "<option value='$key'";
        else $html .= "<option value='$value'";
        if ($usekey) { if ($key==$setting) $html .= " selected='selected'"; }
        else { if ($value==$setting) $html .= " selected='selected'"; }
        $html .= ">$value</option>\n";
    }
    $html .= "</select>\n";
    return $html;
}




// prints the HTML for a drop down list of contacts, with the given name
// and with the given id  selected.
function contact_drop_down($name, $id, $showsite=FALSE)
{
    if ($showsite)
    {
        $sql  = "SELECT contacts.id AS contactid, sites.id AS siteid, surname, forenames, sites.name AS sitename, sites.department AS department ";
        $sql .= "FROM contacts, sites WHERE contacts.siteid=sites.id ORDER BY sites.name, surname ASC, forenames ASC";
    }
    else $sql  = "SELECT id AS contactid, surname, forenames FROM contacts ORDER BY forenames ASC, surname ASC";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    $html = "<select name='$name'>\n";
    if ($id == 0)
        $html .= "<option selected='selected' value='0'></option>\n";
    $prevsite=0;
    while ($contacts = mysql_fetch_array($result))
    {
        if ($showsite AND $prevsite!= $contacts['siteid'] AND $prevsite!=0) $html .= "</optgroup>\n";
        if ($showsite AND $prevsite!= $contacts['siteid']) $html .= "<optgroup label='".htmlentities($contacts['sitename']).", ".htmlentities($contacts['department'])."'>";
        $realname=$contacts['forenames'].' '.$contacts['surname'];
        $html .= "<option ";
        if ($contacts['contactid'] == $id) $html .= "selected='selected' ";
        $html .= "value='{$contacts['contactid']}'>{$realname}";
        $html .= "</option>\n";

        $prevsite = $contacts['siteid'];
    }
    if ($showsite) $html.= "</optgroup>";
    $html .= "</select>\n";
    return $html;
}



/*  prints the HTML for a drop down list of     */
/* contacts along with their site, with the given name and    */
/* with the given id selected.                                */
function contact_site_drop_down($name, $id, $siteid='')
{
   $sql  = "SELECT contacts.id AS contactid, forenames, surname, siteid, sites.name AS sitename FROM contacts, sites ";
   $sql .= "WHERE contacts.siteid=sites.id ";
   if (!empty($siteid)) $sql .= "AND sites.id='$siteid' ";
   $sql .= "ORDER BY surname ASC";
   $result = mysql_query($sql);
   if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

   $html = "<select name='$name'>\n";
   if ($id == 0) $html .= "<option selected='selected' value='0'></option>\n";
   while ($contacts = mysql_fetch_object($result))
   {
        $html .= "<option ";
        if ($contacts->contactid == $id) $html .= "selected='selected' ";
        $html .= "value='{$contacts->contactid}'>";
        $html .= htmlspecialchars("{$contacts->surname}, {$contacts->forenames} of {$contacts->sitename}");
        $html .= "</option>\n";
   }
   $html .= "</select>\n";

   return $html;
}



/*  prints the HTML for a drop down list of     */
/* products, with the given name and with the given id        */
/* selected.                                                  */
function product_drop_down($name, $id)
{
   // extract products
   $sql  = "SELECT id, name FROM products ORDER BY name ASC";
   $result = mysql_query($sql);
   if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

   $html = "<select name='{$name}'>";

   if ($id == 0)
      $html .= "<option selected='selected' value='0'></option>\n";
   while ($products = mysql_fetch_array($result))
   {
        $html .= "<option value='{$products['id']}'";
        if ($products['id']==$id) $html .= " selected='selected'";
        $html .= ">{$products['name']}</option>\n";
   }
   $html .= "</select>\n";
   return $html;
}


function software_drop_down($name, $id)
{
   // extract software
   $sql  = "SELECT id, name FROM software ";
   $sql .= "ORDER BY name ASC";
   $result = mysql_query($sql);
   if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

   $html = "<select name='{$name}'>";

   if ($id == 0)
      $html .= "<option selected='selected' value='0'></option>\n";
   while ($software = mysql_fetch_array($result))
   {
        $html .= "<option value='{$software['id']}'";
        if ($software['id']==$id) $html .= " selected='selected'";
        $html .= ">{$software['name']}</option>\n";
   }
   $html .= "</select>\n";

   return $html;
}


function softwareproduct_drop_down($name, $id, $productid)
{
    // extract software
    $sql  = "SELECT id, name FROM software, softwareproducts WHERE software.id=softwareproducts.softwareid ";
    $sql .= "AND productid='$productid' ";
    $sql .= "ORDER BY name ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) >=1)
    {
        $html ="<select name='$name'>";
        if ($id == 0) $html .= "<option selected='selected' value='0'></option>\n";
        while ($software = mysql_fetch_array($result))
        {
            $html .= "<option";
            if ($software['id'] == $id) $html .= " selected='selected'";
            $html .= " value='{$software['id']}'>{$software['name']}</option>\n";
        }
        $html .= "</select>\n";
    }
    else $html = "-";

    return $html;
}


function vendor_drop_down($name, $id)
{
    $sql = "SELECT id, name FROM vendors ORDER BY name ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $html = "<select name='$name'>";
    if ($id == 0)
        $html .= "<option selected='selected' value='0'></option>\n";
    while ($row = mysql_fetch_array($result))
    {
        $html .= "<option";
        if ($row['id'] == $id) $html .= " selected='selected'";
        $html .= " value='{$row['id']}'>{$row['name']}</option>\n";
    }
   $html .= "</select>";
   return $html;
}



function sitetype_drop_down($name, $id)
{
    $sql = "SELECT typeid, typename FROM sitetypes ORDER BY typename ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $html .= "<select name='$name'>\n";
    if ($id == 0) $html .= "<option selected='selected' value='0'></option>\n";
    while ($row = mysql_fetch_array($result))
    {
        $html .= "<option ";
        if ($row['typeid'] == $id) { $html .="selected='selected' "; }
        $html .= "value='{$row['typeid']}'>{$row['typename']}</option>\n";
    }
    $html .= "</select>";
    return $html;
}



// Prints the HTML for a drop down list of
// supported products for the given contact and with the
// given name and with the given product selected
// FIXME this should use the contract and not he contact
function supported_product_drop_down($name, $contactid, $productid)
{
    global $CONFIG;

    $sql = "SELECT *,products.id AS productid, products.name AS productname FROM supportcontacts,maintenance,products ";
    $sql .= "WHERE supportcontacts.maintenanceid=maintenance.id AND maintenance.product=products.id ";
    $sql .= "AND supportcontacts.contactid='$contactid'";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if ($CONFIG['debug']) $html .= "<!-- Original product {$productid}-->";
    $html .= "<select name=\"$name\">\n";
    if ($productid == 0)
        $html .= "<option selected='selected' value='0'>No Contract - Not Product Related</option>\n";
    if ($productid == -1)
        $html .= "<option selected='selected' value='0'></option>\n";

    while ($products = mysql_fetch_array($result))
    {
        $remainingstring=incidents_remaining($products["incidentpoolid"])." Incidents Left";  // string containing text stating number of incidents remaining
        $html .= "<option ";
        if ($productid == $products['productid']) $html .= "selected='selected' ";
        $html .= "value='{$products['productid']}'>";
        $html .= servicelevel_name($products['servicelevelid'])." ".$products['productname'].", Exp:".date($CONFIG['dateformat_shortdate'], $products["expirydate"]).", $remainingstring";
        $html .= "</option>\n";
    }
    $html .= "</select>\n";
    return $html;
}


//  prints the HTML for a drop down list of  users, with the given name and with the given id selected.
function user_drop_down($name, $id, $accepting=TRUE, $exclude=FALSE, $attribs="")
{
    // INL 1Jul03 Now only shows users with status > 0 (ie current users)
    // INL 2Nov04 Optional accepting field, to hide the status 'Not Accepting'
    // INL 19Jan05 Option exclude field to exclude a user, or an array of
    // users

    $sql  = "SELECT id, realname, accepting FROM users WHERE status > 0 ORDER BY realname ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    echo "<select name='{$name}'";
    if (!empty($attribs)) echo " $attribs";
    echo ">\n";
    if ($id == 0)
        echo "<option selected='selected' value='0'></option>\n";
    while ($users = mysql_fetch_array($result))
    {
        $show=TRUE;
        if ($exclude!=FALSE)
        {
            if (is_array($exclude))
            {
                if (!in_array($users['id'], $exclude)) $show=TRUE;
                else $show=FALSE;
            }
            else
            {
                if ($exclude!=$users['id']) $show=TRUE;
            }
        }
        if ($show==TRUE)
        {
            echo "<option ";
            if ($users["id"] == $id) echo "selected='selected' ";
            if ($users['accepting']=='No' AND $accepting==TRUE) echo " class='expired' ";
            echo "value='{$users['id']}'>";
            echo "{$users['realname']}";
            if ($users['accepting']=='No' AND $accepting==TRUE) echo ", Not Accepting";
            echo "</option>\n";
        }
    }
    echo "</select>\n";
}


function role_drop_down($name, $id)
{
   $sql  = "SELECT id, rolename FROM roles ORDER BY rolename ASC";
   $result = mysql_query($sql);
   if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

   $html = "<select name='{$name}'>";
   if ($id == 0) $html .= "<option selected='selected' value='0'></option>\n";
   while ($role = mysql_fetch_object($result))
   {
        $html .= "<option value='{$role->id}'";
        if ($role->id==$id) $html .= " selected='selected'";
        $html .= ">{$role->rolename}</option>\n";
   }
   $html .= "</select>\n";
   return $html;
}



function interfacestyle_drop_down($name, $id)
{
   // extract statuses
   $sql  = "SELECT id, name FROM interfacestyles ORDER BY name ASC";
   $result = mysql_query($sql);

   // print HTML
   ?>
   <select name="<?php echo $name ?>">
   <?php
   if ($id == 0)
      echo "<option selected='selected' value='0'></option>\n";
   while ($styles = mysql_fetch_array($result))
   {
      ?><option <?php if ($styles["id"] == $id) { ?>selected='selected' <?php } ?>value='<?php echo $styles["id"] ?>'><?php echo $styles["name"] ?></option><?php
      echo "\n";
   }
   echo "</select>\n";
}


function interface_style($id)
{
    global $CONFIG;

    $sql  = "SELECT cssurl, headerhtml FROM interfacestyles WHERE id='$id'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) == 0)
    {
        mysql_free_result($result);
        $style = (array($CONFIG['default_css_url'],''));  // default style
    }
    else
    {
        $style = mysql_fetch_assoc($result);
        mysql_free_result($result);
    }
    if (empty($style)) $style = (array($CONFIG['default_css_url'],''));  // default style
    return($style);
}


//  prints the HTML for a drop down list of
// incident status names (EXCLUDING 'CLOSED'), with the given
// name and with the given id selected.
function incidentstatus_drop_down($name, $id)
{
    // extract statuses
    $sql  = "SELECT id, name FROM incidentstatus WHERE id >0 AND id<>2 AND id<>7 ORDER BY name ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) < 1) trigger_error("Zero rows returned",E_USER_WARNING);

    $html = "<select name='{$name}'>";
    // if ($id == 0) $html .= "<option selected='selected' value='0'></option>\n";
    while ($statuses = mysql_fetch_array($result))
    {
        $html .= "<option ";
        if ($statuses['id'] == $id) $html .= "selected='selected' ";
        $html .= "value='{$statuses['id']}'";
        $html .= ">{$statuses['name']}</option>\n";
    }
    $html .= "</select>\n";
    return $html;
}



/*  prints the HTML for a drop down list of     */
/* incident status names, with the given name and with the    */
/* given id selected. Also prints an 'All' option with value  */
/* 'all' for viewing all incidents.                           */

function incidentstatus_drop_down_all($name, $id)
   {

   // extract statuses
   $sql  = "SELECT id, name FROM incidentstatus ORDER BY name ASC";
   $result = mysql_query($sql);

   // print HTML
   ?>
   <select name="<?php echo $name ?>">
   <?php
   if ($id == 0)
      echo "<option selected='selected' value=\"all\">All</option>\n";
   else
      echo "<option value=\"all\">All</option>\n";
   while ($statuses = mysql_fetch_array($result))
      {
      ?><option <?php if ($statuses["id"] == $id) { ?>selected='selected' <?php } ?>value='<?php echo $statuses["id"] ?>'><?php echo $statuses["name"] ?></option><?php
      echo "\n";
      }
   ?>
   </select>
   <?php

   }



/*  prints the HTML for a drop down list of     */
/* closing statuses, with the given name and with the given   */
/* id selected.                                               */

function closingstatus_drop_down($name, $id)
   {

   // extract statuses
   $sql  = "SELECT id, name FROM closingstatus ORDER BY name ASC";
   $result = mysql_query($sql);

   // print HTML
   ?>
   <select name="<?php echo $name ?>">
   <?php
   if ($id == 0)
      echo "<option selected='selected' value='0'></option>\n";
   while ($statuses = mysql_fetch_array($result))
      {
      ?><option <?php if ($statuses["id"] == $id) { ?>selected='selected' <?php } ?>value='<?php echo $statuses["id"] ?>'><?php echo $statuses["name"] ?></option><?php
      echo "\n";
      }
   ?>
   </select>
   <?php

   }



/*  prints the HTML for a drop down list of     */
/* user status names, with the given name and with the given  */
/* id selected.                                               */
function userstatus_drop_down($name, $id, $userdisable=FALSE)
{
   // extract statuses
   $sql  = "SELECT id, name FROM userstatus ORDER BY name ASC";
   $result = mysql_query($sql);

   $html = "<select name='$name'>\n";
   if ($userdisable) $html .= "<option style='color: red;' selected='selected' value='0'>ACCOUNT DISABLED</option>\n";
   while ($statuses = mysql_fetch_array($result))
   {

        $html .= "<option ";
        if ($statuses["id"] == $id) $html .= "selected='selected' ";
        $html .= "value='{$statuses["id"]}'>";
        $html .= "{$statuses["name"]}</option>\n";
   }
   $html .= "</select>\n";

   echo $html;
}



/*  prints the HTML for a drop down list of     */
/* user status names, with the given name and with the given  */
/* id selected.                                               */
function userstatus_bardrop_down($name, $id)
{
   // extract statuses
   $sql  = "SELECT id, name FROM userstatus ORDER BY name ASC";
   $result = mysql_query($sql);

   // print HTML

    echo "<select name='$name' title='Set your status' onchange=\"if (this.options[this.selectedIndex].value != 'null') { window.open(this.options[this.selectedIndex].value,'_top') }\">\n";
    while ($statuses = mysql_fetch_array($result))
    {
      ?><option <?php if ($statuses["id"] == $id) { ?>selected='selected' <?php }; echo "value='set_user_status.php?mode=setstatus&amp;userstatus=".$statuses['id']; ?>'><?php echo $statuses["name"] ?></option><?php
      echo "\n";
    }
   // <option value='set_user_status.php?mode=return' style="color: #404040; border-bottom: 1px solid black;"></option>
   ?>
   <option value='set_user_status.php?mode=setaccepting&amp;accepting=Yes' style="color: #00AA00; border-top: 1px solid black;">Accepting</option>
   <option value='set_user_status.php?mode=setaccepting&amp;accepting=No' style="color: #FF0000;">Not Accepting</option>
   </select>
   <?php
}




/*  prints the HTML for a drop down list of     */
/* email types, with the given name and with the given id     */
/* selected.                                                  */

function emailtype_drop_down($name, $id)
{
    // INL 22Apr05 Added a filter to only show user templates

   // extract statuses
   $sql  = "SELECT id, name, description FROM emailtype WHERE type='user' ORDER BY name ASC";
   $result = mysql_query($sql);

   // print HTML
  echo "<select name='{$name}'>";
  if ($id == 0)  echo "<option selected='selected' value='0'></option>\n";
  while ($emailtypes = mysql_fetch_array($result))
  {
      echo "<option ";
      if (!empty($emailtypes['description'])) echo "title='{$emailtypes['description']}' ";
      if ($emailtypes["id"] == $id) { echo "selected='selected '"; }
      echo "value='{$emailtypes['id']}'>{$emailtypes['name']}</option>";
      echo "\n";
  }
  echo "</select>\n";
}



// prints the HTML for a drop down list of priorities, with the given name
// and with the given id selected
function priority_drop_down($name, $id, $max=4, $disable=FALSE)
{
    global $CONFIG;
    // INL 8Oct02 - Removed DB Query
    $html = "<select id='priority' name='$name' ";
    if ($disable) $html .= "disabled='disabled'";
    $html .= ">";
    if ($id == 0) $html .= "<option selected='selected' value='0'></option>\n";
    $html .= "<option style='text-indent: 14px; background-image: url({$CONFIG['application_webpath']}/images/low_priority.gif); background-repeat:no-repeat;' value='1'";
    if ($id==1) $html .= " selected='selected'";
    $html .= ">Low</option>\n";
    $html .= "<option style='text-indent: 14px; background-image: url({$CONFIG['application_webpath']}/images/med_priority.gif); background-repeat:no-repeat;' value='2'";
    if ($id==2) $html .= " selected='selected'";
    $html .= ">Medium</option>\n";
    $html .= "<option style='text-indent: 14px; background-image: url({$CONFIG['application_webpath']}/images/high_priority.gif); background-repeat:no-repeat;' value='3'";
    if ($id==3) $html .= " selected='selected'";
    $html .= ">High</option>\n";
    if ($max >=4)
    {
        $html .= "<option style='text-indent: 14px; background-image: url({$CONFIG['application_webpath']}/images/crit_priority.gif); background-repeat:no-repeat;' value='4'";
        if ($id==4) $html .= " selected='selected'";
        $html .= ">Critical</option>\n";
    }
    $html .= "</select>\n";
    return $html;
}



/*  prints the HTML for a multiple select list  */
/* of products, with the given name and with all the products */
/* the given customer has support for already selected.       */

function contactproducts_drop_down($name, $contactid)
{
   // extract products
   $sql  = "SELECT * FROM products ORDER BY name ASC";
   $result = mysql_query($sql);

   // print HTML
   ?>
   <select multiple="mutliple" name="<?php echo $name ?>" size="10">
   <?php
   while ($products = mysql_fetch_array($result))
   {
      ?><option <?php if (contact_productsupport($contactid, $products["id"]) == 1) { ?>selected='selected' <?php } ?>value='<?php echo $products["id"] ?>'><?php echo $products["name"] ?></option><?php
      echo "\n";
   }
   ?>
   </select>
   <?php
}



/*  prints the HTML for a drop down list of     */
/* 'yes' and 'no' with the gven name and with the option      */
/* selected depending on the supplied userid                  */
function accepting_drop_down($name, $userid)
{
   if (user_accepting($userid) == "Yes")
      {
      echo "<select name=\"$name\">\n";
      echo "<option selected='selected' value=\"Yes\">Yes</option>\n";
      echo "<option value=\"No\">No</option>\n";
      echo "</select>\n";
      }
   else
      {
      echo "<select name=\"$name\">\n";
      echo "<option value=\"Yes\">Yes</option>\n";
      echo "<option selected='selected' value=\"No\">No</option>\n";
      echo "</select>\n";
      }
}


/*  prints the HTML for a drop down list of     */
/* months of the year with the gven name and with the option  */
/* selected depending on the supplied argument                */
function month_drop_down($name, $month)
{
    $months = array ('Dummy', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    echo "<select name=\"$name\">\n";
    if ($month == 0) echo "<option value='0'>Select A Month</option>\n";

    for ($counter = 1; $counter <= 12; $counter++)
    {
        echo "<option ";
        if ($counter == $month) echo "selected='selected' ";
        echo "value=\"$counter\">$months[$counter]</option>\n";
    }

    echo "</select>\n";
}



/*  prints the HTML for a drop down list of     */
/* years from 2000 to 2030 with the gven name and with the    */
/* option selected depending on the supplied argument         */
function year_drop_down($name, $year, $pre=5, $post=30)
{
   if ($year=='') $year=date('Y');
   echo "<select name=\"$name\">\n";
   if ($year == 0) echo "<option value='0'>Select A Year</option>\n";

   for ($counter = $year-$pre; $counter <= $year+$post; $counter++)
      {
      echo "<option ";
      if ($counter == $year) echo "selected='selected' ";
      echo "value='$counter'>$counter</option>\n";
      }

   echo "</select>\n";
}


function escalation_path_drop_down($name, $id)
{
   $sql  = "SELECT id, name FROM escalationpaths ";
   $sql .= "ORDER BY name ASC";
   $result = mysql_query($sql);
   if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
   $html = "<select name='{$name}'>";
   $html .= "<option selected='selected' value='0'>None</option>\n";
   while ($path = mysql_fetch_array($result))
   {
        $html .= "<option value='{$path['id']}'";
        if ($path['id']==$id) $html .= " selected='selected'";
        $html .= ">{$path['name']}</option>\n";
   }
   $html .= "</select>\n";

   return $html;
}



/*============================================================*/
/*                                                            */
/*                      OTHER FUNCTIONS                       */
/*                                                            */
/*============================================================*/


/* Returns a string representing the name of   */
/* the given priority. Returns an empty string if the         */
/* priority does not exist.                                   */
function priority_name($id)
{
    // INL 10Oct02 Replaced database query with a simple
    // switch statement to speed this function up.
    // Priorities are not likely to change anyway
    switch ($id)
    {
        case 1: $value = 'Low'; break;
        case 2: $value = 'Medium'; break;
        case 3: $value = 'High'; break;
        case 4: $value = 'Critical'; break;
        case '': $value = 'Not set'; break;
        default: $value = 'Unknown'; break;
   }
   return $value;
}

// Returns HTML for an icon to indicate priority
function priority_icon($id)
{
    global $CONFIG;
    switch ($id)
    {
        case 1: $html = "<img src='{$CONFIG['application_webpath']}images/low_priority.gif' width='10' height='16' alt='Low Priority' title='Low Priority' />"; break;
        case 2: $html = "<img src='{$CONFIG['application_webpath']}images/med_priority.gif' width='10' height='16' alt='Medium Priority' title='Medium Priority' />"; break;
        case 3: $html = "<img src='{$CONFIG['application_webpath']}images/high_priority.gif' width='10' height='16' alt='High Priority' title='High Priority' />"; break;
        case 4: $html = "<img src='{$CONFIG['application_webpath']}images/crit_priority.gif' width='16' height='16' alt='Critical Priority' title='Critical Priority' />"; break;
        default: $html = '?'; break;
   }
   return $html;
}


// Returns an array of fields from the most recent update record for a given incident id
function incident_lastupdate($id)
{
    // Find the most recent update
    $sql = "SELECT userid, type, sla, currentowner, currentstatus, LEFT(bodytext,500) AS body, timestamp, nextaction, id ";
    $sql .= "FROM updates WHERE incidentid='$id' ORDER BY timestamp DESC, id DESC LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (mysql_num_rows($result) == 0) trigger_error("Zero records while retrieving incident last update",E_USER_WARNING);
    else
    {
        $update = mysql_fetch_array($result);

        // In certain circumstances go back even further, find an earlier update
        if(($update['type'] == "reassigning" AND !isset($update['body'])) OR ($update['type'] == 'slamet' AND $row['sla'] == 'opened'))
        {
            //check if the previous update was by userid == 0 if so then we can assume this is a new call
            $sqlPrevious = "SELECT userid, type, currentowner, currentstatus, LEFT(bodytext,500) AS body, timestamp, nextaction, id, sla, type ";
            $sqlPrevious .= "FROM updates WHERE id < ".$update['id']." AND incidentid = '$id' ORDER BY id DESC";
            $resultPrevious = mysql_query($sqlPrevious);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);


            if (mysql_num_rows($result) == 0) trigger_error("Zero records while retrieving incident last update",E_USER_WARNING);
            else
            {
                $row = mysql_fetch_array($resultPrevious);
                if($row['userid'] == 0)
                {
                    $last;
                    //This was an initial assignment so we now want the first update - looping round data retrieved rather than second query
                    while($row = mysql_fetch_array($resultPrevious))
                    {
                        $last = $row;
                        if($row['userid'] != 0)
                        {
                            if($row['type'] ==  'slamet')
                            {
                                $last = mysql_fetch_array($resultPrevious);
                            }
                            break;
                        }
                    }
                    mysql_free_result($resultPrevious);

                    return array($last['userid'], $last['type'] ,$last['currentowner'], $last['currentstatus'], stripslashes($last['body']), $last['timestamp'], $last['nextaction'], $last['id']);

                }
            }

        }
        mysql_free_result($result);
        // Remove Tags from update Body
        $update['body']=trim($update['body']);
        $update['body'] = stripslashes($update['body']);
        return array($update['userid'], $update['type'] ,$update['currentowner'], $update['currentstatus'], $update['body'], $update['timestamp'], $update['nextaction'], $update['id']);
    }
}


/* Returns a string representing the name of   */
/* the given incident status. Returns an empty string if the  */
/* status does not exist.                                     */
function incidentstatus_name($id)
{
   // extract priority
   $sql = "SELECT name FROM incidentstatus WHERE id='$id'";
   $result = mysql_query($sql);
   if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);


   if (mysql_num_rows($result) == 0)
   {
      return("");
   }
   else
   {
      $incidentstatus = mysql_fetch_array($result);
      return($incidentstatus["name"]);
   }

   // free result and disconnect
   mysql_free_result($result);
}


function closingstatus_name($id)
{
    if ($id!='')
        $closingstatus = db_read_column('name', 'closingstatus', $id);
    else $closingstatus = 'Incident Not Closed';

    return($closingstatus);
}



/* Returns a string representing the name of   */
/* the given user status. Returns an empty string if the      */
/* status does not exist.                                     */
function userstatus_name($id)
{
    return db_read_column('name', 'userstatus', $id);
}



/* Returns a string representing the name of   */
/* the given product. Returns an empty string if the product  */
/* does not exist.                                            */
function product_name($id)
{
    return db_read_column('name', 'products', $id);
}



// Returns a string with all occurrences of emailtype special identifiers (in angle brackets) replaced
// with their appropriate values.
function emailtype_replace_specials($string, $incidentid, $userid=0)
{
    global $CONFIG, $application_version, $application_version_string;
    if ($incidentid=='') throw_error('incident ID was blank in emailtype_replace_specials()',$string);

    $contactid=incident_contact($incidentid);
    if ($contactid==0) throw_error('cannot obtain contact ID in email_replace_specials()',$contactid);

    // INL 13Jun03 Do one query to grab the incident details instead of doing a query
    // per replace-keyword - this should save a few queries

    $sql = "SELECT * FROM incidents WHERE id='$incidentid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $incident=mysql_fetch_object($result);

    $email_regex = array(0 => '/<contactemail>/s',
                    1 => '/<contactname>/s',
                    2 => '/<contactfirstname>/s',
                    3 => '/<contactsite>/s',
                    4 => '/<contactphone>/s',
                    5 => '/<contactmanager>/s',
                    6 => '/<contactnotify>/s',
                    7 => '/<incidentid>/s',
                    8 => '/<incidentexternalid>/s',
                    9 => '/<incidentccemail>/s',
                    10 => '/<incidentexternalengineer>/s',
                    11 => '/<incidentexternalengineerfirstname>/s',
                    12 => '/<incidentexternalemail>/s',
                    13 => '/<incidenttitle>/s',
                    14 => '/<incidentpriority>/s',
                    15 => '/<incidentsoftware>/s',
                    16 => '/<incidentowner>/s',
                    17 => '/<useremail>/s',
                    18 => '/<userrealname>/s',
                    19 => "/<applicationname>/s",
                    20 => '/<applicationshortname>/s',
                    21 => '/<applicationversion>/s',
                    22 => '/<supportemail>/s',
                    23 => '/<salesemail>/s',
                    24 => '/<supportmanageremail>/s',
                    25 => '/<signature>/s',
                    26 => '/<globalsignature>/s',
                    27 => '/<todaysdate>/s'
                );

    $email_replace = array(0 => contact_email($contactid),
                    1 => contact_realname($contactid),
                    2 => strtok(contact_realname($contactid),' '),
                    3 => contact_site($contactid),
                    4 => contact_phone($contactid),
                    5 => contact_manager_email($contactid),
                    6 => contact_notify_email($contactid),
                    7 => $incidentid,
                    8 => $incident->externalid,
                    9 => incident_ccemail($incidentid),
                    10 => incident_externalengineer($incidentid),
                    11 => strtok(incident_externalengineer($incidentid),' '),
                    12 => incident_externalemail($incidentid),
                    13 => incident_title($incidentid),
                    14 => priority_name(incident_priority($incidentid)),
                    15 => software_name($incident->softwareid),
                    16 => user_realname($incident->owner),
                    17 => user_email($userid),
                    18 => user_realname($userid),
                    19 => $CONFIG['application_name'],
                    20 => $CONFIG['application_shortname'],
                    21 => $application_version_string,
                    22 => $CONFIG['support_email'],
                    23 => $CONFIG['sales_email'],
                    24 => $CONFIG['support_manager_email'],
                    25 => user_signature($userid),
                    26 => global_signature(),
                    27 => date("jS F Y")
                );

    if($incident->towner != 0)
    {
        //$return_string = str_replace("<incidentreassignemailaddress>", user_email($incident->towner), $return_string);
        $email_regex[] = '/<incidentreassignemailaddress>/s';
        $email_replace[] = user_email($incident->towner);
    }
    else
    {
        //$return_string = str_replace("<incidentreassignemailaddress>", user_email($incident->owner), $return_string);
        $email_regex[] = '/<incidentreassignemailaddress>/s';
        $email_replace[] = user_email($incident->owner);
    }

    //TODO move to seperate plugin
    if (function_exists('escid_novellid'))
    {
        $email_regex[] = '/<novellid>/s';
        $email_replace[] = escid_novellid($userid);
    }

    if (function_exists('escid_microsoftid'))
    {
        $email_regex[] = '/<microsoftid>/s';
        $email_replace[] = escid_microsoftid($userid);
    }

    if (function_exists('escid_dseid'))
    {
        $email_regex[] = '/<dseid>/s';
        $email_replace[] = escid_dseid($userid);
    }

    if (function_exists('escid_cheyenneid'))
    {
        $email_regex[] = '/<cheyenneid>/s';
        $email_replace[] = escid_cheyenneid($userid);
    }
    return preg_replace($email_regex,$email_replace,$string);
}



/*  formats the given number of seconds into a  */
/* string containing the number of days, hours and minutes.   */
/* If the argument is less than 60 returns 1 minute           */
function format_seconds($seconds)
{
   if ($seconds <= 0) return '0 mins';
   elseif ($seconds <= 60 AND $seconds >= 1)
   {
      return ("1 min");
   }
   else
   {
      $years = floor($seconds / ( 2629800 * 12));
      $remainder = ($seconds % ( 2629800 * 12));
      $months = floor($remainder / 2629800);
      $remainder = ($seconds % 2629800);
      $days = floor($remainder / 86400);
      $remainder = ($remainder % 86400);
      $hours = floor($remainder / 3600);
      $remainder = ($remainder % 3600);
      $minutes = floor($remainder / 60);
      if ($years>0)
      {
        $return_string .= "$years Years ";
      }
      if ($months>0 AND $years<2)
      {
        if ($months==1) $return_string .= "1 Month ";
        else $return_string .= "$months Months ";
      }
      if ($days>0 AND $months<6)
      {
        if ($days==1) $return_string .= "1 Day ";
        else $return_string .= "$days Days ";
      }
      if ($months<1 AND $days<7 AND $hours>0)
      {
        if ($hours==1) $return_string .= "1 hour ";
        else $return_string .= "$hours hours ";
      }
      elseif ($months<1 AND $days<1 AND $hours>0)
      {
        if ($minutes==1) $return_string .= "1 minute";
        elseif ($minutes>1) $return_string .= "$minutes minutes";
      }

      if ($months<1 AND $days<1 AND $hours<1 AND $minutes>0)
      {
        $return_string .= "$minutes minutes";
      }
      /*
          if ($months<1 AND $days<1 AND $hours<1 AND $minutes>0)
      {
         $return_string .= "$minutes minutes";
      }
      */
      $return_string=trim($return_string);
      return($return_string);
   }
}


// Return a string containing the time remaining, the time in minutes
// provided as an argument should be in working-days. (eg. 9am - 5pm)
function format_workday_minutes($minutes)
{
    global $CONFIG;
    $working_day_mins = ($CONFIG['end_working_day'] - $CONFIG['start_working_day']) / 60;
    $days = floor($minutes / $working_day_mins);
    $remainder = ($minutes % $working_day_mins);
    $hours = floor($remainder / 60);
    $minutes = floor($remainder % 60);

    if ($days == 1) $time = "{$days} working day";
    elseif ($days > 1) $time = "{$days} working days";

    if ($days <= 3 AND $hours == 1) $time .= " {$hours} hour";
    elseif ($days <= 3 AND $hours > 1) $time .= " {$hours} hours";
    elseif ($days > 3 AND $hours >= 1) $time = "&gt; ".$time;

    if ($days < 1 AND $hours < 8 AND $minutes == 1) $time .= " {$minutes} minute";
    elseif ($days < 1 AND $hours < 8 AND $minutes > 1) $time .= " {$minutes} minutes";

    if ($days == 1 AND $hours < 8 AND $minutes == 1) $time .= " {$minutes} min";
    elseif ($days == 1 AND $hours < 8 AND $minutes > 1) $time .= " {$minutes} mins";

    $time = trim($time);

    return ($time);
}


// Make a readable and friendly date, i.e. say Today, or Yesterday if it is
function format_date_friendly($date)
{
    global $CONFIG;
    if (date('dmy', $date) == date('dmy', time()))
        $datestring = "Today @ ".date($CONFIG['dateformat_time'], $date);
    elseif (date('dmy', $date) == date('dmy', (time()-86400)))
        $datestring = "Yesterday @ ".date($CONFIG['dateformat_time'], $date);
    elseif ($date < $now-86400 AND
            $date > $now-(86400*6))
        $datestring = date('l', $date)." @ ".date($CONFIG['dateformat_time'], $date);
    else
        $datestring = date($CONFIG['dateformat_datetime'], $date);

    return ($datestring);
}

/*  generates a confirmation page containing    */
/* the given message and which refreshes after the given      */
/* number of seconds to the given location.                   */
function confirmation_page($refreshtime, $location, $message)
{
   global $sit, $CONFIG;
   ?>
   <html>
   <head>

   <?php
   echo "<title>{$CONFIG['application_shortname']} Confirmation Page</title>";
   echo "<meta http-equiv=\"refresh\" content=\"$refreshtime; url=$location\" />\n";
   $style = interface_style($_SESSION['style']);
   echo "<link rel='stylesheet' href='{$CONFIG['application_webpath']}styles/webtrack.css' />\n";
   ?>
   </head>
   <body>
   <?php echo "$message\n" ?>
   </body>
   </html>
   <?php
}




/*  calculates the value of the unix timestamp  */
/* which is the number of given days, hours and minutes from  */
/* the current time.                                          */
function calculate_time_of_next_action($days, $hours, $minutes)
{
   $now = time();
   $return_value = $now + ($days * 86400) + ($hours * 3600) + ($minutes * 60);
   return($return_value);
}


// Returns the HTML for a drop down list of service levels,
// with the given name and with the given id selected.
function servicelevel_drop_down($name, $id, $collapse=FALSE)
{
   if ($collapse) $sql = "SELECT DISTINCT id, tag FROM servicelevels";
   else $sql  = "SELECT id, priority FROM servicelevels";
   $result = mysql_query($sql);

   $html = "<select name='$name'>\n";
   // INL 30Mar06 Removed this ability to select a null service level
   // if ($id == 0) $html .= "<option selected='selected' value='0'></option>\n";
   while ($servicelevels = mysql_fetch_object($result))
   {
      $html .= "<option ";
      $html .= "value='{$servicelevels->id}' ";
      if ($servicelevels->id == $id) $html .= "selected='selected'";
      $html .= ">";
      if ($collapse) $html .= $servicelevels->tag;
      else $html .= "{$servicelevels->tag} ".priority_name($servicelevels->priority);
      $html .= "</option>\n";
   }
   $html .= "</select>";
   return $html;
}


function serviceleveltag_drop_down($name, $tag, $collapse=FALSE)
{
   if ($collapse) $sql = "SELECT DISTINCT tag FROM servicelevels";
   else $sql  = "SELECT tag, priority FROM servicelevels";
   $result = mysql_query($sql);

   $html = "<select name='$name'>\n";
   if ($tag == '') $html .= "<option selected='selected' value=''></option>\n";
   while ($servicelevels = mysql_fetch_object($result))
   {
      $html .= "<option ";
      $html .= "value='{$servicelevels->tag}' ";
      if ($servicelevels->tag == $tag) $html .= "selected='selected'";
      $html .= ">";
      if ($collapse) $html .= $servicelevels->tag;
      else $html .= "{$servicelevels->tag} ".priority_name($servicelevels->priority);
      $html .= "</option>\n";
   }
   $html .= "</select>";
   return $html;
}


/* Returns a string representing the name of   */
/* the given servicelevel. Returns an empty string if the     */
/* priority does not exist.                                   */
function servicelevel_name($id)
{
    global $CONFIG;

    $servicelevel = db_read_column('tag', 'servicelevels', $id);

    if ($servicelevel == '')
        $servicelevel=$CONFIG['default_service_level'];
    return($servicelevel);
}


// FIXME: default service level needs changeing/checking
function maintenance_servicelevel($maintid)
{
  $sql = "SELECT servicelevelid FROM maintenance WHERE id='$maintid' ";
  $result = mysql_query($sql);
  if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
  if (mysql_num_rows($result) < 1)
  {
    // in case there is no maintenance contract associated with the incident, use default service level
    // if there is a maintenance contract then we should throw an error because there should be
    // service level
    if ($maintid==0) $servicelevelid=1;
    ## else throw_error('!Error: Could not find a service level for maintenance ID:', $maintid);
  }
  else
  {
    list($servicelevelid) = mysql_fetch_row($result);
  }

  return $servicelevelid;
}


function maintenance_siteid($id)
{
   return db_read_column('site', 'maintenance', $id);
}


// Temporary solution, eventually we will move away from using servicelevel id's
// and just use tags instead
function servicelevel_id2tag($id)
{
    return db_read_column('tag', 'servicelevels', $id);
}


// Returns the number of remaining incidents given an incident pool id
// Returns 'Unlimited' if theres no match on ID
function incidents_remaining($id)
{
    $remainging = db_read_column('incidentsremaining', 'incidentpools', $id);
    if (empty($remaining)) $remaining = '&infin;';

    return($remaining);
}


// OBSOLETE
/* Returns an incidentpoolid given             */
/* a contactproduct id        */
/* Returns 0 if none is found, meaning unlimited */
function incidentpoolid($id)
{
    return db_read_column('incidentpoolid', 'contactproducts', $id);
}


function decrement_free_incidents($siteid)
{
    $sql = "UPDATE sites SET freesupport = (freesupport - 1) WHERE id='$siteid'";
    mysql_query($sql);
    if (mysql_affected_rows() < 1) trigger_error("No rows affected while updating freesupport",E_USER_ERROR);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    else return TRUE;
}


function increment_incidents_used($maintid)
{
    $sql = "UPDATE maintenance SET incidents_used = (incidents_used + 1) WHERE id='$maintid'";
    mysql_query($sql);
    if (mysql_affected_rows() < 1) trigger_error("No rows affected while updating freesupport",E_USER_ERROR);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    else return TRUE;
}


// Functions to handle error reporting
function error_handler($errno, $errstr, $errfile, $errline)
{
  switch ($errno)
  {
    case E_USER_ERROR:    // fatal
      $errormessage="USER_FATAL [$errno] $errstr<br />\n";
      $errordetails="  Warning in line ".$errline." of file ".$errfile;
      $errordetails.=", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
      $errordetails.="Aborting...<br />\n";
      throw_fatal_error($errormessage,$errordetails);
    exit -1;
    break;

    case E_USER_WARNING:  // warning
      $errormessage="USER WARNING [$errno] $errstr<br />\n";
      $errordetails="  Warning in line ".$errline." of file ".$errfile;
      $errordetails.=", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
      $errordetails.="Aborting...<br />\n";
      throw_error($errormessage,$errordetails);

    break;

    case E_USER_NOTICE:   // comment
      $errormessage="USER NOTICE [$errno] $errstr<br />\n";
      $errordetails="  Warning in line ".$errline." of file ".$errfile;
      $errordetails.=", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
      $errordetails.="Aborting...<br />\n";
      throw_error($errormessage,$errordetails);
    break;

    case E_NOTICE:
      // do nothing, this is usually benign
    break;

    case E_WARNING:
      $errormessage="WARNING [$errno] $errstr<br />\n";
      $errordetails="  Warning in line ".$errline." of file ".$errfile;
      $errordetails.=", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
      $errordetails.="Aborting...<br />\n";
      throw_error($errormessage,$errordetails);
    break;

    case E_PARSE:
      $errormessage="PARSE [$errno] $errstr<br />\n";
      $errordetails="  Parse error in line ".$errline." of file ".$errfile;
      $errordetails.=", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
      $errordetails.="Aborting...<br />\n";
      throw_fatal_error($errormessage,$errordetails);
    break;

    case E_CORE_ERROR:
      $errormessage="CORE ERROR [$errno] $errstr<br />\n";
      $errordetails="  Error in line ".$errline." of file ".$errfile;
      $errordetails.=", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
      $errordetails.="Aborting...<br />\n";
      throw_fatal_error($errormessage,$errordetails);
    break;

    case E_CORE_WARNING:
      $errormessage="CORE WARNING [$errno] $errstr<br />\n";
      $errordetails="  Warning in line ".$errline." of file ".$errfile;
      $errordetails.=", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
      $errordetails.="Aborting...<br />\n";
      throw_error($errormessage,$errordetails);
    break;


    default:
      $errormessage="[$errno] $errstr<br />\n";
      $errordetails="  in line ".$errline." of file ".$errfile;
      $errordetails.=", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
      $errordetails.="Aborting...<br />\n";
      throw_error($errormessage,$errordetails);
    break;
  }
}

// FIXME improve this
function throw_fatal_error($message,$details)
{
    trigger_error("{$message}: {$details}", E_USER_ERROR);
}

// FIXME Don't use throw_error in new code, use trigger_error() php function instead
// we'll have a proper trap eventually
function throw_error($message, $details)
{
  global $CONFIG, $application_version_string, $sit;
  echo "<hr style='color: red; background: red;' />";
  echo "<strong>A system error has occured</strong>";
  echo "<hr style='color: red; background: red;' />";
  echo "<strong class='error'>ERROR:</strong><br />";
  $errorstring=date("r")."\n";
  $errorstring.=strip_tags($message);
  $errorstring.=' ';
  $errorstring.=strip_tags($details);
  $errorstring.="\n";
  $errorstring.="Application Version: {$CONFIG['application_shortname']} {$application_version_string}\n";
  $errorstring.="User: ".$sit[0]."\n";
  $errorstring.="USER_AGENT: ".getenv('HTTP_USER_AGENT')."\n";
  $errorstring.="REMOTE_ADDR: ".getenv('REMOTE_ADDR')."\n";
  $errorstring.="REQUEST_METHOD: ".getenv('REQUEST_METHOD')."\n";
  $errorstring.="REQUEST_URI: ".getenv('REQUEST_URI')."\n";
  $errorstring.="QUERY_STRING: ".getenv('QUERY_STRING')."\n";
  $errorstring.="HTTP_REFERER: ".getenv('HTTP_REFERER')."\n";
  $errorstring.="SERVER_SIGNATURE: ".strip_tags(getenv('SERVER_SIGNATURE'))."\n";
  echo nl2br($errorstring);
  $errlog=error_log("---\n$errorstring", 3, $CONFIG['error_logfile']);
  if (!$errlog) echo "Fatal error logging this problem<br />";
  echo "ABORTED!<br />\n";
  echo "<hr style='color: red; background: red;' />";
  exit;
}


function throw_user_error($message, $details='')
{
    global $CONFIG, $application_version_string, $sit;

    $html = "<div class='error'>";
    if (is_array($message)) echo "<p class='error'>Oops</p>";



    if (is_array($message))
    {
        $html .= "<ul>";
        // Loop through the array
        foreach ($message AS $msg)
        {
            $html .= "<li>$msg</li>";
        }
        $html .- "</ul>";
    }
    else
    {
        $html .= "<p class='error'>$message</p>";
    }

    $html .= "</div>\n";

    echo $html;
}



/*              SITE FUNCTIONS                                */


/*  prints the HTML for a drop down list of     */
/* sites, with the given name and with the given id selected. */
function site_drop_down($name, $id)
{
    $sql  = "SELECT id, name, department FROM sites ORDER BY name ASC";
    $result = mysql_query($sql);

    $html = "<select name='{$name}'>\n";
    if ($id == 0) $html .="<option selected='selected' value='0'></option>\n";
    while ($sites = mysql_fetch_object($result))
    {
        $text=$sites->name;
        if (!empty($sites->department)) $text.= ", {$sites->department}";
        if (empty($text)) $text = $sites->name;
        if (strlen($text) >= 55) $text=htmlspecialchars(substr(trim($text), 0, 55))."&hellip;";
        else $text=htmlspecialchars($text);
        $html .= "<option ";
        if ($sites->id == $id) $html .= "selected='selected' ";
        $html .= "value='{$sites->id}'>{$text}</option>\n";
    }
    $html .= "</select>\n";

    return $html;
}


function site_name($id)
{
    $sitename = stripslashes(db_read_column('name', 'sites', $id));
    if (empty($sitename)) $sitename="Unknown";

    return($sitename);
}


//  prints the HTML for a drop down list of
// maintenance contracts, with the given name and with the
// given id selected.
function maintenance_drop_down($name, $id)
{
    // FIXME make maintenance_drop_down a hierarchical selection box sites/contracts
    // extract all maintenance contracts
    $sql  = "SELECT sites.name AS sitename, products.name AS productname, maintenance.id AS id FROM maintenance, sites, products ";
    $sql .= "WHERE site=sites.id AND product=products.id ORDER BY sites.name ASC";
    $result = mysql_query($sql);

    // print HTML
    ?>
    <select name="<?php echo $name ?>">
    <?php
    if ($id == 0)
        echo "<option selected='selected' value='0'></option>\n";
    while ($maintenance = mysql_fetch_array($result))
    {
        ?><option <?php if ($maintenance["id"] == $id) { ?>selected='selected' <?php } ?>value='<?php echo $maintenance["id"] ?>'><?php echo $maintenance["sitename"] ?> | <?php echo $maintenance["productname"] ?></option><?php
        echo "\n";
    }
    ?>
    </select>
    <?php
}


//  prints the HTML for a drop down list of resellers, with the given name and with the given id
// selected.                                                  */
function reseller_drop_down($name, $id)
{
    $sql  = "SELECT id, name FROM resellers ORDER BY name ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    // print HTML
    ?>
    <select name="<?php echo $name ?>">
    <?php
    if ($id == 0)
        echo "<option selected='selected' value='0'></option>\n";
    while ($resellers = mysql_fetch_array($result))
    {
        ?><option <?php if ($resellers["id"] == $id) { ?>selected='selected' <?php } ?>value='<?php echo $resellers["id"] ?>'><?php echo $resellers["name"] ?></option><?php
        echo "\n";
    }
    ?>
    </select>
    <?php
}


function reseller_name($id)
{
    return db_read_column('name', 'resellers', $id);
}


//  prints the HTML for a drop down list of
// licence types, with the given name and with the given id
// selected.
function licence_type_drop_down($name, $id)
{
    $sql  = "SELECT id, name FROM licencetypes ORDER BY name ASC";
    $result = mysql_query($sql);

    // print HTML
    ?>
    <select name="<?php echo $name ?>">
    <?php
    if ($id == 0)
        echo "<option selected='selected' value='0'></option>\n";
    while ($licencetypes = mysql_fetch_array($result))
    {
        ?><option <?php if ($licencetypes["id"] == $id) { ?>selected='selected' <?php } ?>value='<?php echo $licencetypes["id"] ?>'><?php echo $licencetypes["name"] ?></option><?php
        echo "\n";
    }
    ?>
    </select>
    <?php
}


function licence_type($id)
{
    return db_read_column('name', 'licencetypes', $id);
}


function countdayincidents($day, $month, $year)
{
    // Counts the number of incidents opened on a specified day
    $unixstartdate=mktime(0,0,0,$month,$day,$year);
    $unixenddate=mktime(23,59,59,$month,$day,$year);
    $sql = "SELECT count(*) FROM incidents ";
    $sql .= "WHERE opened BETWEEN '$unixstartdate' AND '$unixenddate' ";
    $result= mysql_query($sql);
    list($count)=mysql_fetch_row($result);
    mysql_free_result($result);
    return $count;
}


function countdayclosedincidents($day, $month, $year)
{
    // Counts the number of incidents closed on a specified day
    $unixstartdate=mktime(0,0,0,$month,$day,$year);
    $unixenddate=mktime(23,59,59,$month,$day,$year);
    $sql = "SELECT count(*) FROM incidents ";
    $sql .= "WHERE closed BETWEEN '$unixstartdate' AND '$unixenddate' ";
    $result= mysql_query($sql);
    list($count)=mysql_fetch_row($result);
    mysql_free_result($result);
    return $count;
}


function countdaycurrentincidents($day, $month, $year)
{
    // Counts the number of incidents opened on a specified day
    $unixstartdate=mktime(0,0,0,$month,$day,$year);
    $unixenddate=mktime(23,59,59,$month,$day,$year);
    $sql = "SELECT count(*) FROM incidents ";
    $sql .= "WHERE opened <= '$unixenddate' AND closed >= '$unixstartdate' ";
    $result= mysql_query($sql);
    list($count)=mysql_fetch_row($result);
    mysql_free_result($result);
    return $count;
}


// Takes a contact ID and prints HTML listing all the flags
// LEGACY / DEPRECATED
function print_contact_flags($id, $editlink=FALSE)
{
    $sql = "SELECT contactflags.flag, flags.name FROM contactflags, flags ";
    $sql .= "WHERE contactflags.flag=flags.flag AND contactflags.contactid='$id' ";
    $result= mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    while( $contactflagrows = mysql_fetch_array($result) )
    {
        if ($editlink==TRUE) echo "<a href='edit_contact_flags.php?mode=removeflag&amp;id=$id&amp;flag={$contactflagrows['flag']}' title='{$contactflagrows['name']} (Click to Remove)'>";
        else echo "<span title=\"".$contactflagrows['name']."\">";
        echo strtoupper($contactflagrows['flag']);
        if ($editlink==TRUE) echo "</a>";
        else echo "</span>";
        echo ' ';
    }
    if (mysql_num_rows($result)==0) echo "<em>None</em>";
    mysql_free_result($result);
    return TRUE;
}

// LEGACY / DEPRECATED
function check_contact_flag($id, $flag)
{
    $sql = "SELECT flag FROM contactflags WHERE contactid='$id' AND flag='$flag'";
    $result=mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
}

// LEGACY / DEPRECATED
function add_contact_flag($id, $flag)
{
    // first check that contact does not already have this flag
    $sql = "";
    trigger_error("add_contact_flag feature is not available yet", E_USER_WARNING);
}


// Inserts an entry into the Journal table
function journal($loglevel, $event, $bodytext, $journaltype, $refid)
{
    global $CONFIG, $sit;
    // Journal Types
    // 1 = Logon/Logoff
    // 2 = Support Incidents
    // 3 = -Unused-
    // 4 = Sites
    // 5 = Contacts
    // 6 = Admin
    // 7 = User Management

    // Logging Level
    // 0 = No logging
    // 1 = Minimal Logging
    // 2 = Normal Logging
    // 3 = Full Logging
    // 4 = Max Debug Logging

    if ($loglevel<=$CONFIG['journal_loglevel'])
    {
        $sql  = "INSERT INTO journal ";
        $sql .= "(userid, event, bodytext, journaltype, refid) ";
        $sql .= "VALUES ('".$sit[2]."', '$event', '$bodytext', '$journaltype', '$refid') ";
        $result= mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        return TRUE;
    }
    else
    {
        // Below minimum log level - do nothing
        return FALSE;
    }
}


// prints the HTML for a checkbox, the 'state' value should be a 1, yes, true or 0, no, false */
function html_checkbox($name,$state)
{
    if ($state==1 || $state=='Yes' || $state=='yes' || $state=='true' || $state=='TRUE')
        echo "<input type='checkbox' checked='checked' name='$name' value='$state' />" ;
    else
        echo "<input type='checkbox' name='$name' value='$state' />" ;
}


function send_template_email($template, $incidentid, $info1='', $info2='')
{
    global $CONFIG, $application_version_string, $sit, $now;
    if (empty($template)) throw_error('Blank template ID:', 'send_template_email()');
    if (empty($incidentid)) throw_error('Blank incident ID:', 'send_template_email()');

    if (is_numeric($template)) $templateid = $template;
    else
    {
        // Lookup the template id using the name
        $sql = "SELECT id FROM emailtype WHERE name='$template' LIMIT 1";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        list($templateid) = mysql_fetch_row($result);
    }

    // Set up headers
    $email_to      = trim(emailtype_replace_specials(emailtype_to($templateid), $incidentid, $sit[2]));
    $email_from    = trim(emailtype_replace_specials(emailtype_from($templateid), $incidentid, $sit[2]));
    $email_replyto = trim(emailtype_replace_specials(emailtype_replyto($templateid), $incidentid, $sit[2]));
    $email_cc      = trim(emailtype_replace_specials(emailtype_cc($templateid), $incidentid, $sit[2]));
    $email_bcc     = trim(emailtype_replace_specials(emailtype_bcc($templateid), $incidentid, $sit[2]));
    $email_subject = trim(emailtype_replace_specials(emailtype_subject($templateid), $incidentid, $sit[2]));
    $email_body    = trim(emailtype_replace_specials(emailtype_body($templateid), $incidentid, $sit[2]));
    $email_customervisibility = trim(emailtype_customervisibility($templateid));
    $email_storeinlog = trim(emailtype_storeinlog($templateid));

    // Additional information
    if (empty($info1)==FALSE || empty($info2)==FALSE)
    {
        $email_body = str_replace("<info1>", "$info1", $email_body);
        $email_subject = str_replace("<info1>", "$info1", $email_subject);
        $email_body = str_replace("<info2>", "$info2", $email_body);
        $email_subject = str_replace("<info2>", "$info2", $email_subject);
    }

    ##echo "Sending email to $email_to with subject '".stripslashes($email_subject)."'";

    // build the extra headers string for email
    $extra_headers  = "From: $email_from\r\nReply-To: $email_replyto\r\nErrors-To: {$CONFIG['support_email']}\r\n";
    $extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion()."\r\n";
    $extra_headers .= "X-Originating-IP: {$_SERVER['REMOTE_ADDR']}\r\n";
    if ($email_cc != '')
        $extra_headers .= "CC: $email_cc\r\n";
    if ($email_bcc != "")
        $extra_headers .= "BCC: $email_bcc\r\n";

    $extra_headers .= "\r\n";

    if($email_storeinlog == 'Yes')
    {
        $bt   = "To: <b>$email_to</b>\nFrom: <b>$email_from</b>\nReply-To: <b>$emailreplyto</b>\n";
        $bt  .= "BCC: <b>$email_bcc</b>\nSubject: <b>$email_subject</b>\n<hr>".$email_body;
        $sql = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, customervisibility) VALUES ";
        $sql .= "($incidentid, 0, 'email', '".mysql_escape_string($bt)."', ";
        $sql .= "$now, '$email_customervisibility')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    }

    // send email
    if ($CONFIG['demo']) $rtnvalue = TRUE;
    else $rtnvalue = mail($email_to, stripslashes($email_subject), stripslashes($email_body), $extra_headers);
    return $rtnvalue;
}


function generate_password($length=8)
{
   $possible = '0123456789'.'abcdefghijkmnpqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'-';
   // $possible = '23456789'.'abcdefghjkmnpqrstuvwxyz'.'ABCDEFGHJKLMNPQRSTUVWXYZ'.'-';
               // not using 1's 0's etc. to save confusion
               // '-=!&';
   $str ="";
   while (strlen($str) < $length)
   {
        $str .= substr($possible, (rand() % strlen($possible)),1);
   }
   return($str);
}


if (!function_exists('list_dir'))
{
    // returns an array contains all files in a directory and optionally recurses subdirectories
    function list_dir($dirname, $recursive = 1)
    {
        // try to figure out what delimeter is being used (for windows or unix)...
        $delim = (strstr($dirname,"/")) ? "/" : "\\";

        if($dirname[strlen($dirname)-1]!=$delim)
        $dirname.=$delim;

        $handle = opendir($dirname);
        if ($handle==FALSE) throw_error('Error in list_dir() Problem attempting to open directory',$dirname);

        while ($file = readdir($handle))
        {
            if($file=='.'||$file=='..')
                continue;
            if(is_dir($dirname.$file) && $recursive)
            {
                $x = list_dir($dirname.$file.$delim);
                $result_array = array_merge($result_array, $x);
            }
            else
            {
                $result_array[]=$dirname.$file;
            }
        }
        closedir($handle);

        if (sizeof($result_array))
        {
            natsort($result_array);
        }
        return $result_array;
    }
}


if (!function_exists('is_number'))
{
    function is_number($string)
    {
        $number=TRUE;
        for ($i=0;$i<strlen($string);$i++)
        {
            if (!(ord(substr($string,$i,1)) <= 57 && ord(substr($string,$i,1)) >= 48))
            {
                $number=FALSE;
            }
        }
        return $number;
    }
}


// recursive copy from one directory to another
function rec_copy ($from_path, $to_path)
{
    if ($from_path=='') throw_error('Cannot move file', 'from_path not set');
    if ($to_path=='') throw_error('Cannot move file', 'to_path not set');

    $mk=mkdir($to_path, 0700);
    if (!$mk) throw_error('Failed creating directory: ',$to_path);
    $this_path = getcwd();
    if (is_dir($from_path))
    {
        chdir($from_path);
        $handle=opendir('.');
        while (($file = readdir($handle))!==false)
        {
            if (($file != ".") && ($file != ".."))
            {
                if (is_dir($file))
                {
                    rec_copy ($from_path.$file."/",
                    $to_path.$file."/");
                    chdir($from_path);
                }
                if (is_file($file))
                {
                    if (!(substr(rtrim($file),strlen(rtrim($file))-8,4)=='mail' || substr(rtrim($file),strlen(rtrim($file))-10,5)=='part1'|| substr(rtrim($file),strlen(rtrim($file))-8,4)=='.vcf'))
                    {
                        copy($from_path.$file, $to_path.$file);
                    }
                }
            }
        }
        closedir($handle);
    }
}


function getattachmenticon($filename)
{
    global $CONFIG, $iconset;
    // Maybe sometime make this use mime typesad of file extensions
    $ext=strtolower(substr($filename, (strlen($filename)-3) , 3));
    $imageurl="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/mime_empty.gif";

    $filetype[]="gif";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/image.gif";
    $filetype[]="jpg";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/image.gif";
    $filetype[]="bmp";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/image.gif";
    $filetype[]="png";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/image.gif";
    $filetype[]="pcx";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/image.gif";
    $filetype[]="xls";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/spreadsheet.gif";
    $filetype[]="csv";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/spreadsheet.gif";
    $filetype[]="zip";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/tgz.gif";
    $filetype[]="arj";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/tgz.gif";
    $filetype[]="rar";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/tgz.gif";
    $filetype[]="cab";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/tgz.gif";
    $filetype[]="lzh";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/tgz.gif";
    $filetype[]="txt";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/txt.gif";
    $filetype[]="f90";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source_f.gif";
    $filetype[]="f77";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source_f.gif";
    $filetype[]="inf";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source.gif";
    $filetype[]="ins";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source.gif";
    $filetype[]="adm";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source.gif";
    $filetype[]="f95";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source_f.gif";
    $filetype[]="cpp";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source_cpp.gif";
    $filetype[]="for";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source_f.gif";
    $filetype[]=".pl";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source_pl.gif";
    $filetype[]=".py";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source_py.gif";
    $filetype[]="rtm";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/misc_doc.gif";
    $filetype[]="doc";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/wordprocessing.gif";
    $filetype[]="rtf";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/wordprocessing.gif";
    $filetype[]="wri";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/wordprocessing.gif";
    $filetype[]="wri";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/wordprocessing.gif";
    $filetype[]="pdf";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/pdf.gif";
    $filetype[]="htm";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/html.gif";
    $filetype[]="tml";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/html.gif";
    $filetype[]="wav";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/sound.gif";
    $filetype[]="mp3";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/sound.gif";
    $filetype[]="voc";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/sound.gif";
    $filetype[]="exe";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary.gif";
    $filetype[]="com";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary.gif";
    $filetype[]="nlm";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary.gif";
    $filetype[]="evt";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/log.gif";
    $filetype[]="log";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/log.gif";
    $filetype[]="386";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary2.gif";
    $filetype[]="dll";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary2.gif";
    $filetype[]="asc";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/txt.gif";
    $filetype[]="asp";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/html.gif";
    $filetype[]="avi";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/video.gif";
    $filetype[]="bkf";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/tar.gif";
    $filetype[]="chm";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/man.gif";
    $filetype[]="hlp";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/man.gif";
    $filetype[]="dif";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/txt.gif";
    $filetype[]="hta";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/html.gif";
    $filetype[]="reg";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/resource.gif";
    $filetype[]="dmp";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/core.gif";
    $filetype[]="ini";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source.gif";
    $filetype[]="jpe";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/image.gif";
    $filetype[]="mht";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/html.gif";
    $filetype[]="msi";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary.gif";
    $filetype[]="aot";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary.gif";
    $filetype[]="pgp";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary.gif";
    $filetype[]="dbg";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary.gif";
    $filetype[]="axt";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source.gif"; // zen text
    $filetype[]="rdp";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary2.gif";
    $filetype[]="sig";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/document.gif";
    $filetype[]="tif";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/image.gif";
    $filetype[]="ttf";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/font_ttf.gif";
    $filetype[]="for";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/font_bitmap.gif";
    $filetype[]="vbs";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/shellscript.gif";
    $filetype[]="vbe";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/shellscript.gif";
    $filetype[]="bat";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/shellscript.gif";
    $filetype[]="wsf";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/shellscript.gif";
    $filetype[]="cmd";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/shellscript.gif";
    $filetype[]="scr";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary2.gif";
    $filetype[]="xml";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/source.gif";
    $filetype[]="zap";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary2.gif";
    $filetype[]=".ps";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/postscript.gif";
    $filetype[]=".rm";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/real_doc.gif";
    $filetype[]="ram";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/real_doc.gif";
    $filetype[]="vcf";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/vcard.gif";
    $filetype[]="wmf";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/vectorgfx.gif";
    $filetype[]="cer";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/document.gif";
    $filetype[]="tmp";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/unknown.gif";
    $filetype[]="cap";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary.gif";
    $filetype[]="tr1";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/binary.gif";
    $filetype[]=".gz";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/tgz.gif";
    $filetype[]="tar";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/tar.gif";
    $filetype[]="nfo";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/info.gif";
    $filetype[]="pal";    $imgurl[]="{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/mimetypes/colorscm.gif";


    // bugbug: think there is an inifinate loop here if a filetype
    // is not recognised.
    $cnt = count($filetype);
    if ( $cnt > 0 )
    {
        $a=0;
        $stop=FALSE;
        while($a < $cnt && $stop==FALSE)
        {
            if ($ext==$filetype[$a])
            {
                $imageurl=$imgurl[$a];
                $stop=TRUE;
            }
            $a++;
        }
    }
    unset ($filetype);
    unset ($imgurl);
    return $imageurl;
}


function count_incoming_updates()
{
    $sql = "SELECT id FROM updates WHERE incidentid=0";
    $result=mysql_query($sql);
    $count=mysql_num_rows($result);
    mysql_free_result($result);
    return $count;
}


function global_signature()
{
    //$sql = "SELECT signature FROM emailsig LIMIT 1";
    $sql = "SELECT signature FROM emailsig ORDER BY RAND() LIMIT 1";
    $result=mysql_query($sql);
    list($signature)=mysql_fetch_row($result);
    mysql_free_result($result);
    $signature=stripslashes($signature);
    return $signature;
}


// checks the spelling of a word and returns true if spelled correctly and
// false if misspelled.  Uses the pspell library using link provided.
function spellcheck_word($pspell_link, $word)
{
    return pspell_check($pspell_link,$word);
}


function spellcheck_addword($word)
{
    global $CONFIG;
    $pspell_config = pspell_config_create ("en");
    pspell_config_personal ($pspell_config, $CONFIG['main_dictionary_file']);
    pspell_config_repl ($pspell_config, $CONFIG['main_dictionary_file']);
    $pspell_link = pspell_new_personal ($CONFIG['custom_dictionary_file'], 'en' , 'british', '', 'iso8859-1', PSPELL_FAST);

    pspell_add_to_personal ($pspell_link, $word);
    pspell_save_wordlist ($pspell_link);
}


// urltext should take the form '&var=value'
function spellcheck_text($text, $urltext)
{
    global $CONFIG;
    $pspell_config = pspell_config_create ("en");
    pspell_config_personal ($pspell_config, $CONFIG['main_dictionary_file']);
    pspell_config_repl ($pspell_config, $CONFIG['main_dictionary_file']);
    $pspell_link = pspell_new_personal ($CONFIG['custom_dictionary_file'], 'en' , 'british', '', 'iso8859-1', PSPELL_FAST);

    if (!$pspell_link) throw_error('Dictionary Link Error','');

    // try and stop html getting through in the source text (INL 2July03)
    $text = str_replace('<','&#060;', $text);
    $text = str_replace('>','&#062;', $text);

    for($c=0;$c<=strlen($text);$c++)
    {
        $char=strtolower(substr($text,$c,1));
        if (!(ord($char) >= 97 && ord($char) <= 122))
        {
            if ($endwordpos==-1 && $startwordpos==-1) $newtext .= $char;
            if ($startwordpos==-1) $startwordpos=$c+1;
            else $endwordpos=$c;
        }
        if ($c==0 && (ord($char) >= 97 && ord($char) <= 122)) $startwordpos=0;
        if ($endwordpos!=-1 && $startwordpos!=-1)
        {
            $word=substr($text, $startwordpos, ($endwordpos-$startwordpos));
            if (!spellcheck_word($pspell_link, $word))
            {
                $suggestions=pspell_suggest($pspell_link, $word);
                if (count($suggestions)>1)
                {
                    $tooltiptext="Possible spellings:<br /><br />";
                    $tooltiptext .= "<table summary='suggestions'>";
                    $col=0;
                    foreach ($suggestions as $suggestion)
                    {
                        if ($col>3) { $tooltiptext .= "</tr>\n<tr>"; $col=0; }
                        $tooltiptext .= "<td valign='top' align='left'><a href='{$_SERVER['PHP_SELF']}?changepos=$c&amp;replacement=$suggestion$urltext&amp;step=3'>$suggestion</a></td>";
                        $col++;
                    }
                    $tooltiptext .= "</tr>\n</table>\n";
                }
                else
                {
                    $tooltiptext = "Sorry, there are no reasonable suggested spellings for '$word' in the dictionary<br />";
                }
                $tooltiptext .= "<br /><a href='{$_SERVER['PHP_SELF']}?addword=$word$urltext&amp;step=3' onclick='return confirm_addword();'>Add</a> '$word' to the dictionary.";
                echo "<script type=\"text/javascript\">var linkHelp$c = \"$tooltiptext\";</script>\n";

                $newtext .= "<a class=\"spellLink\" href=\"?\" onclick=\"showHelpTip(event, linkHelp$c); return false\">$word</a>";
            }
            else
                $newtext .= "$word";
            $c--;
            $startwordpos=-1;
            $endwordpos=-1;
        }
    }
    return $newtext;
}


// replace word in text
function replace_word($text, $changepos, $replacement)
{
    // changepos is the position of the end of the word needing to be changed

    // read backwards until the end of the word and store the word end position
    $limit=$changepos-30;
    $c=$changepos-1;
    do
    {
        $char=strtolower(substr($text,$c,1));
        $startwordpos=$c;
        $c--;
    } while ((ord($char) >= 97 && ord($char) <= 122) && $c > 1 );

    $newtext = substr($text, 0, $startwordpos+1 );
    $newtext .= $replacement;
    $newtext .= substr($text, $changepos);

    return $newtext;
}


function holiday_type($id)
{
    if ($id==10) $holidaytype='Public Holiday';
    else
    {
        $holidaytype = db_read_column('name', 'holidaytypes', $id);
        if (empty($holidaytype))
        $holidaytype = 'Holiday';  // default: holiday
    }
    return($holidaytype);
}


function holiday_approval_status($approvedid)
{
    // We add 10 to normal status when we archive holiday
    switch ($approvedid)
    {
        case -2: $status = "Not requested"; break;
        case -1: $status = "Denied"; break;
        case 0: $status = "Requested"; break;
        case 1: $status = "Approved"; break;
        case 2: $status = "Approved 'Free'"; break;
        case 8: $status = "Archived. Not Requested";
        case 9: $status = "Archived. Denied";
        case 10: $status = "Archived. Requested";
        case 11: $status = "Archived. Approved";
        case 12: $status = "Archived. Approved 'Free'";
        default: $status = "Approval Status Unknown"; break;
    }
    return $status;
}


function holidaytype_drop_down($name, $id)
{
    $sql  = "SELECT id, name FROM holidaytypes ORDER BY name ASC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    // print HTML
    ?>
    <select name="<?php echo $name ?>">
    <?php
    if ($id == 0)
        echo "<option selected value='0'></option>\n";
    while ($holidays = mysql_fetch_array($result))
    {
        ?><option <?php if ($holidays["id"] == $id) { ?>selected='selected' <?php } ?>value='<?php echo $holidays["id"] ?>'><?php echo $holidays["name"] ?></option><?php
        echo "\n";
    }
    echo "</select>";
}


// check to see if any fellow group members have holiday
// on the date specified
function check_group_holiday($userid, $date, $length='day')
{
    // get groupid
    $sql = "SELECT groupid FROM usergroups WHERE userid='$userid' ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    while ($group = mysql_fetch_object($result))
    {
        // list group members
        $msql = "SELECT userid FROM usergroups WHERE groupid='{$group->groupid}' AND userid!='$userid' ";
        $mresult = mysql_query($msql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($member = mysql_fetch_object($mresult))
        {
            // check to see if this group member has holiday
            $hsql = "SELECT * FROM holidays WHERE userid='{$member->userid}' AND startdate='{$date}' ";
            if ($length=='am' || $length=='pm') $hsql .= "AND length = '$length' || length = 'day' ";
            $hresult = mysql_query($hsql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            if (mysql_num_rows($hresult) >= 1)
            {
                // FIXME: need to deal with am / pm etc
                $namelist .= user_realname($member->userid)." ($length)";
                $namelist .= "&nbsp;&nbsp;";
            }
        }
    }
    return $namelist;
}


function country_drop_down($name, $country, $extraattributes='')
{
    global $CONFIG;
    if ($country=='') $country=$CONFIG['home_country'];

    if ($country=='UK') $country='UNITED KINGDOM';
    $countrylist[]='ALBANIA';
    $countrylist[]='ALGERIA';
    $countrylist[]='AMERICAN SAMOA';
    $countrylist[]='ANDORRA';
    $countrylist[]='ANGOLA';
    $countrylist[]='ANGUILLA';
    $countrylist[]='ANTIGUA';
    $countrylist[]='ARGENTINA';
    $countrylist[]='ARMENIA';
    $countrylist[]='ARUBA';
    $countrylist[]='AUSTRALIA';
    $countrylist[]='AUSTRIA';
    $countrylist[]='AZERBAIJAN';
    $countrylist[]='BAHAMAS';
    $countrylist[]='BAHRAIN';
    $countrylist[]='BANGLADESH';
    $countrylist[]='BARBADOS';
    $countrylist[]='BELARUS';
    $countrylist[]='BELGIUM';
    $countrylist[]='BELIZE';
    $countrylist[]='BENIN';
    $countrylist[]='BERMUDA';
    $countrylist[]='BHUTAN';
    $countrylist[]='BOLIVIA';
    $countrylist[]='BONAIRE';
    $countrylist[]='BOSNIA HERZEGOVINA';
    $countrylist[]='BOTSWANA';
    $countrylist[]='BRAZIL';
    $countrylist[]='BRUNEI';
    $countrylist[]='BULGARIA';
    $countrylist[]='BURKINA FASO';
    $countrylist[]='BURUNDI';
    $countrylist[]='CAMBODIA';
    $countrylist[]='CAMEROON';
    $countrylist[]='CANADA';
    $countrylist[]='CANARY ISLANDS';
    $countrylist[]='CAPE VERDE ISLANDS';
    $countrylist[]='CAYMAN ISLANDS';
    $countrylist[]='CENTRAL AFRICAN REPUBLIC';
    $countrylist[]='CHAD';
    $countrylist[]='CHANNEL ISLANDS';
    $countrylist[]='CHILE';
    $countrylist[]='CHINA';
    $countrylist[]='COLOMBIA';
    $countrylist[]='COMOROS ISLANDS';
    $countrylist[]='CONGO';
    $countrylist[]='COOK ISLANDS';
    $countrylist[]='COSTA RICA';
    $countrylist[]='CROATIA';
    $countrylist[]='CUBA';
    $countrylist[]='CURACAO';
    $countrylist[]='CYPRUS';
    $countrylist[]='CZECH REPUBLIC';
    $countrylist[]='DENMARK';
    $countrylist[]='DJIBOUTI';
    $countrylist[]='DOMINICA';
    $countrylist[]='DOMINICAN REPUBLIC';
    $countrylist[]='ECUADOR';
    $countrylist[]='EGYPT';
    $countrylist[]='EL SALVADOR';
    $countrylist[]='EQUATORIAL GUINEA';
    $countrylist[]='ERITREA';
    $countrylist[]='ESTONIA';
    $countrylist[]='ETHIOPIA';
    $countrylist[]='FAROE ISLANDS';
    $countrylist[]='FIJI ISLANDS';
    $countrylist[]='FINLAND';
    $countrylist[]='FRANCE';
    $countrylist[]='FRENCH GUINEA';
    $countrylist[]='GABON';
    $countrylist[]='GAMBIA';
    $countrylist[]='GEORGIA';
    $countrylist[]='GERMANY';
    $countrylist[]='GHANA';
    $countrylist[]='GIBRALTAR';
    $countrylist[]='GREECE';
    $countrylist[]='GREENLAND';
    $countrylist[]='GRENADA';
    $countrylist[]='GUADELOUPE';
    $countrylist[]='GUAM';
    $countrylist[]='GUATEMALA';
    $countrylist[]='GUINEA REPUBLIC';
    $countrylist[]='GUINEA-BISSAU';
    $countrylist[]='GUYANA';
    $countrylist[]='HAITI';
    $countrylist[]='HONDURAS REPUBLIC';
    $countrylist[]='HONG KONG';
    $countrylist[]='HUNGARY';
    $countrylist[]='ICELAND';
    $countrylist[]='INDIA';
    $countrylist[]='INDONESIA';
    $countrylist[]='IRAN';
    $countrylist[]='IRELAND, REPUBLIC';
    $countrylist[]='ISRAEL';
    $countrylist[]='ITALY';
    $countrylist[]='IVORY COAST';
    $countrylist[]='JAMAICA';
    $countrylist[]='JAPAN';
    $countrylist[]='JORDAN';
    $countrylist[]='KAZAKHSTAN';
    $countrylist[]='KENYA';
    $countrylist[]='KIRIBATI, REP OF';
    $countrylist[]='KOREA, SOUTH';
    $countrylist[]='KUWAIT';
    $countrylist[]='KYRGYZSTAN';
    $countrylist[]='LAOS';
    $countrylist[]='LATVIA';
    $countrylist[]='LEBANON';
    $countrylist[]='LESOTHO';
    $countrylist[]='LIBERIA';
    $countrylist[]='LIBYA';
    $countrylist[]='LIECHTENSTEIN';
    $countrylist[]='LITHUANIA';
    $countrylist[]='LUXEMBOURG';
    $countrylist[]='MACAU';
    $countrylist[]='MACEDONIA';
    $countrylist[]='MADAGASCAR';
    $countrylist[]='MALAWI';
    $countrylist[]='MALAYSIA';
    $countrylist[]='MALDIVES';
    $countrylist[]='MALI';
    $countrylist[]='MALTA';
    $countrylist[]='MARSHALL ISLANDS';
    $countrylist[]='MARTINIQUE';
    $countrylist[]='MAURITANIA';
    $countrylist[]='MAURITIUS';
    $countrylist[]='MEXICO';
    $countrylist[]='MOLDOVA, REP OF';
    $countrylist[]='MONACO';
    $countrylist[]='MONGOLIA';
    $countrylist[]='MONTSERRAT';
    $countrylist[]='MOROCCO';
    $countrylist[]='MOZAMBIQUE';
    $countrylist[]='MYANMAR';
    $countrylist[]='NAMIBIA';
    $countrylist[]='NAURU, REP OF';
    $countrylist[]='NEPAL';
    $countrylist[]='NETHERLANDS';
    $countrylist[]='NEVIS';
    $countrylist[]='NEW CALEDONIA';
    $countrylist[]='NEW ZEALAND';
    $countrylist[]='NICARAGUA';
    $countrylist[]='NIGER';
    $countrylist[]='NIGERIA';
    $countrylist[]='NIUE';
    $countrylist[]='NORWAY';
    $countrylist[]='OMAN';
    $countrylist[]='PAKISTAN';
    $countrylist[]='PANAMA';
    $countrylist[]='PAPUA NEW GUINEA';
    $countrylist[]='PARAGUAY';
    $countrylist[]='PERU';
    $countrylist[]='PHILLIPINES';
    $countrylist[]='POLAND';
    $countrylist[]='PORTUGAL';
    $countrylist[]='PUERTO RICO';
    $countrylist[]='QATAR';
    $countrylist[]='REUNION ISLAND';
    $countrylist[]='ROMANIA';
    $countrylist[]='RUSSIAN FEDERATION';
    $countrylist[]='RWANDA';
    $countrylist[]='SAIPAN';
    $countrylist[]='SAO TOME & PRINCIPE';
    $countrylist[]='SAUDI ARABIA';
    $countrylist[]='SENEGAL';
    $countrylist[]='SEYCHELLES';
    $countrylist[]='SIERRA LEONE';
    $countrylist[]='SINGAPORE';
    $countrylist[]='SLOVAKIA';
    $countrylist[]='SLOVENIA';
    $countrylist[]='SOLOMON ISLANDS';
    $countrylist[]='SOUTH AFRICA';
    $countrylist[]='SPAIN';
    $countrylist[]='SRI LANKA';
    $countrylist[]='ST BARTHELEMY';
    $countrylist[]='ST EUSTATIUS';
    $countrylist[]='ST KITTS';
    $countrylist[]='ST LUCIA';
    $countrylist[]='ST MAARTEN';
    $countrylist[]='ST VINCENT';
    $countrylist[]='SUDAN';
    $countrylist[]='SURINAME';
    $countrylist[]='SWAZILAND';
    $countrylist[]='SWEDEN';
    $countrylist[]='SWITZERLAND';
    $countrylist[]='SYRIA';
    $countrylist[]='TAHITI';
    $countrylist[]='TAIWAN';
    $countrylist[]='TAJIKISTAN';
    $countrylist[]='TANZANIA';
    $countrylist[]='THAILAND';
    $countrylist[]='TOGO';
    $countrylist[]='TONGA';
    $countrylist[]='TRINIDAD & TOBAGO';
    $countrylist[]='TURKEY';
    $countrylist[]='TURKMENISTAN';
    $countrylist[]='TURKS & CAICOS ISLANDS';
    $countrylist[]='TUVALU';
    $countrylist[]='UGANDA';
    // $countrylist[]='UK';
    $countrylist[]='UKRAINE';
    $countrylist[]='UNITED KINGDOM';
    $countrylist[]='UNITED STATES';
    $countrylist[]='URUGUAY';
    $countrylist[]='UTD ARAB EMIRATES';
    $countrylist[]='UZBEKISTAN';
    $countrylist[]='VANUATU';
    $countrylist[]='VENEZUELA';
    $countrylist[]='VIETNAM';
    $countrylist[]='VIRGIN ISLANDS';
    $countrylist[]='VIRGIN ISLANDS (UK)';
    $countrylist[]='WESTERN SAMOA';
    $countrylist[]='YEMAN, REP OF';
    $countrylist[]='YUGOSLAVIA';
    $countrylist[]='ZAIRE';
    $countrylist[]='ZAMBIA';
    $countrylist[]='ZIMBABWE';

    if (in_array(strtoupper($country), $countrylist))
    {
        // make drop down
        $html = "<select name=\"$name\" $extraattributes>";
        foreach ($countrylist as $key => $value)
        {
            $value=htmlspecialchars($value);
            $html .= "<option value='$value'";
            if ($value==strtoupper($country)) $html .= " selected='selected'";
            $html .= ">$value</option>\n";
        }
        $html .= "</select>";
    }
    else
    {
        // make editable input box
        $html = "<input maxlength='100' name=\"$name\" size='40' value=\"$country\" $extraattributes />";
    }
    return $html;
}


function check_email($email, $check_dns = FALSE)
{
    if((preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email)) ||
       (preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/',$email)))
    {
        if($check_dns)
        {
            $host = explode('@', $email);
            // Check for MX record
            if( checkdnsrr($host[1], 'MX') ) return TRUE;
            // Check for A record
            if( checkdnsrr($host[1], 'A') ) return TRUE;
            // Check for CNAME record
            if( checkdnsrr($host[1], 'CNAME') ) return TRUE;
        }
        else
        {
            return TRUE;
        }
    }
    return FALSE;
}


function incident_get_next_target($incidentid)
{
    global $now;
    // Find the most recent SLA target that was met
    $sql = "SELECT sla,timestamp FROM updates WHERE incidentid='$incidentid' AND type='slamet' ORDER BY id DESC LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (mysql_num_rows($result) > 0)
    {
        $upd=mysql_fetch_object($result);

        switch ($upd->sla)
        {
            case 'opened': $target->type='initialresponse'; break;
            case 'initialresponse': $target->type='probdef'; break;
            case 'probdef': $target->type='actionplan'; break;
            case 'actionplan': $target->type='solution'; break;
            // case 'solution': $target->type='closed'; break;
            case 'solution': $target->type='probdef'; break;
            case 'closed': $target->type='opened'; break;
        }

        $target->since=calculate_incident_working_time($incidentid,$upd->timestamp,$now);
    }
    else
    {
        $target->type='regularcontact';
        $target->since=0;
    }
    return $target;
}


function target_type_name($targettype)
{
    switch ($targettype)
    {
        case 'opened': $name='Started'; break;
        case 'initialresponse': $name='Initial Response'; break;
        case 'probdef': $name='Problem Definition'; break;
        case 'actionplan': $name='Action Plan'; break;
        case 'solution': $name='Resolution/Reprioritisation'; break;
        case 'closed': $name=''; break;
        case 'regularcontact': $name=''; break; // Contact Customer
        default: $name=''; break;
    }
    return $name;
}


function target_radio_buttons($incidentid)
{
    $target = incident_get_next_target($incidentid);
    if (empty($target->time))
    {
        echo "N/A (This incident has no unfulfilled targets)";
    }
    else
    {
        switch ($target->type)
        {
            case 'initialresponse':
                echo "<input type='radio' name='target' checked='checked' value='none' />No ";
                echo "<input type='radio' name='target' value='initialresponse' />Initial Response ";
                echo "<input type='radio' name='target' value='probdef' />Prob. Def. ";
                echo "<input type='radio' name='target' value='actionplan' />Act. Plan ";
                echo "<input type='radio' name='target' disabled='disabled' value='solution' />Reprioritise ";
            break;

            case 'probdef':
                echo "<input type='radio' name='target' checked='checked' value='none' />No ";
                echo "<input type='radio' name='target' disabled='disabled' value='initialresponse' />Init. Response ";
                echo "<input type='radio' name='target' value='probdef' />Prob. Def. ";
                echo "<input type='radio' name='target' value='actionplan' />Act. Plan ";
                echo "<input type='radio' name='target' value='solution' />Reprioritise ";
            break;

            case 'actionplan':
                echo "<input type='radio' name='target' checked='checked' value='none' />No ";
                echo "<input type='radio' name='target' disabled='disabled' value='initialresponse' />Init. Response ";
                echo "<input type='radio' name='target' disabled='disabled' value='probdef' />Prob. Def. ";
                echo "<input type='radio' name='target' value='actionplan' />Act. Plan ";
                echo "<input type='radio' name='target' value='solution' />Reprioritise ";
            break;

            case 'solution':
                echo "<input type='radio' name='target' checked='checked' value='none' />No ";
                echo "<input type='radio' name='target' disabled='disabled' value='initialresponse' />Init. Response ";
                echo "<input type='radio' name='target' disabled='disabled' value='probdef' />Prob. Def. ";
                echo "<input type='radio' name='target' disabled='disabled' value='actionplan' />Act. Plan ";
                echo "<input type='radio' name='target' value='solution' />Reprioritise ";
            break;
        }
    }
}


function incident_get_next_review($incidentid)
{
    global $now;
    $sql = "SELECT timestamp FROM updates WHERE incidentid='$incidentid' AND type='reviewmet' ORDER BY id DESC LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (mysql_num_rows($result) > 0)
    {
        $upd=mysql_fetch_object($result);
        $timesincereview=floor(($now-($upd->timestamp))/60);
    }
    return $timesincereview;
}


function unhtmlentities ($string)
{
    // We may not need this function after PHP 4.3
    // http://uk.php.net/manual/en/function.get-html-translation-table.php
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    $trans_tbl[' '] = '&#160;';
    $trans_tbl = array_flip ($trans_tbl);
    $ret = strtr ($string, $trans_tbl);
    return preg_replace('/&#(\d+);/me', "chr('\\1')",$ret);
}


function strip_anchor_tags ($string)
{
    return preg_replace('/<a class=\"spellLink\" href=\"?\" onclick=\"showHelpTip.event, linkHelp(.*).; return false\">/', '', $string);
}




// PARAMS:  A date from a mysql database 'date' format
// RETURNS: A php style date (unix).
function mysql2date($mysqldate)
{
    // for the zero/blank case, return 0
    if (empty($mysqldate)) return 0;
    if ($mysqldate=='0000-00-00 00:00:00' OR $mysqldate=='0000-00-00') return 0;

    // Takes a MYSQL date and converts it to a proper PHP date
    $day = substr($mysqldate,8,2);
    $month = substr($mysqldate,5,2);
    $year = substr($mysqldate,0,4);

    if (strlen($mysqldate) > 10)
    {
        $hour = substr($mysqldate,11,2);
        $minute = substr($mysqldate,14,2);
        $second = substr($mysqldate,17,2);
        $phpdate= mktime($hour,$minute,$second,$month,$day,$year);
    }
    else $phpdate= mktime(0,0,0,$month,$day,$year);

    return $phpdate;
}


// PARAMS:  A date from a mysql database 'timestamp' format
// RETURNS: a php style date (unix).
function mysqlts2date($mysqldate)
{
    // for the zero/blank case, return 0
    if (empty($mysqldate)) return 0;

    // Takes a MYSQL date and converts it to a proper PHP date
    if (strlen($mysqldate) == 14)
    {
        $day = substr($mysqldate,6,2);
        $month = substr($mysqldate,4,2);
        $year = substr($mysqldate,0,4);
        $hour = substr($mysqldate,8,2);
        $minute = substr($mysqldate,10,2);
        $second = substr($mysqldate,12,2);
    }
    elseif (strlen($mysqldate) > 14)
    {
        $day = substr($mysqldate,8,2);
        $month = substr($mysqldate,5,2);
        $year = substr($mysqldate,0,4);
        $hour = substr($mysqldate,11,2);
        $minute = substr($mysqldate,14,2);
        $second = substr($mysqldate,17,2);
    }
    $phpdate= mktime($hour,$minute,$second,$month,$day,$year);
    return $phpdate;
}


function iso_8601_date($timestamp)
{
   $date_mod = date('Y-m-d\TH:i:s', $timestamp);
   $pre_timezone = date('O', $timestamp);
   $time_zone = substr($pre_timezone, 0, 3).":".substr($pre_timezone, 3, 2);
   $date_mod .= $time_zone;
   return $date_mod;
}


// Returns the number of working minutes (minutes in the working day)
// between two unix timestamps
function calculate_working_time($t1,$t2) {
// Note that this won't work if we have something
// more complicated than a weekend

  global $CONFIG;
  $swd=$CONFIG['start_working_day']/3600;
  $ewd=$CONFIG['end_working_day']/3600;

  // Just in case they are the wrong way around ...

  if ( $t1>$t2 ) {
    $t3=$t2;
    $t2=$t1;
    $t1=$t3;
  }

  // We don't need all the elements here.  hours, days and year are used
  // later on to calculate the difference.  wday is just used in this
  // section

  $at1=getdate($t1);
  $at2=getdate($t2);

  // Make sure that the start time is on a valid day and within normal hours
  // if it isn't then move it forward to the next work minute

  if ($at1['hours']>$ewd) {
    do {
      $at1['yday']++;
      $at1['wday']++;
      $at1['wday']%=7;
      if ($at1['yday']>365) {
        $at1['year']++;
        $at1['yday']=0;
      }
    } while (!in_array($at1['wday'],$CONFIG['working_days']));

    $at1['hours']=$swd;
    $at1['minutes']=0;

  } else {
    if (($at1['hours']<$swd) || (!in_array($at1['wday'],$CONFIG['working_days']))) {
      while (!in_array($at1['wday'],$CONFIG['working_days'])) {
        $at1['yday']++;
        $at1['wday']++;
        $at1['wday']%=7;
        if ($at1['days']>365) {
          $at1['year']++;
          $at1['yday']=0;
        }
      }

      $at1['hours']=$swd;
      $at1['minutes']=0;
    }
  }

  // Same again but for the end time
  // if it isn't then move it backward to the previous work minute

  if ( $at2['hours']<$swd) {
    do {
      $at2['yday']--;
      $at2['wday']--;
      if ($at2['wday']<0) $at2['wday']=6;
      if ($at2['yday']<0) {
        $at2['yday']=365;
        $at2['year']--;
      }
    } while (!in_array($at2['wday'],$CONFIG['working_days']));

    $at2['hours']=$ewd;
    $at2['minutes']=0;

  } else {
    if (($at2['hours']>$ewd) || (!in_array($at2['wday'],$CONFIG['working_days']))) {
      while (!in_array($at2['wday'],$CONFIG['working_days'])) {
        $at2['yday']--;
        $at2['wday']--;
        if ($at2['wday']<0) $at2['wday']=6;
        if ($at2['yday']<0) {
          $at2['yday']=365;
          $at2['year']--;
        }
      }
      $at2['hours']=$ewd;
      $at2['minutes']=0;
    }
  }


  $t1=mktime($at1['hours'],$at1['minutes'],0,1,$at1['yday']+1,$at1['year']);
  $t2=mktime($at2['hours'],$at2['minutes'],0,1,$at2['yday']+1,$at2['year']);

  $weeks=floor(($t2-$t1)/(60*60*24*7));
  $t1+=$weeks*60*60*24*7;

  while ( date('z',$t2) != date('z',$t1) ) {
    if (in_array(date('w',$t1),$CONFIG['working_days'])) $days++;
    $t1+=(60*60*24);
  }

  // this could be negative and that's not ok

  $coefficient=1;
  if ($t2<$t1) {
    $t3=$t2;
    $t2=$t1;
    $t1=$t3;
    $coefficient=-1;
  }

  $min=floor( ($t2-$t1)/60 )*$coefficient;

  $minutes= $min + ($weeks * count($CONFIG['working_days']) + $days ) * ($ewd-$swd) * 60;
  return $minutes;
}


function is_active_status($status, $states) {
    if (in_array($status,$states)) return false;
    else return true;
}


function calculate_incident_working_time($incidentid, $t1, $t2, $states=array(2,7,8))
{
    // Calculate the working time between two timestamps for a given incident
    // i.e. ignore times when customer has action

    if ( $t1>$t2 ) {
        $t3=$t2;
        $t2=$t1;
        $t1=$t3;
    }

    $sql="SELECT id, currentstatus, timestamp FROM updates WHERE incidentid='$incidentid' ORDER BY id ASC";
    $result=mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $time=0;
    $timeptr=0;
    $laststatus=2; // closed
    while ($update=mysql_fetch_array($result))
    {
        //  if ($t1<=$update['timestamp'])
        if ($t1<=$update['timestamp'])
        {
            if ($timeptr==0)
            {
                // This is the first update
                // If it's active, set the ptr = t1
                // otherwise set to current timestamp ???
                if (is_active_status($laststatus, $states)) $timeptr=$t1;
                else $timeptr=$update['timestamp'];
            }
            if ($t2<$update['timestamp'])
            {
                // If we have reached the very end of the range, increment time to end of range, break
                if (is_active_status($laststatus, $states)) $time+=calculate_working_time($timeptr,$t2);
                break;
            }

            // if status has changed or this is the first (active update)
            if (is_active_status($laststatus, $states)!=is_active_status($update['currentstatus'], $states))
            {
                // If it's active and we've not reached the end of the range, increment time
                if (is_active_status($laststatus, $states) && ($t2 >= $update['timestamp'])) $time+=calculate_working_time($timeptr,$update['timestamp']);
                else
                {
                    $timeptr=$update['timestamp'];
                }
                // if it's not active set the ptr
            }
        }
        $laststatus=$update['currentstatus'];
    }
    mysql_free_result($result);

    // Calculate remainder
    if ( is_active_status($laststatus, $states) && ($t2 >= $update['timestamp']))
    {
        $time+=calculate_working_time($timeptr,$t2);
    }

    return $time;
}


function strip_comma($string)
{
    // also strips Tabs, CR's and LF's
    $string=str_replace(",", " ", $string);
    $string=str_replace("\r", " ", $string);
    $string=str_replace("\n", " ", $string);
    $string=str_replace("\t", " ", $string);
    return $string;
}


function leading_zero($length,$number)
{
    $length=$length-strlen($number);
    for ($i = 0; $i < $length; $i++) { $number = "0" . $number;  }
    return($number);
}


function readable_date($date)
{
    // Takes a UNIX Timestamp and resturns a string with a pretty readable date
    // e.g. Yesterday @ 5:28pm
    if (date('dmy', $date) == date('dmy', time()))
        $datestring = "Today @ ".date('g:ia', $date);
    elseif (date('dmy', $date) == date('dmy', (time()-86400)))
        $datestring = "Yesterday @ ".date('g:ia', $date);
    else
        $datestring = date("l jS M y @ g:ia", $date);
    return $datestring;
}


// Select a header style, h1, h2 etc.
function header_listbox($headersize,$header,$element)
{
    $html .= "<select id='header$element' name='header$element' style='display:inline' onchange=\"change_header($element,'$header');\">\n";
    $html .= "<option value='h1' ";  if ($headersize=='h1') $html .= "selected='selected'";  $html .= ">H1 (Largest)</option>\n";
    $html .= "<option value='h2' ";  if ($headersize=='h2') $html .= "selected='selected'";  $html .= ">H2</option>\n";
    $html .= "<option value='h3' ";  if ($headersize=='h3') $html .= "selected='selected'";  $html .= ">H3</option>\n";
    $html .= "<option value='h4' ";  if ($headersize=='h4') $html .= "selected='selected'";  $html .= ">H4</option>\n";
    $html .= "<option value='h5' ";  if ($headersize=='h5') $html .= "selected='selected'";  $html .= ">H5 (Smallest)</option>\n";
    $html .= "</select>\n";
    return $html;
}


function distribution_listbox($name, $distribution)
{
    $html  = "<select name='$name'>\n";
    $html .= "<option value='public' ";  if ($distribution=='public') $html .= "selected='selected'";  $html .= ">Public</option>\n";
    $html .= "<option value='private' style='color: blue;'";  if ($distribution=='private') $html .= "selected='selected'";  $html .= ">Private</option>\n";
    $html .= "<option value='restricted' style='color: red;'";  if ($distribution=='restricted') $html .= "selected='selected'";  $html .= ">Restricted</option>\n";
    $html .= "</select>\n";
    return $html;
}


function remove_slashes($string)
{
    $string = str_replace("\\'", "'", $string);
    $string = str_replace("\'", "'", $string);
    $string = str_replace("\\'", "'", $string);
    $string = str_replace("\\\"", "\"", $string);

    return $string;
}


// Uses flag MGR to determine manager
function contact_manager_email($contactid)
{
    $sql = "SELECT siteid FROM contacts WHERE id='$contactid' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    list($siteid) = mysql_fetch_row($result);

    $sql = "SELECT * FROM contacts,contactflags WHERE contacts.id=contactflags.contactid ";
    $sql .= "AND contacts.siteid='{$siteid}' AND contactflags.flag='MGR'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) >= 1)
    {
        while ($contact = mysql_fetch_object($result))
        {
            $manager[]="{$contact->email}";
        }
        $managers=implode(", ", $manager);
    }
    else $managers='';
    return ($managers);
}


function contact_notify_email($contactid)
{
    $sql = "SELECT notify_contactid FROM contacts WHERE id='$contactid' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    list($notify_contactid) = mysql_fetch_row($result);

    $sql = "SELECT email FROM contacts WHERE id='$notify_contactid' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    list($email) = mysql_fetch_row($result);

    return $email;
}


function software_backup_dropdown($name, $userid, $softwareid, $backupid)
{
    $sql = "SELECT *, users.id AS userid FROM usersoftware, software, users WHERE usersoftware.softwareid=software.id ";
    $sql .= "AND software.id='$softwareid' ";
    $sql .= "AND userid!='{$userid}' AND users.status > 0 ";
    $sql .= "AND usersoftware.userid=users.id ";
    $sql .= " ORDER BY realname";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    $countsw=mysql_num_rows($result);
    if ($countsw >= 1)
    {
        $html = "<select name='$name'>\n";
        $html .= "<option value='0'";
        if ($user->userid==0) $html .= " selected='selected'";
        $html .= ">None</option>\n";
        while ($user = mysql_fetch_object($result))
        {
            $html .= "<option value='{$user->userid}'";
            if ($user->userid==$backupid) $html .= " selected='selected'";
            $html .= ">{$user->realname}</option>\n";
        }
        $html .= "</select>\n";
    }
    else
    {
        $html .= "<input type='hidden' name='$name' value='0' />None available";
    }
    return($html);
}


function software_backup_userid($userid, $softwareid)
{
    $backupid=0; // default
    // Find out who is the backup for this
    $sql = "SELECT backupid FROM usersoftware WHERE userid='$userid' AND userid!='$userid' AND softwareid='$softwareid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    list($backupid)=mysql_fetch_row($result);
    $backup1=$backupid;

    // Check to see if that user is accepting
    if (empty($backupid) OR user_accepting($backupid)!='Yes')
    {
        $sql = "SELECT backupid FROM usersoftware WHERE userid='$backupid' AND userid!='$userid' AND softwareid='$softwareid' AND backupid!='$backup1'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        list($backupid)=mysql_fetch_row($result);
        $backup2=$backupid;
    }

    // One more iteration, is the backup of the backup accepting?
    if (empty($backupid) OR user_accepting($backupid)!='Yes')
    {
        $sql = "SELECT backupid FROM usersoftware WHERE userid='$backupid' AND userid!='$userid' AND softwareid='$softwareid' AND backupid!='$backup1' AND backupid!='$backup2'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        list($backupid)=mysql_fetch_row($result);
    }
    return ($backupid);
}


function incident_backup_switchover($userid, $accepting)
{
    // Switches incidents to temporarily by the backup/substitute engineer depending on the setting of 'accepting'
    // yes: back to user
    // no: assign to backup
    global $now;

    if (strtolower($accepting)=='no')
    {
        // Look through the incidents that this user OWNS (and are not closed)
        $sql = "SELECT * FROM incidents WHERE (owner='$userid' OR towner='$userid') AND status!=2";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($incident = mysql_fetch_object($result))
        {
            // Try and find a backup/substitute engineer
            $backupid=software_backup_userid($userid, $incident->softwareid);

            if (empty($backupid))
            {
                // no backup engineer found so add to the holding queue
                // Look to see if this assignment is in the queue already
                $fsql = "SELECT * FROM tempassigns WHERE incidentid='{$incident->id}' AND originalowner='{$userid}'";
                $fresult = mysql_query($fsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                if (mysql_num_rows($fresult) < 1)
                {
                    // it's not in the queue, and the user isn't accepting so add it
                    $userstatus=user_status($userid);
                    $usql = "INSERT INTO tempassigns (incidentid,originalowner,userstatus) VALUES ('{$incident->id}', '{$userid}', '$userstatus')";
                    mysql_query($usql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                }
            }
            else
            {
                // do an automatic temporary reassign
                // update incident
                $rusql = "UPDATE incidents SET ";
                $rusql .= "towner='{$backupid}', lastupdated='$now' WHERE id='{$incident->id}' LIMIT 1";
                mysql_query($rusql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

                // add update
                $username=user_realname($userid);
                $userstatus=userstatus_name(user_status($userid));
                $usermessage=user_message($userid);
                $bodytext="Previous Incident Owner ({$username}) {$userstatus}  {$usermessage}";
                $assigntype='tempassigning';
                $risql  = "INSERT INTO updates (incidentid, userid, bodytext, type, timestamp, currentowner, currentstatus) ";
                $risql .= "VALUES ('{$incident->id}', '0', '$bodytext', '$assigntype', '$now', ";
                $risql .= "'{$backupid}', ";
                $risql .= "'{$incident->status}')";
                mysql_query($risql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

                // Look to see if this assignment is in the queue already
                $fsql = "SELECT * FROM tempassigns WHERE incidentid='{$incident->id}' AND originalowner='{$userid}'";
                $fresult = mysql_query($fsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                if (mysql_num_rows($fresult) < 1)
                {
                    $userstatus=user_status($userid);
                    $usql = "INSERT INTO tempassigns (incidentid,originalowner,userstatus,assigned) VALUES ('{$incident->id}', '{$userid}', '$userstatus','yes')";
                    mysql_query($usql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                }
                else
                {
                    // mark the temp assigns table so it's not showing in the holding queue
                    $tasql = "UPDATE tempassigns SET assigned='yes' WHERE originalowner='$userid' AND incidentid='{$incident->id}' LIMIT 1";
                    mysql_query($tasql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                }
            }
        }
    }
    elseif($accepting=='')
    {
        // Do nothing when accepting status doesn't exist
    }
    else
    {
        // The user is now ACCEPTING, so first have a look to see if there are any reassignments in the queue
        $sql = "SELECT * FROM tempassigns WHERE originalowner='{$userid}' ";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($assign = mysql_fetch_object($result))
        {
            if ($assign->assigned=='yes')
            {
                // Incident has actually been reassigned, so have a look if we can grab it back.
                $lsql = "SELECT id,status FROM incidents WHERE id='{$assign->incidentid}' AND owner='{$assign->originalowner}' AND towner!=''";
                $lresult = mysql_query($lsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                while ($incident = mysql_fetch_object($lresult))
                {
                    // Find our tempassign
                    $usql = "SELECT id,currentowner FROM updates WHERE incidentid='{$incident->id}' AND userid='0' AND type='tempassigning' ORDER BY id DESC LIMIT 1";
                    $uresult = mysql_query($usql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                    list($prevassignid,$tempowner) = mysql_fetch_row($uresult);

                    // Look to see if the temporary owner has updated the incident since we temp assigned it
                    // If he has, we leave it in his queue
                    $usql = "SELECT id FROM updates WHERE incidentid='{$incident->id}' AND id > '{$prevassignid}' AND userid='$tempowner' LIMIT 1 ";
                    $uresult = mysql_query($usql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                    if (mysql_num_rows($uresult) < 1)
                    {
                        // Incident appears not to have been updated by the temporary owner so automatically reassign back to orignal owner
                        // update incident
                        $rusql = "UPDATE incidents SET ";
                        $rusql .= "towner='', lastupdated='$now' WHERE id='{$incident->id}' LIMIT 1";
                        mysql_query($rusql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

                        // add update
                        $username=user_realname($userid);
                        $userstatus=userstatus_name(user_status($userid));
                        $usermessage=user_message($userid);
                        $bodytext="Reassigning to original owner {$username} ({$userstatus})";
                        $assigntype='reassigning';
                        $risql  = "INSERT INTO updates (incidentid, userid, bodytext, type, timestamp, currentowner, currentstatus) ";
                        $risql .= "VALUES ('{$incident->id}', '0', '$bodytext', '$assigntype', '$now', ";
                        $risql .= "'{$backupid}', ";
                        $risql .= "'{$incident->status}')";
                        mysql_query($risql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

                        // remove from assign queue now, all done
                        $rsql = "DELETE FROM tempassigns WHERE incidentid='{$assign->incidentid}' AND originalowner='{$assign->originalowner}'";
                        mysql_query($rsql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    }
                }
            }
            else
            {
                // now have a look to see if the reassign was completed
                $ssql = "SELECT id FROM incidents WHERE id='{$assign->incidentid}' LIMIT 1";
                $sresult = mysql_query($ssql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                if (mysql_num_rows($sresult) >= 1)
                {
                    // reassign wasn't completed, or it was already assigned back, simply remove from assign queue
                    $rsql = "DELETE FROM tempassigns WHERE incidentid='{$assign->incidentid}' AND originalowner='{$assign->originalowner}'";
                    mysql_query($rsql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                }
            }
        }
    }
    return;
}


function format_external_id($externalid, $escalationpath='')
{
    global $CONFIG;
    if (!empty($escalationpath))
    {
        // Extract escalation path
        $epsql = "SELECT id, name, track_url, home_url, url_title FROM escalationpaths ";
        if (!empty($escalationpath)) $epsql .= "WHERE id='$escalationpath' ";
        $epresult = mysql_query($epsql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        if (mysql_num_rows($epresult) >= 1)
        {
            while ($escalationpath = mysql_fetch_object($epresult))
            {
                $epath['name'] = $escalationpath->name;
                $epath['track_url'] = $escalationpath->track_url;
                $epath['home_url'] = $escalationpath->home_url;
                $epath['url_title'] = $escalationpath->url_title;
            }
            if (!empty($externalid))
            {
                $epathurl = str_replace('%externalid%',$externalid,$epath['track_url']);
                $html = "<a href='{$epathurl}' title='{$epath['url_title']}'>{$externalid}</a>";
            }
            else
            {
                $epathurl = $epath['home_url'];
                $html = "<a href='{$epathurl}' title='{$epath['url_title']}'>{$epath['name']}</a>";
            }
        }
    }
    else
    {
        $html = $externalid;
        foreach($CONFIG['ext_esc_partners'] AS $partner)
        {
            if(!empty($partner['ext_callid_regexp']))
            {
                if(preg_match($partner['ext_callid_regexp'], $externalid))
                {
                    if(!empty($partner['ext_url']))
                    {
                        $html = "<a href='".str_replace("%externalid", $externalid, $partner['ext_url'])."' title = '".$partner['ext_url_title']."'>{$externalid}</a>";
                    }
                }
            }
        }
    }
    return $html;
}


// Converts a PHP.INI integer into a byte value
function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last)
    {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}


function draw_tabs($tabsarray, $selected='')
{
      if ($selected=='') $selected=key($tabsarray);
      $html .= "<div id='tabcontainer'>";
      $html .= "<ul id='tabnav'>";
      foreach ($tabsarray AS $tab => $url)
      {
        $html .= "<li><a href='$url'";
        if (strtolower($tab)==strtolower($selected)) $html .= " class='active'";
        $tab=str_replace('_', ' ', $tab);
        $html .= ">$tab</a></li>\n";
      }
      $html .= "</ul>";
      $html .= "</div>";

  return ($html);
}


// Creates a blank feedback form response
function create_incident_feedback($formid, $incidentid)
{
    $contactid = incident_contact($incidentid);
    $email = contact_email($respondent);

    $sql = "INSERT INTO feedbackrespondents (formid, contactid, email, incidentid) VALUES (";
    $sql .= "'".mysql_escape_string($formid)."', ";
    $sql .= "'".mysql_escape_string($contactid)."', ";
    $sql .= "'".mysql_escape_string($email)."', ";
    $sql .= "'".mysql_escape_string($incidentid)."') ";
    mysql_query($sql);
    if (mysql_error()) trigger_error ("MySQL Error: ".mysql_error(), E_USER_ERROR);
    $blankformid=mysql_insert_id();
    return $blankformid;
}


function random_tip()
{
    global $CONFIG;
    $delim="\n";
    if(!file_exists($CONFIG['tipsfile']))
    {
        trigger_error("Tips file '{$CONFIG['tipsfile']}' was not found!  check your paths!",E_USER_WARNING);
    }
    else
    {
        $fp = fopen($CONFIG['tipsfile'], "r");
        if (!$fp) trigger_error("{$CONFIG['tipsfile']} was not found!", E_USER_WARNING);
    }
    $contents = fread($fp, filesize($CONFIG['tipsfile']));
    $tips = explode($delim,$contents);
    fclose($fp);
    srand((double)microtime()*1000000);
    $atip = (rand(1, sizeof($tips)) - 1);
    $thetip = "#".($atip+1).": ".$tips[$atip];

    return $thetip;
}


function file_permissions_info($perms)
{
    if (($perms & 0xC000) == 0xC000) $info = 's';
    elseif (($perms & 0xA000) == 0xA000) $info = 'l';
    elseif (($perms & 0x8000) == 0x8000) $info = '-';
    elseif (($perms & 0x6000) == 0x6000) $info = 'b';
    elseif (($perms & 0x4000) == 0x4000) $info = 'd';
    elseif (($perms & 0x2000) == 0x2000) $info = 'c';
    elseif (($perms & 0x1000) == 0x1000) $info = 'p';
    else $info = 'u';

    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}


function cleanvar($var,$striphtml=TRUE, $transentities=TRUE)
{
    if ($striphtml) $var = strip_tags($var);
    if ($transentities) $var = htmlentities($var);
    else $var = htmlspecialchars($var);

    $var = mysql_escape_string($var);
    $var = trim($var);
    return $var;
}


function external_escalation($escalated, $incid)
{

   foreach($escalated as $i => $id){
	if($id == $incid){
	   return "yes";
	}
   }

   return "no";
}

function user_notification_on_reassign($user)
{
    return db_read_column('var_notify_on_reassign', 'users', $user);
}

function bbcode($text)
{
    $bbcode_regex = array(0 => '/\[b\](.*?)\[\/b\]/s',
                        1 => '/\[i\](.*?)\[\/i\]/s',
                        2 => '/\[u\](.*?)\[\/u\]/s',
                        3 => '/\[quote\](.*?)\[\/quote\]/s',
                        4 => '/\[quote\=(.*?)](.*?)\[\/quote\]/s',
                        5 => '/\[url\](.*?)\[\/url\]/s',
                        6 => '/\[url\=(.*?)\](.*?)\[\/url\]/s',
                        7 => '/\[img\](.*?)\[\/img\]/s',
                        8 => '/\[color\=(.*?)\](.*?)\[\/color\]/s',
                        9 => '/\[size\=(.*?)\](.*?)\[\/size\]/s',
                        10 => '/\[code\](.*?)\[\/code\]/s',
                        11 => '/\[hr\]/s');

    $bbcode_replace = array(0 => '<strong>$1</strong>',
                            1 => '<em>$1</em>',
                            2 => '<u>$1</u>',
                            3 => '<blockquote><p>$1</p></blockquote>',
                            4 => '<blockquote cite="$1"><p>$1 said:<br />$2</p></blockquote>',
                            5 => '<a href="$1" title="$1">$1</a>',
                            6 => '<a href="$1" title="$1">$2</a>',
                            7 => '<img src="$1" alt="User submitted image" title="User submitted image"/>',
                            8 => '<span style="color:$1">$2</span>',
                            9 => '<span style="font-size:$1">$2</span>',
                            10 => '<code>$1</code>',
                            11 => '<hr />');

    return preg_replace($bbcode_regex, $bbcode_replace, $text);
}

function strip_bbcode_tooltip($text)
{
    $bbcode_regex = array(0 => '/\[url\](.*?)\[\/url\]/s',
                        1 => '/\[url\=(.*?)\](.*?)\[\/url\]/s',
                        2 => '/\[color\=(.*?)\](.*?)\[\/color\]/s',
                        3 => '/\[size\=(.*?)\](.*?)\[\/size\]/s');
    $bbcode_replace = array(0 => '$1',
                            1 => '$2',
                            2 => '$2',
                            3 => '$2');

    return preg_replace($bbcode_regex, $bbcode_replace, $text);
}


function date_picker($formelement)
{
    // Parameter1: form element id, eg. myform.dateinputbox
    global $CONFIG, $iconset;

    $divid = "datediv".str_replace('.','',$formelement);
    $html = "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/actions/1day.png' ";
    $html .= "onmouseup=\"toggleDatePicker('$divid','$formelement')\" width='16' height='16' alt='date picker' style='cursor: pointer;' />";
    $html .= "<div id='$divid' style='position: absolute;'></div>";
    return $html;
}


function percent_bar($percent)
{
    if ($percent=='') $percent=0;
    // #B4D6B4;
    $html = "<div style='width: 100px; border: 1px solid #ccc; background-color: white; height: 12px;'>";
    $html .= "<div style='text-align: center; height: 12px; font-size: 90%; width: {$percent}%; background: #AFAFAF;'>  {$percent}&#037;";
    $html .= "</div></div>\n";
    return $html;
}


function incident_open($incidentid)
{
    $sql = "SELECT id FROM incidents WHERE id='$incidentid' AND status!=2";
    $result=mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result) > 0)
    {
        return "Yes";
    }
    else
    {
        $sql = "SELECT id FROM incidents WHERE id = '$incidentid'";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if (mysql_num_rows($result) > 0)
        {
            //closed
            return "No";
        }
        else
        {
            //doesn't exist
            return "Doesn't exist";
        }
    }
}

// Return HTML for a table column header (th and /th) with links for sorting
// Filter parameter can be an assocative array containing fieldnames and values
// to pass on the url for data filtering purposes
function colheader($colname, $coltitle, $sort=FALSE, $order='', $filter='', $defaultorder='a')
{
    global $CONFIG;
    $html = "<th>";
    $qsappend='';
    if (!empty($filter) AND is_array($filter))
    {
        foreach ($filter AS $key => $var)
        {
            if ($var != '') $qsappend .= "&amp;{$key}=".urlencode($var);
        }
    }
    else $qsappend='';
    if ($sort==$colname)
    {
        //if ($order=='') $order=$defaultorder;
        if ($order=='a')
        {
            $html .= "<a href='{$_SERVER['PHP_SELF']}?sort=$colname&amp;order=d{$qsappend}'>{$coltitle}</a> ";
            $html .= "<img src='{$CONFIG['application_webpath']}images/sort_a.png' width='5' height='5' alt='Sort Ascending' style='border: 0px;' /> ";
        }
        else
        {
            $html .= "<a href='{$_SERVER['PHP_SELF']}?sort=$colname&amp;order=a{$qsappend}'>{$coltitle}</a> ";
            $html .= "<img src='{$CONFIG['application_webpath']}images/sort_d.png' width='5' height='5' alt='Sort Descending' style='border: 0px;' /> ";
        }
    }
    else
    {
        if ($sort===FALSE) $html .= "{$coltitle}";
        else $html .= "<a href='{$_SERVER['PHP_SELF']}?sort=$colname&amp;order={$defaultorder}{$qsappend}'>{$coltitle}</a> ";
    }
    $html .= "</th>";
    return $html;
}

function parse_updatebody($updatebody)
{
    if (!empty($updatebody))
    {
        $updatebody=stripslashes($updatebody);
        $updatebody=str_replace("&lt;hr&gt;", "[hr]\n", $updatebody);
        $updatebody=strip_tags($updatebody);
        $updatebody=nl2br($updatebody);
        $updatebody=str_replace("&amp;quot;", "&quot;", $updatebody);
        $updatebody=str_replace("&amp;gt;", "&gt;", $updatebody);
        $updatebody=str_replace("&amp;lt;", "&lt;", $updatebody);
        // Insert path to attachments
        $updatebody = preg_replace("/\[\[att\]\](.*?)\[\[\/att\]\]/","$1", $updatebody);
        //remove tags that are incompatable with tool tip
        $updatebody = strip_bbcode_tooltip($updatebody);
        //then show compatable BBCode
        $updatebody = bbcode($updatebody);
        if (strlen($updatebody)>490) $updatebody .= '...';
    }

    return $updatebody;
}


function add_note_form($linkid, $refid)
{
    global $now, $sit, $iconset;
    $html = "<form name='addnote' action='add_note.php' method='post'>";
    $html .= "<div class='detailhead note'> <div class='detaildate'>".readable_date($now)."</div>\n";
    $html .= "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/mimetypes/document2.png' width='16' height='16' alt='Note icon' /> ";
    $html .= "New Note by ".user_realname($sit[2])."</div>\n";
    $html .= "<div class='detailentry note'>";
    $html .= "<textarea rows='3' cols='40' name='bodytext' style='width: 94%; margin-top: 5px; margin-bottom: 5px; margin-left: 3%; margin-right: 3%; background-color: transparent; border: 1px dashed #A2A86A;'></textarea>";
    if (!empty($linkid)) $html .= "<input type='hidden' name='link' value='$linkid' />";
    else $html .= "&nbsp;Link <input type='text' name='link' size='3' />";
    if (!empty($refid)) $html .= "<input type='hidden' name='refid' value='{$refid}' />";
    else $html .= "&nbsp;Ref ID <input type='text' name='refid' size='4' />";
    $html .= "<input type='hidden' name='action' value='addnote' />";
    $html .= "<input type='hidden' name='rpath' value='{$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}' />";
    $html .= "<div style='text-align: right'><input type='submit' value='Add note' /></div>\n";
    $html .= "</div>\n";
    $html .= "</form>";
    return $html;
}

function show_notes($linkid, $refid)
{
    global $sit, $iconset;
    $sql = "SELECT * FROM notes WHERE link='{$linkid}' AND refid='{$refid}' ORDER BY timestamp DESC, id DESC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    $countnotes = mysql_num_rows($result);
    if ($countnotes >= 1)
    {
        while ($note = mysql_fetch_object($result))
        {
            $html .= "<div class='detailhead note'> <div class='detaildate'>".readable_date(mysqlts2date($note->timestamp));
            if ($sit[2]==$note->userid) $html .= "<a href='delete_note.php?id={$note->id}&amp;rpath={$_SERVER['PHP_SELF']}?{$_SERVER['QUERY_STRING']}' onclick='return confirm_delete();'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/actions/eventdelete.png' width='16' height='16' alt='Delete icon' style='border: 0px;' /></a>";
            $html .= "</div>\n";
            $html .= "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/mimetypes/document2.png' width='16' height='16' alt='Note icon' /> ";
            $html .= "Note added by ".user_realname($note->userid,TRUE)."</div>\n";
            $html .= "<div class='detailentry note'>";
            $html .= nl2br(bbcode(stripslashes($note->bodytext)));
            $html .= "</div>\n";
        }
    }
    return $html;
}

function dashboard_do($context, $row=0, $dashboardid=0)
{
    global $DASHBOARDCOMP;
    $action = $DASHBOARDCOMP[$context];
    if($action != NULL || $action != "")
    {
        $action($row,$dashboardid);
    }
}

function show_dashboard_component($row, $dashboardid)
{
    $sql = "SELECT name FROM dashboard WHERE enabled = 'true' AND id = '$dashboardid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    if(mysql_num_rows($result) == 1)
    {
        $obj = mysql_fetch_object($result);
        dashboard_do("dashboard_".$obj->name,'db_'.$row,$dashboardid);
    }
}

// Recursive function to list links as a tree
function show_links($origtab, $colref, $level=0, $parentlinktype='', $direction='lr')
{
    // Maximum recursion
    $maxrecursions=15;

    if ($level <= $maxrecursions)
    {
        $sql = "SELECT * FROM linktypes WHERE origtab='$origtab' ";
        if (!empty($parentlinktype)) $sql .= "AND id='{$parentlinktype}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($linktype = mysql_fetch_object($result))
        {
            // Look up links of this type
            $lsql = "SELECT * FROM links WHERE linktype='{$linktype->id}' ";
            if ($direction=='lr') $lsql .= "AND origcolref='{$colref}'";
            elseif ($direction=='rl') $lsql .= "AND linkcolref='{$colref}'";
            $lresult = mysql_query($lsql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            if (mysql_num_rows($lresult) >= 1)
            {
                if (mysql_num_rows($lresult) >= 1)
                {
                    $html .= "<ul>";
                    $html .= "<li>";
                    while ($link = mysql_fetch_object($lresult))
                    {
                        $recsql = "SELECT {$linktype->selectionsql} AS recordname FROM {$linktype->linktab} WHERE ";
                        if ($direction=='lr') $recsql .= "{$linktype->linkcol}='{$link->linkcolref}' ";
                        elseif ($direction=='rl') $recsql .= "{$linktype->origcol}='{$link->origcolref}' ";
                        $recresult = mysql_query($recsql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                        while ($record = mysql_fetch_object($recresult))
                        {
                            if ($link->direction=='bi') $html .= "<strong>{$linktype->name}</strong> ";
                            elseif ($direction=='lr') $html .= "<strong>{$linktype->lrname}</strong> ";
                            elseif ($direction=='rl') $html .= "<strong>{$linktype->rlname}</strong> ";
                            else $html = "Whoops";

                            if ($direction=='lr') $currentlinkref=$link->linkcolref;
                            elseif ($direction=='rl') $currentlinkref=$link->origcolref;

                            $viewurl = str_replace('%id%',$currentlinkref,$linktype->viewurl);

                            $html .= "{$currentlinkref}: ";
                            if (!empty($viewurl)) $html .= "<a href='$viewurl'>";
                            $html .= "{$record->recordname}";
                            if (!empty($viewurl)) $html .= "</a>";
                            $html .= " - ".user_realname($link->userid,TRUE);
                            $html .= show_links($linktype->linktab, $currentlinkref, $level+1, $linktype->id, $direction); // Recurse
                            $html .= "</li>\n";
                        }
                    }
                    $html .= "</ul>\n";
                }
                else $html .= "<p>None</p>";
            }
        }
    }
    else $html .= "<p class='error'>Maximum number of {$maxrecursions} recursions reached</p>";
    return $html;
}


function show_create_links($table, $ref)
{
    $html .= "<p align='center'>Add Link: ";
    $sql = "SELECT * FROM linktypes WHERE origtab='$table' OR linktab='$table' ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $numlinktypes=mysql_num_rows($result);
    $rowcount=1;
    while ($linktype = mysql_fetch_object($result))
    {
        if ($linktype->origtab == $table AND $linktype->linktab != $table) $html .= "<a href='add_link.php?origtab=tasks&amp;origref={$ref}&amp;linktype={$linktype->id}'>{$linktype->lrname}</a>";
        elseif ($linktype->origtab != $table AND $linktype->linktab == $table) $html .= "<a href='add_link.php?origtab=tasks&amp;origref={$ref}&amp;linktype={$linktype->id}'>{$linktype->rlname}</a>";
        else
        {
            $html .= "<a href='add_link.php?origtab=tasks&amp;origref={$ref}&amp;linktype={$linktype->id}'>{$linktype->lrname}</a> | ";
            $html .= "<a href='add_link.php?origtab=tasks&amp;origref={$ref}&amp;linktype={$linktype->id}&amp;dir=rl'>{$linktype->rlname}</a>";
        }
        if ($rowcount < $numlinktypes) $html .= " | ";
        $rowcount++;
    }
    $html .= "</p>";
    return $html;
}


// Function to create a PNG chart
// Returns an image resource
// Currently only has support for pie charts (type='pie')
function draw_chart_image($type, $width, $height, $data, $legends, $title='')
{
    global $CONFIG;
    // Graph settings
    if (empty($width)) $width = 500;
    if (empty($height)) $height = 150;
    $fontfile="{$CONFIG['application_fspath']}FreeSans.ttf";

    if (!empty($fontfile) AND file_exists($fontfile)) $use_ttf=TRUE;
    else $use_ttf=FALSE;

    $countdata = count($data);
    $sumdata = array_sum($data);

    if ($countdata > 8) $height += (($countdata - 8) * 14);

    $img = imagecreatetruecolor($width, $height);

    $white = imagecolorallocate($img, 255, 255, 255);
    $blue = imagecolorallocate($img, 240, 240, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    $red = imagecolorallocate($img, 255, 0, 0);

    imagefill($img, 0, 0, $white);

    $rgb[] = "190,190,255";
    $rgb[] = "205,255,255";
    $rgb[] = "255,255,156";
    $rgb[] = "156,255,156";
    $rgb[] = "255,205,195";
    $rgb[] = "255,140,255";
    $rgb[] = "100,100,155";
    $rgb[] = "98,153,90";
    $rgb[] = "205,210,230";
    $rgb[] = "192,100,100";
    $rgb[] = "204,204,0";
    $rgb[] = "255,102,102";
    $rgb[] = "0,204,204";
    $rgb[] = "0,255,0";
    $rgb[] = "255,168,88";
    $rgb[] = "128,0,128";
    $rgb[] = "0,153,153";
    $rgb[] = "255,230,204";
    $rgb[] = "128,170,213";
    $rgb[] = "75,75,75";
    // repeats...
    $rgb[] = "190,190,255";
    $rgb[] = "156,255,156";
    $rgb[] = "255,255,156";
    $rgb[] = "205,255,255";
    $rgb[] = "255,205,195";
    $rgb[] = "255,140,255";
    $rgb[] = "100,100,155";
    $rgb[] = "98,153,90";
    $rgb[] = "205,210,230";
    $rgb[] = "192,100,100";
    $rgb[] = "204,204,0";
    $rgb[] = "255,102,102";
    $rgb[] = "0,204,204";
    $rgb[] = "0,255,0";
    $rgb[] = "255,168,88";
    $rgb[] = "128,0,128";
    $rgb[] = "0,153,153";
    $rgb[] = "255,230,204";
    $rgb[] = "128,170,213";
    $rgb[] = "75,75,75";

    switch ($type)
    {
        case 'pie':
            $cx = '120';$cy ='60'; //Set Pie Postition. CenterX,CenterY
            $sx = '200';$sy='100';$sz ='15';// Set Size-dimensions. SizeX,SizeY,SizeZ

            // Title
            if (!empty($title))
            {
                $cy += 10;
                if ($use_ttf) imagettftext($img, 10, 0, 2, 10, $black, $fontfile, $title);
                else imagestring($img,2, 2, ($legendY-1), "{$title}", $black);
            }

            //convert to angles.
            for($i=0;$i<=$countdata;$i++)
            {
                $angle[$i] = (($data[$i] / $sumdata) * 360);
                $angle_sum[$i] = array_sum($angle);
            }

            $background = imagecolorallocate($img, 255, 255, 255);
            //Random colors.

            for($i=0;$i<=$countdata;$i++)
            {
                $rgbcolors = explode(',',$rgb[$i]);
                $colors[$i] = imagecolorallocate($img,$rgbcolors[0],$rgbcolors[1],$rgbcolors[2]);
                $colord[$i] = imagecolorallocate($img,($rgbcolors[0]/1.5),($rgbcolors[1]/1.5),($rgbcolors[2]/1.5));
            }

            //3D effect.
            $legendY = 80 - ($countdata * 10);
            if ($legendY < 10) $legendY = 10;
            for($z=1;$z<=$sz;$z++)
            {
                for($i=0;$i<$countdata;$i++)
                {
                    imagefilledarc($img,$cx,($cy+$sz)-$z,$sx,$sy,$angle_sum[$i-1],$angle_sum[$i],$colord[$i],IMG_ARC_PIE);
                }

            }
            imagerectangle($img, 250, $legendY-5, 470, $legendY+($countdata*15), $black);
            //Top pie.
            for($i=0;$i<$countdata;$i++)
            {
                imagefilledarc($img,$cx,$cy,$sx,$sy,$angle_sum[$i-1] ,$angle_sum[$i], $colors[$i], IMG_ARC_PIE);
                imagefilledrectangle($img, 255, ($legendY+1), 264, ($legendY+9), $colors[$i]);
                // Legend
                if ($use_ttf) imagettftext($img, 8, 0, 270, ($legendY+9), $black, $fontfile, substr(urldecode($legends[$i]),0,27)." ({$data[$i]})");
                else imagestring($img,2, 270, ($legendY-1), substr(urldecode($legends[$i]),0,27)." ({$data[$i]})", $black);
                // imagearc($img,$cx,$cy,$sx,$sy,$angle_sum[$i1] ,$angle_sum[$i], $blue);
                $legendY+=15;
            }
        break;

        default:
            imagestring($img,3, 10, 10, "Invalid chart type", $red);
    }

    // Return a PNG image
    return $img;
}


function get_tag_id($tag)
{
    $sql = "SELECT tagid FROM tags WHERE name = LOWER('$tag')";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if(mysql_num_rows($result) == 1)
    {
        $id = mysql_fetch_row($result);
        return $id[0];
    }
    else
    {
        //need to add
        $sql = "INSERT INTO tags (name) VALUES (LOWER('$tag'))";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        return mysql_insert_id();
    }
}

function add_tag($id, $type, $tag)
{
    /*
    TAG TYPES
    1 - contact
    2 - incident
    3 - Site
    */
    if ($tag!='')
    {
        $tagid = get_tag_id($tag);
        // Ignore errors, die silently
        $sql = "INSERT INTO set_tags VALUES ('$id', '$type', '$tagid')";
        $result = @mysql_query($sql);
    }
    return true;
}


function replace_tags($type, $id, $tagstring)
{
    // first remove old tags
    $sql = "DELETE FROM set_tags WHERE id = '$id' AND type = '$type'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    // Change seperators to spaces
    $seperators=array(', ',';',',');
    $tags=str_replace($seperators, ' ', trim($tagstring));
    $tag_array = explode(" ", $tags);
    foreach($tag_array AS $tag)
    {
        add_tag($id, $type, trim($tag));
    }
}

function list_tags($recordid, $type, $html=TRUE)
{
    $sql = "SELECT tags.name, tags.tagid FROM set_tags, tags WHERE set_tags.tagid = tags.tagid AND ";
    $sql .= "set_tags.type = '$type' AND set_tags.id = '$recordid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    while($tags = mysql_fetch_object($result))
    {
        if($html) $str .= "<a href='view_tags.php?tagid={$tags->tagid}'>{$tags->name}</a>, ";
        else $str .= $tags->name.", ";
    }
    return trim(substr($str, 0, strlen($str)-2));
}

function show_tag_cloud($orderby="name")
{
    $sql = "SELECT COUNT(name) AS occurrences, name, tags.tagid FROM tags, set_tags WHERE tags.tagid = set_tags.tagid GROUP BY name ORDER BY $orderby";
    if($orderby == "occurrences") $sql .= " DESC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    $countsql = "SELECT COUNT(*) AS counted FROM set_tags GROUP BY tagid ORDER BY counted DESC LIMIT 1";

    $countresult = mysql_query($countsql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    list($max) = mysql_fetch_row($countresult);

    if($_SERVER['PHP_SELF'] != "/main.php")
    {
        //not in the dashbaord
        $html .= "<p align='center'>Sort: <a href='view_tags.php?orderby=name'>alphabetically</a> | ";
        $html .= "<a href='view_tags.php?orderby=occurrences'>popularity</a></p>";
    }
    if(mysql_num_rows($result) > 0)
    {
        $html .= "<table align='center'><tr><td>";
        $min=1;

        while($obj = mysql_fetch_object($result))
        {
            $size = (($obj->occurrences) * 300 / $max) +100;
            if ($size==0) $size=100;
            $html .= "<a href='view_tags.php?tagid=$obj->tagid' style='font-size: {$size}%;' title='{$obj->occurrences}'>{$obj->name}</a> &nbsp;";
        }
        $html .= "</td></tr></table>";
    }
    return $html;
}

// -------------------------- // -------------------------- // --------------------------
// leave this section at the bottom of functions.inc.php ================================

// Evaluate and Load plugins
if (is_array($CONFIG['plugins']))
{
    foreach($CONFIG['plugins'] AS $plugin)
    {
        // Remove any dots
        $plugin=str_replace('.','',$plugin);
        // Remove any slashes
        $plugin=str_replace('/','',$plugin);
        if ($plugin!='') include("{$CONFIG['application_fspath']}/plugins/{$plugin}.php");
    }
}

function plugin_register($context, $action)
{
    global $PLUGINACTIONS;
    $PLUGINACTIONS[$context][] = $action;
}


function plugin_do($context, $optparams=FALSE)
{
    global $PLUGINACTIONS;

    if (is_array($PLUGINACTIONS[$context]))
    {
        foreach($PLUGINACTIONS[$context] AS $action)
        {
            // Call Variable function (function with variable name)
            if ($optparams) $rtn = $action($optparams);
            else $rtn = $action();

            // Append return value
            if (is_array($rtn) AND is_array($rtnvalue)) array_push($rtnvalue, $rtn);
            elseif (is_array($rtn) AND !is_array($rtnvalue)) { $rtnvalue=array(); array_push($rtnvalue, $rtn); }
            else $rtnvalue .= $rtn;
        }
    }
    return $rtnvalue;
}




// These are the modules that we are dependent on, without these something
// or everything will fail, so let's throw an error here.
// Check that the correct modules are loaded
// if (!extension_loaded('gd')) throw_error("FATAL ERROR: {$CONFIG['application_name']} requires the gd module", '');
if (!extension_loaded('pspell')) $CONFIG['enable_spellchecker']=FALSE; // FORCE Turn off spelling if module not found
if (!extension_loaded('mysql')) throw_error("FATAL ERROR: {$CONFIG['application_name']} requires the mysql module", '');
if (strtolower(ini_get("register_globals"))=="off") throw_error("FATAL ERROR: {$CONFIG['application_name']} requires the register globals to be ON, see php.ini", '');
## if (!extension_loaded('ftp')) throw_error("FATAL ERROR: {$CONFIG['application_name']} requires the ftp module", '');
## if (!extension_loaded('sockets')) throw_error("FATAL ERROR: {$CONFIG['application_name']} requires the sockets module", '');
//

?>
