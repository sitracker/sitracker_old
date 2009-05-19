<?php

require_once ('../lib/nusoap/nusoap.php');

$client = new nusoap_client('http://localhost/sit/soap.php?wsdl', true);
$err = $client->getError();
if ($err)
{
  echo "<h2>ERROR</h2><pre>{$err}</pre>";
  exit;
}

$result = $client->call('add', array(1, 3));
echo "A\n";
print_r($result);
$result = $client->call('sit_login', array('admin', '111'));
echo "\nB\n";
$err = $client->getError();
if ($err)
{
  echo "<h2>ERROR</h2><pre>{$err}</pre>";
  exit;
}
$sessionid = $result['sessionid'];
print_r($result);

$result = $client->call('list_incidents', array($sessionid));
echo "\nB\n";
$err = $client->getError();
if ($err)
{
  echo "<h2>ERROR</h2><pre>{$err}</pre>";
  exit;
}

echo "<pre>";
print_r($result);
echo "</pre>";


?>
