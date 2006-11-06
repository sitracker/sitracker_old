<?php



// Display Contacts
echo "<h3>Contacts</h3>";

// List Contacts
$sql="SELECT * FROM contacts WHERE siteid='$id' ORDER BY surname, forenames";
$contactresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$countcontacts = mysql_num_rows($contactresult);
if ($countcontacts > 0)
{
    echo "<p align='center'>{$countcontacts} Contact(s)</p>";
    echo "<table align='center'>";
    echo "<tr><th>Name</th><th>Job Title</th><th>Department</th><th>Phone</th><th>Email</th><th>Address</th><th>Data Protection</th><th>Notes</th></tr>";
    $shade='shade1';
    while ($contactrow=mysql_fetch_array($contactresult))
    {
        echo "<tr class='$shade'>";
        echo "<td><a href=\"contact_details.php?id=".$contactrow['id']."\">".$contactrow['forenames'].' '.$contactrow['surname']."</a></td>";
        echo "<td>{$contactrow['jobtitle']}</td>";
        echo "<td>{$contactrow['department']}</td>";
        echo "<td>{$contactrow['phone']}</td>";
        echo "<td>{$contactrow['email']}</td>";
        echo "<td>";
        if (!empty($contactrow['address1'])) echo "{$contactrow['address1']}";
        echo "</td>";
        echo "<td>";
        if ($contactrow['dataprotection_email']=='yes') { echo "<strong>No Email</strong>, "; }
        if ($contactrow['dataprotection_phone']=='yes') { echo "<strong>No Calls</strong>, "; }
        if ($contactrow['dataprotection_address']=='yes') { echo "<strong>No Post</strong>"; }
        echo "</td>";
        echo "<td>{$contactrow['notes']}</td>";
        echo "</tr>";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
    }
    echo "</table>\n";
}
else
{
    echo "<p align='center'>There are no contacts associated with this site</p>";
}
echo "<p align='center'><a href='add_contact.php?siteid={$id}'>Add Contact</a></p>";


?>