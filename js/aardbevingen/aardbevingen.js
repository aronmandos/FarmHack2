var currentPage = 0;
var numberOfMessages = 0;
$(document).ready(function() {
	$(".slidingTable").hide();
	$(".show_hide").show();

	$("#zoek").click(function(){
		startDocument(currentPage);	
	});


	$(document).on("click", "#submit", function(){
		var text = document.getElementById("textarea").value;
		var p = getUrlVars()["p"];
		var gebruikerID = document.getElementById("userID").value;

		if(gebruikerID == ''){
			window.location.href = "http://localhost/quakepoint/php/logout.php";
		}
		else{
			$.ajax({
	            url: "api/index_ajax.php",
	            type: "POST",
	            dataType:'text',
	            data: { text: text,
	            		p:p,
	            		gebruikerID: gebruikerID},
        		success: function (response) {
        			//location.reload();
        			console.log("Success");
        			console.log(response);
        			$(".itemconfiguration").prepend(response);
        			$(".message").first().slideDown('slow');

				}		
			});
		}
	});

	var count = getAardbevingenCount();

	$(document).on("click", ".thumbs-up", function(){ 
		var p = $(this).attr('value');
		$.ajax({
            url: "api/index_ajax.php",
            type: "POST",
            dataType:'text',
            data: { comment: p},
    		success: function (response) {
    			$("#likes"+p).empty();
    			$( "#likes"+p).append(response);
    			$(this).removeClass("thumbs-up");
			}	
		});
	});

	$(document).on("click", ".deleteMessage", function(){ 
		var p = $(this).attr('value');
		$.ajax({
            url: "api/index_ajax.php",
            type: "POST",
            dataType:'json',
            data: { deleteMessage: p},
    		success: function (response) {
    			$("#messageBox"+p).slideUp('slow');
			},
		    error: function(xhr, textStatus, errorThrown){
		    	console.error('Request failed!');
		    	console.error(xhr);
		    	console.error(textStatus);
		    }	
		});
	});
});

function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
	vars[key] = value;
	});
	return vars;
}

function startDocument(position){
	var jaartal = document.getElementById("0").options[document.getElementById("0").selectedIndex].text;
	var maand =  document.getElementById("1").value;
	var provincie = document.getElementById("provincie").value;
	var gemeente = document.getElementById("gemeente").value;

	if(position == count-10){
		var l = document.getElementById('browse');
		 $("#browse").empty().append('<table>'+
						'<tr>'+
						'<td width = 40%>'+
							'<a href="#/" class="nextPage" value="previous" onclick = "previousPage()">Vorige pagina</a>'+
						'</td>'+
						'<td>'+
							'<h4>Huidige pagina: <div id = "CurrentPage"></div></h4>'+
						'</td>'+
						'</tr>'+
					'</table>'
					);
		$("#CurrentPage").empty();
   		$( "#CurrentPage").append(position/10+1);
	}
	else if(position == 0){
		$("#browse").empty().append('<table>'+
						'<tr>'+
						'<td width = 40%>'+
							''+
						'</td>'+
						'<td>'+
							'<h4>Huidige pagina: <div id = "CurrentPage"></div></h4>'+
						'</td>'+
						'<td width = 15%>'+
						'<a href="#/" class="nextPage" value="next" onclick = "nextPage()">Volgende pagina</a>'+
						'</td>'+
						'</tr>'+
					'</table>'
					);
		$("#CurrentPage").empty();
   		$( "#CurrentPage").append(position/10+1);
	}
	else{
		var l = document.getElementById('browse');
		 $("#browse").empty().append(
	 		'<table>'+
			'<tr>'+
				'<td width = 40%>'+
					'<a href="#/" class="nextPage" value="previous" onclick = "previousPage()">Vorige pagina</a>'+
				'</td>'+
				'<td>'+
					'<h4>Huidige pagina: <div id = "CurrentPage"></div></h4>'+
				'</td>'+
				'<td width = 15%>'+
					'<a href="#/" class="nextPage" value="next" onclick = "nextPage()">Volgende pagina</a>'+
				'</td>'+
			'</tr>'+
			'</table>'
		);
	}
	$("#pageInfo").empty();
	$.ajax({
        url: "api/index_ajax.php",
        type: "POST",
        dataType:'text',
        data: { position: position,
        		jaartal: jaartal,
            	maand: maand,
            	provincie: provincie,
            	gemeente: gemeente},
    	success: function (response) {
    		$( "#pageInfo" ).append( response);
    		$(".slidingTable").hide();

			$(".show_hide").click(function(){
				var id = $(this).attr('id');
				var divID = id.replace("slide", "slideTable");

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
    	}
	});
	$("#CurrentPage").empty();
    $( "#CurrentPage").append(position/10+1);
}


function previousPage(){
	currentPage -=10;
	startDocument(currentPage);
}

function nextPage(){
	currentPage +=10;
	startDocument(currentPage);
}


function getAardbevingenCount(){
	$.ajax({
		async: false,
        url: "api/index_ajax.php",
        type: "POST",
        dataType:'text',
    	success: function (response) {
    		count = response;
		}
	});
	return count;
}

function getData(p){
	$.ajax({
        url: "api/index_ajax.php",
        type: "POST",
        dataType:'text',
        data: { id: p},
    	success: function (response) {
    		var responeArray = response.split(",");
    		var datum = responeArray[0];
    		var mag = responeArray[1];
    		var gemeente = responeArray[2];
    		var provincie = responeArray[3];
    		var depth = responeArray[4];
    		var lat = responeArray[5];
    		var lng = responeArray[6];
    		var datumArray = datum.split("-"); 
    		var headerEl = document.getElementById("header1");
			headerEl.innerHTML = gemeente+', '+provincie+' '+datum;
    		initialize(lat, lng, datum, mag, gemeente, provincie, depth);
    		var infoEl = document.getElementById("aardbevingInfo");
    		infoEl.innerHTML = '<br>Deze aardbeving heeft plaats gevonden op '+datumArray[2]+' '+getMaand(datumArray[1])+ ' in '+datumArray[0]+ 
    		' in de gemeente '+gemeente+'.'+
    		'<br>De aardbeving had een kracht van '+mag+' op de schaal van richter en was op '+depth+' kilometer diepte.'+
    		'<br>Heeft u schade ondervonden van deze aardbeving? Klik dan <a href=schade.php?p='+p+'>hier</a>';
    		
    		$.ajax({
				url: "api/get_messages.php",
				type: "GET",
				dataType:'text',
				data: { earthquakeID: $("#earthqeakeID").val(),
						startLimit: numberOfMessages},
				success: function (response) {
					$(".itemconfiguration").append(response);
					$(".itemconfiguration").slideDown('fast');

					numberOfMessages += 3;
				},
				error: function(xhr, textStatus, errorThrown){
					console.error('Request failed!');
					console.error(xhr);
					console.error(textStatus);
				}
			});

    		$(window).scroll(function () {
				if ($(window).scrollTop() >= $(document).height() - $(window).height() - 100) {

					$.ajax({
						url: "api/get_messages.php",
						type: "GET",
						dataType:'text',
						data: { earthquakeID: $("#earthqeakeID").val(),
								startLimit: numberOfMessages},
						success: function (response) {
							$(".itemconfiguration").append(response);

							numberOfMessages += 3;
						},
						error: function(xhr, textStatus, errorThrown){
							console.error('Request failed!');
							console.error(xhr);
							console.error(textStatus);
						}
					});
			   }
			});
    	}
	});
}

$(function() {
	var availableTags = [
		"Friesland",
		"Groningen",
		"Drenthe",
		"Overijssel",
		"Gelderland",
		"Flevoland",
		"Noord-Holland",
		"Zuid-Holland",
		"Noord-Brabant",
		"Limburg",
		"Zeeland",
		"Utrecht"
	];

	$( "#provincie" ).autocomplete({
		source: availableTags
	});
});

function getMaand(maand){
 	switch(maand) {
	 	case '01': return 'januari';
	 	break;
	 	case '02': return 'februari';
	 	break;
	 	case '03': return 'maart';
	 	break;
	 	case '04': return 'april';
	 	break;
	 	case '05': return 'mei';
	 	break;
	 	case '06': return 'juni';
	 	break;
	 	case '07': return 'juli';
	 	break;
	 	case '08': return 'augustus';
	 	break;
	 	case '09': return 'september';
	 	break;
	 	case '10': return 'oktober';
	 	break;
	 	case '11': return 'november';
	 	break;
	 	case '12': return 'december';
	 	break;
 	}
}

function initialize(lat, lng, datum, magnitude, gemeente, provincie, depth) {
  var mapOptions = {
    center: { lat: 52.155018, lng: 5.388193},
     zoom: 7
  };

  var map = new google.maps.Map(document.getElementById('map_canvas'),
       mapOptions);

  google.maps.event.addDomListener(window, 'load');

          var circle = new google.maps.Circle ({
                map: map,
                center: new google.maps.LatLng(lat,lng),
                radius : Math.exp(magnitude/1.01-0.13)*1000,
                fillColor: 'red',
                fillOpacity: .2,
                strokeColor: 'white',
                strokeWeight: .5,
                clickable: true,
         });

        circle.info = new google.maps.InfoWindow({
             maxWidth: 500,
             content: 			  '<div>'+
                                  '<table class="mapstable">'+
                                  '<tr>'+
                                    '<td>'+
                                    '<b>'+
                                      'Locatie epicentrum'+
                                    '</b>'+
                                    '</td>'+
                                    '<td>'+
                                     gemeente+', '+provincie+
                                    '</td>'+
                                  '</tr>'+
                                  '<tr>'+
                                    '<td>'+
                                    '<b>'+
                                    'Datum'+
                                    '</b>'+
                                    '</td>'+
                                    '<td>'+
                                     datum+
                                    '</td>'+
                                  '</tr>'+
                                  '<tr>'+
                                    '<td>'+
                                    '<b>'+
                                    'Hevigheid'+
                                    '</b>'+
                                    '</td>'+
                                    '<td>'+
                                     magnitude+
                                    '</td>'+
                                  '</tr>'+
                                  '<tr>'+
                                    '<td>'+
                                    '<b>'+
                                    'Diepte'+
                                    '</b>'+
                                    '</td>'+
                                    '<td>'+
                                     depth+' kilometer'+
                                    '</td>'+
                                  '</tr>'+
                                  '</table>'+
                                  '</div>'
           });
		circle.info.setPosition(new google.maps.LatLng(lat,lng));
		circle.info.open(map);
}