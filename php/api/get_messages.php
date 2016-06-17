<?php
include "../database.php";

// connect to mongo
$db = makeConnectionMongo();

// select table Bericht
$collection = $db->Bericht;

if(isset($_GET["earthquakeID"]) && isset($_GET["startLimit"])){
	// load rows in cursor
	$cursor = $collection->find(array('aardbevingID' => (int)$_GET['earthquakeID']))->sort(array('datum' => -1))->limit(3)->skip($_GET["startLimit"]);

	$array = array();
	$messageArray = array();

	foreach ($cursor as $document) {
		$messageArray["messageID"] = $document['_id'];
		$messageArray["user"] = getName($document["gebruikerID"]);
		$messageArray["description"] = $document["omschrijving"];
		$messageArray["likes"] = $document["likes"];
		$messageArray["date"] = gmdate("Y-m-d", $document['datum']->sec);

		array_push($array, $messageArray);
	}

	buildTable($array);
}

function getName($userID){
	$return = "";

	//connect to MySQL
	$mysqli = makeConnection();

	$query = "SELECT voornaam, achternaam FROM gebruiker WHERE gebruikerID = ?";
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->bind_param ( 's' , $userID );

		$stmt->execute();

		$stmt->bind_result($firstName, $lastName);

		while($stmt->fetch()) {
			$return = $firstName . " " . $lastName;
		}

		$stmt->close();
	}

	return $return;
}

function buildTable($array){
	session_start();

	foreach ($array as $message) {
		echo '<div id="messageBox' . $message["messageID"] . '" class="message">';
			echo '<table>';
				echo '<tr>';
					echo '<th>';
						echo $message["user"];
					echo '</th>'; 
					echo '<th>';
						if(isset($_SESSION ['gebruikerID'])){
							if ($_SESSION ['medewerker'] == 1) {
								echo '<a href="#/" class="deleteMessage" value="' . $message["messageID"] . '" style="float:left;"><button style="cursor:pointer;">Bericht verwijderen</button></a>';
							}
						}
					echo '</th>';
				echo '</tr>';
				echo '<tr>';
					echo '<td width = 80%>';
						echo $message["description"];
					echo '</td>';
					echo '<td>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td>';
						echo '<a href="#/" class="thumbs-up" value = '.$message["messageID"].'><img src="../img/thumbs-up.png" width="20" height="20" border="0" /></a> ';
						echo '<b><div id = "likes'.$message["messageID"].'" class="inl">'.$message["likes"].'</div></b> ';
						echo '<i> Likes</i>';
					echo '</td>';
					echo '<td>';
						//if(isset($_SESSION ['gebruikerID'])){
							//if ($_SESSION ['medewerker'] == 1) {
								
							//}
						//}
						echo '<i>Datum: </i>';
						echo '<i>'. $message["date"] .'</i>';
					echo '</td>'; 
				echo '</tr>';
			echo '</table>';
		echo '</div>';
	}
}
?>