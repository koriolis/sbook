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
uses('debug','lessc.inc');
class Template
{
	private $_template_vars;
    private $_css_vars;
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
			'lifetime'                => 7200, // cache lifetime of 2 hours
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
                // Hack para ficheiros do tipo .less serem considerados .css pela rotina
                //
                if($file_type == 'less'){
                    $file_type = 'css';
                }
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
                // Hack para ficheiros do tipo .less serem considerados .css pela rotina
                //
                if($file_type == 'less'){
                    $file_type = 'css';
                }
                
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
        /*
        try {
            lessc::ccompile('input.less', 'out.css');
        } catch (exception $ex) {
            exit('lessc fatal error:<br />'.$ex->getMessage());
        }
        */

		// Se existirem ficheiros globais
		//
		if(is_array($this->_template_vars['global_css']) && count($this->_template_vars['global_css'])>0){
			
            $files = array_values($this->_template_vars['global_css']);

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
                    if(is_file(TEMPLATES.$file)) {
					   $source[] = file_get_contents(TEMPLATES.$file);
                    }
				}

                //$source = $this->compress_css(implode("",$source));
                $source = array_unique($source);
                $less = new lessc(); // a blank lessc
                   
                if(is_array($this->_css_vars) && !empty($this->_css_vars)) {
                    $compiled_source = $less->parse(implode("",$source), $this->_css_vars);
                } else {
                    $compiled_source = $less->parse(implode("",$source));
                }
                $compiled_source = $this->compress_css($compiled_source);

				file_put_contents($cached_filepath,$compiled_source);

				unset($this->_template_vars['css']);
			}
			echo '<link rel="stylesheet" href="'.$cached_file_uri.'" type="text/css" media="all">';
		}

		

		// Se existirem ficheiros locais
		//
		if(isset($this->_template_vars['css']) && is_array($this->_template_vars['css']) && count($this->_template_vars['css'])>0){

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
                    if(is_file(TEMPLATES.$file)) {
					   $source[] = file_get_contents(TEMPLATES.$file);
                    }
				}

				$source = array_unique($source);
                $less = new lessc(); // a blank lessc
                
                if(is_array($this->_css_vars) && !empty($this->_css_vars)) {
                    $compiled_source = $less->parse(implode("\r\n",$source), $this->_css_vars);                  
                } else {
                    $compiled_source = $less->parse(implode("\r\n",$source));                  
                }

				//$source = $this->compress_css(implode("",$source));
				file_put_contents($cached_filepath,$compiled_source);

				unset($this->_template_vars['css']);
			}
			echo '<link rel="stylesheet" href="'.$cached_file_uri.'" type="text/css" media="all">';
		}

		// Se existirem ficheiros media_print
		//
		if(isset($this->_template_vars['print_css']) && is_array($this->_template_vars['print_css']) && count($this->_template_vars['print_css'])>0){

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

	private function compress_js($js)
	{

		uses("jsmin");
    	return JSMin::minify($js);
	
	}

    private function compress_css($css) {
        // remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // remove tabs, spaces, newlines, etc.
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
        
        return $css;
    }

	

}