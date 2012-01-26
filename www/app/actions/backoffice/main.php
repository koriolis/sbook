<?php
/**
 * Description of main.php
 *
 * @author Nuno Ferreira
 */
// FirePHP
pear('FirePHPCore/fb');

class controller_main extends controller{
	
	public $tab;
	public $subtab;
	public $view;
	public $request_method;
	public $request_url;
	public $error_message;

	
	public function initialize()
	{
	
		if(defined('ACCESS_RESTRICTED') && constant("ACCESS_RESTRICTED") == "1"){
			//HTTP AUTH
			
			if(!isset($_SERVER['PHP_AUTH_USER'])){
				header('WWW-Authenticate: Basic realm="Site Bel"');
				header('HTTP/1.0 401 Unauthorized');
				echo 'Esta é uma área reservada!';
				exit;
			}
			else{
				$user = $_SERVER['PHP_AUTH_USER'];
				$pass = $_SERVER['PHP_AUTH_PW'];

				if(!($user == ACCESS_USER && $pass == ACCESS_PASS)){
					$_SERVER['PHP_AUTH_USER'] = null;
					$_SERVER['PHP_AUTH_PW'] = null;

					header('HTTP/1.0 401 Unauthorized');
					echo 'User ou password inválidos!';
					exit;
				}
			}
			
		}
	
	
		uses('debug','util','template_functions','modelfields');


		session_start();
		
		if(!empty($_SESSION['errorMessage'])){
			$this->tpl->assign('errorMessage',$_SESSION['errorMessage']);
			unset($_SESSION['errorMessage']);
		}

		

		define('ROWS_PER_PAGE',30);
		define('PAGES_PER_GROUP',20);

		// Get request method and store it
		//
		$this->request_method	= Rest::getRequestMethod();
		$this->request_url		= BASEURI . substr($_SERVER["REQUEST_URI"],1);

		$this->parseErrorMessages();
		
	}

	public function index()
	{
		$this->_redirect('noticias/',BO_URI);
	}

	



	public function finalize()
	{

		$this->tpl->assign('tab',$this->tab);
		$this->tpl->assign('subtab',$this->subtab);
		$this->tpl->assign('subtab_menu_item',$this->subtab_menu_item);
		$this->tpl->display('backoffice/'.$this->view.'.tpl');
	}

	/****************************************************************
	 *  Protected Methods
	 ****************************************************************/
	
	/**
	 * Verifica se existe mensagem de erro na sessão.
	 * Caso existam:
	 *	- guarda na variável de classe error_message,
	 *	- apaga da sessão o erro (para que não se repita em caso de reload).
	 *  - faz assign do erro para a template.
	 *
	 * É da responsabilidade do view (template) 'mostrar' os erros caso existam.
	 */
	protected function parseErrorMessages()
	{
		if( !empty($_SESSION['error_message']) ){

			$this->error_message = $_SESSION['error_message'];

			unset($_SESSION['error_message']);

			$this->tpl->assign('error_message',$this->error_message);

		}
	}

	/**
	 * Adiciona erros à mensagem de erro da sessão
	 *
	 * @param string	$message - A mensagem de erro
	 * @param boolean	$append  - Se deve ser adicionada (com line feed) às mensagens pré-existentes
	 */
	protected function addErrorMessage($message, $append = false)
	{
		if($append === true) {
			$_SESSION['error_message'] .= "\r\n".$message;

		} else {
			$_SESSION['error_message'] = $message;
			
		}

	}

}