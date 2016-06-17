<?php
include "../database.php";
$mysqli = makeConnection();

$array = array();
$clientArray = array();
$ticketArray = array();
$statusArray = array();

if(isset($_GET['ticket']) && isset($_GET['client'])){ 
  // select from database
  $query = "SELECT * FROM gebruiker WHERE gebruikerID = ?";
  if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param ( 's' , $_GET['client'] );
    
    $stmt->execute();

    $stmt->bind_result($clientID, $bankNumber, $email, $password, $postalCode, $houseNumber, $yearOfBuild, $typeHouseID, $firstName, $lastName);

    while($stmt->fetch()) {
      $clientArray["clientID"] = $clientID;
      $clientArray["bankNumber"] = $bankNumber;
      $clientArray["email"] = $email;
      $clientArray["postalCode"] = $postalCode;
      $clientArray["houseNumber"] = $houseNumber;
      $clientArray["yearOfBuild"] = $yearOfBuild;
      $clientArray["typeHouseID"] = $typeHouseID;
      $clientArray["firstName"] = $firstName;
      $clientArray["lastName"] = $lastName;
    }

    $stmt->close();
  }

  $query = "SELECT straat, plaats, provincie FROM postcodesnl WHERE postcode = ?";
  if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param ( 's' , $clientArray["postalCode"] );

    $stmt->execute();

    $stmt->bind_result($street, $city, $province);

    while($stmt->fetch()) {
      $clientArray["street"] = $street;
      $clientArray["city"] = $city;
      $clientArray["province"] = $province;
    }

    $stmt->close();
  }

  $query = "SELECT omschrijving FROM huistype WHERE huistypeID = ?";
  if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param ( 's' , $clientArray["typeHouseID"] );

    $stmt->execute();

    $stmt->bind_result($typeHouse);

    while($stmt->fetch()) {
      $clientArray["typeHouse"] = $typeHouse;
    }

    $stmt->close();
  }

  $query = "SELECT statusID, omschrijving FROM status";
  if ($stmt = $mysqli->prepare($query)) {

    $stmt->execute();

    $stmt->bind_result($statusID, $omschrijving);

    while($stmt->fetch()) {
      $statusArray[$statusID] = $omschrijving;
    }

    $stmt->close();
  }

  // connect to mongo database
  $db = makeConnectionMongo();

  // Select table
  $collection = $db->Ticket;

  // get info from table
  $cursor = $collection->find(array("_id" => $_GET['ticket']));

  //get ticket from database
  foreach ($cursor as $document) {
    $ticketArray["ticketID"] = $document["_id"];
    $ticketArray["description"] = $document["omschrijving"];
    $ticketArray["clientID"] = $document["gebruikerID"];
    $ticketArray["earthquakeID"] = $document["aardbevingID"];
    $ticketArray["statusID"] = $document["statusID"];
    $ticketArray["priority"] = $document["prioriteit"];
    $ticketArray["date"] = date('d-m-Y', $document['datum']->sec);
    if(isset($document["oplossing"])){
      $ticketArray["solution"] = $document["oplossing"];
    }
    else {
      $ticketArray["solution"] = "";
    }
    if(isset($document["schadebedrag"])){
      $ticketArray["compensation"] = (string)$document["schadebedrag"];
    }
    else {
      $ticketArray["compensation"] = "";
    }
    if(isset($document["bedrijfID"])){
      $ticketArray["companyID"] = $document["bedrijfID"];
    }
    else {
      $ticketArray["companyID"] = "";
    }
    $ticketArray["employeeID"] = $document["medewerkerID"];
  }

  // Select table
  $collection = $db->Afspraak;

  // get info from table
  $cursor = $collection->find(array("ticketID" => $_GET['ticket']));

  //get ticket from database
  foreach ($cursor as $document) {
    $ticketArray["appointmentDate"] = date("d-m-Y", $document['datum']->sec);
    $ticketArray["appointmentTime"] = date("H:i", $document['datum']->sec);
  }

  // add clientinformation to main array
  $array["client"] = $clientArray;

  // add ticketinformation to main array
  $array["ticket"] = $ticketArray;

  // add statusinformation to main array
  $array["status"] = $statusArray;

  echo json_encode($array);
}
?>