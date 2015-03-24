<?php

require_once("config.inc.php");

if (isset($_POST["CONFIG"])) {
	// adds user data to the challenge data field in the mobileconfig template
	
	$userData = $_POST['name'] . " <" . $_POST['email'] . ">";
	$base64 = base64_encode($userData);

	$config = file_get_contents("udid.mobileconfig");
	$config = str_replace("CHALLENGE_DATA", $base64, $config);
	
	header("Content-Type: application/x-apple-aspen-config");
	header("Content-Disposition: attachment; filename=udid.mobileconfig");
	
	print $config;
	
	exit();
}

include_once("PlistParser.inc.php");
include_once("UrbanAirship.inc.php");

$START = "<?xml";
$END = "</plist>";
	
$contents = file_get_contents('php://input');

$startIndex = strpos($contents, $START);
$endIndex = strpos($contents, $END);

$plistString = substr($contents, $startIndex, $endIndex - $startIndex + strlen($END));

$parser = new plistParser();
$plist = $parser->parseString($plistString);

$userData = $plist["CHALLENGE"];

$emailStart = strpos($userData, "<");
$emailEnd = strpos($userData, ">");

$name = substr($userData, 0, $emailStart - 1);
$email = substr($userData, $emailStart + 1, $emailEnd - $emailStart - 1);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = sprintf(
	"INSERT INTO udids (udid, name, email, product, created) VALUES ('%s', '%s', '%s', '%s', NOW())",
	$plist["UDID"],
	$name,
	$email,
	$plist['PRODUCT']
);

$conn->query($query);	

if (UA_ENABLED) {
	// push notification for client apps
	$ua = new UrbanAirship(UA_KEY, UA_SECRET);
	$ua->push("You received a new UDID.");
}

header("HTTP/1.1 301 Moved Permanently"); 
header("Location: http://joris.kluivers.nl/udid/success.html");