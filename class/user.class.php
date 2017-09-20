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
	public $pdo;
	public $data;
	public $time;
	
	
	//############################ MYSQL & LOGIN ##################################################
	
	function send_hire_message(){
		$tpl['source']=file_get_contents('tpl/mail/hire.html');
		
		// Include user name to template {$user} & {$hash}
		$tpl['source']=str_replace("{\$username}",$this->data['username'],$tpl['source']);
		$tpl['source']=str_replace("{\$message}",$this->data['message'],$tpl['source']);

		//SMTP needs accurate times, and the PHP time zone MUST be set
		//This should be done in your php.ini, but this is how to do it if you don't have access to that
		date_default_timezone_set('Etc/UTC');
		
		require_once($this->data['root_path'].'/once/libs/phpmailer/phpmailerautoload.php');
		
		/**
		 * This example shows making an SMTP connection with authentication.
		*/
		
		//Create a new PHPMailer instance to send activation email
		$mail = new PHPMailer;
		//Some funny options
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = "oncebuilder.com";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = 25;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = "support@oncebuilder.com";
		//Password to use for SMTP authentication
		$mail->Password = "%:ogFwdY-^+Y";
		//Set who the message is to be sent from
		$mail->setFrom('support@oncebuilder.com', 'Message from hire form - OnceBuilder.com');//accounts
		//Set an alternative reply-to address
		//$mail->addReplyTo($this->data['email'], $this->data['name']);
		//Set who the message is to be sent to
		$mail->addAddress($this->data['user_email'], 'Dear friend');
		//Set the subject line
		$mail->Subject = 'Someone send you offer';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($tpl['source']);//file_get_contents('contents.html'), dirname(__FILE__)
		//Replace the plain text body with one created manually
		$mail->AltBody = $this->data['message'];
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');

		//send the message, check for errors
		if (!$mail->send()) {
			$obj['error']=$mail->ErrorInfo;
		} else {
			$obj['status']='ok';
		}
	}
	
	function item_user_hire(){
		// Check if user is logged
		if($this->data['user_logged']){
			// Prepare statements to get snippet.
			$stmt = $this->pdo->prepare("SELECT username, email, hire_form FROM edit_users 
			LEFT JOIN edit_users_settings ON edit_users.id=edit_users_settings.user_id
			WHERE edit_users.id=:id LIMIT 1");
			$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();

			$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

			// Check if snippet exist.
			if($obj['item']){
				if($obj['item']['hire_form']){
					// Prepare statements to get vote.
					$stmt = $this->pdo->prepare("SELECT id FROM edit_users_messages WHERE user_id=:user_id AND user_to=:user_to AND mktime+5000>".($this->data['time'])." LIMIT 1");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':user_to', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();

					$obj['star'] = $stmt->fetch(PDO::FETCH_ASSOC);
							
					// Check if snippet exist.
					if(!$obj['star']){
						// Prepare statements to insert vote.
						$stmt = $this->pdo->prepare("
							INSERT INTO edit_users_messages (user_id, user_to, mktime, message) 
							VALUES (:user_id, :user_to, :mktime, :message)
						");
						$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
						$stmt->bindParam(':user_to', $this->data['id'], PDO::PARAM_INT);
						$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
						$stmt->bindParam(':message', $this->data['message'], PDO::PARAM_STR. 250);
						$stmt->execute();
						
						// if set settings send email
						if($obj['item']['email']!=''){
							$this->data['user_email']=$obj['item']['email'];
							if($obj['item']['username']!=''){
								$this->data['username']=' sent by '.$obj['item']['username'];
							}
							$this->send_hire_message();
						}
						
						$obj['status']='ok';
					}else{
						$obj['errors'][]='you can not send more message to this user';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='user not allow to send hire form message';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='you can not send more message in 5min';
				$obj['error']++;
			}	
		}else{
			$obj['errors'][]='user not logged';
			$obj['error']++;
		}
		// Print JSON object
		echo json_encode($obj);
	}
	
	function get_user_data(){
		// Prepare statements to get socials information.
		$stmt = $this->pdo->prepare("SELECT facebook, twitter, youtube, linkedin, dribbble, github, google, behance, codepen FROM edit_users_socials WHERE user_id=:id");
		$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();
		$data['socials'] = $stmt->fetch(PDO::FETCH_ASSOC);
		
		// Prepare statements to get socials information.
		$stmt = $this->pdo->prepare("SELECT firstname, lastname, location, position, skills FROM edit_users_informations WHERE user_id=:id");
		$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();
		$data['informations'] = $stmt->fetch(PDO::FETCH_ASSOC);
		
		// Prepare statements to check if followed.
		$stmt = $this->pdo->prepare("SELECT followed_id FROM edit_users_follows WHERE user_id=:user_id");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		$data['follow'] = false;
		// Return result in table
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$data['follow'][$row['followed_id']]=true;
		}

		// Prepare statements to get counts of resources.
		$stmt = $this->pdo->prepare("SELECT snippets, plugins, themes, tutorials, followers, following FROM edit_users_counts WHERE user_id=:id");
		$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();
		$data['counts'] = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $data;
	}
	
	function get_followers(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("
			SELECT edit_users.id AS id, username, firstname, lastname, location, location, position FROM edit_users 
			LEFT JOIN edit_users_follows ON edit_users.id=edit_users_follows.user_id 
			LEFT JOIN edit_users_informations ON edit_users.id=edit_users_informations.user_id 
			WHERE edit_users_follows.followed_id=:id
		");
		$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		$data['items'] = array();
		while($wiersz = $stmt->fetch(PDO::FETCH_ASSOC)){
			$data['items'][] = $wiersz;
		}
		
		return $data;
	}
	
	function get_following(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("
			SELECT edit_users.id AS id, username, firstname, lastname, location, location, position FROM edit_users 
			LEFT JOIN edit_users_follows ON edit_users.id=edit_users_follows.followed_id 
			LEFT JOIN edit_users_informations ON edit_users.id=edit_users_informations.user_id 
			WHERE edit_users_follows.user_id=:id
		");
		$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		$data['items'] = array();
		while($wiersz = $stmt->fetch(PDO::FETCH_ASSOC)){
			$data['items'][] = $wiersz;
		}
		
		return $data;
	}
	
	function item_follow(){
		if($this->data['user_logged']){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("SELECT id FROM edit_users_follows WHERE user_id=:user_id AND followed_id=:followed_id");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':followed_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();

				if(!$stmt->rowCount()){
					// Prepare statements to insert tutorial.
					$stmt = $this->pdo->prepare("
						INSERT INTO edit_users_follows (user_id, followed_id) 
						VALUES (:user_id, :followed_id)
					");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':followed_id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
					
					if($stmt->rowCount()){
						// Get last insert id
						$lastInsertId=$this->pdo->lastInsertId();
						
						$this->once_set_user_count('following');

						$this->data['user_id']=$this->data['id'];
						$this->once_set_user_count('followers');
						
						
						$obj['status']='ok';
					}
				}else{
					$obj['errors'][]='You are already following!';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='CSFR token invalid!';
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
	
	function item_unfollow(){
		if($this->data['user_logged']){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("SELECT id FROM edit_users_follows WHERE user_id=:user_id AND followed_id=:followed_id");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':followed_id', $this->data['id'], PDO::PARAM_INT);
				$stmt->execute();

				if($stmt->rowCount()){
					// Prepare statements to insert tutorial.
					$stmt = $this->pdo->prepare("DELETE FROM edit_users_follows WHERE user_id=:user_id AND followed_id=:followed_id");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':followed_id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
					
					if($stmt->rowCount()){
						$this->once_set_user_count('following',-1);

						$this->data['user_id']=$this->data['id'];
						$this->once_set_user_count('followers',-1);
						
						$obj['status']='ok';
					}else{
						$obj['errors'][]='can not unfollow!';
						$obj['error']++;
					}
				}else{
					$obj['errors'][]='You aren\'t following!';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='CSFR token invalid!';
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

}
?>