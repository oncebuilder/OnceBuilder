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
	function bulk_action(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Prepare statements to delete selected id.
			$stmt3 = $this->pdo->prepare("DELETE FROM edit_langs WHERE id=:id");
					
			// Loop bulk items and make action
			foreach ($this->data['ids'] as $position => $item){
				$obj['ids'][]=$position;
				// Action type then do it
				$stmt3->bindParam(':id', $position, PDO::PARAM_INT);
				$stmt3->execute();
			}
				
			// Refresh lang & grids
			$this->gen_langs();
			$this->gen_grids();
		}
		return $this->once_response();
	}
	function set_limit(){
		// Update page limit with once
		$this->once_page_limit('langs');
		return $this->once_response();
	}
	
	function item_delete(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			if($this->once_delete_item('langs','project_id')){
				$this->gen_langs();
			}
		}else{
			$this->set_error('CSFR token invalid!');
		}
		return $this->once_response();
	}
	function item_new(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Check if lang exist
			$stmt = $this->pdo->prepare("SELECT * FROM edit_themes_langs WHERE project_id=:project_id LIMIT 1");
			$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
			$stmt->execute();
				
			if($stmt->rowCount()){
				// Checks if exists => adding prefix to name
				$stmt = $this->pdo->prepare("SELECT * FROM edit_langs WHERE name=:name AND project_id=:project_id ORDER by id DESC LIMIT 1");
				$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$row=$stmt->fetch(PDO::FETCH_ASSOC);
					$row['name_id']=($row['name_id']+1);
				}else{
					$row['name']=$this->data['name'];
					$row['name_id']='';
				}

				if($this->once_insert_item('langs')){
					// Insert new lang
					$stmt = $this->pdo->prepare("UPDATE edit_langs SET name=:name, name_id=:name_id WHERE id=:id LIMIT 1");
					$stmt->bindParam(':name', $row['name'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':name_id', $row['name_id'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':id', $this->item['id'], PDO::PARAM_INT);
					$stmt->execute();

					if($stmt->rowCount()){
						// JSON object in return with data to display
						$this->item=array(
							"id" => $this->item['id'],
							"category_id" => $this->data['category_id'],
							"name" => $row['name'],
							"name_id" => $row['name_id'],
							"source" => '',
							"temp" => ''
						);
						
						// Refresh langs
						$this->gen_langs();
					}else{
						$this->set_error('Can not add new lang');
					}
				}else{
					$this->set_error('Unknow adding error');
				}
			}else{
				$this->set_error('Could not find languages');
			}
		}
		return $this->once_response();
	}
	function item_update(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Check if lang exist
			$stmt = $this->pdo->prepare("SELECT * FROM edit_langs WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();
			
			if($stmt->rowCount()){
				$stmt = $this->pdo->prepare("UPDATE edit_langs SET ".$this->data['param']."=:".$this->data['param']." WHERE id=:id LIMIT 1");
				$stmt->bindParam(':'.$this->data['param'].'', $this->data['value'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_STR, 255);
				$stmt->execute();

				if($stmt->rowCount()){
					// Refresh lang & grids
					$this->gen_langs();
					$this->gen_grids();
						
					// Set fields to update
					$this->item=array(
						"id" => $this->data['id'],
						$this->data['param'] => $this->data['value']
					);
				}else{
					$this->set_error('Cant save');
				}
			}else{
				$this->set_error('Lang not exist');
			}
		}
		return $this->once_response();
	}
}
?>