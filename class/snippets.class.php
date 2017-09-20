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
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets_categories WHERE LOWER(name) LIKE :category");
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
			$obj['categories']=$this->category_get('snippets');

			# GET DATA -------------------
			$obj['data']=$this->once_select_items_page('snippets');
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
	function bulk_action(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin
			if($this->once_creator_check()){
				// Prepare statements to star selected id.
				$stmt1 = $this->pdo->prepare("UPDATE edit_snippets SET stared = 1 WHERE id=:id");
				
				// Prepare statements to unstar selected id.
				$stmt2 = $this->pdo->prepare("UPDATE edit_snippets SET stared = 0 WHERE id=:id");
				
				// Prepare statements to delete selected id.
				$stmt3 = $this->pdo->prepare("DELETE FROM edit_snippets WHERE id=:id");
					
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
							$this->recurse_delete($this->data['root_path'].'/once/snippets/'.$position.'');
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
	function set_limit(){//ok
		// Update page limit with once
		$this->once_page_limit('snippets');
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
			$file = file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/thumbnail.png');
			$base = base64_encode($file);
			$media1 = $connection->upload('media/upload', array('media' => array('media' => $base)));

			// Short link before publish
			require_once($this->data['root_path'].'/once/libs/bitlyphp/bitly.php');

			$url='http://oncebuilder.com/snippet/'.$this->data['id'];
			
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

	function item_approve(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			if($this->once_creator_check()){
				// Prepare statements to get selected data
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id LIMIT 1");
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
						$stmt = $this->pdo->prepare("UPDATE edit_snippets SET published=1 WHERE id=:id LIMIT 1");
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();

						// Set count for user when added
						$this->data['user_id']=$obj['item']['user_id'];
						$this->once_set_user_count('snippets');

						$obj['status']='ok';
					
					}else{
						// Prepare statements to publish selected data
						$stmt = $this->pdo->prepare("UPDATE edit_snippets SET published=0 WHERE id=:id LIMIT 1");
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
						
					}
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Snippet doesn\'t exist';
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
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
				// Use once to delete item
				$obj=$this->once_delete_item('snippets');
				
				if($obj['count']){
					$this->recurse_delete($this->data['root_path'].'/once/snippets/'.$this->data['id'].'');
				}
			}else{
				$obj['errors'][]='Snippet not exist / for selected user';
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
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			// Get count of returned records
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
				$obj['item']=$stmt->fetch(PDO::FETCH_ASSOC);

				// Prepare statements to update snippet.
				$stmt = $this->pdo->prepare("UPDATE edit_snippets SET category_id=:category_id, name=:name, tags=:tags, author=:author, author_url=:author_url, description=:description WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $obj['item']['id'], PDO::PARAM_INT);
				$stmt->bindParam(':category_id', $this->data['category_id'], PDO::PARAM_INT, 50);
				$stmt->bindParam(':name', $this->data['name'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':tags', $this->data['tags'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':author', $this->data['author'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':author_url', $this->data['author_url'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':description', $this->data['description'], PDO::PARAM_STR, 50);
				$stmt->execute();
				
				if($stmt->rowCount()){
					// Set status ok
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Cant save';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='Snippet not exist / for selected user';
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
	function item_insights_image(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Get count of returned records
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
				$this->once_insights_image("http://oncebuilder.com/once/snippets/".$this->data['id'],$this->data['root_path']."/once/snippets/".$this->data['id']."/thumbnail.png");
				$obj['status']='ok';
			}else{
				$obj['errors'][]='Snippet not exist / for selected user';
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
	function item_download(){//ok
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=snippets&o=item_download");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "api=".$this->data['api_key']."&snippet_id=".$this->data['id']); //dane do wyslania
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$response = curl_exec($ch);
			curl_close($ch);
			// Decode json
			$obj=json_decode($response, true);
	
			// Use once to insert empty snippet
			$objx=$this->once_insert('snippets',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			));

			$stmt = $this->pdo->prepare("UPDATE edit_snippets SET name=:name WHERE id=:id LIMIT 1");
			$stmt->bindParam(':name', $obj['item']['name'], PDO::PARAM_STR, 255);
			$stmt->bindParam(':id', $objx['item']['id'], PDO::PARAM_INT);
			$stmt->execute();
			
			if($objx['count']){
				// Make snippet dir
				@mkdir("./snippets");
				@chmod("./snippets", 0777);
				@mkdir("./snippets/".$objx['item']['id']);
				@chmod("./snippets/".$objx['item']['id'], 0777);
			}
			
			// Prepare statements to get snippet.php template
			$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/snippet.php');
			
			// Create preview file
			file_put_contents($this->data['root_path'].'/once/snippets/'.$objx['item']['id'].'/index.php',$tpl['source']);
			
			// DOWNLOAD SNIPPET FROM UNIQUE URL AND UNPACK
			$obj['itemx']['source_html']=@file_get_contents('http://oncebuilder.com/once/snippets/'.$this->data['id'].'/snippet.html');
			$obj['itemx']['source_css']=@file_get_contents('http://oncebuilder.com/once/snippets/'.$this->data['id'].'/snippet.css');
			$obj['itemx']['source_js']=@file_get_contents('http://oncebuilder.com/once/snippets/'.$this->data['id'].'/snippet.js');
			
			@file_put_contents("./snippets/".$objx['item']['id'].'/snippet.html',$obj['itemx']['source_html']);
			@file_put_contents("./snippets/".$objx['item']['id'].'/snippet.css',$obj['itemx']['source_css']);
			@file_put_contents("./snippets/".$objx['item']['id'].'/snippet.js',$obj['itemx']['source_js']);
			
			@file_put_contents("./snippets/".$objx['item']['id'].'/thumbnail.png',file_get_contents('http://oncebuilder.com/once/snippets/'.$this->data['id'].'/thumbnail.png'));
			
			$obj['status']='ok';
			
			//return $obj;
		}else{
			// Check if user exist by api_key
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE api_key=:api_key LIMIT 1");
			$stmt->bindParam(':api_key', $this->data['api'], PDO::PARAM_STR, 255);
			$stmt->execute();

			if($stmt->rowCount()){
				$row=$stmt->fetch(PDO::FETCH_ASSOC);
				// Check if user exist by api_key
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:snippet_id LIMIT 1");
				$stmt->bindParam(':snippet_id', $this->data['snippet_id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					$row2=$stmt->fetch(PDO::FETCH_ASSOC);
					// Check if snippet is premium else download
					if($row2['price']>0){
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_snippets_downloads WHERE user_id=:user_id AND snippet_id=:snippet_id LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':snippet_id', $this->data['snippet_id'], PDO::PARAM_INT);
						$stmt2->execute();

						if($stmt2->rowCount()){
							// Return whole snippet
							$obj['item']=$row2;
							$obj['status']='ok';
						}else{
							if($row['balance']>$row2['price']){
								// Mark snippet unlocked for future use
								$stmt3 = $this->pdo->prepare("INSERT INTO edit_snippets_downloads (user_id, snippet_id, mktime) VALUES(:user_id, :snippet_id, :mktime)");
								$stmt3->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
								$stmt3->bindParam(':snippet_id', $this->data['snippet_id'], PDO::PARAM_INT);
								$stmt3->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
								$stmt3->execute();
								
								// Update balance
								$stmt4 = $this->pdo->prepare("UPDATE edit_users WHERE balance=balance-'".$row2['price']."'");
								$stmt4->execute();
								
								// Return whole snippet
								$obj['item']=$row2;
								$obj['status']='ok';
							}else{
								$obj['error']='not enough balance';
							}
						}
					}else{
						//  Check if it has been already downloaded then download else check balance
						$stmt2 = $this->pdo->prepare("SELECT * FROM edit_snippets_downloads WHERE user_id=:user_id AND user_ip=:user_ip LIMIT 1");
						$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
						$stmt2->execute();
						
						if(!$stmt2->rowCount()){
							// Count downloads of snippet
							$stmt2 = $this->pdo->prepare("INSERT INTO edit_snippets_downloads (user_id, snippet_id, mktime, user_ip) VALUES(:user_id, :snippet_id, :mktime, :user_ip)");
							$stmt2->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
							$stmt2->bindParam(':snippet_id', $this->data['snippet_id'], PDO::PARAM_INT);
							$stmt2->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
							$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 16);
							$stmt2->execute();
						}
						
						// Return whole snippet
						$obj['item']=$row2;
						$obj['status']='ok';
					}
				}else{
					$obj['errors'][]='Snippet not exist';
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
	function item_new(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Use once to insert empty record
			$obj=$this->once_insert('snippets',array(
				"id" => '',
				"user_id"=> $this->data['user_id']
			));
			
			if($obj['count']){
				// Make snippets dir
				@mkdir('./once/snippets');
				@chmod('./once/snippets', 0777);
				
				// Make snippet dir
				@mkdir('./once/snippets/'.$obj['item']['id']);
				@chmod('./once/snippets/'.$obj['item']['id'], 0777);

				// Prepare statements to get snippet.php template
				$tpl['source']=@file_get_contents('./once/default/snippet.php');
				
				// Create preview file
				file_put_contents('./once/snippets/'.$obj['item']['id'].'/index.php',$tpl['source']);

				// Create other default files
				file_put_contents('./once/snippets/'.$obj['item']['id'].'/snippet.html','');
				file_put_contents('./once/snippets/'.$obj['item']['id'].'/snippet.css','');
				file_put_contents('./once/snippets/'.$obj['item']['id'].'/snippet.js','');
				
				// Set status ok
				$obj['status']='ok';
			}else{
				// Return error if item not created
				$obj['errors'][]='Can not insert item to: snippets';
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
			curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=snippets&o=item_preview");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "api=".$this->data['api_key']."&snippet_id=".$this->data['id']); //dane do wyslania
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
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:snippet_id LIMIT 1");
				$stmt->bindParam(':snippet_id', $this->data['snippet_id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					$obj['item']=$stmt->fetch(PDO::FETCH_ASSOC);
					
					if($obj['item']['price']>0){
						// Check if user bought this snippet
						$stmt = $this->pdo->prepare("SELECT COUNT(*) AS ile FROM edit_snippets_downloads WHERE user_id=:user_id AND snippet_id=:snippet_id LIMIT 1");
						$stmt->bindParam(':user_id', $row['id'], PDO::PARAM_INT);
						$stmt->bindParam(':snippet_id', $this->data['snippet_id'], PDO::PARAM_INT);
						$stmt->execute();
						
						if($stmt->fetchColumn() > 0) {
							$obj['item']['bought']=true;
						}
					}
					
					if($obj['item']['price']==0 || $obj['item']['bought']){
						// Return snippet source
						$obj['item']['source_html']=@file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['snippet_id'].'/snippet.html');
						$obj['item']['source_css']=@file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['snippet_id'].'/snippet.css');
						$obj['item']['source_js']=@file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['snippet_id'].'/snippet.js');
					}
					
					$obj['status']='ok';
				}else{
					$obj['errors'][]='Snippet not exist';
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
				$stmt = $this->pdo->prepare("SELECT id, category_id, version, name, tags, author, author_url, description FROM edit_snippets WHERE id=:id LIMIT 1");
				$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();

				$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

				// Check if snippet exist.
				if($obj['item']){
					// Update file if not exist
					if(!file_exists($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/thumbnail.png')){
						file_put_contents($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/thumbnail.png','');
					}
					
					// Set CURL
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=snippets&o=item_publish");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					
					// Add files depends on version PHP
					$version = explode('.', phpversion());
					if($version[0]>=5 && $version[1]>=5){
						//curl_file_create(realpath('snippets/'.$obj['item']['id'].'/snippet.zip'))
												
						$file_html = new CURLFile(realpath('snippets/'.$obj['item']['id'].'/snippet.html'));
						$file_html->setPostFilename('snippet.zip');
						
						$file_css = new CURLFile(realpath('snippets/'.$obj['item']['id'].'/snippet.css'));
						$file_css->setPostFilename('snippet.css');
						
						$file_js = new CURLFile(realpath('snippets/'.$obj['item']['id'].'/snippet.js'));
						$file_js->setPostFilename('snippet.js');
						
						$thumbnail = new CURLFile(realpath('snippets/'.$obj['item']['id'].'/thumbnail.png'));
						$thumbnail->setPostFilename('thumbnail.zip');
					}else{
						$file_html = '@' . realpath($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/snippet.html');
						$file_css = '@' . realpath($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/snippet.css');
						$file_js = '@' . realpath($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/snippet.js');
						$thumbnail = '@' . realpath($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/thumbnail.png');
					}
					
					// Send files and parms
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt(
						$ch,
						CURLOPT_POSTFIELDS,
						array(
							'api' => $this->data['api_key'],
							"file_html" => $file_html,		
							"file_css" => $file_css,		
							"file_js" => $file_js,		
							"thumbnail" => $thumbnail,			
							'category_id' => $obj['item']['category_id'],
							'object_id' => $obj['item']['id'],
							'version' => $obj['item']['version'],
							'name' => $obj['item']['name'],
							'tags' => $obj['item']['tags'],
							'author' => $obj['item']['author'],
							'author_url' => $obj['item']['author_url'],
							'description' => $obj['item']['description']
						)
					);

					// Get response
					$response = curl_exec($ch);

					if(strpos($response, '"status":"ok"')){
						// Prepare set as published
						$stmt = $this->pdo->prepare("UPDATE edit_snippets SET object_id=:object_id WHERE id=:id LIMIT 1");
						$stmt->bindParam(':object_id', $this->data['id'], PDO::PARAM_INT);
						$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
						$stmt->execute();
						$obj['status']='ok';
					}else{
						$obj['error']='Upload API error';
					}

					curl_close($ch);
				}else{
					$obj['error']='Snippet doesn\'t exist';
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
					if($this->data["file_html"]["error"] > 0 || $this->data["file_css"]["error"] > 0 || $this->data["file_js"]["error"] > 0 || $this->data["thumbnail"]["error"] > 0){
						$obj['error']='error file upload';
					}else{
						// Insert new snippet
						$stmt = $this->pdo->prepare("
							INSERT INTO edit_snippets (user_id, object_id, version, name, tags, author, author_url, description, created)
							VALUES (:user_id, :object_id, :version, :name, :tags, :author, :author_url, :description, :created)
						");

						$stmt->bindParam(':user_id', $obj['user']['id'], PDO::PARAM_INT);
						$stmt->bindParam(':object_id', $this->data['object_id'], PDO::PARAM_INT);
						$stmt->bindParam(':version', $this->data['version'], PDO::PARAM_INT);
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
								INSERT INTO edit_snippets_reports (user_id, snippet_id, mktime, message)
								VALUES (:user_id, :snippet_id, :mktime, :message)
							");
							$stmt->bindParam(':user_id', $obj['user']['id'], PDO::PARAM_INT);
							$stmt->bindParam(':snippet_id', $lastInsertId, PDO::PARAM_INT);
							$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
							$stmt->bindParam(':message', $this->data['message'], PDO::PARAM_STR, 255);
							$stmt->execute();
						}
						
						// Make dirs and generate files
						@mkdir($this->data['root_path']."/once/snippets/".$lastInsertId."");
						@chmod($this->data['root_path']."/once/snippets/".$lastInsertId."", 0777);

						// Prepare statements to get snippet.php template
						$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/snippet.php');
						
						// Create preview file
						file_put_contents($this->data['root_path'].'/once/snippets/'.$lastInsertId.'/index.php',$tpl['source']);
							
						// Move uploaded file to snippet dir
						move_uploaded_file($this->data["file_html"]["tmp_name"],$this->data['root_path'].'/once/snippets/'.$lastInsertId.'/snippet.html');
						move_uploaded_file($this->data["file_css"]["tmp_name"],$this->data['root_path'].'/once/snippets/'.$lastInsertId.'/snippet.css');
						move_uploaded_file($this->data["file_js"]["tmp_name"],$this->data['root_path'].'/once/snippets/'.$lastInsertId.'/snippet.js');
						move_uploaded_file($this->data["thumbnail"]["tmp_name"],$this->data['root_path'].'/once/snippets/'.$lastInsertId.'/thumbnail.png');
						
						// Resize image
						$this->once_image_resize($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/thumbnail.png',320,240);
										
						// Use google to get screenshot
						if($this->data["thumbnail"]["tmp_name"]==''){
							$this->once_insights_image("http://oncebuilder.com/once/snippets/".$this->data['id'],$this->data['root_path']."/once/snippets/".$this->data['id']."/thumbnail.png");
						}
						
						//Resize image if its 320x240 if it's larger
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
	function item_star(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
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
					$stmt = $this->pdo->prepare("UPDATE edit_snippets SET stared=0 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}else{
					// Prepare statements to star selected data
					$stmt = $this->pdo->prepare("UPDATE edit_snippets SET stared=1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}
				$obj['status']='ok';
			}else{
				$obj['errors'][]='Snippet doesn\'t exist';
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
	
	function load_source(){//ok
		if(true){//$this->once_csrf_token_check($this->data['csrf_token']
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}

			// Check if snippet exist then open source file
			if($stmt->rowCount()){
				$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

				// Open selected file
				if($this->data['file']=='snippet.html'){
					$obj['source']=@file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/snippet.html');
					$obj['status']='ok';
				}else if($this->data['file']=='snippet.css'){
					$obj['source']=@file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/snippet.css');
					$obj['status']='ok';
				}else if($this->data['file']=='snippet.js'){
					$obj['source']=@file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/snippet.js');
					$obj['status']='ok';
				}else{
					$obj['errors'][]='can not load file';
					$obj['error']++;
				}

				if(!$obj['source']){
					$obj['source']='';
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
 	function save_source(){//ok
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if user is creator/admin or just user
			if($this->once_creator_check()){
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
			
			// Check if snippet exist then save source to file
			$obj['count']=$stmt->rowCount();
			if($obj['count']){
				$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
				
				// Save file with content
				if($this->data['file']=='snippet.html'){
					$obj['source']=@file_put_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/snippet.html',$this->data['source']);
				}else if($this->data['file']=='snippet.css'){
					$obj['source']=@file_put_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/snippet.css',$this->data['source']);
				}else if($this->data['file']=='snippet.js'){
					$obj['source']=@file_put_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/snippet.js',$this->data['source']);
				}else{
					$obj['errors'][]='can not save to file';
					$obj['error']++;
				}
					
				//2do Update archive

				if($obj['source']){
					$obj['status']='ok';
				}
			}else{
				$obj['errors'][]='Snippet not exist / for selected user';
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
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
			}else{
				// Prepare statements to get snippet.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_snippets WHERE id=:id AND user_id=:user_id AND published=0 LIMIT 1");
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
								@mkdir("./snippets");
								@mkdir("./snippets/".$this->data['id']."");
								chmod("./snippets/".$this->data['id']."", 0777);
									
								// Move uploaded file to upload dir
								move_uploaded_file($this->data["image"]["tmp_name"],$this->data['root_path'].'/once/snippets/'.$this->data['id'].'/thumbnail.png');
								
								// Resize image
								$this->once_image_resize($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/thumbnail.png',170,170);

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
				$obj['errors'][]='Snippet not exist / for selected user';
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
	function item_user_publish(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("SELECT id, version, category_id, name, description, tags, author, author_url FROM edit_snippets WHERE id=:id AND user_id=:user_id LIMIT 1");
			$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam('user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

			// Check if snippet exist.
			if($obj['item']){
				// Prepare statements to update snippet.
				$stmt = $this->pdo->prepare("UPDATE edit_snippets SET object_id=:object_id WHERE id=:id AND user_id=:user_id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':object_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
					
				$obj['status']='ok';
			}else{
				$obj['errors'][]='snippet not exist';
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
	function item_user_fork(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id, version, category_id, name, description, tags, author, author_url FROM edit_snippets WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
				
		// Check if snippet exist.
		if($obj['item']){
			// Prepare statements to insert snippet.
			$stmt = $this->pdo->prepare("
				INSERT INTO edit_snippets (user_id, category_id, object_id, version, name, tags, description, author, author_url) 
				VALUES (:user_id, :category_id, :object_id, :version, :name, :tags, :description, :author, :author_url)
			");
			$obj['item']['object_id']=0;
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':category_id', $obj['item']['category_id'], PDO::PARAM_INT);
			$stmt->bindParam(':object_id', $obj['item']['object_id'], PDO::PARAM_INT);
			$stmt->bindParam(':version', $obj['item']['version'], PDO::PARAM_INT);
			$stmt->bindParam(':name', $obj['item']['name'], PDO::PARAM_STR, 100);
			$stmt->bindParam(':tags', $obj['item']['tags'], PDO::PARAM_STR, 100);
			$stmt->bindParam(':description', $obj['item']['description'], PDO::PARAM_STR, 100);
			$stmt->bindParam(':author', $obj['item']['author'], PDO::PARAM_STR, 100);
			$stmt->bindParam(':author_url', $obj['item']['author_url'], PDO::PARAM_STR, 100);
			$stmt->execute();
					
			// Get last insert id
			$lastInsertId=$this->pdo->lastInsertId();

			// Make dirs and generate files
			@mkdir("./once/snippets/".$lastInsertId."");
			chmod("./once/snippets/".$lastInsertId."", 0777);
			
			// Prepare statements to get snippet.php template
			$tpl['source']=@file_get_contents($this->data['root_path'].'/once/default/snippet.php');
			
			// Create preview file
			file_put_contents($this->data['root_path'].'/once/snippets/'.$lastInsertId.'/index.php',$tpl['source']);
			
			// Copy snippet
			copy($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/snippet.html',$this->data['root_path'].'/once/snippets/'.$lastInsertId.'/snippet.html');
			copy($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/snippet.css',$this->data['root_path'].'/once/snippets/'.$lastInsertId.'/snippet.css');
			copy($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/snippet.js',$this->data['root_path'].'/once/snippets/'.$lastInsertId.'/snippet.js');
			
			// Copy logo
			copy($this->data['root_path'].'/once/snippets/'.$obj['item']['id'].'/thumbnail.png',$this->data['root_path'].'/once/snippets/'.$lastInsertId.'/thumbnail.png');

			// Return new item id
			$obj['item']['id']=$lastInsertId;
			
			$obj['status']='ok';
		}else{
			$obj['errors'][]='snippet not exist';
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
	function item_user_vote(){
		if($this->data['user_logged']){
			// Prepare statements to get snippet.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_snippets WHERE id=:id LIMIT 1");
			$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();

			$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
					
			// Check if snippet exist.
			if($obj['item']){
				// Prepare statements to get vote.
				$stmt = $this->pdo->prepare("SELECT id FROM edit_snippets_votes WHERE snippet_id=:snippet_id AND user_id=:user_id LIMIT 1");
				$stmt->bindParam(':snippet_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();

				$obj['star'] = $stmt->fetch(PDO::FETCH_ASSOC);
						
				// Check if snippet exist.
				if(!$obj['star']){
					// Prepare statements to insert vote.
					$stmt = $this->pdo->prepare("
						INSERT INTO edit_snippets_votes (snippet_id, user_id, mktime) 
						VALUES (:snippet_id, :user_id, :mktime)
					");
					$stmt->bindParam(':snippet_id', $this->data['id'], PDO::PARAM_INT);
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
					$stmt->execute();
							
					// Prepare statements to update snippet.
					$stmt = $this->pdo->prepare("UPDATE edit_snippets SET votes=votes+1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
					
					$obj['status']='ok';
				}else{
					$obj['errors'][]='can not vote';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='snippet not exist';
				$obj['error']++;
			}
		}else{
			$obj['errors'][]='user not logged';
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
	function item_user_download(){
		// Load ZipArchive class to procces download project as zip archive
		if(!extension_loaded('zip')){
			dl('zip.so');
		}

		$zip = new ZipArchive();
		$archiveName = $this->data['root_path']."/snippet.zip";

		if ($zip->open($archiveName, ZIPARCHIVE::CREATE)!==TRUE) {
			exit("cannot open <$archiveName>\n");
		}
		
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id, version, category_id, name, description, tags, author, author_url FROM edit_snippets WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
				
		// Check if snippet exist.
		if($obj['item']){
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
						".file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/snippet.css')."
					</style>
					<script>
						".file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/snippet.js')."
					</script>
				</head>
				<body>
					<div class=\"text-center\" style=\"border-bottom: 1px solid #fefefe; padding: 5px 0 10px 0;\">Snippet URL and MIT License: <a href=\"http://oncebuilder.com/snippets/".$this->data['id']."\">http://oncebuilder.com/snippets/".$this->data['id']."</a></div>
					<div id=\"body\">".file_get_contents($this->data['root_path'].'/once/snippets/'.$this->data['id'].'/snippet.html')."
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
		}else{
			$obj['errors'][]='snippet not exist';
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
	function item_user_report(){
		// Prepare statements to get snippet.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_snippets WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
				
		// Check if snippet exist.
		if($obj['item']){
			// Prepare statements to get vote.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_snippets_reports WHERE snippet_id=:snippet_id AND user_id=:user_id LIMIT 1");
			$stmt->bindParam(':snippet_id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$obj['star'] = $stmt->fetch(PDO::FETCH_ASSOC);
					
			// Check if snippet exist.
			if(!$obj['star']){
				// Prepare statements to insert vote.
				$stmt = $this->pdo->prepare("
					INSERT INTO edit_snippets_reports (snippet_id, user_id, mktime, message) 
					VALUES (:snippet_id, :user_id, :mktime, :message)
				");
				$stmt->bindParam(':snippet_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
				$stmt->bindParam(':message', $this->data['message'], PDO::PARAM_STR. 250);
				$stmt->execute();
						
				// Prepare statements to update snippet.
				$stmt = $this->pdo->prepare("UPDATE edit_snippets SET reports=reports+1 WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();
				
				$obj['status']='ok';
			}else{
				$obj['errors'][]='can not report';
				$obj['error']++;
			}
		}else{
			$obj['errors'][]='snippet not exist';
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