<?php
function makeConnection(){
	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "project_aardbeving";

// Create connection
	$mysqli = new mysqli($servername, $username, $password, $database);

// Check connection
	if (mysqli_connect_errno()) {
    	printf("Connect failed: %s\n", mysqli_connect_error());
    	exit();
	}
	return $mysqli;
}

function makeConnectionMongo() {
	// Make connection with MongoDB
	try{
	    $m = new MongoClient(); // connect
	    $db = $m->selectDB("project_aardbeving");
	}
	catch ( MongoConnectionException $e ){
		header ( 'Location: http://localhost/quakepoint/php/error.php?error_code=7.php' );
		exit();
	}

	return $db;
}
?>