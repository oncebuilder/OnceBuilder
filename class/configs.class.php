<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is core connector class
 *
*/


class once extends core{
	// Get category data
	function variables_get($table){

		if(file_exists('../oconfig.php')){
			// Read funny config.php
			$home=true;
			require('../oconfig.php');
			$obj['keys']=array();

			if(isset($_CONFIG)){
				foreach($_CONFIG as $k => $v){
					$tab=explode("_",$k);
					$string=''.$tab[0];
					if($this->data['query']!=''){
						if(strpos($k,$this->data['query'])>-1){
							$obj['items'][$k]=$v;
						}
					}else if($this->data['key']!=''){
						if($this->data['key']==$string){
							$obj['items'][$k]=$v;
						}
					}else{
						$obj['items'][$k]=$v;
					}
					
					if(isset($tab[0])){
						if(!in_array($string,$obj['keys']) && isset($tab[1])){
							$obj['keys'][]=$tab[0];
						}
					}
				}
			}
			
		}
		return $obj;
	}
	
	function bulk_action(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin
			if($this->once_creator_check()){
				// Read funny config.php
				$home=true;
				require('../oconfig.php');
				$obj['keys']=array();
					
				// Loop bulk items and make action
				foreach ($this->data['ids'] as $position => $item){
					unset($_CONFIG[$position]);
					$obj['ids'][]=$position;
				}
			
				$obj=$this->gen_config($_CONFIG);
			}else{
				$obj['errors'][]='No permission!';
				$obj['error']++;
			}
		}else{
			$obj['errors'][]='CSFR token invalid!';
			$obj['error']++;
		}
		
		// Return depends on type
		if($this->data['ajax']){
			// Print JSON object
			echo json_encode($obj);
		}else{
			// Return JSON object
			return $obj;
		}
	}

	function item_delete(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Read funny config.php
				$home=true;
				require_once('../config.php');
				$obj['keys']=array();
	
				if(isset($_CONFIG)){
					if(strlen($this->data['key'])>0){
						unset($_CONFIG[$this->data['key']]);

						$obj=$this->gen_config($_CONFIG);
					}else{
						$obj['errors'][]='Variable not exists';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Config not exists';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='You don\'t have permission';
				$obj['error']++;
			}
		}else{
			$obj['errors'][]='CSFR token invalid';
			$obj['error']++;
		}
		
		// Return depends on type
		if($this->data['ajax']){
			// Print JSON object
			echo json_encode($obj);
		}else{
			// Return JSON object
			return $obj;
		}
	}
	function item_new(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Read funny config.php
				$home=true;
				require('../oconfig.php');
				$obj['keys']=array();
	
				if(isset($_CONFIG)){
					if(!array_key_exists($this->data['key'],$_CONFIG)){
						foreach($_CONFIG as $k => $v){
							$_NEW_CONFIG[$this->data['key']]='';
							$_NEW_CONFIG[$k]=$v;
						}

						$obj=$this->gen_config($_NEW_CONFIG);
					}else{
						$obj['errors'][]='Variable exists';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Config not exists';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='You don\'t have permission';
				$obj['error']++;
			}
		}else{
			$obj['errors'][]='CSFR token invalid';
			$obj['error']++;
		}
		
		// Return depends on type
		if($this->data['ajax']){
			// Print JSON object
			echo json_encode($obj);
		}else{
			// Return JSON object
			return $obj;
		}
	}
	function item_update(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Read funny config.php
				$home=true;
				require('../oconfig.php');
				$obj['keys']=array();
	
				if(isset($_CONFIG)){
					if(array_key_exists($this->data['key'],$_CONFIG)){
						$_CONFIG[$this->data['key']]=$this->data['value'];

						$obj=$this->gen_config($_CONFIG);
						$obj['status']='ok';
					}else{
						$obj['errors'][]='Variable not exists';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Config not exists';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='You don\'t have permission';
				$obj['error']++;
			}
		}else{
			$obj['errors'][]='CSFR token invalid';
			$obj['error']++;
		}
		
		// Return depends on type
		if($this->data['ajax']){
			// Print JSON object
			echo json_encode($obj);
		}else{
			// Return JSON object
			return $obj;
		}
	}
}
?>