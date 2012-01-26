<?php

class template_functions
{

	static function getDimensions($tamanho){
		$dim = explode('x',$tamanho);
		$hori = $dim[0];
		$vert = $dim[1];

		$width = (244 * $hori)-1;
		$height = (228 * $vert)-1;

		$ret = new StdClass;
		$ret->width = $width;
		$ret->height = $height;

		return $ret;

	}

	static function minibutton($text, $icon=null, $link='#', $classes=null, $attributes=array())
	{
		if(!empty($attributes) && is_array($attributes)) {
			foreach($attributes as $attr=>$val)
			{
				$parsed_attributes .= "$attr='$val' ";
			}
			$parsed_attributes = trim($parsed_attributes);
		}
		if(!empty($text)) {
			return "<a href='$link' class='minibutton $classes' $parsed_attributes><span><span class='icon $icon'></span>$text</span></a>";
		} else {
			return "<a href='$link' class='minibutton $classes' $parsed_attributes><span class='notext'><span class='icon $icon'></span></span></a>";
		}

	}

	static function rowActions($texts_array, $links_array = array(), $li_classes_array = array())
	{

		/*
		<ul class="row-actions">
														<li class="ui-corner-all"><a href="#">Editar</a></li>
														<li class="ui-corner-all"><a href="#">Apagar</a></li>
													</ul>
		 *
		 */
	}

	

	static function html_image($filename=null, $folder = null, $width = 100, $height=100, $img_attrs = null, $return = 'echo', $type = 'resize')
	{
		uses('WideImage/WideImage');
		if($filename !== null && $folder !== null) {
			
			if(is_dir(UPLOADS.'thumbs') === false){
				mkdir(UPLOADS.'thumbs');
			}

			$thumb_destination_folder 	= UPLOADS.'thumbs'.DS.$folder.DS;
			$cache_hash_id 			= md5(serialize(func_get_args()));
			$source_filename 		= UPLOADS.$folder.DS.$filename;

			// Criar a pasta da área no thumbs caso não exista
			if(is_dir($thumb_destination_folder) === false) {
			
				mkdir($thumb_destination_folder);
			}
			//echo "<small>".$source_filename."</small>";

			if(is_file($source_filename)){ // Se não existir o ficheiro original então não fazemos nada
	
				// Verifica se o ficheiro source foi modificado
				$source_modified = filemtime($source_filename);
				
				// O thumbnail leva o modification time do source, mesmo que o ficheiro seja substituido por outro de mesmo nome,
				// e o sistema actualize o thumb pela data de modificação, o thumb terá outro nome
				// motivo: problemas de cache do Arthur

				$thumb_filename 		= $thumb_destination_folder.$cache_hash_id.$source_modified.".jpg";
				//debug::dump($thumb_filename);

				
				if (is_file($thumb_filename)) {
					$thumb_modified = filemtime($thumb_filename);
				} else {
					$thumb_modified = 0;
				}

				if((!is_file($thumb_filename)) || ($source_modified > $thumb_modified)){ // Se não existir nenhum ficheiro na pasta ou o ficheiro de source é mais recente então temos que o criar
					
				    
				    if($type == 'resize'){
				    	
				    	wideimage::load($source_filename)->resize($width, $height,'outside')->crop(0,0,$width,$height)->saveToFile($thumb_filename,65);

				    } else if($type == 'resizeDown'){
				    	
				    	wideimage::load($source_filename)->resizeDown($width, $height,'inside')->saveToFile($thumb_filename,65);

				    }

				}

				$src = UPLOADS_URI.'thumbs/'.$folder.'/'.$cache_hash_id.$source_modified.'.jpg';
				$imgData = getImageSize($src);
				$img_html = '<img src="'.$src.'" width="'.$imgData[0].'" height="'.$imgData[1].'" '.(($img_attrs !== null) ? $img_attrs:'').' />';

				if($return === 'echo'){
					echo $img_html;

				} else if($return === 'tag'){
					return $img_html;
				}

			}

		}
	}

	


	/**
	 * Truncate
	 *
	 * Type:     modifier<br>
	 * Name:     truncate<br>
	 * Purpose:  Truncate a string to a certain length if necessary,
	 *           optionally splitting in the middle of a word, and
	 *           appending the $etc string or inserting $etc into the middle.
	 * @author   Nuno Ferreira <nuno.ferreira at wiz dot pt>
	 * @param string
	 * @param integer
	 * @param string
	 * @param boolean
	 * @param boolean
	 * @return string
	 */
	static function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false, $return = false)
	{
		// Mendes : 18/08/2010 
		// A string pode conter new line HTML (<br>) que ocupa 4 bytes na string, mas visualmente ocupa o espaço de mais caracteres, troca-se por espaço
		$string = str_replace('<br>', ' ', $string);
		$string = str_replace('<br/>', ' ', $string);

	    if ($length == 0){
	        if($return === false) {
	        	echo '';
	        } else {
	        	return '';
	        }
	    }

	    if (strlen($string) > $length) {
	        $length -= strlen($etc);
	        if (!$break_words && !$middle) {
	            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
	        }
	        if(!$middle) {
	        	if($return === false ){
	            	echo substr($string, 0, $length).$etc;
	           	} else {
	           		return substr($string, 0, $length).$etc;
	           	}
	        } else {
	        	if($return === false ){
	            	echo substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
	            } else {
	            	return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
	            }
	        }
	    } else {
	    	if($return === false ){
	        	echo $string;
	        } else {
	        	return $string;
	        }
	    }
	}

	/*
	 * Extract file extension in lowercase
	 */
	static function file_ext($filename)
	{
		return strtolower(str_replace(".", "", strrchr(basename($filename), ".")));
	}


	static function html_options($array,$selected = null,$css_class=null)
	{
		if(is_array($array)){
			if($css_class !== null) $class="$css_class";
			if($selected !== null) $selected_value = $selected;

			foreach($array as $value=>$text) echo "<option ".(($selected_value == $value)?"selected":"")." value=\"$value\">$text</option>\r\n";

		}
	}



}




?>
