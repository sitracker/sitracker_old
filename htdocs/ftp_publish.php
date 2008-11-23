<?php
// ftp_publish.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// TODO concert HTML to PHP
// FIXME i18n

@include ('set_include_path.inc.php');
$permission = 44; // Publish Files to FTP site

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// seed with microseconds since last "whole" second
mt_srand((double)microtime()*1000000);
$maxVal = 1000000;
$minVal = 1;
$randvala = (mt_rand() % ($maxVal-$minVal)) + $minVal;
// seed with current time
mt_srand($now);
$maxVal = 1000000;
$minVal = 1;
$randvalb = (mt_rand() % ($maxVal-$minVal)) + $minVal;
$randomdir = dechex(crc32($randvala.$randvalb));

$filesize = filesize($source_file);

$pretty_file_size = readable_file_size($filesize);

// FIXME This temp variable name can't be right can it?  INL
if (!isset($temp_directory))
{
    // show form
    include ('htmlheader.inc.php');
    echo "<h2>{$strFTPPublish}</h2>";
    ?>
    <form name="publishform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input type="hidden" name="source_file" value="<?php echo $source_file; ?>" />
    <input type="hidden" name="destination_file" value="<?php echo $destination_file; ?>" />
    <input type="hidden" name="temp_directory" value="<?php echo $randomdir; ?>" />
    <input type="hidden" name="ftp_url" value="<?php echo 'ftp://'.$CONFIG['ftp_hostname'].$CONFIG['ftp_path'].$randomdir.'/'.$destination_file; ?>" />
    <table summary="ftp-publish" align="center" width="60%" class='vertical'>
    <tr><th>Publish:</th><td><img src="<?php echo getattachmenticon($filename); ?>" alt="<?php echo $filename; ?> (<?php echo $pretty_file_size; ?>)" border='0' />
    <strong><?php echo $destination_file; ?></strong> (<?php echo $pretty_file_size; ?>)</td></tr>
    <tr><th>To:</th><td><code><?php echo 'ftp://'.$CONFIG['ftp_hostname'].$CONFIG['ftp_path'].$randomdir.'/'.$destination_file; ?></code></td></tr>
    <tr><th>Title:</th><td><input type="text" name="shortdescription" maxlength="255" size="40" /></td></tr>
    <tr><th>Description:</th><td><textarea name="longdescription" cols="40" rows="3"></textarea></td></tr>
    <tr><th>File Version:</th><td><input type="text" name="fileversion" maxlength="50" size="10" /></td></tr>
    <tr><th>Expire:</th><td>
    <input type="radio" name="expiry_none" value="time"> In <em>x</em> days, hours, minutes<br />&nbsp;&nbsp;&nbsp;
    <input maxlength=3 name="expiry_days" value="<?php echo $na_days ?>" onclick="window.document.publishform.expiry_none[0].checked = true;" size='3' /> Days&nbsp;
    <input maxlength=2 name="expiry_hours" value="<?php echo $na_hours ?>"onclick="window.document.publishform.expiry_none[0].checked = true;" size='3' /> Hours&nbsp;
    <input maxlength=2 name="expiry_minutes" value="<?php echo $na_minutes ?>"onclick="window.document.publishform.expiry_none[0].checked = true;" size='3' /> Minutes<br />
    <input type="radio" name="expiry_none" value="date">On specified Date<br />&nbsp;&nbsp;&nbsp;
    <?php
    // Print Listboxes for a date selection    
    echo "<select name='day' onclick=\"window.document.publishform.expiry_none[1].checked = true;\">";
    
    for ($t_day = 1; $t_day <= 31; $t_day++)
    {
        echo "<option value=\"$t_day\" ";
        if ($t_day == date("j"))
        {
            echo "selected='selected'";
        }
        echo ">$t_day</option>\n";
    }
    
    echo "</select><select name='month' onclick=\"window.document.publishform.expiry_none[1].checked = true;\">";
    
    for ($t_month = 1; $t_month <= 12; $t_month++)
    {
        echo "<option value=\"$t_month\"";
        if ($t_month == date("n"))
        {
            echo " selected='selected'";
        }
        echo ">". date ("F", mktime(0,0,0,$t_month,1,2000)) ."</option>\n";
    }
    
    echo "</select><select name='year' onclick=\"window.document.publishform.expiry_none[1].checked = true;\">";
    
    for ($t_year=(date("Y")-1); $t_year <= (date("Y")+5); $t_year++)
    {
        echo "<option value=\"$t_year\" ";
        if ($t_year == date("Y"))
        {
            echo "selected='selected'";
        }
        echo ">$t_year\n";
    }
    
    echo "</select>";
    echo "</td></tr>";
    echo "</table>";
    echo "<p align='center'><input type='submit' value='publish' /></p>";
    echo "</form>";

    include ('htmlfooter.inc.php');
}
else
{
    // publish file
    include ('htmlheader.inc.php');
    echo "<h2>{$strFTPPublish}</h2>";
    // set up basic connection
    $conn_id = create_ftp_connection();

    $destination_filepath = $CONFIG['ftp_path'] . $temp_directory . '/' . $destination_file;

    // make the temporary directory
    $mk = @ftp_mkdir($conn_id, $CONFIG['ftp_path'] . $temp_directory);
    if (!mk) trigger_error("FTP Failed creating directory: {$temp_directory}", E_USER_WARNING);

    // check the source file exists
    if (!file_exists($source_file)) trigger_error("Source file cannot be found: {$source_file}", E_USER_WARNING);

    // set passive mode
    if (!ftp_pasv($conn_id, TRUE)) trigger_error("Problem setting passive FTP mode", E_USER_WARNING);

    // upload the file
    $upload = ftp_put($conn_id, "$destination_filepath", "$source_file", FTP_BINARY);

    // check upload status
    if (!$upload)
    {
        echo "FTP upload has failed!<br />";
    }
    else
    {
        echo "Uploaded $source_file to {$CONFIG['ftp_hostname']} as $destination_file<br />";
        echo "<code>$ftp_url</code>";

        journal(CFG_LOGGING_NORMAL, 'FTP File Published', "File $destination_file_file was published to {$CONFIG['ftp_hostname']}", CFG_JOURNAL_OTHER, 0);

        switch ($expiry_none)
        {
            case 'none': $expirydate = 0; break;
            case 'time':
                if ($expiry_days < 1 && $expiry_hours < 1 && $expiry_minutes < 1) $expirydate = 0;
                else
                {
                    // uses calculate_time_of_next_action() because the function suits our purpose
                    $expirydate = calculate_time_of_next_action($expiry_days, $expiry_hours, $expiry_minutes);
                }
            break;

            case 'date':
                // $now + ($days * 86400) + ($hours * 3600) + ($minutes * 60);
                $unixdate = mktime(9,0,0,$month,$day,$year);
                $expirydate = $unixdate;
                if ($expirydate < 0) $expirydate = 0;
            break;

            default:
                $expirydate = 0;
            break;
        }

        // store file details in database
        $sql = "INSERT INTO `{$dbFiles}` (filename, size, userid, shortdescription, longdescription, path, date, expiry, fileversion) ";
        $sql .= "VALUES ('$destination_file', '$filesize', '".$sit[2]."', '$shortdescription', '$longdescription', '".$temp_directory.'/'."', '$now', '$expirydate' ,'$fileversion')";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    }
    // close the FTP stream
    ftp_close($conn_id);
    include ('htmlfooter.inc.php');
}
?>
