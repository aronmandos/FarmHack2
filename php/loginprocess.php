<?php
require_once 'database.php';
require_once 'login.php';
include 'error.php';

mysqli_report ( MYSQLI_REPORT_ALL );

if (isset ( $_POST ['email'], $_POST ['wachtwoord'] )) {
	$email = $_POST ['email'];
	$password = $_POST ['wachtwoord']; // The hashed password.
	
	$mysqli = makeConnection ();
	
	$email = strtolower ( $email );
	
	if (login ( $email, $password, $mysqli ) == true) {
		// Login success
		header ( 'Location: http://localhost/quakepoint/php/profiel.php' );
	} else {
		// Login failed
		header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=0' );
	}
} else {
	// The correct POST variables were not sent to this page.
	echo 'Invalid Request';
}

