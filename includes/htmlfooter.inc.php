<?php
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05
if ($CONFIG['debug'] == TRUE)
{
  $exec_time_end = getmicrotime();
  $exec_time = $exec_time_end - $exec_time_start;
  echo "<p>CPU Time: ".number_format($exec_time,3)." seconds</p>";
}
echo "<div id='statusbar'><a href='about.php'><img src='{$CONFIG['application_webpath']}images/sitting_man_logo16x16.png' width='16' height='16' border='0' alt='About {$CONFIG['application_shortname']}' /></a> ";
echo "<strong><a href='http://sourceforge.net/projects/sitracker/'>Support Incident Tracker</a> v{$application_version} </strong> running on ";
echo strip_tags($_SERVER["SERVER_SOFTWARE"]);
echo " at ".date('H:i',$now);
if ($CONFIG['bugtracker_url']!='') echo ", Report bugs in <a href='{$CONFIG['bugtracker_url']}' class='barlink'>{$CONFIG['bugtracker_name']}</a>";
echo "</div>\n";
?>
</body>
</html>