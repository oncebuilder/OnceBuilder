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
	case 'api_search':{
		$once->set_data(array(
			"api" => $once->filter_string($_POST['api'])
		));
		$once->api_search();
	}break;
	case 'set_limit':{
		$once->set_data(array(
			"limit" => intval($_GET['limit'])
		));
		$once->set_limit();
	}break;
	//############################ ITEM ##################################################
	case 'item_approve':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_approve();
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
			"category_id" => intval($_POST['category_id']),
			"version" => intval($_POST['version']),
			"price" => intval($_POST['price']),
			"name" => $once->filter_string($_POST['name']),
			"tags" => $once->filter_string($_POST['tags']),
			"author" => $once->filter_string($_POST['author']),
			"author_url" => $once->filter_string($_POST['author_url']),
			"licence" => $once->filter_string($_POST['licence']),
			"description" => $once->filter_string($_POST['description']),
		));
		$once->item_edit();
	}break;
	case 'item_download':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"theme_id" => intval($_POST['theme_id']),
			"api" => $once->filter_string($_POST['api'])
		));
		$once->item_download();
	}break;
	case 'item_export':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_export();
	}break;
	case 'item_import':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"file" => $_FILES['file'],
			"url" => $_POST['url']
		));
		$once->item_import();
	}break;
	case 'item_new':{
		$once->set_data(array(
			"type_id" => intval($_GET['type_id']),
			"category_id" => intval($_GET['category_id'])
		));
		$once->item_new();
	}break;
	case 'item_publish':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"api" => $once->filter_string($_POST['api']),
			"file" => $_FILES['file'],
			"thumbnail" => $_FILES['thumbnail'],
			"object_id" => intval($_POST['object_id']),
			"version" => $once->filter_string($_POST['version']),
			"price" => intval($_POST['price']),
			"name" => $once->filter_string($_POST['name']),
			"tags" => $once->filter_string($_POST['tags']),
			"author" => $once->filter_string($_POST['author']),
			"author_url" => $once->filter_string($_POST['author_url']),
			"licence" => $once->filter_string($_POST['licence']),
			"description" => $once->filter_string($_POST['description']),
			"message" => $once->filter_string($_POST['message'])
		));
		$once->item_publish();
	}break;
	case 'item_preview':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"theme_id" => intval($_POST['theme_id']),
			"api" => $once->filter_string($_POST['api'])
		));
		$once->item_preview();
	}break;
	case 'item_use':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_use();
	}break;
	case 'item_star':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_star();
	}break;
	//############################ IMAGES ##################################################
	case 'delete_image':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"currentImage" => intval($_GET['currentImage'])
		));
		$once->delete_image();
	}break;
	case 'upload_image':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"image" => $_FILES['myImage'],
			"currentImage" => intval($_POST['currentImage'])
		));
		$once->upload_image();
	}break;
	case 'upload_thumbnail':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"image" => $_FILES['myImage']
		));
		$once->upload_thumbnail();
	}break;
	//############################ WEBSITE ##################################################
	
	case 'item_user_buy':{
		$once->set_data(array(
			"theme_id" => intval($_GET['id'])
		));
		$once->item_user_buy();
	}break;
	case 'item_user_publish':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_user_publish();
	}break;
	case 'item_user_fork':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_user_fork();
	}break;
	case 'item_user_vote':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_user_vote();
	}break;
	case 'item_user_download':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_user_download();
	}break;
	case 'item_user_report':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"message" => $once->filter_string($_POST['message'])
		));
		$once->item_user_report();
	}break;
	
	//############################ OTHER ##################################################
	default:
		echo 'wrong connector command';
	break;
}
?>
