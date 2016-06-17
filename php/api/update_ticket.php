<?php
include "../database.php";
$mysqli = makeConnection();
$db = makeConnectionMongo();

$statusID = 0;
if(isset($_GET["action"])){
	switch ($_GET["action"]) {
	    case "appointment":
	        setAppointment($mysqli, $db, $_GET["ticket"], $_GET["date"]);
	        break;
	    case "reject":
	        rejectTicket($mysqli, $db, $_GET["ticket"]);
	        break;
	    case "rejectDate":
	        rejectAppointment($mysqli, $db, $_GET["ticket"]);
	        break;
	    case "approveDate":
	        approveAppointment($mysqli, $db, $_GET["ticket"]);
	        break;
	    case "solution":
	        setSolution($mysqli, $db, $_GET["ticket"], $_GET["solution"], $_GET["compensation"]);
	        break;
		case "approveSolution":
	        approveSolution($mysqli, $db, $_GET["ticket"]);
	        break;
	    case "rejectSolution":
	        rejectSolution($mysqli, $db, $_GET["ticket"]);
	        break;
	    case "pay":
	        pay($mysqli, $db, $_GET["ticket"]);
	        break;
	    default: 
	    	$array = array();
	    	$array["actie"] = "geen actie";
	    	echo json_encode($array);
	}
}

function setAppointment($mysqli, $db, $ticket, $date){
	// initiate array
	$array = array();

	if(isset($ticket) && isset($date)){ 
		$statusID = 0;

		$query = "SELECT statusID FROM status WHERE Omschrijving = 'Afspraak aangemaakt'";
		if ($stmt = $mysqli->prepare($query)) {

			$stmt->execute();

			$stmt->bind_result($statusID);

			while($stmt->fetch()) {
				$statusID = $statusID;
			}
		}

		// create MongoDate object based on original date-time
		$dateTime = new MongoDate(strtotime($date));

		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("statusID" => $statusID)));

		$db->Afspraak->insert(array("ticketID" => $ticket, "goedkeuring" => 0, "datum" => $dateTime));

		// Select table
		$collection = $db->Afspraak;

		// get info from table
		$cursor = $collection->find();

		// initiate array
		$array = array();

		//get ticket from database
		foreach ($cursor as $document) {
			$array["afspraakID"] = $document["_id"];
			$array["ticketID"] = $document["ticketID"];
			$array["aproval"] = $document["goedkeuring"];
			$array["date"] = $document["datum"];
		}
	}
	// encode array to json
	echo json_encode($array);
}

function rejectTicket($mysqli, $db, $ticket){
	// initiate array
	$array = array();

	if(isset($ticket)){ 
		$statusID = 0;

		$query = "SELECT statusID FROM status WHERE Omschrijving = 'Gesloten'";
		if ($stmt = $mysqli->prepare($query)) {

			$stmt->execute();

			$stmt->bind_result($statusID);

			while($stmt->fetch()) {
				$statusID = $statusID;
			}
		}

		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("statusID" => $statusID)));
	}
	// encode array to json
	echo json_encode($array);
}

function rejectAppointment($mysqli, $db, $ticket){
	// initiate array
	$array = array();

	if(isset($ticket)){ 
		$statusID = 0;

		$query = "SELECT statusID FROM status WHERE Omschrijving = 'Afspraak niet geaccepteerd'";
		if ($stmt = $mysqli->prepare($query)) {

			$stmt->execute();

			$stmt->bind_result($statusID);

			while($stmt->fetch()) {
				$statusID = $statusID;
			}
		}

		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("statusID" => $statusID)));
	}
	// encode array to json
	echo json_encode($array);
}

function approveAppointment($mysqli, $db, $ticket){
	// initiate array
	$array = array();

	if(isset($ticket)){ 
		$statusID = 0;

		$query = "SELECT statusID FROM status WHERE Omschrijving = 'Afspraak geaccepteerd'";
		if ($stmt = $mysqli->prepare($query)) {

			$stmt->execute();

			$stmt->bind_result($statusID);

			while($stmt->fetch()) {
				$statusID = $statusID;
			}
		}

		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("statusID" => $statusID)));
	}
	// encode array to json
	echo json_encode($array);
}

function setSolution($mysqli, $db, $ticket, $solution, $compensation){
	// initiate array
	$array = array();

	if(isset($ticket) && isset($solution) && isset($compensation)){ 
		$statusID = 0;

		$query = "SELECT statusID FROM status WHERE Omschrijving = 'Oplossing aangeboden'";
		if ($stmt = $mysqli->prepare($query)) {

			$stmt->execute();

			$stmt->bind_result($statusID);

			while($stmt->fetch()) {
				$statusID = $statusID;
			}
		}

		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("statusID" => $statusID)));
		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("oplossing" => $solution)));
		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("schadebedrag" => $compensation)));
	}
	// encode array to json
	echo json_encode($array);
}

function approveSolution($mysqli, $db, $ticket){
	// initiate array
	$array = array();

	if(isset($ticket)){ 
		$statusID = 0;

		$query = "SELECT statusID FROM status WHERE Omschrijving = 'Oplossing goedgekeurd'";
		if ($stmt = $mysqli->prepare($query)) {

			$stmt->execute();

			$stmt->bind_result($statusID);

			while($stmt->fetch()) {
				$statusID = $statusID;
			}
		}

		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("statusID" => $statusID)));
	}
	// encode array to json
	echo json_encode($array);
}

function rejectSolution($mysqli, $db, $ticket){
	// initiate array
	$array = array();

	if(isset($ticket)){ 
		$statusID = 0;

		$query = "SELECT statusID FROM status WHERE Omschrijving = 'Oplossing afgekeurd'";
		if ($stmt = $mysqli->prepare($query)) {

			$stmt->execute();

			$stmt->bind_result($statusID);

			while($stmt->fetch()) {
				$statusID = $statusID;
			}
		}

		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("statusID" => $statusID)));
	}
	// encode array to json
	echo json_encode($array);
}

function pay($mysqli, $db, $ticket){
	// initiate array
	$array = array();

	if(isset($ticket)){ 
		$statusID = 0;

		$query = "SELECT statusID FROM status WHERE Omschrijving = 'Klant betaald'";
		if ($stmt = $mysqli->prepare($query)) {

			$stmt->execute();

			$stmt->bind_result($statusID);

			while($stmt->fetch()) {
				$statusID = $statusID;
			}
		}

		$db->Ticket->update(array("_id" => $ticket), array('$set' => array("statusID" => $statusID)));
	}
	// encode array to json
	echo json_encode($array);
}
?>