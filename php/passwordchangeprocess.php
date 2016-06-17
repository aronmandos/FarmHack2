<?php
require_once 'database.php';

session_start ();

if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	if (isset ( $_POST ['oudwachtwoord'], $_POST ['wachtwoord'], $_POST ['wachtwoordherhaald'] )) {
		$mysqli = makeConnection ();
		
		$oudwachtwoord = $_POST ['oudwachtwoord'];
		$wachtwoord = $_POST ['wachtwoord'];
		$wachtwoordherhaald = $_POST ['wachtwoordherhaald'];
		
		if ($stmt = $mysqli->prepare ( "SELECT wachtwoord FROM gebruiker WHERE gebruikerID = ?
        LIMIT 1" )) {
			
			$stmt->bind_param ( 'i', $_SESSION ['gebruikerID'] );
			$stmt->execute ();
			$stmt->store_result ();
			
			$stmt->bind_result ( $db_password );
			$stmt->fetch ();
			
			$oudwachtwoord = hash ( 'sha256', $oudwachtwoord );
			
			if ($oudwachtwoord == $db_password) {
				if ($wachtwoord == $wachtwoordherhaald) {
					$wachtwoord = hash ( 'sha256', $wachtwoord );
					
					$stmt = $mysqli->prepare ( "UPDATE gebruiker SET wachtwoord = ? WHERE gebruikerID = ?" );
					$stmt->bind_param ( 'si', $wachtwoord, $_SESSION ['gebruikerID'] );
					$stmt->execute ();
					
					header ( 'Location: http://localhost/quakepoint/php/logout.php' );
				} else {
					header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=5.php' );
				}
			} else {
				header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=4.php' );
			}
		}
	}
}