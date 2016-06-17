<?php
require_once 'database.php';
require_once 'login.php';

mysqli_report ( MYSQLI_REPORT_ERROR );

$mysqli = makeConnection ();

$error_msg = "";

if (isset ( $_POST ['voornaam'], $_POST ['achternaam'], $_POST ['wachtwoord'], $_POST ['functie'], $_POST ['bedrijf'], $_POST ['email'] )) {
	
	$voornaam = $_POST ['voornaam'];
	$achternaam = $_POST ['achternaam'];
	$wachtwoord = $_POST ['wachtwoord'];
	$functie = $_POST ['functie'];
	$bedrijf = $_POST ['bedrijf'];
	$email = $_POST ['email'];
	
	$email = strtolower ( $email );
	
	$stmto = $mysqli->prepare ( "SELECT functieID FROM functie WHERE omschrijving = ?" );
	$stmto->bind_param ( 's', $functie );
	$stmto->execute ();
	$stmto->store_result ();
	$stmto->bind_result ( $functieID );
	$stmto->fetch ();
	
	$stmtb = $mysqli->prepare ( "SELECT bedrijfID FROM bedrijf WHERE bedrijfsnaam = ?" );
	$stmtb->bind_param ( 's', $bedrijf );
	$stmtb->execute ();
	$stmtb->store_result ();
	$stmtb->bind_result ( $bedrijfID );
	$stmtb->fetch ();
	
	$prep_stmt = "SELECT medewerkerID FROM medewerker WHERE email = ? LIMIT 1";
	$stmt = $mysqli->prepare ( $prep_stmt );
	
	// check existing email
	if ($stmt) {
		$stmt->bind_param ( 's', $email );
		$stmt->execute ();
		$stmt->store_result ();
		
		if ($stmt->num_rows == 1) {
			// A user with this email address already exists
			$error_msg .= '<p class="error">A user with this email address already exists.</p>';
			$stmt->close ();
			header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=1.php' );
		}
		$stmt->close ();
	} else {
		$error_msg .= '<p class="error">Database error Line 39</p>';
		$stmt->close ();
		header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
	}
	if (empty ( $error_msg )) {
		
		$wachtwoord = hash ( 'sha256', $wachtwoord );
		
		// Insert the new user into the database
		if ($insert_stmt = $mysqli->prepare ( "INSERT INTO medewerker (voornaam, achternaam, wachtwoord,
				 functieID, bedrijfID, email) VALUES (?, ?, ?, ?, ?, ?)" )) {
			$insert_stmt->bind_param ( 'ssssss', $voornaam, $achternaam, $wachtwoord, $functieID, $bedrijfID, $email );
			// Execute the prepared query.
			if (! $insert_stmt->execute ()) {
				header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
			} else {
				
				header ( 'Location: http://localhost/quakepoint/php/accountbeheer.php' );
			}
		}
	}
}

?>