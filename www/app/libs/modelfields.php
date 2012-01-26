<?php 
class ModelFields {
	static private $fieldTypes = array(
		'int'		=> 'int',
		'bigint'	=> 'int',
		'smallint'	=> 'int',
		'tinyint'	=> 'int',
		'mediumint'	=> 'int',
		'integer'	=> 'int',
		'bit'		=> 'int',
		'bool'		=> 'int',
		'boolean'	=> 'int',
		'serial'	=> 'int',

		'float'		=> 'float',
		'double'	=> 'float',
		'precision'	=> 'float',
		'decimal'	=> 'float',
		'dec'		=> 'float',
		'numeric'	=> 'float',
		'fixed'		=> 'float',

		'datetime'	=> 'datetime',
		'date'		=> 'date',
		'timestamp'	=> 'timestamp',

		'char'		=> 'text',
		'varchar'	=> 'text',
		'binary'	=> 'text',
		'varbinary'	=> 'text',
		'blob'		=> 'text',
		'text'		=> 'text',
		'enum'		=> 'text',
		'set'		=> 'text',

	);

	/**
	 * @param object $modelObj com a referência ao objecto do model criado
	 *
	 * @return array $conditions array com as condições para o model, por campo, em formato sprintf exemplo array('field' => 'field = %d ');
	 */
	static public function getConditionsByFields(&$modelObj)
	{
		uses('util');
		
		$conditions = array();
		$fieldsInfo = util::array_vectorize("name","type",$modelObj->_fields_info);

		foreach($fieldsInfo as $field=>$field_type)
		{
			$type = self::getTypeByDBType($field_type);
			switch($type)
			{
				case 'int':
				case 'float':
					$conditions[$field] = "$field = %d";
					break;

				case 'datetime':
					$conditions[$field] = "$field = '%s'";
					break;

				case 'text':
					$conditions[$field] = "$field LIKE '%%%s%%'";
					break;
				default:
					break;
			}
		}
		$modelObj->_fieldConditions = $conditions;
		
		return $conditions;
	}

	static public function getFieldType(&$modelObj){
		uses('util');


	}


	static public function getTypeByDBType($dbType)
	{
		return self::$fieldTypes[$dbType];
	}

	static public function getTypesByFieldInfo(&$modelObj)
	{
		$fieldsInfo = util::array_vectorize("name","type",$modelObj->_fields_info);
		
		foreach($fieldsInfo as $field=>$dbType)
		{
			$fieldsInfo[$field] = self::$fieldTypes[$dbType];
		}
		return $fieldsInfo;
	}

	static public function getFilterParamsByFields(&$modelObj)
	{
		$rulesByFieldType = self::getFilterParamsByFieldType();
		debug::dump($rulesByFieldType);
	}

	static public function getFilterParamsByFieldType()
	{
		$filter_params = array();
		$typesArray = self::$fieldTypes;

		foreach($typesArray as $dbType)
		{
			$type = self::getTypeByDBType($dbType);


			switch($type)
			{
				case 'int':
					$filter_params['int'] = array('filter' => FILTER_VALIDATE_INT);
					break;
				case 'float':
					$filter_params['float']	= array('filter' => FILTER_VALIDATE_FLOAT);
					break;
				case 'datetime':
					$filter_params['datetime'] = array('filter'=> FILTER_VALIDATE_REGEXP, 'options'=> array('regexp'=>'/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/'));
					break;
				case 'timestamp':
					$filter_params['timestamp'] = array('filter'=> FILTER_VALIDATE_REGEXP, 'options'=> array('regexp'=>'/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/'));
					break;
				case 'date':
					$filter_params['date'] = array('filter'=> FILTER_VALIDATE_REGEXP, 'options'=> array('regexp'=>'/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])?$/'));
					break;
				case 'text':
					$filter_params['text'] = array('filter'=> FILTER_SANITIZE_STRING);
					break;
				default:
					break;
			}
		}

		return $filter_params;

	}

}
?>