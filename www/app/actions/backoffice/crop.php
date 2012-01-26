<?php

class controller_crop extends controller {
	
	function index(){
		
	}

	function getDefinedCrops($filename, $parent_table, $imagem_id)
	{
		models('crops', 'crops_tipos');
		uses('util');


		// Get Crops Tipos
		//
		$crops_tipos = util::array_vectorize('id', 'dimensions', Make::a('crops_tipos')->findAll(array(
			'select'	=> "id, concat_ws('x',width,height) as dimensions",
			'order'		=> 'width asc',
		),true));

		$options = array();
		$options['select']	= "id, crops_tipos_id, imagem_id, concat_ws('x', `crops_tipos`.width, `crops_tipos`.height) as dimensions, x1,x2,y1,y2,crops.width as width, crops.height as height, `crops_tipos`.width as crop_width, `crops_tipos`.height as crop_height";
		$options['join']	= array('type'=>'inner', 'table'=>'crops_tipos', 'condition'=>'crops.crops_tipos_id = crops_tipos.id');
		$options['conditions'] = array(
			'language_id 	= '.LOCALE_ID,
			"filename	 	= '".utf8_decode($filename)."'",
			"parent_table 	= '$parent_table'",
			"imagem_id		= $imagem_id"
		);
		$crops = Make::a('crops')->findAll($options,true);



		echo json_encode($crops);
		exit;
	}

	function deleteCrop($crop_id = null)
	{
		if($crop_id !== null){
			models('crops');
			$crop = new crops($crop_id);
			if($crop->is_record){
				$crop->delete();
				echo json_encode(array('error'=>false));
				exit;
			} else {
				echo json_encode(array('error'=>true));
				exit;	
			}

		} else {
			echo json_encode(array('error'=>true));
			exit;	
		}

	}

	function getCroppedImage()
	{
		uses('WideImage/WideImage');
		models('crops_tipos');
		
		$filter_options = array(
			'filename'		=>	FILTER_SANITIZE_STRING,
			'folder'		=>	FILTER_SANITIZE_STRING,
			'type'			=>  FILTER_SANITIZE_STRING,
			'crop_id'	=>	FILTER_VALIDATE_INT,
			'x1'		=>	FILTER_VALIDATE_INT,
			'x2'		=>	FILTER_VALIDATE_INT,
			'y1'		=>	FILTER_VALIDATE_INT,
			'y2'		=>	FILTER_VALIDATE_INT,
			'width'		=>	FILTER_VALIDATE_INT,
			'height'	=>	FILTER_VALIDATE_INT
		);
		$get = filter_input_array(INPUT_GET, $filter_options);

		if(!empty($get['filename']) && !empty($get['folder']) && !empty($get['type']) && $get['crop_id'] !== false && $get['x1'] !== false && $get['x2'] !== false && $get['y1'] !== false && $get['y2'] !== false && $get['width'] !== false && $get['height'] !== false ){
			
			$get['filename'] = utf8_decode($get['filename']);

			$crop_tipo = new crops_tipos($get['crop_id']);
			if($crop_tipo->is_record){
				$image = wideimage::load(UPLOADS.$get['folder'].'/'.$get['filename'])->crop($get['x1'], $get['y1'],$get['width'],$get['height'])->resizeDown($crop_tipo->width,$crop_tipo->height,'outside')->crop(0,0,$crop_tipo->width, $crop_tipo->height)->output('jpg',65);
			}
			
		}

	}

	function addCrop(){
		$filter_options = array(
			'filename'		=>	FILTER_SANITIZE_STRING,
			'folder'		=>	FILTER_SANITIZE_STRING,
			'type'			=>  FILTER_SANITIZE_STRING,

			'crop_id'	=>	FILTER_VALIDATE_INT,
			'imagem_id'	=>	FILTER_VALIDATE_INT,
			'x1'		=>	FILTER_VALIDATE_INT,
			'x2'		=>	FILTER_VALIDATE_INT,
			'y1'		=>	FILTER_VALIDATE_INT,
			'y2'		=>	FILTER_VALIDATE_INT,
			'width'		=>	FILTER_VALIDATE_INT,
			'height'	=>	FILTER_VALIDATE_INT
		);


		$get = filter_input_array(INPUT_GET, $filter_options);


		if(!empty($get['filename']) && !empty($get['folder']) && !empty($get['type']) && $get['crop_id'] !== false && $get['imagem_id'] !== false && $get['x1'] !== false && $get['x2'] !== false && $get['y1'] !== false && $get['y2'] !== false && $get['width'] !== false && $get['height'] !== false ){

			models('crops');
			$get['filename'] = utf8_decode($get['filename']);
			$crop = new crops();
			$crop->crops_tipos_id	= $get['crop_id'];
			$crop->parent_table		= $get['type'];
			$crop->imagem_id		= $get['imagem_id'];
			$crop->filename			= $get['filename'];
			$crop->x1				= $get['x1'];
			$crop->y1				= $get['y1'];
			$crop->x2				= $get['x2'];
			$crop->y2				= $get['y2'];
			$crop->width			= $get['width'];
			$crop->height			= $get['height'];

			$crop->save();

			if($crop->is_record){
				echo json_encode(array('error'=>false));
				exit;	
			}
		}

		echo json_encode(array('error'=>true,'msg'=>$get));
		exit;	
	}

}