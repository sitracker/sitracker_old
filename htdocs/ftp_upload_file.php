<?php
// ftp_upload_file.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
@include ('set_include_path.inc.php');
$permission=44; // ftp publishing
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External variables
$file = cleanvar($_REQUEST['file']);
$action = cleanvar($_REQUEST['action']);

if (empty($action))
{
    include ('htmlheader.inc.php');
    ?>
    <h2>Upload Public File</h2>
    <p align='center'>IMPORTANT: Files published here are <strong>public</strong> and available to all ftp users.</p>
    <form name="publishform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
    <table class='vertical'>
    <tr><th>File <small>(&lt;<?php echo $CONFIG['upload_max_filesize']; ?> bytes)</small>:</th>
    <td class='shade2'><input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $CONFIG['upload_max_filesize']; ?>" />
    <input type="file" name="file" size="40" /></td></tr>

    <tr><th>Title:</th><td><input type="text" name="shortdescription" maxlength="255" size="40" /></td></tr>
    <tr><th>Description:</th><td><textarea name="longdescription" cols="40" rows="3"></textarea></td></tr>
    <tr><th>File Version:</th><td><input type="text" name="fileversion" maxlength="50" size="10" /></td></tr>
    <tr><th>Expire:</th><td>
    <input type="radio" name="expiry_none" value="time" /> In <em>x</em> days, hours, minutes<br />&nbsp;&nbsp;&nbsp;
    <input maxlength="3" name="expiry_days" value="<?php echo $na_days ?>" onclick="window.document.publishform.expiry_none[0].checked = true;" size="3" /> Days&nbsp;
    <input maxlength="2" name="expiry_hours" value="<?php echo $na_hours ?>" onclick="window.document.publishform.expiry_none[0].checked = true;" size="3" /> Hours&nbsp;
    <input maxlength="2" name="expiry_minutes" value="<?php echo $na_minutes ?>" onclick="window.document.publishform.expiry_none[0].checked = true;" size="3" /> Minutes<br />
    <input type="radio" name="expiry_none" value="date" />On specified Date<br />&nbsp;&nbsp;&nbsp;
    <?php
    // Print Listboxes for a date selection
    ?><select name='day' onclick="window.document.publishform.expiry_none[1].checked = true;"><?php
    for ($t_day=1;$t_day<=31;$t_day++)
    {
        echo "<option value=\"$t_day\" ";
        if ($t_day==date("j"))
        {
        echo "selected='selected'";
        }
        echo ">$t_day</option>\n";
    }
    ?></select><select name='month' onclick="window.document.publishform.expiry_none[1].checked = true;"><?php
    for ($t_month=1;$t_month<=12;$t_month++)
    {
        echo "<option value=\"$t_month\"";
        if ($t_month==date("n"))
        {
            echo " selected='selected'";
        }
        echo ">". date ("F", mktime(0,0,0,$t_month,1,2000)) ."</option>\n";
    }
    ?></select><select name='year' onclick="window.document.publishform.expiry_none[1].checked = true;"><?php
    for ($t_year=(date("Y")-1);$t_year<=(date("Y")+5);$t_year++)
    {
        echo "<option value=\"$t_year\" ";
        if ($t_year==date("Y"))
        {
            echo "selected='selected'";
        }
        echo ">$t_year</option>\n";
    }
    ?></select>
    </td>
    </tr>
    </table>
    <p align='center'><input type="submit" value="Publish" /><input type="hidden" name="action" value="publish" /></p>
    <p align='center'><a href='ftp_list_files.php'>Back to list</a></p>
    </form>
    <?php
    include ('htmlfooter.inc.php');
}
else
{
//     echo "<pre>".print_r($_REQUEST,true)."</pre>";
//     echo "<pre>".print_r($_FILES,true)."</pre>";

    // TODO v3.2x ext variables
    $file_name = $_FILES['file']['name'];

    // receive the uploaded file to a temp directory on the local server
    if ($_FILES['file']['error']==0)
    {
        $filepath = $CONFIG['attachment_fspath'].$file_name;
        $mv=move_uploaded_file($_FILES['file']['tmp_name'], $filepath);
        if (!mv) throw_error('!Error: Problem moving uploaded file from temp directory:',$filepath);

        if (!file_exists($filepath)) throw_error("Error the temporary upload file ($file) was not found at: ",$filepath);

        // Check file size
        $filesize=filesize($filepath);
        if ($filesize > $CONFIG['upload_max_filesize'])
        {
            throw_error('User Error: Attachment too large or file ('.$file.') upload error - size:',filesize($filepath));
            // throwing an error isn't the nicest thing to do for the user but there seems to be no way of
            // checking file sizes at the client end before the attachment is uploaded. - INL
        }
        if ($filesize==FALSE) throw_error('Error handling uploaded file:',$file);


        // set up basic connection
        $conn_id = ftp_connect($CONFIG['ftp_hostname']);

        // login with username and password
        $login_result = ftp_login($conn_id, $CONFIG['ftp_username'], $CONFIG['ftp_password']);

        // check connection
        if ((!$conn_id) || (!$login_result))
        {
            throw_error("FTP Connection failed, connecting to {$CONFIG['ftp_hostname']} for user {$CONFIG['ftp_user_name']}",'');
        }
        $destination_filepath=$CONFIG['ftp_path'] . $file_name;

        // check the source file exists
        if (!file_exists($filepath)) throw_error('Source file cannot be found', $filepath);
        ## throw_error('dest', $destination_filepath);


        // set passive mode if required
        if (!ftp_pasv($conn_id, $CONFIG['ftp_pasv'])) throw_error("Error: Problem setting passive ftp mode", '');

        // upload the file
        $upload = ftp_put($conn_id, "$destination_filepath", "$filepath", FTP_BINARY);

        // check upload status
        if (!$upload)
        {
            trigger_error("FTP upload has failed!",E_USER_ERROR);
        }
        else
        {
            // store file details in database
            // important: path must be blank for public files (all go in same dir)
            $sql = "INSERT INTO files (filename, size, userid, shortdescription, longdescription, path, date, expiry, fileversion) ";
            $sql .= "VALUES ('$file_name', '$filesize', '".$sit[2]."', '$shortdescription', '$longdescription', '', '$now', '$expirydate' ,'$fileversion')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            journal(CFG_LOGGING_NORMAL, 'FTP File Uploaded', "FTP File $file_name Uploaded", CFG_JOURNAL_OTHER, 0);

            html_redirect('ftp_upload_file.php');
            echo "<code>$ftp_url</code>";
        }

        // close the FTP stream
        ftp_quit($conn_id);
    }
    else echo "<p class='error'>An error has occurred.</p>";

}
?>