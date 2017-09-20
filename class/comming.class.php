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
	function check_errors(){
		$obj['error']=0;
	
		if(!preg_match("^[0-9a-z_.-]+@([0-9a-z-]+.)+[a-z]{2,4}$^",$this->data['email'])){
			//$obj['errors'][3][]='invalid email format';
			$obj['errors'][]='invalid email format';
			$obj['error']++;
		}

		if(isset($this->data['email'])){
			// Prepare statements to get selected email.
			$stmt = $this->pdo->prepare("SELECT email FROM edit_users WHERE email=:email LIMIT 1");
			$stmt->bindParam(':email', $this->data['email'], PDO::PARAM_STR, 254);
			$stmt->execute();
			$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);
				
			if($obj['item']){
				//$obj['errors'][3][]='email already exist';
				$obj['errors'][]='email already exist';
				$obj['error']++;
			}
		}
		return $obj;
	}
	
	function api_key_request(){//ok
		// Check for errors on set fields
		$obj=$this->check_errors();

		// Add user if no errors
		if(!$obj['error']){
			// Prepare statements to get selected id.
			$stmt = $this->pdo->prepare("INSERT INTO edit_users (email, password, api_key, referer_id) VALUES(:email, :password, :api_key, :referer_id)");
			$stmt->bindParam(':email', $this->data['email'], PDO::PARAM_STR, 254);
			$stmt->bindParam(':password', md5($this->data['time']), PDO::PARAM_STR, 32);
			$stmt->bindParam(':api_key', md5($this->data['time'].''.$this->data['api_key'].''.rand(1,1000)), PDO::PARAM_STR, 32);
			$stmt->bindParam(':referer_id', $this->data['referer_id'], PDO::PARAM_INT);
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
					$obj=$this->send_download();
				}else{
					// Return error if item not created
					$obj['errors'][]='can not insert item to: edit_users_activations';
					$obj['error']++;
				}
			}else{
				// Close session
				session_unset();
				session_destroy();
				
				// Return error if item not created
				$obj['errors'][]='can not insert item to: edit_users_activations';
				$obj['error']++;
			}
		}
		
		// Print JSON object
		echo json_encode($obj);
	}
	
	//############################ USER ACTIVIATION ##################################################
	
	function send_download(){
		$tpl['source']=file_get_contents('tpl/mail/download.html');

		// Include user name to template {$user} & {$hash}
		$tpl['source']=str_replace("{\$api_key}",$this->data['api_key'],$tpl['source']);
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
		$mail->SMTPDebug = $this->data['comming_debug'];
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = $this->data['comming_host'];
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $this->data['comming_port'];
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = $this->data['comming_username'];
		//Password to use for SMTP authentication
		$mail->Password = $this->data['comming_password'];
		//Set who the message is to be sent from
		$mail->setFrom($this->data['comming_from'], 'OnceBuilder.com - API');//accounts
		//Set who the message is to be sent to
		$mail->addAddress($this->data['email'], 'Dear OnceBuilder friend');
		//Set the subject line
		$mail->Subject = 'Click the button to complete API key activation';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($tpl['source']);//file_get_contents('contents.html'), dirname(__FILE__)
		//Replace the plain text body with one created manually
		//$mail->AltBody = $this->data['message'];
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
	
	function item_download(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT id FROM edit_users_activations WHERE user_id=:user_id AND hash=:hash AND actived=0 ORDER by id ASC LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':hash', $this->data['hash'], PDO::PARAM_STR, 32);
		$stmt->execute();

		// Fetch data
		$obj['item'] = $stmt->fetch(PDO::FETCH_ASSOC);

		// Check if snippet exist.
		if($stmt->rowCount()){
			// Prepare statements to get selected email.
			$stmt2 = $this->pdo->prepare("SELECT api_key FROM edit_users WHERE id=:user_id LIMIT 1");
			$stmt2->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt2->execute();
			$obj2['item'] = $stmt2->fetch(PDO::FETCH_ASSOC);
				
			if($obj2['item']){
				// Prepare statements to get selected id.
				$stmt = $this->pdo->prepare("UPDATE edit_users SET type_id=0 WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
				
				$stmt = $this->pdo->prepare("UPDATE edit_users_activations SET actived=1 WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $obj['item']['id'], PDO::PARAM_INT);
				$stmt->execute();
					
				// Load ZipArchive class to procces download project as zip archive
				if(!extension_loaded('zip')){
					dl('zip.so');
				}

				copy('../once/once.zip','../once/once1.zip');
					
				$zip = new ZipArchive;
				$fileToModify = 'installer.php';
				$archiveName = '../once/once1.zip';
					
				if ($zip->open($archiveName) === TRUE) {
					//Read contents into memory
					$oldContents = $zip->getFromName($fileToModify);

					//Modify contents:
					$newContents = str_replace('d41d8cd98f00b204e9800998ecf8427e', $obj2['item']['api_key'], $oldContents);
					
					//Delete the old...
					$zip->deleteName($fileToModify);
					
					//Write the new...
					$zip->addFromString($fileToModify, $newContents);
					
					///And write back to the filesystem.
					$zip->close();
		
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
					$obj['errors'][]='can not open archive';
					$obj['error']++;
				}
			}
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
}
?>