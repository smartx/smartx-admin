<html> 
<head> 
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" /> 
	<title>Google Map V3 Polygon Creator</title>
	<meta name="keywords" content="polygon,creator,google map,v3,draw,paint">
	<meta name="description" content="Google Map V3 Polygon Creator">
	
	
	<link rel="stylesheet" type="text/css" href="css/polymaker.css">
            
        </link>
	
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript" src="js/vendor/jquery-1.9.0.min.js"></script>
	<script type="text/javascript" src="js/polymaker.js"></script>
	
	<script type="text/javascript">
	$(function(){
		  //create map
		 var singapoerCenter=new google.maps.LatLng(-12.032423, -77.030303);
		 var myOptions = {
		  	zoom: 12,
		  	center: singapoerCenter,
		  	mapTypeId: google.maps.MapTypeId.ROADMAP
		  }
		 map = new google.maps.Map(document.getElementById('main-map'), myOptions);
		 
		 var creator = new PolygonCreator(map);
		 
		 //reset
		 $('#reset').click(function(){ 
		 		creator.destroy();
		 		creator=null;
		 		
		 		creator=new PolygonCreator(map);
		 });		 
		 
		 
		 //show paths
		 $('#showData').click(function(){ 
		 		$('#dataPanel').empty();
		 		if(null==creator.showData()){
		 			$('#dataPanel').append('Please first create a polygon');
		 		}else{
		 			$('#dataPanel').append(creator.showData());
		 		}
		 });
		 
		 //show color
		 $('#showColor').click(function(){ 
		 		$('#dataPanel').empty();
		 		if(null==creator.showData()){
		 			$('#dataPanel').append('Please first create a polygon');
		 		}else{
		 				$('#dataPanel').append(creator.showColor());
		 		}
		 });
	});	
	</script>
</head>
<body>



	<div id="header">
		<ul>
			<li class="title">
				Polygon Creator Class (For Google Map API v3)
			</li>
			<li>
			<input type="hidden" name="cmd" value="_donations">
			<input type="hidden" name="lc" value="US">
			<input type="hidden" name="item_name" value="Polygon Creator">
			<input type="hidden" name="no_note" value="0">
			<input type="hidden" name="currency_code" value="USD">
			</form>
			</li>
	

			
		</ul>
	</div>
	<div id="main-map">
	</div>
	<div id="side">
		<input id="reset" value="Reset" type="button" class="navi"/>
		<input id="showData"  value="Show Paths (class function) " type="button" class="navi"/>
		<input id="showColor"  value="Show Color (class function) " type="button" class="navi"/>
		<div   id="dataPanel">
		</div>
	</div>
</body>
</html>