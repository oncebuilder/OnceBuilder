<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is simple Preview -> PHP connector
 *
*/

// Report simple running errors except notices
error_reporting(E_ERROR | E_WARNING | E_PARSE);

ob_start("ob_gzhandler");
$home=true;

# SESSION -----------------
session_start();
if($_GET['route']=='logout'){
	session_unset();
	session_destroy();
}

# CONFIG -----------------
require_once('../once/config.php');

# ROUTES -------------------
require_once('../routes/routes.'.$_SESSION['user_lang'].'.php');

# LANGS -------------------
require_once('../langs/langs.'.$_SESSION['user_lang'].'.php');

# CLASS -------------------
require_once('../once/class/core.class.php');
$once=new core($_CONFIG);

if($_GET['path']!='layers' && $_GET['path']!='pages') exit;

// Is it required??
if(!preg_match("/^[a-zA-Z0-9_.]+$/i",$_GET['file'])) exit;

# PAGE START -------------------
echo '
<!DOCTYPE html>
<html>
	<head>
		';
		# PAGE HEADER -------------------
		echo '
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
	
		<!-- Latest compiled and minified font awesome -->
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

		<!-- Latest compiled and minified global styles from layers -->
		<link rel="stylesheet" href="./css/global.css" type="text/css">
		
		<!-- Latest compiled and minified styles from layers -->
		<link rel="stylesheet" href="./css/style.css" type="text/css">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div id="body">';
		# PAGE GRIDS -------------------
		require_once('../'.$_GET['path'].'/'.$_GET['file']);
		echo '
		</div>
		<!-- Latest compiled and minified jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		
		<!-- Latest compiled and minified modernizr -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
		
		<!-- Latest compiled and minified script of all layers -->
		<script type="text/javascript" src="/js/script.js"></script>
		
		<!-- Latest compiled and minified once -->
		<script type="text/javascript" src="/js/once.js"></script>
	</body>
</html>';
ob_end_flush(); 
?>