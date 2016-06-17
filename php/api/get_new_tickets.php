<?php
include "../database.php";

// connect to mongo
$db = makeConnectionMongo();

if(isset($_GET["startLimit"])){
	if(isset($_GET["search"])){
		searchNewTickets($db, $_GET["startLimit"], $_GET["search"]);
	}
	else{
		getNewTickets($db, $_GET["startLimit"]);
	}
}


function getNewTickets($db, $startLimit){
	// select table Ticket
	$collection = $db->Ticket;

	// load rows in cursor
	$cursor = $collection->find(array("medewerkerID" => 0))->sort(array('datum' => 1))->limit(5)->skip($startLimit);

	foreach ($cursor as $document) {
		buildTable($document);
	}
}

function searchNewTickets($db, $startLimit, $search){
	if(strlen($search) < 1){
		getNewTickets($db, $startLimit);
		return;
	}

	$endLimit = $startLimit + 5; 
	$array = explode(" ", $search);
	$user = array();
	$searchUser = false;
	$searchDatum = false;
	$oneElement = true;

	foreach ($array as &$value) {
	    if (($timestamp = strtotime($value)) === false) {
	    	$searchUser = true;
	    	$tempArray = array();
			$tempArray = searchOnUser($value);

			if($oneElement){
				foreach ($tempArray as &$userID) {
					array_push($user, $userID);
				}
				$oneElement = false;
			}
			else{
				$tempUserArray = array();
				foreach ($tempArray as &$userID) {
					if (in_array($userID, $user)) {
						array_push($tempUserArray, $userID);
					}
				}
				$user = $tempUserArray;
			}
		} 
		else {
		    $searchDatum = true;
		    $search = $value;
		}
	}

	// select table Ticket
	$collection = $db->Ticket;
	$cursor = array(); 

	if($searchDatum){
		$time = strtotime($search);
		$time = date("d-m-Y", $time);
		$startTime = strtotime($time . " 00:00:00");
		$startTime = new MongoDate($startTime);
		$endTime = strtotime($time . " 23:59:59");
		$endTime = new MongoDate($endTime);

		if($searchUser){
			// load rows in cursor
			$cursor = $collection->find(array("medewerkerID" => 0, "datum" => array('$gt' => $startTime, '$lt' => $endTime)))->sort(array('datum' => 1));
		}
		else{
			// load rows in cursor
			$cursor = $collection->find(array("medewerkerID" => 0, "datum" => array('$gt' => $startTime, '$lt' => $endTime)))->sort(array('datum' => 1))->limit(5)->skip($startLimit);
		}
	}
	else{
		// load rows in cursor
		$cursor = $collection->find(array("medewerkerID" => 0))->sort(array('datum' => 1));
	}

	$i = 0;
	$hasDocument = false;
	foreach ($cursor as $document) {
		$hasDocument = true;
		if($searchUser){
			foreach ($user as &$userID) {
				if($userID == $document["gebruikerID"]){
					if($i >= $startLimit && $i < ($startLimit + 5)){
						buildTable($document);
					}
					$i++;
				}
			}
		}
		else{
			buildTable($document);
		}
	}
}

function buildTable($document){
	$userInfo = getUserInfo($document["gebruikerID"]);
	echo "<table><thead>";
	echo '<tr>';
	echo '<th width="45%">';
	echo $document["prioriteit"] . ' - ' . $userInfo['lastName'] . ' </th>';
	echo '<th width="45%">';
	echo 'Ticket ingediend - ' . date('d-m-Y', $document['datum']->sec) . ' </th>';
	echo '<th width="10%"><img src="../img/next.png" id="slideLink'.$document["_id"].'" class="show_hide">';
	echo "</th></tr></thead></table>";
	echo '<div id="slideTable'.$document["_id"].'" class="slidingTable" style="display:none;">';
	echo '<table>';
	echo '<tbody><tr><td>';
	echo $userInfo['firstName'] . " " . $userInfo['lastName'];
	echo '</td></tr>';
	echo '<tr><td width>';
	echo $userInfo['email'];
	echo "</td></tr>";
	echo '<tr><td>';
	echo date('d-m-Y', $document['datum']->sec);
	echo "</td></tr>";
	echo '<tr><td>';
	echo $document["omschrijving"];
	echo '</td></tr>';
	echo '<tr><td>';
	echo '<a href=# id="' . $document["_id"] . '" class="getTicket" value="' . $document["_id"] . '" style="float:right;"><button style="cursor:pointer;">Koppel Ticket aan mij</button></a>';
	echo "</td></tr></tbody>";
	echo "</table>";
	echo "</div>";
}

function getUserInfo($userID){
	$return = array();

	//connect to MySQL
	$mysqli = makeConnection();

	$query = "SELECT * FROM gebruiker WHERE gebruikerID = ?";
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->bind_param ( 's' , $userID );

		$stmt->execute();

		$stmt->bind_result($clientID, $bankNumber, $email, $password, $postalCode, $houseNumber, $yearOfBuild, $typeHouseID, $firstName, $lastName);

		while($stmt->fetch()) {
			$return["userID"] = $clientID;
			$return["bankNumber"] = $bankNumber;
			$return["email"] = $email;
			$return["postalCode"] = $postalCode;
			$return["houseNumber"] = $houseNumber;
			$return["yearOfBuild"] = $yearOfBuild;
			$return["typeHouseID"] = $typeHouseID;
			$return["firstName"] = $firstName;
			$return["lastName"] = $lastName;
		}
		$stmt->close();
	}
	return $return;
}

function searchOnUser($search){
	$user = array();

	//connect to MySQL
	$mysqli = makeConnection();

	$name = "%" . $search . "%";

	$query = "SELECT gebruikerID FROM gebruiker WHERE  voornaam LIKE ? OR achternaam LIKE ?";
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->bind_param ( 'ss' , $name,  $name);

		$stmt->execute();

		$stmt->bind_result($clientID);

		while($stmt->fetch()) {
			array_push($user, $clientID);
		}
		$stmt->close();
	}
	return $user;
}
?>