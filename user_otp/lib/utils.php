<?php

	function generateRandomString(
		$length_min,
		$length_max = null,
		$length_step = 1,
		$valid_char='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
	){
		if($length_max===null){
			$length_max = $length_min;
			$length = $length_min;
		}else{
			$length = mt_rand($length_min,$length_max) ;
			$length = $length - $length % $length_step;
		}
		
		//~ var_dump(
			//~ array(
				//~ $length_min,
				//~ $length_max,
				//~ $length_step, 
				//~ $length
			//~ )
		//~ );
		
		$str = '';
		$count = strlen($valid_char);
		while ($length--) {
			$str .= $valid_char[mt_rand(0, $count-1)];
		}
		return $str;
	}
