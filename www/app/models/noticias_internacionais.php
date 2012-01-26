<?php
/**
 * Model noticias
 * 
 * 
 * @author Wiz Interactive
 */
 
class noticias_internacionais extends model
{

	public $_table 		= 'noticias_internacionais';
	public $_slug_field	= 'titulo';

	public $_sql_create = "
		CREATE TABLE `noticias_internacionais` (
			`id` INT(10) UNSIGNED NOT NULL,
			`created_on` DATETIME NULL DEFAULT NULL,
			`titulo` VARCHAR(255) NOT NULL COLLATE 'latin1_general_ci',
			`subtitulo` VARCHAR(255) NOT NULL COLLATE 'latin1_general_ci',
			`slug` VARCHAR(500) NOT NULL COLLATE 'latin1_general_ci',
			`data` DATE NOT NULL,
			`ficheiro` VARCHAR(255) NOT NULL COLLATE 'latin1_general_ci',
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
    public $titulo;
	public $subtitulo;
    public $slug;
    public $data;
    public $ficheiro;
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
				$condition = "$field = %d";
				break;
			
			case 'created_on':
			case 'data':
			case 'titulo':
			case 'slug':
			case 'imagem':
			case 'ficheiro':
				$condition = "$field = '%s'";
				break;
			case 'corpo':
				$condition = "$field LIKE '%%%s%%'";
				break;
			default:
				$condition = '';
				break;
			}
			return $condition;
	}
}