<?php
/**
 * Version: 1.0, 30.06.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Login plugin (once.login)
 *
*/
class once extends core{
	public $pdo;
	public $data;
	public $time;
	
	//############################ USER LOGIN #############################################
	
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
			//$obj['error']++;
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
		
		return $obj;
	}
	
	function item_login(){
		// Check for errors on set fields
		$obj=$this->check_errors();

		// Add user if no errors
		if(!$obj['error']){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE email=:email AND password=:password LIMIT 1");
			$stmt->bindParam('email', $this->data['email'], PDO::PARAM_STR, 50);
			$stmt->bindParam('password', md5($this->data['password']), PDO::PARAM_STR, 50);
			$stmt->execute();

			$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

			// Check if snippet exist.
			if($obj['item']){
				// Prepare statements to get selected id.
				/*
		
				$stmt = $this->pdo->prepare("SELECT type_id FROM edit_bany WHERE user_id=:user_id LIMIT 1");
				$stmt->bindParam(':type_id', $this->data['type_id'], PDO::PARAM_INT);
				$stmt->execute();
				$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
				switch($obj['item']['type_id']){
					case 1:
						//dodawanie nowych
					break;
					case 2:
						//user banned
					break;
					case 3:
						//total ban
						//$_SERVER['REMOTE_ADDR']
					break;
				}
				
				*/
				
				if($obj['item']['type_id']==-1){
					//$obj['errors'][6][]='username not activated';
					$obj['errors'][]='username not activated';
					$obj['error']++;
				}else{
					// Set session lifetime
					ini_set("session.gc_maxlifetime","86400");
					
					// Set session
					$_SESSION['user_logged']=true;
					
					// user data
					$_SESSION['user_id']=$obj['item']['id'];
					$_SESSION['user_login']=$obj['item']['login'];
					$_SESSION['user_type_id']=$obj['item']['type_id'];
					$_SESSION['user_username']=$obj['item']['username'];
					$_SESSION['user_email']=$obj['item']['email'];
					$_SESSION['api_key']=$obj['item']['api_key'];
					
					// user browser
					$_SESSION['user_ip']=$_SERVER['REMOTE_ADDR'];
					$_SESSION['user_status']=$obj['item']['status'];
					
					$obj['status']='ok';
				}
			}else{
				//$obj['errors'][1][]='login data is not correct';
				$obj['errors'][]='login data is not correct';
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
	
	//############################ PASSWORD CHANGE URL ####################################
	
	function send_remind(){
		$tpl['source']=file_get_contents('tpl/mail/change.html');
		
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
		$mail->setFrom('support@oncebuilder.com', 'Reset password form - OnceBuilder.com');//accounts
		//Set an alternative reply-to address
		$mail->addReplyTo($this->data['email'], $this->data['name']);
		//Set who the message is to be sent to
		$mail->addAddress($this->data['email'], 'Dear friend');
		//Set the subject line
		$mail->Subject = 'Click the button to reset password';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($tpl['source']);//file_get_contents('contents.html'), dirname(__FILE__)
		//Replace the plain text body with one created manually
		$mail->AltBody = $this->data['message'];
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');

		//send the message, check for errors
		if(!$mail->send()){
			$obj['errors'][]=$mail->ErrorInfo;
			$obj['error']++;
		}else {
			$obj['status']='ok';
		}
		return $obj;
	}
	
	function check_remind(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_users_reminds WHERE user_id=:user_id AND hash=:hash AND actived=0 ORDER by id ASC LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
		$stmt->execute();

		// Fetch data
		$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if user remind exist.
		if($obj['item']){
			$obj['status']='ok';
		}else{
			$obj['errors'][]='remind error';
			$obj['error']++;
		}
		
		return $obj;
	}

	function item_remind(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id, username FROM edit_users WHERE email=:email LIMIT 1");
		$stmt->bindParam('email', $this->data['email'], PDO::PARAM_STR, 50);
		$stmt->execute();

		$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if snippet exist.
		if($obj['item']){
			// Prepare email template varibles
			$this->data['user_id']=$obj['item']['id'];
			$this->data['hash']=md5(time());
			$this->data['username']=$obj['item']['username'];
			
			$stmt = $this->pdo->prepare("DELETE FROM edit_users_reminds WHERE user_id=:user_id");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();
			
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("INSERT INTO edit_users_reminds (user_id, hash , mktime) VALUES(:user_id, :hash, :mktime)");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
			$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
			$stmt->execute();

			$in = $this->pdo->lastInsertId();
				
			if($in>0){
				//$this->send_remind();
			}
			
			$obj['status']='ok';
		}else{
			$obj['errors'][]='Email not found in database';
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
	
	//############################ PASSWORD CHANGE ########################################
	
	function send_confirm(){
		$tpl['source']=file_get_contents('tpl/mail/confirm.html');

		// Include user name to template {$user} & {$hash}
		$tpl['source']=str_replace("{\$username}",$this->data['username'],$tpl['source']);

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
		$mail->setFrom('support@oncebuilder.com', 'Password changed - OnceBuilder.com');//accounts
		//Set an alternative reply-to address
		$mail->addReplyTo($this->data['email'], $this->data['name']);
		//Set who the message is to be sent to
		$mail->addAddress($this->data['email'], 'Dear friend');
		//Set the subject line
		$mail->Subject = 'Password has been successfully changed';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($tpl['source']);//file_get_contents('contents.html'), dirname(__FILE__)
		//Replace the plain text body with one created manually
		$mail->AltBody = $this->data['message'];
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');

		//send the message, check for errors
		if(!$mail->send()) {
			$obj['errors'][]=$mail->ErrorInfo;
			$obj['error']++;
		}else{
			$obj['status']='ok';
		}
	}
	
	function item_change(){
		// Check for errors on set fields

		if(isset($this->data['password']) && strlen($this->data['password'])<6 OR strlen($this->data['password'])>20){
			$obj['errors'][1][]='password length should be between 6 to 20 chars';
			$obj['error']++;
		}
		
		if(isset($this->data['password']) && !preg_match("^[a-zA-Z0-9]+$^",$this->data['password'])){
			//$obj['errors'][1][]='password can contain only a-z, A-Z, 0-9 and _';
			$obj['errors'][]='password can contain only a-z, A-Z, 0-9 and _';
			$obj['error']++;
		}
		
		// Add user if no errors
		if(!$obj['error']){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users_reminds WHERE user_id=:user_id AND hash=:hash AND actived=0 ORDER by id ASC LIMIT 1");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
			$stmt->execute();

			// Fetch data
			$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

			// Check if snippet exist.
			if($obj['item']){
				$stmt = $this->pdo->prepare("SELECT id, username, email FROM edit_users WHERE id=:user_id AND type_id>=0 LIMIT 1");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Fetch data
				$obj['user'] = $stmt->fetch(PDO::FETCH_ASSOC);
				if($obj['user']){
					// Prepare statements to get selected id.
					$stmt = $this->pdo->prepare("UPDATE edit_users SET password=:password WHERE id=:user_id LIMIT 1");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':password', md5($this->data['password']), PDO::PARAM_INT);
					$stmt->execute();
					
					$stmt = $this->pdo->prepare("UPDATE edit_users_reminds SET actived=1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $obj['item']['id'], PDO::PARAM_INT);
					//$stmt->execute();
					
					// Prepare email template varibles
					$this->data['username']=$obj['user']['username'];
					$this->data['email']=$obj['user']['email'];
					
					// Send confirmation
					//$this->send_confirm();
					
					$obj['status']='ok';
				}else{
					$obj['errors'][]='user not found';
					$obj['error']++;
				}
			}else{
				$obj['errors'][]='change error';
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
	
}
?>