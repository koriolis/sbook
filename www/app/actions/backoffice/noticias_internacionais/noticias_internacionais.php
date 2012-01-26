<?php
/**
 *
 * @author Nuno Ferreira
 */
class controller_noticias_internacionais extends controller_main{

	public $tab 	= 'noticias_internacionais';
	//public $subtab = 'noticias';


	public function index()
	{
		
		// Obter listagem paginada consoante os parametros passados por get
		//
		$list = Make::model('noticias_internacionais')->listPaged($this->parseGetParameters());
		$this->tpl->assign('list', $list);

		$this->view = 'noticias_internacionais';
	}
	
	
	public function adicionar()
	{

		// Filtrar os parâmetros passados por get.
		//
		$get = $this->parseGetParameters();

		
		if($this->request_method == 'get'){ // Show Form
			$this->view = 'noticias_internacionais_adicionar';

		} else if($this->request_method == 'post') { // Process Form
			
			// Filtrar os parâmetros enviados por POST
			//
			
			$post = $this->parsePostParameters();

			if(!empty($post['titulo']) && !empty($post['data']) ){
			
				pear('HTTP/Upload');
				$upload = new HTTP_Upload('pt');
				$upload->setChmod(0777);
				

				$redirect_url = str_replace('/adicionar', '', $this->request_url); // Definir o url para ir para a lista
				
				$noticia = Make::model('noticias_internacionais');
				// Campos directos
				$noticia->titulo 	= $post['titulo'];
				$noticia->subtitulo = $post['subtitulo'];
				$noticia->data		= $post['data'];

				// Processar upload de imagem e ficheiro se existirem
				
				// Ficheiro
				//
				$ficheiro = $upload->getFiles('ficheiro');
				if($ficheiro->isValid()){
					$ficheiro->setName(util::string_normalize($ficheiro->getProp('name')));
					$moved = $ficheiro->moveTo('app/uploads/noticias_internacionais/ficheiros');
					
					if (PEAR::isError($moved)) {
						$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities($moved->getMessage()))));
						$this->view = 'noticias_internacionais_adicionar';
					}
					
					$noticia->ficheiro = $ficheiro->getProp('name');

				} else {
					$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities($ficheiro->errorMsg()))));
					$this->view = 'noticias_internacionais_adicionar';
				}
				
				// Imagem
				//
				$imagem = $upload->getFiles('imagem');
				if($imagem->isValid()){
					$imagem->setName(util::string_normalize($imagem->getProp('name')));
					$moved = $imagem->moveTo('app/uploads/noticias_internacionais/imagens');
					
					if (PEAR::isError($moved)) {
						$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities($moved->getMessage()))));
						$this->view = 'noticias_internacionais_adicionar';
					}
					
					$noticia->imagem = $imagem->getProp('name');

				} else {
					$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities($ficheiro->errorMsg()))));
					$this->view = 'noticias_internacionais_adicionar';
				}


				$noticia->save();
				
			} else if(empty($post['titulo'])) {
				$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities('O campo Título é obrigatório.'))));
				$this->view = 'noticias_internacionais_adicionar';
			} else if(empty($post['data'])) {
				$this->tpl->assign('error_message', str_replace('%','\u00',rawurlencode(htmlentities('O campo Data é obrigatório.'))));
				$this->view = 'noticias_internacionais_adicionar';
			}
			
			$this->view = 'noticias_internacionais_adicionar';
			
		}
		
	}
	
	
	public function editar($itemId=null) {
	
		models('noticias_internacionais');
		

		if($itemId !== null) {
		
			// Filtrar os parâmetros passados por get.
			//
			$get = $this->parseGetParameters();

			// Buscar a row para editar
			//
			$noticia = new noticias_internacionais($itemId);
			// Inclui as imagens laterais da notícia
			

			if($this->request_method == 'get' && $noticia->is_record){ // Show Form
				// Passar o objecto regiao para a template
				//
				$this->tpl->assign('item',$noticia);
				$this->view = 'noticias_internacionais_editar';
				
			} else if($this->request_method == 'post' && $noticia->is_record) { // Process Form
    				// Filtrar os parâmetros enviados por POST
				//
				$post = $this->parsePostParameters();

				$redirect_url = str_replace('/editar/'.$itemId, '', $this->request_url); // Definir o url para ir para a lista
				
				if(!empty($post['titulo'])){
					$noticia->titulo	= $post['titulo'];
					$noticia->subtitulo	= $post['subtitulo'];
					$noticia->corpo 	= $post['corpo'];
					$noticia->local		= $post['local'];
					$noticia->data 		= $post['data'];

					// Processar upload de imagem e ficheiro se existirem
					//
					pear('HTTP/Upload');
					$upload = new HTTP_Upload('pt');
					$upload->setChmod(0777);

					
					// Ficheiro
					//
					$ficheiro = $upload->getFiles('ficheiro');
					if($ficheiro->isValid()){
						
						$ficheiro->setName(util::string_normalize($ficheiro->getProp('name')));
						$moved = $ficheiro->moveTo('app/uploads/noticias_internacionais/ficheiros');
						
						if (PEAR::isError($moved)) {
							$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities($moved->getMessage()))));
							$this->tpl->assign('item',$noticia);
							$this->view = 'noticias_internacionais_editar';
						}
						
						$noticia->ficheiro = $ficheiro->getProp('name');

					} else {
						$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities($ficheiro->errorMsg()))));
						$this->view = 'noticias_internacionais_editar';
					}
					
					// Imagem
					//
					$imagem = $upload->getFiles('imagem');
					if($imagem->isValid()){
						$imagem->setName(util::string_normalize($imagem->getProp('name')));
						$moved = $imagem->moveTo('app/uploads/noticias_internacionais/imagens');
						
						if (PEAR::isError($moved)) {
							$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities($moved->getMessage()))));
							$this->tpl->assign('item',$noticia);
							$this->view = 'noticias_internacionais_editar';
						}
						
						$noticia->imagem = $imagem->getProp('name');

					} else {
						$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities($imagem->errorMsg()))));
						$this->view = 'noticias_internacionais_editar';
					}
					

					$noticia->save();
					$this->_redirect($redirect_url,'');	// O '' no fim é para definir um prefixo vazio ao path de redirect
																// em vez de BASEURI. -> Ver método _redirect@Controller
				} else if(empty($post['titulo'])) {
					$this->tpl->assign('error_message',str_replace('%','\u00',rawurlencode(htmlentities('O campo Título é obrigatório.'))));
					$this->tpl->assign('item',$noticia);
					$this->view = 'noticias_internacionais_editar';
				} else if(empty($post['corpo'])) {
					$this->tpl->assign('error_message', str_replace('%','\u00',rawurlencode(htmlentities('O campo Corpo é obrigatório.'))));
					$this->tpl->assign('item',$noticia);
					$this->view = 'noticias_internacionais_editar';
				}
				
				
			}
			
		}
	}
	
	
	public function apagar($itemId=null) {
		if($itemId !== null){
			models('noticias_internacionais');

			$redirect_url = str_replace('/apagar/'.$itemId, '', $this->request_url); // Definir o url para ir para a lista
			// Buscar a row para apagar
			//
			$noticia = new noticias_internacionais($itemId);
			// Caso exista então apagamos
			//
			if($noticia->is_record){
				// Se houver imagens/ficheiros, apagar
				if (!empty($noticia->ficheiro)) {
					$fileFicheiroPath = UPLOADS.'noticias_internacionais/ficheiros'.DS.$noticia->ficheiro;
					if(is_file($fileFicheiroPath)){
						@unlink($fileFicheiroPath);
					}		
				}
				if (!empty($noticia->imagem)) {
					$fileImagemPath = UPLOADS.'noticias_internacionais/imagens'.DS.$noticia->imagem;
					if(is_file($fileImagemPath)){
						@unlink($fileImagemPath);
					}		
				}
				
				
				$noticia->delete();
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
			'titulo'			=> FILTER_SANITIZE_STRING,
			'subtitulo'			=> FILTER_SANITIZE_STRING,
			'data'				=> FILTER_SANITIZE_STRING,
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
			'palavra'		=> FILTER_SANITIZE_STRING,
			'significado'		=> FILTER_SANITIZE_STRING,
			'sortField'		=> FILTER_SANITIZE_STRING,
			'sortDirection'	=> array(
					'filter'	=> FILTER_VALIDATE_REGEXP,
					'options'	=> array("regexp"=>"/^(asc|desc)*$/")
			)
		);

		return filter_input_array(INPUT_GET, $filter_options);
	}
	
	public function retira_imagem($itemId) {
		$this->apagarImagem($itemId);
	}

	public function apagarImagem($itemId, $uploadsFolder='noticias_internacionais/imagens') {
		models('noticias_internacionais');
		// Buscar a row para editar
		//
		$row = new noticias_internacionais($itemId);

		if($row->is_record && !empty($row->imagem)) {
			$fileImagemPath = UPLOADS.$uploadsFolder.DS.$row->imagem;
			if(is_file($fileImagemPath)){
				@unlink($fileImagemPath);
			}
			$row->save();
			echo 'ok';

		} else {
			echo 'error';

		}
		exit;
	}

	public function apagarFicheiro($itemId, $uploadsFolder='noticias_internacionais/ficheiros') {
		models('noticias_internacionais');
		// Buscar a row para editar
		//
		$row = new noticias_internacionais($itemId);

		if($row->is_record && !empty($row->ficheiro)) {
			$fileFicheiroPath = UPLOADS.$uploadsFolder.DS.$row->ficheiro;
			if(is_file($fileFicheiroPath)){
				@unlink($fileFicheiroPath);
			}
			$row->ficheiro = null;
			$row->save();
			echo 'ok';

		} else {
			echo 'error';

		}
		exit;
	}
	
	
}