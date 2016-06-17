<?php
	include "../database.php";
  $function = array('lat','jaartal');

	
  foreach($function as $value){
    if (isset ( $_POST [$value] )) {
      switch($value){
        case 'lat':
          getLocation($_POST['lng'], $_POST['lat']);
          break;
        case 'jaartal':
          getAardbeving();
          break;
        }
    }
  }

  function getAardbeving(){
    $mysqli = makeConnection();
    $i=0;
    if(isset($_POST['jaartal']) && $_POST['jaartal'] != 'Selecteer een optie'){
      list($jaartal1, $Bmaand, $Emaand, $jaartal2) = buildQuery();
      $datum1 = $jaartal1. "/". $Bmaand. "/". "01";
      $datum2 = $jaartal2. "/". $Emaand. "/". "31"; 
      // $latMin = (float)52.5;
      // $latMax = (float)53.0;
      // $longMin = (float)5.5;
      // $longMax = (float)6.5;
      	//$query = "SELECT latitude, longitude, magnitude FROM aardbeving WHERE YEAR(datum) = ?"; " AND latitude BETWEEN ? AND ? AND longitude BETWEEN ? AND ?";
      $query = "SELECT aardbevingID, latitude, longitude, magnitude, depth, datum FROM aardbeving WHERE datum BETWEEN ? AND ? ";
        if ($stmt = $mysqli->prepare($query)) {
          $stmt->bind_param ( 'ss' , $datum1, $datum2 ); //, $latMin, $latMax, $longMin, $longMax );
          $stmt->execute();
      		$stmt->bind_result($id, $lat, $long, $mag, $depth, $datum);
      		while ($stmt->fetch()) {
      			$array[$i]['lat'] = $lat;
      			$array[$i]['long'] = $long;
      			$array[$i]['mag'] = $mag;
            $array[$i]['depth'] = $depth;
            $array[$i]['datum'] = $datum;
            $array[$i]['aardbevingID'] = $id;
      			$i++;
      		}
      		$stmt->close();
      	}
        if(isset($array)){
        	foreach($array as $value){
        		echo $value['lat'].",". $value['long']. ",". $value['mag'] .",". $value['depth'] .",". $value['datum'].",".$value['aardbevingID'].","; 
        	}
        }
    }
  }

  function getLocation($long, $lat){
     $mysqli = makeConnection();
     $query = "SELECT gemeente, provincie FROM postcodesnl WHERE lng = ? AND lat = ?";
          if ($stmt = $mysqli->prepare($query)) {
             $stmt->bind_param ( 'ss' , $long, $lat );
             $stmt->execute();
             $stmt->bind_result($gemeente, $provincie);
             while ($stmt->fetch()) {
              $gemeente = $gemeente;
              $provincie = $provincie;
            }
           $stmt->close();
          }
      echo $gemeente.", " . $provincie;
  }

  function buildQuery(){
    $jaartal = $_POST['jaartal'];

    if($_POST['Bmaand'] == ''){
      $Bmaand = 1;
      if($_POST['Emaand']== ''){
          $Emaand  = 12;
      }
      else{
          $Emaand = $_POST['Emaand'];
      }
    }
    else{
      $Bmaand = $_POST['Bmaand'];
      if($_POST['Emaand']== ''){
         $Emaand  = 12;
      }
      else{
          $Emaand = $_POST['Emaand'];
      }
    }
    if($Bmaand > $Emaand){
      return array($jaartal,$Bmaand,$Emaand,$jaartal+1);
    }
    else{
      return array($jaartal,$Bmaand,$Emaand, $jaartal);
    }
  }
?>
