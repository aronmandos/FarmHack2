<?php
require_once 'database.php';
require_once 'login.php';

session_start ();

if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	mysqli_report ( MYSQLI_REPORT_ERROR );
	
	$mysqli = makeConnection ();
	
	$error_msg = "";
	
	if (isset ( $_POST ['IBANnummer'], $_POST ['email'], $_POST ['postcode'], $_POST ['woonplaats'], $_POST ['straat'], $_POST ['provincie'], $_POST ['huisnummer'], $_POST ['bouwjaar'], $_POST ['huistype'], $_POST ['voornaam'], $_POST ['achternaam'] )) {
		
		// print_r ( $_POST );
		
		$IBANnummer = $_POST ['IBANnummer'];
		$email = $_POST ['email'];
		$postcode = $_POST ['postcode'];
		$woonplaats = $_POST ['woonplaats'];
		$straat = $_POST ['straat'];
		$provincie = $_POST ['provincie'];
		$huisnummer = $_POST ['huisnummer'];
		$bouwjaar = $_POST ['bouwjaar'];
		$huistype = $_POST ['huistype'];
		$voornaam = $_POST ['voornaam'];
		$achternaam = $_POST ['achternaam'];
		
		if (isset ( $_POST ['toevoeging'] )) {
			$huisnummer = $huisnummer . $_POST ['toevoeging'];
		}
		
		$email = strtolower ( $email );
		$query = 'SELECT huistypeID FROM huistype WHERE omschrijving = ?';
		
		if ($stmt = $mysqli->prepare ( $query )) {
			
			$stmt->bind_param ( 's', $huistype );
			/* execute statement */
			$stmt->execute ();
			
			/* bind result variables */
			$stmt->bind_result ( $huistypeDB );
			
			$omschrijvingArray = array ();
			
			/* fetch values */
			while ( $stmt->fetch () ) {
				$huistype = $huistypeDB;
			}
			$stmt->close ();
		}
		
		$prep_stmt = "SELECT gebruikerID FROM gebruiker WHERE email = ? LIMIT 1";
		$stmt = $mysqli->prepare ( $prep_stmt );
		
		// check existing email
		if ($stmt) {
			$stmt->bind_param ( 's', $email );
			$stmt->execute ();
			$stmt->store_result ();
			
			if (! ($email == $_SESSION ['email'])) {
				if ($stmt->num_rows == 1) {
					// A user with this email address already exists
					$error_msg .= '<p class="error">A user with this email address already exists.</p>';
					$stmt->close ();
					header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=1.php' );
				}
				$stmt->close ();
			}
		} else {
			$error_msg .= '<p class="error">Database error Line 39</p>';
			$stmt->close ();
			header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
		}
		if (empty ( $error_msg )) {
			
			// Insert the new user into the database
			if ($insert_stmt = $mysqli->prepare ( "UPDATE gebruiker SET IBANnummer = ?, email = ?,
				 postcode = ?, huisnummer = ?, bouwjaar = ?, huistypeID = ?, voornaam = ?, achternaam = ? WHERE gebruikerID = ?" )) {
				$insert_stmt->bind_param ( 'ssssiissi', $IBANnummer, $email, $postcode, $huisnummer, $bouwjaar, $huistype, $voornaam, $achternaam, $_SESSION ['gebruikerID'] );
				// Execute the prepared query.
				if (! $insert_stmt->execute ()) {
					header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
				} else {
					header ( 'Location: http://localhost/quakepoint/php/logout.php' );
				}
			}
		}
	}
}
?>