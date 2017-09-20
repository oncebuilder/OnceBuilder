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
			"id" => intval($_GET['id'])
		));
		$once->item_delete();
	}break;
	case 'item_edit':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"name" => $once->filter_string($_POST['name']),
			"title" => $once->filter_string($_POST['title']),
			"keywords" => $once->filter_string($_POST['keywords']),
			"description" => $once->filter_string($_POST['description']),
			"layer_id" => intval($_POST['layer_id']),
			"route_id" => intval($_POST['route_id']),
			"private" => $once->filter_string($_POST['private']),
			"password" => $once->filter_string($_POST['password']),
			"logged" => $once->filter_string($_POST['logged']),
			"adult" => $once->filter_string($_POST['adult']),
			"admins" => $once->filter_string($_POST['admins']),
			"moderators" => $once->filter_string($_POST['moderators']),
			"users" => $once->filter_string($_POST['users'])
		));
		$once->item_edit();
	}break;
	case 'item_new':{
		$once->set_data(array(
			"type_id" => intval($_GET['type_id'])
		));
		$once->item_new();
	}break;
	case 'item_star':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_star();
	}break;
	//############################ GRID ##################################################
	case 'item_grid_delete':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_grid_delete();
	}break;
	case 'item_grid_edit':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"page_id" => intval($_POST['page_id']),
			"item_id" =>  $once->filter_string($_POST['item_id']),
			"item_class" => $once->filter_string($_POST['item_class']),
			"row_id" => $once->filter_string($_POST['row_id']),
			"row_class" => $once->filter_string($_POST['row_class']),
			"namespace" => $once->filter_string($_POST['namespace'])
		));
		$once->item_grid_edit();
	}break;
	case 'item_grid_new':{
		$once->set_data(array(
			"page_id" => intval($_GET['page_id'])
		));
		$once->item_grid_new();
	}break;
	case 'item_grid_save':{
		if (get_magic_quotes_gpc()){
			$_POST['data'] = stripslashes($_POST['data']);
		}
		
		$once->set_data(array(
			"page_id" => intval($_GET['page_id']),
			"data" => json_decode($_POST['data'], true)
		));
		$once->item_grid_save();
	}break;
	case 'item_grid_save_as':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"type" => $once->filter_string($_GET['type'])
		));
		$once->item_grid_save_as();
	}break;
	case 'item_grid_select':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"field" => $once->filter_string($_GET['field']),
			"value" => intval($_GET['value'])
		));
		$once->item_grid_select();
	}break;
	case 'item_grid_visibility':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"type" => 'pages'
		));
		$once->item_grid_visibility();
	}break;
	//############################ HELPERS ##################################################
	case 'save_source':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"page_id" => intval($_GET['page_id']),
			"file" => $once->filter_string($_GET['file']),
			"source" => rawurldecode($_POST['source'])
		));
		$once->save_source();
	}break;
	case 'load_source':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"page_id" => intval($_GET['page_id']),
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