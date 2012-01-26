<?php

class debug
{
	function dump2log($var){
		if(is_array($var)){
			foreach($var as $key=>$value){
				logmsg($key . "=>" . $value);
			}
		} else {
			logmsg($var);
		}
	}
	
	function trace($var){
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
		exit;
	}
	
	function dump($var){
		echo "<pre>";
		print_r($var);
		echo "</pre>";
		exit;
	}
}

?>
