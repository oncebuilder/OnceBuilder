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
	function api_search(){//ok
		// Check if user exist by api_key
		$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE api_key=:api_key LIMIT 1");
		$stmt->bindParam(':api_key', $this->data['api'], PDO::PARAM_STR, 255);
		$stmt->execute();

		if($stmt->rowCount()){
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
				$row=$stmt->fetch(PDO::FETCH_ASSOC);

				// Check if item exist
				if($row['id']){
					$_GET['category_id']=$row['id'];
				}
			}

			# SET DATA -------------------
			$this->set_data(array(
				"type_id" => intval($_GET['type_id']),
				"category_id" => intval($_GET['category_id']),
				"page" => intval($_GET['page']),
				"sort_by" => $data_sort[$_GET['sort_by']],
				"ids" => $_GET['idsx'],
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
	function export_config($theme_id){//ok
		$stmt = $this->pdo->prepare("SHOW TABLES");
		$stmt->execute();
					
		// Get count of returned records
		$obj['count']=$stmt->rowCount();
		if($obj['count']){
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
	function import_config($theme_id){//ok
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
		
		// Import layers
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
	function set_limit(){//ok
		// Update page limit with once
		$this->once_page_limit('themes');
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
	function twitter_publish($obj){//ok??
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

			$url='http://oncebuilder.com/theme/'.$this->data['id'];
			
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
			$message=$obj['item']['name'].' '.$url.' '.($obj['item']['author']!=''?' by @'.$obj['item']['author']:'');

			$parameters = array('status' => $message, 'media_ids' => $media1->media_id_string);
			$status = $connection->post('statuses/update', $parameters);
		}
	}
	function update_resource(){//ok
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

	function item_approve(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Prepare statements to get selected data
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Get count of returned records
				$obj['count']=$stmt->rowCount();
				if($obj['count']){
					// Data as item
					$obj['item']=$stmt->fetch(PDO::FETCH_ASSOC);

					// Check if its published/unpublished then unpublish/publish
					if($obj['item']['published']==0){
						//2do Check if it was already published on twitter then publish
						if(true){
							$this->twitter_publish($obj);
						}
						
						// Set as published
						$stmt = $this->pdo->prepare("UPDATE edit_themes SET published=1 WHERE id=:id LIMIT 1");
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();

						// Set count for user when added
						$this->data['user_id']=$obj['item']['user_id'];
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

							$obj['status']='ok';
						}else{
							$obj['errors'][]='You don\'t have access to .zip file';
							$obj['error']++;
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
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Theme doesn\'t exist';
					$obj['error']++;
				}
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
	function item_delete(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
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
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
				// Use once to delete item
				$obj=$this->once_delete_item('themes');
				
				if($obj['count']){
					$this->recurse_delete($this->data['root_path'].'/once/themes/'.$this->data['id'].'');
				}
				//@2do delete all from db
			}else{
				$obj['errors'][]='Theme not exist / for selected user';
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
	function item_edit(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
				
			// Get count of returned records
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
				$obj['item']=$stmt->fetch(PDO::FETCH_ASSOC);

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
				
				if($stmt->rowCount()){
					// Set status ok
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Cant save';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='Theme not exist';
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
	function item_download(){//ok
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
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
			

			// Use once to insert empty theme
			$objx=$this->once_insert('themes',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			));
		
			$stmt = $this->pdo->prepare("UPDATE edit_themes SET name=:name WHERE id=:id LIMIT 1");
			$stmt->bindParam(':name', $obj['item']['name'], PDO::PARAM_STR, 255);
			$stmt->bindParam(':id', $objx['item']['id'], PDO::PARAM_INT);
			$stmt->execute();
			
			if($objx['count']){
				// Make theme dir
				@mkdir("./themes");
				@chmod("./themes", 0777);
				@mkdir("./themes/".$objx['item']['id']);
				@chmod("./themes/".$objx['item']['id'], 0777);
			}
			
			//DOWNLOAD THEME FROM UNIQUE URL AND UNPACK
			$file=@file_get_contents('http://oncebuilder.com/once/themes/'.$obj['item']['id'].'/theme.zip');
			
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
					$obj['status']='ok';
				}else{
					$obj['errors'][]='You don\'t have access to config file';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='You don\'t have access to .zip file';
				$obj['error']++;
			}
			//return $obj;
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
							$obj['item']=$row2;
							$obj['status']='ok';
						}else{
							if($row['balance']>$row2['price']){
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
								$obj['item']=$row2;
								$obj['status']='ok';
							}else{
								$obj['error']='Not enough balance';
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
						$obj['item']=$row2;
						$obj['status']='ok';
					}
				}else{
					$obj['errors'][]='Theme not exist';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='API not authorized';
				$obj['error']++;
			}
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
	function item_export(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
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
				
				$obj['status']='ok';
				echo json_encode($obj);
			}else{
				$obj['errors'][]='Plugin not exist / for selected user';
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
	function item_import(){//ok
		// Check if url field is set then download else handle file
		if($this->once_creator_check()){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				if($this->data['url']){
					//2do
				}else{
					// Get file source from upload
					// Use once to insert empty
					$obj=$this->once_insert('themes',array(
						"id" => '',
						"user_id"=> $this->data['user_id']
					));

					// Make dir for new theme
					@mkdir("./themes/".$obj['item']['id']."");
					@chmod("./themes/".$obj['item']['id']."", 0777);
					
					move_uploaded_file($this->data["file"]["tmp_name"],'./themes/'.$obj['item']['id'].'/theme.zip');

					$zip = new ZipArchive;
					$res = $zip->open('./themes/'.$obj['item']['id'].'/theme.zip');
					if($res === TRUE){
						$zip->extractTo('./themes/'.$obj['item']['id'].'/');
						$zip->close();
						
						// Import conifg
						$this->import_config($obj['item']['id']);

						$obj['status']='ok';
					}else{
						$obj['errors'][]='You don\'t have access to .zip file';
						$obj['error']++;
					}
					
					@unlink('./themes/'.$obj['item']['id'].'/theme.zip');
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
	function item_new(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Use once to insert empty record
			$obj=$this->once_insert('themes',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			));
			
			if($obj['count']){
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
				$stmt->bindParam(':id', $obj['item']['id'], PDO::PARAM_INT);
				$stmt->execute();

				if($obj['count']){
					$this->data['project_id']=$_SESSION['project_id']=$obj['item']['id'];
				}
				
				// Make dirs for new theme
				@mkdir($this->data['root_path'].'/once/themes/'.$obj['item']['id']);
				@chmod($this->data['root_path'].'/once/themes/'.$obj['item']['id'], 0777);
				@mkdir($this->data['root_path'].'/once/themes/'.$obj['item']['id'].'/images');
				@chmod($this->data['root_path'].'/once/themes/'.$obj['item']['id'].'/images', 0777);

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
				
				// Set status ok
				$obj['status']='ok';
			}else{
				// Return error if item not created
				$obj['errors'][]='can not insert item to: theme';
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
	function item_preview(){//ok
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
					$obj['item']=$stmt->fetch(PDO::FETCH_ASSOC);
					
					if($obj['item']['price']>0){
						// Check if user bought this theme
						$stmt = $this->pdo->prepare("SELECT COUNT(*) AS ile FROM edit_themes_downloads WHERE user_id=:user_id AND theme_id=:theme_id LIMIT 1");
						$stmt->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt->bindParam(':theme_id', $this->data['theme_id'], PDO::PARAM_INT);
						$stmt->execute();
						
						if($stmt->fetchColumn() > 0) {
							$obj['item']['bought']=true;
						}
					}
					
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Theme not exist';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='API not authorized';
				$obj['error']++;
			}
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
	function item_publish(){//ok??? .'..
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("SELECT id, version, name, description, tags, author, author_url FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();

				$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
					
				// Check if theme exist.
				if($obj['item']){
					// Update file if not exist
					if(!file_exists($this->data['root_path'].'/once/themes/'.$obj['item']['id'].'/thumbnail.png')){
						file_put_contents($this->data['root_path'].'/once/themes/'.$obj['item']['id'].'/thumbnail.png','');
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
						//curl_file_create(realpath('themes/'.$obj['item']['id'].'/theme.zip'))
						
						$file = new CURLFile(realpath('themes/'.$obj['item']['id'].'/theme.zip'));
						$file->setPostFilename('theme.zip');
						
						$thumbnail = new CURLFile(realpath('themes/'.$obj['item']['id'].'/thumbnail.png'));
						$thumbnail->setPostFilename('thumbnail.png');
					}else{
						$file = '@' . realpath($this->data['root_path'].'/once/themes/'.$obj['item']['id'].'/theme.zip');
						$thumbnail = '@' . realpath($this->data['root_path'].'/once/themes/'.$obj['item']['id'].'/thumbnail.png');
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
							'object_id' => $obj['item']['id'],
							'version' => $obj['item']['version'],
							'price' => $obj['item']['price'],
							'name' => $obj['item']['name'],
							'tags' => $obj['item']['tags'],
							'author' => $obj['item']['author'],
							'author_url' => $obj['item']['author_url'],
							'description' => $obj['item']['description'],
							'licence' => $obj['item']['licence'],
							'message' => $this->data['message']
						)
					);

					// Get response
					$response = curl_exec($ch);

					if(strpos($response, '"status":"ok"')){
						// Prepare set as published
						$stmt = $this->pdo->prepare("UPDATE edit_themes SET object_id=:object_id WHERE id=:id LIMIT 1");
						$stmt->bindParam(':object_id', $this->data['id'], PDO::PARAM_INT);
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
						$obj['status']='ok';
					}else{
						$obj['error']='Upload API error';
					}

					curl_close($ch);
					
					// Delete file when its done.
					@unlink($archiveName);
				}else{
					$obj['error']='Theme doesn\'t exist';
				}
			}else{
				$obj['errors'][]='CSFR token invalid!';
				$obj['error']++;
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
					if($this->data["file"]["error"] > 0){
						$obj['error']='error file upload';
					}else{
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
						
						$obj['status']='ok';
					}
				}else{
					$obj['error']='API key not found';
				}
			}else{
				$obj['errors'][]='Object missed!';
				$obj['error']++;
			}
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
	function item_use(){//ok
		// Check if item exists
		$obj=$this->once_select_item('themes');

		if($obj['count']){
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
			
			if($stmt->rowCount()){
				// Set status ok
				$obj['status']='ok';
			}else{
				$obj['errors'][]='Cant save';
				$obj['error']++;
			}

			// Set fields to update
			$this->data['item']=array(
				"default" => 1
			);
			
			$stmt = $this->pdo->prepare("UPDATE edit_themes SET `default`=1 WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();

			if($obj['count']){
				$this->data['project_id']=$_SESSION['project_id']=$this->data['id'];
			}
		}
		
		// Print JSON object
		echo json_encode($obj);
	}
	function item_star(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			// Get count of returned records
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
				$once['item']=$stmt->fetch(PDO::FETCH_ASSOC);
				// Check if its stared/unstared then unstar/star
				if($once['item']['stared']==1){
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
				$obj['status']='ok';
			}else{
				$obj['errors'][]='Theme doesn\'t exist';
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

	function delete_image(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
				
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
				if($this->data['project_id']==$this->data['id']){
					@unlink('../images/ss'.$this->data['currentImage'].'.png');
				}

				@unlink('./themes/'.$this->data['id'].'/images/ss'.$this->data['currentImage'].'.png');

				$obj['status']='ok';
			}else{
				$obj['errors'][]='Theme not exist / for selected user';
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
	function upload_image(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
				
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
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
									$obj['item']=array(
										"id" => $this->data['id'],
										"currentImage" => $ok
									);
									
									$obj['status']='ok';
								}else{
									$obj['errors'][]='LIMIT 9';
									$obj['error']++;
								}
							}else{
								$obj['errors'][]='We only accept images up to 1MB';
								$obj['error']++;
							}
						}else{
							$obj['errors'][]='We only accept GIF and JPEG images';
							$obj['error']++;
						}
					}else{
						$obj['errors'][]='Extension not allowed';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Upload error';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='Theme not exist / for selected user';
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
	function upload_thumbnail(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
				
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_themes WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
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
								$this->once_image_resize($this->data['root_path'].'/once/themes/'.$this->data['id'].'/thumbnail.png',170,170);

								// Set fields to update
								$obj['item']=array(
									"id" => $this->data['id']
								);
								
								$obj['status']='ok';
							}else{
								$obj['errors'][]='We only accept images up to 1MB';
								$obj['error']++;
							}
						}else{
							$obj['errors'][]='We only accept GIF and JPEG images';
							$obj['error']++;
						}
					}else{
						$obj['errors'][]='Extension not allowed';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='Upload error';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='Plugin not exist / for selected user';
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
	
	//2do
	function item_user_report(){
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=themes&o=item_report");
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
		}else{
			// Check if api key exist
			$result=mysql_query("SELECT id FROM edit_users WHERE api_key='".$this->data['api']."'");
			if(mysql_num_rows($result)){
				$row=mysql_fetch_array($result);
				// Check if theme exist
				$result2=mysql_query("SELECT id FROM edit_themes WHERE id='".$this->data['theme_id']."' LIMIT 1");
				if(mysql_num_rows($result2)){
					// Check if user already reported this theme
					$result3=mysql_query("SELECT id FROM edit_themes_reports WHERE theme_id='".$this->data['theme_id']."' AND user_id='".$row['id']."' AND status=0 LIMIT 1");
					if(!mysql_num_rows($result3)){
						// Mark theme has voted
						mysql_query("INSERT INTO edit_themes_reports (theme_id, user_id, mktime) VALUES('".$this->data['theme_id']."', '".$row['id']."', '".$this->data['time']."')");
						$obj['status']='ok';
					}else{
						$obj['error']='couldn\'t report';
					}
				}else{
					$obj['error']='theme doesnt exists';
				}
			}else{
				$obj['error']='api key doesn\'t exists';
			}
		}
		// Print JSON object
		echo json_encode($obj);
	}
	function item_user_vote(){
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=themes&o=item_vote");
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
		}else{
			// Check if api key exist
			$result=mysql_query("SELECT id FROM edit_users WHERE api_key='".$this->data['api']."'");
			if(mysql_num_rows($result)){
				$row=mysql_fetch_array($result);
				// Check if theme exist
				$result2=mysql_query("SELECT id FROM edit_themes WHERE id='".$this->data['theme_id']."' LIMIT 1");
				if(mysql_num_rows($result2)){
					// Check if user already voted this theme
					$result3=mysql_query("SELECT id FROM edit_themes_votes WHERE theme_id='".$this->data['theme_id']."' AND user_id='".$row['id']."' LIMIT 1");
					if(!mysql_num_rows($result3)){
						// Mark theme has voted
						mysql_query("INSERT INTO edit_themes_votes (theme_id, user_id, mktime) VALUES('".$this->data['theme_id']."', '".$row['id']."', '".$this->data['time']."')");
						// Update stats
						mysql_query("UPDATE edit_themes SET vote=vote+1 WHERE id='".$this->data['id']."'");
						$obj['status']='ok';
					}else{
						$obj['error']='couldn\'t vote';
					}
				}else{
					$obj['error']='theme doesnt exists';
				}
			}else{
				$obj['error']='api key doesn\'t exists';
			}
		}
		// Print JSON object
		echo json_encode($obj);
	}
}
?>