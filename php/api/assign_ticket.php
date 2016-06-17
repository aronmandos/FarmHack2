<?php
include "../database.php";
$mysqli = makeConnection();

$array = array();

if(isset($_GET['ticket']) && isset($_GET['medewerker'])){
  
  // insert in database
  $query = "INSERT INTO ticket_medewerker VALUES (?,?)";
    if ($stmt = $mysqli->prepare($query)) {
      $stmt->bind_param ( 'ss' , $_GET['ticket'], $_GET['medewerker'] );
      $stmt->execute();
      $stmt->close();
  	}
  // connect to mongo database
  $db = makeConnectionMongo();

  // Select table
  $collection = $db->Ticket;

  // set update variable
  $update = array('medewerkerID' => (int)$_GET['medewerker']);

  //update ticket in database
  $collection->update(array( '_id' => $_GET['ticket']), array( '$set' => $update ));
}
echo json_encode($array);
?>