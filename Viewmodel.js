/**
 * Created by aron on 17-6-16.
 */


var marker_array = ko.observableArray([]);
var marker;
var map;
var circle;
var location;

function initialize() {
    geocoder = new google.maps.Geocoder();
    var mapOptions = {
        center: { lat: 51.316207, lng: 5.181078},
        zoom: 16,
        mapTypeId: google.maps.MapTypeId.SATELLITE
    };
    map = new google.maps.Map(document.getElementById('map-canvas'),
        mapOptions);
    var rectangleCoords = [
        {lat: 51.318078, lng: 5.182258},
        {lat: 51.316804, lng: 5.178557},
        {lat: 51.316549, lng: 5.178772},
        {lat: 51.316422, lng: 5.178396},
        {lat: 51.314504, lng: 5.179780},
        {lat: 51.315805, lng: 5.183771},
        {lat: 51.318078, lng: 5.182258}
    ];
    var parcelRectangle = new google.maps.Polygon({
        map: map,
        paths: rectangleCoords,
        strokeColor: '#FF0000 ',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000 ',
        fillOpacity: 0.35
    });
    parcelRectangle.setMap(map);

    google.maps.event.addListener(parcelRectangle, 'click', function(e) {
        if(google.maps.geometry.poly.containsLocation(e.latLng, parcelRectangle)){

            var icon = new google.maps.Marker({
                position: e.latLng,
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: 'blue',
                    fillOpacity: .2,
                    strokeColor: 'white',
                    strokeWeight: .5,
                    scale: 10
                }
            });
            marker_array.push(icon)
        }
    });
    marker = new google.maps.Marker({
        position: {lat: 51.316804, lng: 5.178557},
        map: map,
        title: 'Hello World!'
    });
}

google.maps.event.addDomListener(window, 'load');


// Here's my data model
var vm = function() {
    self.polygon = [ [ 51.332536, 5.171928 ], [ 51.332891, 5.173665 ], [ 51.330090, 5.174586 ], [ 51.329875, 5.172955 ], [ 51.332536, 5.171928 ] ];
    self.reflectOnPolygon = true;
    self = this;
    self.movesPerSecond = 2;
    self.moveSpeed = 0.0002;
    self.dotX = ko.observable(5.178557);
    self.dotY = ko.observable(51.316804);
    self.directionX = ko.observable(self.moveSpeed);
    self.directionY = ko.observable(-1*(self.moveSpeed));

    console.debug(self.dotX());

    self.goright = function() {
        self.directionX(self.moveSpeed)
        self.directionY(0)
    };
    self.goleft = function() {
        self.directionX(-1*self.moveSpeed)
        self.directionY(0)
    };
    self.goup = function() {
        self.directionX(0)
        self.directionY(self.moveSpeed)
    };
    self.godown = function() {
        self.directionX(0)
        self.directionY(-1*self.moveSpeed)
    };

    self.marge= 0.00015;

    self.moveDot = function() {
        for (i = 0; i < marker_array().length; i++) {
            if(marker_array()[i].position.lng() > (self.dotX()-self.marge) && marker_array()[i].position.lng() < (self.dotX()+self.marge)){
                if(marker_array()[i].position.lat() > (self.dotY()-self.marge) && marker_array()[i].position.lat() < (self.dotY()+self.marge)) {
                    window.alert("Ik maak nu een foto! op veld 1.");
                }
            }
        }
        self.dotX(self.dotX() + self.directionX());
        self.dotY(self.dotY() + self.directionY());
        var newLatLng = new google.maps.LatLng(self.dotY(), self.dotX());
        marker.setPosition(newLatLng);
        console.debug("testmove");
        console.debug(marker_array());
    };
    setInterval(self.moveDot, self.movesPerSecond*1000);

    self.inside = function(point, vs) {
        // ray-casting algorithm based on
        // http://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html

        var x = point[0], y = point[1];

        var inside = false;
        for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
            var xi = vs[i][0], yi = vs[i][1];
            var xj = vs[j][0], yj = vs[j][1];

            var intersect = ((yi > y) != (yj > y))
                && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }

        return inside;
    };
};

ko.applyBindings(new vm());
