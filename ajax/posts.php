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
	case 'bulk_action':{
		$once->set_data(array(
			"action" => $once->filter_string($_POST['action']),
			"ids" => $_POST['ids']
		));
		$once->bulk_action();
	}break;
	case 'item_delete':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_delete();
	}break;
	case 'item_edit':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"type_id" => intval($_POST['type_id']),
			"created" => intval($_POST['created']),
			"title" => $once->filter_string($_POST['title']),
			"keywords" => $once->filter_string($_POST['keywords']),
			"description" => $once->filter_string($_POST['description'])
		));
		$once->item_edit();
	}break;
	case 'item_edit_content':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"source" => rawurldecode($_POST['source'])
		));
		$once->item_edit_content();
	}break;
	case 'item_new':{
		$once->set_data(array(
			"type_id" => 2,
			"category_id" => intval($_GET['category_id'])
		));
		$once->item_new();
	}break;
	case 'item_star':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_star();
	}break;

	case 'upload_image':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"image" => $_FILES['myImage']
		));
		$once->upload_image();
	}break;
	//############################ OTHER ##################################################
	default:
	break;
}
?>
