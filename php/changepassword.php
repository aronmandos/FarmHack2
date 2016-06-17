<?php
include_once 'layout.php';
getHeader ();

?>
<div id="container">
	<h1>Wachtwoord Wijzigen:</h1>
	<form action="passwordchangeprocess.php" method="post">
		<table>
			<td>Huidig wachtwoord: <br> Nieuw wachtwoord: <br> Herhaal nieuw
				wachtwoord: <br>
			</td>
			<td><input type="password" name="oudwachtwoord" required><br> <input
				type="password" name="wachtwoord" required><br> <input
				type="password" name="wachtwoordherhaald" required><br> <input
				type="submit" value="Wachtwoord wijzigen"></td>
		</table>
	</form>
</div>
<?php

getFooter ();

?>