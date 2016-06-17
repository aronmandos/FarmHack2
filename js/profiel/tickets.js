$(document).ready(function() {
	$.ajax({
	    url: "api/get_own_tickets.php",
	    timeout: 5000,
	    type: "GET",
	    dataType:'json',
	    data: {client: $("#userID").attr("value")},
	    success: function (response) {
	    	$("#container").append(buildTicketTable(response));

	    	if($("#newTicket").length > 0){
	    		$('#ticketTable' + $("#newTicket").val()).slideDown('fast');
				$('#slideLink' + $("#newTicket").val()).attr("src", "../img/prev.png");
				$('html, body').animate({scrollTop:$('#slideLink' + $("#newTicket").val()).position().top}, 'slow');
	    	}
	    },
	    error: function(xhr, textStatus, errorThrown){
	    	console.error('Request failed!');
	    	console.error(xhr);
	    	console.error(textStatus);
	    	console.log(errorThrown);
	    }
	});

	$(document).on("click", ".showTicketTable", function(){
		var id = $(this).attr('id');
		var divID = id.replace("slideLink", "ticketTable");

		if($("#" + divID).is(':visible')){
			$(this).attr("src", "../img/next.png");
			$("#" + divID).slideUp();
		}
		else{
			$(".ticketTable").slideUp();
			$("showTicketTable").attr("src", "../img/next.png");
			$(this).attr("src", "../img/prev.png");
			$("#" + divID).slideDown();
		}
	});

	$(document).on("mouseover", ".ticketTable", function() {
		var id = this.id.replace("ticketTable", "");
		$("#approveDate" + id).slideDown("slow");
	});

	$(document).on("mouseover", ".ticketTable", function() {
		var id = this.id.replace("ticketTable", "");
		$("#approveSolution" + id).slideDown("slow");
	});
});

function buildTicketTable(array){
	var html = '';

	$.each(array, function(i, ticket) {
		html +=
		'<table>' +
			'<thead>' +
				'<tr>' +
					'<th width="45%">';
						if($("#newTicket").val() != ticket["ticketID"]){
							html += 'Status - ' + ticket["status"];
						}
						else{
							html += '<p style="color : red;">Status - ' + ticket["status"] + '</p>';
						}
					html += 
					'</th>' +
					'<th>';
						if($("#newTicket").val() != ticket["ticketID"]){
							console.log()
							html += 'Ticket ingediend - ' + ticket["date"];
						}
						else{
							html += '<p style="color : red;">Ticket ingediend - ' + ticket["date"] + '</p>';
						}
					html += 
					'</th width="45%">' +
					'<th width="10%">' +
						'<img src="../img/next.png" id="slideLink' + ticket["ticketID"] + '" class="showTicketTable">' +
					'</th>' +
				'</tr>' +
			'</thead>' +
		'</table>' +
		'<div id="ticketTable' + ticket["ticketID"] + '" class="ticketTable" style="display:none;">' +
			'<table>' +
				'<tr>' +
					'<td width="20%">' +
						'<b>Ticket</b>' +
					'</td>' +
					'<td>' +
						ticket["ticketID"] +
					'</td>' +
				'</tr>' +
				'<tr>' +
					'<td width="20%">' +
						'<b>omschrijving</b>' +
					'</td>' +
					'<td>' +
						ticket["description"] +
					'</td>' +
				'</tr>' +
				'<tr>' +
					'<td width="20%">' +
						'<b>Aardbeving</b>' +
					'</td>' +
					'<td>' +
						'<a href="index.php?p=' + ticket["earthquakeID"] + '">Details</a>' + 
					'</td>' +
				'</tr>' +
				'<tr>' +
					'<td width="20%">' +
						'<b>Status</b>' +
					'</td>' +
					'<td>' +
						'<div id="statusID' + ticket["ticketID"] + '">' + ticket["status"] + '</div>'+
					'</td>' +
				'</tr>';
				if(ticket["status"] == "Afspraak aangemaakt" || ticket["status"] == "Afspraak geaccepteerd"){
					html +=
					'<tr>' +
						'<td width="20%">' +
							'<b>Datum afspraak</b>' +
						'</td>' +
						'<td>' +
							'<div id="dateRow' + ticket["ticketID"] + '">'+ 
								'<p>' + ticket["appointmentDate"] + ' <b>om</b> ' + ticket["appointmentTime"] + '</p>';
								if(ticket["status"] == "Afspraak aangemaakt"){
									html += 
									'<div id="approveDate' + ticket["ticketID"] + '" style="display : none;">' + 
										'<br>' + 
										'<a href="#/" id="approveDate/' + ticket["ticketID"] + '" onclick="changeStatus(this)"><img src="../img/checkmark.png" height="30" width="30"></a>'+
										'<img src="../img/empty.png" height="30" width="30">' +
										'<a href="#/" id="rejectDate/' + ticket["ticketID"] + '" onclick="changeStatus(this)"><img src="../img/reject.png" height="30" width="30"></a>'+
									'</div>';
								}
							html +=
							'</div>' +
						'</td>' +
					'</tr>';
				}
				html +=
				'<tr>' +
					'<td width="20%">' +
						'<b>Prioriteit</b>' +
					'</td>' +
					'<td>' +
						ticket["priority"] +
					'</td>' +
				'</tr>' +
				'<tr>' +
					'<td width="20%">' +
						'<b>Datum ingediend</b>' +
					'</td>' +
					'<td>' +
						ticket["date"] +
					'</td>' +
				'</tr>';
				if(ticket["solution"].length > 0){
					html +=
					'<tr>' +
						'<td width="20%">' +
							'<b>Oplossing</b>' +
						'</td>' +
						'<td>';
							if(ticket["status"] == "Oplossing aangeboden"){
								html +=
								'<div id="solutionBox' + ticket["ticketID"] + '">' + 
									'<p>' + ticket["solution"] + '</p>' +
									'<div id="approveSolution' + ticket["ticketID"] + '" style="display : none;">' + 
										'<br>' + 
										'<p>'+ 
											'<a href="#/" id="approveSolution/' + ticket["ticketID"] + '" onclick="changeStatus(this)"><img src="../img/checkmark.png" height="30" width="30"></a>'+
											'<img src="../img/empty.png" height="30" width="30">' +
											'<a href="#/" id="rejectSolution/' + ticket["ticketID"] + '" onclick="changeStatus(this)"><img src="../img/reject.png" height="30" width="30"></a>'+ 
										'</p>' + 
									'</div>' +
								'</div>';
							}
							else {
								html += ticket["solution"];
							}
						html +=
						'</td>' +
					'</tr>';
				}
				if(ticket["compensation"] != 'undefined' && ticket["compensation"].length > 0){
					html+=
					'<tr>' +
						'<td width="20%">' +
							'<b>Schadebedrag</b>' +
						'</td>' +
						'<td>' +
							'€' + ticket["compensation"] +
						'</td>' +
					'</tr>';
				}
				if(ticket["companyID"].length > 0){
					html +=
					'<tr>' +
						'<td width="20%">' +
							'<b>BedrijfID</b>' +
						'</td>' +
						'<td>' +
							ticket["companyID"] +
						'</td>' +
					'</tr>';
				}
				if(ticket["employee"].length > 0){
					html +=
					'<tr>' +
						'<td width="20%">' +
							'<b>Medewerker</b>' +
						'</td>' +
						'<td>' +
							ticket["employee"] +
						'</td>' +
					'</tr>';
				}
				html +=
			'</table>' + 
			'<input type="hidden" id="ticketID" value="' + ticket["ticketID"] + '">' +
			'</div>';
	});
	return html;
}

function changeStatus(id){
	var array = $(id).attr("id").split("/");
	console.log(array[0]);
	console.log(array[1]);

	id = array[0];
	var ticketID = array[1];

	switch(id){
		case "approveDate":
			$.ajax({
		        url: "api/update_ticket.php",
		        timeout: 5000,
		        type: "GET",
		        dataType:'json',
		        data: { ticket: ticketID,
		            	action: id},
		        success: function (response) {
		        	$("#statusID" + ticketID).empty().append("Afspraak geaccepteerd");
		        	$("#approveDate" + ticketID).slideUp("slow", function(){
		        		$("#approveDate" + ticketID).empty();
		        	});
		        },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    }
			});
	    	break;
	    case "rejectDate":
	    	$.ajax({
		        url: "api/update_ticket.php",
		        timeout: 5000,
		        type: "GET",
		        dataType:'json',
		        data: { ticket: ticketID,
		            	action: id},
		        success: function (response) {
		        	$("#statusID" + ticketID).empty().append("Afspraak niet geaccepteerd");
		        	$("#dateRow" + ticketID).slideUp("slow");
		        },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    }
			});
	    	break;
	    case "approveSolution":
	    	$.ajax({
		        url: "api/update_ticket.php",
		        timeout: 5000,
		        type: "GET",
		        dataType:'json',
		        data: { ticket: ticketID,
		            	action: id},
		        success: function (response) {
		        	$("#statusID" + ticketID).empty().append("Oplossing goedgekeurd");
		        	$("#approveSolution" + ticketID).slideUp("slow", function(){
		        		$("#approveSolution" + ticketID).empty();
		        	});
		        },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    }
			});
	    	break;
	    case "rejectSolution":
	    	$.ajax({
		        url: "api/update_ticket.php",
		        timeout: 5000,
		        type: "GET",
		        dataType:'json',
		        data: { ticket: ticketID,
		            	action: id},
		        success: function (response) {
		        	$("#statusID" + ticketID).empty().append("Oplossing afgekeurd");
		        	$("#solutionBox" + ticketID).slideUp("slow");
		        },
			    error: function(xhr, textStatus, errorThrown){
			    	console.error('Request failed!');
			    	console.error(xhr);
			    	console.error(textStatus);
			    }
			});
	    	break;
	}
}