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
	case 'item_select':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"plugin_id" => intval($_GET['plugin_id']),
			"layer_id" => intval($_GET['layer_id']),
			"page_id" => intval($_GET['page_id']),
			"grid_id" => intval($_GET['grid_id'])
		));
		$once->item_select();
	}break;
	//############################ OTHER ##################################################
	default:
		echo 'wrong connector command';
	break;
}
?>
