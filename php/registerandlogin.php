<?php
include "layout.php";
require_once 'database.php';
getHeader ();
$mysqli = makeConnection ();

$query = "SELECT omschrijving FROM huistype";

if ($stmt = $mysqli->prepare ( $query )) {
	
	/* execute statement */
	$stmt->execute ();
	
	/* bind result variables */
	$stmt->bind_result ( $omschrijving );
	
	$omschrijvingArray = array ();
	
	/* fetch values */
	while ( $stmt->fetch () ) {
		array_push ( $omschrijvingArray, $omschrijving );
	}
}
?>
<div id="container">

	<div id="register">
		<form action="registerprocess.php" method="post">
			<table>
				<td width="500">
					<h1>Registreren</h1> 
					IBAN: <br> 
					E-mail: <br> 
					Wachtwoord: <br>
					Postcode: <br> 
					Woonplaats: <br> 
					Straat: <br> 
					Provincie: <br>
					Huisnummer: <br> 
					Toevoeging: <br> 
					Bouwjaar: <br> 
					Type Huis: <br>
					Voornaam: <br> 
					Achternaam: <br>
				</td>
				<td>
					<h1>Vul in:</h1> 
					<input type="text" name="IBANnummer" placeholder="Voorbeeld: NL66DHBN5961326040" pattern="[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}" required><br> 
					<input type="text" name="email" pattern="[a-z0-9!#$%&'*+/=?^_`{|}~.-]+@[a-z0-9-]+(\.[a-z0-9-]+)*" required><br> 
					<input type="password" name="wachtwoord" required><br>
					<input type="text" name="postcode" pattern="^[1-9][0-9]{3} ?(?!SA|SD|SS)[A-Z]{2}$" placeholder="Voorbeeld: 9718AB" required><br> 
					<input type="text" name="woonplaats" required><br> 
					<input type="text" name="straat" required><br> 
					<input type="text" name="provincie" required><br> 
					<input type="number" name="huisnummer" required><br> 
					<input type="text" name="toevoeging"><br> 
					<input type="number" name="bouwjaar" required><br> 
					<select name="huistype">
					<?php
						foreach ( $omschrijvingArray as $omschrijving ) {
						echo '<option value="' . $omschrijving . '">' . $omschrijving . '</option>';
						}
		 		
					?>
					</select></br>
			 		<input type="text" name="voornaam" required><br> 
					<input type="text" name="achternaam" required><br> 
					<input type="submit" value="Registreren">
				</td>
			</table>
		</form>
	</div>


	<form action="loginprocess.php" method="post">
		<table>
			<td width="500">
				<h1>Inloggen</h1>
				E-mail: <br> 
				Wachtwoord <br>
			</td>
			<td>
				<h1>Vul in:</h1> 
				<input type="text" name="email"><br> 
				<input type="password" name="wachtwoord"><br> 
				<input type="submit" value="Inloggen"></td>
		</table>

	</form>


</div>
<?php
getFooter ();
?>