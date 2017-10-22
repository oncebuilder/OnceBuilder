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

set_time_limit(60);

// This is OnceBuilder core it should be loaded before HTML output
class core{
	public $data;
	public $error;
	public $errors;
	public $item;
	public $items;
	public $pdo;
	public $settings;
	// VARS BELOW CAN BE DEPRECATED IN FUTURE

	// Initialize varibles and files that cant be oversaved
	function __construct($_CONFIG){
		if($_CONFIG['datahost'] && $_CONFIG['database']){
			// Start up database driver
			try {
				/*
					"Many web applications will benefit from making persistent connections to database servers.
					Persistent connections are not closed at the end of the script, but are cached and re-used when another script requests a connection using the same credentials.
					The persistent connection cache allows you to avoid the overhead of establishing a new connection
					every time a script needs to talk to a database, resulting in a faster web application."
				*/
				$this->pdo = new PDO('mysql:host='.$_CONFIG['datahost'].';dbname='.$_CONFIG['database'].'', $_CONFIG['datauser'], $_CONFIG['datapass'], array(
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
				));
				//PDO::ATTR_PERSISTENT => true,
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			} catch (Exception $e) {
				die("Unable to connect: " . $e->getMessage());
			}
		}
		// Global roots data
		$this->data = $_CONFIG;
		
		// Start of usefull variables
		$this->data['time'] = time();

		//echo "-1-->".__DIR__."<---<br>";
		$this->data['root_path']=str_replace('\once\class', '', __DIR__);
		$this->data['root_path']=str_replace('/once/class', '', __DIR__);
		$this->data['root_path']=str_replace('\\', '/', $this->data['root_path']);
		$this->data['root_path']=$this->data['root_path']==''?'..':$this->data['root_path'];
		$this->data['root_path']='..';
		
		//echo "-222-->".$this->data['root_path']."<---<br>";

		//$this->data['root_path'] = $_SERVER["DOCUMENT_ROOT"]=='/'?'../':($_SERVER["DOCUMENT_ROOT"]);//??realpath

		// Users data
		$this->data['user_logged'] = isset($_SESSION['user_logged']) ? $_SESSION['user_logged'] : '';
		$this->data['user_id'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
		$this->data['user_type_id'] = isset($_SESSION['user_type_id']) ? $_SESSION['user_type_id'] : '';
		$this->data['user_username'] = $_SESSION['user_username']!=''?$_SESSION['user_username']:'Web developer';
		$this->data['user_level'] = $this->data['user_type_id']==1?'Creator':'User';
		$this->data['user_balance'] = isset($_SESSION['user_balance']) ? $_SESSION['user_balance'] : '1337';
		$this->data['user_lang'] = isset($_SESSION['user_lang']) ? $this->filter_string($_SESSION['user_lang']) : 'en';
		$this->data['user_ip'] = $this->once_user_ip();

		// Project data for development mode
		if(!isset($_SESSION['project_id'])){
			if($this->once_demo() || $this->once_creator_check()){
				// Get selected user
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE `default`=1");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Get count of returned records
				$user['count']=$stmt->rowCount();
				if($user['count']){
					$user['item']=$stmt->fetch(PDO::FETCH_ASSOC);
					$this->data['project_id'] = $_SESSION['project_id'] = $user['item']['id'];
				}
			}
		}else{
			if($_SESSION['project_id']==0){
				if($this->once_creator_check()){
					$stmt = $this->pdo->prepare("UPDATE edit_themes SET `default`=1 WHERE user_id=:user_id LIMIT 1");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->execute();
				}
				
				// Get selected user
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE `default`=1");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Get count of returned records
				$user['count']=$stmt->rowCount();
				if($user['count']){
					$user['item']=$stmt->fetch(PDO::FETCH_ASSOC);
					$this->data['project_id'] = $_SESSION['project_id'] = $user['item']['id'];
				}
			}else{
				$this->data['project_id'] = intval($_SESSION['project_id']);
			}
		}

		// Builder settings
		$this->settings['perm_delete'] = true;
	}
	function once_demo(){
		return true;
	}
	// Set & get data
	function set_data($t){
		/*
			Set data with array into function args
			Example:
			$this->set_data(array(
				"project_name" => $this->filter_string($_GET['name'])
			));
		*/
		foreach($t as $key => $value){
			$this->data[$key]=$value;
		}
	}
	function get_data($t){
		return $this->data[$t];
	}
	// Set error
	function set_error($obj){
		$this->error++;
		$this->errors[]=$obj;
	}
	// Response
	function once_response(){
		$obj=array();
		if(isset($this->item)) $obj['item']=$this->item;
		if(isset($this->items)) $obj['items']=$this->items;
		if(isset($this->error)) $obj['error']=$this->error;
		if(isset($this->errors)) $obj['errors']=$this->errors;
		if($this->error==0) $obj['status']='ok';
		
		// Return depends on type
		if($this->data['ajax']){
			// Print JSON object
			echo json_encode($obj);
		}else{
			// Return Array object
			return $obj;
		}
	}
	// Get the user IP address
	function once_user_ip() {
		$_SERVER['HTTP_CLIENT_IP'] = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '';
		$_SERVER['HTTP_X_FORWARDED_FOR'] = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
		$_SERVER['HTTP_X_FORWARDED'] = isset($_SERVER['HTTP_X_FORWARDED']) ? $_SERVER['HTTP_X_FORWARDED'] : '';
		$_SERVER['HTTP_FORWARDED_FOR'] = isset($_SERVER['HTTP_FORWARDED_FOR']) ? $_SERVER['HTTP_FORWARDED_FOR'] : '';
		$_SERVER['HTTP_FORWARDED'] = isset($_SERVER['HTTP_FORWARDED']) ? $_SERVER['HTTP_FORWARDED'] : '';
		$_SERVER['REMOTE_ADDR'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

		if($_SERVER['HTTP_CLIENT_IP']){
			return $_SERVER['HTTP_CLIENT_IP'];
		}else if($_SERVER['HTTP_X_FORWARDED_FOR']){
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else if($_SERVER['HTTP_X_FORWARDED']){
			return $_SERVER['HTTP_X_FORWARDED'];
		}else if($_SERVER['HTTP_FORWARDED_FOR']){
			return $_SERVER['HTTP_FORWARDED_FOR'];
		}else if($_SERVER['HTTP_FORWARDED']){
			return $_SERVER['HTTP_FORWARDED'];
		}else if($_SERVER['REMOTE_ADDR']){
			return $_SERVER['REMOTE_ADDR'];
		}else{
			return 'UNKNOWN';
		}
	}
	
	############################ RECURSES FUNCTIONS ##################################################
	// Copy whole dirs and files from dir
	function recurse_copy($src,$dst){
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
	// Delete whole dirs and files from dir
	function recurse_delete($dir){
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir"){
						$this->recurse_delete($dir."/".$object);
					}else{
						@chmod($dir."/".$object, 0777);
						unlink($dir."/".$object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	############################ PATHS ##################################################
	function get_absolute_path($path) {
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		$absolutes = array();
		foreach ($parts as $part) {
			if ('.' == $part) continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		return implode(DIRECTORY_SEPARATOR, $absolutes);
	}
	############################ CSRF PROTECTION ##################################################
	// Creating csrf_token
	function once_csrf_token($option=false){
		// Generate token && return
		if($option==false){
			$_SESSION['csrf_token'] = md5($this->data['api_key'].''.$this->data['time']);
		}
			
		return $_SESSION['csrf_token'];
	}
	// Checking csrf_token
	function once_csrf_token_check($token){
		if($token==$_SESSION['csrf_token']){
			return true;
		}else{//its off currently
			$this->set_error('CSFR token is invalid');
			return true;
		}
	}
	############################ PERMISSION PROTECTION ##################################################
	// Checking permissions
	function once_creator_check($option=false){
		if($this->data['user_type_id']==1){
			return true;
		}else{
			if(!$option) $this->set_error('No permission');
			return false;
		}
	}
	// Checking permissions
	function once_logged_check(){
		if($this->data['user_logged']){
			return true;
		}else{
			$this->set_error('No permission');
			return false;
		}
	}
	############################ SQL ONCE QUERIES ##################################################
	//	Lets make simple to make query, 1st parm describe table, 2nd contains fields and values in array
	//	Example:
	//	$this->once_insert('edit_files',array(
	//		"id" => 1
	//	));
	//	Function automaticly convert it into:
	//	INSERT INTO `edit_files` (id) VALUES ('1') and execute it.
	//	Any errors will be saved
	// Insert record
	function once_insert($table,$f){//&updates
		if(count($f)>0){
			foreach($f as $key => $value){
				$fields=$key.",";
				$values="'".$value."',";
				//$this->data[$key]=$value; depraced
			}
			$fields=substr($fields, 0, -1);
			$values=substr($values, 0, -1);
			$stmt = $this->pdo->prepare("INSERT INTO `edit_".$table."` (".$fields.") VALUES (".$values.")");
			$stmt->execute();
		}

		// Get count of returned records
		if($stmt->rowCount()){
			// Get created data
			$this->item=array(
				"id" => $this->pdo->lastInsertId()
			);
			return array("item" => $this->item);
		}else{
			$this->set_error('Could not insert to: '.$table.'');
		}
	}
	// Create once item
	function once_insert_item($table){

		if(!isset($this->data['type_id'])){
			$this->data['type_id']=0;
		}
		if(!isset($this->data['category_id'])){
			$this->data['category_id']=0;
		}

		// Insert new record
		$stmt = $this->pdo->prepare("
			INSERT INTO edit_".$table." (user_id, project_id, type_id, category_id)
			VALUES (:user_id, :project_id, :type_id, :category_id)
		");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->bindParam(':type_id', $this->data['type_id'], PDO::PARAM_INT);
		$stmt->bindParam(':category_id', $this->data['category_id'], PDO::PARAM_INT);
		$stmt->execute();

		// Return data & status if item created
		if($stmt->rowCount()){
			// Get created data
			$this->item=array(
				"id" => $this->pdo->lastInsertId(),
				"user_id" => $this->data['user_id'],
				"project_id" => $this->data['project_id'],
				"type_id" => $this->data['type_id'],
				"category_id" => $this->data['category_id']
			);
			return array("item" => $this->item);
		}else{
			// Return error if item not created
			$this->set_error('Can not insert item to: '.$table.'');
		}
	}
	
	// Get once item
	function once_select_item($table,$type=''){
		// Check statements type
		if($type=='project_id'){
			$sql="AND project_id=".$this->data['project_id']."";
		}else if($type=='user_id'){
			$sql="AND user_id=".$this->data['user_id']."";
		}else if($type=='all' || $type=='both'){
			$sql="AND project_id=".$this->data['project_id']." AND user_id=".$this->data['user_id']."";
		}else{
			$sql='';
		}

		// Get selected data
		$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table." WHERE id=:id ".$sql." ORDER by id ASC");
		$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		// Return data & status if item exit
		if($stmt->rowCount()){
			$this->item=$stmt->fetch(PDO::FETCH_ASSOC);
			return array("item" => $this->item);
		}else{
			// Return error if item not created
			$this->set_error('Can find item in: '.$table.'');
		}
	}
	// Get once item
	function once_select_item_key($table,$key,$type=''){
		// Check statements type
		if($type=='project_id'){
			$sql="AND project_id=".$this->data['project_id']."";
		}else if($type=='user_id'){
			$sql="AND user_id=".$this->data['user_id']."";
		}else if($type=='all' || $type=='both'){
			$sql="AND project_id=".$this->data['project_id']." AND user_id=".$this->data['user_id']."";
		}else{
			$sql='';
		}

		// Get selected data
		$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table." WHERE ".$key."=:".$key." ".$sql." ORDER by id ASC");
		$stmt->bindParam(":".$key."", $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		// Get count of returned records
		$once['count']=$stmt->rowCount();
		if($once['count']){
			$once['item']=$stmt->fetch(PDO::FETCH_ASSOC);
		}

		// Return once
		return $once;
	}
	// Get all records (used for small results)
	function once_select_items($table,$type=''){
		// Check statements type
		if($type=='project_id'){
			$sql="WHERE project_id=".$this->data['project_id']."";
		}else if($type=='user_id'){
			$sql="WHERE user_id=".$this->data['user_id']."";
		}else if($type=='all' || $type=='both'){
			$sql="WHERE project_id=".$this->data['project_id']." AND user_id=".$this->data['user_id']."";
		}else{
			$sql='';
		}

		// Get selected data
		$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table." ".$sql." ORDER by id ASC");
		$stmt->execute();

		// Get count of returned records
		$once['count']=$stmt->rowCount();
		if($once['count']){
			// Return result in table
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$once['items'][]=$row;
			}
		}

		// Return once
		return $once;
	}
	// Get records resources with range and display limit
	function once_select_items_page($table,$type=''){
		# XAMPP fix without turning error info off -------------------
		$sql='';

		// Start condition if any statements
		if($type!='' || $this->data['type_id']!=0 || $this->data['category_id']!=0 || $this->data['ids']!='' || $this->data['query']!='' || $this->data['where']!=''){
			$sql.="WHERE ";
		}

		// Check statements type
		if($type=='project_id'){
			$sql.="project_id=".$this->data['project_id']." ";
		}else if($type=='user_id'){
			$sql.="user_id=".$this->data['user_id']." ";
		}else if($type=='all'){
			$sql.="project_id=".$this->data['project_id']." AND user_id=".$this->data['user_id']." ";
		}

		// Check for more statements
		if($type=='project_id' || $type=='user_id' || $type=='all'){
			if($this->data['type_id']!=0 || $this->data['category_id']!=0 || $this->data['ids'] || $this->data['query']!='' || $this->data['where']!=''){
				$sql.=" AND";
			}
		}

		// Check type_id statement
		if($this->data['type_id']!=0){
			$sql.=" type_id='".$this->data['type_id']."'";
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

		$once['limit']=$limit;
		$once['page']=$this->data['page'];
		$once['pages']=ceil($row['ile']/$limit);

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
		$once['count']=$stmt->rowCount();
		if($once['count']){
			// Return result in table
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$once['items'][]=$row;
			}
		}

		return $once;
	}
	// Delete once item by id
	function once_delete_item($table,$type=''){
		// Check statements type
		if($type=='project_id'){
			$sql="AND project_id=".$this->data['project_id']."";
		}else if($type=='user_id'){
			$sql="AND user_id=".$this->data['user_id']."";
		}else if($type=='all'){
			$sql="AND project_id=".$this->data['project_id']." AND user_id=".$this->data['user_id']."";
		}else{
			$sql='';
		}

		// Get selected data
		$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table." WHERE id=:id ".$sql." LIMIT 1");
		$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		// Check if item exist
		if($stmt->rowCount()){
			// Delete selected item
			$stmt = $this->pdo->prepare("DELETE FROM edit_".$table." WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();

			if($stmt->rowCount()){
				return true;
			}else{
				$this->set_error('Can not delete item from: '.$table.'');
			}
		}else{
			$this->set_error('Item not exist in: '.$table.'');
		}
	}
	// Set page limit
	function once_page_limit($module){
		$_SESSION[$module]['results']=$this->data['limit'];
	}
	############################ ONCE USER SET ##################################################
	// Set unique visit
	function once_set_user_visit($table){
		$object=substr($table, 0, -1);
		if($this->data['user_logged']){
			// Get visit from selected user
			$stmt = $this->pdo->prepare("SELECT id FROM edit_".$table."_visits WHERE user_id=:user_id AND ".$object."_id=:".$object."_id LIMIT 1");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':'.$object.'_id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();
		}else{
			// Get visit from selected user ip
			$stmt = $this->pdo->prepare("SELECT id FROM edit_".$table."_visits WHERE ".$object."_id=:".$object."_id AND user_ip=:user_ip LIMIT 1");
			$stmt->bindParam(':'.$object.'_id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 32);
		}
		$stmt->execute();

		$obj['star'] = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if tutorial exist.
		if(!$obj['star']){
			if($this->data['user_logged']){
				// Insert visit.
				$stmt = $this->pdo->prepare("
					INSERT INTO edit_".$table."_visits (user_id, ".$object."_id, mktime)
					VALUES (:user_id, :".$object."_id, :mktime)
				");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':'.$object.'_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
			}else{
				// Insert visit.
				$stmt = $this->pdo->prepare("
					INSERT INTO edit_".$table."_visits (".$object."_id, user_ip, mktime)
					VALUES (:".$object."_id, :user_ip, :mktime)
				");
				$stmt->bindParam(':'.$object.'_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 32);
				$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
			}

			$stmt->execute();

			// Update tutorial.
			$stmt = $this->pdo->prepare("UPDATE edit_".$table." SET visits=visits+1 WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();

			$obj['status']='ok';
		}else{
			$obj['errors'][]='can not count visits';
			$obj['error']++;
		}

		// Return once
		return $once;
	}
	// Set unique counts
	function once_set_user_count($table,$count=1){
		// Get user count number
		$stmt = $this->pdo->prepare("SELECT id FROM edit_users_counts WHERE user_id=:user_id LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount()){
			// Update user count number
			$stmt = $this->pdo->prepare("UPDATE edit_users_counts SET ".$table."=".$table."+".$count." WHERE user_id=:user_id LIMIT 1");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();
		}else{
			// Insert user user count information.
			$stmt = $this->pdo->prepare("
				INSERT INTO edit_users_counts (user_id, ".$table.")
				VALUES (:user_id, :".$table.")
			");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':'.$table.'', $count, PDO::PARAM_INT);
			$stmt->execute();
		}
	}
	function once_get_user_downloads($table){
		$object=substr($table, 0, -1);
		//  Check if it has been already downloaded then download else check balance
		$stmt = $this->pdo->prepare("SELECT id FROM edit_".$table."_downloads WHERE user_id=:user_id AND ".$object."_id=:".$object."_id LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':'.$object.'_id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();
		if($stmt->rowCount()){
			return true;
		}else{
			return false;
		}
	}
	############################ ONCE TABLE CATEGORIES ##################################################
	// Get category data
	function category_get($table,$type=''){
		// Prepare varibles
		$this->categories=array();
		$this->index=array();
		$this->str='';

		// Check statements type
		if($type=='project_id'){
			$sql="WHERE project_id=".$this->data['project_id']."";
		}else if($type=='user_id'){
			$sql="WHERE user_id=".$this->data['user_id']."";
		}else if($type=='all' || $type=='both'){
			$sql="WHERE project_id=".$this->data['project_id']." AND user_id=".$this->data['user_id']."";
		}else{
			$sql='';
		}

		// Get selected data
		$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table."_categories ".$sql." ORDER by level, position");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();

		// Check if item exist
		$once['count']=$stmt->rowCount();
		if($once['count']){
			// fetch count as table
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$this->categories[$row['id']]=$row;
				$this->index[$row['parent_id']][]=$row['id'];
				if($row['parent_id']==0){
					$this->root_categories[]=$row;
				}
				$once['items'][]=$row;
			}
			$once['status']='ok';
		}else{
			$once['errors'][]=''.$table.' category - not exists';
			$once['error']++;
		}
		return $once;
	}
	// Get category data of first level
	function category_get_roots(){
		$once['items']=$this->root_categories;
		return $once;
	}
	// Create new category
	function category_new($table){
		// Get selected user
		$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE id=:id ORDER by id ASC");
		$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->execute();

		// Get count of returned records
		$user['count']=$stmt->rowCount();
		if($user['count']){
			$user['item']=$stmt->fetch(PDO::FETCH_ASSOC);

			// Check if user is creator or once
			if($user['item']['type_id']==1 || $user['item']['type_id']==2){

				// Insert new category
				$stmt2 = $this->pdo->prepare("SELECT MAX(position) AS ile FROM edit_".$table."_categories WHERE project_id=:project_id AND parent_id=:parent_id LIMIT 1");
				$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt2->bindParam(':parent_id', $this->data['id'], PDO::PARAM_INT);
				$stmt2->execute();

				// Check if item exist
				$once['count']=$stmt2->rowCount();
				if($once['count']){
					// Fetch result as table
					$wierszx=$stmt2->fetchAll(PDO::FETCH_ASSOC);
					$this->data['position']=$wierszx['ile']+1;
					$this->data['name']='New category';

					// Insert new record
					$stmt = $this->pdo->prepare("
						INSERT INTO edit_".$table."_categories (project_id, name, parent_id, position)
						VALUES (:project_id, :name, :parent_id, :position)
					");
					$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
					$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 255);
					$stmt->bindParam(':parent_id', $this->data['id'], PDO::PARAM_INT);
					$stmt->bindParam(':position', $this->data['position'], PDO::PARAM_INT);
					$stmt->execute();

					$once['count'] = $stmt->rowCount();

					// Return data & status if item created
					if($once['count']){
						// Get created data
						$once['item']=array(
							"id" => $this->pdo->lastInsertId(),
							"name" => $this->data['name'],
							"parent_id" => $this->data['id']
						);

						// Set status ok
						$once['status']='ok';
					}else{
						// Return error if item not created
						$once['errors'][]='can not insert item to: '.$table.' ';
						$once['error']++;
					}
				}else{
					$once['errors'][]=''.$table.' category - not exists';
					$once['error']++;
				}
			}else{
				$once['errors'][]='you don\'t have permission';
				$once['error']++;
			}
		}

		echo json_encode($once);
	}
	// Delete category by id
	function category_delete($table){
		// Prepare statements to get selected user
		$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE id=:id ORDER by id ASC");
		$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->execute();

		// Get count of returned records
		$user['count']=$stmt->rowCount();
		if($user['count']){
			$user['item']=$stmt->fetch(PDO::FETCH_ASSOC);

			// Check if user is creator or once
			if($user['item']['type_id']==1 || $user['item']['type_id']==2){
				// Get selected data
				$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table."_categories WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();

				$once['count'] = $stmt->rowCount();

				// Check if item exist
				if($once['count']){
					// Prepare statements to delete selected item
					$stmt = $this->pdo->prepare("DELETE FROM edit_".$table."_categories WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();

					$once['count'] = $stmt->rowCount();

					// Check if item exist
					if($once['count']){
						$once['status']='ok';
					}
				}else{
					$once['errors'][]=''.$table.' category - not exists';
					$once['error']++;
				}
			}else{
				$once['errors'][]='you don\'t have permission';
				$once['error']++;
			}
		}

		echo json_encode($once);
	}
	// Edit category by id
	function category_edit($table){
		// Prepare statements to get selected user
		$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE id=:id ORDER by id ASC");
		$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->execute();

		// Get count of returned records
		$user['count']=$stmt->rowCount();
		if($user['count']){
			$user['item']=$stmt->fetch(PDO::FETCH_ASSOC);

			// Check if user is creator or once
			if($user['item']['type_id']==1 || $user['item']['type_id']==2){
				// Get selected data
				$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table."_categories WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();

				$once['count'] = $stmt->rowCount();

				// Check if item exist
				if($once['count']){
					// Prepare statements to delete selected item
					$stmt = $this->pdo->prepare("UPDATE edit_".$table."_categories SET name=:name, ico=:ico WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 50);
					$stmt->bindParam(':ico', $this->data['ico'], PDO::PARAM_STR, 50);
					$stmt->execute();

					$once['count'] = $stmt->rowCount();

					// Check if item exist
					if($once['count']){
						$once['item']=array(
							"id" => $this->data['id'],
							"name" => $this->data['name']
						);
						$once['status']='ok';
					}
				}else{
					$once['errors'][]=''.$table.' category - not exists';
					$once['error']++;
				}
			}else{
				$once['errors'][]='you don\'t have permission';
				$once['error']++;
			}
		}

		echo json_encode($once);
	}
	// Sort category
	function category_sort($table){
		// Prepare statements to update selected category
		$stmt = $this->pdo->prepare("UPDATE edit_".$table."_categories SET position=:position WHERE id=:id");
		foreach ($this->data['category'] as $position => $item){
			$stmt->bindParam(':position', $position, PDO::PARAM_INT);
			$stmt->bindParam(':id', $item, PDO::PARAM_INT);
			$stmt->execute();
		}
		$once['status']='ok';
		echo json_encode($once);
	}
	// Display category ul tree
	function category_display_ul_simple_tree($parent_id, $level){
		if(isset($this->index[$parent_id])) {
			$this->str.='<ul class="nav nav-list">';
			foreach ($this->index[$parent_id] as $id) {
				$this->str.='<li id="category_'.$this->categories[$id]["id"].'" data-id="'.$this->categories[$id]["id"].'" data-parent_id="'.$this->categories[$id]["parent_id"].'" data-name="'.$this->categories[$id]["name"].'"><span>'.$this->categories[$id]["name"].'</span>';
				if(isset($this->index[$id])) {
					$this->category_display_ul_simple_tree($id, $level + 1);
				}
				$this->str.='</li>';
			}
			$this->str.='</ul>';
		}
		return $this->str;
	}
	// Display category ul once tree
	function category_display_ul_once_tree($parent_id, $level){
		if($level==0) $this->str='';
		if(isset($this->index[$parent_id])) {
			$this->str.='<ul class="nav nav-list sortable '.($level>0?'tree':'tree').'">';
			foreach ($this->index[$parent_id] as $id) {
				$this->str.='<li id="category_'.$this->categories[$id]["id"].'" data-id="'.$this->categories[$id]["id"].'" data-parent_id="'.$this->categories[$id]["parent_id"].'" data-name="'.$this->categories[$id]["name"].'"><a><b>'.$this->categories[$id]["name"].'</b><span><i class="fa fa-edit" name="edit category"></i><i class="fa fa-level-down" name="create relative category"></i><i class="fa fa-plus" name="create sub category"></i><i class="fa fa-minus" name="del category"></i></span></a>';
				if(isset($this->index[$id])) {
					$this->category_display_ul_once_tree($id, $level + 1);
				}
				$this->str.='</li>';
			}
			$this->str.='</ul>';
		}
		return $this->str;
	}
	// Display category ul once tree
	function category_display_select_tree($parent_id, $level){
		if(isset($this->index[$parent_id])) {
			foreach ($this->index[$parent_id] as $id) {
				if($this->categories[$id]["id"]!=$this->category['id']){
					$this->str.='<option value="'.$this->categories[$id]["id"].'"
					data-test="'.$this->categories[$id]["parent_id"].'|'.$this->categories[$id]["id"].' / '.$this->category['id'].' '.$this->category['parent_id'].'" '.($this->categories[$id]["id"]==$this->category['parent_id']?"selected":"").'>'.str_repeat("- - - ", ($level+1)).''.$this->categories[$id]["name"].'</option>';
					if(isset($this->index[$id])) {
						$this->category_display_select_tree($id, $level + 1);
					}
				}
			}
		}
		return $this->str;
	}
	############################ ONCE TABLE TYPES ##################################################
	// Get type data
	function type_get($table){
		// Prepare varibles
		$once['error']=0;

		// Get selected data
		$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table."_types ORDER by position");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();

		// Check if item exist
		$once['count']=$stmt->rowCount();
		if($once['count']){
			// fetch count as table
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$once['items'][]=$row;
			}
			$once['status']='ok';
		}else{
			$once['errors'][]=''.$table.' type - not exists';
			$once['error']++;
		}
		return $once;
	}
	// Create new type
	function type_new($table){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Get selected user
				$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE id=:id ORDER by id ASC");
				$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();

				// Get count of returned records
				$user['count']=$stmt->rowCount();
				if($user['count']){
					$user['item']=$stmt->fetch(PDO::FETCH_ASSOC);

					// Check if user is creator or once
					if($user['item']['type_id']==1 || $user['item']['type_id']==2){

						// Insert new type
						$stmt2 = $this->pdo->prepare("SELECT MAX(position) AS ile FROM edit_".$table."_types WHERE project_id=:project_id LIMIT 1");
						$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
						$stmt2->execute();

						// Check if item exist
						$once['count']=$stmt2->rowCount();
						if($once['count']){
							// Fetch result as table
							$wierszx=$stmt2->fetch(PDO::FETCH_ASSOC);
							$this->data['position']=intval($wierszx['ile'])+1;
							$this->data['name']='New type';

							// Insert new record
							$stmt = $this->pdo->prepare("
								INSERT INTO edit_".$table."_types (project_id, name, position)
								VALUES (:project_id, :name, :position)
							");
							$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
							$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 255);
							$stmt->bindParam(':position', $this->data['position'], PDO::PARAM_INT);
							$stmt->execute();

							$once['count'] = $stmt->rowCount();

							// Return data & status if item created
							if($once['count']){
								// Get created data
								$once['item']=array(
									"id" => $this->pdo->lastInsertId(),
									"name" => $this->data['name']
								);

								// Set status ok
								$once['status']='ok';
							}else{
								// Return error if item not created
								$once['errors'][]='can not insert item to: '.$table.' ';
								$once['error']++;
							}
						}else{
							$once['errors'][]=''.$table.' type - not exists';
							$once['error']++;
						}
					}else{
						$once['errors'][]='you don\'t have permission';
						$once['error']++;
					}
				}
			}
		}
		echo json_encode($once);
	}
	// Delete type by id
	function type_delete($table){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected user
				$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE id=:id ORDER by id ASC");
				$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();

				// Get count of returned records
				$user['count']=$stmt->rowCount();
				if($user['count']){
					$user['item']=$stmt->fetch(PDO::FETCH_ASSOC);

					// Check if user is creator or once
					if($user['item']['type_id']==1 || $user['item']['type_id']==2){
						// Get selected data
						$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table."_types WHERE id=:id LIMIT 1");
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();

						$once['count'] = $stmt->rowCount();

						// Check if item exist
						if($once['count']){
							// Prepare statements to delete selected item
							$stmt = $this->pdo->prepare("DELETE FROM edit_".$table."_types WHERE id=:id LIMIT 1");
							$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
							$stmt->execute();

							$once['count'] = $stmt->rowCount();

							// Check if item exist
							if($once['count']){
								$once['status']='ok';
							}
						}else{
							$once['errors'][]=''.$table.' type - not exists';
							$once['error']++;
						}
					}else{
						$once['errors'][]='you don\'t have permission';
						$once['error']++;
					}
				}
			}
		}
		echo json_encode($once);
	}
	// Edit type by id
	function type_edit($table){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected user
				$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE id=:id ORDER by id ASC");
				$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();

				// Get count of returned records
				$user['count']=$stmt->rowCount();
				if($user['count']){
					$user['item']=$stmt->fetch(PDO::FETCH_ASSOC);

					// Check if user is creator or once
					if($user['item']['type_id']==1 || $user['item']['type_id']==2){
						// Get selected data
						$stmt = $this->pdo->prepare("SELECT * FROM edit_".$table."_types WHERE id=:id LIMIT 1");
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();

						$once['count'] = $stmt->rowCount();

						// Check if item exist
						if($once['count']){
							// Prepare statements to delete selected item
							$stmt = $this->pdo->prepare("UPDATE edit_".$table."_types SET name=:name, ico=:ico, action=:action WHERE id=:id LIMIT 1");
							$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
							$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 50);
							$stmt->bindParam(':ico', $this->data['ico'], PDO::PARAM_STR, 50);
							$stmt->bindParam(':action', $this->data['action'], PDO::PARAM_STR, 50);
							$stmt->execute();

							$once['count'] = $stmt->rowCount();

							// Check if item exist
							if($once['count']){
								$once['item']=array(
									"id" => $this->data['id'],
									"name" => $this->data['name']
								);
								$once['status']='ok';
							}
						}else{
							$once['errors'][]=''.$table.' type - not exists';
							$once['error']++;
						}
					}else{
						$once['errors'][]='you don\'t have permission';
						$once['error']++;
					}
				}
			}
		}

		echo json_encode($once);
	}
	// Sort type
	function type_sort($table){
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to update selected type
				$stmt = $this->pdo->prepare("UPDATE edit_".$table."_types SET position=:position WHERE id=:id");
				foreach ($this->data['type'] as $position => $item){
					$stmt->bindParam(':position', $position, PDO::PARAM_INT);
					$stmt->bindParam(':id', $item, PDO::PARAM_INT);
					$stmt->execute();
				}
				$once['status']='ok';
			}
		}
		echo json_encode($once);
	}
	############################ HELPERS ##################################################
	// Setting route file link
	function set_route_link(){
		// Get selected user
		$stmt = $this->pdo->prepare("UPDATE edit_routes SET page_id=0 WHERE page_id=:page_id AND project_id=:project_id");
		$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();

		$stmt2 = $this->pdo->prepare("SELECT * FROM edit_routes WHERE id=:route_id AND project_id=:project_id");
		$stmt2->bindParam(':route_id', $this->data['route_id'], PDO::PARAM_INT);
		$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt2->execute();
		
		if($stmt2->rowCount()){
			$this->item=$stmt2->fetch(PDO::FETCH_ASSOC);

			$stmt = $this->pdo->prepare("UPDATE edit_routes SET page_id=:page_id WHERE id=:route_id AND project_id=:project_id");
			$stmt->bindParam(':page_id', $this->data['page_id'], PDO::PARAM_INT);
			$stmt->bindParam(':route_id', $this->data['route_id'], PDO::PARAM_INT);
			$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item=array(
				"id" => $this->data['page_id'],// wtf?
				"name" => $this->item['name'],
				"route_id" => $this->data['route_id']
			);

			$this->gen_seo();
		}else{
			$this->set_error('Route not exist');
		}

		// Refresh routes if exist
		$this->gen_switch();
		
		// Refresh routes if exist
		$this->gen_grids();
		
		return $obj;
	}
	// Get plugin list
	function once_plugin_list(){
		// Prepare statements to get all plugin list
		$stmt = $this->pdo->prepare("SELECT id, name FROM edit_plugins ORDER by name");
		$stmt->execute();

		$plugins=array();
		$plugins2=array();
		
		// Return result in table
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$plugins[]=$row;
			$plugins2[$row['id']]=$row;
		}

		$obj['items']=$plugins;
		$obj['plugins']=$plugins2;
		return $obj;
	}
	// Get all projects langs
	function once_lang_list(){
		// Prepare statements to get all plugin list
		$stmt = $this->pdo->prepare("SELECT *, edit_langs_types.id AS id FROM edit_langs_types
		LEFT JOIN edit_themes_langs ON edit_langs_types.id=edit_themes_langs.type_id WHERE project_id=".$this->data['project_id']."
		ORDER by edit_themes_langs.id ASC");
		$stmt->execute();

		if($stmt->rowCount()){
			// Return result in table
			foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $wiersz) {
				if(intval($wiersz['type_id'])>0){
					$row[]=$wiersz;
				}
			}
			$obj['items']=$row;
		}else{
			$obj['errors'][]='no project langs';
			$obj['error']++;
		}

		$obj['status']='ok';
		return $obj;
	}
	############################ AUTO GENERATION FILES ##################################################
	// Generate index.php
	function gen_index(){
		// Prepare statements to get index.php template
		$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/index.php');

		// If local_sessions is set => include session start and logout condition to template {session}
		$session="";
		$session.="session_start();\n";
		$session.="if(\$_GET['route']=='logout'){\n";
		$session.="	session_unset();\n";
		$session.="	session_destroy();\n";
		$session.="}";
		$tpl['source']=str_replace("{session}",$session,$tpl['source']);

		// Set body headers to template {headers}
		$headers ="require_once('./head.php');";
		$tpl['source'] = str_replace("{headers}",$headers,$tpl['source']);

		$routes="";
		$langs="";

		// check if any langs is used
		$stmt = $this->pdo->prepare("SELECT * FROM edit_themes_langs WHERE project_id=:project_id LIMIT 1");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount()){
			// If any routes varible is set => require_once to template {routes}
			$stmt2 = $this->pdo->prepare("SELECT id FROM edit_routes WHERE project_id=:project_id LIMIT 1");
			$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
			$stmt2->execute();

			if($stmt2->rowCount()){
				$routes.="require_once('./routes/routes.'.\$_SESSION['user_lang'].'.php');\n";
			}

			// If any langs varible is set => require_once to template {langs}
			$stmt2 = $this->pdo->prepare("SELECT id FROM edit_langs WHERE project_id=:project_id LIMIT 1");
			$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
			$stmt2->execute();

			if($stmt2->rowCount()){
				$langs.="require_once('./langs/langs.'.\$_SESSION['user_lang'].'.php');\n";
			}
		}
		$tpl['source']=str_replace("{routes}",substr($routes, 0, -1),$tpl['source']);
		$tpl['source']=str_replace("{langs}",substr($langs, 0, -1),$tpl['source']);

		// If any seo varible or langs or database is set => require_once to template {seo}
		$seo="";
		$seo.="require_once('./langs/seo.php');\n";//.'.\$_SESSION['user_lang'].'
		$tpl['source']=str_replace("{seo}",substr($seo, 0, -1),$tpl['source']);
		
		// If any config varible or langs or database is set => require_once to template {config}
		$config="";
		$config.="require_once('./once/config.php');\n";
		$tpl['source']=str_replace("{config}",substr($config, 0, -1),$tpl['source']);

		// Set require_once to template {classes}
		$classes="";
		$classes.="require_once('./once/class/core.class.php');\n";
		$classes.="\$once=new core(\$_CONFIG);\n";
		$tpl['source']=str_replace("{classes}",substr($classes, 0, -1),$tpl['source']);

		// Set body headers to template {headers}
		$grids="";

		// If any routes varible is set => require_once to template {routes}
		$stmt = $this->pdo->prepare("SELECT id FROM edit_layers WHERE project_id=:project_id AND `default`=1 LIMIT 1");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount()){
			// If any routes varible is set => require_once to template {routes}
			$stmt2 = $this->pdo->prepare("SELECT id FROM edit_layers_cols WHERE project_id=:project_id LIMIT 1");
			$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
			$stmt2->execute();

			if($stmt2->rowCount()){
				$grids.="require_once('./routes/grids.'.\$_SESSION['user_lang'].'.php');";
			}
		}else{
			$grids.="require_once('./once/default/template.php');";
		}
		$tpl['source'] = str_replace("{grids}",$grids,$tpl['source']);

		// get all dependencies
		// If any routes varible is set => require_once to template {routes}
		$stmt3 = $this->pdo->prepare("SELECT plugin_id FROM edit_layers_cols WHERE project_id=:project_id AND plugin_id!=0 GROUP by plugin_id");
		$stmt3->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt3->execute();
		
		foreach ($stmt3->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$plugins[$row['plugin_id']]=true;
		}
		
		$stmt3 = $this->pdo->prepare("SELECT plugin_id FROM edit_pages_cols WHERE project_id=:project_id AND plugin_id!=0 GROUP by plugin_id");
		$stmt3->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt3->execute();
		
		foreach ($stmt3->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$plugins[$row['plugin_id']]=true;
		}	

		$dependencies='';
		if(isset($plugins)){
			foreach($plugins as $key => $val){
				if(file_exists($this->data['root_path'].'/once/plugins/'.$key.'/dependencies.html')){
					$dependencies.='
		'.file_get_contents($this->data['root_path'].'/once/plugins/'.$key.'/dependencies.html').'
		';
				}
			}
		}

		$tpl['source']=str_replace("{dependencies}",substr($dependencies, 0, -1),$tpl['source']);
		
		file_put_contents('../index.php',$tpl['source']);
	}
	// Generate routes/grids{lang}.php
	function gen_grids(){
		// Generate lang switch file
		$stmt = $this->pdo->prepare("
			SELECT name FROM edit_themes_langs
			JOIN edit_langs_types ON edit_langs_types.id=edit_themes_langs.type_id
			WHERE project_id=:project_id
		");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount()){
			// Make dirs and generate files
			@mkdir("../langs/");
			chmod("../langs/", 0777);

			@mkdir("../routes/");
			chmod("../routes/", 0777);

			// Get default layer
			$stmt2 = $this->pdo->prepare("SELECT id FROM `edit_layers` WHERE project_id=:project_id AND `default`=1");
			$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
			$stmt2->execute();

			if($stmt2->rowCount()){
				$wierszx=$stmt2->fetch(PDO::FETCH_ASSOC);
			}else{
				// If not exist try another
				$stmt3 = $this->pdo->prepare("SELECT id FROM `edit_layers` WHERE project_id=:project_id LIMIT 1");
				$stmt3->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt3->execute();

				if($stmt2->rowCount()){
					$wierszx=$stmt2->fetch(PDO::FETCH_ASSOC);
				}
			}
			
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wierszy) {
				unset($pages);

				// Get pages list
				$stmt4 = $this->pdo->prepare("
					SELECT layer_id, page_id, source_".$wierszy['name']." AS source FROM `edit_routes` 
					INNER JOIN edit_pages ON edit_pages.id = edit_routes.page_id
					WHERE edit_pages.project_id=:project_id AND layer_id>0 AND page_id>0
				");
				$stmt4->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt4->execute();
				
				if($stmt4->rowCount()){
					foreach ($stmt4->fetchAll(PDO::FETCH_ASSOC) as $wiersz) {
						$pages[]=$wiersz;
						$grids[$wiersz['layer_id']][]=$wiersz;
					}
				}

				$strx='';
				
				if(count($grids)>0){
					foreach($grids as $key => $val){
						$strx.="}else if(";
						
						unset($arra);
						foreach($pages as $key2 => $val2){
							if($pages[$key2]['layer_id']==$key){
								$arra[]="\$_GET['route']=='".$pages[$key2]['source']."'";
							}
						}
						
						$strx .=implode(" || ",$arra);
						
						$strx.="){\n";
						$strx .="	include_once './grids/grid_".$key.".php';\n";	
					}
				}
				
				$str="<?php\n";
				$str.="# SECURE -----------------\n";
				$str.="if(!\$home) exit;\n\n";
				$str.="# GRID SWICHER -----------------\n";
				$str.="if(!isset(\$_GET['route'])){\n";
				$str.="	include_once './grids/grid_".$wierszx['id'].".php';\n";
				$str.="";

				if($strx!=''){
					$str.=$strx;
				}
				
				$str.="}else{\n";
				$str.="	include_once './grids/grid_".$wierszx['id'].".php';\n";
				$str.="}\n";
				$str.="?>";

				file_put_contents('../routes/grids.'.$wierszy['name'].'.php',$this->filter_string($str,true));
			}
		}
	}
	// Generate grids/grid{id}.php
	function gen_grid($id=0){
		if($id==0){
			$id=$this->data['layer_id'];
		}

		$layersGrids = array();
		$layersRows = array();
		$max=0;

		// Set body layers to template {layers}
		$layers="";
		$stmt = $this->pdo->prepare("SELECT * FROM `edit_layers_cols` WHERE project_id=:project_id AND layer_id=:layer_id AND hidden=0 ORDER by row_id, col_id");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->bindParam(':layer_id', $id, PDO::PARAM_INT);
		$stmt->execute();
		if($stmt->rowCount()){
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wiersz) {
				$layersGrids[$wiersz['row_id']][$wiersz['col_id']]=$wiersz;
				if($wiersz['row_id']>$max){
					$max=$wiersz['row_id'];
				}
			}
		}
		$obj['layers']=$layersGrids;
		$obj['max']=$max;

		$stmt = $this->pdo->prepare("SELECT * FROM `edit_layers_rows` WHERE layer_id=:layer_id");
		$stmt->bindParam(':layer_id', $id, PDO::PARAM_INT);
		$stmt->execute();
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wiersz) {
			$layersRows[$wiersz['row_id']]=$wiersz;
		}

		$layers.='<?php';
		$switch=false;
		if(count($layersGrids)>0){
			foreach($layersGrids as $key => $val){
				$layers.='
		# PAGE layer -------------------
		echo \'';
			if(isset($layersRows[$key])){
				if($layersRows[$key]['container']==0){
					$layers.='
		<div'.($layersRows[$key]['css_id']!=''?' id="'.$layersRows[$key]['css_id'].'"':'').' class="container-fluid'.($layersRows[$key]['css_class']!=''?' '.$layersRows[$key]['css_class']:'').'">';
				}
				if($layersRows[$key]['container']==1){
					$layers.='
		<div'.($layersRows[$key]['css_id']!=''?' id="'.$layersRows[$key]['css_id'].'"':'').' class="container'.($layersRows[$key]['css_class']!=''?' '.$layersRows[$key]['css_class']:'').'">';
				}
			}else{
				$layers.='
		<div'.($layersRows[$key]['css_id']!=''?' id="'.$layersRows[$key]['css_id'].'"':'').' class="container-fluid'.($layersRows[$key]['css_class']!=''?' '.$layersRows[$key]['css_class']:'').'">';
			}
			
				$layers.='
			<div class="row">';
					foreach($layersGrids[$key] as $key2 => $val2){//id="'.$layersGrids[$key][$key2]['row_id'].'x'.$layersGrids[$key][$key2]['col_id'].'"
						$layers.='
				<div'.($layersGrids[$key][$key2]['css_id']!=''?' id="'.$layersGrids[$key][$key2]['css_id'].'"':'').' class="col-md-'.$layersGrids[$key][$key2]['size'].''.($layersGrids[$key][$key2]['css_class']!=''?' '.$layersGrids[$key][$key2]['css_class']:'').'">\';';
					if($layersGrids[$key][$key2]['plugin_id']==-1){
						$layers.='
					require_once(\'./routes/switches.\'.$_SESSION[\'user_lang\'].\'.php\');';
						$switch=true;
					}else{
						$layers.='
					require_once(\'./layers/layer_'.$layersGrids[$key][$key2]['id'].'.php\');';
					}
				$layers.='
				echo \'</div>';
					}
				$layers.='
			</div>';
			if(isset($layersRows[$key])){
				if($layersRows[$key]['container']==0 || $layersRows[$key]['container']==1){
				$layers.='
		</div>';
				}
			}else{
				$layers.='
		</div>';
			}
				$layers.='\';';
			}
		}
		$layers.='
?>';

		// If there is layers with switch => gen_switch
		if($switch==true){
			$this->gen_switch();
		}


		// Make dirs and generate files
		@mkdir("../grids");
		chmod("../grids", 0777);
		file_put_contents('../grids/grid_'.$id.'.php',$layers);
	}
	// Generate switches.php
	function gen_switch(){
		$stmt = $this->pdo->prepare("SELECT * FROM edit_layers_cols WHERE project_id=:project_id AND plugin_id=-1 LIMIT 1");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();
		if($stmt->rowCount()){
			// get default layer
			$stmt = $this->pdo->prepare("SELECT id FROM `edit_pages` WHERE project_id=:project_id AND `default`=1");
			$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
			$stmt->execute();

			$wierszx = $stmt->fetch(PDO::FETCH_ASSOC);

			$stmt2 = $this->pdo->prepare("SELECT name FROM edit_themes_langs JOIN edit_langs_types ON edit_langs_types.id=edit_themes_langs.type_id WHERE project_id=:project_id");
			$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
			$stmt2->execute();

			if($stmt2->rowCount()){
				foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $wierszy) {
					$str="<?php\n";
					$str.="# SECURE -----------------\n";
					$str.="if(!\$home) exit;\n\n";
					$str.="# PAGE SWICHER -----------------\n";
					$str.="if(!isset(\$_GET['route'])){\n";
					if($wierszx['id']){
						$str.="	include_once './pages/page_".$wierszx['id'].".php';\n";
					}
					$str.="}";

					// get routes
					$stmt3 = $this->pdo->prepare("
						SELECT edit_pages.id AS id, edit_pages.name AS name, source_".$wierszy['name']." FROM edit_routes
						LEFT JOIN edit_pages ON edit_routes.page_id=edit_pages.id
						WHERE edit_pages.project_id=:project_id
					");
					$stmt3->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
					$stmt3->execute();

					if($stmt3->rowCount()){
						foreach ($stmt3->fetchAll(PDO::FETCH_ASSOC) as $wierszz) {
							$str.="else if(\$_GET['route']=='".$wierszz['source_'.$wierszy['name']]."'){\n";
							$str.="	include_once './pages/page_".$wierszz['id'].".php';\n";
							$str.="}";
						}
					}

					$str.="else{\n";
					if($wierszx['id']){
						$str.="	include_once './pages/page_".$wierszx['id'].".php';\n";
					}
					$str.="}\n";
					$str.="?>";

					file_put_contents('../routes/switches.'.$wierszy['name'].'.php',$this->filter_string($str,true));
				}
			}
		}
	}
	// Generate whole page
	function gen_page($id){
		// We set up page content
		$layers="";

		// Set body layers to template {layers}
		$layers="";
		$stmt = $this->pdo->prepare("SELECT * FROM `edit_pages_cols` WHERE project_id=:project_id AND page_id=:page_id ORDER by row_id, col_id");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->bindParam(':page_id', $id, PDO::PARAM_INT);
		$stmt->execute();
		if($stmt->rowCount()){
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wiersz) {
				$layersGrids[$wiersz['row_id']][$wiersz['col_id']]=$wiersz;
				if($wiersz['row_id']>$max){
					$max=$wiersz['row_id'];
				}
			}
		}
		$obj['layers']=$layersGrids;
		$obj['max']=$max;

		// Collect all rows
		$stmt = $this->pdo->prepare("SELECT * FROM `edit_pages_rows` WHERE page_id=:page_id");
		$stmt->bindParam(':page_id', $id, PDO::PARAM_INT);
		$stmt->execute();
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wiersz) {
			$layersRows[$wiersz['row_id']]=$wiersz;
		}

		// Get page content
		$layers.='<!-- # PAGE '.$id.' -->';
		if(count($layersGrids)>0){
			foreach($layersGrids as $key => $val){
				$layers.='
<div'.($layersRows[$key]['css_id']!=''?' id="'.$layersRows[$key]['css_id'].'"':'').' class="row'.($layersRows[$key]['css_class']!=''?' '.$layersRows[$key]['css_class']:'').'">';
					foreach($layersGrids[$key] as $key2 => $val2){
						$layers.='
	<div'.($layersGrids[$key][$key2]['css_id']!=''?' id="'.$layersGrids[$key][$key2]['css_id'].'"':'').' class="col-md-'.$layersGrids[$key][$key2]['size'].''.($layersGrids[$key][$key2]['css_class']!=''?' '.$layersGrids[$key][$key2]['css_class']:'').'">
		'.@file_get_contents('../pages/page_'.$id.'_'.$layersGrids[$key][$key2]['id'].'.php').'
	</div>';
					}
				$layers.='
</div>
';
			}
		}

		// Output goes to file
		file_put_contents('../pages/page_'.$id.'.php',$layers);
	}
	// Generate config.php from records
	function gen_config($config=false){
		// Get config.php template
		$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/config.php');
	
		if(!is_array($config)){
			// Read funny config.php
			$home=true;
			if(file_exists('../oconfig.php')) require('../oconfig.php');
		}else{
			$_CONFIG=$config;
		}
		
		
		// Get config.php template
		$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/config.php');
			
		// Get and set config from to template {config}
		$config="";
		if(isset($_CONFIG)){
			foreach($_CONFIG as $k => $v){
				$config.="\$_CONFIG['".$k."']='".$v."';\n";
			}
		}
		$tpl['source']=str_replace("{config}",$config,$tpl['source']);
				
		//Set project langs settings to template {langs}
		$stmt = $this->pdo->prepare("
			SELECT name, `desc`
			FROM edit_themes_langs JOIN edit_langs_types ON edit_langs_types.id=edit_themes_langs.type_id
			WHERE project_id=:project_id
		");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();
				
		$langs="";
		if($stmt->rowCount()){
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wiersz) {
				$langs.="\$_LANGS['".$wiersz['name']."']='".$wiersz['desc']."';\n";
			}
		}
		$tpl['source'] = str_replace("{langs}",$langs,$tpl['source']);
		
		file_put_contents('../oconfig.php',$tpl['source']);
		$obj['status']='ok';
		
		return $obj;
	}
	// Generate langs.{lang}.php file
	function gen_langs(){
		// Get project languages
		$stmt = $this->pdo->prepare("
			SELECT name, `desc`
			FROM edit_themes_langs JOIN edit_langs_types ON edit_langs_types.id=edit_themes_langs.type_id
			WHERE project_id=:project_id
		");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount()){
			// Make sure that dirs exists
			@mkdir("../langs/");
			chmod("../langs/", 0777);

			// Generate langs files
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wierszy) {
				$str="<?php\n";
				$str.="# SECURE -----------------\n";
				$str.="if(!\$home) exit;\n\n";
				$str.="# USED LANGS -----------------\n";

				// Get langs varibiles
				$stmt2 = $this->pdo->prepare("SELECT name, name_id, source_".$wierszy['name']." FROM edit_langs WHERE project_id=:project_id");
				$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt2->execute();

				if($stmt2->rowCount()){
					foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wierszz) {
						$str.="\$_LANG['".$wierszz['name']."".$wierszz['name_id']."']='".$wierszz['source_'.$wierszy['name']]."';\n";
					}
				}

				$str.="?>";
				file_put_contents('../langs/langs.'.$wierszy['name'].'.php',$str);
			}
		}else{
			//$this->rem_dir($this->data['project_path']."/langs/");
			//$this->rem_dir($this->data['project_path']."/routes/");
		}
	}
	// Generate routes.{route}.php file
	function gen_routes(){
		// Get project languages
		$stmt = $this->pdo->prepare("
			SELECT name, `desc`
			FROM edit_themes_langs JOIN edit_langs_types ON edit_langs_types.id=edit_themes_langs.type_id
			WHERE project_id=:project_id
		");
		$stmt->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
		$stmt->execute();

		if($stmt->rowCount()){
			// Make sure that dirs exists
			@mkdir("../routes/");
			chmod("../routes/", 0777);

			// Refresh switch
			$this->gen_switch();

			// Generate routes files
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wierszy) {
				$str="<?php\n";
				$str.="# SECURE -----------------\n";
				$str.="if(!\$home) exit;\n\n";
				$str.="# USED ROUTES -----------------\n";

				// Get routes varibiles
				$stmt2 = $this->pdo->prepare("SELECT name, name_id, source_".$wierszy['name']." FROM edit_routes WHERE project_id=:project_id");
				$stmt2->bindParam(':project_id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt2->execute();

				if($stmt2->rowCount()){
					foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $wierszz) {
						$str.="\$_ROUTE['".$wierszz['name']."".$wierszz['name_id']."']='".$wierszz['source_'.$wierszy['name']]."';\n";
					}
				}

				$str.="?>";
				file_put_contents('../routes/routes.'.$wierszy['name'].'.php',$str);
			}
		}else{
			//$this->rem_dir($this->data['project_path']."/langs/");
			//$this->rem_dir($this->data['project_path']."/routes/");
		}
	}
	// Generate routes.{route}.php file
	function gen_seo(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id, `source_".$this->data['user_lang']."` AS source FROM edit_routes WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['route_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		// Check if page grid exists
		if($stmt->rowCount()){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			// Make prefix to reconize pages
			$this->data['type']='seo';
			$this->data['path']='langs';
			$this->data['file']='seo.php';
			$this->data['id']=$row['id'];

			$str="\$_SEO['".$row['source']."']['title']='".$this->data['title']."';\n";
			$str.="\$_SEO['".$row['source']."']['keywords']='".$this->data['keywords']."';\n";
			$str.="\$_SEO['".$row['source']."']['description']='".$this->data['description']."';";
			
			$this->data['source']=$str;
			
			$this->once_save_source_php();
		}
	}	
	############################ ONCE SOURCE BLOCK ##################################################
	// Generate style.css
	function once_read_source(){
		$source=@file_get_contents('../'.$this->data['path'].'/'.$this->data['file']);
		if(!$source){
			$source='';
		}

		// Set up search selectors
		$layer_start="/* <once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line below! */";
		$layer_start_pos = strpos($source, $layer_start)+2;

		$layer_end="/* </once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line above! */";
		$layer_end_pos = strpos($source, $layer_end)+2;

		$source=substr($source, $layer_start_pos+strlen($layer_start), (strlen($source)-$layer_end_pos+4)*(-1));
		if(!$source){
			$source='';
		}

		return $source;
	}
	// Save file as once source
	function once_save_source(){
		$source=@file_get_contents('../'.$this->data['path'].'/'.$this->data['file']);
		if(!$source){
			$source='';
		}

		// Set up search selectors
		$layer_start="/* <once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line below! */";
		$layer_start_pos = strpos($source, $layer_start);
		$style_start=substr($source, 0, $layer_start_pos+strlen($layer_start));

		$layer_end="/* </once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line above! */";
		$layer_end_pos = strpos($source, $layer_end);
		$style_end=substr($source, $layer_end_pos,strlen($source));

		// Append new once block at the end if not exist
		if(!$layer_end_pos){
			$source=$source."\n\n".$layer_start."\n\n".$this->data['source']."\n\n".$layer_end;
		}else{
			$source=$style_start."\n\n".$this->data['source']."\n\n".$style_end;
		}

		return file_put_contents('../'.$this->data['path'].'/'.$this->data['file'],$this->filter_string($source,true));
	}
	// Save file as once php source
	function once_save_source_php(){
		$source=@file_get_contents('../'.$this->data['path'].'/'.$this->data['file']);
		if(!$source){
			$source='<?php ?>';
		}

		// Set up search selectors
		$layer_start="/* <once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line below! */";
		$layer_start_pos = strpos($source, $layer_start);
		$style_start=substr($source, 0, $layer_start_pos+strlen($layer_start));

		$layer_end="/* </once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line above! */";
		$layer_end_pos = strpos($source, $layer_end);
		$style_end=substr($source, $layer_end_pos,strlen($source));

		// Append new once block at the end if not exist
		if(!$layer_end_pos){
			$source="".substr($source,0,-2)."\n".$layer_start."\n\n".$this->data['source']."\n\n".$layer_end."\n?>";
		}else{
			$source=$style_start."\n\n".$this->data['source']."\n\n".$style_end;
		}

		return file_put_contents('../'.$this->data['path'].'/'.$this->data['file'],$this->filter_string($source,true));
	}
	// Save file as once source
	function once_comment_source(){
		$source=@file_get_contents('../'.$this->data['path'].'/'.$this->data['file']);
		if(!$source){
			$source='';
		}

		// Set up search selectors
		$tag_start="/* <once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line below! */";
		$tag_start_pos = strpos($source, $tag_start);

		$tag_end="/* </once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line above! */";
		$tag_end_pos = strpos($source, $tag_end);

		if($tag_start_pos>0){
			$str_start=substr($source, 0, $tag_start_pos);
			$str_source=substr($source, $tag_start_pos+strlen($tag_start), (strlen($source)-$tag_end_pos)*(-1));
			$str_end=substr($source, $tag_end_pos+strlen($tag_end), strlen($source)-$tag_end_pos+strlen($tag_end));


			// if next chat is not \n then set \n
			if(strpos($str_source[0], "\n") === FALSE) {
				$str_source="\n/*".$str_source;
				if(strpos($str_source[strlen($str_source)-1], "\n") === FALSE) {
					$str_source=$str_source."\n*/\n";
				}else{
					$str_source=$str_source."*/\n";
				}
			}else if(strpos($str_source[strlen($str_source)-1], "\n") === FALSE) {
				$str_source="/*".$str_source."\n*/\n";
			}else{
				$str_source="\n/*".$str_source."*/\n";
			}

			$str_source=$str_start."".$tag_start."".$str_source."".$tag_end."".$str_end."";

			return file_put_contents('../'.$this->data['path'].'/'.$this->data['file'],$this->filter_string($str_source,true));
		}
	}
	// Save file as once source
	function once_uncomment_source(){
		$source=@file_get_contents('../'.$this->data['path'].'/'.$this->data['file']);
		if(!$source){
			$source='';
		}

		// Set up search selectors
		$tag_start="/* <once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line below! */";
		$tag_start_pos = strpos($source, $tag_start);

		$tag_end="/* </once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line above! */";
		$tag_end_pos = strpos($source, $tag_end);

		if($tag_start_pos>0){
			$str_start=substr($source, 0, $tag_start_pos);
			$str_source=substr($source, $tag_start_pos+strlen($tag_start), (strlen($source)-$tag_end_pos)*(-1));
			$str_end=substr($source, $tag_end_pos+strlen($tag_end), strlen($source)-$tag_end_pos+strlen($tag_end));

			$str_source=substr($str_source, 3);
			$str_source=substr($str_source,0,-3);

			$str_source=$str_start."".$tag_start."\n".$str_source."\n".$tag_end."".$str_end."";

			return file_put_contents('../'.$this->data['path'].'/'.$this->data['file'],$this->filter_string($str_source,true));
		}
	}
	// Save file as once source
	function once_delete_source(){
		$source=@file_get_contents('../'.$this->data['path'].'/'.$this->data['file']);
		if(!$source){
			$source='';
		}

		// Set up search selectors
		$tag_start="/* <once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line below! */";
		$tag_start_pos = strpos($source, $tag_start);

		$tag_end="/* </once_".$this->data['type']."_".$this->data['id']."> Do not remove / change this line and line above! */";
		$tag_end_pos = strpos($source, $tag_end);

		if($tag_start_pos>0){
			$str_start=substr($source, 0, $tag_start_pos);
			$str_source=substr($source, $tag_start_pos+strlen($tag_start), (strlen($source)-$tag_end_pos)*(-1));
			$str_end=substr($source, $tag_end_pos+strlen($tag_end), strlen($source)-$tag_end_pos+strlen($tag_end));

			$str_source=$str_start."".$str_end;

			return file_put_contents('../'.$this->data['path'].'/'.$this->data['file'],$this->filter_string($str_source,true));
		}
	}
	############################ ONCE IMAGE HELPERS ##################################################
	// Resize image with given with/height
	function once_image_resample($file){
		$fileinfo=getimagesize($file);

		// Getting width/height
		$width_orig=$fileinfo[0];
		$height_orig=$fileinfo[1];

		// Resample
		$resized_image = imagecreatetruecolor($width_orig, $height_orig);

		switch ($fileinfo[2]){
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif($file);
			break;
			case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($file);
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng($file);
			break;
			default:
			return false;
		}

		imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $width_orig, $height_orig, $width_orig, $height_orig);
		imagejpeg($resized_image, $file, 100);

		imagedestroy($image);
		imagedestroy($resized_image);
	}
	// Resize image with given with/height
	function once_image_resize($file,$width_new=320,$height_new=240){
		$fileinfo=getimagesize($file);

		// Getting width/height
		$width_orig=$fileinfo[0];
		$height_orig=$fileinfo[1];

		// Resample
		$resized_image = imagecreatetruecolor($width_new, $height_new);

		switch ($fileinfo[2]){
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif($file);
			break;
			case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($file);
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng($file);
			break;
			default:
			return false;
		}

		imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $width_new, $height_new, $width_orig, $height_orig);
		imagejpeg($resized_image, $file, 100);

		imagedestroy($image);
		imagedestroy($resized_image);
	}
	// Use google insights to get thumb 320x240
	function once_insights_image($url,$image){
		// Use google to get screenshot
		$response=$this->get_page("https://www.googleapis.com/pagespeedonline/v1/runPagespeed?url=".$url."/&key=".$this->data['google_insight_api_key']."&screenshot=true");
		$objx=json_decode($response, true);

		$data=str_replace('_','/',$objx['screenshot']['data']);
		$data=str_replace('-','+',$data);
		$decoded=base64_decode($data);

		file_put_contents($image,$decoded);
	}
	############################ ONCE URL CONTENT HELPERS ##################################################
	// Getting page with selected Url
	function get_page($url){
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0';
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers[] = 'Accept-Language: pl,en-us;q=0.7,en;q=0.3';
        $headers[] = 'Connection: keep-alive';
        $headers[] = 'DNT: 1';

       	$response=$this->get_page_response(array(
			"url" => $url,
			"headers" => $headers,
			"cookie" => '',
			"proxy" => '',
			"postfields" => '',
			"file" => ''
		));
		return $response;
    }
	// cUrl instance to get page response
	function get_page_response($config){
		$parts=parse_url($config['url']);
		if(!$parts) {
			return '/* the URL was seriously wrong */';
		}
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $config['url']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $config['headers']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		//curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		//curl_setopt($ch, CURLOPT_CONNECT_ONLY, true);

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 9);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname( __FILE__ ) . '/cookie.txt' );
		curl_setopt($ch, CURLOPT_COOKIEJAR,  dirname( __FILE__ ) . '/cookie.txt' );
		if($config['cookie']!=''){
			curl_setopt($ch, CURLOPT_COOKIE, $config['cookie']);
		}
		if($config['proxy']!=''){
			curl_setopt($ch, CURLOPT_PROXY, $config['proxy']);
			//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true)
		}
		if($config['postfields']!=''){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $config['postfields']);
		}
		if($parts['scheme']=='https'){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		if($config['file']!=false){
			$config['file']=fopen($config['file'],'wb');
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_FILE, $config['file']);
		}else{
			curl_setopt($ch, CURLOPT_HEADER, false);
		}
		$response=curl_exec($ch);

		return $response;

		$error=curl_error($ch);
		curl_close($ch);
		if($config['file']!=false){
			return $response;
			fclose($config['file']);
		}
		file_put_contents("tmp/".md5($config['url'])."".md5($response).".html", $response);
		if(preg_match_all('/HTTP\/1\.\d+\s+(\d+)/', $response, $matches)){
			$code=intval($matches[1][(count($matches[1])-1)]);
		}else{
			if(preg_match('operation timed out', $error)){
				return '/*error - time out*/';
			}else{
				return '/*error - not found*/';
			}
		};
		if(($code>=200) && ($code<400)){
			return $response;//success
		}else{
			return '/*code - not found*/';
		}
	}
	// Alternate to file_get_contents: http://stackoverflow.com/questions/3979802/alternative-to-file-get-contents
	function url_get_contents ($Url) {
		if (!function_exists('curl_init')){
			die('CURL is not installed!');
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $Url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	function download($url) {
		set_time_limit(0);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$r = curl_exec($ch);
		curl_close($ch);
		header('Expires: 0'); // no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
		header('Cache-Control: private', false);
		header('Content-Type: application/force-download');
		header('Content-Disposition: attachment; filename="' . basename($url) . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . strlen($r)); // provide file size
		header('Connection: close');
		echo $r;
	}
	# SOME FUNCTIONS -------------------
	function filter_string($pole,$un=false){
		$pole=trim($pole); // uwam zbędne spacje
		if (get_magic_quotes_gpc())
		//$nazwa = htmlspecialchars($nazwa, ENT_QUOTES);
		$pole = stripslashes($pole); // usuwam ukośniki'
		if($un==false){
			$pole = str_replace(
				array("&"    , '"'     , "<"   , ">"   , "\0", "\\"  , "'"),   // z
				array("&amp;", "&quot;", "&lt;", "&gt;", ""  , "\\\\", "\'" ), // na
				$pole
			);
		}else{
			$pole = str_replace(
				array("&amp;", "&quot;", "&lt;", "&gt;", ""  , "\\\\", "\'" ),   // z
				array("&"    , '"'     , "<"   , ">"   , "\0", "\\"  , "'"), // na
				$pole
			);
		}
		return $pole;
	}
	function filter_html($pole){ return $pole;
		/*$pole=trim($pole); // uwam zbedne spacje
		if (get_magic_quotes_gpc())
		//$nazwa = htmlspecialchars($nazwa, ENT_QUOTES);
		$pole = stripslashes($pole); // usuwam ukosniki'
		if($un==false){
			$pole = str_replace(
				array("&"    , '"'     , "<"   , ">"   , "'"      ),   // z
				array("&amp;", "&quot;", "&lt;", "&gt;", "&#039;" ), // na
				$pole
			);
		}else{
			$pole = str_replace(
				array("&amp;", "&quot;", "&lt;", "&gt;" , "&#039;" ),   // z
				array("&"    , '"'     , "<"   , ">"    , "'"      ), // na
				$pole
			);
		}
		return $pole;
		*/
	}
	/**
	 * Create a web friendly URL slug from a string.
	 * 
	 * Although supported, transliteration is discouraged because
	 *     1) most web browsers support UTF-8 characters in URLs
	 *     2) transliteration causes a loss of information
	 *
	 * @author Sean Murphy <sean@iamseanmurphy.com>
	 * @copyright Copyright 2012 Sean Murphy. All rights reserved.
	 * @license http://creativecommons.org/publicdomain/zero/1.0/
	 *
	 * @param string $str
	 * @param array $options
	 * @return string
	 */
	function url_slug($str, $options = array()) {
		// Make sure string is in UTF-8 and strip invalid UTF-8 characters
		$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
		
		$defaults = array(
			'delimiter' => '-',
			'limit' => null,
			'lowercase' => true,
			'replacements' => array(),
			'transliterate' => false,
		);
		
		// Merge options
		$options = array_merge($defaults, $options);
		
		$char_map = array(
			// Latin
			'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
			'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
			'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
			'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
			'ß' => 'ss', 
			'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
			'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
			'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
			'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
			'ÿ' => 'y',

			// Latin symbols
			'©' => '(c)',

			// Greek
			'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
			'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
			'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
			'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
			'Ϋ' => 'Y',
			'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
			'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
			'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
			'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
			'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

			// Turkish
			'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
			'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 

			// Russian
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
			'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
			'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
			'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
			'Я' => 'Ya',
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
			'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
			'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
			'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
			'я' => 'ya',

			// Ukrainian
			'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
			'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

			// Czech
			'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
			'Ž' => 'Z', 
			'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
			'ž' => 'z', 

			// Polish
			'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
			'Ż' => 'Z', 
			'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
			'ż' => 'z',

			// Latvian
			'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
			'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
			'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
			'š' => 's', 'ū' => 'u', 'ž' => 'z'
		);
		
		// Make custom replacements
		$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
		
		// Transliterate characters to ASCII
		if ($options['transliterate']) {
			$str = str_replace(array_keys($char_map), $char_map, $str);
		}
		
		// Replace non-alphanumeric characters with our delimiter
		$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
		
		// Remove duplicate delimiters
		$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
		
		// Truncate slug to max. characters
		$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
		
		// Remove delimiter from ends
		$str = trim($str, $options['delimiter']);
		
		return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
	}
	
	
}
?>
