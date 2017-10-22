<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is simply JS -> PHP connector
 *
*/

// Report simple running errors except notices
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Set headers if u need more functionality CORS
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
//header('Access-Control-Max-Age: 604800');
header('Access-Control-Allow-Headers: x-requested-with');

// Some xampp fixing :/
$_GET['o'] = isset($_GET['o']) ? $_GET['o'] : '';
$_GET['c'] = isset($_GET['c']) ? $_GET['c'] : '';
$_GET['s'] = isset($_GET['s']) ? $_GET['s'] : '';

// Is it required??
if(!preg_match("/^[a-zA-Z0-9_.]+$/i",$_GET['c'])) exit;
if(!preg_match("/^[0-9]+$/i",$_GET['plugin_id'])) exit;

// Secure var
$home=true;

// Lets start session handling
session_start();

// Configuration varibles & langs
require_once('config.php');

// Require core class functions
require_once('class/core.class.php');

// Require connector class but not core again...
if($_GET['c']!='core') require_once('plugins/'.$_GET['plugin_id'].'/'.$_GET['c'].'.class.php');

// CREATE OBJECT CLASS -------------------
$once = new once($_CONFIG);

if($once->once_creator_check()){
	// Require connector ajax and let it works with connector class
	require_once('plugins/'.$_GET['plugin_id'].'/'.$_GET['c'].''.($_GET['s']==''?'':'.'.$_GET['s']).'.php');
}
?>