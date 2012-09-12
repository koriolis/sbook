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
 * TODO 
 */
uses('debug');
pear('Net' . DS . 'URL' . DS . 'Mapper');
require_once(SBOOK.'core/controller.php');

/**
 * sBook 
 *
 * TODO
 * 
 * @package 
 * @copyright 2004-2006 Wiz Interactive
 * @author Jorge Pena <jmgpena@gmail.com> 
 */
class sBook
{
    public $dblink  = null;
    public $baseuri = null;
    public $url     = null;
    private $routes = null;

	static $soap_args = null;

    /**
     *
     */
    public function __construct()
    {
        $this->routes     = parse_ini_string(ROUTES,true);
        $this->routes_alt = self::load(CONFIGS . SERVER_NAME . '-routes.php');
        
        $this->Dispatch();
    }

    private static function dump_constants($type = 'user'){
        $defined_constants = get_defined_constants(true);
        debug::dump($defined_constants[$type]);
    }

    public static function &_GetInstance()
    {
        static $instance = null;

        if (!isset($instance)) {
            $instance = new sBook();
        }
        return $instance;
    }

    public static function &DBLink($dsn=null)
    {
        pear('MDB2');
        $sBook =& sBook::_GetInstance();

		if($dsn === null) $dsn = DEFAULT_DSN;
        
        //var_dump($sBook->dblink->database_name . '/ '. $dsn);
        $dsn_dbname = array_pop(explode('/', $dsn));

        //if ( !isset($sBook->dblink) || $sBook->dblink->database_name !== $dsn_dbname )
        if( !(isset($sBook->dblink) && $sBook->dblink->database_name == $dsn_dbname) ) 
        { // connect to db

            $sBook->dblink =& MDB2::connect($dsn);
			
            if (PEAR::isError($sBook->dblink))
            {
                trigger_error('sBook::DBLink: '.$sBook->dblink->getDebugInfo());
            }
            else
            {
				//if(is_array($options)) foreach($options as $option=>$value) $sBook->dblink->setOption($option,constant($value));
                $sBook->dblink->setFetchMode(MDB2_FETCHMODE_ASSOC);
            }
        }
        
        return $sBook->dblink;
    }

    public static function Pager($page,$rows,$pprow,$ppgroup=null)
    {
        $pager['numpages'] = (($rows - ($rows % $pprow)) / $pprow);
        if( ($rows % $pprow) > 0 ) $pager['numpages']++;
        if (($page > 0) || ($page <= $pager['numpages']))
        {
            $pager['page'] = $page;
        }
        else
        {
            $pager['page'] = 1;
        }
        $pager['next'] = ($pager['page'] < $pager['numpages'])?$page+1:0;
        $pager['prev'] = ($pager['page'] > 1)?$page-1:0;
        $pager['first'] = 1;
        $pager['last'] = $pager['numpages'];
        $pager['offset'] = ($pager['page'] - 1) * $pprow;
        $pager['limit'] = $pprow;
        $pager['count'] = $rows;

        // 0 based index
        //
        $pager['page_first_row'] = $pager['offset']; 
        $pager['page_last_row'] = min(($pager['page_first_row']+$pager['limit']),($pager['count']-1));
        
        
        if($ppgroup != null){
			//Calculate groups if any
			$pager['numgroups'] 		= ceil($pager['numpages']/$ppgroup);
			$pager['group']				= ceil($pager['page']/$ppgroup);
			$pager['group_start']		= ($pager['group']*$ppgroup-$ppgroup)+1;
			$pager['group_end']			= $pager['group_start']+$ppgroup-1;
			if($pager['group_end'] > $pager['last']) $pager['group_end'] = $pager['last'];
			
			$pager['next_group_start']	= ($pager['group']<$pager['numgroups'])?$pager['group_end']+1:$pager['group_start'];
			$pager['next_group_end']	= $pager['next_group_start']+$ppgroup-1;
			
			
			$pager['prev_group_end'] 	= ($pager['group']>1)?$pager['group_start']-1:$pager['group_start'];
			$pager['prev_group_start']	= $pager['prev_group_start']+$ppgroup-1;
        }

        return $pager;
    }

    private function _notFound()
    {
        require_once(CORE . 'controller.php');
        $controller = new controller();
        $controller->_error_404();
        exit;
    }

    public static function load($file)
    {
        return include $file;
    }

    /**
     * This section dispatches the action
     *
     */

    public function Dispatch()
    {
        // Instantiate the Mapper class
        //
        $mapper = Net_URL_Mapper::getInstance();

        // Iterate over the defined routes in the config
        // and compile them
        //

        foreach($this->routes_alt as $match=>$route){
            if(isset($route['params'])){ // Dynamic routing: If there are additional params
                $mapper->connect($match, $route['map'], $route['params']);
            } else { // Static Routing: No additional parameters
                $mapper->connect($match, $route['map']);
            }
        }
        
        // Map URL to route
        //
        try {
            $route = $mapper->match($_SERVER['REQUEST_URI']);
            
        } catch (Net_URL_Mapper_InvalidException $e){ // If a path conforms to a given structure, but contains invalid parameters, catch the exception here. And throw not found.
            $this->_notFound();
            exit;
        }

        // If there's no route found then throw a 404 - not found
        //
        if($route === null){
            $this->_notFound();
            exit;

        } else { // We got a valid route

            $route['params']     = (isset($route['params']) ? $route['params'] : array());  // call_user_func_array below needs always 2 params, if we don't pass params in the
                                                                                            // route then we need to pass an empty array as the argument
            
            $controllerFileName  = $route['controller'] . '.php';       // Controller filenames are always ".php" files with whatever we pass in the controller param as filename.
                                                                        // ex: filename for "controller" = "foo" is "foo.php"
            
            $controllerClassName = 'controller_'.$route['controller'];  // Controller with whatever we pass in the controller param as class name prefixed with "controller_"
                                                                        // ex: classname for "controller" = "foo" is "controller_foo"

            $methodName = $route['action'];                             // Method name is whatever we pass in the controller param as method name

            require_once(ACTIONS . $controllerFileName);    // Require the controller file 
                                                            // TODO: use folder param to be able to include a file inside a folder.

            $controller = new $controllerClassName();       // Instantiate the controller class

            // Only 
            if(isset($route['action']) && method_exists($controller,$methodName) && is_callable(array($controller, $methodName), true)){
                
                // Call the intitialize, action, and finalize methods;
                //
                $controller->initialize();

                call_user_func_array( array(&$controller, $methodName), $route['params'] );

                $controller->finalize();

            } else {

                if(!isset($route['action'])){
                    $this->_error('Route Action param not defined');

                } else if(!method_exists($controller,$methodName)){
                    $this->_error("Route action does not exist @ class: {$controllerClassName}, file: ({$controllerFileName})");

                } else if(!is_callable(array($controller, $methodName), true)){
                    $this->_error("Route action is not callable @ class: {$controllerClassName}, file: ({$controllerFileName})");

                } else {
                     $this->_error("Unknown Routing error");
                }



            }
        }
        exit;
    }

    public static function _error($msg){
        
        include(CORETEMPLATES . 'sbook_error.tpl');
        exit;
    }

	
}
