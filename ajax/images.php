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
	//############################ ITEM ##################################################
	case 'item_new':{
		$once->set_data(array(
			"path" => $once->filter_string($_GET['path'])
		));
		$once->item_new();
	}break;
	case 'item_delete':{
		$once->set_data(array(
			"path" => $once->filter_string($_GET['path'])
		));
		$once->item_delete();
	}break;
	case 'item_edit':{
		$once->set_data(array(
			"path" => $once->filter_string($_GET['path']),
			"name" => $once->filter_string($_POST['name'])
		));
		$once->item_edit();
	}break;
	//############################ HELPERS ##################################################
	case 'upload_files':{
		$once->set_data(array(
			"path" => $once->filter_string($_GET['path']),
			"files" => $_FILES['myFiles']
		));
		$once->upload_files();
	}break;
	//############################ OTHER ##################################################
	default:
		echo 'wrong connector command';
	break;
}
?>
