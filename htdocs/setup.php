<?php
// setup.php - Install/Upgrade and set up plugins
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// Load config defaults
@include("defaults.inc.php");
// Keep the defaults as a seperate array
$DEFAULTS = $CONFIG;

// Load config file with customisations
// @include("config.inc-dist.php");
@include("config.inc.php");
// Server Configuration
@include('/etc/webtrack.conf'); // for legacy systems
@include('/etc/sit.conf');

// These are the required variables we want to configure during installation
$SETUP=array('db_hostname','db_database','db_username','db_password','application_fspath','application_webpath');

// Descriptions of all the config variables
$CFGVAR['db_hostname']['title']='MySQL Database Hostname';
$CFGVAR['db_hostname']['help']="The Hostname or IP address of the MySQL Database Server, usually 'localhost'";
$CFGVAR['db_username']['title']='MySQL Database Username';
$CFGVAR['db_password']['title']='MySQL Database Password';
$CFGVAR['db_database']['title']='MySQL Database Name';
$CFGVAR['application_fspath']['title']='Filesystem Path';
$CFGVAR['application_fspath']['help']="The full absolute filesystem path to the SiT! directory with trailing slash. e.g. '/var/www/sit/'";
$CFGVAR['application_webpath']['title'] = 'The path to SiT! from the browsers perspective with a training slash. e.g. /sit/';
$CFGVAR['application_name']['title']='The application name';
$CFGVAR['application_name']['help']='You should not normally need to change this';
$CFGVAR['application_shortname']['title'] = 'A short version of the application name';
$CFGVAR['application_shortname']['help']='You should not normally need to change this';
$CFGVAR['home_country']['title'] = "The default country in capitals. e.g. 'UNITED KINGDOM'";
$CFGVAR['support_email']['title'] = 'Emails sent by SiT will come from this address';
$CFGVAR['sales_email']['title'] = 'Your sales departments email address';
$CFGVAR['support_manager_email']['title'] = 'The email address of the person in charge of your support service';
$CFGVAR['bugtracker_name']['title'] = 'Bug tracker name';
$CFGVAR['bugtracker_url']['title'] = 'Bug tracker url';
$CFGVAR['dateformat_datetime']['title'] = 'Date and Time format';
$CFGVAR['dateformat_filedatetime']['title'] = 'Date and Time format to use for files';
$CFGVAR['dateformat_shortdate']['title'] = 'Short date format';
$CFGVAR['dateformat_shorttime']['title'] = 'Short time format';
$CFGVAR['dateformat_date']['title'] = 'Long date format';
$CFGVAR['dateformat_time']['title'] = 'Long time format';
$CFGVAR['closure_delay']['title'] = 'Closure Delay';
$CFGVAR['closure_delay']['help'] = 'The amount of time (in seconds) to wait before closing when an incident is marked for closure';
$CFGVAR['working_days']['title'] = 'Array containing working days (0=Sun, 1=Mon ... 6=Sat)';
$CFGVAR['start_working_day']['title'] = 'Time of the start of the working day (in seconds)';
$CFGVAR['end_working_day']['title'] = 'Time of the end of the working day (in seconds)';
$CFGVAR['attachment_fspath']['title'] = "The full absolute file system path to the attachments directory";
$CFGVAR['attachment_fspath']['help'] = "This directory should be writable";
$CFGVAR['attachment_webpath']['title'] = "The path to the attachments directory from the browsers perspective";
$CFGVAR['mailin_spool_path']['title'] = "Incoming mail spool directory, the location of mail processed by mailfilter shell script";
$CFGVAR['upload_max_filesize']['title'] = "The maximum file upload size (in bytes)";
$CFGVAR['ftp_hostname']['title'] = 'The ftp hostname or IP address';
$CFGVAR['ftp_username']['title'] = 'Ftp username';
$CFGVAR['ftp_password']['title'] = 'Ftp password';
$CFGVAR['ftp_pasv']['title'] = 'Set to TRUE to enable ftp PASSV mode or FALSE to disable';
$CFGVAR['ftp_path']['title'] = 'The path to the directory where we store files on the ftp server';
$CFGVAR['ftp_path']['help'] = '(e.g. /pub/support/) the trailing slash is important';
$CFGVAR['enable_spellchecker']['title'] = 'Set to TRUE to enable spellchecking or FALSE to disable';
$CFGVAR['main_dictionary_file']['title'] = 'Spell check main dictionary file';
$CFGVAR['custom_dictionary_file']['title'] = 'Spell check custom words dictionary file';
$CFGVAR['default_css_url']['title'] = 'The CSS file to use when no other is configured';
$CFGVAR['default_interface_style']['title'] = 'The interface style that new users should use (user default style)';
$CFGVAR['kb_id_prefix']['title'] = 'Knowledgebase ID prefix';
$CFGVAR['kb_id_prefix']['help'] = 'inserted before the ID to give it uniqueness';
$CFGVAR['kb_disclaimer_html']['title']  = 'Knowledgebase disclaimer, displayed at the bottom of every article';
$CFGVAR['default_service_level']['title'] = 'The service level to use in case the contact does not specify (text not the tag)';
$CFGVAR['regular_contact_days']['title'] = 'The number of days to elapse before we are prompted to contact the customer (usually overridden by SLA)';
$CFGVAR['free_support_limit']['title'] = 'Number of free (site) support incidents that can be logged to a site';
$CFGVAR['incident_pools']['title'] = 'Comma seperated list specifying the numbers of incidents to assign to contracts';
$CFGVAR['feedback_form']['title'] = 'Incident feedback form (the id number of the feedback form to use or empty to disable sending feedback forms out)';
$CFGVAR['trusted_server']['title'] = 'Enable trusted server mode';
$CFGVAR['trusted_server']['help'] = 'If you set this to TRUE, passwords will no longer be used or required, this assumes that you are using another mechanism for authentication';
$CFGVAR['record_lock_delay']['title'] = 'Lock records for (number of seconds)';
$CFGVAR['max_incoming_email_perday']['title']='maximum no. of incoming emails per incident before a mail-loop is detected';
$CFGVAR['spam_email_subject']['title']='String to look for in email message subject to determine a message is spam';
$CFGVAR['spam_forward']['title']='Email address to forward spam messages that are to be marked as spam';
$CFGVAR['feedback_max_score']['title']='The max score to use in rating fields for feedback forms';
$CFGVAR['tipsfile']['title']='Path to a file containing tips to be shown on the main page, one per line';
$CFGVAR['session_name']['title']='The session name for use in cookies and URLs, Must contain alphanumeric characters only';
$CFGVAR['demo']['title']='Run in demo mode, some features are disabled or replaced with mock-ups';
$CFGVAR['debug']['title'] = 'Set to TRUE to output extra debug information, some as HTML comments and some in the page footer, FALSE to disable';
$CFGVAR['journal_loglevel']['title'] = 'Journal Logging Level';
$CFGVAR['journal_loglevel']['help'] = '0 = none, 1 = minimal, 2 = normal, 3 = full, 4 = maximum/debug';
$CFGVAR['journal_purge_after']['title'] = 'How long should we keep journal entries (in seconds), entries older than this will be purged (deleted)';
$CFGVAR['logout_url']['title'] = "The URL to redirect the user too after he/she logs out";
$CFGVAR['error_logfile']['title'] = "Path to a file to log error messages";
$CFGVAR['error_logfile']['telp'] = "This file must be writable of course";
$CFGVAR['access_logfile']['title'] = 'Filename to log authentication failures';
$CFGVAR['access_logfile']['telp'] = "This file must be writable of course";
$CFGVAR['plugins']['title'] = "An array of plugin names";
$CFGVAR['plugins']['help'] = "e.g. 'array('magic_plugin', 'lookup_plugin')'";
$CFGVAR['error_notavailable_url']['title']="The URL to redirect too for pages that do not exist yet.";

$upgradeok = FALSE;
$config_filename='../includes/config.inc.php';

function setup_configure()
{
    global $SETUP, $CFGVAR, $CONFIG, $config_filename;
    $html = '';

    $cfg_file_exists = file_exists($config_filename);
    $cfg_file_writable = is_writable($config_filename);
    if ($cfg_file_exists)
    {
        $html .= "<h2>Found an existing <var>config.inc.php</var> file</h2>";
        $html .= "<p>Since you already have a config.inc.php file we assume you are upgrading or reconfiguring, if this is not the case please delete the file config.inc.php</p>";
        if ($cfg_file_writable)
        {
            $html .= "<p class='warning'>Important: The file permissions on the file config.inc.php allow the file to be modified, we recommend you make this file read-only once SiT! is configured.</p>";
        }
        else
        {
            $html .= "<p class='error'>A config file already exists but it is not writable</p>";
            $html .= "<p>For security we won't show your existing settings here unless the config.inc.php is writable.</p>";
        }
    }
    else $html .= "<h2>Configuration</h2><p>Please complete this form to create a new config.inc.php configuration file for SiT!</p>";

    $html .= "<form action='setup.php' method='post'>\n";

    if ($_REQUEST['config']=='advanced')
    {
        $html .= "<input type='hidden' name='config' value='advanced' />\n";
        foreach($CFGVAR AS $setupvar => $setupval)
        {
            $SETUP[] = $setupvar;
        }
    }

    foreach($SETUP AS $setupvar)
    {
        if ($CFGVAR[$setupvar]['title']!='') $title = $CFGVAR[$setupvar]['title'];
        else $title = $setupvar;
        $html .= "<h4>{$title}</h4>";
        if ($CFGVAR[$setupvar]['help']!='') $html .= "<p>{$CFGVAR[$setupvar]['help']}</p>\n";

        $html .= "\$CONFIG['$setupvar'] = <input type='text' name='$setupvar' size='60' value='";
        if (!$cfg_file_exists OR ($cfg_file_exists AND $cfg_file_writable))
        {
            $value = $CONFIG[$setupvar];
            if (is_bool($value))
            {
                if ($value==TRUE) $value='TRUE';
                else $value='FALSE';
            }
            if ($setupvar=='db_password') $value='';
            $html .= $value;
        }
        $html .= "' />";
        $html .= "<br />\n";
    }
    $html .= "<input type='hidden' name='action' value='save_config' />";
    $html .= "<br /><input type='submit' name='submit' Value='Save Configuration' />";
    $html .= "</form>\n";
    return $html;
}

function setup_exec_sql($sqlquerylist)
{
    global $CONFIG;
    if (!empty($sqlquerylist))
    {
        $sqlqueries = explode( ';', $sqlquerylist);
        // We don't need the last entry it's blank, as we end with a ;
        array_pop($sqlqueries);
        foreach($sqlqueries AS $sql)
        {
            mysql_query($sql);
            if (mysql_error())
            {
                $html .= "<p><strong>FAILED:</strong> ".htmlspecialchars($sql)."</p>";
                $html .= "<p class='error'>".mysql_error()."<br />A MySQL error occurred, this could be because the MySQL user '{$CONFIG['db_username']}' does not have appropriate permission to modify the database schema.<br />";
                //echo "The SQL command was:<br /><code>$sql</code><br />";
                $html .= "An error might also be caused by an attempt to upgrade a version that is not supported by this script.<br />";
                $html .= "Alternatively, you may have found a bug, if you think this is the case please report it.</p>";
            }
            else $html .= "<p><strong>OK:</strong> ".htmlspecialchars($sql)."</p>";
        }
    }
    return $html;
}


session_start();

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
echo " \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\" />\n";
echo "<style type=\"text/css\">\n";
echo "body { background-color: #FFF; font-family: Tahoma, Helvetica, sans-serif; font-size: 10pt;}\n";
echo "h1,h2,h3,h4,h5 { background-color: #203894; padding: 0.1em; border: 1px solid #203894; color: #FFF; }\n";
echo "h4 {background-color: #F7FAFF; color: #000; border: 1px solid #3165CD; }\n";
echo ".error {background: #FFFFCC; border: 1px solid red; color: red; padding: 2px;}\n";
echo ".help {background: #F7FAFF; border: 1px solid #3165CD; color: #203894; padding: 2px;}\n";
echo ".warning {background: #FFFFE6; border: 2px solid #FFFF31; color: red; padding: 2px;}\n";
echo "pre {background:#FFF; border:#999; padding: 1em;}\n";
echo "a:link,a:visited { color: #000099; }\n";
echo "a:hover { background: #99CCFF; }\n";
echo "hr { background-color: #203894; margin-top: 3em; }\n";
echo "</style>\n";
echo "<title>Support Incident Tracker Setup</title\n";
echo "</head>\n<body>\n";

echo "<h1>Support Incident Tracker - Installation &amp; Setup</h1>";

$include_path = ini_get('include_path');

// Check that includes worked
if ($CONFIG['application_name']=='' AND $CONFIG['application_shortname']=='')
{
    echo "<p class='error'>We couldn't find configuration defaults, this probably means your include_path is wrong. ";
    echo "Your current include path is <code>{$include_path}</code><br />";
    echo "SiT! Requires it's libraries to be in the include path which is specified in your php.ini file, modify your php.ini and set the new include path ";
    if (file_exists('../includes'))
    {
        $curdir = getcwd();
        $include_path .= ":".substr($curdir,0,strrpos($curdir,'/'))."/includes";
        // $include_path .= ":../includes";
        echo "to be <code>$include_path</code></p>";
    }
    else echo "to point to the includes directory";
    echo "</p>";
}


switch ($_REQUEST['action'])
{
    case 'save_config':
        $newcfgfile = "<";
        $newcfgfile .= "?php\n";
        $newcfgfile .= "# config.inc.php - SiT! Config file generated automatically by setup.php on ".date('r')."\n\n";

        if ($_REQUEST['config']=='advanced')
        {
            foreach($CFGVAR AS $setupvar => $setupval)
            {
                $SETUP[] = $setupvar;
            }
        }

        // Keep the posted setup
        foreach($SETUP AS $setupvar)
        {
            if ($_POST[$setupvar]=='TRUE') $_POST[$setupvar] = TRUE;
            if ($_POST[$setupvar]=='FALSE') $_POST[$setupvar] = FALSE;
            $CONFIG[$setupvar]=$_POST[$setupvar];
        }
        // Extract the differences between the defaults and the newly configured items
        $CFGDIFF = array_diff_assoc($CONFIG, $DEFAULTS);

        foreach($CFGDIFF AS $setupvar => $setupval)
        {
            if ($CFGVAR[$setupvar]['title']!='') $newcfgfile .= "# {$CFGVAR[$setupvar]['title']}\n";
            if ($CFGVAR[$setupvar]['help']!='') $newcfgfile .= "# {$CFGVAR[$setupvar]['help']}\n";
            $newcfgfile .= "\$CONFIG['$setupvar'] = ";
            if (is_numeric($setupval)) $newcfgfile .= "{$setupval}";
            elseif (is_bool($setupval)) $newcfgfile .= $setupval == TRUE ? "TRUE" : "FALSE";
            else $newcfgfile .= "'{$setupval}'";
            $newcfgfile .= ";\n\n";
        }
        $newcfgfile .= "?";
        $newcfgfile .= ">";

        $fp = @fopen($config_filename, 'w');
        if (!$fp)
        {
            echo "<p class='error'>Could not write {$config_filename}</p>";
            echo "<p class='help'>Copy this text and paste it into a <var>config.inc.php</var> file in the includes directory<br />";
            echo "Or change the permissions on the file so that it is writable and refresh this page to try again (if you do this remember to make it ";
            echo "read-only again afterwards)</p>";
            echo "<div style='margin-left: 5%; margin-right: 5%; background-color: white; padding: 1em;'>";
            highlight_string($newcfgfile);
            echo "</div>";
        }
        else
        {
            echo "<p>Writing to {$config_filename}</p>";
            fwrite($fp, $newcfgfile);
            fclose($fp);
            echo "<p>Config file modified</p>";
            echo "<p class='warning'>Important: The file permissions on the file config.inc.php allow the file to be modified, we recommend you now make this file read-only.</p>";
        }
        echo "<h2>After creating your config.inc.php file</h2>";
        echo "<p>Now run <a href='setup.php'>setup</a> again</p>";
    break;

    default:
        // Check we have the mysql extension
        if (!extension_loaded('mysql'))
        {
            echo "<p class='error'>Error: Could not find the mysql extension, SiT! requires MySQL to be able to run, you should install and enable the MySQL PHP Extension then run setup again.</p>";
        }
        // Connect to Database server
        $db = @mysql_connect($CONFIG['db_hostname'], $CONFIG['db_username'], $CONFIG['db_password']);
        if (mysql_error())
        {
            echo "<p class='error'>".mysql_error()."<br />Could not connect to database server '{$CONFIG['db_hostname']}'.  Did you configure the hostname correctly? Is the MySQL Server running?</p>";
            echo setup_configure();
        }
        else
        {
            // Connected to database
            // Select database
            mysql_select_db($CONFIG['db_database'], $db);
            if (mysql_error())
            {
                echo "<p class='error'>".mysql_error()."<br />Could not select database";
                if ($CONFIG['db_database']!='') echo " '{$CONFIG['db_database']}', check the database name,";
                else
                {
                    echo ", the database name was not configured, please set the <code>\$CONFIG['db_database'] config variable";
                    $CONFIG['db_database'] = 'sit';
                }
                echo "</p>";
                $sql = "CREATE DATABASE `{$CONFIG['db_database']}`";
                if ($_REQUEST['action']=='createdatabase')
                {
                    echo "<h2>Creating database...</h2>";
                    $result = mysql_query($sql);
                    if ($result) echo "<p><strong>OK</strong> Database '{$CONFIG['db_database']}' created.</p>";
                    else
                    {
                        echo "<p class='error'>".mysql_error()."<br />The database could not be created automatically, ";
                        echo "you can create it manually by executing the SQL statement <br /><code>{$sql};</code></p>";
                    }
                }
                else
                {
                    echo "<p class='help'>If this is the first time you have used SiT! you may need to create the database, ";
                    echo "if you have the necessary MySQL permissions you can <a href='setup.php?action=createdatabase'>create the database automatically</a>.<br />";
                    echo "Alternatively you can create it manually by executing the SQL statement <br /><code>{$sql};</code></p";
                }
                echo "<p>After creating the database run <a href='setup.php'>setup</a> again to create the database schema</p>";
                echo setup_configure();
            }
            else
            {
                require('functions.inc.php');
                // Generate a random admin password to use for new schema installations
                $adminpw = generate_password(10);

                // Load the empty schema
                require('setup-schema.php');

                // Connected to database and db selected
                echo "<p>Connected to database - ok</p>";
                // Check to see if we're already installed
                $sql = "SHOW TABLES LIKE 'users'";
                $result = mysql_query($sql);
                if (mysql_error())
                {
                    echo "<p class='error'>Could not find a users table, an error occurred ".mysql_error()."</p>";
                    exit;
                }
                if (mysql_num_rows($result) < 1)
                {
                    $_SESSION['adminpw'] = $adminpw;
                    echo "<h2>Creating new database schema...</h2>";
                    // No users table or empty users table, proceed to install
                    echo setup_exec_sql($schema);
                    // Set the system version number
                    $sql = "INSERT INTO system ( id, version) VALUES (0, $application_version)";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error($sql.mysql_error(),E_USER_ERROR);
                    $installed_version = $application_version;
                    echo "<h2>Database schema created</h2>";
                    echo "<p>If no errors were reported above you should now check the installation by running <a href='setup.php'>setup</a> again.</p>";
                }
                else
                {
                    // users table exists and has at least one record, must be already installed
                    // Do upgrade

                    // Have a look what version is installed
                    // First look to see if the system table exists
                    $exists = mysql_query("SELECT 1 FROM system LIMIT 0");
                    if (!$exists)
                    {
                        echo "<p class='error'>Could not find a 'system' table which probably means you have a version prior to v3.21</p>";
                        $installed_version = 3.00;
                    }
                    else
                    {
                        $sql = "SELECT version FROM system WHERE id = 0";
                        $result = mysql_query($sql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                        list($installed_version) = mysql_fetch_row($result);
                    }
                    if (empty($installed_version)) die ("<p class='error'>Fatal setup error - Could not determine version of installed software.  Try wiping your installation and installing from clean. (sorry)</p>");
                    echo "<h2>Installed OK</h2>";

                    if ($_REQUEST['action']=='upgrade')
                    {
                        // Upgrade schema
                        for($v=(($installed_version*100)+1);$v<=($application_version*100);$v++)
                        {
                            if (!empty($upgrade_schema[$v]))
                            {
                                echo "<p>Updating schema to v".number_format(($v/100),2)."</p>";
                                //echo  $upgrade_schema[$v];
                                echo setup_exec_sql($upgrade_schema[$v]);
                            }
                        }

                        // Other special tasks
                        if ($installed_version < 3.21)
                        {
                            echo "<p>Upgrading incidents data from version prior to 3.21...</p>";
                            // Fill the new servicelevel field in the incidents table using information from the maintenance contract
                            echo "<p>Upgrading incidents table to store service level...</p>";
                            $sql = "SELECT *,incidents.id AS incidentid FROM incidents, maintenance, servicelevels WHERE incidents.maintenanceid=maintenance.id AND ";
                            $sql .= "maintenance.servicelevelid = servicelevels.id ";
                            $result = mysql_query($sql);
                            while ($row = mysql_fetch_object($result))
                            {
                                $sql = "UPDATE incidents SET servicelevel='{$row->tag}' WHERE id='{$row->incidentid}' AND servicelevel IS NULL LIMIT 1";
                                mysql_query($sql);
                                if (mysql_error())
                                {
                                    trigger_error(mysql_error(),E_USER_WARNING);
                                    echo "<p><strong>FAILED:</strong> $sql</p>";
                                    $upgradeok = FALSE;
                                }
                                else echo "<p><strong>OK:</strong> $sql</p>";
                            }
                            echo "<p>".mysql_num_rows($result)." incidents upgraded</p>";
                        }
                        elseif ($installed_version == $application_version)
                        {
                            echo "<p>Everything is up to date</p>";
                        }
                        else
                        {
                            $upgradeok = TRUE;
                            echo "<p>See the <code>doc/UPGRADE</code> file for further upgrade instructions and help.<br />";
                        }
                        if ($upgradeok)
                        {
                            // Update the system version number
                            $sql = "REPLACE INTO system ( id, version) VALUES (0, $application_version)";
                            mysql_query($sql);
                            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                            $installed_version = $application_version;
                            echo "<h2>Upgrade complete</h2>";
                            echo "<p>Upgraded to v{$application_version}</p>";
                        }
                        else echo "<p class='error'>Upgrade failed.  Maybe you could try a fresh installation?</p>";
                    }
                    else
                    {
                        echo "<p>Your database schema is v".number_format($installed_version,2);
                        if ($installed_version < $application_version) echo ", after making a backup you should <a href='setup.php?action=upgrade'>upgrade</a> your schema to v{$application_version}";
                        echo "</p>";
                    }
                    // Check installation
                    echo "<h2>Checking installation...</h2>";
                    if ($CONFIG['attachment_fspath']=='') echo "<p class='error'>Attachment path must not be empty, please set the \$CONFIG['attachment_fspath'] configuration variable</p>";
                    elseif (file_exists($CONFIG['attachment_fspath'])==FALSE) echo "<p class='error'>The attachment path that you have configured ({$CONFIG['attachment_fspath']}) does not exist, please create this directory or alter the \$CONFIG['attachment_fspath'] configuration variable to point to a directory that does exist.</p>";
                    elseif (is_writable($CONFIG['attachment_fspath'])==FALSE)
                    {
                        echo "<p class='error'>Attachment path '{$CONFIG['attachment_fspath']}' not writable<br />";
                        echo "Permissions:  <code>{$CONFIG['attachment_fspath']} ".file_permissions_info(fileperms($CONFIG['attachment_fspath']));
                        echo " (".substr(sprintf('%o', fileperms($CONFIG['attachment_fspath'])), -4).")</code><br />";
                        echo "To fix this run the following command at the console (or set other appropriate permissions to allow write access)<br /><br />";
                        echo "<code>chmod -R 777 {$CONFIG['attachment_fspath']}</code>";
                        echo "</p>";
                    }
                    elseif(!isset($_REQUEST)) echo "<p class='error'>SiT! requires PHP 4.1.0 or later</p>";
                    elseif(@ini_get('register_globals')==1) echo "<p class='error'>SiT! strongly recommends that you change your php.ini setting <code>register_globals</code> to OFF.</p>";
                    else
                    {
                        if (!empty($_SESSION['adminpw']))
                        {
                            echo "<p>SiT! is initially configured with just one user, <var><strong>admin</strong></var> with an automatically generated password of <var><strong>{$_SESSION['adminpw']}</strong></var>, ";
                            echo "you should make a note of this password and change it as soon as you have logged in.</p>";
                        }
                        else
                        {
                            echo "<p class='error'>An error occurred during installation and we forgot the random admin password that was generated, this will prevent you logging in. Sorry. ";
                            echo "Check your PHP session settings, in particular make sure you have '<code>sesion.auto.start = 0</code>' in your php.ini.</p>";
                        }
                        $_SESSION['adminpw']='';
                        echo "<p>SiT! v".number_format($installed_version,2)." is installed and ready to <a href='index.php'>run</a>.</p>";
                    }
                }
            }
        }
}
echo "<hr />";
echo "<p><a href='http://sourceforge.net/projects/sitracker'>{$CONFIG['application_name']}</a> Setup</p>";
echo "<p></p>";

echo "\n</body>\n</html>";
?>