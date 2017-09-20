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
	function get_edit_data(){//ok
		// Prepare statements to get all layers
		$stmt = $this->pdo->prepare("
			SELECT edit_layers_cols.id AS id, edit_layers_cols.css_id AS item_id, edit_layers_cols.css_class AS item_class, edit_layers_rows.css_id AS row_id, edit_layers_rows.css_class AS row_class, container, edit_layers_cols.layer_id AS layer_id, edit_layers_cols.plugin_id AS plugin_id, namespace FROM `edit_layers_cols`
			LEFT JOIN edit_layers_rows ON edit_layers_cols.row_id=edit_layers_rows.row_id AND edit_layers_cols.layer_id=edit_layers_rows.layer_id 
			WHERE edit_layers_cols.id=:id");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount()){
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}else{
			return false;
		}
	}
	function get_grid_data(){//ok
		// Prepare statements to get all grids
		$stmt = $this->pdo->prepare("SELECT * FROM edit_layers_cols WHERE layer_id=:id");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();
		
		$temp=array();
		$temp2=array();
		$layers=array();
		$layers2=array();
		$max=0;
		$switcher=0;
		
		// Fetch results
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			if($max<$row['row_id']){
				$max=$row['row_id'];
			}

			if(!isset($layers[$row['row_id']])){
				$layers[$row['row_id']]=$row;
			}else{
				$layers2[]=$row;
			}
			
			if($row['plugin_id']==-1){
				$switcher=1;
			}
		}

		// Loop with rest update to fix
		if(count($layers2)>0){
			foreach($layers2 as $key => $val){
				$max++;
				$layers[$max]=$layers2[$key];
			}
		}
		
		$obj['grid']=$layers;
		$obj['max']=$max;
		$obj['switcher']=$switcher;
		return $obj;
	}
	
	function item_copy(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("SELECT id FROM edit_layers WHERE user_id=:user_id AND project_id=:project_id AND id=:layer_id LIMIT 1");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
				$stmt->execute();
						
				// Prepare statements to get selected id.
				$stmt2 = $this->pdo->prepare("SELECT id FROM edit_layers WHERE project_id=:project_id AND id=:layer_id_to LIMIT 1");
				$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt2->bindParam(':layer_id_to', $this->data['layer_id_to'], PDO::PARAM_INT);
				$stmt2->execute();

				// Check if layers exist.
				if($stmt->rowCount() && $stmt2->rowCount()){

					// Fetch data
					$obj['layer'] = $stmt->fetch(PDO::FETCH_ASSOC);

					// Prepare statements to get max row id.
					$stmt = $this->pdo->prepare("SELECT MAX(row_id) AS max FROM edit_layers_cols WHERE project_id=:project_id AND layer_id=:layer_id LIMIT 1");
					$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
					$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
					$stmt->execute();
							
					// Get max row
					$rowx = $stmt->fetch(PDO::FETCH_ASSOC);
					$rowx['max']++;

					// Prepare statements to get all layers
					$stmt3 = $this->pdo->prepare("SELECT * FROM edit_layers_cols WHERE project_id=:project_id AND layer_id=:layer_id");
					$stmt3->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
					$stmt3->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
					$stmt3->execute();
							
					// Return result in table
					foreach ($stmt3->fetchAll(PDO::FETCH_ASSOC) as $row) {
						// Insert new layer
						$stmt = $this->pdo->prepare("
							INSERT INTO edit_layers_cols (project_id, layer_id, plugin_id, row_id, col_id, size, css_id, css_class, hidden, namespace) 
							VALUES (:project_id, :layer_id, :plugin_id, :row_id, :col_id, :size, :css_id, :css_class, :hidden, :namespace)
						");
								
						$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt->bindParam(':layer_id', $this->data['layer_id_to'], PDO::PARAM_INT);
						$stmt->bindParam(':plugin_id', $row['plugin_id'], PDO::PARAM_INT);
						$stmt->bindParam(':row_id', $rowx['max'], PDO::PARAM_INT);
						$stmt->bindParam(':col_id', $row['col_id'], PDO::PARAM_INT);
						$stmt->bindParam(':size', $row['size'], PDO::PARAM_INT);
						$stmt->bindParam(':css_id', $row['css_id'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':css_class', $row['css_class'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':hidden', $row['hidden'], PDO::PARAM_INT);
						$stmt->bindParam(':namespace', $row['namespace'], PDO::PARAM_STR, 32);
						$stmt->execute();
										
						// Get last insert id
						$lastInsertId=$this->pdo->lastInsertId();
								
						// Check row if exists
						$stmt = $this->pdo->prepare("SELECT * FROM edit_layers_rows WHERE layer_id=:layer_id AND row_id=:row_id LIMIT 1");
						$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
						$stmt->bindParam('row_id', $row['row_id'], PDO::PARAM_INT);
						$stmt->execute();
								
						// Check if layer exist.
						if($stmt->rowCount()){
							// Get max row
							$rowz = $stmt->fetch(PDO::FETCH_ASSOC);
								
							// Insert new layer
							$stmt = $this->pdo->prepare("
								INSERT INTO edit_layers_rows (project_id, layer_id, row_id, css_id, css_class, container) 
								VALUES (:project_id, :layer_id, :row_id, :css_id, :css_class, :container)
							");
									
							$stmt->bindParam(':project_id', $rowz['project_id'], PDO::PARAM_INT);
							$stmt->bindParam(':layer_id', $this->data['layer_id_to'], PDO::PARAM_INT);
							$stmt->bindParam(':row_id', $rowx['max'], PDO::PARAM_INT);
							$stmt->bindParam(':css_id', $rowz['css_id'], PDO::PARAM_STR, 255);
							$stmt->bindParam(':css_class', $rowz['css_class'], PDO::PARAM_STR, 255);
							$stmt->bindParam(':container', $rowz['container'], PDO::PARAM_INT);
							$stmt->execute();
						}
								
						$rowx['max']++;
							
						//Copy all layer files
						@copy('../layers/layer_'.$row['id'].'.php','../layers/layer_'.$lastInsertId.'.php'); 
						@copy('../layers/layer_'.$row['id'].'.css','../layers/layer_'.$lastInsertId.'.css'); 
						@copy('../layers/layer_'.$row['id'].'.js','../layers/layer_'.$lastInsertId.'.js'); 
					}

					// Refresh grid and layer
					$this->gen_grid($this->data['layer_id_to']);
								
					// Refresh index
					$this->gen_index();

					$obj['status']='ok';
				}else{
					$obj['errors'][]='Both layers must exist';
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
	function item_delete(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				$stmt = $this->pdo->prepare("SELECT * FROM edit_layers WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					if($row['default']==0){
						$temp_layer=$this->data['id'];
						
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_layers_cols WHERE layer_id=:layer_id");
						$stmt2->bindParam(':layer_id', $this->data['id'], PDO::PARAM_INT);
						$stmt2->execute();
						if($stmt2->rowCount()){
							foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $wierszx) {
								// Check if setting allow to perm_delete // Refresh styles / scripts and ajax requests
								if($this->settings['perm_delete']){
									@unlink('../layers/layer_'.$wierszx['id'].'.php');
							
									$this->data['id']=$wierszx['id'];
										
									// Make prefix to reconize pages
									$this->data['type']='layers';
									$this->data['path']='css';
									$this->data['file']='style.css';
									$this->data['source']='';
											
									$this->once_delete_source();
										
									// Make prefix to reconize pages
									$this->data['type']='layers';
									$this->data['path']='js';
									$this->data['file']='script.js';
									$this->data['source']='';
										
									$this->once_delete_source();
								}
							}
						}

						$this->data['id']=$temp_layer;
						
						// Delete layers cols
						$stmt = $this->pdo->prepare("DELETE FROM edit_layers_cols WHERE layer_id=:layer_id");
						$stmt->bindParam(':layer_id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
						
						// Delete layers rows
						$stmt = $this->pdo->prepare("DELETE FROM edit_layers_rows WHERE layer_id=:layer_id");
						$stmt->bindParam(':layer_id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
						
						// Delete grid
						$stmt = $this->pdo->prepare("DELETE FROM edit_layers WHERE id=:id");
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
						
						// Check if setting allow to perm_delete
						if($this->settings['perm_delete']){
							@unlink('../grids/grid_'.$this->data['id'].'.php');
						}
						
						// Refresh index
						$this->gen_index();
						
						// Refresh routes if exist
						$this->gen_grids();
						
						// Refresh switch
						$this->gen_switch();
						
						$obj['status']='ok';
					}else{
						$obj['errors'][]='can not delete default';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Layer not exist';
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
	function item_edit(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Check if layer exist
				$stmt = $this->pdo->prepare("SELECT * FROM edit_layers WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
				
					if($row['default']==0 && $this->data['default']==1){
						$stmt = $this->pdo->prepare("UPDATE edit_layers SET `default`=0 WHERE project_id=:project_id");
						$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt->execute();
						
						// Update default & reset rest
						$stmt = $this->pdo->prepare("UPDATE edit_layers SET name=:name, `default`=:default WHERE id=:id LIMIT 1");
						$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 250);
						$stmt->bindParam(':default', $this->data['default'], PDO::PARAM_INT);
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();

						// Refresh index
						$this->gen_index();
							
						// Refresh routes if exist
						$this->gen_grids();
							
						// Refresh current grid
						$this->gen_grid($this->data['id']);
					}else{
						// Update default
						$stmt = $this->pdo->prepare("UPDATE edit_layers SET name=:name WHERE id=:id LIMIT 1");
						$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 250);
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
					}
					
					// Set fields to update
					$obj['item']=array(
						"id" => $this->data['id'],
						"name" => $this->data['name'],
						"default" => $this->data['default']
					);
					
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Layer not exists';
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
				// Use once to insert item
				$obj=$this->once_insert_item('layers');
				if($obj['count']){
					// Check if any langs is used
					$stmt = $this->pdo->prepare("SELECT id FROM edit_layers WHERE `default`=1 AND project_id=:project_id LIMIT 1");
					$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
					$stmt->execute();
						
					if(!$stmt->rowCount()){
						$stmt = $this->pdo->prepare("UPDATE edit_layers SET `default`=1 WHERE `default`!=1 AND project_id=:project_id LIMIT 1");
						$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt->execute();
					}
					
					// Refresh index
					$this->gen_index();
					
					// Refresh routes if exist
					$this->gen_grids();
					
					// Refresh current grid
					$this->gen_grid($obj['item']['id']);
					
					// Refresh switch
					$this->gen_switch();
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
	
	
	function item_grid_copy(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("SELECT id FROM edit_layers WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['layer_id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					// Prepare statements to get selected id.
					$stmt = $this->pdo->prepare("SELECT * FROM edit_layers_cols WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();

					if($stmt->rowCount()){
						$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
						
						// Prepare statements to get max row id.
						$stmt = $this->pdo->prepare("SELECT MAX(row_id) AS max FROM edit_layers_cols WHERE project_id=:project_id AND layer_id=:layer_id LIMIT 1");
						$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
						$stmt->execute();
								
						// Get max row
						$rowx = $stmt->fetch(PDO::FETCH_ASSOC);
						$rowx['max']++;

						$stmt = $this->pdo->prepare("
							INSERT INTO edit_layers_cols (project_id, layer_id, plugin_id, row_id, col_id, size, css_id, css_class, hidden, namespace) 
							VALUES(:project_id, :layer_id, :plugin_id, :row_id, :col_id, :size, :css_id, :css_class, :hidden, :namespace)
						");
						$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
						$stmt->bindParam(':plugin_id', $wiersz['plugin_id'], PDO::PARAM_INT);
						$stmt->bindParam(':row_id', $rowx['max'], PDO::PARAM_INT);
						$stmt->bindParam(':col_id', $wiersz['col_id'], PDO::PARAM_INT);
						$stmt->bindParam(':size', $wiersz['size'], PDO::PARAM_INT);
						$stmt->bindParam(':css_id', $wiersz['css_id'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':css_class', $wiersz['css_class'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':hidden', $wiersz['hidden'], PDO::PARAM_INT);
						$stmt->bindParam(':namespace', $wiersz['namespace'], PDO::PARAM_STR, 32);
						$stmt->execute();
						
						// Get last insert id
						$lastInsertId=$this->pdo->lastInsertId();
						
						$col=array(
							"id" => $lastInsertId,
							"layer_id" => $this->data['layer_id'],
							"row_id" => $rowx['max'],
							"col_id" => $wiersz['col_id'],
							"size" => $wiersz['size'],
							"css_id" => $wiersz['css_id'],
							"css_class" => $wiersz['css_class'],
							"container" => $wiersz['container'],
							"hidden" => $wiersz['hidden'],
							"namespace" => $wiersz['namespace']
						);
						$obj['col']=$col;
							
						//Copy all layer files
						@copy('../layers/layer_'.$this->data['id'].'.php','../layers/layer_'.$lastInsertId.'.php'); 
						
						
						// 2do or not / copy code block and save
						
						//@copy('../layers/layer_'.$this->data['id'].'.css','../layers/layer_'.$obj['col']['id'].'.css'); 
						//@copy('../layers/layer_'.$this->data['id'].'.js','../layers/layer_'.$obj['col']['id'].'.js'); 

						// Refresh grid and layer
						$this->gen_grid($this->data['layer_id']);

						// Refresh index
						$this->gen_index();

						$obj['status']='ok';
					}else{
						$obj['errors'][]='Col not exists';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Layer not exists';
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
	function item_grid_delete(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Get selected col
				$stmt = $this->pdo->prepare("SELECT id, layer_id, plugin_id FROM edit_layers_cols WHERE id=:id");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
					
					// Get selected col
					$stmt = $this->pdo->prepare("DELETE FROM edit_layers_cols WHERE id=:id");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
					
					// Check if setting allow to perm_delete
					if($this->settings['perm_delete']){
						@unlink('../layers/layer_'.$this->data['id'].'.php');
						
						// Make prefix to reconize pages
						$this->data['type']='layers';
						$this->data['path']='css';
						$this->data['file']='style.css';
						$this->data['source']='';
							
						$this->once_delete_source();
							
						// Make prefix to reconize pages
						$this->data['type']='layers';
						$this->data['path']='js';
						$this->data['file']='script.js';
						$this->data['source']='';
							
						$this->once_delete_source();
					}else{
						// Make prefix to reconize pages
						$this->data['type']='layers';
						$this->data['path']='css';
						$this->data['file']='style.css';
						$this->data['source']='';
							
						$this->once_comment_source();
							
						// Make prefix to reconize pages
						$this->data['type']='layers';
						$this->data['path']='js';
						$this->data['file']='script.js';
						$this->data['source']='';
							
						$this->once_comment_source();
					}
					
					// Delete row
					$stmt = $this->pdo->prepare("SELECT id FROM edit_layers_cols WHERE layer_id=:layer_id AND row_id=:row_id");
					$stmt->bindParam(':layer_id', $wiersz['layer_id'], PDO::PARAM_INT);
					$stmt->bindParam(':row_id', $wiersz['row_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					if(!$stmt->rowCount()){
						$stmt = $this->pdo->prepare("DELETE FROM edit_layers_rows WHERE layer_id=:layer_id AND row_id=:row_id");
						$stmt->bindParam(':layer_id', $wiersz['layer_id'], PDO::PARAM_INT);
						$stmt->bindParam(':row_id', $wiersz['row_id'], PDO::PARAM_INT);
						$stmt->execute();
					}
					
					// Check if deleted layer was page content then refresh switch
					if($wiersz['plugin_id']==-1){
						$this->gen_switch();
					}
					
					// Refresh grid
					$this->gen_grid($wiersz['layer_id']);
					
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Col not exists';
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
	function item_grid_download(){//ok
		if($this->once_creator_check()){
			// Load ZipArchive class to procces download project as zip archive
			if(!extension_loaded('zip')){
				dl('zip.so');
			}

			$layersRows = array();
			$zip = new ZipArchive();
			$archiveName = "../grid.zip";

			if ($zip->open($archiveName, ZIPARCHIVE::CREATE)!==TRUE) {
				exit("cannot open <$archiveName>\n");
			}

			// Set body layers to template {layers}
			$layers="";
			$stmt = $this->pdo->prepare("SELECT * FROM `edit_layers_cols` WHERE layer_id=:layer_id ORDER by row_id, col_id");
			$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()){
				foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wiersz) {
					$layersGrids[$wiersz['row_id']][$wiersz['col_id']]=$wiersz;
					if($wiersz['row_id']>$max){
						$max=$wiersz['row_id'];
					}
				}
			}
			$obj['layers']=$layersSettings;
			$obj['max']=$max;

			//a($obj);
			$stmt = $this->pdo->prepare("SELECT * FROM `edit_layers_rows` WHERE layer_id=:layer_id");
			$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()){
				foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wiersz) {
					$layersRows[$wiersz['row_id']]=$wiersz;
				}
			}
				

			//a($layersRows);
			//a($layersGrids);
				
			$switch=false;
			if(count($layersGrids)>0){
				foreach($layersGrids as $key => $val){
					if($layersRows[$key]['container']==0){
						$layers.='
						<div'.($layersRows[$key]['css_id']!=''?' id="'.$layersRows[$key]['css_id'].'"':'').' class="container-fluid'.($layersRows[$key]['css_class']!=''?' '.$layersRows[$key]['css_class']:'').'">';
					}
					if($layersRows[$key]['container']==1){
						$layers.='
						<div'.($layersRows[$key]['css_id']!=''?' id="'.$layersRows[$key]['css_id'].'"':'').' class="container'.($layersRows[$key]['css_class']!=''?' '.$layersRows[$key]['css_class']:'').'">';
					}
					$layers.='
							<div class="row">';
						foreach($layersGrids[$key] as $key2 => $val2){//id="'.$layersGrids[$key][$key2]['row_id'].'x'.$layersGrids[$key][$key2]['col_id'].'"
							$layers.='
								<div'.($layersGrids[$key][$key2]['css_id']!=''?' id="'.$layersGrids[$key][$key2]['css_id'].'"':'').' class="col-md-'.$layersGrids[$key][$key2]['size'].''.($layersGrids[$key][$key2]['css_class']!=''?' '.$layersGrids[$key][$key2]['css_class']:'').'">';
					$layers.='
								</div>';
						}
					$layers.='
							</div>';
					if($layersRows[$key]['container']==0 || $layersRows[$key]['container']==1){
					$layers.='
						</div>';
					}		
				}
			}
			
			$str="
			<!DOCTYPE html>
			<html xmlns=\"http://www.w3c.org/1999/xhtml\" xml:lang=\"pl\" lang=\"pl\">
				<head>
					<!-- Latest compiled and minified jquery -->
					<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js\"></script>
					
					<!-- Latest compiled and minified CSS -->
					<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css\">
					
					<!-- Optional theme -->
					<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css\">
					
					<!-- Latest compiled and minified JavaScript -->
					<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js\"></script>
				</head>
				<body>
					<div id=\"body\">".$layers."
					</div>
				</body>
			</html>";

			$zip->addFromString("index.html", $str);

			// Close zip
			$zip->close();
			//return false;
			// Setting headers to let it be downloaded
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-Type: application/zip");
			header("Content-Disposition: attachment; filename=" . basename($archiveName) . ";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($archiveName));
			readfile($archiveName);
					
			// Delete file when its done.
			@unlink($archiveName);

			$obj['status']='ok';
			echo json_encode($obj);
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
	function item_grid_edit(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Get selected col
				$stmt = $this->pdo->prepare("SELECT id, layer_id, plugin_id, row_id FROM edit_layers_cols WHERE id=:id");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
					
					$stmt = $this->pdo->prepare("UPDATE edit_layers_cols SET css_id=:css_id, css_class=:css_class, namespace=:namespace WHERE id=:id");
					$stmt->bindParam(':css_id', $this->data['item_id'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':css_class', $this->data['item_class'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':namespace', $this->data['namespace'], PDO::PARAM_STR, 32);
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
					
					// Get selected row for col
					$stmt = $this->pdo->prepare("SELECT id FROM edit_layers_rows WHERE layer_id=:layer_id AND row_id=:row_id");
					$stmt->bindParam(':layer_id', $wiersz['layer_id'], PDO::PARAM_INT);
					$stmt->bindParam(':row_id', $wiersz['row_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					if(!$stmt->rowCount()){
						$stmt = $this->pdo->prepare("
							INSERT INTO edit_layers_rows (project_id, layer_id, row_id, css_id, css_class, container) 
							VALUES(:project_id, :layer_id, :row_id, :css_id, :css_class, :container)
						");
						$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt->bindParam(':layer_id', $wiersz['layer_id'], PDO::PARAM_INT);
						$stmt->bindParam(':row_id', $wiersz['row_id'], PDO::PARAM_INT);
						$stmt->bindParam(':css_id', $this->data['row_id'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':css_class', $this->data['row_class'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':container', $this->data['container'], PDO::PARAM_INT);
						$stmt->execute();
					}else{
						$stmt = $this->pdo->prepare("UPDATE edit_layers_rows SET css_id=:css_id, css_class=:css_class, container=:container WHERE layer_id=:layer_id AND row_id=:row_id");
						$stmt->bindParam(':layer_id', $wiersz['layer_id'], PDO::PARAM_INT);
						$stmt->bindParam(':row_id', $wiersz['row_id'], PDO::PARAM_INT);
						$stmt->bindParam(':css_id', $this->data['row_id'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':css_class', $this->data['row_class'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':container', $this->data['container'], PDO::PARAM_INT);
						$stmt->execute();
					}
					
					$obj['item']=array(
						"id" => $this->data['id'],
						"item_id" => $this->data['item_id'],
						"item_class" => $this->data['item_class'],
						"row_id" => $this->data['row_id'],
						"row_class" => $this->data['row_class'],
						"container" => $this->data['container'],
						"namespace" => $this->data['namespace']
					);

					// Refresh grid
					$this->gen_grid($wiersz['layer_id']);

					$obj['status']='ok';
				}else{
					$obj['errors'][]='Col not exists';
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
	function item_grid_new(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get all layers
				$stmt = $this->pdo->prepare("SELECT id FROM edit_layers WHERE id=:layer_id");
				$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					// Prepare statements to get all layers
					$stmt = $this->pdo->prepare("SELECT MAX(row_id) AS max FROM edit_layers_cols WHERE layer_id=:layer_id");
					$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
					$wiersz['max']++;
					
					// Prepare statements to get all layers
					$stmt = $this->pdo->prepare("SELECT id FROM edit_layers_cols WHERE layer_id=:layer_id AND row_id=:row_id AND col_id=1 AND size=1");
					$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
					$stmt->bindParam(':row_id', $wiersz['max'], PDO::PARAM_INT);
					$stmt->execute();
					
					if(!$stmt->rowCount()){
						// Prepare statements to get all layers
						$stmt = $this->pdo->prepare("
							INSERT INTO edit_layers_cols (project_id, layer_id, row_id, col_id, size) 
							VALUES(:project_id, :layer_id, :max, 1, 1)
						");
						$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
						$stmt->bindParam(':max', $wiersz['max'], PDO::PARAM_INT);
						$stmt->execute();
						
						$col=array(
							"id" => $this->pdo->lastInsertId(),
							"layer_id" => $this->data['layer_id'],
							"row_id" => $wiersz['max'],
							"col_id" => 1,
							"size" => 1
						);
						$obj['col']=$col;
						
						// Prepare statements to get all layers
						$stmt2 = $this->pdo->prepare("SELECT id FROM edit_layers_cols WHERE layer_id=:layer_id AND row_id=:row_id");
						$stmt2->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
						$stmt2->bindParam(':row_id', $wiersz['max'], PDO::PARAM_INT);
						$stmt2->execute();
						
						if(!$stmt2->rowCount()){
							$stmt2 = $this->pdo->prepare("INSERT INTO edit_layers_rows (project_id, layer_id, row_id) VALUES(:project_id, :layer_id, :row_id)");
							$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
							$stmt2->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
							$stmt2->bindParam(':row_id', $wiersz['max'], PDO::PARAM_INT);
							$stmt2->execute();
						}

						// Create empty layer file
						@mkdir("../layers");
						chmod("../layers", 0777);
						file_put_contents('../layers/layer_'.$col['id'].'.php','');
						
						// Refresh grid and layer
						$this->gen_grid($this->data['layer_id']);
						
						// Refresh index
						$this->gen_index();

						$obj['status']='ok';
						
					}else{
						$obj['errors'][]='Row already exists';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Layer not exists';
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
	function item_grid_save(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Read data from JSON
				$grid=$this->data['data'];

				$stmt = $this->pdo->prepare("UPDATE `edit_layers_cols` SET col_id=:col_id, row_id=:row_id, size=:size WHERE id=:id AND layer_id=:layer_id");

				// Loop with update
				foreach($grid as $key => $val){
					$stmt->bindParam(':col_id', $grid[$key]['col_id'], PDO::PARAM_INT);
					$stmt->bindParam(':row_id', $grid[$key]['row_id'], PDO::PARAM_INT);
					$stmt->bindParam(':size', $grid[$key]['size'], PDO::PARAM_INT);
					$stmt->bindParam(':id', $grid[$key]['id'], PDO::PARAM_INT);
					$stmt->bindParam(':layer_id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}
				// Refresh current grid
				$this->gen_grid($this->data['id']);
				
				$obj['status']='ok';
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
	function item_grid_save_as(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("SELECT id, plugin_id  FROM edit_layers_cols WHERE id=:id LIMIT 1");
				$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Check if page grid exists
				if($stmt->rowCount()){
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					if($this->data['type']=='plugins-theme'){
						// copy style to /css folder
						$this->data['type']='layers';
						$this->data['path']='css';
						$this->data['file']='style.css';
						$this->data['id']=$row['id'];
						@mkdir("./plugins/".$row['plugin_id']."/css");
						chmod("./plugins/".$row['plugin_id']."/css", 0777);
						file_put_contents('./plugins/'.$row['plugin_id'].'/css/style'.$this->data['time'].'.css',$this->once_read_source());
					}else if($this->data['type']=='plugins'){
						// Use once to insert empty
						$obj=$this->once_insert_item('plugins');
						if($obj['count']){
							// Make plugin dir
							@mkdir("./plugins/".$obj['item']['id']."");
							@chmod("./plugins/".$obj['item']['id']."", 0777);

							// Prepare statements to get plugin template.
							$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/plugin.php');
							
							// Create preview file
							@mkdir("./plugins");
							chmod("./plugins", 0777);
							file_put_contents('./plugins/'.$obj['item']['id'].'/index.php',$tpl['source']);

							// Copy object files
							copy('../layers/layer_'.$row['id'].'.php','./plugins/'.$obj['item']['id'].'/plugin.php');

							// Make prefix to reconize layers
							$this->data['type']='layers';
							$this->data['path']='css';
							$this->data['file']='style.css';
							$this->data['id']=$row['id'];
							@mkdir("./plugins/".$obj['item']['id']);
							chmod("./plugins/".$obj['item']['id'], 0777);
							file_put_contents('./plugins/'.$obj['item']['id'].'/plugin.css',$this->once_read_source());
							
							$this->data['type']='layers';
							$this->data['path']='js';
							$this->data['file']='script.js';
							$this->data['id']=$row['id'];
							@mkdir("./plugins/".$obj['item']['id']);
							chmod("./plugins/".$obj['item']['id'], 0777);
							file_put_contents('./plugins/'.$obj['item']['id'].'/plugin.js',$this->once_read_source());
						}
					}else{
						// Use once to insert record
						$obj=$this->once_insert('snippets',array(
							"id" => '',
							"user_id"=> $this->data['user_id']
						));
							
						if($obj['count']){
							// Make snippet dir
							@mkdir("./snippets/".$obj['item']['id']."");
							@chmod("./snippets/".$obj['item']['id']."", 0777);

							// Prepare statements to get snippet template.
							$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/snippet.php');
							
							// Create preview file
							file_put_contents('./snippets/'.$obj['item']['id'].'/index.php',$tpl['source']);

							// Transfer php to html -> curl exec
							file_put_contents('./snippets/'.$obj['item']['id'].'/snippet.html',$this->getPage('localhost/layers/layer_'.$row['id'].'.php'));

							// Make prefix to reconize layers
							$this->data['type']='layers';
							$this->data['path']='css';
							$this->data['file']='style.css';
							$this->data['id']=$row['id'];
							@mkdir("./snippets/".$obj['item']['id']);
							chmod("./snippets/".$obj['item']['id'], 0777);
							file_put_contents('./snippets/'.$obj['item']['id'].'/snippet.css',$this->once_read_source());
							
							$this->data['type']='layers';
							$this->data['path']='js';
							$this->data['file']='script.js';
							$this->data['id']=$row['id'];
							@mkdir("./snippets/".$obj['item']['id']);
							chmod("./snippets/".$obj['item']['id'], 0777);
							file_put_contents('./snippets/'.$obj['item']['id'].'/snippet.js',$this->once_read_source());
						}
					}
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
	function item_grid_select(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get all layers
				$stmt = $this->pdo->prepare("SELECT id, layer_id, plugin_id FROM edit_layers_cols WHERE id=:id");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
					$ok=true;
					
					// Check if page content is already exist
					if($this->data['plugin_id']==-1){
						// Prepare statements to check if page plugin exist
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_layers_cols WHERE project_id=:project_id AND layer_id=:layer_id AND plugin_id=-1");
						$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt2->bindParam(':layer_id', $wiersz['layer_id'], PDO::PARAM_INT);
						$stmt2->execute();
						
						if($stmt2->rowCount()){
							$ok=false;
							$obj['errors'][]='Page plugin already exists';
							$obj['error']++;
						}
					}
					
					if($ok){
						// Update source with default plugin code
						if($wiersz['plugin_id']!=$this->data['plugin_id'] && $this->data['plugin_id']>0){
							// Prepare statements to check if page plugin exist
							$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id");
							$stmt->bindParam(':id', $this->data['plugin_id'], PDO::PARAM_INT);
							$stmt->execute();
							
							if($stmt->rowCount()){
								// Copy plugin from folder and save to files. 
								copy('../once/plugins/'.$this->data['plugin_id'].'/plugin.php','../layers/layer_'.$wiersz['id'].'.php'); 
								
								// Make prefix to reconize pages
								$this->data['type']='layers';
								$this->data['path']='css';
								$this->data['file']='style.css';
								$this->data['source']=@file_get_contents('../once/plugins/'.$this->data['plugin_id'].'/plugin.css');
								$this->data['id']=$this->data['id'];
								$response=$this->once_save_source();
									
								$this->data['type']='layers';
								$this->data['path']='js';
								$this->data['file']='script.js';
								$this->data['source']=@file_get_contents('../once/plugins/'.$this->data['plugin_id'].'/plugin.js');
								$this->data['id']=$this->data['id'];
								$response=$this->once_save_source();
							}
						}
						
						// Prepare statements to check if page plugin exist
						$stmt = $this->pdo->prepare("UPDATE `edit_layers_cols` SET plugin_id=:plugin_id WHERE id=".$wiersz['id']."");
						$stmt->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
						$stmt->execute();

						// Refresh code
						$this->gen_grid($wiersz['layer_id']);

						// Refresh index
						$this->gen_index();
						
						$obj['status']='ok';
					}
					// Return old values
					$obj['old']=$wiersz['plugin_id'];
				}else{
					$obj['errors'][]='Col not exist';
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
	function item_grid_visibility(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("SELECT layer_id, hidden FROM edit_layers_cols WHERE id=:id LIMIT 1");
				$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Check if page grid exists
				if($stmt->rowCount()){
					$wiersz = $stmt->fetch(PDO::FETCH_ASSOC);
					
					
					if($wiersz['hidden']==1){
						$wiersz['hidden']=0;

						// Make prefix to reconize pages
						$this->data['type']='layers';
						$this->data['path']='css';
						$this->data['file']='style.css';
						$this->once_uncomment_source();
						
						// Make prefix to reconize pages
						$this->data['type']='layers';
						$this->data['path']='js';
						$this->data['file']='script.js';
						$this->once_uncomment_source();
					}else{
						$wiersz['hidden']=1;
						
						// Make prefix to reconize pages
						$this->data['type']='layers';
						$this->data['path']='css';
						$this->data['file']='style.css';
						$this->once_comment_source();
						
						// Make prefix to reconize pages
						$this->data['type']='layers';
						$this->data['path']='js';
						$this->data['file']='script.js';
						$this->once_comment_source();
					}
					
					// Prepare statements to get selected id.
					$stmt = $this->pdo->prepare("UPDATE edit_layers_cols SET hidden=:hidden WHERE id=:id LIMIT 1");
					$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
					$stmt->bindParam('hidden', $wiersz['hidden'], PDO::PARAM_INT);
					$stmt->execute();

					// Set fields to update
					$obj['item']=array(
						"id" => $this->data['id'],
						"hidden" => $wiersz['hidden']
					);
					
					// Refresh code
					$this->gen_grid($wiersz['layer_id']);
					
					$obj['status']='ok';
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

 	function load_source(){//ok
		if(true){//$this->once_csrf_token_check($this->data['csrf_token']
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Open selected file
				if($this->data['file']=='head.php'){
					$obj['source']=@file_get_contents($this->data['root_path'].'/head.php');
					$obj['status']='ok';
				}else if($this->data['file']=='global.css'){
					$obj['source']=@file_get_contents($this->data['root_path'].'/css/global.css');
					$obj['status']='ok';
				}else if($this->data['file']=='main.js'){
					$obj['source']=@file_get_contents($this->data['root_path'].'/js/main.js');
					$obj['status']='ok';
				}else if($this->data['file']=='style.css'){
					$this->data['type']='layers';
					$this->data['path']='css';
					$obj['source']=$this->once_read_source();
					$obj['status']='ok';
				}else if($this->data['file']=='script.js'){
					$this->data['type']='layers';
					$this->data['path']='js';
					$obj['source']=$this->once_read_source();
					$obj['status']='ok';
				}else if($this->data['file']=='file.php'){
					$obj['source']=@file_get_contents($this->data['root_path'].'/layers/layer_'.$this->data['id'].'.php');
					$obj['status']='ok';
				}else{
					$obj['errors'][]='can not load file';
					$obj['error']++;
				}

				if(!$obj['source']){
					$obj['source']='';
				}
			}else{
				$obj['errors'][]='You don\'t have permission';
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
 	function save_source(){//ok
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected grid
				$stmt = $this->pdo->prepare("SELECT * FROM edit_layers_cols WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Check if grid exist then save source to file
				$obj['count']=$stmt->rowCount();
				if($obj['count']){
					$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

					@mkdir("../js");
					chmod("../js", 0777);
					
					@mkdir("../css");
					chmod("../css", 0777);
					
					@mkdir("../layers");
					chmod("../layers", 0777);
					
					// Save file with content
					if($this->data['file']=='head.php'){
						$obj['source']=@file_put_contents($this->data['root_path'].'/head.php',$this->data['source']);
						$obj['status']='ok';
					}else if($this->data['file']=='global.css'){
						$obj['source']=@file_put_contents($this->data['root_path'].'/css/global.css',$this->data['source']);
					}else if($this->data['file']=='main.js'){
						$obj['source']=@file_put_contents($this->data['root_path'].'/js/main.js',$this->data['source']);
					}else if($this->data['file']=='style.css'){
						$this->data['type']='layers';
						$this->data['path']='css';
						$obj['source']=$this->once_save_source();
					}else if($this->data['file']=='script.js'){
						$this->data['type']='layers';
						$this->data['path']='js';
						$obj['source']=$this->once_save_source();
					}else if($this->data['file']=='file.php'){
						// Check if it has namespace
						if($obj['item']['namespace']!=''){
							// Prepare statements to get selected user
							$stmt = $this->pdo->prepare("SELECT * FROM edit_layers_cols WHERE project_id=:project_id AND namespace=:namespace");
							$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
							$stmt->bindParam(':namespace', $obj['item']['namespace'], PDO::PARAM_STR, 255);
							$stmt->execute();
							if($stmt->rowCount()){
								// Return source in table
								foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
									file_put_contents($this->data['root_path'].'/layers/layer_'.$row['id'].'.php',$this->data['source']);
								}
							}
						}else{
							$obj['source']=@file_put_contents($this->data['root_path'].'/layers/layer_'.$this->data['id'].'.php',$this->data['source']);
						}
					}else{
						$obj['errors'][]='can not load file';
						$obj['error']++;
					}

					if($obj['source']){
						$obj['status']='ok';
					}
				}else{
					$obj['errors'][]='Plugin not exist / for selected user';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='CSFR token invalid!';
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