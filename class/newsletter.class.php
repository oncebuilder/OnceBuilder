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
	function add_newsletter(){
		require_once($this->data['root_path'].'/once/libs/mailchimp-api/src/Drewm/MailChimp.php');
		
		$MailChimp = new MailChimp($this->data['mailchimp_apikey']);
			
		$this->item['result'] = $MailChimp->call('lists/subscribe', array(
			'id'                => $this->data['mailchimp_listid'],
			'email'             => array('email'=>$this->data['email']),
			'merge_vars'        => array(),
			'double_optin'      => false,
			'update_existing'   => true,
			'replace_interests' => false,
			'send_welcome'      => true,
		));
		
		return $this->once_response();
	}
}
?>