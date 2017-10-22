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
	function variables_get($table){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
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
		}
		return $obj;
	}
	function bulk_action(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Read funny config.php
			$home=true;
			require('../oconfig.php');
					
			// Loop bulk items and make action
			foreach ($this->data['ids'] as $position => $item){
				unset($_CONFIG[$position]);
				$obj['ids'][]=$position;
			}
			$this->gen_config($_CONFIG);
		}
		return $this->once_response();
	}
	function item_delete(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Read funny config.php
			$home=true;
			require_once('../config.php');
			
			if(isset($_CONFIG)){
				if(strlen($this->data['key'])>0){
					unset($_CONFIG[$this->data['key']]);
					$this->gen_config($_CONFIG);
				}else{
					$this->set_error('Variable not exists');
				}
			}else{
				$this->set_error('Config not exists');
			}
		}
		return $this->once_response();
	}
	function item_new(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Read funny config.php
			$home=true;
			require('../oconfig.php');
	
			if(isset($_CONFIG)){
				if(!array_key_exists($this->data['key'],$_CONFIG)){
					foreach($_CONFIG as $k => $v){
						$_NEW_CONFIG[$this->data['key']]='';
						$_NEW_CONFIG[$k]=$v;
					}
					$this->gen_config($_NEW_CONFIG);
				}else{
					$this->set_error('Variable exists');
				}
			}else{
				$this->set_error('API not authorized');
			}
		}
		return $this->once_response();
	}
	function item_update(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Read funny config.php
			$home=true;
			require('../oconfig.php');
			$obj['keys']=array();
	
			if(isset($_CONFIG)){
				if(array_key_exists($this->data['key'],$_CONFIG)){
					$_CONFIG[$this->data['key']]=$this->data['value'];
					$this->gen_config($_CONFIG);
				}else{
					$this->set_error('Variable not exists');
				}
			}else{
				$this->set_error('Config not exists');
			}
		}
		return $this->once_response();
	}
}
?>