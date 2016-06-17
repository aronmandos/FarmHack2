<?php
include 'layout.php';
include 'database.php';
getHeader ();
if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	if ($_SESSION ['medewerker'] == 1) {
		$mysqli = makeConnection ();
		
		$stmt = $mysqli->prepare ( "SELECT functieID FROM medewerker WHERE medewerkerID = ?
        LIMIT 1" );
		
		$stmt->bind_param ( 'i', $_SESSION ['gebruikerID'] );
		$stmt->execute ();
		$stmt->store_result ();
		$stmt->bind_result ( $functieID );
		$stmt->fetch ();
		
		$stmt = $mysqli->prepare ( "SELECT rechtID FROM rechten_functie WHERE functieID = ? LIMIT 1" );
		$stmt->bind_param ( 'i', $functieID );
		$stmt->execute ();
		$stmt->store_result ();
		$stmt->bind_result ( $recht );
		$stmt->fetch ();
		
		if ($recht <= 2) {
			$stmt = $mysqli->prepare ( "SELECT functieID, omschrijving FROM functie" );
			$stmt->execute ();
			$stmt->store_result ();
			$stmt->bind_result ( $functieID, $omschrijving );
			
			echo '
					<div id="container">
						<h1>Functies</h1>
					<a href="new_functie.php">Maak nieuwe functie aan</a>';
			
			$i = 0;
			while ( $stmt->fetch () ) {
				
				$stmtr = $mysqli->prepare ( "SELECT rechtID FROM rechten_functie WHERE functieID = ?" );
				$stmtr->bind_param ( 'i', $functieID );
				$stmtr->execute ();
				$stmtr->store_result ();
				$stmtr->bind_result ( $rechtID );
				$stmtr->fetch ();
				
				$stmtre = $mysqli->prepare ( "SELECT omschrijving FROM rechten WHERE rechtID = ?" );
				$stmtre->bind_param ( 'i', $rechtID );
				$stmtre->execute ();
				$stmtre->store_result ();
				$stmtre->bind_result ( $rechten );
				$stmtre->fetch ();
				
				echo '
							<table>
								<tr>
									<td>
										FunctieID: <br>
										Omschrijving: <br>
										Rechten: <br>
										<form action="changefunctiedetails.php" method="post">
										<input type="hidden" name="functieID" value="' . $functieID . '">
										<input type="submit" value="Gegevens functie aanpassen">
										</form>
									</td>
									<td>' . $functieID . '<br>' . $omschrijving . '<br>' . $rechten . '<br>
											<form action="deleteconfirmation.php" method="post">
											<input type="hidden" name="functieID" value="' . $functieID . '">
											<input type="submit" value="Functie verwijderen">
											</form>';
				echo '
		
									</td>
								</tr>
							</table>
						';
			}
			echo '</div>';
		} else {
			echo 'geert';
			header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=6' );
		}
	}
} else {
	header ( 'Location: http://localhost/quakepoint/php/logout.php' );
}
getFooter ();
?>