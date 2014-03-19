<?php

$str = file_get_contents(dirname(__FILE__) . '/config.json');
$jsonArr = array();
$jsonArr = json_decode($str, TRUE);

# Include the Dropbox SDK libraries
require_once dirname(__FILE__) . "/dropbox-sdk/Dropbox/autoload.php";
use \Dropbox as dbx;
  
$dbxClient = new dbx\Client($jsonArr['dropbox']['token'] , "PHP-Example/1.0");


foreach ($jsonArr['mysql']['databases'] as $dbname => $data) {
  $filename = ($data['filename'] !== ''  ? $data['filename'] : $dbname);
  $timestamp = (!empty($data['timestamp']) ? time() : FALSE);
  
  $cmd = 'mysqldump -u ' . $jsonArr['mysql']['username'] .
    ' -p' . $jsonArr['mysql']['password'] .
    ' ' . $dbname .
    ' | gzip > /tmp/' . $dbname . '.gz';
  
  
  exec($cmd);

  echo 'Backup of ' . $dbname . " taken. \n";
  
  // Save to dropbox.
  
  $f = fopen('/tmp/' . $dbname . '.gz', "rb");
  if ($timestamp) {
    $result = $dbxClient->uploadFile('/' . $filename . '/' . $timestamp . '.gz', dbx\WriteMode::add(), $f);
  }
  else {
    $result = $dbxClient->uploadFile('/' . $filename . '.gz', dbx\WriteMode::force(), $f); 
  }
  fclose($f);
  print_r($result);
  
  $message = send_notification($jsonArr, 'Backup finished - Dropbox', 'Backup of database "' . $dbname . '" finished.');
  
  $status = unlink('/tmp/' . $dbname . '.gz');
  if ($status) {
    echo "Temp file deleted. \n";
  }
  else {
    echo "Temp file not deleted.\n";
  }

}



function send_notification($config, $title, $message, $priority = '1') {
  if (empty($config['pushover']['token']) 
    || empty($config['pushover']['user'])) {
    return FALSE;
  }
  curl_setopt_array(
    $ch = curl_init(),
    array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => "https://api.pushover.net/1/messages.json",
      CURLOPT_POSTFIELDS => array(
        "token"     => $config['pushover']['token'],
        "user"      => $config['pushover']['user'],
        "title"     => $title,
        "message"   => $message,
        "priority"  => $priority,
      )
    )
  );
  curl_exec($ch);
  curl_close($ch);
}