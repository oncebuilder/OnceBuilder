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
	case 'set_link':{
		$once->set_data(array(
			"page_id" => intval($_GET['page_id']),
			"route_id" => intval($_GET['route_id'])
		));
		$once->set_link();
	}break;
	//############################ ITEM ##################################################
	case 'item_delete':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_delete();
	}break;
	case 'item_new':{
		$_POST['name'] = str_replace(
			array("0","1","2","3","4","5","6","7","8","9"),   // z
			array("","","","","","","","","",""), // na
			$_POST['name']
		);
		$once->set_data(array(
			"name" => $once->filter_string($_POST['name']),
			"category_id" => intval($_GET['category_id'])
		));
		$once->item_new();
	}break;
	case 'item_update':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"param" => $once->filter_string($_GET['param']),
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