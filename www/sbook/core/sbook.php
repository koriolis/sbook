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
    public $dblink = null;
    public $baseuri = null;
	static $soap_args = null;

    /**
     *
     */
    function sBook()
    {
		
    }

    function &_GetInstance()
    {
        static $instance = null;

        if (!isset($instance))
        {
            $instance = new sBook();
        }
        return $instance;
    }

    function &DBLink($dsn=null)
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

    static function Pager($page,$rows,$pprow,$ppgroup=null)
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

    /**
     * This section dispatches the action
     *
     */

    function Dispatch()
    {
        if(isset($_GET['url']) or (count($_GET) == 0))
        {
            $url = $_GET['url'];
            sBook::DispatchRewrite($url);
        }
        else
        {
            sBook::DispatchSimple();
        }
    }
    
    
    function DispatchSimple()
    {
        $url = implode('/',$_GET);
        sBook::DispatchRewrite($url);
    }

    function FindDefaultController($folder)
    {
        $folder_name = basename($folder);
    }

    function DispatchRewrite($url)
    {
	
	
	
        $url_parts = explode('/',$url);
        //Folder
        $folder_name = '';
        $folder = ACTIONS; // Default folder
        while (is_dir($folder.$url_parts[0]) && ($url_parts[0]!=''))
        {
            $folder = $folder.$url_parts[0].DS;
            $folder_name = $url_parts[0];
            $url_parts = array_slice($url_parts,1);
        }

        //Controller
        if(file_exists($folder.$url_parts[0].'.php'))
        {
            $controller_name = $url_parts[0];
            $url_parts = array_slice($url_parts,1);
        }
        elseif(file_exists($folder.$folder_name.'.php'))
        {
            $controller_name = $folder_name; // Default controller
        }
        else
        {
            $controller_name = 'main';
        }

        // Include the required controllers
	//
        if($controller_name == 'main')
        {
            require_once($folder.'main.php'); // Always include
        }
        else
        {
            $require_array = array();
            $temp_folder = $folder;
	    
            while(!file_exists($temp_folder.'main.php'))
            {
                $dir = dirname($temp_folder).DS;
                $base = basename($temp_folder);
                array_unshift($require_array,$temp_folder.$base.'.php');
                $temp_folder = $dir;
            }
            array_unshift($require_array,$temp_folder.'main.php');
	    
            foreach($require_array as $req_file) {
                require_once($req_file);
            }
	    	    
            //require($folder.$controller_name.'.php');
            require_once($folder.$controller_name.'.php');
        }

        // Instantiate the controller
        // All controller classes have the prefix 'controller_'
        // to avoid name clashes with other classes
        $controller_name = 'controller_'.$controller_name;
        $controller = new $controller_name();

        //Action
        if(method_exists($controller,$url_parts[0]))
        {
            $action = $url_parts[0];
            $url_parts = array_slice($url_parts,1);
        }
        else
        {
            $action = 'index';
        }

        // Clean empty params
        $params = array();
        foreach($url_parts as $part)
        {
            if($part != "") $params[] = $part;
        }
	
        // Call the intitialize, action, and finalize methods;
        $controller->initialize();
        call_user_func_array(array(&$controller,$action),$params);
        $controller->finalize();
    }
    function _DispatchRewrite($url)
    {
	uses('debug');
	pear('FirePHPCore/fb');
		 
	// Variaveis para acesso a 404
        //
        $controllerExists   = false;
        $methodExists       = false;
    	$folderExists       = false;
        $actionExists       = false;
    	$show404            = false;
    	
        $url_parts = explode('/',$url);
        
        // Folder
        //
        $folder_name = '';
        $folder = ACTIONS; // Default folder
        
        while (is_dir($folder.$url_parts[0]) && ($url_parts[0]!=''))
        {
        	$folderExists   = true;
            
            $folder         = $folder.$url_parts[0].DS;
            $folder_name    = $url_parts[0];
            $url_parts      = array_slice($url_parts,1);
        }
        
        
        // Controller
        //
        if(file_exists($folder.$url_parts[0].'.php'))
        {
            $controllerExists = true;
            $controller_name = $url_parts[0];
            $url_parts = array_slice($url_parts,1);
        }
        elseif(file_exists($folder.$folder_name.'.php'))
        {
            $controllerExists = true;
            $controller_name = $folder_name; // Default controller (controller name = $folder name)
        }
        else
        {
            $controllerExists = false;
            $controller_name = 'main';
        }
        
       
        

        // Include the required controllers
        //
        if($controller_name == 'main' && file_exists($folder.'main.php')) {
            require_once($folder.'main.php'); // Always include
            
        } else {
            
            $require_array = array();
            $temp_folder = $folder;
            
            while(!file_exists($temp_folder.'main.php'))
            {
                $dir = dirname($temp_folder).DS;
                $base = basename($temp_folder);
                array_unshift($require_array,$temp_folder.$base.'.php');
                $temp_folder = $dir;
            }
            array_unshift($require_array,$temp_folder.'main.php');
            
            foreach($require_array as $req_file){
                if(file_exists($req_file)) {
                    require_once($req_file);
                }
            }
            
            if(file_exists($folder.$controller_name.'.php')) {
                require_once($folder.$controller_name.'.php');
            }
        }
        
               

        // Instantiate the controller
        // All controller classes have the prefix 'controller_'
        // to avoid name clashes with other classes
        //
        $controller_name = 'controller_'.$controller_name;
        $controller = new $controller_name();
        

        // Action
        //
        if(method_exists($controller,$url_parts[0]) && is_callable(array($controller,$url_parts[0])) ) {
            $methodExists = true;
            $actionExists = true;
            
            $action = $url_parts[0];
            $url_parts = array_slice($url_parts,1);
            
            
        } else {
            $methodExists = false;
            
            if(method_exists($controller,'index') && is_callable(array($controller,'index'))) {
                $actionExists = true;
                $action = 'index';
                
            } else {
                $actionExists = false;
                $action = '_error_404';
                
            }
        }
        
        // If we turn on 404 pages check if the url is valid, else show 404
        //
        //$method = new ReflectionMethod('controller','DefaultAction');
	//debug::dump($method->getParameters());
	//exit;
	
	$log_array = array(
	    'folderExists'	=> $folderExists,
	    'controllerExists'	=> $controllerExists,
	    'methodExists'	=> $methodExists,
	    'actionExists'	=> $actionExists
	);
	
	fb::log($log_array);
	
        
        if (SHOW404 == true){

	    // Empty URL == BASEURI ex: http://domain.com/
            //
	    if(!empty($url)) { // Se o url estiver vazio quer dizer que vamos chamar o controller_main->index em /main.php

		if($folderExists == false && $controllerExists === false) { 
		    $show404 = true;
		
		} else if($folderExists == true && $controllerExists==false){
		    $show404 = true;
		    
		} else {					
			// Check to see if params are allowed
			//
			$params = array();
			foreach($url_parts as $part)
			{
			    if($part != "") $params[] = $part;
			}
			
			$method				= new ReflectionMethod($controller_name,$action);
			$numParams			= $method->getNumberOfParameters();
			$numRequiredParams	= $method->getNumberOfRequiredParameters();
			$methodParams		= $method->getParameters();

			if($numParams == 0 && count($params) > 0) {
				$show404 = true;

			} else if($numParams > 0 && count($params) < $numRequiredParams){
				$show404 = true;
				
			}
			
		}


	    }

	    // Show 404
            //
	    if ($show404==true){
		//header('location:'.PAGE404_URI);
		debug::dump('404');
				
	    } else {
		
	    }

        }
        		
        // Clean empty params
		//
        $params = array();
        foreach($url_parts as $part)
        {
            if($part != "") $params[] = $part;
        }

        // Call the intitialize, action, and finalize methods;
        $controller->initialize();
        call_user_func_array(array(&$controller,$action),$params);
        $controller->finalize();

		
		
    }

	
}