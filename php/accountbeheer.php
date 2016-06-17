<?php
include 'layout.php';
include 'database.php';
getHeader ();
if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	if ($_SESSION ['medewerker'] == 1) {
		$mysqli = makeConnection ();
		
		if ($stmt = $mysqli->prepare ( "SELECT functieID FROM medewerker WHERE medewerkerID = ?
        LIMIT 1" )) {
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
			
			if ($recht <= 3) {
				$stmt = $mysqli->prepare ( "SELECT medewerkerID, voornaam, achternaam, functieID, bedrijfID, email FROM medewerker" );
				$stmt->execute ();
				$stmt->store_result ();
				$stmt->bind_result ( $medewerkerID, $voornaam, $achternaam, $functieID, $bedrijfID, $email );
				
				echo '
					<div id="container">
						<h1>Account beheer</h1>
						<a href="new_medewerker.php">Maak nieuwe medewerker aan</a>
						';
				
				$i = 0;
				while ( $stmt->fetch () ) {
					
					$stmtf = $mysqli->prepare ( "SELECT omschrijving FROM functie WHERE functieID = ? LIMIT 1" );
					$stmtf->bind_param ( 'i', $functieID );
					$stmtf->execute ();
					$stmtf->store_result ();
					$stmtf->bind_result ( $functie );
					$stmtf->fetch ();
					
					$stmtb = $mysqli->prepare ( "SELECT bedrijfsnaam FROM bedrijf WHERE bedrijfID = ? LIMIT 1" );
					$stmtb->bind_param ( 'i', $bedrijfID );
					$stmtb->execute ();
					$stmtb->store_result ();
					$stmtb->bind_result ( $bedrijf );
					$stmtb->fetch ();
					
					$stmtr = $mysqli->prepare ( "SELECT rechtID FROM rechten_functie WHERE functieID = ? LIMIT 1" );
					$stmtr->bind_param ( 'i', $functieID );
					$stmtr->execute ();
					$stmtr->store_result ();
					$stmtr->bind_result ( $rechtID );
					$stmtr->fetch ();
					
					$stmto = $mysqli->prepare ( "SELECT omschrijving FROM rechten WHERE rechtID = ? LIMIT 1" );
					$stmto->bind_param ( 'i', $rechtID );
					$stmto->execute ();
					$stmto->store_result ();
					$stmto->bind_result ( $rechten );
					$stmto->fetch ();
					
					echo '
							<table>
								<tr>
									<td>
										MedewerkerID: <br>
										Voornaam: <br>
										Achternaam: <br>
										Functie: <br>
										Rechten: <br>
										Bedrijf: <br>
										E-mail: <br>
										<form action="changemedewerkerdetails.php" method="post">
										<input type="hidden" name="medewerkerID" value="' . $medewerkerID . '">
										<input type="submit" value="Gegevens medewerker aanpassen">
										</form>
									</td>
									<td>' . $medewerkerID . '<br>' . $voornaam . '<br>' . $achternaam . '<br>' . $functie . '<br>' . $rechten . '<br>' . $bedrijf . '<br>' . $email . '<br>
										<form action="changemedewerkerpassword.php" method="post">
										<input type="hidden" name="medewerkerID" value="' . $medewerkerID . '">
										<input type="submit" value="Wachtwoord medewerker aanpassen">
										</form>';
					if (! ($medewerkerID == $_SESSION ['gebruikerID'])) {
						
						echo '
											<form action="deleteconfirmation.php" method="post">
											<input type="hidden" name="medewerkerID" value="' . $medewerkerID . '">
											<input type="submit" value="Medewerker verwijderen">
											</form>';
					}
					echo '
												
									</td>
								</tr>
							</table>
						';
				}
				echo '</div>';
			}
			else {
				header('Location: http://localhost/quakepoint/php/error.php?error_code=6.php');
			}
			
		}
	} else {
		header ( 'Location: http://localhost/quakepoint/php/logout.php' );
	}
}
getFooter ();
?>