<?
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is simply JS -> PHP connector command switch
 *
*/

// Initialize connector class
$once = new once($_CONFIG);
$once->set_data(array("ajax" => true, "csrf_token" => $_GET['csrf_token']));
		
switch($_GET['o']){
	case 'bulk_action':{
		$once->set_data(array(
			"action" => filter($_POST['action']),
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

	case 'item_new':{
		$once->set_data(array(
			"type_id" => intval($_GET['type_id']),
			"category_id" => intval($_GET['category_id'])
		));
		$once->item_new();
	}break;
	case 'item_delete':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_delete();
	}break;
	case 'item_star':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_star();
	}break;
	case 'item_edit':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"name" => filter($_POST['name']),
			"category_id" => filter($_POST['category_id']),
			"author" => filter($_POST['author']),
			"url" => filter($_POST['url']),
			"tags" => filter($_POST['tags']),
			"description" => filter($_POST['description'])
		));
		$once->item_edit();
	}break;
	case 'item_publish':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"api" => filter($_POST['api']),
			"file_html" => $_FILES['file_html'],
			"file_css" => $_FILES['file_css'],
			"file_js" => $_FILES['file_js'],
			"logo_jpg" => $_FILES['logo_jpg'],
			"category_id" => filter($_POST['category_id']),
			"object_id" => intval($_POST['object_id']),
			"version" => filter($_POST['version']),
			"name" => filter($_POST['name']),
			"tags" => filter($_POST['tags']),
			"description" => filter($_POST['description']),
			"author" => filter($_POST['author']),
			"author_url" => filter($_POST['author_url']),
			"message" => filter($_POST['message'])
		));
		$once->item_publish();
	}break;
	case 'item_approve':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_approve();
	}break;
	case 'item_insights_image':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->item_insights_image();
	}break;
	case 'item_import':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"file" => $_FILES['file'],
			"url" => $_POST['url'] 
		));
		$once->item_import();
	}break;
	case 'item_install':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"snippet_id" => intval($_POST['snippet_id']),
			"api" => filter($_POST['api'])
		));
		$once->item_install();
	}break;
	case 'item_preview':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"snippet_id" => intval($_POST['snippet_id']),
			"api" => filter($_POST['api'])
		));
		$once->item_preview();
	}break;
	case 'item_report':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"snippet_id" => intval($_POST['snippet_id']),
			"api" => filter($_POST['api'])
		));
		$once->item_report();
	}break;
	case 'item_vote':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"snippet_id" => intval($_POST['snippet_id']),
			"api" => filter($_POST['api'])
		));
		$once->item_vote();
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
			"message" => filter($_POST['message'])
		));
		$once->item_user_report();
	}break;
	
	case 'upload_image':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"image" => $_FILES['myImage']
		));
		$once->upload_image();
	}break;
	case 'load_source':{
		$once->set_data(array(
			"id" => $_GET['id'],
			"path" => $_GET['path'],
			"file" => $_GET['file']
		));
		$once->load_source();
	}break;

	case 'save_source':{
		$once->set_data(array(
			"id" => $_GET['id'],
			"path" => $_GET['path'],
			"file" => $_GET['file'],
			"title" => $_GET['title'],
			"source" => rawurldecode($_POST['source'])
		));
		$once->save_source();
	}break;
	//############################ OTHER ##################################################
	default:
	break;
}
?>