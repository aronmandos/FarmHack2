<?php
include 'layout.php';
require_once 'database.php';
getHeader ();

mysqli_report ( MYSQLI_REPORT_ERROR );

if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	if ($_SESSION ['medewerker'] == 1) {
		if (isset ( $_POST ['functieID'] )) {
			$mysqli = makeConnection ();
			
			$functieID = $_POST ['functieID'];
			
			$stmt = $mysqli->prepare ( "SELECT functieID, omschrijving FROM functie WHERE functieID = ?" );
			$stmt->bind_param ( 'i', $functieID );
			$stmt->execute ();
			$stmt->store_result ();
			$stmt->bind_result ( $functieID, $omschrijving );
			$stmt->fetch ();
			
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
			
			$stmtrec = $mysqli->prepare ( "SELECT omschrijving FROM rechten" );
			$stmtrec->execute ();
			$stmtrec->store_result ();
			$stmtrec->bind_result ( $rechten_lijst );
			
			echo '<div id="container">
					<form action="changefunctieprocess.php" method="post">
						<table>
								<td>
									Functie: <br>
									Rechten: <br>
			
								</td>
								<td>
									<input type="text" name="omschrijving" value="' . $omschrijving . '"><br>
									<input type="hidden" name="functieID" value="' . $functieID . '">
									<select name="rechten">';
			while ( $stmtrec->fetch () ) {
				echo '<option value="' . $rechten_lijst . '">' . $rechten_lijst . '</option>';
			}
			echo '
									</select>
									<br>
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