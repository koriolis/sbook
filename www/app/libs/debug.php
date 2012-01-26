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
	
	function _trace($var){
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
		exit;
	}

	function trace(){
		if(func_num_args()>1){
			echo "<pre>";
			foreach(func_get_args() as $arg){
				echo "<hr>";
				var_dump($arg);
			}
			echo "</pre>";
			exit;
		} else {
			self::_trace(func_get_arg(0));
		}
	}
	
	function _dump($var){
		echo "<pre>";
		print_r($var);
		echo "</pre>";
		exit;
	}

	function dump(){
		if(func_num_args()>1){
			echo "<pre>";
			foreach(func_get_args() as $arg){
				echo "<hr>";
				print_r($arg);
			}
			echo "</pre>";
			exit;
		} else {
			self::_dump(func_get_arg(0));
		}
	}
}

?>
