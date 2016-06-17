<?php 

include "../database.php";

if(isset($_GET['priority']) && isset($_GET['client']) && isset($_GET['description'])){ 
	$db = makeConnectionMongo();

	$id = md5(microtime().rand());

	$dt = new DateTime(date('Y-m-d'), new DateTimeZone('UTC'));
	$ts = $dt->getTimestamp();
	$today = new MongoDate($ts);

	$db->Ticket->insert(array("_id" => $id, "omschrijving" => $_GET['description'], 
							  "gebruikerID" => (int)$_GET['client'], "aardbevingID" => $_GET['earthquakeID'], 
							  "statusID" => 0, "prioriteit" => $_GET['priority'], 
							  "datum" => $today, "medewerkerID" => 0));


	$array = array();
	$array["ticketID"] = $id;

	// build json
	echo json_encode($array);
}
?>