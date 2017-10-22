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
	//############################ LOGIN ##################################################
	function check_remind(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_users_reminds WHERE user_id=:user_id AND hash=:hash AND actived=0 ORDER by id ASC LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
		$stmt->execute();

		// Fetch data
		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if snippet exist.
		if(!$this->item){
			$this->set_error('Could not verify remind hash / or password has been change');
		}
		return $this->once_response();
	}
	function item_change(){
		$this->validate_password();
		
		// Change user if no errors
		if($this->error==0){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users_reminds WHERE user_id=:user_id AND hash=:hash AND actived=0 ORDER by id ASC LIMIT 1");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
			$stmt->execute();

			// Fetch data
			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

			// Check if snippet exist.
			if($this->item){
				$stmt = $this->pdo->prepare("SELECT id, username, email FROM edit_users WHERE id=:user_id AND type_id>=0 LIMIT 1");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				// Fetch data
				$obj['user'] = $stmt->fetch(PDO::FETCH_ASSOC);
				if($obj['user']){
					$hash = password_hash($this->data['password'], PASSWORD_DEFAULT);
					
					// Prepare statements to get selected id.
					$stmt = $this->pdo->prepare("UPDATE edit_users SET password=:password WHERE id=:user_id LIMIT 1");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':password', $hash, PDO::PARAM_INT);
					$stmt->execute();
					
					$stmt = $this->pdo->prepare("UPDATE edit_users_reminds SET actived=1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->item['id'], PDO::PARAM_INT);
					$stmt->execute();
					
					// Prepare email template varibles
					$this->data['username']=$obj['user']['username'];
					$this->data['email']=$obj['user']['email'];
					
					// Send confirmation
					$this->send_confirm();
				}else{
					$this->set_error('User not found');
				}
			}else{
				$this->set_error('Change error');
			}
		}
		return $this->once_response();
	}
	function item_login(){
		$this->validate_email();
		$this->validate_password();

		// Login user if no errors
		if($this->error==0){
			// Check how many fail logins already
			$stmt = $this->pdo->prepare("SELECT * FROM edit_users_logs WHERE user_ip=:user_ip AND mktime+600>:mktime LIMIT 3");
			$stmt->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 30);
			$stmt->bindParam(':mktime', $this->data['time'], PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()==3){
				$this->set_error('You have done more than 3 fail in 10 minutes, try later');
			}
			if($this->error==0){
				// Prepare statements to get selected user by email.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE email=:email LIMIT 1");
				$stmt->bindParam('email', $this->data['email'], PDO::PARAM_STR, 50);
				$stmt->execute();

				$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

				if($this->item){
					if(!password_verify($this->data['password'], $this->item['password'])){
						$this->set_error('Wrong password');
					}else{
						// Check for bans
						$stmt = $this->pdo->prepare("SELECT type_id FROM edit_users_logs WHERE user_id=:user_id OR user_ip=:user_ip LIMIT 1");
						$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
						$stmt->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 30);
						$stmt->execute();
						if($stmt->rowCount()){
							$obj2['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
							switch($obj2['item']['type_id']){
								case 1:
									// Post ban
								break;
								case 2:
									// User ban
								break;
								case 3:
									// Total ban
								break;
							}
							$this->set_error('You have been banned');
						}
						
						if($this->error==0){
							if($this->item['type_id']==-1){
								$this->set_error('User not activated');
							}else{
								// Set session lifetime
								ini_set("session.gc_maxlifetime","86400");
								
								// Set session & user data
								$_SESSION['user_logged']=true;
								$_SESSION['user_id']=$this->item['id'];
								$_SESSION['user_login']=$this->item['login'];
								$_SESSION['user_type_id']=$this->item['type_id'];
								$_SESSION['user_username']=$this->item['username'];
								$_SESSION['user_email']=$this->item['email'];
								$_SESSION['api_key']=$this->item['api_key'];
								$_SESSION['user_status']=$this->item['status'];
							}
						}
					}
				}else{
					$this->set_error('Login data is not correct');
				}
			}
		}
		return $this->once_response();
	}
	function item_remind(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id, username FROM edit_users WHERE email=:email LIMIT 1");
		$stmt->bindParam('email', $this->data['email'], PDO::PARAM_STR, 50);
		$stmt->execute();

		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if snippet exist.
		if($this->item){
			// Prepare email template varibles
			$this->data['user_id']=$this->item['id'];
			$this->data['hash']=md5(time());
			$this->data['username']=$this->item['username'];
			
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
				$this->send_remind();
			}
		}else{
			$this->set_error('Email not found in database');
		}
		return $this->once_response();
	}
	function send_remind(){
		$tpl['source']=file_get_contents('tpl/mail/change.html');
		
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
		$mail->SMTPDebug = $this->data['login_debug'];
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = $this->data['login_host'];
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $this->data['login_port'];
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = $this->data['login_username'];
		//Password to use for SMTP authentication
		$mail->Password = $this->data['login_password'];
		//Set who the message is to be sent from
		$mail->setFrom($this->data['login_from'], 'Reset password - OnceBuilder.com');//accounts
		//Set an alternative reply-to address
		$mail->addReplyTo($this->data['email'], $this->data['username']);
		//Set who the message is to be sent to
		$mail->addAddress($this->data['email'], 'Dear '.$this->data['username']);
		//Set the subject line
		$mail->Subject = 'Click the button to reset password';
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
	function send_confirm(){
		$tpl['source']=file_get_contents('tpl/mail/confirm.html');

		// Include user name to template {$user} & {$hash}
		$tpl['source']=str_replace("{\$username}",$this->data['username'],$tpl['source']);

		///SMTP needs accurate times, and the PHP time zone MUST be set
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
		$mail->SMTPDebug = $this->data['login_debug'];
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = $this->data['login_host'];
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $this->data['login_port'];
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = $this->data['login_username'];
		//Password to use for SMTP authentication
		$mail->Password = $this->data['login_password'];
		//Set who the message is to be sent from
		$mail->setFrom($this->data['login_from'], 'Password changed - OnceBuilder.com');//accounts
		//Set an alternative reply-to address
		$mail->addReplyTo($this->data['email'], $this->data['username']);
		//Set who the message is to be sent to
		$mail->addAddress($this->data['email'], 'Dear '.$this->data['username']);
		//Set the subject line
		$mail->Subject = 'Password has been successfully changed';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($tpl['source']);//file_get_contents('contents.html'), dirname(__FILE__)
		//Replace the plain text body with one created manually
		$mail->AltBody = $this->data['message'];
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');

		if(!$mail->send()){
			$this->set_error($mail->ErrorInfo);
		}
	}
}
?>