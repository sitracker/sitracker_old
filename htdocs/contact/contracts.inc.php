<?php
        echo "<h4>Related Contracts:</h4>";
        $sql  = "SELECT supportcontacts.maintenanceid AS maintenanceid, maintenance.product, products.name AS productname, ";
        $sql .= "maintenance.expirydate, maintenance.term ";
        $sql .= "FROM supportcontacts, maintenance, products ";
        $sql .= "WHERE supportcontacts.maintenanceid=maintenance.id AND maintenance.product=products.id AND supportcontacts.contactid='$id' ";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if (mysql_num_rows($result)>0)
        {
            echo "<table align='center' class='vertical'>";
            echo "<tr>";
            echo "<th>ID</th><th>Product</th><th>Expires</th>";
            echo "</tr>\n";

            $supportcount=1;
            $shade='shade2';
            while ($supportedrow=mysql_fetch_array($result))
            {
                if ($supportedrow['term']=='yes') $shade='expired';
                if ($supportedrow['expirydate']<$now) $shade='expired';

                echo "<tr><td class='$shade'><a href=\"maintenance_details.php?id=".$supportedrow['maintenanceid']."\">Contract: ".$supportedrow['maintenanceid']."</a></td>";
                echo "<td class='$shade'>".$supportedrow['productname']."</td>";
                echo "<td class='$shade'>".date("jS M Y", $supportedrow['expirydate']);
                if ($supportedrow['term']=='yes') echo " Terminated";
                echo "</td>";
                echo "</tr>\n";
                $supportcount++;
                $shade='shade2';
            }
            echo "</table>\n";

        }
        else
        {
            echo "<p align='center'>This contact is not supported via any contracts</p>\n";
        }
        echo "<p align='center'><a href='add_maintenance_support_contact.php?contactid=$id&amp;context=contact'>Associate this contact with an existing contract</a></p>\n";
    

?>