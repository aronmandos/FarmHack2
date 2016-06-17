<?php
include 'layout.php';
getHeader ();

if (isset ( $_POST ['medewerkerID'] )) {
	echo '<div id="container">
		<h2>Weet u zeker dat u deze medewerker wilt verwijderen?<h2> <br><table>
			<td>
			<form action="deletemedewerker.php" method="post">
			<input type="hidden" name="medewerkerID" value="' . $_POST ['medewerkerID'] . '">
			<input type="submit" value="Medewerker verwijderen"> 
			</form></td>
			<td>
			<form action="accountbeheer.php" method="post">
			<input type="submit" value="Annuleren"></form></td></table></div>';
} elseif (isset ( $_POST ['functieID'] )) {
	
	echo '<div id="container">
		<h2>Weet u zeker dat u deze functie wilt verwijderen?<h2> <br><table>
			<td>
			<form action="deletefunctie.php" method="post">
			<input type="hidden" name="functieID" value="' . $_POST ['functieID'] . '">
			<input type="submit" value="Functie verwijderen">
			</form></td>
			<td>
			<form action="functie.php" method="post">
			<input type="submit" value="Annuleren"></form></td></table></div>';
} else {
	header ( 'Location: http://localhost/quakepoint/php/logout.php' );
}

getFooter ();