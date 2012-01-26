$(function(){
	
	
	// Google Maps
	//

	
	$("a.ver_mapa").css('cursor','pointer').bind('click', function(event){
		event.stopPropagation();
		event.preventDefault();

		var el 		= $(this);
		var lat		= el.data('gmaps-latitude');
		var lon		= el.data('gmaps-longitude');
		var latlng = new google.maps.LatLng(lat,lon);
 		
	    var myOptions = {
	    	zoom: 12,
	      	center: latlng,
	      	mapTypeId: google.maps.MapTypeId.ROADMAP,
	      	streetViewControl: false,
	      	mapTypeControl: false

	    };
	    
	    var map = new google.maps.Map($("#gmap").get(0), myOptions);
	    
	    var marker = new google.maps.Marker({
      		position: latlng,
      		map: map
  		});
	
	}).first().click(); // Carregar o primeiro mapa da lista automaticamente
	
	

});