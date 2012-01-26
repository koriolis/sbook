<?php
/**
 * Model noticias
 * 
 * 
 * @author Wiz Interactive
 */
 
class candidaturas_espontaneas extends model
{

	public $_table 		= 'candidaturas_espontaneas';

	public $_sql_create = "
		CREATE TABLE `candidaturas_espontaneas` (
			`id` INT(10) UNSIGNED NOT NULL,
			`created_on` DATETIME NULL DEFAULT NULL,
			`nome` VARCHAR(255) NOT NULL COLLATE 'latin1_general_ci',
			`email` VARCHAR(255) NOT NULL COLLATE 'latin1_general_ci',
			`telefone` VARCHAR(255) NOT NULL COLLATE 'latin1_general_ci',
			`cv` VARCHAR(255) NULL COLLATE 'latin1_general_ci',
			`observacoes` text NULL COLLATE 'latin1_general_ci',
			PRIMARY KEY (`id`)
		)
		COLLATE='latin1_general_ci'
		ENGINE=MyISAM";

    /**
     * Campos da tabela para facilitar o lookup
     */
    public $id;
    public $created_on;
    public $nome;
    public $email;
    public $telefone;
    public $cv;
    public $observacoes;
	

	/***************************/

	public function listPaged(&$get)
	{
		uses('util','modelfields');

		$page = (empty($get['page'])) ? 1 : $get['page'];
		unset($get['page']);
		
		$sortField = (empty($get['sortField'])) ? 'created_on' : $get['sortField'];
		unset($get['sortField']);

		$sortDirection = (empty($get['sortDirection'])) ? 'desc' : $get['sortDirection'];
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
			case 'nome':
			case 'email':
			case 'telefone':
			case 'cv':
				$condition = "$field = '%s'";
				break;
			case 'observacoes':
				$condition = "$field LIKE '%%%s%%'";
				break;
			default:
				$condition = '';
				break;
			}
			return $condition;
	}
}