<ul>
    <li><a href='#about'>About Support Incident Tracker</a></li>
    <li>Support
    <ul>
        <li><a href='#incidents'>Incidents</a></li>
        <li><a href='#addincident'>Adding an Incident</a></li>
        <li><a href='#incidentqueues'>Incident Queues</a></li>
        <li><a href='#watchincidents'>Watching Incidents</a></li>
    </ul>
    </li>
    <li><a href='#customers'>Customers</a></li>
    <li><a href='#contracts'>Contracts</a></li>
    <li><a href='#softwareproducts'>Software &amp; Products</a></li>
    <li><a href='#morehelp'>More Help</a></li>
</ul>


<h3><a name='about'>About Support Incident Tracker</a></h3>
<p>Support Incident Tracker (or SiT!) is a web based application for tracking technical support calls/emails.
Manage contacts, sites, technical support contracts and support incidents in one place. Send and receive emails
directly from SiT!, attach files and record every communication in the incident log. SiT is aware of Service Level
Agreements and incidents are flagged if they stray outside of them.</p>


<h3><a name='incidents'>Incidents</a></h3>
<p>As is clear from the name Support <em>Incident</em> tracker, incidents are an important part of SiT!, 'Incident'
is the name we give to support call or helpdesk ticket.</p>
<p>Incidents are usually referred to by their reference number, what we call the 'Incident Number'.  Incidents
have a title, an associated product, contract and contact and possibly other information as well.</p>
<p>After an incident is added it is always 'owned' by a SiT! user although the user can reassign this ownership while the
incident is open.</p>
<p>Each incident has a current status which may be one of the following:
<ul>
<li>Active</li>
<li>Closed</li>
<li>Research Needed</li>
<li>Called And Left Message</li>
<li>Awaiting Colleague Response</li>
<li>Awaiting Support Response</li>
<li>Awaiting Closure</li>
<li>Awaiting Customer Action</li>
<li>Unsupported</li>
</ul>
</p>
<p>In addition closed incidents have an additional closing status:
<ul>
<li>Sent Information</li>
<li>Incidents
<li>Solved Problem</li>
<li>Reported Bug</li>
<li>Action Taken</li>
<li>Duplicate</li>
<li>No Longer Relevant</li>
<li>Unsupported</li>
<li>Support Expired</li>
<li>Unsolved</li>
</ul>
</p>
<p>Each incident has an 'updates log' which shows everything that has happened during the lifetime of the incident,
a complete record of all contact with the customer, with colleagues and with external engineers.</p>
<p>Incidents can also have files attached.</p>


<h3><a name='addincident'>Adding an Incident</a></h3>
<p>Before you can add an incident, the customer must already have a contract that entitles them to support.</p>
<p>Adding an incident is a four step process that goes like this:
<ol>
<li>Enter all or part of the contact name in the box and click on the <strong>Find Contact</strong> button</li>
<li>From the list of contacts or people, click the the appropriate <strong>Add Incident</strong> link</li>
<li>Enter full details of the incident, give the incident a title and enter a problem description etc.</li>
<li>Assign the incident to an engineer who is to work on it</li>
</ol>
</p>


<h3><a name='incidentqueues'>Incident Queues</a></h3>
<p>SiT! works in a manner that is perhaps different to other support tracking/helpdesk applications that you may
have used in several respects.
<ul>
<li>It's possible for any engineer to view and even work on incidents that are in other engineers queues</li>
<li>Incidents are assigned to engineers as soon as they are logged, rather than waiting in a central pending queue
or inbox for an engineer to decide to work on them.</li>
<li>Incidents can be passed easily from engineer to engineer by re-assigning</li>
<li>Engineers can keep an eye on incidents that their colleagues are working on, if they have skills in those areas</li>
</ul>
We believe this to be a better, more collaborative, way of working.  It helps prevent difficult or unpopular issues
from being ignored or left till last and it encourages team working.</p>
<p>There are a number of queues and they do work in different ways, lets look at them one at a time.</p>

<p>Select <strong>Support</strong> | <strong>View Incidents</strong> (or simply click <strong>Support</strong>)
from the menu to display your 'Action Needed' queue.  From here you can select your other queues, 'Waiting', 'All Open'
and 'All Closed'.</p>

<h4>'Action Needed' Queue</h4>
<p>Lists all incidents that are assigned to you and require your action in some way.  The list is sorted so that
incidents requiring action first are displayed at the top, but you can change how the list is sorted by clicking
on the column headings.</p>

<h4>'Waiting' Queue</h4>
<p>Lists incidents that are assigned to you and do not require immediate action but are waiting for response from
a third party (For example an incident that is waiting for the customer to send in log files)</p>

<h4>'All Open' Queue</h4>
<p>This queue lists all your current incidents regardless of their status.  This is the same as combining the
Action Needed and Waiting queues.</p>

<h4>'All Closed' Queue</h4>
<p>As is evident from the name this queue contains all the incidents that are assigned to you that are now closed.</p>

<p>Rows in the various queues are coloured according to their importance, the colours will vary depending on the theme
you are using, in the default theme incidents are normally coloured blue, coloured yellow when they approach a target
and then red as they require immediate attention.</p>

<p class='tip'>Hover your pointer over an incident title in the queue to see a brief extract of the latest update.</p>

<p>The next queue we are going to look at is the 'Holding Queue', this is slightly different to the other queues in that
it can contain incoming emails as well as incidents and it can be viewed by selecting <strong>Support</strong> |
<strong>Holding Queue</strong> from the menu.</p>

<h4>Holding Queue</h4>
<p>This special queue has three sections, 'Held Emails', 'Spam Emails' and 'Pending Re-Assignments'.  The three sections
show items that cannot be handled automatically by SiT for one reason or another.  Incoming email is held in this queue if they
arrive without the incident number correctly formatted on the subject line or if the incident number quoted in the subject
relates to an incident that has been closed.  Email is determined to be spam if certain text is found in the subject line
(this can set by your anti-spam software for example SpamAssassin) and are always held for you to review.  The third sections
is a list of incidents that could not be reassigned automatically after a user went away, normally when a user marks himself
away incidents are automatically assigned to backup engineers, but in the case where a backup engineer was unavailable the
incident is displayed in this queue.</p>


<h3><a name='watchincidents'>Watching Incidents</a></h3>
<p>As well as viewing your own queue which shows just your own incidents you can view all incidents no matter who owns them
by selecting <strong>Support</strong> | <strong>Watch Incidents</strong> from the menu.  You can also view the various
combined queues here by selecting, 'Action Needed', 'Waiting' or 'All Open' from the pulldown menu.</p>


<h3><a name='customers'>Customers</a></h3>
<p>People and organisations that you provide support to using SiT are referred to within SiT as 'customers'</p>

<h4>Sites &amp; Contacts</h4>
<p>Every organisation that you provide to support to is referred to as a 'site' and each site can have a number of
'contacts'.</p>
<p>View sites by selecting <strong>Customers</strong> | <strong>Sites</strong> to get a list and then click on the
site name to view details of that site, including contacts.</p>
<p>View contacts by selecting <strong>Customers</strong> | <strong>Contacts</strong></p>
<p>You can set any of the three data protection fields on the contact record to indicate that the person does not
want to be contacted by email, letter or phone.</p>
<p>We recommend that you never delete sites or contacts unless absolutely necessary, this is because each site or
contact may have a number of associated records, such as contracts or incidents that are related only to that site.
If you do decide to Delete, SiT! will prompt you to select another site or contact to receive the associated records.</p>


<h3><a name='contracts'>Contracts</a></h3>
<p>Before you can add an incident on behalf of a contact there must first be an agreement in place to provide such
support, these agreements are referred to within SiT as 'contracts'.</p>
<p>To add a new contract select <strong>Customers</strong> | <strong>Maintenance</strong> | <strong>New Contract</strong>
and fill in the details on the form.</p>
<p class='info'>Admin Contacts are not supported contacts, you must add supported contacts seperately.</p>
<p>Each contract holds information about the agreement such as the <a href='#softwareproducts'>product</a> supported, the number of incidents included
with the contract and the expiration date.</p>
<p>To be useful each contract must have at least one contact associated with it.  To add a contact simply follow the link
<strong>Add a support contact to this contract</strong> on the contract details page.  These supported contacts are the
only people who can log incidents.</p>


<h3><a name='softwareproducts'>Software &amp; Products</a></h3>
<p>Each item of software that is supported by SiT! should have it's own software record.  For example if you plan to
support software called 'Debian GNU/Linux' you should add a software record by selecting <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Software</strong> | <strong>Add Software</strong>.</p>
<p>Don't add different versions of the same software as seperate records unless you do treat them differently in the
way you support them (e.g. you plan to phase out support for an older version).  You'll be prompted for a version
number when <a href='#addincident'>adding an incident</a>.</p>

<p>In order to simplify supporting a large number of different software applications SiT! has the concept of 'Products',
products can be thought of as groups of software.  For example, you may support three software applications,
'Debian GNU/Linux', 'SUSE Linux' and 'Mandriva Linux' but want to offer support for all three as one product called
'Linux'.</p>
<p>To add a product go to <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Software</strong> | <strong>Add Product</strong>, select a vendor
enter a product name a description then click <strong>Add Product</strong>.
Then to link software to that, go to <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Software</strong> | <strong>Link Products/Software</strong>.</p>
<p>To list existing products and the software associated with them go to <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Software</strong> | <strong>List Products</strong>.</p>
<p>Products can also be grouped by vendor, you can add vendors by going to <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Software</strong> | <strong>Add Vendor</strong>.</p>

<h3><a name='morehelp'>More Help</a></h3>

<p>Please note that this help is currently incomplete and sometimes innacurate, we hope to have this fixed for a
release in the near future, if you think you can help us with this, your help would be most welcome.</p>

<ul>
  <li><strong>Incidents...</strong></li>
  <ul>
    <li><a href="help.php?id=18">Close Incidents</a></li>
  </ul>
</ul>

<ul>
  <li><strong>Users...</strong></li>
  <ul>
    <li><a href="help.php?id=20">Add Users</a></li>
    <li><a href="help.php?id=23">Edit User</a></li>
    <li><a href="help.php?id=9">Edit User Permissions</a></li>
    <li><a href="help.php?id=14">View Users</a></li>
  </ul>
</ul>

<ul>
  <li><strong>Email...</strong></li>
  <ul>
    <li><a href="help.php?id=16">Add Email Templates</a></li>
    <li><a href="help.php?id=17">Edit Email Templates</a></li>
    <li><a href="help.php?id=33">Send Emails</a></li>
    <li><a href="help.php?id=43">Edit Global Signature</a></li>
  </ul>
</ul>

<ul>
  <li><strong>Contracts...</strong></li>
  <ul>
    <li><a href="help.php?id=39">Add Contract</a></li>
    <li><a href="help.php?id=21">Edit Contracts</a></li>
    <li><a href="help.php?id=19">View Contracts</a></li>
  </ul>
</ul>

<ul>
  <li><strong>Manage your Account...</strong></li>
  <ul>
    <li><a href="help.php?id=4">Edit your profile</a></li>
    <li><a href="help.php?id=27">View Your Calendar</a></li>
    <li><a href="help.php?id=35">Set your status</a></li>
    <li><a href="help.php?id=50">Approve Holidays</a></li>
    <li><a href="help.php?id=58">Edit your Software Skills</a></li>
  </ul>
</ul>

<ul>
  <li><strong>Administrate...</strong></li>
  <ul>
    <li><a href="help.php?id=37">Run Reports</a></li>
    <li><a href="help.php?id=41">View Status</a></li>
    <li><a href="help.php?id=57">Disable User's Account</a></li>
    <li><a href="help.php?id=59">Manage User's Software</a></li>
  </ul>
</ul>

<ul>
  <li><strong>Other...</strong></li>
  <ul>
    <!--<li><a href="help.php?id=26">Get Help</a></li>-->
    <li><a href="help.php?id=44">Publish files to FTP site and remove them</a></li>
    <li><a href="help.php?id=53">Administrate Store</a></li>
    <li><a href="help.php?id=54">View Knowledge Base Articles</a></li>
    <li><a href="help.php?id=60">Perform Searches</a></li>
    </ul>
</ul>
