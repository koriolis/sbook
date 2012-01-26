$(function(){
	
	var params = { allowfullscreen:'false', allowscriptaccess:'always', wmode: 'transparent' };
	var attributes = { id:'player1', name:'player1' };
	var flashvars_1 = { file: globals.uploads_uri+'marcas/LVQR_wip_4_1_final.flv',autostart:'false', image: globals.templates_uri + 'img/homepage/video_1_thumb.jpg', controlbar: 'false', icons: 'false'};
	var flashvars_2 = { file: globals.uploads_uri+'marcas/limiano_fatias_1_final.flv',autostart:'false', image: globals.templates_uri + 'img/homepage/video_2_thumb.jpg', controlbar: 'false', icons: 'false'};
	var flashvars_3 = { file: globals.uploads_uri+'marcas/terranostra_gourmet_final.flv',autostart:'false', image: globals.templates_uri + 'img/homepage/video_3_thumb.jpg', controlbar: 'false', icons: 'false'};

	swfobject.embedSWF(globals.templates_uri+'swf/player.swf','video-vaca-que-ri','231','145','9.0.115','false', flashvars_1, params, attributes);
	swfobject.embedSWF(globals.templates_uri+'swf/player.swf','video-limiano','231','145','9.0.115','false', flashvars_2, params, attributes);
	swfobject.embedSWF(globals.templates_uri+'swf/player.swf','video-terra-nostra','231','145','9.0.115','false', flashvars_3, params, attributes);
	

});