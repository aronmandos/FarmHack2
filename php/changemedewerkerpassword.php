<?php
include_once 'layout.php';
getHeader ();

if (isset ($_POST ['medewerkerID'] )) {
	echo '
<div id="container">
	<h1>Wachtwoord Wijzigen:</h1>
	<form action="medewerkerpasswordchangeprocess.php" method="post">
		<table>
			<td>
			Nieuw wachtwoord: 
			<br> Herhaal nieuw wachtwoord: <br>
			</td>
			<td>
				<input type="password" name="wachtwoord" required><br> 
				<input type="password" name="wachtwoordherhaald" required><br>
				<input type="hidden" name="medewerkerID" value="' . $_POST ['medewerkerID'] . '"> 
				<input type="submit" value="Wachtwoord wijzigen"></td>
		</table>
	</form>
</div>
';
} else {
}
getFooter ();

?>