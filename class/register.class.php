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
	//############################ VALIDATION ##################################################
	function validate_email(){
		$isValid = true;
		$atIndex = strrpos($this->data['email'], "@");
		$domain = substr($this->data['email'], $atIndex+1);
		$local = substr($email, 0, $atIndex);
		if (is_bool($atIndex) && !$atIndex){
			$this->set_error('Email without @ ?');
		}
		if (strlen($domain)==0){
			$this->set_error('Domain cannot be empty');
			$isValid=false;
		}
		if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))){
			$this->set_error('Domain not found in DNS');
		}
		if(!preg_match("/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i",$this->data['email'])){
			$this->set_error('Invalid email format');
		}
		if(isset($this->data['email'])){
			// Prepare statements to get selected email.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE email=:email LIMIT 1");
			$stmt->bindParam(':email', $this->data['email'], PDO::PARAM_STR, 254);
			$stmt->execute();
			if($stmt->rowCount()){
				$this->set_error('Email already exist in database');
			}
		}
	}
	function validate_username(){
		if(isset($this->data['username']) && strlen($this->data['username'])<2 OR strlen($this->data['username'])>=16){
			$this->set_error('Username length should be between 2 to 16 chars');
		}
		if(isset($this->data['username']) && !preg_match("^[a-zA-Z0-9_.]+$^",$this->data['username'])){
			$this->set_error('Username can contain only a-z, A-Z, 0-9 and _');
		}
		if(isset($this->data['Username'])){
			// Prepare statements to get selected username
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE username=:username LIMIT 1");
			$stmt->bindParam(':Username', $this->data['username'], PDO::PARAM_STR, 15);
			$stmt->execute();
			if($stmt->rowCount()){
				$this->set_error('Username already exist in database');
			}
		}
	}
	function validate_password(){
		if(isset($this->data['password']) && strlen($this->data['password'])<6 OR strlen($this->data['password'])>20){
			$this->set_error('Password length should be between 6 to 20 chars');
		}
		if(isset($this->data['password']) && !preg_match("^[a-zA-Z0-9]+$^",$this->data['password'])){
			$this->set_error('Password can contain only a-z, A-Z, 0-9 and _');
		}
		if(isset($this->data['passwords']) && $this->data['password']!=$this->data['passwords']){
			$this->set_error('Passwords not compatible');
		}	
	}
	function validate_login(){
		if(isset($this->data['login']) && strlen($this->data['login'])>0){
			if(strlen($this->data['login'])<2 OR strlen($this->data['login'])>=16){
				$this->set_error('Login length should be between 2 to 16 chars');
			}
			if(!preg_match("^[a-zA-Z0-9_.]+$^",$this->data['login'])){
				$this->set_error('Login can contain only a-z, A-Z, 0-9 and _');
			}
			if(isset($this->data['login'])){
				// Prepare statements to get selected login
				$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE login=:login LIMIT 1");
				$stmt->bindParam(':login', $this->data['login'], PDO::PARAM_STR, 15);
				$stmt->execute();
				if($stmt->rowCount()){
					$this->set_error('Login already exist in database');
				}
			}
		}
	}
	//############################ REGISTER ##################################################
	function check_activiation(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_users_activations WHERE user_id=:user_id AND hash=:hash AND actived=0 ORDER by id ASC LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
		$stmt->execute();

		// Fetch data
		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if snippet exist.
		if($this->item){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("UPDATE edit_users SET type_id=0 WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();
			
			$stmt = $this->pdo->prepare("UPDATE edit_users_activations SET actived=1 WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->item['id'], PDO::PARAM_INT);
			$stmt->execute();
		}else{
			$this->set_error('Could not verify activation hash / or it has been actived');
		}
		return $this->once_response();
	}
	function check_deletion(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_users_deletions WHERE user_id=:user_id AND hash=:hash AND actived=0 ORDER by id ASC LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
		$stmt->execute();

		// Fetch data
		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if snippet exist.
		if($this->item){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("DELETE FROM edit_users WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
			//$stmt->execute();
			
			$stmt = $this->pdo->prepare("DELETE FROM edit_users_informations WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
			//$stmt->execute();

			$stmt = $this->pdo->prepare("UPDATE edit_users_deletions SET actived=1 WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->item['id'], PDO::PARAM_INT);
			//$stmt->execute();
			
			$this->set_error('Function currently is disabled, please <a href="/contact">contact us</a>');
			
			// Close session
			session_unset();
			session_destroy();
		}else{
			$this->set_error('Could not delete / or user has been deleted');
		}
		return $this->once_response();
	}
	function item_register(){
		$this->validate_email();
		$this->validate_username();
		$this->validate_password();
		$this->validate_login();

		// Add user if no errors
		if($this->error==0){
			// Check how many regs already
			$stmt = $this->pdo->prepare("SELECT * FROM edit_users_activations WHERE user_ip=:user_ip AND mktime+86000>:mktime LIMIT 3");
			$stmt->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 30);
			$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()==3){
				$this->set_error('You have done more than 3 registration today, <a href="/contact">contact us</a> for explanation');
			}
			
			// Check if user IP is banned
			$stmt = $this->pdo->prepare("SELECT * FROM edit_users_bans WHERE user_ip=:user_ip LIMIT 1");
			$stmt->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 30);
			$stmt->execute();
			if($stmt->rowCount()){
				$this->set_error('You have been banned from our service, <a href="/contact">contact us</a> for explanation');
			}

			if($this->error==0){
				$hash = password_hash($this->data['password'], PASSWORD_DEFAULT);
				
				if($this->data['register_reqactivation']==1){$type_id=0;}else{$type_id=-1;}

				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("INSERT INTO edit_users (username, email, password, api_key, referer_id, type_id) VALUES(:username, :email, :password, :api_key, :referer_id, :type_id)");
				$stmt->bindParam(':username', $this->data['username'], PDO::PARAM_STR, 254);
				$stmt->bindParam(':email', $this->data['email'], PDO::PARAM_STR, 254);
				$stmt->bindParam(':password', $hash, PDO::PARAM_STR, 255);
				$stmt->bindParam(':api_key', md5($hash.''.$this->data['api_key']), PDO::PARAM_STR, 32);
				$stmt->bindParam(':referer_id', $this->data['referer_id'], PDO::PARAM_INT);
				$stmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);
				$stmt->execute();
					
				// Include 
				$id = $this->pdo->lastInsertId();
				
				// Return item data and send activation
				if($id>0){
					// Prepare email template varibles
					$this->data['user_id']=$id;
					$this->data['hash']=md5($this->data['time']);
					
					// Prepare statements to get selected id.
					$stmt = $this->pdo->prepare("INSERT INTO edit_users_activations (user_id, hash, user_ip, mktime) VALUES(:user_id, :hash, :user_ip, :mktime)");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
					$stmt->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 32);
					$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
					$stmt->execute();

					if($this->pdo->lastInsertId()>0){
						if($this->data['register_reqactivation']==1){
							$this->send_activiation();
						}
					}else{
						$this->set_error('Can not insert item to: edit_users_activations');
					}
				}else{
					session_unset();
					session_destroy();
					$this->set_error('Can not insert item to: edit_users');
				}
			}
		}
		return $this->once_response();
	}
	function send_activiation(){
		$tpl['source']=file_get_contents('tpl/mail/active.html');

		// Include user name to template {$user} & {$hash}
		$tpl['source']=str_replace("{\$username}",$this->data['username'],$tpl['source']);
		$tpl['source']=str_replace("{\$user_id}",$this->data['user_id'],$tpl['source']);
		$tpl['source']=str_replace("{\$hash}",$this->data['hash'],$tpl['source']);

		//SMTP needs accurate times, and the PHP time zone MUST be set
		//This should be done in your php.ini, but this is how to do it if you don't have access to that
		date_default_timezone_set('Etc/UTC');
		// Load library
		require_once($this->data['root_path'].'/once/libs/phpmailer/phpmailerautoload.php');
		// Create a new instance
		$mail = new PHPMailer;
		// Some funny options for SSL websites
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		// Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = $this->data['register_debug'];
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = $this->data['register_host'];
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $this->data['register_port'];
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = $this->data['register_username'];
		//Password to use for SMTP authentication
		$mail->Password = $this->data['register_password'];
		//Set who the message is to be sent from
		$mail->setFrom($this->data['register_from'], 'Account activation - OnceBuilder.com');//accounts
		//Set an alternative reply-to address
		$mail->addReplyTo($this->data['email'], $this->data['username']);
		//Set who the message is to be sent to
		$mail->addAddress($this->data['email'], 'Dear '.$this->data['username']);
		//Set the subject line
		$mail->Subject = 'Click the button to complete activation';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($tpl['source']);//file_get_contents('contents.html'), dirname(__FILE__)
		//Replace the plain text body with one created manually
		$mail->AltBody = $this->data['message'];
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');

		// Send the message, check for errors
		if(!$mail->send()){
			$this->set_error($mail->ErrorInfo);
		}
	}
}
?>