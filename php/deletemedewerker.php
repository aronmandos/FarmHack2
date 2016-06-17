<?php
require_once 'database.php';
session_start ();
if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	if ($_SESSION ['medewerker'] == 1) {
		if (isset ( $_POST ['medewerkerID'] )) {
			$mysqli = makeConnection ();
			
			$medewerkerID = $_POST ['medewerkerID'];
			
			if ($delete_stmt = $mysqli->prepare ( "DELETE FROM medewerker
													WHERE medewerkerID = ?; " )) {
				$delete_stmt->bind_param ( 'i', $medewerkerID );
				if (! $delete_stmt->execute ()) {
					header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=2.php' );
				} else {
					header ( 'Location: http://localhost/quakepoint/php/accountbeheer.php' );
				}
			}
		}
	}
}

?>