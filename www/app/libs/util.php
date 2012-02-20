<?php 
class util
{
	
	// Parsa uma string de HTML 'html_string' e procura pelo atributo 'attribute' de uma 'tag' 
	// e devolve o valor na 'index_position' da lista de valores encontrados
	//
    
        static  function isOdd($number){
            return $number & 1;
        }
	static function getAttribs($html_string = '', $tag = 'img', $attribute = 'src', $index_position=0) {
		preg_match_all("/(<".$tag." .*?".$attribute.".*?=.*?\")(.*?)(\".*?>)/", $html_string, $result);
		return $result[2][$index_position];
	}

	static function get_content($url) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}

	
	//UPLOAD DE QUALQUER TIPO DE FICHEIRO (GENÉRICA)
	static function upload($path = null, $file = null, $filetypeextension=null)
	{
		uses('debug');
		pear('HTTP/Upload');

        $mime_types = array(
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'gif'   => 'image/gif',
            'png'   => 'image/png'
        );
			
		if($path == null) {
			return null;

		} else {
			
			$temp_filename = '';
			$erro = '';
			
			$return_value = array();
		
			if ($file->isValid()) {
				/*5 242 880*/
                //if( $filetypeextension !== null  && strtolower($file->getProp('type'))!='image/jpeg' && strtolower($file->getProp('type'))!='image/pjpeg'){
                //if( $filetypeextension !== null &&  strtolower($file->getProp('type')) !== $mime_types[$filetypeextension]){
                
                if($filetypeextension !== null && strtolower($file->getProp('ext')) !== strtolower($filetypeextension)){
                    $temp_filename = '';
                    $erro = 'Só são permitidos ficheiros no formato '.$filetypeextension;
                    

                } else if($filetypeextension !== null && strtolower($file->getProp('type')) != $mime_types[$filetypeextension]){

                    $temp_filename = '';
                    $erro = 'Só são permitidos ficheiros no formato '.$filetypeextension;

				} else if($file->getProp('size') >= 5242880){
					$temp_filename = '';
					$erro = 'O ficheiro deve ter no m&aacute;ximo 5Mb de tamanho.';

				} else {
					$originalName = $file->getProp("name");
					$file->setName('uniq');
					$temp_filename = $file->moveTo($path);
				}
				
			} else if($file->isMissing()){
				$temp_filename = '';
				$erro = 'Não foi feito upload do ficheiro.';
			} else if($file->isError()){
				$temp_filename = '';
				$erro = $file->errorMsg();
			}
		
			if($erro == ''){
				$return_value[0] = $erro;
				$return_value[1] = filter_var($temp_filename,FILTER_SANITIZE_STRING);
				$return_value[2] = $file->getProp("type");
				$return_value[2] = $originalName;
				return $return_value;
					
			} else {
				$return_value[0] = $erro;
				$return_value[1] = "nodata";
				return $return_value;
			}
		}
	}
	
	
	
	
	// Locale independent PT strtolower
	//
	static  function strtolower($str)
	{
		$patterns 		= array("/À/","/Á/","/Â/","/Ã/","/É/","/Ê/","/Ì/","/Í/","/Ó/","/Ô/","/Õ/","/Ú/","/Ü/","/Ç/","/Ñ/");
		$replacements 	= array("à","á","â","ã","é","ê","ì","í","ó","ô","õ","ú","ü","ç","ñ"); 
		
		$str = strtolower($str);
		
		return preg_replace($patterns, $replacements, $str);
	}
	
	// Locale independent PT strtoupper
	//
	static  function strtoupper($str)
	{
		$patterns 		= array("/à/","/á/","/â/","/ã/","/é/","/ê/","/ì/","/í/","/ó/","/ô/","/õ/","/ú/","/ü/","/ç/","/ñ/");
		$replacements 	= array("À","Á","Â","Ã","É","Ê","Ì","Í","Ó","Ô","Õ","Ú","Ü","Ç","Ñ");
		
		$str = strtoupper($str);
		
		return preg_replace($patterns, $replacements, $str);
	}
		
	// Normalizar string
	//
	static function string_normalize($string, $case='lower')
	{
		$chars['in'] = chr(32).chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
			.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
			.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
			.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
			.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
			.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
			.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
			.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
			.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
			.chr(252).chr(253).chr(255).chr(180);
		
		$chars['out'] = "-EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy-";
		$ret = strtr($string, $chars['in'], $chars['out']);
		
		if($case == 'lower') {
			$ret = strtolower($ret);
			
		} else if($case == 'upper'){
			$ret = strtoupper($ret);
			
		} else if($case =='first'){
			$ret = ucfirst($ret);
		}
		
		return $ret;
			
	}

   


	static function sanitize_filename($string,$remove_extension = true)
	{
		$string = util::strtolower($string);

		$chars[' in'] = chr(32).chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
			.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
			.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
			.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
			.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
			.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
			.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
			.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
			.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
			.chr(252).chr(253).chr(255);


        $chars['out'] = "-EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
        

		$string = strtr($string, $chars['in'], $chars['out']);
		
		$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
		$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
		$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		
	
		$string = preg_replace('/&.+?;/', '', $string); // kill entities
		$string = preg_replace('/[^a-z0-9\s-._]/', '', $string);
		$string = preg_replace('/\s+/', '_', $string);
		$string = preg_replace('|-+|', '_', $string);
		$string = trim($string, '_');
				
		// Remove extension
		//
		if($remove_extension === true) {
			$extension = strrchr($string, ".");
			$string = substr($string, 0, -strlen($extension));
		}
			
		return $string;

	}

	static function array_rand_assoc($array, $numElements=1)
	{
		$keys = array_keys($array);
		shuffle($keys);
		
		$randomElements = array_flip(array_slice($keys,0,$numElements));
		return array_intersect_key($array,$randomElements);

	}
	
	static function array_rand($array)
	{
		$keys = array_keys($array);
		shuffle($keys);
		
		$ret = array();
		foreach($keys as $key){
			$ret[$key] = $array[$key];
		}
		return $ret;
	}
	
	static function array_sort_onkey($array, $key, $ascending=true)
	{
		if(is_array($array) && !empty($array) && count($array)>0 && isset($array[0][$key]) ){
			
			$temp = array();
			foreach($array as $idx=>$item){
				$temp[$idx] = util::string_normalize(util::strtolower($item[$key]));
			}
			if($ascending === true){
				asort($temp);
			} else {
				arsort($temp);
			}
			
			$ret = array();
			foreach($temp as $idx=>$item){
				$ret[] = $array[$idx];
			}
			
			return $ret;
			
		} else {
			
			return false;
		}
	}

	static function array_pop_rand_assoc(&$array, $numElements=1)
	{
		$elementToPop = util::array_rand_assoc($array, 1);
		$keyToPop = array_pop(array_keys($elementToPop));
		
		unset($array[$keyToPop]);

		return $elementToPop;
	}

	static function array_rand_key($array)
	{
		$elementToPop = util::array_rand_assoc($array, 1);
		return array_pop(array_keys($elementToPop));

	}
	
	
	

	
	
	static function array_keyvaluepair_exists($key = null, $value = null, $array = null)
    {
    	if(!is_array($array) || $key == null || $value == null) return null;   	
    	
    	foreach($array as $i=>$v) if($v[$key] == $value) return true;
    	
    	return false;
    }
    
 	static function array_extractlist_bykey($array = null, $key = null)
    {
    	if($key === null && !is_array($array)) return null;
    	    	
    	$ret = array();
    	foreach($array as $idx=>$value) $ret[$idx] = $value[$key];
    	    	
    	return $ret;
    }
    
    static function array_reindex_bykey($key = null, $array = null)
    {
    	if(!is_array($array) || $key == null) return null;
    	
    	$ret = array();
    	foreach($array as $v) {
			$ret[$v[$key]] = $v;
		}
    	
    	return $ret; 
    }
	
	

	
    
    /*
     * 	Pega numa array de rows, e extrai um vector com index_key como indices e value_key como valor 
     *  	
     * 		@param string index_key - key do valor a utilizar como índices do vector
     * 		@param string value_key	- key do valor a utilizar como valor do vector
     * 		@param array  array		- array de rows (vinda por exemplo da base de dados)
     * 
     * 		Formato da array de rows
     *      -------------------------
     * 		array
	 *		(
	 *		    [0] => array(
	 *		           	"field_1" => 30,
	 *		            "field_2" => "field_2_value_1",
	 * 					"field_3" => "field_3_value_1"
	 *		    ),
	 *		    [1] => array(
	 *		           	"field_1" => 51,
	 *		            "field_2" => "field_2_value_2",
	 * 					"field_3" => "field_3_value_2"
	 *		    ), 			
	 *		)
	 * 
	 * 		Exemplo de utilização para array acima
	 * 		-------------------------------------------
	 * 		array_vectorize("field_1","field_2",$array)
	 * 
	 * 		Resultado devolvido
	 * 		-------------------
	 * 		array(
     * 		(
     * 			[30] => "field_2_value_1",
     * 			[51] => "field_2_value_2"
     * 		)
     * 
     */
    static function array_vectorize($index_key = null, $value_key = null, $array = null, $sort_on_value = null, $utf8 = false)
    {
    	if(!is_array($array) || $value_key == null) return null;
    	
    	$ret = array();
    	$cnt = 0;
    	foreach($array as $i=>$v) {
    		if($index_key == null) $index = $i;
    		else $index = $v[$index_key];

			if($utf8 === false) {
				$ret[$index] = $v[$value_key];
			} else {
				$ret[$index] = utf8_encode($v[$value_key]);
			}
    	}
    	
    	if($sort_on_value === true) asort($ret);
		else if($sort_on_value === false) ksort($ret);
		
    	
    	return $ret;
    }

    static function array_search_string($needle=null,$array=null){
    	if(empty($needle) || empty($array) || !is_array($array) || !is_string($needle)){
    		return null;
    	}
    	$ret = array();
    	foreach($array as $key=>$value){
    		if(strpos($value,$needle)!== false) {
    			$ret[$key] = $value;
    		}
    	}
    	return $ret;
    	
    }

	/** Faz utf8 encoding de uma array de rows vindas do model
	 *	array(
     * 			[0] => array(
	 *	            'id'	=> 234
	 *				'name'	=> 'Xpto
	 *          )
	 *  )
	 */
	static function rows_utf8_encode($rows)
	{
		$ret = array();
		foreach($rows as $idx=>$row){
			foreach($row as $key=>$value){
				$rows[$idx][$key] = utf8_encode($value);
			}
		}
		
		return $rows;
	}
	static function array_utf8_decode($rows)
	{
		$ret = array();
		foreach($rows as $key=>$value){
			$ret[$key] = utf8_decode($value);			
		}
		return $ret;
	}
	
	static function array_utf8_encode($rows)
	{
		$ret = array();
		foreach($rows as $key=>$value){
			$ret[$key] = utf8_encode($value);			
		}
		return $ret;
	}

    static function arrayToJson($array,$wrapInTextarea=false){
        $ret = json_encode(self::array_utf8_encode($array));
        if($wrapInTextarea === false) {
            return $ret;
        } else {
            return '<textarea>'.$ret.'</textarea>';
        }
    }

    static function rowsToJson($rows,$wrapInTextarea=false){
        $ret = json_encode(self::rows_utf8_encode($rows));
        
        if($wrapInTextarea === false) {
            return $ret;
        } else {
            return '<textarea>'.$ret.'</textarea>';
        } 
    }



	/**
	 *	Faz truncate de uma string de texto de acordo com certos parâmetros
	 **/
	 static function truncate($string, $length = 80, $etc = '&hellip;', $break_words = false, $middle = false, $clean_html = false)
	{
		// Mendes : 18/08/2010 
		// A string pode conter new line HTML (<br>) que ocupa 4 bytes na string, 
		// mas visualmente ocupa o espaço de mais caracteres, troca-se por espaço
		//
		$string = str_replace('<br>', ' ', $string);
		$string = str_replace('<br/>', ' ', $string);
		
	    if ($length == 0)
	        return '';

	    if ($clean_html === true){
	    	$string = strip_tags($string);
	    }

	    if (strlen($string) > $length) {
	        $length -= strlen($etc);
	        if (!$break_words && !$middle) {
	            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
	        }
	        if(!$middle) {
	            return substr($string, 0, $length).$etc;
	        } else {
	            return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
	        }
	    } else {
	        return $string;
	    }
	}

   
   
   static function array_groupby_keyvalue($key, $array){
   		
   		if(empty($key) || !is_array($array) || !sizeof($array)>0 || !array_key_exists($key, $array[0]) ){
   			return null;
   		} else {
   			$ret = array();
   			foreach($array as $value){
   				$ret[$value[$key]][] = $value;
   			}
   			return $ret;
   		}
   }

       
    static function array_groupbykeys($key_groups = null, $array = null, $index_key = "id")
    {
    	
    	// Verificar se os parâmetros são passados
    	if( !is_array($key_groups) || !is_array($array) || !(count($key_groups)>0) || !(count($array)>0) ) return null;
    	
    	
    	
    	// Verificar que todas as keys pedidas existem na array
    	
    	$keys = array_keys($array[0]);
    
    	$groups = array();
    	$grouped_fields = array();
    	
    	foreach($key_groups as $label=>$fields){
			
    		$field_names = explode(",",$fields);
			if($field_names !== array_intersect($field_names,$keys)) {
				trigger_error("Key name not found in array_groupbykeys",E_USER_WARNING);
			} else {
				$groups[$label] = $field_names;
				foreach($field_names as $field) $grouped_fields[] = $field;
			}
    	}
    	
    	
    	
    	// Get flat fields
    	$flat_fields = array_diff($keys,$grouped_fields);
    	
    	    	
    	$ret = array();
    	
    	foreach($array as $idx=>$row){
    		
    		foreach($flat_fields as $flat_field) {
	    		$ret[$row[$index_key]][$flat_field] = $row[$flat_field];
	    	}
			
    		foreach($groups as $label=>$fields) {
	    		foreach($fields as $field) {
		    		if($row[$field]) $ret[$row[$index_key]][$label][$row[$label."_id"]][$field] = $row[$field];	
		    	}
		    }
    		

    	}
    	    	
    	return $ret;
    	
    	
    	
    	
    }

	static function array_remove_empty_values($array,$removeFalse=false)
	{
		foreach($array as $key=>$value)
		{
			if($value === null || ($removeFalse === true && $value === false)) unset($array[$key]);
		}
		return $array;
	}

    static function array_merge_bykeys()
    {
        $params = func_get_args();
        $num_params = func_num_args();
        if($num_params < 1){
            return null;
        } else if($num_params == 1){
            return $params[0];
        } else {
            $ret = array();
            foreach($params as $array){
                foreach($array as $key=>$value){
                    $ret[$key] = $value;
                }
            }
            return $ret;
        }
    }


	static function array_remove_fields($array,$array_keys){
		$temp_array = $array;

		foreach($temp_array as &$row){
			foreach($array_keys as $key){
				unset($row[$key]);
			}
		}
		return $temp_array;
	}

	static function getimagesize($url, $option='all'){
		//list($width, $height, $type, $attr) = getimagesize(IMG.'areas/enciclopedia/wineexpert/acessorios_01.jpg');
		$options = getimagesize($url);
		switch($option){
			case 'width':
				return $options[0];
				break;

			case 'height':
				return $options[1];
				break;

			case 'type':
				return $options[2];
				break;

			case 'attr':
				return $options[3];
				break;

			case 'css':
				return 'width:'.$options[0].'px;height:'.$options[1].'px;';
				break;

			case 'all':
			default:
				return $options;
				break;
		}


	}

	     
	
}


class Make {
	static private $classes = array();

	static function a($class) { $args = func_get_args(); return self::makeByArray($args); }
	static function an($class) { $args = func_get_args(); return self::makeByArray($args); }
	
	static function model($class) {
		
		if( !is_file(MODELS.$class.'.php') ){
			self::generateModelFile($class);
		}
		models($class);
		$args = func_get_args(); return self::makeByArray($args);
	}

	static private function makeByArray($args) {
		$class = array_shift($args);
		if($class != null) {
			if(!isset(self::$classes[$class])) {
				self::$classes[$class] = new ReflectionClass($class);
			}
			return self::$classes[$class]->newInstanceArgs($args);
		}
	}

	static private function generateModelFile($table){
		uses('modelfields');
		
		// Get File Template
		//
		$model_file = file_get_contents(CORETEMPLATES.'model_extended_class.tpl');
		
		// Assign Table/Class Name
		//
		$model_file = str_replace('{$table}',$table,$model_file);
		$db = &sBook::DBLink();
		
		// Get and Assign Create SQL
		//
		
		$tableCreateInfoArray = $db->queryAll("SHOW CREATE TABLE `$table`");

		$create_statement = $tableCreateInfoArray[0]['create table'];
		$model_file = str_replace('{$createSQL}',$create_statement,$model_file);

		// Get and Assign Fields
		//
		$db->loadModule('Reverse');
		$tableFields = $db->reverse->tableInfo($table);
		$fields = "\r\n";
		foreach($tableFields as $field){

			$fields .= "\tpublic $".$field['name'].";\r\n";
		}
		$model_file = str_replace('{$fields}',$fields,$model_file);

		// Get and Assign Field Conditions
		//
		$fields = util::array_vectorize('name','nativetype',$tableFields);
		if( isset($fields['languages_id']) ){
			$this->tpl->assign('languages_id',true);
		}
		
		$conditions  = array();

		foreach($fields as $field=>$nativeType){
			$type = modelfields::getTypeByDBType($nativeType);
			
			switch($type){
				case 'int':
				case 'float':
					$conditions['number'] .= "case '$field':\r\n";
				break;

				case 'text':
					$conditions['like'] .= "case '$field':\r\n";
				break;

				case 'datetime':
				case 'date':
				case 'timestamp':
					$conditions['exact'] .= "case '$field':\r\n";
				break;
			}
		}

		$model_file = str_replace('{$conditionNumber}',$conditions['number'],$model_file);
		$model_file = str_replace('{$conditionLike}',$conditions['like'],$model_file);
		$model_file = str_replace('{$conditionExact}',$conditions['exact'],$model_file);

		if($languages_id == true) {
			$model_file = str_replace('{$langCondition}','			$options["conditions"][] = "languages_id = ".LOCALE_ID;',$model_file);
		} else {
			$model_file = str_replace('{$langCondition}','',$model_file);
		}

		file_put_contents(MODELS.$table.'.php',$model_file);
		chmod(MODELS.$table.'.php',0765);
	}
}



class Rest {
	static private $requestMethod;

	static function getRequestMethod(){
		
        return strtolower($_SERVER['REQUEST_METHOD']);

	}

    static function isAjax(){
        return ($_SERVER[ 'HTTP_X_REQUESTED_WITH' ] === 'XMLHttpRequest');
    }
	
	static function getExtension($url = null)
	{
        if($url === null) {
            $url = basename($_SERVER['REQUEST_URI']);
        }

		$dotPos = stripos($url,'.');
		
		if($dotPos>0){
			return strtolower(substr($url,$dotPos+1));
		}
		
		return null;
	}
	
	static function removeExtension($filename)
	{
		$dotPos = stripos($filename,'.');
		return strtolower(substr($filename,0, $dotPos));
		
	}
}

class Date {
    private $date;
    private $meses = array(
        '1'  => 'Janeiro',
        '2'  => 'Fevereiro',
        '3'  => 'Março',
        '4'  => 'Abril',
        '5'  => 'Maio',
        '6'  => 'Junho',
        '7'  => 'Julho',
        '8'  => 'Agosto',
        '9'  => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    );
    public function __construct($dateString=null){
        
        if(!empty($dateString)){
            $this->date = getdate(strtotime($dateString));
        } else {
            $this->date = getdate(mktime());
        }
    }
    /*
     * $dateString: any string that strtotime understands. i.e. 2012-02-23 for ex.
     */
    public function between($start_dateString=null, $end_dateString=null){
        $start = Make::a('Date',$start_dateString)->date[0];
        $end   = Make::a('Date',$end_dateString)->date[0];
        $now   = $this->date[0];

        if($now >= $start && $now <= $end){
            return true;

        } else {
            return false;
        }
    }

    public function getYear(){
        return $this->date['year'];
    }
}

Class Growl {

    static function send($message=''){
        $_SESSION['growl_message'] = $message;
    }

    static function get(){
        $ret = $_SESSION['growl_message'];
        unset($_SESSION['growl_message']);
        return $ret;
    }

    static function gotMessage() {
        if(!empty($_SESSION['growl_message'])){
            return true;
        } else {
            return false;
        }
    }

}
