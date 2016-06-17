<?php
include 'layout.php';

getHeader ();
echo '
		<div id="container">
<form action="loginprocess.php" method="post">
<table>
<td><h1>U kunt nu inloggen!</h1>E-mail: <br> Wachtwoord <br></td>
<td><h1>Vul in:</h1> <input type="text" name="email"><br> <input
type="password" name="wachtwoord"><br> <input type="submit"
		value="Inloggen"></td>
		</table>

		</form></div>';

getFooter ();
?>