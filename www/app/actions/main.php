<?php

/**
 * Description of main
 *
 * @author Nuno Ferreira 
 */
set_include_path((get_include_path().':'.MODULES));

// setup the zend framework autoloader
//modules('Zend/Loader/Autoloader');
//Zend_Loader_Autoloader::getInstance()->registerNamespace('Zend_');

uses('template_functions','util','debug');
class controller_main extends controller{

	protected $area;
	protected $subarea;
	protected $view;
	protected $page_title;
	protected $page_description;
	protected $page_keywords;

	public function initialize()
	{
		

	}

	
	public function index()
	{
		$this->area 			= 'homepage';
		$this->page_title 		= 'Homepage';
		$this->page_description = 'Homepage';
		$this->page_keywords	= 'Homepage';
		$this->view		  		= 'homepage.tpl';
		
	}


	public function finalize()
	{
		$this->tpl->assign('area',$this->area);
		$this->tpl->assign('subarea',$this->subarea);
		$this->tpl->assign('page_title',$this->page_title);
		$this->tpl->assign('page_description',$this->page_description);
		$this->tpl->assign('page_keywords',$this->page_keywords);
		$this->tpl->assign('view',$this->view);

		$this->tpl->display('main.tpl');

	}

	protected function httpStatus($errorCode = 404)
	{
		header('Sogrape Vinhos', true, $errorCode);

		if($errorCode == 404) {
			$this->tpl->display('404.tpl');
			exit;
		}
	}


	


}
?>
