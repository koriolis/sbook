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
    public $dblink;
    public $baseuri;
    public $url;
    private $routes;

    static $soap_args;
    private static $instance;

    /**
     *
     */
    public function __construct()
    {
        self::$instance = $this;

        $config_file         = CONFIGS.SERVER_NAME.'.php';
        $default_config_file = CONFIGS.'default_config_file.php';
        $routes_file         = CONFIGS.'routes.php';


        
        
        if (!is_file($config_file)) { // If there's no config subdomain file
            
            if(!is_file($default_config_file)){ // If there's also not a default config file
                copy(SBOOK.'config/sample_config.php',$default_config_file);
            }
            self::load($default_config_file); // read the default config file

        } else {
            self::load($config_file); // read the subdomain config file
        }

        if (!is_file($routes_file)) { // If there's no config subdomain file
            copy(SBOOK.'config/sample_routes.php', $routes_file);
        }
        $routes = self::load(CONFIGS . 'routes.php');
        if(isset($routes[SERVER_NAME])){
            $this->routes = $routes[SERVER_NAME];

        } else if(isset($routes['default'])){
            $this->routes = $routes['default'];

        } else {
            sBook::_error("No subdomain or default route found @ <code>{$routes_file}</code>");
            exit;
        }
        
        $this->Dispatch();
    }



    private static function dump_constants($type = 'user'){
        $defined_constants = get_defined_constants(true);
        debug::dump($defined_constants[$type]);
    }

    public static function &_GetInstance()
    {
        return self::$instance;
    }

    
    
    public static function &DBLink($dsn=null)
    {
        pear('MDB2');
        $sBook =& sBook::_GetInstance();
        

        if($dsn === null) {
            $dsn = DEFAULT_DSN;
        }

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
            $pager['numgroups']         = ceil($pager['numpages']/$ppgroup);
            $pager['group']             = ceil($pager['page']/$ppgroup);
            $pager['group_start']       = ($pager['group']*$ppgroup-$ppgroup)+1;
            $pager['group_end']         = $pager['group_start']+$ppgroup-1;
            if($pager['group_end'] > $pager['last']) $pager['group_end'] = $pager['last'];
            
            $pager['next_group_start']  = ($pager['group']<$pager['numgroups'])?$pager['group_end']+1:$pager['group_start'];
            $pager['next_group_end']    = $pager['next_group_start']+$ppgroup-1;
            
            
            $pager['prev_group_end']    = ($pager['group']>1)?$pager['group_start']-1:$pager['group_start'];
            $pager['prev_group_start']  = $pager['prev_group_start']+$ppgroup-1;
        }

        return $pager;
    }

    private function _notFound()
    {
        require_once(CORE . 'controller.php');
        $controller = new Controller();
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

    private function Dispatch()
    {
        // Instantiate the Mapper class
        //
        $mapper = Net_URL_Mapper::getInstance();

        // Iterate over the defined routes in the config
        // and compile them
        //
        foreach($this->routes as $match=>$route){
            if(isset($route['rules'])){ // Dynamic routing: If there are rules to apply
                $mapper->connect($match, $route['map'], $route['rules']);

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
            //sBook::_error("Routing: ". $e->getMessage());
            exit;
        }

        

        // If there's no route found then throw a 404 - not found
        //
        if($route === null){
            $this->_notFound();
            exit;

        } else { // We got a valid route
            $controller_path = (dirname($route['controller']) !== '' && dirname($route['controller']) !== '.' && dirname($route['controller']) !== DS ) ? dirname($route['controller']).DS : '';
            $controller      = basename($route['controller']);
            $action          = $route['action'];

            unset($route['controller'], $route['action'], $route['controller_path']);

            // Get and pass the Request Method (TODO: A full rest method routing would call a method based on the request method. ie. if the action is index
            // and the request method is POST, it should call Controller::postIndex instead of Controller::index or something similar.
            // For now we will pass the request method as an additional arg to the Controller method called.
            // 
            $route['request_method'] = strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get');

            $controllerFileName  = $controller . '.php';        // Controller filenames are always ".php" files with whatever we pass in the controller param as filename
                                                                // ex: filename for "controller" = "foo" is "foo.php"
            
            $controllerClassName = 'controller_'.$controller;   // Controller with whatever we pass in the controller param as class name prefixed with "controller_"
                                                                // ex: classname for "controller" = "foo" is "controller_foo"

            $methodName = $action;                              // Method name is whatever we pass in the controller param as method name

            $controller_full_path = ACTIONS . $controller_path . $controllerFileName;
            
            if(is_file($controller_full_path)){
                require_once($controller_full_path);                  // Require the controller file
                
                if(!class_exists($controllerClassName)){
                    $this->_error("Class <code>{$controllerClassName}</code> does not exist @ requested controller: <code>{$controller_full_path}</code>");
                    
                }

                $controller = new $controllerClassName();       // Instantiate the controller class

                
                if(isset($action) && method_exists($controller, $methodName) && is_callable(array($controller, $methodName), true)){
                    
                    // Call the intitialize, action, and finalize methods passsing the same params as we pass to the method name
                    //
                    $controller->initialize($route);

                    call_user_func_array( array(&$controller, $methodName), array($route) );    // Passing the params inside an array so we get the same params 
                                                                                                // array as the hook methods (initialize and finalize)

                    $controller->finalize($route);


                } else {

                    if(!isset($action)){
                        $this->_error('Route Action param not defined');

                    } else if(!method_exists($controller, $methodName)){
                        $this->_error("Route action does not exist @ class: {$controllerClassName}, file: ({$controllerFileName})");

                    } else if(!is_callable(array($controller, $methodName), true)){
                        $this->_error("Route action is not callable @ class: {$controllerClassName}, file: ({$controllerFileName})");

                    } else {
                         $this->_error("Unknown Routing error");
                    }

                }
            } else {
                $this->_error("The Requested Controller: <code>{$controller_full_path}</code> was not found.");
            }
        }
        exit;
    }

    public static function _error($msg){
        
        include(CORETEMPLATES . 'sbook_error.tpl');
        exit;
    }

    
}
