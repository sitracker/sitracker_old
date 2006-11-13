<?php
// send_email.inc.php - Send email tab (new style)
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Paul Heaney <paulheaney[at]users.sourceforge.net>
//          Ivan Lucas <ivanlucas[at]users.sourceforge.net>

        ?>
        <script type='text/javascript'>
        function confirm_send_mail()
        {
            return window.confirm('Are you sure you want to send this email?');
        }
        </script>
        <?php
        // External vars
        $emailtype = cleanvar($_REQUEST['emailtype']);
        $newincidentstatus = cleanvar($_REQUEST['newincidentstatus']);
        $timetonextaction_none = cleanvar($_REQUEST['timetonextaction_none']);
        $timetonextaction_days = cleanvar($_REQUEST['timetonextaction_days']);
        $timetonextaction_hours = cleanvar($_REQUEST['timetonextaction_hours']);
        $timetonextaction_minutes = cleanvar($_REQUEST['timetonextaction_minutes']);
        $day = cleanvar($_REQUEST['day']);
        $month = cleanvar($_REQUEST['month']);
        $year = cleanvar($_REQUEST['year']);
        $target = cleanvar($_REQUEST['target']);

        if ($emailtype == 0)
        {
            echo "<p class='error'>You must select an email type</p>\n";
        }
        else
        {
            // encoding is multipart/form-data again as it no longer works without (why was this disabled?) - TPG 13/08/2002
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $id ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm_send_mail()" >
            <table align='center' class='vertical' width='95%'>
            <tr><th width='30%'>From:</th><td><input maxlength='100' name="fromfield" size='40' value="<?php echo emailtype_replace_specials(emailtype_from($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>Reply To:</th><td><input maxlength='100' name="replytofield" size='40' value="<?php echo emailtype_replace_specials(emailtype_replyto($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>CC:</th><td><input maxlength='100' name="ccfield" size='40' value="<?php echo emailtype_replace_specials(emailtype_cc($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>BCC:</th><td><input maxlength='100' name="bccfield" size='40' value="<?php echo emailtype_replace_specials(emailtype_bcc($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>To:</th><td><input maxlength='100' name="tofield" size='40' value="<?php echo emailtype_replace_specials(emailtype_to($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>Subject:</th><td><input maxlength='255' name="subjectfield" size='40' value="<?php echo emailtype_replace_specials(emailtype_subject($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>Attachment
            <?php
            // calculate filesize
            $j = 0;
            $ext =
            array("Bytes","KBytes","MBytes","GBytes","TBytes");
            $file_size = $CONFIG['upload_max_filesize'];
            while ($file_size >= pow(1024,$j)) ++$j;
            $file_size = round($file_size / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];
            echo "(&lt; $file_size)";
            ?>
            :</th><td>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $CONFIG['upload_max_filesize'] ?>" />
            <input type="file" name="attachment" size="40" maxfilesize="<?php echo $CONFIG['upload_max_filesize'] ?>" />
            </td></tr>
            <tr><th>Message:</th><td>
            <textarea name="bodytext" rows="20" cols="65"><?php
            // Attempt to restore email body from session in case there was an error submitting previously
            if (!empty($_SESSION['temp-emailbody'])) echo $_SESSION['temp-emailbody'];
            else echo emailtype_replace_specials(emailtype_body($emailtype), $id, $sit[2])
            ?></textarea>
            </td></tr>
            <?php
            if ($CONFIG['enable_spellchecker']==TRUE) echo "<tr><th>&nbsp;</th><td><input type='checkbox' name='spellcheck' value='yes' /> Check Spelling before sending</td></tr>\n";
            ?>
            </table>
            <p align='center'>
            <input name="newincidentstatus" type="hidden" value="<?php echo $newincidentstatus; ?>" />
            <input name="timetonextaction_none" type="hidden" value="<?php echo $timetonextaction_none; ?>" />
            <input name="timetonextaction_days" type="hidden" value="<?php echo $timetonextaction_days; ?>" />
            <input name="timetonextaction_hours" type="hidden" value="<?php echo $timetonextaction_hours; ?>" />
            <input name="timetonextaction_minutes" type="hidden" value="<?php echo $timetonextaction_minutes; ?>" />
            <input name="day" type="hidden" value="<?php echo $day; ?>" />
            <input name="month" type="hidden" value="<?php echo $month; ?>" />
            <input name="year" type="hidden" value="<?php echo $year; ?>" />
            <input name="target" type="hidden" value="<?php echo $target; ?>" />
            <input type="hidden" name="step" value="3" />
            <input type="hidden" name="emailtype" value="<?php echo $emailtype; ?>" />
            <input type="hidden" name="action" value="send-email" />
            <input name="submit2" type="submit" value="Send Email" />
            </p>
            </form>
            <?php
        }


?>