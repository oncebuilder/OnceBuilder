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
	function item_select(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				if($this->data['layer_id']>0){
					// Prepare statements to get all layers
					$stmt = $this->pdo->prepare("SELECT id, layer_id, plugin_id FROM edit_layers_cols WHERE id=:id");
					$stmt->bindParam(':id', $this->data['grid_id'], PDO::PARAM_INT);
					$stmt->execute();

					if($stmt->rowCount()){
						$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);

						// Check if that plugin is rl used
						if($wiersz['plugin_id']==$this->data['plugin_id']){
							// Update source with default snippet code
							// Prepare statements to check if page snippet exist
							$stmt = $this->pdo->prepare("SELECT id FROM edit_snippets WHERE id=:id");
							$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
							$stmt->execute();
									
							if($stmt->rowCount()){
								$row=$stmt->fetch(PDO::FETCH_ASSOC);
								
								// Copy snippet from folder and save to files.
								copy($this->data['root_path'].'/once/snippets/'.$row['id'].'/snippet.html',$this->data['root_path'].'/layers/layer_'.$wiersz['id'].'.php'); 

								// Make prefix to reconize layers
								$this->data['type']='layers';
								$this->data['path']='css';
								$this->data['file']='style.css';
								$this->data['source']=file_get_contents($this->data['root_path'].'/once/snippets/'.$row['id'].'/snippet.css');
								$this->data['id']=$this->data['grid_id'];
								$response=$this->once_save_source();
										
								$this->data['type']='layers';
								$this->data['path']='js';
								$this->data['file']='script.js';
								$this->data['source']=file_get_contents($this->data['root_path'].'/once/snippets/'.$row['id'].'/snippet.js');
								$this->data['id']=$this->data['grid_id'];
								$response=$this->once_save_source();
							}

							$obj['status']='ok';
						}else{
							$obj['errors'][]='Page snippet not already exists';
							$obj['error']++;
						}
					}else{
						$obj['errors'][]='Col not exist';
						$obj['error']++;
					}
				}else if($this->data['page_id']>0){
					// Prepare statements to get all layers
					$stmt = $this->pdo->prepare("SELECT id, page_id, plugin_id FROM edit_pages_cols WHERE id=:id");
					$stmt->bindParam(':id', $this->data['grid_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					if($stmt->rowCount()){
						$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
						
						// Check if that plugin is rl used
						if($wiersz['plugin_id']==$this->data['plugin_id']){
							// Update source with default snippet code
							// Prepare statements to check if page snippet exist
							$stmt = $this->pdo->prepare("SELECT id FROM edit_snippets WHERE id=:id");
							$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
							$stmt->execute();
									
							if($stmt->rowCount()){
								$row=$stmt->fetch(PDO::FETCH_ASSOC);
								
								// Copy snippet from folder and save to files.
								copy($this->data['root_path'].'/once/snippets/'.$row['id'].'/snippet.html',$this->data['root_path'].'/pages/page_'.$wiersz['page_id'].'_'.$wiersz['id'].'.php'); 

								// Make prefix to reconize pages
								$this->data['type']='pages';
								$this->data['path']='css';
								$this->data['file']='style.css';
								$this->data['source']=file_get_contents($this->data['root_path'].'/once/snippets/'.$row['id'].'/snippet.css');
								$this->data['id']=$wiersz['page_id'].'_'.$wiersz['id'];
								$response=$this->once_save_source();
										
								$this->data['type']='pages';
								$this->data['path']='js';
								$this->data['file']='script.js';
								$this->data['source']=file_get_contents($this->data['root_path'].'/once/snippets/'.$row['id'].'/snippet.js');
								$this->data['id']=$wiersz['page_id'].'_'.$wiersz['id'];
								$response=$this->once_save_source();
							}

							$obj['status']='ok';
						}else{
							$obj['errors'][]='Page snippet not already exists';
							$obj['error']++;
						}

						// Refresh code
						$this->gen_page($wiersz['page_id']);
						
						$obj['status']='ok';
					}else{
						$obj['errors'][]='Col not exist';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Definitely something wrong';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='CSFR token invalid';
				$obj['error']++;
			}
		}else{
			$obj['errors'][]='You don\'t have permission';
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