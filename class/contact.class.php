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
	function send_message(){
		//SMTP needs accurate times, and the PHP time zone MUST be set
		//This should be done in your php.ini, but this is how to do it if you don't have access to that
		date_default_timezone_set('Etc/UTC');
		
		require_once($this->data['root_path'].'/once/libs/phpmailer/phpmailerautoload.php');
		
		/**
		 * This example shows making an SMTP connection with authentication.
		*/

		//Create a new PHPMailer instance
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
		$mail->setFrom($this->data['contact_from'],$this->data['contact_fromtitle']);
		//Set an alternative reply-to address
		$mail->addReplyTo($this->data['email'], $this->data['name']);
		//Set who the message is to be sent to
		$mail->addAddress($this->data['contact_to'],$this->data['contact_totitle']);
		//Set the subject line
		$mail->Subject = $this->data['name'].' write:';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($this->data['message']);//file_get_contents('contents.html'), dirname(__FILE__)
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

	
		//$obj['error']='asd';
		//$obj['status']='ok';
		
		// Print JSON object
		echo json_encode($obj);
	}
}
?>