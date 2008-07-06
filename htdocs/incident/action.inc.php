<?php
/*
incident/action.inc.php - Performs incident tasks, included by ../incident.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

// FIXME this code isn't used

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

/**
    * @author Ivan Lucas
    * @returns string. HTML tabs, a div 'tabcontainer' containing an unnumbered list
*/
function draw_tabs_submit($tabsarray, $selected='')
{
    if ($selected=='') $selected=$tabsarray[0];
    $html .= "<div id='tabcontainer'>";
    $html .= "<ul id='tabnav'>";
    foreach ($tabsarray AS $tab)
    {
        $html .= "<li>";
        $html .= "<a href=\"javascript:gotab('$tab');\"";
        $tab=str_replace('_', ' ', $tab);
        if (strtolower($tab)==strtolower($selected)) $html .= " class='active'";
        $html .= ">$tab</a></li>\n";
    }
    $html .= "</ul>";
    $html .= "</div>\n";

    return ($html);
}


/* Define requirements for each state -
//
    bits 0-3 : SLA 'next state' IR, PD, AP, RES
                provided ONE matches in 'require' we are OK
    bit 4    : I am the incident owner
    bit 5    : Incident maintenance is a Netware contract  - FIXME
    bit 6    : External ID is set to Novell number - FIXME
    bit 7    : I have review permission
*/

// Requirements for each control -
//
//  bit 0    : Email headers ----------------------------------------------------------------------------------------------------------\
//  bit 1    : Make Email headers optional -------------------------------------------------------------------------------------------\|
//  bit 2    : Input box 1: Text Box ------------------------------------------------------------------------------------------------\||
//  bit 3    : Can alter priority --------------------------------------------------------------------------------------------------\|||
//  bit 4    : Telephone in/out/vm ------------------------------------------------------------------------------------------------\||||
//  bit 5    : Telephone in/out --------------------------------------------------------------------------------------------------\|||||
//  bit 6    : Telephone out ----------------------------------------------------------------------------------------------------\||||||
//  bit 7    : Visible ---------------------------------------------------------------------------------------------------------\|||||||
//  bit 8    : Visibiltiy optional --------------------------------------------------------------------------------------------\||||||||
//  bit 9    : Next action time ---    ---------------------------------------------------------------------------------------\|||||||||
//                                                                ----                                                       |||||||||||
$actions['Initial_Response']   = array('require' => bindec('00010001'), 'mask' => bindec('00010000'), 'controls' => bindec('000010010101'));
$actions['Define_Problem']     = array('require' => bindec('00010011'), 'mask' => bindec('00010000'), 'controls' => bindec('000010100101'));
$actions['Redefine_Problem']   = array('require' => bindec('00011100'), 'mask' => bindec('00010000'), 'controls' => bindec('000110100111'));
$actions['Action_Plan']        = array('require' => bindec('00011111'), 'mask' => bindec('00010000'), 'controls' => bindec('000010100101'));
$actions['Monitor']            = array('require' => bindec('00011100'), 'mask' => bindec('00010000'), 'controls' => bindec('001010100101'));
$actions['Resolve']            = array('require' => bindec('00011000'), 'mask' => bindec('00010000'), 'controls' => bindec('000010100111'));
$actions['Reassign']           = array('require' => bindec('00000000'), 'mask' => bindec('00001111'), 'controls' => bindec('000000000000'));
$actions['Other']              = array('require' => bindec('00011111'), 'mask' => bindec('00010000'), 'controls' => bindec('000010010100'));
$actions['Private_Note']       = array('require' => bindec('00001111'), 'mask' => bindec('00010000'), 'controls' => bindec('000000000100'));
$actions['Review']             = array('require' => bindec('10001111'), 'mask' => bindec('10000000'), 'controls' => bindec('000011000100'));
$actions['Reopen']             = array('require' => bindec('00000000'), 'mask' => bindec('00001111'), 'controls' => bindec('000010000000'));
$actions['Close']              = array('require' => bindec('00001111'), 'mask' => bindec('00000000'), 'controls' => bindec('000000000001'));
//$actions['Escalate_to_Novell'] = array('require' => bindec('00111110'), 'mask' => bindec('01110000'), 'controls' => bindec('00001000'));
//$actions['Update_Novell']      = array('require' => bindec('01111110'), 'mask' => bindec('01110000'), 'controls' => bindec('00001000'));


if (isset($_REQUEST['oldaction']))
{
    // reconcile any data that has been posted (mode change during edit)
    // We could probably just loop round any POSTed data ...?  Depends if we
    // end up displaying things we shouldn't set

    $bm = $actions[$_REQUEST['oldaction']]['controls'];

    if ($bm & 1)
    {
        $save_vars[] = 'fromfield';
        $save_vars[] = 'tofield';
        $save_vars[] = 'ccfield';
    }

    if ($bm & 4)
    {
        $save_vars[]='text2field';
    }

    foreach ($save_post as $save_var)
        $_SESSION[$incidentid.$save_var]=$_POST[$save_var];
}

if ($_REQUEST['action'] != 'doit')
{
    // User clicked the main submit button
    // All data should now be in the session vars...

    // Do whatever

    // Cleanup
    foreach ($_SESSION as $key => $value )
        if (strncmp($key,$incidentid,5) === 0) unset($_SESSION['key']);
}


if (!isset($_SESSION[$incidentid.'cs']))
{
    // Work out the current status as we don't already know

    $cs=0;

    if ($incident->status != 2)
    {
        switch ($target->type)
        {
            case 'initialresponse':
                $cs |= 1;
            break;
            case 'probdef':
                $cs |= 2;
            break;
            case 'actionplan':
                $cs |= 4;
            break;
            case 'solution':
                $cs |= 8;
            break;
        }
    }

    // FIXME move novell stuff to plugin

    if ($sit[2]==$incident->owner)
      $cs |= 16;

    $sql  = "SELECT p.vendorid ";
    $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbProducts}` AS p ";
    $sql .= "WHERE (m.id='{$incident->maintenanceid}' AND m.product = p.id) ";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $vendor = mysql_fetch_object($result) ;

    if ($vendor->vendorid == 2)
      $cs |=32;

    if (preg_match('/[0-9]{10,11}/', $incident->externalid))
      $cs |= 64;

    if (user_permission($sit[2],40))       // WRONG PERMISSION !
      $cs |= 128;

    $_SESSION[$incidentid.'cs'] = $cs;
}
else $cs=$_SESSION[$incidentid.'cs'];


// Now to build the array of what we can have

foreach ($actions as $type => $action)
{
    if (
      (
        ($cs & $action['mask']) == ($action['require'] & 240)
      )
      &&
      (
        ((($cs & $action['require']) & 15) != 0 )
        ||
        (($action['mask'] & 15) !=0))
      ) {
      $tabsarray[]=$type;
    }
}

echo "<form id='actiontabs' name='actiontabs' action ='{$_SERVER['PHP_SELF']}?id={$incidentid}&oldaction={$_REQUEST['action']}&tab={$_REQUEST['tab']}' method='post'>";
// Hidden field to store the action before we submit
echo "<input type='hidden' name='action' value='' />";

echo draw_tabs_submit($tabsarray, $selectedaction);


// And now draw the controls
if ($bm=$actions[$_REQUEST['action']])
{  // Some action is selected

    echo "<p>{$_REQUEST['action']} Form</p>"; // FIXME temporary
    if ($bm['controls'] & 1)
    {
        // style='padding: 10px; background: #F6FAFF; border: 1px solid #9C9D9C; margin-left: 5%; margin-right: 5%;'
        echo "<div class='detailentry' >\n";
        if ($bm['controls'] & 2)
        {
            echo "<p>Hide/Show</p>";
        }
        echo "<label>From:<br /><input maxlength='100' name='fromfield' size='50' value='{$_SESSION[$incidentid.'fromfield']}' /></label><br />\n";
        echo "<label>To:<br /><input maxlength='100' name='tofield' size='50' value='{$_SESSION[$incidentid.'tofield']}' /></label><br />\n";
        echo "<label>CC:<br /><input maxlength='100' name='ccfield' size='50' value='{$_SESSION[$incidentid.'ccfield']}' /></label><br />\n";
        echo "</div>\n";
    }

    if ($bm['controls'] & 16 OR $bm['controls'])
    {
        echo "<div class='detailentry'>\n";
        echo "<input type='radio' name='phonecall' value='IN'> <label for'phonecall'>Incoming call</label> ";
        echo "<input type='radio' name='phonecall' value='OUT'> <label for'phonecall'>Outgoing call</label> ";
        if ($bm['controls'] & 16)
        {
            echo "<input type='checkbox' name='voicemail' value='voicemail'> <label for'voicemail'>Left voicemail</label><br />";
        }
        echo "</div\n";
    }


    if ($bm['controls'] & 4)
    {
        echo "<div class='detailentry'>\n";
        echo "<label>Message:<br /><textarea name='text2field' rows='20' cols='65'>{$_SESSION[$incidentid.'text2field']}</textarea></label><br />\n";
        if ($bm['controls'] & 128)
        {
            echo "Visible<br />\n";
            if ($bm['controls'] & 256)
            {
                echo "visibility optional";
            }
        }
        echo "</div\n";
    }

    if ($bm['controls'] & 8)
    {
        echo "<div class='detailentry'>\n";
        echo "<label>Priority:<br />Set priority here</label><br />\n";
        echo "</div\n";

    }

    if ($bm['controls'] & 512)
    {
        echo "<div class='detailentry'>\n";
        echo "<label>Next action time:<br />Set here</label><br />\n";
        echo "</div\n";

    }

    echo "<p align='center'><input name='action' type='submit' value='Do it'></p>";
}
echo "</form>";
?>
