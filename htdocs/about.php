<?php
// about.php - Credit, Copyright and Licence page
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional! 28Oct05

$permission=41; // View Status

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');
?>
<table summary="by Ivan Lucas" align="center">
<tr><td class='shade1' colspan="2">About <?php echo $CONFIG['application_shortname']; ?>...</td></tr>
<tr><td class='shade2' colspan="2">
<?php
echo "<h2>{$CONFIG['application_name']}</h2>";
echo "<p align='center'>Version: {$application_version} {$application_revision}</p><br />";
?>
</td>
</tr>
<tr><td class='shade1' colspan="2">Credits:</td></tr>
<tr><td class='shade2' width='20%' align='right'><strong>Code:</strong></td><td class='shade2'>Ivan Lucas, Tom Gerrard, Andy Smith</td></tr>
<tr><td class='shade2' width='20%' align='right'><strong>Design/Testing:</strong></td><td class='shade2'>Paul Lees, Peter Atkins, Tom Gerrard, Ivan Lucas, Micky Campbell and the Salford Software Staff</td></tr>
<tr><td class='shade2' width='20%' align='right'><strong>CSS Styles:</strong></td><td class='shade2'>Ivan Lucas, Tom Gerrard, Paul Harrison</td></tr>
<tr><td class='shade2' width='20%' align='right'><strong>Version 2:</strong></td><td class='shade2'>Martin Kilkoyne</td></tr>
<tr><td class='shade2' width='20%' align='right'><strong>Version 1:</strong></td><td class='shade2'>Kevin Shrimpton</td></tr>
<tr><td class='shade1' colspan="2">&nbsp;</td></tr>

<tr><td class='shade1' colspan="2">Copyright Information:</td></tr>
<tr><td class='shade2' colspan='2'>
<p align='center'><?php echo "{$CONFIG['application_name']}"; ?> is Copyright &copy; 2000-<?php echo date('Y'); ?> <a href='http://www.salfordsoftware.co.uk/'>Salford Software Ltd.</a><br />
Licensed under the GNU General Public License.<br />
Salford Software, Lancastrian Office Centre, Talbot Road, Old Trafford, Manchester. M32 0FP.</p>

<p align='center'>Incorporating:</p>

<p align='center'>Help Tip 1.12 by <a href='http://webfx.eae.net/contact.html#erik'>Erik Arvidsson</a><br />
Copyright &copy; 1999 - 2002 Erik Arvidsson. Licensed under the GPL.</p>

<p align='center'>KDEClassic Icon theme<br />
Completely free for commercial and non-commercial use.</p>

<p align='center'>whatever:hover (csshover.htc) 1.41 by <a hfref='http://www.xs4all.nl/~peterned/'>Peter Nederlof</a><br />
&copy; 2005 - Peter Nederlof.  Licensed under the LGPL.</p>

</td></tr>
<tr><td class='shade1' colspan="2">Licence:</td></tr>
<tr><td class='shade2' colspan='2'>
<textarea cols="100%" rows="10" readonly="readonly" style="background: transparent;">
<?php
$fp = fopen('../doc/LICENSE', "r");
$contents = htmlentities(fread($fp, filesize('../doc/LICENSE')));
fclose($fp);
echo $contents;
?>
</textarea>
</td></tr>
<tr><td class='shade1' colspan="2">Changelog:</td></tr>
<tr><td colspan="2" class="shade2">
<textarea cols="100%" rows="10" readonly="readonly" style="background: transparent;">
<?php
$fp = fopen('../doc/Changelog', "r");
$contents = htmlentities(fread($fp, filesize('../doc/Changelog')));
fclose($fp);
echo $contents;
?>
</textarea>
</td></tr>
<?php
echo "<tr><td class='shade1' colspan='2'>Plugins:</td></tr>";
echo "<tr><td class='shade2' colspan='2'>";
if (count($CONFIG['plugins']) >= 1)
{
    foreach($CONFIG['plugins'] AS $plugin)
    {
        echo "<p><strong>$plugin</strong>";
        if ($PLUGININFO[$plugin]['version'] != '') echo " version ".number_format($PLUGININFO[$plugin]['version'], 2)."<br />";
        else echo "<br />";

        if ($PLUGININFO[$plugin]['description'] != '') echo "{$PLUGININFO[$plugin]['description']}<br />";
        if ($PLUGININFO[$plugin]['author'] != '') echo "Author: {$PLUGININFO[$plugin]['author']}<br />";
        if ($PLUGININFO[$plugin]['legal'] != '') echo "{$PLUGININFO[$plugin]['legal']}<br />";
        if ($PLUGININFO[$plugin]['sitminversion'] > $application_version) echo "<strong class='error'>This plugin was designed for {$CONFIG['application_name']} version {$PLUGININFO[$plugin]['sitminversion']}</strong><br />";
        echo "</p>";
    }
}
else echo "<p>None</p>";
echo "</td></tr>";
echo "</table>\n";
include('htmlfooter.inc.php');
?>