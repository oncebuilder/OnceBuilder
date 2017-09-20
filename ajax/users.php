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
			"login" => $once->filter_string($_POST['login']),
			"password" => $once->filter_string($_POST['password']),
			"username" => $once->filter_string($_POST['username']),
			"type_id" => $once->filter_string($_POST['type_id']),
			"email" => $once->filter_string($_POST['email']),
			"referer_id" => $once->filter_string($_POST['referer_id'])
		));
		$once->item_edit();
	}break;
	case 'item_edit_contact':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"firstname" => $once->filter_string($_POST['firstname']),
			"lastname" => $once->filter_string($_POST['lastname']),
			"email" => $once->filter_string($_POST['email']),
			"website" => $once->filter_string($_POST['website']),
			"company" => $once->filter_string($_POST['company']),
			"address" => $once->filter_string($_POST['address']),
			"address2" => $once->filter_string($_POST['address2']),
			"city" => $once->filter_string($_POST['city']),
			"phone" => $once->filter_string($_POST['phone']),
			"zip" => $once->filter_string($_POST['zip']),
			"province" => $once->filter_string($_POST['province']),
			"country" => $once->filter_string($_POST['country'])
		));
		$once->item_edit_contact();
	}break;
	case 'item_edit_social':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"facebook" => $once->filter_string($_POST['facebook']),
			"twitter" => $once->filter_string($_POST['twitter']),
			"youtube" => $once->filter_string($_POST['youtube']),
			"linkedin" => $once->filter_string($_POST['linkedin']),
			"dribbble" => $once->filter_string($_POST['dribbble']),
			"github" => $once->filter_string($_POST['github']),
			"google" => $once->filter_string($_POST['google']),
			"behance" => $once->filter_string($_POST['behance']),
			"codepen" => $once->filter_string($_POST['codepen'])
		));
		$once->item_edit_social();
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
	//############################ HELPERS ##################################################
	case 'upload_thumbnail':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"image" => $_FILES['myImage']
		));
		$once->upload_thumbnail();
	}break;
	//############################ OTHER ##################################################
	default:
		echo 'wrong connector command';
	break;
}
?>