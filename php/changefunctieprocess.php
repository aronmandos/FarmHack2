<?php
require_once 'database.php';
require_once 'login.php';

session_start ();

if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	mysqli_report ( MYSQLI_REPORT_ERROR );
	
	$mysqli = makeConnection ();
	
	$error_msg = "";
	
	if (isset ( $_POST ['omschrijving'], $_POST ['functieID'] )) {
		
		$omschrijving = $_POST ['omschrijving'];
		$functieID = $_POST ['functieID'];
		$rechten = $_POST ['rechten'];
		
		$stmto = $mysqli->prepare ( "SELECT rechtID FROM rechten WHERE omschrijving = ?" );
		$stmto->bind_param ( 's', $rechten );
		$stmto->execute ();
		$stmto->store_result ();
		$stmto->bind_result ( $rechtID );
		$stmto->fetch ();
		
		if ($insert_stmt = $mysqli->prepare ( "UPDATE functie SET omschrijving = ? WHERE functieID = ?" )) {
			$insert_stmt->bind_param ( 'si', $omschrijving, $functieID );
			
			$rechten_insert = $mysqli->prepare ( "UPDATE rechten_functie SET rechtID = ? WHERE functieID = ?" );
			$rechten_insert->bind_param ( 'ii', $rechtID, $functieID );
			
			if (($insert_stmt->execute () && $rechten_insert->execute ())) {
				header ( 'Location: http://localhost/quakepoint/php/functies.php' );
			}
		} else {
			header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
		}
	}
}

?>