<?php
	include "../database.php";

	if(isset($_POST['position'])){
		createSearch($_POST['position']);
		return;
	}
	else if(isset($_POST['id'])){
		aardbevingPagina();
		return;
	}
	else if(isset($_POST['comment'])){
		likeComment();
		return;
	}
	else if(isset($_POST['p']) && isset($_POST['text']) && isset($_POST['gebruikerID'])){
		commitComment();
		return;
	}
	else if(isset($_POST['deleteMessage'])){
		deleteComment($_POST['deleteMessage']);
		return;
	}
	else{
		getAardbevingenCount();
		return;
	}


	function aardbevingPagina(){

		$mysqli = makeConnection();
		$query = "SELECT datum,magnitude,latitude,longitude,depth,aardbevingID FROM aardbeving WHERE aardbevingID = ?";
		
		if ($stmt = $mysqli->prepare($query)) {	
			$stmt->bind_param ( 's' , $_POST['id'] );
			$stmt->execute();
	  		$stmt->bind_result($datum, $mag, $lat, $lng, $depth, $id);
	  		$i=0;
	  		while ($stmt->fetch()) {
		  		echo $datum.",".$mag.",".getLocation($lng,$lat).",".$depth.",".$lat.",".$lng;
		  	}
	 		$stmt->close();
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
          return $gemeente.", " . $provincie;
      }

   	function drawAardbevingBox($datum, $mag, $lat, $lng, $depth, $id){
		$code = 
		'<table><thead>'.
		'<tr>'.
		'<th width="90%">'.
		 getLocation($lng, $lat). ' - ' . $datum . ' </th>'.
		'<th width="10%"><img src="../img/next.png" id="slide'.$id.'" class="show_hide">'.
		"</th></tr></thead></table>".
		'<div id="slideTable'.$id.'" class="slidingTable">'.
		'<table>'.
		'<tbody><tr><td>'.
		'<b>Locatie: </b>'.getLocation($lng, $lat) .
		'</td></tr>'.
		'<tr><td width>'.
		'<b>Hevigheid: </b>'.$mag.
		"</td></tr>".
		'<tr><td>'.
		'<b>Diepte: </b>'.$depth.' Kilometer'.
		"</td></tr>".
		'<tr><td>'.
		'<b>Datum: </b>'.$datum.
		'</td></tr>'.
		'<tr><td>'.
		'<a href=aardbevingen.php?p='.$id.'>Ga naar de aardbeving</a>'.
		'</td></tr></tbody>'.
		'</table>'.
		'</div>';
		echo $code;
	}

	function likeComment(){
		$p = $_POST['comment'];
		$db = makeConnectionMongo();
		$collection = $db->Bericht;
		$query = array('_id' => $p);
		$cursor = $collection->find($query);
		foreach($cursor as $document){
			$likes = (int)$document['likes'];
		}
		$likes++;
		$collection->update(array("_id" => $p), array('$set' => array("likes" => $likes)));
		echo $likes;
	}

	function deleteComment($id){
		$array = array();
		$db = makeConnectionMongo();
		$collection = $db->Bericht;
		$db->Bericht->remove(array("_id" => $id));
		echo json_encode($array);
	}

	function getAardbevingenCount(){
		 $mysqli = makeConnection();
		 $query = "SELECT COUNT(aardbevingID) FROM aardbeving";

		if ($stmt = $mysqli->prepare($query)) {	
			$stmt->execute();
  			$stmt->bind_result($count);
  		while ($stmt->fetch()) {
 			echo $count;
	  	}
 			$stmt->close();
   		}
	}

	function commitComment(){
		$array = array();
		$db = makeConnectionMongo();
		$collection = $db->Bericht;
		$aardbevingID = $_POST['p'];
		$gebruikerID = (int)$_POST['gebruikerID'];
		$text = $_POST['text'];
		$dt = new DateTime(date('Y-m-d'), new DateTimeZone('UTC'));
		$ts = $dt->getTimestamp();
		$today = new MongoDate($ts);

		$id = md5(microtime().rand());

		$collection->insert(array("_id" => $id,
								  "omschrijving" => $text,
								  "aardbevingID"=> (int)$aardbevingID,
								  "gebruikerID" => $gebruikerID, 
								  "datum" => $today, 
								  "likes"=> (int)0 ));
		$cursor = $collection->find(array('_id' => $id));

		foreach ($cursor as $document) {
			$messageArray["messageID"] = $document['_id'];
			$messageArray["user"] = getName($document["gebruikerID"]);
			$messageArray["description"] = $document["omschrijving"];
			$messageArray["likes"] = $document["likes"];
			$messageArray["date"] = gmdate("Y-m-d", $document['datum']->sec);

			array_push($array, $messageArray);
		}

		buildMessage($array);
	}

	function createSearch($position){
		$jaartal = $_POST['jaartal'];
		$maand = $_POST['maand'];
		$gemeente = $_POST['gemeente'];
		$provincie = $_POST['provincie'];
		$query = "SELECT datum,magnitude,latitude,longitude,depth,aardbevingID FROM aardbeving";
		$boolean = true;
		$string = '';

		//echo $jaartal . ' ' .  $maand . ' ' .  $gemeente . ' ' .  $provincie . ' ' . $position;

		if($jaartal != 'Selecteer een optie'){
			$string = $string. 'j';
			$boolean = false;
			$query = $query . ' WHERE YEAR(datum) = ? ';
			if($maand != ''){
				$string= $string. 'm';
				$query = $query . 'AND MONTH(datum) = ? ';
			}
		}

		$mysqli = makeConnection();
		$array = array();

        if($provincie != '' && $gemeente == ''){
				$string = $string. 'p';
				if($boolean){
			 		$boolean = false;
			 		$query = $query . ' WHERE provincie = ? ';
			 	}
			 	else{ 
			 		$query = $query . ' AND provincie = ? ';
			 	}
		}
		else if($gemeente != ''){
			$string = $string. 'g';
			if($boolean){
		 		$boolean = false;
		 		$query = $query . ' WHERE gemeente = ? ';
		 	}
		 	else{ 
		 		$query = $query . ' AND gemeente = ? ';
			}	
		}

		$query = $query.' ORDER BY datum DESC LIMIT ?, 10';

		if ($stmt = $mysqli->prepare($query)) {
            switch($string){
            	case '':
            		$stmt->bind_param ( 's' , $position );	
            		break;
            	case 'j':
            		$stmt->bind_param ( 'ss' , $jaartal, $position );
            		break;
            	case 'jm':
            		$stmt->bind_param ( 'sss' , $jaartal, $maand, $position );
            		break;
            	case 'jmp':
            		$stmt->bind_param ( 'ssss' , $jaartal, $maand, $provincie,$position );
            		break;
            	case 'jmpg':
            		$stmt->bind_param ( 'sssss' , $jaartal, $maand, $provincie, $gemeente,$position );
            		break;
            	case 'jp':
            		$stmt->bind_param ( 'sss' , $jaartal, $provincie, $position );
            		break;
            	case 'jpg':
            		$stmt->bind_param ( 'ssss' , $jaartal, $provincie, $gemeente, $position );
            		break;
            	case 'jg':
            		$stmt->bind_param ( 'sss' , $jaartal, $gemeente, $position );
            		break;
            	case 'p':
            		$stmt->bind_param ( 'ss' , $provincie, $position );
            		break;
            	case 'pg':
            		$stmt->bind_param ( 'sss' , $provincie, $gemeente, $position );
            		break;
            	case 'g':
            		$stmt->bind_param ( 'ss' , $gemeente, $position );
            		break;
            	case 'jmg':
            		$stmt->bind_param ( 'ssss', $jaartal, $maand, $gemeente, $position );
            		break;
            }
            $stmt->execute();
            $stmt->bind_result($datum, $mag, $lat, $lng, $depth, $id);
            $i = 0;
            while ($stmt->fetch()) {
            	$array[$i]['datum']= $datum;
            	$array[$i]['mag']= $mag;
            	$array[$i]['lat']= $lat;
            	$array[$i]['lng']= $lng;
            	$array[$i]['depth']= $depth;
            	$array[$i]['id']= $id;
            	$i++;
           	}
          $stmt->close();
        }

        foreach ($array as $value) {
			drawAardbevingBox($value['datum'],$value['mag'],$value['lat'],$value['lng'],$value['depth'],$value['id']);
		}
	}

	function buildMessage($array){
		session_start();

		foreach ($array as $message) {
			echo '<div id="messageBox' . $message["messageID"] . '" class="message" style="display:none;">';
				echo '<table>';
					echo '<tr>';
						echo '<th>';
							echo $message["user"];
						echo '</th>'; 
						echo '<th>';
							if(isset($_SESSION ['gebruikerID'])){
								if ($_SESSION ['medewerker'] == 1) {
									echo '<a href="#/" class="deleteMessage" value="' . $message["messageID"] . '" style="float:left;"><button style="cursor:pointer;">Bericht verwijderen</button></a>';
								}
							}
						echo '</th>';
					echo '</tr>';
					echo '<tr>';
						echo '<td width = 80%>';
							echo $message["description"];
						echo '</td>';
						echo '<td>';
						echo '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>';
							echo '<a href="#/" class="thumbs-up" value = '.$message["messageID"].'><img src="../img/thumbs-up.png" width="20" height="20" border="0" /></a> ';
							echo '<b><div id = "likes'.$message["messageID"].'" class="inl">'.$message["likes"].'</div></b> ';
							echo '<i> Likes</i>';
						echo '</td>';
						echo '<td>';
							//if(isset($_SESSION ['gebruikerID'])){
								//if ($_SESSION ['medewerker'] == 1) {
									
								//}
							//}
							echo '<i>Datum: </i>';
							echo '<i>'. $message["date"] .'</i>';
						echo '</td>'; 
					echo '</tr>';
				echo '</table>';
			echo '</div>';
		}
	}

	function getName($userID){
		$return = "";

		//connect to MySQL
		$mysqli = makeConnection();

		$query = "SELECT voornaam, achternaam FROM gebruiker WHERE gebruikerID = ?";
		if ($stmt = $mysqli->prepare($query)) {
			$stmt->bind_param ( 's' , $userID );

			$stmt->execute();

			$stmt->bind_result($firstName, $lastName);

			while($stmt->fetch()) {
				$return = $firstName . " " . $lastName;
			}

			$stmt->close();
		}

		return $return;
	}
?>