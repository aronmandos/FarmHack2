/**
 * Created by MADspace
 */
google.maps.event.addDomListener(window, 'load');

routesLatLng = {'mainRoute':[
	{
		lat : 51.318078,
		lng : 5.182258
	},
	{
		lat : 51.316804,
		lng : 5.178557
	},
	{
		lat : 51.316549,
		lng : 5.178772
	},
	{
		lat : 51.316422,
		lng : 5.178396
	},
	{
		lat:51.3158,
		lng : 5.1760
	},
	{
		lat : 51.3137,
		lng : 5.1775
	},
	{
		lat : 51.314504,
		lng : 5.179780// verkeerde hoek
	},
	{
		lat : 51.315805,
		lng : 5.183771
	},
	{
		lat : 51.318078,
		lng : 5.182258
	}
]};
routes = { 'altRoute' : [ [ 51.332536, 5.171928 ], [ 51.332891, 5.173665 ],
			[ 51.330090, 5.174586 ], [ 51.329875, 5.172955 ],
			[ 51.332536, 5.171928 ] ],'baseRoute':toTupleList(routesLatLng['mainRoute'])};


function PhotoAlbum() {
	this.begin=0; 
	this.size=8;
	this.last=7; 
}
PhotoAlbum.prototype.getAlbumPosition=function() {
	var tuple = $('#displayParcelPartNames option:selected').html().replace(/ /g,'').replace(/\)/g,'').split('(')[1].split(',');
	var latLng = new google.maps.LatLng(tuple[0],tuple[1]);
	
	return latLng; 
}

PhotoAlbum.prototype.getUri=function(index) {
	var newStr = ''+index;
	if (index<10) {
		newStr = '0'+index;
	}
	return $('#displayParcelPartNames option:selected').attr('title').replace('_00','_'+newStr); 
}
PhotoAlbum.prototype.getState=function() {
	var temp = $('table#displayAlbum img').attr('src').replace('.jpg','').split('_');
	return parseInt(temp[temp.length-1]); 
}
PhotoAlbum.prototype.getUriBegin=function() {
	return this.getUri(this.begin);
}
PhotoAlbum.prototype.getUriLast=function() {
	return this.getUri(this.last);
}
PhotoAlbum.prototype.toggle=function() {
	var isOpen = $('#map-canvas:visible').length>0; 
	$('#map-canvas').toggle();
	if (isOpen) {
		$('#displayImage').css('width','800px').css('height','400px');
	} else {
		$('#displayImage').css('width','100px').css('height','50px');
	}
	
}
function RouteRecorder() {
	this.routeName='';
	this.index=-1;
}
function toTupleList(mapList) {
	var ret = []; 
	
	for (var index=0;index<mapList.length;index++) {
		ret.push([mapList[index]['lat'],mapList[index]['lng']]); 
	}
	return ret; 
}
RouteRecorder.prototype.selectRoute=function(routeName) {
	this.routeName=routeName; 
}
RouteRecorder.prototype.reset=function() {
	for (var i = 0; i < store.marker_array().length; i++) {
		  store.marker_array()[i].setMap(null);
	}
	store.marker_array([])
	document.getElementById('displayMarkers').innerHTML=store.toHtml();
	store.lastEvent=null;
	store.lastIcon =null; 
	store.lastObj=null;
}
RouteRecorder.prototype.doStart=function() {
	this.index = 0; 
	store.marker.setPosition(store.marker_array()[0].position)
}
RouteRecorder.prototype.doNext=function() {
	this.index++; 
	if (store.marker_array().length==this.index) {
		this.index = 0; 
	}
	console.log('next=' + this.index + ' ' +store.marker_array()[this.index].position.lng()); 
	store.marker.setPosition(store.marker_array()[this.index].position)
}

function Store() {
	this.mapCanvas = $('#map-canvas')
	
	this.marker_array = ko.observableArray([]);
	this.photoAlbum = new PhotoAlbum(); 
	this.routeRecorder = new RouteRecorder(); 
	this.geocoder = new google.maps.Geocoder();
	this.mapOptions = {
		center : {
			lat : 51.316207,
			lng : 5.181078
		},
		zoom : 16,
		mapTypeId : google.maps.MapTypeId.SATELLITE
	};
	this.map = new google.maps.Map(this.mapCanvas[0],
			this.mapOptions);

	this.parcelCoords = routesLatLng.mainRoute;
	this.parcelRectangle = new google.maps.Polygon({
		map : this.map,
		paths : this.parcelCoords,
		strokeColor : '#FF0000 ',
		strokeOpacity : 0.8,
		strokeWeight : 2,
		fillColor : '#FF0000 ',
		fillOpacity : 0.35
	});
//	this.parcelRectangle.setMap(this.map);
	this.marker = new google.maps.Marker({
		position : {
			lat : 51.316804,
			lng : 5.178557
		},
//		icon: 'fourbyfour.png',
		icon : {
			url : 'fourbyfour.png',
			fillOpacity : 1,
			strokeColor : 'white',
			strokeWeight : .5,
			scale : 10
		},
		map : this.map,
		title : 'VEHICLE'
	});
	//this.init(); 
}





Store.prototype.toHtml=function() {
	//console.log(this.marker_array());
	var lastObjStr = '';
	if (this.marker_array().length>0) {
		this.lastObj = {}; 
		this.lastObj.lat = parseFloat(this.lastIcon.position.lat()).toFixed(5);
		this.lastObj.lng = parseFloat(this.lastIcon.position.lng()).toFixed(5);
		
		lastObjStr = ' - ' +JSON.stringify(this.lastObj);
	}
	return 'points: ' + this.marker_array().length + lastObjStr; 
}
Store.prototype.isInParcel=function(latLng) {
	return google.maps.geometry.poly.containsLocation(latLng,
			store.parcelRectangle)
}
Store.prototype.isInParcelCurrent=function() {
	return this.isInParcel(store.marker.position)
}
Store.prototype.isPhotoPosition=function(latLng, isLog) {
	var result = false; 
	$(this.photoPositions).each(function() {
		var delta = Math.abs(this.lat() - latLng.lat())+Math.abs(this.lng() - latLng.lng());
		if (delta<0.0005) {
			result = true; 
		}
		if (isLog) {
			console.log('p.point ' + latLng.lat() +' ' + latLng.lng()); 
			console.log('p.photo ' + this.lat()+' ' + this.lng() + ' --- '+ result);
		}
		 
	})
	return result; 
}

Store.prototype.init=function() {
	var photoPositions = []; 
	$('#displayParcelPartNames option').each(function() {
		var tuple = $(this).html().replace(/ /g,'').replace(/\)/g,'').split('(')[1].split(',');
		var latLng = new google.maps.LatLng(tuple[0],tuple[1]);
		photoPositions.push(latLng); 
	})
	this.photoPositions=photoPositions;
}

Store.prototype.newMarker=function(latLng, colorStr) {
	if (!colorStr) {
		colorStr = 'red'; 
		
	}
	this.lastLatLng = latLng;
	if (!latLng.lat()) {
		return; 
	}
	var size = this.marker_array().length;
	this.lastIcon = new google.maps.Marker({
		position : latLng,
	 	map : this.map,
	 	title: 'LatLng(' +latLng.lat() + ',' + latLng.lng() +')='+size,
		icon : {
			path : google.maps.SymbolPath.CIRCLE,
			fillColor : colorStr,
			fillOpacity : 1,
			strokeColor : 'white',
			strokeWeight : .5,
			scale : 10
		}
	});
	this.lastIcon.addListener('click', function() {
		var pos = this.getPosition(); 
	    store.map.setZoom(15);
	    store.map.setCenter(pos);
	    if (store.isPhotoPosition(pos, true)) {
	    	var key = parseFloat(pos.lat()).toFixed(3) + ', ' +parseFloat(pos.lng()).toFixed(3);
	    	console.log('key ' + key); 
	    	$('#displayParcelPartNames').val(key); 
	    	$('#displayParcelPartNames option:selected').parent().change(); 
	    } 
	});
	this.marker_array().push(this.lastIcon);
	
	console.trace('latLng [' + latLng.lat() +', ' + latLng.lng()+']');
	return this.lastIcon
}

function initEventParcelClick() {
	google.maps.event.addListener(store.parcelRectangle, 'click', function(e) {
		var isInPolygon = store.isInParcel(e.latLng)
		if (isInPolygon) {
			store.lastEvent = e.latLng; 
			var latLng = new google.maps.LatLng(e.latLng.lat(),e.latLng.lng());
			
			store.lastIcon = store.newMarker(latLng);
			
			//store.lastEvent.lat()
		} else {
			self.directionX(0)
			self.directionY(0)
		}
	});
	google.maps.event.addListener(store.map, 'click', function (event) {
        displayCoordinates(event.latLng);               
    });
}
function displayCoordinates(pnt) {

    var lat = pnt.lat();
    lat = lat.toFixed(4);
    var lng = pnt.lng();
    lng = lng.toFixed(4);
	console.trace('latLng [' + lat +', ' + lng+']');
}

function initCtrlKeys() {
	$('body').keydown(function(e) {

		
		if (e.ctrlKey) {
			e.preventDefault();
			if (e.keyCode != 17) {
				console.log('e.keyCode ' +e.keyCode)			
			}
		}
		if (e.keyCode == 37) { // 'A' or 'a'
			store.vehicle.goleft();
		}
		if (e.keyCode == 39) { // 'A' or 'a'
			store.vehicle.goright();
		}
		if (e.keyCode == 38) { // 'A' or 'a'
			store.vehicle.goup();
		}
		if (e.keyCode == 40) { // 'A' or 'a'
			store.vehicle.godown();
		}
	});
}

function initPage() {
	store = new Store();
	store.init(); 
	// initialize 
	initEventParcelClick();
	initCtrlKeys(); 
	store.vehicle = new vehicle();

	ko.applyBindings(store.vehicle);

}




// Here's my data model
var vehicle = function() {
	self = this;
	//self.polygon = routes.baseRoute;
	self.movesPerSecond = 2;
	self.moveSpeed = 0.0002;
	self.reflectOnPolygon = true;
	self.dotX = ko.observable(5.178557);
	self.dotXview= ko.observable(5.178557);
	self.dotY = ko.observable(51.316804);
	self.dotYview= ko.observable(51.316804);
	self.directionX = ko.observable(self.moveSpeed);
	self.directionY = ko.observable(-self.moveSpeed);
	self.photoNr = 0; 

	
	self.routeFollow = function() {
		if (store.routeRecorder.index == -1) {
			store.routeRecorder.doStart();
		}  else {
			store.routeRecorder.doNext();
		}
		
	};
	
	self.goright = function() {
		self.directionX(self.moveSpeed)
		self.directionY(0)
	};
	self.goUpRight = function() {
		self.directionX(self.moveSpeed)
		self.directionY(self.moveSpeed)
	};
	self.goleft = function() {
		self.directionX(-self.moveSpeed)
		self.directionY(0)
	};
	self.goDownLeft = function() {
		self.directionX(-self.moveSpeed)
		self.directionY(-self.moveSpeed)
	};
	self.goUpLeft = function() {
		self.directionX(-self.moveSpeed)
		self.directionY(self.moveSpeed)
	};
	self.goDownRight = function() {
		self.directionX(self.moveSpeed)
		self.directionY(-self.moveSpeed)
	};
	self.goup = function() {
		self.directionX(0)
		self.directionY(self.moveSpeed)
	};
	self.godown = function() {
		self.directionX(0)
		self.directionY(-1 * self.moveSpeed)
	};
	self.photoFirst = function() {
		$('#displayImage').attr('src',store.photoAlbum.getUriBegin()); 
	};
	self.photoLast = function() {
		$('#displayImage').attr('src',store.photoAlbum.getUriLast()); 
	};	
	self.photoBackward = function() {
		var stateNr = store.photoAlbum.getState(); 
		if (stateNr>store.photoAlbum.begin) {
			stateNr = stateNr-1; 
		}
		$('#displayImage').attr('src',store.photoAlbum.getUri(stateNr)); 
	};	
	self.photoForward = function() {
		var stateNr = store.photoAlbum.getState(); 
		if (stateNr<store.photoAlbum.last) {
			stateNr = stateNr+1; 
		}
		$('#displayImage').attr('src',store.photoAlbum.getUri(stateNr)); 
	};	
	self.marge = 0.00015;
	
	self.isInSpecialSpotLong=function(specialSpot) {
		return specialSpot.position.lng() > (self.dotX() - self.marge)
		&& specialSpot.position.lng() < (self.dotX() + self.marge);
	}
	self.isInSpecialSpotLat=function(specialSpot) {
		return specialSpot.position.lat() > (self.dotY() - self.marge)
			&& specialSpot.position.lat() < (self.dotY() + self.marge);
	}
	self.reset=function() {
	  store.routeRecorder.reset();
	}
	self.checkPhoto=function() {
		for (i = 0; i < store.marker_array().length; i++) {
			var specialSpot = store.marker_array()[i];
			var isInLongRange = self.isInSpecialSpotLong(specialSpot); 
			
			if (isInLongRange) {
				var isInLatRange = self.isInSpecialSpotLat(specialSpot)
				if (isInLatRange) {
					self.photoNr++;
					var isLast = store.photoAlbum.getState()!=store.photoAlbum.last
					console.log('isLast '+ isLast+ ' ' +self.photoNr ); 
					if(isLast) {
						store.vehicle.photoForward(); 						
					} else {
						store.vehicle.photoFirst();
					}
					$('#displayMarkers').html("foto op veld " + self.photoNr);
				}
			}
		}
	}
	self.moveDot = function() {
		
		
		
		var newX = self.dotX() + self.directionX();
		var newY = self.dotY() + self.directionY();
					
		self.lastLatLng= new google.maps.LatLng(newY, newX);
		if (store.isInParcel(self.lastLatLng)) {
			self.dotX(newX);
			self.dotY(newY);
			
			store.marker.setPosition(self.lastLatLng);
			
			self.dotXview(parseFloat(newX).toFixed(6));
			self.dotYview(parseFloat(newY).toFixed(6));
			$('#displayMarkers').html(store.toHtml());

			self.checkPhoto(); 
		} else {
			$('#displayMarkers').html(store.toHtml());
		}
		
	};
	self.draw=function(routeStr, size) {
		if (!routeStr) {
			routeStr = this.routeName;
		}
		if (!routeStr||!routes[routeStr]) {
			routeStr = 'baseRoute';
		}
		store.routeRecorder.reset(); 
		var currentRoute = routes[routeStr]; 
		
		if (isNaN(size)||size > currentRoute.length) {
			size = currentRoute.length;
		}
		console.log('size ' +size)
		for (var index=0; index<size;index++) {
			var tuple = currentRoute[index]; 
			var latLng = new google.maps.LatLng(tuple[0], tuple[1]);
			var colorStr = 'orange';
			if (store.isPhotoPosition(latLng)) {
				colorStr = 'green';
			}
			store.lastIcon = store.newMarker(latLng,colorStr);
			store.lastEvent = latLng; 
		}
	}
	setInterval(self.moveDot, self.movesPerSecond * 500);

};
