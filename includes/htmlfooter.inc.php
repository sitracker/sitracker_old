<?php
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05
if ($CONFIG['debug'] == TRUE)
{
  $exec_time_end = getmicrotime();
  $exec_time = $exec_time_end - $exec_time_start;
  echo "<p>CPU Time: ".number_format($exec_time,3)." seconds</p>";
}
?>
</body>
</html>