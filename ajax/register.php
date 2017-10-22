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
	case 'item_register':{
		$once->set_data(array(
			"email" => $once->filter_string($_POST['email']),
			"login" => $once->filter_string($_POST['login']),
			"username" => $once->filter_string($_POST['username']),
			"password" => $once->filter_string($_POST['password']),
			"referer_id" => intval($_COOKIE['referer_id'])
		));
		$once->item_register();
	}break;
	case 'send_activiation':{
		$once->set_data(array(
			"hash" => $once->filter_string($_POST['hash'])
		));
		$once->send_activiation();
	}break;
	case 'check_activiation':{
		$once->set_data(array(
			"user_id" => intval($_GET['uid']),
			"hash" => $once->filter_string($_GET['hash'])
		));
		$once->check_activiation();
	}break;
	//############################ OTHER ##################################################
	default:
	break;
}
?>
