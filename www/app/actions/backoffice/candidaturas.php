<?php
/**
 *
 * @author Nuno Ferreira
 */
class controller_candidaturas extends controller_main{

	public $tab 	= 'candidaturas';
	//public $subtab = 'noticias';


	public function index()
	{
		
		// Obter listagem paginada consoante os parametros passados por get
		//
		$list = Make::model('candidaturas_espontaneas')->listPaged($this->parseGetParameters());
		$this->tpl->assign('list', $list);

		$this->view = 'candidaturas';
	}
	
	
	
	
	
	public function apagar($itemId=null) {
		if($itemId !== null){
			models('candidaturas_espontaneas');

			$redirect_url = str_replace('/apagar/'.$itemId, '', $this->request_url); // Definir o url para ir para a lista
			// Buscar a row para apagar
			//
			$candidatura = new candidaturas_espontaneas($itemId);
			// Caso exista então apagamos
			//
			if($candidatura->is_record){
				// Se houver imagens/ficheiros, apagar
				
				if (!empty($candidatura->cv)) {
					$fileFicheiroPath = UPLOADS.'cvs'.DS.$candidatura->cv;
					if(is_file($fileFicheiroPath)){
						@unlink($fileFicheiroPath);
					}		
				}
				
				$candidatura->delete();
			}


			$this->_redirect($redirect_url,''); // O '' no fim é para definir um prefixo vazio ao path de redirect
												// em vez de BASEURI. -> Ver método _redirect@Controller
			
		} else {

		}
	}
	
	
	/*
	 * Parsa e valida os parâmetros passados por post para adicionar / editar
	 */
	private function parsePostParameters()
	{
		$filter_options = array(
			'nome'			=> FILTER_SANITIZE_STRING,
			'email'				=> FILTER_SANITIZE_STRING
		);

		return filter_input_array(INPUT_POST, $filter_options);
	}

	/**
	 *
	 * @return array 
	 */
	private function parseGetParameters()
	{

		// Filtrar os parâmetros passados por get.
		// Apesar de ser um backoffice, convém não dar abébias
		//
		$filter_options = array(
			'page'			=> FILTER_VALIDATE_INT,
			'id'			=> FILTER_VALIDATE_INT,
			'name'			=> FILTER_SANITIZE_STRING,
			'created_on'	=> array(
			'filter'	=> FILTER_VALIDATE_REGEXP,
			'options'	=> array('regexp'=> '/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/')
			),
			'sortField'		=> FILTER_SANITIZE_STRING,
			'sortDirection'	=> array(
					'filter'	=> FILTER_VALIDATE_REGEXP,
					'options'	=> array("regexp"=>"/^(asc|desc)*$/")
			)
		);

		return filter_input_array(INPUT_GET, $filter_options);
	}


	
	public function uploadImgTexto() {
		pear('HTTP/Upload');
		$upload = new HTTP_Upload('pt');
		$upload->setChmod(0777);
		
		$imagem = $upload->getFiles('upload');
		if($imagem->isValid()){
			$imagem->setName(util::string_normalize($imagem->getProp('name')));
			$moved = $imagem->moveTo('app/uploads/noticias/img_corpo');
			
		} 
		$img = UPLOADS_URI."noticias/img_corpo/".$imagem->getProp('name');
		$strOutput = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction(4, "'.$img .'", "");</script>';
		
		exit($strOutput);
	}
}