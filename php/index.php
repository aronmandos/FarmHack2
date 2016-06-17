
<html>
<head>
    <style type="text/css">
        html, body, #map-canvas {
            height: 500px;
            margin: 0;
            padding: 0;
        }
    </style>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBc9ixDh4d1VkszWvjNv3NT_3PssMSYJ3M"></script>
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

    </script>
</head>
<body>
<div id="map-canvas" style ="border-style:solid;border-width: 5px;"></div>
</div>
</body>
</html>