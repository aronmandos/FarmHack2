<?php
include "layout.php";
include "database.php";

// connect to mySQL database
$mysqli = makeConnection();

// connect to mongo database
$db = makeConnectionMongo();

// Select table
$collection = $db->Ticket;

// build header to get session information
getHeader();

// check if page is called by employee
if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	if ($_SESSION ['medewerker'] == 1) {
		if(isset($_GET['p'])){
			$pagina = $_GET['p'];
			switch ($pagina) {
		    case 'Werkpagina';
		        getWerkPagina($mysqli, $collection);
		        break;
		   	case 'Helpdesk';
		        getHelpdesk($mysqli, $collection);
			    break;
			default;
				goHome();
				break;
			}
		}
	}
}
else {
	goHome();
}

function getWerkPagina($mysqli, $collection) {
	?>
	<link rel="stylesheet" type="text/css" media="screen"
		href="../css/autocomplete.css">
	<script src="../js/werkpagina/werkpagina.js"></script>
	<div id="container">
		<h1>Werkpagina</h1>
		<table>
			<tr>
				<td>
					<input id="searchBox" type="search" placeholder="Search">
				</td>
			</tr>
		</table>
		<input type="hidden" id="employee" value=<?php echo '"' . $_SESSION ['gebruikerID'] . '"'?>>
		<div id="newTickets"></div>
	</div>
	<!-- END container -->
	<?php
	getFooter();
}

function getHelpdesk($mysqli, $collection) {
	?>
	<link rel="stylesheet" type="text/css" href="../css/jquery.datetimepicker.css"/ >
	<script src="../js/jquery/jquery.datetimepicker.js"></script>
	<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
	<script src="../js/werkpagina/helpdesk.js"></script>
	<div id="container">
		<h1>Helpdesk</h1>
		<input type="hidden" id="actie" value=<?php if(isset($_GET["a"])){ echo '"'.$_GET["a"].'"';} else{ echo '""';} ?>>
		<?php getMyTickets($_SESSION ['gebruikerID'], $collection, $mysqli) ?>
	</div>
	<?php
	getFooter();
}

function goHome() {
	header("Location: ../index.php");
}

function getMyTickets($employee, $collection, $mysqli){
	$cursor = $collection->find(array("medewerkerID" => $employee, "statusID" => array('$lt' => 9)))->sort(array("datum" => 1));
		// Iterate cursor
	$i = 0;
	foreach ($cursor as $document) {
		$userInfo = getUserInfo($mysqli, $document["gebruikerID"]);
		$status = getStatusDescription($mysqli, $document["statusID"]);
		echo "<table><thead>";
		echo '<tr>';
		echo '<th width="30%">';
		echo $document["prioriteit"];
		echo '</th>';
		echo '<th width="30%">';
		echo $userInfo['firstName']. ' ' .$userInfo['lastName'];
		echo '</th>';
		echo '<th width="30%">';
		echo $status['omschrijving'];
		echo '</th>';
		echo '<th width="10%"><img src="../img/next.png" id="slideLink'.$document["_id"].'" class="show_hide">';
		echo "</th></tr></thead></table>";
		echo '<div id="slideTable'.$document["_id"].'" class="slidingTable">';
		echo '<input type="hidden" id="client' . $document["_id"] . '" value="' . $document["gebruikerID"] . '">';
		echo '<table>';
		echo '<tbody><tr><td width="20%">';
		echo '<b>Naam en Achternaam</b>';
		echo "</td><td>";
		echo $userInfo['firstName'] . " " . $userInfo['lastName'];
		echo '</td></tr>';
		echo '<tr><td width="20%">';
		echo '<b>Ticketdatum</b>';
		echo "</td><td>";
		echo date('d-m-Y', $document['datum']->sec);
		echo "</td></tr>";
		echo '<tr><td width="20%">';
		echo '<b>Status</b>';
		echo "</td><td>";
		echo $status['omschrijving'];
		echo "</td></tr>";
		echo '<tr><td width="20%">';
		echo '<b>Klachtomschrijving</b>';
		echo "</td><td>";
		echo $document["omschrijving"];
		echo '</td></tr>';
		echo '<tr><td>';
		echo '</td><td>';
		echo '<a href="#/" class="getHelpdeskTicket" value="' . $document["_id"] . '" style="float:right;"><button style="cursor:pointer;">Open Ticket</button></a>';
		echo "</td></tr></tbody>";
		echo "</table>";
		echo "</div>";
		$i++;
	}
}

function getUserInfo($mysqli, $id){
	$userArray = array();
	$query = "SELECT gebruikerID, achternaam, voornaam, email FROM gebruiker WHERE gebruikerID = ?";
	if ($stmt = $mysqli->prepare($query)) {
		/* bind param */
		$stmt->bind_param ( 's' , $id );

		/* execute statement */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($userID, $lastName, $firstName, $email);

		while ($stmt->fetch()) {
			$userArray = array(
				'userID' => $userID,
				'firstName' => $firstName,
				'lastName' => $lastName,
				'email' => $email);
		}
		/* close statement */
		$stmt->close();
	}
	return $userArray;
}

function getStatusDescription($mysqli, $statusID){
	$decsriptionArray = array();
	$query = "SELECT Omschrijving FROM status WHERE statusID = ?";
	if ($stmt = $mysqli->prepare($query)) {
		/* bind param */
		$stmt->bind_param ( 's' , $statusID );

		/* execute statement */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($omschrijving);

		while ($stmt->fetch()) {
			$decsriptionArray = array(
				'omschrijving' => $omschrijving);
		}
		/* close statement */
		$stmt->close();
	}
	return $decsriptionArray;
}
?>