<?php
include 'layout.php';
require_once 'database.php';
getHeader ();

mysqli_report ( MYSQLI_REPORT_ERROR );

if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
	if ($_SESSION ['medewerker'] == 1) {
		$mysqli = makeConnection ();
		
		$stmto = $mysqli->prepare ( "SELECT omschrijving FROM rechten" );
		$stmto->execute ();
		$stmto->store_result ();
		$stmto->bind_result ( $rechten_omschrijving );
		
		echo '<div id="container">
					<form action="createfunctie.php" method="post">
						<table>
								<td>
									Functie: <br>
									Rechten: <br>
								</td>
								<td> 
									<input type="text" name="functie"><br>
									<select name="rechten">';
		while ( $stmto->fetch () ) {
			echo '<option value="' . $rechten_omschrijving . '">' . $rechten_omschrijving . '</option>';
		}
		echo '
					</select> 
					<br>
					<input type="submit" value="Functie aanmaken">
					</td>
					</table>
				</div>';
	}
} else {
	header ( 'Location: http://localhost/quakepoint/php/logout.php' );
}

getFooter ()?>