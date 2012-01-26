<?php
////////////////////////////////////////////////////
// Template - PHP template class
//
// PHP Templating class for sBook
// Uses a fairly simple method to require_once a php file
// Code is isolated to the class so you only have access to class public data and globals
//
//
////////////////////////////////////////////////////

/**
 * Template - PHP Template class
 * @package sbook
 * @author Nuno Ferreira <koriols@gmail.com>
 * @copyright 2004-2008 Wiz Interactive
 */
uses('debug');
class Template
{
	private $_template_vars;
	public $template_dir;

	public function __construct()
	{
		// Caso nos tenhamos 'esquecido' de definir as constantes de paths e URIs no config
		if(!defined('TEMPLATES'))	define('TEMPLATES',APP.'templates/');
		if(!defined('UPLOADS'))		define('UPLOADS',APP.'uploads/');
		if(!defined('CACHE'))		define('CACHE',UPLOADS.'_cache/');

		if(!defined('UPLOADS_URI'))	define('UPLOADS_URI',BASEURI.'app/uploads/');
		if(!defined('CACHE_URI'))	define('CACHE_URI',UPLOADS_URI.'_cache/');

		// Caso ainda não existam as pastas, então criam-se
		if(!is_dir(UPLOADS)) mkdir(UPLOADS);
		if(!is_dir(CACHE)) mkdir(CACHE);

		$frontendOptions = array(
			'lifetime' => 7200, // cache lifetime of 2 hours
			'automatic_serialization' => false
		);

	}

	/** BASIC FUNCTIONALITY **/

	public function assign($tpl_var, $value = null)
    {
        if (is_array($tpl_var)){
            foreach ($tpl_var as $key => $val) {
                if ($key != '') {
                    $this->_template_vars[$key] = $val;
                }
            }
        } else {
            if ($tpl_var != ''){
                $this->_template_vars[$tpl_var] = $value;
			}
        }
    }

	public function display($_template_file = false) {
		if($_template_file !== false){
			if(is_array($this->_template_vars)) extract($this->_template_vars, EXTR_SKIP);
			include ($this->template_dir.$_template_file);
			return;
		}
	}


    public function fetch($_template_file = false) {
        if($_template_file !== false){
            ob_start();
            if(is_array($this->_template_vars)) {
	            extract($this->_template_vars, EXTR_SKIP);
			}
            include ($this->template_dir.$_template_file);
            $html = ob_get_clean();
            return $html;
        }
    }

	/**** COMPRESSOR METHODS ****/

	public function load()
	{
		$files = array_values(func_get_args());
		if(is_array($files) && count($files)>0) {
			foreach($files as $file) {
				$file_type = substr(strrchr($file, '.'),1);
				$this->_template_vars[$file_type][] = $file;
			}
		}
	}

	public function loadGlobal()
	{
		$files = array_values(func_get_args());
		if(is_array($files) && count($files)>0) {
			foreach($files as $file) {
				$file_type = substr(strrchr($file, '.'),1);
				$this->_template_vars['global_'.$file_type][] = $file;
			}
		}
	}

	public function loadPrint()
	{
		$files = array_values(func_get_args());
		if(is_array($files) && count($files)>0) {
			foreach($files as $file) {
				$file_type = substr(strrchr($file, '.'),1);
				$this->_template_vars['print_'.$file_type][] = $file;
			}
		}
	}

	public function displayCSS()
	{

		// Se existirem ficheiros globais
		//
		if(is_array($this->_template_vars['global_css']) && count($this->_template_vars['global_css'])>0){

			$files = array_values($this->_template_vars['css']);
			// Get last-modified times from files
			foreach($files as $i=>$file) {
				if(is_file(TEMPLATES.$file)) {
					$modtimes[]=filemtime(TEMPLATES.$file);
				}
			}

			$cached_filepath 	= CACHE.md5(implode("",$modtimes).implode("",$files)).".css";
			$cached_file_uri	= CACHE_URI.basename($cached_filepath);

			if(!is_file($cached_filepath)){
				$source = array();
				foreach($files as $i=>$file) {
					$source[] = file_get_contents(TEMPLATES_URI.$file);
				}
				$source = array_unique($source);

				$source = $this->compress_css(implode("",$source));
				file_put_contents($cached_filepath,$source);

				unset($this->_template_vars['css']);
			}
			echo '<link rel="stylesheet" href="'.$cached_file_uri.'" type="text/css" media="all">';
		}

		

		// Se existirem ficheiros locais
		//
		if(is_array($this->_template_vars['css']) && count($this->_template_vars['css'])>0){

			$files = array_values($this->_template_vars['css']);
			// Get last-modified times from files
			foreach($files as $i=>$file) {
				if(is_file(TEMPLATES.$file)) {
					$modtimes[]=filemtime(TEMPLATES.$file);
				}
			}

			$cached_filepath 	= CACHE.md5(implode("",$modtimes).implode("",$files)).".css";
			$cached_file_uri	= CACHE_URI.basename($cached_filepath);

			if(!is_file($cached_filepath)){
				foreach($files as $i=>$file) {
					$source[] = file_get_contents(TEMPLATES_URI.$file);
				}

				$source = array_unique($source);

				$source = $this->compress_css(implode("",$source));
				file_put_contents($cached_filepath,$source);

				unset($this->_template_vars['css']);
			}
			echo '<link rel="stylesheet" href="'.$cached_file_uri.'" type="text/css" media="all">';
		}

		// Se existirem ficheiros media_print
		//
		if(is_array($this->_template_vars['print_css']) && count($this->_template_vars['print_css'])>0){

			$files = array_values($this->_template_vars['print_css']);
			// Get last-modified times from files
			foreach($files as $i=>$file) {
				if(is_file(TEMPLATES.$file)) {
					$modtimes[]=filemtime(TEMPLATES.$file);
				}
			}

			$cached_filepath 	= CACHE.md5(implode("",$modtimes).implode("",$files)).".css";
			$cached_file_uri	= CACHE_URI.basename($cached_filepath);

			if(!is_file($cached_filepath)){
				$source = array();
				foreach($files as $i=>$file) {
					$source[] = file_get_contents(TEMPLATES_URI.$file);
				}
				$source = array_unique($source);

				$source = $this->compress_css(implode("",$source));
				file_put_contents($cached_filepath,$source);

				unset($this->_template_vars['print_css']);
			}
			echo '<link rel="stylesheet" href="'.$cached_file_uri.'" type="text/css" media="print">';
		}
		

	}

	public function displayJS()
	{
		// Global JS
		//
		if(is_array($this->_template_vars['global_js']) && count($this->_template_vars['global_js'])>0){
			logmsg('------------------------------------- GLOBAL');
			$files = array_values($this->_template_vars['global_js']);

			// Get last-modified times from files
			foreach($files as $i=>$file) {
				if(is_file(TEMPLATES.$file)) {
					$modtimes[]=filemtime(TEMPLATES.$file);
				}
			}

			$cached_filepath 	= CACHE.md5(implode("",$modtimes).implode("",$files)).".js";
			$cached_file_uri	= CACHE_URI.basename($cached_filepath);


			if(!is_file($cached_filepath)){
				$source = array();

				foreach($files as $i=>$file) {

					$already_minified = (substr($file, strlen($file) - strlen("min.js"))=="min.js");

					if($already_minified === true) {
						logmsg('minified: '.$file);
						$source[] = file_get_contents(TEMPLATES.$file);
					} else {
						logmsg('not minified: '.$file);
						$source[] = $this->compress_js(file_get_contents(TEMPLATES.$file));
					}

				}
				$source = array_unique($source);
				$source = implode("",$source);
				file_put_contents($cached_filepath,$source);

				unset($this->_template_vars['global_js']);
			}

			echo "<script type='text/javascript' src='".$cached_file_uri."'></script>";
		}
		
		// Local JS
		//
		if(is_array($this->_template_vars['js']) && count($this->_template_vars['js'])>0){
			logmsg('------------------------------------- LOCAL');
			
			$files = array_values($this->_template_vars['js']);


			// Get last-modified times from files
			foreach($files as $i=>$file) {
				//echo "<script>console.log('".json_encode(is_file('/var/disco2/sites/sogrape2010/www/app/templates/js/areas/agepreloader/preloader.js'))."');</script>";
				if(is_file(TEMPLATES.$file)) {
					$modtimes[]=filemtime(TEMPLATES.$file);
				}
			}

			$cached_filepath 	= CACHE.md5(implode("",$modtimes).implode("",$files)).".js";
			$cached_file_uri	= CACHE_URI.basename($cached_filepath);

			if(!is_file($cached_filepath)) {
				$source = array();

				foreach($files as $i=>$file) {

					$already_minified = (substr($file, strlen($file) - strlen("min.js"))=="min.js");

					if($already_minified === true) {
						logmsg('not minified:'.$file);
						$source[] = file_get_contents(TEMPLATES.$file);
					} else {
						logmsg('minified:'.$file);
						$source[] = $this->compress_js(file_get_contents(TEMPLATES.$file));
					}

				}
				$source = array_unique($source);
				$source = implode("",$source);
				file_put_contents($cached_filepath,$source);

				unset($this->_template_vars['js']);
			}

			echo "<script type='text/javascript' src='".$cached_file_uri."'></script>";
		}

	}

	private function compress_js($js)
	{

		uses("jsmin");

		if(defined('COMPRESS_JS') && COMPRESS_JS !== false) {
			return JSMin::minify($js);
		} else {
			return $js;
		}
	}

	private function compress_css($css)
	{
		/*
		 *  Helper functions for this method
		 */
		function _selectorsCB($m)
		{
			// remove ws around the combinators
			return preg_replace('/\\s*([,>+~])\\s*/', '$1', $m[0]);
		}

		function _commentCB($m)
		{

			$m = $m[1];
			// $m is the comment content w/o the surrounding tokens,
			// but the return value will replace the entire comment.
			if ($m === 'keep') {
				return '/**/';
			}
			if ($m === '" "') {
				// component of http://tantek.com/CSS/Examples/midpass.html
				return '/*" "*/';
			}
			if (preg_match('@";\\}\\s*\\}/\\*\\s+@', $m)) {
				// component of http://tantek.com/CSS/Examples/midpass.html
				return '/*";}}/* */';
			}

			if (substr($m, -1) === '\\') { // comment ends like \*/
				// begin hack mode and preserve hack
				return '/*\\*/';
			}
			if ($m !== '' && $m[0] === '/') { // comment looks like /*/ foo */
				// begin hack mode and preserve hack
				return '/*/*/';
			}

			return ''; // remove all other comments
		}

		function _fontFamilyCB($m)
		{
			$m[1] = preg_replace('/
					\\s*
					(
						"[^"]+"      # 1 = family in double qutoes
						|\'[^\']+\'  # or 1 = family in single quotes
						|[\\w\\-]+   # or 1 = unquoted family
					)
					\\s*
				/x', '$1', $m[1]);
			return 'font-family:' . $m[1] . $m[2];
		}

		function _urlCB($m)
		{

			$isImport = (0 === strpos($m[0], '@import'));
			if ($isImport) {
				$quote = $m[1];
				$url = $m[2];
			} else {
				// is url()
				// $m[1] is either quoted or not
				$quote = ($m[1][0] === "'" || $m[1][0] === '"')
					? $m[1][0]
					: '';
				$url = ($quote === '')
					? $m[1]
					: substr($m[1], 1, strlen($m[1]) - 2);
			}
			if ('/' !== $url[0]) {
				if (strpos($url, '//') > 0) {
					// probably starts with protocol, do not alter
				} else {
					// relative URI, rewrite!
					// rewrite absolute url from scratch!
					// prepend path with current dir separator (OS-independent)


					$path = TEMPLATES.strtr($url, '/', DS);

					// strip doc root
					$path = substr($path, strlen(realpath(ROOT)));

					// fix to absolute URL
					$url = strtr($path, DS, '/');
					// remove /./ and /../ where possible
					$url = str_replace('/./', '/', $url);
					$url = str_replace('/../', '/', $url);
					//$url = substr(BASEURI,0,-1).$url;
				}
			}
			return $isImport
				? "@import {$quote}{$url}{$quote}"
				: "url({$quote}{$url}{$quote})";
		}

		// converter line breaks em line feeds
		$css = str_replace("\r\n", "\n", $css);

		// preserve empty comment after '>'
        // http://www.webdevout.net/css-hacks#in_css-selectors
        $css = preg_replace('@>/\\*\\s*\\*/@', '>/*keep*/', $css);

        // preserve empty comment between property and value
        // http://css-discuss.incutio.com/?page=BoxModelHack
        $css = preg_replace('@/\\*\\s*\\*/\\s*:@', '/*keep*/:', $css);
        $css = preg_replace('@:\\s*/\\*\\s*\\*/@', ':/*keep*/', $css);

         // apply callback to all valid comments (and strip out surrounding ws
        $css = preg_replace_callback('@\\s*/\\*([\\s\\S]*?)\\*/\\s*@','_commentCB', $css);

         // remove ws around { } and last semicolon in declaration block
        $css = preg_replace('/\\s*{\\s*/', '{', $css);
        $css = preg_replace('/;?\\s*}\\s*/', '}', $css);

        // remove ws surrounding semicolons
		$css = preg_replace('/\\s*;\\s*/', ';', $css);

		// remove ws around urls

        $css = preg_replace('/
                url\\(      # url(
                \\s*
                ([^\\)]+?)  # 1 = the URL (really just a bunch of non right parenthesis)
                \\s*
                \\)         # )
            /x', 'url($1)', $css);

        // remove ws between rules and colons
        $css = preg_replace('/
                \\s*
                ([{;])              # 1 = beginning of block or rule separator
                \\s*
                ([\\*_]?[\\w\\-]+)  # 2 = property (and maybe IE filter)
                \\s*
                :
                \\s*
                (\\b|[#\'"])        # 3 = first character of a value
            /x', '$1$2:$3', $css);
        // remove ws in selectors
        $css = preg_replace_callback('/
                (?:              # non-capture
                    \\s*
                    [^~>+,\\s]+  # selector part
                    \\s*
                    [,>+~]       # combinators
                )+
                \\s*
                [^~>+,\\s]+      # selector part
                {                # open declaration block
            /x','_selectorsCB', $css);
        // minimize hex colors
        $css = preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i','$1#$2$3$4$5', $css);

		 // remove spaces between font families
        $css = preg_replace_callback('/font-family:([^;}]+)([;}])/','_fontFamilyCB', $css);
		$css = preg_replace('/@import\\s+url/', '@import url', $css);

		// replace any ws involving newlines with a single newline
        $css = preg_replace('/[ \\t]*\\n+\\s*/', "\n", $css);

        // separate common descendent selectors w/ newlines (to limit line lengths)
        $css = preg_replace('/([\\w#\\.\\*]+)\\s+([\\w#\\.\\*]+){/', "$1\n$2{", $css);

         // Use newline after 1st numeric value (to limit line lengths).
        $css = preg_replace('/
            ((?:padding|margin|border|outline):\\d+(?:px|em)?) # 1 = prop : 1st numeric value
            \\s+
            /x'
            ,"$1\n", $css);

		$css = preg_replace_callback('/@import\\s+([\'"])(.*?)[\'"]/','_urlCB', $css);
		$css = preg_replace_callback('/url\\(\\s*([^\\)\\s]+)\\s*\\)/','_urlCB', $css);

        return $css;


	}

}