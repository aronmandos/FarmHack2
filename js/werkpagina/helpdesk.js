$(document).ready(function() {
	$(".slidingTable").hide();
	$(".show_hide").show();

    if ($("#actie").attr("value").length != 0){
		$('#slideTable' + $("#actie").attr("value")).slideDown('fast');
		$('#slideLink' + $("#actie").attr("value")).attr("src", "../img/prev.png");
		$('html, body').animate({scrollTop:$('#slideLink' + $("#actie").attr("value")).position().top}, 'slow');
	}
	else{
		$('.slidingTable:first').slideDown('fast');
		$('.slidingTable:first').attr("src", "../img/prev.png");
	}

	$(".show_hide").click(function(){
		var id = $(this).attr('id');
		var divID = id.replace("slideLink", "slideTable");

		if($("#" + divID).is(':visible')){
			$(this).attr("src", "../img/next.png");
			$("#" + divID).slideUp('fast');
		}
		else{
			$(".slidingTable").slideUp('fast');
			$(".show_hide").attr("src", "../img/next.png");
			$(this).attr("src", "../img/prev.png");
			$("#" + divID).slideDown('fast');
		}
	});

	$("#toolButton").click(function(){
		$("#workbox").slideDown('slow');
	});

	$(".getHelpdeskTicket").click(function(){
		var ticket = $(this).attr("value");
		var client = $("#client" + ticket).attr("value");
		$("#container").empty();
		$("#container").append('<div id="toolbar"></div>');
		$("#container").append('<div id="userInfo" class="workbox" style="display : none;"></div>');
		$("#container").append('<div id="ticketInfo" class="workbox" style="display : none;"></div>');
		$("#container").append('<div id="changeStatus" class="workbox" style="display : none;"></div>');
		var userInfoButton = buildTool("Gebruikersinfo", "userInfo", ticket, client);
    	var ticketButton = buildTool("Ticketinfo", "ticketInfo", ticket, client);
    	var changeStatusButton = buildTool("Verander Status", "changeStatus", ticket, client);
    	var buttonArray = [];
    	buttonArray.push(userInfoButton, ticketButton, changeStatusButton);
    	$("#toolbar").append(buildToolMenu(buttonArray));
    	slideDownWorkbox("userInfo", ticket, client, false);
	});
});

function buildToolMenu(buttonArray){
	html = '';
	html += '<table><tr>'
	$.each(buttonArray, function(i, val){
		html += 
		'<th>' +
			val +
		'</th>';
	});
	html += '</tr>';
	return html;
}

function buildTool(name, id, ticket, client){
	html = '';
	html += '<button type="button" onclick="slideDownWorkbox(\'' + id + '\', \'' + ticket + '\', \'' + client + '\', false)" style="cursor:pointer;">' + name + '</button>';
	return html;
}

function slideDownWorkbox(id, ticket, client, newStatus){
	if($("#" + id).is(':visible')){
	}
	else{
		$(".workbox:not(#" + id + ")").slideUp();

		$.ajax({
            url: "api/get_ticket.php",
            timeout: 5000,
            type: "GET",
            dataType:'json',
            data: { ticket: ticket,
                    client: client},
            success: function (response) {
            	switch (id) {
				    case "userInfo":
				    	$("#userInfo").empty();
				    	$("#userInfo").append(buildUserTable(response));
				    	break;
				    case "ticketInfo":
				    	$("#ticketInfo").empty();
				    	$("#ticketInfo").append(buildTicketTable(response, newStatus)); 
				    	break;
				    case "changeStatus":
				    	$("#changeStatus").empty();
				    	$("#changeStatus").append(buildChangeStatus(response));
				    	checkSelectedOption();
				    	break;
				}
				$("#" + id).slideDown();
            },
		    error: function(xhr, textStatus, errorThrown){
		    	console.error('Request failed!');
		    	console.error(xhr);
		    	console.error(textStatus);
		    }
		});
	}
}

function buildUserTable(array){
	var html = '';
	html += 
	'<table>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Naam</b>' +
			'</td>' +
			'<td id="name">' +
				array["client"]["firstName"] + ' ' + array["client"]["lastName"] +
			'<td>' +
		'</tr>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Emailadres</b>' +
			'</td>' +
			'<td id="email">' +
				array["client"]["email"] +
			'<td>' +
		'</tr>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Adres</b>' +
			'</td>' +
			'<td id="adres">' +
				array["client"]["street"] +
				' ' +
				array["client"]["houseNumber"] +
				'<br>' +
				array["client"]["postalCode"] +
				' ' +
				array["client"]["city"] +
				'<br>' +
				array["client"]["province"] +
				' Nederland' +
			'<td>' +
		'</tr>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Huistype</b>' +
			'</td>' +
			'<td id="typeHouse">' +
				array["client"]["typeHouse"] +
			'<td>' +
		'</tr>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Bouwjaar</b>' +
			'</td>' +
			'<td id="yearOfBuild">' +
				array["client"]["yearOfBuild"] +
			'<td>' +
		'</tr>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Rekeningnummer</b>' +
			'</td>' +
			'<td id="bankNumber">' +
				array["client"]["bankNumber"] +
			'<td>' +
		'</tr>' +
	'</table>';
	return html;
}

function buildTicketTable(array, newStatus){
	var html = '';
	html += 
	'<table>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Huistype</b>' +
			'</td>' +
			'<td>' +
				array["client"]["typeHouse"] +
			'</td>' +
		'</tr>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Bouwjaar</b>' +
			'</td>' +
			'<td>' +
				array["client"]["yearOfBuild"] +
			'</td>' +
		'</tr>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Status</b>' +
			'</td>' +
			'<td>';
				if(newStatus){
					html += '<p style="color : red;">' + array["status"][array["ticket"]["statusID"]] + '</p>';
				}
				else{
					html += array["status"][array["ticket"]["statusID"]];
				}
			html +=
			'</td>' +
		'</tr>';
		if(array["status"][array["ticket"]["statusID"]] == "Afspraak aangemaakt"){
			html +=
			'<tr>' +
				'<td width="20%">' +
					'<b>Datum afspraak</b>' +
				'</td>' +
				'<td>';
					if(newStatus){
						html += '<p style="color : red;">' + array["ticket"]["appointmentDate"] + ' <b>om</b> ' + array["ticket"]["appointmentTime"]; + '</p>';
					}
					else{
						html += array["ticket"]["appointmentDate"] + ' <b>om</b> ' + array["ticket"]["appointmentTime"];
					}
				html +=
				'</td>' +
			'</tr>';
		}
		if(array["ticket"]["solution"].length > 0 && array["ticket"]["compensation"].length > 0){
			html +=
			'<tr>' +
				'<td width="20%">' +
					'<b>Oplossing</b>' +
				'</td>' +
				'<td>';
					if(newStatus && array["status"][array["ticket"]["statusID"]] == "Oplossing aangeboden"){
						html += '<p style="color : red;">' + array["ticket"]["solution"] + '</p>';
					}
					else{
						html += array["ticket"]["solution"];
					}
				html +=
				'</td>' +
			'</tr>' +
			'<tr>' +
				'<td width="20%">' +
					'<b>Schadebedrag</b>' +
				'</td>' +
				'<td>';
					if(newStatus && array["status"][array["ticket"]["statusID"]] == "Oplossing aangeboden"){
						html += '<p style="color : red;">' + '€' + array["ticket"]["compensation"] + '</p>';
					}
					else{
						html += '€' + array["ticket"]["compensation"];
					}
				html +=
				'</td>' +
			'</tr>';
		}
		html +=
		'<tr>' +
			'<td width="20%">' +
				'<b>Datum ingediend</b>' +
			'</td>' +
			'<td>' +
				array["ticket"]["date"] +
			'</td>' +
		'</tr>' +
		'<tr>' +
			'<td width="20%">' +
				'<b>Ticket omschrijving</b>' +
			'</td>' +
			'<td>' +
				array["ticket"]["description"] +
			'</td>' +
	'</table>';

	if(array["status"][array["ticket"]["statusID"]] == "Afspraak aangemaakt"){

	}
	return html;
}

function buildChangeStatus(response){
	var html = '';
	var options = '';

	var array = [];
	array.push("Afwijzen", "Afspraak inplannen", "Oplossing bieden", "Uitbetalen", "Ticket sluiten");
	$.each(array, function(i, val){
		options += '<option value="' + val + '">' + val + '</option>';
	})

	html += 
	'<table>' +
		'<tr>'+ 
			'<th>' +
				'<select id="statusSelect">' +
					'<option selected disabled>Selecteer een actie</option>' +
					options +
				'</select>' +
			'</th>' +
		'</tr>' +
		'<tr>'+ 
			'<th>' +
				'<div id="expandStatus" style="display:none;"></div>' +
			'</th>' +
		'</tr>' +
		'<tr>' +
			'<th>' +
				'<textarea style="width:100%;height:100%;margin:0;padding:0;resize:none;" rows=1 cols=1 placeholder="Omschrijf hier de statusverandering"></textarea>' +
			'</th>' +
		'</tr>' + 
		'<tr>'+ 
			'<th>' +
				'<button type="button" onclick="updateTicket()">Opslaan</button>' +
			'</th>' +
		'</tr>' +  
	'</table>' +
	'<input type="hidden" id="ticketID" value="' + response["ticket"]["ticketID"] + '">' + 
	'<input type="hidden" id="userID" value="' + response["client"]["clientID"] + '">' + 
	'<input type="hidden" id="userBankNumber" value="' + response["client"]["bankNumber"] + '">' +
	'<input type="hidden" id="userFirstName" value="' + response["client"]["firstName"] + '">' +
	'<input type="hidden" id="userLastName" value="' + response["client"]["lastName"] + '">';
	return html;
}

function checkSelectedOption(){
	$("#statusSelect").change(function() {
		switch($(this).val()){
			case "Afspraak inplannen":
				$("#expandStatus").slideUp("slow", function(){
					$("#expandStatus").empty();
		    		$("#expandStatus").append('<input id="datetime" type="text" placeholder="Datum"><br><input id="calendar" type="text">');
		    		$('#calendar').datetimepicker({
		    			minDate:0,
					  	format:'d-m-Y H:i',
					  	inline:true,
					  	lang:'nl',
					  	minTime:'08:00',
					  	maxTime:'17:00',
					  	onSelectDate:function(ct,$i){
							$("#datetime").val(ct.dateFormat('d/m/Y H:i'))
						},
						onSelectTime:function(ct,$i){
							$("#datetime").val(ct.dateFormat('d/m/Y H:i'))
						}
					});

		    		$("#expandStatus").slideDown("slow");
				});
				
				break;
			case "Oplossing bieden":
				$("#expandStatus").slideUp("slow", function(){
					$("#expandStatus").empty();
					$("#expandStatus").append(
						'<textarea id="solution" style="width:100%;height:100%;margin:0;padding:0;resize:none;" rows=1 cols=1 placeholder="Omschrijf hier de oplossing"></textarea>' +
						'<textarea id="compensation" style="width:100%;height:100%;margin:0;padding:0;resize:none;" rows=1 cols=1 placeholder="Schadebedrag"></textarea>');
					$("#expandStatus").slideDown("slow");
				});
				break;
			case "Uitbetalen":
				$("#expandStatus").slideUp("slow", function(){
					$("#expandStatus").empty();
					
					$("#expandStatus").append(
						'<p>' + $("#userFirstName").val() + ' ' + $("#userLastName").val() + ' wil het bedrag op rekeningnummer ' + $("#userBankNumber").val() + ' uitbetaald krijgen.</p>');
					$("#expandStatus").slideDown("slow");
				});
				break;
			default:
				$("#expandStatus").slideUp("slow");
    			$("#expandStatus").empty();
		}
	});
}

function updateTicket(){
	var change = $("#statusSelect").val();

	switch (change) {
	    case "Afwijzen":
	    	$.ajax({
		        url: "api/update_ticket.php",
		        timeout: 5000,
		        type: "GET",
		        dataType:'json',
		        data: { ticket: $("#ticketID").val(),
		            	action: "reject"},
		        success: function (response) {
		        	slideDownWorkbox("ticketInfo", $("#ticketID").val(), $("#userID").val(), true);
		        },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    }
			});
	    	break;
	    case "Afspraak inplannen":
	    	var date = $("#datetime").val().replace('/','-').replace('/','-');

			$.ajax({
		        url: "api/update_ticket.php",
		        timeout: 5000,
		        type: "GET",
		        dataType:'json',
		        data: { ticket: $("#ticketID").val(),
		                date: date,
		            	action: "appointment"},
		        success: function (response) {
		        	slideDownWorkbox("ticketInfo", $("#ticketID").val(), $("#userID").val(), true);
		        },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    }
			});
	    	break;
	    case "Oplossing bieden":
	    	var solution = $("#solution").val();
	    	var compensation = $("#compensation").val();

	    	$.ajax({
		        url: "api/update_ticket.php",
		        timeout: 5000,
		        type: "GET",
		        dataType:'json',
		        data: { ticket: $("#ticketID").val(),
		                solution: solution,
		                compensation: compensation,
		            	action: "solution"},
		        success: function (response) {
		        	slideDownWorkbox("ticketInfo", $("#ticketID").val(), $("#userID").val(), true);
		        },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    }
			});
	    	break;
    	case "Uitbetalen":
    		$.ajax({
		        url: "api/update_ticket.php",
		        timeout: 5000,
		        type: "GET",
		        dataType:'json',
		        data: { ticket: $("#ticketID").val(),
		            	action: "pay"},
		        success: function (response) {
		        	slideDownWorkbox("ticketInfo", $("#ticketID").val(), $("#userID").val(), true);
		        },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    }
			});
	    	break;
	    case "Ticket sluiten":
    		$.ajax({
		        url: "api/update_ticket.php",
		        timeout: 5000,
		        type: "GET",
		        dataType:'json',
		        data: { ticket: $("#ticketID").val(),
		            	action: "reject"},
		        success: function (response) {
		        	slideDownWorkbox("ticketInfo", $("#ticketID").val(), $("#userID").val(), true);
		        },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    }
			});
	    	break;
	    default:
	    	break;
	}
}