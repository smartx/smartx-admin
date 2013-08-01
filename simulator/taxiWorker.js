M_PI = 3.14159265358979323846;
var tdriver_id;
var interval;
var lat;
var lng;
var pointToGoLat;
var pointToGoLng;
var distancePerSeconds;
var seconds;

self.addEventListener('message', function(e) {
	importScripts('../js/third/jquery.hive.pollen.js');
//        importScripts('https://maps.googleapis.com/maps/api/js?libraries=drawing&key=AIzaSyAK2rfeeudxNw8JVF_9u3tk9xxXkOe7-Mc&sensor=false');
	if(e.data.type=='init'){
		tdriver_id=e.data.tdriver_id;
		lat=e.data.lat;
		lng=e.data.lng;
		interval=1+Math.random()*10; // Interval for refreshing location
		seconds=interval;
	}
	init();
  	self.postMessage({id:tdriver_id, data:"init with value "+interval+" interval time to send gps locations"});
  	
}, false);

var di=[1,-1,0,0,1,1,-1,-1];
var dj=[0,0,1,-1,-1,1,-1,1];
var factor=1000;

function init(){
	setInterval(function(){
		var newlat;
		var newlng;
		var comment='wandering';
		if(pointToGoLat!=null && pointToGoLng!=null){ // There is a user waiting for this driver
			//from 166 meters to 250 meters - 60km/h to 90km/h // UPD: 126
			var distanceInMettersIWouldTravelInSecondsSeconds = 126+Math.random()*84*10/seconds;
			distanceInMettersIWouldTravelInSecondsSeconds/=1000;
			// distanceInMettersIWouldTravelInSecondsSeconds = 10/seconds;	
			var distanceToUser = distanceFromTwoPoints(lat, lng, pointToGoLat, pointToGoLng);

			//SOME TWEAKING MAY BE NEEDED - sometimes it does not get to the same point as the user is, even though the locations are ident
			if(distanceToUser==0){	
				self.postMessage({id:tdriver_id,data:"Arrived"});
			}

			var maxDistanceToTravel = Math.min(distanceToUser,distanceInMettersIWouldTravelInSecondsSeconds);
			var factorX = (maxDistanceToTravel)/distanceToUser;

			if(factorX==1){
				newlat=pointToGoLat;
				newlng=pointToGoLng
			}else{
				var latDirection=1;
				if(lat>pointToGoLat){
					latDirection=-1;
				}
				var lngDirection=1;
				if(lng>pointToGoLng){
					lngDirection=-1;
				}
				newlat = lat+Math.abs(pointToGoLat-lat)*factorX*latDirection;
				newlng = lng+Math.abs(pointToGoLng-lng)*factorX*lngDirection;
			}
			
			comment="lat+parseFloat(pointToGoLat)="+(lat+(pointToGoLat))/factorX+"Math.sin(3)="+Math.sin(3)+" pointToGoLat="+pointToGoLat+" pointToGoLng="+pointToGoLng+" distance="+distanceInMettersIWouldTravelInSecondsSeconds+" distanceToUser="+distanceToUser+" factorX="+factorX+" picking someone up";
		}else{
			//you can wander around
			var newlat;
                        var newlng;
                        var nAttempts=0;
                        do{
                            if(nAttempts>=5){
                                newlat=limaCenter[0];
                                newlng=limaCenter[1];
                                break;
                            }else{
                                var si=Math.floor(Math.random()*4);
                                var distance = Math.random()*10;
                                newlat = lat+di[si]*distance/factor;
                                newlng = lng+dj[si]*distance/factor;
                            }
                            nAttempts++;
                        }
                        while(!pnpoly(newlat,newlng));
		}
		self.postMessage({id:tdriver_id,data:comment+" going from "+lat+" "+lng+" to "+newlat+" "+newlng});
		// self.postMessage({id:tdriver_id,data:});
		lat=newlat;
		lng=newlng;
		 $.ajax.post({
	        url: "../tdriver/bot_update_location1",
	        data:"id="+tdriver_id+"&lat="+newlat+"&lng="+newlng,
	        type:'json',
	        success: function(data) {
//                    self.postMessage(data);
//                    return;
	        	var a = {id:tdriver_id, data:data};
	        	self.postMessage(a);
	        	if(data.json!=null && data.json.length!=0){
	        		pointToGoLat = parseFloat(data.json.lat);
	        		pointToGoLng = parseFloat(data.json.lng);
	        		var a = {id:tdriver_id, data:data};
	        		self.postMessage(a);
	        	}else{
	        		pointToGoLat=null;
	        		pointToGoLng=null;
	        	}
	      	}
	      })
	},interval*1000);
	var distanceFromTwoPoints = function(lat1, lon1, lat2, lon2) {
		var R = 6371; // km
		var dLat = toRad(lat2 - lat1);
		var dLon = toRad(lon2 - lon1);
		var lat1 = toRad(lat1);
		var lat2 = toRad(lat2);

		var a = Math.sin(dLat / 2.0) * Math.sin(dLat / 2.0)
				+ Math.sin(dLon / 2.0) * Math.sin(dLon / 2.0) * Math.cos(lat1)
				* Math.cos(lat2);
		var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1.0 - a));
		var d = R * c;
		return d;
	}

	function toRad(val) {
		return val *  M_PI/ 180;
	}
        
        // lima polygon
        var verts=[
            [-12.064334351194189, -77.15200424194336],
            [-12.052583194893435, -77.14187622070312],
            [-12.03294082782201, -77.13741302490234],
            [-12.02521779452024, -77.0529556274414],
            [-12.03378027459577, -76.9508171081543],
            [-12.081960119698051, -76.9259262084961],
            [-12.140033001789861, -76.94583892822266],
            [-12.19020717674992, -76.91373825073242],
            [-12.232487073104325, -76.90086364746094],
            [-12.25043720716067, -76.93244934082031],
            [-12.2046367890781, -77.02840805053711],
            [-12.18584458121737, -77.02188491821289],
            [-12.171246143923073, -77.02617645263672],
            [-12.133655672968242, -77.02720642089844],
            [-12.085820847427279, -77.09295272827148],
            [-12.073902769432062, -77.11921691894531]
        ];
        
        var limaCenter=[-12.065509438501918, -77.05965042114258];
        function pnpoly(testx, testy)
        {
          var i, j, c = 0;
          for (i = 0, j = verts.length-1; i < verts.length; j = i++) {
            if ( ((verts[i][1]>testy) != (verts[j][1]>testy)) &&
             (testx < (verts[j][0]-verts[i][0]) * (testy-verts[i][1]) / (verts[j][1]-verts[i][1]) + verts[i][0]) )
               c = !c;
          }
          return c;
        }

}



