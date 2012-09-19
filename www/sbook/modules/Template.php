<?php
////////////////////////////////////////////////////
// Template - PHP template class
//
// PHP Templating class for sBook
// - Uses a fairly simple method to require_once a php file
//   Code is isolated to the class so you only have access to class public data and globals
// - Parses .less files and compiles them into .css files (uses lessphp)
// - Minifies .css files and concatenates them into a single file
// - Compresses JS files and concatenates them into a single file (uses jsmin.php)
// - Includes methods to 'load' and 'display' the <script> and <link> tags for the files
//
////////////////////////////////////////////////////

/**
 * Template - PHP Template class
 * @package sbook
 * @author Nuno Ferreira <koriols@gmail.com>
 * @copyright 2004-2012 Wiz Interactive
 */
uses('debug');
class Template
{
	private $_template_vars;
    private $_css_vars;
    private $_template_file;
	public $template_dir;
	private $paths = array();

	public function __construct() {
		// If we forgot to define the path and URI constants in the config file, we define them here by default
		//
		// TODO:	This is a lousy system that isn't flexible and uses defined constants which are slow
		//			We should use a javascript like options with default values that can be overwritten 
		//			with values passed at construct time. Ex: new Template(array('template_path'=>'\home\web-root\app\templates\'))
		//
		if(!defined('TEMPLATES'))	define('TEMPLATES', APP.'templates/');
		
		if(!defined('TEMPLATES_URI'))	define('TEMPLATES_URI',BASEURI.'app/templates/');
		
		// If our required folders do not exist we create them.
		//
		if(!is_dir(TEMPLATES)) mkdir(TEMPLATES);
		
	}


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

    public function cssAssign($tpl_var, $value = null)
    {
        if (is_array($tpl_var)){
            foreach ($tpl_var as $key => $val) {
                if ($key != '') {
                    $this->_css_vars[$key] = $val;
                }
            }
        } else {
            if ($tpl_var != ''){
                $this->_css_vars[$tpl_var] = $value;
            }
        }
    }


    public function fetch($_template_file = false) {
        if($_template_file !== false){
    		$this->_template_file = $_template_file;
            ob_start();
            if(is_array($this->_template_vars)) {
	            extract($this->_template_vars, EXTR_SKIP);
			}
            include ($this->template_dir.$this->_template_file);
            $html = ob_get_clean();
            return $html;
        }
    }

    public function display($_template_file = false) {
		if($_template_file !== false){
			$this->_template_file = $_template_file;
			if(is_array($this->_template_vars)) {
				extract($this->_template_vars, EXTR_SKIP);
			}
			include ($this->template_dir.$this->_template_file);
			return;
	
		}
	}

	public function __call( $name, $arguments ){

		switch($name){
			case 'loadLocal':
				$this->_load($arguments,'local');
				break;

			case 'load':
				$this->_load($arguments);
				break;

			case 'render':
				if(!empty($arguments) && is_array($arguments) && count($arguments > 0)){
					$render_type = $arguments[0];
					$this->_display($render_type);
				}
				break;
			case 'displayCSS': // Backwards compatibility.
				$this->_displayCSS();
				break;
			case 'displayJS': // Backwards compatibility
				$this->_displayJS();
				break;

			default:
				sBook::_error("Method <i>$name</i> not available in Template class.");
				break;
		}
	}

	private function _load($files, $type='global'){
		if(is_array($files) && count($files)>0) {
			
			foreach($files as $file) {
				$file_type = $this->getExtension($file);//substr(strrchr($file, '.'),1);

	            if(!isset($this->_template_vars[$type][$file_type])){
	            	$this->_template_vars[$type][$file_type] = array();
	            }
	            $this->_template_vars[$type][$file_type][] = $file;
	        }
    	}
        
    }

	private function getCSSLink($path, $media="all"){
		
		//return  "<link rel=\"stylesheet\" href=\"$path\" type=\"text/css\" media=\"$media\">\r\n";
			$doc = new DOMDocument();
			$link = $doc->appendChild($doc->createElement('link'));
			$link->setAttribute("rel", "stylesheet");
			$link->setAttribute("href", $path);
			$link->setAttribute("type", "text/css");
			$link->setAttribute("media", $media);
			return $doc->saveHTML();

	}

	private function getJSScript($path, $is_new_version){
		if($is_new_version === true) {
			$path = $this->removeExtension($path);
			debug::dump($path);
		}
		$doc = new DOMDocument();
		$script = $doc->appendChild($doc->createElement('script'));
		$script->setAttribute("type", "text/javascript");
		$script->setAttribute("src", $path);
		return $doc->saveHTML();
	}

	

	private function getExtension($url = null){
        if($url === null) {
            $url = basename($_SERVER['REQUEST_URI']);
        }
		$dotPos = stripos($url,'.');

		
		if($dotPos>0){
			return strtolower(substr($url,$dotPos+1));
		}
		
		return null;
	}
	
	private function removeExtension($filename){
		$dotPos = stripos($filename,'.');
		return strtolower(substr($filename,0, $dotPos));
		
	}

	private function _display($type){
		$method = '_display'.strtoupper($type);
		$this->$method();
	}

	private function _displayCSS(){
		
		// Check if we've got any request for css files
		//
		if( (isset($this->_template_vars['global']) && count($this->_template_vars['global'])>0) ||  (isset($this->_template_vars['local']) && count($this->_template_vars['local'])>0) ){
	        
	        // Initialize the arrays
	        //
			$files_array        = array(); // Temporary array that will hold the files to process
			$files_to_display	= array(); // Array holding the files to minify after compiling
			$file_scopes_array	= array(); // Array holding the types of files (global | local) we're can load
			$old_time           = mktime(0,0,0,21,5,1980); // Old time to compare agains file mod times	
			
			// Because the template_vars contains many keys we have to check explicity for global and local
			//
			if(!empty($this->_template_vars['global'])){
				$file_scopes_array[] = $this->_template_vars['global'];
			}

			if(!empty($this->_template_vars['local'])){
				$file_scopes_array[] = $this->_template_vars['local'];
			}

			

			//Iterate over the file_types
			//
			foreach($file_scopes_array as $type => $file_types_array){

				$css_files  = (!empty($file_types_array['css'])) ? $file_types_array['css'] : array();
				$less_files = (!empty($file_types_array['less'])) ? $file_types_array['less'] : array();

				$files_array = array_unique(
					array_reverse(
						array_merge($css_files, $less_files)
					)
				); 	// We reverse because they are loaded FILO style and the correct order is FIFO;
					// We use array_unique to remove file duplicates

				
				if(!empty($less_files)){ // Lazy Instantiation. We only require and instantiate lessc if we need it.
					uses('lessc.inc');
					$less = new lessc(); // a blank less
	            	$less->setFormatter("compressed");
	            }

	            $time = $old_time; // Initialize the compare-to time to an old time

				foreach($files_array as $file){

					$full_file_path = TEMPLATES . $file;
					$file_type      = $this->getExtension($file);


					if(is_file($full_file_path)) { // Check if file actually exists
						if($file_type == 'less'){ // Lets process the less file
							$cache_file_path = TEMPLATES . $this->removeExtension($file) . '.css';					

	                		$less->checkedCompile($full_file_path, $cache_file_path); // Only compiles if mod time of compiled file is older
	                		
	                		$files_to_display[] = $cache_file_path;
	                		$file_time = filemtime($cache_file_path);

						} else if( $file_type == 'css'){
							$files_to_display[] = $full_file_path;
							$file_time = filemtime($full_file_path);

						}

						if($file_time > $time) {
							$time = $file_time;
						}


					} else {
						sBook::_error("File <code>{$file}</code> not found, referenced in view <code>{$this->_template_file}</code>");

					}
				}

				// Check against minified file
				$css_folder = dirname($files_to_display[0]); // Get the css folder from the path of the requested files
				$minified_file_path = $css_folder . '/global.min.css';

				if(is_file($minified_file_path)){
					$minified_file_time = filemtime($minified_file_path);

				} else {
					$minified_file_time = $old_time;

				}
				$minified_file_string = '';

				if($time > $minified_file_time){ // We need to concatenate all the files
					foreach($files_to_display as $file){
						$file_type   = $this->getExtension($file);
						$file_source = file_get_contents($file);
						
						if($file_type == 'css'){
							$file_source = $this->_compress_css($file_source);
						}
						$minified_file_string .= $file_source;
					}
					file_put_contents($minified_file_path, $minified_file_string);
				}
				$minified_file_time   = filemtime($minified_file_path);
				$minified_file_path   = str_replace('.min.', ".v{$minified_file_time}.min.", $minified_file_path);
				$minified_filename    = basename($minified_file_path);
				$templates_css_folder = basename($css_folder);
				$minified_path        = TEMPLATES_URI . $templates_css_folder . "/" . $minified_filename;
								
				echo $this->getCSSLink($minified_path);

			}

			
			
			
		
		}
	}

	

	private function _displayJS(){
		
		// Check if we've got any request for js files
		//

		if( (isset($this->_template_vars['global']) && count($this->_template_vars['global'])>0) ||  (isset($this->_template_vars['local']) && count($this->_template_vars['local'])>0) ){
			
			// Initialize the arrays
	        //
			$files_array        = array(); // Temporary array that will hold the files to process
			$files_to_display	= array(); // Array holding the files to minify after compiling
			$file_scopes_array	= array(); // Array holding the types of files (global | local) we're can load
			$old_time           = mktime(0,0,0,21,5,1980); // Old time to compare agains file mod times

			// Because the template_vars contains many keys we have to check explicity for global and local
			//
			if(!empty($this->_template_vars['global'])){
				$file_scopes_array['global'] = $this->_template_vars['global'];
			}

			if(!empty($this->_template_vars['local'])){
				$file_scopes_array['local'] = $this->_template_vars['local'];
			}
			//Iterate over the file_types
			//
			
			foreach($file_scopes_array as $type => $file_types_array){
				$js_files = (!empty($file_types_array['js'])) ? $file_types_array['js'] : array();
				$js_files = array_unique($js_files);

				$time     = $old_time; // Initialize the compare-to time to an old time

				foreach($js_files as $file){
					$full_file_path = TEMPLATES . $file;

					if(is_file($full_file_path)) {
						$file_time = filemtime($full_file_path);
						if($file_time > $time){
							$time = $file_time;
						}
						
					} else {
						sBook::_error("File <code>{$file}</code> not found, referenced in view <code>{$this->_template_file}</code>");
					}
					
				}

				// Check agains minified file
				//
				$js_folder          = dirname($js_files[0]); // Get the js folder from the path of the requested files
				$minified_file_path = TEMPLATES . $js_folder . "/$type.min.js";
				$is_new_version     = false;

				if(is_file($minified_file_path)){
					$minified_file_time = filemtime($minified_file_path);

				} else {
					$minified_file_time = $old_time;

				}

				$minified_file_string = '';
				$file_string          = '';

				if($time > $minified_file_time){ // We need to minify and concatenate all the files
					foreach($js_files as $file){
						$full_file_path = TEMPLATES . $file;
						$file_string .= "\r\n".file_get_contents($full_file_path);
					}

					$minified_file_string .= $this->_compress_js($file_string);
					file_put_contents($minified_file_path, $minified_file_string);
					$is_new_version = true;
				}

				$minified_file_time	= filemtime($minified_file_path);
				$minified_filename   = str_replace(".min.",".v{$minified_file_time}.min.",basename($minified_file_path));
				$templates_js_folder = $js_folder;
				$minified_path       = TEMPLATES_URI . $templates_js_folder . "/" . $minified_filename;

				echo $this->getJSScript($minified_path, $is_new_version);

			}

		}
	}



	public function __displayJS()
	{

		// Global JS
		//
		if(isset($this->_template_vars['global_js']) && is_array($this->_template_vars['global_js']) && count($this->_template_vars['global_js'])>0){
			
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
						//logmsg('minified: '.$file);
						$source[] = file_get_contents(TEMPLATES.$file);
					} else {
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
		if(isset($this->_template_vars['js']) && is_array($this->_template_vars['js']) && count($this->_template_vars['js'])>0){
			//logmsg('------------------------------------- LOCAL');
			
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
						
						$source[] = file_get_contents(TEMPLATES.$file);
					} else {
						
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

	private function _compress_js($js)
	{

		uses("jsmin");
    	return JSMin::minify($js);
	
	}

    private function _compress_css($css) {
        // remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // remove tabs, spaces, newlines, etc.
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
        
        return $css;
    }

	

}