<?php
include "layout.php";
getHeader ();

if (! (isset ( $_GET ['error_code'] ))) {
	header ( 'Location: http://localhost/quakepoint/index.php' );
}
switch ($_GET ['error_code']) {
	case 0 :
		echo '<html><div id="container"><h2>U heeft een verkeerd e-mail adres of wachtwoord ingevoerd.</h2></div></html>';
		break;
	case 1 :
		echo '<html><div id="container"><h2>Er bestaat al een gebruiker met dit e-mail adres.</h2></div></html>';
		break;
	case 2 :
		echo '<html><div id="container"><h2>Er ging iets mis, probeer het later opnieuw.</h2></div></html>';
		break;
	case 3 :
		echo '<html><div id="container"><h2>U moet eerst inloggen voordat u deze functie kunt gebruiken.</h2></div></html>';
		break;
	case 4 :
		echo '<html><div id="container"><h2>U heeft u huidige wachtwoord niet juist ingevuld. <br><br> <li><a href="changepassword.php">Klik hier om het opnieuw te proberen</a></li></h2></div></html>';
		break;
	case 5 :
		echo '<html><div id="container"><h2>De nieuwe wachtwoorden waren niet gelijk. <br><br> <li><a href="changepassword.php">Klik hier om het opnieuw te proberen</a></li></h2></div></html>';
		break;
	case 6 :
		echo '<html><div id="container"><h2>U heeft niet voldoende rechten om deze actie uit te voeren.<br><br></h2></div></html>';
		break;
	case 7 :
		echo '<html><div id="container"><h2>Er kon geen connectie met de MongoDB database gelegd worden.<br><br></h2></div></html>';
		break;
	default :
		header ( 'Location: http://localhost/quakepoint/index.php' );
}

getFooter ();

?>