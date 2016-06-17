<?php
include 'layout.php';
require_once 'database.php';
getHeader ();

if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	$mysqli = makeConnection ();
	
	$query = "SELECT omschrijving FROM huistype";
	
	if ($stmt = $mysqli->prepare ( $query )) {
		
		$stmt->execute ();
		
		$stmt->bind_result ( $omschrijving );
		
		$omschrijvingArray = array ();
		
		while ( $stmt->fetch () ) {
			array_push ( $omschrijvingArray, $omschrijving );
		}
	}
	
	$stmt = $mysqli->prepare ( "SELECT IBANnummer, postcode, huisnummer, bouwjaar,  voornaam, achternaam
				FROM gebruiker WHERE gebruikerID = ? LIMIT 1" );
	
	$stmt->bind_param ( 'i', $_SESSION ['gebruikerID'] );
	$stmt->execute ();
	$stmt->store_result ();
	
	$stmt->bind_result ( $IBANnummer, $postcode, $huisnummer, $bouwjaar, $voornaam, $achternaam );
	$stmt->fetch ();
	
	$stmt = $mysqli->prepare ( "SELECT straat, provincie, plaats
				FROM postcode WHERE postcode = ? LIMIT 1" );
	
	$stmt->bind_param ( 's', $postcode );
	$stmt->execute ();
	$stmt->store_result ();
	
	$stmt->bind_result ( $straat, $provincie, $plaats );
	$stmt->fetch ();
	
	echo '
		<div id="container">
			<form action="changedetails.php" method="post">
				<table>
					<td>
						<h1>Profiel aanpassen</h1> 
						IBAN: <br> 
						E-mail: <br> 
						Postcode: <br>
						Woonplaats: <br> 
						Straat: <br> 
						Provincie: <br> 
						Huisnummer: <br>
						Toevoeging: <br> 
						Bouwjaar: <br> 
						Type Huis: <br> 
						Voornaam: <br>
						Achternaam: <br>
					</td>
					<td>
						<h1>Vul in:</h1> 
						<input type="text" name="IBANnummer" value="' . $IBANnummer . '" placeholder="Voorbeeld: NL66DHBN5961326040" pattern="[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}" required><br> 
						<input type="text" name="email" value="' . $_SESSION ['email'] . '" pattern="[a-z0-9!#$%&\'*+/=?^_`{|}~.-]+@[a-z0-9-]+(\.[a-z0-9-]+)*"required><br> 
						<input type="text" name="postcode" value="' . $postcode . '" pattern="^[1-9][0-9]{3} ?(?!SA|SD|SS)[A-Z]{2}$" placeholder="Voorbeeld: 9718AB" required><br> 
						<input type="text" name="woonplaats" value="' . $plaats . '" required><br> 
						<input type="text" name="straat" value="' . $straat . '" required><br> 
						<input type="text" name="provincie" value="' . $provincie . '" required><br> 
						<input type="number" name="huisnummer" value="' . $huisnummer . '" required><br> 
						<input type="text" name="toevoeging"><br> 
						<input type="number" name="bouwjaar" value="' . $bouwjaar . '"required><br> 
						<select name="huistype">';
						foreach ( $omschrijvingArray as $omschrijving ) {
						echo '<option value="' . $omschrijving . '">' . $omschrijving . '</option>';
						}
	echo				'</select></br>
				 		<input type="text" name="voornaam" value="' . $voornaam . '" required> <br>
						<input type="text" name="achternaam" value="' . $achternaam . '" required> <br>
						<input type="submit" value="Aanpassen"></td>
				</table>
			</form>
		</div>';
	getFooter ();
}
?>
