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

// Secure var
$home=true;

// Lets start session handling
session_start();

// Configuration varibles & langs
require_once('config.php');

// Require core class functions
require_once('class/core.class.php');

// Require connector class on type
require_once('class/'.$_GET['c'].'.class.php');

# CONTENT START -------------------
$once = new once($_CONFIG);

// Require connector depends on type and let it do work
require_once('dialogs/'.$_GET['c'].'-'.$_GET['o'].'.dialog.php');
?>