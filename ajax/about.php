<?php
/**
 * Version: 1.0, 04.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Update plugin (once.update)
 *
*/
// $home must be true
if(!$home){exit;}

// Initialize connector class
$once = new once($_CONFIG);
$once->set_data(array("ajax" => true, "csrf_token" => $_GET['csrf_token']));

switch($_GET['o']){
	case 'check_server':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"version" => intval($_POST['version']),
			"api" => $once->filter_string($_POST['api'])
		));
		$once->check_server();
	}break;
	//############################ OTHER ##################################################
	default:
	break;
}
?>
