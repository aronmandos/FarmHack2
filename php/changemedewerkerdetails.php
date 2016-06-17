<?php
include 'layout.php';
require_once 'database.php';
getHeader ();

mysqli_report ( MYSQLI_REPORT_ERROR );

if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	if ($_SESSION ['medewerker'] == 1) {
		if (isset ( $_POST ['medewerkerID'] )) {
			$mysqli = makeConnection ();
			
			$medewerkerID = $_POST ['medewerkerID'];
			
			$stmt = $mysqli->prepare ( "SELECT voornaam, achternaam, functieID, bedrijfID, email FROM medewerker WHERE medewerkerID = ?" );
			$stmt->bind_param ( 'i', $medewerkerID );
			$stmt->execute ();
			$stmt->store_result ();
			$stmt->bind_result ( $voornaam, $achternaam, $functieID, $bedrijfID, $email );
			$stmt->fetch ();
			
			$stmto = $mysqli->prepare ( "SELECT omschrijving FROM functie" );
			$stmto->execute ();
			$stmto->store_result ();
			$stmto->bind_result ( $functie_omschrijving );
			
			$stmtb = $mysqli->prepare ( "SELECT bedrijfsnaam FROM bedrijf" );
			$stmtb->execute ();
			$stmtb->store_result ();
			$stmtb->bind_result ( $bedrijfsnaam );
			
			echo '<div id="container">
					<form action="changemedewerkerdetailsprocess.php" method="post">
						<table>
								<td>
									Voornaam: <br>
									Achternaam: <br>
									Functie: <br>
									Bedrijf: <br>
									E-mail: <br>
					
								</td>
								<td> 
									<input type="text" name="voornaam" value="' . $voornaam . '"><br>
									<input type="text" name="achternaam" value="' . $achternaam . '"><br>
									<input type="hidden" name="medewerkerID" value="' . $medewerkerID . '">
									<select name="functie">';
			while ( $stmto->fetch () ) {
				echo '<option value="' . $functie_omschrijving . '">' . $functie_omschrijving . '</option>';
			}
			echo '</select><br><select name="bedrijf">';
			
			while ( $stmtb->fetch () ) {
				echo '<option value="' . $bedrijfsnaam . '">' . $bedrijfsnaam . '</option>';
			}
			echo '
									</select> 
									<br>
									<input type="text" name="email" value="' . $email . '"><br>
									
									<input type="submit" value="Gegevens Aanpassen">
								</td>
						</table>
				</div>';
		}
	}
} else {
	header ( 'Location: http://localhost/quakepoint/php/logout.php' );
}

getFooter ()?>