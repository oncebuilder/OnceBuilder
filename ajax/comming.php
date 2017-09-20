<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is simply JS -> PHP connector command switch
 *
*/
// $home must be true
if(!$home){exit;}

// Initialize connector class
$once = new once($_CONFIG);
$once->set_data(array("ajax" => true, "csrf_token" => $_GET['csrf_token']));

switch($_GET['o']){
	case 'api_key_request':{
		$once->set_data(array(
			"email" => $once->filter_string($_GET['email']),
			"referer_id" => intval($_GET['referer_id'])
		));
		$once->api_key_request();
	}break;
	case 'item_download':{
		$once->set_data(array(
			"user_id" => intval($_GET['uid']),
			"hash" => $once->filter_string($_GET['hash'])
		));
		$once->item_download();
	}break;
	//############################ OTHER ##################################################
	default:
	break;
}
?>
