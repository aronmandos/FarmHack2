/**
 * Created by aron on 17-6-16.
 */


// viewmodel
var vm = function() {
    self = this;

    self.POIs = ko.observableArray([]);
    self.geocoder = new google.maps.Geocoder();
    self.mapOptions = {
        center: { lat: 51.316207, lng: 5.181078},
        zoom: 16,
        mapTypeId: google.maps.MapTypeId.SATELLITE
    };
    self.map= new google.maps.Map(document.getElementById('map-canvas'), self.mapOptions);
    self.field1 = [
        {lat: 51.318078, lng: 5.182258},
        {lat: 51.316804, lng: 5.178557},
        {lat: 51.316549, lng: 5.178772},
        {lat: 51.316422, lng: 5.178396},
        {lat: 51.314504, lng: 5.179780},
        {lat: 51.315805, lng: 5.183771},
        {lat: 51.318078, lng: 5.182258}
    ];

    self.init = function() {
        console.debug("init", self.map, self.field1);

        self.addTractorToMap(self.map, {lat: 51.316804, lng: 5.178557}, 'Hello World');
        var fields = [
            self.addFieldToMap(self.map, self.field1)
        ];
        self.addFieldClickListeners(fields, self.map);

        setInterval(self.moveDot, 1000/self.movesPerSecond);
    };

    self.addTractorToMap = function(map, location, name) {
        "use strict";
        self.tractor = new google.maps.Marker({
            position: location,
            map: map,
            title: name
        });
    };

    self.addFieldToMap = function(map, fieldPoly) {
        "use strict";
        var field = new google.maps.Polygon({
            map: map,
            paths: fieldPoly,
            strokeColor: '#FF0000 ',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000 ',
            fillOpacity: 0.35
        });
        field.setMap(map);
        return field;
    };

    self.initMapClickListener = function(map, handlers) {
        console.debug("initListener", map, handlers);
        map.addListener('click', function(e) {
            console.debug("mapclick", e, handlers);
            for (i = 0; i < handlers.length; i++) {
                handlers[i].function(map, e, handlers[i].data);
            }
        });

    };
    self.addFieldClickListeners = function(fields, map) {
        "use strict";
        for (var i = 0; i < fields.length; i++) {
            self.addFieldClickListener(fields[i], [
                {function: self.FieldClickHandler, data: {map: map}}
            ]);
        }
    };
    self.addFieldClickListener = function(field, handlers) {
        "use strict";
        console.debug("fieldListener", field, handlers);
        field.addListener('click', function(e) {
            console.debug("fieldclick1", e, handlers);
            for (var i = 0; i < handlers.length; i++) {
                handlers[i].function(field, e, handlers[i].data);
            }
        });
    };

    self.FieldClickHandler = function(field, event, data) {
        console.debug("fieldClick2", map, data);
        "use strict";
        var map = data.map;
        if(self.pointInField(event.latLng, field)){
            self.addPoiToMap(map, event.latLng);
        }
    };

    self.addPoiToMap = function(map, point) {
        "use strict";
        self.POIs.push(new google.maps.Marker({
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
        }));
    };
    self.pointInField = function(point, field) {
        "use strict";
        return google.maps.geometry.poly.containsLocation(point, field);
    };


    self.movesPerSecond = 4;
    self.moveSpeed = 0.0001;
    self.dotX = ko.observable(5.178557);
    self.dotY = ko.observable(51.316804);
    self.directionX = ko.observable(0);
    self.directionY = ko.observable(0);

    console.debug(self.dotX());

    self.goright = function() {
        self.directionX(self.moveSpeed);
        self.directionY(0);
    };
    self.goleft = function() {
        self.directionX(-1*self.moveSpeed);
        self.directionY(0);
    };
    self.goup = function() {
        self.directionX(0);
        self.directionY(self.moveSpeed);
    };
    self.godown = function() {
        self.directionX(0);
        self.directionY(-1*self.moveSpeed);
    };

    self.marge= 0.00015;

    self.moveDot = function() {
        for (i = 0; i < self.POIs().length; i++) {
            if(self.POIs()[i].position.lng() > (self.dotX()-self.marge) && self.POIs()[i].position.lng() < (self.dotX()+self.marge)){
                if(self.POIs()[i].position.lat() > (self.dotY()-self.marge) && self.POIs()[i].position.lat() < (self.dotY()+self.marge)) {
                    window.alert("Ik maak nu een foto! op veld 1.");
                }
            }
        }
        self.dotX(self.dotX() + self.directionX());
        self.dotY(self.dotY() + self.directionY());
        var newLatLng = new google.maps.LatLng(self.dotY(), self.dotX());
        self.tractor.setPosition(newLatLng);
        //console.debug("testmove", self.POIs());
    };


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
    self.init();

};

google.maps.event.addDomListener(window, 'load');
ko.applyBindings(new vm());
