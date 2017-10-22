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
	function get_dir_listing(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Source path
			$source=$this->data['root_path'].'/libs'.$this->data['path'];

			// Get listing
			if(file_exists($source)){
				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
				$files->setMaxDepth(0);
				foreach ($files as $file){
					$file = str_replace('\\', '/', $file);
					if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) ) continue;
					
					if (is_dir($file) === true){
						$file=str_replace($source .'/', '', $file);
						if($this->data['query']!=''){
							if (strpos($file, $this->data['query']) !== false) {
								$this->items['dirs'][]=$file;
							}
						}else{
							$this->items['dirs'][]=$file;
						}
					}else if (is_file($file) === true){
						$file=str_replace($source . '/', '', $file);
						if($this->data['query']!=''){
							if (strpos($file, $this->data['query']) !== false) {
								$this->items['files'][]=$file;
							}
						}else{
							$this->items['files'][]=$file;
						}
					}
				}
				
				// Return breadcrumb path
				$this->items['breadcrumb']=explode('/',$this->data['path']);
			}
		}
		return $this->once_response();
	}
	function get_file_info(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Source path
			$source=$this->data['root_path'].'/libs'.$this->data['path'];

			// Get listing
			if(file_exists($source)){
				if (filetype($source) == "dir"){
					$this->item['file']=false;
				}else{
					$this->item['file']=true;
				}
			}
		}
		return $obj;
	}

	function item_delete(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			if($this->data['path']!=''){
				$this->data['path']=$this->data['root_path'].'/libs'.$this->data['path'];
					
				if(filetype($this->data['path']) == "dir"){
					$this->recurse_delete($this->data['path']);
				}else{
					@chmod($this->data['path'], 0777);
					unlink($this->data['path']);
				}
			}else{
				$this->set_error('Empty path!');
			}
		}
		return $this->once_response();
	}
	function item_edit(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			$path_in_array=explode("/",$this->data['path']);
			array_pop($path_in_array);
			$path_to_file=implode("/",$path_in_array);
			
			$this->data['path_new']=$this->data['root_path'].'/libs'.$path_to_file.'/'.$this->data['name'];
			$this->data['path_old']=$this->data['root_path'].'/libs'.$this->data['path'];

			if(!file_exists($this->data['path_new'])) {
				rename($this->data['path_old'],$this->data['path_new']);
				$this->item['path']=$path_to_file.'/'.$this->data['name'];
				$this->item['old']=$this->data['path'];
			}else{
				$this->set_error('Already exist!');
			}
		}
		return $this->once_response();
	}
	function item_new(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Check if dir exist if yes do it until +1
			$this->data['path']=$this->data['root_path'].'/libs'.$this->data['path'];

			while(file_exists($this->data['path'].'/NewDir'.$i)){
				$i++;
			}

			$this->data['path']=$this->data['path'].'/NewDir'.$i;
				
			@mkdir($this->data['path']);
			@chmod($this->data['path'], 0777);
				
			$this->item['name']='NewDir'.$i;
		}
		return $this->once_response();
	}
	
	function upload_files(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			//extract extension
			$image_extensions_allowed = array('jpg', 'jpeg', 'png', 'gif');

			// Get count of returned records
			for($i=0;$i<count($this->data["files"]["error"]);$i++){
				if(!$this->data["files"]["error"][$i]){
	
					$extension = strtolower(substr($this->data["files"]['name'][$i], strrpos($this->data["files"]['name'][$i], '.') + 1));
						
					// Check extension
					if(in_array($extension, $image_extensions_allowed)){
						$image_mimes_allowed = array("image/gif","image/png","image/jpeg","image/pjpeg");
						$imageinfo = getlibsize($this->data["files"]['tmp_name'][$i]);
								
						// Check mime
						if(isset($imageinfo) && in_array($imageinfo['mime'], $image_mimes_allowed)){
							// Check size up to 1MB
							if($this->data["files"]["size"][$i]<= 1000000) {
								// If new fie
								$this->data['currentImage']=$this->data['root_path'].'/libs'.$this->data['path'].'/'.$this->data["files"]['name'][$i];

								// Make sure image dir exist
								@mkdir($this->data['root_path'].'/libs'.$this->data['path'].'');
								@chmod($this->data['root_path'].'/libs'.$this->data['path'].'', 0777);
									
								// Move uploaded file to upload dir
								move_uploaded_file($this->data["files"]["tmp_name"][$i],$this->data['currentImage']);
											
								// Resize image
								$this->once_image_resample($this->data['currentImage']);
							}else{
								$this->set_error('We only accept libs up to 1MB');
							}
						}else{
							$this->set_error('We only accept GIF and JPEG libs');
						}
					}else{
						$this->set_error('Extension not allowed');
					}
				}else{
					$this->set_error('Upload error');
				}
			}
		}
		return $this->once_response();
	}
}
?>