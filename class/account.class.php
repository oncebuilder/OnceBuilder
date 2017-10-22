<?php
/**
 * Version: 1.0, 04.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Account plugin (once.account)
 *
*/

class once extends core{
	//############################ VALIDATION ##################################################
	function validate_password(){
		if(isset($this->data['newpassword']) && strlen($this->data['newpassword'])<6 OR strlen($this->data['newpassword'])>20){
			$this->set_error('Password length should be between 6 to 20 chars');
		}
		if(isset($this->data['newpassword']) && !preg_match("^[a-zA-Z0-9]+$^",$this->data['newpassword'])){
			$this->set_error('Password can contain only a-z, A-Z, 0-9 and _');
		}
		if(isset($this->data['confirmpassword']) && $this->data['confirmpassword']!=$this->data['confirmpassword']){
			$this->set_error('Passwords not compatible');
		}
	}
	//############################ ACCOUNT #############################################
	function get_profile_data(){
		// Prepare statements to get user information.
		$stmt = $this->pdo->prepare("SELECT * FROM edit_users_informations WHERE user_id=:user_id LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR, 50);
		$stmt->execute();

		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $this->once_response();
	}
	function get_social_data(){
		// Prepare statements to get user information.
		$stmt = $this->pdo->prepare("SELECT * FROM edit_users_socials WHERE user_id=:user_id LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR, 50);
		$stmt->execute();

		$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $this->once_response();
	}
	function account_terminiate(){
		//@2do verify + reson
		
		if($this->error==0){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("SELECT username, password FROM edit_users WHERE id=:user_id LIMIT 1");
			$stmt->bindParam('user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
			if($this->item){
				if(!password_verify($this->data['password'], $this->item['password'])){
					$this->set_error('Wrong password');
				}else{
					// Prepare email template varibles
					$this->data['user_id']=$this->data['user_id'];
					$this->data['username']=$this->item['username'];
					$this->data['email']=$this->item['email'];
					$this->data['hash']=md5(time());
						
					// Prepare statements to get selected id.
					$stmt = $this->pdo->prepare("INSERT INTO edit_users_deletions (user_id, hash, mktime, reason) VALUES(:user_id, :hash, :mktime, :reason)");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
					$stmt->bindParam(':mktime', $this->data['mktime'], PDO::PARAM_INT);
					$stmt->bindParam(':reason', $this->data['reason'], PDO::PARAM_STR, 32);
					$stmt->execute();
					
					// Include 
					$id = $this->pdo->lastInsertId();

					if($id>0){
						// Send deletion link
						$this->send_deletion();
					}else{
						$this->set_error('Can not insert item to: edit_users_deletions');
					}
				}
			}else{
				$this->set_error('User not exists');
			}
		}
		return $this->once_response();
	}
	function change_password(){
		$this->validate_password();

		if($this->error==0){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("SELECT password FROM edit_users WHERE id=:user_id LIMIT 1");
			$stmt->bindParam('user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
			if($this->item){
				if(!password_verify($this->data['password'], $this->item['password'])){
					$this->set_error('Wrong password');
				}else{
					$hash = password_hash($this->data['newpassword'], PASSWORD_DEFAULT);
					// Prepare statements to update user password.
					$stmt = $this->pdo->prepare("UPDATE edit_users SET password=:password WHERE id=:user_id LIMIT 1");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':password', $hash, PDO::PARAM_STR, 255);
					$stmt->execute();
				}
			}else{
				$this->set_error('User not exists');
			}
		}
		return $this->once_response();
	}
	function save_information(){
		//@2do some validate functions here l8er

		if($this->error==0){
			// Prepare statements to get user information.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users_informations WHERE user_id=:user_id LIMIT 1");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR, 50);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
			if($this->item){
				// Prepare statements to update user information.
				$stmt = $this->pdo->prepare("
					UPDATE edit_users_informations SET firstname=:firstname, lastname=:lastname, website=:website, company=:company, address=:address, address2=:address2, city=:city, phone=:phone, zip=:zip, province=:province, country=:country
					WHERE user_id=:user_id LIMIT 1");
				
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':firstname', $this->data['firstname'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':lastname', $this->data['lastname'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':website', $this->data['website'], PDO::PARAM_STR, 2000);
				$stmt->bindParam(':company', $this->data['company'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':address', $this->data['address'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':address2', $this->data['address2'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':city', $this->data['city'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':phone', $this->data['phone'], PDO::PARAM_STR, 15);
				$stmt->bindParam(':zip', $this->data['zip'], PDO::PARAM_STR, 10);
				$stmt->bindParam(':province', $this->data['province'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':country', $this->data['country'], PDO::PARAM_STR, 100);
				$stmt->execute();
			}else{
				// Prepare statements to insert user information.
				$stmt = $this->pdo->prepare("
					INSERT INTO edit_users_informations (user_id, firstname, lastname, email, website, company, address, address2, city, phone, zip, province, country) 
					VALUES (:user_id, :firstname, :lastname, :email, :website, :company, :address, :address2, :city, :phone, :zip, :province, :country)
				");
				
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':firstname', $this->data['firstname'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':lastname', $this->data['lastname'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':email', $this->data['email'], PDO::PARAM_STR, 254);
				$stmt->bindParam(':website', $this->data['website'], PDO::PARAM_STR, 2000);
				$stmt->bindParam(':company', $this->data['company'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':address', $this->data['address'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':address2', $this->data['address2'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':city', $this->data['city'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':phone', $this->data['phone'], PDO::PARAM_STR, 15);
				$stmt->bindParam(':zip', $this->data['zip'], PDO::PARAM_STR, 10);
				$stmt->bindParam(':province', $this->data['province'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':country', $this->data['country'], PDO::PARAM_STR, 100);
				$stmt->execute();
			}
		}
		return $this->once_response();
	}
	function save_profile(){
		//@2do
	
		if($this->error==0){
			// Prepare statements to get user information.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE id=:user_id LIMIT 1");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR, 50);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
			if($this->item){
				// Prepare statements to get user information.
				$stmt = $this->pdo->prepare("SELECT * FROM edit_users_informations WHERE user_id=:user_id LIMIT 1");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR, 50);
				$stmt->execute();

				$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
				if($this->item){
					// Prepare statements to update user information.
					$stmt = $this->pdo->prepare("UPDATE edit_users_informations SET firstname=:firstname, lastname=:lastname, position=:position, location=:location WHERE user_id=:user_id LIMIT 1");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':firstname', $this->data['firstname'], PDO::PARAM_STR, 50);
					$stmt->bindParam(':lastname', $this->data['lastname'], PDO::PARAM_STR, 50);
					$stmt->bindParam(':position', $this->data['position'], PDO::PARAM_STR, 50);
					$stmt->bindParam(':location', $this->data['location'], PDO::PARAM_STR, 50);
					$stmt->execute();
				}else{
					// Prepare statements to insert user information.
					$stmt = $this->pdo->prepare("
						INSERT INTO edit_users_informations (user_id, firstname, lastname, position, location) 
						VALUES (:user_id, :firstname, :lastname, :position, :location)
					");
					$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
					$stmt->bindParam(':firstname', $this->data['firstname'], PDO::PARAM_STR, 50);
					$stmt->bindParam(':lastname', $this->data['lastname'], PDO::PARAM_STR, 50);
					$stmt->bindParam(':position', $this->data['position'], PDO::PARAM_STR, 50);
					$stmt->bindParam(':location', $this->data['location'], PDO::PARAM_STR, 50);
					$stmt->execute();
				}
			}else{
				$this->set_error('User not exists');
			}
		}
		return $this->once_response();
	}
	function save_social(){
		//@2do some validate functions here l8er

		if($this->error==0){
			// Prepare statements to get user information.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users_socials WHERE user_id=:user_id LIMIT 1");
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR, 50);
			$stmt->execute();

			if($stmt->rowCount()){
				// Prepare statements to update user information.
				$stmt = $this->pdo->prepare("
					UPDATE edit_users_socials SET facebook=:facebook, twitter=:twitter, youtube=:youtube, linkedin=:linkedin, dribbble=:dribbble, github=:github, google=:google, behance=:behance, codepen=:codepen 
					WHERE user_id=:user_id LIMIT 1");
				
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':facebook', $this->data['facebook'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':twitter', $this->data['twitter'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':youtube', $this->data['youtube'], PDO::PARAM_STR, 2000);
				$stmt->bindParam(':linkedin', $this->data['linkedin'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':dribbble', $this->data['dribbble'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':github', $this->data['github'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':google', $this->data['google'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':behance', $this->data['behance'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':codepen', $this->data['codepen'], PDO::PARAM_STR, 100);
				$stmt->execute();
			}else{
				// Prepare statements to insert user information.
				$stmt = $this->pdo->prepare("
					INSERT INTO user_id (facebook, facebook, twitter, youtube, linkedin, dribbble, github, google, behance, codepen) 
					VALUES (:user_id, :facebook, :twitter, :youtube, :linkedin, :dribbble, :github, :google, :behance, :codepen)
				");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(':facebook', $this->data['facebook'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':twitter', $this->data['twitter'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':youtube', $this->data['youtube'], PDO::PARAM_STR, 2000);
				$stmt->bindParam(':linkedin', $this->data['linkedin'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':dribbble', $this->data['dribbble'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':github', $this->data['github'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':google', $this->data['google'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':behance', $this->data['behance'], PDO::PARAM_STR, 100);
				$stmt->bindParam(':codepen', $this->data['codepen'], PDO::PARAM_STR, 100);
				$stmt->execute();
			}
		}
		return $this->once_response();
	}
	function send_deletion(){
		$tpl['source']=file_get_contents('tpl/mail/deletion.html');

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
		$mail->setFrom($this->data['contact_from'], 'Account deletion - OnceBuilder.com');//accounts
		//Set an alternative reply-to address
		$mail->addReplyTo($this->data['email'], $this->data['name']);
		//Set who the message is to be sent to
		$mail->addAddress('support@oncebuilder.com', 'Dear friend');
		//Set the subject line
		$mail->Subject = 'Click the button to complete deletion';
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
	function upload_image(){
		if($this->once_csrf_token_check($this->data['csrf_token'])){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("SELECT id FROM edit_users WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

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
								@mkdir($this->data['root_path']."/once/users/".$this->data['user_id']."");
								chmod($this->data['root_path']."/once/users/".$this->data['user_id']."", 0777);
									
								// Move uploaded file to upload dir
								move_uploaded_file($this->data["image"]["tmp_name"],$this->data['root_path'].'/once/users/'.$this->data['user_id'].'/thumbnail.png');
								
								// Resize image
								$this->once_image_resize($this->data['root_path'].'/once/users/'.$this->data['user_id'].'/thumbnail.png',170,170);
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
				$this->set_error('User not exist!');
			}
		}
		return $this->once_response();
	}
}
?>