<?php
include "layout.php";

getHeader();

$loggedIn = false;
if (isset ($_SESSION ['gebruikerID'])) {
	$loggedIn = true;
}

if(!$loggedIn){
	header ( 'Location: http://localhost/quakepoint/php/logout.php' );
}
?>
<script type="text/javascript">
	$(document).ready(function() {

		$("#submit").click(function(){
			$.ajax({
	            url: "api/insert_ticket.php",
	            timeout: 5000,
	            type: "GET",
	            dataType:'json',
	            data: { priority: $("#priority option:selected" ).text(),
	                    client: $("#userID").attr("value"),
	                	description: $('textarea#description').val(),
	                	earthquakeID: $("#earthquakeID").attr("value")},
	            success: function (response) {
	            	window.location.href = "profiel.php?action=" + response["ticketID"];
	            },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    	// console.log(errorThrown);
			    }
			});
		});
	});
</script>
<div id="container">
	<h2>Schadeformulier</h2>
	<div id="damageForm">
		<table>
			<tr>
				<th>
					Vraag 1
				</th>
				<th>
					<input type="text" id="questionOne" style="width:100%">
				</th>
			</tr>
			<tr>
				<th>
					Vraag 2
				</th>
				<th>
					<input type="text" id="questionTwo" style="width:100%">
				</th>
			</tr>
			<tr>
				<th>
					Vraag 3
				</th>
				<th>
					<input type="text" id="questionThree" style="width:100%">
				</th>
			</tr>
			<tr>
				<th>
					Vraag 4
				</th>
				<th>
					<input type="text" id="questionFour" style="width:100%">
				</th>
			</tr>
			<tr>
				<th>
					Vraag 5
				</th>
				<th>
					<input type="text" id="questionFive" style="width:100%">
				</th>
			</tr>
			<tr>
				<th>
					Vraag 6
				</th>
				<th>
					<input type="text" id="questionSix" style="width:100%">
				</th>
			</tr>
			<tr>
				<th>
					Prioriteit
				</th>
				<th>
					<select id="priority">
						<option value="hoog">Hoog</option>
						<option value="hoog">Gemiddeld</option>
						<option value="hoog">Laag</option>
					</select>
				</th>
			</tr>
		</table>
		<input type="hidden" id="userID" value=<?php echo '"'.$_SESSION ['gebruikerID'].'"' ?>>
		<input type="hidden" id="earthquakeID" value=<?php echo '"'.$_GET['p'].'"' ?>>
		<textarea placeholder="Hierin kan de klacht gemeld worden" style="width:100%;height:75px;margin:0;padding:0;resize:none;" id="description"></textarea>
		<br>
		<button id="submit" style="float: right; height: 40px; width: 120px">Insturen</button>
		<br>
		<br>
		<br>
		<br>
	</div>
</div>

<?php
getFooter();
?>