var numberOfTickets = 0;
var search = false;
$(document).ready(function() {
	$('#searchBox').bind("enterKey",function(e){
		search = true;
		numberOfTickets = 0;
		$.ajax({
			url: "api/get_new_tickets.php",
			type: "GET",
			dataType:'text',
			data: { startLimit: numberOfTickets,
					search: $("#searchBox").val()},
			success: function (response) {
				$("#newTickets").empty().append(response);
				numberOfTickets = 5;

				$("#suggesstionBox").hide();
			},
			error: function(xhr, textStatus, errorThrown){
				console.error('Request failed!');
				console.error(xhr);
				console.error(textStatus);
			}
		});
	});
	
	$('#searchBox').keyup(function(e){
		if(e.keyCode == 13){
		  $(this).trigger("enterKey");
		}
	});

	$.ajax({
		url: "api/get_new_tickets.php",
		type: "GET",
		dataType:'text',
		data: { startLimit: numberOfTickets},
		success: function (response) {
			$("#newTickets").append(response);
			numberOfTickets = 5;

			$("#suggesstionBox").hide();
		},
		error: function(xhr, textStatus, errorThrown){
			console.error('Request failed!');
			console.error(xhr);
			console.error(textStatus);
		}
	});

	$(document).on("click",".show_hide", function(){
		var id = $(this).attr('id');
		var divID = id.replace("slideLink", "slideTable");

		if($("#" + divID).is(':visible')){
			$(this).attr("src", "../img/next.png");
			$("#" + divID).slideUp();
		}
		else{
			$(".slidingTable").slideUp();
			$(".show_hide").attr("src", "../img/next.png");
			$(this).attr("src", "../img/prev.png");
			$("#" + divID).slideDown();
		}
	});

	$(document).on("click",".getTicket", function(){
		var ticket = $(this).attr('id');
		console.log("ticket is: " + ticket);
		var medewerker = $("#employee").attr("value");
		$.ajax({
            url: "api/assign_ticket.php",
            type: "GET",
            dataType:'json',
            data: { medewerker: medewerker,
                    ticket: ticket},
            success: function (response) {
            	console.log(response);
            	$("#container").empty();
            	var url = window.location.href;
            	url = url.replace("p=Werkpagina#", "p=Helpdesk&a=" + ticket);
                window.location.replace(url);
            },
		    error: function(xhr, textStatus, errorThrown){
		    	console.error('Request failed!');
		    	console.error(xhr);
		    	console.error(textStatus);
		    	// console.log(errorThrown);
		    }
        });
	});

	$(window).scroll(function () {
		if ($(window).scrollTop() >= $(document).height() - $(window).height() - 100) {
			getNewTickets(numberOfTickets + 5);
			numberOfTickets = numberOfTickets + 5;
	   }
	});
});

// $(function() {
// 	$( "#searchBox" ).autocomplete({
// 		source: function (request, response) {
//             $.ajax({
//                 url: "../api/search_ticket.php",
//                 dataType: "json",
//                 success: function (data) {
//                     response(function (response) {
//                         return {
//                             label: response.firstName,
//                             value: response.lastName
//                         }
//                     });
//                 }
//             });
//         }
// 	});
// });

function getNewTickets(number){
	if(search){
		$.ajax({
			url: "api/get_new_tickets.php",
			type: "GET",
			dataType:'text',
			data: { startLimit: number,
					search: $("#searchBox").val()},
			success: function (response) {
				$("#newTickets").append(response);
				console.log("skipnumber: " + number);
			},
			error: function(xhr, textStatus, errorThrown){
				console.error('Request failed!');
				console.error(xhr);
				console.error(textStatus);
			}
		});
	}
	else {
		$.ajax({
			url: "api/get_new_tickets.php",
			type: "GET",
			dataType:'text',
			data: { startLimit: number},
			success: function (response) {
				$("#newTickets").append(response);
			},
			error: function(xhr, textStatus, errorThrown){
				console.error('Request failed!');
				console.error(xhr);
				console.error(textStatus);
			}
		});
	}
}