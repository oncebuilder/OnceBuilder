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
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins_categories WHERE LOWER(name) LIKE :category");
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
			$obj['categories']=$this->category_get('plugins');

			# GET DATA -------------------
			$obj['data']=$this->once_select_items_page('plugins');
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
	function bulk_action(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Prepare statements to star selected id.
			$stmt1 = $this->pdo->prepare("UPDATE edit_plugins SET stared = 1 WHERE id=:id");

			// Prepare statements to unstar selected id.
			$stmt2 = $this->pdo->prepare("UPDATE edit_plugins SET stared = 0 WHERE id=:id");

			// Prepare statements to delete selected id.
			$stmt3 = $this->pdo->prepare("DELETE FROM edit_plugins WHERE id=:id");

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
						$this->recurse_delete($this->data['root_path'].'/once/plugins/'.$position.'');
					}
				}
			}
		}
		return $this->once_response();
	}
	function export_config($plugin_id){
		$stmt = $this->pdo->prepare("SHOW TABLES");
		$stmt->execute();

		// Get count of returned records
		if($stmt->rowCount()){
			// Export config & categories
			$stmt1 = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=".$plugin_id."");
			$stmt1->execute();

			$dump['edit_plugins']=$stmt1->fetch(PDO::FETCH_ASSOC);
		}

		return $dump;
	}
	function import_config($plugin_id){
		// install db from file
		$config = unserialize(@file_get_contents($this->data['root_path'].'/once/plugins/'.$plugin_id.'/once.config'));

		// Prepare statements to create selected plugin.
		$stmt = $this->pdo->prepare("
			UPDATE edit_plugins 
			SET name=:name, description=:description, tags=:tags, author=:author, author_url=:author_url, price=:price
			WHERE id=:id LIMIT 1
		");
		$stmt->bindParam(':id', $plugin_id, PDO::PARAM_INT);
		$stmt->bindParam(':name', $config['edit_plugins']['name'], PDO::PARAM_STR, 55);
		$stmt->bindParam(':description', $config['edit_plugins']['description'], PDO::PARAM_STR, 255);
		$stmt->bindParam(':tags', $config['edit_plugins']['tags'], PDO::PARAM_STR, 255);
		$stmt->bindParam(':author', $config['edit_plugins']['author'], PDO::PARAM_STR, 255);
		$stmt->bindParam(':author_url', $config['edit_plugins']['author_url'], PDO::PARAM_STR, 255);
		$stmt->bindParam(':price', $config['edit_plugins']['price'], PDO::PARAM_INT);
		$stmt->execute();
	}
	function set_limit(){
		// Update page limit with once
		$this->once_page_limit('plugins');
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
			$file = file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/thumbnail.png');
			$base = base64_encode($file);
			$media1 = $connection->upload('media/upload', array('media' => array('media' => $base)));

			// Short link before publish
			require_once($this->data['root_path'].'/once/libs/bitlyphp/bitly.php');

			$url='http://oncebuilder.com/plugin/'.$this->data['id'];
			
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
		$source=$this->data['root_path'].'/once/plugins/'.$this->data['id'];

		$zip = new ZipArchive();
		$archiveName = $source."/plugin.zip";

		@unlink($archiveName);
		if ($zip->open($archiveName, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$archiveName>\n");
		}
	
		// check if plugin exist
		if(file_exists($source)){
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($files as $file){
				$file = str_replace('\\', '/', $file);
					if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) ) continue;
					if(!strpos($file, '/.git') && !strpos($file, '/plugin.zip')){
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
			$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();
			
			if($stmt->rowCount()){
				// Data as item
				$this->item=$stmt->fetch(PDO::FETCH_ASSOC);

				// Check if its published/unpublished then unpublish/publish
				if($this->item['published']==0){
					//@2do Check if it was already published on twitter then publish
					if(true){
						$this->twitter_publish();
					}
						
					// Set as published
					$stmt = $this->pdo->prepare("UPDATE edit_plugins SET published=1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();

					// Set count for user when added
					$this->data['user_id']=$this->item['user_id'];
					$this->once_set_user_count('plugins');
						
					// File expception
					$except = array('thumbnail.png');

					// Unzip
					$zip = new ZipArchive;
					$res = $zip->open($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.zip');
					if($res === TRUE){
						// list of all the files in zip
						$files = array();
						for ($idx = 0; $idx < $zip->numFiles; $idx++) {
							if(!in_array($zip->getNameIndex($idx),$except)) $files[] = $zip->getNameIndex($idx);
						}

						// only extract the remaining $files
						$zip->extractTo($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/', $files);

						$zip->close();

						// Import conifg
						$this->import_config($this->data['id']);
					}else{
						$this->set_error('No access to .zip file');
					}
				}else{
					// Prepare statements to publish selected data
					$stmt = $this->pdo->prepare("UPDATE edit_plugins SET published=0 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();

					// File expception
					$except = array('thumbnail.png','plugin.zip');

					// Update zip before send
					$this->update_resource();
						
					// Delete all files except images/ss , thumbnail and plugin zip
					$path=$this->data['root_path']."/once/plugins/".$this->data['id']."";
					if ($handle = opendir($path)) {
						while(false !== ($file = readdir($handle))) {
							if($file == "." || $file == ".." || in_array($file,$except))  continue;
							if(@filetype($path."/".$file) != "dir") {
								if($file){
									@unlink($this->data['root_path']."/once/plugins/".$this->data['id']."/".$file);
								}
							}
							if(@filetype($path."/".$file) == "dir" && $file!='once') {
								$this->recurse_delete($path."/".$file,$this->data['root_path']."/once/plugins/".$this->data['id']."/".$file);
							}
						}
						closedir($handle);
					}
				}
			}else{
				$this->set_error('Plugin not exist');
			}
		}
		return $this->once_response();
	}
	function item_delete(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			if($stmt->rowCount()){
				// Use once to delete item
				if($this->once_delete_item('plugins')){
					$this->recurse_delete($this->data['root_path'].'/once/plugins/'.$this->data['id'].'');
				}
			}else{
				$this->set_error('Plugin not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	function item_edit(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check(true)){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			// Get count of returned records
			if($stmt->rowCount()){
				$this->item=$stmt->fetch(PDO::FETCH_ASSOC);

				// Prepare statements to update plugin.
				$stmt = $this->pdo->prepare("UPDATE edit_plugins SET category_id=:category_id, version=:version, price=:price, name=:name, tags=:tags, author=:author, author_url=:author_url, description=:description WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':category_id', $this->data['category_id'], PDO::PARAM_INT);
				$stmt->bindParam(':version', $this->data['version'], PDO::PARAM_STR, 10);
				$stmt->bindParam(':price', $this->data['price'], PDO::PARAM_INT);
				$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 55);
				$stmt->bindParam(':tags', $this->data['tags'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':author', $this->data['author'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':author_url', $this->data['author_url'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':description', $this->data['description'], PDO::PARAM_STR, 255);
				$stmt->execute();

				if(!$stmt->rowCount()){
					$this->set_error('Can not save');
				}
			}else{
				$this->set_error('Plugin not exist');
			}
		}
		return $this->once_response();
	}
	function item_download(){
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=plugins&o=item_download");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "api=".$this->data['api_key']."&plugin_id=".$this->data['id']); //dane do wyslania
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				$response = curl_exec($ch);
				curl_close($ch);
				// Decode json
				$obj=json_decode($response, true);
				$this->item=$obj['item'];
				
				// Use once to insert empty plugin
				$objx=$this->once_insert('plugins',array(
					"id" => '',
					"name" => $this->item['name'],
					"user_id"=> $this->data['user_id']
				));
				
				if($objx){
					// Make plugin dir
					@chmod($this->data['root_path']."/once/plugins", 0777);
					@mkdir($this->data['root_path']."/once/plugins/".$objx['item']['id']);
					@chmod($this->data['root_path']."/once/plugins/".$objx['item']['id'], 0777);
				}

				//DOWNLOAD THEME FROM UNIQUE URL AND UNPACK
				$file=@file_get_contents('http://oncebuilder.com/once/plugins/'.$this->item['id'].'/plugin.zip');
				
				@file_put_contents($this->data['root_path']."/once/plugins/".$objx['item']['id']."/plugin.zip",$file);

				// Unpack file
				$zip = new ZipArchive;
				$res = $zip->open($this->data['root_path'].'/once/plugins/'.$objx['item']['id'].'/plugin.zip');
				if($res === TRUE){
					$zip->extractTo($this->data['root_path'].'/once/plugins/'.$objx['item']['id'].'/');
					$zip->close();

					if(file_exists($this->data['root_path'].'/once/plugins/'.$objx['item']['id'].'/once.config')) {
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
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:plugin_id LIMIT 1");
				$stmt->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					$row2=$stmt->fetch(PDO::FETCH_ASSOC);
					// Check if plugin is premium else download
					if($row2['price']>0){
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_plugins_downloads WHERE user_id=:user_id AND plugin_id=:plugin_id LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
						$stmt2->execute();

						if($stmt2->rowCount()){
							// Return whole plugin
							$this->item=$row2;
						}else{
							if($row['balance']>=$row2['price']){
								// Mark plugin unlocked for future use
								$stmt3 = $this->pdo->prepare("INSERT INTO edit_plugins_downloads (user_id, plugin_id, mktime) VALUES(:user_id, :plugin_id, :mktime)");
								$stmt3->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
								$stmt3->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
								$stmt3->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
								$stmt3->execute();

								// Update balance
								$stmt4 = $this->pdo->prepare("UPDATE edit_users WHERE balance=balance-'".$row2['price']."'");
								$stmt4->execute();

								// Return whole plugin
								$this->item=$row2;
							}else{
								$this->set_error('Not enough balance');
							}
						}
					}else{
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_plugins_downloads WHERE user_id=:user_id AND user_ip=:user_ip LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
						$stmt2->execute();

						if(!$stmt2->rowCount()){
							// Count downloads of plugin
							$stmt2 = $this->pdo->prepare("INSERT INTO edit_plugins_downloads (user_id, plugin_id, mktime, user_ip) VALUES(:user_id, :plugin_id, :mktime, :user_ip)");
							$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
							$stmt2->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
							$stmt2->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
							$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
							$stmt2->execute();
						}

						// Return whole plugin
						$this->item=$row2;
					}
				}else{
					$this->set_error('Plugin not exist');
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
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			if($stmt->rowCount()){
				// Export all files of plugin
				$archiveName=$this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.zip';
					
				if($this->item['published']==1){
					// Update zip before send
					$this->update_resource();
				}
				
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
				$this->set_error('Plugin not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	function item_import(){
		// Check if url field is set then download else handle file
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Use once to insert empty record
			if($this->once_insert('plugins',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			))){
				// Make plugin dir
				@mkdir($this->data['root_path'].'/once/plugins');
				@chmod($this->data['root_path'].'/once/plugins', 0777);

				// Make plugin dir
				@mkdir($this->data['root_path'].'/once/plugins/'.$this->item['id']);
				@chmod($this->data['root_path'].'/once/plugins/'.$this->item['id'], 0777);

				if($this->data['url']){
					if (!preg_match("~^(?:f|ht)tps?://~i", $this->data['url'])) {
						$this->data['url'] = "http://" . $this->data['url'];
					}
					$headers=get_headers($this->data['url'],1);
					if($headers['Content-Type']=='application/zip'){
						// Get file source from url
						file_put_contents($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.zip',file_get_contents($this->data['url']));
					}else{
						$this->set_error('URL must contain .zip file');
					}
				}else{
					move_uploaded_file($this->data["file"]["tmp_name"],$this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.zip');
				}

				$zip = new ZipArchive;
				$res = $zip->open($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.zip');
				if($res === TRUE){
					$zip->extractTo($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/');
					$zip->close();

					// Import conifg
					$this->import_config($this->item['id']);
				}else{
					$this->set_error('No access to .zip file');
				}
				//@2do @unlink($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.zip');
			}else{
				$this->set_error('Can not insert item to: plugins');
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
			if($this->once_insert('plugins',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			))){
				// Make plugins dir
				@mkdir($this->data['root_path'].'/once/plugins');
				@chmod($this->data['root_path'].'/once/plugins', 0777);

				// Make plugin dir
				@mkdir($this->data['root_path'].'/once/plugins/'.$this->item['id']);
				@chmod($this->data['root_path'].'/once/plugins/'.$this->item['id'], 0777);
				
				// Make plugin image dir
				@mkdir($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/images');
				@chmod($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/images', 0777);

				// Prepare statements to get plugin.php template
				$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/plugin.php');

				// Create preview file
				file_put_contents($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/index.php',$tpl['source']);

				// Create other default files
				file_put_contents($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/ui.php','');
				file_put_contents($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.php','');
				file_put_contents($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.css','');
				file_put_contents($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.js','');
				file_put_contents($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/dependencies.html','');
			}else{
				$this->set_error('Can not insert item to: plugins');
			}
		}
		return $this->once_response();
	}
	function item_preview(){
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=plugins&o=item_preview");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "api=".$this->data['api_key']."&plugin_id=".$this->data['id']); //dane do wyslania
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
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:plugin_id LIMIT 1");
				$stmt->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					$this->item=$stmt->fetch(PDO::FETCH_ASSOC);

					if($this->item['price']>0){
						// Check if user bought this plugin
						$stmt = $this->pdo->prepare("SELECT COUNT(*) AS ile FROM edit_plugins_downloads WHERE user_id=:user_id AND plugin_id=:plugin_id LIMIT 1");
						$stmt->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
						$stmt->execute();

						if($stmt->fetchColumn() > 0) {
							$this->item['bought']=true;
						}
					}

					if($this->item['price']==0 || $this->item['bought']){
						// Return plugin source
						$this->item['source_ui']=@file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['plugin_id'].'/ui.php');
						$this->item['source_php']=@file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['plugin_id'].'/plugin.php');
						$this->item['source_css']=@file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['plugin_id'].'/plugin.css');
						$this->item['source_js']=@file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['plugin_id'].'/plugin.js');
					}
					
					for($i=1;$i<10;$i++){
						if(file_exists($this->data['root_path'].'/once/plugins/'.$this->data['plugin_id'].'/images/ss'.$i.'.png')){
							$this->item['images'][]="ss".$i.".png";
						}
					}
				}else{
					$this->set_error('Plugin not exist');
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
				$stmt = $this->pdo->prepare("SELECT id, version, price, name, description, tags, author, author_url FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();

				$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
				
				// Check if plugin exist.
				if($this->item){
					// Update file if not exist
					if(!file_exists($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/thumbnail.png')){
						file_put_contents($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/thumbnail.png','');
					}
					
					// Update zip before send
					$this->update_resource();
					
					// Set CURL
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=plugins&o=item_publish");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					
					// Add files depends on version PHP
					$version = explode('.', phpversion());
					if($version[0]>=5 && $version[1]>=5){
						//curl_file_create(realpath('plugins/'.$this->item['id'].'/plugin.zip'))
						
						$file = new CURLFile(realpath('plugins/'.$this->item['id'].'/plugin.zip'));
						$file->setPostFilename('plugin.zip');
						
						$thumbnail = new CURLFile(realpath('plugins/'.$this->item['id'].'/thumbnail.png'));
						$thumbnail->setPostFilename('thumbnail.png');
					}else{
						$file = '@' . realpath($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.zip');
						$thumbnail = '@' . realpath($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/thumbnail.png');
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
							'message' => $this->data['message']
						)
					);

					// Get response
					$response = curl_exec($ch);
					$obj=json_decode($response, true);
					$this->item=$obj['item'];
					
					if(strpos($response, '"status":"ok"')){
						// Prepare set as published
						$stmt = $this->pdo->prepare("UPDATE edit_plugins SET object_id=:object_id WHERE id=:id LIMIT 1");
						$stmt->bindParam(':object_id', $this->data['id'], PDO::PARAM_INT);
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
					}else{
						$this->set_error('Upload API error');
					}
					curl_close($ch);
				}else{
					$this->set_error('Plugin does not exist');
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
					if(!$this->data["file"]["error"]){
						// Insert new plugin
						$stmt = $this->pdo->prepare("
							INSERT INTO edit_plugins (user_id, object_id, version, price, name, tags, author, author_url, description, created)
							VALUES (:user_id, :object_id, :version, :price, :name, :tags, :author, :author_url, :description, :created)
						");

						$stmt->bindParam(':user_id', $obj['user']['id'], PDO::PARAM_INT);
						$stmt->bindParam(':object_id', $this->data['object_id'], PDO::PARAM_INT);
						$stmt->bindParam(':version', $this->data['version'], PDO::PARAM_INT);
						$stmt->bindParam(':price', $this->data['price'], PDO::PARAM_INT);
						$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':tags', $this->data['tags'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':author', $this->data['author'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':author_url', $this->data['author_url'], PDO::PARAM_STR, 100);
						$stmt->bindParam(':description', $this->data['description'], PDO::PARAM_STR, 100);						
						$stmt->bindParam(':created', $this->data['time'], PDO::PARAM_INT);
						$stmt->execute();

						// Get last insert id
						$lastInsertId=$this->pdo->lastInsertId();

						// Insert message
						if($this->data['message']!=''){
							$stmt = $this->pdo->prepare("
								INSERT INTO edit_plugins_reports (user_id, plugin_id, mktime, message)
								VALUES (:user_id, :plugin_id, :mktime, :message)
							");
							$stmt->bindParam(':user_id', $obj['user']['id'], PDO::PARAM_INT);
							$stmt->bindParam(':plugin_id', $lastInsertId, PDO::PARAM_INT);
							$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
							$stmt->bindParam(':message', $this->data['message'], PDO::PARAM_STR, 255);
							$stmt->execute();
						}

						// Make dirs and generate files
						@mkdir($this->data['root_path']."/once/plugins/".$lastInsertId."");
						@chmod($this->data['root_path']."/once/plugins/".$lastInsertId."", 0777);

						// Upload file to folder
						move_uploaded_file($this->data["file"]["tmp_name"],$this->data['root_path'].'/once/plugins/'.$lastInsertId.'/plugin.zip');

						// Move uploaded thumbnail to plugin dir
						move_uploaded_file($this->data["thumbnail"]["tmp_name"],$this->data['root_path'].'/once/plugins/'.$lastInsertId.'/thumbnail.png');

						//Resize image if its 320x240 if it's larger
						$this->once_image_resize($this->data['root_path'].'/once/plugins/'.$lastInsertId.'/thumbnail.png',320,240);

						// Make dirs and generate files
						@mkdir($this->data['root_path']."/once/plugins/".$lastInsertId."/images");
						@chmod($this->data['root_path']."/once/plugins/".$lastInsertId."/images", 0777);

						// Unzip only images
						$zip = new ZipArchive;
						$res = $zip->open($this->data['root_path'].'/once/plugins/'.$lastInsertId.'/plugin.zip');
						if($res === TRUE){
							$zip->extractTo($this->data['root_path'].'/once/plugins/'.$lastInsertId.'/', array('images/ss1.png','images/ss2.png','images/ss3.png','images/ss4.png','images/ss5.png','images/ss6.png','images/ss7.png','images/ss8.png','images/ss9.png'));
							$zip->close();
						}

						// Resample images
						for($i=1;$i<10;$i++){
							if(file_exists($this->data['root_path'].'/once/plugins/'.$lastInsertId.'/images/ss'.$i.'.png')) {
								$this->once_image_resample($this->data['root_path'].'/once/plugins/'.$lastInsertId.'/images/ss'.$i.'.png');
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
	function item_star(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
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
					$stmt = $this->pdo->prepare("UPDATE edit_plugins SET stared=0 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}else{
					// Prepare statements to star selected data
					$stmt = $this->pdo->prepare("UPDATE edit_plugins SET stared=1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}
			}else{
				$this->set_error('Plugin not exist');
			}
		}
		return $this->once_response();
	}

	function load_source(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			// Check if plugin exist then open source file
			if($stmt->rowCount()){
				$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

				// Open selected file
				if($this->data['file']=='ui.php'){
					$this->item['source']=@file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/ui.php');
				}else if($this->data['file']=='plugin.php'){
					$this->item['source']=@file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.php');
				}else if($this->data['file']=='plugin.css'){
					$this->item['source']=@file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.css');
				}else if($this->data['file']=='plugin.js'){
					$this->item['source']=@file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.js');
				}else if($this->data['file']=='dependencies.html'){
					$this->item['source']=@file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/dependencies.html');
				}else{
					$this->set_error('Can not load file');
				}

				if(!$this->item['source']){
					$this->item['source']='';
				}
			}else{
				$this->set_error('Plugin not exist / for selected user');
			}
		}
		return $this->once_response();
	}
 	function save_source(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			// Check if plugin exist then save source to file
			if($stmt->rowCount()){
				$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

				// Save file with content
				if($this->data['file']=='ui.php'){
					$this->item['source']=@file_put_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/ui.php',$this->data['source']);
				}else if($this->data['file']=='plugin.php'){
					$this->item['source']=@file_put_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.php',$this->data['source']);
				}else if($this->data['file']=='plugin.css'){
					$this->item['source']=@file_put_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.css',$this->data['source']);
				}else if($this->data['file']=='plugin.js'){
					$this->item['source']=@file_put_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.js',$this->data['source']);
				}else if($this->data['file']=='dependencies.html'){
					$this->item['source']=@file_put_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/dependencies.html',$this->data['source']);
				}else{
					$this->set_error('Can not load this file');
				}

				//@2do Update archive

				if(!$this->item['source']){
					$this->set_error('Source could not be saved');
				}
			}else{
				$this->set_error('Plugin not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	function delete_image(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			// Get count of returned records
			if($stmt->rowCount()){
				@unlink($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/images/ss'.$this->data['currentImage'].'.png');
			}else{
				$this->set_error('Plugin not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	function upload_image(){	
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
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
								@mkdir($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/images');
				
								if($this->data['currentImage']==0){
									$ok=0;
									for($i=1;$i<10;$i++){
										if(!file_exists("plugins/".$this->data['id']."/images/ss".$i.".png")){
											$this->data['currentImage']=$this->data['root_path']."/once/plugins/".$this->data['id']."/images/ss".$i.".png";
											$ok=$i;
											$i=10;
										}
									}
								}else{
									// Default image path if ok
									$ok=$this->data['currentImage'];
									if($this->data['project_id']==$this->data['id']){
										$this->data['currentImage']=$this->data['root_path'].'/images/ss'.$this->data['currentImage'].'.png';
									}else{
										$this->data['currentImage']=$this->data['root_path'].'/once/plugins/'.$this->data['id'].'/images/ss'.$this->data['currentImage'].'.png';
									}
								}
								
								if($ok>0){
									// Move uploaded file to upload dir
									move_uploaded_file($this->data["image"]["tmp_name"],$this->data['currentImage']);
										
									// Resize image
									$this->once_image_resample($this->data['currentImage']);
										
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
				$this->set_error('Plugin not exist / for selected user');
			}
		}
		return $this->once_response();
	}
	function upload_thumbnail(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get plugin.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_plugins WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
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
								@mkdir($this->data['root_path']."/once/plugins");
								@mkdir($this->data['root_path']."/once/plugins/".$this->data['id']."");
								chmod($this->data['root_path']."/once/plugins/".$this->data['id']."", 0777);

								// Move uploaded file to upload dir
								move_uploaded_file($this->data["image"]["tmp_name"],$this->data['root_path'].'/once/plugins/'.$this->data['id'].'/thumbnail.png');

								// Resize image
								$this->once_image_resize($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/thumbnail.png',320,240);

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
				$this->set_error('Plugin not exist / for selected user');
			}
		}
		return $this->once_response();
	}

	//@2do
		function item_user_buy(){
		if($this->once_csrf_token_check($this->data['csrf_token']) || true){
			// Check if user exist by api_key
			$stmt = $this->pdo->prepare("SELECT id, balance FROM edit_users WHERE id=:user_id LIMIT 1");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR, 255);
			$stmt->execute();

			if($stmt->rowCount()){
				$row=$stmt->fetch(PDO::FETCH_ASSOC);
				// Check if user exist by api_key
				$stmt = $this->pdo->prepare("SELECT id, price FROM edit_plugins WHERE id=:plugin_id LIMIT 1");
				$stmt->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					$row2=$stmt->fetch(PDO::FETCH_ASSOC);
					// Check if plugin is premium else download
					if($row2['price']>0){
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_plugins_downloads WHERE user_id=:user_id AND plugin_id=:plugin_id LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
						$stmt2->execute();

						if($stmt2->rowCount()){
							// Return whole plugin
							$this->item=$row2;
						}else{
							if($row['balance']>=$row2['price']){
								// Mark plugin unlocked for future use
								$stmt3 = $this->pdo->prepare("INSERT INTO edit_plugins_downloads (user_id, plugin_id, mktime) VALUES(:user_id, :plugin_id, :mktime)");
								$stmt3->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
								$stmt3->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
								$stmt3->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
								$stmt3->execute();
								
								// Update balance
								$stmt4 = $this->pdo->prepare("UPDATE edit_users SET balance=balance-".$row2['price']." WHERE id=".$row['id']."");
								$stmt4->execute();
								
								// Return whole plugin
								$this->item=$row2;
							}else{
								$this->set_error('Not enough balance');
							}
						}
					}else{
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_plugins_downloads WHERE user_id=:user_id AND user_ip=:user_ip LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
						$stmt2->execute();
						
						if(!$stmt2->rowCount()){
							// Count downloads of plugin
							$stmt2 = $this->pdo->prepare("INSERT INTO edit_plugins_downloads (user_id, plugin_id, mktime, user_ip) VALUES(:user_id, :plugin_id, :mktime, :user_ip)");
							$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
							$stmt2->bindParam(':plugin_id', $this->data['plugin_id'], PDO::PARAM_INT);
							$stmt2->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
							$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
							$stmt2->execute();
						}
						
						// Return whole plugin
						$this->item=$row2;
					}
				}else{
					$this->set_error('Plugin not exist');
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
			$stmt = $this->pdo->prepare("SELECT id, version, category_id, name, description, tags, author, author_url FROM edit_plugins WHERE id=:id AND user_id=:user_id LIMIT 1");
			$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam('user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

			// Check if plugin exist.
			if($this->item){
				// Prepare statements to update plugin.
				$stmt = $this->pdo->prepare("UPDATE edit_plugins SET object_id=".$this->item['id']." WHERE id=:id AND user_id=:user_id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				$this->set_error('Plugin not exist');
			}
		}
		return $this->once_response();
	}
	function item_user_fork(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id, version, category_id, name, description, tags, author, author_url FROM edit_plugins WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
				
		// Check if plugin exist.
		if($this->item){
			// Prepare statements to insert plugin.
			$stmt = $this->pdo->prepare("
				INSERT INTO edit_plugins (user_id, category_id, object_id, version, name, tags, description, author, author_url) 
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

			// Make plugins dir
			@mkdir($this->data['root_path'].'/once/plugins');
			@chmod($this->data['root_path'].'/once/plugins', 0777);
				
			// Make plugin dir
			@mkdir($this->data['root_path'].'/once/plugins/'.$lastInsertId);
			@chmod($this->data['root_path'].'/once/plugins/'.$lastInsertId, 0777);


			// Prepare statements to get plugin.php template
			$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/plugin.php');
			
			// Create preview file
			file_put_contents($this->data['root_path'].'/once/plugins/'.$lastInsertId.'/index.php',$tpl['source']);
			
			// Copy plugin
			copy($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.html',$this->data['root_path'].'/once/plugins/'.$lastInsertId.'/plugin.html');
			copy($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.css',$this->data['root_path'].'/once/plugins/'.$lastInsertId.'/plugin.css');
			copy($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/plugin.js',$this->data['root_path'].'/once/plugins/'.$lastInsertId.'/plugin.js');
			
			// Copy logo
			copy($this->data['root_path'].'/once/plugins/'.$this->item['id'].'/thumbnail.png',$this->data['root_path'].'/once/plugins/'.$lastInsertId.'/thumbnail.png');

			// Return new item id
			$this->item['id']=$lastInsertId;
		}else{
			$this->set_error('Plugin not exist');
		}
		return $this->once_response();
	}
	function item_user_new(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if(!$this->once_creator_check()){
				// @2do dd limits
			}
			
			// Use once to insert empty record
			if($this->once_insert('plugins',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			))){
			
				// Make plugins dir
				@mkdir('once/plugins');
				@chmod('once/plugins', 0777);
				
				// Make plugin dir
				@mkdir('once/plugins/'.$this->item['id']);
				@chmod('once/plugins/'.$this->item['id'], 0777);

				// Prepare statements to get plugin.php template
				$tpl['source']=@file_get_contents('once/default/plugin.php');
				
				// Create preview file
				@file_put_contents('once/plugins/'.$this->item['id'].'/index.php',$tpl['source']);

				// Create other default files
				@file_put_contents('once/plugins/'.$this->item['id'].'/plugin.html','');
				@file_put_contents('once/plugins/'.$this->item['id'].'/plugin.css','');
				@file_put_contents('once/plugins/'.$this->item['id'].'/plugin.js','');
			}else{
				$this->set_error('Can not insert item to: plugins');
			}
		}
		return $this->once_response();
	}
	function item_user_vote(){
		if($this->data['user_logged']){
			// Prepare statements to get plugin.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_plugins WHERE id=:id LIMIT 1");
			$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
					
			// Check if plugin exist.
			if($this->item){
				// Prepare statements to get vote.
				$stmt = $this->pdo->prepare("SELECT id FROM edit_plugins_votes WHERE plugin_id=:plugin_id AND user_id=:user_id LIMIT 1");
				$stmt->bindParam(':plugin_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();

				$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
						
				// Check if plugin exist.
				if(!$this->item){
					// Prepare statements to insert vote.
					$stmt = $this->pdo->prepare("
						INSERT INTO edit_plugins_votes (plugin_id, user_id, mktime) 
						VALUES (:plugin_id, :user_id, :mktime)
					");
					$stmt->bindParam(':plugin_id', $this->data['id'], PDO::PARAM_INT);
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
					$stmt->execute();
							
					// Prepare statements to update plugin.
					$stmt = $this->pdo->prepare("UPDATE edit_plugins SET votes=votes+1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}else{
					$this->set_error('Can not vote');
				}
			}else{
				$this->set_error('Plugin not exist');
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
		$archiveName = $this->data['root_path']."/plugin.zip";

		if ($zip->open($archiveName, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$archiveName>\n");
		}
		
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id, version, category_id, name, description, tags, author, author_url FROM edit_plugins WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
				
		// Check if plugin exist.
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
						".file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.css')."
					</style>
					<script>
						".file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.js')."
					</script>
				</head>
				<body>
					<div class=\"text-center\" style=\"border-bottom: 1px solid #fefefe; padding: 5px 0 10px 0;\">Plugin URL and MIT License: <a href=\"https://oncebuilder.com/plugins/".$this->data['id']."\">https://oncebuilder.com/plugins/".$this->data['id']."</a></div>
					<div id=\"body\">".file_get_contents($this->data['root_path'].'/once/plugins/'.$this->data['id'].'/plugin.html')."
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
			$this->set_error('Plugin not exist');
		}	
		return $this->once_response();
	}
	function item_user_report(){
		// Prepare statements to get plugin.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_plugins WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
				
		// Check if plugin exist.
		if($this->item){
			// Prepare statements to get vote.
			$stmt = $this->pdo->prepare("SELECT id, star FROM edit_plugins_reports WHERE plugin_id=:plugin_id AND user_id=:user_id LIMIT 1");
			$stmt->bindParam(':plugin_id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
					
			// Check if plugin exist.
			if(!$this->item['star']){
				// Prepare statements to insert vote.
				$stmt = $this->pdo->prepare("
					INSERT INTO edit_plugins_reports (plugin_id, user_id, mktime, message) 
					VALUES (:plugin_id, :user_id, :mktime, :message)
				");
				$stmt->bindParam(':plugin_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
				$stmt->bindParam(':message', $this->data['message'], PDO::PARAM_STR. 250);
				$stmt->execute();
						
				// Prepare statements to update plugin.
				$stmt = $this->pdo->prepare("UPDATE edit_plugins SET reports=reports+1 WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
			$this->set_error('Can not report');
			}
		}else{
			$this->set_error('Plugin not exist');
		}
		return $this->once_response();
	}
}
?>
