<?php
include "layout.php";
getHeader ();
?>
<html>
<head>
<style type="text/css">
html, body, #map-canvas {
	height: 500px;
	margin: 0;
	padding: 0;
}
</style>
<script type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBc9ixDh4d1VkszWvjNv3NT_3PssMSYJ3M">
       </script>
<script type="text/javascript">
       var map;
       var circle;
          
       function initialize() {
          geocoder = new google.maps.Geocoder();
          var mapOptions = {
            center: { lat: 52.155018, lng: 5.388193},
             zoom: 7
          };
          map = new google.maps.Map(document.getElementById('map-canvas'),
               mapOptions);
          }

          google.maps.event.addDomListener(window, 'load');

        function getPlaats(lat,lon){
          var plaats;
            $.ajax({
                     async: false,
                     url: "api/aardbevingen_ajax.php",
                     type: "POST",
                     dataType:'text',
                      data: {lat: lat,
                             lng: lon},
                      success: function (response) {
                        plaats = response;
                      }
                  });
              return plaats;
        }

        function datumSelector(){
         initialize();
         var jaartal = document.getElementById("0").options[document.getElementById("0").selectedIndex].text;
         var Bmaand = document.getElementById("1").options[document.getElementById("1").selectedIndex].value;
         var Emaand = document.getElementById("2").options[document.getElementById("2").selectedIndex].value;

         $.ajax({
            url: "api/aardbevingen_ajax.php",
            type: "POST",
            dataType:'text',
            data: { jaartal: jaartal,
                    Bmaand: Bmaand,
                    Emaand: Emaand},
            success: function (response) {

               var result = response.split(",");
               var circle_array = [];
               var length = (result.length-1)/6;

               for(var i = 0; i < length; i++){
                  var lat = result[i*6];
                  var lon = result[(i*6)+1];
                  var mag = result[(i*6)+2];
                  var depth = result[(i*6)+3];
                  var datum = result[(i*6)+4];
                  var id  = result[(i*6)+5];


                  var circle = new google.maps.Circle ({
                        map: map,
                        center: new google.maps.LatLng(lat,lon),
                        radius : Math.exp(mag/1.01-0.13)*1000,
                        fillColor: 'red',
                        fillOpacity: .2,
                        strokeColor: 'white',
                        strokeWeight: .5,
                        clickable: true,
                        id: i
                 });

                circle.info = new google.maps.InfoWindow({
                     maxWidth: 500,
                     content:''
                   });

                  google.maps.event.addListener(circle, 'click', function(ev) {
                      circle.info.close();
                      this.info.setPosition(ev.latLng);
                      this.info.open(map, circle.info);
                      console.log(result[(i*6)+5]);
                      this.info.setContent('<div>'+
                                          '<table class="mapstable">'+
                                          '<tr>'+
                                            '<td>'+
                                            '<b>'+
                                              'Locatie epicentrum'+
                                            '</b>'+
                                            '</td>'+
                                            '<td>'+
                                             getPlaats(result[this.id*6], result[(this.id*6)+1])+
                                            '</td>'+
                                          '</tr>'+
                                          '<tr>'+
                                            '<td>'+
                                            '<b>'+
                                            'Datum'+
                                            '</b>'+
                                            '</td>'+
                                            '<td>'+
                                             result[(this.id*6)+4]+
                                            '</td>'+
                                          '</tr>'+
                                          '<tr>'+
                                            '<td>'+
                                            '<b>'+
                                            'Hevigheid'+
                                            '</b>'+
                                            '</td>'+
                                            '<td>'+
                                             result[(this.id*6)+2]+
                                            '</td>'+
                                          '</tr>'+
                                          '<tr>'+
                                            '<td>'+
                                            '<b>'+
                                            'Diepte'+
                                            '</b>'+
                                            '</td>'+
                                            '<td>'+
                                             result[(this.id*6)+3]+
                                            '</td>'+
                                          '</tr>'+
                                          '<tr>'+
                                           '<td>'+
                                             '<a href=aardbevingen.php?p='+result[(this.id*6)+5]+'>Aardbeving pagina</a>'+
                                           '</td>'+
                                          '</tr>'+
                                          '</table>'+
                                          '</div>');        
                  });
                }
              }
           });
        }
       </script>
</head>
<body onload="(datumSelector(<?php echo ""; ?>))">
	<div id="container">
		<h1>Home pagina</h1>
    <br>
    <div id="map-canvas" style ="border-style:solid;border-width: 5px;"></div>
		<table>
			<tr>
				<td width="20%">
					<h4>Selecteer een jaartal:</h4>
					<br>
					<h4>Selecteer een maand:</h4>
				</td>
				<td width="85%"><select name="jaartal" onchange="datumSelector()"
					id=0>
						<option value='' selected=true;>Selecteer een optie</option>>
						<option value='2014'>2014</option>
						<option value='2015'>2015</option>
				</select><br>
				<br> Start: <br>
				<select name="Bmaand" onchange="datumSelector()" id=1>
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
				</select><br> Eind: <br>
				<select name="Emaand" onchange="datumSelector()" id=2>
						<option value='' selected=true;>Selecteer een optie</option>>
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
			</tr>
		</table>
	</div>
</body>
</html>
<?php
getFooter ();
?>