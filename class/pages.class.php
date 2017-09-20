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
	function bulk_action(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin
			if($this->once_creator_check()){
				// Prepare statements to star selected id.
				$stmt1 = $this->pdo->prepare("UPDATE edit_pages SET stared = 1 WHERE id=:id");

				// Prepare statements to unstar selected id.
				$stmt2 = $this->pdo->prepare("UPDATE edit_pages SET stared = 0 WHERE id=:id");

				// Prepare statements to delete selected id.
				$stmt3 = $this->pdo->prepare("DELETE FROM edit_pages WHERE id=:id");

				// Loop bulk items and make action
				foreach ($this->data['ids'] as $position => $item){
					$obj['ids'][]=$position;
					// Check action type then do it
					if($this->data['action']=='star'){
						$stmt1->bindParam(':id', $position, PDO::PARAM_INT);
						$stmt1->execute();
					}else if($this->data['action']=='unstar'){
						$stmt2->bindParam(':id', $position, PDO::PARAM_INT);
						$stmt2->execute();
					}else if($this->data['action']=='delete'){
						$stmt3->bindParam(':id', $position, PDO::PARAM_INT);
						$stmt3->execute();

						if($stmt3->rowCount()){
							$this->recurse_delete($this->data['root_path'].'/once/pages/'.$position.'');
						}
					}
				}
				$obj['status']='ok';
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
	function get_edit_data(){//ok
		// Prepare statements to get all layers
		$stmt = $this->pdo->prepare("
			SELECT edit_pages_cols.id AS id, edit_pages_cols.css_id AS item_id, edit_pages_cols.css_class AS item_class, edit_pages_rows.css_id AS row_id, edit_pages_rows.css_class AS row_class, edit_pages_cols.page_id AS page_id, edit_pages_cols.plugin_id AS plugin_id FROM `edit_pages_cols`
			LEFT JOIN edit_pages_rows ON edit_pages_cols.row_id=edit_pages_rows.row_id AND edit_pages_cols.page_id=edit_pages_rows.page_id 
			WHERE edit_pages_cols.id=:id
		");
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
		$stmt = $this->pdo->prepare("SELECT * FROM edit_pages_cols WHERE page_id=:id");
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
			
			if($row['plugin_id']!=0){
				$obj['gridPlugins'][]=$row['plugin_id'];
				$obj['gridPluginsx'][]=$row['id'];
			}
			$obj['gridPluginsz'][]=$row;
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
	
	function set_limit(){//ok
		// Update page limit with once
		$this->once_page_limit('pages');
		$obj['status']='ok';

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
				// Use once to delete item
				$obj=$this->once_delete_item('pages','project_id');
				if($obj['count']){
					$stmt = $this->pdo->prepare("SELECT * FROM edit_pages_cols WHERE page_id=:page_id LIMIT 1");
					$stmt->bindParam(':page_id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
					
					$this->data['page_id']=$this->data['id'];

					if($stmt->rowCount()){
						foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wierszx) {
							// Check if setting allow to perm_delete // Refresh styles / scripts and ajax requests
							if($this->settings['perm_delete']){
								@unlink('../pages/page_'.$this->data['page_id'].'_'.$wierszx['id'].'.php');
								
								// Make prefix to reconize pages
								$this->data['type']='pages';
								$this->data['path']='css';
								$this->data['file']='style.css';
								$this->data['source']='';
								$this->data['id']=$this->data['page_id'].'_'.$wierszx['id'];
								
								$this->once_delete_source();
								
								// Make prefix to reconize pages
								$this->data['type']='pages';
								$this->data['path']='js';
								$this->data['file']='script.js';
								$this->data['source']='';
								$this->data['id']=$this->data['page_id'].'_'.$wierszx['id'];
								
								$this->once_delete_source();
							}
						}
					}
					
					// Delete selected item
					$stmt = $this->pdo->prepare("DELETE FROM edit_pages_cols WHERE page_id=:page_id LIMIT 1");
					$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					// Delete selected item
					$stmt = $this->pdo->prepare("DELETE FROM edit_pages_rows WHERE page_id=:page_id LIMIT 1");
					$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					// Refresh switch
					$this->gen_switch();
					
					// Refresh routes if exist
					$this->gen_grids();

					$obj['status']='ok';
				}else{
					$obj['errors'][]='Page not exist';
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
				// Check if page exist
				$stmt = $this->pdo->prepare("SELECT * FROM edit_pages WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$row=$stmt->fetch(PDO::FETCH_ASSOC);

					$this->data['private']=($this->data['private']=='on'?1:0);
					$this->data['logged']=($this->data['logged']=='on'?1:0);
					$this->data['adult']=($this->data['adult']=='on'?1:0);
					$this->data['admins']=($this->data['admins']=='on'?1:0);
					$this->data['moderators']=($this->data['moderators']=='on'?1:0);
					$this->data['users']=($this->data['users']=='on'?1:0);
					
					$stmt = $this->pdo->prepare("UPDATE edit_pages SET name=:name, title=:title, keywords=:keywords, description=:description, layer_id=:layer_id ,private=:private ,password=:password ,logged=:logged ,adult=:adult ,admins=:admins ,moderators=:moderators ,users=:users WHERE id=:id LIMIT 1");
					$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':title', $this->data['title'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':keywords', $this->data['keywords'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':description', $this->data['description'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':layer_id', $this->data['layer_id'], PDO::PARAM_INT);
					$stmt->bindParam(':private', $this->data['private'], PDO::PARAM_INT);
					$stmt->bindParam(':password', $this->data['password'], PDO::PARAM_STR, 32);
					$stmt->bindParam(':logged', $this->data['logged'], PDO::PARAM_INT);
					$stmt->bindParam(':adult', $this->data['adult'], PDO::PARAM_INT);
					$stmt->bindParam(':admins', $this->data['admins'], PDO::PARAM_INT);
					$stmt->bindParam(':moderators', $this->data['moderators'], PDO::PARAM_INT);
					$stmt->bindParam(':users', $this->data['users'], PDO::PARAM_INT);
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
					
					// Refresh route
					$this->data['page_id']=$this->data['id'];
					$objx=$this->set_route_link();
					
					if($stmt->rowCount()){
						// Set fields to update
						$obj['item']=array(
							"id" => $this->data['id'],
							"name" => $this->data['name'],
							"title" => $this->data['title'],
							"keywords" => $this->data['keywords'],
							"description" => $this->data['description'],
							"layer_id" => $this->data['layer_id'],
							"private" => $this->data['private'],
							"password" => $this->data['password'],
							"logged" => $this->data['logged'],
							"adult" => $this->data['adult'],
							"admins" => $this->data['admins'],
							"moderators" => $this->data['moderators'],
							"users" => $this->data['users']
						);
						
						// Refresh page
						$this->gen_page($this->data['id']);
					}
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Page not exist';
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
				$obj=$this->once_insert_item('pages');
				if($obj['count']){
					// Create empty page file
					@mkdir("../pages/");
					chmod("../pages/", 0777);
					file_put_contents('../pages/page_'.$obj['item']['id'].'.php','');
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
	function item_star(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Prepare statements to get all layers
				$stmt = $this->pdo->prepare("SELECT * FROM edit_pages WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Get count of returned records
				$obj['count']=$stmt->rowCount();
				if($obj['count']){
					$row=$stmt->fetch(PDO::FETCH_ASSOC);
					// Check if its stared/unstared then unstar/star
					if($row['stared']==1){
						// Prepare statements to unstar selected data
						$stmt = $this->pdo->prepare("UPDATE edit_pages SET stared=0 WHERE id=:id LIMIT 1");
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
					}else{
						// Prepare statements to star selected data
						$stmt = $this->pdo->prepare("UPDATE edit_pages SET stared=1 WHERE id=:id LIMIT 1");
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
					}
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Page not exist';
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
	
	function item_grid_delete(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get all layers
				$stmt = $this->pdo->prepare("SELECT id, page_id, row_id FROM edit_pages_cols WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
					
					// Prepare statements to get all layers
					$stmt = $this->pdo->prepare("SELECT SUM(size) AS size FROM edit_pages_cols WHERE page_id=:page_id AND row_id=:row_id LIMIT 1");
					$stmt->bindParam(':page_id', $wiersz['page_id'], PDO::PARAM_INT);
					$stmt->bindParam(':row_id', $wiersz['row_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					$wierszx=$stmt->fetch(PDO::FETCH_ASSOC);
					$obj['total']=$wierszx['size'];
					
					$temp=$this->data['id'];
					
					// Prepare statements to get all layers
					$stmt = $this->pdo->prepare("DELETE FROM edit_pages_cols WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $wiersz['id'], PDO::PARAM_INT);
					$stmt->execute();

					// Check if setting allow to perm_delete // Refresh styles / scripts and ajax requests
					if($this->settings['perm_delete']){
						@unlink('../pages/page_'.$wiersz['page_id'].'_'.$temp.'.php');
						
						// Make prefix to reconize pages
						$this->data['type']='pages';
						$this->data['path']='css';
						$this->data['file']='style.css';
						$this->data['id']=$wiersz['page_id'].'_'.$temp;
						$response=$this->once_delete_source();
							
						$this->data['type']='pages';
						$this->data['path']='js';
						$this->data['file']='script.js';
						$this->data['id']=$wiersz['page_id'].'_'.$temp;
						$response=$this->once_delete_source();
					}
					
					// Make sure to delete row if nothing left in row
					$stmt = $this->pdo->prepare("SELECT id FROM edit_pages_cols WHERE page_id=:page_id AND row_id=:row_id LIMIT 1");
					$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
					$stmt->bindParam(':row_id', $this->data['row_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					if(!$stmt->rowCount()){
						$stmt = $this->pdo->prepare("DELETE FROM edit_pages_rows WHERE page_id=:page_id AND row_id=:row_id");
						$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
						$stmt->bindParam(':row_id', $this->data['row_id'], PDO::PARAM_INT);
					}
					
					// Refresh grid
					$this->gen_page($wiersz['page_id']);
					
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
	function item_grid_edit(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Get selected col
				$stmt = $this->pdo->prepare("SELECT page_id, row_id FROM edit_pages_cols WHERE id=:id");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
					
					// Update grid
					$stmt = $this->pdo->prepare("UPDATE edit_pages_cols SET css_id=:css_id, css_class=:css_class, namespace=:namespace WHERE id=:id");
					$stmt->bindParam(':css_id', $this->data['item_id'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':css_class', $this->data['item_class'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':namespace', $this->data['namespace'], PDO::PARAM_STR, 32);
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
					
					// Get selected row for col
					$stmt = $this->pdo->prepare("SELECT id FROM edit_pages_rows WHERE page_id=:page_id AND row_id=:row_id");
					$stmt->bindParam(':page_id', $wiersz['page_id'], PDO::PARAM_INT);
					$stmt->bindParam(':row_id', $wiersz['row_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					if(!$stmt->rowCount()){
						$stmt = $this->pdo->prepare("
							INSERT INTO edit_pages_rows (project_id, page_id, row_id, css_id, css_class) 
							VALUES(:project_id, :page_id, :row_id, :css_id, :css_class)
						");
						$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt->bindParam(':page_id', $wiersz['page_id'], PDO::PARAM_INT);
						$stmt->bindParam(':row_id', $wiersz['row_id'], PDO::PARAM_INT);
						$stmt->bindParam(':css_id', $this->data['row_id'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':css_class', $this->data['row_class'], PDO::PARAM_STR, 255);
						$stmt->execute();
					}else{
						$stmt = $this->pdo->prepare("UPDATE edit_pages_rows SET css_id=:css_id, css_class=:css_class WHERE page_id=:page_id AND row_id=:row_id");
						$stmt->bindParam(':page_id', $wiersz['page_id'], PDO::PARAM_INT);
						$stmt->bindParam(':row_id', $wiersz['row_id'], PDO::PARAM_INT);
						$stmt->bindParam(':css_id', $this->data['row_id'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':css_class', $this->data['row_class'], PDO::PARAM_STR, 255);
						$stmt->execute();
					}
					
					$obj['item']=array(
						"id" => $this->data['id'],
						"item_id" => $this->data['item_id'],
						"item_class" => $this->data['item_class'],
						"row_id" => $this->data['row_id'],
						"row_class" => $this->data['row_class'],
						"namespace" => $this->data['namespace']
					);

					// Refresh grid
					$this->gen_page($wiersz['page_id']);

					$obj['status']='ok';
				}else{
					$obj['errors'][]='CSFR token invalid';
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
				$stmt = $this->pdo->prepare("SELECT id FROM edit_pages WHERE id=:page_id");
				$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					// Prepare statements to get all layers
					$stmt = $this->pdo->prepare("SELECT MAX(row_id) AS max FROM edit_pages_cols WHERE page_id=:page_id");
					$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
					$wiersz['max']++;
					
					// Prepare statements to get all layers
					$stmt = $this->pdo->prepare("SELECT id FROM edit_pages_cols WHERE page_id=:page_id AND row_id=:row_id AND col_id=1 AND size=1");
					$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
					$stmt->bindParam(':row_id', $wiersz['max'], PDO::PARAM_INT);
					$stmt->execute();
					
					if(!$stmt->rowCount()){
						// Prepare statements to get all layers
						$stmt = $this->pdo->prepare("
							INSERT INTO edit_pages_cols (project_id, page_id, row_id, col_id, size) 
							VALUES(:project_id, :page_id, :max, 1, 1)
						");
						$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
						$stmt->bindParam(':max', $wiersz['max'], PDO::PARAM_INT);
						$stmt->execute();
						
						$col=array(
							"id" => $this->pdo->lastInsertId(),
							"page_id" => $this->data['page_id'],
							"row_id" => $wiersz['max'],
							"col_id" => 1,
							"size" => 1
						);
						$obj['col']=$col;
						
						// Prepare statements to get all layers
						$stmt2 = $this->pdo->prepare("SELECT id FROM edit_pages_cols WHERE page_id=:page_id AND row_id=:row_id");
						$stmt2->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
						$stmt2->bindParam(':row_id', $wiersz['max'], PDO::PARAM_INT);
						$stmt2->execute();
						
						if(!$stmt2->rowCount()){
							$stmt2 = $this->pdo->prepare("INSERT INTO edit_pages_cols (project_id, page_id, row_id) VALUES(:project_id, :page_id, :row_id)");
							$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
							$stmt2->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
							$stmt2->bindParam(':row_id', $wiersz['max'], PDO::PARAM_INT);
							$stmt2->execute();
						}

						// Refresh grid and page
						$this->gen_page($this->data['page_id']);

						$obj['status']='ok';
						
					}else{
						$obj['errors'][]='Row already exists';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Page not exist';
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
	function item_grid_select(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get all layers
				$stmt = $this->pdo->prepare("SELECT id, page_id, plugin_id FROM edit_pages_cols WHERE id=:id");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
					
					// Update source with default plugin code
					if($wiersz['plugin_id']!=$this->data['value'] && $this->data['value']>0){
						// Prepare statements to get all layers
						$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id");
						$stmt->bindParam(':id', $this->data['value'], PDO::PARAM_INT);
						$stmt->execute();
						
						if($stmt->rowCount()){
							$wierszx=$stmt->fetch(PDO::FETCH_ASSOC);
							
							// Copy plugin from folder and save to files. 
							copy('../once/plugins/'.$this->data['value'].'/plugin.php','../pages/page_'.$wiersz['page_id'].'_'.$wiersz['id'].'.php');

							// Make prefix to reconize pages
							$this->data['type']='pages';
							$this->data['path']='css';
							$this->data['file']='style.css';
							$this->data['source']=@file_get_contents('../once/plugins/'.$this->data['value'].'/plugin.css');
							$this->data['id']=$wiersz['page_id'].'_'.$wiersz['id'];
							$response=$this->once_save_source();
							
							$this->data['type']='pages';
							$this->data['path']='js';
							$this->data['file']='script.js';
							$this->data['source']=@file_get_contents('../once/plugins/'.$this->data['value'].'/plugin.js');
							$this->data['id']=$wiersz['page_id'].'_'.$wiersz['id'];
							$response=$this->once_save_source();
						}
					}
					
					// Prepare statements to check if page plugin exist
					$stmt = $this->pdo->prepare("UPDATE `edit_pages_cols` SET plugin_id=:plugin_id WHERE id=:id");
					$stmt->bindParam(':plugin_id', $this->data['value'], PDO::PARAM_INT);
					$stmt->bindParam(':id', $wiersz['id'], PDO::PARAM_INT);
					$stmt->execute();
					
			
					// Check if page content is already exist
					$stmt = $this->pdo->prepare("SELECT COUNT(id) as plugins FROM edit_pages_cols WHERE project_id=:project_id AND page_id=:page_id AND plugin_id>0");
					$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
					$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					$row=$stmt->fetch(PDO::FETCH_ASSOC);
					$obj['plugins']=$row['plugins'];
					
					// Refresh code
					$this->gen_page($wiersz['page_id']);
					
					$obj['status']='ok';
						
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
	function item_grid_save(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Read data from JSON
				$grid=$this->data['data'];

				$stmt = $this->pdo->prepare("UPDATE `edit_pages_cols` SET col_id=:col_id, row_id=:row_id, size=:size WHERE id=:id AND page_id=:page_id");

				// Loop with update
				foreach($grid as $key => $val){
					$stmt->bindParam(':col_id', $grid[$key]['col_id'], PDO::PARAM_INT);
					$stmt->bindParam(':row_id', $grid[$key]['row_id'], PDO::PARAM_INT);
					$stmt->bindParam(':size', $grid[$key]['size'], PDO::PARAM_INT);
					$stmt->bindParam(':id', $grid[$key]['id'], PDO::PARAM_INT);
					$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
					$stmt->execute();
				}
				// Refresh current grid
				$this->gen_page($this->data['page_id']);
				
				$obj['status']='ok';
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
	function item_grid_save_as(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("SELECT id, page_id, plugin_id FROM edit_pages_cols WHERE id=:id LIMIT 1");
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
						$this->data['id']=$row['page_id'].'_'.$row['id'];
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
							file_put_contents('./plugins/'.$obj['item']['id'].'/index.php',$tpl['source']);

							// Copy object files
							copy('../pages/page_'.$row['page_id'].'_'.$row['id'].'.php','./plugins/'.$obj['item']['id'].'/plugin.php');

							// Make prefix to reconize pages
							$this->data['type']='pages';
							$this->data['path']='css';
							$this->data['file']='style.css';
							$this->data['id']=$row['page_id'].'_'.$row['id'];
							file_put_contents('./plugins/'.$obj['item']['id'].'/plugin.css',$this->once_read_source());
							
							$this->data['type']='pages';
							$this->data['path']='js';
							$this->data['file']='script.js';
							$this->data['id']=$row['page_id'].'_'.$row['id'];
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

							// Copy object files
							copy('../pages/page_'.$row['page_id'].'_'.$row['id'].'.php','./snippets/'.$obj['item']['id'].'/snippet.php');

							// Make prefix to reconize pages
							$this->data['type']='pages';
							$this->data['path']='css';
							$this->data['file']='style.css';
							$this->data['id']=$row['page_id'].'_'.$row['id'];
							file_put_contents('./snippets/'.$obj['item']['id'].'/snippet.css',$this->once_read_source());
							
							$this->data['type']='pages';
							$this->data['path']='js';
							$this->data['file']='script.js';
							$this->data['id']=$row['page_id'].'_'.$row['id'];
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
 	function item_grid_visibility(){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to check if layer exists
				$stmt = $this->pdo->prepare("SELECT page_id, hidden FROM edit_pages_cols WHERE id=:id LIMIT 1");
				$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
					
				// Check if page grid exists
				if($stmt->rowCount()){
					$wiersz = $stmt->fetch(PDO::FETCH_ASSOC);
					
					$temp=$this->data['id'];
					$this->data['id']=$wiersz['page_id'].'_'.$this->data['id'];
						
					if($wiersz['hidden']==1){
						$wiersz['hidden']=0;

						// Make prefix to reconize pages
						$this->data['type']='pages';
						$this->data['path']='css';
						$this->data['file']='style.css';
						$this->once_uncomment_source();
						
						// Make prefix to reconize pages
						$this->data['type']='pages';
						$this->data['path']='js';
						$this->data['file']='script.js';
						$this->once_uncomment_source();
					}else{
						$wiersz['hidden']=1;
						
						// Make prefix to reconize pages
						$this->data['type']='pages';
						$this->data['path']='css';
						$this->data['file']='style.css';
						$this->once_comment_source();
						
						// Make prefix to reconize pages
						$this->data['type']='pages';
						$this->data['path']='js';
						$this->data['file']='script.js';
						$this->once_comment_source();
					}
					
					$this->data['id']=$temp;

					// Prepare statements to get selected id.
					$stmt = $this->pdo->prepare("UPDATE edit_pages_cols SET hidden=:hidden WHERE id=:id LIMIT 1");
					$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
					$stmt->bindParam('hidden', $wiersz['hidden'], PDO::PARAM_INT);
					$stmt->execute();
							
					// Set fields to update
					$obj['item']=array(
						"id" => $this->data['id'],
						"hidden" => $wiersz['hidden']
					);
					
					// Refresh code
					$this->gen_page($wiersz['page_id']);
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
					$this->data['type']='pages';
					$this->data['path']='css';
					$this->data['id']=$this->data['page_id'].'_'.$this->data['id'];
					$obj['source']=$this->once_read_source();
					$obj['status']='ok';
				}else if($this->data['file']=='script.js'){
					$this->data['type']='pages';
					$this->data['path']='js';
					$this->data['id']=$this->data['page_id'].'_'.$this->data['id'];
					$obj['source']=$this->once_read_source();
					$obj['status']='ok';
				}else if($this->data['file']=='file.php'){
					$obj['source']=@file_get_contents($this->data['root_path'].'/pages/page_'.$this->data['page_id'].'_'.$this->data['id'].'.php');
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
				$stmt = $this->pdo->prepare("SELECT * FROM edit_pages_cols WHERE id=:id LIMIT 1");
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
					
					@mkdir("../pages");
					chmod("../pages", 0777);
					
					// Save file with content
					if($this->data['file']=='head.php'){
						$obj['source']=@file_put_contents($this->data['root_path'].'/head.php',$this->data['source']);
						$obj['status']='ok';
					}else if($this->data['file']=='global.css'){
						$obj['source']=@file_put_contents($this->data['root_path'].'/css/global.css',$this->data['source']);
					}else if($this->data['file']=='main.js'){
						$obj['source']=@file_put_contents($this->data['root_path'].'/js/main.js',$this->data['source']);
					}else if($this->data['file']=='style.css'){
						$this->data['type']='pages';
						$this->data['path']='css';
						$this->data['id']=$this->data['page_id'].'_'.$this->data['id'];
						$obj['source']=$this->once_save_source();
					}else if($this->data['file']=='script.js'){
						$this->data['type']='pages';
						$this->data['path']='js';
						$this->data['id']=$this->data['page_id'].'_'.$this->data['id'];
						$obj['source']=$this->once_save_source();
					}else if($this->data['file']=='file.php'){
						$obj['source']=@file_put_contents($this->data['root_path'].'/pages/page_'.$this->data['page_id'].'_'.$this->data['id'].'.php',$this->data['source']);
						$obj['source']=$this->gen_page($this->data['page_id']);
					}else{
						$obj['errors'][]='can not load file';
						$obj['error']++;
					}

					//2do Update archive

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