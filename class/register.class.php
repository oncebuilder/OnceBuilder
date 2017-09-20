<?php
/**
 * Version: 1.0, 01.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Register plugin (once.register)
 *
*/
class once extends core{
	public $pdo;
	public $data;
	public $msg;
	public $exceptions;
	public $settings;
	public $time;
	
	//############################ USER REGISTER ##################################################

	function check_banned(){
		
	}

	function check_errors(){
		$obj['error']=0;
		
		if(isset($this->data['username']) && strlen($this->data['username'])<2 OR strlen($this->data['username'])>=16){
			//$obj['errors'][0][]='username length should be between 2 to 16 chars';
			$obj['errors'][]='username length should be between 2 to 16 chars';
			$obj['error']++;
		}
		
		if(isset($this->data['username']) && !preg_match("^[a-zA-Z0-9_.]+$^",$this->data['username'])){
			//$obj['errors'][0][]='username can contain only a-z, A-Z, 0-9 and _';
			$obj['errors'][]='username can contain only a-z, A-Z, 0-9 and _';
			$obj['error']++;
		}
		
		if(isset($this->data['password']) && strlen($this->data['password'])<6 OR strlen($this->data['password'])>20){
			//$obj['errors'][1][]='password length should be between 6 to 20 chars';
			$obj['errors'][]='password length should be between 6 to 20 chars';
			$obj['error']++;
		}
		
		if(isset($this->data['password']) && !preg_match("^[a-zA-Z0-9]+$^",$this->data['password'])){
			//$obj['errors'][1][]='password can contain only a-z, A-Z, 0-9 and _';
			$obj['errors'][]='password can contain only a-z, A-Z, 0-9 and _';
			$obj['error']++;
		}
		
		if(isset($this->data['passwords']) && $this->data['password']!=$this->data['passwords']){
			//$obj['errors'][2][]='passwords not compatible';
			$obj['errors'][]='passwords not compatible';
			$obj['error']++;
		}	
	
		if(!preg_match("^[0-9a-z_.-]+@([0-9a-z-]+.)+[a-z]{2,4}$^",$this->data['email'])){
			//$obj['errors'][3][]='invalid email format';
			$obj['errors'][]='invalid email format';
			$obj['error']++;
		}

		if(isset($this->data['email'])){
			// Prepare statements to get selected email.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE email=:email LIMIT 1");
			$stmt->bindParam(':email', $this->data['email'], PDO::PARAM_STR, 254);
			$stmt->execute();
			$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
				
			if($obj['item']){
				//$obj['errors'][3][]='email already exist';
				$obj['errors'][]='email already exist';
				$obj['error']++;
			}
		}
		
		if($this->data['login']!=''){
			// Prepare statements to get selected login
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE login=:login LIMIT 1");
			$stmt->bindParam(':login', $this->data['login'], PDO::PARAM_STR, 15);
			$stmt->execute();
			$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
				
			if($obj['item']){
				//$obj['errors'][0][]='login already exist';
				$obj['errors'][]='login already exist';
				$obj['error']++;
			}
		}
		
		if(isset($this->data['username'])){
			// Prepare statements to get selected username
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE username=:username LIMIT 1");
			$stmt->bindParam(':username', $this->data['username'], PDO::PARAM_STR, 15);
			$stmt->execute();
			$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
				
			if($obj['item']){
				//$obj['errors'][0][]='username already exist';
				$obj['errors'][]='username already exist';
				$obj['error']++;
			}
		}
		return $obj;
	}
	
	function item_register(){
		// Check for errors on set fields
		$obj=$this->check_errors();

		// Add user if no errors
		if(!$obj['error']){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("INSERT INTO edit_users (username, email, password, api_key, referer_id) VALUES(:username, :email, :password, :api_key, :referer_id)");
			$stmt->bindParam(':username', $this->data['username'], PDO::PARAM_STR, 254);
			$stmt->bindParam(':email', $this->data['email'], PDO::PARAM_STR, 254);
			$stmt->bindParam(':password', md5($this->data['password']), PDO::PARAM_STR, 32);
			$stmt->bindParam(':api_key', md5($this->data['password'].''.$this->data['api_key']), PDO::PARAM_STR, 32);
			$stmt->bindParam(':referer_id', intval($this->data['referer_id']), PDO::PARAM_INT);
			$stmt->execute();
				
			// Include 
			$id = $this->pdo->lastInsertId();
			
			// Return item data and send activation
			if($id>0){
				// Set fields to update
				$obj['item']=array(
					"id" => $id
				);
				
				// Prepare email template varibles
				$this->data['user_id']=$id;
				$this->data['hash']=md5(time());
				
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("INSERT INTO edit_users_activations (user_id, hash, mktime) VALUES(:user_id, :hash, :mktime)");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
				$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
				$stmt->execute();
		
				// Include 
				$in = $this->pdo->lastInsertId();
				
				if($in>0){
					//$this->send_activiation();
				}
				
				$obj['status']='ok';
			}else{
				// Close session
				session_unset();
				session_destroy();
				
				// Return error if item not created
				$obj['errors'][]='can not insert item to: edit_users_activations';
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

	//############################ USER ACTIVIATION ##################################################

	function send_activiation(){
		$tpl['source']=file_get_contents('tpl/mail/active.html');

		// Include user name to template {$user} & {$hash}
		$tpl['source']=str_replace("{\$username}",$this->data['username'],$tpl['source']);
		$tpl['source']=str_replace("{\$user_id}",$this->data['user_id'],$tpl['source']);
		$tpl['source']=str_replace("{\$hash}",$this->data['hash'],$tpl['source']);

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
		$mail->SMTPDebug = $this->data['contact_debug'];
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = $this->data['contact_host'];
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = 25;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = $this->data['contact_username'];
		//Password to use for SMTP authentication
		$mail->Password = $this->data['contact_password'];
		//Set who the message is to be sent from
		$mail->setFrom($this->data['contact_from'], 'Account activation - OnceBuilder.com');//accounts
		//Set an alternative reply-to address
		$mail->addReplyTo($this->data['email'], $this->data['name']);
		//Set who the message is to be sent to
		$mail->addAddress($this->data['email'], 'Dear friend');
		//Set the subject line
		$mail->Subject = 'Click the button to complete activation';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($tpl['source']);//file_get_contents('contents.html'), dirname(__FILE__)
		//Replace the plain text body with one created manually
		$mail->AltBody = $this->data['message'];
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');

		//send the message, check for errors
		if (!$mail->send()) {
			$obj['errors'][]=$mail->ErrorInfo;
			$obj['error']++;
		} else {
			$obj['status']='ok';
		}
		return $obj;
	}
	
	function check_activiation(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_users_activations WHERE user_id=:user_id AND hash=:hash AND actived=0 ORDER by id ASC LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
		$stmt->execute();

		// Fetch data
		$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if snippet exist.
		if($obj['item']){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("UPDATE edit_users SET type_id=0 WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();
			
			$stmt = $this->pdo->prepare("UPDATE edit_users_activations SET actived=1 WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $obj['item']['id'], PDO::PARAM_INT);
			$stmt->execute();
			
			$obj['status']='ok';
		}else{
			$obj['errors'][]='activation error';
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
	
	//############################ USER DELETION ##################################################
	function check_deletion(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_users_deletions WHERE user_id=:user_id AND hash=:hash AND actived=0 ORDER by id ASC LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
		$stmt->execute();

		// Fetch data
		$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if snippet exist.
		if($obj['item']){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("DELETE FROM edit_users WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
			//$stmt->execute();
			
			$stmt = $this->pdo->prepare("DELETE FROM edit_users_informations WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
			//$stmt->execute();

			$stmt = $this->pdo->prepare("UPDATE edit_users_deletions SET actived=1 WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $obj['item']['id'], PDO::PARAM_INT);
			//$stmt->execute();
			
			// Close session
			session_unset();
			session_destroy();
				
			$obj['status']='ok';
		}else{
			$obj['errors'][]='deletion error';
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