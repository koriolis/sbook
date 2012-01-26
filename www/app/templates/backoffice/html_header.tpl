<!DOCTYPE HTML>
<html>
	<head>
		<title>Backoffice</title>

		<link rel="stylesheet" href="<?= BO_CSS_URI ?>reset-min.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?= BO_CSS_URI ?>smoothness/jquery-ui-1.8.custom.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?= BO_CSS_URI ?>global.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?= BO_CSS_URI ?>imgareaselect-default.css" type="text/css" media="screen">
		<!--[if lte IE 7]>
		<link rel="stylesheet" href="<?= BO_CSS_URI ?>ie7.css" type="text/css" media="screen">
		<![endif]-->
		<script type="text/javascript" charset="utf-8">
		   function format_quote(data){return data.quote + " - " + "<i>"+data.author+"</i>";}
		</script>
		<script src="http://quotesondesign.com/api/3.0/api-3.0.js?formatter=format_quote&cachebuster=<?= mktime(); ?>" type="text/javascript" charset="utf-8"></script>
		
		<script type="text/javascript">
			var globals = {
				uri: {
					base:	'<?= BO_URI ?>',
					css:	'<?= BO_CSS_URI ?>',
					js:		'<?= BO_JS_URI ?>',
					img:	'<?= BO_IMG_URI ?>',
					uploads:'<?= UPLOADS_URI ?>'
				},
				errorMessage: '<?= nl2br(trim($error_message)) ?>'
			}
		</script>

		<script type="text/javascript" src="http://use.typekit.com/tal5qnj.js"></script>
		<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

		<script type="text/javascript" src="<?= BO_JS_URI ?>jquery-1.7.min.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/jquery.url_toolbox.js"></script>		
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/jquery.blockUI.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/swfobject.js"></script>	
		
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/jquery.equalizecols.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/validate/jquery.form.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/validate/jquery.validate.min.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/validate/localization/messages_ptpt.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/validate/localization/methods_pt.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>ui/jquery.ui.core.min.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>ui/jquery.ui.widget.min.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>ui/jquery.ui.mouse.min.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>ui/jquery.ui.resizable.min.js"></script>
		<script src="<?= BO_JS_URI ?>ui/jquery.ui.tabs.min.js"></script>

        <!--Editor WYSIWYG-->
        <script type="text/javascript" src="<?= BO_JS_URI ?>ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="<?= BO_JS_URI ?>ckeditor/adapters/jquery.js"></script>
		
		
		<script type="text/javascript" defer="defer">
			globals.url = $(document).url();
		</script>

		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/jquery.selectboxes.min.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/jquery.lightbox_me.js"></script>
		<script type="text/javascript" src="<?= BO_JS_URI ?>plugins/jquery.imgareaselect.min.js"></script>
		
		<script type="text/javascript" src="<?= BO_JS_URI ?>global.js"></script>


