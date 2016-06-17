<?php
require_once 'database.php';

session_start ();

if (isset ( $_POST ['medewerkerID'], $_POST ['wachtwoord'], $_POST ['wachtwoordherhaald'] )) {
	$mysqli = makeConnection ();
	
	$medewerkerID = $_POST ['medewerkerID'];
	$wachtwoord = $_POST ['wachtwoord'];
	$wachtwoordherhaald = $_POST ['wachtwoordherhaald'];
	
	if ($stmt = $mysqli->prepare ( "SELECT wachtwoord FROM medewerker WHERE medewerkerID = ?
        LIMIT 1" )) {
		
		$stmt->bind_param ( 'i', $medewerkerID );
		$stmt->execute ();
		$stmt->store_result ();
		
		$stmt->bind_result ( $db_password );
		$stmt->fetch ();
		
		if ($wachtwoord == $wachtwoordherhaald) {
			$wachtwoord = hash ( 'sha256', $wachtwoord );
			
			$stmt = $mysqli->prepare ( "UPDATE medewerker SET wachtwoord = ? WHERE medewerkerID = ?" );
			$stmt->bind_param ( 'si', $wachtwoord, $medewerkerID );
			$stmt->execute ();
			
			header ( 'Location: http://localhost/quakepoint/php/accountbeheer.php' );
		} else {
			header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=5.php' );
		}
	}
}

