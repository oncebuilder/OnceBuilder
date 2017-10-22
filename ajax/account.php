<?php
/**
 * Version: 1.0, 04.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Account plugin (once.account)
 *
*/
// $home must be true
if(!$home){exit;}

// Initialize connector class
$once = new once($_CONFIG);
$once->set_data(array("ajax" => true, "csrf_token" => $_GET['csrf_token']));

switch($_GET['o']){
	case 'upload_image':{
		$once->set_data(array(
			"image" => $_FILES['myImage']
		));
		$once->upload_image();
	}break;
	case 'change_password':{
		$once->set_data(array(
			"password" => $once->filter_string($_POST['password']),
			"newpassword" => $once->filter_string($_POST['newpassword']),
			"confirmpassword" => $once->filter_string($_POST['confirmpassword'])
		));
		$once->change_password();
	}break;
	case 'save_profile':{
		$once->set_data(array(
			"firstname" => $once->filter_string($_POST['firstname']),
			"lastname" => $once->filter_string($_POST['lastname']),
			"position" => $once->filter_string($_POST['position']),
			"location" => $once->filter_string($_POST['location'])
		));
		$once->save_profile();
	}break;
	case 'save_information':{
		$once->set_data(array(
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
		$once->save_information();
	}break;
	case 'save_social':{
		$once->set_data(array(
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
		$once->save_social();
	}break;
	case 'account_terminiate':{
		$once->set_data(array(
			"reason" => $once->filter_string($_POST['reason']),
			"password" => $once->filter_string($_POST['password'])
		));
		$once->account_terminiate();
	}break;
	//############################ OTHER ##################################################
	default:
	break;
}
?>
