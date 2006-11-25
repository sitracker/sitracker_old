<?php
// Display site
echo "<table align='center' class='vertical'>";
$sql="SELECT * FROM sites WHERE id='$id' ";
$siteresult = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
while ($siterow=mysql_fetch_array($siteresult))
{
    echo "<tr><th>Site:</th><td><h3>".stripslashes($siterow['name'])."</h3></td></tr>";
    echo "<tr><th>Department:</th><td>".stripslashes($siterow['department'])."</td></tr>";
    echo "<tr><th>Address1:</th><td>".stripslashes($siterow['address1'])."</td></tr>";
    echo "<tr><th>Address2:</th><td>".stripslashes($siterow['address2'])."</td></tr>";
    echo "<tr><th>City:</th><td>".stripslashes($siterow['city'])."</td></tr>";
    echo "<tr><th>County:</th><td>".stripslashes($siterow['county'])."</td></tr>";
    echo "<tr><th>Country:</th><td>".stripslashes($siterow['country'])."</td></tr>";
    echo "<tr><th>Postcode:</th><td>".stripslashes($siterow['postcode'])."</td></tr>";
    echo "<tr><th>Telephone:</th><td>".stripslashes($siterow['telephone'])."</td></tr>";
    echo "<tr><th>Fax:</th><td>".stripslashes($siterow['fax'])."</td></tr>";
    echo "<tr><th>Email:</th><td><a href=\"mailto:".stripslashes($siterow['email'])."\">".stripslashes($siterow['email'])."</a></td></tr>";
    echo "<tr><th>Website:</th><td><a href=\"".stripslashes($siterow['websiteurl'])."\">".stripslashes($siterow['websiteurl'])."</a></td></tr>";
    echo "<tr><th>Notes:</th><td>".stripslashes($siterow['notes'])."</td></tr>";
    echo "<tr><td colspan='2'>&nbsp;</td></tr>";
    echo "<tr><th>Support Incidents:</th><td>See <a href=\"contact_support.php?id=".$siterow['id']."&amp;mode=site\">here</a></td></tr>";
    echo "<tr><th>Site Incident Pool:</th><td>{$siterow['freesupport']} Incidents remaining</td></tr>";
    echo "<tr><th>Salesperson:</th><td>";
    if ($siterow['owner']>=1) echo user_realname($siterow['owner'],TRUE);
    else echo 'Not Set';
    echo "</td></tr>\n";
}
mysql_free_result($siteresult);

plugin_do('site_details');

echo "</table>\n";
echo "<p align='center'><a href='edit_site.php?action=edit&amp;site={$id}'>Edit</a> | ";
echo "<a href='delete_site.php?id={$id}'>Delete</a>";
echo "</p>";
?>