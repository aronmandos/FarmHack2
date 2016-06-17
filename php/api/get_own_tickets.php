<?php 
include "../database.php";

// Connect to MySQL
$mysqli = makeConnection();
// Connect to mongodb database
$db = makeConnectionMongo();
// Select table
$collection = $db->Ticket;
// Select table
$appointmentCollection = $db->Afspraak;

// initiate array
$array = array();
$ticketArray = array();
$appointmentArray = array();
$statusArray = array();

if(isset($_GET["client"])){
	$query = "SELECT statusID, omschrijving FROM status";
	if ($stmt = $mysqli->prepare($query)) {

		$stmt->execute();

		$stmt->bind_result($statusID, $omschrijving);

		while($stmt->fetch()) {
			$statusArray[$statusID] = $omschrijving;
		}
		$stmt->close();
	}

	// load documents in cursor
	$cursor = $collection->find(array("gebruikerID" => (int)$_GET["client"]))->sort(array('datum' => -1));
	
	//get ticket from database
	$i = 0;
	foreach ($cursor as $document) {
		$ticketArray["ticketID"] = $document["_id"];
		$ticketArray["description"] = $document["omschrijving"];
		$ticketArray["clientID"] = $document["gebruikerID"];
		$ticketArray["earthquakeID"] = $document["aardbevingID"];
		$ticketArray["status"] = $statusArray[$document["statusID"]];
		$ticketArray["priority"] = $document["prioriteit"];
		$ticketArray["date"] = date('d-m-Y', $document['datum']->sec);
		if(isset($document["oplossing"])){
			$ticketArray["solution"] = $document["oplossing"];
		}
		else {
			$ticketArray["solution"] = "";
		}
		if(isset($document["schadebedrag"])){
			$ticketArray["compensation"] = $document["schadebedrag"];
		}
		else {
			$ticketArray["compensation"] = "";
		}
		if(isset($document["bedrijfID"])){
			$ticketArray["companyID"] = $document["bedrijfID"];
		}
		else {
			$ticketArray["companyID"] = "";
		}
		$ticketArray["employeeID"] = $document["medewerkerID"];

		// load documents in cursor
		$appointmentCursor = $appointmentCollection->find(array("ticketID" => $ticketArray["ticketID"]));

		//get appointment information from database
	  	foreach ($appointmentCursor as $appointmentDocument) {
	    	$ticketArray["appointmentDate"] = date("d-m-Y", $appointmentDocument['datum']->sec);
	    	$ticketArray["appointmentTime"] = date("H:i", $appointmentDocument['datum']->sec);
	  	}

	  	$ticketArray["employee"] = "";
	  	$query = "SELECT voornaam, achternaam FROM medewerker WHERE medewerkerID = ?";
		if ($stmt = $mysqli->prepare($query)) {
			$stmt->bind_param ( 's' , $ticketArray["employeeID"] );

			$stmt->execute();

			$stmt->bind_result($firstname, $lastname);

			while($stmt->fetch()) {
				$ticketArray["employee"] = $firstname . " " . $lastname;
			}

			$stmt->close();
		}

	  	// push ticketinformation to main array
		array_push($array, $ticketArray);
	}
}
// encode array to json
echo json_encode($array);
?>