<?php
include_once '../database.php';

mysqli_report ( MYSQLI_REPORT_ERROR );
$mysqli = makeConnection ();
$error_msg = "";

echo trim(strip_tags($_GET['term']));
if(isset($_GET['term']) && !empty($_GET['term'])) {
	$search = trim(strip_tags($_GET['term']));

	$query = "SELECT voornaam, achternaam FROM gebruiker WHERE voornaam LIKE ? OR achternaam LIKE ?";
	if ($stmt = $mysqli->prepare($query)) {

		/* bind param */
		$stmt->bind_param ( 'ss' , $search, $search );

		/* execute statement */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($firstName, $lastName);

		/* fetch values */
		echo '<ul id="nameList">';
		while ($stmt->fetch()) {
			echo '<li>' .$firstName . ' ' . $lastName . '</li>';
		}
		echo '</ul>';
		/* close statement */
		$stmt->close();
	}
}

/* close connection */
$mysqli->close();
?>