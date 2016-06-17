<?php
require_once 'database.php';
session_start ();
mysqli_report ( MYSQLI_REPORT_ERROR );

if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	if ($_SESSION ['medewerker'] == 1) {
		if (isset ( $_POST ['functieID'] )) {
			$mysqli = makeConnection ();
			
			$functieID = $_POST ['functieID'];
			
			if ($delete_stmt = $mysqli->prepare ( "DELETE FROM functie
													WHERE functieID = ?; " )) {
				$delete_stmt->bind_param ( 'i', $functieID );
				if (! $delete_stmt->execute ()) {
					header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
				} else {
					header ( 'Location: http://localhost/quakepoint/php/functies.php' );
				}
			}
		}
	} else {
		print_r ( $_POST );
	}
}

?>