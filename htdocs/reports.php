<?php
// reports.php - Reports list/menu
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=37; // Run Reports

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');
?>
<h2><?php echo $CONFIG['application_shortname']; ?> Reports</h2>
<table align='center'>
<thead>
<tr><th>REPORTS</th></tr>
</thead>
<tbody>
<td><a href='reports/qbe.php'>Query by Example</a><br />
Design your own report
</td></tr>


<tr><td><a href='reports/marketing.php'>Marketing Mailshot</a><br />
Select a list of contacts by product
</td></tr>

<tr><td><a href='reports/cust_export.php'>Customer Export</a><br />
Select a list of contacts by site
</td></tr>


<tr><td><a href="reports/yearly_customer_export.php">Incidents logged by sites (past year)</a><br />
List the numbers and titles of incidents logged by each site in the past year.
</td></tr>

<tr><td><a href="reports/yearly_engineer_export.php">Incidents logged to engineers (past year)</a><br />
List the numbers and titles of incidents logged to each engineer in the past year.
</td></tr>

<tr><td><a href='reports/site_products.php'>Site Products</a><br />
A list of products that each site has been or are currently is under contract for.
</td></tr>

<tr><td><a href='reports/supportbycontract.php'>Site Contracts</a><br />
A list of sites and their contracts and named contacts
</td></tr>

<tr><td><a href="reports/feedback.php">Customer Feedback</a><br />
Reports showing results from customer feedback surveys.
</td></tr>

<tr><td><a href="reports/site_incidents.php">Count Site Incidents</a><br />
CSV File showing how many incidents each site has logged, including sites that have logged none.
</td></tr>

<tr><td><a href="reports/allnames.php">Contacts List</a><br />
A full list of all contacts, showing number of incidents logged
</td></tr>

<tr><td><a href="holiday_calendar.php">Holiday Planner</a><br />
Calendar showing which staff are in and out of the office.
</td></tr>

<tr><td><a href="reports/incident_graph.php">Incidents Logged (Open/Closed)</a><br />
Bar Chart showing incident logging trends <em>(May take a few mins to calculate)</em>
</td></tr>

<tr><td><a href="reports/average_incident_duration.php">Average Incident Duration</a><br />
Report showing the average length of time taken to close incidents <em>(May take a few mins to calculate)</em>
</td></tr>

<tr><td><a href='reports/recent_incidents_table.php'>Recent Incidents Table</a><br />
A 'league' style table showing sites that have logged incidents recently.
</td></tr>

<tr><td><a href='reports/incidents_by_software.php'>Incidents by Skill</a><br />
Table and chart showing the number of incidents logged under different skills.
</td></tr>


<tr><td><a href='reports/incidents_by_vendor.php'>Incidents by Vendor</a><br />
Table showing the number of incidents logged for different Vendors.
</td></tr>

<?php

plugin_do('reports_menu');
echo "</table>\n";

include('htmlfooter.inc.php');
?>