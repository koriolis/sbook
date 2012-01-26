<!DOCTYPE html>  
<html>
	<head>
		<meta charset="utf-8">
		<title><?= $page_title ?></title>
		
		<meta name="description" content="<?=$page_description?>">
		<meta name="keywords" content="<?=$page_keywords?>">
		<meta name="author" content="Wiz Interactive">

		<link rel="shortcut icon" href="<?= BASEURI ?>favicon.ico">

		<? $this->loadGlobal('css/style.css','css/global.css','css/mediaqueries.css');?>
		
		<? $this->load("css/".$area.".css");?>
		<? $this->displayCSS();?>
		
		<? $this->loadGlobal('js/libs/modernizr-1.7.min.js','js/libs/jquery-1.6.1.min.js','js/global.js');?>
		
		<script>
			var globals = {
				baseuri:		'<?= BASEURI ?>',
				js_uri:			'<?= JS_URI ?>',
				templates_uri:  '<?= TEMPLATES_URI ?>',
				uploads_uri:    '<?= UPLOADS_URI ?>'
			}
		</script>

	</head>
	<body id="body-<?= $area ?><?= (!empty($subarea)) ? "-".$subarea : "" ?>">

		<header>
			Header
		</header>
			
		<div id="container">
			<? include(TEMPLATES.'areas/'.$view); ?>
		</div>

		<footer>
			Footer
		</footer>		
		
		<? $this->displayJS();?>
		

		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-7074764-4']);
		  _gaq.push(['_trackPageview']);
		  _gaq.push(['_trackPageLoadTime']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>

	</body>
</html>