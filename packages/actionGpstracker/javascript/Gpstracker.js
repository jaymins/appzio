
var lastlong, lastlati, route;

route = [];

function position(x) {
  if (!lastlati || !lastlong) {
    console.log("Started Lat " + (x.coords.latitude) + " Lon " + (x.coords.longitude));
    lastlati = x.coords.latitude;
    lastlong = x.coords.longitude;
    route.push(x);
    var pos = new google.maps.LatLng(x.coords.latitude,x.coords.longitude);
  	map.setCenter(pos);
  	map.setZoom(16);
  	
  	marker = new google.maps.Marker({
  	    position: pos,
  	    map: map,
  	    title:"Start"
  	});
  	
  	var polyOptions = {
      strokeColor: '#000000',
      strokeOpacity: 1.0,
      strokeWeight: 3
    };
    poly = new google.maps.Polyline(polyOptions);
    poly.setMap(map);
  }
  updateMapRoute();
  if (x.coords.latitude != lastlati || x.coords.longitude != lastlong) {
    console.log("Moved Lat " + (lastlati - x.coords.latitude) + " Lon " + (lastlong - x.coords.longitude));

    route.push(x);
    if (typeof(Storage)!=="undefined") {
      var r = JSON.stringify(route);
      //console.log("Storing route as " + r);
      sessionStorage.setItem(token, r);
    }
    updateMapRoute();
    var dp = "{%distance_travelled_so_far%} " + Math.round(getPathLength()*100)/100;
    if (typeof km_target != 'undefined') {
      var pl = getPathLength();
      dp += "km. " + Math.round((pl/km_target)*100) + "% {%of_your_target_covered%}.";
   
      console.log(pl >= km_target);
      if (pl >= km_target) {
        //window.location.href = doneurl;
        document.getElementById('donebtn').disabled = false;
      }
    } else if (typeof lat_target != 'undefine' && typeof lon_target != 'undefined') {
      var r = getDistanceFromStart();
      var rTpos = new Object();
      rTpos.coords = new Object();
      rTpos.coords.latitude = lat_target;
      rTpos.coords.longitude = lon_target;
      console.log(rTpos);
      console.log(route[route.length-1]);
      var rT = getDistance(route[route.length-1], rTpos);
      if (r > rmax) {
        dp += ", and you are out of your defined game range!";
        console.log("Out of R!");
      } else {
        if (rT < 0.5) {
          //window.location.href = doneurl;
          document.getElementById('donebtn').disabled = false;
        } else {
          dp += ", and you are about " + Math.round(rT) + "km from the target";
          console.log("Distance to target  " + rT);
        }
      }
    }
    document.getElementById('dp').innerHTML = dp ;

    lastlati = x.coords.latitude;
    lastlong = x.coords.longitude;

  }
}

function error(x) {
  console.log(x);
}
function done() {
	sessionStorage.removeItem(token);
	window.location.href = doneurl;
}
function initMap() {
  var myOptions = {
	      zoom: 4,
	      mapTypeControl: true,
	      mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
	      navigationControl: true,
	      navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
	      mapTypeId: google.maps.MapTypeId.ROADMAP      
	    }	
  map = new google.maps.Map(document.getElementById("map"), myOptions);
 
  if (typeof(Storage)!=="undefined") {
    var pR = sessionStorage.getItem(token);
    pR = JSON.parse(pR);
    if (pR != null) {
      for (var i=0;i<pR.length; i++) {
        position(pR[i]);
      }
    }
  }

  navigator.geolocation.watchPosition(position, error, {enableHighAccuracy:true});
}

function updateMapRoute() {
  var path = poly.getPath();
  var last = route[route.length-1];
  gpos = new google.maps.LatLng(last.coords.latitude, last.coords.longitude);
  map.setCenter(gpos);
  path.push(gpos);
}

function moveSomewhere() {
  var sp = new Object();
  sp.coords = new Object();
  var last = route[route.length-1];
  if (last) {
    sp.coords.latitude = last.coords.latitude + Math.cos( Math.PI * Math.random() )/1000;
    sp.coords.longitude = last.coords.longitude + Math.cos( Math.PI * Math.random()  )/1000;
    position(sp);
  } else {
    console.log("no route" + route);
  }
}


function moveToTarget() {
  var sp = new Object();
  sp.coords = new Object();
  sp.coords.latitude = lat_target;
  sp.coords.longitude = lon_target;
  position(sp);
}

function moveFarAway() {
  var sp = new Object();
  sp.coords = new Object();
  var last = route[route.length-1];
  if (last) {
    sp.coords.latitude = 31.6353;
    sp.coords.longitude = -8.0003;
    position(sp);
  } else {
    console.log("no route" + route);
  }
}

function getPathLength() {
  // we use harvensin here for convenience
  var d = 0;
  console.log(route);
  for (var i=0;i<route.length-1; i++) {
    var a = route[i];
    var b = route[i+1];
    var dLat = (b.coords.latitude - a.coords.latitude) * Math.PI/180;
    var dLon = (b.coords.longitude - a.coords.longitude) * Math.PI/180;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(a.coords.latitude*Math.PI/180) * Math.cos(b.coords.latitude*Math.PI/180) * Math.sin(dLon/2) * Math.sin(dLon/2);

    d = d + 6371 * (Math.asin(Math.sqrt(a))*2);
  }
  return d;
}

function getDistanceFromStart() {
  var d = 0;

  var a = route[0];
  var b = route[route.length-1];

  return getDistance(a, b);
}

function getDistance(a, b) {
  var d = 0;

  var dLat = (b.coords.latitude - a.coords.latitude) * Math.PI/180;
  var dLon = (b.coords.longitude - a.coords.longitude) * Math.PI/180;
  var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(a.coords.latitude*Math.PI/180) * Math.cos(b.coords.latitude*Math.PI/180) * Math.sin(dLon/2) * Math.sin(dLon/2);

  d = d + 6371 * (Math.asin(Math.sqrt(a))*2);

  return d;
}

