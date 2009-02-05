<?php
// inventory.php - Browse inventory items
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 0;

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');
// This page requires authentication
require ($lib_path.'auth.inc.php');

include ('./inc/htmlheader.inc.php');

if (is_numeric($_GET['site']) AND empty($_GET['action']) AND empty($_GET['edit']))
{
    //View site inventory
    $siteid = $_GET['site'];

    if (!empty($_REQUEST['filter']))
    {
        $filter = cleanvar($_REQUEST['filter']);
    }

    echo "<h2>".icon('site', 32)." ".site_name($siteid)."</h2>";
    echo "<p align='center'>";
    echo "<a href='{$_SERVER['PHP_SELF']}?site={$siteid}&action=new'>";
    echo icon('add', 16)." {$strAddNew}</a> | ";
    echo "<a href='inventory.php'>".icon('site', 16)." {$strBackToSites}</a></p>";
    $sql = "SELECT *, i.name AS name , i.id AS id, ";
    $sql .= "i.notes AS notes, ";
    $sql .= "i.active AS active ";
    $sql .= "FROM `{$dbInventory}` AS i, `{$dbSites}` AS s ";
    $sql .= "WHERE siteid='{$siteid}' ";
    $sql .= "AND siteid=s.id ";
    if (!empty($filter))
    {
        $sql .= "AND type='{$filter}' ";
    }
    $sql .= "ORDER BY i.active DESC, ";
    $sql .= "i.modified DESC";
    //$sql .= "GROUP BY type DESC ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

    echo "<form action='{$_SERVER['PHP_SELF']}?site={$siteid}' method='post'>";
    echo "<p align='center'>".icon('filter', 16)." {$strFilter}: ";
    echo "<select name='filter' onchange='form.submit();'>";
    echo "<option value=''></option>";
    foreach ($CONFIG['inventory_types'] as $code => $name)
    {
        echo "<option value='{$code}'";
        if ($filter == $code)
        {
            echo " selected='selected' ";
        }
        echo ">{$name}</option>";
    }
    echo "</select> <a href='{$_SERVER['PHP_SELF']}?site={$siteid}'>";
    echo "{$strClearFilter}</a></p>";
    echo "</form>";

    if (mysql_num_rows($result) > 0)
    {
        while ($row = mysql_fetch_object($result))
        {
            echo "<div id='container' style='width: 40%'>";
            echo "<h3>{$row->name}";

            if ($row->active != 1)
            {
                echo " (inactive)";
            }
            echo " (<small><a href='?edit={$row->id}&site={$row->siteid}'>";
            echo "{$strEdit}</a></small>)</h3>";
            echo "<p><strong>{$strType}:</strong> ";
            echo "{$CONFIG['inventory_types'][$row->type]}</p>";
            if (!empty($row->identifier))
            {
                echo "<p><strong>{$strID}:</strong> {$row->identifier}</p>";
            }

            echo "<p><strong>{$strAddress}:</strong> $row->address</p>";
            if (!empty($row->contactid))
            {
                echo "<p><strong>{$strOwner}:</strong> ";
                echo "<a href='contact_details.php?id={$row->contactid}'>";
                echo contact_realname($row->contactid)."</a></p>";
            }
            echo "<p><strong>{$strUsername}:</strong> ";
            if (($row->privacy == 'adminonly' AND !user_permission($sit[2], 22)) OR
                ($row->privacy == 'private' AND $row->createdby != $sit[2]))
            {
                echo "<strong>{$strWithheld}</strong>";
            }
            else
            {
                echo $row->username;
            }
            echo "</p>";
            echo "<p><strong>{$strPassword}:</strong> ";
            if (($row->privacy == 'adminonly' AND !user_permission($sit[2], 22)) OR
                ($row->privacy == 'private' AND $row->createdby != $sit[2]))
            {
                echo "<strong>{$strWithheld}</strong>";
            }
            else
            {
                echo $row->password;
            }
            echo "</p>";
            if (!empty($row->notes))
            {
                echo "<p><strong>{$strNotes}: </strong> {$row->notes}</p>";
            }
            echo "<strong>{$strCreatedBy}:</strong> ".user_realname($row->createdby);
            echo " {$row->created}, <strong>{$strLastModifiedBy}:</strong> ";
            echo user_realname($row->modifiedby)." {$row->modified}</p>";
            echo "</div>";
        }
        echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?site={$siteid}&action=new'>";
        echo "{$strAddNew}</a></p>";
    }
    else
    {
        echo "<p class='info'>{$strNoRecords}</p>";
    }
    include ('./inc/htmlfooter.inc.php');
}
elseif(is_numeric($_GET['edit']) OR $_GET['action'] == 'new')
{
    //Edit inventry object
    $edit = cleanvar($_GET['edit']);
    $action = cleanvar($_GET['action']);
    
    if (!empty($_GET['newsite']))
    {
        $newsite = TRUE;
    }
    else
    {
        $newsite = FALSE;
        $siteid = intval($_GET['site']);
    }

    if (isset($_POST['submit']))
    {
        $post = cleanvar($_POST);
        
        if ($post['active'] == 'on')
        {
            $post['active'] = 1;
        }
        elseif (isset($post['active']))
        {
            $post['active'] = 0;
        }

        if ($_GET['action'] == 'new')
        {
            //if we're adding to a site with nothing previous
            if (!empty($post['site']))
            {
                $siteid = $post['site'];
            }
            
            $sql = "INSERT INTO `{$dbInventory}`(address, username, password, type,";
            $sql .= " notes, created, createdby, modified, modifiedby, active,";
            $sql .= " name, siteid, privacy, identifier) VALUES('{$post['address']}', ";
            $sql .= "'{$post['username']}', '{$post['password']}', ";
            $sql .= "'{$post['type']}', ";
            $sql .= "'{$post['notes']}', NOW(), '{$sit[2]}', NOW(), ";
            $sql .= "'{$sit[2]}', '1', '{$post['name']}', '{$siteid}', ";
            $sql .= "'{$post['privacy']}', '{$post['identifier']}')";
        }
        else
        {
            $sql = "UPDATE `{$dbInventory}` ";
            $sql .= "SET address='{$post['address']}', ";
            if (isset($post['username']))
                $sql .= "username='{$post['username']}', ";
            
            if (isset($post['password']))
                $sql .= "password='{$post['password']}', ";
                
            $sql .= "type='{$post['type']}', ";
            $sql .= "notes='{$post['notes']}', modified=NOW(), ";
            $sql .= "modifiedby='{$sit[2]}', ";
            $sql .= "name='{$post['name']}', ";
            $sql .= "contactid='{$post['owner']}', identifier='{$post['identifier']}' ";
            
            if (isset($post['privacy']))
            {
                $sql .= ", privacy='{$post['privacy']}' ";
            }
            
            if (isset($post['active']))
            {
                $sql .= ", active='{$post['active']}' ";
            }

            $sql .= " WHERE id='{$edit}'";
        }

        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        else html_redirect($_SERVER['PHP_SELF']."?site={$siteid}");
    }
    else
    {
        //FIXME
        //$row = cleanvar($row);
        if ($action == 'new')
        {
            echo "<h2>".icon('add', 32)." {$strAdd}</h2>";
            $siteid = intval($_GET['site']);
        }
        else
        {
            $sql = "SELECT * FROM `{$dbInventory}` WHERE id='{$edit}'";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
            $row = mysql_fetch_object($result);
            echo "<h2>".icon('edit', 32)." {$strEdit}</h2>";
        }
        
        if ($action == 'new')
        {
            echo "<form action='{$_SERVER['PHP_SELF']}?action=new&site={$siteid}' method='post'>";
        }
        else
        {
            echo "<form action='{$_SERVER['PHP_SELF']}?edit={$edit}&site={$row->siteid}' method='post'>";
        }
        echo "<table class='vertical' align='center'>";
        echo "<tr><th>{$strName}</th>";
        echo "<td><input class='required' name='name' value='{$row->name}' />";
        echo "<span class='required'>{$strRequired}</span></td></tr>";
        echo "<tr><th>{$strType}</th>";
        echo "<td>".array_drop_down($CONFIG['inventory_types'], 'type', $row->type, '', TRUE)."</td></tr>";

        if ($newsite)
        {
            echo "<tr><th>{$strSite}</th><td>";
            echo site_drop_down('site', 0, TRUE);
	    echo " <span class='required'>{$strRequired}</td>";
            echo "<tr><th>{$strOwner}</th><td>";
            echo contact_site_drop_down('owner', '');
            echo "</td></tr>";
        }
        else
        {
            echo "<tr><th>{$strOwner}</th><td>";
            echo contact_site_drop_down('owner', $row->contactid, $siteid, NULL, FALSE);
            echo "</td></tr>";
        }
        echo "<tr><th>{$strID} ".help_link('InventoryID')."</th>";
        echo "<td><input name='identifier' value='{$row->identifier}' /></td></tr>";
        echo "<tr><th>{$strAddress}</th>";
        echo "<td><input name='address' value='{$row->address}' /></td></tr>";
                  
        if (!is_numeric($edit) OR
            (($row->privacy == 'adminonly' AND user_permission($sit[2], 22)) OR
            ($row->privacy == 'private' AND ($row->createdby == $sit[2]))))
        {
            echo "<tr><th>{$strUsername}</th>";
            echo "<td><input name='username' value='{$row->username}' /></td></tr>";
            echo "<tr><th>{$strPassword}</th>";
            echo "<td><input name='password' value='{$row->password}' /></td></tr>";
        }
        
        echo "<tr><th>{$strNotes}</th>";
        echo "<td><textarea name='notes'>$row->notes</textarea></td></tr>";

        if (($row->privacy == 'adminonly' AND user_permission($sit[2], 22)) OR
            ($row->privacy == 'private' AND $row->createdby == $sit[2]) OR
            $row->privacy == 'none' OR $_GET['action'] == 'new')
        {
            echo "<tr><th>{$strPrivacy} ".help_link('InventoryPrivacy')."</th>";
            echo "<td><input type='radio' name='privacy' value='private' ";
            if ($row->privacy == 'private')
            {
                echo " checked='checked' ";
                $selected = TRUE;
            }
            echo "/>{$strPrivate}<br />";

            echo "<input type='radio' name='privacy' value='adminonly'";
            if ($row->privacy == 'adminonly')
            {
                echo " checked='checked' ";
                $selected = TRUE;
            }
            echo "/>";
            echo "{$strAdminOnly}<br />";
            
            echo "<input type='radio' name='privacy' value='none'";
            if (!$selected)
            {
                echo " checked='checked' ";
            }
            echo "/>";
            echo "{$strNone}<br />";
        }
        
        echo "</td></tr>";
        
        if ($action != 'new')
        {
            echo "<tr><th>{$strActive}</th>";
            echo "<td><input type='checkbox' name='active' ";
            if ($row->active == '1')
            {
               echo "checked = 'checked' ";
            }
            echo "/>";
        }
        echo "</table>";
        echo "<p align='center'>";
        if ($action == 'new')
        {
            echo "<input name='submit' type='submit' value='{$strAdd}' /></p>";
        }
        else
        {
            echo "<input name='submit' type='submit' value='{$strUpdate}' /></p>";
        }
        echo "</form>";
        echo "<br /><p align='center'>";
        
        if ($newsite == 'true')
        {
            echo icon('site', 16);
            echo " <a href='{$_SERVER['PHP_SELF']}'>{$strBackToSites}</a>";
        }
        else
        {
            echo "<a href='{$_SERVER['PHP_SELF']}?site={$siteid}'>{$strBackToList}</a>";
        }
        include ('./inc/htmlfooter.inc.php');
    }
}
else
{
    echo "<h2>".icon('inventory', 32)." {$strInventory}</h2>";
    echo "<p align='center'>{$strInventoryDesc}</p>";

    $sql = "SELECT COUNT(*) AS count, s.* FROM `{$dbInventory}` AS i, `{$dbSites}` AS s ";
    $sql .= "WHERE siteid=s.id ";
    $sql .= "GROUP BY siteid ";
    $result = mysql_query($sql);

    if (mysql_num_rows($result) > 0)
    {
        echo "<table class='vertical' align='center'>";
        echo "<th>{$strSite}</th><th>{$strCount}</th>";
        while ($row = mysql_fetch_object($result))
        {
            echo "<tr><td>".icon('site', 16);
            echo " <a href='?site={$row->id}'>{$row->name}</a></td>";
            echo "<td>{$row->count}</td></tr>";
        }
        echo "</table>";
    }
    else
    {
        echo "<p align='center'>{$strNoRecords}</p>";
    }
    
    echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?action=new&newsite=true'>";
    echo "{$strSiteNotListed}</a></p>";
    include ('./inc/htmlfooter.inc.php');
}


?>
