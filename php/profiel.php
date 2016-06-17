<?php
include "layout.php";
require 'database.php';
getHeader ();
?>
<div id="container">
	<h1>Profiel</h1>
	<?php
	if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['medewerker'] )) {
		if ($_SESSION ['medewerker'] != 1) {
			echo '<script src="../js/profiel/tickets.js"></script>';

			if(isset($_GET["action"])){
				echo '<input type="hidden" id="newTicket" value="' . $_GET["action"] . '">';
			}
		}
	}


	$mysqli = makeConnection ();

	if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
		if ($_SESSION ['medewerker']) {
			$stmt = $mysqli->prepare ( "SELECT voornaam, achternaam, functieID, bedrijfID
				FROM medewerker WHERE medewerkerID = ? LIMIT 1" );
			
			$stmt->bind_param ( 'i', $_SESSION ['gebruikerID'] );
			$stmt->execute ();
			$stmt->store_result ();
			
			$stmt->bind_result ( $voornaam, $achternaam, $functieID, $bedrijfID );
			$stmt->fetch ();
			
			$stmt = $mysqli->prepare ( "SELECT omschrijving FROM functie WHERE functieID = ?" );
			$stmt->bind_param ( 'i', $functieID );
			$stmt->execute ();
			$stmt->store_result ();
			
			$stmt->bind_result ( $functie );
			$stmt->fetch ();
			
			$stmt = $mysqli->prepare ( "SELECT bedrijfsnaam FROM bedrijf WHERE bedrijfID = ?" );
			$stmt->bind_param ( 'i', $bedrijfID );
			$stmt->execute ();
			$stmt->store_result ();
			
			$stmt->bind_result ( $bedrijf );
			$stmt->fetch ();
			
			echo '<table><td><h2> Profiel van:<strong> ' . $_SESSION ['email'] . '</strong></h2><br>Voornaam: <br>E-mail adres: <br>
				 Achternaam: <br> Functie: <br>Bedrijf: </td>
				<td><h2><strong>Gegevens:</strong></h2><br>' . $voornaam . '<br>' . $_SESSION ['email'] . '<br>' . $achternaam . '<br>' . $functie . '<br>' . $bedrijf . '</td></table>';
		} else {
			$stmt = $mysqli->prepare ( "SELECT IBANnummer, postcode, huisnummer, bouwjaar, huistypeID, voornaam, achternaam 
				FROM gebruiker WHERE gebruikerID = ? LIMIT 1" );
			
			$stmt->bind_param ( 'i', $_SESSION ['gebruikerID'] );
			$stmt->execute ();
			$stmt->store_result ();
			
			// get variables from result.
			$stmt->bind_result ( $IBANnummer, $postcode, $huisnummer, $bouwjaar, $huistypeID, $voornaam, $achternaam );
			$stmt->fetch ();
			
			$stmt = $mysqli->prepare ( "SELECT omschrijving FROM huistype WHERE huistypeID = ?" );
			$stmt->bind_param ( 'i', $huistypeID );
			$stmt->execute ();
			$stmt->store_result ();
			
			// get variables from result.
			$stmt->bind_result ( $huistype );
			$stmt->fetch ();
			
			echo '<table><td><h2> Profiel van:<strong> ' . $_SESSION ['email'] . '</strong></h2><br>IBANnummer: <br>E-mail adres: <br>
				 Postcode: <br> Huisnummer: <br>Bouwjaar huis: <br> Huistype: <br>Voornaam: <br>Achternaam: <br> <li><a href="editprofiel.php">Gegevens aanpassen</a></li></td>
				<td><h2><strong>Gegevens:</strong></h2><br>' . $IBANnummer . '<br>' . $_SESSION ['email'] . '<br>' . $postcode . '<br>' . $huisnummer . '<br>' . $bouwjaar . '<br>' . $huistype . '<br>' . $voornaam . '<br>' . $achternaam . '<br> <li><a href="changepassword.php">Wachtwoord aanpassen</a></li> </td></table>';
		}
	} else {
		header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=3.php' );
	}
	?>

	
	<?php
	if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['medewerker'] )) {
		if ($_SESSION ['medewerker'] != 1) {
			echo '<h2>Mijn Tickets</h2>';
		}
	}
	?>
	
	<div id="hiddenUserID">
		<input type="hidden" id="userID" value=<?php echo '"'.$_SESSION ['gebruikerID'].'"' ?>>
	</div>
</div>

<?php
getFooter ();
?> 