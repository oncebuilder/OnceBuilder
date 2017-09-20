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
				// Prepare statements to delete selected id.
				$stmt3 = $this->pdo->prepare("DELETE FROM edit_routes WHERE id=:id");
				
				// Loop bulk items and make action
				foreach ($this->data['ids'] as $position => $item){
					$obj['ids'][]=$position;
					// Action type then do it
					$stmt3->bindParam(':id', $position, PDO::PARAM_INT);
					$stmt3->execute();
				}
				
				// Refresh config & grids
				$this->gen_routes();
				$this->gen_grids();
							
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
	function set_limit(){//ok
		// Update page limit with once
		$this->once_page_limit('routes');
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
			// Check if user is creator/admin
			if($this->once_creator_check()){
				// Use once to delete item
				$obj=$this->once_delete_item('routes','project_id');
				
				// Refresh routes
				if($obj['count']){
					$this->gen_routes();
					$obj['status']='ok';
				}
			}else{
				$obj['errors'][]='You don\'t have permission!';
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
	function item_new(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Check if route exist
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes_langs WHERE project_id=:project_id LIMIT 1");
				$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					// Checks if exists => adding prefix to name
					$stmt = $this->pdo->prepare("SELECT * FROM edit_routes WHERE name=:name AND project_id=:project_id ORDER by id DESC LIMIT 1");
					$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
					$stmt->execute();
				
					if($stmt->rowCount()){
						$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
						$wiersz['name_id']=($wiersz['name_id']+1);
					}else{
						$wiersz['name']=$this->data['name'];
						$wiersz['name_id']='';
					}

					$obj=$this->once_insert_item('routes');
					if($obj['count']){
						// Insert new route
						$stmt = $this->pdo->prepare("UPDATE edit_routes SET name=:name, name_id=:name_id WHERE id=:id LIMIT 1");
						$stmt->bindParam(':name', $wiersz['name'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':name_id', $wiersz['name_id'], PDO::PARAM_STR, 255);
						$stmt->bindParam(':id', $obj['item']['id'], PDO::PARAM_INT);
						$stmt->execute();

						if($stmt->rowCount()){
							// JSON object in return with data to display
							$obj['item']=array(
								"id" => $obj['item']['id'],
								"category_id" => $this->data['category_id'],
								"name" => $wiersz['name'],
								"name_id" => $wiersz['name_id'],
								"source" => '',
								"temp" => ''
							);
							
							// Refresh routes
							$this->gen_routes();
							$obj['status']='ok';
						}else{
							$obj['error']='can not add new routes';
						}
					}else{
						$obj['error']='unknow adding error';
					}
				}else{
					$obj['errors'][]='Route not exist';
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
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Check if route exist
				$stmt = $this->pdo->prepare("SELECT * FROM edit_routes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$wiersz=$stmt->fetch(PDO::FETCH_ASSOC);
					
					$stmt = $this->pdo->prepare("UPDATE edit_routes SET ".$this->data['param']."=:".$this->data['param']." WHERE id=:id LIMIT 1");
					$stmt->bindParam(':'.$this->data['param'].'', $this->data['value'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_STR, 255);
					$stmt->execute();

					if($stmt->rowCount()){
						// Refresh routes & grids
						$this->gen_routes();
						$this->gen_grids();
						
						// Set fields to update
						$obj['item']=array(
							"id" => $this->data['id'],
							$this->data['param'] => $this->data['value']
						);

						$obj['status']='ok';
					}else{
						$obj['errors'][]='Cant save';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Route not exist';
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
	function set_link(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				$obj=$this->set_route_link();
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