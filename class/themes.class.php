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
	function api_search(){
		// Check if user exist by api_key
		$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE api_key=:api_key LIMIT 1");
		$stmt->bindParam(':api_key', $this->data['api'], PDO::PARAM_STR, 255);
		$stmt->execute();

		if($stmt->rowCount()){
			$row=$stmt->fetch(PDO::FETCH_ASSOC);
				
			# XAMPP fix without turning error info off -------------------
			$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
			$_GET['ids'] = isset($_GET['ids']) ? $_GET['ids'] : '';
			$_GET['idsx'] = isset($_GET['idsx']) ? $_GET['idsx'] : '';
			$_GET['idsxs'] = isset($_GET['idsxs']) ? $_GET['idsxs'] : '';
			$_GET['option'] = isset($_GET['option']) ? $_GET['option'] : '';
			$_GET['type_id'] = isset($_GET['type_id']) ? $_GET['type_id'] : '';
			$_GET['category_id'] = isset($_GET['category_id']) ? $_GET['category_id'] : '';
			$_GET['sort_by'] = isset($_GET['sort_by']) ? $_GET['sort_by'] : 0;
			$_GET['query'] = isset($_GET['query']) ? $_GET['query'] : '';

			# DECLARE SORT ARRAY -------------------
			$data_sort=array('','id DESC','id ASC','data DESC','data ASC','name DESC','name ASC');

			# FIX ARRAY -------------------
			if(gettype($_GET['ids'])=='array'){
				foreach ($_GET['ids'] as $position => $item){
					$_GET['idsx'][]=intval($position);
					$_GET['idsxs'].='&ids['.intval($position).']=on';
				}
			}

			# CHECK QUERIES -------------------
			if(!preg_match("/^[a-zA-Z0-9-]+$/", $_GET['option'])) {
				$_GET['option']='';
			}

			if(!preg_match("/^[a-zA-Z0-9]+$/", $_GET['query'])) {
				$_GET['query']='';
			}

			# FIX CATEGORY -------------------
			if($_GET['option']!=''){
				// Reset category_id
				$_GET['category_id']=0;

				// Clean category name
				$_GET['option'] = preg_replace('/-/i',' ', $_GET['option']);

				// Prepare statements to get selected data
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes_categories WHERE LOWER(name) LIKE :category");
				$stmt->bindParam(':category', $_GET['option'], PDO::PARAM_STR, 50);
				$stmt->execute();

				// Return result in table
				$rowx=$stmt->fetch(PDO::FETCH_ASSOC);

				// Check if item exist
				if($rowx['id']){
					$_GET['category_id']=$rowx['id'];
				}
			}

			# SET DATA -------------------
			$this->set_data(array(
				"type_id" => intval($_GET['type_id']),
				"category_id" => intval($_GET['category_id']),
				"page" => intval($_GET['page']),
				"sort_by" => $data_sort[$_GET['sort_by']],
				//"ids" => $_GET['idsx'], @2do unblock
				"query" => $this->filter_string($_GET['query']),
				"query_in" => array('name','description'),
				"where" => 'published=1'
			));


			# GET DATA -------------------
			$obj['categories']=$this->category_get('themes');

			# GET DATA -------------------
			$obj['data']=$this->once_select_items_page('themes');
		}else{
			$obj['error']='not authorized';
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
	function export_config($theme_id){
		$stmt = $this->pdo->prepare("SHOW TABLES");
		$stmt->execute();
					
		// Get count of returned records
		if($stmt->rowCount()){
			// Export langs & categories
			$stmt1 = $this->pdo->prepare("SELECT * FROM edit_langs WHERE project_id=".$theme_id."");
			$stmt1->execute();
			foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_langs'][]=$row;
			}
			$stmt2 = $this->pdo->prepare("SELECT * FROM edit_langs_categories WHERE project_id=".$theme_id."");
			$stmt2->execute();
			foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_langs_categories'][]=$row;
			}

			// Export layers & cols & rows
			$stmt1 = $this->pdo->prepare("SELECT * FROM edit_layers WHERE project_id=".$theme_id."");
			$stmt1->execute();
			foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_layers'][]=$row;
			}
			$stmt2 = $this->pdo->prepare("SELECT * FROM edit_layers_cols WHERE project_id=".$theme_id."");
			$stmt2->execute();
			foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_layers_cols'][]=$row;
			}
			$stmt3 = $this->pdo->prepare("SELECT * FROM edit_layers_rows WHERE project_id=".$theme_id."");
			$stmt3->execute();
			foreach ($stmt3->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_layers_rows'][]=$row;
			}
				
			// Export pages & cols & rows
			$stmt1 = $this->pdo->prepare("SELECT * FROM edit_pages WHERE project_id=".$theme_id."");
			$stmt1->execute();
			foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_pages'][]=$row;
			}
			$stmt2 = $this->pdo->prepare("SELECT * FROM edit_pages_cols WHERE project_id=".$theme_id."");
			$stmt2->execute();
			foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_pages_cols'][]=$row;
			}
			$stmt3 = $this->pdo->prepare("SELECT * FROM edit_pages_rows WHERE project_id=".$theme_id."");
			$stmt3->execute();
			foreach ($stmt3->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_pages_rows'][]=$row;
			}	
				
			// Export plugins
			$stmt1 = $this->pdo->prepare("SELECT * FROM edit_plugins");
			$stmt1->execute();
			foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_plugins'][]=$row;
			}	
				
			// Export routes & categories
			$stmt1 = $this->pdo->prepare("SELECT * FROM edit_routes WHERE project_id=".$theme_id."");
			$stmt1->execute();
			foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_routes'][]=$row;
			}
			$stmt2 = $this->pdo->prepare("SELECT * FROM edit_routes_categories WHERE project_id=".$theme_id."");
			$stmt2->execute();
			foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$dump['edit_routes_categories'][]=$row;
			}
		}
		
		return $dump;
	}
	function import_config($theme_id){
		// install db from file
		$config = unserialize(@file_get_contents('./themes/'.$theme_id.'/once.config'));

		// Import langs
		if(count($config['edit_langs_categories'])){
			foreach ($config['edit_langs_categories'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_langs_categories ($sql1) VALUES ($sql2)");
				$stmt1->execute();

				$categories[$record['id']]=$this->pdo->lastInsertId();
			}
		}
		if(count($config['edit_langs'])){
			foreach ($config['edit_langs'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}
						if($key=='category_id'){
							$value=$categories[$value];
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_langs ($sql1) VALUES ($sql2)");
				$stmt1->execute();
			}
		}

		// Import layers
		if(count($config['edit_layers'])){
			foreach ($config['edit_layers'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}
						if($key=='default'){
							$key="`".$key."`";
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_layers ($sql1) VALUES ($sql2)");
				$stmt1->execute();

				$layes[$record['id']]=$this->pdo->lastInsertId();
			}
		}
		if(count($config['edit_layers_cols'])){
			foreach ($config['edit_layers_cols'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}
						if($key=='layer_id'){
							$value=$layes[$value];
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_layers_cols ($sql1) VALUES ($sql2)");
				$stmt1->execute();
			}
		}
		if(count($config['edit_layers_rows'])){
			foreach ($config['edit_layers_rows'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}
						if($key=='layer_id'){
							$value=$layes[$value];
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_layers_rows ($sql1) VALUES ($sql2)");
				$stmt1->execute();
			}
		}
		
		// Import pages
		if(count($config['edit_pages'])){
			foreach ($config['edit_pages'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}
						if($key=='layer_id'){
							$value=$layes[$value];
						}
						if($key=='default'){
							$key="`".$key."`";
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_pages ($sql1) VALUES ($sql2)");
				$stmt1->execute();

				$pages[$record['id']]=$this->pdo->lastInsertId();
			}
		}
		if(count($config['edit_pages_cols'])){
			foreach ($config['edit_pages_cols'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}
						if($key=='page_id'){
							$value=$pages[$value];
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_pages_cols ($sql1) VALUES ($sql2)");
				$stmt1->execute();
			}
		}
		if(count($config['edit_layers_rows'])){
			foreach ($config['edit_layers_rows'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}
						if($key=='page_id'){
							$value=$pages[$value];
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_layers_rows ($sql1) VALUES ($sql2)");
				$stmt1->execute();
			}
		}
		
		// Import plugins
		if(count($config['edit_plugins'])){
			foreach ($config['edit_plugins'] as $record){
				// check used plugins in project and check permision if some1 can download it
			}
		}
		
		// Import langs
		if(count($config['edit_routes_categories'])){
			foreach ($config['edit_routes_categories'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_routes_categories ($sql1) VALUES ($sql2)");
				$stmt1->execute();

				$categories[$record['id']]=$this->pdo->lastInsertId();
			}
		}
		if(count($config['edit_routes'])){
			foreach ($config['edit_routes'] as $record){
				// Get columns
				$i=0;
				$sql1='';
				$sql2='';
				foreach ($record as $key => $value){
					// Interator
					$i++;
					if($key!='id'){
						// Change values
						if($key=='project_id'){
							$value=$theme_id;
						}
						if($key=='category_id'){
							$value=$categories[$value];
						}
						if($key=='page_id'){
							$value=$pages[$value];
						}

						// Extracting keys
						if($i==count($record)){
							$sql1.=$key;
						}else{
							$sql1.=$key.', ';
						}

						// Extracting values
						if($i==count($record)){
							$sql2.="'".$value."'";
						}else{
							$sql2.="'".$value."', ";
						}
					}
				}

				$stmt1 = $this->pdo->prepare("INSERT INTO edit_routes ($sql1) VALUES ($sql2)");
				$stmt1->execute();
			}
		}
	}
	function set_limit(){
		// Update page limit with once
		$this->once_page_limit('themes');
		return $this->once_response();
	}
	function twitter_publish(){
		if($this->data['twitter_publish_on']){
			// Include twitteroauth to post twitter
			require_once($this->data['root_path'].'/once/libs/twitteroauth/twitteroauth/twitteroauth.php');
			
			/** Set access tokens here - see: https://dev.twitter.com/apps/ **/

			function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
				$connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
				return $connection;
			}
			 
			$connection = getConnectionWithAccessToken($this->data['twitter_consumerkey'], $this->data['twitter_consumersecret'], $this->data['twitter_accesstoken'], $this->data['twitter_accesstokensecret']);

			// Get time line
			//$parameters = array('screen_name' => $this->data['twitter_user'], 'count' => $this->data['twitter_notweets']);
			//$tweets = $connection->get('statuses/user_timeline', $parameters);

			// Insert status to twitter
			date_default_timezone_set('GMT');

			// Upload photo
			$file = file_get_contents($this->data['root_path'].'/once/themes/'.$this->data['id'].'/thumbnail.png');
			$base = base64_encode($file);
			$media1 = $connection->upload('media/upload', array('media' => array('media' => $base)));

			// Short link before publish
			require_once($this->data['root_path'].'/once/libs/bitlyphp/bitly.php');

			$url='https://oncebuilder.com/theme/'.$this->data['id'];
			
			$params = array();
			$params['access_token'] = $this->data['bitly_access_token'];
			$params['longUrl'] = $url;
			$results = bitly_get('shorten', $params);

			if($results['status_code']==200){
				if($results['data']['url']!=''){
					$url=$results['data']['url'];
				}
			}

			// Prepare message to twitt
			$message=$this->item['name'].' '.$url.' '.($this->item['author']!=''?' by @'.$this->item['author']:'');

			$parameters = array('status' => $message, 'media_ids' => $media1->media_id_string);
			$status = $connection->post('statuses/update', $parameters);
		}
	}
	function update_resource(){
		// Load ZipArchive class to procces download project as zip archive
		if(!extension_loaded('zip')){
			dl('zip.so');
		}
		
		// Source to update
		$source=$this->data['root_path'].'/once/themes/'.$this->data['id'];

		$zip = new ZipArchive();
		$archiveName = $source."/theme.zip";

		@unlink($archiveName);
		if ($zip->open($archiveName, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$archiveName>\n");
		}
	
		// check if theme exist
		if(file_exists($source)){
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($files as $file){
				$file = str_replace('\\', '/', $file);
					if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) ) continue;
					if(!strpos($file, '/.git') && !strpos($file, '/theme.zip')){
					if (is_dir($file) === true){
						$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
					}
					else if (is_file($file) === true){
						$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
					}
				}
			}
		}

		//export sql... exporet all tables with project_id
		$dump=$this->export_config($this->data['id']);
		$zip->addFromString("once.config", serialize($dump));
		
		// Close zip
		$zip->close();
	}

	function item_approve(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Prepare statements to get selected data
			$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();
				
			// Get count of returned records
			if($stmt->rowCount()){
				// Data as item
				$this->item=$stmt->fetch(PDO::FETCH_ASSOC);

				// Check if its published/unpublished then unpublish/publish
				if($this->item['published']==0){
					//2do Check if it was already published on twitter then publish
					if(true){
						$this->twitter_publish();
					}
						
					// Set as published
					$stmt = $this->pdo->prepare("UPDATE edit_themes SET published=1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();

					// Set count for user when added
					$this->data['user_id']=$this->item['user_id'];
					$this->once_set_user_count('themes');
						
					// File expception
					$except = array('thumbnail.png','images/','images/ss1.png','images/ss2.png','images/ss3.png','images/ss4.png','images/ss5.png','images/ss6.png','images/ss7.png','images/ss8.png','images/ss9.png');

					// Unzip
					$zip = new ZipArchive;
					$res = $zip->open('./themes/'.$this->data['id'].'/theme.zip');
					if($res === TRUE){
						// list of all the files in zip
						$files = array();
						for ($idx = 0; $idx < $zip->numFiles; $idx++) {
							if(!in_array($zip->getNameIndex($idx),$except)) $files[] = $zip->getNameIndex($idx);
						}

						// only extract the remaining $files
						$zip->extractTo('./themes/'.$this->data['id'].'/', $files);

						$zip->close();
							
						// Import conifg
						$this->import_config($this->data['id']);
					}else{
						$this->set_error('No access to .zip file');
					}
				}else{
					// Prepare statements to publish selected data
					$stmt = $this->pdo->prepare("UPDATE edit_themes SET published=0 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
						
					// File expception
					$except = array('thumbnail.png','images','theme.zip');

					// Update zip before send
					$this->update_resource();
						
					// Delete all files except images/ss , thumbnail and theme zip
					$path="./themes/".$this->data['id']."";
					if ($handle = opendir($path)) {
						while(false !== ($file = readdir($handle))) {
							if($file == "." || $file == ".." || in_array($file,$except))  continue;
							if(@filetype($path."/".$file) != "dir") {
								if($file){
									@unlink("./themes/".$this->data['id']."/".$file);
								}
							}
							if(@filetype($path."/".$file) == "dir" && $file!='once') {
								$this->recurse_delete($path."/".$file,"./themes/".$this->data['id']."/".$file);
							}
						}
						closedir($handle);
					}
				}
			}else{
				$this->set_error('Theme not exist');
			}
		}
		return $this->once_response();
	}
	function item_delete(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			if($stmt->rowCount()){
				// Use once to delete item
				if($this->once_delete_item('themes')){
					$this->recurse_delete($this->data['root_path'].'/once/themes/'.$this->data['id'].'');
				}
				//@2do delete all from db
			}else{
				$this->set_error('Theme not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	function item_edit(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check(true)){
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			// Get count of returned records
			if($stmt->rowCount()){
				$this->item=$stmt->fetch(PDO::FETCH_ASSOC);

				// Prepare statements to update theme.
				$stmt = $this->pdo->prepare("UPDATE edit_themes SET category_id=:category_id, version=:version, price=:price, name=:name, tags=:tags, author=:author, author_url=:author_url, licence=:licence, description=:description WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':category_id', $this->data['category_id'], PDO::PARAM_INT);
				$stmt->bindParam(':version', $this->data['version'], PDO::PARAM_INT);
				$stmt->bindParam(':price', $this->data['price'], PDO::PARAM_INT);
				$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 55);
				$stmt->bindParam(':tags', $this->data['tags'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':author', $this->data['author'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':author_url', $this->data['author_url'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':licence', $this->data['licence'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':description', $this->data['description'], PDO::PARAM_STR, 255);
				$stmt->execute();
				
				if(!$stmt->rowCount()){
					$this->set_error('Can not save');
				}
			}else{
				$this->set_error('Theme not exist');
			}
		}
		return $this->once_response();
	}
	function item_download(){
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=themes&o=item_download");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "api=".$this->data['api_key']."&theme_id=".$this->data['id']); //dane do wyslania
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				$response = curl_exec($ch);
				curl_close($ch);
				// Decode json
				$obj=json_decode($response, true);
				$this->item=$obj['item'];
				
				// Use once to insert empty theme
				$objx=$this->once_insert('themes',array(
					"id" => '',
					"name" => $this->item['name'],
					"user_id"=> $this->data['user_id']
				));

				if($objx){
					// Make theme dir
					@mkdir("./themes");
					@chmod("./themes", 0777);
					@mkdir("./themes/".$objx['item']['id']);
					@chmod("./themes/".$objx['item']['id'], 0777);
				}
				
				//DOWNLOAD THEME FROM UNIQUE URL AND UNPACK
				$file=@file_get_contents('https://oncebuilder.com/once/themes/'.$this->item['id'].'/theme.zip');
				
				@file_put_contents("./themes/".$objx['item']['id'].'/theme.zip',$file);
				
				// Unpack file
				$zip = new ZipArchive;
				$res = $zip->open('./themes/'.$objx['item']['id'].'/theme.zip');
				if($res === TRUE){
					$zip->extractTo('./themes/'.$objx['item']['id'].'/');
					$zip->close();

					if(file_exists('./themes/'.$objx['item']['id'].'/once.config')) {
						// Import conifg
						$this->import_config($objx['item']['id']);
					}else{
						$this->set_error('No access to config file');
					}
				}else{
					$this->set_error('No access to .zip file');
				}
			}
		}else{
			// Check if user exist by api_key
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE api_key=:api_key LIMIT 1");
			$stmt->bindParam(':api_key', $this->data['api'], PDO::PARAM_STR, 255);
			$stmt->execute();

			if($stmt->rowCount()){
				$row=$stmt->fetch(PDO::FETCH_ASSOC);
				// Check if user exist by api_key
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:theme_id LIMIT 1");
				$stmt->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					$row2=$stmt->fetch(PDO::FETCH_ASSOC);
					// Check if theme is premium else download
					if($row2['price']>0){
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_themes_downloads WHERE user_id=:user_id AND theme_id=:theme_id LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
						$stmt2->execute();

						if($stmt2->rowCount()){
							// Return whole theme
							$this->item=$row2;
						}else{
							if($row['balance']>=$row2['price']){
								// Mark theme unlocked for future use
								$stmt3 = $this->pdo->prepare("INSERT INTO edit_themes_downloads (user_id, theme_id, mktime) VALUES(:user_id, :theme_id, :mktime)");
								$stmt3->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
								$stmt3->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
								$stmt3->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
								$stmt3->execute();
								
								// Update balance
								$stmt4 = $this->pdo->prepare("UPDATE edit_users WHERE balance=balance-'".$row2['price']."'");
								$stmt4->execute();
								
								// Return whole theme
								$this->item=$row2;
							}else{
								$this->set_error('Not enough balance');
							}
						}
					}else{
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_themes_downloads WHERE user_id=:user_id AND user_ip=:user_ip LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
						$stmt2->execute();
						
						if(!$stmt2->rowCount()){
							// Count downloads of theme
							$stmt2 = $this->pdo->prepare("INSERT INTO edit_themes_downloads (user_id, theme_id, mktime, user_ip) VALUES(:user_id, :theme_id, :mktime, :user_ip)");
							$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
							$stmt2->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
							$stmt2->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
							$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
							$stmt2->execute();
						}
						
						// Return whole theme
						$this->item=$row2;
					}
				}else{
					$this->set_error('Theme not exist');
				}
			}else{
				$this->set_error('API not authorized');
			}
		}
		return $this->once_response();
	}
	function item_export(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			if($stmt->rowCount()){
				// Copy without deleting of current version to themes
				if($this->data['project_id']==$this->data['id']){
					$path=$this->data['root_path']."";
					if ($handle = opendir($path)) {
						while(false !== ($file = readdir($handle))) {
							if($file == "." || $file == "..")  continue;
							if(@filetype($path."/".$file) != "dir") {
								if($file){
									copy($path."/".$file,"./themes/".$this->data['project_id']."/".$file);
								}
							}
							if(@filetype($path."/".$file) == "dir" && $file!='once') {
								$this->recurse_copy($path."/".$file,"./themes/".$this->data['project_id']."/".$file);
							}
						}
						closedir($handle);
					}
				}
			
				// Update zip before send
				$this->update_resource();
				
				// Export all files of themes
				$archiveName=$this->data['root_path'].'/once/themes/'.$this->data['id'].'/theme.zip';

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
				echo json_encode($obj);
			}else{
				$this->set_error('Theme not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	function item_import(){
		// Check if url field is set then download else handle file
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Use once to insert empty record
			if($this->once_insert('themes',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			))){
				// Make theme dir
				@mkdir($this->data['root_path'].'/once/themes');
				@chmod($this->data['root_path'].'/once/themes', 0777);

				// Make theme dir
				@mkdir($this->data['root_path'].'/once/themes/'.$this->item['id']);
				@chmod($this->data['root_path'].'/once/themes/'.$this->item['id'], 0777);

				if($this->data['url']){
					if (!preg_match("~^(?:f|ht)tps?://~i", $this->data['url'])) {
						$this->data['url'] = "http://" . $this->data['url'];
					}
					$headers=get_headers($this->data['url'],1);
					if($headers['Content-Type']=='application/zip'){
						// Get file source from url
						file_put_contents($this->data['root_path'].'/once/themes/'.$this->item['id'].'/theme.zip',file_get_contents($this->data['url']));
					}else{
						$this->set_error('URL must contain .zip file');
					}
				}else{
					move_uploaded_file($this->data["file"]["tmp_name"],$this->data['root_path'].'/once/themes/'.$this->item['id'].'/theme.zip');
				}

				$zip = new ZipArchive;
				$res = $zip->open($this->data['root_path'].'/once/themes/'.$this->item['id'].'/theme.zip');
				if($res === TRUE){
					$zip->extractTo($this->data['root_path'].'/once/themes/'.$this->item['id'].'/');
					$zip->close();

					// Import conifg
					$this->import_config($this->item['id']);
				}else{
					$this->set_error('No access to .zip file');
				}
				//@2do @unlink($this->data['root_path'].'/once/themes/'.$this->item['id'].'/theme.zip');
			}else{
				$this->set_error('Can not insert item to: themes');
			}
		}
		return $this->once_response();
	}
	function item_new(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if(!$this->once_creator_check()){
				// @2do dd limits
			}

			// Use once to insert empty record
			if($this->once_insert('themes',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			))){
				// Make themes dir
				@mkdir($this->data['root_path'].'/once/themes');
				@chmod($this->data['root_path'].'/once/themes', 0777);

				// Make dir for old theme
				@mkdir($this->data['root_path'].'/once/themes/'.$this->data['project_id']);
				@chmod($this->data['root_path'].'/once/themes/'.$this->data['project_id'], 0777);

				// Copy of current version to themes
				$path=$this->data['root_path']."";
				if ($handle = opendir($path)) {
					while(false !== ($file = readdir($handle))) {
						if($file == "." || $file == "..")  continue;
						if(@filetype($path."/".$file) != "dir") {
							if($file){
								copy($path."/".$file,"./themes/".$this->data['project_id']."/".$file);
								@unlink($path."/".$file);
							}
						}
						if(@filetype($path."/".$file) == "dir" && $file!='once') {
							$this->recurse_copy($path."/".$file,"./themes/".$this->data['project_id']."/".$file);
							$this->recurse_delete($path."/".$file);
						}
					}
					closedir($handle);
				}

				// Make new dirs and copy default files
				@mkdir($this->data['root_path'].'/ajax/', 0777);
				@mkdir($this->data['root_path'].'/class/', 0777);
				@mkdir($this->data['root_path'].'/css/', 0777);
				@mkdir($this->data['root_path'].'/fonts/', 0777);
				@mkdir($this->data['root_path'].'/grids/', 0777);
				@mkdir($this->data['root_path'].'/images/', 0777);
				@mkdir($this->data['root_path'].'/include/', 0777);
				@mkdir($this->data['root_path'].'/js/', 0777);
				@mkdir($this->data['root_path'].'/langs/', 0777);
				@mkdir($this->data['root_path'].'/layers/', 0777);
				@mkdir($this->data['root_path'].'/libs/', 0777);
				@mkdir($this->data['root_path'].'/pages/', 0777);
				@mkdir($this->data['root_path'].'/routes/', 0777);
				@mkdir($this->data['root_path'].'/tpl/', 0777);
				
				// Prepare starter teplate
				@file_put_contents($this->data['root_path'].'/css/global.css',file_get_contents($this->data['root_path'].'/once/default/css/global.css'));
				@file_put_contents($this->data['root_path'].'/css/style.css',file_get_contents($this->data['root_path'].'/once/default/css/style.css'));
				@file_put_contents($this->data['root_path'].'/js/main.js',file_get_contents($this->data['root_path'].'/once/default/js/main.js'));
				@file_put_contents($this->data['root_path'].'/js/script.js',file_get_contents($this->data['root_path'].'/once/default/js/script.js'));
				@file_put_contents($this->data['root_path'].'/.htaccess',file_get_contents($this->data['root_path'].'/once/default/.htaccess'));
				@file_put_contents($this->data['root_path'].'/ajax.php',file_get_contents($this->data['root_path'].'/once/default/ajax.php'));
				@file_put_contents($this->data['root_path'].'/head.php',file_get_contents($this->data['root_path'].'/once/default/head.php'));
				
				// Unset all project
				$stmt = $this->pdo->prepare("UPDATE edit_themes SET `default`='0' WHERE user_id=:user_id");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();

				// Set fields to update
				$stmt = $this->pdo->prepare("UPDATE edit_themes SET `default`='1' WHERE id=:id");
				$stmt->bindParam(':id', $this->item['id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					$this->data['project_id']=$_SESSION['project_id']=$this->item['id'];
				}
				
				// Make dirs for new theme
				@mkdir($this->data['root_path'].'/once/themes/'.$this->item['id']);
				@chmod($this->data['root_path'].'/once/themes/'.$this->item['id'], 0777);
				@mkdir($this->data['root_path'].'/once/themes/'.$this->item['id'].'/images');
				@chmod($this->data['root_path'].'/once/themes/'.$this->item['id'].'/images', 0777);

				// Insert new lags
				$stmt = $this->pdo->prepare("INSERT INTO edit_themes_langs (project_id, type_id) VALUES('".$this->data['project_id']."', '1')");
				$stmt->bindParam(':id', $this->data['project_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Some files must be auto generated to not occur a bugs
				$this->gen_config();
				$this->gen_index();
				$this->gen_switch();//?
				$this->gen_grids();
				$this->gen_langs();
				$this->gen_routes();
			}else{
				$this->set_error('Can not insert item to: themes');
			}
		}
		return $this->once_response();
	}
	function item_preview(){
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=themes&o=item_preview");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "api=".$this->data['api_key']."&theme_id=".$this->data['id']); //dane do wyslania
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$response = curl_exec($ch);
			curl_close($ch);
			// Decode json
			//$response=preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);
			$obj=json_decode($response, true);
			$this->item=$obj['item'];
			return $obj;
		}else{
			// Check if user exist by api_key
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE api_key=:api_key LIMIT 1");
			$stmt->bindParam(':api_key', $this->data['api'], PDO::PARAM_STR, 255);
			$stmt->execute();

			if($stmt->rowCount()){
				$row=$stmt->fetch(PDO::FETCH_ASSOC);
				// Check if user exist by api_key
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:theme_id LIMIT 1");
				$stmt->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					$this->item=$stmt->fetch(PDO::FETCH_ASSOC);

					if($this->item['price']>0){
						// Check if user bought this theme
						$stmt = $this->pdo->prepare("SELECT COUNT(*) AS ile FROM edit_themes_downloads WHERE user_id=:user_id AND theme_id=:theme_id LIMIT 1");
						$stmt->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
						$stmt->execute();

						if($stmt->fetchColumn() > 0) {
							$this->item['bought']=true;
						}
					}

					for($i=1;$i<10;$i++){
						if(file_exists($this->data['root_path'].'/once/themes/'.$this->data['theme_id'].'/images/ss'.$i.'.png')){
							$this->item['images'][]="ss".$i.".png";
						}
					}
				}else{
					$this->set_error('Theme not exist');
				}
			}else{
				$this->set_error('API not authorized');
			}
		}
		return $this->once_response();
	}
	function item_publish(){
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("SELECT id, version, name, description, tags, author, author_url FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();

				$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
					
				// Check if theme exist.
				if($this->item){
					// Update file if not exist
					if(!file_exists($this->data['root_path'].'/once/themes/'.$this->item['id'].'/thumbnail.png')){
						file_put_contents($this->data['root_path'].'/once/themes/'.$this->item['id'].'/thumbnail.png','');
					}

					// Update zip before send
					$this->update_resource();

					// Set CURL
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=themes&o=item_publish");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					
					// Add files depends on version PHP
					$version = explode('.', phpversion());
					if($version[0]>=5 && $version[1]>=5){
						//curl_file_create(realpath('themes/'.$this->item['id'].'/theme.zip'))
						
						$file = new CURLFile(realpath('themes/'.$this->item['id'].'/theme.zip'));
						$file->setPostFilename('theme.zip');
						
						$thumbnail = new CURLFile(realpath('themes/'.$this->item['id'].'/thumbnail.png'));
						$thumbnail->setPostFilename('thumbnail.png');
					}else{
						$file = '@' . realpath($this->data['root_path'].'/once/themes/'.$this->item['id'].'/theme.zip');
						$thumbnail = '@' . realpath($this->data['root_path'].'/once/themes/'.$this->item['id'].'/thumbnail.png');
					}

					// Send files and parms
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt(
						$ch,
						CURLOPT_POSTFIELDS,
						array(
							'api' => $this->data['api_key'],
							"file" => $file,		
							"thumbnail" => $thumbnail,
							'object_id' => $this->item['id'],
							'version' => $this->item['version'],
							'price' => $this->item['price'],
							'name' => $this->item['name'],
							'tags' => $this->item['tags'],
							'author' => $this->item['author'],
							'author_url' => $this->item['author_url'],
							'description' => $this->item['description'],
							'licence' => $this->item['licence'],
							'message' => $this->data['message']
						)
					);

					// Get response
					$response = curl_exec($ch);
					$obj=json_decode($response, true);
					$this->item=$obj['item'];

					if(strpos($response, '"status":"ok"')){
						// Prepare set as published
						$stmt = $this->pdo->prepare("UPDATE edit_themes SET object_id=:object_id WHERE id=:id LIMIT 1");
						$stmt->bindParam(':object_id', $this->data['id'], PDO::PARAM_INT);
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
					}else{
						$this->set_error('Upload API error');
					}

					curl_close($ch);
				}else{
					$this->set_error('Theme does not exist');
				}
			}
		}else{
			if($this->data['object_id']){
				// Prepare statements to get selected api_key
				$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE api_key=:api_key LIMIT 1");
				$stmt->bindParam('api_key', $this->data['api'], PDO::PARAM_STR, 32);
				$stmt->execute();
				$obj['user'] = $stmt->fetch(PDO::FETCH_ASSOC);
				
				// Check if api_key is connected with user.
				if($obj['user']){
					if(!$this->data["file"]["error"] > 0){
						// Insert new theme
						$stmt = $this->pdo->prepare("
							INSERT INTO edit_themes (user_id, object_id, version, name, tags, author, author_url, licence, description, created) 
							VALUES (:user_id, :object_id, :version, :name, :tags, :author, :author_url, :licence, :description, :created)
						");
						
						$stmt->bindParam(':user_id', $obj['user']['id'], PDO::PARAM_INT);
						$stmt->bindParam(':object_id', $this->data['object_id'], PDO::PARAM_INT);
						$stmt->bindParam(':version', $this->data['version'], PDO::PARAM_INT);
						$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':tags', $this->data['tags'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':author', $this->data['author'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':author_url', $this->data['author_url'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':licence', $this->data['licence'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':description', $this->data['description'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':created', $this->data['time'], PDO::PARAM_INT);
						$stmt->execute();
						
						// Get last insert id
						$lastInsertId=$this->pdo->lastInsertId();

						// Insert message
						if($this->data['message']!=''){
							$stmt = $this->pdo->prepare("
								INSERT INTO edit_themes_reports (user_id, theme_id, mktime, message) 
								VALUES (:user_id, :theme_id, :mktime, :message)
							");
							$stmt->bindParam(':user_id', $obj['user']['id'], PDO::PARAM_INT);
							$stmt->bindParam(':theme_id', $lastInsertId, PDO::PARAM_INT);
							$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
							$stmt->bindParam(':message', $this->data['message'], PDO::PARAM_STR, 255);
							$stmt->execute();
							$lasxtInsertId=$this->pdo->lastInsertId();
						}

						// Make dirs and generate files
						@mkdir($this->data['root_path']."/once/themes/".$lastInsertId."");
						@chmod($this->data['root_path']."/once/themes/".$lastInsertId."", 0777);
	
						// Upload file to folder
						move_uploaded_file($this->data["file"]["tmp_name"],$this->data['root_path'].'/once/themes/'.$lastInsertId.'/theme.zip');

						// Move uploaded thumbnail to theme dir
						move_uploaded_file($this->data["thumbnail"]["tmp_name"],$this->data['root_path'].'/once/themes/'.$lastInsertId.'/thumbnail.png');

						//Resize image if its 320x240 if it's larger
						$this->once_image_resize($this->data['root_path'].'/once/themes/'.$lastInsertId.'/thumbnail.png',320,240);
						
						// Make dirs and generate files
						@mkdir($this->data['root_path']."/once/themes/".$lastInsertId."/images");
						@chmod($this->data['root_path']."/once/themes/".$lastInsertId."/images", 0777);
						
						// Unzip only images
						$zip = new ZipArchive;
						$res = $zip->open($this->data['root_path'].'/once/themes/'.$lastInsertId.'/theme.zip');
						if($res === TRUE){
							$zip->extractTo($this->data['root_path'].'/once/themes/'.$lastInsertId.'/', array('images/ss1.png','images/ss2.png','images/ss3.png','images/ss4.png','images/ss5.png','images/ss6.png','images/ss7.png','images/ss8.png','images/ss9.png'));
							$zip->close();
						}

						// Resample images
						for($i=1;$i<10;$i++){
							if(file_exists($this->data['root_path'].'/once/themes/'.$lastInsertId.'/images/ss'.$i.'.png')) {
								$this->once_image_resample($this->data['root_path'].'/once/themes/'.$lastInsertId.'/images/ss'.$i.'.png');
							}
						}
					}else{
						$this->set_error('Error file upload');
					}
				}else{
					$this->set_error('API key not found');
				}
			}else{
				$this->set_error('Object missed!');
			}
		}
		return $this->once_response();
	}
	function item_use(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			if($this->once_select_item('themes')){
				// Make sure that there is dir for theme
				@mkdir("./themes/".$this->data['project_id']."");
				@chmod("./themes/".$this->data['project_id']."", 0777);
				
				// Copy of current version to themes and delete as main
				$path=$this->data['root_path']."";
				if ($handle = opendir($path)) {
					while(false !== ($file = readdir($handle))) {
						if($file == "." || $file == "..")  continue;
						if(@filetype($path."/".$file) != "dir") {
							if($file){
								copy($path."/".$file,"./themes/".$this->data['project_id']."/".$file);
								@unlink($path."/".$file);
							}
						}
						if(@filetype($path."/".$file) == "dir" && $file!='once') {
							$this->recurse_copy($path."/".$file,"./themes/".$this->data['project_id']."/".$file);
							$this->recurse_delete($path."/".$file);
						}
					}
					closedir($handle);
				}
							
				// Copy selected theme
				$this->recurse_copy("./themes/".$this->data['id'],"../");
				
				// Unset themes and set new one
				$stmt = $this->pdo->prepare("UPDATE edit_themes SET `default`=0 WHERE user_id=:user_id");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				$stmt = $this->pdo->prepare("UPDATE edit_themes SET `default`=1 WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				if($stmt->rowCount()){
					$this->data['project_id']=$_SESSION['project_id']=$this->data['id'];
				}
			}
		}
		return $this->once_response();
	}
	function item_star(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			// Get count of returned records
			if($stmt->rowCount()){
				$this->item=$stmt->fetch(PDO::FETCH_ASSOC);
				// Check if its stared/unstared then unstar/star
				if($this->item['stared']==1){
					// Prepare statements to unstar selected data
					$stmt = $this->pdo->prepare("UPDATE edit_themes SET stared=0 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}else{
					// Prepare statements to star selected data
					$stmt = $this->pdo->prepare("UPDATE edit_themes SET stared=1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}
			}else{
				$this->set_error('Theme not exist');
			}
		}
		return $this->once_response();
	}
	function delete_image(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			if($stmt->rowCount()){
				if($this->data['project_id']==$this->data['id']){
					@unlink('../images/ss'.$this->data['currentImage'].'.png');
				}
				@unlink('./themes/'.$this->data['id'].'/images/ss'.$this->data['currentImage'].'.png');
			}else{
				$this->set_error('Theme not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	function upload_image(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			if($stmt->rowCount()){
				if(!$this->data["image"]["error"]){
					//extract extension
					$image_extensions_allowed = array('jpg', 'jpeg', 'png', 'gif');
					$extension = strtolower(substr($this->data["image"]['name'], strrpos($this->data["image"]['name'], '.') + 1));
					
					// Check extension
					if(in_array($extension, $image_extensions_allowed)){
						$image_mimes_allowed = array("image/gif","image/png","image/jpeg","image/pjpeg");
						$imageinfo = getimagesize($this->data["image"]['tmp_name']);
							
						// Check mime
						if(isset($imageinfo) && in_array($imageinfo['mime'], $image_mimes_allowed)){
							// Check size up to 1MB
							if($this->data["image"]["size"]<= 1000000) {
								// If new fie
								$image=$this->data['currentImage'];

								// Make sure image dir exist
								@mkdir($this->data['root_path'].'/once/themes/'.$this->data['id'].'/images');
								
								if($this->data['currentImage']==0){
									if($this->data['project_id']==$this->data['id']){
										$dir='../images/';
									}else{
										$dir='themes/'.$this->data['id'].'/images/';
									}
									$ok=0;
									for($i=1;$i<10;$i++){
										if(!file_exists($dir."ss".$i.".png")){
											$this->data['currentImage']="./".$dir."ss".$i.".png";
											$ok=$i;
											$i=10;
										}
									}
								}else{
									// Default image path if ok
									$ok=$this->data['currentImage'];
									if($this->data['project_id']==$this->data['id']){
										$this->data['currentImage']='../images/ss'.$this->data['currentImage'].'.png';
									}else{
										$this->data['currentImage']='./themes/'.$this->data['id'].'/images/ss'.$this->data['currentImage'].'.png';
									}
								}
								
								if($ok>0){
									// Move uploaded file to upload dir
									move_uploaded_file($this->data["image"]["tmp_name"],'./'.$this->data['currentImage']);
										
									// Resize image
									$this->once_image_resample('./'.$this->data['currentImage']);
										
									// Set fields to update
									$this->item=array(
										"id" => $this->data['id'],
										"currentImage" => $ok
									);
								}else{
									$this->set_error('LIMIT 9');
								}
							}else{
								$this->set_error('We only accept images up to 1MB');
							}
						}else{
							$this->set_error('We only accept GIF and JPEG images');
						}
					}else{
						$this->set_error('Extension not allowed');
					}
				}else{
					$this->set_error('Upload error');
				}
			}else{
				$this->set_error('Theme not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	function upload_thumbnail(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get theme.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			if($stmt->rowCount()){
				if(!$this->data["image"]["error"]){
					//extract extension
					$image_extensions_allowed = array('jpg', 'jpeg', 'png', 'gif');
					$extension = strtolower(substr($this->data["image"]['name'], strrpos($this->data["image"]['name'], '.') + 1));
					
					// Check extension
					if(in_array($extension, $image_extensions_allowed)){
						$image_mimes_allowed = array("image/gif","image/png","image/jpeg","image/pjpeg");
						$imageinfo = getimagesize($this->data["image"]['tmp_name']);
							
						// Check mime
						if(isset($imageinfo) && in_array($imageinfo['mime'], $image_mimes_allowed)){
							// Check size up to 1MB
							if($this->data["image"]["size"]<= 1000000) {
								// Make dirs and generate files
								@mkdir("./themes");
								@mkdir("./themes/".$this->data['id']."");
								chmod("./themes/".$this->data['id']."", 0777);
									
								// Move uploaded file to upload dir
								move_uploaded_file($this->data["image"]["tmp_name"],$this->data['root_path'].'/once/themes/'.$this->data['id'].'/thumbnail.png');
								
								// Resize image
								$this->once_image_resize($this->data['root_path'].'/once/themes/'.$this->data['id'].'/thumbnail.png',320,240);

								// Set fields to update
								$this->item=array(
									"id" => $this->data['id']
								);
							}else{
								$this->set_error('We only accept images up to 1MB');
							}
						}else{
							$this->set_error('We only accept GIF and JPEG images');
						}
					}else{
						$this->set_error('Extension not allowed');
					}
				}else{
					$this->set_error('Upload error');
				}
			}else{
				$this->set_error('Theme not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	
	
	
	
	
	
	
	
	
	function item_user_buy(){
		if($this->once_csrf_token_check($this->data['csrf_token']) || true){
			// Check if user exist by api_key
			$stmt = $this->pdo->prepare("SELECT id, balance FROM edit_users WHERE id=:user_id LIMIT 1");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR, 255);
			$stmt->execute();

			if($stmt->rowCount()){
				$row=$stmt->fetch(PDO::FETCH_ASSOC);
				// Check if user exist by api_key
				$stmt = $this->pdo->prepare("SELECT id, price FROM edit_themes WHERE id=:theme_id LIMIT 1");
				$stmt->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					$row2=$stmt->fetch(PDO::FETCH_ASSOC);
					// Check if theme is premium else download
					if($row2['price']>0){
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_themes_downloads WHERE user_id=:user_id AND theme_id=:theme_id LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
						$stmt2->execute();

						if($stmt2->rowCount()){
							// Return whole theme
							$this->item=$row2;
						}else{
							if($row['balance']>=$row2['price']){
								// Mark theme unlocked for future use
								$stmt3 = $this->pdo->prepare("INSERT INTO edit_themes_downloads (user_id, theme_id, mktime) VALUES(:user_id, :theme_id, :mktime)");
								$stmt3->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
								$stmt3->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
								$stmt3->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
								$stmt3->execute();
								
								// Update balance
								$stmt4 = $this->pdo->prepare("UPDATE edit_users SET balance=balance-".$row2['price']." WHERE id=".$row['id']."");
								$stmt4->execute();
								
								// Return whole theme
								$this->item=$row2;
							}else{
								$this->set_error('Not enough balance');
							}
						}
					}else{
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_themes_downloads WHERE user_id=:user_id AND user_ip=:user_ip LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
						$stmt2->execute();
						
						if(!$stmt2->rowCount()){
							// Count downloads of theme
							$stmt2 = $this->pdo->prepare("INSERT INTO edit_themes_downloads (user_id, theme_id, mktime, user_ip) VALUES(:user_id, :theme_id, :mktime, :user_ip)");
							$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
							$stmt2->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
							$stmt2->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
							$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
							$stmt2->execute();
						}
						
						// Return whole theme
						$this->item=$row2;
					}
				}else{
					$this->set_error('Theme not exist');
				}
			}else{
				$this->set_error('API not authorized');
			}
		}
		return $this->once_response();
	}
	function item_user_publish(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("SELECT id, version, category_id, name, description, tags, author, author_url FROM edit_themes WHERE id=:id AND user_id=:user_id LIMIT 1");
			$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam('user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

			// Check if theme exist.
			if($this->item){
				// Prepare statements to update theme.
				$stmt = $this->pdo->prepare("UPDATE edit_themes SET object_id=".$this->item['id']." WHERE id=:id AND user_id=:user_id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				$this->set_error('Theme not exist');
			}
		}
		return $this->once_response();
	}
	function item_user_fork(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id, version, category_id, name, description, tags, author, author_url FROM edit_themes WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
				
		// Check if theme exist.
		if($this->item){
			// Prepare statements to insert theme.
			$stmt = $this->pdo->prepare("
				INSERT INTO edit_themes (user_id, category_id, object_id, version, name, tags, description, author, author_url) 
				VALUES (:user_id, :category_id, :object_id, :version, :name, :tags, :description, :author, :author_url)
			");
			$this->item['object_id']=0;
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':category_id', $this->item['category_id'], PDO::PARAM_INT);
			$stmt->bindParam(':object_id', $this->item['object_id'], PDO::PARAM_INT);
			$stmt->bindParam(':version', $this->item['version'], PDO::PARAM_INT);
			$stmt->bindParam(':name', $this->item['name'], PDO::PARAM_STR, 100);
			$stmt->bindParam(':tags', $this->item['tags'], PDO::PARAM_STR, 100);
			$stmt->bindParam(':description', $this->item['description'], PDO::PARAM_STR, 100);
			$stmt->bindParam(':author', $this->item['author'], PDO::PARAM_STR, 100);
			$stmt->bindParam(':author_url', $this->item['author_url'], PDO::PARAM_STR, 100);
			$stmt->execute();
					
			// Get last insert id
			$lastInsertId=$this->pdo->lastInsertId();

			// Make themes dir
			@mkdir($this->data['root_path'].'/once/themes');
			@chmod($this->data['root_path'].'/once/themes', 0777);
				
			// Make theme dir
			@mkdir($this->data['root_path'].'/once/themes/'.$lastInsertId);
			@chmod($this->data['root_path'].'/once/themes/'.$lastInsertId, 0777);


			// Prepare statements to get theme.php template
			$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/theme.php');
			
			// Create preview file
			file_put_contents($this->data['root_path'].'/once/themes/'.$lastInsertId.'/index.php',$tpl['source']);
			
			// Copy theme
			copy($this->data['root_path'].'/once/themes/'.$this->item['id'].'/theme.html',$this->data['root_path'].'/once/themes/'.$lastInsertId.'/theme.html');
			copy($this->data['root_path'].'/once/themes/'.$this->item['id'].'/theme.css',$this->data['root_path'].'/once/themes/'.$lastInsertId.'/theme.css');
			copy($this->data['root_path'].'/once/themes/'.$this->item['id'].'/theme.js',$this->data['root_path'].'/once/themes/'.$lastInsertId.'/theme.js');
			
			// Copy logo
			copy($this->data['root_path'].'/once/themes/'.$this->item['id'].'/thumbnail.png',$this->data['root_path'].'/once/themes/'.$lastInsertId.'/thumbnail.png');

			// Return new item id
			$this->item['id']=$lastInsertId;
		}else{
			$this->set_error('Theme not exist');
		}
		return $this->once_response();
	}
	function item_user_new(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if(!$this->once_creator_check()){
				// @2do dd limits
			}
			
			// Use once to insert empty record
			if($this->once_insert('themes',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			))){
			
				// Make themes dir
				@mkdir('once/themes');
				@chmod('once/themes', 0777);
				
				// Make theme dir
				@mkdir('once/themes/'.$this->item['id']);
				@chmod('once/themes/'.$this->item['id'], 0777);

				// Prepare statements to get theme.php template
				$tpl['source']=@file_get_contents('once/default/theme.php');
				
				// Create preview file
				@file_put_contents('once/themes/'.$this->item['id'].'/index.php',$tpl['source']);

				// Create other default files
				@file_put_contents('once/themes/'.$this->item['id'].'/theme.html','');
				@file_put_contents('once/themes/'.$this->item['id'].'/theme.css','');
				@file_put_contents('once/themes/'.$this->item['id'].'/theme.js','');
			}else{
				$this->set_error('Can not insert item to: themes');
			}
		}
		return $this->once_response();
	}
	function item_user_vote(){
		if($this->data['user_logged']){
			// Prepare statements to get theme.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_themes WHERE id=:id LIMIT 1");
			$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
					
			// Check if theme exist.
			if($this->item){
				// Prepare statements to get vote.
				$stmt = $this->pdo->prepare("SELECT id FROM edit_themes_votes WHERE theme_id=:theme_id AND user_id=:user_id LIMIT 1");
				$stmt->bindParam(':theme_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();

				$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
						
				// Check if theme exist.
				if(!$this->item){
					// Prepare statements to insert vote.
					$stmt = $this->pdo->prepare("
						INSERT INTO edit_themes_votes (theme_id, user_id, mktime) 
						VALUES (:theme_id, :user_id, :mktime)
					");
					$stmt->bindParam(':theme_id', $this->data['id'], PDO::PARAM_INT);
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
					$stmt->execute();
							
					// Prepare statements to update theme.
					$stmt = $this->pdo->prepare("UPDATE edit_themes SET votes=votes+1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}else{
					$this->set_error('Can not vote');
				}
			}else{
				$this->set_error('Theme not exist');
			}
		}else{
			$this->set_error('User not logged');
		}
		return $this->once_response();
	}
	function item_user_download(){
		// Load ZipArchive class to procces download project as zip archive
		if(!extension_loaded('zip')){
			dl('zip.so');
		}

		$zip = new ZipArchive();
		$archiveName = $this->data['root_path']."/theme.zip";

		if ($zip->open($archiveName, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$archiveName>\n");
		}
		
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id, version, category_id, name, description, tags, author, author_url FROM edit_themes WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
				
		// Check if theme exist.
		if($this->item){
			// Prepare index.html
			$str="
			<!DOCTYPE html>
			<html xmlns=\"http://www.w3c.org/1999/xhtml\" xml:lang=\"pl\" lang=\"pl\">
				<head>
					<!-- Latest compiled and minified CSS -->
					<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css\">

					<!-- Optional theme -->
					<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css\">

					<!-- Latest compiled and minified JavaScript -->
					<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js\"></script>
					
					<style>
						".file_get_contents($this->data['root_path'].'/once/themes/'.$this->data['id'].'/theme.css')."
					</style>
					<script>
						".file_get_contents($this->data['root_path'].'/once/themes/'.$this->data['id'].'/theme.js')."
					</script>
				</head>
				<body>
					<div class=\"text-center\" style=\"border-bottom: 1px solid #fefefe; padding: 5px 0 10px 0;\">Theme URL and MIT License: <a href=\"https://oncebuilder.com/themes/".$this->data['id']."\">https://oncebuilder.com/themes/".$this->data['id']."</a></div>
					<div id=\"body\">".file_get_contents($this->data['root_path'].'/once/themes/'.$this->data['id'].'/theme.html')."
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
		}else{
			$this->set_error('Theme not exist');
		}	
		return $this->once_response();
	}
	function item_user_report(){
		// Prepare statements to get theme.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_themes WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
				
		// Check if theme exist.
		if($this->item){
			// Prepare statements to get vote.
			$stmt = $this->pdo->prepare("SELECT id, star FROM edit_themes_reports WHERE theme_id=:theme_id AND user_id=:user_id LIMIT 1");
			$stmt->bindParam(':theme_id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
					
			// Check if theme exist.
			if(!$this->item['star']){
				// Prepare statements to insert vote.
				$stmt = $this->pdo->prepare("
					INSERT INTO edit_themes_reports (theme_id, user_id, mktime, message) 
					VALUES (:theme_id, :user_id, :mktime, :message)
				");
				$stmt->bindParam(':theme_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
				$stmt->bindParam(':message', $this->data['message'], PDO::PARAM_STR. 250);
				$stmt->execute();
						
				// Prepare statements to update theme.
				$stmt = $this->pdo->prepare("UPDATE edit_themes SET reports=reports+1 WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
			$this->set_error('Can not report');
			}
		}else{
			$this->set_error('Theme not exist');
		}
		return $this->once_response();
	}
}
?>