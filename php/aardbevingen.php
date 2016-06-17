<?php
	include "layout.php";
	include "database.php";
	getHeader();
?>
<script type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBc9ixDh4d1VkszWvjNv3NT_3PssMSYJ3M">
</script>
<script src="../js/aardbevingen/aardbevingen.js"></script>  
<link rel="stylesheet" type="text/css" media="screen"
	href="../css/autocomplete.css">
<?php
	echo'<div id="container">';
	if(isset($_GET['p'])){
		aardbevingPagina();
	}
	else{
		weergeefAlleAardbevingen();
	}
	echo '</div>'; 
	getFooter();

function weergeefAlleAardbevingen(){
?>	
	<h1>Alle Aardbevingen</h1>

	<table>
			<tr>
				<td width="20%">
					<h4>Selecteer een jaartal:</h4>
					<br>
					<h4>Selecteer een maand:</h4>
				</td>
				<td width="10%"><select name="jaartal" id=0>
						<option value='' selected=true;>Selecteer een optie</option>
						<option value='2014'>2014</option>
						<option value='2015'>2015</option>
				</select><br><br>
				<select name="Bmaand" id=1>
						<option value='' selected=true;>Selecteer een optie</option>
						<option value=1>Januari</option>
						<option value=2>Februari</option>
						<option value=3>Maart</option>
						<option value=4>April</option>
						<option value=5>Mei</option>
						<option value=6>Juni</option>
						<option value=7>Juli</option>
						<option value=8>Augustus</option>
						<option value=9>September</option>
						<option value=10>Oktober</option>
						<option value=11>November</option>
						<option value=12>December</option>
				</select></td>
				<td width ='5%'>
					<h4>Provincie: </h4><br>
					<h4>Gemeente: <h4>
				</td>
				<td>
					<input type= 'text' id="provincie" placeholder="Provincie"><br><br>
					<input type= 'text' id="gemeente" placeholder="Gemeente">
				</td>
				<td>
					<br><br><br><input type = 'submit' value ='Zoeken' id='zoek'>
				</td>
			</tr>
		</table>
	
	<body onload="(startDocument(0))">
	
	<div id="pageInfo"></div>
	<div id="browse"></div>

<?php
}
function aardbevingPagina(){

	if(isset($_SESSION ['gebruikerID'])){
		echo '<input type="hidden" id="userID" value="'.$_SESSION ['gebruikerID'].'">';
		if ($_SESSION ['medewerker'] == 1) {
			echo '<input type="hidden" id="medewerkerID" value="'.$_SESSION ['gebruikerID'].'">';
		}
	}
	echo '<input type="hidden" id="earthqeakeID" value="'.$_GET['p'].'">';
	// connect to MongoDB
	$db = makeConnectionMongo();

	?>
	<body onload="(getData(<?php echo $_GET['p']; ?>))">
	<h2 id="header1"></h2>
	<div id = "map_canvas" style="height: 400px;border-style:solid;border-width: 5px;"></div> 
	<h2>Omschrijving</h2>
	<div id = "aardbevingInfo"></div><br>
	<h2>Comments</h2>
	<div class="left_contentlist">
		<textarea id = "textarea" placeholder= "Voer uw eigen comment in" style="width:100%;height:100%;margin:0;padding:0;resize:none;" rows=2 cols=1></textarea>
		<button id="submit" style="float: right; height: 40px; width: 120px">Comment</button>
		<br><br><br>
		<table></table>
		<div class="itemconfiguration" style="display: none;">
		</div>
	</div>
	<style>
	.inl {
	    display: inline;
	}
	</style>
	<?php
}	

function getNaam($id){
	$mysqli = makeConnection();
    $query = "SELECT voornaam, achternaam FROM gebruiker WHERE gebruikerID = ?";
    $voornaam='';
    $achternaam='';

          if ($stmt = $mysqli->prepare($query)) {
             $stmt->bind_param ( 's' , $id );
             $stmt->execute();
             $stmt->bind_result($voornaam, $achternaam);
             while ($stmt->fetch()) {
              $voornaam = $voornaam;
              $achternaam = $achternaam;
            }
           $stmt->close();
          }
      return $voornaam.' ' .$achternaam;
}
?>