/**
 * Created by aron on 17-6-16.
 */

var initPage = function() {
    "use strict";
    google.maps.event.addDomListener(window, 'load');
    ko.applyBindings(new vm());
}

function Vehicle() {
    "use strict";
    var self = this;

    self.drawsPerSecond = 4;
    self.moveDistancePerSecond = ko.observable(0.0001);
    self.speedIncrementSize = 0.00005;
    self.dotX = ko.observable(5.178557);
    self.dotY = ko.observable(51.316804);
    self.directionX = ko.observable(0);
    self.directionY = ko.observable(0);
    self.marge = 0.00015;
    self.markers = [];
    self.POIs = ko.observableArray([]);

    self.init = function() {
        setInterval(self.timePassed(), 1000/self.movesPerSecond); //start time
    };

    //direction functions
    self.goright = function() {
        self.setDirection(self, 1, 0);
    };
    self.goUpRight = function() {
        self.setDirection(self, 0.5, 0.5);
    };
    self.goDownRight = function() {
        self.setDirection(self, 0.5, -0.5);
    };
    self.goUpLeft = function() {
        self.setDirection(self, -0.5, 0.5);
    };
    self.goleft = function() {
        self.setDirection(self, -1, 0);
    };
    self.goDownLeft = function() {
        self.setDirection(self, -0.5, -0.5);
    };
    self.goup = function() {
        self.setDirection(self, 0, 1);
    };
    self.godown = function() {
        self.setDirection(self, 0, -1);
    };

    self.turnRight = function() {
        self.turnDirection(self, 0.1, false);
    };

    self.turnLeft = function() {
        self.turnDirection(self, 0.1, true);
    };

    self.turnDirection = function(vehicle, turningFactor, turningLeft) {
        if (!turningFactor) {
            turningFactor = 0.1;
        }
        var directionMultiplier = 1;
        if (turningLeft) {
            directionMultiplier = -1;
        }

        //some basic optimisation
        var multipliedTurningFactor = turningFactor * directionMultiplier;
        var dirYMinusMulFactor = vehicle.directionY - multipliedTurningFactor;
        var dirYPlusMulFactor = vehicle.directionY + multipliedTurningFactor;
        var dirXMinusMulFactor = vehicle.directionX - multipliedTurningFactor;
        var dirXPlusMulFactor = vehicle.directionX + multipliedTurningFactor;

        if(vehicle.directionX >= 1) { // exact right
            self.setDirection(vehicle, (vehicle.directionX - turningFactor), dirYMinusMulFactor);// X same for both left and right turn
        } else if(vehicle.directionX <= -1) { //exact left
            self.setDirection(vehicle, (vehicle.directionX + turningFactor), dirYPlusMulFactor);// X same for both left and right turn
        } else if (vehicle.directionY() > 0) {//going some degree up
            if (vehicle.directionX > 0) { // up-right
                self.setDirection(vehicle, dirXPlusMulFactor, dirYMinusMulFactor);
            } else if(vehicle.directionX == 0) { //up
                self.setDirection(vehicle, dirXPlusMulFactor, (vehicle.directionY - turningFactor)); // Y same for both left and right turn
            } else { //up-left
                self.setDirection(vehicle, dirXPlusMulFactor, dirYPlusMulFactor);
            }
        } else { //going some degree down
            if (vehicle.directionX > 0) { //down-right
                self.setDirection(vehicle, dirXMinusMulFactor, dirYMinusMulFactor);
            } else if(self.directionX == 0) { //down
                self.setDirection(vehicle, dirXMinusMulFactor, vehicle.directionY + turningFactor); // Y same for both left and right turn
            } else { // down-left
                self.setDirection(vehicle, dirXMinusMulFactor, dirYPlusMulFactor);
            }
        }
    };

    self.setDirection = function(vehicle, dirX, dirY) {
        vehicle.directionX(dirX);
        vehicle.directionY(dirY);
    };

    self.speedUp = function() {
        self.speedChange(self, self.speedIncrementSize);
    };
    self.speedDown = function() {
        self.speedChange(self, -self.speedIncrementSize);
    };

    self.speedChange = function(vehicle, speedAmount) {
        vehicle.moveDistancePerSecond(vehicle.moveDistancePerSecond() + speedAmount);
    };


    self.timePassed = function() {
        var lng = self.dotX();
        var lat = self.dotY();

        var inPOIs = self.inPOIs(self.POIs(), lng, lat, self.marge);
        for (var i = 0; i < inPOIs.length; i++) {
            self.handleInPOI(inPOIs[i]);
        }

        self.move(self);

        self.redraw(self);
    };

    self.move = function(vehicle) {
        var moveDistance = vehicle.moveDistancePerSecond() / vehicle.drawsPerSecond;
        vehicle.dotX(vehicle.dotX() + (vehicle.directionX() * moveDistance));
        vehicle.dotY(vehicle.dotY() + (vehicle.directionY() * moveDistance));
    };

    self.redraw = function(vehicle) {
        for (var i = 0; i < vehicle.markers.length; i++) {
            vehicle.markers[i].setPosition(vehicle.dotY(), vehicle.dotX());
        }
    };

    self.addMarker = function(marker) {
        vehicle.markers.push(marker);
    };

    self.inPOIs = function(POIs, lng, lat, marge){
        var inPOIs = [];
        for (var i = 0; i < POIs.length; i++) {
            var POI = POIs[i];
            var POIlng = POI.position.lng();
            var POIlat = POI.position.lat();
            if(POIlng > (lng-marge) && POIlng < (lng+marge)){
                if(POIlat > (lat-marge) && POIlat < (lat+marge)) {
                    inPOIs.push(POI);
                }
            }
        }
        return inPOIs;
    };

    self.handleInPOI = function(POI) {
        window.alert("Ik maak nu een foto! op veld 1.");
    };

    self.getLocation = function() {
        return {lat: self.dotY , lng: self.dotX};
    }
    self.init();
}

function VehicleMarker(map, vehicle, name) {
    "use strict";
    var self = this;
    self.map = map;
    self.vehicle = vehicle;
    self.name = name;
    self.gMarker = new google.maps.Marker({
        position: new google.maps.LatLng(self.vehicle.getLocation().lat,self.vehicle.getLocation().lng),
        map: self.map.map,
        title: self.name,
        icon : {
            url : 'fourbyfour.png',
            fillOpacity : 1,
            strokeColor : 'white',
            strokeWeight : .5,
            scale : 10
        }
    });

    self.setPosition = function(lat, lng) {
        gMarker.setPosition(new google.maps.LatLng(lat, lng));
    };

}

function MapData() {
    "use strict";
    var self = this;

    self.geocoder = new google.maps.Geocoder();
    self.mapOptions = {
        center: { lat: 51.316207, lng: 5.181078},
        zoom: 16,
        mapTypeId: google.maps.MapTypeId.SATELLITE
    };
    self.map= new google.maps.Map(document.getElementById('map-canvas'), self.mapOptions);

    self.POIs = ko.observableArray([]);
    self.Fields = ko.observableArray([]);

    self.init = function() {
    };

    self.addPolygonToMap = function(map, polygonCoordinates) {
        "use strict";
        var poly = new google.maps.Polygon({
            map: map,
            paths: polygonCoordinates,
            strokeColor: '#FF0000 ',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000 ',
            fillOpacity: 0.35
        });
        poly.setMap(map);
        return poly;
    };

    self.addPolygonClickListener = function(polygon, handlers) {
        "use strict";
        console.debug("polygonListener", polygon, handlers);
        polygon.addListener('click', function(e) {
            console.debug("polygonclick1", e, handlers);
            for (var i = 0; i < handlers.length; i++) {
                handlers[i].function(polygon, e, handlers[i].data);
            }
        });
    };

    self.FieldClickHandler = function(field, event, data) {
        "use strict";
        var map = data.map;
        if(self.pointInPolygon(event.latLng, field)){
            self.addPoiToMap(event.latLng);
        }
    };

    self.addPoiToMap = function(point) {
        "use strict";
        var map = self.map;
        self.POIs.push(new FieldPOIMarker(map, point));
    };

    self.pointInPolygon = function(point, polygon) {
        "use strict";
        return google.maps.geometry.poly.containsLocation(point, polygon);
    };

}

function FieldPOIMarker(map, point) {
    "use strict";
    var self = this;

    self.POI = new google.maps.Marker({
        position: point,
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
}

function Field(name, polygon) {
    "use strict";
    var self = this;

    self.polygon = polygon;
    self.name = name;

    //TODO find out if this is usefull
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
}

// viewmodel
var vm = function() {
    var self = this;

    self.init = function() {
        console.debug("init_vm");
        self.map = new MapData();
        self.tractor = new Vehicle();
        self.marker = new VehicleMarker(self.map, self.tractor, "Tractor");
        self.fields = self.getFields();

        self.fields = self.addFieldsToMap(self.map, self.fields);

        for (var i = 0; i < self.fields.length; i++) {
            self.map.addPolygonClickListener(self.fields[i].onMap, [{function: self.FieldClickHandler, data: {map: self.map}}]);
        }
        self.map.addPolygonClickListener()
    };


    self.getFields = function() {
        //TODO placeholder fields
        return [
            new Field("Field1", [
                {lat: 51.318078, lng: 5.182258},
                {lat: 51.316804, lng: 5.178557},
                {lat: 51.316549, lng: 5.178772},
                {lat: 51.316422, lng: 5.178396},
                {lat: 51.314504, lng: 5.179780},
                {lat: 51.315805, lng: 5.183771},
                {lat: 51.318078, lng: 5.182258}
            ]),
            new Field("Field2", [
                {lat: 51.332536, lng: 5.171928 },
                {lat: 51.332891, lng: 5.173665 },
                {lat: 51.330090, lng: 5.174586 },
                {lat: 51.329875, lng: 5.172955 },
                {lat: 51.332536, lng: 5.171928 }
            ])
        ];
    };

    self.addFieldsToMap = function(map, fields) {
        for (var i = 0; i < fields.length; i++) {
            fields[i].onMap = map.addPolygonToMap(map, fields[i].polygon);
        }
        return fields;
    };

    self.FieldClickHandler = function(field, event, data) {
        "use strict";
        var map = data.map;
        if(self.pointInPolygon(event.latLng, field)){
            self.addPoiToMap(map, event.latLng);
            console.debug("fieldClickHandler", event.latLng);
        }
    };

    self.addPoiToMap = function(map, point) {
        "use strict";
        tractor.POIs.push(new FieldPOIMarker(map, point));
    };

    self.pointInPolygon = function(point, polygon) {
        "use strict";
        return google.maps.geometry.poly.containsLocation(point, polygon);
    };

    self.init();

};


initPage();