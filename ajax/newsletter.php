<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is simply JS -> PHP connector comand switch
 *
*/
// $home must be true
if(!$home){exit;}

// Initialize connector class
$once = new once($_CONFIG);
$once->set_data(array("ajax" => true, "csrf_token" => $_GET['csrf_token']));

switch($_GET['o']){
	case 'add_newsletter':
		$once->set_data(array(
			"email" => $once->filter_string($_GET['email'])
		));
		$once->add_newsletter();
	break;
	//############################ OTHER ##################################################
	default:
		echo 'wrong connector command';
	break;
}
?>
