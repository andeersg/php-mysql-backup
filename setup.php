<?php

# Include the Dropbox SDK libraries
require_once dirname(__FILE__) . "/dropbox-sdk/Dropbox/autoload.php";
use \Dropbox as dbx;

$appInfo = dbx\AppInfo::loadFromJsonFile("dropbox.json");
$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");

$authorizeUrl = $webAuth->start();

echo "1. Go to: " . $authorizeUrl . "\n";
echo "2. Click \"Allow\" (you might have to log in first).\n";
echo "3. Copy the authorization code.\n";
$authCode = \trim(\readline("Enter the authorization code here: "));

list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
print "Access Token: " . $accessToken . "\n";

$str = file_get_contents(dirname(__FILE__) . '/config.json');
$jsonArr = json_decode($str, TRUE);

$jsonArr['dropbox']['token'] = $accessToken;

echo "Want notifications when a backup is created? \n";
echo "Enter your pushover user-key:\n";
$userkey = \trim(\readline("Pushover User-key: "));

echo "Enter your pushover app-token:\n";
$token = \trim(\readline("Pushover App-token: "));

$jsonArr['pushover']['user'] = $userkey;
$jsonArr['pushover']['token'] = $token;

file_put_contents(dirname(__FILE__) . 'config.json', json_encode($jsonArr));

$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");
$accountInfo = $dbxClient->getAccountInfo();

print_r($accountInfo);