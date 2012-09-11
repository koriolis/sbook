<?php
// sbook configuration file
// The config file is a php file
define('CONFIG',true);

// database config
define('DEFAULT_DSN','mysql://user:password@host/dbname');

define('BASEURI','http://sbook/');

define('UPLOADS',APP.'uploads/');

define('TEMPLATES_URI',BASEURI.'app/templates/');
define('JS_URI',TEMPLATES_URI.'js/');
define('CSS_URI',TEMPLATES_URI.'css/');
define('IMG_URI',TEMPLATES_URI.'img/');
define('UPLOADS_URI',BASEURI.'app/uploads/');

define('BO_TEMPLATES',APP.'templates/backoffice/');
define('BO_URI',BASEURI.'backoffice/');
define('BO_TEMPLATES_URI',BASEURI.'app/templates/backoffice/');
define('BO_JS_URI',BO_TEMPLATES_URI.'js/');
define('BO_CSS_URI',BO_TEMPLATES_URI.'css/');
define('BO_IMG_URI',BO_TEMPLATES_URI.'img/');


//Swtch between show 404 page (true) or sBook default behaviour
define('SHOW404',true);
define('PAGE404_URI',BASEURI.'404.html');

// General error options
define('ERROR_DEBUG','1');
define('ERROR_PHP','');
define('ERROR_MAIL','nuno.ferreira@wiz.pt');

// Access Control
//
define('ACCESS_RESTRICTED',0);
define('ACCESS_USER','user');
define('ACCESS_PASS','password');

//Email
define('MAIL_SERVER','mail.wiz.pt');
define('MAIL_FROM_EMAIL','info@wiz.pt');
define('MAIL_FROM_NAME','Wiz Interactive');

define('ROUTES','
	[default]
		match           = "/"
		map[controller] = "main"
		map[action]     = "index"
		#params[hello]   = "[a-z]+"		
');

?>