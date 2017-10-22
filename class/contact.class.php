<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is OnceBuilder Contact plugin (once.contact)
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
	//############################ CONTACT ##################################################
	function send_message(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Check if recaptcha is acvited
			if($this->data['recaptcha_contact']){
				// Load library
				require_once($this->data['root_path'].'/once/libs/recaptcha/php/recaptchalib.php');
				// Create a new instance
				$reCaptcha = new ReCaptcha($this->data['recaptcha_secret']);
				
				// The response from reCAPTCHA
				$resp = null;
				
				// The error code from reCAPTCHA, if any
				$error = null;
				
				// Was there a reCAPTCHA response?
				if ($_POST["g-recaptcha-response"]) {
					$resp = $reCaptcha->verifyResponse(
						$_SERVER["REMOTE_ADDR"],
						$_POST["g-recaptcha-response"]
					);
				}
				
				if ($resp != null && $resp->success) {
					// Continiue code
				}else{
					$this->set_error('Recaptcha response is wrong, check if it configured properly');
					$this->set_error($error);
				}
			}
			
			// Check email if is ok
			$this->validate_email();

			// Check if sending email is acvited
			if($this->data['contact_mailme']==1 && $this->error==0){
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
				// Enable SMTP debugging
				// 0 = off (for production use)
				// 1 = client messages
				// 2 = client and server messages
				$mail->SMTPDebug = $this->data['contact_debug'];
				// Ask for HTML-friendly debug output
				$mail->Debugoutput = 'html';
				// Set the hostname of the mail server
				$mail->Host = $this->data['contact_host'];
				// Set the SMTP port number - likely to be 25, 465 or 587
				$mail->Port = $this->data['contact_port'];
				// Whether to use SMTP authentication
				$mail->SMTPAuth = true;
				// Username to use for SMTP authentication
				$mail->Username = $this->data['contact_username'];
				// Password to use for SMTP authentication
				$mail->Password = $this->data['contact_password'];
				// Set who the message is to be sent from
				$mail->setFrom($this->data['contact_from'], 'Contact Form');
				// Set an alternative reply-to address
				$mail->addReplyTo($this->data['email'], $this->data['name']);
				// Set who the message is to be sent to
				$mail->addAddress($this->data['contact_to'], 'Contact Form');
				// Set the subject line
				$mail->Subject = $this->data['name'].' wrote:';
				// Read an HTML message body from an external file, convert referenced images to embedded,
				// convert HTML into a basic plain-text alternative body
				$mail->msgHTML($this->data['message']);//file_get_contents('contents.html'), dirname(__FILE__)
				// Replace the plain text body with one created manually
				$mail->AltBody = $this->data['message'];
				// Attach an image file
				// $mail->addAttachment('images/phpmailer_mini.png');

				// Send the message, check for errors
				if(!$mail->send()){
					$this->set_error($mail->ErrorInfo);
				}
			}
			
			if($this->data['contact_mailbox']==1 && $this->error==0){
				// Check if user exist and person exists on contact list already
				$stmt = $this->pdo->prepare("SELECT * FROM edit_users WHERE email=:email LIMIT 1");
				$stmt->bindParam(':email', $this->data['email'], PDO::PARAM_STR, 30);
				$stmt->execute();
				if($stmt->rowCount()){
					$this->item=$stmt->fetch(PDO::FETCH_ASSOC);
					// Get the user name from DB
					$this->data['user_id']=$this->item['id'];
				}else{
					// Set it as Guest
					$this->data['user_id']=0;
				}
				
				// Insert new contact if not exist
				$stmt2 = $this->pdo->prepare("SELECT * FROM edit_mailbox_contacts WHERE email=:email LIMIT 1");
				$stmt2->bindParam(':email', $this->data['email'], PDO::PARAM_STR, 30);
				$stmt2->execute();
				if(!$stmt2->rowCount()){
					$stmt = $this->pdo->prepare("
						INSERT INTO edit_mailbox_contacts (user_id, email) 
						VALUES(".$this->data['user_id'].", '".$this->data['email']."')
					");
					$stmt->execute();
				}
				
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("
					INSERT INTO edit_mailbox (user_id, title, message, time) 
					VALUES('".$this->data['user_id']."', '".$this->data['title']."', '".$this->data['message']."', ".$this->data['time'].")
				");
				$stmt->execute();
				
				// Get last insert
				$this->item=array(
					"id" => $this->pdo->lastInsertId()
				);
				
				// Check last insert
				if(!$this->item['id']){
					// Return error if item not created
					$this->set_error('Can not insert item to: edit_mailbox');
				}
			}

			if($this->data['contact_mailme']==0 && $this->data['contact_mailbox']==0){
				$this->set_error('Your contact variables $_CONFIG[contact_mailme] and $_CONFIG[contact_mailbox] are both not configured');
			}
		}
		return $this->once_response();
	}
}
?>