<?php
require_once 'database.php';
require_once 'login.php';

session_start ();

if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	mysqli_report ( MYSQLI_REPORT_ERROR );
	
	$mysqli = makeConnection ();
	
	$error_msg = "";
	
	if (isset ( $_POST ['voornaam'], $_POST ['achternaam'], $_POST ['functie'], $_POST ['bedrijf'], $_POST ['email'] )) {
		
		$voornaam = $_POST ['voornaam'];
		$achternaam = $_POST ['achternaam'];
		$functie = $_POST ['functie'];
		$bedrijf = $_POST ['bedrijf'];
		$email = $_POST ['email'];
		$medewerkerID = $_POST ['medewerkerID'];
		
		$email = strtolower ( $email );
		
		print_r ( $_POST );
		
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
		
		if ($stmt) {
			$stmt->bind_param ( 's', $email );
			$stmt->execute ();
			$stmt->store_result ();
		} else {
			$error_msg .= '<p class="error">Database error Line 39</p>';
			$stmt->close ();
			header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
		}
		if (! ($stmt->num_rows > 1)) {
			if (empty ( $error_msg )) {
				
				if ($insert_stmt = $mysqli->prepare ( "UPDATE medewerker SET voornaam = ?, achternaam = ?,
				 functieID = ?, bedrijfID = ?, email = ? WHERE medewerkerID = ?" )) {
					$insert_stmt->bind_param ( 'ssiisi', $voornaam, $achternaam, $functieID, $bedrijfID, $email, $medewerkerID );
					// Execute the prepared query.
					if (! $insert_stmt->execute ()) {
						header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
					} else {
						header ( 'Location: http://localhost/quakepoint/php/accountbeheer.php' );
					}
				}
			}
		}
	}
}
?>