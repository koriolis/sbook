<?php
    /**
     * Spellbook: Web application framework
     *
     * Copyright Wiz Interactive (2006)
     *
     * Spellbook is yet another MVC web framework written in PHP. It is 
     * oriented on simplicity and speed. It has been used internally to build 
     * sites for several clients and as such it is perfectly usable in its 
     * current incarnation. Uses PEAR for library routines and Smarty templating.
     *
     * This program is free software; you can redistribute it and/or
     * modify it under the terms of the GNU General Public License
     * as published by the Free Software Foundation; either version 2
     * of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with this program; if not, write to the Free Software
     * Foundation, Inc., 51 Franklin Street, Fifth Floor, 
     * Boston, MA  02110-1301, USA.
     *
     * @author       Jorge Pena <jmgpena@gmail.com>
     * @version 3.0
     * @package sbook
     */

    /**
     * Set Include Path for PEAR
     */
    set_include_path('.'.PATH_SEPARATOR.PEAR);

    /**
     * Set Default Timezone
     */
    date_default_timezone_set('Europe/Lisbon');
    

    /**
     * Basic defines
     */
    define('SECOND',  1);
    define('MINUTE', 60 * SECOND);
    define('HOUR',   60 * MINUTE);
    define('DAY',    24 * HOUR);
    define('WEEK',    7 * DAY);
    define('MONTH',  30 * DAY);
    define('YEAR',  365 * DAY);

    /**
     * Lists PHP files in given directory.
     *
     * @param string $path Path to scan for files
     * @return array List of files in directory
     */
    function listClasses($path) 
    {
        $modules = new Folder($path);
        return $modules->find('(.+)\.php');
    }

    function trace($var){
        echo "<pre style='font-family:monospaced;'>";
        var_dump($var);
        echo "</pre>";
        exit;
    }

    /**
     * Loads component/components from LIBS.
     *
     * Example:
     * <code>
     * uses('flay', 'time');
     * </code>
     *
     * @uses LIBS
     */
    function uses ()
    {
        $args = func_get_args();
        
        foreach ($args as $arg) 
        {
            
            if(file_exists(APPLIBS.$arg.'.php'))
            {
                require_once(APPLIBS.$arg.'.php');
            }
            else
            {
                require_once(LIBS.$arg.'.php');
            }
        }
    }

    /**
     * Require given files in the Models directory. Function takes optional number of parameters.
     *
     * @uses MODELS
     */
    function models()
    {
        uses('model');
        $args = func_get_args();
        foreach ($args as $arg) 
        {
            require_once(MODELS.$arg.'.php');
        }
    }

    /**
     * Require given files in the Modules directory. Function takes optional number of parameters.
     *
     * @uses MODULES
     */
    function modules()
    {
        $args = func_get_args();
        foreach ($args as $arg) 
        {
            if(file_exists(APPMODULES.$arg.'php'))
            {
                require_once(APPMODULES.$arg.'php');
            }
            else
            {
                require_once(MODULES.$arg.'.php');
            }
        }
    }

    /**
     * Require given files in the PEAR library. Function takes optional number of parameters.
     *
     * @param string $name Filename without the .php part.
     *
     */
    function pear()
    {
        $args = func_get_args();
        foreach ($args as $arg) 
        {
            require_once(PEAR.$arg.'.php');
        }
    }

    /**
     * The hostname part of the URI 
     */

    define('SERVER_NAME',parse_url('http://'.$_SERVER['HTTP_HOST'],PHP_URL_HOST));


    /**
     * config 
     *
     * Reads a value or array from the config file
     * 
     * @param string $path Path of value or values in XML Config file
     * @return Config value or associative array if is a set of values
     */
    function config($path = null)
    {
        if (!defined('CONFIG')) {
            // Ex. www.wiz.pt.cfg
            
            $config_file = CONFIGS.SERVER_NAME.'.php';
            if (!file_exists($config_file)) {
                copy(SBOOK.'config/sample_config.php',$config_file);
                echo("New config file created: '".$config_file."'<br />");
            }

            require_once($config_file); // read the config file
        }

        $parts = explode('/',$path);
        $name  = strtoupper(implode('_',$parts));
        if (defined($name))
            return constant($name);
        else
            return null;
    }

    /**
     * Basepath for links in the site. 
     */
    $basics_baseuri = config('baseuri');



    if (empty($basics_baseuri))
        if(!defined('BASEURI')) define('BASEURI','http://'.SERVER_NAME.'/');
    else
        if(!defined('BASEURI')) define('BASEURI',config('baseuri'));
    /**
     * Level of debugging for this app.
     */
    define('DEBUG',config('error/debug'));

    /**
     * Set the error log with the same name as the server name. 
     */
    ini_set("error_log",LOGS.SERVER_NAME.'.log');


    /**
     * User defined error handler function
     *
     */
    function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
       $curr_level = error_reporting();
       if (($errno & $curr_level) != $errno) {
           return;
       }
       // timestamp for the error entry
       $dt = date("Y-m-d H:i:s (T)");

       // define an assoc array of error string
       // in reality the only entries we should
       // consider are E_WARNING, E_NOTICE, E_USER_ERROR,
       // E_USER_WARNING and E_USER_NOTICE
       $errortype = array (
                   E_ERROR           => "Error",
                   E_WARNING         => "Warning",
                   E_PARSE           => "Parsing Error",
                   E_NOTICE          => "Notice",
                   E_CORE_ERROR      => "Core Error",
                   E_CORE_WARNING    => "Core Warning",
                   E_COMPILE_ERROR   => "Compile Error",
                   E_COMPILE_WARNING => "Compile Warning",
                   E_USER_ERROR      => "User Error",
                   E_USER_WARNING    => "User Warning",
                   E_USER_NOTICE     => "User Notice",
                   E_STRICT          => "Runtime Notice"
                   );
       // set of errors for which a var trace will be saved
       $user_errors = array(E_USER_ERROR, E_USER_WARNING);
      
       $err  = "--------------------------------------------------------------------------\n";
       $err .= "[".$dt."]:".$errno." ".$errortype[$errno]."\n";
       $err .= "@ ".$filename.":".$linenum."\n";
       $err .= "Msg: ".$errmsg."\n";

       if (in_array($errno, $user_errors)) {
           $err .= print_r($vars,true)."\n";
       }
      
       if(DEBUG>0) {
           echo "<pre style='display:block;font-family:Consolas,monospaced;padding:10px;'>".$err."</pre>";
       } else {
           // save to the error log, and e-mail me if there is a critical user error
           error_log($err, 3, LOGS.$_SERVER['SERVER_NAME'].'.log');
           if ($errno == E_USER_ERROR) {
               mail(config('error/mail'), "Critical User Error", $err);
           }
       }
    }

    /**
     * Set the PHP error handler to our above function.  
     */
    $old_error_handler = set_error_handler("userErrorHandler");


    /**
     * Setup a debug point.
     *
     * @param boolean $var
     * @param boolean $show_html
     */
    function debug($var = false, $show_html = false) 
    {
        if ($show_html) {
            $var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
            $output  = "\n<pre>\n";
            $output .= print_r($var,true);
            $output .= "\n</pre>\n";
        }
        else
        {
            $output = print_r($var,true);
        }

        if (DEBUG>0) {
            print($output);
        } else {
            error_log($output);
        }
    }

    /**
     * Log a message to the log file
     *
     * @param string $msg
     */
    function logmsg($msg)
    {
        error_log($msg);
    }
    
    
	function get_method_closure($object,$method_name){
		if(method_exists(get_class($object),$method_name)) {
			$func            = create_function( '',
'
                                $args            = func_get_args();
                                static $object    = NULL;
                               
                                /*
                                * Check if this function is being called to set the static $object, which
                                * containts scope information to invoke the method
                                */
                                if(is_null($object) && count($args) && get_class($args[0])=="'.get_class($object).'"){
                                    $object = $args[0];
                                    return;
                                }

                                if(!is_null($object)){
                                    return call_user_func_array(array($object,"'.$method_name.'"),$args);
                                }else{
                                    return FALSE;
                                }'
                            );
           
            //Initialize static $object
            $func($object);
           
            //Return closure
            return $func;
        }else{
            return false;
        }       
    } 
    


    if (!function_exists('getMicrotime')) 
    {
    /**
     * Returns microtime for execution time checking.
     *
     * @return integer
     */
        function getMicrotime() 
        {
            list($usec, $sec) = explode(" ", microtime());
            return ((float)$usec + (float)$sec);
        }
    }

    if (!function_exists('sortByKey')) 
    {
    /**
     * Sorts given $array by key $sortby.
     *
     * @param array $array
     * @param string $sortby
     * @param string $order Sort order asc/desc (ascending or descending).
     * @param integer $type
     * @return mixed
     */
        function sortByKey(&$array, $sortby, $order='asc', $type=SORT_NUMERIC) 
        {
            if (!is_array($array))
                return null;

            foreach ($array as $key => $val)
            {
                $sa[$key] = $val[$sortby];
            }

            $order == 'asc'
                ? asort($sa, $type)
                : arsort($sa, $type);

            foreach ($sa as $key=>$val)
            {
                $out[] = $array[$key];
            }

            return $out;
        }
    }

    if (!function_exists('array_combine')) 
    {
    /**
     * Combines given identical arrays by using the first array's values as keys,
     * and the second one's values as values. (Implemented for back-compatibility with PHP4.)
     *
     * @param array $a1
     * @param array $a2
     * @return mixed Outputs either combined array or false.
     */
        function array_combine($a1, $a2) 
        {
            $a1 = array_values($a1);
            $a2 = array_values($a2);
            $c1 = count($a1);
            $c2 = count($a2);

            if ($c1 != $c2) return false; // different lenghts
            if ($c1 <= 0)   return false; // arrays are the same and both are empty

			$output = array();

			for ($i = 0; $i < $c1; $i++)
			{
				$output[$a1[$i]] = $a2[$i];
			}

			return $output;
		}
	}
?>