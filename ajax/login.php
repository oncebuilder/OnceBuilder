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
	case 'item_login':{
		$once->set_data(array(
			"email" => $once->filter_string($_POST['email']),
			"password" => $once->filter_string($_POST['password'])
		));
		$once->item_login();
	}break;
	case 'item_remind':{
		$once->set_data(array(
			"email" => $once->filter_string($_POST['email'])
		));
		$once->item_remind();
	}break;
	case 'item_change':{
		$once->set_data(array(
			"user_id" => intval($_POST['uid']),
			"hash" => $once->filter_string($_POST['hash']),
			"password" => $once->filter_string($_POST['password'])
		));
		$once->item_change();
	}break;
	//############################ OTHER ##################################################
	default:
	break;
}
?>
