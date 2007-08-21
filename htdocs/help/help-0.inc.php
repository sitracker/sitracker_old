<ul>
    <li><a href='#about'>About Support Incident Tracker</a></li>
    <li>Support
    <ul>
        <li><a href='#incidents'>Incidents</a></li>
        <li><a href='#addincident'>Adding an Incident</a></li>
        <li><a href='#incidentqueues'>Incident Queues</a></li>
        <li><a href='#watchincidents'>Watching Incidents</a></li>
        <li><a href='#servicelevels'>Service Levels</a></li>
        <li><a href='#closeincident'>Closing an Incident</a></li>
    </ul>
    </li>
    <li><a href='#customers'>Customers</a></li>
    <li><a href='#contracts'>Contracts</a></li>
    <li><a href='#softwareproducts'>Skills &amp; Products</a></li>
    <li>Control Panel
    <ul>
        <li><a href='#adduser'>Adding Users</a></li>
        <li><a href='#disableuser'>Removing Users</a></li>
    </ul>
    <li><a href='#morehelp'>More Help</a></li>
</ul>


<h3><a name='about'>About Support Incident Tracker</a></h3>
<p>Support Incident Tracker (or SiT!) is a web based application for tracking technical support calls/emails.
Manage contacts, sites, technical support contracts and support incidents in one place. Send and receive emails
directly from SiT!, attach files and record every communication in the incident log. SiT is aware of Service Level
Agreements and incidents are flagged if they stray outside of them.</p>


<h3><a name='incidents'>Incidents</a></h3>
<p>As is clear from the name Support <em>Incident</em> tracker, incidents are an important part of SiT!, 'Incident'
is the name we use for what may also be referred to as a 'support call'. 'service request' or 'helpdesk ticket'.</p>
<p>Incidents are usually referred to by their reference number, what we call the 'Incident Number'.  Incidents
have a title, an associated product, contract and contact and possibly other information as well.</p>
<p>After an incident is added it is always 'owned' by a SiT! user although the user can reassign this ownership while the
incident is open.</p>
<p>Each incident has a current status which may be one of the following:</p>
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

<p>Closed incidents have an additional closing status:</p>
<ul>
<li>Sent Information</li>
<li>Solved Problem</li>
<li>Reported Bug</li>
<li>Action Taken</li>
<li>Duplicate</li>
<li>No Longer Relevant</li>
<li>Unsupported</li>
<li>Support Expired</li>
<li>Unsolved</li>
<li>Escalated</li>
</ul>

<p>Each incident has an 'updates log' which shows everything that has happened during the lifetime of the incident,
a complete record of all contact with the customer, with colleagues and with external engineers.</p>
<p>Incidents can also have files attached.</p>


<h3><a name='addincident'>Adding an Incident</a></h3>
<p>Before you can add an incident, the customer must already have a contract that entitles them to support.</p>
<p>Adding an incident is a four step process that goes like this:</p>
<ol>
<li>Enter all or part of the contact name in the box and click on the <strong>Find Contact</strong> button</li>
<li>From the list of contacts or people, click the the appropriate <strong>Add Incident</strong> link</li>
<li>Enter full details of the incident, give the incident a title and enter a problem description etc.</li>
<li>Assign the incident to an engineer who is to work on it</li>
</ol>


<h3><a name='incidentqueues'>Incident Queues</a></h3>
<p>SiT! works in a manner that is perhaps different to other support tracking/helpdesk applications that you may
have used in several respects.</p>
<ul>
<li>It's possible for any engineer to view and even work on incidents that are in other engineers queues</li>
<li>Incidents are assigned to engineers as soon as they are logged, rather than waiting in a central pending queue
or inbox for an engineer to decide to work on them.</li>
<li>Incidents can be passed easily from engineer to engineer by re-assigning</li>
<li>Engineers can keep an eye on incidents that their colleagues are working on, if they have skills in those areas</li>
</ul>

<p>We believe this to be a better, more collaborative, way of working.  It helps prevent difficult or unpopular issues
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
<p>This special queue has four sections, 'Held Emails', 'Spam Emails', 'New Incidents' and 'Pending Re-Assignments'.
The four sections show items that cannot be handled automatically by SiT for one reason or another.  Incoming email is
held in this queue if they arrive without the incident number correctly formatted on the subject line or if the incident
number quoted in the subject relates to an incident that has been closed.  Email is determined to be spam if certain
text is found in the subject line (this can set by your anti-spam software for example SpamAssassin) and are always held
for you to review.  Next is a section with new incidents created directly by the customer that need to be assigned to
engineers.  The final section is a list of incidents that could not be reassigned automatically after a user went away,
normally when a user marks himself away incidents are automatically assigned to substitute engineers, but in the case where
a substitute engineer was unavailable the incident is displayed in this queue.</p>


<h3><a name='watchincidents'>Watching Incidents</a></h3>
<p>As well as viewing your own queue which shows just your own incidents you can view all incidents no matter who owns them
by selecting <strong>Support</strong> | <strong>Watch Incidents</strong> from the menu.  You can also view the various
combined queues here by selecting, 'Action Needed', 'Waiting' or 'All Open' from the pulldown menu.</p>

<h3><a name='servicelevels'>Service Levels</a></h3>
<p>Each incident created is allocated a service level according to the service level set in the <a href='#contracts'>contract</a>.
SiT comes with just one service level 'standard' defined by default, but you can add more or customize existing levels
to suit your requirements via <strong>SiT!</strong> | <strong>Control Panel</strong> | <strong>Service Levels</strong>.</p>
<p>The service level targets define an amount of time allowed for the incident to reach a certain stage of progression,
ensuring your team meet these targets helps you to provide a better service.  Targets have different times for each
incident priority so you can aim to respond to high priority incidents faster.</p>
<p>The service level targets are:</p>
<ul>
<li>Initial Response</li>
<li>Problem Determination</li>
<li>Action Plan</li>
<li>Resolution</li>
<li>Review<sup>*</sup></li>
</ul>

<p>You can meet a service level target by making an update to an incident or by sending an email and marking the
service level you want to meet.<p>

<p><sup>*</sup> An incident review is a special type of service level target, the review period is not affected
by the working week it is simply based on the amount of time an incident has been open, to review an incident
make an update and mark the update type as 'Review'.  Review periods are a useful way of preventing incidents from
dragging on and on.</p>

<h3><a name='closeincident'>Closing an Incident</a></h3>
<p>When you close an incident you are given the choice to mark the incident for closure or to close it immediately.
If you choose to mark it for closure it will be closed after seven days. (this period can be configured by setting the
<var>closure_delay</var> in the config file.)</p>
<p>You must select a closing status at this point, this can be used later to quickly see whether the query was
answered etc.  The options available are:</p>

<ul>
<li>Sent Information</li>
<li>Solved Problem</li>
<li>Reported Bug</li>
<li>Action Taken</li>
<li>Duplicate</li>
<li>No Longer Relevant</li>
<li>Unsupported</li>
<li>Support Expired</li>
<li>Unsolved</li>
</ul>

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


<h3><a name='softwareproducts'>Skills &amp; Products</a></h3>
<p>Each item that is supported by SiT! is called a skill and each should have it's own skill record.  For example if
you plan to support software called 'Debian GNU/Linux' you should add a skill record by selecting <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Skills</strong> | <strong>Add Skill</strong>.</p>
<p>Don't add different versions of the same item as seperate records unless you do treat them differently in the
way you support them (e.g. you plan to phase out support for an older version).  You'll be prompted for a version
number when <a href='#addincident'>adding an incident</a>.</p>

<p>In order to simplify supporting a large number of different software applications SiT! has the concept of 'Products',
products can be thought of as groups of software.  For example, you may support three software applications,
'Debian GNU/Linux', 'SUSE Linux' and 'Mandriva Linux' but want to offer support for all three as one product called
'Linux'.</p>
<p>To add a product go to <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Skills</strong> | <strong>Add Product</strong>, select a vendor
enter a product name a description then click <strong>Add Product</strong>.
Then to link skills to that, go to <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Skills</strong> | <strong>Link Products/Skills</strong>.</p>
<p>To list existing products and the software associated with them go to <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Skills</strong> | <strong>List Products</strong>.</p>
<p>Products can also be grouped by vendor, you can add vendors by going to <strong>Customers</strong> |
<strong>Maintenance</strong> | <strong>Products &amp; Skills</strong> | <strong>Add Vendor</strong>.</p>

<h3><a name='adduser'>Adding Users</a></h3>
<p>If you have appropriate permission you can create additional SiT users. (The 'admin' user always has this
permission).  Go to <strong>Control Panel</strong> | <strong>Users</strong> | <strong>Add User</strong>.</p>
<p>Usernames must be unique and cannot contain spaces.  Each user must have an email address.</p>
<p>There are three available roles</p>
<ul>
<li>Administrator</li>
<li>Manager</li>
<li>User</li>
</ul>
<p>Each role has different permissions, and you can alter the permissions assigned to roles should you require.</p>

<p>After adding a user you are presented with the user permissions page where you can grant additional permissions
should the user have needs beyond that provided by the nearest role.</p>

<p class='info'>Note that permissions are additive and you cannot take permissions away from a user that are granted
by the role except by changing the users role or altering the permissions on the role itself.</p>

<h3><a name='disableuser'>Removing Users</a></h3>
<p>To maintain data integrity there isn't a way to delete users from SiT!, however user accounts can be disabled which
will prevent further logins and stop the users name from showing up in selection boxes etc.</p>

<p>To disable a users account go to <strong>Control Panel</strong> | <strong>Users</strong> and select <strong>Edit</strong>
next to the user you would like to disable.  Then select 'DISABLED ACCOUNT' from the Status drop down box and click save.</p>

<p>Re-enabling a user account is simple, just edit the users profile and set their status to 'In Office'.</p>



<h3><a name='morehelp'>More Help</a></h3>

<p>Please note that this documentation below is currently incomplete and sometimes innacurate, we hope to have this fixed for a
release in the near future, if you think you can help us with this, your help would be most welcome.</p>

<ul>
    <li><a href="help.php?id=43">Edit Global Signature</a></li>
    <li><a href="help.php?id=4">Edit your profile</a></li>
    <li><a href="help.php?id=39">Add Contract</a></li>
    <li><a href="help.php?id=27">View Your Calendar</a></li>
    <li><a href="help.php?id=35">Set your status</a></li>
    <li><a href="help.php?id=54">View Knowledge Base Articles</a></li>
    <li><a href="help.php?id=55">Delete a contact</a></li>
</ul>
