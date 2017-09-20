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
// $home must be true
if(!$home){exit;}

// Initialize connector class
$once = new once($_CONFIG);
$once->set_data(array("ajax" => true, "csrf_token" => $_GET['csrf_token']));

switch($_GET['o']){
	case 'item_follow':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_follow();
	}break;
	case 'item_unfollow':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_unfollow();
	}break;
	case 'item_user_hire':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"message" => $once->filter_string($_POST['message'])
		));
		$once->item_user_hire();
	}break;
	//############################ OTHER ##################################################
	default:
	break;
}
?>
