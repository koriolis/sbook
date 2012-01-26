<?php
/**
 * Model noticias
 * 
 * 
 * @author Wiz Interactive
 */
 
class noticias_imagens extends model
{

	public $_table 		= 'noticias_imagens';

	public $_sql_create = "
		CREATE TABLE `noticias_imagens` (
			`id` INT(10) UNSIGNED NOT NULL,
			`created_on` DATETIME NULL DEFAULT NULL,
			`noticias_id` INT(11) NOT NULL,
			`imagem` VARCHAR(255) NOT NULL COLLATE 'latin1_general_ci',
			PRIMARY KEY (`id`)
		)
		COLLATE='latin1_general_ci'
		ENGINE=MyISAM";

    /**
     * Campos da tabela para facilitar o lookup
     */
    public $id;
    public $created_on;
    public $noticias_id;
    public $imagem;
    
	

	/***************************/

	public function listPaged(&$get)
	{
		uses('util','modelfields');

		$page = (empty($get['page'])) ? 1 : $get['page'];
		unset($get['page']);
		
		$sortField = (empty($get['sortField'])) ? 'data' : $get['sortField'];
		unset($get['sortField']);

		$sortDirection = (empty($get['sortDirection'])) ? 'asc' : $get['sortDirection'];
		unset($get['sortDirection']);

		$options = array();
		$options['order'] = "$sortField $sortDirection";
		
		$ret = array();
		
		foreach($get as $field=>$value) {
			if(!empty($value)) {
				$options['conditions'][] = sprintf($this->getFilterConditionByField($field),$value);
				$ret['queryFields'][$field] = $value;
			}
		}


        		
		$numRows = $this->count($options);

		$pager = sBook::Pager($page, $numRows, ROWS_PER_PAGE, PAGES_PER_GROUP);

		$options['offset']	= $pager['offset'];
		$options['limit']	= $pager['limit'];

		$ret = array_merge($ret, array( 
			'rows'			=> $this->findAll($options, true),
			'pager'			=> $pager,
			'sortField'		=> $sortField,
			'sortDirection'	=> $sortDirection
		));
		
		return $ret;
		
		
	}

	private function getFilterConditionByField($field)
	{
		switch($field)
		{
			case 'id':
			case 'noticias_id':
				$condition = "$field = %d";
				break;
			
			case 'created_on':
			case 'imagem':
				$condition = "$field = '%s'";
				break;
			default:
				$condition = '';
				break;
			}
			return $condition;
	}
	
	public function deleteByNoticiaId($noticia_id = null) {
		if ($noticia_id != null) {
			$val = $this->_link->queryAll("delete from noticias_imagens where noticias_id = $noticia_id");
			$this->_check_error($val);
		}
	}
}