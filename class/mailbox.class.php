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
			// Prepare statements to star selected id.
			$stmt1 = $this->pdo->prepare("UPDATE edit_mailbox SET stared = 1 WHERE id=:id");
				
			// Prepare statements to unstar selected id.
			$stmt2 = $this->pdo->prepare("UPDATE edit_mailbox SET stared = 0 WHERE id=:id");
				
			// Prepare statements to delete selected id.
			$stmt3 = $this->pdo->prepare("DELETE FROM edit_mailbox WHERE id=:id");
					
			// Loop bulk items and make action
			foreach ($this->data['ids'] as $position => $item){
				$this->item['ids'][]=$position;
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
				}
			}
		}
		return $this->once_response();
	}
	function set_limit(){
		// Update page limit with once
		$this->once_page_limit('mailbox');
		return $this->once_response();
	}

	function get_mailbox($table){
		# XAMPP fix without turning error info off -------------------
		$sql='';

		// Start condition if any statements
		if($this->data['type_id']!=0 || $this->data['category_id']!=0 || $this->data['ids']!='' || $this->data['query']!='' || $this->data['where']!=''){
			$sql.="WHERE ";
		}

		// Check type_id statement
		if($this->data['type_id']!=0){
			if($this->data['type_id']==1){
				$sql.=" user_id_to=".$this->data['user_id']." AND type_id!=4 AND type_id!=5";
			}else if($this->data['type_id']==2){
				$sql.=" user_id=".$this->data['user_id']." AND type_id=2";
			}else if($this->data['type_id']==3){
				$sql.=" (user_id=".$this->data['user_id']." OR user_id_to=".$this->data['user_id'].") AND stared=1";
			}else{
				$sql.=" user_id_to=".$this->data['user_id']." AND type_id='".$this->data['type_id']."'";
			}
			
			// Check for more statements
			if($this->data['category_id']!=0 || $this->data['ids'] || $this->data['query']!='' || $this->data['where']!=''){
				$sql.=" AND";
			}
		}

		// Check category_id statement
		if($this->data['category_id']!=0){
			$sql.=" category_id='".$this->data['category_id']."'";
			// Check for more statements
			if($this->data['query']!='' || $this->data['ids'] || $this->data['where']!=''){
				$sql.=" AND";
			}
		}

		// Check categories statement
		if(strlen($this->data['ids'])>0){
			$sql.=" category_id IN (".implode(",",$this->data['ids']).")";
			// Check for more statements
			if($this->data['query']!='' || $this->data['where']!=''){
				$sql.=" AND";
			}
		}

		// Check query statement
		if($this->data['query']!=''){
			$sq='(';
			$binds = array();
			foreach($this->data['query_in'] as $k => $v){
				$sq.=''.$v.' LIKE :'.$v.' OR ';
				$binds[]=$v;
			}
			$sq=substr($sq,0, -3);
			$sq.=')';
			$sql.=$sq;
			// Check for more statements
			if($this->data['where']!=''){
				$sql.=" AND";
			}
		}

		// Check where statement
		if($this->data['where']!=''){
			$sql.=" ".$this->data['where']." ";
		}
		
		// Prepare query to get count of items with conditions
		$stmt = $this->pdo->prepare("SELECT COUNT(id) AS ile FROM edit_".$table." ".$sql);
		if($this->data['query']!=''){
			$this->data['query']='%'.$this->data['query'].'%';
			// Loop to bind specified params
			foreach($binds as $k => $v){
				$stmt->bindParam(':'.$binds[$k].'', $this->data['query'], PDO::PARAM_STR, 50);
			}
		}

		$stmt->execute();
		$row=$stmt->fetch(PDO::FETCH_ASSOC);

		if($this->data['page']<=0){
			$this->data['page']=1;
		}

		// Results on page
		if(isset($_SESSION[$table])){
			if(in_array($_SESSION[$table]['results'],array(10,20,50,100))){
				$limit=$_SESSION[$table]['results'];
			}else{
				$limit=10;
			}
		}else{
			$limit=10;
		}

		$limitfrom=intval($this->data['page']*$limit)-$limit;
		$limitto=$limit;

		$obj['limit']=$limit;
		$obj['page']=$this->data['page'];
		$obj['pages']=ceil($row['ile']/$limit);

		// Sort by
		if($this->data['sort_by']!=''){
			$order_by='ORDER by '.$this->data['sort_by'].'';
		}else{
			$order_by='ORDER by id DESC';
		}

		// Get list of items with conditions
		$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table." ".$sql." ".$order_by." LIMIT ".$limitfrom.",".$limitto."");
		if($this->data['query']!=''){
			// Loop to bind specified params
			foreach($binds as $k => $v){
				$stmt->bindParam(':'.$binds[$k].'', $this->data['query'], PDO::PARAM_STR, 50);
			}
		}
		$stmt->execute();

		// Get count of returned records
		if($stmt->rowCount()){
			// Return result in table
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$obj['items'][]=$row;
			}
		}

		// Check if snippet exist.
		if($obj['items']){
			return $obj;
		}else{
			return false;
		}
	}

	function item_new(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Check settings if mailer is on
			$stmt = $this->pdo->prepare("SELECT * FROM edit_settings LIMIT 1");
			//$stmt->bindParam(':email_to', $this->data['email_to'], PDO::PARAM_STR, 30);
			$stmt->execute();

			//@2do settings on
			if($stmt->rowCount()){
				
			}
				
			// Check if user exist
			$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE email=:email_to LIMIT 1");
			$stmt->bindParam(':email_to', $this->data['email_to'], PDO::PARAM_STR, 30);
			$stmt->execute();

			// Get count of returned records
			if($stmt->rowCount()){
				$this->item=$stmt->fetch(PDO::FETCH_ASSOC);
					
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("INSERT INTO edit_mailbox (user_id, type_id, user_id_to, title, message, time) VALUES('".$this->data['user_id']."', 2 , ".$this->item['id'].", '".$this->data['title']."', '".$this->data['message']."', ".$this->data['time'].")");
				$stmt->execute();
					
				// Get item object
				$this->item=array(
					"id" => $this->pdo->lastInsertId(),
					"type_id" => 2
				);
					
				if(!$this->item['id']){
					$this->set_error('Can not insert item to: users');
				}
			}else{
				$this->set_error('User not exist');
			}
		}
		return $this->once_response();
	}
	function item_star(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Prepare statements to get all layers
			$stmt = $this->pdo->prepare("SELECT * FROM edit_mailbox WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();
				
			if($stmt->rowCount()){
				$this->item=$stmt->fetch(PDO::FETCH_ASSOC);
				// Check if its stared/unstared then unstar/star
				if($this->item['stared']==1){
					$stmt = $this->pdo->prepare("UPDATE edit_mailbox SET stared=0 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}else{
					$stmt = $this->pdo->prepare("UPDATE edit_mailbox SET stared=1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}
			}else{
				$this->set_error('User not exist');
			}
		}
		return $this->once_response();
	}
}
?>