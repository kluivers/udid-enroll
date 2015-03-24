<?php

require_once("config.inc.php");

if ($_GET["key"] != API_SECRET) {
	header("401 Not Authorized", true, 401);
	exit("Not authorized");
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT * FROM udids ORDER BY created DESC LIMIT 30";

$result = $conn->query($query);

$json = array();

while ($row = $result->fetch_assoc()) {
	$json[] = array(
		"name" => $row["name"],
		"email" => $row["email"],
		"udid" => $row["udid"],
		"product" => $row["product"]
	);
}

header('Content-type: application/json');
print json_encode(array("udids" => $json));