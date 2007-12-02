<?php
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05
echo "</div>"; // mainframe
echo "<div id='statusbar'>";
if ($_SESSION['auth']==TRUE) echo "<a href='about.php'>";
echo "<img src='{$CONFIG['application_webpath']}images/sitting_man_logo16x16.png' width='16' height='16' border='0' alt='About {$CONFIG['application_shortname']}' />";
if ($_SESSION['auth']==TRUE) echo "</a>";
echo " <strong><a href='http://sitracker.sourceforge.net/'>Support Incident Tracker</a>";
if ($_SESSION['auth']==TRUE) echo " {$application_version_string}";
echo "</strong>";
if ($_SESSION['auth']==TRUE)
{
    echo " running ";
    if ($CONFIG['demo']) echo "in DEMO mode ";
    echo "on ".strip_tags($_SERVER["SERVER_SOFTWARE"]);
    echo " at ".date('H:i',$now);
}
echo "</div>\n";
if ($CONFIG['debug'] == TRUE)
{
    echo "<div id='tail' style='background:#AAA; color: #fff; padding: 10px 10px; overflow: hidden;'><strong>DEBUG</strong><br />";
    $exec_time_end = getmicrotime();
    $exec_time = $exec_time_end - $exec_time_start;
    echo "<p>CPU Time: ".number_format($exec_time,3)." seconds</p>";
    if (isset($dbg)) echo "<hr /><pre>".print_r($dbg,true)."</pre>";
    echo "</div>";
}
echo "</body>\n</html>\n";
?>