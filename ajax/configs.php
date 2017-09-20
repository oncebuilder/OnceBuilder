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
	//############################ MISC ##################################################
	case 'bulk_action':{
		$once->set_data(array(
			"action" => $once->filter_string($_POST['action']),
			"ids" => $_POST['ids']
		));
		$once->bulk_action();
	}break;
	case 'set_limit':{
		$once->set_data(array(
			"limit" => intval($_GET['limit'])
		));
		$once->set_limit();
	}break;
	//############################ ITEM ##################################################
	case 'item_delete':{
		$once->set_data(array(
			"key" => $once->filter_string($_GET['key'])
		));
		$once->item_delete();
	}break;
	case 'item_new':{
		$once->set_data(array(
			"key" => $once->filter_string($_POST['key'])
		));
		$once->item_new();
	}break;
	case 'item_update':{
		$once->set_data(array(
			"key" => $once->filter_string($_GET['key']),
			"value" => $once->filter_string($_GET['value'])
		));
		$once->item_update();
	}break;
	//############################ OTHER ##################################################
	default:
		echo 'wrong connector command';
	break;
}
?>