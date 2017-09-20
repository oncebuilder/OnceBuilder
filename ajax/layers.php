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
	case 'item_copy':{
		$once->set_data(array(
			"layer_id" => intval($_GET['layer_id']),
			"layer_id_to" => intval($_GET['layer_id_to'])
		));
		
		$once->item_copy();
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
			"name" => $once->filter_string($_POST['name']),
			"default" => intval($_POST['default'])
		));
		
		$once->item_edit();
	}break;
	case 'item_new':{
		$once->item_new();
	}break;
	//############################ GRID ##################################################
	case 'item_grid_delete':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_grid_delete();
	}break;
	case 'item_grid_copy':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"layer_id" => intval($_GET['layer_id'])
		));
		$once->item_grid_copy();
	}break;
	case 'item_grid_download':{
		$once->set_data(array(
			"layer_id" => intval($_GET['layer_id'])
		));
		$once->item_grid_download();
	}break;
	case 'item_grid_edit':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"layer_id" => intval($_POST['layer_id']),
			"item_id" =>  $once->filter_string($_POST['item_id']),
			"item_class" => $once->filter_string($_POST['item_class']),
			"row_id" => $once->filter_string($_POST['row_id']),
			"row_class" => $once->filter_string($_POST['row_class']),
			"container" => intval($_POST['container']),
			"namespace" => $once->filter_string($_POST['namespace'])
		));
		$once->item_grid_edit();
	}break;
	case 'item_grid_save_as':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"type" => $once->filter_string($_GET['type'])
		));
		$once->item_grid_save_as();
	}break;
	case 'item_grid_new':{
		$once->set_data(array(
			"layer_id" => intval($_GET['layer_id'])
		));
		$once->item_grid_new();
	}break;
	case 'item_grid_save':{
		if (get_magic_quotes_gpc()){
			$_POST['data'] = stripslashes($_POST['data']);
		}
		
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"data" => json_decode($_POST['data'], true)
		));
		$once->item_grid_save();
	}break;
	case 'item_grid_select':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"plugin_id" => intval($_GET['plugin_id'])
		));
		$once->item_grid_select();
	}break;
	case 'item_grid_visibility':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_grid_visibility();
	}break;
	//############################ HELPERS ##################################################
	case 'save_source':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"file" => $once->filter_string($_GET['file']),
			"source" => rawurldecode($_POST['source'])
		));
		$once->save_source();
	}break;
	case 'load_source':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"file" => $once->filter_string($_GET['file'])
		));
		$once->load_source();
	}break;
	//############################ OTHER ##################################################
	default:
		echo 'wrong connector command';
	break;
}
?>