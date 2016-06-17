<?php
require_once 'database.php';
require_once 'login.php';

mysqli_report ( MYSQLI_REPORT_ERROR );

$mysqli = makeConnection ();

$error_msg = "";

if (isset ( $_POST ['functie'], $_POST ['rechten'] )) {
	
	$functie = $_POST ['functie'];
	$rechten = $_POST ['rechten'];
	
	$stmto = $mysqli->prepare ( "SELECT rechtID FROM rechten WHERE omschrijving = ?" );
	$stmto->bind_param ( 's', $rechten );
	$stmto->execute ();
	$stmto->store_result ();
	$stmto->bind_result ( $rechtID );
	$stmto->fetch ();
	
	if (empty ( $error_msg )) {
		
		if ($insert_stmt = $mysqli->prepare ( "INSERT INTO functie (omschrijving) VALUES (?)" )) {
			$insert_stmt->bind_param ( 's', $functie );
			
			if (! $insert_stmt->execute ()) {
				header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
			} else {
				$functieID = $insert_stmt->insert_id;
				$insert_rechten_functie = $mysqli->prepare ( "INSERT INTO rechten_functie (rechtID, functieID) VALUES (?, ?)" );
				$insert_rechten_functie->bind_param ( 'ii', $rechtID, $functieID );
				$insert_rechten_functie->execute ();
				header ( 'Location: http://localhost/quakepoint/php/functies.php' );
			}
		}
	}
}

?>